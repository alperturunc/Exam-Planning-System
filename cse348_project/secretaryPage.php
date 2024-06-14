<!DOCTYPE html>
<html>

<head>
    <title>Secretary Page</title>
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 110vh;
            margin: 0;
        }

        .secretary-page-container {
            text-align: center;
        }
        .a

        .form-group {
            margin-bottom: 3vh;
        }
    </style>
</head>

<?php

session_start();

$servername = "localhost";
$username = "root";
$password = "mysql";
$dbname = "examplanning_system";


$conn = mysqli_connect($servername, $username, $password, $dbname);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}


$current_user = $_SESSION['username'];

// Obtaining current users department
$sql_current_user_department = "SELECT department_id FROM employee WHERE employee_username ='$current_user'";
$result_current_user_department = mysqli_query($conn, $sql_current_user_department) or die("Error");
$current_user_department = mysqli_fetch_array($result_current_user_department)['department_id'];



// Obtaining the courses in the secretarys department
$sql_courses_in_department = "SELECT course.course_code 
    FROM course 
    JOIN department ON course.department_id = department.department_id 
    JOIN employee ON employee.department_id = department.department_id
    WHERE employee.employee_username = '$current_user'";
$result_of_courses = mysqli_query($conn, $sql_courses_in_department);

$courses = [];
while ($row = mysqli_fetch_array($result_of_courses)) {
    $courses[] = $row["course_code"];
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if ($_POST['form_type'] == 'create_course' && isset($_POST['coursecode']) && isset($_POST['semester'])) {
        
        $course_code = $_POST['coursecode'];
        $semester = $_POST['semester'];

        // Checking if an course is already existing
        $sql_check_course = "SELECT course_code FROM course
        WHERE course_code = '$course_code'";
        $result_check_course = mysqli_query($conn, $sql_check_course) or die("Error");

        if (mysqli_num_rows($result_check_course) > 0) {
            echo "Already exists!";
        } else {
            $sql_create_course = "INSERT INTO course (course_code, course_semester, department_id) 
                              VALUES ('$course_code', '$semester', 
                              (SELECT department.department_id FROM department 
                               JOIN employee ON employee.department_id = department.department_id 
                               WHERE employee.employee_username = '$current_user'))";
            if (mysqli_query($conn, $sql_create_course)) {
                echo "Course created!";
            } else {
                echo "Error: " . mysqli_error($conn);
            }
        }
    } elseif ($_POST['form_type'] == 'create_lecture' && isset($_POST['lectureToInsert']) && isset($_POST['lectureday']) && isset($_POST['lecture_starthour']) && isset($_POST['lecture_endhour'])) {
        

        // Adding the lecture to a course
        $course_code = $_POST['lectureToInsert'];
        $lecture_day = $_POST['lectureday'];
        $lecture_start_hour = $_POST['lecture_starthour'];
        $lecture_end_hour = $_POST['lecture_endhour'];

        $sql_course_id = "SELECT course_id FROM course WHERE course_code = '$course_code'";
        $result_course_id = mysqli_query($conn, $sql_course_id);
        $row_course_id = mysqli_fetch_array($result_course_id);
        $course_id = $row_course_id['course_id'];

        $sql_add_lecture = "INSERT INTO lectures (course_id, lecture_day, lecture_start_hour, lecture_end_hour) 
                            VALUES ('$course_id', '$lecture_day', '$lecture_start_hour', '$lecture_end_hour')";

        if (mysqli_query($conn, $sql_add_lecture)) {
            echo "Lecture added!";
        } else {
            echo "Error: " . mysqli_error($conn);
        }
    } elseif ($_POST['form_type'] == 'create_exam' && isset($_POST['courseSelected'])&& isset($_POST['semester_exam'])&& isset($_POST['date']) && isset($_POST['day']) && isset($_POST['start_hour']) && isset($_POST['end_hour']) && isset($_POST['assistant_num'])) {
        
        //Obtaining the values from the submitted form
        $course_selected = $_POST['courseSelected'];
        $exam_semester = $_POST['semester_exam'];
        $exam_date = $_POST['date'];
        $exam_day = $_POST['day'];
        $exam_start_hour = $_POST['start_hour'];
        $exam_end_hour = $_POST['end_hour'];
        $exam_assistant_num = $_POST['assistant_num'];

        $sql_course_selected = "SELECT course.course_id FROM course WHERE course.course_code = '$course_selected'";
        $result_course_id = mysqli_query($conn, $sql_course_selected);
        $row_course_id = mysqli_fetch_array($result_course_id);
        $selected_course_id = $row_course_id['course_id'];



        // Creating exam if available assistants exist
        $sql_assistants_in_department = "SELECT employee.employee_id,employee.employee_name,employee.employee_score
        FROM employee
        WHERE employee.employee_type = 'Assistant' AND employee.department_id = '$current_user_department'
        ORDER BY employee.employee_score ASC";
        $result_assistants_in_department = mysqli_query($conn, $sql_assistants_in_department);

        $counter = 0;
        $observers = [];

        // Looping the assistants to find available one
        while ($assistant = mysqli_fetch_array($result_assistants_in_department)) {
            if ($counter == $exam_assistant_num) {
                break;
            } else {

                $assistant_id = $assistant['employee_id'];
                

                $sql_is_registered = "SELECT course.course_id 
                FROM course
                JOIN registration
                ON registration.course_id = course.course_id
                WHERE registration.employee_id = '$assistant_id' AND course.course_id = '$selected_course_id'";
                $result_registered_courses = mysqli_query($conn, $sql_is_registered);



                $sql_lectures = "SELECT course.course_code FROM course
                       JOIN lectures ON course.course_id = lectures.course_id
                       JOIN registration ON course.course_id = registration.course_id
                       JOIN employee ON employee.employee_id = registration.employee_id
                       WHERE lectures.lecture_day = '$exam_day' AND lectures.lecture_start_hour BETWEEN '$exam_start_hour' AND '$exam_end_hour'
                       AND employee.employee_id = '$assistant_id'";
                $result_lectures = mysqli_query($conn, $sql_lectures);


                $sql_exams = "SELECT course.course_code FROM course 
                JOIN registration ON course.course_id = registration.course_id 
                JOIN employee ON employee.employee_id = registration.employee_id 
                JOIN exam ON exam.course_id = course.course_id 
                WHERE exam.exam_day = '$day' AND exam.exam_start_hour BETWEEN '$exam_start_hour' AND '$exam_end_hour'
                AND employee.employee_id = '$assistant_id'";
                $result_exams = mysqli_query($conn, $sql_exams);

                



                if (mysqli_num_rows($result_lectures) > 0 || mysqli_num_rows($result_exams) > 0 || mysqli_num_rows($result_registered_courses) > 0 ) {
                    continue;
                } else {
                    $counter++;
                    $observers[] = $assistant["employee_id"];
                }
            }
        }
        // if sufficient number of available assistants found
        if ($counter == $exam_assistant_num) {
            $sql_insert_exam = "INSERT INTO exam (exam_semester,exam_date, exam_day, exam_start_hour, exam_end_hour, exam_asistant_num, course_id) 
                        VALUES ('$exam_semester','$exam_date','$exam_day', '$exam_start_hour', '$exam_end_hour', '$exam_assistant_num', '$selected_course_id')";
            if (mysqli_query($conn, $sql_insert_exam)) {
                
            } else {
                echo "Error: " . mysqli_error($conn);
            }
            $sql_get_exam_id = "SELECT MAX(exam_id) AS largest_exam_id FROM exam";
            $result_get_exam_id = mysqli_query($conn, $sql_get_exam_id);
            $get_exam_id = mysqli_fetch_array($result_get_exam_id);
            $exam_id = $get_exam_id['largest_exam_id'];

            foreach($observers as $observer){
                $observer_id = $observer;
                $sql_insert_observer = "INSERT INTO `observer` (`employee_id`, `exam_id`) VALUES('$observer_id','$exam_id')";
                if (mysqli_query($conn, $sql_insert_observer)) {
                } else {
                    echo "Error: " . mysqli_error($conn);
                }
                $sql_update_score = "UPDATE employee
                SET employee_score = employee_score + 1
                WHERE employee.employee_id = '$observer'";
                $result_update_score = mysqli_query($conn, $sql_update_score);

            
            }
            
            header('Location: ' . $_SERVER['PHP_SELF']);
            exit();
        }
        else{
           
        }
    }
}
?>

