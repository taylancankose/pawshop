<?php
include_once 'classes/db.class.php';
include_once 'classes/product.class.php';
?>

<?php
session_start();

$pageCount = isset($_GET["pageCount"]) ? $_GET["pageCount"] : 5;

$product = new Products();
$is_admin = $product->isAdmin();

if (!$is_admin) {
    header("Location: index.php");
}

$page = 1;
if (isset($_GET["page"]) && is_numeric($_GET["page"])) $page = $_GET["page"];


$user = $product->getUserByUsername($_SESSION["username"]);
$result = $product->getAllOrders();
$filtered_results = [];

$status = isset($_GET["status"]) ? $_GET["status"] : null;
$page = isset($_GET["page"]) ? $_GET["page"] : 1; // Sayfa numarası varsayılan olarak 1 olarak belirlendi
$filtered_results = $product->getAllOrdersByFilters($page, $status);
$total_pages = $filtered_results['total_pages'];
$results = $filtered_results['orders'];


?>

<?php include_once "views/_header.php" ?>
<?php include_once "views/_navbar.php" ?>

<style>
    body {
        color: #566787;
        font-family: 'Varela Round', sans-serif;
        font-size: 13px;
    }

    .table-responsive {
        margin: 30px 0;
    }

    .table-wrapper {
        min-width: 1000px;
        background: #f5f5f5;
        padding: 20px 25px;
        border-radius: 3px;
        box-shadow: 0 1px 1px rgba(0, 0, 0, .05);
    }

    .table-wrapper .btn {
        float: right;
        color: #333;
        background-color: #fff;
        border-radius: 3px;
        border: none;
        outline: none !important;
        margin-left: 10px;
    }

    .table-wrapper .btn:hover {
        color: #333;
        background: #f2f2f2;
    }

    .table-wrapper .btn.btn-primary {
        color: #fff;
        background: #03A9F4;
    }

    .table-wrapper .btn.btn-primary:hover {
        background: #03a3e7;
    }

    .table-title .btn {
        font-size: 13px;
        border: none;
    }

    .table-title .btn i {
        float: left;
        font-size: 21px;
        margin-right: 5px;
    }

    .table-title .btn span {
        float: left;
        margin-top: 2px;
    }

    .table-title {
        color: #fff;
        background: #4b5366;
        padding: 16px 25px;
        margin: -20px -25px 10px;
        border-radius: 3px 3px 0 0;
    }

    .table-title h2 {
        margin: 5px 0 0;
        font-size: 24px;
    }

    .show-entries select.form-control {
        width: 60px;
        margin: 0 5px;
    }

    .table-filter .filter-group {
        float: right;
        margin-left: 15px;
    }

    .table-filter input,
    .table-filter select {
        height: 34px;
        border-radius: 3px;
        border-color: #ddd;
        box-shadow: none;
    }

    .table-filter {
        padding: 5px 0 15px;
        border-bottom: 1px solid #e9e9e9;
        margin-bottom: 5px;
    }

    .table-filter .btn {
        height: 34px;
    }

    .table-filter label {
        font-weight: normal;
        margin-left: 10px;
    }

    .table-filter select,
    .table-filter input {
        display: inline-block;
        margin-left: 5px;
    }

    .table-filter input {
        width: 200px;
        display: inline-block;
    }

    .filter-group select.form-control {
        width: 110px;
    }

    .filter-icon {
        float: right;
        margin-top: 7px;
    }

    .filter-icon i {
        font-size: 18px;
        opacity: 0.7;
    }

    table.table tr th,
    table.table tr td {
        border-color: #e9e9e9;
        padding: 12px 15px;
        vertical-align: middle;
    }

    table.table tr th:first-child {
        width: 60px;
    }

    table.table tr th:last-child {
        width: 80px;
    }

    table.table-striped tbody tr:nth-of-type(odd) {
        background-color: #fcfcfc;
    }

    table.table-striped.table-hover tbody tr:hover {
        background: #f5f5f5;
    }

    table.table th i {
        font-size: 13px;
        margin: 0 5px;
        cursor: pointer;
    }

    table.table td a {
        font-weight: bold;
        color: #566787;
        display: inline-block;
        text-decoration: none;
    }

    table.table td a:hover {
        color: #2196F3;
    }

    table.table td a.view {
        width: 30px;
        height: 30px;
        color: #2196F3;
        border: 2px solid;
        border-radius: 30px;
        text-align: center;
    }

    table.table td a.view i {
        font-size: 22px;
        margin: 2px 0 0 1px;
    }

    table.table .avatar {
        border-radius: 50%;
        vertical-align: middle;
        margin-right: 10px;
    }

    .status {
        font-size: 30px;
        margin: 2px 2px 0 0;
        display: inline-block;
        vertical-align: middle;
        line-height: 10px;
    }

    .text-success {
        color: #10c469;
    }

    .text-info {
        color: #62c9e8;
    }

    .text-warning {
        color: #FFC107;
    }

    .text-danger {
        color: #ff5b5b;
    }

    .pagination {
        float: right;
        margin: 0 0 5px;
    }

    .pagination li a {
        border: none;
        font-size: 13px;
        min-width: 30px;
        min-height: 30px;
        color: #999;
        margin: 0 2px;
        line-height: 30px;
        border-radius: 2px !important;
        text-align: center;
        padding: 0 6px;
    }

    .pagination li a:hover {
        color: #666;
    }

    .pagination li.active a {
        background: #03A9F4;
    }

    .pagination li.active a:hover {
        background: #0397d6;
    }

    .pagination li.disabled i {
        color: #ccc;
    }

    .pagination li i {
        font-size: 16px;
        padding-top: 6px
    }

    .hint-text {
        float: left;
        margin-top: 10px;
        font-size: 13px;
    }
