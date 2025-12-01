<?php
// Thông tin đơn mua
class Order {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Lấy tất cả đơn hàng
    public function getAll() {
        $sql = "SELECT orders.*, users.username AS user_name FROM orders LEFT JOIN users ON orders.user_id = users.id ORDER BY orders.created_at DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Tạo đơn hàng
    // === SỬA HÀM createOrder() ===
    public function createOrder(
        int $user_id, string $fullname, string $phone, string $email,
        string $address, string $district, string $city, string $postcode,
        string $note, string $payment_method, float $total
    ) {
        $status = 'Chờ xác nhận';
        $payment_status = ($payment_method === 'COD') ? 'Chưa thanh toán' : 'Chưa thanh toán'; // VNPay cũng là Chưa thanh toán (chờ callback)

        $sql = "INSERT INTO orders (
            user_id, fullname, phone, email, address, district, city, postcode,
            note, payment_method, total, status, payment_status
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            $user_id, $fullname, $phone, $email,
            $address, $district, $city, $postcode,
            $note, $payment_method, $total, $status, $payment_status
        ]);
        return $this->pdo->lastInsertId();
    }

    // === THÊM HÀM MỚI: cập nhật payment_status ===
    public function updatePaymentStatus(int $orderId, string $paymentStatus): bool
    {
        $sql = "UPDATE orders SET payment_status = ?, updated_at = NOW() WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$paymentStatus, $orderId]);
    }

    // Cập nhật trạng thái đơn hàng
    // public function updateStatus(string $newStatus, int $id): bool {
    //     $sql = "UPDATE orders SET status = ?, updated_at = NOW() WHERE id = ?";
    //     $stmt = $this->pdo->prepare($sql);
    //     return $stmt->execute([$newStatus, $id]); 
    // }

    // Đếm tất cả đơn hàng
    public function countAll() {
        $sql = "SELECT COUNT(*) AS c FROM orders";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetch(PDO::FETCH_ASSOC)['c']; //trả về c
    }

    // Đếm đơn hàng theo trạng thái
    // public function countByStatus(int $status) {
    //     $sql = "SELECT COUNT(*) AS c FROM orders WHERE status = ?";
    //     $stmt = $this->pdo->prepare($sql);
    //     $stmt->execute([$status]);
    //     return $stmt->fetch(PDO::FETCH_ASSOC)['c'];
    // }

    public function countByStatus(string $status) { // trong db lưu là chuỗi
        $sql = "SELECT COUNT(*) AS c FROM orders WHERE status = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$status]);
        return $stmt->fetch(PDO::FETCH_ASSOC)['c'];
    }

    // Tổng doanh thu theo ngày
    public function getTotalRevenueDay() {
        $sql = "SELECT SUM(total) AS total FROM orders WHERE DATE(created_at) = CURDATE() AND status = 'Giao thành công'";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }

    // Tổng doanh thu theo tháng
    public function getTotalRevenueMonth() {
        $sql = "SELECT SUM(total) AS total FROM orders WHERE MONTH(created_at) = MONTH(NOW()) AND YEAR(created_at) = YEAR(NOW()) AND status = 'Giao thành công'";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }

    // Tổng doanh thu theo năm
    public function getTotalRevenueYear() {
        $sql = "SELECT SUM(total) AS total FROM orders WHERE YEAR(created_at) = YEAR(NOW()) AND status = 'Giao thành công'";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }

    // Tổng doanh thu tất cả
    public function getTotalRevenueAll() {
        $sql = "SELECT SUM(total) AS total FROM orders WHERE status = 'Giao thành công'";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }

    // Khách hàng mua nhiều nhất
    public function getBestCustomers() {
        $sql = "SELECT users.id, users.username, users.fullname, COUNT(orders.id) AS total_orders FROM users INNER JOIN orders ON users.id = orders.user_id   WHERE orders.status = 'Giao thành công' GROUP BY users.id ORDER BY total_orders DESC LIMIT 10";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function purchaseHistory($user_id){
        $stmt = $this->pdo->prepare("
            SELECT 
                o.id AS order_id,
                o.created_at AS order_date,
                o.status,
                o.total AS total_price,
                o.payment_status,                    -- THÊM DÒNG NÀY
                p.name AS product_name,
                oi.quantity 
            FROM orders o 
            JOIN order_items oi ON o.id = oi.order_id 
            JOIN products p ON oi.product_id = p.id 
            WHERE o.user_id = ? AND o.status = 'Giao thành công'  
            ORDER BY o.created_at DESC, o.id DESC
        ");
        $stmt->execute([$user_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function pendingOrder($user_id){
        $stmt = $this->pdo->prepare("
            SELECT 
                o.id AS order_id, 
                o.created_at AS order_date, 
                o.status, 
                o.total AS total_price,
                o.payment_status,                    -- THÊM DÒNG NÀY
                p.name AS product_name, 
                oi.quantity 
            FROM orders o 
            JOIN order_items oi ON o.id = oi.order_id 
            JOIN products p ON oi.product_id = p.id 
            WHERE o.user_id = ? 
            AND (o.status = 'Chờ xác nhận' OR o.status = 'Đang giao') 
            ORDER BY o.created_at DESC, o.id DESC
        ");
        $stmt->execute([$user_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function cancelOrder($order_id,$user_id) {
        $stmt = $this->pdo->prepare("UPDATE orders SET status = 'Đã hủy' WHERE id = ? AND user_id= ? ");
        return $stmt->execute([$order_id,$user_id]);
    }
    public function getOrderById($order_id) {
        $stmt = $this->pdo->prepare("SELECT * FROM orders WHERE id = ?");
        $stmt->execute([$order_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function getCancelledOrders($user_id) {
        $stmt = $this->pdo->prepare("
            SELECT 
                o.id AS order_id,
                o.created_at AS order_date,
                o.status,
                o.total AS total_price,
                o.payment_status,                    -- THÊM DÒNG NÀY
                p.name AS product_name,
                oi.quantity 
            FROM orders o 
            JOIN order_items oi ON o.id = oi.order_id 
            JOIN products p ON oi.product_id = p.id 
            WHERE o.user_id = ? AND o.status = 'Đã hủy' 
            ORDER BY o.created_at DESC, o.id DESC
        ");
        $stmt->execute([$user_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // thêm
    public function updateOrderStatus(string $statusValue, int $orderId): bool {
        $sql = "UPDATE orders SET status = ?, updated_at = NOW() WHERE id = ?";
        
        try {
            $stmt = $this->pdo->prepare($sql);

            if ($stmt === false) {
                error_log("PDO Prepare Failed in updateOrderStatus.");
                return false;
            }

            $success = $stmt->execute([$statusValue, $orderId]);
            return $success; 

        } catch (PDOException $e) {
            error_log("Order status update DB error: " . $e->getMessage());
            return false;
        }
    }
    public function getUserIdByOrderId(int $orderId): ?int { 
        $sql = "SELECT user_id FROM orders WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$orderId]);
        $userId = $stmt->fetchColumn();
        return $userId ? (int)$userId : null;
    }
    
    //thêm 
    public function getAllByStatus(string $status) {
        $sql = "SELECT orders.*, users.username AS user_name FROM orders LEFT JOIN users ON orders.user_id = users.id WHERE orders.status = ? ORDER BY orders.created_at DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$status]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function confirmManualPayment(int $orderId): bool {
        // Cập nhật payment_status thành 'Đã thanh toán'
        // CHỈ cập nhật nếu payment_status HIỆN TẠI KHÔNG phải là 'Đã thanh toán'
        $sql = "UPDATE orders 
                SET payment_status = 'Đã thanh toán', updated_at = NOW() 
                WHERE id = ? 
                AND payment_status != 'Đã thanh toán'"; 
        $stmt = $this->pdo->prepare($sql);
        // Trả về true nếu lệnh execute thành công VÀ có hàng bị ảnh hưởng (tức là trạng thái được thay đổi)
        return $stmt->execute([$orderId]) && $stmt->rowCount() > 0; 
    }
}
?>
