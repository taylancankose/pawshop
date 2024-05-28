<?php
require "libs/vars.php";
require "libs/functions.php";

$title = $description = $image = "";
$price = 0;
$title_err = $description_err = $price_err = $err = $image_err = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $input_title = trim($_POST["title"]);

    // error management
    if (empty($input_title)) {
        $title_err = "Title can not be empty";
    } else if (strlen($input_title) > 255) {
        $title_err = "Title can not be longer than 255 characters";
    } else if (strlen($input_title) < 5) {
        $title_err = "Title can not be less than 5 characters";
    } else {
        $title = control_input($input_title);
    }

    $input_description = trim($_POST["description"]);
    if (empty($input_description)) {
        $description_err = "Description can not be empty";
    } else if (strlen($input_description) < 15) {
        $description_err = "Description can not be less than 15 characters";
    } else {
        $description = control_input($input_description);
    }

    if (empty($_FILES["image"]["name"])) {
        $image_err = "Please select an image";
    }else{
        $result = uploadImage($_FILES["image"]);
        if($result["isSuccess"] == 0){
            $image_err = $result["message"];
        }else {
            $image = $result["image"];
        }
    }

    $stock = isset($_POST["stock"]) && $_POST["stock"] == 1 ? 1 : 0;

    $input_price = $_POST["price"];
    if (empty($input_price)) {
        $price_err = "Price can not be empty";
    } else if (strlen($input_price) == 0) {
        $price_err = "Price can not be 0";
    } else {
        $price = $input_price;
    }

    if (empty($title_err) && empty($price_err) && empty($description_err)) {
        if (createProduct($title,  $description,  $price, $image, $stock)) {
            if ($image) echo ($image);
            header('Location: index.php');
        } else {
            $err = "A problem occured while creating a new product";
        }
    }
}

?>

<?php include "views/_header.php" ?>
<?php include "views/_navbar.php" ?>

<div class="container mt-4 justify-content-center align-items-center">
    <?php if (!empty($err)) : ?>
        <div class="alert alert-danger">
            <?php echo $err ?>
        </div>
    <?php endif; ?>
    <form class="gap-4" action="add-product.php" method="POST" enctype="multipart/form-data">
        <div class="form-group mb-4">
            <label for="title" class="col-sm-2 col-form-label">Product Title</label>
            <div class="col-sm-10">
                <input type="text" class="form-control <?php echo (!empty($title_err)) ? 'is-invalid' : '' ?>" name="title" id="title" value="<?php echo $title ?>">
                <span class="invalid-feedback"><?php echo $title_err ?></span>
            </div>
        </div>
        <div class="form-group mb-4">
            <label for="description" class="col-sm-2 col-form-label">Description</label>
            <div class="col-sm-10">
                <textarea value="<?php echo $description ?>" name="description" id="description" class="form-control  <?php echo (!empty($description_err)) ? 'is-invalid' : '' ?>"></textarea>
                <span class="invalid-feedback"><?php echo $description_err ?></span>
            </div>
        </div>
        <div class="form-group mb-4">
            <label for="price" class="col-sm-2 col-form-label">Price</label>
            <div class="col-sm-10">
                <input type="number" class="form-control <?php echo (!empty($price_err)) ? 'is-invalid' : '' ?>" name="price" id="price" value="<?php echo $price ?>">
                <span class="invalid-feedback"><?php echo $price_err ?></span>
            </div>
        </div>
        <div class="form-group mb-4">
            <label for="upload">Product Image</label>
            <div class="col-sm-10 mt-2">
                <input type="file" class="form-control <?php echo (!empty($image_err)) ? 'is-invalid' : '' ?>" name="image" id="image">
                <span class="invalid-feedback"><?php echo $image_err ?></span>
            </div>
        </div>
        <div class="form-check mb-4 form-group">
            <input class="form-check-input" type="checkbox" value="1" id="stock" name="stock" checked>
            <label class="form-check-label" for="stock">
                In Stock
            </label>
        </div>
        <div class="form-group">
            <button type="submit" value="Upload" name="btnUpload" class="btn btn-primary mb-2">Add to Panel</button>
        </div>
    </form>
</div>




<?php include "views/_ckeditor.php" ?>