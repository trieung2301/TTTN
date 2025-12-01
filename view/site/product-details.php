<?php include __DIR__ . '/components/header.php'; ?>

<style>
  body {
    background: #f8fafc;
  }
  .product-img {
    border-radius: 16px;
    box-shadow: 0 10px 25px rgba(0,0,0,0.1);
    transition: transform 0.3s ease;
    width: 100%; /* Đảm bảo ảnh chiếm toàn bộ chiều rộng phần tử chứa */
    max-width: 500px; /* Giới hạn chiều rộng ảnh */
    height: auto; /* Giữ tỷ lệ chiều cao tự động theo chiều rộng */
    max-height: 400px; /* Giới hạn chiều cao để ảnh không phóng to quá */
    object-fit: contain; /* Giữ ảnh trong khuôn mà không bị cắt bớt */
  }

  .product-img:hover {
    transform: scale(1.03);
  }

  h1 {
    font-weight: 700;
    color: #222;
  }

  .price-tag {
    font-size: 1.8rem;
    font-weight: 700;
    background: linear-gradient(to right, #4facfe, #00c6ff);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
  }

  /* Nút chính */
  .btn-primary {
    background: linear-gradient(to right, #4facfe, #00c6ff);
    border: none;
    font-weight: 600;
    padding: 0.6rem 1.2rem;
    border-radius: 10px;
    transition: all 0.3s ease;
  }
  .btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0,198,255,0.4);
  }

  /* Nút phụ (Mua Ngay) */
  .btn-outline-primary {
    border: 2px solid #4facfe;
    color: #4facfe;
    background: none;
    font-weight: 600;
    padding: 0.6rem 1.2rem;
    border-radius: 10px;
    transition: all 0.3s ease;
  }
  .btn-outline-primary:hover {
    background: #4facfe;
    color: white;
  }
  
  /* CSS cho Đánh giá sao */
  .rating-area {
    display: flex;
    font-size: 1.6rem;
    color: #ccc;
    cursor: pointer;
  }
  .star {
    margin-right: 5px;
    transition: color 0.2s, transform 0.2s;
  }
  .star:hover {
    transform: scale(1.1);
  }
  .star-filled {
    color: #ffc107;
  }

  /* Thiết kế lại nút mua và thêm giỏ hàng */
  .btn-buy-card { 
    background: #4facfe; 
    color: white; 
    border-radius: 8px; 
    font-weight: 600;
    height: 48px;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 0 1.2rem;
  }
  .btn-buy-card:hover { 
    background: #c82333; 
  }
  .btn-add-cart-icon-card { 
    background: #28a745; 
    color: white; 
    width: 48px; 
    height: 48px; 
    border-radius: 8px; 
    display: flex; 
    align-items: center; 
    justify-content: center; 
  }
  .btn-add-cart-icon-card:hover { 
    background: #1e7e34; 
  }
</style>

