<?php
session_start();
include '../Includes/dbcon.php'; // Ensure this path is correct for your setup

// Check if the teacher is logged in
if (!isset($_SESSION['userId'])) {
    die(json_encode(['error' => 'Teacher not logged in']));
}

$tea_id = $_SESSION['userId'];

// Check if course ID and date are set
if (isset($_POST['course_id']) && isset($_POST['att_date'])) {
    $course_id = $_POST['course_id'];
    $att_date = $_POST['att_date'];

    // Fetch attendance records for the selected course and date
    $attendance_query = $conn->prepare("
        SELECT tblstudents.std_id, tblstudents.std_firstName, tblstudents.std_lastName,
            tblattendance.att_hr_1, tblattendance.att_hr_2, tblattendance.att_hr_3,
            tblattendance.att_hr_4, tblattendance.att_hr_5
        FROM tblattendance
        INNER JOIN tblstudents ON tblattendance.std_id = tblstudents.std_id
        WHERE tblattendance.course_id = ? AND tblattendance.att_date = ?
    ");
    $attendance_query->bind_param("is", $course_id, $att_date);
    $attendance_query->execute();
    $attendance_result = $attendance_query->get_result();

    $attendanceRecords = [];
    while ($row = $attendance_result->fetch_assoc()) {
        $attendanceRecords[] = $row;
    }

    // Return JSON response
    echo json_encode($attendanceRecords);
} else {
    echo json_encode(['error' => 'No course or date selected.']);
}
