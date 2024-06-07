<?php
include_once 'classes/db.class.php';
include_once 'classes/product.class.php';
include_once 'classes/utils.class.php';
require_once "classes/vars.php";

$product = new Products();
$utils = new Utils();

$is_admin = $utils->isAdmin();

if(!$is_admin){
    header("Location: index.php");
}

$id = $_GET["id"];
echo $id;
if ($product->deleteProduct($id)) {
    echo "Product ID: " . $id . "deleted";

    header('Location: admin-products.php');
} else {
    echo "Error";
}
