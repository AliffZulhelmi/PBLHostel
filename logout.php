<?php
session_start();

// Destroy all session data
$_SESSION = [];
session_unset();
session_destroy();
header('Location: login.php')
?>