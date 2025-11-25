<?php
ob_start();
include_once "includes/header.php";

if (!isset($_SESSION['loggedin'])) {
    header("Location: DangNhap.php");
    exit();
}

$maUser = $_SESSION['MaUser'];
$maDanhGia = $_GET['id'] ?? 0;

if (!$maDanhGia) {
    header("Location: DanhGia.php");
    exit();
}

// Lấy thông tin đánh giá cần sửa
$reviewSQL = "SELECT dg.*, m.TenMonAn, m.HinhAnh 
              FROM danh_gia dg 
              JOIN monan m ON dg.MaMonAn = m.MaMonAn 
              WHERE dg.MaDanhGia = ? AND dg.MaUser = ?";
$stmt = mysqli_prepare($conn, $reviewSQL);
mysqli_stmt_bind_param($stmt, "ii", $maDanhGia, $maUser);
mysqli_stmt_execute($stmt);
$reviewResult = mysqli_stmt_get_result($stmt);
$review = mysqli_fetch_assoc($reviewResult);
mysqli_stmt_close($stmt);

if (!$review) {
    $_SESSION['review_error'] = "Không tìm thấy đánh giá hoặc bạn không có quyền sửa!";
    header("Location: DanhGiaCuaToi.php");
    exit();
}

// Xử lý cập nhật đánh giá
$error = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $soSao = $_POST['soSao'] ?? 0;
    $noiDung = trim($_POST['noiDung'] ?? '');

    // Validate dữ liệu
    if ($soSao < 1 || $soSao > 5) {
        $error = "Vui lòng chọn số sao từ 1 đến 5";
    } elseif (empty($noiDung)) {
        $error = "Vui lòng nhập nội dung đánh giá";
    } elseif (strlen($noiDung) < 10) {
        $error = "Nội dung đánh giá phải có ít nhất 10 ký tự";
    } elseif (strlen($noiDung) > 500) {
        $error = "Nội dung đánh giá không được vượt quá 500 ký tự";
    } else {
        // Cập nhật đánh giá
        $updateSQL = "UPDATE danh_gia SET SoSao = ?, NoiDung = ? WHERE MaDanhGia = ? AND MaUser = ?";
        $stmt = mysqli_prepare($conn, $updateSQL);
        mysqli_stmt_bind_param($stmt, "isii", $soSao, $noiDung, $maDanhGia, $maUser);

        if (mysqli_stmt_execute($stmt)) {
            $_SESSION['review_success'] = "Cập nhật đánh giá thành công!";
            mysqli_stmt_close($stmt);
            ob_end_clean();
            header("Location: DanhGia.php");
            exit();
        } else {
            $error = "Lỗi cập nhật: " . mysqli_error($conn);
        }
        mysqli_stmt_close($stmt);
    }
}
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sửa Đánh Giá - <?php echo htmlspecialchars($review['TenMonAn']); ?></title>
    <link rel="stylesheet" href="css/suadanhgia.css">
</head>

