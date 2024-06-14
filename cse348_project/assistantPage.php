<!DOCTYPE html>
<html>
<head>
    <title>Assistant Page</title>
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
        .assistant-page-container {
            
            width: 160vh;
            margin: 100px auto 0;
            
            background-color: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .users_name{
            margin-top: 1vh;
            margin-left: 10vh;
            text-align: left;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 10px;
            border: 2px solid #ccc;
            text-align: center;
        }
        th {
            background-color: #f2f2f2;
        }
        .hour {
            width: 60px;
        }
        
    </style>
</head>
<body>
<div class="assistant-page-container">
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

$conn = mysqli_connect($servername, $username, $password, $dbname);
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$current_user = $_SESSION['username'];
$sql_current_user_id = "SELECT employee_name, employee_id FROM employee WHERE employee_username ='$current_user'";
$result_user_id = mysqli_query($conn, $sql_current_user_id)  or die("Error");
$row_user = mysqli_fetch_array($result_user_id);


echo "<h1>Hello!  " . $current_user . "</h1><br>";


$sql_current_user_department = "SELECT department_id FROM employee WHERE employee_username ='$current_user'";
$result_current_user_department = mysqli_query($conn, $sql_current_user_department) or die("Error");
$current_user_department = mysqli_fetch_array($result_current_user_department)['department_id'];

$sql_faculty = "SELECT department.faculty_id
FROM department
WHERE department.department_id = '$current_user_department'";
$result_faculty = mysqli_query($conn, $sql_faculty) or die("Error");
$current_faculty = mysqli_fetch_array($result_faculty)['faculty_id'];

$sql_courses_in_department_faculty = "SELECT course.course_code
FROM course
JOIN department ON department.department_id = course.department_id
JOIN employee ON employee.department_id = department.department_id
JOIN faculty ON department.faculty_id = faculty.faculty_id
WHERE faculty.faculty_id = '$current_faculty' && faculty.faculty_name = department.department_name
UNION
SELECT course.course_code
FROM course
JOIN department ON department.department_id = course.department_id
JOIN faculty ON department.faculty_id = faculty.faculty_id
JOIN employee ON employee.department_id = department.department_id
WHERE employee.employee_username = '$current_user'";
$result_of_courses = mysqli_query($conn, $sql_courses_in_department_faculty) or die("Error");

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['courseSelected'])) {
    $course_selected = $_POST['courseSelected'];
    $sql_course_selected = "SELECT course.course_id FROM course WHERE course.course_code = '$course_selected'";
    $result_course_id = mysqli_query($conn, $sql_course_selected);
    $row_course_id = mysqli_fetch_array($result_course_id);
    $selected_course_id = $row_course_id['course_id'];
    $user_id = $row_user['employee_id'];

    $sql_reg_check = "SELECT *
    FROM registration
    WHERE course_id = '$selected_course_id'
    AND employee_id = '$user_id'";
    $result_reg_check = mysqli_query($conn, $sql_reg_check);
    
    if ($result_reg_check && mysqli_num_rows($result_reg_check) > 0) {
        echo "Already Registered.";
    } else {
        $sql_register_course = "INSERT INTO registration (course_id, employee_id) VALUES ('$selected_course_id', '$user_id')";
        if (mysqli_query($conn, $sql_register_course)) {
            echo "Registered.";
        } else {
            echo "Error";
        }
    }   
}


echo "<h1>Hello!  " . $current_user . "</h1><br>";


if (mysqli_num_rows($result_of_courses) > 0) {
    echo "<h2>Register to a course</h2>";
    echo '<form method="post" action="">';
    echo '<select name="courseSelected">';
    while ($row = mysqli_fetch_array($result_of_courses)) {
        echo "<option value='" . $row["course_code"] . "'>" . $row["course_code"] . "</option>";
    }
    echo '</select>';
    echo '<input type="submit" value="Submit">';
    echo '</form>';
}

echo "<h2>Select Week</h2>";
echo '<form method="post" action="">';
echo '<label for="week">Week (DD-MM-YYYY): </label>';
echo '<input type="date" id="week" name="week" value="' . (isset($_POST['week']) ? $_POST['week'] : date('Y-m-d')) . '">';
echo '<input type="submit" value="Change Week">';
echo '</form>';

