<!DOCTYPE html>
<html>
<head>
    <title>Login Page</title>
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .login-container {
            text-align: center;
        }
        .form-group{
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
//create connection
$conn = mysqli_connect($servername, $username, $password, $dbname);
if (!$conn) {
    //check connection
    die("Connection failed: " . mysqli_connect_error());
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login'])) {

    $username = $_POST['username'];
    $password = $_POST['password'];

    $sql = "SELECT * 
    FROM employee
    WHERE employee_username='$username'
    AND employee_password='$password'";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) == 1) {
        $row_result = mysqli_fetch_assoc($result);
        
        
        $_SESSION['username'] = $username;
        
        $type = $row_result['employee_type'];
        
        if ($type == "Assistant") {
            header("Location: assistantPage.php");
        } 
        else if ($type == "Secretaries") {
            header("Location: secretaryPage.php");
        }
        else if ($type == "Head of Department") {
            header("Location: headofdepartmentPage.php");
        }
        else if ($type == "Head of Secretary") {
            header("Location: headofsecretaryPage.php");
        }
        else if ($type == "Dean") {
            header("Location: deanPage.php");
        }
    } 
    else {
        $login_error = "Invalid Username or Password. Try again!";
    }
}
mysqli_close($conn);

?>

<body>
    <div class="login-container">
        <h1>Exam Planning System</h1>
        <h2>Login</h2>
        <form action="login.php" method="post">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit", name="login">Login</button>
        </form>
        <?php if (isset($login_error)) :?>
            <p style='color: red;'>Invalid username or password! Try again.</p>  
        <?php endif;?>
        <a href='forgotpassword.php' style="margin-top:3vh">Forgot Password?</a>
    </div>
</body>

</html>
