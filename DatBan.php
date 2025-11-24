<?php
// Include file header
include_once "includes/header.php";

// Kiểm tra xem user đã đăng nhập chưa
if (!isset($_SESSION['loggedin'])) {
    header("Location: DangNhap.php");
    exit();
}

// Lấy mã user từ session
$maUser = $_SESSION['MaUser'];

// Xử lý khi form được submit
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['btnDatBan'])) {
    // Lấy và làm sạch dữ liệu từ form
    $hoTen = mysqli_real_escape_string($conn, $_POST['hoten']);
    $sdt = mysqli_real_escape_string($conn, $_POST['sdt']);
    $soNguoi = $_POST['songuoi'];
    $ngayDat = $_POST['ngaydat'];
    $gioDat = $_POST['giodat'];
    $ghiChu = mysqli_real_escape_string($conn, $_POST['ghichu']);

    // Câu lệnh SQL insert dữ liệu đặt bàn
    $insertSQL = "INSERT INTO DatBan (MaUser, HoTen, SDT, SoNguoi, NgayDat, GioDat, GhiChu) 
                 VALUES ($maUser, '$hoTen', '$sdt', $soNguoi, '$ngayDat', '$gioDat', '$ghiChu')";

    // Thực thi câu lệnh SQL
    if (mysqli_query($conn, $insertSQL)) {
        $success = "Đặt bàn thành công! Chúng tôi sẽ liên hệ xác nhận.";
    } else {
        $error = "Lỗi đặt bàn: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đặt bàn - Jollibee</title>
    <!-- Link đến file CSS -->
    <link rel="stylesheet" href="css/datban.css">
</head>

<body>
    <div class="container">
        <!-- Header section -->
        <div class="page-header">
            <h1>ĐẶT BÀN TRƯỚC</h1>
            <p>Đảm bảo chỗ ngồi tốt nhất cho bạn</p>
        </div>

        <!-- Main content container -->
        <div class="content-container">
            <!-- Grid layout cho thông tin và form -->
            <div class="main-grid">
                <!-- Cột thông tin đặt bàn -->
                <div class="info-column">
                    <h3 class="section-title">THÔNG TIN ĐẶT BÀN</h3>

                    <!-- Thông tin để người dùng chú ý trước khi đặt bàn -->
                    <div class="info-item">
                        <h4 class="info-title">🕒 GIỜ MỞ CỬA</h4>
                        <p>Thứ 2 - Chủ Nhật: 7:00 - 22:00</p>
                        <h4 class="info-title">👥 SỨC CHỨA</h4>
                        <p>Tối đa 10 người / bàn</p>
                        <h4 class="info-title">⏰ ĐẶT TRƯỚC</h4>
                        <p>Tối thiểu 2 giờ trước khi đến</p>
                        <h4 class="info-title">📞 HOTLINE</h4>
                        <p>1900 1234</p>
                    </div>

                    <!-- Khung lưu ý quan trọng -->
                    <div class="note-box">
                        <h4 class="note-title">💡 LƯU Ý</h4>
                        <p class="note-item">• Vui lòng đến đúng giờ đã đặt</p>
                        <p class="note-item">• Bàn sẽ được giữ tối đa 15 phút</p>
                        <p class="note-item">• Hủy đặt bàn trước 1 giờ</p>
                    </div>
                </div>

                <!-- Cột form đặt bàn -->
                <div class="form-column">
                    <h3 class="section-title">ĐẶT BÀN NGAY</h3>

                    <!-- Hiển thị thông báo thành công/ lỗi -->
                    <?php if (isset($success)): ?>
                        <div class="alert alert-success">
                            <?php echo $success; ?>
                        </div>
                    <?php elseif (isset($error)): ?>
                        <div class="alert alert-error">
                            <?php echo $error; ?>
                        </div>
                    <?php endif; ?>

                    <!-- Form đặt bàn -->
                    <form method="POST" action="" class="booking-form">
                        <!-- Trường họ tên -->
                        <input type="text" name="hoten" placeholder="Họ tên *"
                            value="<?php echo $_SESSION['HoTen'] ?? ''; ?>" required
                            class="form-input">

                        <!-- Trường số điện thoại -->
                        <input type="tel" name="sdt" placeholder="Số điện thoại *" required
                            class="form-input">

                        <!-- Dropdown chọn số người -->
                        <select name="songuoi" required class="form-select">
                            <option value="">Chọn số người *</option>
                            <?php for ($i = 1; $i <= 10; $i++): ?>
                                <option value="<?php echo $i; ?>"><?php echo $i; ?> người</option>
                            <?php endfor; ?>
                        </select>

                        <!-- Trường chọn ngày -->
                        <input type="date" name="ngaydat" placeholder="Ngày đặt *"
                            min="<?php echo date('Y-m-d'); ?>" required
                            class="form-input">

                        <!-- Trường chọn giờ -->
                        <input type="time" name="giodat" placeholder="Giờ đặt *"
                            min="07:00" max="22:00" required
                            class="form-input">

                        <!-- Textarea ghi chú -->
                        <textarea name="ghichu" placeholder="Ghi chú (yêu cầu đặc biệt, vị trí mong muốn...)" rows="4"
                            class="form-textarea"></textarea>

                        <!-- Nút submit -->
                        <button type="submit" name="btnDatBan" class="submit-btn">
                            🍽️ ĐẶT BÀN NGAY
                        </button>
                    </form>
                </div>
            </div>

            <!-- Section không gian nhà hàng -->
            <div class="restaurant-space">
                <h3 class="section-title center">KHÔNG GIAN NHÀ HÀNG</h3>
                <div class="space-container">
                    <p class="space-description">Jollibee mang đến không gian ấm cúng, phù hợp cho mọi dịp:</p>
                    <div class="occasion-grid">
                        <!-- Các dịch vụ -->
                        <div class="occasion-item">
                            <div class="occasion-icon">👨‍👩‍👧‍👦</div>
                            <p class="occasion-name">Gia đình</p>
                        </div>
                        <div class="occasion-item">
                            <div class="occasion-icon">💼</div>
                            <p class="occasion-name">Họp mặt</p>
                        </div>
                        <div class="occasion-item">
                            <div class="occasion-icon">🎉</div>
                            <p class="occasion-name">Sinh nhật</p>
                        </div>
                        <div class="occasion-item">
                            <div class="occasion-icon">💑</div>
                            <p class="occasion-name">Hẹn hò</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Include file JavaScript -->
    <script src="js/datban.js"></script>
</body>

</html>

<?php include_once "includes/footer.php"; ?>