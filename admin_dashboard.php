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
    
    <style>
        body { font-family: 'Inter', system-ui, sans-serif; }
    </style>
</head>
<body class="bg-gray-100 text-gray-900 m-0 p-0">

    <?php include 'admin_nav.php'; // Includes navigation bar ?>

    <div class="max-w-7xl mx-auto my-10 p-5 bg-white rounded-xl shadow-2xl">

        <h2 class="text-3xl font-bold text-gray-800 mb-6 border-b pb-2">Hostel Capacity Overview</h2>
        
        <section class="mb-10 p-6 bg-blue-50 rounded-lg border border-blue-200">
            <h3 class="text-2xl font-semibold text-blue-800 mb-4">Capacity Analysis by Block</h3>
            <div class="bg-white p-4 rounded-lg shadow">
                <canvas id="capacityChart"></canvas>
            </div>
        </section>

        <section class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <a href="admin_change_requests.php" class="bg-indigo-500 text-white p-6 rounded-lg shadow-lg hover:bg-indigo-600 transition">
                <h3 class="text-xl font-bold">Manage Room Changes</h3>
                <p class="mt-2">Review pending room requests.</p>
            </a>
            <a href="admin_complaint_tickets.php" class="bg-purple-500 text-white p-6 rounded-lg shadow-lg hover:bg-purple-600 transition">
                <h3 class="text-xl font-bold">Handle Complaints</h3>
                <p class="mt-2">View and update filed complaints.</p>
            </a>
            <a href="admin_room_status.php" class="bg-green-500 text-white p-6 rounded-lg shadow-lg hover:bg-green-600 transition">
                <h3 class="text-xl font-bold">View Live Room Status</h3>
                <p class="mt-2">Check availability and room lists.</p>
            </a>
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
                        backgroundColor: 'rgba(59, 130, 246, 0.7)',
                        borderColor: 'rgba(30, 64, 175, 1)',
                        borderWidth: 1
                    },
                    {
                        label: 'Occupied Spaces',
                        data: chartData.occupied,
                        backgroundColor: 'rgba(239, 68, 68, 0.7)',
                        borderColor: 'rgba(185, 28, 28, 1)',
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