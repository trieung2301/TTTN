<?php 
include __DIR__ . "/components/header.php";
include __DIR__ . "/components/navbar.php";
include __DIR__ . "/components/sidebar.php";

function formatCurrency($amount) {
    return number_format($amount, 0, '', ' ') . ' VNĐ';
}
?>
<body>
<div class="main-content">
    <h1 class="mb-4 text-dark"><i class="fa-solid fa-tachometer-alt"></i> Tổng quan</h1> 

    <div class="row g-4 mb-5">
        
        <div class="col-md-3">
            <div class="card bg-primary text-white shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title">Tổng người dùng</h5>
                            <h3 style="font-size: 1.8rem;"><?php echo htmlspecialchars($totalUsers ?? 0); ?></h3>
                        </div>
                        <i class="fa-solid fa-users fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card bg-success text-white shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title">Tổng sản phẩm</h5>
                            <h3 style="font-size: 1.8rem;"><?php echo htmlspecialchars($totalProducts ?? 0); ?></h3>
                        </div>
                        <i class="fa-solid fa-box fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card bg-warning text-dark shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title">Tổng đơn hàng</h5>
                            <h3 style="font-size: 1.8rem;"><?php echo htmlspecialchars($totalOrders ?? 0);?></h3>
                        </div>
                        <i class="fa-solid fa-shopping-cart fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card bg-info text-white shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title">Doanh thu Hôm nay</h5> 
                            <h3 class="text-nowrap" style="font-size: 1.8rem;"><?php echo formatCurrency($revenueToday); ?></h3> 
                        </div>
                        <i class="fa-solid fa-money-bill-wave fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <h1 class="mb-4 text-dark"><i class="fa-solid fa-chart-bar"></i> Thống kê</h1>

    <div class="row g-4 mb-5">
        <div class="col-md-6">
            
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0"><i class="fa-solid fa-chart-line"></i> Phân tích Doanh thu</h5>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Doanh thu Tháng này:
                            <span class="badge bg-success fs-6"><?php echo formatCurrency($revenueMonth); ?></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Doanh thu Năm nay:
                            <span class="badge bg-success fs-6"><?php echo formatCurrency($revenueYear); ?></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Tổng Doanh thu:
                            <span class="badge bg-success fs-6"><?php echo formatCurrency($revenueAll); ?></span>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0"><i class="fa-solid fa-list-check"></i> Trạng thái Đơn hàng</h5>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Đơn Chờ xác nhận:
                            <span class="badge bg-warning text-dark fs-6"><?php echo htmlspecialchars($pendingOrders); ?></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Đơn Đang giao:
                            <span class="badge bg-info fs-6"><?php echo htmlspecialchars($shippingOrders); ?></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Tổng số Đơn hàng:
                            <span class="badge bg-primary fs-6"><?php echo htmlspecialchars($totalOrders); ?></span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0"><i class="fa-solid fa-crown"></i> Top 10 Khách hàng VIP</h5>
                </div>
                <div class="card-body p-0">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Username</th>
                                <th>Tên đầy đủ</th>
                                <th>Số đơn đã hoàn thành</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($bestCustomers)): ?>
                                <tr><td colspan="4" class="text-center">Chưa có dữ liệu khách hàng.</td></tr>
                            <?php else: ?>
                                <?php $rank = 1; foreach ($bestCustomers as $customer): ?>
                                <tr>
                                    <td><?= $rank++ ?></td>
                                    <td><?= htmlspecialchars($customer['username']) ?></td>
                                    <td><?= htmlspecialchars($customer['fullname'] ?? 'N/A') ?></td>
                                    <td><span class="badge bg-success"><?= htmlspecialchars($customer['total_orders']) ?></span></td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>


        <div class="row g-4 mb-5">
            <div class="col-md-6">
                
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-dark text-white">
                        <h5 class="mb-0"><i class="fa-solid fa-trophy"></i> Sản phẩm bán chạy </h5>
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>ID</th>
                                    <th>Tên sản phẩm</th>
                                    <th>Số sản phẩm đã bán</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($quantity)): ?>
                                    <tr><td colspan="10" class="text-center">Chưa có dữ liệu sản phẩm.</td></tr>
                                <?php else: ?>
                                <?php $rank = 1; foreach ($quantity as $q): ?>
                                <tr>
                                    <td><?= $rank++ ?></td>
                                    <td><?= htmlspecialchars($q['product_id']) ?></td>
                                    <td><?= htmlspecialchars($q['name']) ?></td>
                                    <td><?= htmlspecialchars($q['quantity'] ) ?></td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>



    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>