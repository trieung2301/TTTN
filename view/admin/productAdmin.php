<?php 
include __DIR__ . "/components/header.php";
include __DIR__ . "/components/navbar.php";
include __DIR__ . "/components/sidebar.php";

$success_message = $_SESSION['success_message'] ?? '';
$error_message = $_SESSION['error_message'] ?? '';
unset($_SESSION['success_message'], $_SESSION['error_message']);

$searchTerm = $_GET['search'] ?? ''; 
?>

<div class="main-content p-4"> 
    <h1 class="mb-4 text-dark"><i class="fa-solid fa-box-open"></i> Quản Lý Sản Phẩm</h1> 
    
    <?php if ($success_message): ?><div class="alert alert-success"><?= htmlspecialchars($success_message) ?></div><?php endif; ?>
    <?php if ($error_message): ?><div class="alert alert-danger"><?= htmlspecialchars($error_message) ?></div><?php endif; ?>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <form class="d-flex w-50" method="GET" action="index.php">
            <input type="hidden" name="action" value="admin/products"> 
            <input class="form-control me-2" type="search" placeholder="Tìm kiếm sản phẩm theo Tên..." name="search" value="<?= htmlspecialchars($searchTerm) ?>">
            <button class="btn btn-outline-primary" type="submit"><i class="fa-solid fa-search"></i></button>
        </form>
        
        <a href="/php-pj/admin/addProduct" class="btn btn-success">
            <i class="fa-solid fa-plus"></i> Thêm Sản Phẩm
        </a>
    </div>

    <div class="table-responsive">
        <table class="table table-hover table-striped">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Ảnh</th>
                    <th>Tên Sản phẩm</th>
                    <th>Danh mục</th>
                    <th>Giá (VNĐ)</th>
                    <th class="d-flex justify-content-between align-items-center">Tồn kho
                        
                        <span class="d-flex flex-column ms-2">
                            <a href="?sort=stock_asc&search=<?= urldecode($searchTerm) ?>" class="text-white text-decoration-none p-0 lh-1">
                                <i class="fa-solid fa-caret-up"></i>
                            </a>
                            <a href="?sort=stock_desc&search=<?= urldecode($searchTerm) ?>" class="text-white text-decoration-none p-0 lh-1">
                                <i class="fa-solid fa-caret-down"></i>
                            </a>
                        </span>
                    </th>
                    <th>Ngày tạo</th>
                    <th class="text-center">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($products)): ?>
                    <tr>
                        <td colspan="8" class="text-center">Không tìm thấy sản phẩm nào.</td>
                    </tr>
                <?php else: ?>
                    <?php 
                    $modals = []; 
                    foreach ($products as $product): 
                    ?>
                        <tr>
                            <td><?= htmlspecialchars($product['id']) ?></td>
                            <td>
                                <img src="/php-pj/view/image/<?= htmlspecialchars($product['image'] ?? 'placeholder.jpg') ?>" alt="<?= htmlspecialchars($product['name']) ?>" style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px;">
                            </td>
                            <td><?= htmlspecialchars($product['name']) ?></td>
                            <td>
                                <span class="badge bg-info">
                                    <?= htmlspecialchars($categoryMap[$product['category_id']] ?? 'Không rõ') ?>
                                </span>
                            </td>
                            <td><?= number_format($product['price'], 0, ',', '.') ?></td>
                            <td>
                                <?php if ($product['stock'] > 0): ?>
                                    <span class="badge bg-success"><?= htmlspecialchars($product['stock']) ?></span>
                                <?php else: ?>
                                    <span class="badge bg-danger">Hết hàng</span>
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars(date('Y-m-d', strtotime($product['created_at']))) ?></td>
                            
                            <td class="text-center" style="min-width: 140px;">
                                
                                <a href="/php-pj/admin/editProduct&id=<?= $product['id'] ?>" class="btn btn-sm btn-primary" title="Chỉnh sửa">
                                    <i class="fa-solid fa-edit"></i>
                                </a>

                                <button type="button" class="btn btn-sm btn-danger" title="Xóa sản phẩm" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#deleteProductModal-<?= $product['id'] ?>">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                                
                                <?php 
                                $modals[] = [
                                    'id' => $product['id'],
                                    'name' => $product['name'],
                                ];
                                ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php 
foreach ($modals as $data) {
    $productId = $data['id'];
    $productName = $data['name'];
?>
<div class="modal fade" id="deleteProductModal-<?= $productId ?>" tabindex="-1" aria-labelledby="deleteProductModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteProductModalLabel">Xác nhận Xóa Sản phẩm</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Bạn có chắc chắn muốn xóa sản phẩm "<?= htmlspecialchars($productName) ?>" (ID: <?= $productId ?>) không?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <a href="/php-pj/admin/deleteProduct&id=<?= $productId ?>" class="btn btn-danger">Xóa</a>
            </div>
        </div>
    </div>
</div>
<?php
}
?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>