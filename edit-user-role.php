<?php
include_once 'classes/db.class.php';
include_once 'classes/utils.class.php';
include_once 'classes/auth.class.php';
require_once "classes/vars.php";


$utils = new Utils();
$auth = new Auth();

$is_admin = $utils->isAdmin();

if (!$is_admin) {
    header("Location: index.php");
}

$user_name = $_GET["user_name"];
$user = $auth->getUserByUsername($user_name);
if(isset($_POST["user_role"])){
    $user_role = $_POST["user_role"];
    if($auth->updateUserType($user->id, $user_role)){
        header("Location: admin-users.php");
    }
}
?>

<?php include_once "views/_header.php" ?>
<?php include_once "views/_navbar.php" ?>


<div class="container">
    <?php $user = $auth->getUserByUsername($user_name); ?>
<div class="card mt-4 w-100">
  <div class="card-body">
    <h5 class="card-title"><?php echo $user->username ?></h5>
    <p class="card-text"><?php echo $user->email ?></p>
    <div class="form-group">
    <label for="exampleFormControlSelect1">User Role</label>
    <form method="POST">
    <select name="user_role" class="form-control" id="user_role">
      <option value="user" <?php echo $user->user_type == "user" ? "selected" : "" ?>>User</option>
      <option value="admin" <?php echo $user->user_type == "admin" ? "selected" : "" ?>>Admin</option>
    </select>
  </div>
    <button type="submit" href="#" class="btn btn-primary mt-4">Button</button>
  </div>
</form>
</div>
</div>