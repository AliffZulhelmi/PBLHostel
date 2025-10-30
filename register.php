<?php
require_once 'conn.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name'] ?? '');
    $student_id = trim($_POST['student_id'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $gender = $_POST['gender'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if ($student_id && $full_name && $gender && $email && $password && $confirm_password) {
        if ($password !== $confirm_password) {
            $message = "Passwords do not match.";
        } else {
            $result = register($full_name, $student_id, $email, $phone, $password, 'student', $gender);
            if ($result) {
                $message = "Registration successful! You can now <a href='login.php'>login</a>.";
            } else {
                $message = "Registration failed. Student ID or Email may already exist.";
            }
        }
    } else {
        $message = "Please fill in all required fields.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Student Registration</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Tailwind CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="style.css">
    <style>
        body { font-family: 'Inter', system-ui, sans-serif; }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center">

    <div class="w-full max-w-md shadow-2xl rounded-2xl px-8 py-10 bg-white border border-gray-200 mt-16 mb-16">
        <div class="mb-7 text-center">
            <img src="images/gmi_logo.png" alt="GMI Logo" class="mx-auto mb-2 h-[80px] rounded-lg">
            <h2 class="text-3xl font-extrabold text-indigo-900 mb-1">Student Registration</h2>
            <p class="text-sm text-gray-500">Fill in the form to create your hostel account</p>
        </div>
        <?php if ($message): ?>
            <div class="mb-4 <?php echo strpos($message, 'successful') !== false ? 'bg-green-100 border-green-300 text-green-700' : 'bg-red-100 border-red-300 text-red-700'; ?> border px-4 py-3 rounded text-center">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>
        <form method="post" action="" class="space-y-5">

            <div>
                <label for="full_name" class="block mb-1 text-gray-700 font-medium">Full Name</label>
                <input type="text" name="full_name" id="full_name"
                       required
                       class="w-full px-3 py-2 rounded border border-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-300 focus:border-indigo-500 transition"
                       placeholder="Your Name">
            </div>

            <div>
                <label for="gender" class="block mb-1 text-gray-700 font-medium">Gender</label>
                <select name="gender" id="gender" required
                        class="w-full px-3 py-2 rounded border border-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-300 focus:border-indigo-500 transition">
                    <option value="">Select gender</option>
                    <option value="male">Male</option>
                    <option value="female">Female</option>
                </select>
            </div>

            <div>
                <label for="student_id" class="block mb-1 text-gray-700 font-medium">Student ID</label>
                <input type="text" name="student_id" id="student_id"
                       required
                       class="w-full px-3 py-2 rounded border border-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-300 focus:border-indigo-500 transition"
                       placeholder="ABC12345678">
            </div>
`
            <div>
                <label for="email" class="block mb-1 text-gray-700 font-medium">Email</label>
                <input type="email" name="email" id="email"
                       required
                       class="w-full px-3 py-2 rounded border border-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-300 focus:border-indigo-500 transition"
                       placeholder="student@student.gmi.edu.my">
            </div>

            <div>
                <label for="phone" class="block mb-1 text-gray-700 font-medium">Phone Number</label>
                <input type="text" name="phone" id="phone"
                       required
                       class="w-full px-3 py-2 rounded border border-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-300 focus:border-indigo-500 transition"
                       placeholder="0123456789">
            </div>

            <div>
                <label for="password" class="block mb-1 text-gray-700 font-medium">Password</label>
                <input type="password" name="password" id="password"
                       required
                       class="w-full px-3 py-2 rounded border border-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-300 focus:border-indigo-500 transition"
                       placeholder="Password">
            </div>

            <div>
                <label for="confirm_password" class="block mb-1 text-gray-700 font-medium">Confirm Password</label>
                <input type="password" name="confirm_password" id="confirm_password"
                       required
                       class="w-full px-3 py-2 rounded border border-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-300 focus:border-indigo-500 transition"
                       placeholder="Re-enter Password">
            </div>

            <div>
                <button type="submit"
                        class="w-full py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-lg shadow transition focus:ring-4 focus:ring-indigo-300">
                    Register
                </button>
            </div>
        </form>
        <p class="mt-4 text-center text-gray-500">
            Already have an account?
            <a href="login.php" class="text-indigo-600 hover:text-indigo-800 font-medium">Login here</a>.
        </p>
    </div>
</body>
</html>