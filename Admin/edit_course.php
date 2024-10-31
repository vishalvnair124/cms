<?php
session_start();
include('../includes/dbcon.php');

// Check if course_id is set in the URL
if (isset($_GET['course_id'])) {
    $course_id = $_GET['course_id'];

    // Fetch course details
    $course_query = "SELECT c.course_name, c.course_start, ci.tea_id, ci.isActive 
                     FROM tblcourse AS c
                     LEFT JOIN tblcourseincharge AS ci ON c.course_id = ci.course_id 
                     WHERE c.course_id = '$course_id'";

    $course_result = $conn->query($course_query);
    $course = $course_result->fetch_assoc();

    if (!$course) {
        die("Course not found.");
    }

    // Fetch all teachers
    $teachers_result = $conn->query("SELECT tea_id, CONCAT(tea_firstName, ' ', tea_lastName) as teacher_name FROM tblteachers");

    // Handle the form submission
    if (isset($_POST['edit_course'])) {
        $new_course_name = $_POST['course_name'];
        $new_course_start = $_POST['course_start'];
        $new_teacher_id = $_POST['teacher_id'];
        $isActive = isset($_POST['is_active']) ? 1 : 0;

        // Check if the new teacher is already assigned to another active course
        $active_teacher_check = $conn->query("SELECT COUNT(*) as count FROM tblcourseincharge WHERE tea_id = '$new_teacher_id' AND isActive = 1");
        $active_teacher = $active_teacher_check->fetch_assoc();

        // If the selected teacher is active in another course, show an error message
        if ($active_teacher['count'] > 0 && $new_teacher_id != $course['tea_id']) {
            echo "<script>alert('The selected teacher is already assigned to another active course. Please select a different teacher.');</script>";
        } else {
            // Proceed with updating course details
            $conn->query("UPDATE tblcourse SET course_name = '$new_course_name', course_start = '$new_course_start' WHERE course_id = '$course_id'");

            // Check if teacher assignment has changed
            if ($new_teacher_id != $course['tea_id']) {
                // Update the teacher in-charge
                $conn->query("UPDATE tblcourseincharge SET tea_id = '$new_teacher_id', isActive = '$isActive' WHERE course_id = '$course_id'");

                // Update the old teacher's assignment status if needed
                $conn->query("UPDATE tblteachers SET tea_is_assigned = 0 WHERE tea_id = '{$course['tea_id']}'");
                // Update the new teacher's assignment status
                $conn->query("UPDATE tblteachers SET tea_is_assigned = 1 WHERE tea_id = '$new_teacher_id'");
            } else {
                // Update the active status only if the teacher hasn't changed
                $conn->query("UPDATE tblcourseincharge SET isActive = '$isActive' WHERE course_id = '$course_id'");
            }

            // Redirect to the courses page after successful update
            header("Location: ../Admin/index.php?page=courses.php");
            exit();
        }
    }
} else {
    die("Invalid course ID.");
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Course</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" integrity="sha384-k6RqeWeci5ZR/Lv4MR0sA0FfDOMF4zU0EYYWZ1uMEq3fzt5MQuvM1qq5v9Kzq42" crossorigin="anonymous">
    <style>
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
            /* Purple color */
            border-radius: 8px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
            color: white;
            /* Text color for better contrast */
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #fff;
            /* Header color */
        }

        .form-group {
            margin-bottom: 15px;
        }

        label {
            font-weight: bold;
            margin-bottom: 5px;
            display: block;
            color: #fff;
            /* Label color */
        }

        input[type="text"],
        input[type="number"],
        select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
            transition: border-color 0.3s;
        }

        input[type="text"]:focus,
        input[type="number"]:focus,
        select:focus {
            border-color: #4da6ff;
            outline: none;
        }

        .checkbox-container {
            display: flex;
            align-items: center;
        }

        .checkbox-container input {
            width: auto;
            margin-right: 10px;
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
            /* Slightly increase size on hover */
        }

        .back-link {
            text-align: center;
            margin-top: 20px;
        }

        .back-link a {
            color: #fff;
            /* Link color */
            text-decoration: none;
        }

        .back-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>

    <div class="container">
        <h2>Edit Course</h2>
        <form method="POST" action="edit_course.php?course_id=<?= $course_id ?>">
            <div class="form-group">
                <label for="course_name">Course Name</label>
                <input type="text" name="course_name" value="<?= htmlspecialchars($course['course_name']) ?>" required>
            </div>
            <div class="form-group">
                <label for="course_start">Course Start Year</label>
                <input type="number" name="course_start" value="<?= htmlspecialchars($course['course_start']) ?>" required min="2000" max="2100">
            </div>
            <div class="form-group">
                <label for="teacher_id">Course In-charge</label>
                <select name="teacher_id" required>
                    <option value="" disabled>Select a Teacher</option>
                    <?php while ($row = $teachers_result->fetch_assoc()): ?>
                        <option value="<?= $row['tea_id'] ?>" <?= $row['tea_id'] == $course['tea_id'] ? 'selected' : '' ?>>
                            <?= $row['teacher_name'] ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="form-group checkbox-container">
                <label for="is_active">Activate Course</label>
                <input type="checkbox" name="is_active" value="1" <?= $course['isActive'] ? 'checked' : '' ?>>
            </div>
            <button type="submit" class="submit-btn" name="edit_course">Save Changes</button>
        </form>
        <div class="back-link">
            <a href="../Admin/index.php?page=courses.php"><i class="fas fa-arrow-left"></i> Back to Courses</a>
        </div>
    </div>

</body>

</html>