<?php
session_start();
require_once 'conn.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

$change_requests = getRoomChangeRequests();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Room Change Requests - Admin</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: 'Inter', system-ui, sans-serif; }
    </style>
</head>
<body class="bg-gray-100 text-gray-900 m-0 p-0">

    <?php include 'admin_nav.php'; ?>

    <div class="max-w-7xl mx-auto my-10 p-5 bg-white rounded-xl shadow-2xl">
        <h2 class="text-3xl font-bold text-gray-800 mb-6 border-b pb-2">Room Change Requests</h2>

        <?php if (isset($_SESSION['admin_message'])): ?>
            <?php $msg = $_SESSION['admin_message']; 
            $is_error = strpos($msg, 'rejected') !== false || strpos($msg, 'failed') !== false;
            $color = $is_error ? 'bg-red-100 text-red-700 border-red-300' : 'bg-green-100 text-green-700 border-green-300';
            ?>
            <div class="mb-4 p-4 rounded-lg <?php echo $color; ?> border">
                <?php echo htmlspecialchars($msg); ?>
            </div>
            <?php unset($_SESSION['admin_message']); ?>
        <?php endif; ?>

        <div class="overflow-x-auto bg-white rounded-lg shadow">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ticket ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Requested Change</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php if (empty($change_requests)): ?>
                        <tr><td colspan="5" class="px-6 py-4 text-center text-gray-500 italic">No room change requests found.</td></tr>
                    <?php endif; ?>
                    <?php foreach ($change_requests as $request): ?>
                        <tr class="<?php echo ($request['status'] === 'Pending') ? 'bg-yellow-50' : ''; ?>">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?php echo htmlspecialchars($request['ticket_id']); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                <?php echo htmlspecialchars($request['full_name']); ?> (<?php echo htmlspecialchars($request['student_id']); ?>)
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-700">
                                <span class="font-medium">FROM:</span> <?php echo htmlspecialchars($request['old_room_id']); ?><br>
                                <span class="font-medium">TO:</span> <?php echo htmlspecialchars($request['new_room_id']); ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    <?php 
                                        if ($request['status'] === 'Pending') echo 'bg-yellow-100 text-yellow-800';
                                        elseif ($request['status'] === 'Approved') echo 'bg-green-100 text-green-800';
                                        else echo 'bg-red-100 text-red-800';
                                    ?>">
                                    <?php echo htmlspecialchars($request['status']); ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                <?php if ($request['status'] === 'Pending'): ?>
                                    <a href="admin_process_request.php?action=approve&ticket_id=<?php echo $request['ticket_id']; ?>" 
                                       onclick="return confirm('APPROVE room change for <?php echo htmlspecialchars($request['student_id']); ?> to <?php echo htmlspecialchars($request['new_room_id']); ?>?')"
                                       class="text-green-600 hover:text-green-900 font-bold">Approve</a>
                                    <a href="admin_process_request.php?action=reject&ticket_id=<?php echo $request['ticket_id']; ?>" 
                                       onclick="return confirm('REJECT room change for <?php echo htmlspecialchars($request['student_id']); ?>?')"
                                       class="text-red-600 hover:text-red-900">Reject</a>
                                <?php else: ?>
                                    <span class="text-gray-500">Completed</span>
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