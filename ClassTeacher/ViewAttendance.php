<?php
session_start();
include '../Includes/dbcon.php'; // Ensure this path is correct for your setup

// Check if the teacher is logged in
if (!isset($_SESSION['userId'])) {
    die("Teacher not logged in");
}

$tea_id = $_SESSION['userId'];
$courses = [];

// Fetch courses where the teacher is a co-teacher
$course_query = $conn->prepare("
    SELECT tblcourse.course_id, tblcourse.course_name
    FROM tblcoteachers
    INNER JOIN tblcourse ON tblcoteachers.course_id = tblcourse.course_id
    WHERE tblcoteachers.tea_id = ?
");
$course_query->bind_param("i", $tea_id);
$course_query->execute();
$course_result = $course_query->get_result();

while ($row = $course_result->fetch_assoc()) {
    $courses[] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>View Attendance</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        h2 {
            text-align: center;
            margin-bottom: 30px;
            color: #5752e3;
        }

        form {
            background: white;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            margin: 0 auto;
            max-width: 800px;
        }

        label {
            display: block;
            margin: 25px 0 8px;
            font-weight: bold;
            font-size: 1.1em;
        }

        select,
        input[type="date"],
        button {
            width: 100%;
            padding: 15px;
            margin-bottom: 25px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 1em;
            transition: border-color 0.3s;
        }

        button {
            background-color: #5752e3;
            color: white;
            font-size: 1.1em;
            cursor: pointer;
            border: none;
        }

        button:hover {
            background-color: #4744c0;
        }

        #attendanceRecords {
            margin-top: 30px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            padding: 15px;
            text-align: center;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #5752e3;
            color: white;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        tr:hover {
            background-color: #e9ecef;
        }

        /* Styles for the No Records Message */
        .no-records {
            text-align: center;
            background-color: #f8d7da;
            color: #721c24;
            padding: 20px;
            border: 1px solid #f5c6cb;
            border-radius: 8px;
            margin-top: 20px;
        }

        .no-records h3 {
            margin: 0;
            font-size: 1.5em;
        }

        .no-records p {
            margin-top: 10px;
            font-size: 1.1em;
        }

        .no-access {
            text-align: center;
            background-color: #d1ecf1;
            color: #0c5460;
            padding: 20px;
            border: 1px solid #bee5eb;
            border-radius: 8px;
            margin: 20px auto;
            max-width: 800px;
        }

        .no-access h3 {
            margin: 0;
            font-size: 1.5em;
        }
    </style>
</head>

<body>
    <h2>View Attendance</h2>

    <?php if (count($courses) > 0): ?>
        <!-- Course Selection Form -->
        <form id="attendanceForm">
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

            <button type="submit">View Attendance</button>
        </form>

        <div id="attendanceRecords"></div>

        <script>
            $(document).ready(function() {
                $('#attendanceForm').on('submit', function(event) {
                    event.preventDefault(); // Prevent the default form submission

                    $.ajax({
                        url: 'fetchAttendance.php',
                        method: 'POST',
                        data: $(this).serialize(),
                        dataType: 'json',
                        success: function(data) {
                            displayAttendanceRecords(data);
                        },
                        error: function() {
                            alert('Error fetching attendance records.');
                        }
                    });
                });

                function displayAttendanceRecords(data) {
                    let html = '';

                    if (data.length > 0) {
                        html += '<table>';
                        html += '<thead><tr>';
                        html += '<th>Student ID</th>';
                        html += '<th>Student Name</th>';
                        html += '<th>Hour 1</th>';
                        html += '<th>Hour 2</th>';
                        html += '<th>Hour 3</th>';
                        html += '<th>Hour 4</th>';
                        html += '<th>Hour 5</th>';
                        html += '</tr></thead><tbody>';

                        data.forEach(function(record) {
                            html += '<tr>';
                            html += `<td>${record.std_id}</td>`;
                            html += `<td>${record.std_firstName} ${record.std_lastName}</td>`;
                            html += `<td>${record.att_hr_1 ? 'Present' : 'Absent'}</td>`;
                            html += `<td>${record.att_hr_2 ? 'Present' : 'Absent'}</td>`;
                            html += `<td>${record.att_hr_3 ? 'Present' : 'Absent'}</td>`;
                            html += `<td>${record.att_hr_4 ? 'Present' : 'Absent'}</td>`;
                            html += `<td>${record.att_hr_5 ? 'Present' : 'Absent'}</td>`;
                            html += '</tr>';
                        });

                        html += '</tbody></table>';
                    } else {
                        html += `
                            <div class="no-records">
                                <h3>No Attendance Records Found</h3>
                                <p>Please check the selected course and date.</p>
                            </div>
                        `;
                    }

                    $('#attendanceRecords').html(html);
                }
            });
        </script>
    <?php else: ?>
        <div class="no-access">
            <h3>No Access</h3>
            <p>You do not have any courses assigned as a co-teacher.</p>
        </div>
    <?php endif; ?>
</body>

</html>