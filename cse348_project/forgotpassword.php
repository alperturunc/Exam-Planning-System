<!DOCTYPE html>
<html>
<head>
    <title>Forgot Password Page</title>
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .forgotpassword-container {
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

$conn = mysqli_connect($servername, $username, $password, $dbname);
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$flag = false;

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['changepassword'])) {

    $username = $_POST['username'];
    $password = $_POST['password'];

    $sql_update_password = "UPDATE employee 
        SET employee_password='$password'
        WHERE employee_username='$username'";
    $result_update_password = mysqli_query($conn, $sql_update_password);

    // if password change occurred or username is valid
    if ($result_update_password && mysqli_affected_rows($conn) > 0) {
        $flag = true;
    } else {
        $flag = false;
    }
    
}

?>

<body>
    <div class="forgotpassword-container">
        <h1>Update Password</h1>
        <form action="" method="post">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit", name="changepassword" style="margin-bottom: 2vh">Confirm</button>
        </form>
        <?php if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['changepassword'])) : ?>
            <?php if ($flag == true): ?>
                <p>Password updated successfully.</p>
            <?php else: ?>
                <p>Error updating password. Invalid username or password didn't changed!!!</p>
            <?php endif; ?>
        <?php endif; ?>
        <a href="login.php"><button>Return to Login Page </button></a>
    </div>
</body>

<?php mysqli_close($conn); ?>

</html>
