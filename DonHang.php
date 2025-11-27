<?php
include_once "includes/header.php";

// Kiểm tra trạng thái đăng nhập của người dùng
if (!isset($_SESSION['loggedin'])) {
    header("Location: DangNhap.php");
    exit();
}

// Lấy mã người dùng từ session
$maUser = $_SESSION['MaUser'];
// Lấy biến $conn từ header.php

// Truy vấn lấy danh sách đơn hàng đã mua của người dùng
$ordersSQL = "SELECT dh.*, COUNT(ctdh.MaChiTiet) as SoMon 
             FROM DonHang dh 
             LEFT JOIN ChiTietDonHang ctdh ON dh.MaDonHang = ctdh.MaDonHang 
             WHERE dh.MaUser = ? 
             GROUP BY dh.MaDonHang 
             ORDER BY dh.NgayDat DESC";
$stmt = mysqli_prepare($conn, $ordersSQL);
if ($stmt) {
    mysqli_stmt_bind_param($stmt, "i", $maUser);
    mysqli_stmt_execute($stmt);
    $ordersResult = mysqli_stmt_get_result($stmt);
} else {
    $ordersResult = false;
    error_log("Lỗi truy vấn đơn hàng: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đơn Hàng Của Tôi</title>
    <link rel="stylesheet" href="css/donhang.css">
    </head>

<body>
    <div class="container">
        <div class="page-header">
            <h1>ĐƠN HÀNG CỦA TÔI</h1>
            <p>Theo dõi và quản lý đơn hàng đã mua</p>
        </div>

        <div class="content-container">
            <?php if (isset($_SESSION['order_success'])): ?>
                <div class="alert alert-success">
                    <?php echo $_SESSION['order_success'];
                    unset($_SESSION['order_success']); ?>
                </div>
            <?php endif; ?>

            <?php if (isset($_SESSION['order_error'])): ?>
                <div class="alert alert-error">
                    <?php echo $_SESSION['order_error'];
                    unset($_SESSION['order_error']); ?>
                </div>
            <?php endif; ?>

            <?php if ($ordersResult && mysqli_num_rows($ordersResult) > 0): ?>
                <div class="orders-container">
                    <?php while ($order = mysqli_fetch_assoc($ordersResult)): ?>
                        <div class="order-card">
                            <div class="order-header">
                                <div class="order-info">
                                    <div class="order-id">Mã đơn: <strong>#<?php echo $order['MaDonHang']; ?></strong></div>
                                    <div class="order-date"><?php echo date('d/m/Y H:i', strtotime($order['NgayDat'])); ?></div>
                                </div>
                                <div class="order-status <?php
                                                            $trangThai = $order['TrangThai'] ?? 'cho_xac_nhan';
                                                            echo str_replace('_', '-', $trangThai);
                                                            ?>">
                                    <?php
                                    switch ($order['TrangThai'] ?? 'cho_xac_nhan') {
                                        case 'cho_xac_nhan':
                                            echo 'Chờ xác nhận';
                                            break;
                                        case 'dang_xu_ly':
                                            echo 'Đang xử lý';
                                            break;
                                        case 'dang_giao':
                                            echo 'Đang giao';
                                            break;
                                        case 'hoan_thanh':
                                            echo 'Hoàn thành';
                                            break;
                                        case 'da_huy':
                                            echo 'Đã hủy';
                                            break;
                                        default:
                                            echo $order['TrangThai'];
                                    }
                                    ?>
                                </div>
                            </div>

                            <div class="order-content">
                                <div class="order-details">
                                    <div class="detail-item">
                                        <span class="label">Số món:</span>
                                        <span class="value"><?php echo $order['SoMon']; ?> món</span>
                                    </div>
                                    <div class="detail-item">
                                        <span class="label">Tổng tiền:</span>
                                        <span class="value"><?php echo number_format($order['TongTien'], 0, ',', '.'); ?>₫</span>
                                    </div>
                                    <div class="detail-item">
                                        <span class="label">Phương thức:</span>
                                        <span class="value">
                                            <?php
                                            $phuongThuc = $order['PhuongThucThanhToan'] ?? 'tien_mat';
                                            switch ($phuongThuc) {
                                                case 'tien_mat':
                                                    echo 'Tiền mặt';
                                                    break;
                                                case 'chuyen_khoan':
                                                    echo 'Chuyển khoản';
                                                    break;
                                                case 'the':
                                                    echo 'Thẻ tín dụng';
                                                    break;
                                                default:
                                                    echo $phuongThuc;
                                            }
                                            ?>
                                        </span>
                                    </div>
                                    <div class="detail-item">
                                        <span class="label">Địa chỉ giao:</span>
                                        <span class="value"><?php echo htmlspecialchars($order['DiaChiGiaoHang'] ?? ''); ?></span>
                                    </div>
                                </div>

                                <div class="order-actions">
                                    <a href="ChiTietDonHang.php?id=<?php echo $order['MaDonHang']; ?>" class="btn-view">
                                        <span class="btn-icon">👁️</span>
                                        Xem chi tiết
                                    </a>
                                    <?php if (($order['TrangThai'] ?? '') == 'hoan_thanh'): ?>
                                        <button class="btn-review" onclick="openReview(<?php echo $order['MaDonHang']; ?>, event)">
                                            <span class="btn-icon">⭐</span>
                                            Đánh giá
                                        </button>
                                    <?php endif; ?>
                                    <?php if (($order['TrangThai'] ?? '') == 'cho_xac_nhan' || ($order['TrangThai'] ?? '') == 'dang_xu_ly'): ?>
                                        <button class="btn-cancel" onclick="showCancelConfirmModal(<?php echo $order['MaDonHang']; ?>)">
                                            <span class="btn-icon">❌</span>
                                            Hủy đơn
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <div class="empty-orders">
                    <div class="empty-icon">📦</div>
                    <h3>Chưa có đơn hàng nào</h3>
                    <p>Hãy thực hiện đơn hàng đầu tiên của bạn!</p>
                    <a href="ThucDon.php" class="btn-primary">
                        <span class="btn-icon">🛒</span>
                        Mua sắm ngay
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <script src="js/donhang.js"></script>

    <script>
        /**
         * Chuyển hướng đến trang tạo đánh giá (TaoDanhGia.php).
         * 🔥 SỬA: Nhận đối tượng sự kiện e (event) làm tham số thứ hai.
         * @param {number} orderId Mã đơn hàng cần đánh giá.
         * @param {Event} e Đối tượng sự kiện click.
         */
        function openReview(orderId, e) {
            // Lấy nút đã click để thêm hiệu ứng loading
            const button = e.target.closest('.btn-review');
            
            // Hiệu ứng loading
            button.innerHTML = '<span class="btn-icon">⏳</span> Đang tải...';
            button.disabled = true;
            
            // Chuyển hướng đến trang TaoDanhGia.php
            setTimeout(() => {
                window.location.href = 'TaoDanhGia.php?id=' + orderId;
            }, 200);
        }
        
        // Ghi chú: Các hàm showCancelConfirmModal, confirmCancelOrder... được định nghĩa trong donhang.js
    </script>
    
    <?php include_once "includes/footer.php"; ?>
</body>

</html>