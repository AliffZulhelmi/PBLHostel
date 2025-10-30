<?php
// ===============================
// Admin - View and Manage Room Change Requests
// ===============================

session_start();
require_once 'conn.php';

// ----------- AUTHORIZATION -----------
// Only allow admin access. Redirect others to login page.
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

// ----------- FETCH REQUESTS DATA -----------
$change_requests = getRoomChangeRequests();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Room Change Requests - Admin</title>
    
    <!-- TailwindCSS for styling -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">

    <style>
        body { font-family: 'Inter', system-ui, sans-serif; box-sizing: border-box; }
    </style>
</head>
<body class="bg-gray-50 text-gray-900 m-0 p-0 min-h-full">

    <?php include 'admin_nav.php'; // Admin navigation bar ?>

    <div class="max-w-7xl mx-auto my-10 p-5">
    <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
    <div class="px-8 py-6">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">Room Change Requests</h2>

        <?php
        // ----------- FLASH MESSAGE FOR ADMIN ACTIONS -----------
        // Shows success/error after approving or rejecting requests.
        if (isset($_SESSION['admin_message'])):
            $msg = $_SESSION['admin_message'];
            $is_error = strpos($msg, 'rejected') !== false || strpos($msg, 'failed') !== false;
            $color = $is_error ? 'bg-red-50 text-red-800 border-red-200' : 'bg-green-50 text-green-800 border-green-200';
            $icon  = $is_error ? 'fas fa-exclamation-circle' : 'fas fa-check-circle';
            ?>
            <div class="mb-6 p-4 rounded-lg <?php echo $color; ?> border flex items-center space-x-2">
                <i class="<?php echo $icon; ?>"></i>
                <span><?php echo htmlspecialchars($msg); ?></span>
            </div>
            <?php unset($_SESSION['admin_message']); ?>
        <?php endif; ?>

        <!-- Request overview callout -->
         <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
            <h3 class="text-lg font-semibold text-blue-800 flex items-center space-x-2">
                <i class="fa-solid fa-right-left"></i>
                <span>Pending Room Change Request</span>
            </h3>
            <p class="text-sm text-blue-700 mt-1">Review and process student room change requests below.</p>
         </div>

        <!--
            Room Change Requests Table
            - Shows: Ticket ID, Student Info, Requested Change (Old/New Room), Request Status, and Action
            - Actions: Approve / Reject (only if status is Pending)
        -->
        <div class="overflow-x-auto bg-white border border-gray-200 rounded-lg shadow-sm">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <!-- Table Headers for clarity -->
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ticket ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Requested Change</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php if (empty($change_requests)): ?>
                        <!-- Empty state: No change requests -->
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center">
                                <div class="flex flex-col items-center space-y-2">
                                    <i class="fas fa-inbox text-gray-400 text-2xl"></i>
                                    <span class="text-gray-500 italic">No room change requests found.</span>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                    <?php foreach ($change_requests as $request): ?>
                        <tr class="hover:bg-gray-50 transition-colors duration-150">
                            <!-- Ticket ID -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-bold text-gray-900">
                                <?php echo htmlspecialchars($request['ticket_id']); ?>
                                </div>
                            </td>
                            <!-- Student Name (ID) -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="space-y-1">
                                    <?php echo htmlspecialchars($request['full_name']); ?>
                                </div>
                                <div class="text-xs text-gray-500">
                                    <?php echo htmlspecialchars($request['student_id']); ?>
                                </div>
                            </td>
                            <!-- Requested Change info: FROM old room TO new room -->
                            <td class="px-6 py-4">
                                <div class="space-y-1">
                                <div class="text-sm text-gray-700">
                                    <span class="text-xs font-medium text-gray-500 uppercase tracking-wide">FROM:</span>
                                    <span class="font-bold text-gray-900"><?php echo htmlspecialchars($request['old_room_id']); ?></span>
                                </div>
                                <div class="text-sm text-gray-700">
                                    <span class="text-xs font-medium text-gray-500 uppercase tracking-wide">TO:</span>
                                    <span class="font-bold text-gray-900"><?php echo htmlspecialchars($request['new_room_id']); ?></span>
                                </div>
                                </div>
                            </td>
                            <!-- Request Status, color-tagged -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full
                                    <?php 
                                        if ($request['status'] === 'Pending')      echo 'bg-yellow-100 text-yellow-800';
                                        elseif ($request['status'] === 'Approved') echo 'bg-green-100 text-green-800';
                                        else                                       echo 'bg-red-100 text-red-800';
                                    ?>">
                                    <?php echo htmlspecialchars($request['status']); ?>
                                </span>
                            </td>
                            <!-- ADMIN ACTION BUTTONS: Approve/Reject if Pending, else Completed -->
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <?php if ($request['status'] === 'Pending'): ?>
                                <div class="flex space-x-3">
                                    <!-- Approve request button -->
                                    <a href="admin_process_request.php?action=approve&ticket_id=<?php echo $request['ticket_id']; ?>" 
                                       onclick="return confirm('APPROVE room change for <?php echo htmlspecialchars($request['student_id']); ?> to <?php echo htmlspecialchars($request['new_room_id']); ?>?')"
                                       class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded-md text-xs font-medium transition-colors duration-200 flex items-center space-x-1">
                                       <i class="fas fa-check"></i><span>Approve</span>
                                    </a>
                                    <!-- Reject request button -->
                                    <a href="admin_process_request.php?action=reject&ticket_id=<?php echo $request['ticket_id']; ?>" 
                                       onclick="return confirm('REJECT room change for <?php echo htmlspecialchars($request['student_id']); ?>?'"
                                       class="bg-white hover:bg-gray-50 text-red-600 border border-red-300 px-3 py-1 rounded-md text-xs font-medium transition-colors duration-200 flex items-center space-x-1">
                                       <i class="fas fa-times"></i><span>Reject</span>
                                    </a>
                                </div>
                                <?php else: ?>
                                    <span class="text-gray-400 text-xs inline-flex items-center space-x-1">
                                        <i class="fas fa-check-circle"></i><span>Completed</span>
                                    </span>
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