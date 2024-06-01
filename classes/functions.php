<?php

function connectDb()
{
    $mydb = fopen("db.json", "r");
    $size = filesize("db.json");

    $data = json_decode(fread($mydb, $size), true);
    fclose($mydb);

    return $data;
}

function getUser(string $email){
    $userList = connectDb()["users"];

    foreach ($userList as $user) {
        if ($user["email"] == $email) {
            return $user;
        }
    }
    return null;
}

 
function registerUser(string $username, string $email, string $password)
{
    $db = connectDb();

    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    if (password_verify($password, $hashed_password)) {
        // db users array'ine pushla kullanıcıyı
        array_push($db["users"], array(
            "id" => count($db["users"]) + 1,
            "username" => $username,
            "email" => $email,
            "password" => $hashed_password,
            "is_admin" => false
        ));
    }

    // dosyayı db.json'a kaydet.
    $mydb = fopen("db.json", "w");
    fwrite($mydb, json_encode($db, JSON_PRETTY_PRINT));
    fclose($mydb);
}

function createProduct(string $title, string $description, int $price, string $image, int $stock = 1)
{
    include_once "dbsettings.php";

    $query = "INSERT INTO products (title, description, price, image, stock) VALUES (?, ?, ?, ?, ?)";
    $result = mysqli_prepare($connection, $query);
    mysqli_stmt_bind_param($result, 'ssisi', $title, $description, $price, $image, $stock);
    mysqli_stmt_execute($result);
    mysqli_stmt_close($result);
    mysqli_close($connection);

    return $result;
}

function getProducts()
{
    include_once "dbsettings.php";
    $query = "SELECT * FROM products";
    $result = mysqli_query($connection, $query);
    mysqli_close($connection);

    return $result;
}

function getCategories()
{
    include_once "dbsettings.php";
    $query = "SELECT * FROM categories";
    $result = mysqli_query($connection, $query);
    mysqli_close($connection);

    return $result;
}

function clearProductCategories(int $product_id){
    include_once "dbsettings.php";
    $query = "DELETE FROM product_category WHERE product_id = $product_id";
    $result = mysqli_query($connection, $query);
    echo mysqli_error($connection);

    return $result;
}

function getProductById(int $id)
{
    include_once "dbsettings.php";

    $query = "SELECT * FROM products WHERE id='$id'";
    $result = mysqli_query($connection, $query);

    mysqli_close($connection);

    return $result;
}

function getCategoryById(int $id)
{
    include_once "dbsettings.php";

    $query = "SELECT * FROM categories WHERE id=$id";
    $result = mysqli_query($connection, $query);

    mysqli_close($connection);

    return $result;
}

function getCategoriesByProductId($id) {
    include_once "dbsettings.php";

    $query = "SELECT c.id, c.name 
              FROM product_category pc 
              INNER JOIN categories c ON pc.category_id = c.id 
              WHERE pc.product_id = ?";
    $stmt = mysqli_prepare($connection, $query);
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    mysqli_close($connection);
    return $result;
}


function getProductsByCategoryId($id) {
    include_once "dbsettings.php";

    $query = "SELECT * FROM product_category pc INNER JOIN products p on pc.product_id=p.id WHERE pc.category_id=$id";
    $result = mysqli_query($connection, $query);
    mysqli_close($connection);
    return $result;
}

function uploadImage($file)
{
    $message = "";
    $uploadOK = 1;
    $fileTempPath = $file["tmp_name"];
    $fileName = $file["name"];
    $fileSize = $file["size"];
    $maxfileSize = ((1024 * 1024) * 1);
    $fileExtensions = array("jpg", "jpeg", "png");
    $file_path = "./uploads/";

    if ($fileSize > $maxfileSize) {
        $message = "File size is too big";
        $uploadOK = 0;
    }
    $fileName_arr = pathinfo($fileName);
    $fileName_without_extension = $fileName_arr['filename'];
    $file_extention = isset($fileName_arr['extension']) ? $fileName_arr['extension'] : '';

    if (!in_array($file_extention, $fileExtensions)) {
        $message .= "Undefined file extension";
        $message .= "Please upload only: " . implode(", ", $fileExtensions);
    }
    $new_file_name = md5(time() . $fileName_without_extension) . '.' . $file_extention;
    $final_path = $file_path . $new_file_name;
    if ($uploadOK == 0) {
        $message .= "Dosya yüklenemedi";
    } else {
        if (move_uploaded_file($fileTempPath, $final_path)) {
            $message .= "dosya yüklendi.";
        }
    }

    return array(
        "isSuccess" => $uploadOK,
        "message" => $message,
        "image" => $new_file_name
    );
}

