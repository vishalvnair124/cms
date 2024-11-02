<?php
// Suppress error reporting for cleaner output (can remove for production)
// error_reporting(0);

// Include required files
include '../Includes/dbcon.php';
include '../Includes/session.php';

// Ensure session variables are available
// session_start();

// Check if teacher_id exists in session
if (!isset($_SESSION['userId'])) {
    die("<div class='alert alert-danger'>Unauthorized access! Please log in as a teacher.</div>");
}

// Get teacher's ID from session
$teacher_id = $_SESSION['userId'];

// Check if the logged-in teacher is a class teacher
$classTeacherCheckQuery = "SELECT tea_id FROM tblcourseincharge WHERE tea_id = $teacher_id AND isActive = 1 LIMIT 1";
$classTeacherCheckResult = $conn->query($classTeacherCheckQuery);

if ($classTeacherCheckResult->num_rows === 0) {
    die("<div class='alert alert-danger'><h1>You are not assigned as a class teacher!</h1></div>");
}

// Fetch the course information associated with the teacher from tblcourseincharge
$teacherCourseQuery = "SELECT course_id FROM tblcourseincharge WHERE tea_id = $teacher_id AND isActive = 1 LIMIT 1";
$teacherCourseResult = $conn->query($teacherCourseQuery);
$teacherCourseInfo = $teacherCourseResult->fetch_assoc();

// Check if the course information was fetched
if (!$teacherCourseInfo) {
    die("<div class='alert alert-danger'>Course information not found!</div>");
}

// Get the course name based on course_id
$course_id = $teacherCourseInfo['course_id'];
$courseNameQuery = "SELECT course_name FROM tblcourse WHERE course_id = $course_id";
$courseNameResult = $conn->query($courseNameQuery);
$courseNameInfo = $courseNameResult->fetch_assoc();

// Check if course name was fetched
if (!$courseNameInfo) {
    die("<div class='alert alert-danger'>Course name not found!</div>");
}

// Get the currently enrolled students based on the course info
$studentsQuery = "SELECT s.std_id, s.std_firstName, s.std_lastName, s.std_otherName, s.std_admissionNumber
                  FROM tblstudents s
                  JOIN tblcoursetaken ct ON s.std_id = ct.std_id
                  WHERE ct.course_id = " . $course_id . " AND ct.isActive = 1";
$studentsResult = $conn->query($studentsQuery);
if (!$studentsResult) {
    die("Error fetching students: " . $conn->error);
}

// Fetch available exams for the dropdown
$examsQuery = "SELECT exam_id, subject_name FROM tblexam";
$examsResult = $conn->query($examsQuery);
if (!$examsResult) {
    die("Error fetching exams: " . $conn->error);
}
?>

<style>
    /* Styles remain unchanged */
    .container {
        width: 80%;
        margin: 0 auto;
        padding: 20px;
        background-color: #fff;
        border-radius: 8px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }

    header {
        text-align: center;
        padding-bottom: 20px;
    }

    h1,
    h2 {
        color: #5752e3;
    }

    table {
        width: 100%;
        border-collapse: collapse;
    }

    th,
    td {
        padding: 10px;
        border: 1px solid #ddd;
    }

    th {
        background-color: #5752e3;
    }

    input[type="number"],
    select {
        width: 100%;
        padding: 8px;
        margin: 4px 0;
        border: 1px solid #ccc;
        border-radius: 4px;
    }

    button {
        background-color: #5752e3;
        color: white;
        padding: 10px 15px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        margin-top: 10px;
    }

    button:hover {
        background-color: #5752e5;
    }

    .alert {
        color: red;
        padding: 10px;
        background-color: #f8d7da;
        border: 1px solid #f5c6cb;
        border-radius: 5px;
    }

    .alert-success {
        background-color: #d4edda;
        border: 1px solid #c3e6cb;
        color: #155724;
    }

    .heading {
        color: #5752e3;
    }

    /* General alert styles */
    .alert {
        padding: 15px;
        margin: 15px 0;
        border-radius: 5px;
        display: flex;
        align-items: center;
        font-weight: bold;
    }

    .alert-success {
        background-color: #d4edda;
        border: 1px solid #c3e6cb;
        color: #155724;
    }

    .alert-danger {
        background-color: #f8d7da;
        border: 1px solid #f5c6cb;
        color: #721c24;
    }

    .alert i {
        margin-right: 10px;
        font-size: 20px;
    }

    /* Example for specific message styles */
    .alert-success i {
        color: #155724;
    }

    .alert-danger i {
        color: #721c24;
        font-size: larger;
    }
</style>

<div class="container">
    <main>
        <h2>Course: <?php echo $courseNameInfo['course_name']; ?></h2>
        <h3 class="heading">Student List & Marks Entry</h3>

        <?php if ($studentsResult->num_rows > 0 && $examsResult->num_rows > 0): ?>
            <form method="post" action="save_marks.php">
                <div class="form-group">
                    <label for="exam_id">Select Exam:</label>
                    <select name="exam_id" id="exam_id" required>
                        <option value="">-- Select Exam --</option>
                        <?php while ($exam = $examsResult->fetch_assoc()): ?>
                            <option value="<?php echo $exam['exam_id']; ?>">
                                <?php echo $exam['exam_id'] . ' - ' . $exam['subject_name']; ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Admission No</th>
                            <th>Marks</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sn = 0;
                        while ($student = $studentsResult->fetch_assoc()):
                            $sn++;
                        ?>
                            <tr>
                                <td><?php echo $sn; ?></td>
                                <td><?php echo $student['std_firstName'] . ' ' . $student['std_lastName']; ?></td>
                                <td><?php echo $student['std_admissionNumber']; ?></td>
                                <td>
                                    <input type="number" name="marks[<?php echo $student['std_id']; ?>]" min="0" max="100" required>
                                </td>
                                <td>
                                    <select name="status[<?php echo $student['std_id']; ?>]" required>
                                        <option value="1">Present</option>
                                        <option value="0">Absent</option>
                                    </select>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>

                <button type="submit" name="save_marks">Save Marks & Attendance</button>
            </form>
        <?php else: ?>
            <div class="alert">No students or exams found!</div>
        <?php endif; ?>
    </main>
</div>