<?php
// 🔥 SỬA LỖI NOTICE: Chỉ gọi session_start() nếu session chưa được khởi động
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
// ----------------------------------------------------------------------

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
    // Lấy dữ liệu từ form
    $ho = trim($_POST['ho']);
    $ten = trim($_POST['ten']);
    $email = trim($_POST['email']);
    $soDienThoai = trim($_POST['sodienthoai']);
    $diaChi = trim($_POST['diachi']);
    
    // Validate dữ liệu
    if(empty($ho)) {
        $errors['ho'] = "Họ không được để trống";
    }
    
    if(empty($ten)) {
        $errors['ten'] = "Tên không được để trống";
    }
    
    if(empty($email)) {
        $errors['email'] = "Email không được để trống";
    } elseif(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Email không hợp lệ";
    }
    
    if(empty($soDienThoai)) {
        $errors['sodienthoai'] = "Số điện thoại không được để trống";
    } elseif(!preg_match('/^[0-9]{10,11}$/', $soDienThoai)) {
        $errors['sodienthoai'] = "Số điện thoại phải có 10-11 chữ số";
    }
    
    // Kiểm tra email có trùng với người khác không
    if(empty($errors)) {
        $checkEmailSQL = "SELECT MaUser FROM users WHERE Email = ? AND MaUser != ?";
        $stmt = mysqli_prepare($conn, $checkEmailSQL);
        if($stmt) {
            mysqli_stmt_bind_param($stmt, "si", $email, $maUser);
            mysqli_stmt_execute($stmt);
            $emailResult = mysqli_stmt_get_result($stmt);
            if(mysqli_num_rows($emailResult) > 0) {
                $errors['email'] = "Email này đã được sử dụng bởi tài khoản khác";
            }
            mysqli_stmt_close($stmt);
        }
    }
    
    // Kiểm tra số điện thoại có trùng với người khác không
    if(empty($errors)) {
        $checkPhoneSQL = "SELECT MaUser FROM users WHERE SDT = ? AND MaUser != ?";
        $stmt = mysqli_prepare($conn, $checkPhoneSQL);
        if($stmt) {
            mysqli_stmt_bind_param($stmt, "si", $soDienThoai, $maUser);
            mysqli_stmt_execute($stmt);
            $phoneResult = mysqli_stmt_get_result($stmt);
            if(mysqli_num_rows($phoneResult) > 0) {
                $errors['sodienthoai'] = "Số điện thoại này đã được sử dụng bởi tài khoản khác";
            }
            mysqli_stmt_close($stmt);
        }
    }
    
    // Nếu không có lỗi validation thì thực hiện cập nhật
    if(empty($errors)) {
        $updateSQL = "UPDATE users SET Ho = ?, Ten = ?, Email = ?, SDT = ?, DiaChi = ? WHERE MaUser = ?";
        $stmt = mysqli_prepare($conn, $updateSQL);
        if($stmt) {
            mysqli_stmt_bind_param($stmt, "sssssi", $ho, $ten, $email, $soDienThoai, $diaChi, $maUser);
            
            if(mysqli_stmt_execute($stmt)) {
                $success = "🎉 Cập nhật thông tin thành công!";
                // Cập nhật lại thông tin user
                $userInfo['Ho'] = $ho;
                $userInfo['Ten'] = $ten;
                $userInfo['Email'] = $email;
                $userInfo['SDT'] = $soDienThoai;
                $userInfo['DiaChi'] = $diaChi;
            } else {
                $errors['general'] = "❌ Cập nhật thông tin thất bại!";
            }
            mysqli_stmt_close($stmt);
        } else {
            $errors['general'] = "❌ Lỗi kết nối database!";
        }
    }
}
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
    <?php include_once "includes/header.php"; ?>
    <div class="container">
        <div class="page-header">
            <h1>Thông Tin Tài Khoản</h1>
            <p>Quản lý thông tin cá nhân của bạn</p>
        </div>

        <div class="content-container">
            <div class="profile-layout">
                <div class="profile-sidebar">
                    <div class="user-info">
                        <div class="user-avatar">
                            <div class="avatar-circle">
                                <?php 
                                    $initial = !empty($userInfo['Ten']) ? mb_substr($userInfo['Ten'], 0, 1) : 
                                              (!empty($userInfo['Ho']) ? mb_substr($userInfo['Ho'], 0, 1) : 'U');
                                    echo strtoupper($initial);
                                ?>
                            </div>
                        </div>
                        <div class="user-details">
                            <h3 class="user-name"><?php echo htmlspecialchars(($userInfo['Ho'] ?? '') . ' ' . ($userInfo['Ten'] ?? 'Người dùng')); ?></h3>
                            <p class="user-email"><?php echo htmlspecialchars($userInfo['Email'] ?? 'Chưa có email'); ?></p>
                            <p class="user-phone"><?php echo htmlspecialchars($userInfo['SDT'] ?? 'Chưa có số điện thoại'); ?></p>
                            <?php if(!empty($userInfo['DiaChi'])): ?>
                                <p class="user-address">📍 <?php echo htmlspecialchars($userInfo['DiaChi']); ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="profile-sidebar">
                <div class="sidebar-menu">
                    <a href="ThongTinTaiKhoan.php" class="menu-item active">
                        <span class="menu-icon">👤</span>
                        Thông tin tài khoản
                    </a>
                    <a href="DonHang.php" class="menu-item">
                        <span class="menu-icon">📦</span>
                        Đơn hàng của tôi
                    </a>
                    <a href="DanhGia.php" class="menu-item">
                        <span class="menu-icon">⭐</span>
                        Đánh giá
                    </a>
                    <a href="DieuKhoan.php" class="menu-item">
                        <span class="menu-icon">📄</span>
                        Điều khoản sử dụng
                    </a>
                    <a href="BaoMat.php" class="menu-item">
                        <span class="menu-icon">🔒</span>
                        Chính sách bảo mật
                    </a>
                </div>
            </div>
                </div>

                <div class="profile-content">
                    <div class="profile-card">
                        <div class="card-header">
                            <h3>Thông tin cá nhân</h3>
                        </div>
                        <div class="card-body">
                            <?php if(isset($errors['general'])): ?>
                                <div class="alert alert-error">
                                    <?php echo htmlspecialchars($errors['general']); ?>
                                </div>
                            <?php endif; ?>
                            
                            <?php if($success): ?>
                                <div class="alert alert-success">
                                    <?php echo htmlspecialchars($success); ?>
                                </div>
                            <?php endif; ?>
                            
                            <form method="POST" action="">
                                <div class="form-row">
                                    <div class="form-group">
                                        <label class="form-label">Họ *</label>
                                        <input type="text" name="ho" class="form-input <?php echo isset($errors['ho']) ? 'error' : ''; ?>" 
                                               value="<?php echo htmlspecialchars($userInfo['Ho'] ?? ''); ?>">
                                        <?php if(isset($errors['ho'])): ?>
                                            <span class="error-message"><?php echo htmlspecialchars($errors['ho']); ?></span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">Tên *</label>
                                        <input type="text" name="ten" class="form-input <?php echo isset($errors['ten']) ? 'error' : ''; ?>" 
                                               value="<?php echo htmlspecialchars($userInfo['Ten'] ?? ''); ?>">
                                        <?php if(isset($errors['ten'])): ?>
                                            <span class="error-message"><?php echo htmlspecialchars($errors['ten']); ?></span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group">
                                        <label class="form-label">Email *</label>
                                        <input type="email" name="email" class="form-input <?php echo isset($errors['email']) ? 'error' : ''; ?>" 
                                               value="<?php echo htmlspecialchars($userInfo['Email'] ?? ''); ?>">
                                        <?php if(isset($errors['email'])): ?>
                                            <span class="error-message"><?php echo htmlspecialchars($errors['email']); ?></span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">Số điện thoại *</label>
                                        <input type="tel" name="sodienthoai" class="form-input <?php echo isset($errors['sodienthoai']) ? 'error' : ''; ?>" 
                                               value="<?php echo htmlspecialchars($userInfo['SDT'] ?? ''); ?>">
                                        <?php if(isset($errors['sodienthoai'])): ?>
                                            <span class="error-message"><?php echo htmlspecialchars($errors['sodienthoai']); ?></span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group full-width">
                                        <label class="form-label">Địa chỉ</label>
                                        <textarea name="diachi" class="form-input" rows="3"><?php echo htmlspecialchars($userInfo['DiaChi'] ?? ''); ?></textarea>
                                    </div>
                                </div>
                                <div class="form-actions">
                                    <button type="submit" name="btnUpdate" class="btn-primary">Cập nhật thông tin</button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="profile-card">
                        <div class="card-header">
                            <h3>Thống kê đơn hàng</h3>
                        </div>
                        <div class="card-body">
                            <?php
                            // Truy vấn thống kê đơn hàng thực tế
                            $statsSQL = "
                                SELECT 
                                    COUNT(*) as total_orders,
                                    COALESCE(SUM(TongTien), 0) as total_spent,
                                    (SELECT COUNT(*) FROM DanhGia WHERE MaUser = ?) as total_reviews
                                FROM DonHang 
                                WHERE MaUser = ?
                            ";
                            $stmt = mysqli_prepare($conn, $statsSQL);
                            $stats = ['total_orders' => 0, 'total_spent' => 0, 'total_reviews' => 0];
                            
                            if($stmt) {
                                mysqli_stmt_bind_param($stmt, "ii", $maUser, $maUser);
                                mysqli_stmt_execute($stmt);
                                $statsResult = mysqli_stmt_get_result($stmt);
                                if($statsData = mysqli_fetch_assoc($statsResult)) {
                                    $stats = $statsData;
                                }
                                mysqli_stmt_close($stmt);
                            }
                            ?>
                            <div class="stats-grid">
                                <div class="stat-item">
                                    <div class="stat-icon">📦</div>
                                    <div class="stat-info">
                                        <div class="stat-number"><?php echo $stats['total_orders']; ?></div>
                                        <div class="stat-label">Đơn hàng</div>
                                    </div>
                                </div>
                                <div class="stat-item">
                                    <div class="stat-icon">⭐</div>
                                    <div class="stat-info">
                                        <div class="stat-number"><?php echo $stats['total_reviews']; ?></div>
                                        <div class="stat-label">Đánh giá</div>
                                    </div>
                                </div>
                                <div class="stat-item">
                                    <div class="stat-icon">💰</div>
                                    <div class="stat-info">
                                        <div class="stat-number"><?php echo number_format($stats['total_spent'], 0, ',', '.'); ?>đ</div>
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
    
    <script src="js/ThongTinTaiKhoan.js"></script>
</body>
</html>

<?php include_once "includes/footer.php"; ?>