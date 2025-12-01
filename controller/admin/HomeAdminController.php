<?php
require_once __DIR__ . "/../../model/User.php";
require_once __DIR__ . "/../../model/Product.php";
require_once __DIR__ . "/../../model/Order.php";
require_once __DIR__ . "/../../model/OrderItems.php";

class HomeAdminController {
    private User $user;
    private Product $product;
    private Order $order;
    private OrderItems $orderitems;

    public function __construct(User $user, Product $product, Order $order, OrderItems $orderitems) 
    {
        $this->user = $user;
        $this->product = $product;
        $this->order = $order;
        $this->orderitems = $orderitems;
    }

    private function adminCheck(): void {
        if (!isset($_SESSION['user']) || ($_SESSION['user']['role'] ?? 'user') !== 'admin') { 
            header("Location: /php-pj/login");
            exit;
        }
    }

    public function homeAdmin() {
        $this->adminCheck();
        
        try {
            $totalUsers = $this->user->countAll(); 
        } catch (Exception $e) {
            $totalUsers = 0; 
            error_log("Tải số lượng người dùng thất bại: " . $e->getMessage());
        }
        
        try {
            $totalProducts = $this->product->countAll();
        } catch (Exception $e) {
            $totalProducts = 0;
            error_log("Tải số lượng sản phẩm thất bại: " . $e->getMessage());
        }
        
        try {
            $totalOrders = $this->order->countAll();
            
            $pendingOrders = $this->order->countByStatus('Chờ xác nhận'); 
            $shippingOrders = $this->order->countByStatus('Đang giao'); 
            
        } catch (Exception $e) {
            $totalOrders = $pendingOrders = $shippingOrders = 0;
            error_log("Tải số lượng đơn hàng thất bại: " . $e->getMessage());
        }
        
        try {
            $revenueToday = $this->order->getTotalRevenueDay() ?? 0;
            $revenueMonth = $this->order->getTotalRevenueMonth() ?? 0;
            $revenueYear = $this->order->getTotalRevenueYear() ?? 0;
            $revenueAll = $this->order->getTotalRevenueAll() ?? 0;
        } catch (Exception $e) {
            $revenueToday = $revenueMonth = $revenueYear = $revenueAll = 0;
            error_log("Tải dữ liệu doanh thu thất bại: " . $e->getMessage());
        }

        try {
            $bestCustomers = $this->order->getBestCustomers();
        } catch (Exception $e) {
            $bestCustomers = [];
            error_log("Tải dữ liệu khách hàng VIP thất bại: " . $e->getMessage());
        }

        try {
            $quantity = $this->orderitems->getQuantity();
        } catch (Exception $e) {
            $quantity = [];
            error_log("Tải dữ liệu đánh giá sản phẩm thất bại: " . $e->getMessage());
        }

        include __DIR__ . "/../../view/admin/homeAdmin.php";
    }
}