<?php
include_once 'classes/db.class.php';
include_once 'classes/product.class.php';
?>

<?php

$order_number = $_GET["success"];

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
                    <button class="btn btn-danger btn-block order-button">Go to your Order</button>
                </div>
            </div>
        </div>
    </div>
</div>