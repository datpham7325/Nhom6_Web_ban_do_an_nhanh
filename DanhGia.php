<?php
include_once "includes/header.php";

// Kiểm tra trạng thái đăng nhập của người dùng
if (!isset($_SESSION['loggedin'])) {
    header("Location: DangNhap.php");
    exit();
}

// Lấy mã người dùng từ session
$maUser = $_SESSION['MaUser'];

// Kiểm tra và tạo bảng danh_gia nếu chưa tồn tại
$checkTableSQL = "SHOW TABLES LIKE 'danh_gia'";
$tableResult = mysqli_query($conn, $checkTableSQL);

// Nếu bảng chưa tồn tại, tạo bảng mới
if (mysqli_num_rows($tableResult) == 0) {
    $createTableSQL = "CREATE TABLE danh_gia (
        MaDanhGia INT AUTO_INCREMENT PRIMARY KEY,
        MaUser INT NOT NULL,
        MaMonAn INT NOT NULL,
        SoSao INT NOT NULL CHECK (SoSao BETWEEN 1 AND 5),
        NoiDung TEXT,
        NgayDanhGia DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (MaUser) REFERENCES users(MaUser) ON DELETE CASCADE,
        FOREIGN KEY (MaMonAn) REFERENCES monan(MaMonAn) ON DELETE CASCADE
    )";

    // Thực hiện tạo bảng và thêm dữ liệu mẫu
    if (mysqli_query($conn, $createTableSQL)) {
        $sampleDataSQL = "INSERT INTO danh_gia (MaUser, MaMonAn, SoSao, NoiDung) VALUES 
            (?, 1, 5, 'Gà giòn rất ngon, da giòn thịt mềm. Sẽ quay lại ủng hộ!'),
            (?, 10, 4, 'Mì Ý sốt cay vừa miệng, hương vị đậm đà. Rất đáng thử!')";

        $stmt = mysqli_prepare($conn, $sampleDataSQL);
        mysqli_stmt_bind_param($stmt, "ii", $maUser, $maUser);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }
}

// Truy vấn lấy tất cả đánh giá của người dùng hiện tại
$reviewsSQL = "SELECT dg.*, m.TenMonAn, m.HinhAnh 
              FROM danh_gia dg 
              JOIN monan m ON dg.MaMonAn = m.MaMonAn 
              WHERE dg.MaUser = ? 
              ORDER BY dg.NgayDanhGia DESC";
$stmt = mysqli_prepare($conn, $reviewsSQL);

// Kiểm tra lỗi khi chuẩn bị câu lệnh
if ($stmt === false) {
    die("Lỗi chuẩn bị câu lệnh: " . mysqli_error($conn));
}

// Thực thi truy vấn với tham số mã người dùng
mysqli_stmt_bind_param($stmt, "i", $maUser);
mysqli_stmt_execute($stmt);
$reviewsResult = mysqli_stmt_get_result($stmt);

// Kiểm tra lỗi khi thực thi truy vấn
if (!$reviewsResult) {
    die("Lỗi thực thi câu lệnh: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đánh Giá Của Tôi</title>
    <link rel="stylesheet" href="css/danhgiacuatoi.css">
</head>

<body>
    <?php include_once "includes/header.php"; ?>

    <div class="container">
        <div class="page-header">
            <h1>ĐÁNH GIÁ CỦA TÔI</h1>
            <p>Xem và quản lý đánh giá của bạn</p>
        </div>

        <div class="content-container">
            <!-- Hiển thị thông báo thành công nếu có -->
            <?php if (isset($_SESSION['review_success'])): ?>
                <div class="alert alert-success">
                    <?php echo $_SESSION['review_success'];
                    unset($_SESSION['review_success']); ?>
                </div>
            <?php endif; ?>

            <!-- Hiển thị thông báo lỗi nếu có -->
            <?php if (isset($_SESSION['review_error'])): ?>
                <div class="alert alert-danger">
                    <?php echo $_SESSION['review_error'];
                    unset($_SESSION['review_error']); ?>
                </div>
            <?php endif; ?>

            <!-- Kiểm tra xem có đánh giá nào không -->
            <?php if (mysqli_num_rows($reviewsResult) > 0): ?>
                <div class="reviews-container">
                    <!-- Lặp qua từng đánh giá và hiển thị -->
                    <?php while ($review = mysqli_fetch_assoc($reviewsResult)): ?>
                        <div class="review-card">
                            <div class="review-header">
                                <div class="review-info">
                                    <div class="review-product">
                                        <strong><?php echo $review['TenMonAn']; ?></strong>
                                    </div>
                                    <div class="review-date">
                                        <!-- Hiển thị ngày đánh giá đã định dạng -->
                                        <?php echo date('d/m/Y H:i', strtotime($review['NgayDanhGia'])); ?>
                                    </div>
                                </div>
                                <div class="review-rating">
                                    <!-- Hiển thị số sao đánh giá -->
                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                        <span class="star <?php echo $i <= $review['SoSao'] ? 'active' : ''; ?>">⭐</span>
                                    <?php endfor; ?>
                                    <span class="rating-score"><?php echo $review['SoSao']; ?>/5</span>
                                </div>
                            </div>

                            <div class="review-content">
                                <div class="review-details">
                                    <div class="detail-item">
                                        <span class="label">Món ăn:</span>
                                        <span class="value"><?php echo $review['TenMonAn']; ?></span>
                                    </div>
                                    <div class="detail-item">
                                        <span class="label">Số sao:</span>
                                        <span class="value">
                                            <?php echo $review['SoSao']; ?> sao
                                            <span class="stars-preview">
                                                <!-- Hiển thị số sao dạng preview nhỏ -->
                                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                                    <span class="star-small <?php echo $i <= $review['SoSao'] ? 'active' : ''; ?>">⭐</span>
                                                <?php endfor; ?>
                                            </span>
                                        </span>
                                    </div>
                                    <div class="detail-item full-width">
                                        <span class="label">Nội dung:</span>
                                        <!-- Hiển thị nội dung đánh giá, sử dụng htmlspecialchars để bảo mật -->
                                        <span class="value"><?php echo htmlspecialchars($review['NoiDung']); ?></span>
                                    </div>
                                </div>

                                <!-- Các nút hành động cho đánh giá -->
                                <div class="review-actions">
                                    <button class="btn-edit" onclick="editReview(<?php echo $review['MaDanhGia']; ?>)">
                                        <span class="btn-icon">✏️</span>
                                        Sửa đánh giá
                                    </button>
                                    <button class="btn-delete" onclick="showDeleteConfirmModal(<?php echo $review['MaDanhGia']; ?>)">
                                        <span class="btn-icon">🗑️</span>
                                        Xóa đánh giá
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <!-- Hiển thị khi không có đánh giá nào -->
                <div class="empty-reviews">
                    <div class="empty-icon">⭐</div>
                    <h3>Chưa có đánh giá nào</h3>
                    <p>Hãy đánh giá các món ăn bạn đã thưởng thức!</p>
                    <a href="ThucDon.php" class="btn-primary">
                        <span class="btn-icon">🛒</span>
                        Mua sắm ngay
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="js/danhgiacuatoi.js"></script>
    <?php include_once "includes/footer.php"; ?>
</body>

</html>

<?php
// Đóng statement để giải phóng tài nguyên
if ($stmt) {
    mysqli_stmt_close($stmt);
}
?>