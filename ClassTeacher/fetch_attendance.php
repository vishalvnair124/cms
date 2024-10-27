<?php
session_start();
include('../includes/dbcon.php');

// Get student’s admission number from session
$student_id = $_SESSION['userId'];
$query = "SELECT admissionNumber FROM tblstudents WHERE Id = $student_id";
$result = $conn->query($query);
$admissionNo = $result->fetch_assoc()['admissionNumber'];

if (isset($_POST['viewType']) && isset($_POST['dateInput'])) {
    $viewType = $_POST['viewType'];
    $dateInput = $_POST['dateInput'];

    $query = "";

    if ($viewType == 'day') {
        $query = "
            SELECT tblattendance.status, tblattendance.dateTimeTaken AS date, 
                   tblclass.className, tblclassarms.classArmName, 
                   tblsessionterm.sessionName, tblterm.termName
            FROM tblattendance
            INNER JOIN tblclass ON tblclass.Id = tblattendance.classId
            INNER JOIN tblclassarms ON tblclassarms.Id = tblattendance.classArmId
            INNER JOIN tblsessionterm ON tblsessionterm.Id = tblattendance.sessionTermId
            INNER JOIN tblterm ON tblterm.Id = tblsessionterm.termId
            WHERE tblattendance.admissionNo = '$admissionNo' 
              AND DATE(tblattendance.dateTimeTaken) = '$dateInput'";
    } else {
        $query = "
            SELECT tblattendance.status, tblattendance.dateTimeTaken AS date, 
                   tblclass.className, tblclassarms.classArmName, 
                   tblsessionterm.sessionName, tblterm.termName
            FROM tblattendance
            INNER JOIN tblclass ON tblclass.Id = tblattendance.classId
            INNER JOIN tblclassarms ON tblclassarms.Id = tblattendance.classArmId
            INNER JOIN tblsessionterm ON tblsessionterm.Id = tblattendance.sessionTermId
            INNER JOIN tblterm ON tblterm.Id = tblsessionterm.termId
            WHERE tblattendance.admissionNo = '$admissionNo' 
              AND MONTH(tblattendance.dateTimeTaken) = MONTH('$dateInput')";
    }

    $result = $conn->query($query);
    $records = [];

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $records[] = [
                'date' => $row['date'],
                'className' => $row['className'],
                'classArmName' => $row['classArmName'],
                'sessionName' => $row['sessionName'],
                'termName' => $row['termName'],
                'status' => $row['status'] == '1' ? 'Present' : 'Absent'
            ];
        }
    }
    echo json_encode($records);
} else {
    echo json_encode([]);
}
