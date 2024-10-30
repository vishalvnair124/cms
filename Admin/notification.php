<?php
session_start();
include('../includes/dbcon.php'); // Database connection

if (file_exists('../includes/session_check.php')) {
    include('../includes/session_check.php');
} else {
    echo "<script>console.warn('Session check file not found.');</script>";
}

// Add New Notification
if (isset($_POST['add_notification'])) {
    $title = $_POST['notification_title'];
    $text = $_POST['notification_text'];
    $status = 1; // Active by default
    $sql = "INSERT INTO tblnotification (notification_title, notification_text, notification_status) 
            VALUES ('$title', '$text', '$status')";
    $conn->query($sql);
    header("Location: index.php?page=notification.php"); // Redirect to the same page
    exit();
}

// Delete Notification
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $conn->query("DELETE FROM tblnotification WHERE notification_id=$id");
    header("Location: index.php?page=notification.php");
    exit();
}

// Activate/Deactivate Notification
if (isset($_GET['status'])) {
    $id = $_GET['status'];
    $currentStatus = $_GET['current_status'];
    $newStatus = $currentStatus == 1 ? 0 : 1; // Toggle status
    $conn->query("UPDATE tblnotification SET notification_status=$newStatus WHERE notification_id=$id");
    header("Location: index.php?page=notification.php");
    exit();
}

// Get notification data for editing
$edit_notification = false;
$notification = [];
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $result = $conn->query("SELECT * FROM tblnotification WHERE notification_id=$id");

    // Debugging: Check if a row is fetched
    if ($result && $result->num_rows > 0) {
        $notification = $result->fetch_assoc();
        $edit_notification = true;
    } else {
        echo "<script>alert('Notification not found!');</script>";
    }
}

// Update Notification
if (isset($_POST['update_notification'])) {
    $id = $_POST['notification_id'];
    $title = $_POST['notification_title'];
    $text = $_POST['notification_text'];
    $sql = "UPDATE tblnotification SET 
            notification_title='$title', notification_text='$text' WHERE notification_id=$id";
    if ($conn->query($sql) === TRUE) {
        header("Location: index.php?page=notification.php");
        exit();
    } else {
        echo "<script>alert('Database error: " . $conn->error . "');</script>";
    }
}
?>

<style>
    /* Main container */
    .notifications-section {
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

    .form-row input,
    .form-row textarea {
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

    /* Notifications list */
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

    .btn-status {
        background-color: #ffcc00;
    }

    a {
        text-decoration: none;
    }
</style>

<div class="notifications-section">
    <h2 class="text-center">Manage Notifications</h2>

    <!-- Add/Edit Notification Form -->
    <div class="form-container">
        <h4><?php echo $edit_notification ? 'Edit Notification' : 'Add New Notification'; ?></h4>
        <form method="POST" action="notification.php">
            <input type="hidden" name="notification_id" value="<?= $edit_notification ? $notification['notification_id'] : '' ?>">
            <div class="form-row">
                <input type="text" name="notification_title" placeholder="Notification Title"
                    value="<?= $edit_notification ? htmlspecialchars($notification['notification_title']) : '' ?>" required>
                <input type="text" name="notification_text" placeholder="Notification Text"
                    value="<?= $edit_notification ? htmlspecialchars($notification['notification_text']) : '' ?>" required>
            </div>
            <?php if ($edit_notification): ?>
                <button type="submit" name="update_notification" class="submit-btn">Update Notification</button>
            <?php else: ?>
                <button type="submit" name="add_notification" class="submit-btn">Add Notification</button>
            <?php endif; ?>
        </form>
    </div>

    <!-- Notifications List -->
    <div class="list-container">
        <h4>Notifications List</h4>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>Text</th>
                    <th>Status</th>
                    <th>Edit</th>
                    <th>Delete</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $result = $conn->query("SELECT * FROM tblnotification");
                while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= $row['notification_id'] ?></td>
                        <td><?= $row['notification_title'] ?></td>
                        <td><?= $row['notification_text'] ?></td>
                        <td>
                            <a href="notification.php?status=<?= $row['notification_id'] ?>&current_status=<?= $row['notification_status'] ?>" class="btn btn-status">
                                <?= $row['notification_status'] == 1 ? 'Deactivate' : 'Activate' ?>
                            </a>
                        </td>
                        <td>
                            <a href="index.php?page=notification.php?edit=<?= $row['notification_id'] ?>" class="btn btn-edit">Edit</a>
                        </td>
                        <td>
                            <a href="notification.php?delete=<?= $row['notification_id'] ?>" class="btn btn-delete"
                                onclick="return confirm('Are you sure you want to delete this notification?');">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>