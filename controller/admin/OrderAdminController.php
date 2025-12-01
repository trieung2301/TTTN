<?php
require_once __DIR__ . "/../../model/Order.php";

class OrderAdminController {
    private Order $orderModel;
    private User $userModel;
    private OrderItems $orderItems;

    public const STATUS_MAP = [
        'Chờ xác nhận' => ['name' => 'Chờ xác nhận', 'class' => 'warning text-dark', 'db_value' => 'Chờ xác nhận'],
        'Đang giao' => ['name' => 'Đang giao', 'class' => 'info text-dark', 'db_value' => 'Đang giao'],
        'Giao thành công' => ['name' => 'Hoàn thành', 'class' => 'success', 'db_value' => 'Giao thành công'], 
        'Đã hủy' => ['name' => 'Đã hủy', 'class' => 'danger', 'db_value' => 'Đã hủy'],
    ];
    
    public function __construct(Order $orderModel, User $userModel, OrderItems $orderItems) {
        $this->orderModel = $orderModel;
        $this->userModel = $userModel; 
        $this->orderItems = $orderItems;
    }

    private function adminCheck(): void {
        if (!isset($_SESSION['user']) || ($_SESSION['user']['role'] ?? 'user') !== 'admin') { 
            header("Location: /php-pj/login");
            exit;
        }
    }

    public function getOrders(): void {
        $this->adminCheck();
        
        $currentStatusKey = trim($_GET['status'] ?? 'Tất cả'); 
        $statusKeysFromMap = array_keys(self::STATUS_MAP);
        
        $pendingKey = array_search('Chờ xác nhận', $statusKeysFromMap);
        if ($pendingKey !== false) {
            unset($statusKeysFromMap[$pendingKey]); 
            array_unshift($statusKeysFromMap, 'Chờ xác nhận');
        }

        array_unshift($statusKeysFromMap, 'Tất cả');
        $allStatusesKeys = $statusKeysFromMap;

        if (!in_array($currentStatusKey, $allStatusesKeys)) {
            $currentStatusKey = 'Tất cả'; 
        }

        try {
            if ($currentStatusKey === 'Tất cả') {
                $orders = $this->orderModel->getAll(); 
            } else {
                $dbStatusValue = self::STATUS_MAP[$currentStatusKey]['db_value']; 
                $orders = $this->orderModel->getAllByStatus($dbStatusValue); 
            }

        } catch (Exception $e) {
            error_log("Error loading orders: " . $e->getMessage());
            $orders = [];
            $_SESSION['error_message'] = "Không thể tải dữ liệu đơn hàng.";
        }
        
        include __DIR__ . "/../../view/admin/orderAdmin.php";
    }

    public function updateOrderStatus(): void {
        $this->adminCheck();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $orderId = (int)($_POST['id'] ?? 0);
            $newStatusKey = trim($_POST['status'] ?? ''); 

            if (!isset(self::STATUS_MAP[$newStatusKey])) {
                $_SESSION['error_message'] = "Trạng thái '{$newStatusKey}' không hợp lệ.";
                header("Location: /php-pj/admin/orders");
                exit;
            }
            $newDbValue = self::STATUS_MAP[$newStatusKey]['db_value'];
            $newStatusName = self::STATUS_MAP[$newStatusKey]['name'];

            try {
                if ($this->orderModel->updateOrderStatus($newDbValue, $orderId)) { 
                    $_SESSION['success_message'] = "Cập nhật trạng thái đơn hàng ID: {$orderId} thành công! (Trạng thái mới: {$newStatusName}) ✅";
                    
                    $userId = $this->orderModel->getUserIdByOrderId($orderId);
                    
                    if ($userId !== null && $userId > 0) {
                        $newTotalSpent = $this->userModel->calculateTotalSpent($userId);
                        $newLevel = $this->userModel->determineLevel($newTotalSpent); 
                        $this->userModel->updateLevel($userId, $newTotalSpent, $newLevel);
                    }

                } else {
                    $_SESSION['error_message'] = "Cập nhật thất bại. Đơn hàng ID: {$orderId} không tồn tại hoặc trạng thái đã là '{$newStatusName}'.";
                }
            } catch (PDOException $e) {
                error_log("Order update DB error: " . $e->getMessage());
                $_SESSION['error_message'] = "Lỗi Database: Không thể cập nhật trạng thái đơn hàng.";
            }
        }

        header("Location: /php-pj/admin/orders");
        exit;
    }

    public function viewOrderDetail(): void {
        $this->adminCheck();

        $orderId = $_GET['id'] ?? null;
        
        if (!$orderId) {
            $_SESSION['error_message'] = "Không tìm thấy ID đơn hàng.";
            header('Location: /php-pj/admin/orders');
            exit;
        }
        $orderDetail = $this->orderModel->getOrderById($orderId); 
        
        if (!$orderDetail) {
            $_SESSION['error_message'] = "Đơn hàng ID {$orderId} không tồn tại.";
            header('Location: /php-pj/admin/orders');
            exit;
        }
        
        $currentStatusKey = $orderDetail['status'] ?? '';
        $statusInfo = self::STATUS_MAP[$currentStatusKey] ?? ['name' => $currentStatusKey, 'class' => 'secondary']; // Lấy giá trị từ STATUS_MAP, không có thì name lấy $currentStatusKey class lấy secondary
        $statusText = $statusInfo['name'];
        $badgeColor = $statusInfo['class'];
        $orderItems = $this->orderItems->getOrderItemsByOrderId($orderId);
        
        include __DIR__ . '/../../view/admin/orderDetail.php';
    }

    // Thêm hàm này vào class OrderAdminController
    public function confirmManualPayment(): void {
        $this->adminCheck();

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['order_id'])) {
            $orderId = (int)$_POST['order_id'];
            
            // Dùng $this->orderModel để gọi hàm trong Order.php
            if ($this->orderModel->confirmManualPayment($orderId)) {
                $_SESSION['success_message'] = "Đã xác nhận thanh toán thành công cho đơn hàng #$orderId";
            } else {
                $_SESSION['error_message'] = "Cập nhật thất bại! Có thể đơn hàng #$orderId đã được xác nhận trước đó.";
            }
        } else {
            $_SESSION['error_message'] = "Yêu cầu không hợp lệ.";
        }

        // BẮT BUỘC: TRẢ VỀ ĐÚNG TRANG DANH SÁCH ĐƠN HÀNG
        header("Location: /php-pj/admin/orders");
        exit;
    }
}