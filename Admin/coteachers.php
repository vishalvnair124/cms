<?php
include '../Includes/dbcon.php';
session_start();
?>

<div class="container">
    <h1>Manage Co-Teachers</h1>




    <!-- Add Co-Teacher Form -->
    <form method="POST" action="process_coteachers.php">
        <div class="form-group">
            <label for="tea_id">Select Teacher</label>
            <select id="tea_id" name="tea_id" class="form-control" required>
                <option value="">Select a teacher</option>
                <?php
                $teachersQuery = "SELECT tea_id, tea_firstName, tea_lastName FROM tblteachers";
                $teachersResult = $conn->query($teachersQuery);
                while ($row = $teachersResult->fetch_assoc()) {
                    echo "<option value='" . $row['tea_id'] . "'>" . htmlspecialchars($row['tea_firstName']) . " " . htmlspecialchars($row['tea_lastName']) . "</option>";
                }
                ?>
            </select>
        </div>
        <div class="form-group">
            <label for="course_id">Select Course</label>
            <select id="course_id" name="course_id" class="form-control" required>
                <option value="">Select a course</option>
                <?php
                $coursesQuery = "SELECT course_id, course_name FROM tblcourse";
                $coursesResult = $conn->query($coursesQuery);
                while ($row = $coursesResult->fetch_assoc()) : ?>
                    <option value="<?= $row['course_id']; ?>"><?= htmlspecialchars($row['course_name']); ?></option>
                <?php endwhile; ?>
            </select>
        </div>
        <button type="submit" name="add_teacher" class="btn">Add Co-Teacher</button>
    </form>

    <!-- List of Co-Teachers -->
    <h2>Current Co-Teachers</h2>
    <table>
        <thead>
            <tr>
                <th>Teacher Name</th>
                <th>Course Name</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Fetch all co-teachers
            $coTeachersQuery = "SELECT tblcoteachers.cotea_id, tblteachers.tea_firstName, tblteachers.tea_lastName, tblcourse.course_name
                                FROM tblcoteachers
                                INNER JOIN tblteachers ON tblcoteachers.tea_id = tblteachers.tea_id
                                INNER JOIN tblcourse ON tblcoteachers.course_id = tblcourse.course_id";
            $coTeachersResult = $conn->query($coTeachersQuery);

            if ($coTeachersResult->num_rows > 0) {
                while ($row = $coTeachersResult->fetch_assoc()) {
                    echo "<tr>
                        <td>" . htmlspecialchars($row['tea_firstName']) . " " . htmlspecialchars($row['tea_lastName']) . "</td>
                        <td>" . htmlspecialchars($row['course_name']) . "</td>
                        <td>
                            <form method='POST' action='delete_coteacher.php'>
                                <input type='hidden' name='delete_id' value='" . $row['cotea_id'] . "'>
                                <button type='submit' class='btn'>Delete</button>
                            </form>
                        </td>
                    </tr>";
                }
            } else {
                echo "<tr><td colspan='3'>No co-teachers found.</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>


<style>
    .container {
        max-width: 800px;
        /* Center the container */
        margin: 0 auto;
        /* Center align */
        background: #fff;
        /* White background for content area */
        border-radius: 8px;
        /* Rounded corners */
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        /* Subtle shadow */
        padding: 20px;
    }

    h1,
    h2 {
        color: #5752e3;
        /* Use your primary color */
        text-align: center;
        /* Center headings */
    }

    h2 {
        margin-top: 20px;
        /* Space above */
    }

    .form-group {
        margin-bottom: 15px;
        /* Space between form elements */
    }

    label {
        display: block;
        /* Block layout for labels */
        margin-bottom: 5px;
        /* Space between label and input */
        font-weight: bold;
        /* Bold text for labels */
    }

    select,
    .btn {
        width: 100%;
        /* Full width for inputs and buttons */
        padding: 10px;
        /* Padding for comfort */
        border: 1px solid #ccc;
        /* Border color */
        border-radius: 4px;
        /* Rounded corners */
        box-sizing: border-box;
        /* Include padding in total width */
    }

    select:focus,
    .btn:focus {
        outline: none;
        /* Remove default focus outline */
        border-color: #252037;
        /* Change border color on focus */
    }

    .btn {
        background-color: #5752e3;
        /* Button color */
        color: white;
        /* White text */
        cursor: pointer;
        /* Pointer cursor */
        transition: background-color 0.3s;
        /* Smooth transition */
    }

    .btn:hover {
        background-color: #5752e5;
        /* Darker shade on hover */
    }

    table {
        width: 100%;
        /* Full width for the table */
        border-collapse: collapse;
        /* Merge borders */
        margin-top: 20px;
        /* Space above the table */
    }

    th,
    td {
        padding: 10px;
        /* Cell padding */
        text-align: left;
        /* Left-align text */
        border-bottom: 1px solid #ddd;
        /* Bottom border for rows */
    }

    th {
        background-color: #5752e3;
        /* Header background color */
        color: #252037;
        /* Header text color */
    }

    tr:hover {
        background-color: #f9f9f9;
        /* Highlight row on hover */
    }

    @media (max-width: 600px) {
        .container {
            padding: 10px;
            /* Less padding on small screens */
        }

        .btn {
            padding: 8px;
            /* Smaller buttons */
        }

        select {
            padding: 8px;
            /* Smaller input */
        }
    }
</style>