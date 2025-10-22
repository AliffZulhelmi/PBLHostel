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
    <style>
        body { font-family: 'Inter', system-ui, sans-serif; }
    </style>
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center">

    <div class="text-center p-8 max-w-2xl w-full bg-white rounded-xl shadow-2xl space-y-6">
        <div class="mb-6">
            <img src="https://placehold.co/80x80/374151/FFFFFF?text=HMS" alt="Hostel Logo" class="mx-auto mb-4 h-20 rounded-full shadow-md">
            <h1 class="text-4xl font-extrabold text-indigo-800">Welcome to Hostel Management System</h1>
            <p class="text-lg text-gray-600 mt-2">Your seamless solution for room registration and issue tracking.</p>
        </div>

        <div class="flex flex-col sm:flex-row justify-center space-y-4 sm:space-y-0 sm:space-x-4">
            <a href="login.php" class="inline-flex items-center justify-center px-6 py-3 border border-transparent text-base font-medium rounded-lg shadow-lg text-white bg-indigo-600 hover:bg-indigo-700 transition duration-300 transform hover:scale-105 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                Login to Account
            </a>
            <a href="register.php" class="inline-flex items-center justify-center px-6 py-3 border border-indigo-600 text-base font-medium rounded-lg shadow-lg text-indigo-600 bg-white hover:bg-indigo-50 transition duration-300 transform hover:scale-105 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                Student Registration
            </a>
        </div>
    </div>
</body>
</html>