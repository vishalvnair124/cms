<?php
session_start();
include('../includes/dbcon.php'); // Adjust path if needed

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if the student is logged in
if (!isset($_SESSION['userId'])) {
    die("Unauthorized access.");
}

// Get the student ID from session
$student_id = $_SESSION['userId'];

// Fetch the student's exam results
$query = "
    SELECT e.subject_name, e.subject_code, e.exam_date, e.maximum_marks, 
           es.marks_obtained, es.status
    FROM tblexam_stu es
    INNER JOIN tblexam e ON es.exam_id = e.exam_id
    WHERE es.student_id = $student_id";

$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Exam Results</title>
    <style>
        /* Matching CSS */
        body {
            font-family: Arial, sans-serif;
            background-color: #a9c2f9;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 800px;
            margin: 50px auto;
            background-color: #fff;
            padding: 30px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #333;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table th,
        table td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: center;
        }

        table th {
            background-color: #534edc;
            color: white;
        }

        table tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        .status-pass {
            color: green;
            font-weight: bold;
        }

        .status-fail {
            color: red;
            font-weight: bold;
        }

        .back-btn {
            display: block;
            margin: 20px auto;
            background-color: #5752e3;
            color: white;
            padding: 10px 20px;
            text-align: center;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
        }

        .back-btn:hover {
            background-color: #534edc;
        }
    </style>
</head>

<body>
    <div class="container">
        <h2>Your Exam Results</h2>

        <?php if ($result->num_rows > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Subject Name</th>
                        <th>Subject Code</th>
                        <th>Exam Date</th>
                        <th>Maximum Marks</th>
                        <th>Marks Obtained</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['subject_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['subject_code']); ?></td>
                            <td><?php echo htmlspecialchars($row['exam_date']); ?></td>
                            <td><?php echo htmlspecialchars($row['maximum_marks']); ?></td>
                            <td><?php echo htmlspecialchars($row['marks_obtained']); ?></td>
                            <td class="<?php echo $row['status'] ? 'status-pass' : 'status-fail'; ?>">
                                <?php echo $row['status'] ? 'Pass' : 'Fail'; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No exam results found.</p>
        <?php endif; ?>

        <a href="index.php" class="back-btn">Back to Dashboard</a>
    </div>
</body>

</html>