if (isset($_POST['week'])) {
    $selected_week = $_POST['week'];
} else {
    $selected_week = date('Y-m-d');
}

echo "<h1>Weekly Schedule</h1>";
echo "<table>";
echo "<tr><th>Time</th><th>Monday</th><th>Tuesday</th><th>Wednesday</th><th>Thursday</th><th>Friday</th><th>Saturday</th><th>Sunday</th></tr>";

$hours = array("09:00", "10:00", "11:00", "12:00", "13:00", "14:00", "15:00", "16:00", "17:00", "18:00");

foreach ($hours as $hour) {
    $next_hour = date("H:i", strtotime($hour . " +1 hour"));
    echo "<tr>";
    echo "<td>$hour - $next_hour</td>";

    $days = array("Monday", "Tuesday", "Wednesday", "Thursday", "Friday","Saturday","Sunday");

    foreach ($days as $day) {
        $emp_id = $row_user['employee_id'];

        $sql_lectures = "SELECT course.course_code FROM course
                       JOIN lectures ON course.course_id = lectures.course_id
                       JOIN registration ON course.course_id = registration.course_id
                       JOIN employee ON employee.employee_id = registration.employee_id
                       WHERE lectures.lecture_day = '$day' AND lectures.lecture_start_hour <= '$hour'
                       AND lectures.lecture_end_hour > '$hour' AND employee.employee_id = '$emp_id'
                       ";
        $result_lectures = mysqli_query($conn, $sql_lectures);

        $sql_exams = "SELECT course.course_code FROM course 
        JOIN registration ON course.course_id = registration.course_id 
        JOIN employee ON employee.employee_id = registration.employee_id 
        JOIN exam ON exam.course_id = course.course_id 
        WHERE exam.exam_day = '$day' AND exam.exam_start_hour <= '$hour' 
        AND exam.exam_end_hour > '$hour' AND employee.employee_id = '$emp_id'
        AND WEEK(exam.exam_date) = WEEK('$selected_week')";
        $result_exams = mysqli_query($conn, $sql_exams);

        $sql_observer = "SELECT course.course_code FROM course
        JOIN exam ON exam.course_id = course.course_id
        JOIN observer ON observer.exam_id = exam.exam_id
        JOIN employee ON employee.employee_id = observer.employee_id
        WHERE exam.exam_day = '$day' AND exam.exam_start_hour <= '$hour' 
        AND exam.exam_end_hour > '$hour' AND employee.employee_id = '$emp_id'
        AND WEEK(exam.exam_date) = WEEK('$selected_week')";
        $result_observer = mysqli_query($conn, $sql_observer);

            if(mysqli_num_rows($result_observer) > 0){
                $row_observer = mysqli_fetch_array($result_observer);
                echo "<td>Observer-{$row_observer['course_code']} </td>";
            }
            else if(mysqli_num_rows($result_lectures) > 0 && mysqli_num_rows($result_exams) > 0){
                if($selected_week < '2024-07-01' && $selected_week > '2024-03-04'){
                    $row_lectures = mysqli_fetch_array($result_lectures);
                    $row_exams = mysqli_fetch_array($result_exams);
                    echo "<td>{$row_lectures['course_code']} / Exam-{$row_exams['course_code']} </td>";

                }
                else{
                    $row_exams = mysqli_fetch_array($result_exams);
                    echo "<td>Exam-{$row_exams['course_code']} </td>";
                }  
            }
            elseif (mysqli_num_rows($result_lectures) > 0) {
                if($selected_week < '2024-07-01' && $selected_week > '2024-03-04'){
                    $row_lectures = mysqli_fetch_array($result_lectures);
                    echo "<td>{$row_lectures['course_code']}</td>";
                }
                else{
                    echo "<td></td>";
                }
                
            } 
            else if(mysqli_num_rows($result_exams) > 0){
                $row_exams = mysqli_fetch_array($result_exams);
                echo "<td>Exam-{$row_exams['course_code']}</td>";
            }
            else {
                echo "<td></td>";
            }


        
    }
    echo "</tr>";
}

echo "</table>";

mysqli_close($conn);
?>

<a href="login.php"><button style="margin-top: 2vh">Logout</button></a>

</div>
</body>
</html>
