<?php
session_start();
require_once 'conn.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

// Objective 2: Sorting logic
$sort_by = $_GET['sort'] ?? 'assigned_at';
$message = '';

// Handle Admin Actions (Release/Edit Assignment)
if (isset($_GET['action'])) {
    $action = $_GET['action'];
    $sr_id = (int)$_GET['sr_id'];

    $assignment = getAssignmentBySRID($sr_id);

    if ($assignment) {
        if ($action === 'release') {
            $room_id = $assignment['room_identifier'];
            if (checkoutStudentFromRoom($assignment['student_id'], $room_id) && updateRoomCapacityAfterCheckout($room_id)) {
                $message = "Assignment #$sr_id successfully released. Room $room_id capacity restored.";
            } else {
                $message = "Error releasing assignment #$sr_id.";
            }
        } elseif ($action === 'activate') {
            // Note: Does not check room availability before activation
            if (updateAssignmentStatus($sr_id, 'Active')) {
                $message = "Assignment #$sr_id status set to Active.";
            } else {
                $message = "Error activating assignment #$sr_id.";
            }
        }
    }
}

$register_records = getRoomRegisterRecords($sort_by);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assignment Records - Admin</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: 'Inter', system-ui, sans-serif; }
    </style>
</head>
<body class="bg-gray-100 text-gray-900 m-0 p-0">

    <?php include 'admin_nav.php'; ?>

    <div class="max-w-7xl mx-auto my-10 p-5 bg-white rounded-xl shadow-2xl">
        <h2 class="text-3xl font-bold text-gray-800 mb-6 border-b pb-2">Room Assignment Records</h2>
        
        <?php if ($message): ?>
            <div class="mb-4 p-4 rounded-lg <?php echo strpos($message, 'Error') !== false ? 'bg-red-100 text-red-700 border-red-300' : 'bg-green-100 text-green-700 border-green-300'; ?> border">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <div class="mb-4 flex justify-end items-center space-x-2">
            <label for="sort-select" class="text-sm font-medium text-gray-700">Sort By:</label>
            <select id="sort-select" onchange="window.location.href='admin_assignment_records.php?sort=' + this.value" class="p-1 border border-gray-300 rounded text-sm">
                <option value="assigned_at" <?php echo $sort_by === 'assigned_at' ? 'selected' : ''; ?>>Assigned Date</option>
                <option value="student_id" <?php echo $sort_by === 'student_id' ? 'selected' : ''; ?>>Student ID</option>
                <option value="assignment_status" <?php echo $sort_by === 'assignment_status' ? 'selected' : ''; ?>>Status</option>
                <option value="room_identifier" <?php echo $sort_by === 'room_identifier' ? 'selected' : ''; ?>>Room ID</option>
            </select>
        </div>

        <div class="overflow-x-auto bg-white rounded-lg shadow">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Record ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student ID / Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Room ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Assigned At</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php foreach ($register_records as $record): ?>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?php echo htmlspecialchars($record['sr_id']); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                <?php echo htmlspecialchars($record['student_id']); ?> (<?php echo htmlspecialchars($record['full_name']); ?>)
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700"><?php echo htmlspecialchars($record['room_identifier']); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    <?php 
                                        if ($record['assignment_status'] === 'Active') echo 'bg-green-100 text-green-800';
                                        elseif (strpos($record['assignment_status'], 'Released') !== false) echo 'bg-gray-200 text-gray-800';
                                        else echo 'bg-yellow-100 text-yellow-800';
                                    ?>">
                                    <?php echo htmlspecialchars($record['assignment_status']); ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo htmlspecialchars($record['assigned_at']); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <?php if ($record['assignment_status'] === 'Active'): ?>
                                    <a href="admin_assignment_records.php?action=release&sr_id=<?php echo $record['sr_id']; ?>"
                                       onclick="return confirm('Confirm RELEASE Student <?php echo $record['student_id']; ?> from Room <?php echo $record['room_identifier']; ?>?')"
                                       class="text-red-600 hover:text-red-900 font-medium">Release</a>
                                <?php else: ?>
                                    <a href="admin_assignment_records.php?action=activate&sr_id=<?php echo $record['sr_id']; ?>"
                                       onclick="return confirm('Confirm ACTIVATE this assignment for Student <?php echo $record['student_id']; ?>? NOTE: This does NOT check room availability.')"
                                       class="text-green-600 hover:text-green-900 font-medium">Activate</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>