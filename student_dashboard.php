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
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    
    <style>
        body {
            /* Using Inter as a modern, clean font, similar to Segoe UI */
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>
<body class="bg-gray-100 text-gray-900 m-0 p-0">

<?php include 'student_header.php'; ?>

<div class="max-w-[900px] mx-auto my-10 p-6 bg-white rounded-xl shadow-lg border border-gray-100">

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

    <!-- CURRENT ROOM ASSIGNMENT -->
    <section class="mb-10">
        <h2 class="text-xl font-bold text-gray-900 mb-6">Current Room Assignment</h2>
            
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 px-6 py-5">
                <?php if ($has_active_assignment && $room_details) { ?>
                    <!-- Main Room Details -->
                     <div class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div class="space-y-1">
                                <label class="text-sm font-medium text-gray-500 uppercase tracking-wide">Block</label>
                                <div class="text-lg font-bold text-gray-900"><?php echo htmlspecialchars($room_details['block_id']); ?></div>
                            </div>
                            <div class="space-y-1">
                                <label class="text-sm font-medium text-gray-500 uppercase tracking-wide">Floor</label>
                                <div class="text-lg font-bold text-gray-900"><?php echo htmlspecialchars($room_details['floor']); ?></div>
                            </div>
                            <div class="space-y-1">
                            <label class="text-sm font-medium text-gray-500 uppercase tracking-wide">Room Number</label>
                            <div class="text-lg font-bold text-gray-900"><?php echo htmlspecialchars($room_details['room_number']); ?></div>
                            </div>
                        </div>

                        <!-- Separator -->
                        <div class="border-b border-gray-200 my-6"></div>

                        <!-- Adminstrative Details -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-1">
                                <label class="text-sm font-medium text-gray-500 uppercase tracking-wide">Semester</label>
                                <div class="text-lg font-bold text-gray-900"><?php echo htmlspecialchars($room_details['semester']); ?></div>
                            </div>
                            <div class="space-y-1">
                                <label class="text-xs font-medium text-gray-400 uppercase tracking-wide">Assigned At</label>
                                <div class="text-sm text-gray-400"><?php echo htmlspecialchars($room_details['assigned_at']); ?></div>
                            </div>
                        </div>
                     </div>

                    <!-- Call to Action Notice -->
                    <div class="mt-8 mb-6">
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <p class="text-sm text-blue-800">
                                <i class="fas fa-info-circle mr-2"></i>
                                If you wish to request a room change, please use the button below to submit your request.
                            </p>
                        </div>
                    </div>

                <?php } else { ?>
                    <p class="text-gray-500 italic">No active room assignment found.</p>
                    <div class="mt-4 bg-blue-50 border border-blue-200 rounded-lg p-3 text-blue-800 text-sm">
                        If no room is assigned, please use the registration button below.
                    </div>
                <?php } ?>
        </div>
    </section>
    <!-- ACTIONS SECTION -->
        <section class="mb-10">
            <h3 class="text-lg font-semibold text-gray-900 mb-6">Actions</h3>
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-2">

                <?php if (!$has_active_assignment) { ?>
                    <!-- Register Room -->
                    <button onclick="window.location.href='room_register.php'"
                    class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-3 text-sm rounded-md transition-colors duration-200 flex items-center justify-center space-x-2">
                    <i class="fas fa-door-open"></i>
                    <span>Register Room</span>
                    </button>
                <?php } else { ?>
                    <button class="bg-gray-300 text-gray-600 font-medium py-2 px-3 text-sm rounded-md cursor-not-allowed flex items-center justify-center space-x-2" disabled>
                    <i class="fas fa-door-open"></i>
                    <span>Register Room</span>
                    </button>
                <?php } ?>

                <?php if ($has_active_assignment) { ?>
                    <!-- Request Room Change -->
                    <button onclick="window.location.href='room_change.php?user_id=<?php echo urlencode($student_id); ?>'"
                    class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-3 text-sm rounded-md border border-gray-300 transition-colors duration-200 flex items-center justify-center space-x-2">
                    <i class="fas fa-exchange-alt"></i>
                    <span>Request Room Change</span>
                    </button>

                    <!-- Checkout Room -->
                    <button onclick="if(confirm('Are you sure you want to checkout from <?php echo htmlspecialchars($room_details['room_identifier']); ?>?')) { window.location.href='room_checkout.php?room_identifier=<?php echo urlencode($room_details['room_identifier']); ?>'; }"
                    class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-3 text-sm rounded-md border border-gray-300 transition-colors duration-200 flex items-center justify-center space-x-2">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Checkout Room</span>
                    </button>

                <?php } else { ?>
                    <button class="bg-gray-300 text-gray-600 font-medium py-2 px-3 text-sm rounded-md cursor-not-allowed transition-colors duration-200 flex items-center justify-center space-x-2" disabled>
                    <i class="fas fa-exchange-alt"></i>
                    <span>Request Room Change</span>
                    </button>

                    <button class="bg-gray-300 text-gray-600 font-medium py-2 px-3 text-sm rounded-md cursor-not-allowed transition-colors duration-200 flex items-center justify-center space-x-2" disabled>
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Checkout Room</span>
                    </button>
                <?php } ?>

                    <!-- File Complaint -->
                    <button onclick="window.location.href='student_file_complaint.php'"
                    class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-3 text-sm rounded-md border border-gray-300 transition-colors duration-200 flex items-center justify-center space-x-2">
                    <i class="fas fa-exclamation-triangle"></i>
                    <span>File a Complaint</span>
                    </button>

                    <!-- View Complaints -->
                    <button onclick="window.location.href='student_complaint_list.php'"
                    class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-3 text-sm rounded-md border border-gray-300 transition-colors duration-200 flex items-center justify-center space-x-2">
                    <i class="fas fa-eye"></i>
                    <span>View My Complaints</span>
                    </button>

            </div>
        </section>
    </div>
</body>
</html>