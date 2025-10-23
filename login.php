<?php
session_start();
require_once 'conn.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email && $password) {
        $user = login($email, $password); 
        
        if ($user) {
            $_SESSION['full_name'] = $user['full_name'];
            $_SESSION['student_id'] = $user['student_id'];
            $_SESSION['role'] = $user['role'];
            header('Location: index.php');
            exit;
        } else {
            $error = 'Invalid email or password.';
        }
    } else {
        $error = 'Please enter email and password.';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Login</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
            <h2 class="text-3xl font-extrabold text-indigo-900 mb-1">Hostel Login</h2>
            <p class="text-sm text-gray-500">Sign in to your hostel account</p>
        </div>
        <?php if ($error): ?>
            <div class="mb-4 bg-red-100 border border-red-300 text-red-700 px-4 py-3 rounded">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>
        <form method="post" action="" class="space-y-5">
            <div>
                <label for="email" class="block mb-1 text-gray-700 font-medium">Email</label>
                <input type="text" name="email" id="email"
                       required
                       class="w-full px-3 py-2 rounded border border-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-300 focus:border-indigo-500 transition"
                       placeholder="your.email@student.gmi.edu.my">
            </div>
            <div>
                <label for="password" class="block mb-1 text-gray-700 font-medium">Password</label>
                <input type="password" name="password" id="password"
                       required
                       class="w-full px-3 py-2 rounded border border-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-300 focus:border-indigo-500 transition"
                       placeholder="Password">
            </div>
            <div>
                <button type="submit"
                        class="w-full py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-lg shadow transition focus:ring-4 focus:ring-indigo-300">
                    Login
                </button>
            </div>
        </form>
        <p class="mt-4 text-center text-gray-500">
            Don't have an account?
            <a href="register.php" class="text-indigo-600 hover:text-indigo-800 font-medium">Register here</a>.
        </p>
    </div>
</body>
</html>