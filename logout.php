<?php 

    setcookie("auth[email]","", time() - (60*60));
    setcookie("auth[username]","", time() - (60*60));

    header('Location: index.php');
?>