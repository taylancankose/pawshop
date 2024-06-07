<?php
include_once 'classes/db.class.php';
?>

<?php

class Orders extends Db
{
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

    public function getOrdersByFilters($user_id, $page){
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
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->bindParam(':pageCount', $pageCount, PDO::PARAM_INT);
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


}
