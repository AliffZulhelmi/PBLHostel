<?php
session_start();

require_once 'conn.php';

// If user is not logged in or not a student, redirect to login page
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'student' || !isset($_SESSION['student_id'])) {
    header('Location: login.php');
    exit;
}

// Extract session information
$full_name = isset($_SESSION['full_name']) ? $_SESSION['full_name'] : 'Name not found! DB issue';
$student_id = $_SESSION['student_id']; // The string ID (PK) for all DB queries

// Fetch accommodation info for this student
$has_active_assignment = false;
$room_details = null;

if ($student_id) {
    // FIXED: Queries new columns total_capacity and available_capacity
    $sql = "SELECT sr.*, r.block_id, r.floor_no as floor, r.room_no as room_number, r.total_capacity, r.available_capacity, r.status as room_status
            FROM student_rooms sr
            LEFT JOIN rooms r ON sr.room_identifier = r.room_identifier
            WHERE sr.student_id = '$student_id' AND sr.status = 'Active'
            LIMIT 1";
    $res = $conn->query($sql);
    if ($res && $res->num_rows > 0) {
        $room_details = $res->fetch_assoc();
        $has_active_assignment = true;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    
    <style>
        body {
            /* Using Inter as a modern, clean font, similar to Segoe UI */
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>
<body class="bg-gray-100 text-gray-900 m-0 p-0">

    <header class="bg-gray-800 text-white py-3 px-4 flex items-center justify-between shadow-lg">
        
        <div class="flex items-center">
            
            <div class="mr-4">
                <img src="https://placehold.co/60x60/374151/FFFFFF?text=GMI" alt="GMI Logo" class="h-[60px] rounded-lg">
            </div>
            
            <div>
                <h1 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold m-0">
                    Hostel Dashboard
                </h1>
            </div>
        </div>

        <div class="hidden sm:block text-right p-2 rounded-lg bg-gray-700 ml-4">
            <p class="text-base sm:text-lg font-semibold text-indigo-400">
                Hello, <strong><?php echo htmlspecialchars($full_name); ?></strong>
            </p>
            <p class="text-sm text-gray-300">
                ID: <strong><?php echo htmlspecialchars($student_id); ?></strong>
            </p>
        </div>
    </header>

<div class="max-w-[900px] mx-auto my-10 p-5 bg-white rounded-xl shadow-2xl">

    <?php 
    // Display all session messages (new and old)
    if (isset($_SESSION['room_message'])): ?>
        <div class="mb-4 p-4 rounded-lg bg-green-100 text-green-700 border border-green-300">
            <?php echo htmlspecialchars($_SESSION['room_message']); ?>
        </div>
        <?php unset($_SESSION['room_message']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['admin_message'])): ?>
        <?php $msg = $_SESSION['admin_message']; 
        $is_error = strpos($msg, 'rejected') !== false || strpos($msg, 'failed') !== false;
        $color = $is_error ? 'bg-red-100 text-red-700 border-red-300' : 'bg-green-100 text-green-700 border-green-300';
        ?>
        <div class="mb-4 p-4 rounded-lg <?php echo $color; ?> border">
            <?php echo htmlspecialchars($msg); ?>
        </div>
        <?php unset($_SESSION['admin_message']); ?>
    <?php endif; ?>


    <section class="mb-8">
        <h2 class="text-2xl font-semibold text-gray-800 mb-4 border-b pb-2">Current Room Assignment</h2>
            
            <div class="bg-indigo-50 p-4 rounded-lg space-y-2 border border-indigo-200">
                <?php if ($has_active_assignment && $room_details) { ?>
                    <p><strong class="text-indigo-700">Block:</strong> <?php echo htmlspecialchars($room_details['block_id']); ?></p>
                    <p><strong class="text-indigo-700">Floor:</strong> <?php echo htmlspecialchars($room_details['floor']); ?></p>
                    <p><strong class="text-indigo-700">Room Number:</strong> <?php echo htmlspecialchars($room_details['room_number']); ?></p>
                    <p><strong class="text-indigo-700">Total Capacity:</strong> <?php echo htmlspecialchars($room_details['total_capacity']); ?></p>
                    <p><strong class="text-indigo-700">Semester:</strong> <?php echo htmlspecialchars($room_details['semester']); ?></p>
                    <p><strong class="text-indigo-700">Assigned At:</strong> <?php echo htmlspecialchars($room_details['assigned_at']); ?></p>
                <?php } else { ?>
                    <p class="text-gray-500 italic">No active room assignment found.</p>
                <?php } ?>
            </div>
            
            <p class="text-sm text-gray-500 mt-3 italic">
                <?php if ($has_active_assignment && $room_details) { ?>
                    If you wish to request a room change, use the button below.
                <?php } else { ?>
                    If no room is assigned, please use the registration button below.
                <?php } ?>
            </p>
        </section>

        <section class="mb-8">
            <h2 class="text-2xl font-semibold text-gray-800 mb-4 border-b pb-2">Actions</h2>
            
            <div class="bg-gray-200 p-4 rounded-lg flex flex-wrap gap-3">
                
                <?php if (!$has_active_assignment) { ?>
                <button onclick="window.location.href='room_register.php'"
                        class="py-2.5 px-4 text-sm bg-blue-600 text-white font-medium rounded-lg cursor-pointer transition duration-300 hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 transform hover:scale-[1.01]">
                    Register Room
                </button>
                <?php } else { ?>
                <button
                    class="py-2.5 px-4 text-sm bg-gray-400 text-white font-medium rounded-lg cursor-not-allowed opacity-60"
                    disabled>
                    Register Room
                </button>
                <?php } ?>

                <?php if ($has_active_assignment) { ?>
                <button onclick="window.location.href='room_change.php?user_id=<?php echo urlencode($student_id); ?>'"
                        class="py-2.5 px-4 text-sm bg-blue-600 text-white font-medium rounded-lg cursor-pointer transition duration-300 hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 transform hover:scale-[1.01]">
                    Request Room Change
                </button>
                
                <button onclick="if(confirm('Are you sure you want to checkout from <?php echo htmlspecialchars($room_details['room_identifier']); ?>?')) { window.location.href='room_checkout.php?room_identifier=<?php echo urlencode($room_details['room_identifier']); ?>'; }"
                        class="py-2.5 px-4 text-sm bg-yellow-600 text-white font-medium rounded-lg cursor-pointer transition duration-300 hover:bg-yellow-700 focus:ring-4 focus:ring-yellow-300 transform hover:scale-[1.01]">
                    Checkout Room
                </button>

                <?php } else { ?>
                <button
                    class="py-2.5 px-4 text-sm bg-gray-400 text-white font-medium rounded-lg cursor-not-allowed opacity-60"
                    disabled>
                    Request Room Change
                </button>

                <button
                    class="py-2.5 px-4 text-sm bg-gray-400 text-white font-medium rounded-lg cursor-not-allowed opacity-60"
                    disabled>
                    Checkout Room
                </button>
                <?php } ?>

                <button onclick="window.location.href='student_file_complaint.php'"
                        class="py-2.5 px-4 text-sm bg-purple-600 text-white font-medium rounded-lg cursor-pointer transition duration-300 hover:bg-purple-700 focus:ring-4 focus:ring-purple-300 transform hover:scale-[1.01]">
                    File a Complaint
                </button>
                
                <button onclick="window.location.href='student_complaint_list.php'"
                        class="py-2.5 px-4 text-sm bg-purple-600 text-white font-medium rounded-lg cursor-pointer transition duration-300 hover:bg-purple-700 focus:ring-4 focus:ring-purple-300 transform hover:scale-[1.01]">
                    View My Complaints
                </button>
                <button onclick="window.location.href='logout.php'"
                        class="py-2.5 px-4 text-sm bg-red-500 text-white font-medium rounded-lg cursor-pointer transition duration-300 hover:bg-red-600 focus:ring-4 focus:ring-red-300 transform hover:scale-[1.01]">
                    Logout
                </button>
            </div>
        </section>
    </div>
</body>
</html>