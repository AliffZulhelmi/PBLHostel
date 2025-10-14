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
<html>
<head>
    <title>Register</title>
</head>
<body>
    <h2>Register</h2>
    <?php if ($message): ?>
        <p><?php echo $message; ?></p>
    <?php endif; ?>
    <form method="post" action="">
        <!-- Collect full name -->
        <label>Full Name:</label><br>
        <input type="text" name="full_name" required><br>
        
        <!-- Collect gender -->
        <label>Gender:</label><br>
        <select name="gender" required>
            <!-- <option value=""></option> -->
            <option value="male">Male</option>
            <option value="female">Female</option>
        </select><br>

        <!-- Collect student id -->
        <label>Student ID:</label><br>
        <input type="text" name="student_id" required><br>
        
        <!-- Collect email -->
        <label>Email:</label><br>
        <input type="email" name="email" required><br>
        
        <!-- Collect phone number -->
        <label>Phone Number:</label><br>
        <input type="text" name="phone" required><br>

        <!-- Collect password -->
        <label>Password:</label><br>
        <input type="password" name="password" required><br>
        <label>Confirm Password:</label><br>
        <input type="password" name="confirm_password" required><br><br>

        <!-- Submit button -->
        <button type="submit">Register</button>
    </form>
    <p>Already have an account? <a href="login.php">Login here</a>.</p>
</body>
</html>