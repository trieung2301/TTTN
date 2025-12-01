<?php
require_once __DIR__ . "/../../model/Product.php";
require_once __DIR__ . "/../../model/Rating.php";
require_once __DIR__ . "/../../model/Comments.php";
require_once __DIR__ . "/../../controller/site/CartController.php";
class ProductController {
    private Product $productModel;
    private Rating $ratingModel;
    private Comments $commentsModel;

    public function __construct(Product $productModel, Rating $ratingModel, Comments $commentsModel) {
        $this->productModel = $productModel; // Khởi tạo model Product được truyền từ index $productsModel =new Product($pdo); $productController = new ProductController($productsModel);
        $this->ratingModel = $ratingModel;
        $this->commentsModel = $commentsModel;
    }
    public function getAll(){
        $categoryId = $_GET['category'] ?? 'all';
        $sort       = $_GET['sort'] ?? '';
        $search     = trim($_GET['search'] ?? '');

        return $this->productModel->getFilteredProducts($categoryId, $sort, $search);
    }

    public function add() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Xử lý dữ liệu từ form
            $data = [
                'name'        => $_POST['name'],
                'slug'        => $_POST['slug'],
                'description' => $_POST['description'],
                'image'       => $_FILES['image']['name'], // Xử lý ảnh
                'price'       => $_POST['price'],
                'stock'       => $_POST['stock'],
                'category_id' => $_POST['category_id'],
            ];

            // Di chuyển ảnh tải lên
            move_uploaded_file($_FILES['image']['tmp_name'], 'uploads/images/' . $_FILES['image']['name']);

            // Tạo sản phẩm mới
            $this->productModel->createProduct($data);

            // Chuyển hướng về danh sách sản phẩm
            header('Location: /php-pj/home');
        }
    }

    // Cập nhật sản phẩm
    public function update($id) {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $data = [
                'name'        => $_POST['name'],
                'slug'        => $_POST['slug'],
                'description' => $_POST['description'],
                'image'       => $_FILES['image']['name'], // Xử lý ảnh
                'price'       => $_POST['price'],
                'stock'       => $_POST['stock'],
                'category_id' => $_POST['category_id'],
            ];

            // Di chuyển ảnh mới
            move_uploaded_file($_FILES['image']['tmp_name'], 'uploads/images/' . $_FILES['image']['name']);
            // Cập nhật sản phẩm
            $this->productModel->updateProduct($id, $data);

            // Chuyển hướng về danh sách sản phẩm
            header('Location:/php-pj/home');
        }
    }

    // Xóa sản phẩm
    public function delete($id) {
        $this->productModel->deleteProduct($id);
        header('Location: /php-pj/home');
    }
    public function getProductCategory($categoryId):array
    {
        return $this->productModel->getProductsByCategory($categoryId);
    }
    //trang chi tiết sản phẩm
    public function productDetails()
    {
 
        $user_id = $_SESSION['user']['id'] ?? null;
        $id = $_GET['id'] ?? null;// Lấy product_id từ URL
        if (!$id) {
            $_SESSION['ERROR'] = 'Sản phẩm không tồn tại!';
            header('Location: /php-pj/home');
            exit;
        }
        $product = $this->productModel->getProductById($id);
        $comments = $this->commentsModel->getComments($id);
        $averageRating = $this->ratingModel->getAverageRating($id);
        if($user_id != null)
        {
            $isRating = $this->ratingModel->checkRating($id,$user_id);
        }else{
            $isRating = false;
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!isset($_SESSION['user'])) {
                $_SESSION['ERROR'] = 'Bạn cần đăng nhập để thực hiện hành động này!';
                header('Location: /php-pj/login');
                exit;
            }

            if (isset($_POST['comment_text'])) {// kiểm tra request từ comment
                $comment_text = trim($_POST['comment_text']);
                if ($comment_text === '') {
                    $_SESSION['ERROR'] = 'Bình luận không được để trống!';
                } else {
                    $result = $this->commentsModel->addComments($id, $user_id, $comment_text);
                    if ($result) {
                        $_SESSION['SUCCESS'] = 'Thêm bình luận thành công!';
                    } else {
                        $_SESSION['ERROR'] = 'Có lỗi xảy ra khi thêm bình luận!';
                    }
                }

                header("Location: /php-pj/productDetails&id=$id");
                exit;
            }
            if (isset($_POST['rating'])) {// kiểm tra request từ rating
                $rating = (int)$_POST['rating'];

                if ($rating < 1 || $rating > 5) {
                    $_SESSION['ERROR'] = 'Giá trị đánh giá không hợp lệ!';
                } else {
                    $result2 = $this->ratingModel->addRating($id, $user_id, $rating);

                    if ($result2) {
                        $_SESSION['SUCCESS'] = 'Cảm ơn bạn đã đánh giá!';
                    } else {
                        $_SESSION['ERROR'] = 'Có lỗi xảy ra khi gửi đánh giá!';
                    }
                }

                header("Location: /php-pj/productDetails&id=$id");
                exit;
            }
        }
        include __DIR__ . "/../../view/site/product-details.php";
    }

}