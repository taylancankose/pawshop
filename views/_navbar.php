<?php

require_once "libs/clientActions.php"

// if (!empty($_GET['q'])) {
//     $keyword = $_GET['q'];

//     $filmler = array_filter($filmler, function($film) use ($keyword) {
//         return (stristr($film['baslik'], $keyword) or stristr($film['aciklama'], $keyword));
//     });
// }

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
                    <a href="blog-create.php" class="nav-link">Home</a>
                </li>
                <li class="nav-item">
                    <a href="admin-blogs.php" class="nav-link">Shop</a>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link">About Us</a>
                </li>
            </ul>
            <ul class="navbar-nav mb-2 mb-lg-0">

                <?php if (isset($_COOKIE["auth"])) : ?>
                    <li class="nav-item">
                        <a href="logout.php" class="nav-link">Logout</a>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link">My Account</a>
                    </li>

                    <?php
                    $user = getUser($_COOKIE["auth"]["email"]);
                    if ($user["is_admin"]) {
                        echo "<li class='nav-item'>
                            <a href='add-product.php' class='nav-link'>Add Product</a>
                        </li>";
                    } else {
                        echo "<li class='nav-item'>
                        <a href='#' class='nav-link'>
                          <i class='fa fa-shopping-cart '></i>
                        </a>
                      </li>";
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