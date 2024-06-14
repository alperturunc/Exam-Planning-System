<!DOCTYPE html>
<html>
<head>
    <title>Head of Department Page</title>
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
        .headofdepartment-page-container {
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


$current_user = $_SESSION['username'];

$sql_current_user_department = "SELECT department_id FROM employee WHERE employee_username ='$current_user'";
$result_current_user_department = mysqli_query($conn, $sql_current_user_department) or die("Error");
$current_user_department = mysqli_fetch_array($result_current_user_department)['department_id'];


?>

<body>
    <div class="headofdepartment-page-container">
        <h1> Hello! <?php echo "$current_user"; ?> </h1>
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
                        WHERE department.department_id = '$current_user_department'
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
        <div class="assistantpercentage-list-container">
            <h1>Workloads of Assistants</h1>
            <table>
                    <thead>
                        <tr>
                            <th>Assistant Name&nbsp;&nbsp;</th>
                            <th>Percentage&nbsp;&nbsp;</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $sql_assistants = "SELECT employee.employee_name, employee.employee_score
                        FROM employee
                        WHERE employee.employee_type = 'Assistant' AND employee.department_id = '$current_user_department'
                        ORDER BY employee.employee_score ASC;";
                        $result_assistants = mysqli_query($conn, $sql_assistants); 
                        $score_total = 0;
                        while($row_assistants = mysqli_fetch_array($result_assistants)){
                            $score_total = $score_total + $row_assistants['employee_score'];
                        }
                        
                        mysqli_data_seek($result_assistants, 0);
                        ?>
                        <?php if (mysqli_num_rows($result_assistants) > 0 && $score_total != 0) : ?>
                            <?php while($row_assistants = mysqli_fetch_array($result_assistants) ) : ?>
                                <tr>  
                                    <td><?= $row_assistants['employee_name']?> &nbsp;&nbsp;</td>
                                    <?php $score_percentage = ($row_assistants['employee_score'] / $score_total)*100 ?>
                                    <td><?= $score_percentage?>%&nbsp;&nbsp;</td>
                                </tr>
                            <?php endwhile ?>
                        <?php else: ?>
                            <?php while($row_assistants = mysqli_fetch_array($result_assistants) ) : ?>
                                <tr>  
                                    <td><?= $row_assistants['employee_name']?> &nbsp;&nbsp;</td>
                                    <td>0%&nbsp;&nbsp;</td>
                                </tr>
                            <?php endwhile ?>

                            
                        <?php endif ?>
                    </tbody>
            </table>
            <a href="login.php"><button style="margin-top: 2vh">Return to Login Page </button></a>
        </div>


    </div>
    <?php mysqli_close($conn); ?>
</body>
</html>
