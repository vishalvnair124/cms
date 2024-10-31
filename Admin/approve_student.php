<?php
include '../Includes/dbcon.php';
session_start();

// Check if the student ID is set in the URL
if (isset($_GET['id'])) {
    $studentId = $_GET['id'];

    // Prepare the SQL statement to update the student's status to active
    $query = "UPDATE tblstudents SET std_status = 1 WHERE std_id = ?";
    $stmt = $conn->prepare($query);

    if ($stmt) {
        // Bind parameters and execute the statement
        $stmt->bind_param("i", $studentId);
        if ($stmt->execute()) {
            // Redirect to the students management page with a success message
            $_SESSION['message'] = "Student approved successfully!";
            $_SESSION['message_type'] = "success";
        } else {
            // Redirect with an error message
            $_SESSION['message'] = "Failed to approve student.";
            $_SESSION['message_type'] = "error";
        }
        $stmt->close();
    } else {
        // Handle statement preparation error
        $_SESSION['message'] = "Database error: could not prepare statement.";
        $_SESSION['message_type'] = "error";
    }
} else {
    // Handle the case where the ID is not set
    $_SESSION['message'] = "No student ID provided.";
    $_SESSION['message_type'] = "error";
}

// Redirect back to the manage students page
header("Location: index.php?page=students.php");
exit;
