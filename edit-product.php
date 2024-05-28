<?php
require "libs/vars.php";
require_once "libs/functions.php";

$id = $_GET["id"];
$result = getProductById($id);
$selectedProduct = mysqli_fetch_assoc($result);
echo $selectedProduct["image"];
echo "<br>";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST["title"];
    $description = $_POST["description"];
    $price = $_POST["price"];
    $stock = $_POST["stock"];
    $image = $_POST["image"];
    print_r($image);
    // Yeni bir dosya yüklendiyse
    if (!empty($_FILES["image"]["name"])) {
        $result = uploadImage($_FILES["image"]);

        if ($result["isSuccess"] == 1) {
            $image = $result["image"]; // Yeni dosya adını al
        }
    }

    if(editProduct($id, $title, $description, $price, $image, $stock)){
        $_SESSION['message'] = $title . " isimli blog güncellendi.";
        $_SESSION['type'] = "success";

        header('Location: admin-products.php');
    } else {
        echo "error";
    }

}

?>

<?php include "views/_header.php" ?>
<?php include "views/_navbar.php" ?>


<div class="container mt-4 justify-content-center align-items-center">
    <form class="gap-4" method="POST" enctype="multipart/form-data">
        <div class="row">
        <div class="col-9">
            <div class="form-group mb-4">
                <label for="title" class="col-sm-2 col-form-label">Product Title</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" name="title" id="title" value="<?php echo $selectedProduct["title"] ?>">
                </div>
            </div>
            <div class="form-group mb-4">
                <label for="description" class="col-sm-2 col-form-label">Description</label>
                <div class="col-sm-10">
                    <textarea name="description" id="description" class="form-control">
                <?php echo html_entity_decode($selectedProduct["description"]) ?>
                </textarea>
                </div>
            </div>
            <div class="form-group mb-4">
                <label for="price" class="col-sm-2 col-form-label">Price</label>
                <div class="col-sm-10">
                    <input type="number" class="form-control" name="price" id="price" value="<?php echo $selectedProduct["price"] ?>">
                </div>
            </div>
            <div class="form-group mb-4">
                <label for="upload">Product Image</label>
                <div class="col-sm-10 mt-2">
                    <input type="file" class="form-control " name="image" id="image">
                </div>
            </div>
            <div class="form-check mb-4 form-group">
                <input class="form-check-input" type="checkbox" value="<?php echo $selectedProduct["stock"] ?>" id="stock" name="stock" checked="<?php echo $selectedProduct["stock"] == 1 ? $selectedProduct["stock"]  : false ?>">
                <label class="form-check-label" for="stock">
                    In Stock
                </label>
            </div>
            <div class="form-group">
                <button type="submit" value="Upload" name="btnUpload" class="btn btn-primary mb-2">Edit Product</button>
            </div>
        </div>
        <div class="col-3">
            <input type="hidden" name="image" value="<?php echo $selectedProduct["image"] ?>">
            <img src="uploads/<?php echo $selectedProduct["image"] ?>" alt="" class="img-fluid">
        </div>
        </div>
    </form>
</div>




<?php include "views/_ckeditor.php" ?>
<?php include "views/_footer.php" ?>