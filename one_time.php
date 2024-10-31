<?php
session_start();
include('./includes/dbcon.php');

// Function to generate the admission number
function generateAdmissionNumber($conn)
{
    $result = $conn->query("SELECT COUNT(*) as total FROM tblstudents");
    $count = $result->fetch_assoc()['total'];
    return 'ADM' . ($count + 1);
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];
    $otherName = $_POST['otherName'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $dob = $_POST['dob'];
    $address = $_POST['address'];
    $aadhar_no = $_POST['aadhar_no'];
    $parent_name = $_POST['parent_name'];
    $parent_phone = $_POST['parent_phone'];

    // Check if email or phone number already exists
    $checkQuery = "SELECT * FROM tblstudents WHERE std_email = '$email' OR std_phone_number = '$phone'";
    $checkResult = $conn->query($checkQuery);

    if ($checkResult->num_rows > 0) {
        echo "<script>alert('Student already registered with this email or phone number.');</script>";
    } else {
        // Generate admission number
        $admissionNumber = generateAdmissionNumber($conn);
        $defaultPassword = md5('12345');
        $status = 2; // Default status

        // Insert new student into database
        $insertQuery = "INSERT INTO tblstudents (std_firstName, std_lastName, std_otherName, std_admissionNumber, std_password, std_dateCreated, std_email, std_phone_number, stud_dob, std_address, std_aadhar_no, std_parent_name, std_parent_ph, std_status)
                        VALUES ('$firstName', '$lastName', '$otherName', '$admissionNumber', '$defaultPassword', NOW(), '$email', '$phone', '$dob', '$address', '$aadhar_no', '$parent_name', '$parent_phone', '$status')";

        if ($conn->query($insertQuery)) {
            echo "<script>alert('Registration successful! Your admission number is: $admissionNumber');</script>";
        } else {
            echo "<script>alert('Error during registration. Please try again.');</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Registration</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" integrity="sha384-k6RqeWeci5ZR/Lv4MR0sA0FfDOMF4zU0EYYWZ1uMEq3fzt5MQuvM1qq5v9Kzq42" crossorigin="anonymous">
    <style>
        /* Add your custom styles here */
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background-color: #8A2BE2;
            border-radius: 8px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
            color: white;
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #fff;
        }

        .form-group {
            margin-bottom: 15px;
        }

        label {
            font-weight: bold;
            margin-bottom: 5px;
            display: block;
            color: #fff;
        }

        input[type="text"],
        input[type="email"],
        input[type="number"],
        input[type="date"],
        textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
            transition: border-color 0.3s;
        }

        input[type="text"]:focus,
        input[type="email"]:focus,
        input[type="number"]:focus,
        input[type="date"]:focus,
        textarea:focus {
            border-color: #4da6ff;
            outline: none;
        }

        .submit-btn {
            background-color: #4da6ff;
            color: #fff;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            width: 100%;
            font-size: 16px;
            transition: background-color 0.3s, transform 0.2s;
        }

        .submit-btn:hover {
            background-color: #007bff;
            transform: scale(1.05);
        }

        .back-link {
            text-align: center;
            margin-top: 20px;
        }

        .back-link a {
            color: #fff;
            text-decoration: none;
        }

        .back-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>
    <div class="container">
        <h2>Student Registration</h2>
        <form method="POST" action="">
            <div class="form-group">
                <label for="firstName">First Name</label>
                <input type="text" name="firstName" required>
            </div>
            <div class="form-group">
                <label for="lastName">Last Name</label>
                <input type="text" name="lastName" required>
            </div>
            <div class="form-group">
                <label for="otherName">Other Name</label>
                <input type="text" name="otherName">
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="phone">Phone Number</label>
                <input type="text" name="phone" required>
            </div>
            <div class="form-group">
                <label for="dob">Date of Birth</label>
                <input type="date" name="dob">
            </div>
            <div class="form-group">
                <label for="address">Address</label>
                <textarea name="address" rows="4"></textarea>
            </div>
            <div class="form-group">
                <label for="aadhar_no">Aadhar Number</label>
                <input type="number" name="aadhar_no" required>
            </div>
            <div class="form-group">
                <label for="parent_name">Parent Name</label>
                <input type="text" name="parent_name" required>
            </div>
            <div class="form-group">
                <label for="parent_phone">Parent Phone Number</label>
                <input type="number" name="parent_phone" required>
            </div>
            <button type="submit" class="submit-btn">Register</button>
        </form>
        <div class="back-link">
            <a href="./index.php"><i class="fas fa-arrow-left"></i> Back to Home</a>
        </div>
    </div>
</body>

</html>