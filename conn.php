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
    // Optimization: returning $user directly as it is an array or false/null
    return $user;
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

// FIXED: Function now accepts and queries by the numeric users.id ($user_id)
function getUserGender($user_id) {
    global $conn;
    $genderQuery = $conn->query("SELECT gender FROM users WHERE id = $user_id");
    return $genderQuery ? $genderQuery->fetch_assoc()['gender'] : null;
}

// ------------------------------------
// Room Management Functions
// ------------------------------------

// FIXED: Removed the overly strict status check 'Unoccupied'. Now only checks for available space.
function findavailableroom($blocks) {
    global $conn;
    $roomQuery = $conn->query("
        SELECT r.room_id, r.partition_capacity
        FROM rooms r
        WHERE r.block_id IN ($blocks)
        AND (
            SELECT COUNT(*) FROM student_rooms sr
            WHERE sr.room_id = r.room_id AND sr.status = 'Active'
        ) < CAST(r.partition_capacity AS UNSIGNED)
        ORDER BY r.room_id LIMIT 1
    ");
    return $roomQuery && $roomQuery->num_rows > 0 ? $roomQuery->fetch_assoc() : null;
}

// Note: parameter $student_id is interpreted as the numeric user ID from the users table.
function assignstudenttoroom($user_id, $room_id) {
    global $conn;
    return $conn->query("INSERT INTO student_rooms (student_id, room_id, semester, status)
                         VALUES ($user_id, $room_id, '2025/1', 'Active')");
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