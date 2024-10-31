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
    echo "Student not found.";
    exit();
}

// Fetch courses taken by the student
$coursesQuery = "SELECT c.course_name, ct.isActive 
                 FROM tblcoursetaken ct 
                 JOIN tblcourse c ON ct.course_id = c.course_id 
                 WHERE ct.std_id = ?";
$coursesStmt = $conn->prepare($coursesQuery);
$coursesStmt->bind_param("i", $studentId);
$coursesStmt->execute();
$coursesResult = $coursesStmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="../img/logo/attnlg.jpg" rel="icon">
    <title>View Student Details</title>
    <link rel="stylesheet" href="./homestyles/newstyle.css">
    <style>
        body {
            font-family: 'Open Sans', sans-serif;
            background: #f9f9f9;
            margin: 0;
            padding: 20px;
            color: #444;
        }

        .container {
            max-width: 800px;
            background: #fff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
            margin: auto;
        }

        h1 {
            color: #5752e3;
            /* Your main color */
            text-align: center;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th,
        td {
            padding: 12px;
            border: 1px solid #5752e3;
            /* Border color matching your main color */
            text-align: left;
            transition: background-color 0.3s;
        }

        th {
            background-color: #fff;
            /* White background for the header */
            color: #444;
            /* Dark text color */
        }

        tr:hover {
            background-color: #f1f1f1;
            /* Light gray background on hover */
        }

        .back-link {
            display: block;
            margin-top: 20px;
            text-decoration: none;
            color: #5752e3;
            /* Your main color */
            text-align: center;
            transition: color 0.3s;
        }

        .back-link:hover {
            color: #004d40;
            /* Darker shade for hover effect */
        }

        /* Style for the cell outlines */
        td {
            position: relative;
        }

        td::after {
            content: "";
            position: absolute;
            bottom: 0;
            left: 0;
            width: calc(100% - 1px);
            height: 2px;
            /* Thickness of the bottom border */
            background-color: black;
            /* Black bottom border */
            transition: opacity 0.3s;
            opacity: 0;
        }

        td:hover::after {
            opacity: 1;
            /* Show the bottom border on hover */
        }
    </style>
</head>

<body>

    <div class="container">
        <h1>Student Details</h1>

        <table>
            <tr>
                <th>First Name</th>
                <td><?php echo htmlspecialchars($student['std_firstName']); ?></td>
            </tr>
            <tr>
                <th>Last Name</th>
                <td><?php echo htmlspecialchars($student['std_lastName']); ?></td>
            </tr>
            <tr>
                <th>Admission Number</th>
                <td><?php echo htmlspecialchars($student['std_admissionNumber']); ?></td>
            </tr>
            <tr>
                <th>Email</th>
                <td><?php echo htmlspecialchars($student['std_email']); ?></td>
            </tr>
            <tr>
                <th>Phone Number</th>
                <td><?php echo htmlspecialchars($student['std_phone_number']); ?></td>
            </tr>
            <tr>
                <th>Date of Birth</th>
                <td><?php echo htmlspecialchars($student['stud_dob']); ?></td>
            </tr>
            <tr>
                <th>Address</th>
                <td><?php echo nl2br(htmlspecialchars($student['std_address'])); ?></td>
            </tr>
            <tr>
                <th>Aadhar Number</th>
                <td><?php echo htmlspecialchars($student['std_aadhar_no']); ?></td>
            </tr>
            <tr>
                <th>Parent Name</th>
                <td><?php echo htmlspecialchars($student['std_parent_name']); ?></td>
            </tr>
            <tr>
                <th>Parent Phone</th>
                <td><?php echo htmlspecialchars($student['std_parent_ph']); ?></td>
            </tr>
        </table>

        <h2>Courses Enrolled:</h2>
        <?php if ($coursesResult->num_rows > 0): ?>
            <table>
                <tr>
                    <th>Course Name</th>
                    <th>Status</th>
                </tr>
                <?php while ($course = $coursesResult->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($course['course_name']); ?></td>
                        <td><?php echo $course['isActive'] ? 'Active' : 'Inactive'; ?></td>
                    </tr>
                <?php endwhile; ?>
            </table>
        <?php else: ?>
            <p>No courses enrolled.</p>
        <?php endif; ?>

        <a href="index.php?page=students.php" class="back-link">Back to Student List</a>
    </div>

</body>

</html>