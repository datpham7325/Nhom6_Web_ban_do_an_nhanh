<?php 
// Include file header để có kết nối database và session
include_once "includes/header.php";

// DANH SÁCH VOUCHER CỐ ĐỊNH - thay vì lấy từ database để demo
// Trong thực tế, nên lấy từ bảng Voucher trong database
$vouchers = [
    'COMBO50' => [
        'MaVoucher' => 'COMBO50',
        'TenVoucher' => 'Giảm 50% Combo',
        'MoTa' => 'Giảm 50% khi mua 2 combo bất kỳ',
        'PhanTramGiam' => 50.00,        // Phần trăm giảm giá
        'GiamToiDa' => 50000,          // Số tiền giảm tối đa
        'DonToiThieu' => 200000,       // Đơn hàng tối thiểu để áp dụng
        'NgayHetHan' => '2024-12-31'   // Ngày hết hạn voucher
    ],
    'BURGER1T1' => [
        'MaVoucher' => 'BURGER1T1',
        'TenVoucher' => 'Mua 1 Tặng 1 Burger',
        'MoTa' => 'Mua 1 burger tặng 1 burger cùng loại',
        'PhanTramGiam' => 100.00,      // 100% = mua 1 tặng 1
        'GiamToiDa' => 40000,          // Giá trị tối đa được giảm
        'DonToiThieu' => 0,            // Không yêu cầu đơn tối thiểu
        'NgayHetHan' => '2024-12-31'
    ],
    'FREESHIP' => [
        'MaVoucher' => 'FREESHIP',
        'TenVoucher' => 'Miễn phí vận chuyển',
        'MoTa' => 'Miễn phí ship cho đơn hàng từ 200.000 VND',
        'PhanTramGiam' => 100.00,      // 100% phí ship
        'GiamToiDa' => 30000,          // Phí ship tối đa được miễn
        'DonToiThieu' => 200000,       // Đơn tối thiểu 200k
        'NgayHetHan' => '2024-12-31'
    ]
];

// XỬ LÝ ÁP DỤNG MÃ GIẢM GIÁ KHI USER NHẤN NÚT
if(isset($_POST['apply_voucher'])) {
    // Lấy mã voucher từ form và loại bỏ khoảng trắng
    $voucher_code = trim($_POST['voucher_code']);
    
    // KIỂM TRA MÃ VOUCHER CÓ ĐƯỢC NHẬP KHÔNG
    if(empty($voucher_code)) {
        $error = "Vui lòng nhập mã giảm giá!";
    } else {
        // KIỂM TRA MÃ VOUCHER CÓ TỒN TẠI TRONG DANH SÁCH KHÔNG
        if(isset($vouchers[$voucher_code])) {
            $voucher = $vouchers[$voucher_code];
            
            // KIỂM TRA NGÀY HẾT HẠN CỦA VOUCHER
            $today = date('Y-m-d'); // Lấy ngày hiện tại
            if($voucher['NgayHetHan'] >= $today) {
                // LƯU VOUCHER VÀO SESSION ĐỂ SỬ DỤNG Ở TRANG KHÁC
                $_SESSION['voucher'] = $voucher;
                $success = "Áp dụng mã giảm giá thành công!";
            } else {
                $error = "Mã giảm giá đã hết hạn!";
            }
        } else {
            $error = "Mã giảm giá không hợp lệ!";
        }
    }
}

