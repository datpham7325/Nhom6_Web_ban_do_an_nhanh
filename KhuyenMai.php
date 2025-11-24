<?php 
include_once "includes/header.php";

// Xử lý thêm combo vào giỏ hàng
if(isset($_POST['add_combo'])) {
    $combo_type = $_POST['combo_type'];
    $combo_data = getComboData($combo_type);
    
    if($combo_data) {
        // Kiểm tra xem giỏ hàng đã tồn tại chưa
        if(!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }
        
        // Thêm combo vào giỏ hàng
        $cart_item = [
            'type' => 'combo',
            'combo_type' => $combo_type,
            'name' => $combo_data['name'],
            'price' => $combo_data['price'],
            'image' => $combo_data['image'],
            'quantity' => 1
        ];
        
        // Kiểm tra xem combo đã có trong giỏ hàng chưa
        $found = false;
        foreach($_SESSION['cart'] as &$item) {
            if($item['type'] == 'combo' && $item['combo_type'] == $combo_type) {
                $item['quantity'] += 1;
                $found = true;
                break;
            }
        }
        
        if(!$found) {
            $_SESSION['cart'][] = $cart_item;
        }
        
        $success = "Đã thêm " . $combo_data['name'] . " vào giỏ hàng!";
    }
}

// Hàm lấy thông tin combo
function getComboData($combo_type) {
    $combos = [
        'combo_gia_dinh' => [
            'name' => 'COMBO GIA ĐÌNH SIÊU TIẾT KIỆM',
            'price' => 245000,
            'image' => 'img/khuyenmai/comboGD.jpg'
        ],
        'combo_1_nguoi' => [
            'name' => 'COMBO 1 NGƯỜI',
            'price' => 89000,
            'image' => 'img/khuyenmai/combo1n.jpg'
        ],
        'combo_2_nguoi' => [
            'name' => 'COMBO 2 NGƯỜI',
            'price' => 159000,
            'image' => 'img/khuyenmai/combo2n.jpg'
        ],
        'combo_4_nguoi' => [
            'name' => 'COMBO 4 NGƯỜI',
            'price' => 299000,
            'image' => 'img/khuyenmai/combo4n.jpg'
        ]
    ];
    
    return isset($combos[$combo_type]) ? $combos[$combo_type] : null;
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Khuyến Mãi</title>
    <link rel="stylesheet" href="css/KhuyenMai.css">
</head>
<body>
    <div class="container">
        <div class="page-header">
            <h1>KHUYẾN MÃI</h1>
            <p>Ưu đãi hấp dẫn - Giá sốc mỗi ngày!</p>
        </div>

        <div class="content-container">
            <?php if(isset($success)): ?>
                <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
            <?php endif; ?>

            <!-- Combo đặc biệt nổi bật -->
            <div class="featured-promo">
                <div class="promo-badge">🔥 COMBO BÁN CHẠY</div>
                <div class="promo-content">
                    <div class="promo-image">
                        <img src="img/khuyenmai/comboGD.jpg" alt="Combo gia đình siêu tiết kiệm">
                        <div class="discount-tag">-30%</div>
                    </div>
                    <div class="promo-details">
                        <h3 class="promo-title">COMBO GIA ĐÌNH SIÊU TIẾT KIỆM</h3>
                        <h4>🎉 COMBO GIA ĐÌNH 4 NGƯỜI</h4>
                        <ul class="promo-items">
                            <li>✅ 4 Gà Giòn Vui Vẻ</li>
                            <li>✅ 4 Burger Jollibee</li>
                            <li>✅ 4 Nước ngọt</li>
                            <li>✅ 2 Khoai tây chiên lớn</li>
                            <li>✅ 1 Túi quà đặc biệt</li>
                        </ul>
                        <div class="price-section">
                            <div class="original-price">350.000 VND</div>
                            <div class="sale-price">245.000 VND</div>
                            <div class="saving">Tiết kiệm 105.000 VND</div>
                        </div>
                        <form method="POST" action="" class="add-to-cart-form">
                            <input type="hidden" name="combo_type" value="combo_gia_dinh">
                            <button type="submit" name="add_combo" class="btn-order-now">
                                🛒 THÊM VÀO GIỎ HÀNG
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Danh sách combo -->
            <div class="promo-section">
                <h3 class="section-title">COMBO ƯU ĐÃI</h3>
                <div class="promo-grid">
                    <!-- Combo 1 người -->
                    <div class="promo-card">
                        <div class="card-badge">PHỔ BIẾN</div>
                        <div class="promo-card-image">
                            <img src="img/khuyenmai/combo1n.jpg" alt="Combo 1 người">
                        </div>
                        <div class="promo-card-content">
                            <h4>COMBO 1 NGƯỜI</h4>
                            <div class="promo-description">
                                <p>🍗 1 Gà Giòn Vui Vẻ</p>
                                <p>🍔 1 Burger Jollibee</p>
                                <p>🥤 1 Nước ngọt</p>
                            </div>
                            <div class="promo-price">
                                <div class="current-price">89.000 VND</div>
                                <div class="price-note">Giá gốc: 110.000 VND</div>
                            </div>
                            <form method="POST" action="" class="add-to-cart-form">
                                <input type="hidden" name="combo_type" value="combo_1_nguoi">
                                <button type="submit" name="add_combo" class="btn-add-to-cart">
                                    + Thêm vào giỏ
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Combo 2 người -->
                    <div class="promo-card">
                        <div class="card-badge">TIẾT KIỆM</div>
                        <div class="promo-card-image">
                            <img src="img/khuyenmai/combo2n.jpg" alt="Combo 2 người">
                        </div>
                        <div class="promo-card-content">
                            <h4>COMBO 2 NGƯỜI</h4>
                            <div class="promo-description">
                                <p>🍗 2 Gà Giòn Vui Vẻ</p>
                                <p>🍔 2 Burger Jollibee</p>
                                <p>🥤 2 Nước ngọt</p>
                                <p>🍟 1 Khoai tây chiên</p>
                            </div>
                            <div class="promo-price">
                                <div class="current-price">159.000 VND</div>
                                <div class="price-note">Giá gốc: 195.000 VND</div>
                            </div>
                            <form method="POST" action="" class="add-to-cart-form">
                                <input type="hidden" name="combo_type" value="combo_2_nguoi">
                                <button type="submit" name="add_combo" class="btn-add-to-cart">
                                    + Thêm vào giỏ
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Combo 4 người -->
                    <div class="promo-card">
                        <div class="card-badge">HOT</div>
                        <div class="promo-card-image">
                            <img src="img/khuyenmai/combo4n.jpg" alt="Combo 4 người">
                        </div>
                        <div class="promo-card-content">
                            <h4>COMBO 4 NGƯỜI</h4>
                            <div class="promo-description">
                                <p>🍗 4 Gà Giòn Vui Vẻ</p>
                                <p>🍔 4 Burger Jollibee</p>
                                <p>🥤 4 Nước ngọt</p>
                                <p>🍟 2 Khoai tây chiên lớn</p>
                            </div>
                            <div class="promo-price">
                                <div class="current-price">299.000 VND</div>
                                <div class="price-note">Giá gốc: 370.000 VND</div>
                            </div>
                            <form method="POST" action="" class="add-to-cart-form">
                                <input type="hidden" name="combo_type" value="combo_4_nguoi">
                                <button type="submit" name="add_combo" class="btn-add-to-cart">
                                    + Thêm vào giỏ
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Thông tin khuyến mãi -->
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

    <script src="js/KhuyenMai.js"></script>
</body>
</html>

<?php include_once "includes/footer.php"; ?>