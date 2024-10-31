<?php
include '../Includes/dbcon.php'; // Include your database connection file

// Fetch total number of teachers
$teacherQuery = "SELECT COUNT(*) AS total FROM tblteachers";
$teacherResult = $conn->query($teacherQuery);
$totalTeachers = $teacherResult->fetch_assoc()['total'];

// Fetch total number of students
$studentQuery = "SELECT COUNT(*) AS total FROM tblstudents";
$studentResult = $conn->query($studentQuery);
$totalStudents = $studentResult->fetch_assoc()['total'];

// Fetch total number of courses
$courseQuery = "SELECT COUNT(*) AS total FROM tblcourse";
$courseResult = $conn->query($courseQuery);
$totalCourses = $courseResult->fetch_assoc()['total'];

// Fetch pending requests (students with status = 2)
$pendingRequestsQuery = "SELECT COUNT(*) AS total FROM tblstudents WHERE std_status = 2";
$pendingRequestsResult = $conn->query($pendingRequestsQuery);
$totalPendingRequests = $pendingRequestsResult->fetch_assoc()['total'];
?>

<style>
    .container {
        display: flex;
        flex-direction: column;
        /* Stack items vertically */
        align-items: center;
        /* Center items horizontally */
        margin-top: 20px;
        background-color: #f9f9f9;
        /* Light grey background for the container */
        padding: 20px;
        /* Add padding to the container */
        border-radius: 12px;
        /* Rounded corners for the container */
    }

    .box-container {
        display: flex;
        /* Use flexbox for layout */
        flex-wrap: wrap;
        /* Allow items to wrap */
        justify-content: center;
        /* Center the items in the container */
        margin-top: 20px;
        /* Space above the box container */
    }

    .box {
        border: 2px solid #5752e3;
        border-radius: 12px;
        padding: 20px;
        text-align: center;
        background-color: #252037;
        /* Your favorite color */
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s, box-shadow 0.3s;
        flex: 0 1 45%;
        /* Two boxes per row with some margin */
        margin: 10px;
        /* Margin between boxes */
        max-width: 300px;
        /* Set a maximum width for uniformity */
    }

    .box:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
    }

    h1 {
        color: #5752e3;
        /* Main color for the headings */
        text-align: center;
        /* Center the main heading */
        font-size: 2em;
        /* Increased font size for the main heading */
        margin-bottom: 20px;
        /* Margin below the main heading */
    }

    h2 {
        color: #072fe9;
        /* Light blue for the headings */
        margin: 0 0 10px;
        font-size: 1.8em;
        /* Increased font size for section headings */
    }

    p {
        font-size: 1.5em;
        /* Increased font size for paragraph text */
        margin: 0;
        font-weight: bold;
        /* Make the numbers bold */
        color: #072fe9;
        /* White color for the text */
    }

    .box:nth-child(even) {
        background-color: #252037;
    }

    .box:nth-child(odd) {
        background-color: #252037;
    }
</style>

<div class="container">
    <h1>Welcome Admin</h1>
    <div class="box-container">
        <div class="box">
            <h2>Total Teachers</h2>
            <p><?php echo $totalTeachers; ?></p>
        </div>
        <div class="box">
            <h2>Total Students</h2>
            <p><?php echo $totalStudents; ?></p>
        </div>
        <div class="box">
            <h2>Total Courses</h2>
            <p><?php echo $totalCourses; ?></p>
        </div>
        <div class="box">
            <h2>Pending Requests</h2>
            <p><?php echo $totalPendingRequests; ?></p>
        </div>
    </div>
</div>