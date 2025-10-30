<?php
session_start();
require_once 'conn.php';

// =======================
//  Admin Assignment Records Page
//  - For viewing and managing student-room assignments
// =======================

// Check if user is admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

// Sorting logic (default: by assigned date)
// Accept by on dropdown: assigned_at, student_id, assignment_status and room_identifier
$sort_by = $_GET['sort'] ?? 'assigned_at';
$message = '';

// --- Handle Admin Actions: Release or Activate ---
if (isset($_GET['action'])) {
    $action = $_GET['action']; // release | activate
    $sr_id = (int)$_GET['sr_id'];

    // Fetch assignment information by assignment record ID (sr_id)
    $assignment = getAssignmentBySRID($sr_id);

    if ($assignment) {
        if ($action === 'release') {
            // Process: Release student from assigned room
            $room_id = $assignment['room_identifier'];
            // checkoutStudentFromRoom: removes student record from room
            // updateRoomCapacityAfterCheckout: ensures room bed count/capacity is updated
            if (checkoutStudentFromRoom($assignment['student_id'], $room_id) && updateRoomCapacityAfterCheckout($room_id)) {
                $message = "Assignment #$sr_id successfully released. Room $room_id capacity restored.";
            } else {
                $message = "Error releasing assignment #$sr_id.";
            }
        } elseif ($action === 'activate') {
            // NOTE: Does NOT check actual room availability before (re)activation!
            if (updateAssignmentStatus($sr_id, 'Active')) {
                $message = "Assignment #$sr_id status set to Active.";
            } else {
                $message = "Error activating assignment #$sr_id.";
            }
        }
    }
}

// Fetch all room register (assignment) records, sorted according to selection
$register_records = getRoomRegisterRecords($sort_by);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assignment Records - Admin</title>
    <!-- Tailwind CSS, font style -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="style.css">
    <style>
        body { font-family: 'Inter', system-ui, sans-serif; box-sizing: border-box;}
    </style>
</head>
<body class="bg-gray-50 text-gray-900 m-0 p-0">

    <?php include 'admin_nav.php'; // Navigation bar for admin ?>

    <div class="max-w-7xl mx-auto my-10 p-5">
    <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
    <div class="px-8 py-6">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">Room Assignment Records</h2>
        
        <!-- Display feedback messages (success or error) -->
        <?php if ($message): ?>
            <div class="mb-6 p-4 rounded-lg <?php echo strpos($message, 'Error') !== false ? 'bg-red-50 text-red-800 border-red-200' : 'bg-green-50 text-green-800 border-green-200'; ?> border flex items-center space-x-2">
            <i class="<?php echo strpos($message, 'Error') !== false ? 'fas fa-exclamation-circle' : 'fas fa-check-circle'; ?>"></i>
            <span><?php echo htmlspecialchars($message); ?></span>
            </div>
        <?php endif; ?>

        <!-- Sorting selector for admin viewing ease -->
        <div class="mb-4 flex justify-end items-center space-x-2">
            <label for="sort-select" class="text-sm font-medium text-gray-700">Sort By:</label>
            <select id="sort-select" onchange="window.location.href='admin_assignment_records.php?sort=' + this.value" class="p-1 border border-gray-300 rounded text-sm">
                <option value="assigned_at" <?php echo $sort_by === 'assigned_at' ? 'selected' : ''; ?>>Assigned Date</option>
                <option value="student_id" <?php echo $sort_by === 'student_id' ? 'selected' : ''; ?>>Student ID</option>
                <option value="assignment_status" <?php echo $sort_by === 'assignment_status' ? 'selected' : ''; ?>>Status</option>
                <option value="room_identifier" <?php echo $sort_by === 'room_identifier' ? 'selected' : ''; ?>>Room ID</option>
            </select>
        </div>

        <!-- Assignment Records Table -->
        <div class="overflow-x-auto bg-white border border-gray-200 rounded-lg shadow-sm">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Record ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Room ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Assigned At</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php foreach ($register_records as $record): ?>
                        <tr class="hover:bg-gray-50 transition-colors duration-150">
                            <!-- Record (assignment) ID -->
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900">
                                <?php echo htmlspecialchars($record['sr_id']); ?></td>
                            <!-- Student ID and name -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="space-y-1">
                                    <div class="text-sm font-medium text-gray-900">
                                        <?php echo htmlspecialchars($record['full_name']); ?> 
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        <?php echo htmlspecialchars($record['student_id']); ?>
                                    </div>
                                </div>
                            </td>
                            <!-- Room Identifier -->
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                <?php echo htmlspecialchars($record['room_identifier']); ?>
                            </td>
                            <!-- Status indicator (Active / Released / etc.) -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    <?php 
                                        if ($record['assignment_status'] === 'Active') echo 'bg-green-100 text-green-800';
                                        // If "Released" in status, eg. Released by admin, Released by student, etc.
                                        elseif (strpos($record['assignment_status'], 'Released') !== false) echo 'bg-gray-200 text-gray-800';
                                        else echo 'bg-yellow-100 text-yellow-800'; // For pending or other statuses
                                    ?>">
                                    <?php echo htmlspecialchars($record['assignment_status']); ?>
                                </span>
                            </td>
                            <!-- Assignment date -->
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <?php echo htmlspecialchars($record['assigned_at']); ?></td>
                            <!-- Admin actions: Release or Reactivate assignment -->
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex space-x-3">
                                <?php if ($record['assignment_status'] === 'Active'): ?>
                                    <a href="admin_assignment_records.php?action=release&sr_id=<?php echo $record['sr_id']; ?>"
                                       onclick="return confirm('Confirm RELEASE Student <?php echo $record['student_id']; ?> from Room <?php echo $record['room_identifier']; ?>?')"
                                       class="bg-white hover:bg-gray-50 text-red-600 border border-red-300 px-3 py-1 rounded-md text-xs font-medium transition-colors duration-200">
                                       Release
                                    </a>
                                <?php else: ?>
                                    <a href="admin_assignment_records.php?action=activate&sr_id=<?php echo $record['sr_id']; ?>"
                                       onclick="return confirm('Confirm ACTIVATE this assignment for Student <?php echo $record['student_id']; ?>? NOTE: This does NOT check room availability.')"
                                       class="bg-white hover:bg-gray-50 text-green-600 border border-green-600 px-3 py-1 rounded-md text-xs font-medium transition-colors duration-200">
                                       Activate
                                    </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    </div>
    </div>
</body>
</html>