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

$id = $_GET["id"];
$result = $product->getCategoryById($id);
 
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $categoryname = htmlspecialchars($_POST["categoryname"]);
    $isActive = isset($_POST["is_active"]) ? 1 : 0;

    if ($product->editCategory($id, $categoryname, $isActive)) {
        $_SESSION['message'] = $categoryname . " updated successfully.";
        $_SESSION['type'] = "success";

        header('Location: admin-categories.php');
    } else {
        echo "Error";
    }
}

?>

<?php include_once "views/_header.php" ?>
<?php include_once "views/_navbar.php" ?>

<div class="container my-3">

    <div class="row">

        <div class="col-12">

            <div class="card">

                <div class="card-body">
                    <form method="POST">

                        <div class="mb-3">
                            <label for="categoryname" class="form-label">title</label>
                            <input type="text" class="form-control" name="categoryname" id="categoryname" value="<?php echo $result->name ?>">
                        </div>

                        <div class="form-check mb-3">
                            <label for="is_active" class="form-check-label">is active</label>
                            <input type="checkbox" class="form-check-input" name="is_active" id="is_active" <?php if ($result->is_active) {
                                                                                                                echo "checked";
                                                                                                            } ?>>
                        </div>

                        <input type="submit" value="Submit" class="btn btn-primary">


                    </form>
                </div>
            </div>

        </div>

    </div>

</div>