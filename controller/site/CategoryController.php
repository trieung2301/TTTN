<?php
require_once __DIR__ . "/../../model/Category.php";
class CategoryController {
    private Category $categoryModel; //khai báo kiểu dữ liệu object

    public function __construct(Category $categoryModel) {
        $this->categoryModel = $categoryModel; //khởi tạo đối tượng
    }
    public function getAll(){
       return $this->categoryModel->getAllCategories();
    }
   
}