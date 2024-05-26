<?php 

function connectDb(){
    $mydb = fopen("db.json", "r");
    $size = filesize("db.json");

    $data = json_decode(fread($mydb, $size), true);
    fclose($mydb);

    return $data;
}

function getUser(string $email){
    $userList = connectDb()["users"];

    foreach($userList as $user){
        if($user["email"] == $email){
            return $user;
        }
    }
    return null;
}

function registerUser(string $username,string $email,string $password){
    $db = connectDb();

    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    if(password_verify($password, $hashed_password)){
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


function createProduct(string $title, string $description, int $price, string $image, int $stock = 1){
    include "dbsettings.php";

    $query = "INSERT INTO products (title, description, price, image, stock) VALUES (?, ?, ?, ?, ?)";
    $result = mysqli_prepare($connection, $query);
    mysqli_stmt_bind_param($result, 'ssisi', $title, $description, $price, $image, $stock);
    mysqli_stmt_execute($result);
    mysqli_stmt_close($result);
    mysqli_close($connection);

    return $result;
}

function getProducts(){
    include "dbsettings.php";
    $query = "SELECT * FROM products";
    $result = mysqli_query($connection,$query);
    mysqli_close($connection);
    
    return $result;
}

// for safety and database compatibility
function control_input($data){
    $data = htmlspecialchars($data);
    $data = stripslashes($data);

    return $data;
}




?>