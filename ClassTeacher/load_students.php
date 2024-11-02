<?php
session_start();
include '../Includes/dbcon.php'; // Adjust path as needed

if (!isset($_SESSION['userId']) || !isset($_POST['course_id']) || !isset($_POST['att_date'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit;
}

$course_id = $_POST['course_id'];
$att_date = $_POST['att_date'];

// Fetch students for the selected course
$students_query = $conn->prepare("
    SELECT std_id, std_firstName, std_lastName 
    FROM tblstudents 
    WHERE std_id IN (SELECT std_id FROM tblcoursetaken WHERE course_id = ? AND isActive = 1)
");
$students_query->bind_param("i", $course_id);
$students_query->execute();
$students_result = $students_query->get_result();

$students = [];
while ($row = $students_result->fetch_assoc()) {
    $students[] = $row;
}

// Fetch attendance records if needed
$attendance_records = [];
if ($students) {
    $attendance_query = $conn->prepare("
        SELECT std_id, att_hr_1, att_hr_2, att_hr_3, att_hr_4, att_hr_5 
        FROM tblattendance 
        WHERE course_id = ? AND att_date = ?
    ");
    $attendance_query->bind_param("is", $course_id, $att_date);
    $attendance_query->execute();
    $attendance_result = $attendance_query->get_result();

    while ($record = $attendance_result->fetch_assoc()) {
        $attendance_records[$record['std_id']] = $record;
    }
}

echo json_encode(['success' => true, 'students' => $students, 'attendance_records' => $attendance_records]);
