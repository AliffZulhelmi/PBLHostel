<?php
session_start();

if (isset($_SESSION['role'])) {
    if ($_SESSION['role'] === 'admin') {
        header('Location: admin_dashboard.php');
        exit;
    } elseif ($_SESSION['role'] === 'student') {
        header('Location: student_dashboard.php');
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <title>Hostel Management System</title>
</head>
<body>
    <h2>Welcome to the Hostel Management System</h2>
    <p><a href="login.php" style="">Login</a> | <a href="register.php">Register</a></p>
</body>
</html>