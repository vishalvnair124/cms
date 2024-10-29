<?php
include 'session_check.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="../img/logo/attnlg.jpg" rel="icon">
    <title>CMS

    </title>
    <link rel="stylesheet" href="./homestyles/newstyle.css">
    <link rel="stylesheet" href="./homestyles/responsive.css">
    <script src="script.js"></script> <!-- jQuery for easier AJAX handling -->
</head>

<body>

    <!-- Header -->
    <header>
        <div class="logosec">
            <div class="logo">CMS🧑‍🎓</div>
            <img src="../media/icon-menu.png" class="icn menuicn" id="menuicn" alt="menu-icon">
        </div>
    </header>

    <div class="main-container">
        <!-- Sidebar Navigation -->
        <div class="navcontainer">
            <nav class="nav">
                <div class="nav-upper-options">
                    <a href="?page=dashboard.php" class="nav-option option1 no-a" data-page="dashboard.php">
                        <img src="../media/dashboard.png" class="nav-img" alt="dashboard">
                        <h3>Dashboard</h3>
                    </a>
                    <a href="?page=profile.php" class="nav-option no-a" data-page="profile.php">
                        <img src="../media/profile_icon.png" class="nav-img" alt="profile">
                        <h3>Teacher</h3>
                    </a>
                    <a href="?page=students.php" class="nav-option no-a" data-page="students.php">
                        <img src="../media/profile_icon.png" class="nav-img" alt="profile">
                        <h3>Students</h3>
                    </a>
                    <a href="?page=students.php" class="nav-option no-a" data-page="students.php">
                        <img src="../media/profile_icon.png" class="nav-img" alt="profile">
                        <h3>Courses</h3>
                    </a>
                    <a href="?page=notification.php" class="nav-option no-a" data-page="notification.php">
                        <img src="../media/profile_icon.png" class="nav-img" alt="profile">
                        <h3>Notification</h3>
                    </a>
                    <div>
                        <a href="" class="nav-option no-a" onclick="subdataToggle('att-subdata')">
                            <img src="../media/attendanc_icon.png" class="nav-img" alt="results">
                            <h3>Attendance</h3>

                        </a>
                        <div id='att-subdata'>
                            <a href="?page=AddAttendance.php" class="nav-option no-a" data-page="AddAttendance.php">
                                <img src="../media/results_icon.png" class="nav-img" alt="results">
                                <h3>Add Attendance</h3>
                            </a>
                            <a href="?page=ViewAttendance.php" class="nav-option no-a" data-page="ViewAttendance.php">
                                <img src="../media/results_icon.png" class="nav-img" alt="results">
                                <h3>View Attendance</h3>
                            </a>
                        </div>
                    </div>
                    <div>
                        <a href="" class="nav-option no-a" onclick="subdataToggle('res-subdata')">
                            <img src="../media/results_icon.png" class="nav-img" alt="results">
                            <h3>Exam</h3>
                        </a>
                        <div id='res-subdata'>
                            <a href="?page=AddExams.php" class="nav-option no-a" data-page="AddExams.php">
                                <img src="../media/results_icon.png" class="nav-img" alt="results">
                                <h3>Add Exams</h3>
                            </a>
                            <a href="?page=AddResuts.php" class="nav-option no-a" data-page="AddResuts.php">
                                <img src="../media/results_icon.png" class="nav-img" alt="results">
                                <h3>Add Resuts</h3>
                            </a>
                            <a href="?page=ViewResuts.php" class="nav-option no-a" data-page="ViewResuts.php">
                                <img src="../media/results_icon.png" class="nav-img" alt="results">
                                <h3>View Resuts</h3>
                            </a>
                        </div>
                    </div>


                    <a href="logout.php" class="no-a">
                        <div class="nav-option logout" data-page="logout.php">
                            <img src="../media/logout_icon.png" class="nav-img" alt="logout">
                            <h3>Logout</h3>
                        </div>
                    </a>
                </div>
            </nav>
        </div>


        <!-- Main Content -->
        <div class="main">
            <div id="content"></div> <!-- Dynamic content will be loaded here -->
        </div>
    </div>

    <script src="index.js"></script> <!-- Separate JS file for handling dynamic loading and other interactions -->
</body>

</html>