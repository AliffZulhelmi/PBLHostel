<?php
session_start();
require 'conn.php';

// If user is not logged in or not a student, redirect to login page
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'student' || !isset($_SESSION['student_id'])) {
    header('Location: login.php');
    exit;
}

$student_id = $_SESSION['student_id'];
$gender = getUserGender($student_id);
$full_name = $_SESSION['full_name'] ?? 'Student';

// --- Fetch Current Room ID ---
$current_room_id = '';
$sql_current_room = "SELECT room_identifier FROM student_rooms WHERE student_id = '$student_id' AND status = 'Active' LIMIT 1";
$res_current_room = $conn->query($sql_current_room);
if ($res_current_room && $res_current_room->num_rows > 0) {
    $current_room_id = $res_current_room->fetch_assoc()['room_identifier'];
} else {
    $_SESSION['room_message'] = "Error: You must have an active room assignment to request a room change.";
    header('Location: student_dashboard.php');
    exit;
}
// --- End Fetch Current Room ID ---


// ------------------------------------
// 1. Handle Form Submission (POST)
// ------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $block = $_POST['block'] ?? '';
    $floor = $_POST['floor'] ?? '';
    $room_no = $_POST['room'] ?? '';
    $partition_no = $_POST['partition_no'] ?? 'N/A';
    
    // Validate selection
    if ($block && $floor && $room_no) {
        // Construct the new room identifier
        $new_room_id = $block . '-' . $floor . '-' . str_pad($room_no, 3, '0', STR_PAD_LEFT);
        
        // 1. Submit the request to the admin (via the tickets table)
        $result = submitRoomChangeRequest($student_id, $current_room_id, $new_room_id);

        if ($result) {
            $_SESSION['room_message'] = "Room change request to **$new_room_id** submitted successfully. The request will be processed by the admin shortly.";
        } else {
            $_SESSION['room_message'] = "Room change request failed due to a database error.";
        }
    } else {
        $_SESSION['room_message'] = "Please select a valid block, floor, and room.";
    }

    // Redirect back to dashboard
    header('Location: student_dashboard.php');
    exit;
}

// ------------------------------------
// 2. Prepare Data for UI (GET/Initial Load)
// ------------------------------------
$available_rooms_data = getAvailableRoomsByGender($gender);
$available_blocks = [];
$available_floors = []; // Stores floors grouped by block
$available_rooms = []; // Stores rooms grouped by block/floor

foreach ($available_rooms_data as $room) {
    $block_id = $room['block_id'];
    $floor_no = $room['floor_no'];
    $room_no = $room['room_no'];
    
    // Populate blocks list
    if (!in_array($block_id, $available_blocks)) {
        $available_blocks[] = $block_id;
    }
    
    // Initialize block/floor array structure
    if (!isset($available_floors[$block_id])) {
        $available_floors[$block_id] = [];
        $available_rooms[$block_id] = [];
    }
    if (!isset($available_rooms[$block_id][$floor_no])) {
        $available_rooms[$block_id][$floor_no] = [];
    }

    // Populate floors list
    if (!in_array($floor_no, $available_floors[$block_id])) {
        $available_floors[$block_id][] = $floor_no;
    }

    // Populate rooms list
    $available_rooms[$block_id][$floor_no][] = $room_no;
}

// Convert PHP data to JSON for JavaScript use
$json_available_rooms = json_encode($available_rooms);
$json_available_floors = json_encode($available_floors);
$json_available_blocks = json_encode($available_blocks);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Room Change Request</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .partition.selected {
            background-color: #2563eb; 
            border-color: #1d4ed8;  
            color: white;
            transform: translateY(-4px) scale(1.02);
            box-shadow: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1); 
            font-weight: 700;
        }
        .partition {
            display: none; /* Hide partitions by default */
        }
    </style>
</head>
<body class="bg-gray-100 text-gray-900 m-0 p-0">

<?php include 'student_header.php'; ?>

