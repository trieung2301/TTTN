<?php
require_once __DIR__ . "/../../model/Cart.php";
require_once __DIR__ . "/../../model/Product.php";
class CartController {
    private Cart $cartModel;
    private Product $productsModel;
    public function __construct(Cart $cartModel,Product $productsModel) {
        $this->cartModel = $cartModel;
        $this->productsModel= $productsModel;
    }
    public function index() {
        if (!isset($_SESSION['user'])) {
            $_SESSION['ERROR'] = 'Bạn cần đăng nhập để xem giỏ hàng!';
            header('Location: /php-pj/login');
            exit;
        }
        $user_id = $_SESSION['user']['id'];
        $cartItems = $this->cartModel->getCartItems($user_id);
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id'])) {
            $product_id = $_POST['product_id'];
            $this->cartModel->deleteFromCart($user_id,$product_id);
            header('Location: /php-pj/cart');
        }
        include __DIR__ . '/../../view/site/cart.php';
    }

    public function add() {
        if (!isset($_SESSION['user'])) {
            $_SESSION['ERROR'] = 'Bạn cần đăng nhập để thêm sản phẩm!';
            header('Location: /php-pj/login');
            exit;
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id'])) {
            $user_id    = $_SESSION['user']['id'];
            $product_id = (int)$_POST['product_id'];
            $quantity   = (int)($_POST['quantity']);

            $stockProduct= $this->productsModel->checkQuantityProducts($product_id);//check số lg trong sp
            $stockCart= $this->productsModel->checkQuantityCart($user_id, $product_id);//check số lg trong giỏ hàng
            $newStock= $stockCart + $quantity; // số lg mới bằng số đã có trong giỏ hàng + với số vừa thêm vào
            if($newStock > $stockProduct){ // nếu thêm vào vượt quá thì redirect thoát luôn
                header('Location: /php-pj/cart');
                exit;
            }
            $this->cartModel->addToCart($user_id, $product_id, $quantity);
            header('Location: /php-pj/home');
            return;;
        }
    }

    public function buyNow() {
        if (!isset($_SESSION['user'])) {
            $_SESSION['ERROR'] = 'Bạn cần đăng nhập để thực hiện thanh toán!';
            header('Location: /php-pj/login');
            exit;
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id'])) {
            $user_id    = $_SESSION['user']['id'];
            $product_id = (int)$_POST['product_id'];
            $quantity   = (int)($_POST['quantity']);

            // 1. Kiểm tra số lượng tồn kho (Giống logic Add to Cart)
            $stockProduct= $this->productsModel->checkQuantityProducts($product_id);
            $stockCart= $this->productsModel->checkQuantityCart($user_id, $product_id);
            $newStock= $stockCart + $quantity;
            
            if($newStock > $stockProduct){ 
                // Nếu vượt quá tồn kho, chuyển hướng về trang giỏ hàng (hoặc trang chi tiết sản phẩm với thông báo lỗi)
                header('Location: /php-pj/cart');
                exit;
            }
            
            // 2. Thêm sản phẩm vào giỏ hàng
            $this->cartModel->addToCart($user_id, $product_id, $quantity);
            
            // 3. CHUYỂN HƯỚNG NGAY LẬP TỨC ĐẾN TRANG THANH TOÁN
            header('Location: /php-pj/checkout');
            exit;
        }
        
        // Trường hợp truy cập trực tiếp mà không phải POST, chuyển hướng về trang chủ
        header('Location: /php-pj/home');
        exit;
    }
}
?>
