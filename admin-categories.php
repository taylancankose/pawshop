<?php
    include_once 'classes/db.class.php';
    include_once 'classes/product.class.php';

    require_once "classes/vars.php";

    $product = new Products();

    $is_admin = $product->isAdmin();

if(!$is_admin){
    header("Location: index.php");
}

?>

<?php include_once "views/_header.php" ?>
<?php include_once "views/_message.php" ?>
<?php include_once "views/_navbar.php" ?>

<div class="container my-3">

    <div class="row">

        <div class="col-12">

            <div class="card mb-1">
                <div class="card-body">
                    <a href="add-category.php" class="btn btn-primary">New Category</a>
                </div>
            </div>

            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th style="width: 80px;" class="text-center">Id</th>
                        <th>Category Name</th>
                        <th style="width: 100px;">is active</th>
                        <th style="width: 130px;"></th>
                    </tr>
                </thead>
                <tbody>
                    <?php $result = $product->getCategories();  foreach($result as $item): ?>
                        <tr>
                            <td class="text-center"><?php echo $item->id?></td>
                            <td><?php echo $item->name?></td>
                            <td class="text-center">
                                <?php if($item->is_active): ?>
                                    <i class="fa fa-check"></i>
                                <?php else: ?>
                                    <i class="fa fa-times"></i>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a class="btn btn-primary btn-sm" href="edit-categories.php?id=<?php echo $item->id?>">edit</a>
                                <a class="btn btn-danger btn-sm" href="delete-category.php?id=<?php echo $item->id?>">delete</a>
                            </td>
                        </tr>
                    <?php endforeach ; ?>
                </tbody>
            </table>
            

        </div>    
    
    </div>

</div>



