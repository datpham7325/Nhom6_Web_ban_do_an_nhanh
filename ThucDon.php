<?php
include_once "includes/header.php";
include_once "function/functions.php";

// Kết nối database
include_once("includes/myenv.php");
$conn = mysqli_connect($db_host, $db_user, $db_password, $db_db,$db_port);

// Xử lý truy vấn món ăn theo danh mục
if(isset($_GET['maloaimonan'])) {
    $maLoai = $_GET['maloaimonan'];
    if($maLoai == 0) {
        // Lấy tất cả món ăn không lọc theo loại
        $strSQL = "SELECT ma.*, bto.MaBienThe, bto.DonGia, bto.MaSize, kt.TenSize 
                  FROM monan ma 
                  JOIN bienthemonan bto ON ma.MaMonAn = bto.MaMonAn 
                  JOIN kichthuoc kt ON bto.MaSize = kt.MaSize";
    } else {
        // Lấy món ăn theo loại cụ thể
        $strSQL = "SELECT ma.*, bto.MaBienThe, bto.DonGia, bto.MaSize, kt.TenSize 
                  FROM monan ma 
                  JOIN bienthemonan bto ON ma.MaMonAn = bto.MaMonAn 
                  JOIN kichthuoc kt ON bto.MaSize = kt.MaSize
                  WHERE ma.MaLoai = '$maLoai'";
    }
} else {
    // Mặc định lấy tất cả món ăn
    $maLoai = 0;
    $strSQL = "SELECT ma.*, bto.MaBienThe, bto.DonGia, bto.MaSize, kt.TenSize 
              FROM monan ma 
              JOIN bienthemonan bto ON ma.MaMonAn = bto.MaMonAn 
              JOIN kichthuoc kt ON bto.MaSize = kt.MaSize";
}

// Thực thi truy vấn
$result = mysqli_query($conn, $strSQL);
?>

<div class="container">
    <!-- Banner Header cho trang thực đơn -->
    <div class="menu-banner">
        <div class="banner-content">
            <h1 class="banner-title">Thực Đơn Jollibee</h1>
            <p class="banner-subtitle">Hương vị hạnh phúc - Trọn vẹn yêu thương</p>
        </div>
    </div>

    <div class="content-container">
        <!-- Grid hiển thị danh sách món ăn -->
        <div class="menu-grid">
            <?php
            // Kiểm tra có món ăn nào không
            if(mysqli_num_rows($result) > 0) {
                // Lặp qua từng món ăn và hiển thị
                while($row = mysqli_fetch_assoc($result)) {
                    $anh = "img/". $row['HinhAnh'];
                    $mbt = $row['MaBienThe'];
                    // Xử lý tên món: với loại 6 (có thể là đồ uống) thì thêm tên size
                    $tenMon = $row['MaLoai'] == 6 ? $row['TenMonAn'] . " " . $row['TenSize'] : $row['TenMonAn'];
                    $gia = number_format($row['DonGia'], 0, ",", ".");
                    ?>
                    <!-- Item món ăn - click để mở modal chi tiết -->
                    <div class="menu-item" onclick="openModal('<?php echo $mbt; ?>', '<?php echo htmlspecialchars($tenMon); ?>', '<?php echo $anh; ?>', <?php echo $row['DonGia']; ?>, '<?php echo htmlspecialchars($row['MoTa'] ?? ''); ?>')">
                        <div class="item-image">
                            <img src='<?php echo $anh; ?>' alt='<?php echo htmlspecialchars($tenMon); ?>' onerror="this.src='img/default-food.jpg'">
                            <!-- Overlay hiển thị khi hover -->
                            <div class="item-overlay">
                                <div class="overlay-content">
                                    <span class="view-detail">👁️ Xem chi tiết</span>
                                </div>
                            </div>
                        </div>
                        <div class="item-info">
                            <h3 class="item-name"><?php echo htmlspecialchars($tenMon); ?></h3>
                            <p class="item-price"><?php echo $gia; ?> VND</p>
                        </div>
                    </div>
                    <?php
                }
            } else {
                // Hiển thị khi không có món ăn
                echo "<div class='no-items'>
                        <div class='no-items-icon'>🍴</div>
                        <h3>Không có món ăn</h3>
                        <p>Hiện không có món ăn nào trong danh mục này.</p>
                      </div>";
            }
            ?>
        </div>
    </div>
</div>

