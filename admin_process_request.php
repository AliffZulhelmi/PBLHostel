<?php
session_start();
require_once 'conn.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin' || !isset($_GET['action']) || !isset($_GET['ticket_id'])) {
    header('Location: admin_dashboard.php');
    exit;
}

$action = $_GET['action'];
$ticket_id = (int)$_GET['ticket_id'];

$redirect_url = 'admin_change_requests.php';

if ($action === 'approve') {
    $details = getTicketDetails($ticket_id);
    if ($details) {
        // Extract room IDs from the description string
        preg_match("/from\s(.*?)\sto\s(.*?)\./", $details['description'], $matches);
        $old_room_id = $matches[1] ?? null;
        $new_room_id = $matches[2] ?? null;
        $student_id = $details['student_id'];

        if ($old_room_id && $new_room_id) {
            $result = approveRoomChange($student_id, $old_room_id, $new_room_id, $ticket_id);
            if ($result) {
                $_SESSION['admin_message'] = "Room change for $student_id approved. Room $old_room_id released, $new_room_id assigned.";
                // Store message for student dashboard
                $_SESSION['student_message'] = "Your room change request (Ticket #$ticket_id) has been APPROVED! You are now assigned to room $new_room_id.";
            } else {
                $_SESSION['admin_message'] = "Error: Approval failed for Ticket #$ticket_id. Database rollback occurred.";
            }
        } else {
            $_SESSION['admin_message'] = "Error: Ticket details for approval are malformed.";
        }
    } else {
        $_SESSION['admin_message'] = "Error: Ticket not found or not a room change request.";
    }

} elseif ($action === 'reject') {
    $result = rejectRoomChange($ticket_id);
    if ($result) {
        $details = getTicketDetails($ticket_id);
        $_SESSION['admin_message'] = "Room change request (Ticket #$ticket_id) has been successfully rejected.";
        // Store message for student dashboard
        $_SESSION['student_message'] = "Your room change request (Ticket #$ticket_id) has been REJECTED.";
    } else {
        $_SESSION['admin_message'] = "Error: Rejection failed for Ticket #$ticket_id.";
    }
}

header("Location: $redirect_url");
exit;
?>