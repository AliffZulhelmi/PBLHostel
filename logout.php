<!DOCTYPE html>
<html>
<head>
    <title>Logged Out</title>
</head>
<body>
    <h2>You have been logged out.</h2>
    <p><a href="login.php">Login again</a></p>
</body>
</html>

<?php
session_start();

// Destroy all session data
$_SESSION = [];
session_unset();
session_destroy();
?>