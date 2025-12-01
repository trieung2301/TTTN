<?php

class ProductAdminController {
    private $productModel;
    private $categoryModel;

    public function __construct($productModel, $categoryModel) {
        $this->productModel = $productModel;
        $this->categoryModel = $categoryModel;
    }

    private function adminCheck(): void {
        if (!isset($_SESSION['user']) || ($_SESSION['user']['role'] ?? 'user') !== 'admin') { //isset kiểm tra tồn tại
            header("Location: /php-pj/login");
            exit;
        }
    }

    private function handleImageUpload($fileInputName): string {
        if (!isset($_FILES[$fileInputName]) || empty($_FILES[$fileInputName]["name"])) {
             return ''; 
        }
        $targetDir = __DIR__ . "/../../view/image/";
        $fileName = basename($_FILES[$fileInputName]["name"]);
        $targetFilePath = $targetDir . $fileName;

        if (move_uploaded_file($_FILES[$fileInputName]["tmp_name"], $targetFilePath)) {
            return $fileName;
        } else {
            $_SESSION['error_message'] = "Lỗi lưu file hoặc lỗi upload khác.";
            return 'error';
        }
    }
    
    public function manageProducts(): void {
        $this->adminCheck();

        $searchTerm = $_GET['search'] ?? '';
        $sortBy = $_GET['sort'] ?? '';

        if (!empty($searchTerm)) {
            $products = $this->productModel->SearchProduct($searchTerm); 
        }elseif($sortBy === 'stock_asc'){
            $products = $this->productModel->getProductOrderByStock('ASC');
        }elseif($sortBy === 'stock_desc'){
            $products = $this->productModel->getProductOrderByStock('DESC');
        } else {
            $products = $this->productModel->getAllProducts();
        }

        $categories = $this->categoryModel->getAllCategories(); 
        $categoryMap = [];
        foreach ($categories as $cate) {
            $categoryMap[$cate['id']] = $cate['name'];
        }
        
        include __DIR__ . "/../../view/admin/productAdmin.php";
    }

    public function addProductForm(): void {
        $this->adminCheck();
        
        $categories = $this->categoryModel->getAllCategories(); 
        $product = null;
        $actionUrl = 'admin/addProduct/submit';
        
        include __DIR__ . "/../../view/admin/productForm.php";
    }

    public function addProduct(): void {
        $this->adminCheck();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            
            $name = trim($_POST['name'] ?? '');
            $description = trim($_POST['description'] ?? '');
            $price = (float)($_POST['price'] ?? 0);
            $stock = (int)($_POST['stock'] ?? 0);
            $categoryId = (int)($_POST['category_id'] ?? 0);
            
            $uploadedFileName = $this->handleImageUpload('image_file'); 
            
            if ($uploadedFileName === 'error') {
                header("Location: /php-pj/admin/addProduct");
                exit;
            }

            $image = $uploadedFileName ?: 'default.jpg'; 

            $data = [
                'name' => $name, 'description' => $description, 
                'price' => $price, 'stock' => $stock, 'category_id' => $categoryId, 'image' => $image
            ];
            
            if (empty($name) || $price <= 0 || $categoryId <= 0) {
                $_SESSION['error_message'] = "Vui lòng nhập đầy đủ Tên sản phẩm, Giá và chọn Danh mục.";
                header("Location: /php-pj/admin/addProduct");
                exit;
            }

            if ($this->productModel->createProduct($data)) {
                $_SESSION['success_message'] = "Thêm sản phẩm '{$name}' thành công! ✅";
                header("Location: /php-pj/admin/products");
                exit;
            } else {
                $_SESSION['error_message'] = "Lỗi khi thêm sản phẩm vào cơ sở dữ liệu. Vui lòng thử lại.";
            }
        }
        
        header("Location: /php-pj/admin/addProduct"); 
        exit;
    }

    public function editProduct(): void {
        $this->adminCheck();

        $id = (int)($_GET['id'] ?? 0);
        $product = $this->productModel->getProductById($id); 

        if (!$product) {
            $_SESSION['error_message'] = "Không tìm thấy sản phẩm cần chỉnh sửa.";
            header("Location: /php-pj/admin/products");
            exit;
        }

        $categories = $this->categoryModel->getAllCategories(); 
        $actionUrl = 'admin/updateProduct';

        include __DIR__ . "/../../view/admin/productForm.php";
    }

    public function updateProduct(): void {
        $this->adminCheck();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            
            $id = (int)($_POST['id'] ?? 0); 
            $name = trim($_POST['name'] ?? '');
            $description = trim($_POST['description'] ?? '');
            $price = (float)($_POST['price'] ?? 0);
            $stock = (int)($_POST['stock'] ?? 0);
            $categoryId = (int)($_POST['category_id'] ?? 0);
            $oldImage = trim($_POST['old_image'] ?? 'default.jpg'); 

            $uploadedFileName = $this->handleImageUpload('image_file'); 

            if ($uploadedFileName === 'error') {
                header("Location: /php-pj/admin/editProduct&id={$id}");
                exit;
            }

            $image = !empty($uploadedFileName) ? $uploadedFileName : $oldImage; 

            $data = [
                'name' => $name, 'description' => $description, 
                'price' => $price, 'stock' => $stock, 'category_id' => $categoryId, 'image' => $image
            ];

            if ($id <= 0 || empty($name) || $price <= 0 || $categoryId <= 0) {
                $_SESSION['error_message'] = "Dữ liệu cập nhật không hợp lệ. Vui lòng kiểm tra lại.";
                header("Location: /php-pj/admin/editProduct&id={$id}");
                exit;
            }

            if ($this->productModel->updateProduct($id, $data)) {
                $_SESSION['success_message'] = "Cập nhật sản phẩm ID: {$id} thành công! ✅";
            } else {
                $_SESSION['error_message'] = "Lỗi khi cập nhật sản phẩm. Có thể không có thay đổi nào được thực hiện.";
            }
        }
        
        header("Location: /php-pj/admin/products");
        exit;
    }

    public function deleteProduct(): void {
        $this->adminCheck();
        
        $id = (int)($_GET['id'] ?? 0); 

        if ($id <= 0) {
            $_SESSION['error_message'] = "ID sản phẩm không hợp lệ.";
            header("Location: /php-pj/admin/products");
            exit;
        }

        if ($this->productModel->deleteProduct($id)) {
            $_SESSION['success_message'] = "Xóa sản phẩm ID: {$id} thành công! 🗑️";
        } else {
            $_SESSION['error_message'] = "Lỗi khi xóa sản phẩm ID: {$id}. Có thể do sản phẩm này đang liên quan đến các đơn hàng hoặc bình luận.";
        }
        
        header("Location: /php-pj/admin/products");
        exit;
    }


}