<?php
// Include file header để có kết nối database và session
include_once "includes/header.php";

// KIỂM TRA XEM USER ĐÃ ĐĂNG NHẬP CHƯA
if (!isset($_SESSION['loggedin'])) {
    header("Location: DangNhap.php");
    exit();
}

// LẤY MÃ USER TỪ SESSION VÀ MÃ ĐƠN HÀNG TỪ URL
$maUser = $_SESSION['MaUser'];
$maDonHang = $_GET['id'] ?? 0; // Lấy mã đơn hàng từ tham số URL, mặc định là 0 nếu không có

// KIỂM TRA MÃ ĐƠN HÀNG CÓ HỢP LỆ KHÔNG
if (!$maDonHang) {
    header("Location: DonHang.php");
    exit();
}

// TRUY VẤN LẤY THÔNG TIN CHI TIẾT ĐƠN HÀNG
$orderSQL = "SELECT dh.*, u.Ho, u.Ten, u.SDT as SDTUser, u.Email 
             FROM DonHang dh 
             JOIN Users u ON dh.MaUser = u.MaUser 
             WHERE dh.MaDonHang = ? AND dh.MaUser = ?"; // Chỉ lấy đơn hàng của user hiện tại
$stmt = mysqli_prepare($conn, $orderSQL);
mysqli_stmt_bind_param($stmt, "ii", $maDonHang, $maUser); // Bind 2 tham số integer
mysqli_stmt_execute($stmt);
$orderResult = mysqli_stmt_get_result($stmt);
$order = mysqli_fetch_assoc($orderResult); // Lấy 1 bản ghi duy nhất
mysqli_stmt_close($stmt);

// KIỂM TRA ĐƠN HÀNG CÓ TỒN TẠI KHÔNG
if (!$order) {
    header("Location: DonHang.php");
    exit();
}

// TRUY VẤN LẤY CHI TIẾT CÁC MÓN TRONG ĐƠN HÀNG
$orderItemsSQL = "SELECT ctdh.*, ma.TenMonAn, ma.HinhAnh, kt.TenSize 
                  FROM ChiTietDonHang ctdh 
                  JOIN BienTheMonAn bt ON ctdh.MaBienThe = bt.MaBienThe 
                  JOIN MonAn ma ON bt.MaMonAn = ma.MaMonAn 
                  JOIN KichThuoc kt ON bt.MaSize = kt.MaSize 
                  WHERE ctdh.MaDonHang = ?"; // Lấy tất cả món ăn trong đơn hàng
$stmt = mysqli_prepare($conn, $orderItemsSQL);
mysqli_stmt_bind_param($stmt, "i", $maDonHang);
mysqli_stmt_execute($stmt);
$orderItemsResult = mysqli_stmt_get_result($stmt);
$orderItems = [];
$tongTien = 0; // Khởi tạo biến tính tổng tiền

// LẶP QUA TẤT CẢ CÁC MÓN ĂN TRONG ĐƠN HÀNG
while ($item = mysqli_fetch_assoc($orderItemsResult)) {
    $orderItems[] = $item; // Thêm món ăn vào mảng
    $tongTien += $item['ThanhTien']; // Cộng dồn thành tiền
}
mysqli_stmt_close($stmt);
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chi Tiết Đơn Hàng #<?php echo $maDonHang; ?> - Jollibee</title>
    <!-- Link đến file CSS riêng cho trang Chi Tiết Đơn Hàng -->
    <link rel="stylesheet" href="css/chitietdonhang.css">
</head>

