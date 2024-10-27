<?php
session_start();
include('../includes/dbcon.php'); // Ensure path is correct

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if the student is logged in
if (!isset($_SESSION['userId'])) {
    die("Unauthorized access.");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve data from the form submission
    $newEmail = $_POST['email'];
    $newPhone = $_POST['phone'];
    $newPassword = $_POST['password'];
    $admissionNumber = $_POST['admissionNumber'];

    try {
        // Prepare SQL statement based on whether password is being updated
        if (!empty($newPassword)) {
            $stmt = $conn->prepare(
                "UPDATE tblstudents 
                SET email = ?, phone_number = ?, password = ? 
                WHERE admissionNumber = ?"
            );
            $hashedPassword = md5($newPassword);
            $stmt->bind_param("ssss", $newEmail, $newPhone, $hashedPassword, $admissionNumber);
        } else {
            $stmt = $conn->prepare(
                "UPDATE tblstudents 
                SET email = ?, phone_number = ? 
                WHERE admissionNumber = ?"
            );
            $stmt->bind_param("sss", $newEmail, $newPhone, $admissionNumber);
        }

        // Execute the query and handle the response
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Profile updated successfully!']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error updating profile: ' . $stmt->error]);
        }

        $stmt->close();
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Exception: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
