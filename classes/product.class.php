<?php
include_once 'classes/db.class.php';
?>

<?php

class Products extends Db
{
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

}
