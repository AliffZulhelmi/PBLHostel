<?php
session_start();

// If user is not logged in or not a student, redirect to login page
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'student') {
    header('Location: login.php');
    exit;
}

// Boleh tambah other session info kalau perlu
$full_name = isset($_SESSION['full _name']) ? $_SESSION['full _name'] : 'Name not found! DB issue';
$student_id = isset($_SESSION['student_id']) ? $_SESSION['student_id'] : 'StudentID not found! DB issue';

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Dashboard</title>
</head>
<body>
    <h2>Welcome to the Student Hostel Dashboard</h2>
    <p>Hello, <strong><?php echo htmlspecialchars($full_name); ?></strong>!</p>
    <p>Your Student ID: <strong><?php echo htmlspecialchars($student_id); ?></strong></p>
    <p><a href="logout.php">Logout</a></p>
    <!-- Nanti tambah features kat sini -->
</body>
</html>
