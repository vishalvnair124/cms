<?php
session_start();
include('../includes/dbcon.php'); // Database connection

if (file_exists('../includes/session_check.php')) {
    include('../includes/session_check.php');
} else {
    echo "<script>console.warn('Session check file not found.');</script>";
}

// Add New Teacher
if (isset($_POST['add_teacher'])) {
    $firstName = $_POST['first_name'];
    $lastName = $_POST['last_name'];
    $email = $_POST['email'];
    $password = md5(12345);
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $dateCreated = date('Y-m-d H:i:s');

    $sql = "INSERT INTO tblteachers (tea_firstName, tea_lastName, tea_emailAddress, tea_password, tea_phoneNo, tea_dateCreated, tea_address) 
            VALUES ('$firstName', '$lastName', '$email', '$password', '$phone', '$dateCreated', '$address')";
    $conn->query($sql);
    header("Location: index.php?page=teachers.php"); // Redirect to the same page
    exit();
}

// Delete Teacher
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $conn->query("DELETE FROM tblteachers WHERE tea_id=$id");
    header("Location: index.php?page=teachers.php");
    exit();
}

// Get teacher data for editing
$edit_teacher = false;
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $result = $conn->query("SELECT * FROM tblteachers WHERE tea_id=$id");
    $teacher = $result->fetch_assoc();
    $edit_teacher = true;
}

// Update Teacher
if (isset($_POST['update_teacher'])) {
    $id = $_POST['tea_id'];
    $firstName = $_POST['first_name'];
    $lastName = $_POST['last_name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];

    $sql = "UPDATE tblteachers SET 
            tea_firstName='$firstName', tea_lastName='$lastName', tea_emailAddress='$email', 
            tea_phoneNo='$phone', tea_address='$address' WHERE tea_id=$id";
    $conn->query($sql);
    header("Location: index.php?page=teachers.php");
    exit();
}
?>


<style>
    /* Main container */
    .teachers-section {
        background-color: #e7f3ff;
        width: 80%;
        margin: 20px auto;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }

    /* Form section */
    .form-container {
        margin-bottom: 30px;
        padding: 15px;
        border: 1px solid #b3d7ff;
        background-color: #ffffff;
        border-radius: 8px;
    }

    .form-container h4 {
        color: #004085;
        margin-bottom: 15px;
    }

    .form-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 10px;
    }

    .form-row input {
        width: 48%;
        padding: 10px;
        border: 1px solid #b3d7ff;
        border-radius: 4px;
    }

    .submit-btn {
        background-color: #4da6ff;
        color: #fff;
        padding: 10px 15px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        margin-top: 10px;
    }

    /* Teachers list */
    .list-container {
        padding: 15px;
        background-color: #ffffff;
        border-radius: 8px;
        border: 1px solid #b3d7ff;
    }

    .list-container h4 {
        color: #004085;
        margin-bottom: 15px;
    }

    table {
        width: 100%;
        border-collapse: collapse;
    }

    table th,
    table td {
        padding: 12px;
        text-align: left;
        border-bottom: 1px solid #b3d7ff;
    }

    table thead {
        background-color: #d0e7ff;
    }

    .btn {
        padding: 5px 10px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        color: #fff;
    }

    .btn-edit {
        background-color: #4da6ff;
    }

    .btn-delete {
        background-color: #ff6666;
    }

    a {
        text-decoration: none;
    }
</style>


<div class="teachers-section">
    <h2 class="text-center">Manage Teachers</h2>

    <!-- Add/Edit Teacher Form -->
    <div class="form-container">
        <h4><?php echo $edit_teacher ? 'Edit Teacher' : 'Add New Teacher'; ?></h4>
        <form method="POST" action="teachers.php">
            <input type="hidden" name="tea_id" value="<?= $edit_teacher ? $teacher['tea_id'] : '' ?>">
            <div class="form-row">
                <input type="text" name="first_name" placeholder="First Name"
                    value="<?= $edit_teacher ? $teacher['tea_firstName'] : '' ?>" required>
                <input type="text" name="last_name" placeholder="Last Name"
                    value="<?= $edit_teacher ? $teacher['tea_lastName'] : '' ?>" required>
            </div>
            <div class="form-row">
                <input type="email" name="email" placeholder="Email"
                    value="<?= $edit_teacher ? $teacher['tea_emailAddress'] : '' ?>" required>
                <input type="text" name="phone" placeholder="Phone Number"
                    value="<?= $edit_teacher ? $teacher['tea_phoneNo'] : '' ?>" required>
            </div>
            <div class="form-row">
                <input type="text" name="address" placeholder="Address"
                    value="<?= $edit_teacher ? $teacher['tea_address'] : '' ?>" required>
            </div>
            <?php if ($edit_teacher): ?>
                <button type="submit" name="update_teacher" class="submit-btn">Update Teacher</button>
            <?php else: ?>
                <!-- <input type="password" name="password" placeholder="Password" required> -->
                <button type="submit" name="add_teacher" class="submit-btn">Add Teacher</button>
            <?php endif; ?>
        </form>
    </div>

    <!-- Teachers List -->
    <div class="list-container">
        <h4>Teachers List</h4>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Address</th>
                    <th>Edit</th>
                    <th>Delete</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $result = $conn->query("SELECT * FROM tblteachers");
                while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= $row['tea_id'] ?></td>
                        <td><?= $row['tea_firstName'] . ' ' . $row['tea_lastName'] ?></td>
                        <td><?= $row['tea_emailAddress'] ?></td>
                        <td><?= $row['tea_phoneNo'] ?></td>
                        <td><?= $row['tea_address'] ?></td>
                        <td>
                            <a href="index.php?page=teachers.php?edit=<?= $row['tea_id'] ?>" class="btn btn-edit">Edit</a>
                        </td>
                        <td>

                            <a href="teachers.php?delete=<?= $row['tea_id'] ?>" class="btn btn-delete"
                                onclick="return confirm('Are you sure you want to delete this teacher?');">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>