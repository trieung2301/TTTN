<?php
require_once __DIR__ . "/../../model/Product.php";
require_once __DIR__ . "/../../model/Category.php";
class HomeController
{
    private Product $product;
    private Category $category;
    public function __construct(Product $product, Category $category) //khởi tạo truyền model vào 
    {
        $this->product = $product;
        $this->category= $category;
    }

    public function home(): void
    {
        $products = $this->product->getAllProducts(); //lấy ra tất cả sp
        $categories = $this->category->getAllCategories(); //lấy ra name category
        $cate_id = $_GET['category'] ?? 'all'; //lấy ra input category để search

        if ($cate_id === 'all') {//trả all
            $products = $this->product->getAllProducts();
        } else { //trả theo id
            $products = $this->product->getProductsByCategory((int)$cate_id);
        }

        $keyword = trim($_GET['keyword'] ?? ''); //lấy ra từ search xóa khoảng trống 
        if($keyword !== ''){ //kiểm tra có mới search
            $products = $this->product->searchProduct($keyword);
        }
        
        include __DIR__ . '/../../view/site/home.php';
    }
}
