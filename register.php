<?php 
require "libs/clientActions.php";

if (isset($_POST["register"])) {
    $username = $_POST["username"];
    $email = $_POST["email"];
    $password = $_POST["password"];
    $confirmPassword = $_POST["confirmPassword"];

    if (empty($username) || empty($email) || empty($password) || empty($confirmPassword)) {
        echo "<div class='alert alert-danger mb-0 text-center'>You must fill the blank areas</div>"; 
    } elseif ($password !== $confirmPassword) {
        echo "<div class='alert alert-danger mb-0 text-center'>Passwords do not match</div>"; 
    } else {
        $user = getUser($email);

        // If user exists return error
        if ($user) {
            echo "<div class='alert alert-danger mb-0 text-center'>User already exists!</div>"; 
        } else {
            // If no user is found, create a new user
            registerUser($username, $email, $password);
            header('Location: login.php');
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
