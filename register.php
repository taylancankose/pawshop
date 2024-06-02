<?php
include_once 'classes/db.class.php';
include_once 'classes/product.class.php';
?>


<?php 
require_once "classes/vars.php";

$product = new Products();

$is_loggedIn = $product->isLoggedIn();

if($is_loggedIn){
    header("Location: index.php");
}

require_once "classes/vars.php";
require_once "classes/product.class.php";


$username = $email = $password = $confirm_password = "";
$username_err = $email_err = $password_err = $confirm_password_err = "";

if(isset($_POST['register'])) {
    $username = $_POST["username"];
    $email = $_POST["email"];
    $password = $_POST["password"];
    $confirm_password = $_POST["confirmPassword"];

    if(empty($username)) {
        $username_err = "You must enter a username";
    } elseif(strlen($username) < 5 or strlen($username) > 15) {
        $username_err = "Username must be at least 5, maximum 15 characters";
    } elseif(!preg_match('/^[A-Za-z][A-Za-z0-9]{5,31}$/', $username)) {
        $username_err = "Username must contain number, letter and lines";
    }

    if(empty($email)) {
        $email_err = "You must provide an email address";
    }

    if(empty($password)) {
        $password_err = "You must enter a password";
    } elseif(strlen($password) < 6) {
        $password_err = "Password should be minimum 6 characters";
    }

    if(empty($confirm_password)) {
        $confirm_password_err = "You must enter password again";
    } elseif($password != $confirm_password) {
        $confirm_password_err = "Passwords do not match.";
    }

    if(empty($username_err) && empty($email_err) && empty($password_err) && empty($confirm_password_err)) {
        if($product->registerUser($username, $email, $password)) {
            header('Location: login.php');
            exit;
        } else {
            echo "Error occurred while registering.";
        }
    }
}

?>

<?php include_once "views/_header.php"; ?>
<?php include_once "views/_navbar.php"; ?>

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
