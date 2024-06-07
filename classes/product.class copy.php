<?php
include_once 'classes/db.class.php';
?>
<?php
// Import PHPMailer classes into the global namespace
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Load Composer's autoloader
require 'vendor/autoload.php';
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
        // Check if the username already exists
        $sql_check_username = "SELECT COUNT(*) FROM users WHERE username = :username";
        $stmt_check_username = $this->connect()->prepare($sql_check_username);
        $stmt_check_username->execute(['username' => $username]);
        $username_exists = $stmt_check_username->fetchColumn();

        if ($username_exists > 0) {
            return "Username already exists";
        }

        // Check if the email already exists
        $sql_check_email = "SELECT COUNT(*) FROM users WHERE email = :email";
        $stmt_check_email = $this->connect()->prepare($sql_check_email);
        $stmt_check_email->execute(['email' => $email]);
        $email_exists = $stmt_check_email->fetchColumn();

        if ($email_exists > 0) {
            return "Email already exists";
        }

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

    public function updateUserType($user_id, $user_type){
        $sql = "UPDATE users SET user_type =:user_type WHERE id=:user_id";
        $stmt = $this->connect()->prepare($sql);
        return $stmt->execute(['user_id' => $user_id, 'user_type' => $user_type]);
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

    public function getProductsByCategoryId($id)
    {
        $sql = "SELECT * FROM product_category pc INNER JOIN products p on pc.product_id=p.id WHERE pc.category_id=:id";
        $stmt = $this->connect()->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetchAll();
    }
    public function getProductsByFilters($categoryId, $page)
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

        // Get total count of records
        $total_sql = "SELECT COUNT(*) " . $query;
        $total_stmt = $this->connect()->prepare($total_sql);
        $total_stmt->execute($params);
        $count = $total_stmt->fetchColumn();
        $total_pages = ceil($count / $pageCount);

        // Get paginated records
        $sql = "SELECT * " . $query . " LIMIT :offset, :pageCount";
        $stmt = $this->connect()->prepare($sql);

        // Bind parameters as integers
        $stmt->bindValue(':offset', (int) $offset, PDO::PARAM_INT);
        $stmt->bindValue(':pageCount', (int) $pageCount, PDO::PARAM_INT);
        if (!empty($categoryId)) {
            $stmt->bindValue(1, $categoryId, PDO::PARAM_INT);
        }

        $stmt->execute();

        return array(
            "total_pages" => $total_pages,
            "data" => $stmt->fetchAll(PDO::FETCH_OBJ)
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

    public function deleteProduct($id)
    {
        $sql = "DELETE FROM products WHERE id=:id";
        $stmt = $this->connect()->prepare($sql);
        return $stmt->execute([
            'id' => $id,
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
            $stmt->bindParam(':product_id', $product_id);
            $stmt->bindParam(':category_id', $category);
            $stmt->execute();
        }

        return true;
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

    public function decreaseFromCart($user_id, $product_id)
    {
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

    public function getCart($user_id)
    {
        $sql = "SELECT * FROM shopping_cart WHERE user_id=:user_id ";
        $stmt = $this->connect()->prepare($sql);
        $stmt->execute(["user_id" => $user_id]);
        return $stmt->fetchAll();
    }

    public function createAddress($first_name, $last_name, $email, $address, $country, $state, $zip, $user_id)
    {
        $sql = "INSERT INTO addresses (first_name, last_name, email, address, country, state, zip, user_id) VALUES (:first_name, :last_name, :email, :address, :country, :state, :zip, :user_id)";
        $stmt = $this->connect()->prepare($sql);

        return $stmt->execute([
            'first_name' => $first_name,
            'last_name' => $last_name,
            'email' => $email,
            'address' => $address,
            'country' => $country,
            'state' => $state,
            'zip' => $zip,
            'user_id' => $user_id,
        ]);
    }

    public function getAddressById($address_id)
    {
        $sql = "SELECT * FROM addresses WHERE id=:address_id";
        $stmt = $this->connect()->prepare($sql);
        $stmt->execute(["address_id" => $address_id]);
        return $stmt->fetch();
    }

    public function getAddressesByUser($user_id)
    {
        $sql = "SELECT * FROM addresses WHERE user_id=:user_id";
        $stmt = $this->connect()->prepare($sql);
        $stmt->execute(["user_id" => $user_id]);

        return $stmt->fetchAll();
    }

    public function createOrder($user_id, $address_id, $total_price, $order_number)
    {
        $sql = "INSERT INTO orders (user_id, address_id, total_price, order_number) VALUES (:user_id, :address_id, :total_price, :order_number)";
        $stmt = $this->connect()->prepare($sql);

        return $stmt->execute([
            "user_id" => $user_id,
            "address_id" => $address_id,
            "total_price" => $total_price,
            "order_number" => $order_number
        ]);
    }

    public function getOrdersByOrderNumber($order_number)
    {
        $sql = "SELECT * FROM orders WHERE order_number=:order_number";
        $stmt = $this->connect()->prepare($sql);
        $stmt->execute([
            "order_number" => $order_number,
        ]);
        return $stmt->fetch();
    }

    public function addProductToOrder($order_id, $product_id, $qty)
    {
        $sql = "INSERT INTO order_products (order_id, product_id, qty) VALUES (:order_id, :product_id, :qty)";
        $stmt = $this->connect()->prepare($sql);

        return $stmt->execute([
            'order_id' => $order_id,
            'product_id' => $product_id,
            'qty' => $qty,
        ]);
    }

    public function clearCart($user_id)
    {
        $sql = "DELETE FROM shopping_cart WHERE user_id=:user_id";
        $stmt = $this->connect()->prepare($sql);
        $stmt->execute([
            "user_id" => $user_id
        ]);
    }

    public function getOrders($user_id)
    {
        $sql = "SELECT 
            orders.order_id, 
            orders.order_number, 
            orders.total_price, 
            orders.order_date, 
            orders.status,
            orders.order_date,
            orders.address_id,
            users.id as user_id, 
            users.username, 
            products.id as product_id,
            products.title,
            products.price,
            order_products.qty
        FROM 
            orders
        JOIN 
            users ON orders.user_id = users.id
        JOIN 
            order_products ON orders.order_id = order_products.order_id
        JOIN 
            products ON order_products.product_id = products.id
        WHERE 
            user_id=:user_id";

        $stmt = $this->connect()->prepare($sql);
        $stmt->execute(["user_id" => $user_id]);

        return $stmt->fetchAll();
    }

    public function getAllOrders()
    {
        $sql = "SELECT 
            orders.order_id, 
            orders.order_number, 
            orders.total_price, 
            orders.order_date, 
            orders.status,
            orders.order_date,
            orders.address_id,
            users.id as user_id, 
            users.username, 
            products.id as product_id,
            products.title,
            products.price,
            order_products.qty
        FROM 
            orders
        JOIN 
            users ON orders.user_id = users.id
        JOIN 
            order_products ON orders.order_id = order_products.order_id
        JOIN 
            products ON order_products.product_id = products.id";

        $stmt = $this->connect()->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function getOrdersByFilters($user_id, $page)
    {
        $pageCount = 6;
        $offset = ($page - 1) * $pageCount;

        $query = "FROM orders o 
                  JOIN users u ON o.user_id = u.id
                  JOIN order_products op ON o.order_id = op.order_id 
                  JOIN products p ON op.product_id = p.id ";

        $total_sql = "SELECT COUNT(*) " . $query;
        $total_stmt = $this->connect()->prepare($total_sql);
        $total_stmt->execute();
        $count = $total_stmt->fetchColumn();
        $total_pages = ceil($count / $pageCount);

        $sql = "SELECT DISTINCT 
                    o.order_id, 
                    o.order_number, 
                    o.total_price, 
                    o.order_date, 
                    o.status,
                    o.address_id,
                    u.id as user_id, 
                    u.username, 
                    p.id as product_id,
                    p.title,
                    p.price,
                    op.qty
                " . $query . " 
                WHERE user_id = :user_id
                ORDER BY o.order_date DESC
                LIMIT :offset, :pageCount";

        $stmt = $this->connect()->prepare($sql);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':offset', $offset);
        $stmt->bindParam(':pageCount', $pageCount);
        $stmt->execute();

        return array(
            "total_pages" => $total_pages,
            "data" => $stmt->fetchAll()
        );
    }


    public function getAllOrdersByFilters($page, $status)
    {
        // Sayfalama için başlangıç noktası ve sayfa başına gösterilecek kayıt sayısı
        $records_per_page = 10;
        $offset = ($page - 1) * $records_per_page;

        // Veritabanı bağlantısını al
        $pdo = $this->connect();

        // Toplam kayıt sayısını al
        $countQuery = "SELECT COUNT(*) as total_records
                       FROM orders o 
                       JOIN users u ON o.user_id = u.id
                       JOIN order_products op ON o.order_id = op.order_id 
                       JOIN products p ON op.product_id = p.id 
                       WHERE 1=1";

        // Duruma göre filtreleme
        if (!empty($status)) {
            if ($status != "any") {
                $countQuery .= " AND o.status = :status";
            }
        }

        $countStmt = $pdo->prepare($countQuery);
        // Durum parametresini bağla
        if (!empty($status)) {
            if ($status == "any") {
            } else {
                $countStmt->bindParam(':status', $status, PDO::PARAM_STR);
            }
        }

        // Sorguyu çalıştır
        $countStmt->execute();

        // Toplam kayıt sayısını al
        $total_records = $countStmt->fetch(PDO::FETCH_ASSOC)['total_records'];
        $total_pages = ceil($total_records / $records_per_page);

        // Temel sorgu
        $query = "SELECT o.*, u.*, op.*, p.*
                  FROM orders o 
                  JOIN users u ON o.user_id = u.id
                  JOIN order_products op ON o.order_id = op.order_id 
                  JOIN products p ON op.product_id = p.id 
                  WHERE 1=1";

        // Duruma göre filtreleme
        if (!empty($status)) {
            if ($status != "any") {
                $query .= " AND o.status = :status";
            }
        }

        // Sayfalama için limit ve offset ekle
        $query .= " LIMIT :offset, :records_per_page";

        $stmt = $pdo->prepare($query);

        // Durum parametresini bağla
        if (!empty($status)) {
            if ($status != "any") {
                $stmt->bindParam(':status', $status, PDO::PARAM_STR);
            }
        }

        // Limit ve offset parametrelerini bağla
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->bindParam(':records_per_page', $records_per_page, PDO::PARAM_INT);

        // Sorguyu çalıştır
        $stmt->execute();

        // Sonuçları al
        $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Sonuçları döndür
        return [
            'orders' => $orders,
            'total_pages' => $total_pages
        ];
    }

    public function getOrderProductsByOrderId($order_id)
    {
        // Sorguyu hazırla
        $query = "SELECT p.*
                  FROM order_products op
                  JOIN products p ON op.product_id = p.id
                  WHERE op.order_id = :order_id";

        $stmt = $this->connect()->prepare($query);

        // Order ID parametresini bağla
        $stmt->bindParam(':order_id', $order_id, PDO::PARAM_INT);

        // Sorguyu çalıştır
        $stmt->execute();

        // Sonuçları al
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Sonuçları döndür
        return $products;
    }

    public function editOrderStatus($order_id, $status)
    {
        $sql = "UPDATE orders SET status=:status WHERE order_id=:order_id";
        $stmt = $this->connect()->prepare($sql);
        return $stmt->execute([
            'order_id' => $order_id,
            'status' => $status,
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

    public function sendOTP($email, $username, $otp)
    {
        $mail = new PHPMailer(true);
        $env = parse_ini_file('.env');

        $mailHost = $env["MAIL_HOST"];
        $mailPassword = $env["MAIL_PASSWORD"];
        $mailUsername = $env["MAIL_USERNAME"];
        $mailPort = $env["MAIL_PORT"];

        try {
            // Server settings
            $mail->SMTPDebug = 0;            // Debug mod kapalı redirect için kapatmak lazım
            $mail->isSMTP();                                  // SMTP Kullanarak Gönder
            $mail->Host       = $mailHost;             // SMTP Host
            $mail->SMTPAuth   = true;                         // SMTP Doğrulaması
            $mail->Username   = $mailUsername;  // SMTP Kullanıcı Adı
            $mail->Password   = $mailPassword;          // SMTP Şifre
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // SSL/TLS Ayarı
            $mail->Port       = $mailPort;                          // PORT Ayarı
            $mail->CharSet    = 'UTF-8';                      // Karakter seti
            $mail->setLanguage('tr', '/optional/path/to/language/directory/'); // Dil Ayarı

            // Recipients
            $mail->setFrom($mailUsername, 'Pawshop');
            $mail->addAddress($email, $username); // Alıcı E-Posta - İsim
            $mail->addReplyTo($mailUsername, 'Pawshop'); // Alıcı cevapla dediğinde mailin gideceği adres


            // Content
            $mail->isHTML(true); // E-Posta HTML
            $mail->Subject = 'Welcome to the Pawshop';
            $mail->Body    = '  <body
        style="
          margin: 0;
          background: #ffffff;
          font-size: 14px;
        "
      >
        <div
          style="
            max-width: 680px;
            margin: 0 auto;
            padding: 45px 30px 60px;
            background: #f4f7ff;
            background-repeat: no-repeat;
            background-size: 800px 452px;
            background-position: top center;
            font-size: 14px;
            color: #434343;
          "
        >
          <main>
            <div
              style="
                margin: 0;
                margin-top: 70px;
                padding: 92px 30px 115px;
                background: #ffffff;
                border-radius: 30px;
                text-align: center;
              "
            >
              <div style="width: 100%; max-width: 489px; margin: 0 auto;">
                <h1
                  style="
                    margin: 0;
                    font-size: 24px;
                    font-weight: 500;
                    color: #1f1f1f;
                  "
                >
                  Your OTP
                </h1>
                <p
                  style="
                    margin: 0;
                    margin-top: 17px;
                    font-size: 16px;
                    font-weight: 500;
                  "
                >
                  Hey  ' . htmlspecialchars($username) . ',
                </p>
                <p
                  style="
                    margin: 0;
                    margin-top: 17px;
                    font-weight: 500;
                    letter-spacing: 0.56px;
                  "
                >
                  Thank you for choosing PawShop Company. Use the following OTP
                  to complete the procedure to change your email address. Do not share this code with others, including PawShop
                  employees.
                </p>
                <p
                  style="
                    margin: 0;
                    margin-top: 60px;
                    font-size: 40px;
                    font-weight: 600;
                    letter-spacing: 25px;
                    color: #ba3d4f;
                  "
                >
                ' . htmlspecialchars($otp) . '
                </p>
              </div>
            </div>
    
            <p
              style="
                max-width: 400px;
                margin: 0 auto;
                margin-top: 90px;
                text-align: center;
                font-weight: 500;
                color: #8c8c8c;
              "
            >
              Need help? Ask at
              <a
                href="mailto:sy_bf@hotmail.com"
                style="color: #499fb6; text-decoration: none;"
                >sy_bf@hotmail.com</a
              >
            </p>
          </main>
    
          <footer
            style="
              width: 100%;
              max-width: 490px;
              margin: 20px auto 0;
              text-align: center;
              border-top: 1px solid #e6ebf1;
            "
          >
            <p
              style="
                margin: 0;
                margin-top: 40px;
                font-size: 16px;
                font-weight: 600;
                color: #434343;
              "
            >
              PawShop Company
            </p>
            </div>
            <p style="margin: 0; margin-top: 16px; color: #434343;">
              Copyright © 2024 Company. All rights reserved.
            </p>
          </footer>
        </div>
      </body>';
            $mail->AltBody = 'Welcome to the PawShop Company. Here is your OTP: ' . htmlspecialchars($otp) . '';

            $mail->send();
        } catch (Exception $e) {
            echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
    }

    public function verifyOTP($otp,$user_id){
        $sql = "SELECT * FROM users WHERE id = :user_id";
        $stmt = $this->connect()->prepare($sql);
        $stmt->execute(['user_id' => $user_id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($user) {
            $sql = "UPDATE users SET verified = 1 WHERE id = :user_id";
            $stmt = $this->connect()->prepare($sql);
            return $stmt->execute(['user_id' => $user_id]);
        } else {
            return false;
        }
    }
}
