<?php
session_start();
require_once __DIR__ . '/conn.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'student') {
    header('Location: login.php');
    exit;
}

// Expect these session values to exist
$userId = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : 0; // assuming users.id stored at login
$gender = isset($_SESSION['gender']) ? $_SESSION['gender'] : '';

// Fallback: if userId missing, try to fetch via email if available
if ($userId === 0 && isset($_SESSION['email'])) {
    $email = $conn->real_escape_string($_SESSION['email']);
    $res = $conn->query("SELECT id, gender FROM users WHERE email = '$email' LIMIT 1");
    if ($res && $row = $res->fetch_assoc()) {
        $userId = (int)$row['id'];
        if ($gender === '' && isset($row['gender'])) {
            $gender = $row['gender'];
        }
    }
}

if ($userId === 0) {
    $_SESSION['flash_error'] = 'Unable to identify current user.';
    header('Location: student_dashboard.php');
    exit;
}

// Simple semester input; could be from GET/POST or default
$semester = isset($_POST['semester']) ? $_POST['semester'] : 'Semester 1';

$result = autoRegisterRoom($userId, $gender, $semester);

if ($result['success']) {
    $_SESSION['flash_success'] = 'Successfully assigned to room ID ' . $result['room_id'];
} else {
    $_SESSION['flash_error'] = $result['message'];
}

header('Location: student_dashboard.php');
exit;

?>


