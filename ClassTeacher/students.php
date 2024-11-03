<?php
include '../Includes/dbcon.php';
session_start();

if (!isset($_SESSION['userId'])) {
    header('Location: ../index.php'); // Redirect to login if not logged in
    exit();
}

$teacherId = intval($_SESSION['userId']);

// Check if the teacher is a class teacher
$classQuery = "SELECT * FROM tblcourseincharge WHERE tea_id = ? AND isActive = 1";
$classStmt = $conn->prepare($classQuery);
$classStmt->bind_param("i", $teacherId);
$classStmt->execute();
$classResult = $classStmt->get_result();
$classIncharge = $classResult->fetch_assoc();

if (!$classIncharge) {
    // If the teacher is not a class teacher, show a message
    echo "<h1>You are not a class teacher.</h1>";
    exit();
}

$courseId = $classIncharge['course_id'];

// Fetch students in the class that the teacher is in charge of
$studentsQuery = "SELECT * FROM tblstudents WHERE std_id IN 
                  (SELECT std_id FROM tblcoursetaken WHERE course_id = ? AND isActive = 1)";
$studentsStmt = $conn->prepare($studentsQuery);
$studentsStmt->bind_param("i", $courseId);
$studentsStmt->execute();
$studentsResult = $studentsStmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Class Teacher View</title>

    <style>
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
            text-align: left;
            transition: background-color 0.3s;
        }

        th {
            background-color: #5752e3;
            color: #000;
        }

        tr:hover {
            background-color: #f1f1f1;
        }

        .back-link {
            display: block;
            margin-top: 20px;
            text-decoration: none;
            color: #5752e3;
            text-align: center;
            transition: color 0.3s;
        }

        .back-link:hover {
            color: #004d40;
        }

        /* Style for the View button */
        .view-btn {
            text-decoration: none;
            color: #fff;
            background-color: #ffd700;
            /* Yellow color */
            padding: 6px 12px;
            border-radius: 6px;
            transition: background-color 0.3s;
        }

        .view-btn:hover {
            background-color: #e6b800;
            /* Darker yellow on hover */
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>Your Class Student's List</h1>

        <?php if ($studentsResult->num_rows > 0): ?>
            <table>
                <tr>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Admission Number</th>
                    <th>Email</th>
                    <th>Phone Number</th>
                    <th>Action</th>
                </tr>
                <?php while ($student = $studentsResult->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($student['std_firstName']); ?></td>
                        <td><?php echo htmlspecialchars($student['std_lastName']); ?></td>
                        <td><?php echo htmlspecialchars($student['std_admissionNumber']); ?></td>
                        <td><?php echo htmlspecialchars($student['std_email']); ?></td>
                        <td><?php echo htmlspecialchars($student['std_phone_number']); ?></td>
                        <td>
                            <a href="viewStudent.php?id=<?php echo $student['std_id']; ?>" class="view-btn">View</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </table>
        <?php else: ?>
            <p>No students are assigned to your class.</p>
        <?php endif; ?>

        <a href="index.php" class="back-link">Back to Dashboard</a>
    </div>
</body>

</html>