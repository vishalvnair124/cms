<?php
session_start();
include('../includes/dbcon.php'); // Ensure the path is correct

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if the student is logged in
if (!isset($_SESSION['userId'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized access.']);
    exit;
}

// Get student ID from the session
$student_id = $_SESSION['userId'];

// Collect data from POST request
$std_email = $_POST['std_email'] ?? '';
$std_phone_number = $_POST['std_phone_number'] ?? '';
$std_parent_ph = $_POST['std_parent_ph'] ?? '';
$std_password = $_POST['std_password'] ?? '';
$std_address = $_POST['std_address'] ?? '';

// Validate input
if (empty($std_email) || empty($std_phone_number) || empty($std_parent_ph)) {
    echo json_encode(['status' => 'error', 'message' => 'Email, phone number, and parent\'s phone number are required.']);
    exit;
}

try {
    // Begin transaction
    $conn->begin_transaction();

    // Update student details (address is optional)
    $updateQuery = "UPDATE tblstudents 
                    SET std_email = ?, std_phone_number = ?, std_parent_ph = ?, std_address = ? 
                    WHERE std_id = ?";
    $stmt = $conn->prepare($updateQuery);

    $std_address = !empty($std_address) ? $std_address : ''; // Default to an empty string if not provided

    $stmt->bind_param("ssssi", $std_email, $std_phone_number, $std_parent_ph, $std_address, $student_id);
    $stmt->execute();

    // Check if a new password is provided
    if (!empty($std_password)) {
        // Hash the new password securely using bcrypt
        $hashedPassword = md5($std_password);

        // Update the password in the database
        $passwordQuery = "UPDATE tblstudents SET std_password = ? WHERE std_id = ?";
        $passwordStmt = $conn->prepare($passwordQuery);
        $passwordStmt->bind_param("si", $hashedPassword, $student_id);
        $passwordStmt->execute();
        $passwordStmt->close();
    }

    // Commit the transaction
    $conn->commit();

    echo json_encode(['status' => 'success', 'message' => 'Profile updated successfully.']);
} catch (Exception $e) {
    // Rollback on error
    $conn->rollback();
    echo json_encode(['status' => 'error', 'message' => 'Failed to update profile. ' . $e->getMessage()]);
} finally {
    $stmt->close();
    $conn->close();
}
