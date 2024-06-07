<?php
include_once 'classes/db.class.php';
include_once 'classes/product.class.php';
include_once 'classes/auth.class.php';
include_once 'classes/utils.class.php';

?>


<?php
require_once "classes/vars.php";

$product = new Products();
$auth = new Auth();
$utils = new Utils();

$verify_err = "";

$user = $auth->getUserByUsername($_SESSION["username_tmp"]);

if($user->verified == 1 || !isset($_GET["success"])){
    header("Location: index.php");
}

$otp = $_SESSION["otp"];
if(isset($_POST["otp"])){
    $otp_input = $_POST["otp"];
    if(trim($otp_input) == $otp){
        if(empty($verify_err)){
            if($utils->verifyOTP($otp, $user->id)){
                echo "Verified";
                session_destroy();
                header('Location: login.php');
            }else{
                echo "Failed";
            }
        }
    }else{
        $verify_err = "OTPs not match";
    }
}
?>


<?php include_once "views/_header.php"; ?>
<?php include_once "views/_navbar.php"; ?>

<style>
    .card {
        width: 350px;
        padding: 10px;
        border-radius: 20px;
        background: #fff;
        border: none;
        height: 350px;
        position: relative;
    }

    .mobile-text {
        color: #989696b8;
        font-size: 15px;
    }

    .form-control {
        margin-right: 12px;
    }

    .form-control:focus {
        color: #495057;
        background-color: #fff;
        border-color: #ff8880;
        outline: 0;
        box-shadow: none;
    }

    .cursor {
        cursor: pointer;
    }
</style>


<div class="d-flex justify-content-center align-items-center container">
    <div class="card py-5 px-3">
        <h5 class="m-0">Email verification</h5><span class="mobile-text">Enter the code we just send on your email</span>
        <form method="POST" class="mt-5">
            <input type="number" name="otp" class="form-control" autofocus="" maxlength="6">
            <div class="text-danger mt-2"><?php echo $verify_err ?></div>
            <button name="verify" class="btn btn-primary mt-4">Verify</button>
        </form>

    </div>
</div>