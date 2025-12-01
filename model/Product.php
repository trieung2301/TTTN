<?php
class Product {
    private $pdo;

    public function __construct($pdo) { //tạo đối tượng dpo để querry trực tiếp và mở kết nối db
        $this->pdo = $pdo;
    }

    public function getAllProducts(): array {
        $stmt = $this->pdo->prepare("SELECT * FROM products ORDER BY created_at DESC");
        $stmt->execute(); 
        return $stmt->fetchAll(PDO::FETCH_ASSOC);  // Trả về tất cả các kết quả dưới dạng mảng kết hợp
    }
    public function decreaseStock(int $product_id, int $quantity): bool
    {
        $sql = "UPDATE products SET stock = stock - :quantity WHERE id = :id AND stock >= :quantity"; 
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute(['quantity' => $quantity,'id' => $product_id]);
    }
    public function increaseStock($product_id, $quantity) {
        $stmt = $this->pdo->prepare("UPDATE products SET stock = stock + ? WHERE id = ?");
        return $stmt->execute([$quantity, $product_id]);
    }

    public function checkQuantityProducts($productId):int
    {
        $stmt =  $this->pdo->prepare("SELECT stock FROM products WHERE id = ?");
        $stmt->execute([$productId]);
        return (int)$stmt->fetchColumn(); //trả về cột 
    }
    public function checkQuantityCart($userId,$productId):int
    {
        $stmt =$this->pdo->prepare("SELECT quantity FROM cart_items WHERE user_id = ? AND product_id = ?");
        $stmt->execute([$userId, $productId]);
        return (int)$stmt->fetchColumn();
    }
    public function getProductById(int $id): ?array {
        $stmt = $this->pdo->prepare("SELECT * FROM products WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC); //trả về 1 sản phẩm vì là fetch
        return $product ?: null;
    }
    public function SearchProduct(string $name): ?array {
        $stmt = $this->pdo->prepare("SELECT * FROM products WHERE name LIKE ?");
        $stmt->execute(["%$name%"]);  
        return $stmt->fetchAll(PDO::FETCH_ASSOC); //trả nhiều sản phẩm với fetchall và fetch assocs trả về key value
    }

    public function createProduct(array $data): bool {
        $sql = "INSERT INTO products (name, description, image, price, stock, category_id, created_at, updated_at)
                VALUES (:name, :description, :image, :price, :stock, :category_id, NOW(), NOW())";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            'name'        => $data['name'],
            'description' => $data['description'],
            'image'       => $data['image'],
            'price'       => $data['price'],
            'stock'       => $data['stock'],
            'category_id' => $data['category_id'],
        ]);
    }

    public function updateProduct(int $id, array $data): bool {
        $sql = "UPDATE products 
                SET name = :name, description = :description, image = :image, 
                    price = :price, stock = :stock, category_id = :category_id, updated_at = NOW() 
                WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            'id'          => $id,
            'name'        => $data['name'],
            'description' => $data['description'],
            'image'       => $data['image'],
            'price'       => $data['price'],
            'stock'       => $data['stock'],
            'category_id' => $data['category_id'],
        ]);
    }

    public function deleteProduct(int $id): bool {
        $stmt = $this->pdo->prepare("DELETE FROM products WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }
    public function getProductsByCategory(int $categoryId): array {
        $stmt = $this->pdo->prepare("SELECT * FROM products WHERE category_id = :category_id ORDER BY created_at DESC");
        $stmt->execute(['category_id' => $categoryId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // thêm
    public function countAll(): int {
        $stmt = $this->pdo->query("SELECT COUNT(*) as total FROM products");
        $result = $stmt->fetch();
        return (int)$result['total'];
    }
    public function getProductOrderByStock (string $order = 'DESC'): array {
        $keyStock = ['ASC','DESC'];
        $sortStock = strtoupper($order);
        if(!in_array($sortStock,$keyStock)){
            $sortStock = 'DESC';
        }
        $stmt = $this->pdo->prepare("SELECT * FROM products ORDER BY stock {$sortStock}");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    /**
     * Lấy sản phẩm với bộ lọc danh mục + sắp xếp giá
     */
    // model/Product.php
    public function getFilteredProducts($categoryId = 'all', $sort = '', $search = ''): array
    {
        $sql = "SELECT p.*, c.name AS category_name 
                FROM products p 
                LEFT JOIN categories c ON p.category_id = c.id 
                WHERE 1=1";

        $params = [];

        if ($search) {
            $sql .= " AND p.name LIKE :search";
            $params[':search'] = "%$search%";
        }

        if ($categoryId !== 'all' && is_numeric($categoryId)) {
            $sql .= " AND p.category_id = :category_id";
            $params[':category_id'] = $categoryId;
        }

        if ($sort === 'asc') {
            $sql .= " ORDER BY p.price ASC";
        } elseif ($sort === 'desc') {
            $sql .= " ORDER BY p.price DESC";
        } else {
            $sql .= " ORDER BY p.created_at DESC";
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}