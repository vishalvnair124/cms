<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Take Attendance</title>
</head>

<body>
    <h2>Take Attendance</h2>
    <!-- Course Selection Form -->
    <form method="POST" action="load_students.php">
        <label for="course_id">Select Course:</label>
        <select name="course_id" required>
            <option value="">-- Select Course --</option>
            <?php foreach ($courses as $course): ?>
                <option value="<?= htmlspecialchars($course['course_id']) ?>">
                    <?= htmlspecialchars($course['course_name']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label for="att_date">Select Date:</label>
        <input type="date" name="att_date" required>

        <button type="submit">Load Students</button>
    </form>

    <?php if (!empty($students)): ?>
        <form method="POST" action="submit_attendance.php">
            <input type="hidden" name="course_id" value="<?= htmlspecialchars($course_id) ?>">
            <input type="hidden" name="att_date" value="<?= htmlspecialchars($att_date) ?>">

            <table border="1">
                <tr>
                    <th>Student ID</th>
                    <th>Student Name</th>
                    <th>Hour 1</th>
                    <th>Hour 2</th>
                    <th>Hour 3</th>
                    <th>Hour 4</th>
                    <th>Hour 5</th>
                </tr>

                <?php foreach ($students as $student): ?>
                    <tr>
                        <td><?= htmlspecialchars($student['std_id']) ?></td>
                        <td><?= htmlspecialchars($student['std_firstName'] . ' ' . $student['std_lastName']) ?></td>
                        <td><input type="checkbox" name="attendance[<?= $student['std_id'] ?>][hr1]" value="1"></td>
                        <td><input type="checkbox" name="attendance[<?= $student['std_id'] ?>][hr2]" value="1"></td>
                        <td><input type="checkbox" name="attendance[<?= $student['std_id'] ?>][hr3]" value="1"></td>
                        <td><input type="checkbox" name="attendance[<?= $student['std_id'] ?>][hr4]" value="1"></td>
                        <td><input type="checkbox" name="attendance[<?= $student['std_id'] ?>][hr5]" value="1"></td>
                    </tr>
                <?php endforeach; ?>
            </table>
            <button type="submit">Submit Attendance</button>
        </form>
    <?php endif; ?>
</body>

</html>