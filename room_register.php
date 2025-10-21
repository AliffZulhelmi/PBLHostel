<?php

require 'conn.php';

$student_id = $_GET['student_id'];

function autoAssignRoom($student_id) {
    // Get student gender using function from conn.php
    $gender = getUserGender($student_id);

    // Allowed blocks based on gender
    $blocks = ($gender == 'male') ? "'A4','A5'" : "'A1'";

    // Find an available room using function from conn.php
    $room = findAvailableRoom($blocks);

    if ($room === null) {
        return "No available room found for this student.";
    }

    $room_id = $room['room_id'];
    $capacity = (int)$room['partition_capacity'];

    // Assign student using function from conn.php
    assignStudentToRoom($student_id, $room_id);

    // Check if room full now using function from conn.php
    $currentCount = getRoomCurrentOccupancy($room_id);

    if ($currentCount >= $capacity) {
        // Update room status using function from conn.php
        updateRoomStatusToOccupied($room_id);
    }

    return "Assigned student $student_id to room $room_id successfully.";
}
?>