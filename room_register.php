<?php
session_start();
require 'conn.php';

// Check for session, student role, and the crucial numeric user_id
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'student' || !isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Use the numeric user_id from the session for all DB operations
$user_id = $_SESSION['user_id'];

// 1. Check if the student already has an active assignment (prevents duplicate assignment)
$check_sql = "SELECT sr_id FROM student_rooms WHERE student_id = '$user_id' AND status = 'Active' LIMIT 1";
$check_res = $conn->query($check_sql);

if ($check_res && $check_res->num_rows > 0) {
    $_SESSION['room_message'] = "You already have an active room assignment. Automatic assignment cancelled.";
    header('Location: student_dashboard.php');
    exit;
}

// 2. Get student gender (uses fixed getUserGender in conn.php)
$gender = getUserGender($user_id);

if (!$gender) {
    $_SESSION['room_message'] = "Error: Student gender could not be determined. Assignment failed.";
    header('Location: student_dashboard.php');
    exit;
}

// 3. Allowed blocks based on gender
$blocks = ($gender == 'male') ? "'A4','A5'" : "'A1'";

// 4. Find an available room (uses fixed findAvailableRoom in conn.php)
$room = findAvailableRoom($blocks);

if ($room === null) {
    $_SESSION['room_message'] = "No available room found for $gender students in blocks ($blocks).";
    header('Location: student_dashboard.php');
    exit;
}

$room_id = $room['room_id'];
$capacity = (int)$room['partition_capacity'];

// 5. Assign student to room (uses assignstudenttoroom in conn.php)
if (assignstudenttoroom($user_id, $room_id)) {
    
    // 6. Check occupancy and update room status if full
    $currentCount = getRoomCurrentOccupancy($room_id);

    if ($currentCount >= $capacity) {
        // updates room status in rooms table
        updateroomstatustooccupied($room_id);
        $status_msg = " and Room status updated to Occupied.";
    } else {
        $status_msg = ".";
    }
    
    $_SESSION['room_message'] = "Room registration successful! Assigned to Room ID $room_id" . $status_msg;
    
} else {
    $_SESSION['room_message'] = "Room registration failed. Database operation failed during assignment.";
}

// Redirect to dashboard in all successful and error cases (if they reach this point)
header('Location: student_dashboard.php');
exit;
?>