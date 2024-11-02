<?php
// Include required files
include '../Includes/dbcon.php';
include '../Includes/session.php';

// Initialize variables
$results = [];

// Check if the user is logged in
if (!isset($_SESSION['userId'])) {
    die("Unauthorized access! Please log in.");
}

// Fetch results if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['exam_id'])) {
    $examId = $_POST['exam_id'];

    // Fetch results for the entire class from tblexam_stu
    $resultsQuery = "SELECT std.std_admissionNumber,std.std_firstName, std.std_lastName, es.marks_obtained, es.status 
                     FROM tblstudents std
                     JOIN tblexam_stu es ON std.std_id = es.std_id
                     WHERE es.exam_id = ?";
    $stmt = $conn->prepare($resultsQuery);
    $stmt->bind_param("i", $examId);
    $stmt->execute();
    $results = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

    // Return results as JSON
    echo json_encode($results);
} else {
    echo json_encode([]);
}