<div class="max-w-[900px] mx-auto my-10 p-6 bg-white rounded-xl shadow-lg border border-gray-100">

    <section class="mb-8">
        <h2 class="text-xl font-bold text-gray-900 mb-2">Request a Room Change</h2>
        <p class="text-gray-600">
            Current Room:
            <strong class="text-blue-700"><?php echo htmlspecialchars($current_room_id); ?></strong>
            • Select your desired new room below.
        </p>
    </section>

    <form method="post" action="room_change.php" class="bg-white rounded-xl shadow-sm border border-gray-200 px-6 py-5">
        <h2 class="text-xl font-bold text-gray-900 mb-4 mt-4 text-center">Find Available Room (Gender: <?php echo htmlspecialchars(ucfirst($gender)); ?>)</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-600 mb-4 mt-4">Block</label>
                <select id="block-select" name="block" class="w-full px-3 py-2 rounded-md border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-300 focus:border-blue-500 transition">
                    <option value="" selected disabled>Choose a block</option>
                    <?php foreach ($available_blocks as $block): ?>
                        <option value="<?php echo htmlspecialchars($block); ?>"><?php echo htmlspecialchars($block); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-600 mb-4 mt-4">Floor</label>
                <select id="floor-select" name="floor" class="w-full px-3 py-2 rounded-md border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-300 focus:border-blue-500 transition" disabled>
                    <option value="" selected disabled>Choose a floor</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-600 mb-4 mt-4">Room</label>
                <select id="room-select" name="room" class="w-full px-3 py-2 rounded-md border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-300 focus:border-blue-500 transition" disabled>
                    <option value="" selected disabled>Choose a room</option>
                </select>
            </div>
        </div>
        
        <input type="hidden" name="partition_no" id="partition-input" value="1">

        <button id="searchBtn" type="submit" class="mt-10 mb-4 w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-3 text-sm rounded-md transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-blue-300" disabled>
            Submit Room Change Request
        </button>
    </form>

    <div id="room-visualization-container" class="bg-white rounded-xl shadow-sm border border-gray-200 px-6 py-5 hidden">
        <h2 class="text-2xl font-bold text-slate-700 mb-5 text-center">Room Layout: <span id="visual-room-id" class="text-blue-600"></span></h2>
        <div class="floor-plan-wrapper" data-plan-id="1">
            <div class="p-4 bg-slate-50 border-2 border-slate-200 rounded-xl">
                <div class="grid grid-cols-4 grid-rows-3 gap-2 md:gap-4 aspect-[4/3]">
                    <div class="flex items-center justify-center text-sm font-semibold text-slate-500 bg-slate-200 rounded-lg">WALL</div>
                    <div class="flex items-center justify-center text-sm font-semibold text-slate-500 bg-slate-200 rounded-lg">ENTRANCE</div>
                    <div class="flex items-center justify-center text-sm font-semibold text-red-500 bg-red-100 border-2 border-red-200 rounded-lg col-span-1">TOILET</div>
                    <div class="partition available partition-6">Partition 6</div>

                    <div class="partition available partition-1">Partition 1</div>
                    <div class="col-span-2 row-span-1"></div> <div class="partition available partition-5">Partition 5</div>

                    <div class="partition available partition-2">Partition 2</div>
                    <div class="partition available partition-3">Partition 3</div>
                    <div class="partition available partition-4">Partition 4</div>
                    <div class="flex items-center justify-center text-sm font-semibold text-slate-500 bg-slate-200 rounded-lg">WALL</div>
                </div>
            </div>

            <div class="mt-6">
                <div class="flex flex-wrap justify-center items-center gap-4 md:gap-6 text-sm">
                    <div class="flex items-center gap-2"><div class="w-5 h-5 rounded-full bg-emerald-200 border-2 border-emerald-400"></div><span>Available</span></div>
                    <div class="flex items-center gap-2"><div class="w-5 h-5 rounded-full bg-blue-500 border-2 border-blue-700"></div><span>Selected</span></div>
                </div>
                <div id="selectionInfo" class="text-center text-lg font-medium text-slate-700 mt-6">
                    Your selection: <span class="font-bold text-blue-600">Partition 1</span>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        const availableRooms = <?php echo $json_available_rooms; ?>;
        const availableFloors = <?php echo $json_available_floors; ?>;
        const blockSelect = document.getElementById('block-select');
        const floorSelect = document.getElementById('floor-select');
        const roomSelect = document.getElementById('room-select');
        const submitBtn = document.getElementById('searchBtn');
        const partitionInput = document.getElementById('partition-input');
        const visualizationContainer = document.getElementById('room-visualization-container');
        const visualRoomId = document.getElementById('visual-room-id');
        const selectionInfoSpan = document.querySelector('#selectionInfo span');

        // Initial default partition selection and visual update
        let selectedPartition = '1';
        
        function updatePartitionVisualization(partitionNumber = '1') {
            document.querySelectorAll('.partition').forEach(p => p.classList.remove('selected'));
            const selectedVisual = document.querySelector(`.partition-${partitionNumber}`);
            if (selectedVisual) {
                selectedVisual.classList.add('selected');
                selectionInfoSpan.textContent = `Partition ${partitionNumber}`;
            }
            partitionInput.value = partitionNumber;
        }

        function populateDropdown(dropdown, items, selectedValue = '') {
            dropdown.innerHTML = '<option value="" selected disabled>Choose a ' + dropdown.id.split('-')[0] + '</option>';
            if (items && items.length > 0) {
                dropdown.disabled = false;
                items.forEach(item => {
                    const option = document.createElement('option');
                    option.value = item;
                    option.textContent = item;
                    if (item === selectedValue) {
                        option.selected = true;
                    }
                    dropdown.appendChild(option);
                });
            } else {
                 dropdown.disabled = true;
            }
        }

        function handleBlockChange() {
            const block = blockSelect.value;
            populateDropdown(floorSelect, availableFloors[block] || []);
            populateDropdown(roomSelect, []);
            floorSelect.value = '';
            roomSelect.value = '';
            submitBtn.disabled = true;
            visualizationContainer.classList.add('hidden');
        }

        function handleFloorChange() {
            const block = blockSelect.value;
            const floor = floorSelect.value;
            populateDropdown(roomSelect, availableRooms[block] ? availableRooms[block][floor] || [] : []);
            roomSelect.value = '';
            submitBtn.disabled = true;
            visualizationContainer.classList.add('hidden');
        }

        function handleRoomChange() {
            const block = blockSelect.value;
            const floor = floorSelect.value;
            const room = roomSelect.value;

            if (block && floor && room) {
                submitBtn.disabled = false;
                
                // Show Visualization
                const roomIdentifier = `${block}-${floor}-${String(room).padStart(3, '0')}`;
                visualRoomId.textContent = roomIdentifier;
                visualizationContainer.classList.remove('hidden');

                // Default selection to Partition 1, as requested
                updatePartitionVisualization('1'); 
            } else {
                submitBtn.disabled = true;
                visualizationContainer.classList.add('hidden');
            }
        }

        blockSelect.addEventListener('change', handleBlockChange);
        floorSelect.addEventListener('change', handleFloorChange);
        roomSelect.addEventListener('change', handleRoomChange);


        // Add interactivity to the static partitions (for view purposes)
        document.querySelectorAll('.partition.available').forEach(partition => {
            partition.classList.add('flex', 'items-center', 'justify-center', 'font-semibold', 'text-slate-800', 'rounded-lg', 'border-2', 'transition-all', 'duration-300', 'ease-in-out', 'text-center', 'p-2', 'bg-emerald-200', 'border-emerald-400', 'cursor-pointer', 'hover:bg-emerald-300', 'hover:shadow-md', 'hover:-translate-y-1');

            partition.addEventListener('click', function() {
                const partitionNumber = this.textContent.match(/\d+/)[0];
                updatePartitionVisualization(partitionNumber);
            });
        });
        
    </script>
</div>
</body>
</html>