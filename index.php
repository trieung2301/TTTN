<?php
session_start();

require_once __DIR__ . "/model/database.php";
require_once __DIR__ . "/model/User.php";
require_once __DIR__ . "/model/Product.php";
require_once __DIR__ . "/model/Category.php";
require_once __DIR__ . "/model/Rating.php";
require_once __DIR__ . "/model/Order.php";
require_once __DIR__ . "/model/OrderItems.php";
require_once __DIR__ . "/model/Coupon.php";
require_once __DIR__ . "/model/CouponUsage.php";
require_once __DIR__ . "/model/Comments.php";
require_once __DIR__ . "/model/Cart.php";

require_once __DIR__ . "/controller/site/ProductController.php";
require_once __DIR__ . "/controller/site/AuthController.php";
require_once __DIR__ . "/controller/site/HomeController.php";
require_once __DIR__ . "/controller/site/CategoryController.php";
require_once __DIR__ . "/controller/site/CartController.php";
require_once __DIR__ . "/controller/site/OrderController.php";
require_once __DIR__ . "/controller/site/CheckoutController.php";
require_once __DIR__ . "/controller/site/EditProfileController.php";

require_once __DIR__ . "/controller/admin/HomeAdminController.php";
require_once __DIR__ . "/controller/admin/ProductAdminController.php";
require_once __DIR__ . "/controller/admin/UserAdminController.php";
require_once __DIR__ . "/controller/admin/StaffAdminController.php";
require_once __DIR__ . "/controller/admin/OrderAdminController.php";
require_once __DIR__ . "/controller/admin/DiscountAdminController.php";

$pdo = Database::getConnection();

$userModel = new User($pdo);
$productsModel = new Product($pdo);
$categoryModel = new Category($pdo);
$ratingModel = new Rating($pdo);
$orderModel = new Order($pdo);
$orderItemsModel = new OrderItems($pdo);
$couponModel = new Coupon($pdo);
$couponUsageModel = new CouponUsage($pdo);
$commentsModel = new Comments($pdo);
$cartModel = new Cart($pdo);

$authController = new AuthController($userModel);
$homeController = new HomeController($productsModel,$categoryModel);
$productController = new ProductController($productsModel,$ratingModel,$commentsModel);
$categoryController = new CategoryController($categoryModel);
$cartController = new CartController($cartModel,$productsModel);
$orderController = new OrderController($orderModel,$productsModel,$orderItemsModel);
$checkoutController = new CheckoutController($cartModel,$orderModel,$productsModel,$orderItemsModel,$couponModel,$couponUsageModel);
$editProfileController = new EditProfileController($userModel);

$homeAdminController = new HomeAdminController($userModel, $productsModel, $orderModel,$orderItemsModel);
$productAdminController = new ProductAdminController($productsModel, $categoryModel);
$userAdminController = new UserAdminController($userModel);
$staffAdminController = new StaffAdminController($userModel);
$orderAdminController = new OrderAdminController($orderModel, $userModel, $orderItemsModel);
$discountAdminController = new DiscountAdminController($couponModel);

$url = isset($_GET['url']) ? trim($_GET['url'], '/') : '';
$url = filter_var($url, FILTER_SANITIZE_URL);
$segments = $url === '' ? [] : explode('/', $url);
$main = implode('/', $segments);

switch ($main) {
    case 'login':
        $authController->login(); break;
    case 'home':
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id'])) {
            $cartController->add();
        }
        $homeController->home();
        break;
    case 'logout':
        $authController->logout(); break;
    case 'register':
        $authController->register(); break;
    case 'momo_complete':
        $checkoutController->completeMomoPayment(); break;
    case 'getProducts':
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id'])) {
            $cartController->add();
            $redirect = '/php-pj/getProducts?url=getProducts';
            if (!empty($_POST['category']) && $_POST['category'] !== 'all') $redirect .= '&category=' . $_POST['category'];
            if (!empty($_POST['sort'])) $redirect .= '&sort=' . $_POST['sort'];
            header("Location: $redirect"); exit;
        }
        $categories = $categoryController->getAll();
        $products   = $productController->getAll();
        include __DIR__ . '/view/site/products.php';
        break;
    case 'productDetails':
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id'])) {
            $cartController->add();
        }
        $productController->productDetails(); break;
    case 'cart':
        $cartController->index(); break;
    case 'addCart':
        $cartController->add(); break;
    case 'buyNow':
        $cartController->buyNow(); break;
    case 'updateCart':
        $cartController->update(); break;
    case 'deleteCart':
        $cartController->delete(); break;
    case 'order':
        $orderController->index(); break;
    case 'checkout':
        $checkoutController->index(); break;
    case 'checkout/vnpay_return':
        $checkoutController->vnpay_return(); break;
    case 'success':
        include __DIR__ . '/view/site/success.php'; break;
    case 'editProfile':
        $editProfileController->editProfile(); break;

    case 'homeAdmin':
        $homeAdminController->homeAdmin(); break;
    case 'admin/users':
        $userAdminController->getUsers(); break;
    case 'admin/users/edit':
        $userAdminController->editUser(); break;
    case 'admin/users/update':
        $userAdminController->updateUser(); break;
    case 'admin/users/add':
        $userAdminController->createUser(); break;
    case 'admin/users/addUser':
        $userAdminController->addUser(); break;
    case 'admin/users/toggleStatus':
        $userAdminController->toggleStatus(); break;
    case 'admin/users/changePassword':
        $userAdminController->changePassword(); break;
    case 'admin/users/delete':
        $userAdminController->deleteUser(); break;

    case 'admin/staff':
        $staffAdminController->getStaff(); break;
    case 'admin/staff/add':
        $staffAdminController->createStaff(); break;
    case 'admin/staff/addStaff':
        $staffAdminController->addStaff(); break;
    case 'admin/staff/edit':
        $staffAdminController->editStaff(); break;
    case 'admin/staff/update':
        $staffAdminController->updateStaff(); break;
    case 'admin/staff/toggleStatus':
        $staffAdminController->toggleStaffStatus(); break;
    case 'admin/staff/changePassword':
        $staffAdminController->changeStaffPassword(); break;
    case 'admin/staff/delete':
        $staffAdminController->deleteStaff(); break;

    case 'admin/products':
        $productAdminController->manageProducts(); break;
    case 'admin/addProduct':
        $productAdminController->addProductForm(); break;
    case 'admin/addProduct/submit':
        $productAdminController->addProduct(); break;
    case 'admin/deleteProduct':
        $productAdminController->deleteProduct(); break;
    case 'admin/editProduct':
        $productAdminController->editProduct(); break;
    case 'admin/updateProduct':
        $productAdminController->updateProduct(); break;

    case 'admin/orders':
        $orderAdminController->getOrders(); break;
    case 'admin/orders/detail':
        $orderAdminController->viewOrderDetail(); break;
    case 'admin/orders/updateStatus':
        $orderAdminController->updateOrderStatus(); break;
    case 'admin/orders/confirm-manual-payment':
        $orderAdminController->confirmManualPayment(); break;

    case 'admin/discounts':
        $discountAdminController->getCoupon(); break;
    case 'admin/discounts/add':
        $discountAdminController->add(); break;
    case 'admin/discounts/update':
        $discountAdminController->update(); break;
    case 'admin/discounts/toggleStatus':
        $discountAdminController->toggleStatus(); break;

    case 'about':
        include __DIR__ . '/view/site/about.html'; break;
    case 'vnpay_return':
        $checkoutController->vnpay_return();
        exit;
        break;
    default:
        header("Location: /php-pj/home");
        exit;
}