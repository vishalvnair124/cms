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

// Check if there are courses assigned to the teacher
if (empty($courses)) {
    echo "<h2>You do not have any courses assigned. Please contact your administrator.</h2>";
    exit; // Stop further execution if no courses
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Add Attendance</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            $('form#courseForm').on('submit', function(event) {
                event.preventDefault(); // Prevent default form submission

                $.ajax({
                    type: 'POST',
                    url: 'load_students.php', // URL to load students
                    data: $(this).serialize(), // Serialize form data
                    dataType: 'json',
                    success: function(response) {
                        console.log(response); // Log the response to see its structure
                        if (response.success) {
                            $('#studentsTable tbody').empty(); // Clear existing table rows

                            // Populate the table with students
                            $.each(response.students, function(index, student) {
                                const attendance = response.attendance_records[student.std_id] || {};
                                $('#studentsTable tbody').append(
                                    `<tr>
                                        <td>${student.std_id}</td>
                                        <td>${student.std_firstName} ${student.std_lastName}</td>
                                        <td><input type="checkbox" name="attendance[${student.std_id}][hr1]" value="1" ${attendance.att_hr_1 ? 'checked' : ''}></td>
                                        <td><input type="checkbox" name="attendance[${student.std_id}][hr2]" value="1" ${attendance.att_hr_2 ? 'checked' : ''}></td>
                                        <td><input type="checkbox" name="attendance[${student.std_id}][hr3]" value="1" ${attendance.att_hr_3 ? 'checked' : ''}></td>
                                        <td><input type="checkbox" name="attendance[${student.std_id}][hr4]" value="1" ${attendance.att_hr_4 ? 'checked' : ''}></td>
                                        <td><input type="checkbox" name="attendance[${student.std_id}][hr5]" value="1" ${attendance.att_hr_5 ? 'checked' : ''}></td>
                                    </tr>`
                                );
                            });

                            // Save the selected course ID and attendance date in session
                            const courseId = $('select[name="course_id"]').val();
                            const attDate = $('#hiddenAttDate').val(); // Use hidden date
                            $.ajax({
                                type: 'POST',
                                url: 'set_session.php', // URL to set session variables
                                data: {
                                    course_id: courseId,
                                    att_date: attDate
                                },
                                dataType: 'json',
                                success: function(setSessionResponse) {
                                    if (!setSessionResponse.success) {
                                        alert('Failed to set session variables: ' + setSessionResponse.message);
                                    }
                                }
                            });
                        } else {
                            alert('Error loading students: ' + response.message);
                        }
                    },
                    error: function(xhr, status, error) {
                        alert('An error occurred: ' + error);
                    }
                });
            });
        });
    </script>
</head>

<body>
    <h2>Add Attendance</h2>

    <!-- Course Selection Form -->
    <form id="courseForm" method="POST">
        <label for="course_id">Select Course:</label>
        <select name="course_id" required>
            <option value="">-- Select Course --</option>
            <?php foreach ($courses as $course): ?>
                <option value="<?= htmlspecialchars($course['course_id']) ?>">
                    <?= htmlspecialchars($course['course_name']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <!-- Hidden date input for the current date -->
        <input type="hidden" name="att_date" id="hiddenAttDate" value="<?= date('Y-m-d') ?>">

        <button type="submit">Load Students</button>
    </form>
    <div class="space"></div>
    <!-- Attendance Form -->
    <form method="POST" action="submit_attendance.php">
        <input type="hidden" name="course_id" id="hiddenCourseId">
        <input type="hidden" name="att_date" id="hiddenAttDate" value="<?= date('Y-m-d') ?>"> <!-- Use the current date -->

        <table border="1" id="studentsTable">
            <thead>
                <tr>
                    <th>Student ID</th>
                    <th>Student Name</th>
                    <th>Hour 1</th>
                    <th>Hour 2</th>
                    <th>Hour 3</th>
                    <th>Hour 4</th>
                    <th>Hour 5</th>
                </tr>
            </thead>
            <tbody>
                <!-- Student rows will be populated here -->
            </tbody>
        </table>
        <div class="space"></div>
        <button type="submit">Submit Attendance</button>
    </form>

    <script>
        // Update hidden inputs with selected values when students are loaded
        $(document).on('submit', 'form#courseForm', function() {
            const selectedCourseId = $('select[name="course_id"]').val();
            $('#hiddenCourseId').val(selectedCourseId);
        });
    </script>
</body>

</html>
<style>
    h2 {
        text-align: center;
        margin-bottom: 30px;
        color: #5752e3;
        /* Updated color theme */
    }

    form {
        background: white;
        padding: 40px;
        /* Increased padding for form */
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        /* Enhanced shadow for a deeper effect */
        margin: 0 auto;
        max-width: 800px;
        /* Increased max-width for a bigger container */
    }

    label {
        display: block;
        margin: 25px 0 8px;
        /* Increased margin for label */
        font-weight: bold;
        font-size: 1.1em;
    }

    select,
    input[type="date"],
    button {
        width: 100%;
        padding: 15px;
        /* Increased padding for better click area */
        margin-bottom: 25px;
        /* Increased margin for spacing */
        border: 1px solid #ccc;
        border-radius: 4px;
        font-size: 1em;
        transition: border-color 0.3s;
    }

    select:focus,
    input[type="date"]:focus {
        border-color: #5752e3;
        /* Updated border color to match theme */
        outline: none;
    }

    button {
        background-color: #5752e3;
        /* Updated button background color */
        color: white;
        font-size: 1.1em;
        cursor: pointer;
        border: none;
    }

    button:hover {
        background-color: #4744c0;
        /* Darker shade for hover effect */
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 25px;
        /* Slightly increased margin for spacing */
    }

    th,
    td {
        padding: 15px;
        /* Increased padding for table cells */
        text-align: center;
        border-bottom: 1px solid #ddd;
    }

    th {
        background-color: #5752e3;
        /* Header color matching theme */
        color: white;
    }

    tr:nth-child(even) {
        background-color: #f9f9f9;
    }

    tr:hover {
        background-color: #e9ecef;
        /* Highlight on row hover */
    }

    input[type="checkbox"] {
        width: 24px;
        /* Slightly larger checkbox */
        height: 24px;
        /* Slightly larger checkbox */
    }

    .space {
        height: 20px;
    }
</style>