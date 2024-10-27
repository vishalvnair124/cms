<?php
session_start();
include('../includes/dbcon.php'); // Adjust path if needed

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if the student is logged in
if (!isset($_SESSION['userId'])) {
    die("You need to log in to access this page.");
}

// Get the student ID from session
$student_id = $_SESSION['userId'];

// Fetch the latest published exam result for the student
$query = "
    SELECT e.subject_name, e.subject_code, e.exam_date, e.maximum_marks, 
           es.marks_obtained, es.status
    FROM tblexam_stu es
    INNER JOIN tblexam e ON es.exam_id = e.exam_id
    WHERE es.std_id = ? 
    ORDER BY e.exam_date DESC
    LIMIT 1"; // Fetch only the latest exam result

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();

// Handle attendance based on view type (day or month)
$viewType = isset($_POST['viewType']) ? $_POST['viewType'] : 'day';
$dateInput = isset($_POST['dateInput']) ? $_POST['dateInput'] : date('Y-m-d');

if ($viewType === 'day') {
    $attendanceQuery = "SELECT Att_date, att_hr_1, att_hr_2, att_hr_3, att_hr_4, att_hr_5 
                        FROM tblattendance 
                        WHERE std_id = ? AND Att_date = ?";
    $stmt = $conn->prepare($attendanceQuery);
    $stmt->bind_param("is", $student_id, $dateInput);
} else {
    $dateInput = substr($dateInput, 0, 7) . '%'; // Format for month search (YYYY-MM)
    $attendanceQuery = "SELECT Att_date, att_hr_1, att_hr_2, att_hr_3, att_hr_4, att_hr_5 
                        FROM tblattendance 
                        WHERE std_id = ? AND Att_date LIKE ?";
    $stmt = $conn->prepare($attendanceQuery);
    $stmt->bind_param("is", $student_id, $dateInput);
}
$stmt->execute();
$attendanceResult = $stmt->get_result();

// Fetch notifications from the database
$notificationQuery = "SELECT notification_title, notification_text
                      FROM tblnotification 
                      WHERE notification_status = 1 
                      ORDER BY notification_id ";
$notificationStmt = $conn->prepare($notificationQuery);
$notificationStmt->execute();
$notificationResult = $notificationStmt->get_result();

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard</title>
    <style>
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
    <style>
        .notification-box {
            max-height: 300px;
            /* Increase the maximum height */
            overflow: hidden;
            /* Hide overflow */
            background-color: #f9f9f9;
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 20px;
            /* Increase padding */
            margin-bottom: 20px;
            white-space: nowrap;
            /* Prevent line breaks */
        }

        .notification-title {
            font-weight: bold;
            /* Make title bold */
            font-size: 1.2em;
            /* Increase title font size */
        }

        .notification {
            display: inline-block;
            /* Inline block for notifications */
            padding: 15px 25px;
            /* Increase padding for notifications */
            border-radius: 5px;
            margin-right: 10px;
            /* Space between notifications */
            background-color: #e3f2fd;
            /* Light background color for notifications */
            transition: transform 0.3s;
            /* Smooth transition for hover effect */
        }

        .notification-message {
            font-size: 1em;
            /* Increase message font size */
        }

        .notification-container {
            display: flex;
            /* Use flexbox for alignment */
            animation: scroll-left 40s linear infinite;
            /* Adjust speed for faster scrolling */
        }



        .notification {
            display: inline-block;
            /* Inline block for notifications */
            padding: 15px 25px;
            /* Increase padding for notifications */
            border-radius: 5px;
            margin-right: 10px;
            /* Space between notifications */
            background-color: #e3f2fd;
            /* Light background color for notifications */
            transition: transform 0.3s;
            /* Smooth transition for hover effect */
        }

        .notification:hover {
            /* transform: scale(1.05); */
            /* Slightly enlarge on hover */
            background-color: #bbdefb;
            /* Darker shade on hover */
        }

        @keyframes scroll-left {
            0% {
                transform: translateX(0);
                /* Start at the original position */
            }

            100% {
                transform: translateX(-100%);
                /* Move to the left until just off the left side */
            }
        }
    </style>

</head>

<body>
    <div class="container">
        <!-- Notification Box -->
        <div class="notification-box">
            <h3>Notifications</h3>
            <div class="notification-container">
                <?php if ($notificationResult->num_rows > 0): ?>
                    <?php while ($notification = $notificationResult->fetch_assoc()): ?>
                        <div class="notification">
                            <div class="notification-title"><?php echo htmlspecialchars($notification['notification_title']); ?></div>
                            <div class="notification-message"><?php echo htmlspecialchars($notification['notification_text']); ?></div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="notification">No new notifications.</div>
                <?php endif; ?>
            </div>
        </div>



        <h2>Your Last Published Exam Result</h2>

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
                    <?php $row = $result->fetch_assoc(); ?>
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
                </tbody>
            </table>
        <?php else: ?>
            <p>No exam results found.</p>
        <?php endif; ?>

        <h2>Your Attendance</h2>
        <?php if ($attendanceResult->num_rows > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Hour 1</th>
                        <th>Hour 2</th>
                        <th>Hour 3</th>
                        <th>Hour 4</th>
                        <th>Hour 5</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $attendanceResult->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['Att_date']); ?></td>
                            <td><?php echo $row['att_hr_1'] == 1 ? 'Present' : 'Absent'; ?></td>
                            <td><?php echo $row['att_hr_2'] == 1 ? 'Present' : 'Absent'; ?></td>
                            <td><?php echo $row['att_hr_3'] == 1 ? 'Present' : 'Absent'; ?></td>
                            <td><?php echo $row['att_hr_4'] == 1 ? 'Present' : 'Absent'; ?></td>
                            <td><?php echo $row['att_hr_5'] == 1 ? 'Present' : 'Absent'; ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No attendance records found.</p>
        <?php endif; ?>







        <!-- <a href="logout.php" class="back-btn">Logout</a> -->
    </div>
</body>

</html>