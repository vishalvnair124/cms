<?php
session_start();
include('../includes/dbcon.php'); // Ensure path is correct

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if the student is logged in
if (!isset($_SESSION['userId'])) {
    die("Unauthorized access.");
}

// Get student ID from the session
$student_id = $_SESSION['userId'];

// Initialize variables to store student details
$studentData = [];

// Query to fetch all student details
$query = "SELECT * FROM tblstudents WHERE std_id = ?";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result && $result->num_rows == 1) {
    $studentData = $result->fetch_assoc();
} else {
    echo "<script>alert('Student record not found.');</script>";
}
$stmt->close();
?>

<!-- HTML Form to Display and Update Student Data -->
<style>
    body {
        font-family: Arial, sans-serif;
        background-color: #a9c2f9;
        margin: 0;
        padding: 0;
    }

    .container {
        max-width: 600px;
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

    form label {
        display: block;
        margin-top: 15px;
        font-weight: bold;
        color: #555;
    }

    form input,
    form textarea {
        width: 100%;
        padding: 10px;
        margin-top: 5px;
        border: 1px solid #ddd;
        border-radius: 5px;
        margin-bottom: 10px;
    }

    form input[readonly] {
        background-color: #f2f2f2;
        cursor: not-allowed;
    }

    .btn {
        display: block;
        width: 100%;
        background-color: #5752e3;
        color: white;
        padding: 10px;
        text-align: center;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        margin-top: 20px;
    }

    .btn:hover {
        background-color: #534edc;
    }
</style>

<div class="container">
    <h2>Your Profile</h2>
    <form id="updateForm">
        <label for="std_firstName">First Name:</label>
        <input type="text" id="std_firstName" name="std_firstName"
            value="<?php echo htmlspecialchars($studentData['std_firstName'] ?? ''); ?>" readonly>

        <label for="std_lastName">Last Name:</label>
        <input type="text" id="std_lastName" name="std_lastName"
            value="<?php echo htmlspecialchars($studentData['std_lastName'] ?? ''); ?>" readonly>

        <label for="std_otherName">Other Name:</label>
        <input type="text" id="std_otherName" name="std_otherName"
            value="<?php echo htmlspecialchars($studentData['std_otherName'] ?? ''); ?>" readonly>

        <label for="std_admissionNumber">Admission Number:</label>
        <input type="text" id="std_admissionNumber" name="std_admissionNumber"
            value="<?php echo htmlspecialchars($studentData['std_admissionNumber'] ?? ''); ?>" readonly>

        <label for="std_email">Email:</label>
        <input type="email" id="std_email" name="std_email"
            value="<?php echo htmlspecialchars($studentData['std_email'] ?? ''); ?>" required>

        <label for="std_phone_number">Phone Number:</label>
        <input type="text" id="std_phone_number" name="std_phone_number"
            value="<?php echo htmlspecialchars($studentData['std_phone_number'] ?? ''); ?>" required>

        <label for="stud_dob">Date of Birth:</label>
        <input type="date" id="stud_dob" name="stud_dob"
            value="<?php echo htmlspecialchars($studentData['stud_dob'] ?? ''); ?>" readonly>

        <label for="std_address">Address:</label>
        <textarea id="std_address" name="std_address"><?php echo htmlspecialchars($studentData['std_address'] ?? ''); ?></textarea>

        <label for="std_aadhar_no">Aadhar Number:</label>
        <input type="text" id="std_aadhar_no" name="std_aadhar_no"
            value="<?php echo htmlspecialchars($studentData['std_aadhar_no'] ?? ''); ?>" readonly>

        <label for="std_parent_name">Parent's Name:</label>
        <input type="text" id="std_parent_name" name="std_parent_name"
            value="<?php echo htmlspecialchars($studentData['std_parent_name'] ?? ''); ?>" readonly>

        <label for="std_parent_ph">Parent's Phone:</label>
        <input type="text" id="std_parent_ph" name="std_parent_ph"
            value="<?php echo htmlspecialchars($studentData['std_parent_ph'] ?? ''); ?>" required>

        <label for="std_password">Password:</label>
        <input type="password" id="std_password" name="std_password"
            value="" placeholder="Enter new password (optional)">

        <button type="submit" class="btn">Update Profile</button>
    </form>
</div>

<script>
    // Handle form submission using AJAX
    document.getElementById('updateForm').addEventListener('submit', function(event) {
        event.preventDefault(); // Prevent form from reloading the page

        const formData = new FormData(this);

        fetch('update_profile.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                alert(data.message); // Show success or error message
            })
            .catch(error => {
                alert('An error occurred: ' + error.message);
            });
    });
</script>