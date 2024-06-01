<?php
include_once 'classes/db.class.php';
?>

<?php

class Products extends Db
{
    public function getUsers()
    {
        if ($this->isAdmin()) {
            $sql = "SELECT * FROM users";
            $stmt = $this->connect()->prepare($sql);
            $stmt->execute();

            return $stmt->fetchAll();
        } else {
            return [];
        }
    }

    public function registerUser(string $username, string $email, string $password)
    {
        $sql = "INSERT INTO users (username, email, password) VALUES (:username, :email, :password)";

        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $this->connect()->prepare($sql);
        return $stmt->execute([
            'username' => $username,
            'email' => $email,
            'password' => $hashed_password,
        ]);
    }

    public function login($email, $password)
    {
        $user = $this->getUser($email);
        if ($user && password_verify($password, $user->password)) {
            return $user;
        } else {
            return false;
        }
    }

    public function getUser($email)
    {
        $sql = "SELECT * FROM users WHERE email=:email";
        $stmt = $this->connect()->prepare($sql);
        $stmt->execute(['email' => $email]);
        return $stmt->fetch();
    }

    public function getUserByUsername($username)
    {
        $sql = "SELECT * FROM users WHERE username=:username";
        $stmt = $this->connect()->prepare($sql);
        $stmt->execute(['username' => $username]);
        return $stmt->fetch();
    }

    public function getProducts()
    {
        $sql = "SELECT * FROM products";
        $stmt = $this->connect()->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function getProductById(int $id)
    {
        $sql = "SELECT * FROM products WHERE id=:id";
        $stmt = $this->connect()->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }

    public function getCategories()
    {
        $sql = "SELECT * FROM categories";
        $stmt = $this->connect()->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function getCategoryById(int $id)
    {
        $sql = "SELECT * FROM categories WHERE id=:id";
        $stmt = $this->connect()->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }

    public function getCategoriesByProductId($id)
    {
        $sql = "SELECT c.id, c.name 
            FROM product_category pc 
            INNER JOIN categories c ON pc.category_id = c.id 
            WHERE pc.product_id = ?";
        $stmt = $this->connect()->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetchAll();
    }

    public function getProductsByCategoryId($id)
    {
        $sql = "SELECT * FROM product_category pc INNER JOIN products p on pc.product_id=p.id WHERE pc.category_id=:id";
        $stmt = $this->connect()->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetchAll();
    }

    public function getProductsByFilters($categoryId, $keyword, $page)
    {
        $pageCount = 4;
        $offset = ($page - 1) * $pageCount;
        $query = "";
        $params = array();

        if (!empty($categoryId)) {
            $query = "FROM product_category pc INNER JOIN products p ON pc.product_id = p.id WHERE pc.category_id = ? AND p.stock = 1";
            $params[] = $categoryId;
        } else {
            $query = "FROM products p WHERE p.stock = 1";
        }

        if (!empty($keyword)) {
            $query .= " AND (p.title LIKE ? OR p.description LIKE ?)";
            $params[] = "%$keyword%";
            $params[] = "%$keyword%";
        }

        $total_sql = "SELECT COUNT(*) " . $query;
        $total_stmt = $this->connect()->prepare($total_sql);
        $total_stmt->execute($params);
        $count = $total_stmt->fetchColumn();
        $total_pages = ceil($count / $pageCount);

        $sql = "SELECT * " . $query . " LIMIT :offset, :pageCount";
        $stmt = $this->connect()->prepare($sql);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->bindParam(':pageCount', $pageCount, PDO::PARAM_INT);
        $stmt->execute();

        return array(
            "total_pages" => $total_pages,
            "data" => $stmt->fetchAll()
        );
    }

    public function createProduct($title, $description, $price, $image, $stock = 1)
    {
        $sql = "INSERT INTO products (title, description, price, image, stock) VALUES (:title, :description, :price, :image, :stock)";
        $stmt = $this->connect()->prepare($sql);

        return $stmt->execute([
            'title' => $title,
            'description' => $description,
            'price' => $price,
            'image' => $image,
            'stock' => $stock
        ]);
    }

    public function createCategory($name)
    {
        $check_category_query = "SELECT * FROM categories WHERE name = ?";
        $check_stmt = $this->connect()->prepare($check_category_query);
        if ($check_stmt->rowCount() > 0) {
            return false; // Kategori zaten mevcut
        }

        $sql = "INSERT INTO categories (name) VALUES (:name)";
        $stmt = $this->connect()->prepare($sql);

        return $stmt->execute(["name" => $name]);
    }

    public function addProductToCategories(int $product_id, array $categories)
    {
        $sql = "INSERT INTO product_category (product_id, category_id) VALUES (:product_id, :category_id)";
        $stmt = $this->connect()->prepare($sql);

        foreach ($categories as $category) {
            $stmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
            $stmt->bindParam(':category_id', $category, PDO::PARAM_INT);
            $stmt->execute();
        }

        return true;
    }

    public function editProduct($id, $title, $description, $price, $image, $stock = 1)
    {
        $sql = "UPDATE products SET title=:title, description=:description, price=:price, image=:image, stock=:stock WHERE id=:id";
        $stmt = $this->connect()->prepare($sql);
        return $stmt->execute([
            'id' => $id,
            'title' => $title,
            'description' => $description,
            'price' => $price,
            'image' => $image,
            'stock' => $stock
        ]);
    }

    public function editCategory($id, $name, $is_active)
    {
        $sql = "UPDATE categories SET name=:name, is_active=:is_active WHERE id=:id";
        $stmt = $this->connect()->prepare($sql);
        return $stmt->execute([
            'id' => $id,
            'name' => $name,
            'is_active' => $is_active,
        ]);
    }

    public function addToCart($user_id, $product_id, $qty)
    {
        $data = "SELECT * FROM shopping_cart WHERE user_id = :user_id AND product_id = :product_id";
        $stmt = $this->connect()->prepare($data);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':product_id', $product_id);
        $stmt->execute();
        $existingProduct = $stmt->fetch();
        if ($existingProduct) {
            // Product already exists, update quantity
            $newQty = $existingProduct->qty + $qty;
            $updateSql = "UPDATE shopping_cart SET qty = :new_qty WHERE id = :id";
            $updateStmt = $this->connect()->prepare($updateSql);
            $updateStmt->bindParam(':new_qty', $newQty);
            $updateStmt->bindParam(':id', $existingProduct->id);
            $updateResult = $updateStmt->execute();

            if ($updateResult) {
                // Quantity updated successfully
                return true;
            } else {
                // Failed to update quantity
                return false;
            }
        } else {
            $sql = "INSERT INTO shopping_cart (user_id, product_id, qty) VALUES (:user_id, :product_id, :qty)";
            $stmt = $this->connect()->prepare($sql);
            $result = $stmt->execute([
                'user_id' => $user_id,
                'product_id' => $product_id,
                'qty' => $qty,
            ]);
        }
        return $result;
    }

    public function decreaseFromCart($user_id, $product_id){
        $data = "SELECT * FROM shopping_cart WHERE user_id = :user_id AND product_id = :product_id";
        $stmt = $this->connect()->prepare($data);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':product_id', $product_id);
        $stmt->execute();
        $existingProduct = $stmt->fetch();

        if ($existingProduct) {
          // Product exists, decrement quantity
          $newQty = $existingProduct->qty - 1;
      
          if ($newQty > 0) {
            // Update quantity if not zero
            $updateSql = "UPDATE shopping_cart SET qty = :new_qty WHERE id = :id";
            $updateStmt = $this->connect()->prepare($updateSql);
            $updateStmt->bindParam(':new_qty', $newQty);
            $updateStmt->bindParam(':id', $existingProduct->id);
            $updateResult = $updateStmt->execute();
      
            if ($updateResult) {
              return true;
            } else {
              return false;
            }
          } else {
            // Remove product from cart if quantity reaches zero
            $deleteSql = "DELETE FROM shopping_cart WHERE id = :id";
            $deleteStmt = $this->connect()->prepare($deleteSql);
            $deleteStmt->bindParam(':id', $existingProduct->id);
            $deleteResult = $deleteStmt->execute();
      
            if ($deleteResult) {
              return true;
            } else {
              return false;
            }
          }
        } else {
          return false;
        }
    }

