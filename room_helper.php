<?php
// room_helper.php

// This file encapsulates the logic for determining an available room
// based on gender, current room occupation, and capacity.

function findAvailableRoomId($gender) {
    global $conn;

    // Map allowed blocks by gender per requirement
    $allowedBlocks = [];
    if (strtolower($gender) === 'male') {
        $allowedBlocks = ["A2", "A3", "A4", "A5"];
    } else if (strtolower($gender) === 'female') {
        $allowedBlocks = ["A1", "A7"];
    } else {
        return null;
    }

    $inClause = "'" . implode("','", $allowedBlocks) . "'";

    // Choose the first room in allowed blocks where active occupants < capacity
    // We do not require prepared statements as per instruction
    $sql = "
        SELECT r.room_id,
               r.partition_capacity,
               COUNT(sr.sr_id) AS active_count
        FROM rooms r
        LEFT JOIN student_rooms sr
            ON sr.room_id = r.room_id AND sr.status = 'Active'
        WHERE r.block_id IN ($inClause)
        GROUP BY r.room_id, r.partition_capacity
        HAVING active_count < CAST(r.partition_capacity AS UNSIGNED)
        ORDER BY r.block_id, r.floor_no, r.room_no
        LIMIT 1
    ";

    $result = $conn->query($sql);
    if ($result && $row = $result->fetch_assoc()) {
        return (int)$row['room_id'];
    }

    return null;
}

function updateRoomStatusByOccupancy($roomId) {
    global $conn;

    // Fetch capacity and current active count
    $capRes = $conn->query("SELECT partition_capacity FROM rooms WHERE room_id = " . (int)$roomId . " LIMIT 1");
    if (!$capRes || !$capRow = $capRes->fetch_assoc()) {
        return false;
    }
    $capacity = (int)$capRow['partition_capacity'];

    $cntRes = $conn->query("SELECT COUNT(*) AS c FROM student_rooms WHERE room_id = " . (int)$roomId . " AND status = 'Active'");
    $count = ($cntRes && $cntRow = $cntRes->fetch_assoc()) ? (int)$cntRow['c'] : 0;

    $newStatus = ($count >= $capacity) ? 'Occupied' : 'Unoccupied';
    $conn->query("UPDATE rooms SET status = '" . $newStatus . "' WHERE room_id = " . (int)$roomId . " LIMIT 1");
    return true;
}

?>


