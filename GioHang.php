<?php
include_once "includes/header.php";

// Kiểm tra trạng thái đăng nhập của người dùng
if(!isset($_SESSION['loggedin'])) {
    header("Location: DangNhap.php");
    exit();
}

// Lấy mã người dùng từ session
$maUser = $_SESSION['MaUser'];
$stmt = null;

// Kiểm tra kết nối database
if (!$conn) {
    die("Lỗi kết nối database");
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giỏ Hàng - Jollibee</title>
    <link rel="stylesheet" href="css/GioHang.css">
</head>
<body>
    <div class="container">
        <div class="page-header">
            <h1>GIỎ HÀNG CỦA BẠN</h1>
            <p>Kiểm tra và thanh toán đơn hàng</p>
        </div>

        <div class="content-container">
            <?php
            // Truy vấn lấy thông tin giỏ hàng của người dùng
            $cartSQL = "SELECT gh.*, m.TenMonAn, m.HinhAnh, b.DonGia, k.TenSize 
                       FROM GioHang gh 
                       JOIN BienTheMonAn b ON gh.MaBienThe = b.MaBienThe 
                       JOIN MonAn m ON b.MaMonAn = m.MaMonAn 
                       JOIN KichThuoc k ON b.MaSize = k.MaSize 
                       WHERE gh.MaUser = ?";
            
            $stmt = mysqli_prepare($conn, $cartSQL);
            if (!$stmt) {
                // Hiển thị lỗi nếu không thể chuẩn bị truy vấn
                echo '<p class="error">Lỗi chuẩn bị truy vấn: ' . mysqli_error($conn) . '</p>';
                echo '<p class="empty-cart">Giỏ hàng của bạn đang trống.</p>';
                echo '<a href="ThucDon.php" class="btn-continue">Mua sắm ngay</a>';
            } else {
                mysqli_stmt_bind_param($stmt, "i", $maUser);
                mysqli_stmt_execute($stmt);
                $cartResult = mysqli_stmt_get_result($stmt);
                
                if (!$cartResult) {
                    // Hiển thị lỗi nếu truy vấn thất bại
                    echo '<p class="error">Lỗi truy vấn database: ' . mysqli_error($conn) . '</p>';
                    echo '<p class="empty-cart">Giỏ hàng của bạn đang trống.</p>';
                    echo '<a href="ThucDon.php" class="btn-continue">Mua sắm ngay</a>';
                } 
                // Kiểm tra xem có sản phẩm trong giỏ hàng không
                elseif(mysqli_num_rows($cartResult) > 0) {
                    $total = 0;
                    // Tạo bảng hiển thị giỏ hàng
                    echo '<table class="cart-table">';
                    echo '<tr><th>Món ăn</th><th>Size</th><th>Đơn giá</th><th>Số lượng</th><th>Thành tiền</th><th>Thao tác</th></tr>';
                    
                    // Lặp qua từng sản phẩm trong giỏ hàng
                    while($item = mysqli_fetch_assoc($cartResult)) {
                        $thanhtien = $item['DonGia'] * $item['SoLuong'];
                        $total += $thanhtien;
                        
                        echo "<tr>";
                        echo "<td class='cart-item'>";
                        // Hiển thị hình ảnh sản phẩm với fallback nếu lỗi
                        echo "<img src='img/{$item['HinhAnh']}' width='80' alt='{$item['TenMonAn']}' onerror=\"this.src='img/default-food.jpg'\">";
                        echo "<span class='item-name'>{$item['TenMonAn']}</span>";
                        echo "</td>";
                        echo "<td class='item-size'>{$item['TenSize']}</td>";
                        // Định dạng giá tiền theo kiểu Việt Nam
                        echo "<td class='item-price'>".number_format($item['DonGia'], 0, ",", ".")." VND</td>";
                        echo "<td class='item-quantity'>";
                        // Input số lượng với chức năng cập nhật real-time
                        echo "<input type='number' value='{$item['SoLuong']}' min='1' 
                                    onchange='updateCart({$item['MaGioHang']}, this.value)'>";
                        echo "</td>";
                        echo "<td class='item-total'>".number_format($thanhtien, 0, ",", ".")." VND</td>";
                        echo "<td class='item-actions'>";
                        // Nút xóa sản phẩm khỏi giỏ hàng
                        echo "<button class='btn-remove' onclick='showRemoveConfirm({$item['MaGioHang']}, \"{$item['TenMonAn']}\")'>Xóa</button>";
                        echo "</td>";
                        echo "</tr>";
                    }
                    
                    // Hiển thị tổng tiền
                    echo "<tr class='cart-total'><td colspan='4'><strong>Tổng cộng</strong></td><td colspan='2'><strong>".number_format($total, 0, ",", ".")." VND</strong></td></tr>";
                    echo "</table>";
                    
                    // Các nút hành động cho giỏ hàng
                    echo '<div class="cart-actions">';
                    echo '<a href="ThucDon.php" class="btn-continue">Tiếp tục mua hàng</a>';
                    echo '<a href="ThanhToan.php" class="btn-checkout">Thanh toán</a>';
                    echo '</div>';
                    
                    mysqli_stmt_close($stmt);
                } else {
                    // Hiển thị khi giỏ hàng trống (Giao diện giống Đơn hàng)
                    echo '<div class="empty-cart-state">';
                    echo '    <div class="empty-icon">🛒</div>';
                    echo '    <h3>Giỏ hàng đang trống</h3>';
                    echo '    <p>Bạn chưa thêm món ăn nào. Hãy khám phá thực đơn ngay!</p>';
                    echo '    <a href="ThucDon.php" class="btn-primary btn-empty-cart">';
                    echo '        <span class="btn-icon">🍽️</span> Xem Thực Đơn';
                    echo '    </a>';
                    echo '</div>';
                    
                    mysqli_stmt_close($stmt);
                }
            }
            ?>
        </div>
    </div>

    <!-- Modal xác nhận xóa sản phẩm -->
    <div id="confirmModal" class="modal confirm-modal">
        <div class="modal-content confirm-content">
            <div class="confirm-header">
                <div class="confirm-icon">❓</div>
                <h3>Xác nhận xóa</h3>
            </div>
            <div class="confirm-body">
                <p id="confirmMessage">Bạn có chắc muốn xóa món này khỏi giỏ hàng?</p>
            </div>
            <div class="confirm-actions">
                <button class="btn-cancel" onclick="closeConfirmModal()">Hủy</button>
                <button class="btn-confirm" id="btnConfirmDelete">Xóa</button>
            </div>
        </div>
    </div>

    <!-- Overlay cho modal -->
    <div class="overlay" onclick="closeConfirmModal()"></div>

    <script src="js/GioHang.js"></script>
</body>
</html>

<?php 
include_once "includes/footer.php"; 
?>