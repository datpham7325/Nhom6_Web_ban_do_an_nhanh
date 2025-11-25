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

// Xử lý khi form đặt sự kiện được submit
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['btnDatSuKien'])) {
    // Lấy và làm sạch dữ liệu từ form
    $tenSuKien = mysqli_real_escape_string($conn, $_POST['tensukien']);
    $hoTen = mysqli_real_escape_string($conn, $_POST['hoten']);
    $sdt = mysqli_real_escape_string($conn, $_POST['sdt']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $soNguoi = $_POST['songuoi'];
    $ngaySuKien = $_POST['ngaysukien'];
    $gioBatDau = $_POST['giobatdau'];
    $gioKetThuc = $_POST['gioketthuc'];
    $loaiSuKien = $_POST['loaisukien'];
    $yeuCau = mysqli_real_escape_string($conn, $_POST['yeucau']);

    // Câu lệnh SQL insert dữ liệu đặt sự kiện
    $insertSQL = "INSERT INTO DatSuKien (MaUser, TenSuKien, HoTenNguoiDaiDien, SDT, Email, SoNguoi, NgaySuKien, GioBatDau, GioKetThuc, LoaiSuKien, YeuCauDacBiet) 
                 VALUES ($maUser, '$tenSuKien', '$hoTen', '$sdt', '$email', $soNguoi, '$ngaySuKien', '$gioBatDau', '$gioKetThuc', '$loaiSuKien', '$yeuCau')";

    // Thực thi câu lệnh SQL
    if (mysqli_query($conn, $insertSQL)) {
        $success = "Đặt sự kiện thành công! Chúng tôi sẽ liên hệ xác nhận trong 24h.";
    } else {
        $error = "Lỗi đặt sự kiện: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đặt Sự Kiện - Jollibee</title>
    <!-- Link đến file CSS -->
    <link rel="stylesheet" href="css/datsukien.css">
</head>

<body>
    <div class="container">
        <!-- Header section -->
        <div class="page-header">
            <h1>ĐẶT SỰ KIỆN</h1>
            <p>Tổ chức sự kiện đáng nhớ tại Jollibee</p>
        </div>

        <!-- Main content container -->
        <div class="content-container">
            <!-- Grid layout cho thông tin và form -->
            <div class="main-grid">
                <!-- Cột thông tin sự kiện -->
                <div class="info-column">
                    <h3 class="section-title">THÔNG TIN SỰ KIỆN</h3>

                    <!-- Thông tin chi tiết về sự kiện -->
                    <div class="info-item">
                        <h4 class="info-title">🎉 SINH NHẬT</h4>
                        <p>Trang trí theo chủ đề + Bánh sinh nhật đặc biệt</p>
                        <h4 class="info-title">💼 HỘI NGHỊ</h4>
                        <p>Không gian chuyên nghiệp + Menu tiệc</p>
                        <h4 class="info-title">💒 TIỆC CƯỚI</h4>
                        <p>Trang trí lãng mạn + Menu cao cấp</p>
                        <h4 class="info-title">👨‍👩‍👧‍👦 GIA ĐÌNH</h4>
                        <p>Không gian ấm cúng + Menu gia đình</p>
                    </div>

                    <!-- Khung liên hệ -->
                    <div class="contact-box">
                        <h4 class="contact-title">📞 LIÊN HỆ NGAY</h4>
                        <p class="contact-item"><strong>Hotline:</strong> 1900 1234</p>
                        <p class="contact-item"><strong>Email:</strong> event@jollibee.vn</p>
                        <p class="contact-item"><strong>Giờ làm việc:</strong> 7:00 - 22:00</p>
                    </div>
                </div>

                <!-- Cột form đặt sự kiện -->
                <div class="form-column">
                    <h3 class="section-title">ĐĂNG KÝ SỰ KIỆN</h3>

                    <!-- Hiển thị thông báo thành công/lỗi -->
                    <?php if (isset($success)): ?>
                        <div class="alert alert-success">
                            <?php echo $success; ?>
                        </div>
                    <?php elseif (isset($error)): ?>
                        <div class="alert alert-error">
                            <?php echo $error; ?>
                        </div>
                    <?php endif; ?>

                    <!-- Form đặt sự kiện -->
                    <form method="POST" action="" class="event-form">
                        <!-- Trường tên sự kiện -->
                        <input type="text" name="tensukien" placeholder="Tên sự kiện *" required
                            class="form-input">

                        <!-- Trường họ tên người đại diện -->
                        <input type="text" name="hoten" placeholder="Họ tên người đại diện *"
                            value="<?php echo $_SESSION['HoTen'] ?? ''; ?>" required
                            class="form-input">

                        <!-- Trường số điện thoại -->
                        <input type="tel" name="sdt" placeholder="Số điện thoại *" required
                            class="form-input">

                        <!-- Trường email -->
                        <input type="email" name="email" placeholder="Email"
                            value="<?php echo $_SESSION['Email'] ?? ''; ?>"
                            class="form-input">

                        <!-- Trường số người tham dự -->
                        <input type="number" name="songuoi" placeholder="Số người tham dự *"
                            min="10" max="100" required
                            class="form-input">

                        <!-- Trường ngày sự kiện -->
                        <input type="date" name="ngaysukien" placeholder="Ngày sự kiện *"
                            min="<?php echo date('Y-m-d', strtotime('+3 days')); ?>" required
                            class="form-input">

                        <!-- Grid cho giờ bắt đầu và kết thúc -->
                        <div class="time-grid">
                            <input type="time" name="giobatdau" placeholder="Giờ bắt đầu *"
                                min="07:00" max="20:00" required
                                class="form-input">
                            <input type="time" name="gioketthuc" placeholder="Giờ kết thúc *"
                                min="09:00" max="22:00" required
                                class="form-input">
                        </div>

                        <!-- Dropdown chọn loại sự kiện -->
                        <select name="loaisukien" required class="form-select">
                            <option value="">Chọn loại sự kiện *</option>
                            <option value="sinh_nhat">Sinh nhật</option>
                            <option value="hoi_nghi">Hội nghị</option>
                            <option value="tiec_cuoi">Tiệc cưới</option>
                            <option value="gia_dinh">Gia đình</option>
                            <option value="khac">Khác</option>
                        </select>

                        <!-- Textarea yêu cầu đặc biệt -->
                        <textarea name="yeucau" placeholder="Yêu cầu đặc biệt (trang trí, menu, yêu cầu khác...)" rows="4"
                            class="form-textarea"></textarea>

                        <!-- Nút submit -->
                        <button type="submit" name="btnDatSuKien" class="submit-btn">
                            🎉 GỬI YÊU CẦU ĐẶT SỰ KIỆN
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Include file JavaScript -->
    <script src="js/datsukien.js"></script>
</body>

</html>

<?php include_once "includes/footer.php"; ?>