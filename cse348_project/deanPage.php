<!DOCTYPE html>
<html>
<head>
    <title>Dean Page</title>
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            height: 80vh;
            margin: 0;
            padding: 0;
        }
        .dean-page-container {
            width: 160vh;
            margin: 100px auto 0;
            background-color: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        table {
            width: 100%;
            border-collapse: collapse;
            border:2px solid #000000
        }
        th, td {
            padding: 10px;
            border: 2px solid #ccc;
            text-align: center;
        }
        th {
            background-color: #f2f2f2;
        }
       
    </style>
</head>

<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$servername = "localhost";
$username = "root";
$password = "mysql";
$dbname = "examplanning_system";

// Create connection
$conn = mysqli_connect($servername, $username, $password, $dbname);
if (!$conn) {
    // Check connection
    die("Connection failed: " . mysqli_connect_error());
}

// Current user from session
$current_user = $_SESSION['username'];

// Obtaining current department (faculty of the dean)
$sql_current_user_department = "SELECT department_id FROM employee WHERE employee_username ='$current_user'";
$result_current_user_department = mysqli_query($conn, $sql_current_user_department) or die("Error");
$current_user_department = mysqli_fetch_array($result_current_user_department)['department_id'];

// Obtaining current faculty (faculty of the dean)
$sql_faculty = "SELECT department.faculty_id
FROM department
WHERE department.department_id = '$current_user_department'";
$result_faculty = mysqli_query($conn, $sql_faculty) or die("Error");
$current_faculty = mysqli_fetch_array($result_faculty)['faculty_id'];

// Obtaining departments int the current faculty
$sql_departments_in_faculty = "SELECT department.department_id, department.department_name
FROM department
WHERE department.faculty_id = '$current_faculty'";
$result_departments_in_faculty = mysqli_query($conn, $sql_departments_in_faculty) or die("Error");
//$current_departments_in_faculty = mysqli_fetch_array($result_departments_in_faculty);

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['departmentSelected'])) {
    $department_selected = $_POST['departmentSelected'];
    $sql_selected_department = "SELECT department.department_name, department.department_id 
    FROM department
    WHERE department_name = '$department_selected'";
    $result_selected_department = mysqli_query($conn, $sql_selected_department) or die("Error");
    $selected_department = mysqli_fetch_array($result_selected_department)['department_id'];


}


?>

<body>
    <div class="dean-page-container">
        <h1> Hello! <?php echo "$current_user"; ?> </h1>
        <div class="select-department-container">
            <h2>Select Department</h2>
            <form method="post" action="">
                <select id="departmentSelected" name="departmentSelected" required>
                    <?php while ($department = mysqli_fetch_array($result_departments_in_faculty)) {
                        echo "<option value='{$department['department_name']}'>{$department['department_name']}</option>";
                    } ?>
                </select>
                <input type="submit" value="Submit">
            </form>
        </div>
        <div class="examlist-container">
            <h1>Exam List</h1>
            <table>
                    <thead>
                        <tr>
                            <th>Course Code&nbsp;&nbsp;  </th>
                            <th>Semester&nbsp;&nbsp;  </th>
                            <th>Date&nbsp;&nbsp;  </th>
                            <th>Day&nbsp;&nbsp;  </th>
                            <th>Hour&nbsp; &nbsp; </th>
                            <th>Assistants&nbsp; &nbsp; </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $sql_exams = "SELECT course.course_code, exam.exam_semester, exam.exam_date, exam.exam_day, exam.exam_start_hour,exam.exam_end_hour, exam.exam_asistant_num
                        FROM exam
                        JOIN course ON course.course_id = exam.course_id
                        JOIN department ON department.department_id = course.department_id
                        WHERE department.department_id = '$selected_department'
                        ORDER BY exam.exam_date ASC";
                        $result_exams = mysqli_query($conn, $sql_exams); 
                        ?>
                        <?php if (mysqli_num_rows($result_exams) > 0) : ?>
                            <?php while($row_exams = mysqli_fetch_array($result_exams) ) : ?>
                                <tr>  
                                    <td><?= $row_exams['course_code']?> &nbsp;&nbsp;</td>
                                    <td><?= $row_exams['exam_semester']?>&nbsp;&nbsp;</td>
                                    <td><?= $row_exams['exam_date']?>&nbsp;&nbsp;</td>
                                    <td><?= $row_exams['exam_day']?>&nbsp;&nbsp;</td>
                                    <td><?= $row_exams['exam_start_hour'] . '-' . $row_exams['exam_end_hour']?>&nbsp;&nbsp;</td>
                                    <td><?= $row_exams['exam_asistant_num'] ?> assistants&nbsp;&nbsp;</td>
                                </tr>
                            <?php endwhile ?>
                        <?php endif ?>
                    </tbody>
            </table>
        </div>
         <a href="login.php"><button style="margin-top: 2vh">Return to Login Page </button></a>
    </div>
    <?php mysqli_close($conn); ?>
</body>
</html>
