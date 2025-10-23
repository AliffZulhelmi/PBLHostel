<?php
session_start();
require 'conn.php';

// Check for session and student role
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'student' || !isset($_SESSION['student_id'])) {
    header('Location: login.php');
    exit;
}

// Use the string student_id from the session for all DB operations
$student_id = $_SESSION['student_id'];

// 1. Check if the student already has an active assignment (using new student_id FK)
$check_sql = "SELECT sr_id FROM student_rooms WHERE student_id = '$student_id' AND status = 'Active' LIMIT 1";
$check_res = $conn->query($check_sql);

if ($check_res && $check_res->num_rows > 0) {
    $_SESSION['room_message'] = "You already have an active room assignment. Automatic assignment cancelled.";
    header('Location: student_dashboard.php');
    exit;
}

// 2. Get student gender
$gender = getUserGender($student_id);

if (!$gender) {
    $_SESSION['room_message'] = "Error: Student gender could not be determined. Assignment failed.";
    header('Location: student_dashboard.php');
    exit;
}

// 3. Allowed blocks based on gender
$blocks = ($gender == 'male') ? "'A4','A5'" : "'A1'";

// 4. Find an available room (gets room_identifier and current capacity)
$room = findAvailableRoom($blocks);

if ($room === null) {
    $_SESSION['room_message'] = "No available room found for $gender students in blocks ($blocks).";
    header('Location: student_dashboard.php');
    exit;
}

$room_identifier = $room['room_identifier'];
$capacity_before_assignment = (int)$room['available_capacity'];

// 5. Assign student to room
if (assignstudenttoroom($student_id, $room_identifier)) {
    
    // 6. Update capacity and room status (New function)
    updateRoomCapacityAfterAssignment($room_identifier);

    // Determine success message based on old capacity count
    if ($capacity_before_assignment <= 1) { // If capacity was 1 before assignment, it's now 0/full
        $status_msg = " and Room status updated to Occupied.";
    } else {
        $status_msg = ".";
    }
    
    $_SESSION['room_message'] = "Room registration successful! Assigned to Room ID $room_identifier" . $status_msg;
    
} else {
    $_SESSION['room_message'] = "Room registration failed. Database operation failed during assignment.";
}

// Redirect to dashboard
header('Location: student_dashboard.php');
exit;
?>