<?php
include '../Includes/dbcon.php'; // Include your database connection file
session_start();

// Check if the student ID is set in the URL
if (!isset($_GET['id'])) {
    header('Location: index.php?page=students.php'); // Redirect if no ID
    exit();
}

$studentId = intval($_GET['id']); // Get the student ID from URL

// Fetch student information
$studentQuery = "SELECT * FROM tblstudents WHERE std_id = ?";
$stmt = $conn->prepare($studentQuery);
$stmt->bind_param("i", $studentId);
$stmt->execute();
$studentResult = $stmt->get_result();
$student = $studentResult->fetch_assoc();

if (!$student) {
    echo "<div class='error'>Student not found.</div>";
    exit();
}

// Fetch available courses
$courseQuery = "
    SELECT * FROM tblcourse 
    WHERE course_id NOT IN (
        SELECT course_id FROM tblcoursetaken WHERE std_id = ? AND isActive = 1
    )";

$courseStmt = $conn->prepare($courseQuery);
$courseStmt->bind_param("i", $studentId);
$courseStmt->execute();
$coursesResult = $courseStmt->get_result();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['assign_course'])) {
    $selectedCourseId = $_POST['course'] ?? null;

    if ($selectedCourseId) {
        // Check if a record already exists for the student and course
        $checkQuery = "SELECT * FROM tblcoursetaken WHERE std_id = ? AND course_id = ?";
        $checkStmt = $conn->prepare($checkQuery);
        $checkStmt->bind_param("ii", $studentId, $selectedCourseId);
        $checkStmt->execute();
        $checkResult = $checkStmt->get_result();

        if ($checkResult->num_rows > 0) {
            // Record exists, check its status
            $existingRecord = $checkResult->fetch_assoc();

            if ($existingRecord['isActive'] == 0) {
                // Update the existing record to active
                $updateQuery = "UPDATE tblcoursetaken SET isActive = 1 WHERE course_taken_id = ?";
                $updateStmt = $conn->prepare($updateQuery);
                $updateStmt->bind_param("i", $existingRecord['course_taken_id']);
                $updateStmt->execute();
            } else {
                // Course already active, can add a message if needed
                header("Location: index.php?page=students.php&message=Student is already enrolled in this course.");
                exit();
            }
        } else {
            // No existing record, insert a new one
            $insertQuery = "INSERT INTO tblcoursetaken (std_id, course_id, isActive) VALUES (?, ?, 1)";
            $insertStmt = $conn->prepare($insertQuery);
            $insertStmt->bind_param("ii", $studentId, $selectedCourseId);
            $insertStmt->execute();
        }
    }

    // Update previous active course's isActive to 0 if assigning a new course
    $updatePreviousQuery = "UPDATE tblcoursetaken SET isActive = 0 WHERE std_id = ? AND isActive = 1 AND course_id != ?";
    $updatePreviousStmt = $conn->prepare($updatePreviousQuery);
    $updatePreviousStmt->bind_param("ii", $studentId, $selectedCourseId);
    $updatePreviousStmt->execute();

    header("Location: index.php?page=students.php&message=Course assigned successfully.");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="../img/logo/attnlg.jpg" rel="icon">
    <title>Assign Course to <?php echo htmlspecialchars($student['std_firstName'] . ' ' . $student['std_lastName']); ?></title>
    <link rel="stylesheet" href="./homestyles/newstyle.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            color: #333;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 600px;
            margin: 50px auto;
            padding: 20px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        h1,
        h2 {
            text-align: center;
            color: #5752e3;
        }

        h3 {
            margin: 20px 0;
            font-weight: normal;
        }

        .course-option {
            display: flex;
            align-items: center;
            margin: 10px 0;
        }

        .course-option input {
            margin-right: 10px;
        }

        button {
            width: 100%;
            padding: 10px;
            background-color: #5752e3;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }

        button:hover {
            background-color: #5752e5;
        }

        .back-link {
            display: inline-block;
            margin-top: 20px;
            text-align: center;
            color: #5753e3;
            text-decoration: none;
            font-weight: bold;
        }

        .back-link:hover {
            text-decoration: none;
        }

        .error {
            color: red;
            text-align: center;
            font-weight: bold;
        }
    </style>
</head>

<body>

    <div class="container">
        <h1>Assign Course to</h1>
        <h2><?php echo htmlspecialchars($student['std_firstName'] . ' ' . $student['std_lastName']); ?></h2>

        <form method="POST" action="">
            <h3>Select a Course:</h3>
            <?php while ($course = $coursesResult->fetch_assoc()): ?>
                <div class="course-option">
                    <input type="radio" name="course" value="<?php echo $course['course_id']; ?>" id="course_<?php echo $course['course_id']; ?>">
                    <label for="course_<?php echo $course['course_id']; ?>"><?php echo htmlspecialchars($course['course_name']); ?></label>
                </div>
            <?php endwhile; ?>

            <button type="submit" name="assign_course">Assign Course</button>
        </form>

        <a href="index.php?page=students.php" class="back-link">Back to Student List</a>
    </div>

</body>

</html>