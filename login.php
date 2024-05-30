<?php
require_once "libs/functions.php";
require "libs/dbsettings.php";

session_start(); // Start the session

if(isLoggedin()){
    header("location: index.php");
    exit;
}

$email = $password = "";
$email_err = $password_err = $login_err = "";

// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate email
    if (empty(trim($_POST["email"]))) {
        $email_err = "You must enter an email";
    } else {
        $email = trim($_POST["email"]);
    }

    // Validate password
    if (empty(trim($_POST["password"]))) {
        $password_err = "You must enter a password";
    } else {
        $password = trim($_POST["password"]);
    }

    // Check input errors before querying the database
    if (empty($email_err) && empty($password_err)) {
        $sql = "SELECT id, username, password, user_type FROM users WHERE email=?";
        
        if ($stmt = mysqli_prepare($connection, $sql)) {
            mysqli_stmt_bind_param($stmt, "s", $email); // Use $email for binding

            if (mysqli_stmt_execute($stmt)) {
                mysqli_stmt_store_result($stmt);

                // Check if email exists, if yes then verify password
                if (mysqli_stmt_num_rows($stmt) == 1) {
                    mysqli_stmt_bind_result($stmt, $id, $username, $hashed_password, $user_type);
                    if (mysqli_stmt_fetch($stmt)) {
                        // Debug: Output fetched data
                        echo "Fetched data: id = $id, username = $username, hashed_password = $hashed_password, user_type = $user_type<br>";
                        
                        if (password_verify($password, $hashed_password)) {
                            // Password is correct, so start a new session
                            $_SESSION["loggedIn"] = true;
                            $_SESSION["id"] = $id;
                            $_SESSION["username"] = $username; // Store username in session
                            $_SESSION["user_type"] = $user_type;
                            // Redirect user to welcome page
                            header("location: index.php");
                            exit;
                        } else {
                            // Password is not valid
                            $login_err = "Invalid password.";
                        }
                    }
                } else {
                    // Email doesn't exist
                    $login_err = "No account found with that email.";
                }
            } else {
                echo "Oops! Something went wrong. Please try again later.";
            }

            // Close statement
            mysqli_stmt_close($stmt);
        } else {
            // Debug: Output SQL error
            echo "Failed to prepare SQL statement: " . mysqli_error($connection) . "<br>";
        }

        // Close connection
        mysqli_close($connection);
    }
}
?>



<?php include "views/_header.php"; ?>
<?php include "views/_navbar.php"; ?>

<div class="container mt-4 justify-content-center align-items-center">
    <?php if(!empty($login_err)){
        echo '<div class="alert alert-danger">'.$login_err.'</div>';
    }?>
    <form class="gap-4" action="login.php" method="POST">
        <div class="form-group mb-4">
            <label for="email" class="col-sm-2 col-form-label">Email</label>
            <div class="col-sm-10">
                <input type="email" class="form-control" id="email" placeholder="email@example.com" name="email">
            </div>
        </div>
        <div class="form-group mb-4">
            <label for="password" class="col-sm-2 col-form-label">Password</label>
            <div class="col-sm-10">
                <input type="password" class="form-control" id="password" name="password" placeholder="Password">
            </div>
        </div>
        <div class="form-group">
            <button type="submit" name="login" value="Submit" class="btn btn-primary mb-2">Login</button>
        </div>
    </form>
</div>
