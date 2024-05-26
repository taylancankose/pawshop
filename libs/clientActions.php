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


function createProduct(string $title, string $description, string $price, string $image){
    $db = connectDb();

    array_push($db["products"], array(
        "id" => count($db["products"]) + 1,
        "title" => $title,
        "description" => $description,
        "price" => $price,
        "image" => $image
    ));

    $myfile = fopen("db.json", "w");
    fwrite($myfile, json_encode($db, JSON_PRETTY_PRINT));
    fclose($myfile);
}



?>