<?php
// Include required files
include '../Includes/dbcon.php'; // Ensure this file sets up the $conn variable
include '../Includes/session.php'; // Include session handling, if necessary

// Handle form submission for saving marks and attendance status
if (isset($_POST['save_marks'])) {
    $exam_id = $_POST['exam_id']; // Selected exam from the dropdown
    $success = false; // Flag to track if any update/insert was successful
    $missing_students = []; // Array to hold missing student IDs

    foreach ($_POST['marks'] as $student_id => $mark) {
        // Check if student_id exists in tblstudents
        $checkStudentQuery = "SELECT std_id FROM tblstudents WHERE std_id = $student_id";
        $checkResult = $conn->query($checkStudentQuery);

        if ($checkResult->num_rows > 0) {
            // Proceed with saving marks
            $status = ($_POST['status'][$student_id] === '1') ? 1 : 0;

            // Check if an entry already exists for the exam
            $examCheckQuery = "SELECT * FROM tblexam_stu WHERE std_id = $student_id AND exam_id = $exam_id";
            $examCheckResult = $conn->query($examCheckQuery);

            if ($examCheckResult->num_rows > 0) {
                // Update existing marks
                $updateQuery = "UPDATE tblexam_stu SET marks_obtained = $mark, status = $status WHERE std_id = $student_id AND exam_id = $exam_id";
                $success = $conn->query($updateQuery);
            } else {
                // Insert new marks entry
                $insertQuery = "INSERT INTO tblexam_stu (exam_id, std_id, status, marks_obtained) VALUES ($exam_id, $student_id, $status, $mark)";
                $success = $conn->query($insertQuery);
            }
        } else {
            $missing_students[] = $student_id; // Track missing student IDs
        }
    }

    // Prepare the response message based on success
    if ($success) {
        $message = "Marks and attendance saved successfully!";
    } else {
        $message = "Error saving marks or attendance.";
    }

    // Redirect with JavaScript alert
    echo "<script>
            alert('$message');
            window.location.href = 'http://localhost/cms/ClassTeacher/index.php?page=AddResuts.php';
          </script>";
}
