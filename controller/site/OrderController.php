<?php
require_once __DIR__ . "/../../model/Order.php";
require_once __DIR__ . "/../../model/Product.php";
require_once __DIR__ . "/../../model/OrderItems.php";
require_once __DIR__ . "/../../model/Coupon.php";
require_once __DIR__ . "/../../model/CouponUsage.php";
class OrderController {
    private Order $orderModel;
    private Product $productModel;
    private OrderItems $orderItemsModel;

    public function __construct(Order $orderModel, Product $productModel, OrderItems $orderItemsModel) {
        $this->orderModel = $orderModel; 
        $this->productModel = $productModel;
        $this->orderItemsModel = $orderItemsModel;
    }
    public function index() {
        if (!isset($_SESSION['user'])) {
            $_SESSION['ERROR'] = 'Bạn cần đăng nhập để thực hiện hành động này!';
            header('Location: /php-pj/login');
            exit;
        }
        $user_id= $_SESSION['user']['id'];
        $completedOrders=$this->orderModel->purchaseHistory($user_id);
        $pendingOrders=$this->orderModel->pendingOrder($user_id);
        $cancelledOrders=$this->orderModel->getCancelledOrders($user_id);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $order_id = $_POST['order_id'];
            $user_id  = $_SESSION['user']['id'];
            $cancelOrders = $this->orderModel->cancelOrder($order_id,$user_id);
            $item= $this->orderItemsModel->getOrderItemsByOrderId($order_id);
            foreach ($item as $item) {
                $product_id = $item['product_id'];
                $quantity   = $item['quantity'];
                $this->productModel->increaseStock($product_id, $quantity);
            }
            header("Location: /php-pj/order");
            exit;
        }

        include __DIR__ . "/../../view/site/order.php";
    }
}