<body>
    <?php include_once "includes/header.php"; ?>

    <div class="container">
        <!-- HEADER TRANG CHI TIẾT ĐƠN HÀNG -->
        <div class="page-header">
            <div class="header-content">
                <h1>CHI TIẾT ĐƠN HÀNG</h1>
                <p>Mã đơn hàng: <strong>#<?php echo $maDonHang; ?></strong></p>
            </div>
        </div>

        <!-- PHẦN NỘI DUNG CHÍNH -->
        <div class="content-container">
            <!-- ===== THẺ THÔNG TIN ĐƠN HÀNG ===== -->
            <div class="order-summary-card">
                <div class="card-header">
                    <div class="header-icon">📦</div>
                    <h3>Thông tin đơn hàng</h3>
                </div>
                <div class="card-body">
                    <div class="info-grid">
                        <!-- TRẠNG THÁI ĐƠN HÀNG -->
                        <div class="info-item">
                            <span class="label">Trạng thái:</span>
                            <span class="value status-<?php echo str_replace('_', '-', $order['TrangThai']); ?>">
                                <?php
                                // CHUYỂN ĐỔI TRẠNG THÁI TỪ TIẾNG ANH SANG TIẾNG VIỆT
                                switch ($order['TrangThai']) {
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
                                        echo $order['TrangThai']; // Trường hợp mặc định
                                }
                                ?>
                            </span>
                        </div>

                        <!-- NGÀY ĐẶT HÀNG -->
                        <div class="info-item">
                            <span class="label">Ngày đặt:</span>
                            <span class="value"><?php echo date('d/m/Y H:i', strtotime($order['NgayDat'])); ?></span>
                        </div>

                        <!-- PHƯƠNG THỨC THANH TOÁN -->
                        <div class="info-item">
                            <span class="label">Phương thức thanh toán:</span>
                            <span class="value">
                                <?php
                                // CHUYỂN ĐỔI PHƯƠNG THỨC THANH TOÁN
                                switch ($order['PhuongThucThanhToan']) {
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
                                        echo $order['PhuongThucThanhToan'];
                                }
                                ?>
                            </span>
                        </div>

                        <!-- TỔNG TIỀN ĐƠN HÀNG -->
                        <div class="info-item">
                            <span class="label">Tổng tiền:</span>
                            <span class="value price"><?php echo number_format($order['TongTien'], 0, ',', '.'); ?>₫</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ===== THẺ THÔNG TIN GIAO HÀNG ===== -->
            <div class="order-summary-card">
                <div class="card-header">
                    <div class="header-icon">📍</div>
                    <h3>Thông tin giao hàng</h3>
                </div>
                <div class="card-body">
                    <div class="info-grid">
                        <!-- THÔNG TIN NGƯỜI NHẬN -->
                        <div class="info-item">
                            <span class="label">Người nhận:</span>
                            <span class="value"><?php echo htmlspecialchars($order['Ho'] . ' ' . $order['Ten']); ?></span>
                        </div>

                        <!-- SỐ ĐIỆN THOẠI NGƯỜI NHẬN -->
                        <div class="info-item">
                            <span class="label">Số điện thoại:</span>
                            <span class="value"><?php echo htmlspecialchars($order['SDTGiaoHang'] ?? $order['SDTUser']); ?></span>
                        </div>

                        <!-- ĐỊA CHỈ GIAO HÀNG (CHIẾM TOÀN BỘ CHIỀU RỘNG) -->
                        <div class="info-item full-width">
                            <span class="label">Địa chỉ giao hàng:</span>
                            <span class="value"><?php echo htmlspecialchars($order['DiaChiGiaoHang']); ?></span>
                        </div>

                        <!-- HIỂN THỊ GHI CHÚ NẾU CÓ -->
                        <?php if (!empty($order['GhiChu'])): ?>
                            <div class="info-item full-width">
                                <span class="label">Ghi chú:</span>
                                <span class="value"><?php echo htmlspecialchars($order['GhiChu']); ?></span>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- ===== THẺ CHI TIẾT SẢN PHẨM ===== -->
            <div class="order-summary-card">
                <div class="card-header">
                    <div class="header-icon">🍔</div>
                    <h3>Chi tiết sản phẩm</h3>
                </div>
                <div class="card-body">
                    <!-- DANH SÁCH CÁC MÓN ĂN TRONG ĐƠN HÀNG -->
                    <div class="order-items">
                        <?php foreach ($orderItems as $item): ?>
                            <div class="order-item">
                                <!-- ẢNH MÓN ĂN -->
                                <img src="img/<?php echo $item['HinhAnh']; ?>"
                                    alt="<?php echo $item['TenMonAn']; ?>"
                                    class="item-image"
                                    onerror="this.src='img/food-placeholder.jpg'"> <!-- Ảnh dự phòng nếu lỗi -->

                                <!-- THÔNG TIN CHI TIẾT MÓN ĂN -->
                                <div class="item-details">
                                    <div class="item-name"><?php echo $item['TenMonAn']; ?></div>

                                    <!-- HIỂN THỊ SIZE NẾU KHÔNG PHẢI SIZE "VỪA" -->
                                    <?php if ($item['TenSize'] && $item['TenSize'] != 'Vừa'): ?>
                                        <div class="item-meta">Size: <?php echo $item['TenSize']; ?></div>
                                    <?php endif; ?>

                                    <div class="item-price"><?php echo number_format($item['DonGia'], 0, ',', '.'); ?>₫</div>
                                </div>

                                <!-- SỐ LƯỢNG MÓN ĂN -->
                                <div class="item-quantity">
                                    <span class="quantity">x<?php echo $item['SoLuong']; ?></span>
                                </div>

                                <!-- THÀNH TIỀN CHO MÓN NÀY -->
                                <div class="item-total">
                                    <?php echo number_format($item['ThanhTien'], 0, ',', '.'); ?>₫
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- ===== BẢNG TỔNG TIỀN ===== -->
                    <div class="order-totals">
                        <!-- TẠM TÍNH -->
                        <div class="total-row">
                            <span>Tạm tính:</span>
                            <span><?php echo number_format($tongTien, 0, ',', '.'); ?>₫</span>
                        </div>

                        <!-- PHÍ VẬN CHUYỂN (MIỄN PHÍ) -->
                        <div class="total-row">
                            <span>Phí vận chuyển:</span>
                            <span>0₫</span>
                        </div>

                        <!-- TỔNG CỘNG CUỐI CÙNG -->
                        <div class="total-row final">
                            <span>Tổng cộng:</span>
                            <span class="final-amount"><?php echo number_format($order['TongTien'], 0, ',', '.'); ?>₫</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ===== CÁC NÚT HÀNH ĐỘNG ===== -->
            <div class="action-buttons">
                <!-- NÚT QUAY LẠI DANH SÁCH ĐƠN HÀNG -->
                <a href="DonHang.php" class="btn-secondary">
                    <span class="btn-icon">←</span>
                    Quay lại
                </a>

                <!-- HIỂN THỊ NÚT ĐÁNH GIÁ NẾU ĐƠN HÀNG ĐÃ HOÀN THÀNH -->
                <?php if ($order['TrangThai'] == 'hoan_thanh'): ?>
                    <button class="btn-primary" onclick="openReview(<?php echo $maDonHang; ?>)">
                        <span class="btn-icon">⭐</span>
                        Đánh giá đơn hàng
                    </button>

                    <!-- HIỂN THỊ NÚT HỦY ĐƠN NẾU ĐANG Ở TRẠNG THÁI CÓ THỂ HỦY -->
                <?php elseif ($order['TrangThai'] == 'cho_xac_nhan' || $order['TrangThai'] == 'dang_xu_ly'): ?>
                    <button class="btn-danger" onclick="cancelOrder(<?php echo $maDonHang; ?>)">
                        <span class="btn-icon">❌</span>
                        Hủy đơn hàng
                    </button>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Link đến file JavaScript xử lý tương tác -->
    <script src="js/chitietdonhang.js"></script>

    <?php include_once "includes/footer.php"; ?>
</body>

</html>