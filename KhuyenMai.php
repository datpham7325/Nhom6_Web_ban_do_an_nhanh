<?php 
// 1. Include Header (Kết nối DB + Session start + Lấy số lượng giỏ hàng CŨ)
include_once "includes/header.php";
include_once "includes/myenv.php";

// Đảm bảo kết nối DB
if (!isset($conn)) {
    $conn = mysqli_connect($db_host, $db_user, $db_password, $db_db, $db_port);
}

// 2. XỬ LÝ THÊM VÀO GIỎ HÀNG
if(isset($_POST['add_to_cart'])) {
    // Kiểm tra đăng nhập
    if(!isset($_SESSION['loggedin'])) {
        echo "<script>
            alert('Vui lòng đăng nhập để mua hàng!'); 
            window.location.href='DangNhap.php';
        </script>";
        exit;
    }

    $maUser = $_SESSION['MaUser'];
    $maBienThe = $_POST['ma_bien_the'];
    $soLuong = 1;

    // Kiểm tra sản phẩm đã có trong giỏ chưa
    $checkCart = "SELECT * FROM GioHang WHERE MaUser = ? AND MaBienThe = ?";
    $stmt = mysqli_prepare($conn, $checkCart);
    mysqli_stmt_bind_param($stmt, "ii", $maUser, $maBienThe);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);

    if(mysqli_num_rows($res) > 0) {
        // Cập nhật số lượng
        $updateSQL = "UPDATE GioHang SET SoLuong = SoLuong + 1 WHERE MaUser = ? AND MaBienThe = ?";
        $stmtUpdate = mysqli_prepare($conn, $updateSQL);
        mysqli_stmt_bind_param($stmtUpdate, "ii", $maUser, $maBienThe);
        mysqli_stmt_execute($stmtUpdate);
    } else {
        // Thêm mới
        $insertSQL = "INSERT INTO GioHang (MaUser, MaBienThe, SoLuong) VALUES (?, ?, ?)";
        $stmtInsert = mysqli_prepare($conn, $insertSQL);
        mysqli_stmt_bind_param($stmtInsert, "iii", $maUser, $maBienThe, $soLuong);
        mysqli_stmt_execute($stmtInsert);
    }
    
    // 3. QUAN TRỌNG: Lưu thông báo và Reload trang để cập nhật Header
    $_SESSION['cart_success_msg'] = "Đã thêm món ăn vào giỏ hàng thành công!";
    echo "<script>window.location.href='KhuyenMai.php';</script>";
    exit();
}

// Lấy danh sách Combo (MaLoai = 7)
$sqlCombo = "SELECT m.*, b.MaBienThe, b.DonGia 
             FROM MonAn m 
             JOIN BienTheMonAn b ON m.MaMonAn = b.MaMonAn 
             WHERE m.MaLoai = 7 
             LIMIT 4";
$resultCombo = mysqli_query($conn, $sqlCombo);

$combos = [];
if ($resultCombo) {
    while($row = mysqli_fetch_assoc($resultCombo)) {
        $combos[] = $row;
    }
}

$featuredCombo = !empty($combos) ? $combos[0] : null;
$gridCombos = !empty($combos) ? array_slice($combos, 1) : [];
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Khuyến Mãi - Jollibee</title>
    <link rel="stylesheet" href="css/KhuyenMai.css">
