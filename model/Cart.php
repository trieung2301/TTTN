<?php
//Giở hàng của user
class Cart{
    private $pdo;

    public function __construct($pdo) { //tạo đối tượng dpo để querry trực tiếp và mở kết nối db
        $this->pdo = $pdo;
    }
    public function getCartItems($userId) {
        $stmt = $this->pdo->prepare("SELECT c.*, p.* FROM cart_items AS c JOIN products AS p ON c.product_id = p.id WHERE c.user_id = ?");
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function addToCart($userId, $productId, $quantity) {
        // Kiểm tra sản phẩm đã có trong giỏ chưa
        $stmt = $this->pdo->prepare("SELECT quantity FROM cart_items WHERE user_id = ? AND product_id = ? ");
        $stmt->execute([$userId, $productId]);
        $cartItem = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($cartItem) {// Đã có -> tăng số lượng
            $stmt = $this->pdo->prepare("UPDATE cart_items SET quantity = quantity + ? WHERE user_id = ? AND product_id = ?");
            $stmt->execute([$quantity, $userId, $productId]);
        } else {// Chưa có -> thêm mới
            $stmt = $this->pdo->prepare("INSERT INTO cart_items (user_id, product_id, quantity) VALUES (?, ?, ?)");
            $stmt->execute([$userId, $productId, $quantity]);
        }
    }
    public function deleteFromCart($userId, $productId) {
        $stmt = $this->pdo->prepare("DELETE FROM cart_items WHERE user_id = ? AND product_id = ?");
        $stmt->execute([$userId, $productId]);
    }
    public function updateQuantity($userId, $productId, $quantity) {
        $stmt = $this->pdo->prepare("UPDATE cart_items SET quantity = ? WHERE user_id = ? AND product_id = ?");
        $stmt->execute([$quantity, $userId, $productId]);
    }
    
    public function clearCart($userId) {
        $stmt = $this->pdo->prepare("DELETE FROM cart_items WHERE user_id = ?");
        $stmt->execute([$userId]);
    }
    public function getTotalItems($userId) {
        $stmt = $this->pdo->prepare("SELECT SUM(quantity) AS total_items FROM cart_items WHERE user_id = ?");
        $stmt->execute([$userId]);
        return $stmt->fetch(PDO::FETCH_ASSOC)['total_items'] ?? 0;
    }

}
?>