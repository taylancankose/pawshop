<?php
include_once 'classes/product.class.php';
include_once 'classes/auth.class.php';
include_once 'classes/order.class.php';

session_start();

$product = new Products();
$auth = new Auth();
$order_func = new Orders();


if(isset($_POST['username'])){
$username = $_POST['username'];
$user = $auth->getUserByUsername($username);
}
$categoryId = "";
$page = 1;

if (isset($_GET["categoryid"]) && is_numeric($_GET["categoryid"])) {
    $categoryId = $_GET["categoryid"];
}

if (isset($_GET["page"]) && is_numeric($_GET["page"])) {
    $page = $_GET["page"];
}

$filtered_products = [];

if (!empty($_GET['cats'])) {
    $categoryId = $_GET['cats'];
    $filtered_products = $product->getProductsByCategoryId($categoryId); 
} else {
    // Anahtar kelime filtresi burada eklenebilir
    $productsData = $product->getProductsByFilters($categoryId, $page);
    $total_pages = $productsData['total_pages'];
    $result = $productsData['data'];
}


if(isset($_POST["product_id"])){
    $user_id = $user->id; // Assuming username holds user ID
    $product_id =$_POST["product_id"];
    $qty = 1; // Assuming quantity is always 1 (you can modify this)
    if ($order_func->addToCart($user_id, $product_id, $qty)) {
        $message= "<div class='alert alert-success '>Ürün sepete eklendi. </div>";
      } else {
          $message= "<span class='alert alert-danger'>Ürün sepete eklenemedi. </span>";
    }
}
?>

<style>
    .product-title {
        max-width: 315px;
    }

    .product-link {
        text-decoration: none;
        color: black;
    }

    .product-link:hover {
        transition: all 0.5s ease-in-out;
        font-size: 1.55rem;
    }
</style>

<?php include_once "views/_header.php" ?>
<?php include_once "views/_navbar.php" ?>

<div class="container">
    <div class="d-flex justify-content-center align-items-center mb-4" style="flex-wrap: wrap; margin-bottom: 12em;">
        <?php if (!empty($filtered_products)) : ?>
            <?php foreach ($filtered_products as $item) : ?>
                <?php if($item->stock) :?>
                    <!-- Card -->
                    <div class="col-3 my-4">
                        <img src="uploads/<?php echo $item->image ?>" alt="" style="aspect-ratio: 1; border-radius: 15px; height: 16em;" />
                        <div class="mt-3">
                            <h4 class="product-title" style=" width: 12em; height:3em">
                                <a class="product-link" href="product-details.php?id=<?php echo $item->id ?>">
                                    <?php echo strlen($item->title) > 30 ? substr($item->title, 0, 30) . "..." : $item->title ?>
                                </a>
                            </h4>
                            <h4>$<?php echo $item->price ?></h4>
                            <?php if(isset($_SESSION["user_type"]) && $_SESSION["user_type"] == "user") :?>
                                <form method="POST">
                                <input type="hidden" name="product_id" value="<?php echo $item->id ?>">
                                <button type="submit" name="add_to_cart" class="btn btn-lg btn-outline-secondary my-2">Add to Cart</button>
                            </form>
                            <?php endif ;?>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        <?php else : ?>
            <?php foreach ($result as $item) : ?>
                <?php if ($item->stock) : ?>
                    <!-- Card -->
                    <div class="my-4">
                        <img src="uploads/<?php echo $item->image ?>" alt="" style="aspect-ratio: 1; border-radius: 15px; height: 16em;" />
                        <div class="mt-3">
                            <h4 class="product-title" style=" width: 12em; height:3em">
                                <a class="product-link" href="product-details.php?id=<?php echo $item->id ?>">
                                    <?php echo strlen($item->title) > 30 ? substr($item->title, 0, 30) . "..." : $item->title ?>
                                </a>
                            </h4>
                            <h4>$<?php echo $item->price ?></h4>
                            <?php if(isset($_SESSION["user_type"]) && $_SESSION["user_type"] == "user") :?>
                                <form method="POST">
                                <input type="hidden" name="product_id" value="<?php echo $item->id ?>">
                                <button type="submit" name="add_to_cart" class="btn btn-lg btn-outline-secondary my-2">Add to Cart</button>
                            </form>
                            <?php endif ;?>

                        </div>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>


<?php if (empty($filtered_products) && $total_pages > 1): ?>

<nav class="container justify-content-center d-flex">
  <ul class="pagination">
    <?php for ($x = 1; $x <= $total_pages; $x++): ?>
        <li class="page-item <?php if($x == $page) echo "active" ?>"><a class="page-link" href="
        
            <?php
                $url = "?page=".$x;

                if (!empty($categoryId)) {
                    $url .= "&categoryid=".$categoryId;
                }

                if (!empty($keyword)) {
                    $url .= "&q=".$keyword;
                }          
                echo $url;
            ?>
        "><?php echo $x;?></a></li>
    <?php endfor; ?>    
  </ul>
</nav>

<?php endif; ?>


