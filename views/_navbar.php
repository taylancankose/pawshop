<?php
require_once "classes/vars.php";
require_once "classes/order.class.php";
require_once "classes/auth.class.php";

$auth = new Auth();
$order = new Orders();

if(isset($_SESSION["username"])){
    $user = $auth->getUserByUsername($_SESSION["username"]) ;
    $orders = $order->getOrders($user->id);
}


?>

<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container">
        <a class="navbar-brand" href="index.php">
            <img src="imgs/logo.png" width="30" height="30" class="d-inline-block align-top" alt="">
            Paw Shop
        </a>
        <div class="collapse navbar-collapse">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
            </ul>
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a href="index.php" class="nav-link">Home</a>
                </li>
                <li class="nav-item">
                    <a href="shop.php" class="nav-link">Shop</a>
                </li>
                <?php if(!empty($orders)) : ?>
                    <li class="nav-item">
                    <a href="orders.php" class="nav-link">Orders</a>
                </li>
                <?php endif; ?>
            </ul>
            <ul class="navbar-nav mb-2 mb-lg-0">
                <?php if (isset($_SESSION["loggedIn"])) : ?>
                    <li class="nav-item">
                        <a href="logout.php" class="nav-link">Logout</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link"><?php echo $_SESSION["username"] ?> </a>
                    </li>
                    </li>
                    <?php
                    $user = $auth->getUser($_SESSION["username"]);
                    if ($_SESSION["user_type"] == "admin") {
                        echo "<li class='nav-item dropdown'>
                        <a class='nav-link dropdown-toggle' href='#' id='navbarDropdown' role='button' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'>
                        Shop Operations
                      </a>
                            <div class='dropdown-menu' aria-labelledby='navbarDropdown'>
                            <a href='add-product.php' class='dropdown-item'>Add Product</a>
                            <a class='dropdown-item' href='admin-products.php'>Manage Products</a>
                            <div class='dropdown-divider'></div>
                            <a class='dropdown-item ' href='add-category.php'>Add Category</a>
                            <a class='dropdown-item ' href='admin-categories.php'>Manage Categories</a>
                            <div class='dropdown-divider'></div>
                            <a class='dropdown-item ' href='admin-orders.php'>Manage Orders</a>
                            <a class='dropdown-item ' href='admin-users.php'>Manage Users</a>
                          </div>
                        </li>";
                    } else {
                        echo "
                        <a href='cart.php' class='nav-link'>
                          <i class='fa fa-shopping-cart '></i>
                        </a>
                      </li>
                      
                      ";
                    }
                    ?>
                <?php else : ?>

                    <li class="nav-item">
                        <a href="login.php" class="nav-link">Login</a>
                    </li>
                    <li class="nav-item">
                        <a href="register.php" class="nav-link">Register</a>
                    </li>
                <?php endif; ?>


            </ul>
        </div>
    </div>
</nav>