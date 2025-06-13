<?php
session_start();
require_once "../config.php";

if (isset($_COOKIE['remember_me'])) {
    $token = $_COOKIE['remember_me'];
    $stmt = $conn->prepare("UPDATE students SET remember_token = NULL WHERE remember_token = ?");
    if ($stmt) {
        $stmt->bind_param("s", $token);
        $stmt->execute();
        $stmt->close();
    }
    setcookie('remember_me', '', time() - 3600, "/", "", true, true);
}

$_SESSION = [];
session_destroy();
header("Location: index.php");
exit();
?>