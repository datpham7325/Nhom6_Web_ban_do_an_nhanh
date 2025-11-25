<?php
include_once "includes/header.php";

// Kiểm tra trạng thái đăng nhập của người dùng
if(!isset($_SESSION['loggedin'])) {
    header("Location: DangNhap.php");
    exit();
}

$maUser = $_SESSION['MaUser'];
$errors = [];
$success = '';

// Truy vấn lấy thông tin người dùng từ database
$userSQL = "SELECT * FROM users WHERE MaUser = ?";
$stmt = mysqli_prepare($conn, $userSQL);
if($stmt) {
    mysqli_stmt_bind_param($stmt, "i", $maUser);
    mysqli_stmt_execute($stmt);
    $userResult = mysqli_stmt_get_result($stmt);
    $userInfo = mysqli_fetch_assoc($userResult);
    mysqli_stmt_close($stmt);
} else {
    $errors['general'] = "Lỗi kết nối database!";
}

// Xử lý cập nhật thông tin khi form được submit
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['btnUpdate'])) {
    $hoTen = trim($_POST['hoten']);
    $email = trim($_POST['email']);
    $soDienThoai = trim($_POST['sodienthoai']);
    $diaChi = trim($_POST['diachi']);
    
    // Validate dữ liệu trường họ tên
    if(empty($hoTen)) {
        $errors['hoten'] = "Họ tên không được để trống";
    } elseif(strlen($hoTen) < 2 || strlen($hoTen) > 50) {
        $errors['hoten'] = "Họ tên phải từ 2 đến 50 ký tự";
    }
    
    // Validate dữ liệu trường email
    if(empty($email)) {
        $errors['email'] = "Email không được để trống";
    } elseif(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Email không hợp lệ";
    }
    
    // Validate dữ liệu trường số điện thoại
    if(empty($soDienThoai)) {
        $errors['sodienthoai'] = "Số điện thoại không được để trống";
    } elseif(!preg_match('/^[0-9]{10,11}$/', $soDienThoai)) {
        $errors['sodienthoai'] = "Số điện thoại phải có 10-11 chữ số";
    }
    
    // Validate dữ liệu trường địa chỉ
    if(!empty($diaChi) && strlen($diaChi) > 200) {
        $errors['diachi'] = "Địa chỉ không được quá 200 ký tự";
    }
    
    // Nếu không có lỗi validation thì thực hiện cập nhật
    if(empty($errors)) {
        $updateSQL = "UPDATE users SET HoTen = ?, Email = ?, SoDienThoai = ?, DiaChi = ? WHERE MaUser = ?";
        $stmt = mysqli_prepare($conn, $updateSQL);
        if($stmt) {
            mysqli_stmt_bind_param($stmt, "ssssi", $hoTen, $email, $soDienThoai, $diaChi, $maUser);
            
            if(mysqli_stmt_execute($stmt)) {
                $success = "Cập nhật thông tin thành công!";
                // Cập nhật lại thông tin user trong biến để hiển thị
                $userInfo['HoTen'] = $hoTen;
                $userInfo['Email'] = $email;
                $userInfo['SoDienThoai'] = $soDienThoai;
                $userInfo['DiaChi'] = $diaChi;
            } else {
                $errors['general'] = "Cập nhật thông tin thất bại!";
            }
            mysqli_stmt_close($stmt);
        } else {
            $errors['general'] = "Lỗi kết nối database!";
        }
    }
}

// Hàm kiểm tra và xử lý địa chỉ nếu là JSON OTP
// Đây là giải pháp tạm thời để xử lý lỗi hiển thị JSON OTP trong ô địa chỉ
function getCleanAddress($address) {
    if (empty($address)) {
        return '';
    }
    
    // Kiểm tra nếu là chuỗi JSON bắt đầu bằng {"otp"
    if (is_string($address) && strpos($address, '{"otp"') === 0) {
        $decoded = json_decode($address, true);
        // Nếu decode thành công và có cấu trúc OTP, trả về chuỗi rỗng
        if (is_array($decoded) && isset($decoded['otp']) && isset($decoded['expires'])) {
            return '';
        }
    }
    
    return $address;
}

