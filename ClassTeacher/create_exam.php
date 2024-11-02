<?php

// Include required files
include '../Includes/dbcon.php';
include '../Includes/session.php';

// Initialize variables
$statusMsg = '';
$exams = [];
$courseName = '';

// Get the course ID and name for the logged-in teacher
$teacherId = $_SESSION['userId']; // Assuming the teacher ID is stored in session
$classQuery = "SELECT course_id FROM tblcourseincharge WHERE tea_id = ? AND isActive = 1";
$stmt = $conn->prepare($classQuery);
$stmt->bind_param("i", $teacherId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $courseData = $result->fetch_assoc();
    $course_id = $courseData['course_id'];

    // Fetch course name
    $courseNameQuery = "SELECT course_name FROM tblcourse WHERE course_id = ?";
    $stmt = $conn->prepare($courseNameQuery);
    $stmt->bind_param("i", $course_id);
    $stmt->execute();
    $courseResult = $stmt->get_result();

    if ($courseResult->num_rows > 0) {
        $courseData = $courseResult->fetch_assoc();
        $courseName = $courseData['course_name'];
    }

    // Fetch existing exams for the course
    $examQuery = "SELECT * FROM tblexam WHERE course_id = ?";
    $stmt = $conn->prepare($examQuery);
    $stmt->bind_param("i", $course_id);
    $stmt->execute();
    $exams = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
} else {
    $course_id = null;
}

// Check if the form is submitted
if (isset($_POST['createExam'])) {
    $subject_name = $_POST['subject_name'];
    $subject_code = $_POST['subject_code'];
    $qp_code = $_POST['Qp_code'];
    $exam_date = $_POST['exam_date'];
    $maximum_marks = $_POST['maximum_marks'];

    // Check if Qp_code is unique
    $checkQpQuery = "SELECT * FROM tblexam WHERE Qp_code = ?";
    $stmt = $conn->prepare($checkQpQuery);
    $stmt->bind_param("s", $qp_code);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Set alert message for duplicate Qp Code
        $statusMsg = 'Qp Code must be unique. Please enter a different Qp Code.';
    } else {
        // Insert exam details into tblexam
        $insertQuery = "INSERT INTO tblexam (subject_name, subject_code, Qp_code, exam_date, maximum_marks, course_id) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($insertQuery);
        $stmt->bind_param("ssssii", $subject_name, $subject_code, $qp_code, $exam_date, $maximum_marks, $course_id);

        if ($stmt->execute()) {
            // Set success message
            $statusMsg = 'Exam created successfully!';
        } else {
            // Set failure message
            $statusMsg = 'Failed to create exam. Please try again.';
        }
    }

    // Use JavaScript to show the alert and redirect
    echo "<script>
            alert('$statusMsg');
            window.location.href='http://localhost/cms/ClassTeacher/?page=AddExams.php';
          </script>";
    exit; // Stop further execution
}
