<?php
session_start();
require_once 'conn.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

$message = '';

// Handle Delete Action (Objective 1)
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['ticket_id'])) {
    $ticket_id_to_delete = (int)$_GET['ticket_id'];
    
    if (deleteTicket($ticket_id_to_delete)) {
        $message = "Ticket #$ticket_id_to_delete deleted successfully.";
    } else {
        $message = "Error deleting Ticket #$ticket_id_to_delete.";
    }
}


// Handle Status Update (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ticket_id = (int)$_POST['ticket_id'];
    $new_status = $_POST['status'];
    
    if (updateTicketStatus($ticket_id, $new_status)) {
        $message = "Ticket #$ticket_id status updated to $new_status successfully.";
    } else {
        $message = "Error updating status for Ticket #$ticket_id.";
    }
}

$all_complaints = getAllComplaints();
$status_options = ['Open', 'Under Review', 'In Progress', 'Resolved', 'Unresolved'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Complaint Tickets - Admin</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="style.css">
    <style>
        body { font-family: 'Inter', system-ui, sans-serif; }
    </style>
</head>
<body class="bg-gray-100 text-gray-900 m-0 p-0">

    <?php include 'admin_nav.php'; ?>

    <div class="max-w-7xl mx-auto my-10 p-5 bg-white rounded-xl shadow-2xl">
        <h2 class="text-3xl font-bold text-gray-800 mb-6 border-b pb-2">Student Complaint Tickets</h2>
        
        <?php if ($message): ?>
            <div class="mb-4 p-4 rounded-lg <?php echo strpos($message, 'Error') !== false ? 'bg-red-100 text-red-700 border-red-300' : 'bg-green-100 text-green-700 border-green-300'; ?> border">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <div class="overflow-x-auto bg-white rounded-lg shadow">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category / Description</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php if (empty($all_complaints)): ?>
                        <tr><td colspan="5" class="px-6 py-4 text-center text-gray-500 italic">No general complaints filed.</td></tr>
                    <?php endif; ?>
                    <?php foreach ($all_complaints as $ticket): ?>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?php echo htmlspecialchars($ticket['ticket_id']); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                <?php echo htmlspecialchars($ticket['full_name']); ?><br>(<?php echo htmlspecialchars($ticket['student_id']); ?>)
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-700 max-w-md">
                                <p class="font-medium"><?php echo htmlspecialchars($ticket['category']); ?></p>
                                <p class="text-xs max-w-xs truncate"><?php echo htmlspecialchars($ticket['description']); ?></p>
                                <?php if ($ticket['attachment_path']): ?>
                                    <a href="<?php echo htmlspecialchars($ticket['attachment_path']); ?>" target="_blank" class="text-xs text-blue-600 font-medium hover:underline">
                                        View Attachment
                                    </a>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    <?php 
                                        if ($ticket['status'] === 'Open') echo 'bg-blue-100 text-blue-800';
                                        elseif ($ticket['status'] === 'Under Review') echo 'bg-yellow-100 text-yellow-800';
                                        elseif ($ticket['status'] === 'In Progress') echo 'bg-orange-100 text-orange-800';
                                        elseif ($ticket['status'] === 'Resolved') echo 'bg-green-100 text-green-800';
                                        else echo 'bg-red-100 text-red-800';
                                    ?>">
                                    <?php echo htmlspecialchars($ticket['status']); ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm space-y-1">
                                <form method="post" action="admin_complaint_tickets.php" class="flex flex-col space-y-1">
                                    <input type="hidden" name="ticket_id" value="<?php echo $ticket['ticket_id']; ?>">
                                    <select name="status" class="p-1 border border-gray-300 rounded text-xs">
                                        <?php foreach ($status_options as $status): ?>
                                            <option value="<?php echo $status; ?>" <?php echo ($ticket['status'] === $status) ? 'selected' : ''; ?>>
                                                <?php echo $status; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <button type="submit" class="bg-indigo-500 text-white text-xs py-1 rounded hover:bg-indigo-600 transition">Update</button>
                                </form>
                                <a href="admin_complaint_tickets.php?action=delete&ticket_id=<?php echo $ticket['ticket_id']; ?>" 
                                   onclick="return confirm('Are you sure you want to permanently delete Ticket #<?php echo $ticket['ticket_id']; ?>?')"
                                   class="block text-center bg-red-500 text-white text-xs py-1 rounded hover:bg-red-600 transition">
                                    Delete
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>