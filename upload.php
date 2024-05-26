<?php
    if(isset($_FILES['fileToUpload']) && isset($_POST["btnUpload"])){
        $uploadOk = 1;

        $fileTmpPath = $_FILES["fileToUpload"]["tmp_name"];
        $fileName = $_FILES["fileToUpload"]["name"];
        $fileExtensions = array("jpg", "jpeg", "png", "jfif");

        if(empty($fileName)){
            echo "<p class='alert alert-danger'>Please select a document to upload </p>";
        }

        $fileSize = $_FILES["fileToUpload"]["size"];
        if($fileSize > 500000){
            echo "<p class='alert alert-danger'>File size should be less than 500KB </p>";
            $uploadOk = 0;
        }

        $fileName_arr = pathinfo($fileName);
        $fileName_without_extension = $fileName_arr["filename"];
        // if extension exists
        $file_extention = isset($fileName_arr["extension"]) ? $fileName_arr['extension'] : '';

        if(!in_array($file_extention, $fileExtensions)){
            echo "<p class='alert alert-danger'>Undefined file extension</p> <br>";
            echo "<p class='alert alert-danger'>Please upload only jpg, jpeg, png and jfif documents.</p>";
            $uploadOk = 0;
        }

        // to upload a file with same name we need to rename
        $new_file_name = md5(time().$fileName_without_extension) . '.' . $file_extention;
        $file_path = './uploads/';
        $final_path = $file_path.$new_file_name;
        if($uploadOk == 0){
            echo "<p class='alert alert-danger'> Upload failed </p>";
        }else{
            if(move_uploaded_file($fileTmpPath, $final_path)){
                echo "<p class='alert alert-success'>Uploaded successfully </p>";
                header('Location: index.php');
            }else{
                echo "<p class='alert alert-danger'> Unknown error </p>";
            }
        }
    }
?>
