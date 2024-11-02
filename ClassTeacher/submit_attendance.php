<?php
session_start();
include '../Includes/dbcon.php'; // Ensure this path is correct for your setup

// Check if the teacher is logged in
if (!isset($_SESSION['userId'])) {
    die(json_encode(['success' => false, 'message' => 'Teacher not logged in']));
}

// Fetch course_id and att_date safely
$course_id = isset($_POST['course_id']) ? $_POST['course_id'] : '';
$att_date = isset($_POST['att_date']) ? $_POST['att_date'] : '';
$attendance_data = isset($_POST['attendance']) ? $_POST['attendance'] : [];

// Debug: Print the input data
error_log("Received Data: ");
error_log("Course ID: " . $course_id);
error_log("Attendance Date: " . $att_date);
error_log("Attendance Data: " . print_r($attendance_data, true));

$response = ['success' => false, 'message' => ''];

// Check if course_id and att_date are set and valid
if (!empty($course_id) && !empty($att_date)) {
    foreach ($attendance_data as $std_id => $hours) {
        // Check if the attendance record already exists
        $check_query = $conn->prepare("SELECT * FROM tblattendance WHERE std_id = ? AND course_id = ? AND att_date = ?");
        $check_query->bind_param("iis", $std_id, $course_id, $att_date);
        $check_query->execute();
        $result = $check_query->get_result();

        // Prepare attendance record data
        $hour1 = isset($hours['hr1']) ? 1 : 0;
        $hour2 = isset($hours['hr2']) ? 1 : 0;
        $hour3 = isset($hours['hr3']) ? 1 : 0;
        $hour4 = isset($hours['hr4']) ? 1 : 0;
        $hour5 = isset($hours['hr5']) ? 1 : 0;

        if ($result->num_rows > 0) {
            // Record exists, update the attendance
            $update_query = $conn->prepare("
                UPDATE tblattendance 
                SET att_hr_1 = ?, att_hr_2 = ?, att_hr_3 = ?, att_hr_4 = ?, att_hr_5 = ? 
                WHERE std_id = ? AND course_id = ? AND att_date = ?
            ");
            $update_query->bind_param("iiiiiiis", $hour1, $hour2, $hour3, $hour4, $hour5, $std_id, $course_id, $att_date);
            if (!$update_query->execute()) {
                error_log("Failed to execute update query: " . $update_query->error);
                $response['message'] = "Failed to update attendance for Student ID: " . $std_id;
                break; // Exit on error
            }
        } else {
            // Record does not exist, insert a new attendance record
            $attendance_query = $conn->prepare("
                INSERT INTO tblattendance (std_id, course_id, att_date, att_hr_1, att_hr_2, att_hr_3, att_hr_4, att_hr_5) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)
            ");
            if ($attendance_query) {
                $attendance_query->bind_param("iissiiii", $std_id, $course_id, $att_date, $hour1, $hour2, $hour3, $hour4, $hour5);
                if (!$attendance_query->execute()) {
                    error_log("Failed to execute insert query: " . $attendance_query->error);
                    $response['message'] = "Failed to record attendance for Student ID: " . $std_id;
                    break; // Exit on error
                }
            } else {
                $response['message'] = "Failed to prepare insert query.";
                break;
            }
        }
    }

    if ($response['message'] === '') { // Only change if no errors
        $response['success'] = true;
        $response['message'] = "Attendance recorded successfully.";
    }
} else {
    $response['message'] = "Invalid course ID or attendance date.";
}

// Return a JSON response
header('Content-Type: application/json');
echo json_encode($response);
exit();
