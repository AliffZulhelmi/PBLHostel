<?php
session_start();
require_once 'conn.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

$message = '';
$status_options = ['Available', 'Occupied', 'Broken', 'Under Maintenance', 'Cleaning'];

// Handle POST actions for room status change/delete
if (isset($_GET['action'])) {
    $action = $_GET['action'];
    $room_identifier = $_GET['room_id'] ?? '';

    if ($room_identifier) {
        if ($action === 'change_status' && isset($_GET['new_status'])) {
            $new_status = $_GET['new_status'];
            
            // Objective 3: Unassign logic for non-occupancy statuses
            if (in_array($new_status, ['Broken', 'Under Maintenance', 'Cleaning'])) {
                if (updateRoomStatusAndUnassign($room_identifier, $new_status)) {
                    $message = "Room $room_identifier status updated to '$new_status'. All assigned students have been unassigned.";
                } else {
                    $message = "Error updating room $room_identifier status and unassigning students.";
                }
            } else {
                // For Available/Occupied, only change status
                $sql_update = "UPDATE rooms SET status='$new_status' WHERE room_identifier='$room_identifier'";
                if ($GLOBALS['conn']->query($sql_update)) {
                     $message = "Room $room_identifier status updated to '$new_status'.";
                } else {
                    $message = "Error updating room $room_identifier status.";
                }
            }

        } elseif ($action === 'delete') {
            // Objective 3: Delete Room
            if (deleteRoomAndUnassign($room_identifier)) {
                $message = "Room $room_identifier and all associated active assignments deleted successfully.";
            } else {
                $message = "Error deleting room $room_identifier.";
            }
        }
    }
}


// Objective 2: Fetch rooms grouped by block (Real-time data fetch)
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
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">

    <style>
        body { font-family: 'Inter', system-ui, sans-serif; box-sizing: border-box; }
        /* Objective 2: Expandable UI classes */
        .collapse-content { max-height: 0; overflow: hidden; transition: max-height 0.3s ease-out; }
        .collapse-content.open { max-height: 2000px; /* Arbitrarily large enough height */ }
    </style>
</head>
<body class="bg-gray-50 text-gray-900 m-0 p-0">

    <?php include 'admin_nav.php'; ?>

    <div class="max-w-7xl mx-auto my-10 p-5">
    <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
    <div class="px-8 py-6">
        <h2 class="text-2xl font-bold text-gray-900 mb-62">Room Status & Availability</h2>
        
        <?php if ($message): ?>
            <div class="mb-6 p-4 rounded-lg <?php echo strpos($message, 'Error') !== false ? 'bg-red-50 text-red-800 border-red-200' : 'bg-green-50 text-green-800 border-green-200'; ?> border">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6 mt-6">
            <h3 class="text-lg font-semibold text-blue-800 flex items-center space-x-2">
                <i class="fa-solid fa-building"></i>
                <span>Room List Overview</span>
            </h3>
            <p class="text-sm text-blue-700 mt-1">
                Click on a block to expand the full room list. Use the Action column to manage room status or delete records.
            </p>
        </div>

        <div class="space-y-4">
            <?php foreach ($rooms_grouped as $block_id => $rooms): 
                $summary = array_filter($capacity_summary, fn($s) => $s['block_id'] === $block_id);
                $summary = reset($summary);
                $occupied_count = ($summary['total'] ?? 0) - ($summary['available'] ?? 0);
                $is_full = ($summary['available'] ?? 0) == 0;
            ?>
                <div class="border border-gray-200 rounded-lg bg-gray-50">
                    <button class="w-full text-left p-4 flex justify-between items-center bg-white hover:bg-gray-100 transition rounded-lg shadow-sm" 
                            onclick="toggleCollapse('block-<?php echo $block_id; ?>')">
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
                        <div class="overflow-x-auto bg-white border border-gray-200 rounded-lg shadow-sm overflow-hidden">
                            <table class="min-w-full divide-y divide-gray-200 table-fixed">
                            <colgroup>
                                <col style="width:20%">
                                <col style="width:10%">
                                <col style="width:25%">
                                <col style="width:15%">
                                <col style="width:30%">
                            </colgroup>
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Room ID</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Floor</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Capacity (Available/Total)</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <?php foreach ($rooms as $room): ?>
                                        <tr class="hover:bg-gray-50 transition-colors duration-150">
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                <?php echo htmlspecialchars($room['room_identifier']); ?>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                                <?php echo htmlspecialchars($room['floor_no']); ?>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                                <?php echo htmlspecialchars($room['available_capacity']); ?> / <?php echo htmlspecialchars($room['total_capacity']); ?>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                    <?php 
                                                        $status_class = 'bg-gray-100 text-gray-800';
                                                        if ($room['status'] === 'Available') $status_class = 'bg-green-100 text-green-800';
                                                        elseif ($room['status'] === 'Occupied') $status_class = 'bg-red-100 text-red-800';
                                                        elseif ($room['status'] === 'Broken') $status_class = 'bg-red-500 text-white';
                                                        elseif ($room['status'] === 'Under Maintenance') $status_class = 'bg-yellow-500 text-white';
                                                        elseif ($room['status'] === 'Cleaning') $status_class = 'bg-blue-500 text-white';
                                                        echo $status_class; 
                                                    ?>">
                                                    <?php echo htmlspecialchars($room['status']); ?>
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            <div class="flex items-center space-x-6">
                                                <select onchange="updateRoomStatus(this, '<?php echo $room['room_identifier']; ?>')" class="p-1 border border-gray-300 rounded text-xs bg-white w-28">
                                                    <option value="">-- Change Status --</option>
                                                    <?php foreach ($status_options as $status): ?>
                                                        <option value="<?php echo $status; ?>" <?php echo ($room['status'] === $status) ? 'selected' : ''; ?>>
                                                            <?php echo $status; ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                                <button onclick="deleteRoom('<?php echo $room['room_identifier']; ?>')" 
                                                        class="px-3 py-1 rounded-md text-xs font-medium bg-red-600 text-white hover:bg-red-700 transition-colors duration-200">
                                                    Delete Room
                                                </button>
                                            </div>
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
    </div>
    </div>
    
    <script>
        function toggleCollapse(id) {
            const element = document.getElementById(id);
            element.classList.toggle('open');
        }

        // Objective 3: Status change logic
        function updateRoomStatus(selectElement, room_id) {
            const newStatus = selectElement.value;
            if (!newStatus) return;

            let confirmationMessage = `Are you sure you want to change the status of Room ${room_id} to '${newStatus}'?`;
            
            if (['Broken', 'Under Maintenance', 'Cleaning'].includes(newStatus)) {
                confirmationMessage += `\n\nWARNING: This will UNASSIGN all current tenants and reset available capacity.`;
            }

            if (confirm(confirmationMessage)) {
                window.location.href = `admin_room_status.php?action=change_status&room_id=${room_id}&new_status=${newStatus}`;
            } else {
                // Reset dropdown if cancelled
                selectElement.value = '';
            }
        }

        // Objective 3: Delete room logic
        function deleteRoom(room_id) {
            if (confirm(`WARNING: Are you absolutely sure you want to DELETE Room ${room_id}?\n\nThis action is permanent and will UNASSIGN all current tenants.`)) {
                 window.location.href = `admin_room_status.php?action=delete&room_id=${room_id}`;
            }
        }
    </script>
</body>
</html>