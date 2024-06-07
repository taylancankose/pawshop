<?php
include_once 'classes/db.class.php';
include_once 'classes/product.class.php';
include_once 'classes/utils.class.php';
?>


<?php
require_once "classes/vars.php";

$product = new Products();
$utils = new Utils();

$is_admin = $utils->isAdmin();

if(!$is_admin){
    header("Location: index.php");
}


$categoryname = "";
$categoryname_err = "";



if ($_SERVER["REQUEST_METHOD"]=="POST") {

    $input_categoryname = trim($_POST["category"]);

    if(empty($input_categoryname)) {
        $categoryname_err = "Category cannot be empty";
    } else if (strlen($input_categoryname) > 100) {
        $categoryname_err = "Category cannot be more than 100 characters";
    }
    else {
        $categoryname = $utils->control_input($input_categoryname);
    }

    if(empty($categoryname_err)) {
        if ($product->createCategory($categoryname)) {
            $_SESSION['message'] = $categoryname." category added";
            $_SESSION['type'] = "success";
            
            header('Location: admin-categories.php');
        } else {
            echo "Category might already exist or there might be an unknown problem"; 
        }
    }      
 
}

?>

<?php include_once "views/_header.php" ?>
<?php include_once "views/_navbar.php" ?>


<div class="container">
    <div class="card  my-4">
        <div class="card-body">
            <form class="gap-4" action="add-category.php" method="POST">
                <div class="form-group mb-4">
                    <label for="category" class="col-sm-2 col-form-label">Category</label>
                    <div class="col-sm-10">
                        <input type="category" class="form-control" id="category" placeholder="Dog" name="category">
                    </div>
                </div>

                <input type="submit" value="Submit" class="btn btn-primary">
            </form>
        </div>
    </div>
</div>