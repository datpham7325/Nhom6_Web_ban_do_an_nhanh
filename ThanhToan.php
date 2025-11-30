<?php
include_once "includes/header.php";

if (!isset($_SESSION['loggedin'])) {
    header("Location: DangNhap.php");
    exit();
}

$maUser = $_SESSION['MaUser'];

// Lấy thông tin user từ database
$userSQL = "SELECT Ho, Ten, SDT, DiaChi FROM Users WHERE MaUser = ?";
$stmt = mysqli_prepare($conn, $userSQL);
mysqli_stmt_bind_param($stmt, "i", $maUser);
mysqli_stmt_execute($stmt);
$userResult = mysqli_stmt_get_result($stmt);
$userInfo = mysqli_fetch_assoc($userResult);
mysqli_stmt_close($stmt);

// Khởi tạo biến lỗi
$errors = [];

// Xử lý thanh toán
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['btnThanhToan'])) {
    $phuongThuc = $_POST['phuongthuc'] ?? '';
    $diaChi = $_POST['diachi'] ?? '';
    $sdt = $_POST['sdt'] ?? '';
    $ghiChu = $_POST['ghichu'] ?? '';

    // Validate dữ liệu
    if (empty($sdt)) {
        $errors['sdt'] = "Vui lòng nhập số điện thoại";
    } elseif (!preg_match('/^(0|\+84)[3|5|7|8|9][0-9]{8}$/', $sdt)) {
        $errors['sdt'] = "Số điện thoại không hợp lệ";
    }

    if (empty($diaChi)) {
        $errors['diachi'] = "Vui lòng nhập địa chỉ giao hàng";
    }

    // Nếu không có lỗi, tiến hành thanh toán
    if (empty($errors)) {
        // Lấy giỏ hàng và tính tổng
        $cartSQL = "SELECT gh.MaBienThe, gh.SoLuong, bto.DonGia 
                   FROM GioHang gh 
                   JOIN bienthemonan bto ON gh.MaBienThe = bto.MaBienThe 
                   WHERE gh.MaUser = ?";
        $stmt = mysqli_prepare($conn, $cartSQL);

        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "i", $maUser);
            mysqli_stmt_execute($stmt);
            $cartResult = mysqli_stmt_get_result($stmt);

            $tongTien = 0;
            $cartItems = [];

            while ($item = mysqli_fetch_assoc($cartResult)) {
                $thanhtien = $item['DonGia'] * $item['SoLuong'];
                $tongTien += $thanhtien;
                $cartItems[] = $item;
            }
            mysqli_stmt_close($stmt);

            // Kiểm tra giỏ hàng có sản phẩm không
            if (empty($cartItems)) {
                $errors['general'] = "Giỏ hàng trống, vui lòng thêm sản phẩm trước khi đặt hàng";
            } else {
                // Tạo đơn hàng
                $insertOrderSQL = "INSERT INTO DonHang (MaUser, TongTien, PhuongThucThanhToan, DiaChiGiaoHang, SDTGiaoHang, GhiChu) 
                                  VALUES (?, ?, ?, ?, ?, ?)";
                $stmt = mysqli_prepare($conn, $insertOrderSQL);

                if ($stmt) {
                    mysqli_stmt_bind_param($stmt, "idssss", $maUser, $tongTien, $phuongThuc, $diaChi, $sdt, $ghiChu);

                    if (mysqli_stmt_execute($stmt)) {
                        $maDonHang = mysqli_insert_id($conn);

                        // Thêm chi tiết đơn hàng
                        $insertDetailSQL = "INSERT INTO ChiTietDonHang (MaDonHang, MaBienThe, SoLuong, DonGia, ThanhTien) 
                                          VALUES (?, ?, ?, ?, ?)";
                        $stmt2 = mysqli_prepare($conn, $insertDetailSQL);

                        if ($stmt2) {
                            foreach ($cartItems as $item) {
                                $thanhtien = $item['DonGia'] * $item['SoLuong'];
                                mysqli_stmt_bind_param($stmt2, "iiidd", $maDonHang, $item['MaBienThe'], $item['SoLuong'], $item['DonGia'], $thanhtien);
                                mysqli_stmt_execute($stmt2);
                            }
                            mysqli_stmt_close($stmt2);

                            // Xóa giỏ hàng
                            $deleteCartSQL = "DELETE FROM GioHang WHERE MaUser = ?";
                            $stmt3 = mysqli_prepare($conn, $deleteCartSQL);
                            if ($stmt3) {
                                mysqli_stmt_bind_param($stmt3, "i", $maUser);
                                mysqli_stmt_execute($stmt3);
                                mysqli_stmt_close($stmt3);
                            }

                            // Đặt session trước khi redirect
                            $_SESSION['order_success'] = "Đặt hàng thành công! Mã đơn hàng: #$maDonHang";

                            // Redirect ngay lập tức, không có output trước
                            echo '<script>window.location.href = "DonHang.php";</script>';
                            exit();
                        } else {
                            $errors['general'] = "Lỗi khi tạo chi tiết đơn hàng: " . mysqli_error($conn);
                        }
                    } else {
                        $errors['general'] = "Lỗi khi tạo đơn hàng: " . mysqli_error($conn);
                    }
                    mysqli_stmt_close($stmt);
                } else {
                    $errors['general'] = "Lỗi chuẩn bị câu lệnh SQL: " . mysqli_error($conn);
                }
            }
        } else {
            $errors['general'] = "Lỗi khi lấy giỏ hàng: " . mysqli_error($conn);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thanh Toán</title>
    <link rel="stylesheet" href="css/thanhtoan.css">
</head>

<body>
    <?php include_once "includes/header.php"; ?>

    <div class="container">
        <div class="page-header">
            <h1>Thanh Toán</h1>
            <p>Hoàn tất đơn hàng của bạn</p>
        </div>

        <div class="content-container">
            <!-- Hiển thị thông báo lỗi chung -->
            <?php if (isset($errors['general'])): ?>
                <div class="error-notification" id="generalError">
                    <div class="error-icon">❌</div>
                    <div class="error-message"><?php echo $errors['general']; ?></div>
                    <button class="error-close" onclick="closeError('generalError')">×</button>
                </div>
            <?php endif; ?>

            <form method="POST" action="" class="checkout-form" id="checkoutForm">
                <div class="checkout-layout">
                    <!-- Cột trái - Thông tin giao hàng -->
                    <div class="checkout-main">
                        <!-- Thông tin người nhận -->
                        <div class="checkout-card">
                            <div class="card-header">
                                <div class="header-icon">👤</div>
                                <h3>Thông tin người nhận</h3>
                            </div>
                            <div class="card-body">
                                <div class="user-info">
                                    <div class="info-row">
                                        <span class="info-label">Họ tên:</span>
                                        <span class="info-value"><?php echo htmlspecialchars($userInfo['Ho'] . ' ' . $userInfo['Ten']); ?></span>
                                    </div>
                                    <div class="info-row">
                                        <span class="info-label">Số điện thoại:</span>
                                        <span class="info-value"><?php echo htmlspecialchars($userInfo['SDT'] ?? ''); ?></span>
                                    </div>
                                    <div class="info-row">
                                        <span class="info-label">Địa chỉ mặc định:</span>
                                        <span class="info-value"><?php echo htmlspecialchars($userInfo['DiaChi'] ?? ''); ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Địa chỉ giao hàng -->
                        <div class="checkout-card">
                            <div class="card-header">
                                <div class="header-icon">📍</div>
                                <h3>Địa chỉ giao hàng</h3>
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <label class="form-label">Số điện thoại nhận hàng *</label>
                                    <input type="tel" name="sdt" class="form-input <?php echo isset($errors['sdt']) ? 'error' : ''; ?>"
                                        value="<?php echo htmlspecialchars($_POST['sdt'] ?? $userInfo['SDT'] ?? ''); ?>"
                                        placeholder="Nhập số điện thoại nhận hàng">
                                    <?php if (isset($errors['sdt'])): ?>
                                        <div class="field-error"><?php echo $errors['sdt']; ?></div>
                                    <?php endif; ?>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Địa chỉ giao hàng *</label>
                                    <textarea name="diachi" class="form-textarea <?php echo isset($errors['diachi']) ? 'error' : ''; ?>"
                                        rows="3" placeholder="Số nhà, đường, phường/xã, quận/huyện, thành phố"><?php echo htmlspecialchars($_POST['diachi'] ?? $userInfo['DiaChi'] ?? ''); ?></textarea>
                                    <?php if (isset($errors['diachi'])): ?>
                                        <div class="field-error"><?php echo $errors['diachi']; ?></div>
                                    <?php endif; ?>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Ghi chú (tùy chọn)</label>
                                    <textarea name="ghichu" class="form-textarea" rows="2"
                                        placeholder="Ghi chú về đơn hàng, hướng dẫn giao hàng..."><?php echo htmlspecialchars($_POST['ghichu'] ?? ''); ?></textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Phương thức thanh toán -->
                        <div class="checkout-card">
                            <div class="card-header">
                                <div class="header-icon">💳</div>
                                <h3>Phương thức thanh toán</h3>
                            </div>
                            <div class="card-body">
                                <div class="payment-options">
                                    <label class="payment-method">
                                        <input type="radio" name="phuongthuc" value="tien_mat" <?php echo ($_POST['phuongthuc'] ?? 'tien_mat') == 'tien_mat' ? 'checked' : ''; ?>>
                                        <div class="method-content">
                                            <div class="method-icon">💵</div>
                                            <div class="method-info">
                                                <div class="method-title">Thanh toán khi nhận hàng</div>
                                                <div class="method-desc">Thanh toán bằng tiền mặt khi giao hàng</div>
                                            </div>
                                        </div>
                                        <div class="radio-checkmark"></div>
                                    </label>

                                    <label class="payment-method">
                                        <input type="radio" name="phuongthuc" value="chuyen_khoan" <?php echo ($_POST['phuongthuc'] ?? '') == 'chuyen_khoan' ? 'checked' : ''; ?>>
                                        <div class="method-content">
                                            <div class="method-icon">🏦</div>
                                            <div class="method-info">
                                                <div class="method-title">Chuyển khoản ngân hàng</div>
                                                <div class="method-desc">Chuyển khoản qua Internet Banking/Mobile Banking</div>
                                            </div>
                                        </div>
                                        <div class="radio-checkmark"></div>
                                    </label>

                                    <label class="payment-method">
                                        <input type="radio" name="phuongthuc" value="the" <?php echo ($_POST['phuongthuc'] ?? '') == 'the' ? 'checked' : ''; ?>>
                                        <div class="method-content">
                                            <div class="method-icon">💳</div>
                                            <div class="method-info">
                                                <div class="method-title">Thẻ tín dụng/ghi nợ</div>
                                                <div class="method-desc">Thanh toán qua thẻ Visa, Mastercard</div>
                                            </div>
                                        </div>
                                        <div class="radio-checkmark"></div>
                                    </label>

                                    <label class="payment-method">
                                        <input type="radio" name="phuongthuc" value="momo" id="payment_momo">
                                        <div class="method-content">
                                            <div class="method-icon">
                                                <img src="img/icon/MoMo_Logo.png" alt="MoMo" class="momo-icon">
                                            </div>
                                            <div class="method-info">
                                                <div class="method-title">Ví MoMo</div>
                                                <div class="method-desc">Quét mã QR để thanh toán</div>
                                            </div>
                                        </div>
                                        <div class="radio-checkmark"></div>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Cột phải - Tóm tắt đơn hàng -->
                    <div class="checkout-sidebar">
                        <div class="order-summary-card">
                            <div class="summary-header">
                                <h3>Đơn hàng của bạn</h3>
                            </div>

                            <div class="order-items">
                                <?php
                                $cartSQL = "SELECT gh.*, ma.TenMonAn, bto.DonGia, ma.HinhAnh, kt.TenSize
                                       FROM GioHang gh 
                                       JOIN bienthemonan bto ON gh.MaBienThe = bto.MaBienThe 
                                       JOIN monan ma ON bto.MaMonAn = ma.MaMonAn 
                                       JOIN kichthuoc kt ON bto.MaSize = kt.MaSize
                                       WHERE gh.MaUser = ?";

                                $stmt = mysqli_prepare($conn, $cartSQL);
                                if ($stmt) {
                                    mysqli_stmt_bind_param($stmt, "i", $maUser);
                                    mysqli_stmt_execute($stmt);
                                    $cartResult = mysqli_stmt_get_result($stmt);
                                    $total = 0;

                                    if ($cartResult && mysqli_num_rows($cartResult) > 0) {
                                        while ($item = mysqli_fetch_assoc($cartResult)) {
                                            $thanhtien = $item['DonGia'] * $item['SoLuong'];
                                            $total += $thanhtien;
                                ?>
                                            <div class='order-item'>
                                                <img src='img/<?php echo $item['HinhAnh']; ?>' alt='<?php echo $item['TenMonAn']; ?>' class='item-image'>
                                                <div class='item-details'>
                                                    <div class='item-name'><?php echo $item['TenMonAn']; ?></div>
                                                    <?php if ($item['TenSize'] && $item['TenSize'] != 'Vừa'): ?>
                                                        <div class='item-meta'>Size: <?php echo $item['TenSize']; ?></div>
                                                    <?php endif; ?>
                                                    <div class='item-quantity'>Số lượng: <?php echo $item['SoLuong']; ?></div>
                                                </div>
                                                <div class='item-price'><?php echo number_format($thanhtien, 0, ',', '.'); ?>₫</div>
                                            </div>
                                <?php
                                        }
                                    } else {
                                        echo "<div class='empty-cart'>Giỏ hàng trống</div>";
                                    }
                                    mysqli_stmt_close($stmt);
                                } else {
                                    echo "<div class='error-message'>Lỗi tải giỏ hàng: " . mysqli_error($conn) . "</div>";
                                }
                                ?>
                            </div>

                            <div class="order-totals">
                                <div class="total-row">
                                    <span>Tạm tính:</span>
                                    <span><?php echo isset($total) ? number_format($total, 0, ',', '.') : '0'; ?>₫</span>
                                </div>
                                <div class="total-row">
                                    <span>Phí vận chuyển:</span>
                                    <span>0₫</span>
                                </div>
                                <div class="total-row final">
                                    <span>Tổng cộng:</span>
                                    <span class="final-amount"><?php echo isset($total) ? number_format($total, 0, ',', '.') : '0'; ?>₫</span>
                                </div>
                            </div>

                            <button type="submit" name="btnThanhToan" class="checkout-btn" <?php echo (!isset($total) || $total == 0) ? 'disabled' : ''; ?>>
                                <span class="btn-text">ĐẶT HÀNG</span>
                                <span class="btn-amount"><?php echo isset($total) ? number_format($total, 0, ',', '.') : '0'; ?>₫</span>
                            </button>

                            <div class="security-notice">
                                <div class="lock-icon">🔒</div>
                                <span>Thông tin của bạn được bảo mật an toàn</span>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script src="js/thanhtoan.js"></script>

    <?php include_once "includes/footer.php"; ?>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            var form = document.getElementById("checkoutForm");
            var radios = document.getElementsByName("phuongthuc");

            // Hàm kiểm tra phương thức đang chọn
            function updateAction() {
                var selected = document.querySelector('input[name="phuongthuc"]:checked').value;

                if (selected === 'momo') {
                    // Nếu chọn MoMo, gửi dữ liệu sang file xử lý riêng
                    form.action = "xuly_momo.php";
                } else {
                    // Nếu chọn cái khác, gửi lại chính trang này (để code PHP cũ xử lý)
                    form.action = "";
                }
            }

            // Lắng nghe sự kiện thay đổi radio button
            for (var i = 0; i < radios.length; i++) {
                radios[i].addEventListener('change', updateAction);
            }

            // Kiểm tra ngay khi tải trang (trường hợp user back lại)
            // updateAction(); // Bỏ comment nếu muốn check mặc định
        });
    </script>
</body>

</html>