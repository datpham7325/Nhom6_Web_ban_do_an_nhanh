<?php
// Include file header
include_once "includes/header.php";

// Kiểm tra xem user đã đăng nhập chưa
if (!isset($_SESSION['loggedin'])) {
    // Nếu chưa đăng nhập, chuyển hướng hoặc thông báo
    echo "<script>alert('Vui lòng đăng nhập để đặt sự kiện'); window.location.href='DangNhap.php';</script>";
    exit();
}

// 🔥 KẾT NỐI DATABASE
include_once("includes/myenv.php");
$conn = mysqli_connect($db_host, $db_user, $db_password, $db_db, $db_port);

// Kiểm tra kết nối
if (!$conn) {
    die("❌ Lỗi kết nối database: " . mysqli_connect_error());
}

$maUser = $_SESSION['MaUser'];
$success = "";
$error = "";

// 🔥 SỬA LỖI: Kiểm tra dựa trên hidden field 'is_submit' thay vì nút bấm
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['is_submit'])) {

    // Lấy dữ liệu từ form
    $tenSuKien = $_POST['tensukien'] ?? '';
    $hoTen = $_POST['hoten'] ?? '';
    $sdt = $_POST['sdt'] ?? '';
    $email = $_POST['email'] ?? '';
    $soNguoi = intval($_POST['songuoi'] ?? 0);
    $ngaySuKien = $_POST['ngaysukien'] ?? '';
    $gioBatDau = $_POST['giobatdau'] ?? '';
    $gioKetThuc = $_POST['gioketthuc'] ?? '';
    $loaiSuKien = $_POST['loaisukien'] ?? '';
    $yeuCau = $_POST['yeucau'] ?? '';

    // Kiểm tra dữ liệu cơ bản
    if (empty($tenSuKien) || empty($hoTen) || empty($sdt) || empty($ngaySuKien)) {
        $error = "❌ Vui lòng điền đầy đủ các thông tin bắt buộc.";
    } else {
        try {
            // Câu lệnh SQL chuẩn
            $insertSQL = "INSERT INTO DatSuKien (MaUser, TenSuKien, HoTenNguoiDaiDien, SDT, Email, SoNguoi, NgaySuKien, GioBatDau, GioKetThuc, LoaiSuKien, YeuCauDacBiet) 
                         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

            $stmt = mysqli_prepare($conn, $insertSQL);

            if ($stmt) {
                // Bind param: i=integer, s=string
                // MaUser(i), Ten(s), HoTen(s), SDT(s), Email(s), SoNguoi(i), Ngay(s), GioBD(s), GioKT(s), Loai(s), YeuCau(s)
                // Tổng: issssisssss
                mysqli_stmt_bind_param(
                    $stmt,
                    "issssisssss",
                    $maUser,
                    $tenSuKien,
                    $hoTen,
                    $sdt,
                    $email,
                    $soNguoi,
                    $ngaySuKien,
                    $gioBatDau,
                    $gioKetThuc,
                    $loaiSuKien,
                    $yeuCau
                );

                if (mysqli_stmt_execute($stmt)) {
                    $success = "✅ Đặt sự kiện thành công! Chúng tôi sẽ liên hệ lại sớm.";
                    // Reset biến POST để không hiện lại dữ liệu cũ
                    $_POST = array();
                } else {
                    $error = "❌ Lỗi thực thi SQL: " . mysqli_stmt_error($stmt);
                }
                mysqli_stmt_close($stmt);
            } else {
                $error = "❌ Lỗi chuẩn bị SQL: " . mysqli_error($conn);
            }
        } catch (Exception $e) {
            $error = "❌ Lỗi hệ thống: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đặt Sự Kiện - Jollibee</title>
    <link rel="stylesheet" href="css/datsukien.css">
</head>

<body>
    <div class="container">
        <div class="page-header">
            <h1>ĐẶT SỰ KIỆN</h1>
            <p>Tổ chức sự kiện đáng nhớ tại Jollibee</p>
        </div>

        <div class="content-container">
            <div class="main-grid">
                <div class="info-column">
                    <h3 class="section-title">THÔNG TIN SỰ KIỆN</h3>
                    <div class="info-item">
                        <h4 class="info-title">🎉 SINH NHẬT</h4>
                        <p>Trang trí theo chủ đề + Bánh sinh nhật đặc biệt.</p>

                        <h4 class="info-title">💼 HỘI NGHỊ</h4>
                        <p>Không gian chuyên nghiệp + Menu tiệc.</p>

                        <h4 class="info-title">💒 TIỆC CƯỚI</h4>
                        <p>Trang trí lãng mạn + Menu cao cấp.</p>
                    </div>
                    <div class="contact-box">
                        <h4 class="contact-title">📞 LIÊN HỆ NGAY</h4>
                        <p class="contact-item"><strong>Hotline:</strong> 1900 1234</p>
                    </div>
                </div>

                <div class="form-column">
                    <h3 class="section-title">ĐĂNG KÝ SỰ KIỆN</h3>

                    <?php if (!empty($success)): ?>
                        <div class="alert alert-success"><?php echo $success; ?></div>
                    <?php elseif (!empty($error)): ?>
                        <div class="alert alert-error"><?php echo $error; ?></div>
                    <?php endif; ?>

                    <form method="POST" action="" class="event-form" id="eventForm">

                        <input type="hidden" name="is_submit" value="1">

                        <input type="text" name="tensukien" placeholder="Tên sự kiện *"
                            value="<?php echo htmlspecialchars($_POST['tensukien'] ?? ''); ?>" required class="form-input">

                        <input type="text" name="hoten" placeholder="Họ tên người đại diện *"
                            value="<?php echo htmlspecialchars($_POST['hoten'] ?? $_SESSION['HoTen'] ?? ''); ?>" required class="form-input">

                        <input type="tel" name="sdt" placeholder="Số điện thoại *"
                            value="<?php echo htmlspecialchars($_POST['sdt'] ?? ''); ?>" required class="form-input">

                        <input type="email" name="email" placeholder="Email"
                            value="<?php echo htmlspecialchars($_POST['email'] ?? $_SESSION['Email'] ?? ''); ?>" class="form-input">

                        <input type="number" name="songuoi" placeholder="Số người (10-100) *"
                            value="<?php echo htmlspecialchars($_POST['songuoi'] ?? ''); ?>" min="10" max="100" required class="form-input">

                        <input type="date" name="ngaysukien" placeholder="Ngày sự kiện *"
                            value="<?php echo htmlspecialchars($_POST['ngaysukien'] ?? ''); ?>"
                            min="<?php echo date('Y-m-d', strtotime('+3 days')); ?>" required class="form-input">

                        <div class="time-grid">
                            <div>
                                <label>Giờ bắt đầu *</label>
                                <input type="time" name="giobatdau" value="<?php echo htmlspecialchars($_POST['giobatdau'] ?? ''); ?>" required class="form-input">
                            </div>
                            <div>
                                <label>Giờ kết thúc *</label>
                                <input type="time" name="gioketthuc" value="<?php echo htmlspecialchars($_POST['gioketthuc'] ?? ''); ?>" required class="form-input">
                            </div>
                        </div>

                        <select name="loaisukien" required class="form-select">
                            <option value="">Chọn loại sự kiện *</option>
                            <option value="sinh_nhat" <?php echo (($_POST['loaisukien'] ?? '') == 'sinh_nhat') ? 'selected' : ''; ?>>🎉 Sinh nhật</option>
                            <option value="hoi_nghi" <?php echo (($_POST['loaisukien'] ?? '') == 'hoi_nghi') ? 'selected' : ''; ?>>💼 Hội nghị</option>
                            <option value="tiec_cuoi" <?php echo (($_POST['loaisukien'] ?? '') == 'tiec_cuoi') ? 'selected' : ''; ?>>💒 Tiệc cưới</option>
                            <option value="gia_dinh" <?php echo (($_POST['loaisukien'] ?? '') == 'gia_dinh') ? 'selected' : ''; ?>>👨‍👩‍👧‍👦 Gia đình</option>
                            <option value="khac" <?php echo (($_POST['loaisukien'] ?? '') == 'khac') ? 'selected' : ''; ?>>❓ Khác</option>
                        </select>

                        <textarea name="yeucau" placeholder="Yêu cầu đặc biệt..." rows="4" class="form-textarea"><?php echo htmlspecialchars($_POST['yeucau'] ?? ''); ?></textarea>

                        <button type="submit" name="btnDatSuKien" class="submit-btn" id="submitBtn">
                            🎉 GỬI YÊU CẦU
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script src="js/datsukien.js"></script>
</body>

</html>
<?php
// Đóng kết nối ở cuối file
if (isset($conn)) mysqli_close($conn);
include_once "includes/footer.php";
?>