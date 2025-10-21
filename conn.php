<?php
// conn.php

// Database configuration
$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'hostel';

// Establish database connection
$conn = new mysqli($host, $user, $pass, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// ------------------------------------
// Base User/Auth Functions
// ------------------------------------

function register($full_name, $student_id, $email, $phone, $password, $role, $gender) {
    global $conn;
    $sql = "INSERT INTO users (full_name, student_id, email, phone, password, role, gender)
             VALUES ('$full_name', '$student_id', '$email', '$phone', '$password', '$role', '$gender')";
    return $conn->query($sql);
}

function login($email, $password) {
    global $conn;
    // Note: Admin login is via email and a NULL student_id (changed in SQL to 'ADMIN001' now)
    $sql = "SELECT * FROM users WHERE email = '$email' AND password = '$password'";
    $result = $conn->query($sql);
    $user = $result ? $result->fetch_assoc() : false;
    return $user;
}

// FIXED: Queries by the string student_id (new PK)
function getUserGender($student_id) {
    global $conn;
    $genderQuery = $conn->query("SELECT gender FROM users WHERE student_id = '$student_id'");
    return $genderQuery ? $genderQuery->fetch_assoc()['gender'] : null;
}

// ------------------------------------
// Room Management Functions
// ------------------------------------

// FIXED: Searches for rooms where available_capacity is greater than 0
// RETURNS: room_identifier and available_capacity
function findavailableroom($blocks) {
    global $conn;
    $roomQuery = $conn->query("
        SELECT room_identifier, available_capacity
        FROM rooms
        WHERE block_id IN ($blocks)
        AND available_capacity > 0
        ORDER BY room_identifier LIMIT 1
    ");
    return $roomQuery && $roomQuery->num_rows > 0 ? $roomQuery->fetch_assoc() : null;
}

// FIXED: Uses string $student_id and new string $room_identifier
function assignstudenttoroom($student_id, $room_identifier) {
    global $conn;
    $sql = "INSERT INTO student_rooms (student_id, room_identifier, semester, status)
            VALUES ('$student_id', '$room_identifier', '2025/1', 'Active')";
    return $conn->query($sql);
}

// NEW: Updates room capacity and status after a student is assigned
function updateRoomCapacityAfterAssignment($room_identifier) {
    global $conn;
    // Decrement available_capacity by 1
    $conn->query("UPDATE rooms SET available_capacity = available_capacity - 1 WHERE room_identifier = '$room_identifier'");

    // Check if room is now full
    $checkSql = "SELECT available_capacity FROM rooms WHERE room_identifier = '$room_identifier'";
    $result = $conn->query($checkSql);
    $capacity = $result ? $result->fetch_assoc()['available_capacity'] : 0;

    if ($capacity <= 0) {
        // Change status to 'Occupied' (Full)
        return $conn->query("UPDATE rooms SET status='Occupied' WHERE room_identifier='$room_identifier'");
    }
    return true;
}

function selectAllUser() {
    global $conn;
    $result = $conn->query("SELECT full_name, student_id, email, phone, role, gender, created_at FROM users");
    $users = [];
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $users[] = $row;
        }
    }
    return $users;
}

?>