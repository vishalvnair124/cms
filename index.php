<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link href="img/logo/attnlg.jpg" rel="icon">
    <title>CMS - Choose Your Role</title>
    <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #4e73df, #1cc88a);
            color: #fff;
            font-family: "Arial", sans-serif;
        }

        .header {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 10px;
            margin: 20px 0;
            box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
        }

        .header img {
            height: 50px;
        }

        .header h1 {
            color: #4e73df;
            margin: 0;
            font-size: 28px;
            font-weight: bold;
            display: inline-block;
            vertical-align: middle;
        }

        .container {
            margin-top: 50px;
        }

        .card {
            background-color: #f8f9fc;
            border: none;
            border-radius: 10px;
            box-shadow: 0 5px 10px rgba(0, 0, 0, 0.3);
        }

        .card:hover {
            transform: scale(1.03);
            transition: all 0.3s ease-in-out;
        }

        .card-header {
            background-color: #4e73df;
            color: white;
            font-weight: bold;
            text-align: center;
            border-radius: 10px 10px 0 0;
        }

        .btn-role {
            font-size: 16px;
            padding: 15px;
        }

        .footer {
            margin-top: 20px;
            text-align: center;
        }

        .footer a {
            color: #f8f9fc;
            text-decoration: underline;
        }
    </style>
</head>

<body>
    <div class="container">
        <!-- Header Section -->
        <div class="header text-center">
            <img src="img/logo/attnlg.jpg" alt="CMS Logo">
            <h1>Campus Management System</h1>
        </div>

        <!-- Role Selection Section -->
        <div class="row justify-content-center">
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        Admin Login
                    </div>
                    <div class="card-body text-center">
                        <p class="card-text">Manage the campus with full administrative access.</p>
                        <a href="admin_login.php" class="btn btn-primary btn-role btn-block">Login as Admin</a>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        Teacher Login
                    </div>
                    <div class="card-body text-center">
                        <p class="card-text">Access your teaching resources and manage classes.</p>
                        <a href="teacher_login.php" class="btn btn-secondary btn-role btn-block">Login as Teacher</a>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        Student Login
                    </div>
                    <div class="card-body text-center">
                        <p class="card-text">Check assignments, grades, and other resources.</p>
                        <a href="student_login.php" class="btn btn-success btn-role btn-block">Login as Student</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- One-Time Registration Section -->
        <div class="text-center mt-5">
            <a href="one_time.php" class="btn btn-warning btn-lg">
                <i class="fas fa-user-plus"></i> One-Time Registration
            </a>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>&copy; <?php echo date("Y"); ?> Campus Management System. All Rights Reserved. | <a href="forgotPassword.php">Forgot Password?</a></p>
        </div>
    </div>

    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>

</html>