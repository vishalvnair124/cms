<?php
if (session_status() == PHP_SESSION_NONE) {
    // If the session is not started, start it
    session_start();
}
if (!isset($_SESSION['userId'])) {
    header("Location: ../index.php");
    exit();
}
