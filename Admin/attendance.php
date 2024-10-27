<?php
session_start();
include('../includes/dbcon.php'); // Adjust path if needed

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if the student is logged in
if (!isset($_SESSION['userId'])) {
    die("You need to log in to access this page.");
}

// Get student’s admission number from the session
$student_id = $_SESSION['userId'];
$query = "SELECT admissionNumber FROM tblstudents WHERE Id = $student_id";
$result = $conn->query($query);
$admissionNo = $result->fetch_assoc()['admissionNumber'];
?>

<style>
    body {
        font-family: Arial, sans-serif;
        background-color: #f0f4fa;
        margin: 0;
        padding: 0;
    }

    .container {
        max-width: 800px;
        margin: 50px auto;
        background-color: #fff;
        padding: 30px;
        box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
        border-radius: 8px;
    }

    h2 {
        text-align: center;
        margin-bottom: 20px;
        color: #333;
    }

    .form-group {
        margin-bottom: 15px;
    }

    label {
        font-weight: bold;
        display: block;
        margin-bottom: 5px;
    }

    input,
    select {
        width: 100%;
        padding: 10px;
        margin-top: 5px;
        border: 1px solid #ddd;
        border-radius: 5px;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
    }

    table,
    th,
    td {
        border: 1px solid #ddd;
    }

    th,
    td {
        padding: 10px;
        text-align: center;
    }

    th {
        background-color: #5752e3;
        color: white;
    }

    .status-present {
        background-color: #4caf50;
        color: white;
    }

    .status-absent {
        background-color: #f44336;
        color: white;
    }

    .btn {
        background-color: #5752e3;
        color: white;
        padding: 10px;
        border: none;
        cursor: pointer;
        width: 100%;
    }

    .btn:hover {
        background-color: #534edc;
    }
</style>

<div class="container">
    <h2>View Attendance</h2>

    <div class="form-group">
        <label for="viewType">Select View Type:</label>
        <select id="viewType">
            <option value="day">By Day</option>
            <option value="month">By Month</option>
        </select>
    </div>

    <div class="form-group" id="datePicker">
        <label for="attendanceDate">Select Date:</label>
        <input type="date" id="attendanceDate">
    </div>

    <div class="form-group" id="monthPicker" style="display: none;">
        <label for="attendanceMonth">Select Month:</label>
        <input type="month" id="attendanceMonth">
    </div>

    <button id="viewAttendance" class="btn">View Attendance</button>

    <div id="attendanceTableContainer" style="display: none;">
        <table id="attendanceTable">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Date</th>
                    <th>Class</th>
                    <th>Class Arm</th>
                    <th>Session</th>
                    <th>Term</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>

<script>
    const viewTypeSelect = document.getElementById('viewType');
    const datePicker = document.getElementById('datePicker');
    const monthPicker = document.getElementById('monthPicker');

    // Toggle between day picker and month picker
    viewTypeSelect.addEventListener('change', function() {
        if (this.value === 'day') {
            datePicker.style.display = 'block';
            monthPicker.style.display = 'none';
        } else {
            datePicker.style.display = 'none';
            monthPicker.style.display = 'block';
        }
    });

    // Handle form submission
    document.getElementById('viewAttendance').addEventListener('click', function() {
        const viewType = viewTypeSelect.value;
        let dateInput;

        if (viewType === 'day') {
            dateInput = document.getElementById('attendanceDate').value;
        } else {
            // For month view, we get the month and set the day to 01
            dateInput = document.getElementById('attendanceMonth').value + '-01'; // Adding '-01' for the first day of the month
        }

        if (!dateInput) {
            alert('Please select a valid date or month.');
            return;
        }

        const formData = new FormData();
        formData.append('viewType', viewType);
        formData.append('dateInput', dateInput);

        fetch('fetch_attendance.php', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok: ' + response.statusText);
                }
                return response.json();
            })
            .then(data => {
                const tableBody = document.querySelector('#attendanceTable tbody');
                tableBody.innerHTML = ''; // Clear previous data

                if (data.length > 0) {
                    data.forEach((record, index) => {
                        const row = document.createElement('tr');
                        const statusClass = record.status === 'Present' ? 'status-present' : 'status-absent';

                        row.innerHTML = `
                            <td>${index + 1}</td>
                            <td>${record.date}</td>
                            <td>${record.className}</td>
                            <td>${record.classArmName}</td>
                            <td>${record.sessionName}</td>
                            <td>${record.termName}</td>
                            <td class="${statusClass}">${record.status}</td>
                        `;
                        tableBody.appendChild(row);
                    });
                    document.getElementById('attendanceTableContainer').style.display = 'block';
                } else {
                    tableBody.innerHTML = `<tr><td colspan="7">No records found.</td></tr>`;
                }
            })
            .catch(error => {
                alert('An error occurred: ' + error.message);
            });
    });
</script>