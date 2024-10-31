<?php
include '../Includes/dbcon.php'; // Include your database connection
session_start(); // Start the session

// Check if the user is logged in (optional)
if (!isset($_SESSION['userId'])) {
    header('Location: ../index.php'); // Redirect to login if not logged in
    exit();
}

// Check if the student ID is provided
if (isset($_GET['id'])) {
    $studentId = $_GET['id'];

    // Prepare the statement to disqualify the student
    $query = "UPDATE tblstudents SET std_status = 0 WHERE std_id = ?";
    $stmt = $conn->prepare($query);

    if ($stmt) {
        // Bind the parameters
        $stmt->bind_param("i", $studentId);

        // Execute the statement
        if ($stmt->execute()) {
            // Successfully disqualified the student
            $message = "Student disqualified successfully.";
        } else {
            // Error executing the statement
            $message = "Error disqualifying the student: " . $stmt->error;
        }

        $stmt->close();
    } else {
        // Error preparing the statement
        $message = "Error preparing the statement: " . $conn->error;
    }
} else {
    // No student ID provided
    $message = "No student ID provided.";
}

// Close the database connection
$conn->close();

// Redirect back to the student management page with a message
header("Location: index.php?page=students.php&message=" . urlencode($message));
exit();
