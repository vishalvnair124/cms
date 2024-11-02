<?php
session_start();
include '../Includes/dbcon.php';

if (!isset($_SESSION['userId'])) {
    die("Unauthorized access! Please log in.");
}

$teacherId = $_SESSION['userId'];

// Check if the user is an active course incharge
$checkCourseInchargeQuery = "SELECT COUNT(*) AS count
                             FROM tblcourseincharge c
                             INNER JOIN tblcourse cr ON c.course_id = cr.course_id 
                             WHERE c.tea_id = ? AND c.isActive = 1";
$stmt = $conn->prepare($checkCourseInchargeQuery);

if (!$stmt) {
    die("Database error: " . $conn->error);
}

$stmt->bind_param("i", $teacherId);
$stmt->execute();
$stmt->bind_result($count);
$stmt->fetch();
$stmt->close();

if ($count == 0) {
    die("<h1>Access denied. You are not an active course incharge.</h1>");
}

// Query to fetch exams for which the user is the course incharge
$examQuery = "SELECT e.exam_id, e.subject_name, e.Qp_code 
              FROM tblexam e
              INNER JOIN tblcourseincharge c ON e.course_id = c.course_id 
              WHERE c.tea_id = ?";
$stmt = $conn->prepare($examQuery);

if (!$stmt) {
    die("Database error: " . $conn->error);
}

$stmt->bind_param("i", $teacherId);
$stmt->execute();
$exams = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="../img/logo/attnlg.jpg" rel="icon">
    <title>View Class Results</title>


    <style>
        .container {
            max-width: 900px;
            background-color: #fff;
            padding: 2em;
            margin: 50px auto;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
            border-radius: 8px;
        }

        h1 {
            text-align: center;
            color: #5752e3;
            margin-bottom: 1em;
        }

        .form-group {
            margin-bottom: 1.5em;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5em;
            font-weight: bold;
            color: #5752e3;
        }

        .form-control {
            width: 100%;
            padding: 0.8em;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1em;
            transition: border-color 0.3s;
        }

        .form-control:focus {
            border-color: #252037;
        }

        .btn {
            background-color: #5752e3;
            color: #fff;
            padding: 0.8em 1.5em;
            border: none;
            border-radius: 5px;
            font-size: 1em;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .btn:hover {
            background-color: #5752e3;
        }

        .result-list {
            margin-top: 2em;
        }

        .result-list table {
            width: 100%;
            border-collapse: collapse;
            font-size: 1em;
        }

        .result-list th,
        .result-list td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }

        .result-list th {
            background-color: #5752e3;
            color: #fff;
        }

        .result-list tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .result-list tr:hover {
            background-color: #f1f1f1;
        }

        .status-present {
            color: green;
            font-weight: bold;
        }

        .status-absent {
            color: red;
            font-weight: bold;
        }

        h2 {
            color: #5752e3;
        }
    </style>
    <script>
        function fetchResults() {
            const examId = document.getElementById('exam_id').value;
            const resultContainer = document.getElementById('resultContainer');

            if (examId) {
                fetch('fetch_results.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: 'exam_id=' + encodeURIComponent(examId)
                    })
                    .then(response => response.json())
                    .then(data => {
                        let resultHTML = '<h2>Results</h2><table><thead><tr><th>Admission Number</th><th>Student Name</th><th>Marks Obtained</th><th>Status</th></tr></thead><tbody>';
                        if (data.length > 0) {
                            data.forEach(result => {
                                const statusText = result.status == 1 ? "Present" : "Absent";
                                const statusClass = result.status == 1 ? "status-present" : "status-absent";

                                resultHTML += `<tr>
                                               <td>${result.std_admissionNumber}</td>
                                               <td>${result.std_firstName} ${result.std_lastName}</td>
                                               <td>${result.marks_obtained}</td>
                                               <td class="${statusClass}">${statusText}</td>
                                           </tr>`;
                            });
                        } else {
                            resultHTML += '<tr><td colspan="4">No results found.</td></tr>';
                        }
                        resultHTML += '</tbody></table>';
                        resultContainer.innerHTML = resultHTML;
                    })
                    .catch(error => {
                        console.error('Error fetching results:', error);
                    });
            } else {
                resultContainer.innerHTML = '';
            }
        }
    </script>
</head>

<body>
    <div class="container">
        <h1>View Class Results</h1>

        <!-- Form to Select Exam -->
        <div class="form-group">
            <label>Select Exam</label>
            <select id="exam_id" class="form-control" onchange="fetchResults()">
                <option value="">Select an Exam</option>
                <?php
                while ($exam = $exams->fetch_assoc()) {
                    echo '<option value="' . htmlspecialchars($exam['exam_id']) . '">'
                        . htmlspecialchars($exam['subject_name'] . ' ' . $exam['Qp_code']) . '</option>';
                }
                ?>
            </select>
        </div>

        <!-- Display Results -->
        <div class="result-list" id="resultContainer"></div>
    </div>
</body>

</html>