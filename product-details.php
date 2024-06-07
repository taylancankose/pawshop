<?php
include_once 'classes/db.class.php';
include_once 'classes/product.class.php';
include_once 'classes/order.class.php';
include_once 'classes/auth.class.php';

?>

<?php
require_once "classes/vars.php";

$product = new Products();
$order = new Orders();
$auth = new Auth();

if (!isset($_GET["id"]) or !is_numeric($_GET["id"])) {
    header("Location: index.php");
}

$result = $product->getProductById($_GET["id"]);
$categories = $product->getCategoriesByProductId($result->id);
$alternative_products = $product->getProductsByCategoryId($categories[0]->id);
$user = $auth->getUserByUsername($_SESSION["username"]);

if (!$result) {
    header("Location: index.php");
}

if(isset($_POST["product_id"])){
    $user_id = $user->id; // Assuming username holds user ID
    $product_id =$_POST["product_id"];
    $qty = 1; // Assuming quantity is always 1 (you can modify this)

    if ($order->addToCart($user_id, $product_id, $qty)) {
        $message= "<div class='alert alert-success '>Ürün sepete eklendi. </div>";
      } else {
          $message= "<span class='alert alert-danger'>Ürün sepete eklenemedi. </span>";
    }
}

?>


<?php include_once "views/_header.php" ?>
<?php include_once "views/_navbar.php" ?>

<body>
    <!-- Product section-->
    <section class="py-5">
        <div class="container mt-3">
            <div class="container px-4">
                <div class="row gx-4 gx-lg-5">
                    <div class="col-md-6"><img class="card-img-top mb-md-0" src="uploads/<?php echo $result->image ? $result->image : "https://dummyimage.com/600x700/dee2e6/6c757d.jpg" ?>" alt="<?php echo $result->title ?>" /></div>
                    <div class="col-md-6">
                        <h1 class="display-5 fw-bolder"><?php echo $result->title ?></h1>
                        <div class="">
                            <h3>$40.00</h3>
                            <?php if(isset($_SESSION["user_type"]) && $_SESSION["user_type"] == "user") :?>
                                <form method="POST">
                                <input type="hidden" name="product_id" value="<?php echo $result->id ?>">
                                <button type="submit" name="add_to_cart" class="btn btn-lg btn-outline-secondary my-2">Add to Cart</button>
                            </form>
                            <?php endif ;?>
                        </div>
                        <p class="lead">
                            <?php echo html_entity_decode($result->description) ?>
                        </p>
                    </div>
                </div>
            </div>
    </section>
    <!-- Related items section-->
    <section class="py-5 bg-light">
        <div class="container px-4 px-lg-5 mt-5">
            <h2 class="fw-bolder mb-4">Related products</h2>
            <div class="row gx-4 gx-lg-5 row-cols-2 row-cols-md-3 row-cols-xl-4 justify-content-center">

                <?php foreach ($alternative_products as $ap) : ?>
                    <?php if ($ap->product_id != $result->id) : ?>
                        <div class="col mb-5">
                            <div class="card h-100">
                                <!-- Product image-->
                                <img class="card-img-top h-75" src="uploads/<?php echo $ap->image ?>" alt="<?php echo $ap->title ?>" />
                                <!-- Product details-->
                                <div class="card-body p-4">
                                    <div class="text-center">
                                        <!-- Product name-->
                                        <h5 class="fw-bolder"><?php echo strlen($ap->title) > 20 ? substr($ap->title, 0, 20) . "..." : $ap->title ?></h5>
                                        <!-- Product price-->
                                        $<?php echo $ap->price ?>
                                    </div>
                                </div>
                                <!-- Product actions-->
                                <div class="card-footer p-4 pt-0 border-top-0 bg-transparent">
                                    <div class="text-center">
                                        <a class="btn btn-outline-dark mt-auto" href="product-details.php?id=<?php echo $ap->product_id ?>">
                                            View options
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

</body>



<?php include_once "views/_footer.php"; ?>