<?php
include_once 'classes/db.class.php';
include_once 'classes/product.class.php';
include_once 'classes/utils.class.php';
include_once 'classes/order.class.php';
include_once 'classes/auth.class.php';
include_once "classes/vars.php";
?>


<?php


$order_number = $_GET["order"];


$product = new Products();
$utils = new Utils();
$auth = new Auth();
$orders = new Orders();

$is_admin = $utils->isAdmin();

if(!$is_admin){
    header("Location: index.php");
}

$order = $orders->getOrdersByOrderNumber($order_number);
$products = $orders->getOrderProductsByOrderId($order->order_id);
$address = $orders->getAddressById($order->address_id);

if (isset($_POST["status"])) {
    $status = $_POST["status"];
    $orders->editOrderStatus($order->order_id, $status);
    header("Location: admin-orders.php");
} else {
    // Status parametresi yoksa, uygun bir hata mesajı gösterilebilir veya varsayılan bir değer atanabilir.
    $status = $order->status;
}


?>

<?php include_once "views/_header.php"; ?>
<?php include_once "views/_navbar.php"; ?>


<div class="container mt-5 pb-5">
    <div>
        <?php foreach ($products as $product) : ?>
            <section>
                <div class="py-5">
                    <div class="row justify-content-center ">
                        <div class="col-12">
                            <div class="card shadow-0 border rounded-3">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-12 col-lg-3 col-xl-3 mb-4 mb-lg-0">
                                            <div class="bg-image hover-zoom ripple rounded ripple-surface">
                                                <img src="uploads/<?php echo $product["image"] ?>" class="w-100" />
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-lg-6 col-xl-6">
                                            <h5><?php echo $product["title"] ?></h5>
                                            <p class="text-truncate mb-4 mb-md-0">
                                                <?php echo html_entity_decode(substr($product["description"], 0, 400) . "...") ?>
                                            </p>
                                        </div>
                                        <div class="col-md-6 col-lg-3 col-xl-3 border-sm-start-none border-start">
                                            <h4 class="mb-1 me-1">$<?php echo $product["price"] ?></h4>
                                            <h6 class="text-success"><?php echo $address->state.", ".$address->country ?></h6>
                                            <div class="d-flex flex-column mt-4">
                                            <p class="text-dark"><i><?php echo $address->address ?></i></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        <?php endforeach; ?>
    </div>

    <div>
        <h3 class="mb-4">Progress Bar</h3>
        <div class="progress">
            <div id="progressBar" class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
        </div>

        <form method="POST">
            <div class="mt-3">
                <div> <input <?php echo $status == "cancelled" ? "checked" : "" ?> id="cancelled" name="status" type="radio" value="cancelled" onclick="updateProgress(0)">
                    <label class="form-check-label text-danger" for="cancelled">
                        Cancelled
                    </label>
                </div>
                <div>
                    <input <?php echo $status == "pending" ? "checked" : "" ?> id="pending" name="status" type="radio" value="pending" onclick="updateProgress(30)">
                    <label class="form-check-label" for="pending">
                        Pending
                    </label>
                </div>
                <div>
                    <input <?php echo $status == "shipped" ? "checked" : "" ?> id="shipped" name="status" type="radio" value="shipped" onclick="updateProgress(60)">
                    <label class="form-check-label" for="shipped">
                        Shipped
                    </label>
                </div>
                <div>
                    <input <?php echo $status == "delivered" ? "checked" : "" ?> id="delivered" name="status" type="radio" value="delivered" onclick="updateProgress(100)">
                    <label class="form-check-label" for="delivered">
                        Delivered
                    </label>
                </div>
            </div>
            <button type="submit" class="btn btn-primary mt-3">Update Status</button>
        </form>
    </div>
</div>

<script>
    function updateProgress(width) {
        var progressBar = document.getElementById('progressBar');
        progressBar.style.width = width + '%';
        progressBar.setAttribute('aria-valuenow', width);
    }
</script>