<body>
    <div class="container">
        <div class="page-header">
            <h1>SỬA ĐÁNH GIÁ</h1>
            <p>Cập nhật đánh giá của bạn cho món ăn</p>
        </div>

        <div class="content-container">
            <!-- Thông tin món ăn -->
            <div class="product-card">
                <div class="product-header">
                    <div class="product-info">
                        <h2><?php echo htmlspecialchars($review['TenMonAn']); ?></h2>
                        <p>Món ăn bạn đã đánh giá</p>
                    </div>
                    <div class="product-image">
                        <?php if (!empty($review['HinhAnh'])): ?>
                            <img src="img/<?php echo htmlspecialchars($review['HinhAnh']); ?>" alt="<?php echo htmlspecialchars($review['TenMonAn']); ?>">
                        <?php else: ?>
                            <div class="no-image">📷</div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Form sửa đánh giá -->
            <div class="review-form-card">
                <div class="form-header">
                    <h3>Chỉnh sửa đánh giá</h3>
                    <p>Hãy chia sẻ trải nghiệm của bạn về món ăn này</p>
                </div>

                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger">
                        <?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>

                <form method="POST" class="review-form" id="reviewForm">
                    <!-- Đánh giá sao -->
                    <div class="form-group">
                        <label class="form-label">Đánh giá của bạn:</label>
                        <div class="star-rating">
                            <input type="radio" id="star1" name="soSao" value="1" <?php echo $review['SoSao'] == 1 ? 'checked' : ''; ?>>
                            <label for="star1" class="star-label">
                                <span class="star">⭐</span>
                            </label>
                            <input type="radio" id="star2" name="soSao" value="2" <?php echo $review['SoSao'] == 2 ? 'checked' : ''; ?>>
                            <label for="star2" class="star-label">
                                <span class="star">⭐</span>
                            </label>
                            <input type="radio" id="star3" name="soSao" value="3" <?php echo $review['SoSao'] == 3 ? 'checked' : ''; ?>>
                            <label for="star3" class="star-label">
                                <span class="star">⭐</span>
                            </label>
                            <input type="radio" id="star4" name="soSao" value="4" <?php echo $review['SoSao'] == 4 ? 'checked' : ''; ?>>
                            <label for="star4" class="star-label">
                                <span class="star">⭐</span>
                            </label>
                            <input type="radio" id="star5" name="soSao" value="5" <?php echo $review['SoSao'] == 5 ? 'checked' : ''; ?>>
                            <label for="star5" class="star-label">
                                <span class="star">⭐</span>
                            </label>
                        </div>
                        <div class="rating-text" id="ratingText">
                            <?php
                            $ratingTexts = [
                                1 => 'Rất tệ',
                                2 => 'Tệ',
                                3 => 'Bình thường',
                                4 => 'Tốt',
                                5 => 'Rất tốt'
                            ];
                            echo $ratingTexts[$review['SoSao']] ?? 'Chọn số sao';
                            ?>
                        </div>
                    </div>

                    <!-- Nội dung đánh giá -->
                    <div class="form-group">
                        <label for="noiDung" class="form-label">Nội dung đánh giá:</label>
                        <textarea name="noiDung" id="noiDung" class="form-textarea"
                            placeholder="Hãy chia sẻ chi tiết về trải nghiệm của bạn với món ăn này..."
                            rows="5"><?php echo htmlspecialchars($review['NoiDung']); ?></textarea>
                        <div class="char-count">
                            <span id="charCount"><?php echo strlen($review['NoiDung']); ?></span>/500 ký tự
                        </div>
                    </div>

                    <!-- Nút hành động -->
                    <div class="form-actions">
                        <a href="DanhGia.php" class="btn-secondary">
                            <span class="btn-icon">←</span>
                            Quay lại
                        </a>
                        <button type="submit" class="btn-primary" id="submitBtn">
                            <span class="btn-icon">💾</span>
                            Lưu thay đổi
                        </button>
                        <button type="button" class="btn-danger" onclick="confirmDelete()">
                            <span class="btn-icon">🗑️</span>
                            Xóa đánh giá
                        </button>
                    </div>
                </form>
            </div>

            <!-- Đánh giá hiện tại -->
            <div class="current-review-card">
                <div class="card-header">
                    <h3>Đánh giá hiện tại</h3>
                </div>
                <div class="card-body">
                    <div class="review-preview">
                        <div class="preview-rating">
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                <span class="star <?php echo $i <= $review['SoSao'] ? 'active' : ''; ?>">⭐</span>
                            <?php endfor; ?>
                            <span class="rating-score"><?php echo $review['SoSao']; ?> sao</span>
                        </div>
                        <div class="preview-content">
                            <p><?php echo htmlspecialchars($review['NoiDung']); ?></p>
                        </div>
                        <div class="preview-date">
                            Đánh giá lúc: <?php echo date('d/m/Y H:i', strtotime($review['NgayDanhGia'])); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="js/suadanhgia.js"></script>
</body>

</html>
<?php
ob_end_flush();
include_once "includes/footer.php";
?>