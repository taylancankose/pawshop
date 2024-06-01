<?php
include_once 'classes/product.class.php';
// $result = getProducts();
$categories = getCategories();
$categoryId = "";
$keyword = "";
$page = 1;

if (isset($_GET["categoryid"]) && is_numeric($_GET["categoryid"])) $categoryId = $_GET["categoryid"];
if (isset($_GET["q"])) $keyword = $_GET["q"];
if (isset($_GET["page"]) && is_numeric($_GET["page"])) $page = $_GET["page"];

$productsData = getProductsByFilters($categoryId, $keyword, $page);
$total_pages = $productsData['total_pages'];
$result = $productsData['data'];

$selectedCategory = isset($_GET['cats']) ? $_GET['cats'] : '';
$filtered_products = [];

if (isset($_GET['cats']) && $_GET['cats'] != '') {
    $filtered_results = getProductsByCategoryId($selectedCategory);
    while ($row = mysqli_fetch_assoc($filtered_results)) {
        $filtered_products[] = $row;
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

<?php if (!empty($result) && mysqli_num_rows($result) > 0) : ?>
    <div class="container">
        <div class="d-flex justify-content-between align-items-center">
            <h3 class="">Pet Products</h3>
            <div class="d-flex align-items-center justify-content-center align-middle">
                <form method="GET" action="" class="d-flex">
                    <select class="form-select me-2" aria-label="Default select example" name="cats">
                        <option value="">Select</option>
                        <?php foreach ($categories as $c) : ?>
                            <option value="<?php echo $c['id'] ?>" <?php echo ($selectedCategory == $c['id']) ? 'selected' : ''; ?>>
                                <?php echo $c["name"] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <button type="submit" class="btn btn-primary me-1" style="width:12em;">Filter</button>
                </form>
                <button class="btn btn-primary me-4" style="width:12em;">Shop Now</button>
            </div>
        </div>
        <div class="d-flex justify-content-between mb-4" style="flex-wrap: wrap; margin-bottom: 12em;">
            <?php if (!empty($filtered_products)) : ?>
                <?php foreach ($filtered_products as $filtered_product) : ?>
                    <?php if ($filtered_product["stock"]) : ?>
                        <!-- Card -->
                        <div class="my-4">
                            <img src="uploads/<?php echo $filtered_product['image'] ?>" alt="" style="aspect-ratio: 1; border-radius: 15px; height: 16em;" />
                            <div class="mt-3">
                                <h4 class="product-title" style=" width: 12em; height:3em">
                                    <a class="product-link" href="product-details.php?id=<?php echo $filtered_product['id'] ?>">
                                        <?php echo strlen($filtered_product["title"]) > 30 ? substr($filtered_product["title"], 0, 30) . "..." : $filtered_product["title"] ?>
                                    </a>
                                </h4>
                                <h4>$<?php echo $filtered_product["price"] ?></h4>
                                <button type="button" class="btn btn-lg btn-outline-secondary my-2">Add to Cart</button>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            <?php else : ?>
                <?php foreach($product->getProducts() as $item) : ?>
                    <?php if ($item->stock) : ?>
                        <!-- Card -->
                        <div class="my-4">
                            <img src="uploads/<?php echo $product['image'] ?>" alt="" style="aspect-ratio: 1; border-radius: 15px; height: 16em;" />
                            <div class="mt-3">
                                <h4 class="product-title" style=" width: 12em; height:3em">
                                    <a class="product-link" href="product-details.php?id=<?php echo $item->id ?>">
                                        <?php echo strlen($item->title) > 30 ? substr($item->title, 0, 30) . "..." : $item->title ?>
                                    </a>
                                </h4>
                                <h4>$<?php echo $item->price ?></h4>
                                <button type="button" class="btn btn-lg btn-outline-secondary my-2">Add to Cart</button>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
<?php endif; ?>

<?php if ($total_pages > 1): ?>

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
