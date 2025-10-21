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
    $sql = "SELECT * FROM users WHERE email = '$email' AND password = '$password'";
    $result = $conn->query($sql);
    $user = $result ? $result->fetch_assoc() : false;
    return $user ? $user : false;
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

function getUserGender($student_id) {
    global $conn;
    $genderQuery = $conn->query("SELECT gender FROM users WHERE id = $student_id");
    return $genderQuery->fetch_assoc()['gender'];
}

// ------------------------------------
// Room Management Functions
// ------------------------------------

function findavailableroom($blocks) {
    global $conn;
    $roomQuery = $conn->query("
        SELECT r.room_id, r.partition_capacity
        FROM rooms r
        WHERE r.block_id IN ($blocks)
        AND r.status = 'Unoccupied'
        AND (
            SELECT COUNT(*) FROM student_rooms sr
            WHERE sr.room_id = r.room_id AND sr.status = 'Active'
        ) < r.partition_capacity
        ORDER BY r.room_id LIMIT 1
    ");
    return $roomQuery->num_rows > 0 ? $roomQuery->fetch_assoc() : null;
}

function assignstudenttoroom($student_id, $room_id) {
    global $conn;
    return $conn->query("INSERT INTO student_rooms (student_id, room_id, semester, status)
                         VALUES ($student_id, $room_id, '2025/1', 'Active')");
}

function getroomcurrentoccupancy($room_id) {
    global $conn;
    $countQuery = $conn->query("
        SELECT COUNT(*) AS count FROM student_rooms
        WHERE room_id = $room_id AND status = 'Active'
    ");
    return $countQuery->fetch_assoc()['count'];
}

function updateroomstatustooccupied($room_id) {
    global $conn;
    return $conn->query("UPDATE rooms SET status='Occupied' WHERE room_id=$room_id");
}

?>