<body>
    <div class="secretary-page-container">
        <h1> Hello! <?php echo "$current_user"; ?> </h1>
        <a href="login.php"><button style="margin-top: 2vh">Return to Login Page </button></a>
        <div class="courseform-container">
            <h2>Create Course</h2>
            <form action="" method="post">
                <input type="hidden" name="form_type" value="create_course">
                <div class="form-group">
                    <label for="coursecode">Course Code</label>
                    <input type="text" id="coursecode" name="coursecode" required>
                </div>
                <div class="form-group">
                    <label for="semester">Semester</label>
                    <input type="text" id="semester" name="semester" required>
                </div>
                <button type="submit" name="submit">Confirm</button>
            </form>
        </div>
        <div class="lectureform-container">
            <h2>Add Lecture</h2>
            <form action="" method="post">
                <input type="hidden" name="form_type" value="create_lecture">
                <label for="lecture" style="margin-top: 5vh; margin-left: 5vh">Course</label>
                <select id="lecture" name="lectureToInsert" required>
                    <?php foreach ($courses as $course_code) {
                        echo "<option value='$course_code'>$course_code</option>";
                    } ?>
                </select>
                <div class="form-group">
                    <label for="lectureday">Lecture Day</label>
                    <input type="text" id="lectureday" name="lectureday" required>
                </div>
                <div class="form-group">
                    <label for="lecture_starthour">Lecture Start Hour</label>
                    <input type="text" id="lecture_starthour" name="lecture_starthour" required>
                </div>
                <div class="form-group">
                    <label for="lecture_endhour">Lecture End Hour</label>
                    <input type="text" id="lecture_endhour" name="lecture_endhour" required>
                </div>
                <button type="submit" name="submit">Confirm</button>
            </form>
        </div>

        <div class="examform-container">
            <h2>Create Exam</h2>
            <form action="" method="post">
                <input type="hidden" name="form_type" value="create_exam">
                <label for="course" style="margin-top: 5vh; margin-left: 5vh">Course</label>
                <select id="course" name="courseSelected" required>
                    <?php foreach ($courses as $course_code) {
                        echo "<option value='$course_code'>$course_code</option>";
                    } ?>
                </select>
                <div class="form-group">
                    <label for="semester_exam">Semester</label>
                    <input type="text" id="semester_exam" name="semester_exam" required>
                </div>
                <div class="form-group">
                    <label for="date">Date</label>
                    <input type="text" id="date" name="date" required>
                </div>
                <div class="form-group">
                    <label for="day">Day</label>
                    <input type="text" id="day" name="day" required>
                </div>
                <div class="form-group">
                    <label for="start_hour">Start Hour</label>
                    <input type="text" id="start_hour" name="start_hour" required>
                </div>
                <div class="form-group">
                    <label for="end_hour">End Hour</label>
                    <input type="text" id="end_hour" name="end_hour" required>
                </div>
                <div class="form-group">
                    <label for="assistant_num">Number of assistants needed</label>
                    <input type="text" id="assistant_num" name="assistant_num" required>
                </div>
                <button type="submit" name="submit">Confirm</button>
            </form>
            <?php if ($counter == $exam_assistant_num) :?>
                    <p style="color: green;">Exam Created.</p>
            <?php else: ?>
                <p style="color: red;">Sufficient number of assistants couldn't be found.</p>
                <p style="color: red;">Try to enter another date.</p>
            <?php endif; ?>
            <div class="assistant_list">
                <h2>Assistant Score List</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Assistant Name</th>
                            <th>Score</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $sql_scores = "SELECT employee_name, employee_score FROM employee WHERE employee_type = 'Assistant' AND employee.department_id = '$current_user_department' ORDER BY employee.employee_score ASC";
                        $result_scores = mysqli_query($conn, $sql_scores);;
                        if (mysqli_num_rows($result_scores) > 0) {
                            while($row = mysqli_fetch_array($result_scores) ) {
                                echo "<tr><td>" . $row["employee_name"]. "</td><td>" . $row["employee_score"]. "</td></tr>";
                            }
                        } else {
                            echo "<tr><td colspan='2'>No assistants found</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>

</html>
