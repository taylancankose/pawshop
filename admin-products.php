<?php
require "libs/vars.php";
require "libs/functions.php";

$result = getProducts();
$products = mysqli_fetch_all($result, MYSQLI_ASSOC);
?>


<?php include "views/_header.php" ?>
<?php include "views/_navbar.php" ?>


<div class="container">
<table class="table table-striped table-centered mb-0 mt-4">
    <thead>
        <tr>
            <th>Product Image</th>
            <th>Name</th>
            <th>Price</th>
            <th>Stock</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($products as $product) :?>
        <tr class="align-middle">
            <td class="table-user">
                <img src="uploads/<?php echo $product["image"] ?>" alt="table-user" class="me-2 rounded-circle" style="height: 3em; width:3em; object-fit: cover;" />
            </td>
            <td> 
                <a class="text-decoration-none text-dark"
                href="product-details.php?id=<?php echo $product['id'] ?>"><?php echo $product["title"] ?></a>
            </td>
            <td><?php echo $product["price"] ?></td>
            <td><?php echo $product["stock"] ?></td>
            <td class="table-action">
                <a href="edit-product.php?id=<?php echo $product["id"] ?>" class="action-icon"> <i class="fa fa-pencil me-3" aria-hidden="true"></i>
                </a>
                <a href="delete-product.php?id=<?php echo $product["id"] ?>"  class="action-icon"> <i class="fa fa-trash-o" aria-hidden="true"></i>
                </a>
            </td>
        </tr>
        <?php endforeach ;?>
    </tbody>
</table>
</div>