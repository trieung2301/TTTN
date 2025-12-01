<?php 
include __DIR__ . "/components/header.php";
include __DIR__ . "/components/navbar.php";
include __DIR__ . "/components/sidebar.php";

$isEdit = $product !== null;
$pageTitle = $isEdit ? "Chỉnh Sửa Sản Phẩm: " . htmlspecialchars($product['name']) : "Thêm Sản Phẩm Mới";

function getProductValue($product, $key, $default = '') {
    return htmlspecialchars($product[$key] ?? $default);
}
?>

<div class="main-content">
    <h1 class="mb-4 text-dark">
        <i class="fa-solid fa-box-open"></i> 
        <?php echo $isEdit ? "Sửa Sản Phẩm" : "Thêm Sản Phẩm"; ?>
    </h1> 

    <?php 
    if (isset($_SESSION['error_message'])): ?>
        <div class="alert alert-danger"><?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?></div>
    <?php endif; 
    
    if (isset($_SESSION['success_message'])): ?>
        <div class="alert alert-success"><?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?></div>
    <?php endif; 
    ?>

    <form action="/php-pj/<?php echo $actionUrl; ?>" method="POST" enctype="multipart/form-data">
        
        <?php if ($isEdit): ?>
            <input type="hidden" name="id" value="<?php echo getProductValue($product, 'id'); ?>">
            <input type="hidden" name="old_image" value="<?php echo getProductValue($product, 'image'); ?>">
        <?php endif; ?>

        <div class="row">
            <div class="col-md-8">
                <div class="mb-3">
                    <label for="name" class="form-label">Tên Sản Phẩm (*)</label>
                    <input type="text" class="form-control" id="name" name="name" required
                            value="<?php echo getProductValue($product, 'name'); ?>">
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label">Mô Tả Chi Tiết</label>
                    <textarea class="form-control" id="description" name="description" rows="6"><?php echo getProductValue($product, 'description'); ?></textarea>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="mb-3">
                    <label for="category_id" class="form-label">Danh Mục (*)</label>
                    <select class="form-select" id="category_id" name="category_id" required>
                        <option value="">-- Chọn Danh Mục --</option>
                        <?php 
                        $selectedCategoryId = $isEdit ? $product['category_id'] : null;
                        foreach ($categories as $category): ?>
                            <option value="<?php echo htmlspecialchars($category['id']); ?>"
                                <?php echo ($selectedCategoryId == $category['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($category['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="price" class="form-label">Giá Bán (*)</label>
                    <input type="number" class="form-control" id="price" name="price" required min="0" step="1000"
                            value="<?php echo getProductValue($product, 'price', 0); ?>">
                </div>

                <div class="mb-3">
                    <label for="stock" class="form-label">Số Lượng Tồn Kho (*)</label>
                    <input type="number" class="form-control" id="stock" name="stock" required min="0" 
                            value="<?php echo getProductValue($product, 'stock', 0); ?>">
                </div>
                
                <div class="mb-3">
                    <label for="image" class="form-label">Hình Ảnh Sản Phẩm</label>
                    
                    <input type="file" class="form-control" id="image" name="image_file" 
                         accept="image/*"> <?php if ($isEdit && $product['image']): ?>
                        <small class="form-text text-muted d-block mt-2">
                            Ảnh hiện tại: 
                            <img src="<?php echo getProductValue($product, 'image'); ?>" alt="Ảnh sản phẩm" style="width: 100px; height: auto; border: 1px solid #ccc; padding: 5px;">
                        </small>
                        <input type="hidden" name="old_image" value="<?php echo getProductValue($product, 'image'); ?>">
                    <?php endif; ?>
                    
                    <small class="form-text text-muted">Chỉ chấp nhận file ảnh (jpg, png, gif).</small>
                </div>
                
                <div class="d-grid gap-2 mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="fa-solid fa-save"></i> <?php echo $isEdit ? "Cập Nhật Sản Phẩm" : "Thêm Sản Phẩm"; ?>
                    </button>
                    <a href="/php-pj/admin/products" class="btn btn-secondary">
                        <i class="fa-solid fa-arrow-left"></i> Quay lại Danh sách
                    </a>
                </div>
            </div>
        </div>
    </form>
</div>