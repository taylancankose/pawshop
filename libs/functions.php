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

function getProductById(int $id){
    include "dbsettings.php";

    $query = "SELECT * FROM products WHERE id='$id'";
    $result = mysqli_query($connection,$query);

    mysqli_close($connection);

    return $result;
}

function uploadImage($file){
    $message = "";
    $uploadOK = 1;
    $fileTempPath = $file["tmp_name"];
    $fileName = $file["name"];
    $fileSize = $file["size"];
    $maxfileSize = ((1024 * 1024) * 1);
    $fileExtensions = array("jpg","jpeg","png");
    $file_path = "./uploads/";

    if($fileSize > $maxfileSize){
        $message = "File size is too big";
        $uploadOK = 0;
    }
    $fileName_arr = pathinfo($fileName);
    $fileName_without_extension = $fileName_arr['filename'];
    $file_extention = isset($fileName_arr['extension']) ? $fileName_arr['extension'] : '';

    if(!in_array($file_extention,$fileExtensions)){
        $message .= "Undefined file extension";
        $message .= "Please upload only: ".implode(", ",$fileExtensions);
    }
    $new_file_name = md5(time() . $fileName_without_extension) . '.' . $file_extention;
    $final_path = $file_path.$new_file_name;
    if($uploadOK == 0){
        $message .= "Dosya yüklenemedi";
    }else{
        if(move_uploaded_file($fileTempPath, $final_path)){
            $message .= "dosya yüklendi.";
        }
    }

    return array(
        "isSuccess" => $uploadOK,
        "message" => $message,
        "image" => $new_file_name
    );
}

function editProduct(int $id, string $title, string $description, int $price, string $image, int $stock = 1){
    include "dbsettings.php";

    $title = mysqli_real_escape_string($connection, $title);
    $description = mysqli_real_escape_string($connection, $description);

    $query = "UPDATE products SET title='$title', description='$description', price=$price, image='$image', stock=$stock WHERE id=$id";
    $result = mysqli_query($connection,$query);
    echo mysqli_error($connection);

    return $result;
}

function deleteProduct(int $id){
    include "dbsettings.php";
    $query = "DELETE FROM products WHERE id=$id";
    $result = mysqli_query($connection, $query);
    return $result;
}

// for safety and database compatibility
function control_input($data){
    $data = htmlspecialchars($data);
    $data = stripslashes($data);

    return $data;
}

?>