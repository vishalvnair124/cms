<?php
session_start();
include '../Includes/dbcon.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    $delete_id = $_POST['delete_id'];

    // Delete the co-teacher
    $deleteQuery = "DELETE FROM tblcoteachers WHERE cotea_id = ?";
    $deleteStmt = $conn->prepare($deleteQuery);
    $deleteStmt->bind_param("i", $delete_id);

    if ($deleteStmt->execute()) {
        $message = "Co-teacher deleted successfully!";
    } else {
        $message = "Error deleting co-teacher: " . $conn->error;
    }

    $deleteStmt->close();
    $conn->close();

    // Display the message as an alert and redirect back
    echo "<script>
        alert('" . htmlspecialchars($message) . "');
        window.location.href = 'http://localhost/cms/Admin/index.php?page=coteachers.php';
    </script>";
    exit();
} else {
    echo "<script>
        alert('Invalid request.');
        window.location.href = 'http://localhost/cms/Admin/index.php?page=coteachers.php';
    </script>";
    exit();
}
