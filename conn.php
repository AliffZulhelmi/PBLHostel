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

// NEW: Fetches available room details based on gender
function getAvailableRoomsByGender($gender) {
    global $conn;
    // Allowed blocks based on student_dashboard logic
    $blocks = ($gender == 'male') ? "'A4','A5'" : "'A1'";

    $query = "
        SELECT room_identifier, block_id, floor_no, room_no
        FROM rooms
        WHERE block_id IN ($blocks) AND available_capacity > 0
        ORDER BY block_id, floor_no, room_no";
    
    $result = $conn->query($query);
    $data = [];
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
    }
    return $data;
}

// NEW: Submits a room change request to the tickets table for admin processing
function submitRoomChangeRequest($student_id, $current_room_id, $new_room_id) {
    global $conn;
    $description = "REQUEST: Student $student_id requests room change from $current_room_id to $new_room_id.";
    $sql = "INSERT INTO tickets (student_id, category, description, status)
            VALUES ('$student_id', 'Room Change', '$description', 'Pending')";
    return $conn->query($sql);
}

// FIXED: Uses string $student_id and new string $room_identifier
function assignstudenttoroom($student_id, $room_identifier) {
    global $conn;
    $sql = "INSERT INTO student_rooms (student_id, room_identifier, semester, status)
            VALUES ('$student_id', '$room_identifier', '2025/1', 'Active')";
    return $conn->query($sql);
}

// NEW: Updates room capacity and status after a student checks out
function updateRoomCapacityAfterCheckout($room_identifier) {
    global $conn;
    // Increment available_capacity by 1
    $conn->query("UPDATE rooms SET available_capacity = available_capacity + 1 WHERE room_identifier = '$room_identifier'");

    // Set status to 'Available' if it was 'Occupied' (since there is now at least 1 spot)
    return $conn->query("UPDATE rooms SET status='Available' WHERE room_identifier='$room_identifier' AND status='Occupied'");
}

// NEW: Updates student_rooms status to 'Released'
function checkoutStudentFromRoom($student_id, $room_identifier) {
    global $conn;
    $current_time = date("Y-m-d H:i:s");
    $sql = "UPDATE student_rooms 
            SET status = 'Released', released_at = '$current_time'
            WHERE student_id = '$student_id' AND room_identifier = '$room_identifier' AND status = 'Active'";
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

// ------------------------------------
// Admin Dashboard Functions (NEW)
// ------------------------------------

function getAllRoomsStatus() {
    global $conn;
    $query = "SELECT room_identifier, block_id, floor_no, room_no, total_capacity, available_capacity, status 
              FROM rooms 
              ORDER BY room_identifier";
    $result = $conn->query($query);
    $data = [];
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
    }
    return $data;
}

function getRoomChangeRequests() {
    global $conn;
    $query = "SELECT t.ticket_id, t.student_id, t.description, t.created_at, t.status, u.full_name, u.gender
              FROM tickets t
              JOIN users u ON t.student_id = u.student_id
              WHERE t.category = 'Room Change'
              ORDER BY t.created_at DESC";
    $result = $conn->query($query);
    $data = [];
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
    }
    return $data;
}

function getRoomRegisterRecords() {
    global $conn;
    $query = "SELECT sr.sr_id, sr.student_id, sr.room_identifier, sr.semester, sr.status as assignment_status, sr.assigned_at, u.full_name
              FROM student_rooms sr
              JOIN users u ON sr.student_id = u.student_id
              ORDER BY sr.assigned_at DESC";
    $result = $conn->query($query);
    $data = [];
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
    }
    return $data;
}

function getRoomCapacitySummary() {
    global $conn;
    $query = "SELECT block_id, SUM(total_capacity) as total, SUM(available_capacity) as available
              FROM rooms
              GROUP BY block_id";
    $result = $conn->query($query);
    $data = [];
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
    }
    return $data;
}
?>