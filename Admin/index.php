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
                    <a href="?page=teachers.php" class="nav-option no-a" data-page="teachers.php">
                        <img src="../media/teacher_icon.png" class="nav-img" alt="profile">
                        <h3>Teachers</h3>
                    </a>
                    <a href="?page=coteachers.php" class="nav-option no-a" data-page="coteachers.php">
                        <img src="../media/teacher_icon.png" class="nav-img" alt="profile">
                        <h3>Co-Teachers</h3>
                    </a>
                    <a href="?page=students.php" class="nav-option no-a" data-page="students.php">
                        <img src="../media/profile_icon.png" class="nav-img" alt="profile">
                        <h3>Students</h3>
                    </a>
                    <a href="?page=courses.php" class="nav-option no-a" data-page="courses.php">
                        <img src="../media/courses_icon.png" class="nav-img" alt="profile">
                        <h3>Courses</h3>
                    </a>
                    <a href="?page=notification.php" class="nav-option no-a" data-page="notification.php">
                        <img src="../media/notification_icon.png" class="nav-img" alt="profile">
                        <h3>Notification</h3>
                    </a>


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