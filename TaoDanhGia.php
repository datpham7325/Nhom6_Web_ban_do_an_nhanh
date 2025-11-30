<?php
ob_start(); 

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Giả định file header.php đã bao gồm kết nối CSDL $conn
include_once "includes/header.php";

// 1. Kiểm tra đăng nhập
if (!isset($_SESSION['loggedin'])) {
    header("Location: DangNhap.php");
    exit();
}

$maUser = $_SESSION['MaUser'];
$maDonHang = $_GET['id'] ?? null;

// 2. Validate Mã Đơn Hàng
if (!$maDonHang || !is_numeric($maDonHang)) {
    echo "<div class='container' style='margin-top:20px;'><div class='alert alert-danger'>Mã đơn hàng không hợp lệ.</div></div>";
    include_once "includes/footer.php";
    exit();
}

// 3. LẤY THÔNG TIN ĐƠN HÀNG
$sqlOrder = "SELECT * FROM DonHang WHERE MaDonHang = ? AND MaUser = ?";
$stmt = mysqli_prepare($conn, $sqlOrder);

// 🔥 Bổ sung kiểm tra lỗi prepare
if (!$stmt) {
    echo "<div class='container' style='margin-top:20px;'><div class='alert alert-danger'>Lỗi chuẩn bị truy vấn đơn hàng: " . mysqli_error($conn) . "</div></div>";
    include_once "includes/footer.php";
    exit();
}

mysqli_stmt_bind_param($stmt, "ii", $maDonHang, $maUser);
mysqli_stmt_execute($stmt);
$order = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
mysqli_stmt_close($stmt);

if (!$order) {
    echo "<div class='container' style='margin-top:20px;'><div class='alert alert-danger'>Không tìm thấy đơn hàng.</div></div>";
    include_once "includes/footer.php";
    exit();
}

// Kiểm tra trạng thái đơn hàng
if ($order['TrangThai'] !== 'hoan_thanh') {
    echo "<div class='container' style='margin-top:20px;'><div class='alert alert-warning'>Đơn hàng chưa hoàn thành, chưa thể đánh giá.</div></div>";
    include_once "includes/footer.php";
    exit();
}

// 🔥 Bổ sung logic kiểm tra xem đã đánh giá chưa (Dời lên trước logic POST)
$checkSql = "SELECT MaDanhGia FROM DanhGia WHERE MaDonHang = ?";
$stmtCheck = mysqli_prepare($conn, $checkSql);

if (!$stmtCheck) {
    echo "<div class='container' style='margin-top:20px;'><div class='alert alert-danger'>Lỗi chuẩn bị kiểm tra đánh giá: " . mysqli_error($conn) . "</div></div>";
    include_once "includes/footer.php";
    exit();
}

mysqli_stmt_bind_param($stmtCheck, "i", $maDonHang);
mysqli_stmt_execute($stmtCheck);
if (mysqli_num_rows(mysqli_stmt_get_result($stmtCheck)) > 0) {
    $_SESSION['review_error'] = "Bạn đã đánh giá đơn hàng này rồi.";
    header("Location: DanhGia.php");
    exit();
}
mysqli_stmt_close($stmtCheck);


// 4. XỬ LÝ POST: GỬI ĐÁNH GIÁ
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_review'])) {
    $diem = intval($_POST['rating']);
    $noiDung = trim($_POST['noidung']);

    // Validate dữ liệu
    if ($diem < 1 || $diem > 5) {
        $error = "Vui lòng chọn số sao.";
    } else {
        // SQL ĐÃ SỬA: Phù hợp với cấu trúc bảng (chỉ có MaDonHang)
        $insertSQL = "INSERT INTO DanhGia (MaUser, MaDonHang, Diem, NoiDung) VALUES (?, ?, ?, ?)";
        $stmtInsert = mysqli_prepare($conn, $insertSQL);
        
        if (!$stmtInsert) {
             $error = "Lỗi chuẩn bị truy vấn chèn đánh giá: " . mysqli_error($conn);
        } else {
            mysqli_stmt_bind_param($stmtInsert, "iiis", $maUser, $maDonHang, $diem, $noiDung);
            try {
                if (mysqli_stmt_execute($stmtInsert)) {
                    $_SESSION['review_success'] = "Đánh giá thành công! Cảm ơn bạn.";
                    header("Location: DanhGia.php");
                    exit();
                }
            } catch (mysqli_sql_exception $e) {
                // Lỗi 1062 là lỗi trùng lặp (do ràng buộc UNIQUE(MaDonHang))
                if ($e->getCode() == 1062) { 
                    $error = "Bạn đã đánh giá đơn hàng này rồi.";
                } else {
                    $error = "Lỗi hệ thống: " . $e->getMessage();
                }
            }
            mysqli_stmt_close($stmtInsert);
        }
    }
}

