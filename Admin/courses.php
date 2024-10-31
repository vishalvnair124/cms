<?php
session_start();
include('../includes/dbcon.php');

// Fetch all teachers for dropdown
$teachers_result = $conn->query("SELECT tea_id, CONCAT(tea_firstName, ' ', tea_lastName) as teacher_name FROM tblteachers");

// Handle adding a new course
if (isset($_POST['add_course'])) {
    $course_name = $_POST['course_name'];
    $course_start = $_POST['course_start'];
    $teacher_id = $_POST['teacher_id'];
    $dateCreated = date('Y-m-d H:i:s');

    // Insert course into tblcourse
    $sql_course = "INSERT INTO tblcourse (course_name, course_start) VALUES ('$course_name', '$course_start')";

    if ($conn->query($sql_course) === TRUE) {
        $course_id = $conn->insert_id;

        // Insert in-charge into tblcourseincharge and set teacher assignment status
        $sql_courseincharge = "INSERT INTO tblcourseincharge (course_id, tea_id, isActive) VALUES ('$course_id', '$teacher_id', 1)";

        if ($conn->query($sql_courseincharge) === TRUE) {
            $conn->query("UPDATE tblteachers SET tea_is_assigned = 1 WHERE tea_id = '$teacher_id'");
            header("Location: ../Admin/index.php?page=courses.php");
            exit();
        } else {
            // Error while inserting into tblcourseincharge
            $error_message = "Error assigning teacher. Please try again.";
        }
    } else {
        // Error while inserting into tblcourse
        $error_message = "Error creating course. Please try again.";
    }
}

// Handle editing course in-charge
if (isset($_POST['edit_course'])) {
    $course_id = $_POST['course_id'];
    $new_teacher_id = $_POST['teacher_id'];
    $old_teacher_id = $_POST['old_teacher_id'];

    // Update in-charge teacher
    if ($conn->query("UPDATE tblcourseincharge SET tea_id = '$new_teacher_id' WHERE course_id = '$course_id'") === TRUE) {
        $conn->query("UPDATE tblteachers SET tea_is_assigned = 1 WHERE tea_id = '$new_teacher_id'");
        $conn->query("UPDATE tblteachers SET tea_is_assigned = 0 WHERE tea_id = '$old_teacher_id'");
        header("Location: ../Admin/index.php?page=courses.php");
        exit();
    } else {
        $error_message = "Error updating course in-charge. Please try again.";
    }
}

// Handle deactivating a course
if (isset($_GET['action']) && isset($_GET['course_id'])) {
    $course_id = $_GET['course_id'];
    $action = $_GET['action'];

    if ($action == 'deactivate') {
        $conn->query("UPDATE tblcourseincharge SET isActive = 0 WHERE course_id = '$course_id'");
        $conn->query("UPDATE tblteachers t INNER JOIN tblcourseincharge ci ON t.tea_id = ci.tea_id SET t.tea_is_assigned = 0 WHERE ci.course_id = '$course_id'");
    }
}

// Fetch all courses with in-charge details
$sql = "
    SELECT 
        c.course_id, 
        c.course_name, 
        c.course_start, 
        ci.tea_id, 
        CONCAT(t.tea_firstName, ' ', t.tea_lastName) as teacher_name,
        ci.isActive
    FROM 
        tblcourse AS c
    LEFT JOIN 
        tblcourseincharge AS ci ON c.course_id = ci.course_id
    LEFT JOIN 
        tblteachers AS t ON ci.tea_id = t.tea_id";
$courses_result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Courses</title>
    <style>
        .course-section {
            background-color: #e7f3ff;
            width: 80%;
            margin: 20px auto;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .form-container,
        .table-container {
            padding: 15px;
            border: 1px solid #b3d7ff;
            background-color: #ffffff;
            border-radius: 8px;
            margin-top: 20px;
        }

        .form-row {
            display: flex;
            flex-direction: column;
            margin-bottom: 15px;
        }

        .form-row label {
            margin-bottom: 5px;
            font-weight: bold;
        }

        .form-row input,
        .form-row select {
            padding: 10px;
            border: 1px solid #b3d7ff;
            border-radius: 4px;
            width: 100%;
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

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table,
        th,
        td {
            border: 1px solid #d1d5db;
        }

        th,
        td {
            padding: 12px;
            text-align: left;
        }

        th {
            background-color: #4da6ff;
            color: #fff;
        }

        tr:nth-child(even) {
            background-color: #e7f3ff;
        }

        .btn {
            padding: 8px 12px;
            color: #fff;
            text-decoration: none;
            border-radius: 4px;
            display: inline-block;
        }

        .btn-edit {
            background-color: #4CAF50;
            /* Green */
        }

        .btn-deactivate {
            background-color: #FF9800;
            /* Orange */
        }

        .btn:hover {
            opacity: 0.85;
        }

        .error-message {
            color: red;
            margin-bottom: 10px;
        }
    </style>
</head>

<body>

    <div class="course-section">
        <h2 class="text-center">Courses Management</h2>

        <!-- Error Message -->
        <?php if (isset($error_message)): ?>
            <div class="error-message"><?= htmlspecialchars($error_message) ?></div>
        <?php endif; ?>

        <!-- Add Course Form -->
        <div class="form-container">
            <h4>Add New Course</h4>
            <form method="POST" action="courses.php">
                <div class="form-row">
                    <label for="course_name">Course Name</label>
                    <input type="text" name="course_name" required>
                </div>
                <div class="form-row">
                    <label for="course_start">Course Start Year</label>
                    <input type="number" name="course_start" required min="2000" max="2100">
                </div>
                <div class="form-row">
                    <label for="teacher_id">Course In-charge</label>
                    <select name="teacher_id" required>
                        <option value="" disabled selected>Select a Teacher</option>
                        <?php while ($row = $teachers_result->fetch_assoc()): ?>
                            <option value="<?= $row['tea_id'] ?>"><?= $row['teacher_name'] ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <button type="submit" class="submit-btn" name="add_course">Add Course</button>
            </form>
        </div>

        <!-- Course List Table -->
        <div class="table-container">
            <h4>Existing Courses</h4>
            <table>
                <thead>
                    <tr>
                        <th>Course Name</th>
                        <th>Start Year</th>
                        <th>Course In-charge</th>
                        <th>Edit</th>
                        <th>Deactivate</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($courses_result->num_rows > 0): ?>
                        <?php while ($course = $courses_result->fetch_assoc()): ?>
                            <tr>
                                <td><?= htmlspecialchars($course['course_name']) ?></td>
                                <td><?= htmlspecialchars($course['course_start']) ?></td>
                                <td><?= htmlspecialchars($course['teacher_name']) ?></td>
                                <td>
                                    <a href="edit_course.php?course_id=<?= $course['course_id'] ?>&teacher_id=<?= $course['tea_id'] ?>" class="btn btn-edit">Edit</a>
                                </td>
                                <td>
                                    <?php if ($course['isActive']): ?>
                                        <span>active</span>
                                        <!-- <a href="../Admin/index.php?page=courses.php?action=deactivate&course_id=<?= $course['course_id'] ?>" class="btn btn-deactivate">Deactivate</a> -->
                                    <?php else: ?>
                                        <span>Inactive</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center">No Courses Available</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>

</html>