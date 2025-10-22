<?php
session_start();
require_once 'conn.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

// Objective 2: Fetch rooms grouped by block
$rooms_grouped = getRoomsGroupedByBlock();
$capacity_summary = getRoomCapacitySummary();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Room Status - Admin</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: 'Inter', system-ui, sans-serif; }
        .collapse-content { max-height: 0; overflow: hidden; transition: max-height 0.3s ease-out; }
        .collapse-content.open { max-height: 1000px; /* Arbitrarily large enough height */ }
    </style>
</head>
<body class="bg-gray-100 text-gray-900 m-0 p-0">

    <?php include 'admin_nav.php'; ?>

    <div class="max-w-7xl mx-auto my-10 p-5 bg-white rounded-xl shadow-2xl">
        <h2 class="text-3xl font-bold text-gray-800 mb-6 border-b pb-2">All Room Status & Availability</h2>
        <p class="text-sm text-gray-600 mb-4">Click on a block to expand the full room list. Data is always real-time from the database.</p>
        
        <div class="space-y-4">
            <?php foreach ($rooms_grouped as $block_id => $rooms): 
                $summary = array_filter($capacity_summary, fn($s) => $s['block_id'] === $block_id);
                $summary = reset($summary);
                $occupied_count = ($summary['total'] ?? 0) - ($summary['available'] ?? 0);
                $is_full = ($summary['available'] ?? 0) == 0;
            ?>
                <div class="border border-gray-200 rounded-lg bg-gray-50">
                    <button class="w-full text-left p-4 flex justify-between items-center bg-white hover:bg-gray-100 transition rounded-lg shadow-sm" onclick="toggleCollapse('block-<?php echo $block_id; ?>')">
                        <h3 class="text-xl font-semibold text-gray-800">
                            Block <?php echo htmlspecialchars($block_id); ?>
                        </h3>
                        <div class="flex items-center space-x-4">
                            <span class="text-sm text-gray-600">
                                Total Rooms: <?php echo count($rooms); ?>
                            </span>
                            <span class="text-sm font-bold text-gray-600">
                                Occupancy: <?php echo $occupied_count; ?> / <?php echo htmlspecialchars($summary['total'] ?? 0); ?>
                            </span>
                            <span class="px-3 py-1 text-sm font-semibold rounded-full <?php echo $is_full ? 'bg-red-500 text-white' : 'bg-green-500 text-white'; ?>">
                                <?php echo $is_full ? 'FULL' : 'Available'; ?>
                            </span>
                        </div>
                    </button>

                    <div id="block-<?php echo $block_id; ?>" class="collapse-content">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 mt-2">
                                <thead class="bg-gray-100">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Room ID</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Floor</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Capacity (Available/Total)</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <?php foreach ($rooms as $room): ?>
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?php echo htmlspecialchars($room['room_identifier']); ?></td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700"><?php echo htmlspecialchars($room['floor_no']); ?></td>
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
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    
    <script>
        function toggleCollapse(id) {
            const element = document.getElementById(id);
            element.classList.toggle('open');
        }
    </script>
</body>
</html>