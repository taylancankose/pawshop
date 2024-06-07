<?php
include_once 'classes/db.class.php';
include_once 'classes/product.class.php';
?>

<?php
 
session_start(); // Start the session
$product = new Products();

$is_loggedIn = $product->isLoggedIn();

if($is_loggedIn){
    header("Location: index.php");
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
        $user = $product->login($email, $password);
        print_r($user);

        echo $email;
        echo $password;
        if($user){
            print_r($user);
            $_SESSION["loggedIn"] = true;
            $_SESSION["id"] = $user->id;
            $_SESSION["username"] = $user->username;
            $_SESSION["user_type"] = $user->user_type;
            header("location: index.php");
            exit;
        } else {
            print_r($user);
            $login_err = "Invalid email or password.".$user;
        }
    }
}
?>



<?php include_once "views/_header.php"; ?>
<?php include_once "views/_navbar.php"; ?>

<div class="container mt-4 justify-content-center align-items-center">
    <?php if(!empty($login_err)){
        echo '<div class="alert alert-danger">'.$login_err.'</div>';
    }?>
    <form class="gap-4" action="login.php" method="POST">
        <div class="form-group mb-4">
            <label for="email" class="col-sm-2 col-form-label">Email</label>
            <div class="col-sm-10">
                <input type="email" class="form-control" id="email" placeholder="email@example.com" name="email">
                <?php if (!empty($email_err)) { ?>
                    <span class="text-danger"><?php echo $email_err; ?></span>
                <?php } ?>
            </div>
        </div>
        <div class="form-group mb-4">
            <label for="password" class="col-sm-2 col-form-label">Password</label>
            <div class="col-sm-10">
                <input type="password" class="form-control" id="password" name="password" placeholder="Password">
                <?php if (!empty($password_err)) { ?>
                    <span class="text-danger"><?php echo $password_err; ?></span>
                <?php } ?>
            </div>
        </div>
        <div class="form-group">
            <button type="submit" name="login" value="Submit" class="btn btn-primary mb-2">Login</button>
        </div>
    </form>
</div>
