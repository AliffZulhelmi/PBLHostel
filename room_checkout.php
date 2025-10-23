<?php
session_start();
require 'conn.php';

// Check for session and student role
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'student' || !isset($_SESSION['student_id'])) {
    header('Location: login.php');
    exit;
}

// Ensure the room identifier is passed 
if (!isset($_GET['room_identifier'])) {
    $_SESSION['room_message'] = "Error: Room identifier is missing for checkout.";
    header('Location: student_dashboard.php');
    exit;
}

$student_id = $_SESSION['student_id'];
$room_identifier = $_GET['room_identifier'];

// 1. Update student_rooms status to 'Released' and set released_at timestamp
if (checkoutStudentFromRoom($student_id, $room_identifier)) {
    
    // 2. Update capacity and room status
    updateRoomCapacityAfterCheckout($room_identifier);
    
    $_SESSION['room_message'] = "Checkout successful! Your room assignment ($room_identifier) has been released.";
    
} else {
    $_SESSION['room_message'] = "Checkout failed. No active room assignment found for this student or database error.";
}

// Redirect to dashboard
header('Location: student_dashboard.php');
exit;
?>