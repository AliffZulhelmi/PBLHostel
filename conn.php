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
    return $user;
}

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

function getAvailableRoomsByGender($gender) {
    global $conn;
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

function submitRoomChangeRequest($student_id, $current_room_id, $new_room_id) {
    global $conn;
    $description = "REQUEST: Student $student_id requests room change from $current_room_id to $new_room_id.";
    $sql = "INSERT INTO tickets (student_id, category, description, status)
            VALUES ('$student_id', 'Room Change', '$description', 'Pending')";
    return $conn->query($sql);
}

function assignstudenttoroom($student_id, $room_identifier) {
    global $conn;
    $sql = "INSERT INTO student_rooms (student_id, room_identifier, semester, status)
            VALUES ('$student_id', '$room_identifier', '2025/1', 'Active')";
    return $conn->query($sql);
}

function updateRoomCapacityAfterCheckout($room_identifier) {
    global $conn;
    $conn->query("UPDATE rooms SET available_capacity = available_capacity + 1 WHERE room_identifier = '$room_identifier'");
    return $conn->query("UPDATE rooms SET status='Available' WHERE room_identifier='$room_identifier' AND status='Occupied'");
}

function checkoutStudentFromRoom($student_id, $room_identifier) {
    global $conn;
    $current_time = date("Y-m-d H:i:s");
    $sql = "UPDATE student_rooms 
            SET status = 'Released', released_at = '$current_time'
            WHERE student_id = '$student_id' AND room_identifier = '$room_identifier' AND status = 'Active'";
    return $conn->query($sql);
}

function updateRoomCapacityAfterAssignment($room_identifier) {
    global $conn;
    $conn->query("UPDATE rooms SET available_capacity = available_capacity - 1 WHERE room_identifier = '$room_identifier'");
    $checkSql = "SELECT available_capacity FROM rooms WHERE room_identifier = '$room_identifier'";
    $result = $conn->query($checkSql);
    $capacity = $result ? $result->fetch_assoc()['available_capacity'] : 0;

    if ($capacity <= 0) {
        return $conn->query("UPDATE rooms SET status='Occupied' WHERE room_identifier='$room_identifier'");
    }
    return true;
}

// ------------------------------------
// Admin & Complaint Functions (NEW)
// ------------------------------------

function getTicketDetails($ticket_id) {
    global $conn;
    $query = "SELECT student_id, description FROM tickets WHERE ticket_id = $ticket_id AND category = 'Room Change' LIMIT 1";
    $result = $conn->query($query);
    return $result ? $result->fetch_assoc() : null;
}

function approveRoomChange($student_id, $old_room_id, $new_room_id, $ticket_id) {
    global $conn;
    $conn->autocommit(FALSE);
    $success = true;

    // 1. Mark current assignment as Released
    $current_time = date("Y-m-d H:i:s");
    $sql_release = "UPDATE student_rooms 
                    SET status = 'Released', released_at = '$current_time'
                    WHERE student_id = '$student_id' AND room_identifier = '$old_room_id' AND status = 'Active'";
    if (!$conn->query($sql_release)) { $success = false; }
    
    // 2. Update OLD room capacity (+1) and status ('Available')
    if ($success) {
        $sql_old_capacity = "UPDATE rooms SET available_capacity = available_capacity + 1, status = 'Available' 
                             WHERE room_identifier = '$old_room_id'";
        if (!$conn->query($sql_old_capacity)) { $success = false; }
    }
    
    // 3. Create new assignment (Active)
    if ($success) {
        $sql_assign = "INSERT INTO student_rooms (student_id, room_identifier, semester, status)
                       VALUES ('$student_id', '$new_room_id', '2025/1', 'Active')";
        if (!$conn->query($sql_assign)) { $success = false; }
    }
    
    // 4. Update NEW room capacity (-1) and check if full
    if ($success) {
        $sql_new_capacity = "UPDATE rooms SET available_capacity = available_capacity - 1 
                             WHERE room_identifier = '$new_room_id'";
        if (!$conn->query($sql_new_capacity)) { $success = false; }
        
        $checkSql = "SELECT available_capacity FROM rooms WHERE room_identifier = '$new_room_id'";
        $result = $conn->query($checkSql);
        $capacity = $result ? $result->fetch_assoc()['available_capacity'] : 1;
        if ($capacity <= 0) {
            $sql_full = "UPDATE rooms SET status='Occupied' WHERE room_identifier='$new_room_id'";
            if (!$conn->query($sql_full)) { $success = false; }
        }
    }
    
    // 5. Update ticket status
    if ($success) {
        $sql_ticket = "UPDATE tickets SET status = 'Approved' WHERE ticket_id = $ticket_id";
        if (!$conn->query($sql_ticket)) { $success = false; }
    }

    if ($success) {
        $conn->commit();
        return true;
    } else {
        $conn->rollback();
        return false;
    }
}

function rejectRoomChange($ticket_id) {
    global $conn;
    $sql = "UPDATE tickets SET status = 'Rejected' WHERE ticket_id = $ticket_id";
    return $conn->query($sql);
}

function submitComplaint($student_id, $category, $description, $attachment_path = NULL) {
    global $conn;
    $status = ($category == 'Room Change') ? 'Pending' : 'Open';
    $sql = "INSERT INTO tickets (student_id, category, description, attachment_path, status)
            VALUES ('$student_id', '$category', '$description', '$attachment_path', '$status')";
    return $conn->query($sql);
}

function getStudentComplaints($student_id) {
    global $conn;
    $query = "SELECT ticket_id, category, description, created_at, status, attachment_path FROM tickets 
              WHERE student_id = '$student_id' AND category != 'Room Change'
              ORDER BY created_at DESC";
    $result = $conn->query($query);
    $data = [];
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
    }
    return $data;
}

function getAllComplaints() {
    global $conn;
    $query = "SELECT t.ticket_id, t.student_id, t.category, t.description, t.attachment_path, t.created_at, t.status, u.full_name
              FROM tickets t
              JOIN users u ON t.student_id = u.student_id
              WHERE t.category != 'Room Change'
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

function updateTicketStatus($ticket_id, $status) {
    global $conn;
    $sql = "UPDATE tickets SET status = '$status', updated_at = CURRENT_TIMESTAMP WHERE ticket_id = $ticket_id";
    return $conn->query($sql);
}


// Admin Dashboard Functions (Objective 2 & 3)

function getRoomsGroupedByBlock() {
    global $conn;
    $query = "SELECT room_identifier, block_id, floor_no, room_no, total_capacity, available_capacity, status 
              FROM rooms 
              ORDER BY block_id, room_identifier";
    $result = $conn->query($query);
    $grouped_data = [];
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $block = $row['block_id'];
            if (!isset($grouped_data[$block])) {
                $grouped_data[$block] = [];
            }
            $grouped_data[$block][] = $row;
        }
    }
    return $grouped_data;
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
            preg_match("/from\s(.*?)\sto\s(.*?)\./", $row['description'], $matches);
            $row['old_room_id'] = $matches[1] ?? 'N/A';
            $row['new_room_id'] = $matches[2] ?? 'N/A';
            $data[] = $row;
        }
    }
    return $data;
}

function getRoomRegisterRecords() {
    global $conn;
    $query = "SELECT sr.sr_id, sr.student_id, sr.room_identifier, sr.semester, sr.status as assignment_status, sr.assigned_at, sr.released_at, u.full_name
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