    public function getCart($user_id){
        $sql = "SELECT * FROM shopping_cart WHERE user_id=:user_id ";
        $stmt = $this->connect()->prepare($sql);
        $stmt->execute(["user_id" => $user_id]);
        return $stmt->fetchAll();
    }

    public function deleteProduct($id)
    {
        $sql = "DELETE FROM products WHERE id=:id";
        $stmt = $this->connect()->prepare($sql);
        return $stmt->execute([
            'id' => $id,
        ]);
    }

    public function deleteCategory($id)
    {
        $sql = "DELETE FROM categories WHERE id=:id";
        $stmt = $this->connect()->prepare($sql);
        return $stmt->execute([
            'id' => $id,
        ]);
    }

    public function clearProductCategories($product_id)
    {
        $sql = "DELETE FROM product_category WHERE product_id=:product_id";
        $stmt = $this->connect()->prepare($sql);
        return $stmt->execute([
            'product_id' => $product_id,
        ]);
    }

    public function isLoggedIn()
    {
        if (isset($_SESSION["loggedIn"]) && $_SESSION["loggedIn"] === true) {
            return true;
        } else {
            return false;
        }
    }

    public function isAdmin()
    {
        if ($this->isLoggedIn() && isset($_SESSION["user_type"]) && $_SESSION["user_type"] === "admin") {
            return true;
        } else {
            return false;
        }
    }

    public function control_input($data)
    {
        $data = htmlspecialchars($data);
        $data = stripslashes($data);

        return $data;
    }

    public function uploadImage($file)
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
}
