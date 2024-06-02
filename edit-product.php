<?php
include_once 'classes/db.class.php';
include_once 'classes/product.class.php';
?>

<?php
require_once "classes/vars.php";
 
$product = new Products();

$is_admin = $product->isAdmin();

if(!$is_admin){
    header("Location: index.php");
}

$id = $_GET["id"];
$result = $product->getProductById($id);

$categories = $product->getCategories();
$selectedCategories = $product->getCategoriesByProductId($id);

// $categories değişkeninin kontrolü
if (!isset($categories)) {
    // $categories tanımlı değilse, boş bir dizi olarak tanımlayın
    $categories = [];
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST["title"];
    $description = $_POST["description"];
    $price = $_POST["price"];
    $stock = isset($_POST["stock"]) ? 1 : 0; // Checkbox değeri kontrolü
    $image = $_POST["image"];

    // Yeni bir dosya yüklendiyse
    if (!empty($_FILES["image"]["name"])) {
        $result = $product->uploadImage($_FILES["image"]);

        if ($result["isSuccess"] == 1) {
            $image = $result["image"]; // Yeni dosya adını al
        }
    }

    // Kategori checkbox'larının kontrolü
    $categories = isset($_POST["categories"]) ? $_POST["categories"] : [];

    if ($product->editProduct($id, $title, $description, $price, $image, $stock)) {
        $product->clearProductCategories($id);
        if (count($categories) > 0) {
            $product->addProductToCategories($id, $categories);
        }


        $_SESSION['message'] = $title . " isimli blog güncellendi.";
        $_SESSION['type'] = "success";

        header('Location: admin-products.php');
    } else {
        echo "error";
    }
}

?>

<?php include_once "views/_header.php" ?>
<?php include_once "views/_navbar.php" ?>


<div class="container my-4 justify-content-center align-items-center">
    <form class="gap-4 card p-4" method="POST" enctype="multipart/form-data">
        <div class="row">
            <div class="col-9">
                <div class="form-group mb-4">
                    <label for="title" class="col-sm-2 col-form-label">Product Title</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" name="title" id="title" value="<?php echo $result->title ?>">
                    </div>
                </div>
                <div class="form-group mb-4">
                    <label for="description" class="col-sm-2 col-form-label">Description</label>
                    <div class="col-sm-10">
                        <textarea name="description" id="description" class="form-control">
                        <?php echo html_entity_decode($result->description) ?>
                    </textarea>
                    </div>
                </div>
                <div class="form-group mb-4">
                    <label for="price" class="col-sm-2 col-form-label">Price</label>
                    <div class="col-sm-10">
                        <input type="number" class="form-control" name="price" id="price" value="<?php echo $result->price ?>">
                    </div>
                </div>
                <div class="form-group mb-4">
                    <label for="upload">Product Image</label>
                    <div class="col-sm-10 mt-2">
                        <input type="file" class="form-control " name="image" id="image">
                    </div>
                </div>

                <div class="form-group">
                    <button type="submit" value="Upload" name="btnUpload" class="btn btn-primary mb-2">Edit Product</button>
                </div>
            </div>
            <div class="col-3">
                <?php foreach ($categories as $c) : ?>
                    <div class="form-check">
                        <label for="category_<?php echo $c->id ?>"><?php echo $c->name ?></label>

                        <input type="checkbox" name="categories[]" id="category_<?php echo $c->id ?>" class="form-check-input" value="<?php echo $c->id ?>" <?php $stock = false;
                                                                                                                                                            foreach ($selectedCategories as $s) {
                                                                                                                                                                if ($s->id == $c->id) {
                                                                                                                                                                    $stock = true;
                                                                                                                                                                }
                                                                                                                                                            }
                                                                                                                                                            if ($stock) {
                                                                                                                                                                echo "checked";
                                                                                                                                                            }
                                                                                                                                                            ?>>
                    </div>
                <?php endforeach; ?>
                <hr>
                <div class="form-check mb-4 form-group">
                    <input class="form-check-input" type="checkbox" value="1" id="stock" name="stock" <?php echo $result->stock == 1 ? "checked" : "" ?>>
                    <label class="form-check-label" for="stock">
                        In Stock
                    </label>
                </div>
                <hr>
                <input type="hidden" name="image" value="<?php echo $result->image ?>">
                <img src="uploads/<?php echo $result->image ?>" alt="" class="img-fluid">
            </div>
        </div>
    </form>
</div>




<?php include_once "views/_ckeditor.php" ?>
<?php include_once "views/_footer.php" ?>