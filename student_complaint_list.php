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
    <style>
        body { font-family: 'Inter', system-ui, sans-serif; }
    </style>
</head>
<body class="bg-gray-100 text-gray-900 m-0 p-0">

    <header class="bg-gray-800 text-white py-3 px-4 shadow-lg">
        <div class="max-w-7xl mx-auto flex items-center justify-between">
            <h1 class="text-3xl font-extrabold m-0">My Complaints</h1>
            <a href="student_dashboard.php" class="py-1 px-3 rounded bg-indigo-600 hover:bg-indigo-700">Back to Dashboard</a>
        </div>
    </header>

    <div class="max-w-7xl mx-auto my-10 p-5 bg-white rounded-xl shadow-2xl">
        <p class="text-sm text-gray-600 mb-4">Track the status of your submitted issues and requests.</p>
        
        <div class="overflow-x-auto bg-white rounded-lg shadow">
            <table class="min-w-full divide-y divide-gray-200">
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
                        <tr><td colspan="5" class="px-6 py-4 text-center text-gray-500 italic">No complaints filed yet.</td></tr>
                    <?php endif; ?>
                    <?php foreach ($complaints as $ticket): ?>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?php echo htmlspecialchars($ticket['ticket_id']); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700"><?php echo htmlspecialchars($ticket['category']); ?></td>
                            <td class="px-6 py-4 text-sm text-gray-700 max-w-xs truncate"><?php echo htmlspecialchars($ticket['description']); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo htmlspecialchars($ticket['created_at']); ?></td>
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
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>