// 5. Lấy danh sách món ăn hiển thị (PHẦN GÂY LỖI TYPERROR TRƯỚC ĐÂY)
$sqlItems = "SELECT m.TenMonAn, k.TenSize, c.SoLuong 
             FROM ChiTietDonHang c 
             JOIN BienTheMonAn b ON c.MaBienThe = b.MaBienThe 
             JOIN MonAn m ON b.MaMonAn = m.MaMonAn 
             JOIN KichThuoc k ON b.MaSize = k.MaSize
             WHERE c.MaDonHang = ?";
$stmtItems = mysqli_prepare($conn, $sqlItems);

// 🔥 KIỂM TRA LỖI: Đảm bảo $stmtItems hợp lệ (đã sửa lỗi TypeError)
if (!$stmtItems) {
    echo "<div class='container' style='margin-top:20px;'><div class='alert alert-danger'>Lỗi chuẩn bị truy vấn chi tiết đơn hàng: " . mysqli_error($conn) . "</div></div>";
    include_once "includes/footer.php";
    exit();
}

mysqli_stmt_bind_param($stmtItems, "i", $maDonHang);
mysqli_stmt_execute($stmtItems);
$itemsResult = mysqli_stmt_get_result($stmtItems);
mysqli_stmt_close($stmtItems); // Đóng statement sau khi lấy result

// Bắt đầu phần HTML
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đánh Giá Đơn Hàng #<?php echo $maDonHang; ?></title>
    <link rel="stylesheet" href="css/danhgiacuatoi.css">
    <style>
        .review-form-card {
            background: white; padding: 30px; border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1); max-width: 600px; margin: 0 auto;
        }
        .order-info {
            background: #f8f9fa; padding: 15px; border-radius: 8px; margin-bottom: 20px;
            border-left: 4px solid #667eea;
        }
        .rating-group {
            display: flex; flex-direction: row-reverse; justify-content: center; margin: 20px 0;
        }
        .rating-group input { display: none; }
        .rating-group label {
            font-size: 40px; color: #ddd; cursor: pointer; transition: 0.2s; padding: 0 5px;
        }
        .rating-group input:checked ~ label,
        .rating-group label:hover,
        .rating-group label:hover ~ label { color: #f1c40f; }
        textarea.form-control {
            width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; min-height: 100px;
        }
        .btn-submit {
            width: 100%; padding: 12px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white; border: none; border-radius: 8px; font-weight: bold; margin-top: 15px; cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="page-header">
            <h1>VIẾT ĐÁNH GIÁ</h1>
            <p>Đơn hàng #<?php echo $maDonHang; ?></p>
        </div>

        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <div class="review-form-card">
            <div class="order-info">
                <p><strong>Ngày đặt:</strong> <?php echo date('d/m/Y H:i', strtotime($order['NgayDat'])); ?></p>
                <p><strong>Tổng tiền:</strong> <?php echo number_format($order['TongTien'], 0, ',', '.'); ?> đ</p>
                <ul style="margin-top:10px; padding-left:20px; color:#555;">
                    <?php 
                    // Kiểm tra itemsResult có phải là một result set hợp lệ
                    if ($itemsResult) {
                         while ($item = mysqli_fetch_assoc($itemsResult)): 
                    ?>
                        <li><?php echo $item['TenMonAn']; ?> (<?php echo $item['TenSize']; ?>) x<?php echo $item['SoLuong']; ?></li>
                    <?php 
                         endwhile;
                    } else {
                        echo "<li>Không thể tải chi tiết món ăn.</li>";
                    }
                    ?>
                </ul>
            </div>

            <form method="POST" action="">
                <h3 style="text-align: center;">Bạn hài lòng chứ?</h3>
                
                <div class="rating-group">
                    <input type="radio" id="star5" name="rating" value="5" required /><label for="star5">★</label>
                    <input type="radio" id="star4" name="rating" value="4" /><label for="star4">★</label>
                    <input type="radio" id="star3" name="rating" value="3" /><label for="star3">★</label>
                    <input type="radio" id="star2" name="rating" value="2" /><label for="star2">★</label>
                    <input type="radio" id="star1" name="rating" value="1" /><label for="star1">★</label>
                </div>

                <div class="form-group">
                    <label>Nội dung đánh giá:</label>
                    <textarea name="noidung" class="form-control" placeholder="Chia sẻ trải nghiệm của bạn..."></textarea>
                </div>

                <button type="submit" name="submit_review" class="btn-submit">Gửi Đánh Giá</button>
            </form>
            
            <div style="text-align:center; margin-top:15px;">
                <a href="DonHang.php" style="text-decoration:none; color:#666;">Quay lại</a>
            </div>
        </div>
    </div>

    <?php include_once "includes/footer.php"; ?>
</body>
</html>
<?php ob_end_flush(); ?>