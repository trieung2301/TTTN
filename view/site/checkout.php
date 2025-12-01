<?php include __DIR__ . '/components/header.php'; ?>
  <div class="container my-5">
    <h1 class="mb-4 text-center">Trang Đặt Hàng</h1>
      <?php if (!empty($error)): ?>
        <div class="alert alert-danger text-center">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>
    <form method="POST" action=""> 
      <div class="mb-3">
        <label for="fullname" class="form-label">Họ và Tên</label>
        <input type="text" class="form-control" id="fullname" name="fullname" placeholder="Nhập họ và tên" required>
      </div>

      <div class="mb-3">
        <label for="phone" class="form-label">Số điện thoại</label>
        <input type="text" class="form-control" id="phone" name="phone" placeholder="Nhập số điện thoại" required>
      </div>

      <div class="mb-3">
        <label for="email" class="form-label">Email</label>
        <input type="email" class="form-control" id="email" name="email" placeholder="Nhập email" required>
      </div>

      <div class="mb-3">
        <label for="address" class="form-label">Địa chỉ</label>
        <input type="text" class="form-control" id="address" name="address" placeholder="Số nhà, đường...
        " required>
      </div>

      <div class="row">
        <div class="col-md-4 mb-3">
          <label for="district" class="form-label">Quận / Huyện</label>
          <input type="text" class="form-control" id="district" name="district" placeholder="VD: Quận 1" required>
        </div>
        <div class="col-md-4 mb-3">
          <label for="city" class="form-label">Thành phố</label>
          <input type="text" class="form-control" id="city" name="city" placeholder="VD: Hồ Chí Minh" required>
        </div>
        <div class="col-md-4 mb-3">
          <label for="postcode" class="form-label">Mã bưu điện</label>
          <input type="text" class="form-control" id="postcode" name="postcode" placeholder="VD: 700000">
        </div>
      </div>

      <div class="mb-3">
        <label for="note" class="form-label">Ghi chú</label>
        <textarea class="form-control" id="note" name="note" rows="3" placeholder="Ghi chú thêm (tuỳ chọn)"></textarea>
      </div>

      <div class="mb-4">
        <label class="form-label fw-bold">Phương thức thanh toán</label>
        <div class="border p-3 rounded">
            <div class="form-check mb-2">
                <input class="form-check-input" type="radio" name="payment_method" id="cod" value="COD" checked>
                <label class="form-check-label" for="cod">
                    Thanh toán khi nhận hàng (COD)
                </label>
            </div>
            <div class="form-check mb-2">
                <input class="form-check-input" type="radio" name="payment_method" id="momo" value="MoMo">
                <label class="form-check-label text-danger fw-bold" for="momo">
                    Thanh toán qua MoMo
                </label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="payment_method" id="vnpay" value="VNPay">
                <label class="form-check-label text-primary fw-bold" for="vnpay">
                    Thanh toán qua VNPay
                </label>
            </div>
        </div>
    </div>

    <div id="vnpay_options" style="display: none; border: 1px solid #ccc; padding: 15px; border-radius: 5px; margin-bottom: 15px;">
        <h4 class="mb-3">Tùy chọn VNPay</h4>
        <div class="mb-3">
            <label for="bankcode" class="form-label">Chọn Ngân hàng</label>
            <select class="form-select" id="bankcode" name="bankcode">
                <option value="">Cổng thanh toán VNPay</option>
                <option value="NCB">NCB</option>
                <option value="AGRIBANK">AGRIBANK</option>
                <option value="VIETINBANK">VIETINBANK</option>
                </select>
        </div>
        <div class="mb-3">
            <label for="language" class="form-label">Ngôn ngữ</label>
            <select class="form-select" id="language" name="language">
                <option value="vn">Tiếng Việt</option>
                <option value="en">Tiếng Anh</option>
            </select>
        </div>
    </div>
    <div class="mb-3">
        <label for="coupon" class="form-label">Mã giảm giá</label>
        <input type="text" class="form-control" id="coupon" name="coupon" placeholder="Nhập mã coupon" value="<?= isset($_POST['coupon']) ? htmlspecialchars($_POST['coupon']) : '' ?>">
      </div>

      <div class="mb-3">
        <label for="total_display" class="form-label">Tổng tiền</label>
        <input type="text" class="form-control" id="total_display" value="<?= number_format($total,0,',','.') ?> VND" readonly>
        <input type="hidden" name="total" value="<?= $total ?>">
      </div>

      <button type="submit" id="submit_btn" class="btn btn-primary w-100">Đặt hàng</button>
        </form>
      </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      const form = document.querySelector('form');
      const paymentMethods = document.querySelectorAll('input[name="payment_method"]');
      const vnpayOptionsDiv = document.getElementById('vnpay_options');
      const submitButton = document.getElementById('submit_btn'); // Đã thêm ID vào nút submit

      function updateFormAction() {
        // Lấy phương thức thanh toán được chọn
        const selectedMethod = document.querySelector('input[name="payment_method"]:checked').value;

        if (selectedMethod === 'VNPay') {
          // THAY ĐỔI ACTION FORM để gọi đến hàm vnpay_payment trong Controller
          form.action = '/php-pj/checkout/vnpay'; 
          vnpayOptionsDiv.style.display = 'block';
          submitButton.textContent = 'Thanh toán VNPay';
          submitButton.classList.remove('btn-primary');
          submitButton.classList.add('btn-danger'); 
        } else {
          // Các phương thức khác gửi về action mặc định của checkout
          form.action = '';
          vnpayOptionsDiv.style.display = 'none';
          submitButton.textContent = 'Đặt hàng';
          submitButton.classList.remove('btn-danger');
          submitButton.classList.add('btn-primary');
        }
      }

      paymentMethods.forEach(radio => {
        radio.addEventListener('change', updateFormAction);
      });

      // Thiết lập trạng thái ban đầu
      updateFormAction();
    });
  </script>
  </body>
  </html>