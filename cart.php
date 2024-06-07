<?php include_once "views/_header.php" ?>
<?php include_once "views/_navbar.php" ?>

<?php
include_once 'classes/db.class.php';
include_once 'classes/product.class.php';
include_once 'classes/auth.class.php';
include_once 'classes/utils.class.php';
include_once 'classes/order.class.php';

?>

<?php

$product = new Products();
$auth = new Auth();
$utils = new Utils();
$order = new Orders();


$is_loggedIn = $utils->isLoggedIn();

if (!$is_loggedIn) {
    header("Location: index.php");
}

$user = $auth->getUserByUsername($_SESSION['username']);
$addresses = $order->getAddressesByUser($user->id);
$cart = $order->getCart($user->id);
$total = 0;
$shipping_price = 0;
$final = 0;

foreach ($cart as $c){
    $item = $product->getProductById($c->product_id);
    $total += $item->price * $c->qty;
    $shipping_price = $total * 0.1 ;
    $final = $total + $shipping_price;
}

$selected_address = null;
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["address"])) {
    $selected_address = $_POST["address"];

    $today = date("Ymd");
    $rand = strtoupper(substr(uniqid(sha1(time())), 0, 4));
    $order_number = $today . $rand;


    if ($final > 0 && isset($selected_address) && isset($_POST["cc_name"]) && isset($_POST["cc_number"]) && isset($_POST["cc_expiration"]) && isset($_POST["cc_cvv"])) {

        $order->createOrder($user->id, $selected_address, $final, $order_number);
        $order_id = $order->getOrdersByOrderNumber($order_number)->order_id;

        if ($order_id) {
            // Add products to the order_products table
            foreach ($cart as $c) {
                $order->addProductToOrder($order_id, $c->product_id, $c->qty);
            }
            // Clear the cart
            $order->clearCart($user->id);
            header("Location: order-success.php?success=$order_number");
            exit;
        } else {
            echo "<div class='alert alert-danger'>Order creation failed.</div>";
        }
    }
}


?>

<div class="container mb-5">
    <div class="py-5">
        <h2>Checkout</h2>
    </div>

    <div class="row">
        <div class="col-md-4 order-md-2 mb-4">
            <h4 class="d-flex justify-content-between align-items-center mb-3">
                <span class="text-muted">Your cart</span>
                <span class="badge badge-secondary badge-pill">3</span>
            </h4>
            <ul class="list-group mb-3">
                <?php foreach ($cart as $c) : ?>
                    <?php $item = $product->getProductById($c->product_id)?>
                    <li class="list-group-item d-flex justify-content-between lh-condensed">
                        <div>
                            <h6 class="my-0"><?php echo $item->title ?></h6>
                            <small class="text-muted"><?php echo substr($item->description, 0, 46) . "..." ?></small>
                        </div>
                        <span class="text-muted">$<?php echo $item->price . " (x$c->qty)" ?></span>
                    </li>
                <?php endforeach; ?>
                <li class="list-group-item d-flex justify-content-between bg-light">
                    <div class="text-danger">
                        <h6 class="my-0">Shipping</h6>
                        <small><i>DHL</i></small>
                    </div>
                    <span class="text-danger">$<?php echo $shipping_price ?></span>
                </li>
                <li class="list-group-item d-flex justify-content-between">
                    <span>Total (USD)</span>
                    <strong>$<?php echo $final ?> </strong>
                </li>
            </ul>

        </div>
        <div class="col-md-8 order-md-1">
            <form class="needs-validation" novalidate="" method="POST">
                <div class="d-flex ">
                    <div class="card w-25 me-4">
                        <h5 class="card-header">Add New Address</h5>
                        <div class="card-body d-flex justify-content-center align-items-center text-center">
                            <a href="address-form.php" class="text-dark" style="text-decoration: none; ">
                                <h5 class="card-title">+</h5>
                                <p class="card-text">Add New Address</p>
                            </a>
                        </div>
                    </div>
                    <?php foreach ($addresses as $address) : ?>
                        <div class="card w-25 me-4">
                            <h5 class="card-header"><?php echo $address->first_name . " " . $address->last_name ?></h5>
                            <div class="card-body">
                                <h5 class="card-title"></h5>
                                <p class="card-text"><?php echo $address->address ?></p>
                                <input type="radio" name="address" value="<?php echo $address->id; ?>" <?php if ($selected_address == $address->id) echo "checked"; ?> required>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <hr class="mb-4">

                <h4 class="mb-3">Payment</h4>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="cc-name">Name on card</label>
                        <input type="text" class="form-control" id="cc_name" name="cc_name" placeholder="" required="">
                        <small class="text-muted">Full name as displayed on card</small>
                        <div class="invalid-feedback">
                            Name on card is required
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="cc_number">Credit card number</label>
                        <input type="text" class="form-control" id="cc_number" name="cc_number" placeholder="" required="">
                        <div class="invalid-feedback">
                            Credit card number is required
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label for="cc_expiration">Expiration</label>
                        <input type="text" class="form-control" id="cc_expiration" name="cc_expiration" placeholder="" required="">
                        <div class="invalid-feedback">
                            Expiration date required
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="cc_cvv">CVV</label>
                        <input type="text" class="form-control" id="cc_cvv" name="cc_cvv" placeholder="" required="">
                        <div class="invalid-feedback">
                            Security code required
                        </div>
                    </div>
                </div>
                <button class="btn btn-primary btn-lg btn-block" type="submit">Continue to checkout</button>
            </form>
        </div>
    </div>
</div>