</style>

<div class="container-xl">
    <div class="table-responsive">
        <div class="table-wrapper">
            <div class="table-title">
                <div class="row">
                    <div class="col-sm-4">
                        <h2>Order Details</h2>
                    </div>
                </div>
            </div>
            <div class="table-filter">
                <div class="row">
                    <div class="col-sm-9">
                        <form method="GET">
                            <button type="submit" class="btn btn-primary"><i class="fa fa-search"></i></button>
                            <div class="filter-group">
                                <label>Status</label>
                                <select name="status" class="form-control">
                                    <option value="any" <?php echo $status == "any" ? "selected" : "" ?>>Any</option>
                                    <option <?php echo $status == "delivered" ? "selected" : "" ?> value="delivered">Delivered</option>
                                    <option value="shipped" <?php echo $status == "shipped" ? "selected" : "" ?>>Shipped</option>
                                    <option value="pending" <?php echo $status == "pending" ? "selected" : "" ?>>Pending</option>
                                    <option value="cancelled" <?php echo $status == "cancelled" ? "selected" : "" ?>>Cancelled</option>
                                </select>
                            </div>
                            <span class="filter-icon"><i class="fa fa-filter"></i></span>
                        </form>
                    </div>
                </div>
            </div>
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th></th>
                        <th>Customer</th>
                        <th>Location</th>
                        <th>Order Date</th>
                        <th>Status</th>
                        <th>Net Amount</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($filtered_results)) : ?>
                        <?php foreach ($results as $order) : ?>
                            <?php $address = $product->getAddressById($order["address_id"]) ?>
                            <tr>
                                <td></td>
                                <td><?php echo $order["username"] ?></td>
                                <td><?php echo $address->state ?></td>
                                <td><?php echo $order["order_date"] ?></td>
                                <td>
                                    <span class="status <?= $order["status"] === "delivered" ? "text-success" : ($order["status"] === "cancelled" ? "text-danger" : ($order["status"] === "shipped" ? "text-warning" : "text-primary")) ?>">&bull;</span>
                                    <?= ucfirst($order["status"]) ?>
                                </td>

                                <td>$<?php echo $order["total_price"] ?></td>
                                <td>
                                    <a href="edit-order-status.php?order=<?php echo $order["order_number"] ?>">
                                        <i class="fa-solid fa-pen-to-square me-4"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <?php foreach ($result as $order) : ?>
                            <?php $address = $product->getAddressById($order->address_id) ?>
                            <tr>
                                <td></td>
                                <td><a href="#"><?php echo $order->username ?></a></td>
                                <td><?php echo $address->state ?></td>
                                <td><?php echo $order->order_date ?></td>
                                <td>
                                    <span class="status <?= $order["status"] === "delivered" ? "text-success" : ($order["status"] === "cancelled" ? "text-danger" : ($order["status"] === "shipped" ? "text-warning" : "text-primary")) ?>">&bull;</span>
                                    <?= ucfirst($order["status"]) ?>
                                </td>
                                <td>$<?php echo $order->total_price ?></td>
                                <td>
                                    <a href="edit-order-status.php?order=<?php echo $order["order_number"] ?>"><i class="fa-solid fa-pen-to-square me-4 "></a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>

        </div>

    </div>
</div>

<?php if ($total_pages > 1) : ?>
    <nav class="container justify-content-center d-flex">
        <ul class="pagination">
            <?php for ($x = 1; $x <= $total_pages; $x++) : ?>
                <li class="page-item <?php if ($x == $page) echo "active" ?>"><a class="page-link" href="
        <?php
                $url = "?page=" . $x;

                if (!empty($status)) {
                    $url .= "&status=" . $status;
                }

                echo $url;
        ?>
    "><?php echo $x; ?></a></li>
            <?php endfor; ?>
        </ul>
    </nav>

<?php endif; ?>