<?php
session_start();
include '../Includes/dbcon.php';

// Check if both `tea_id` and `course_id` are present in POST request
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['tea_id']) && isset($_POST['course_id'])) {
    $tea_id = $_POST['tea_id'];
    $course_id = $_POST['course_id'];

    // Check if the teacher is already assigned to the course
    $checkQuery = "SELECT * FROM tblcoteachers WHERE tea_id = ? AND course_id = ?";
    $checkStmt = $conn->prepare($checkQuery);
    $checkStmt->bind_param("ii", $tea_id, $course_id);
    $checkStmt->execute();
    $result = $checkStmt->get_result();

    if ($result->num_rows > 0) {
        $message = "This teacher is already assigned to the selected course.";
    } else {
        // Add the co-teacher
        $insertQuery = "INSERT INTO tblcoteachers (tea_id, course_id) VALUES (?, ?)";
        $insertStmt = $conn->prepare($insertQuery);
        $insertStmt->bind_param("ii", $tea_id, $course_id);

        if ($insertStmt->execute()) {
            $message = "Co-teacher added successfully!";
        } else {
            $message = "Error adding co-teacher: " . $conn->error;
        }
    }

    $checkStmt->close();
    if (isset($insertStmt)) {
        $insertStmt->close();
    }
    $conn->close();

    // Display the alert message directly on this page
    echo "<script>
            alert('" . htmlspecialchars($message) . "');
            window.location.href = 'index.php?page=coteachers.php'; // Redirect after alert
          </script>";
    exit();
}
