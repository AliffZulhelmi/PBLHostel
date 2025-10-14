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
            $_SESSION['full _name'] = $user['full_name'];
            $_SESSION['student_id'] = $user['student_id'];
            $_SESSION['role'] = $user['role'];
            header('Location: index.php');
            exit;
        } else {
            $error = 'Invalid student ID or password.';
        }
    } else {
        $error = 'Please enter student ID and password.';
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
</head>
<body>
    <h2>Login</h2>
    <?php if ($error): ?>
        <p style="color:red;"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>
    <form method="post" action="">
        <label>Student Email:</label>
        <!-- The name of this input is 'email', which is now correctly read in PHP -->
        <input type="text" name="email" required><br><br>
        <label>Password:</label>
        <input type="password" name="password" required><br><br>
        <button type="submit">Login</button>
    </form>
</body>
</html>
