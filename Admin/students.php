<?php
include '../Includes/dbcon.php';
session_start();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Admin - Manage Students</title>

    <style>
        /* Main container */
        .students-section {
            background-color: #e7f3ff;
            width: 90%;
            margin: 20px auto;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.2);
        }

        /* Students list */
        .list-container {
            padding: 15px;
            background-color: #ffffff;
            border-radius: 8px;
            border: 1px solid #b3d7ff;
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        table th,
        table td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #b3d7ff;
        }

        table thead {
            background-color: #d0e7ff;
        }

        .btn-group {
            display: flex;
            gap: 5px;
        }

        .btn {
            padding: 7px 12px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            color: #fff;
            text-align: center;
            font-size: 14px;
        }

        .btn-approve {
            background-color: #4da6ff;
        }

        .btn-disqualify {
            background-color: #ffcc00;
        }

        .btn-assign {
            background-color: #66b2ff;
        }

        .btn-view {
            background-color: #007bff;
        }

        .btn-active {
            background-color: #28a745;
        }

        .btn-pending {
            background-color: #ffc107;
        }

        .btn-disqualified {
            background-color: #dc3545;
        }

        .status-badge {
            padding: 5px 10px;
            border-radius: 5px;
            color: white;
        }

        .email-column {
            max-width: 200px;
            overflow-wrap: break-word;
            word-wrap: break-word;
            white-space: normal;
        }

        a {
            text-decoration: none;
        }
    </style>
</head>

<body>
    <div class="students-section">
        <h2>Manage Students</h2>
        <div class="list-container">
            <table>
                <thead>
                    <tr>
                        <th>Admission Number</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Active Courses</th>
                        <th>Status</th>
                        <th>View</th>
                        <th>Approve</th>
                        <th>Disqualify</th>
                        <th>Assign</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Fetch all student records with their active courses
                    $query = "
        SELECT s.std_id, s.std_admissionNumber, s.std_firstName, s.std_lastName, s.std_email, s.std_status, 
            GROUP_CONCAT(c.course_name SEPARATOR ', ') AS active_courses
        FROM tblstudents s
        LEFT JOIN tblcoursetaken ct ON s.std_id = ct.std_id AND ct.isActive = 1  -- Only include active courses
        LEFT JOIN tblcourse c ON ct.course_id = c.course_id
        GROUP BY s.std_id";

                    $stmt = $conn->prepare($query);
                    $stmt->execute();
                    $result = $stmt->get_result();

                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            // Determine button status and classes based on student status
                            $statusText = $row['std_status'] == 1 ? 'Active' : ($row['std_status'] == 2 ? 'Pending' : 'Disqualified');
                            $statusClass = $row['std_status'] == 1 ? 'btn-active' : ($row['std_status'] == 2 ? 'btn-pending' : 'btn-disqualified');

                            echo "<tr>
                        <td>{$row['std_admissionNumber']}</td>
                        <td>{$row['std_firstName']} {$row['std_lastName']}</td>
                        <td class='email-column'>" . htmlspecialchars($row['std_email']) . "</td>
                        <td>" . ($row['active_courses'] ? htmlspecialchars($row['active_courses']) : 'No Active Courses') . "</td>
                        <td><span class='status-badge $statusClass'>$statusText</span></td>
                        <td>
                            <a href='view_student.php?id={$row['std_id']}' class='btn btn-view'>View</a>
                        </td>
                        <td>
                            <a href='approve_student.php?id={$row['std_id']}' class='btn btn-approve'>Approve</a>
                        </td>
                        <td>
                            <a href='disqualify_student.php?id={$row['std_id']}' class='btn btn-disqualify'>Disqualify</a>
                        </td>
                        <td>
                            <a href='assign_course.php?id={$row['std_id']}' class='btn btn-assign'>Assign</a>
                        </td>
                    </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='9' style='text-align: center;'>No records found.</td></tr>";
                    }
                    $stmt->close();
                    ?>
                </tbody>
            </table>

        </div>
    </div>

    <?php
    if (isset($_GET['message'])) {
        echo "<script>alert(" . json_encode(htmlspecialchars($_GET['message'])) . ");</script>";
    }
    ?>
</body>

</html>