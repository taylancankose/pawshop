<?php 
require "libs/vars.php";

require_once "libs/functions.php";
  
  if(isset($_POST["login"])){
    $email = $_POST["email"];
    $password = $_POST["password"];

    $user = getUser($email);
     // if user exists and email + pw matches with inputs
    if(!is_null($user) and $email == $user["email"] and password_verify($password, $user["password"])){
      setcookie("auth[email]", $user["email"], time() + (60*60));
      setcookie("auth[username]", $user["username"], time() + (60*60));

      header('Location: index.php');
    }else {
      echo "<div class='alert alert-danger mb-0 text-center'>Wrong email or password</div>"; 
    }
  }

?>

<?php include "views/_header.php" ?>
<?php include "views/_navbar.php" ?>

<div class="container mt-4 justify-content-center align-items-center">
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
  <div class="form-group ">
    <button type="submit" name="login" value="Submit"
    class="btn btn-primary mb-2">Login</button>
  </div>
</form>
</div>