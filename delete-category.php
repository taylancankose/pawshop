<?php
include_once 'classes/db.class.php';
include_once 'classes/product.class.php';
?>

<?php
require_once "classes/vars.php";

$product = new Products();
$id = $_GET["id"];

if ($product->deleteCategory($id)) {
    $_SESSION['message'] = "ID No:". $id . " category deleted.";
    $_SESSION['type'] = "danger";

    header('Location: admin-categories.php');
} else {
    echo "Error";
}



?>