<?php
    include_once 'classes/db.class.php';
    include_once 'classes/product.class.php';
require_once "classes/vars.php";

// if(!isAdmin()){
//     header("Location: index.php");
//     exit;
// }

$product = new Products();

?>


<?php include_once "views/_header.php" ?>
<?php include_once "views/_navbar.php" ?>


<div class="container">
    <table class="table my-4 table-striped table-centered">
        <thead>
            <tr>
                <th>Product Image</th>
                <th>Name</th>
                <th>Categories</th>
                <th>Price</th>
                <th>Stock</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php $products= $product->getProducts(); foreach ($products as $item) : ?>
                <tr class="align-middle">
                    <td class="table-user">
                        <img src="uploads/<?php echo $item->image ?>" alt="table-user" class="me-2 rounded-circle" style="height: 3em; width:3em; object-fit: cover;" />
                    </td>
                    <td>
                        <a class="text-decoration-none text-dark" href="product-details.php?id=<?php echo $item->id ?>"><?php echo $item->title ?></a>
                    </td>
                    <td>
                    <?php echo "<ul>";
                        $selected_categories = $product->getCategoriesByProductId($item->id);
                        if (count($selected_categories) > 0) {
                            foreach($selected_categories as $category) {
                                echo "<li>".$category->name."</li>";
                            }
                            }else {
                                echo "<li>No category selected</li>";
                            }
                            echo "</ul>";              
                    ?>    
                    </td>
                    <td><?php echo $item->price ?></td>
                    <td><?php echo $item->stock ?></td>
                    <td class="table-action">
                        <a href="edit-product.php?id=<?php echo $item->id ?>" class="action-icon text-dark"> <i class="fa-solid fa-pen-to-square me-4"></i></i>
                        </a>
                        <a href="delete-product.php?id=<?php echo $item->id ?>" class="action-icon text-danger"> <i class="fa fa-trash" aria-hidden="true"></i>
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
