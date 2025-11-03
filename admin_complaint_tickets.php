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
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <style>
        body { font-family: 'Inter', system-ui, sans-serif; box-sizing: border-box; }
    </style>
</head>
<body class="bg-gray-50 text-gray-900 m-0 p-0 min-h-full">

    <?php include 'admin_nav.php'; ?>

    <div class="max-w-7xl mx-auto my-10 p-5">
    <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden px-8 py-6">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">Student Complaint Tickets</h2>
        
        <?php if ($message): 
            $is_error = str_contains($message, 'Error');
            $color = $is_error ? 'bg-red-50 text-red-800 border-red-200' : 'bg-green-50 text-green-800 border-green-200';
            $icon  = $is_error ? 'fas fa-exclamation-circle' : 'fas fa-check-circle';
        ?>
            <div class="mb-6 p-4 rounded-lg <?php echo $color; ?> border flex items-center space-x-2">
                <i class="<?php echo $icon; ?>"></i>
                <span><?php echo htmlspecialchars($message); ?></span>
            </div>
        <?php endif; ?>

        <!-- Purple callout to align with student_file_complaint.php -->
        <div class="bg-purple-50 border border-purple-200 rounded-lg p-4 mb-6">
            <h3 class="text-lg font-semibold text-purple-800 flex items-center space-x-2">
                <i class="fas fa-clipboard-list"></i>
                <span>Manage Student Complaints</span>
            </h3>
            <p class="text-sm text-purple-700 mt-1">Review, update status, and manage complaint records below.</p>
        </div>

        <div class="overflow-x-auto bg-white border border-gray-200 rounded-lg shadow-smw">
            <table class="min-w-full">
            <colgroup>
                <col style="width:10%">
                <col style="width:20%">
                <col style="width:25%">
                <col style="width:15%">
                <col style="width:30%">
            </colgroup>
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
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center">
                                <div class="flex flex-col items-center space-y-2">
                                    <i class="fas fa-inbox text-gray-400 text-2xl"></i>
                                    <span class="text-gray-500 italic">No general complaints filed.</span>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>

                    <?php foreach ($all_complaints as $ticket): ?>
                        <tr class="hover:bg-gray-50 transition-colors duration-150">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-bold text-gray-900"><?php echo htmlspecialchars($ticket['ticket_id']); ?></div>
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="space-y-1">
                                    <div class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($ticket['full_name']); ?></div>
                                    <div class="text-xs text-gray-500"><?php echo htmlspecialchars($ticket['student_id']); ?></div>
                                </div>
                            </td>

                            <td class="px-6 py-4">
                                <div class="space-y-1 max-w-md">
                                <p class="font-medium text-sm text-gray-900"><?php echo htmlspecialchars($ticket['category']); ?></p>
                                <p class="text-xs text-gray-600 truncate"><?php echo htmlspecialchars($ticket['description']); ?></p>
                                <?php if (!empty($ticket['attachment_path'])): ?>
                                    <a href="<?php echo htmlspecialchars($ticket['attachment_path']); ?>" target="_blank"
                                       class="inline-flex items-center gap-1 text-xs text-purple-700 font-medium hover:underline"></a>
                                        <i class="fas fa-paperclip"></i> View Attachment
                                    </a>
                                <?php endif; ?>
                                </div>
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full
                                    <?php 
                                        if ($ticket['status'] === 'Open')               echo 'bg-blue-100 text-blue-800';
                                        elseif ($ticket['status'] === 'Under Review')   echo 'bg-yellow-100 text-yellow-800';
                                        elseif ($ticket['status'] === 'In Progress')    echo 'bg-orange-100 text-orange-800';
                                        elseif ($ticket['status'] === 'Resolved')       echo 'bg-green-100 text-green-800';
                                        else                                            echo 'bg-red-100 text-red-800';
                                    ?>">
                                    <?php echo htmlspecialchars($ticket['status']); ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <form method="post" action="admin_complaint_tickets.php" class="flex items-center gap-3 flex-wrap">
                                    <input type="hidden" name="ticket_id" value="<?php echo $ticket['ticket_id']; ?>">
                                    <select name="status" class="p-1.5 border border-gray-300 rounded text-xs">
                                        <?php foreach ($status_options as $status): ?>
                                            <option value="<?php echo $status; ?>" <?php echo ($ticket['status'] === $status) ? 'selected' : ''; ?>>
                                                <?php echo $status; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <button type="submit" 
                                            class="bg-purple-600 hover:bg-purple-700 text-white px-3.5 py-1 rounded-md text-xs font-medium transition-colors duration-200">
                                        Update
                                    </button>
                                    <a href="admin_complaint_tickets.php?action=delete&ticket_id=<?php echo $ticket['ticket_id']; ?>"
                                       onlick="return confirm('Are you sure you want to permanently delete Ticket #<?php echo $ticket['ticket_id']; ?>?')"
                                       class="px-4 py-1 rounded-md text-xs font-medium bg-red-600 text-white hover:bg-red-700 transition-colors duration-200 inline-flex">
                                       Delete
                                    </a>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    </div>
</body>
</html>