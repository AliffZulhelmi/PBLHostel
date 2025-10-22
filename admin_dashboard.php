<?php
session_start();
require_once 'conn.php';

// Check if user is logged in and is an admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

$full_name = $_SESSION['full_name'] ?? 'Admin';

// Fetch data for the dashboard
$rooms_status = getAllRoomsStatus();
$change_requests = getRoomChangeRequests();
$register_records = getRoomRegisterRecords();
$capacity_summary = getRoomCapacitySummary();

// Prepare data for Chart.js
$block_labels = [];
$total_data = [];
$occupied_data = [];

foreach ($capacity_summary as $summary) {
    $block_labels[] = $summary['block_id'];
    $total_data[] = (int)$summary['total'];
    // Occupied = Total - Available
    $occupied_data[] = (int)$summary['total'] - (int)$summary['available'];
}

$chart_data_json = json_encode([
    'labels' => $block_labels,
    'total' => $total_data,
    'occupied' => $occupied_data
]);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    
    <style>
        body { font-family: 'Inter', system-ui, sans-serif; }
    </style>
</head>
<body class="bg-gray-100 text-gray-900 m-0 p-0">

    <header class="bg-indigo-800 text-white py-3 px-4 flex items-center justify-between shadow-lg">
        <h1 class="text-3xl font-extrabold m-0">Admin Dashboard</h1>
        <div class="text-right">
            <p class="text-lg font-semibold">Hello, <?php echo htmlspecialchars($full_name); ?></p>
            <a href="logout.php" class="text-sm text-indigo-300 hover:text-white">Logout</a>
        </div>
    </header>

    <div class="max-w-7xl mx-auto my-10 p-5 bg-white rounded-xl shadow-2xl">

        <h2 class="text-3xl font-bold text-gray-800 mb-6 border-b pb-2">Hostel Overview</h2>
        
        <section class="mb-10 p-6 bg-blue-50 rounded-lg border border-blue-200">
            <h3 class="text-2xl font-semibold text-blue-800 mb-4">Capacity Analysis by Block</h3>
            <div class="bg-white p-4 rounded-lg shadow">
                <canvas id="capacityChart"></canvas>
            </div>
        </section>

        <section class="mb-10">
            <h3 class="text-2xl font-semibold text-gray-800 mb-4 border-b pb-2">Room Change Requests (Pending)</h3>
            <div class="overflow-x-auto bg-white rounded-lg shadow">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ticket ID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Details</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Requested At</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php if (empty($change_requests)): ?>
                            <tr><td colspan="6" class="px-6 py-4 text-center text-gray-500 italic">No new room change requests.</td></tr>
                        <?php endif; ?>
                        <?php foreach ($change_requests as $request): ?>
                            <tr class="<?php echo ($request['status'] === 'Pending') ? 'bg-yellow-50' : ''; ?>">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?php echo htmlspecialchars($request['ticket_id']); ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                    <?php echo htmlspecialchars($request['full_name']); ?> (<?php echo htmlspecialchars($request['student_id']); ?>)
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-700 max-w-sm truncate"><?php echo htmlspecialchars($request['description']); ?></td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?php echo ($request['status'] === 'Pending') ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800'; ?>">
                                        <?php echo htmlspecialchars($request['status']); ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo htmlspecialchars($request['created_at']); ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <a href="#" class="text-indigo-600 hover:text-indigo-900">View/Approve</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </section>

        <section class="mb-10">
            <h3 class="text-2xl font-semibold text-gray-800 mb-4 border-b pb-2">All Room Status & Availability</h3>
            <div class="overflow-x-auto bg-white rounded-lg shadow">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Room ID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Block</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Capacity (Available/Total)</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($rooms_status as $room): ?>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?php echo htmlspecialchars($room['room_identifier']); ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700"><?php echo htmlspecialchars($room['block_id']); ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                    <?php echo htmlspecialchars($room['available_capacity']); ?> / <?php echo htmlspecialchars($room['total_capacity']); ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        <?php echo ($room['status'] === 'Occupied') ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800'; ?>">
                                        <?php echo htmlspecialchars($room['status']); ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </section>

        <section class="mb-10">
            <h3 class="text-2xl font-semibold text-gray-800 mb-4 border-b pb-2">Room Assignment Records</h3>
            <div class="overflow-x-auto bg-white rounded-lg shadow">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Record ID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student ID / Name</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Room ID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Semester</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Assigned At</th>
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
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700"><?php echo htmlspecialchars($record['semester']); ?></td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        <?php echo ($record['assignment_status'] === 'Active') ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'; ?>">
                                        <?php echo htmlspecialchars($record['assignment_status']); ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo htmlspecialchars($record['assigned_at']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </div>

    <script>
        const chartData = <?php echo $chart_data_json; ?>;
        
        const config = {
            type: 'bar',
            data: {
                labels: chartData.labels,
                datasets: [
                    {
                        label: 'Total Capacity',
                        data: chartData.total,
                        backgroundColor: 'rgba(59, 130, 246, 0.7)', // Indigo-500
                        borderColor: 'rgba(30, 64, 175, 1)',
                        borderWidth: 1
                    },
                    {
                        label: 'Occupied Spaces',
                        data: chartData.occupied,
                        backgroundColor: 'rgba(239, 68, 68, 0.7)', // Red-500
                        borderColor: 'rgba(185, 28, 28, 1)',
                        borderWidth: 1
                    }
                ]
            },
            options: {
                responsive: true,
                scales: {
                    x: {
                        stacked: true,
                        title: {
                            display: true,
                            text: 'Hostel Block'
                        }
                    },
                    y: {
                        stacked: true,
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Number of Students'
                        }
                    }
                }
            }
        };

        new Chart(
            document.getElementById('capacityChart'),
            config
        );
    </script>
</body>
</html>