// Làm sạch địa chỉ trước khi hiển thị - đảm bảo không hiển thị JSON OTP
$cleanDiaChi = getCleanAddress($userInfo['DiaChi'] ?? '');
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thông Tin Tài Khoản</title>
    <link rel="stylesheet" href="css/ThongTinTaiKhoan.css">
</head>
<body>
    <div class="container">
        <!-- Header trang thông tin tài khoản -->
        <div class="page-header">
            <h1>Thông Tin Tài Khoản</h1>
            <p>Quản lý thông tin cá nhân của bạn</p>
        </div>

        <div class="content-container">
            <div class="profile-layout">
                <!-- Sidebar menu điều hướng -->
                <div class="profile-sidebar">
                    <div class="user-info">
                        <div class="user-avatar">
                            <div class="avatar-circle">
                                <!-- Hiển thị chữ cái đầu của tên người dùng -->
                                <?php 
                                    $initial = !empty($userInfo['HoTen']) ? mb_substr($userInfo['HoTen'], 0, 1) : 'U';
                                    echo strtoupper($initial);
                                ?>
                            </div>
                        </div>
                        <div class="user-details">
                            <h3 class="user-name"><?php echo htmlspecialchars($userInfo['HoTen'] ?? 'Người dùng'); ?></h3>
                            <p class="user-email"><?php echo htmlspecialchars($userInfo['Email'] ?? 'Chưa có email'); ?></p>
                        </div>
                    </div>
                    <div class="sidebar-menu">
                        <!-- Menu item active - trang hiện tại -->
                        <a href="ThongTinTaiKhoan.php" class="menu-item active">
                            <span class="menu-icon">👤</span>
                            <span class="menu-text">Thông tin tài khoản</span>
                        </a>
                        <a href="DonHang.php" class="menu-item">
                            <span class="menu-icon">📦</span>
                            <span class="menu-text">Đơn hàng của tôi</span>
                        </a>
                        <a href="DanhGia.php" class="menu-item">
                            <span class="menu-icon">⭐</span>
                            <span class="menu-text">Đánh giá</span>
                        </a>
                        <a href="DieuKhoan.php" class="menu-item">
                            <span class="menu-icon">📄</span>
                            <span class="menu-text">Điều khoản sử dụng</span>
                        </a>
                        <a href="BaoMat.php" class="menu-item">
                            <span class="menu-icon">🔒</span>
                            <span class="menu-text">Chính sách bảo mật</span>
                        </a>
                    </div>
                </div>

                <!-- Nội dung chính của trang -->
                <div class="profile-content">
                    <!-- Card thông tin cá nhân -->
                    <div class="profile-card">
                        <div class="card-header">
                            <h3>Thông tin cá nhân</h3>
                        </div>
                        <div class="card-body">
                            <!-- Hiển thị thông báo lỗi chung -->
                            <?php if(isset($errors['general'])): ?>
                                <div class="alert alert-error">
                                    <?php echo htmlspecialchars($errors['general']); ?>
                                </div>
                            <?php endif; ?>
                            
                            <!-- Hiển thị thông báo thành công -->
                            <?php if($success): ?>
                                <div class="alert alert-success">
                                    <?php echo htmlspecialchars($success); ?>
                                </div>
                            <?php endif; ?>
                            
                            <!-- Form cập nhật thông tin cá nhân -->
                            <form method="POST" action="" id="profileForm">
                                <div class="form-row">
                                    <div class="form-group">
                                        <label class="form-label">Họ tên *</label>
                                        <input type="text" name="hoten" class="form-input <?php echo isset($errors['hoten']) ? 'error' : ''; ?>" 
                                               value="<?php echo htmlspecialchars($userInfo['HoTen'] ?? ''); ?>"
                                               placeholder="Nhập họ tên của bạn">
                                        <!-- Hiển thị lỗi validation cho trường họ tên -->
                                        <?php if(isset($errors['hoten'])): ?>
                                            <span class="error-message show"><?php echo htmlspecialchars($errors['hoten']); ?></span>
                                        <?php else: ?>
                                            <span class="error-message" id="hoten-error"></span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">Email *</label>
                                        <input type="email" name="email" class="form-input <?php echo isset($errors['email']) ? 'error' : ''; ?>" 
                                               value="<?php echo htmlspecialchars($userInfo['Email'] ?? ''); ?>"
                                               placeholder="Nhập email của bạn">
                                        <!-- Hiển thị lỗi validation cho trường email -->
                                        <?php if(isset($errors['email'])): ?>
                                            <span class="error-message show"><?php echo htmlspecialchars($errors['email']); ?></span>
                                        <?php else: ?>
                                            <span class="error-message" id="email-error"></span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group">
                                        <label class="form-label">Số điện thoại *</label>
                                        <input type="tel" name="sodienthoai" class="form-input <?php echo isset($errors['sodienthoai']) ? 'error' : ''; ?>" 
                                               value="<?php echo htmlspecialchars($userInfo['SoDienThoai'] ?? ''); ?>"
                                               placeholder="Nhập số điện thoại">
                                        <!-- Hiển thị lỗi validation cho trường số điện thoại -->
                                        <?php if(isset($errors['sodienthoai'])): ?>
                                            <span class="error-message show"><?php echo htmlspecialchars($errors['sodienthoai']); ?></span>
                                        <?php else: ?>
                                            <span class="error-message" id="sodienthoai-error"></span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">Địa chỉ</label>
                                        <!-- SỬA QUAN TRỌNG: Sử dụng $cleanDiaChi đã được xử lý thay vì $userInfo['DiaChi'] trực tiếp -->
                                        <!-- Điều này ngăn chặn việc hiển thị JSON OTP trong ô địa chỉ -->
                                        <input type="text" name="diachi" class="form-input <?php echo isset($errors['diachi']) ? 'error' : ''; ?>" 
                                               value="<?php echo htmlspecialchars($cleanDiaChi); ?>"
                                               placeholder="Nhập địa chỉ của bạn">
                                        <!-- Hiển thị lỗi validation cho trường địa chỉ -->
                                        <?php if(isset($errors['diachi'])): ?>
                                            <span class="error-message show"><?php echo htmlspecialchars($errors['diachi']); ?></span>
                                        <?php else: ?>
                                            <span class="error-message" id="diachi-error"></span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="form-actions">
                                    <!-- Nút submit cập nhật thông tin -->
                                    <button type="submit" name="btnUpdate" class="btn-primary">Cập nhật thông tin</button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Card thống kê đơn hàng -->
                    <div class="profile-card">
                        <div class="card-header">
                            <h3>Thống kê đơn hàng</h3>
                        </div>
                        <div class="card-body">
                            <div class="stats-grid">
                                <!-- Thống kê số đơn hàng -->
                                <div class="stat-item">
                                    <div class="stat-icon">📦</div>
                                    <div class="stat-info">
                                        <div class="stat-number">5</div>
                                        <div class="stat-label">Đơn hàng</div>
                                    </div>
                                </div>
                                <!-- Thống kê số đánh giá -->
                                <div class="stat-item">
                                    <div class="stat-icon">⭐</div>
                                    <div class="stat-info">
                                        <div class="stat-number">12</div>
                                        <div class="stat-label">Đánh giá</div>
                                    </div>
                                </div>
                                <!-- Thống kê tổng chi tiêu -->
                                <div class="stat-item">
                                    <div class="stat-icon">💰</div>
                                    <div class="stat-info">
                                        <div class="stat-number">2.5M</div>
                                        <div class="stat-label">Đã chi tiêu</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript xử lý frontend -->
    <script src="js/ThongTinTaiKhoan.js"></script>
</body>
</html>

<?php include_once "includes/footer.php"; ?>