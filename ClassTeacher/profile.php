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

// Initialize variables
$firstName = $lastName = $email = $phone = $admissionNumber = "";

// Get the student’s ID from the session
$student_id = $_SESSION['userId'];

// Fetch student details from the database
$query = "SELECT firstName, lastName, admissionNumber, email, phone_number 
          FROM tblstudents 
          WHERE Id = $student_id";

$result = $conn->query($query);
if ($result && $result->num_rows == 1) {
    $row = $result->fetch_assoc();
    $firstName = $row['firstName'];
    $lastName = $row['lastName'];
    $admissionNumber = $row['admissionNumber'];
    $email = $row['email'];
    $phone = $row['phone_number'];
} else {
    echo "<script>alert('Student record not found.');</script>";
}

// Form HTML
?>
<style>
    /* Same CSS as before */
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

    form input {
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
    <h2>Update Your Profile</h2>

    <form id="updateForm">
        <label for="firstName">First Name:</label>
        <input type="text" id="firstName" name="firstName" value="<?php echo htmlspecialchars($firstName); ?>" readonly>

        <label for="lastName">Last Name:</label>
        <input type="text" id="lastName" name="lastName" value="<?php echo htmlspecialchars($lastName); ?>" readonly>

        <label for="admissionNumber">Admission Number:</label>
        <input type="text" id="admissionNumber" name="admissionNumber" value="<?php echo htmlspecialchars($admissionNumber); ?>" readonly>

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>

        <label for="phone">Phone Number:</label>
        <input type="text" id="phone" name="phone" value="<?php echo htmlspecialchars($phone); ?>" required>

        <label for="password">New Password (Optional):</label>
        <input type="password" id="password" name="password">

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