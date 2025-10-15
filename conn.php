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

// Include helpers for room selection
require_once __DIR__ . '/room_helper.php';

function register($full_name, $student_id, $email, $phone, $password, $role, $gender) {
    global $conn;
    $sql = "INSERT INTO users (full_name, student_id, email, phone, password, role, gender)
            VALUES ('$full_name', '$student_id', '$email', '$phone', '$password', '$role', '$gender')";
    return $conn->query($sql);
}

// Login function
function login($email, $password) {
    global $conn;
    $sql = "SELECT * FROM users WHERE email = '$email' AND password = '$password'";
    $result = $conn->query($sql);
    $user = $result ? $result->fetch_assoc() : false;
    return $user ? $user : false;
}

// Select all users function
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

// Auto register room for a student
// Params: $userId (users.id), $gender (users.gender), $semester (string)
function autoRegisterRoom($userId, $gender, $semester) {
    global $conn;

    $userId = (int)$userId;
    $semester = $conn->real_escape_string($semester);

    // If already has an active room, do nothing
    $existing = $conn->query("SELECT sr_id FROM student_rooms WHERE student_id = $userId AND status = 'Active' LIMIT 1");
    if ($existing && $existing->num_rows > 0) {
        return ['success' => true, 'message' => 'Already assigned'];
    }

    // Find a room id using helper (handles gender and capacity)
    $roomId = findAvailableRoomId($gender);
    if ($roomId === null) {
        return ['success' => false, 'message' => 'No available room found'];
    }

    // Assign
    $insert = $conn->query("INSERT INTO student_rooms (student_id, room_id, semester, status) VALUES ($userId, $roomId, '$semester', 'Active')");
    if (!$insert) {
        return ['success' => false, 'message' => 'Failed to assign room'];
    }

    // Update room status based on occupancy
    updateRoomStatusByOccupancy($roomId);

    return ['success' => true, 'message' => 'Room assigned', 'room_id' => $roomId];
}
?>