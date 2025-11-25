<?php include_once "includes/header.php"; ?>

<div class="container">
    <div class="page-header">
        <h1>TÌM KIẾM MÓN ĂN</h1>
        <p>Khám phá hương vị yêu thích của bạn!</p>
    </div>

    <div class="content-container">

        <?php
        // Xử lý tìm kiếm khi có keyword từ form
        if(isset($_GET['keyword'])) {
            // Lấy và làm sạch dữ liệu từ form
            $keyword = mysqli_real_escape_string($conn, $_GET['keyword']);
            $maloai = $_GET['maloai'] ?? 0;
            $min_price = $_GET['min_price'] ?? 0;
            $max_price = $_GET['max_price'] ?? 999999999;
            
            // Xây dựng câu truy vấn SQL tìm kiếm
            $sql = "SELECT m.*, b.MaBienThe, b.DonGia, b.MaSize, k.TenSize
                    FROM MonAn m 
                    JOIN BienTheMonAn b ON m.MaMonAn = b.MaMonAn 
                    JOIN KichThuoc k ON b.MaSize = k.MaSize
                    WHERE m.TenMonAn LIKE '%$keyword%'";
            
            // Thêm điều kiện lọc theo loại món nếu có
            if($maloai > 0) {
                $sql .= " AND m.MaLoai = $maloai";
            }
            
            // Thêm điều kiện lọc theo khoảng giá
            $sql .= " AND b.DonGia BETWEEN $min_price AND $max_price";
            
            // Thực thi truy vấn
            $result = mysqli_query($conn, $sql);
            
            // Kiểm tra và hiển thị kết quả
            if(mysqli_num_rows($result) > 0) {
                echo '<div class="menu-grid">';
                
                // Lặp qua từng kết quả tìm kiếm
                while($row = mysqli_fetch_assoc($result)) {
                    $anh = "img/". $row['HinhAnh'];
                    $mbt = $row['MaBienThe'];
                    
                    // Xử lý tên món
                    $tenMon = $row['MaLoai'] == 6 ? $row['TenMonAn'] . " " . $row['TenSize'] : $row['TenMonAn'];
                    $gia = number_format($row['DonGia'], 0, ",", ".");
                    
                    ?>
                    <div class="menu-item" onclick="openModal('<?php echo $mbt; ?>', '<?php echo htmlspecialchars($tenMon); ?>', '<?php echo $anh; ?>', <?php echo $row['DonGia']; ?>, '<?php echo htmlspecialchars($row['MoTa'] ?? ''); ?>')">
                        <div class="item-image">
                            <img src='<?php echo $anh; ?>' alt='<?php echo htmlspecialchars($tenMon); ?>' onerror="this.src='img/default-food.jpg'">
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
                echo '</div>'; 
            } else {
                // Hiển thị thông báo khi không tìm thấy kết quả
                echo '<div class="no-items">
                        <div class="no-items-icon">🔍</div>
                        <h3>Không tìm thấy món ăn</h3>
                        <p>Rất tiếc, không có món nào phù hợp với từ khóa của bạn.</p>
                      </div>';
            }
        }
        ?>
    </div>
</div>

<div id="foodModal" class="modal">
    <div class="modal-content">
        <span class="close-modal" onclick="closeModal()">&times;</span>
        <div class="modal-body">
            <div class="modal-image">
                <img id="modalImage" src="" alt="">
            </div>
            <div class="modal-info">
                <div class="modal-scrollable">
                    <h2 id="modalName"></h2>
                    <div class="description-container">
                        <p id="modalDescription" class="modal-description"></p>
                    </div>
                    
                    </div>

                <div class="order-section">
                    <div class="quantity-selector">
                        <label>Số lượng:</label>
                        <div class="quantity-controls">
                            <button type="button" class="btn-quantity minus" onclick="decreaseQuantity()">-</button>
                            <input type="number" id="modalQuantity" value="1" min="1" max="10" readonly>
                            <button type="button" class="btn-quantity plus" onclick="increaseQuantity()">+</button>
                        </div>
                    </div>
                    <div class="price-section">
                        <span class="total-label">Thành tiền:</span>
                        <span id="modalTotalPrice" class="total-price">0 VND</span>
                    </div>
                    <button class="btn-add-to-cart-modal" onclick="addToCartFromModal()">
                        🛒 Thêm vào giỏ hàng
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

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

<div class="cart-sidebar">
    <div class="cart-header">
        <h3>Giỏ hàng của bạn</h3>
        <button class="btn-close-cart" onclick="closeCart()">×</button>
    </div>
    <div class="cart-content">
        <div id="cartItems" class="cart-items"></div>
        <div class="cart-footer">
            <div class="cart-total">
                <span>Tổng cộng:</span>
                <span id="totalAmount">0 VND</span>
            </div>
            <button class="btn-checkout" onclick="checkout()">Thanh toán</button>
        </div>
    </div>
</div>

<button class="cart-toggle" onclick="toggleCart()">
    <span class="cart-icon">🛒</span>
    <span class="cart-count" id="cartCount">0</span>
</button>

<div class="overlay" onclick="closeModal(); closeCart(); closeConfirmModal();"></div>

<link rel="stylesheet" href="css/thucdon.css">
<script src="js/thucdon.js"></script>

<?php include_once "includes/footer.php"; ?>