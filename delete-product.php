<?php

    require "libs/vars.php";
    require "libs/functions.php";

    $id = $_GET["id"];
    echo $id;
    if (deleteProduct($id)) {
        echo "Product ID: " . $id . "deleted";
    
        header('Location: admin-products.php');
    } else {
        echo "Error";
    } 

?>