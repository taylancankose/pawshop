<?php

$result = getProducts();

?>

<style>
    .product-title{
        max-width: 315px;
    }
    .product-link{
       text-decoration: none;
       color:black;
    
    }
    .product-link:hover {
        transition: all 0.5s ease-in-out;
        font-size: 1.55rem;
    }
</style>


<?php if (mysqli_num_rows($result) > 0) : ?>
    <div class="container ">
        <!-- Top Nav -->
        <div class="d-flex justify-content-between align-items-center">
            <h3 class="">Pet Products</h3>
            <div>
                <p class="" style="">Shop Now</p>
            </div>
        </div>
        <div class="d-flex justify-content-between mb-4" style="flex-wrap: wrap; margin-bottom: 12em;">
            <?php while ($product = mysqli_fetch_assoc($result)) : ?>
                <?php if ($product["stock"]) : ?>
                    <!-- Card -->
                    <div class="my-4">
                        <img src="uploads/<?php echo $product['image'] ?>" alt="" style="aspect-ratio: 1; border-radius: 15px; height: 16em;">
                            <div class="mt-3" >
                                <h4 class="product-title" style=";"><a  class="product-link" style="text-decoration: none; color: black;" href="product-details.php?id=<?php echo $product['id'] ?>"
                                ><?php echo strlen($product["title"]) > 40 ? substr($product["title"], 0, 44) . "..." : $product["title"] ?></a></h4>
                                <h4>$<?php echo $product["price"] ?></h4>
                                <button type="button" class="btn btn-lg btn-outline-secondary">Add to Cart</button>
                            </div>
                    </div>
                <?php endif; ?>
            <?php endwhile; ?>
        </div>
    </div>
<?php endif; ?>