function editProduct(int $id, string $title, string $description, int $price, string $image, int $stock = 1)
{
    include_once "dbsettings.php";

    $title = mysqli_real_escape_string($connection, $title);
    $description = mysqli_real_escape_string($connection, $description);

    $query = "UPDATE products SET title='$title', description='$description', price=$price, image='$image', stock=$stock WHERE id=$id";
    $result = mysqli_query($connection, $query);
    echo mysqli_error($connection);

    return $result;
}

function editCategory(int $id, string $name, int $is_active)
{
    include_once "dbsettings.php";

    $query = "UPDATE categories SET name='$name', is_active='$is_active' WHERE id=$id";
    $result = mysqli_query($connection, $query);
    echo mysqli_error($connection);

    return $result;
}

function deleteProduct(int $id)
{
    include_once "dbsettings.php";
    $query = "DELETE FROM products WHERE id=$id";
    $result = mysqli_query($connection, $query);
    return $result;
}

function deleteCategory(int $id)
{
    include_once "dbsettings.php";
    $query = "DELETE FROM categories WHERE id=$id";
    $result = mysqli_query($connection, $query);
    return $result;
}

function createCategory(string $name)
{
    include_once "dbsettings.php";

    $check_category_query = "SELECT * FROM categories WHERE name= ?";
    $check_stmt = mysqli_prepare($connection, $check_category_query);

    if ($check_stmt === false) {
        die('Prepare for check failed: ' . htmlspecialchars(mysqli_error($connection)));
    }
    mysqli_stmt_bind_param($check_stmt, 's', $name);
    mysqli_stmt_execute($check_stmt);
    mysqli_stmt_store_result($check_stmt);

    if (mysqli_stmt_num_rows($check_stmt) > 0) {
        mysqli_stmt_close($check_stmt);
        mysqli_close($connection);
        return false; // Category already exists
    }


    $query = "INSERT INTO categories (name) VALUES (?)";
    $stmt = mysqli_prepare($connection, $query);

    if ($stmt === false) {
        die('Prepare failed: ' . htmlspecialchars(mysqli_error($connection)));
    }

    mysqli_stmt_bind_param($stmt, 's', $name);
    $execute_result = mysqli_stmt_execute($stmt);

    if ($execute_result === false) {
        die('Execute failed: ' . htmlspecialchars(mysqli_stmt_error($stmt)));
    }

    mysqli_stmt_close($stmt);
    mysqli_close($connection);

    return $execute_result;
}

function addProductToCategories(int $product_id, array $categories){
    include_once 'dbsettings.php';

    $query = "";
    foreach($categories as $category){
        $query .= "INSERT INTO product_category(product_id, category_id) VALUES($product_id, $category);";
    }
    $result = mysqli_multi_query($connection, $query);

    echo mysqli_error($connection);

    return $result;
}

// for safety and database compatibility
function control_input($data)
{
    $data = htmlspecialchars($data);
    $data = stripslashes($data);

    return $data;
}

function isLoggedIn(){
    if(isset($_SESSION["loggedIn"]) && $_SESSION["loggedIn"] === true){
        return true;
    }else{
        return false;
    }
}

function isAdmin(){
    // ilk önce giriş yap sonra kontrol et
    if(isLoggedIn() && isset($_SESSION["user_type"]) && $_SESSION["user_type"] === "admin"){
        return true;
    }else{
        return false;
    }
}

function getProductsByFilters($categoryId, $keyword, $page){
    include_once "dbsettings.php";

    $pageCount = 4;
    $offset = ($page - 1) * $pageCount;
    $query = "";

    if(!empty($categoryId)){
        $query = "from product_category pc inner join products p on pc.product_id = p.id WHERE pc.category_id = $categoryId AND p.stock = 1";
    }else{
        $query = "from products p where p.stock = 1";
    }

    if(!empty($keyword)){
        $query .= " AND (p.title LIKE '%$keyword%' OR p.description LIKE '%$keyword%')";
    }

    $total_sql = "SELECT COUNT(*) ".$query;

    $count_data = mysqli_query($connection, $total_sql);
    if(!$count_data){
        die("Query failed: " . mysqli_error($connection));
    }
    $count = mysqli_fetch_array($count_data)[0];
    $total_pages = ceil($count / $pageCount);

    echo $total_pages;

    $sql = "SELECT * ".$query." LIMIT $offset, $pageCount";
    $result = mysqli_query($connection, $sql);
    if(!$result){
        die("Query failed: " . mysqli_error($connection));
    }
    mysqli_close($connection);
    return array(
        "total_pages" => $total_pages,
        "data" => $result
    );
}


?>
