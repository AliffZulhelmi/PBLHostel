<?php
session_start();
require_once 'conn.php';

// Check if user is logged in and is an admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

// Fetch data for the dashboard
$capacity_summary = getRoomCapacitySummary();

// Prepare data for Chart.js
$block_labels = [];
$total_data = [];
$occupied_data = [];

foreach ($capacity_summary as $summary) {
    $block_labels[] = $summary['block_id'];
    $total_data[] = (int)$summary['total'];
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
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">

    <style>
        body { font-family: 'Inter', system-ui, sans-serif; }
    </style>
</head>
<body class="bg-gray-100 text-gray-900 m-0 p-0">

    <?php include 'admin_nav.php'; // Includes navigation bar ?>

    <div class="max-w-6xl mx-auto my-10 px-8 py-6 bg-white rounded-xl shadow-lg border border-gray-100">

        <h2 class="text-xl font-bold text-gray-900 mb-6">Hostel Capacity Overview</h2>
        
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-8">
            <h3 class="text-lg font-semibold text-blue-800 flex items-center space-x-2">
                <i class="fas fa-chart-bar"></i>
                <span>Capacity Analysis by Block</span>
            </h3>
            <p class="text-sm text-blue-700 mt-1">
                Real-time overview of hostel occupancy across all blocks
            </p>
        </div>

        <div class="bg-white border border-gray-200 rounded-lg p-6 shadow-sm">
            <canvas id="capacityChart"></canvas>
        </div>

        <div class="border-b border-gray-200 my-8"></div>

        <div>
            <h3 class="text-lg font-semibold text-gray-900 mb-6">Administrative Actions</h3>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

            <a href="admin_change_requests.php" class="group">
                <div class="rounded-lg shadow-lg transition-all duration-200 p-6 h-full flex flex-col text-white bg-blue-600/85 hover:bg-blue-600">
                <div class="flex items-center space-x-3 mb-3">
                    <i class="fas fa-exchange-alt text-xl"></i>
                    <h4 class="text-xl font-bold">Manage Room Changes</h4>
                </div>
                <p class="text-blue-100/90 flex-grow">Review pending room requests.</p>
                <div class="mt-4 flex items-center text-blue-200 group-hover:text-white transition-colors">
                    <span class="text-sm font-medium">View Requests</span>
                    <i class="fas fa-arrow-right ml-2"></i>
                </div>
                </div>
            </a>

            <a href="admin_complaint_tickets.php" class="group">
                <div class="rounded-lg shadow-lg transition-all duration-200 p-6 h-full flex flex-col text-white bg-purple-600/85 hover:bg-purple-600">
                <div class="flex items-center space-x-3 mb-3">
                    <i class="fas fa-exclamation-triangle text-xl"></i>
                    <h4 class="text-xl font-bold">Handle Complaints</h4>
                </div>
                <p class="text-purple-100/90 flex-grow">View and update filed complaints.</p>
                <div class="mt-4 flex items-center text-purple-200 group-hover:text-white transition-colors">
                    <span class="text-sm font-medium">Manage Tickets</span>
                    <i class="fas fa-arrow-right ml-2"></i>
                </div>
                </div>
            </a>

            <a href="admin_room_status.php" class="group">
                <div class="rounded-lg shadow-lg transition-all duration-200 p-6 h-full flex flex-col text-white bg-green-600/85 hover:bg-green-600">
                <div class="flex items-center space-x-3 mb-3">
                    <i class="fas fa-eye text-xl"></i>
                    <h4 class="text-xl font-bold">View Live Room Status</h4>
                </div>
                <p class="text-green-100/90 flex-grow">Check availability and room lists.</p>
                <div class="mt-4 flex items-center text-green-200 group-hover:text-white transition-colors">
                    <span class="text-sm font-medium">Check Status</span>
                    <i class="fas fa-arrow-right ml-2"></i>
                </div>
                </div>
            </a>
            </div>
        </div>
        </div>

    <script>
        const chartData = <?php echo $chart_data_json; ?>;
        
        const config = {
            type: 'bar',
            data: {
                labels: chartData.labels,
                datasets: [
                    {
                        label: 'Occupied Spaces',
                        data: chartData.occupied,
                        backgroundColor: 'rgba(239, 68, 68, 0.7)',
                        borderColor: 'rgba(185, 28, 28, 1)',
                        borderWidth: 1
                    },
                    {
                        label: 'Total Capacity',
                        data: chartData.total.map((t, i) => t - chartData.occupied[i]),
                        backgroundColor: 'rgba(59, 130, 246, 0.8)',
                        borderColor: 'rgba(30, 64, 175, 1)',
                        borderWidth: 1
                    }
                ]
            },
            options: {
                responsive: true,
                scales: {
                    x: { stacked: true, title: { display: true, text: 'Hostel Block' } },
                    y: { stacked: true, beginAtZero: true, title: { display: true, text: 'Number of Students' } }
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