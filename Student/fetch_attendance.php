<?php
session_start();
include('../includes/dbcon.php'); // Adjust path if needed

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if the student is logged in
if (!isset($_SESSION['userId'])) {
    die("You need to log in to access this page.");
}

$student_id = $_SESSION['userId'];
$viewType = $_POST['viewType'];
$dateInput = $_POST['dateInput'];

// Build SQL query based on view type (day or month)
if ($viewType === 'day') {
    $query = "SELECT Att_date, att_hr_1, att_hr_2, att_hr_3, att_hr_4, att_hr_5 
              FROM tblattendance 
              WHERE std_id = ? AND Att_date = ?";
} else {
    $query = "SELECT Att_date, att_hr_1, att_hr_2, att_hr_3, att_hr_4, att_hr_5 
              FROM tblattendance 
              WHERE std_id = ? AND Att_date LIKE ?";
    $dateInput = substr($dateInput, 0, 7) . '%'; // Format for month search (YYYY-MM)
}

$stmt = $conn->prepare($query);
$stmt->bind_param("is", $student_id, $dateInput);
$stmt->execute();
$result = $stmt->get_result();

$data = [];
while ($row = $result->fetch_assoc()) {
    // Calculate overall attendance status for the day based on individual hours
    $status = (array_sum([$row['att_hr_1'], $row['att_hr_2'], $row['att_hr_3'], $row['att_hr_4'], $row['att_hr_5']]) === 5)
        ? 'Present' : 'Absent';

    $data[] = [
        'date' => $row['Att_date'],
        'att_hr_1' => $row['att_hr_1'],
        'att_hr_2' => $row['att_hr_2'],
        'att_hr_3' => $row['att_hr_3'],
        'att_hr_4' => $row['att_hr_4'],
        'att_hr_5' => $row['att_hr_5'],
        'status' => $status,
    ];
}

header('Content-Type: application/json');
echo json_encode($data);
