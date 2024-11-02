<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="../img/logo/attnlg.jpg" rel="icon">
    <title>View Class Results</title>
    <style>
        /* Add your styles here */
        .container {
            max-width: 900px;
            background-color: #fff;
            padding: 2em;
            margin: 50px auto;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }

        h1,
        h2 {
            text-align: center;
            color: #5752e3;
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
            outline: none;
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
            display: inline-block;
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
        }

        .result-list th,
        .result-list td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }

        .result-list th {
            background-color: #5752e3;
            color: #fff;
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
                        let resultHTML = '<h2>Results</h2><table><thead><tr><th>Student Name</th><th>Marks Obtained</th><th>Status</th></tr></thead><tbody>';
                        if (data.length > 0) {
                            data.forEach(result => {
                                resultHTML += `<tr>
                                               <td>${result.std_firstName} ${result.std_lastName}</td>
                                               <td>${result.marks_obtained}</td>
                                               <td>${result.status}</td>
                                           </tr>`;
                            });
                        } else {
                            resultHTML += '<tr><td colspan="3">No results found.</td></tr>';
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
                // Include required files to fetch exams
                include '../Includes/dbcon.php';
                include '../Includes/session.php';

                // Get the teacher ID from the session
                $teacherId = $_SESSION['userId'];

                // Fetch available exams for the teacher
                $examQuery = "SELECT * FROM tblexam WHERE course_id IN (SELECT course_id FROM tblcourseincharge WHERE tea_id = ?)";
                $stmt = $conn->prepare($examQuery);
                $stmt->bind_param("i", $teacherId);
                $stmt->execute();
                $exams = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

                foreach ($exams as $exam):
                ?>
                    <option value="<?php echo htmlspecialchars($exam['exam_id']); ?>">
                        <?php echo htmlspecialchars($exam['subject_name'] . ' ' . $exam['Qp_code']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- Display Results -->
        <div class="result-list" id="resultContainer"></div>
    </div>

</body>

</html>