<?php
include_once 'classes/db.class.php';
include_once 'classes/product.class.php';
include_once 'classes/utils.class.php';
include_once 'classes/auth.class.php';

?>


<?php
require_once "classes/vars.php";

$product = new Products();
$utils = new Utils();
$auth = new Auth();

$is_loggedIn = $utils->isLoggedIn();

if ($is_loggedIn) {
    header("Location: index.php");
}

$username = $email = $password = $confirm_password = "";
$username_err = $email_err = $password_err = $confirm_password_err = "";

if (isset($_POST['register'])) {
    $username = htmlspecialchars($_POST["username"]);
    $email = htmlspecialchars($_POST["email"]);
    $password = htmlspecialchars($_POST["password"]);
    $confirm_password = htmlspecialchars($_POST["confirmPassword"]);

    if (empty($username)) {
        $username_err = "You must enter a username";
    } elseif (strlen($username) < 5 or strlen($username) > 25) {
        $username_err = "Username must be at least 5, maximum 25 characters";
    }

    if (empty($email)) {
        $email_err = "You must provide an email address";
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $email_err = "Please enter a valid email address";
    }

    if (empty($password)) {
        $password_err = "You must enter a password";
    } elseif (strlen($password) < 6) {
        $password_err = "Password should be minimum 6 characters";
    }

    if (empty($confirm_password)) {
        $confirm_password_err = "You must enter password again";
    } elseif ($password != $confirm_password) {
        $confirm_password_err = "Passwords do not match.";
    }

    if (empty($username_err) && empty($email_err) && empty($password_err) && empty($confirm_password_err)) {
        $result_register = $auth->registerUser($username, $email, $password);
        if ($result_register === "Username already exists") {
            $username_err = "Username already exists";
        } else if ($result_register === "Email already exists") {
            $email_err = "Email already exists";
        } else {
            $otp = rand(100000, 999999);
            $_SESSION['otp'] = $otp;
            $_SESSION['username_tmp'] = $username;
            $utils->sendOTP($email, $username, $otp);
            $timestamp = time();
            header("Location: verify.php?success=$timestamp"); // verify.php'ye yönlendirme
            exit;
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
                <?php if (!empty($username_err)) { ?>
                    <span class="text-danger"><?php echo $username_err; ?></span>
                <?php } ?>
            </div>
        </div>
        <div class="form-group mb-4">
            <label for="email" class="col-sm-2 col-form-label">Email</label>
            <div class="col-sm-10">
                <input type="email" class="form-control" name="email" id="email" placeholder="Email">
                <?php if (!empty($email_err)) { ?>
                    <span class="text-danger"><?php echo $email_err; ?></span>
                <?php } ?>
            </div>
        </div>
        <div class="form-group mb-4">
            <label for="inputPassword" class="col-sm-2 col-form-label">Password</label>
            <div class="col-sm-10">
                <input type="password" name="password" class="form-control" id="password" placeholder="Password">
                <?php if (!empty($password_err)) { ?>
                    <span class="text-danger"><?php echo $password_err; ?></span>
                <?php } ?>
            </div>
        </div>
        <div class="form-group mb-4">
            <label for="inputConfirmPassword" class="col-sm-2 col-form-label">Confirm Password</label>
            <div class="col-sm-10">
                <input type="password" name="confirmPassword" class="form-control" id="confirmPassword" placeholder="Confirm Password">
                <?php if (!empty($confirm_password_err)) { ?>
                    <span class="text-danger"><?php echo $confirm_password_err; ?></span>
                <?php } ?>
            </div>
        </div>
        <div class="form-group">
            <button type="submit" name="register" value="Submit" class="btn btn-primary mb-2">Register</button>
        </div>
    </form>
</div>