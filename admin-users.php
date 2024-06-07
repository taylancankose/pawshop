<?php
include_once 'classes/db.class.php';
include_once 'classes/utils.class.php';
include_once 'classes/auth.class.php';
require_once "classes/vars.php";

$utils = new Utils();
$auth = new Auth();

$is_admin = $utils->isAdmin();
$users = $auth->getUsers();

if (!$is_admin) {
    header("Location: index.php");
}


?>


<?php include_once "views/_header.php" ?>
<?php include_once "views/_navbar.php" ?>


<div class="container">
    <table class="table my-4 table-striped table-centered">
        <thead>
            <tr>
                <th>Username</th>
                <th>Email</th>
                <th>User Type</th>
                <th>Created At</th>
                <th>Verified</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $item) : ?>
                <form method="POST">
                    <tr class="align-middle" data-id="<?php echo $item->id ?>">
                        <td>
                            <?php echo $item->username ?>
                        </td>
                        <td>
                            <?php echo $item->email ?>
                        </td>
                        <td>
                            <?php echo $item->user_type ?>
                        </td>
                        <td> <?php echo $item->created_at ?> </td>
                        <td class="table-action">
                            <?php if ($item->verified == 1) : ?>
                                <a class="action-icon text-dark"> <i class="fa-solid fa-check"></i></a>
                            <?php else : ?>
                                <a class="action-icon text-danger"> <i class="fa fa-xmark" aria-hidden="true"></i></a>
                            <?php endif; ?>
                        </td>
                        <td class="">
                            <a href="edit-user-role.php?user_name=<?php echo $item->username ?>" class="btn btn-sm btn-primary edit-user-type">Edit</a>
                        </td>
                    </tr>
                </form>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>