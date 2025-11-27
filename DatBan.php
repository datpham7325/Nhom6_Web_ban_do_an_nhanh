<?php
// Include file header
include_once "includes/header.php";

// Kiểm tra xem user đã đăng nhập chưa
if (!isset($_SESSION['loggedin'])) {
    echo "<script>alert('Vui lòng đăng nhập để đặt bàn'); window.location.href='DangNhap.php';</script>";
    exit();
}

// 🔥 KẾT NỐI DATABASE RIÊNG (Đảm bảo kết nối ổn định)
include_once("includes/myenv.php");
$conn = mysqli_connect($db_host, $db_user, $db_password, $db_db, $db_port);

if (!$conn) {
    die("❌ Lỗi kết nối database: " . mysqli_connect_error());
}

// Lấy mã user từ session
$maUser = $_SESSION['MaUser'];
$success = "";
$error = "";

// 🔥 SỬA LỖI: Kiểm tra dựa trên hidden field 'is_submit' thay vì nút bấm
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['is_submit'])) {
    
    // Lấy dữ liệu từ form
    $hoTen = $_POST['hoten'] ?? '';
    $sdt = $_POST['sdt'] ?? '';
    $soNguoi = intval($_POST['songuoi'] ?? 0);
    $ngayDat = $_POST['ngaydat'] ?? '';
    $gioDat = $_POST['giodat'] ?? '';
    $ghiChu = $_POST['ghichu'] ?? '';

    // Kiểm tra dữ liệu cơ bản
    if (empty($hoTen) || empty($sdt) || empty($ngayDat) || empty($gioDat)) {
        $error = "❌ Vui lòng điền đầy đủ thông tin bắt buộc.";
    } else {
        try {
            // Sử dụng Prepared Statement để an toàn và tránh lỗi quote
            $insertSQL = "INSERT INTO DatBan (MaUser, HoTen, SDT, SoNguoi, NgayDat, GioDat, GhiChu) 
                         VALUES (?, ?, ?, ?, ?, ?, ?)";

            $stmt = mysqli_prepare($conn, $insertSQL);

            if ($stmt) {
                // Bind param: i=integer, s=string
                // Thứ tự: MaUser(i), HoTen(s), SDT(s), SoNguoi(i), NgayDat(s), GioDat(s), GhiChu(s)
                mysqli_stmt_bind_param($stmt, "ississs", 
                    $maUser, 
                    $hoTen, 
                    $sdt, 
                    $soNguoi, 
                    $ngayDat, 
                    $gioDat, 
                    $ghiChu
                );

                if (mysqli_stmt_execute($stmt)) {
                    $success = "✅ Đặt bàn thành công! Chúng tôi sẽ liên hệ xác nhận sớm nhất.";
                    // Reset biến POST để tránh hiện lại dữ liệu cũ
                    $_POST = array();
                } else {
                    $error = "❌ Lỗi thực thi: " . mysqli_stmt_error($stmt);
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
    <title>Đặt bàn - Jollibee</title>
    <link rel="stylesheet" href="css/datban.css">
</head>

<body>
    <div class="container">
        <div class="page-header">
            <h1>ĐẶT BÀN TRƯỚC</h1>
            <p>Đảm bảo chỗ ngồi tốt nhất cho bạn</p>
        </div>

        <div class="content-container">
            <div class="main-grid">
                <div class="info-column">
                    <h3 class="section-title">THÔNG TIN ĐẶT BÀN</h3>

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

                    <div class="note-box">
                        <h4 class="note-title">💡 LƯU Ý</h4>
                        <p class="note-item">• Vui lòng đến đúng giờ đã đặt</p>
                        <p class="note-item">• Bàn sẽ được giữ tối đa 15 phút</p>
                        <p class="note-item">• Hủy đặt bàn trước 1 giờ</p>
                    </div>
                </div>

                <div class="form-column">
                    <h3 class="section-title">ĐẶT BÀN NGAY</h3>

                    <?php if (!empty($success)): ?>
                        <div class="alert alert-success">
                            <?php echo $success; ?>
                        </div>
                    <?php elseif (!empty($error)): ?>
                        <div class="alert alert-error">
                            <?php echo $error; ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="" class="booking-form">
                        
                        <input type="hidden" name="is_submit" value="1">

                        <input type="text" name="hoten" placeholder="Họ tên *"
                            value="<?php echo htmlspecialchars($_POST['hoten'] ?? $_SESSION['HoTen'] ?? ''); ?>" required
                            class="form-input">

                        <input type="tel" name="sdt" placeholder="Số điện thoại *" 
                            value="<?php echo htmlspecialchars($_POST['sdt'] ?? ''); ?>" required
                            class="form-input">

                        <select name="songuoi" required class="form-select">
                            <option value="">Chọn số người *</option>
                            <?php for ($i = 1; $i <= 10; $i++): ?>
                                <option value="<?php echo $i; ?>" <?php echo (($_POST['songuoi'] ?? '') == $i) ? 'selected' : ''; ?>>
                                    <?php echo $i; ?> người
                                </option>
                            <?php endfor; ?>
                        </select>

                        <input type="date" name="ngaydat" placeholder="Ngày đặt *"
                            value="<?php echo htmlspecialchars($_POST['ngaydat'] ?? ''); ?>"
                            min="<?php echo date('Y-m-d'); ?>" required
                            class="form-input">

                        <input type="time" name="giodat" placeholder="Giờ đặt *"
                            value="<?php echo htmlspecialchars($_POST['giodat'] ?? ''); ?>"
                            min="07:00" max="22:00" required
                            class="form-input">

                        <textarea name="ghichu" placeholder="Ghi chú (yêu cầu đặc biệt, vị trí mong muốn...)" rows="4"
                            class="form-textarea"><?php echo htmlspecialchars($_POST['ghichu'] ?? ''); ?></textarea>

                        <button type="submit" name="btnDatBan" class="submit-btn">
                            🍽️ ĐẶT BÀN NGAY
                        </button>
                    </form>
                </div>
            </div>

            <div class="restaurant-space">
                <h3 class="section-title center">KHÔNG GIAN NHÀ HÀNG</h3>
                <div class="space-container">
                    <p class="space-description">Jollibee mang đến không gian ấm cúng, phù hợp cho mọi dịp:</p>
                    <div class="occasion-grid">
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

    <script src="js/datban.js"></script>
</body>

</html>

<?php 
// Đóng kết nối
if(isset($conn)) mysqli_close($conn);
include_once "includes/footer.php"; 
?>