// XỬ LÝ HỦY ÁP DỤNG VOUCHER KHI USER NHẤN NÚT HỦY
if(isset($_POST['remove_voucher'])) {
    // XÓA VOUCHER KHỎI SESSION
    unset($_SESSION['voucher']);
    // CHUYỂN HƯỚNG LẠI TRANG ĐỂ CẬP NHẬT GIAO DIỆN
    header("Location: UuDai.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ưu Đãi Đặc Biệt - Jollibee</title>
    <!-- Link đến file CSS riêng cho trang Ưu Đãi -->
    <link rel="stylesheet" href="css/UuDai.css">
</head>
<body>
    <div class="container">
        <!-- HEADER CHÍNH CỦA TRANG -->
        <div class="page-header">
            <h1>ƯU ĐÃI ĐẶC BIỆT</h1>
            <p>Giá tốt - Chất lượng tuyệt vời!</p>
        </div>

        <!-- PHẦN NỘI DUNG CHÍNH -->
        <div class="content-container">
            <!-- HIỂN THỊ THÔNG BÁO THÀNH CÔNG HOẶC LỖI -->
            <?php if(isset($success)): ?>
                <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
            <?php elseif(isset($error)): ?>
                <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <!-- LƯỚI HIỂN THỊ DANH SÁCH VOUCHER -->
            <div class="vouchers-grid">
                <!-- VOUCHER 1: GIẢM 50% COMBO -->
                <div class="voucher-card discount-card">
                    <!-- NHÃN "HOT" TRÊN VOUCHER -->
                    <div class="voucher-badge">HOT</div>
                    <!-- ICON BIỂU TƯỢNG CHO VOUCHER -->
                    <div class="voucher-icon">🎉</div>
                    <!-- TIÊU ĐỀ VOUCHER -->
                    <h3 class="voucher-title">GIẢM 50%</h3>
                    <!-- MÔ TẢ NGẮN -->
                    <p class="voucher-description">Khi mua 2 Combo bất kỳ</p>
                    <!-- CHI TIẾT ĐIỀU KIỆN ÁP DỤNG -->
                    <div class="voucher-details">
                        <p><strong>📅 Áp dụng:</strong> Thứ 2 - Thứ 6</p>
                        <p><strong>💰 Đơn tối thiểu:</strong> 200.000 VND</p>
                        <p><strong>🏷️ Mã:</strong> <code class="voucher-code">COMBO50</code></p>
                    </div>
                    <!-- FORM ÁP DỤNG VOUCHER -->
                    <form method="POST" action="" class="voucher-form">
                        <!-- TRUYỀN MÃ VOUCHER DƯỚI DẠNG ẨN -->
                        <input type="hidden" name="voucher_code" value="COMBO50">
                        <!-- NÚT ÁP DỤNG VOUCHER -->
                        <button type="submit" name="apply_voucher" class="btn-voucher">
                            SỬ DỤNG NGAY
                        </button>
                    </form>
                </div>

                <!-- VOUCHER 2: MUA 1 TẶNG 1 BURGER -->
                <div class="voucher-card burger-card">
                    <div class="voucher-badge">MỚI</div>
                    <div class="voucher-icon">🍔</div>
                    <h3 class="voucher-title">MUA 1 TẶNG 1</h3>
                    <p class="voucher-description">Burger Jollibee</p>
                    <div class="voucher-details">
                        <p><strong>📅 Áp dụng:</strong> Thứ 7 & Chủ Nhật</p>
                        <p><strong>🍔 Áp dụng:</strong> Burger Jollibee</p>
                        <p><strong>🏷️ Mã:</strong> <code class="voucher-code">BURGER1T1</code></p>
                    </div>
                    <form method="POST" action="" class="voucher-form">
                        <input type="hidden" name="voucher_code" value="BURGER1T1">
                        <button type="submit" name="apply_voucher" class="btn-voucher">
                            SỬ DỤNG NGAY
                        </button>
                    </form>
                </div>

                <!-- VOUCHER 3: MIỄN PHÍ SHIP -->
                <div class="voucher-card freeship-card">
                    <div class="voucher-badge">FREE</div>
                    <div class="voucher-icon">🚚</div>
                    <h3 class="voucher-title">MIỄN PHÍ SHIP</h3>
                    <p class="voucher-description">Đơn hàng từ 200.000 VND</p>
                    <div class="voucher-details">
                        <p><strong>📅 Áp dụng:</strong> Cả tuần</p>
                        <p><strong>🚚 Phạm vi:</strong> Toàn quốc</p>
                        <p><strong>🏷️ Mã:</strong> <code class="voucher-code">FREESHIP</code></p>
                    </div>
                    <form method="POST" action="" class="voucher-form">
                        <input type="hidden" name="voucher_code" value="FREESHIP">
                        <button type="submit" name="apply_voucher" class="btn-voucher">
                            SỬ DỤNG NGAY
                        </button>
                    </form>
                </div>
            </div>

            <!-- HIỂN THỊ VOUCHER ĐANG ĐƯỢC ÁP DỤNG (NẾU CÓ) -->
            <?php if(isset($_SESSION['voucher'])): ?>
                <div class="active-voucher">
                    <div class="active-voucher-content">
                        <!-- ICON VOUCHER ĐANG ÁP DỤNG -->
                        <div class="active-voucher-icon">🎉</div>
                        <!-- THÔNG TIN CHI TIẾT VOUCHER -->
                        <div class="active-voucher-info">
                            <h3>MÃ GIẢM GIÁ ĐANG ÁP DỤNG</h3>
                            <!-- HIỂN THỊ MÃ VOUCHER VÀ MÔ TẢ -->
                            <p><strong><?php echo htmlspecialchars($_SESSION['voucher']['MaVoucher']); ?></strong> - <?php echo htmlspecialchars($_SESSION['voucher']['MoTa']); ?></p>
                            <!-- HIỂN THỊ THÔNG TIN GIẢM GIÁ -->
                            <p>Giảm <?php echo htmlspecialchars($_SESSION['voucher']['PhanTramGiam']); ?>% - Tối đa <?php echo number_format($_SESSION['voucher']['GiamToiDa']); ?> VND</p>
                            <!-- HIỂN THỊ HẠN SỬ DỤNG -->
                            <p>HSD: <?php echo date('d/m/Y', strtotime($_SESSION['voucher']['NgayHetHan'])); ?></p>
                        </div>
                        <!-- CÁC NÚT HÀNH ĐỘNG -->
                        <div class="active-voucher-actions">
                            <!-- NÚT CHUYỂN ĐẾN GIỎ HÀNG -->
                            <a href="GioHang.php" class="btn-go-to-cart">ĐẾN GIỎ HÀNG</a>
                            <!-- FORM HỦY ÁP DỤNG VOUCHER -->
                            <form method="POST" action="">
                                <input type="hidden" name="remove_voucher" value="1">
                                <button type="submit" class="btn-remove-voucher">HỦY</button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- FORM NHẬP MÃ GIẢM GIÁ THỦ CÔNG -->
            <div class="manual-voucher">
                <h3 class="section-title">NHẬP MÃ GIẢM GIÁ</h3>
                <form method="POST" action="" class="voucher-input-form">
                    <!-- Ô NHẬP MÃ VOUCHER -->
                    <input type="text" name="voucher_code" placeholder="Nhập mã giảm giá của bạn..." required
                           value="<?php echo isset($_POST['voucher_code']) ? htmlspecialchars($_POST['voucher_code']) : ''; ?>">
                    <!-- NÚT ÁP DỤNG MÃ -->
                    <button type="submit" name="apply_voucher">ÁP DỤNG</button>
                </form>
                <!-- GHI CHÚ HƯỚNG DẪN -->
                <p class="voucher-note">💡 Bạn cũng có thể nhập trực tiếp mã giảm giá tại bước thanh toán</p>
            </div>

            <!-- BANNER QUẢNG CÁO LỚN -->
            <div class="promo-banner">
                <!-- HIỂN THỊ HÌNH ẢNH KHUYẾN MÃI -->
                <img src="img/khuyenmai/banner-uu-dai.jpg" alt="Ưu đãi đặc biệt" 
                     onerror="this.style.display='none'"> <!-- Ẩn ảnh nếu không load được -->
            </div>

            <!-- THÔNG TIN BỔ SUNG VỀ CHƯƠNG TRÌNH ƯU ĐÃI -->
            <div class="voucher-info">
                <!-- MỤC THÔNG TIN 1: THỜI GIAN ƯU ĐÃI -->
                <div class="info-item">
                    <div class="info-icon">⏰</div>
                    <h4>ƯU ĐÃI CÓ HẠN</h4>
                    <p>Áp dụng đến hết tháng 12/2024</p>
                </div>
                <!-- MỤC THÔNG TIN 2: CÁCH SỬ DỤNG -->
                <div class="info-item">
                    <div class="info-icon">📱</div>
                    <h4>DỄ DÀNG SỬ DỤNG</h4>
                    <p>Click "Sử dụng ngay" để áp dụng</p>
                </div>
                <!-- MỤC THÔNG TIN 3: LỢI ÍCH -->
                <div class="info-item">
                    <div class="info-icon">💳</div>
                    <h4>ÁP DỤNG NGAY</h4>
                    <p>Giảm giá trực tiếp khi thanh toán</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Link đến file JavaScript xử lý tương tác -->
    <script src="js/UuDai.js"></script>
</body>
</html>

<?php 
// Include file footer để đóng kết nối và hiển thị phần chân trang
include_once "includes/footer.php"; 
?>