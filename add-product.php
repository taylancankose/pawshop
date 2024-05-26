<?php

require "libs/clientActions.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $title = $_POST["title"];
    $description = $_POST["description"];
    $image = $_FILES["fileToUpload"]["name"];
    $price = $_POST["price"];

    createProduct($title,  $description,  $price,  $image);
    header('Location: index.php');
}

?>

<?php include "views/_header.php" ?>
<?php include "views/_navbar.php" ?>

<div class="container mt-4 justify-content-center align-items-center">
    <form class="gap-4" action="process.php" method="POST" enctype="multipart/form-data">
        <div class="form-group mb-4">
            <label for="title" class="col-sm-2 col-form-label">Title</label>
            <div class="col-sm-10">
                <input type="text" name="title" class="form-control" id="title" placeholder="Product Title">
            </div>
        </div>
        <div class="form-group mb-4">
            <label for="description" class="col-sm-2 col-form-label">Description</label>
            <div class="col-sm-10">
                <textarea type="text" class="form-control" name="description" id="description" placeholder="Product Description"></textarea>
            </div>
        </div>
        <div class="form-group mb-4">
            <label for="price" class="col-sm-2 col-form-label">Price</label>
            <div class="col-sm-10">
                <input type="number" name="price" class="form-control" id="price" placeholder="0">
            </div>
        </div>
        <div class="form-group mb-4">
            <label for="upload">Product Image</label>
            <div class="col-sm-10 mt-2">
                <input type="file" class="form-control-file" id="upload" name="fileToUpload">
            </div>
        </div>
        <div class="form-group">
            <button type="submit" value="Upload" name="btnUpload" class="btn btn-primary mb-2">Add to Panel</button>
        </div>
</form>
</div>
