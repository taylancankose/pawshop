<?php
include_once 'classes/db.class.php';
include_once 'classes/product.class.php';
include_once 'classes/utils.class.php';
include_once 'classes/auth.class.php';
include_once 'classes/order.class.php';

?>

<?php
session_start();

$order_number = $_GET["success"];
$product = new Products();
$utils = new Utils();
$auth = new Auth();
$orders_func = new Orders();

$is_loggedIn = $utils->isLoggedIn();

if(!$is_loggedIn){
    header("Location: index.php");
}


$user = $auth->getUserByUsername($_SESSION["username"]);
$orders = $orders_func->getOrders($user->id);
 
?>

<?php include_once "views/_header.php" ?>
<?php include_once "views/_navbar.php" ?>

<div class="container mt-4 mb-4">
    <div class="row d-flex cart align-items-center justify-content-center">
        <div class="col-md-12">
            <div class="card p-5">
                <div class="text-center order-details">
                    <div class="d-flex justify-content-center mb-5 flex-column align-items-center"> 
                        <h3 class="">
                            <i class="fa fa-check text-success"></i>
                        </h3> 
                        <h5 class="text-success text-xl">Order Confirmed</h5> 
                        <h6 class="mt-1"><i>You can track your order by: <?php echo $order_number ?></i></h6>
                    </div> 
                    <a href="orders.php" class="btn btn-danger btn-block order-button">Go to your Order</a>
                </div>
            </div>
        </div>
    </div>
</div>