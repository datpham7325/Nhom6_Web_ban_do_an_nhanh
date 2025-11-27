<?php
// SỬA LỖI NOTICE: Chỉ gọi session_start() nếu session chưa được khởi động
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
// 🔥 BẮT ĐẦU ĐỆM ĐẦU RA
ob_start();

// Giả sử file này chứa kết nối $conn
include_once "includes/header.php"; 

// Kiểm tra trạng thái đăng nhập
if (!isset($_SESSION['loggedin'])) {
    header("Location: DangNhap.php");
    exit();
}

// Lấy mã người dùng từ session
$maUser = $_SESSION['MaUser'];

// ⚠️ CẬP NHẬT SQL: Lấy thông tin từ bảng DonHang thay vì MonAn
// Join bảng DanhGia với DonHang để lấy Ngày đặt và Tổng tiền
$reviewsSQL = "SELECT dg.*, dh.NgayDat, dh.TongTien, dh.TrangThai as TrangThaiDonHang
              FROM DanhGia dg 
              JOIN DonHang dh ON dg.MaDonHang = dh.MaDonHang 
              WHERE dg.MaUser = ? 
              ORDER BY dg.NgayTao DESC";

$stmt = mysqli_prepare($conn, $reviewsSQL);

// Kiểm tra lỗi chuẩn bị
if ($stmt === false) {
    die("Lỗi chuẩn bị câu lệnh: " . mysqli_error($conn));
}

mysqli_stmt_bind_param($stmt, "i", $maUser);
mysqli_stmt_execute($stmt);
$reviewsResult = mysqli_stmt_get_result($stmt);

if (!$reviewsResult) {
    die("Lỗi thực thi câu lệnh: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lịch Sử Đánh Giá</title>
    <link rel="stylesheet" href="css/danhgiacuatoi.css"> 
    <style>
        /* CSS cho Modal và Notification (thêm vào file CSS chính của bạn) */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }
        .modal-content {
            background: #fff;
            padding: 25px;
            border-radius: 8px;
            max-width: 400px;
            width: 90%;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
            animation: fadeIn 0.3s;
        }
        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            border-bottom: 1px solid #eee;
            padding-bottom: 10px;
        }
        .modal-close {
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            color: #aaa;
        }
        .modal-body {
            text-align: center;
        }
        .warning-icon {
            font-size: 3rem;
            margin-bottom: 10px;
        }
        .warning-text {
            color: #d9534f;
            font-weight: bold;
        }
        .modal-actions {
            display: flex;
            justify-content: space-around;
            margin-top: 20px;
        }
        .btn-delete-confirm, .btn-back {
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 5px;
            transition: background-color 0.3s;
        }
        .btn-delete-confirm {
            background-color: #d9534f;
            color: white;
            border: none;
        }
        .btn-delete-confirm:hover {
            background-color: #c9302c;
        }
        .btn-back {
            background-color: #f0f0f0;
            color: #333;
            border: 1px solid #ccc;
        }
        .btn-back:hover {
            background-color: #e0e0e0;
        }
        .notification {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 15px 25px;
            border-radius: 5px;
            color: white;
            z-index: 1001;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            animation: slideIn 0.5s forwards;
            display: flex;
            align-items: center;
        }
        .notification-success {
            background-color: #5cb85c;
        }
        .notification-error {
            background-color: #d9534f;
        }
        .notification-close {
            background: none;
            border: none;
            color: white;
            font-size: 1.2rem;
            margin-left: 15px;
            cursor: pointer;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes slideIn {
            from { right: -300px; opacity: 0; }
            to { right: 20px; opacity: 1; }
        }
        .btn-loading {
            opacity: 0.7;
            cursor: wait;
        }
    </style>
</head>

<body>
    <?php include_once "includes/header.php"; ?>

    <div class="container">
        <div class="page-header">
            <h1>LỊCH SỬ ĐÁNH GIÁ</h1>
            <p>Xem lại các đánh giá về đơn hàng của bạn</p>
        </div>

        <div class="content-container">
            <?php if (isset($_SESSION['review_success'])): ?>
                <div class="alert alert-success">
                    <?php echo $_SESSION['review_success']; unset($_SESSION['review_success']); ?>
                </div>
            <?php endif; ?>

            <?php if (isset($_SESSION['review_error'])): ?>
                <div class="alert alert-danger">
                    <?php echo $_SESSION['review_error']; unset($_SESSION['review_error']); ?>
                </div>
            <?php endif; ?>

            <?php if (mysqli_num_rows($reviewsResult) > 0): ?>
                <div class="reviews-container">
                    <?php while ($review = mysqli_fetch_assoc($reviewsResult)): ?>
                        <div class="review-card">
                            <div class="review-header">
                                <div class="review-info">
                                    <div class="review-product">
                                        <strong>Đơn hàng #<?php echo $review['MaDonHang']; ?></strong>
                                    </div>
                                    <div class="review-date">
                                        Đánh giá ngày: <?php echo date('d/m/Y H:i', strtotime($review['NgayTao'])); ?>
                                    </div>
                                </div>
                                <div class="review-rating">
                                    <?php $diem = $review['Diem'] ?? 0; ?>
                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                        <span class="star <?php echo $i <= $diem ? 'active' : ''; ?>">⭐</span>
                                    <?php endfor; ?>
                                    <span class="rating-score"><?php echo $diem; ?>/5</span>
                                </div>
                            </div>

                            <div class="review-content">
                                <div class="review-details">
                                    <div class="detail-item">
                                        <span class="label">Ngày đặt:</span>
                                        <span class="value"><?php echo date('d/m/Y H:i', strtotime($review['NgayDat'])); ?></span>
                                    </div>
                                    <div class="detail-item">
                                        <span class="label">Tổng tiền:</span>
                                        <span class="value"><?php echo number_format($review['TongTien'], 0, ',', '.'); ?> đ</span>
                                    </div>
                                    <div class="detail-item full-width">
                                        <span class="label">Nội dung:</span>
                                        <span class="value" style="font-style: italic;">
                                            "<?php echo htmlspecialchars($review['NoiDung']); ?>"
                                        </span>
                                    </div>

                                </div>

                                <div class="review-actions">
                                    <button class="btn-edit" onclick="editReview(<?php echo $review['MaDanhGia']; ?>)"> 
                                        <span class="btn-icon">✏️</span>
                                        Sửa
                                    </button>
                                    <button class="btn-delete" onclick="deleteReview(<?php echo $review['MaDanhGia']; ?>)">
                                        <span class="btn-icon">🗑️</span>
                                        Xóa
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <div class="empty-reviews">
                    <div class="empty-icon">🧾</div>
                    <h3>Chưa có đánh giá nào</h3>
                    <p>Hãy đặt hàng và chia sẻ trải nghiệm của bạn!</p>
                    <a href="ThucDon.php" class="btn-primary">
                        <span class="btn-icon">🛒</span>
                        Đặt món ngay
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <?php include_once "includes/footer.php"; ?>
    <script src="js/danhgiacuatoi.js"></script> 
</body>

</html>

<?php
if ($stmt) {
    mysqli_stmt_close($stmt);
}
ob_end_flush();
?>