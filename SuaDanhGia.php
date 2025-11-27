<?php
ob_start();

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include_once "includes/header.php";

// 1. Kiểm tra đăng nhập
if (!isset($_SESSION['loggedin'])) {
    header("Location: DangNhap.php");
    exit();
}

$maUser = $_SESSION['MaUser'];
$maDanhGia = $_GET['id'] ?? 0;

if (!$maDanhGia) {
    header("Location: DanhGia.php");
    exit();
}

// 2. Lấy thông tin đánh giá
$reviewSQL = "SELECT dg.*, dh.NgayDat, dh.TongTien 
              FROM DanhGia dg 
              JOIN DonHang dh ON dg.MaDonHang = dh.MaDonHang 
              WHERE dg.MaDanhGia = ? AND dg.MaUser = ?";
$stmt = mysqli_prepare($conn, $reviewSQL);

if ($stmt === false) {
    $_SESSION['review_error'] = "Lỗi hệ thống: " . mysqli_error($conn);
    header("Location: DanhGia.php");
    exit();
}

mysqli_stmt_bind_param($stmt, "ii", $maDanhGia, $maUser);
mysqli_stmt_execute($stmt);
$reviewResult = mysqli_stmt_get_result($stmt);
$review = mysqli_fetch_assoc($reviewResult);
mysqli_stmt_close($stmt);

if (!$review) {
    $_SESSION['review_error'] = "Không tìm thấy đánh giá hoặc bạn không có quyền sửa!";
    header("Location: DanhGia.php");
    exit();
}

// 3. Xử lý cập nhật (POST)
$error = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $soSao = intval($_POST['soSao'] ?? 0);
    $noiDung = trim($_POST['noiDung'] ?? '');

    // Validate
    if ($soSao < 1 || $soSao > 5) {
        $error = "Vui lòng chọn số sao từ 1 đến 5";
    } elseif (empty($noiDung)) {
        $error = "Vui lòng nhập nội dung đánh giá";
    } elseif (strlen($noiDung) > 500) {
        $error = "Nội dung quá dài (tối đa 500 ký tự)";
    } else {
        // Cập nhật vào DB (Đã bỏ check < 10 ký tự)
        $updateSQL = "UPDATE DanhGia SET Diem = ?, NoiDung = ? WHERE MaDanhGia = ? AND MaUser = ?";
        $stmt = mysqli_prepare($conn, $updateSQL);
        mysqli_stmt_bind_param($stmt, "isii", $soSao, $noiDung, $maDanhGia, $maUser);

        if (mysqli_stmt_execute($stmt)) {
            $_SESSION['review_success'] = "Đã cập nhật đánh giá thành công!";
            header("Location: DanhGia.php");
            exit();
        } else {
            $error = "Lỗi cập nhật: " . mysqli_error($conn);
        }
        mysqli_stmt_close($stmt);
    }
}

// Giữ lại giá trị cũ nếu form lỗi
$currentSoSao = $_POST['soSao'] ?? ($review['Diem'] ?? 0);
$currentNoiDung = $_POST['noiDung'] ?? ($review['NoiDung'] ?? '');
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sửa Đánh Giá</title>
    <link rel="stylesheet" href="css/suadanhgia.css">
    
    <style>
        .no-image {
            background: rgba(255,255,255,0.2);
            color: white;
            display: flex; align-items: center; justify-content: center;
            font-size: 40px; border: 3px solid rgba(255,255,255,0.3);
            width: 80px; height: 80px; border-radius: 10px;
        }
        
        .star-rating { 
            display: flex; 
            flex-direction: row-reverse; 
            justify-content: flex-end; 
            gap: 5px;
        }
        .star-rating input { display: none; }
        
        .star-label {
            font-size: 40px; color: #ddd; cursor: pointer; transition: 0.2s;
            line-height: 1;
        }
        
        .star-rating input:checked ~ .star-label,
        .star-label:hover,
        .star-label:hover ~ .star-label {
            color: #f1c40f; 
            transform: scale(1.1);
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="page-header">
            <h1>SỬA ĐÁNH GIÁ</h1>
            <p>Đơn hàng #<?php echo $review['MaDonHang']; ?></p>
        </div>

        <div class="content-container">
            <div class="product-card">
                <div class="product-header">
                    <div class="product-info">
                        <h2>Đơn hàng #<?php echo $review['MaDonHang']; ?></h2>
                        <p>Ngày đặt: <?php echo date('d/m/Y H:i', strtotime($review['NgayDat'])); ?></p>
                        <p>Tổng tiền: <?php echo number_format($review['TongTien'], 0, ',', '.'); ?> đ</p>
                    </div>
                    <div class="product-image">
                        <div class="no-image">🧾</div>
                    </div>
                </div>
            </div>

            <div class="review-form-card">
                <div class="form-header">
                    <h3>Chỉnh sửa nội dung</h3>
                    <p>Cập nhật trải nghiệm của bạn</p>
                </div>

                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>

                <form method="POST" class="review-form">
                    <div class="form-group">
                        <label class="form-label">Đánh giá sao:</label>
                        <div class="star-rating">
                            <input type="radio" id="star5" name="soSao" value="5" <?php echo $currentSoSao == 5 ? 'checked' : ''; ?>>
                            <label for="star5" class="star-label">★</label>
                            
                            <input type="radio" id="star4" name="soSao" value="4" <?php echo $currentSoSao == 4 ? 'checked' : ''; ?>>
                            <label for="star4" class="star-label">★</label>
                            
                            <input type="radio" id="star3" name="soSao" value="3" <?php echo $currentSoSao == 3 ? 'checked' : ''; ?>>
                            <label for="star3" class="star-label">★</label>
                            
                            <input type="radio" id="star2" name="soSao" value="2" <?php echo $currentSoSao == 2 ? 'checked' : ''; ?>>
                            <label for="star2" class="star-label">★</label>
                            
                            <input type="radio" id="star1" name="soSao" value="1" <?php echo $currentSoSao == 1 ? 'checked' : ''; ?>>
                            <label for="star1" class="star-label">★</label>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Nội dung chi tiết:</label>
                        <textarea name="noiDung" class="form-textarea" rows="5" placeholder="Nhập nội dung đánh giá..." required><?php echo htmlspecialchars($currentNoiDung); ?></textarea>
                        </div>

                    <div class="form-actions">
                        <a href="DanhGia.php" class="btn-secondary">
                            <span class="btn-icon">←</span> Quay lại
                        </a>
                        <button type="submit" class="btn-primary">
                            <span class="btn-icon">💾</span> Lưu thay đổi
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <?php include_once "includes/footer.php"; ?>
</body>
</html>
<?php ob_end_flush(); ?>