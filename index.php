<?php
include 'Includes/dbcon.php';
session_start();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link href="img/logo/attnlg.jpg" rel="icon">
    <title>CMS

    </title>
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css">
    <link href="css/ruang-admin.min.css" rel="stylesheet">
</head>

<body class="bg-gradient-login">
    <div> <a href="./one_time.php"><Button></Button></a></div>
    <div class="container-login">
        <div class="row justify-content-center">
            <div class="col-xl-10 col-lg-12 col-md-9">
                <div class="card shadow-sm my-5">
                    <div class="card-body p-0">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="login-form">
                                    <div class="text-center">
                                        <img src="img/logo/attnlg.jpg" style="width:100px;height:100px">
                                        <br><br>
                                        <h1 class="h4 text-gray-900 mb-4">Login</h1>
                                    </div>

                                    <!-- Role Dropdown -->
                                    <div class="form-group">
                                        <select class="form-control" id="roleSelect" required>
                                            <option value="admin">Admin</option>
                                            <option value="teacher">Class Teacher</option>
                                            <option value="student">Student</option>
                                        </select>
                                    </div>

                                    <form class="user" method="post" action="">
                                        <div class="form-group">
                                            <input type="text" class="form-control" required name="username"
                                                placeholder="Enter Email / Admission Number">
                                        </div>
                                        <div class="form-group">
                                            <input type="password" name="password" required class="form-control"
                                                placeholder="Enter Password">
                                        </div>
                                        <input type="hidden" name="role" id="roleInput">
                                        <div class="form-group">
                                            <input type="submit" class="btn btn-primary btn-block" value="Login" name="login" />
                                        </div>
                                    </form>

                                    <?php
                                    if (isset($_POST['login'])) {
                                        $username =  $_POST['username'];
                                        $password = md5($_POST['password']); // Hashing password
                                        $role = $_POST['role'];

                                        // Verify the role and prepare the appropriate query
                                        if ($role == 'admin') {
                                            $query = "SELECT * FROM tbladmin WHERE adm_emailAddress = ? AND adm_password = ?";
                                            $redirect = 'Admin/index.php';
                                        } elseif ($role == 'teacher') {
                                            $query = "SELECT * FROM tblteachers WHERE tea_emailAddress = ? AND tea_password = ?";
                                            $redirect = 'ClassTeacher/index.php';
                                        } elseif ($role == 'student') {
                                            $query = "SELECT * FROM tblstudents WHERE std_admissionNumber = ? AND std_password = ?";
                                            $redirect = 'Student/index.php';
                                        }


                                        if (isset($query)) {
                                            // Prepare the SQL statement
                                            $stmt = $conn->prepare($query);

                                            // Bind parameters based on role
                                            if ($role == 'student') {
                                                $stmt->bind_param("ss", $username, $password);
                                            } else {
                                                $stmt->bind_param("ss", $username, $password);
                                            }

                                            $stmt->execute();
                                            $result = $stmt->get_result();

                                            if ($result && $result->num_rows > 0) {
                                                $rows = $result->fetch_assoc();

                                                // Set session variables based on the role
                                                if ($role == 'admin') {
                                                    $_SESSION['userId'] = $rows['adm_Id'];
                                                    $_SESSION['firstName'] = $rows['adm_firstName'];
                                                    $_SESSION['lastName'] = $rows['adm_lastName'];
                                                    $_SESSION['emailAddress'] = $rows['adm_emailAddress'];
                                                } elseif ($role == 'teacher') {
                                                    $_SESSION['userId'] = $rows['tea_id'];
                                                    $_SESSION['firstName'] = $rows['tea_firstName'];
                                                    $_SESSION['lastName'] = $rows['tea_lastName'];
                                                    $_SESSION['emailAddress'] = $rows['tea_emailAddress'];
                                                } elseif ($role == 'student') {
                                                    $_SESSION['userId'] = $rows['std_id'];
                                                    $_SESSION['firstName'] = $rows['std_firstName'];
                                                    $_SESSION['lastName'] = $rows['std_lastName'];
                                                    $_SESSION['emailAddress'] = $rows['std_email'];
                                                }

                                                echo "<script type='text/javascript'>
                                                        window.location = ('$redirect');
                                                      </script>";
                                            } else {
                                                echo "<div class='alert alert-danger' role='alert'>
                                                        Invalid Username/Password!
                                                      </div>";
                                            }
                                            $stmt->close();
                                        }
                                    }
                                    ?>

                                    <hr>
                                    <div class="text-center">
                                        <a class="font-weight-bold small" href="forgotPassword.php">Forgot Password?</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="js/ruang-admin.min.js"></script>

    <script>
        const roleSelect = document.getElementById('roleSelect');
        const roleInput = document.getElementById('roleInput');

        roleSelect.addEventListener('change', function() {
            roleInput.value = roleSelect.value;
        });

        roleInput.value = roleSelect.value; // Initialize with default value
    </script>
</body>

</html>