<div class="container py-5">

  <div class="row align-items-start g-4">
    <div class="col-md-6">
      <img src="/php-pj/view/image/<?= htmlspecialchars($product['image']) ?>"
           class="img-fluid product-img"
           alt="<?= htmlspecialchars($product['name']) ?>">
    </div>

    <div class="col-md-6">
      <h1 class="mb-3"><?= htmlspecialchars($product['name']) ?></h1>
      <p class="price-tag mb-3">
        <?= number_format($product['price'], 0, ',', '.') ?> VND
      </p>

      <p><?= nl2br(htmlspecialchars($product['description'])) ?></p>
      <p class="fw-bold text-secondary">Số lượng còn: <?= $product['stock'] ?></p>

      <div class="d-flex gap-2 mt-4">
        <form method="POST" action="/php-pj/buyNow">
            <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
            <input type="hidden" name="quantity" value="1">
            <button type="submit" class="btn btn-buy-card">Mua Ngay</button>
        </form>

        <form method="POST" action="/php-pj/addCart">
            <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
            <input type="hidden" name="quantity" value="1">
            <button type="submit" class="btn btn-add-cart-icon-card">
                <i class="fa-solid fa-cart-shopping"></i>
            </button>
        </form>
      </div>
      </div>
  </div>

  <div class="mt-5">
    <h3 class="mb-4">⭐ Đánh giá sản phẩm</h3>

    <?php if (!$isRating): ?>
      <form method="post" action="/php-pj/productDetails&id=<?= $product['id'] ?>" class="mb-4" id="ratingForm">
        <div class="mb-3">
          <label class="form-label d-block mb-2">Chọn số sao:</label>
          
          <div class="rating-area" id="ratingArea">
            <i class="fa-solid fa-star star" data-rating="1"></i>
            <i class="fa-solid fa-star star" data-rating="2"></i>
            <i class="fa-solid fa-star star" data-rating="3"></i>
            <i class="fa-solid fa-star star" data-rating="4"></i>
            <i class="fa-solid fa-star star" data-rating="5"></i>
          </div>
          <input type="hidden" name="rating" id="selectedRating" value="0" required>
        </div>
        <button type="submit" class="btn btn-primary">Gửi đánh giá</button>
      </form>
    <?php else: ?>
      <p class="text-success fw-bold">✅ Bạn đã đánh giá sản phẩm này rồi.</p>
    <?php endif; ?>

    <?php if (isset($averageRating)): ?>
      <div class="mb-4">
        <strong>Đánh giá trung bình: </strong>
        <span class="text-warning">
          <?= str_repeat('★', floor($averageRating)) ?>
          <?= str_repeat('☆', 5 - floor($averageRating)) ?>
        </span>
        (<?= number_format($averageRating, 1) ?>/5)
      </div>
    <?php endif; ?>
  </div>

  <div class="mt-5">
    <h3 class="mb-4">💬 Bình luận</h3>

    <form method="post" action="/php-pj/productDetails&id=<?= $product['id'] ?>" class="mb-4">
      <div class="mb-3">
        <textarea name="comment_text" rows="3" class="form-control" placeholder="Nhập bình luận..." required></textarea>
      </div>
      <button type="submit" class="btn btn-secondary">Gửi bình luận</button>
    </form>

    <?php if (!empty($comments)): ?>
      <?php foreach ($comments as $cmt): ?>
        <div class="comment-box mb-3">
          <div class="d-flex justify-content-between">
            <strong><?= htmlspecialchars($cmt['username']) ?></strong>
            <small class="text-muted"><?= htmlspecialchars($cmt['created_at']) ?></small>
          </div>
          <p class="mb-0 mt-2"><?= nl2br(htmlspecialchars($cmt['comment_text'])) ?></p>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <p>Chưa có bình luận nào cho sản phẩm này.</p>
    <?php endif; ?>
  </div>

</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        
        // --- Logic Xử lý 5 Sao ---
        const ratingArea = document.getElementById('ratingArea');
        const stars = ratingArea ? ratingArea.querySelectorAll('.star') : [];
        const selectedRatingInput = document.getElementById('selectedRating');
        let currentRating = 0; // Giá trị rating đã chọn

        function highlightStars(rating) {
            stars.forEach(star => {
                const starRating = parseInt(star.getAttribute('data-rating'));
                if (starRating <= rating) {
                    star.classList.add('star-filled');
                } else {
                    star.classList.remove('star-filled');
                }
            });
        }

        if (ratingArea) {
            stars.forEach(star => {
                star.addEventListener('mouseover', function() {
                    const ratingValue = parseInt(this.getAttribute('data-rating'));
                    highlightStars(ratingValue);
                });

                star.addEventListener('click', function() {
                    currentRating = parseInt(this.getAttribute('data-rating'));
                    selectedRatingInput.value = currentRating;
                    highlightStars(currentRating);
                });
            });

            ratingArea.addEventListener('mouseout', function() {
                highlightStars(currentRating);
            });
        }
    });
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<?php include __DIR__ . '/components/footer.php'; ?>