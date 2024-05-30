<?php 
require "libs/vars.php";
require "libs/functions.php";
require "libs/dbsettings.php";

if(isLoggedin()){
    header("location: index.php");
    exit;
}

$username = $email = $password = $confirm_password = "";
$username_err = $email_err = $password_err = $confirm_password_err = "";

if(isset($_POST['register'])){
    
    if(empty(trim($_POST["username"]))){
        $username_err = "You must enter a username";
    }else if(strlen(trim($_POST["username"])) < 5 or strlen(trim($_POST["username"])) > 15){
        $username_err = "Username must be at least 5, maximum 15 characters";
    }else if(!preg_match('/^[A-Za-z][A-Za-z0-9]{5,31}$/', $_POST["username"])){
        $username_err = "Username must contain number, letter and lines";
    }else {
        $sql = "SELECT id FROM users WHERE username = ?";

        if ($stmt = mysqli_prepare($connection, $sql)) {
            $param_username = trim($_POST["username"]);
            mysqli_stmt_bind_param($stmt, "s", $param_username);

            if (mysqli_stmt_execute($stmt)) {
                mysqli_stmt_store_result($stmt);
                if (mysqli_stmt_num_rows($stmt) == 1) {
                    $username_err = "Email already exists";
                } else {
                    $username = $_POST["username"];
                }
            } else {
                echo mysqli_error($connection);
                echo "Error occurred";
            }
        }
    }

    if (empty(trim($_POST["email"]))) {
        $email_err = "You must provide an email address";
    } else {
        $sql = "SELECT id FROM users WHERE email = ?";
        if ($stmt = mysqli_prepare($connection, $sql)) {
            $param_email = trim($_POST["email"]);
            mysqli_stmt_bind_param($stmt, "s", $param_email);

            if (mysqli_stmt_execute($stmt)) {
                mysqli_stmt_store_result($stmt);
                if (mysqli_stmt_num_rows($stmt) == 1) {
                    $email_err = "Username already exists";
                } else {
                    $email = $_POST["email"];
                }
            } else {
                echo mysqli_error($connection);
                echo "Error occurred";
            }
        }
    }
    if (empty(trim($_POST["password"]))) {
        $password_err = "You must enter a password";
    } else if (strlen($_POST["password"]) < 6) {
        $password_err = "Password should be minimum 6 characters";
    } else {
        $password = $_POST["password"];
    }
    if (empty(trim($_POST["confirm_password"]))) {
        $confirm_password_err = "You must enter password again";
    } else {
        $confirm_password = $_POST["confirm_password"];
        if (empty($password_err) && ($password != $confirm_password)) {
            $confirm_password_err = "Passwords do not match.";
        }
    }

    if (empty($username_err) && empty($email_err) && empty($password_err)) {
        $sql = "INSERT INTO users (username, email, password) VALUES (?, ?, ?)";

        if ($stmt = mysqli_prepare($connection, $sql)) {
            $param_username = $username;
            $param_email = $email;
            $param_password = password_hash($password, PASSWORD_DEFAULT);

            mysqli_stmt_bind_param($stmt, "sss", $param_username, $param_email, $param_password);

            if (mysqli_stmt_execute($stmt)) {
                header('Location: login.php');
            } else {
                echo mysqli_error($connection);
                echo "Error occurred";
            }
        }
    }
}

?>

<?php include "views/_header.php"; ?>
<?php include "views/_navbar.php"; ?>

<div class="container mt-4 justify-content-center align-items-center">
    <form class="gap-4" action="register.php" method="POST">
        <div class="form-group mb-4">
            <label for="username" class="col-sm-2 col-form-label">Username</label>
            <div class="col-sm-10">
                <input type="text" name="username" class="form-control" id="username" placeholder="Username">
            </div>
        </div>
        <div class="form-group mb-4">
            <label for="email" class="col-sm-2 col-form-label">Email</label>
            <div class="col-sm-10">
                <input type="email" class="form-control" name="email" id="email" placeholder="Email">
            </div>
        </div>
        <div class="form-group mb-4">
            <label for="inputPassword" class="col-sm-2 col-form-label">Password</label>
            <div class="col-sm-10">
                <input type="password" name="password" class="form-control" id="password" placeholder="Password">
            </div>
        </div>
        <div class="form-group mb-4">
            <label for="inputConfirmPassword" class="col-sm-2 col-form-label">Confirm Password</label>
            <div class="col-sm-10">
                <input type="password" name="confirmPassword" class="form-control" id="confirmPassword" placeholder="Confirm Password">
            </div>
        </div>
        <div class="form-group">
            <button type="submit" name="register" value="Submit" class="btn btn-primary mb-2">Register</button>
        </div>
    </form>
</div>