</head>
<body>
    <div class="container">
        <div class="page-header">
            <h1>KHUYẾN MÃI</h1>
            <p>Ưu đãi hấp dẫn - Giá sốc mỗi ngày!</p>
        </div>

        <div class="content-container">
            <?php if(isset($_SESSION['cart_success_msg'])): ?>
                <div class="alert alert-success" id="success-alert">
                    <?php 
                        echo htmlspecialchars($_SESSION['cart_success_msg']); 
                        unset($_SESSION['cart_success_msg']); // Xóa ngay sau khi hiện
                    ?>
                </div>
                <script>
                    // Tự động ẩn thông báo sau 3 giây
                    setTimeout(function() {
                        var alert = document.getElementById('success-alert');
                        if(alert) alert.style.display = 'none';
                    }, 3000);
                </script>
            <?php endif; ?>

            <?php if ($featuredCombo): ?>
            <div class="featured-promo">
                <div class="promo-badge">🔥 COMBO BÁN CHẠY</div>
                <div class="promo-content">
                    <div class="promo-image">
                        <img src="img/<?php echo htmlspecialchars($featuredCombo['HinhAnh']); ?>" 
                             alt="<?php echo htmlspecialchars($featuredCombo['TenMonAn']); ?>"
                             onerror="this.src='img/default-food.jpg'">
                        <div class="discount-tag">-30%</div>
                    </div>
                    <div class="promo-details">
                        <h3 class="promo-title"><?php echo htmlspecialchars($featuredCombo['TenMonAn']); ?></h3>
                        <h4>🎉 ƯU ĐÃI ĐẶC BIỆT</h4>
                        <div class="promo-items">
                            <p><?php echo nl2br(htmlspecialchars($featuredCombo['MoTa'])); ?></p>
                        </div>
                        <div class="price-section">
                            <div class="original-price"><?php echo number_format($featuredCombo['DonGia'] * 1.3, 0, ',', '.'); ?> VND</div>
                            <div class="sale-price"><?php echo number_format($featuredCombo['DonGia'], 0, ',', '.'); ?> VND</div>
                            <div class="saving">Tiết kiệm ngay hôm nay!</div>
                        </div>
                        <form method="POST" action="KhuyenMai.php" class="add-to-cart-form">
                            <input type="hidden" name="ma_bien_the" value="<?php echo $featuredCombo['MaBienThe']; ?>">
                            <button type="submit" name="add_to_cart" class="btn-order-now">
                                🛒 THÊM VÀO GIỎ HÀNG
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <?php if (!empty($gridCombos)): ?>
            <div class="promo-section">
                <h3 class="section-title">COMBO ƯU ĐÃI KHÁC</h3>
                <div class="promo-grid">
                    <?php foreach ($gridCombos as $combo): ?>
                    <div class="promo-card">
                        <div class="card-badge">TIẾT KIỆM</div>
                        <div class="promo-card-image">
                            <img src="img/<?php echo htmlspecialchars($combo['HinhAnh']); ?>" 
                                 alt="<?php echo htmlspecialchars($combo['TenMonAn']); ?>"
                                 onerror="this.src='img/default-food.jpg'">
                        </div>
                        <div class="promo-card-content">
                            <h4><?php echo htmlspecialchars($combo['TenMonAn']); ?></h4>
                            <div class="promo-description">
                                <p><?php echo htmlspecialchars($combo['MoTa']); ?></p>
                            </div>
                            <div class="promo-price">
                                <div class="current-price"><?php echo number_format($combo['DonGia'], 0, ',', '.'); ?> VND</div>
                                <div class="price-note">Giá gốc: <?php echo number_format($combo['DonGia'] * 1.2, 0, ',', '.'); ?> VND</div>
                            </div>
                            <form method="POST" action="KhuyenMai.php" class="add-to-cart-form">
                                <input type="hidden" name="ma_bien_the" value="<?php echo $combo['MaBienThe']; ?>">
                                <button type="submit" name="add_to_cart" class="btn-add-to-cart">
                                    + Thêm vào giỏ
                                </button>
                            </form>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

            <div class="promo-info">
                <div class="info-card">
                    <div class="info-icon">🚚</div>
                    <h4>MIỄN PHÍ GIAO HÀNG</h4>
                    <p>Đơn hàng từ 150.000 VND trong bán kính 5km</p>
                </div>
                <div class="info-card">
                    <div class="info-icon">🎁</div>
                    <h4>QUÀ TẶNG ĐẶC BIỆT</h4>
                    <p>Tặng voucher 50.000 VND cho đơn hàng tiếp theo</p>
                </div>
                <div class="info-card">
                    <div class="info-icon">⏰</div>
                    <h4>ƯU ĐÃI CÓ HẠN</h4>
                    <p>Áp dụng đến hết ngày 31/12/2024</p>
                </div>
            </div>
        </div>
    </div>
    
    </body>
</html>

<?php include_once "includes/footer.php"; ?>