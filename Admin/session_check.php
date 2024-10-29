
<?php
// Start the session if it hasn't been started yet
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if the user is not logged in (userId is not set)
if (!isset($_SESSION['userId'])) {
    // Redirect to the login page if not authenticated
    header("Location: ../index.php");
    exit(); // Ensure no further code runs after the redirect
}
?>