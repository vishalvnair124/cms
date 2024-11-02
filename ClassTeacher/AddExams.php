<?php
// Include required files
include '../Includes/dbcon.php';
include '../Includes/session.php';

// Initialize variables
$statusMsg = '';
$exams = [];
$courseName = '';

// Check if the user is logged in
if (!isset($_SESSION['userId'])) {
    die("<div class='alert alert-danger'>Unauthorized access! Please log in as a teacher.</div>");
}

// Get the teacher ID from the session
$teacherId = $_SESSION['userId'];

// Get the course ID and name for the logged-in teacher
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

    // Handle exam creation
    if (isset($_POST['createExam'])) {
        $subject_name = $_POST['subject_name'];
        $subject_code = $_POST['subject_code'];
        $Qp_code = $_POST['Qp_code'];
        $exam_date = $_POST['exam_date'];
        $maximum_marks = $_POST['maximum_marks'];

        // Insert the exam into the database
        $insertQuery = "INSERT INTO tblexam (course_id, subject_name, subject_code, Qp_code, exam_date, maximum_marks) VALUES (?, ?, ?, ?, ?, ?)";
        $insertStmt = $conn->prepare($insertQuery);
        $insertStmt->bind_param("issssi", $course_id, $subject_name, $subject_code, $Qp_code, $exam_date, $maximum_marks);

        if ($insertStmt->execute()) {
            $statusMsg = "Exam created successfully.";
            // Refresh the exams list
            $stmt->execute();
            $exams = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        } else {
            $statusMsg = "Error creating exam: " . $conn->error;
        }

        $insertStmt->close();
    }
} else {
    $course_id = null;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="../img/logo/attnlg.jpg" rel="icon">
    <title>Create New Exam</title>
    <style>
        /* General Styles */
        .container {
            max-width: 900px;
            background-color: #fff;
            padding: 2em;
            margin: 50px auto;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }

        h1,
        h2 {
            text-align: center;
            color: #5752e3;
        }

        .form-group {
            margin-bottom: 1.5em;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5em;
            font-weight: bold;
            color: #5752e3;
        }

        .form-control {
            width: 100%;
            padding: 0.8em;
            border: 1px solid #ddd;
            border-radius: 5px;
            outline: none;
            font-size: 1em;
            transition: border-color 0.3s;
        }

        .form-control:focus {
            border-color: #252037;
        }

        .btn {
            background-color: #5752e3;
            color: #fff;
            padding: 0.8em 1.5em;
            border: none;
            border-radius: 5px;
            font-size: 1em;
            cursor: pointer;
            display: inline-block;
            transition: background-color 0.3s;
        }

        .btn:hover {
            background-color: #5752e3;
        }

        .exam-list {
            margin-top: 2em;
        }

        .exam-list table {
            width: 100%;
            border-collapse: collapse;
        }

        .exam-list th,
        .exam-list td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }

        .exam-list th {
            background-color: #5752e3;
            color: #fff;
        }

        .alert {
            color: red;
            padding: 10px;
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            border-radius: 5px;
            text-align: center;
            margin-top: 20px;
        }
    </style>
</head>

<body>

    <div class="container">
        <h1>Create New Exam</h1>

        <?php if ($statusMsg): ?>
            <div class="alert"><?php echo htmlspecialchars($statusMsg); ?></div>
        <?php endif; ?>

        <?php if ($course_id): ?>
            <p style="text-align: center; color: #5752e3;">Course Name: <?php echo htmlspecialchars($courseName); ?></p>

            <!-- Form for Creating an Exam -->
            <form method="post" action="create_exam.php">
                <div class="form-group">
                    <label>Subject Name</label>
                    <input type="text" name="subject_name" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Subject Code</label>
                    <input type="text" name="subject_code" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Qp Code</label>
                    <input type="text" name="Qp_code" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Exam Date</label>
                    <input type="date" name="exam_date" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Maximum Marks</label>
                    <input type="number" name="maximum_marks" class="form-control" required>
                </div>
                <button type="submit" name="createExam" class="btn">Create Exam</button>
            </form>

            <!-- List of Existing Exams -->
            <div class="exam-list">
                <h2>Existing Exams</h2>
                <?php if (count($exams) > 0): ?>
                    <table>
                        <thead>
                            <tr>
                                <th>Subject Name</th>
                                <th>Subject Code</th>
                                <th>Qp Code</th>
                                <th>Exam Date</th>
                                <th>Maximum Marks</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($exams as $exam): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($exam['subject_name']); ?></td>
                                    <td><?php echo htmlspecialchars($exam['subject_code']); ?></td>
                                    <td><?php echo htmlspecialchars($exam['Qp_code']); ?></td>
                                    <td><?php echo htmlspecialchars($exam['exam_date']); ?></td>
                                    <td><?php echo htmlspecialchars($exam['maximum_marks']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p>No exams found for this course.</p>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <div class='alert'>Access denied. You are not an active course incharge.</div>
        <?php endif; ?>
    </div>

</body>

</html>