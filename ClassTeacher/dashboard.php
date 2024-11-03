<?php
session_start();
include '../Includes/dbcon.php';

// Check if the user is logged in as a teacher
if (!isset($_SESSION['userId'])) {
    die("<div class='alert alert-danger'>Unauthorized access! Please log in as a teacher.</div>");
}

$teacherId = $_SESSION['userId']; // Use the logged-in user's ID from session

// Queries
$numCoCourses = 0;
$inChargeCourses = [];
$studentCounts = [];

try {
    // Query 1: Number of co-taught courses
    $query1 = "SELECT COUNT(DISTINCT tblcourse.course_id) AS num_co_courses 
               FROM tblcoteachers 
               INNER JOIN tblcourse ON tblcoteachers.course_id = tblcourse.course_id 
               WHERE tblcoteachers.tea_id = ?";
    $stmt1 = $conn->prepare($query1);
    $stmt1->bind_param("i", $teacherId);
    $stmt1->execute();
    $stmt1->bind_result($numCoCourses);
    $stmt1->fetch();
    $stmt1->close();

    // Query 2: List of courses where teacher is class in-charge and active
    $query2 = "SELECT tblcourse.course_id, tblcourse.course_name, tblcourseincharge.isActive AS course_active 
               FROM tblcourseincharge 
               INNER JOIN tblcourse ON tblcourseincharge.course_id = tblcourse.course_id 
               WHERE tblcourseincharge.tea_id = ? 
               AND tblcourseincharge.isActive = 1";
    $stmt2 = $conn->prepare($query2);
    $stmt2->bind_param("i", $teacherId);
    $stmt2->execute();
    $result2 = $stmt2->get_result();
    while ($row = $result2->fetch_assoc()) {
        $inChargeCourses[] = $row;
    }
    $stmt2->close();

    // Query 3: Number of students in each course where the teacher is in-charge or co-teacher
    $query3 = "SELECT tblcourse.course_id, tblcourse.course_name,
                      CASE WHEN tblcourseincharge.isActive = 1 THEN 'In-charge' ELSE 'Co-teacher' END AS role,
                      COUNT(tblcoursetaken.std_id) AS num_students
               FROM tblcourse
               LEFT JOIN tblcourseincharge ON tblcourse.course_id = tblcourseincharge.course_id 
                   AND tblcourseincharge.tea_id = ?
               LEFT JOIN tblcoursetaken ON tblcourse.course_id = tblcoursetaken.course_id 
                   AND tblcoursetaken.isActive = 1
               WHERE (tblcourseincharge.tea_id = ? AND tblcourseincharge.isActive = 1)
               GROUP BY tblcourse.course_id, role";
    $stmt3 = $conn->prepare($query3);
    $stmt3->bind_param("ii", $teacherId, $teacherId);
    $stmt3->execute();
    $result3 = $stmt3->get_result();
    while ($row = $result3->fetch_assoc()) {
        $studentCounts[] = $row;
    }
    $stmt3->close();
} catch (Exception $e) {
    echo "<div class='alert alert-danger'>Database query failed: " . htmlspecialchars($e->getMessage()) . "</div>";
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher Dashboard</title>
    <style>
        .dashboard {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            /* Adds spacing between boxes */
        }

        .box {
            flex: 1 1 calc(33.333% - 20px);
            /* Responsive boxes */
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 20px;
            text-align: left;

            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: background-color 0.3s, transform 0.3s;
            color: #000;
            /* Text color for better contrast */
            display: flex;
            flex-direction: column;
        }

        .box h2 {
            font-size: 24px;
            margin-bottom: 10px;
        }

        .box p {
            font-size: 18px;
            margin: 0;
        }

        .box ul {
            list-style-type: none;
            padding: 0;
            margin: 0;
        }

        .box li {
            margin-bottom: 5px;
            color: #000;
        }

        .box:hover {
            background-color: #4846c8;
            /* Slightly different color on hover */
            transform: translateY(-2px);
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .box {
                flex: 1 1 100%;
                /* Full width on small screens */
            }
        }

        .box:nth-child(2) {
            background-color: #4846c8;
        }
    </style>
</head>

<body>

    <div class="dashboard">
        <!-- Number of Co-Teacher Courses -->
        <div class="box">
            <p>Number of Courses as Co-Teacher</p>

            <h2><?php echo htmlspecialchars($numCoCourses); ?></h2>
        </div>

        <!-- Courses as In-Charge -->
        <div class="box">
            <p>Courses as In-Charge</p>
            <ul>
                <?php if (empty($inChargeCourses)): ?>
                    <li>
                        <h2>No courses assigned </h2>
                    </li>
                <?php else: ?>
                    <?php foreach ($inChargeCourses as $course): ?>
                        <li>
                            <h2><?php echo htmlspecialchars($course['course_name']); ?></h2>
                        </li>
                    <?php endforeach; ?>
                <?php endif; ?>
            </ul>
        </div>

        <!-- Student Counts per Course -->
        <div class="box">
            <p>Number of Students</p>
            <ul>
                <?php if (empty($studentCounts)): ?>
                    <li>No students enrolled</li>
                <?php else: ?>
                    <?php foreach ($studentCounts as $count): ?>
                        <li>
                            <h2><?php echo htmlspecialchars($count['course_name']) . ": " . htmlspecialchars($count['num_students']) . " students"; ?></h2>
                        </li>
                    <?php endforeach; ?>
                <?php endif; ?>
            </ul>
        </div>
    </div>

</body>

</html>