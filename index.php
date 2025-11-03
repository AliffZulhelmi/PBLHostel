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
// If not logged in, show the welcome page below
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome - Hostel Management</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="style.css">
    <style>
        body { font-family: 'Inter', system-ui, sans-serif; }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center">

    <div class="w-full max-w-md shadow-2xl rounded-2xl px-8 py-10 bg-white border border-gray-200">
        <div class="mb-7 text-center">
            <img src="images/gmi_logo.png" alt="GMI Logo" class="mx-auto mb-2 h-[60px] rounded-lg">
            <h1 class="text-3xl font-extrabold text-indigo-900 mb-1">Hostel Management</h1>
            <p class="text-sm text-gray-500">Welcome to the GMI Hostel portal</p>
        </div>

        <div class="space-y-3">
            <a href="login.php" class="w-full inline-flex items-center justify-center py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-lg shadow transition focus:ring-4 focus:ring-indigo-300">
                Login
            </a>
            <a href="register.php" class="w-full inline-flex items-center justify-center py-2.5 border border-indigo-600 text-indigo-600 bg-white hover:bg-indigo-50 font-semibold rounded-lg shadow transition focus:ring-4 focus:ring-indigo-200">
                Student Registration
            </a>
        </div>
    </div>
</body>
</html>