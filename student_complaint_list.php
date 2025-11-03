<?php
session_start();
require 'conn.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'student' || !isset($_SESSION['student_id'])) {
    header('Location: login.php');
    exit;
}

$student_id = $_SESSION['student_id'];
$complaints = getStudentComplaints($student_id);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Complaints</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <style>
        body { font-family: 'Inter', system-ui, sans-serif; box-sizing: border-box; }
    </style>
</head>
<body class="bg-gray-50 text-gray-900 m-0 p-0 min-h-full">

    <?php include 'student_header.php'; ?>

    <div class="max-w-7xl mx-auto my-10 p-5">
    <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden px-8 py-6">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">My Complaints</h2>

        <!-- Purple callout -->
         <div class="bg-purple-50 border border-purple-200 rounded-lg p-4 mb-6">
            <h3 class="text-lg font-semibold text-purple-800 flex items-center space-x-2">
                <i class="fas fa-clipboard-list"></i>
                <span>Complaint History</span>
            </h3>
        <p class="text-sm text-purple-700 mt-1">
            Track the status of your submitted issues and requests.
        </p>
        </div>
        
        <!-- Table -->
        <div class="overflow-x-auto bg-white border border-gray-200 rounded-lg shadow-sm">
            <table class="min-w-full">

            <colgroup>
              <col style="width:12%">
              <col style="width:20%">
              <col style="width: 30px;%">
              <col style="width:23%">
              <col style="width:15%">
            </colgroup>

                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ticket ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Filed On</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php if (empty($complaints)): ?>
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center">
                                <div class="flex flex-col items-center space-y-2">
                                    <i class="fas fa-inbox text-gray-400 text-2xl"></i>
                                    <span class="text-gray-500 italic">No complaints filed yet.</span>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>

                    <?php foreach ($complaints as $ticket): ?>
                        <tr class="hover:bg-gray-50 transition-colors duration-150">
                            <!-- Ticket ID -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-bold text-gray-900">
                                    <?php echo htmlspecialchars($ticket['ticket_id']); ?>
                                </div>
                            </td>

                            <!-- Category -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">
                                    <?php echo htmlspecialchars($ticket['category']); ?>
                                </div>
                            </td>

                            <!-- Description -->
                            <td class="px-6 py-4">
                                <div class="space-y-1 max-w-md">
                                    <p class="text-sm text-gray-700 truncate">
                                    <?php echo htmlspecialchars($ticket['description']); ?>
                                    </p>
                                    <?php if (!empty($ticket['attachment_path'])): ?>
                                        <a href="<?php echo htmlspecialchars($ticket['attachment_path']); ?>" target="_blank" class="inline-flex items-center gap-1 text-xs text-purple-700 font-medium hover:underline">
                                            <i class="fas fa-paperclip"></i>
                                            <span>View attachment</span>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </td>

                            <!-- Created at -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-600">
                                    <?php echo htmlspecialchars($ticket['created_at']); ?>
                                </div>
                            </td>

                            <!-- Status -->
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
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    </div>
</body>
</html>