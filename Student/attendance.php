<style>
    .container {
        max-width: 800px;
        margin: 0 auto;
        background: white;
        border-radius: 10px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        padding: 20px;
    }

    h2 {
        text-align: center;
        margin-bottom: 20px;
        color: #4a90e2;
    }

    .form-group {
        margin-bottom: 15px;
    }

    label {
        display: block;
        margin-bottom: 5px;
        font-weight: bold;
    }

    select,
    input[type="date"],
    input[type="month"],
    button {
        width: 100%;
        padding: 10px;
        border: 1px solid #ccc;
        border-radius: 5px;
        font-size: 16px;
        margin-top: 5px;
    }

    button {
        background-color: #4a90e2;
        color: white;
        border: none;
        cursor: pointer;
        transition: background-color 0.3s;
    }

    button:hover {
        background-color: #357ab8;
    }

    #attendanceTable {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
    }

    #attendanceTable th,
    #attendanceTable td {
        border: 1px solid #ddd;
        padding: 10px;
        text-align: center;
    }

    #attendanceTable th {
        background-color: #f1f1f1;
    }

    .status-present {
        background-color: #d4edda;
        /* Light green background */
        color: #155724;
        /* Dark green text */
        font-weight: bold;
        padding: 5px;
        border-radius: 5px;
    }

    .status-absent {
        background-color: #f8d7da;
        /* Light red background */
        color: #721c24;
        /* Dark red text */
        font-weight: bold;
        padding: 5px;
        border-radius: 5px;
    }

    @media (max-width: 600px) {
        button {
            font-size: 14px;
        }

        #attendanceTable th,
        #attendanceTable td {
            font-size: 14px;
        }
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
                    <th>Hour 1</th>
                    <th>Hour 2</th>
                    <th>Hour 3</th>
                    <th>Hour 4</th>
                    <th>Hour 5</th>
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

    viewTypeSelect.addEventListener('change', function() {
        if (this.value === 'day') {
            datePicker.style.display = 'block';
            monthPicker.style.display = 'none';
        } else {
            datePicker.style.display = 'none';
            monthPicker.style.display = 'block';
        }
    });

    document.getElementById('viewAttendance').addEventListener('click', function() {
        const viewType = viewTypeSelect.value;
        let dateInput;

        if (viewType === 'day') {
            dateInput = document.getElementById('attendanceDate').value;
        } else {
            dateInput = document.getElementById('attendanceMonth').value + '-01';
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
            .then(response => response.json())
            .then(data => {
                const tableBody = document.querySelector('#attendanceTable tbody');
                tableBody.innerHTML = '';

                if (data.length > 0) {
                    data.forEach((record, index) => {
                        const row = document.createElement('tr');
                        const statusClass = record.status === 'Present' ? 'status-present' : 'status-absent';

                        row.innerHTML = `
                                <td>${index + 1}</td>
                                <td>${record.date}</td>
                                <td class="${record.att_hr_1 === 1 ? 'status-present' : 'status-absent'}">${record.att_hr_1 === 1 ? 'Present' : 'Absent'}</td>
                                <td class="${record.att_hr_2 === 1 ? 'status-present' : 'status-absent'}">${record.att_hr_2 === 1 ? 'Present' : 'Absent'}</td>
                                <td class="${record.att_hr_3 === 1 ? 'status-present' : 'status-absent'}">${record.att_hr_3 === 1 ? 'Present' : 'Absent'}</td>
                                <td class="${record.att_hr_4 === 1 ? 'status-present' : 'status-absent'}">${record.att_hr_4 === 1 ? 'Present' : 'Absent'}</td>
                                <td class="${record.att_hr_5 === 1 ? 'status-present' : 'status-absent'}">${record.att_hr_5 === 1 ? 'Present' : 'Absent'}</td>
                                <td class="${statusClass}">${record.status}</td>
                            `;
                        tableBody.appendChild(row);
                    });
                    document.getElementById('attendanceTableContainer').style.display = 'block';
                } else {
                    tableBody.innerHTML = `<tr><td colspan="8">No records found.</td></tr>`;
                }
            })
            .catch(error => {
                alert('An error occurred: ' + error.message);
            });
    });
</script>