<!-- Modal chi tiết món ăn -->
<div id="foodModal" class="modal">
    <div class="modal-content">
        <!-- Nút đóng modal -->
        <span class="close-modal" onclick="closeModal()">&times;</span>
        <div class="modal-body">
            <!-- Phần hình ảnh món ăn -->
            <div class="modal-image">
                <img id="modalImage" src="" alt="">
            </div>
            <!-- Phần thông tin món ăn -->
            <div class="modal-info">
                <div class="modal-scrollable">
                    <h2 id="modalName"></h2>
                    <!-- Mô tả món ăn -->
                    <div class="description-container">
                        <p id="modalDescription" class="modal-description"></p>
                    </div>
                    
                    <!-- Thông tin dinh dưỡng (hardcode) -->
                    <div class="nutrition-info">
                        <h4>Thông tin dinh dưỡng</h4>
                        <div class="nutrition-grid">
                            <div class="nutrition-item">
                                <span class="nutrition-label">Calories</span>
                                <span class="nutrition-value">450 kcal</span>
                            </div>
                            <div class="nutrition-item">
                                <span class="nutrition-label">Protein</span>
                                <span class="nutrition-value">25g</span>
                            </div>
                            <div class="nutrition-item">
                                <span class="nutrition-label">Carb</span>
                                <span class="nutrition-value">35g</span>
                            </div>
                            <div class="nutrition-item">
                                <span class="nutrition-label">Fat</span>
                                <span class="nutrition-value">20g</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Phần đặt hàng -->
                <div class="order-section">
                    <!-- Chọn số lượng -->
                    <div class="quantity-selector">
                        <label>Số lượng:</label>
                        <div class="quantity-controls">
                            <button type="button" class="btn-quantity minus" onclick="decreaseQuantity()">-</button>
                            <input type="number" id="modalQuantity" value="1" min="1" max="10" readonly>
                            <button type="button" class="btn-quantity plus" onclick="increaseQuantity()">+</button>
                        </div>
                    </div>
                    
                    <!-- Hiển thị tổng tiền -->
                    <div class="price-section">
                        <span class="total-label">Thành tiền:</span>
                        <span id="modalTotalPrice" class="total-price">0 VND</span>
                    </div>

                    <!-- Nút thêm vào giỏ hàng -->
                    <button class="btn-add-to-cart-modal" onclick="addToCartFromModal()">
                        🛒 Thêm vào giỏ hàng
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal xác nhận xóa món khỏi giỏ hàng -->
<div id="confirmModal" class="modal confirm-modal">
    <div class="modal-content confirm-content">
        <div class="confirm-header">
            <div class="confirm-icon">❓</div>
            <h3>Xác nhận xóa</h3>
        </div>
        <div class="confirm-body">
            <p>Bạn có chắc muốn xóa món này khỏi giỏ hàng?</p>
        </div>
        <div class="confirm-actions">
            <button class="btn-cancel" onclick="closeConfirmModal()">Hủy</button>
            <button class="btn-confirm" id="btnConfirmDelete">Xóa</button>
        </div>
    </div>
</div>

<!-- Sidebar giỏ hàng -->
<div class="cart-sidebar">
    <div class="cart-header">
        <h3>Giỏ hàng của bạn</h3>
        <button class="btn-close-cart" onclick="closeCart()">×</button>
    </div>
    <div class="cart-content">
        <!-- Danh sách items trong giỏ hàng -->
        <div id="cartItems" class="cart-items">
            <!-- Cart items will be loaded here -->
        </div>
        <div class="cart-footer">
            <!-- Tổng tiền giỏ hàng -->
            <div class="cart-total">
                <span>Tổng cộng:</span>
                <span id="totalAmount">0 VND</span>
            </div>
            <!-- Nút thanh toán -->
            <button class="btn-checkout" onclick="checkout()">Thanh toán</button>
        </div>
    </div>
</div>

<!-- Nút toggle mở/đóng giỏ hàng -->
<button class="cart-toggle" onclick="toggleCart()">
    <span class="cart-icon">🛒</span>
    <!-- Hiển thị số lượng món trong giỏ -->
    <span class="cart-count" id="cartCount">0</span>
</button>

<!-- Overlay để đóng các modal khi click bên ngoài -->
<div class="overlay" onclick="closeModal(); closeCart(); closeConfirmModal();"></div>

<!-- Liên kết CSS và JavaScript -->
<link rel="stylesheet" href="css/thucdon.css">
<script src="js/thucdon.js"></script>

<?php include_once "includes/footer.php"; ?>