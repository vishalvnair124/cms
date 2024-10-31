<?php
include '../Includes/dbcon.php';
// session_start(); // Ensure session is started

// Ensure student is logged in
if (!isset($_SESSION['userId'])) {
    header("Location: status_page.php?status=unauthorized");
    exit;
}

// Retrieve the student's ID from session
$student_id = $_SESSION['userId'];

// Query to check student status
$query = "SELECT std_status FROM tblstudents WHERE std_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$stmt->bind_result($std_status);
$stmt->fetch();
$stmt->close();
$conn->close();

// Redirect to the status page with the appropriate status
if ($std_status === 2) {
    header("Location: status_page.php?status=pending");
} elseif ($std_status === 0) {
    header("Location: status_page.php?status=removed");
} elseif ($std_status != 1) {
    header("Location: status_page.php?status=error");
}
