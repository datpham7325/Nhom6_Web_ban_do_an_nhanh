<?php
include_once "includes/header.php";
?>

<div class="container">
    <div class="page-header">
        <h1>Điều Khoản Sử Dụng</h1>
        <p>Quy định và điều khoản sử dụng dịch vụ</p>
    </div>

    <div class="content-container">
        <div class="profile-layout">
            <!-- Sidebar menu điều hướng tài khoản -->
            <div class="profile-sidebar">
                <div class="sidebar-menu">
                    <a href="ThongTinTaiKhoan.php" class="menu-item">
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
                    <a href="DieuKhoan.php" class="menu-item active">
                        <span class="menu-icon">📄</span>
                        Điều khoản sử dụng
                    </a>
                    <a href="BaoMat.php" class="menu-item">
                        <span class="menu-icon">🔒</span>
                        Chính sách bảo mật
                    </a>
                </div>
            </div>

            <!-- Nội dung chính của trang điều khoản -->
            <div class="profile-content">
                <div class="profile-card">
                    <div class="card-body">
                        <div class="terms-content">
                            <h2>Điều Khoản Dịch Vụ</h2>
                            <!-- Hiển thị ngày cập nhật điều khoản -->
                            <p class="last-updated">Cập nhật lần cuối: <?php echo date('d/m/Y'); ?></p>

                            <!-- Phần 1: Điều khoản chấp nhận -->
                            <div class="terms-section">
                                <h3>1. Chấp nhận điều khoản</h3>
                                <p>Bằng việc truy cập và sử dụng website JOLIBEE, bạn đồng ý tuân thủ và bị ràng buộc bởi các điều khoản và điều kiện sử dụng được quy định dưới đây.</p>
                            </div>

                            <!-- Phần 2: Điều khoản đăng ký tài khoản -->
                            <div class="terms-section">
                                <h3>2. Đăng ký tài khoản</h3>
                                <p>Để sử dụng một số tính năng của website, bạn cần đăng ký tài khoản. Bạn cam kết:</p>
                                <ul>
                                    <li>Cung cấp thông tin chính xác, đầy đủ và cập nhật</li>
                                    <li>Bảo mật thông tin đăng nhập</li>
                                    <li>Chịu trách nhiệm cho mọi hoạt động xảy ra dưới tài khoản của bạn</li>
                                </ul>
                            </div>

                            <!-- Phần 3: Điều khoản đặt hàng và thanh toán -->
                            <div class="terms-section">
                                <h3>3. Đặt hàng và thanh toán</h3>
                                <p>Khi đặt hàng trên website, bạn đồng ý:</p>
                                <ul>
                                    <li>Cung cấp thông tin giao hàng chính xác</li>
                                    <li>Thanh toán đầy đủ theo giá niêm yết</li>
                                    <li>Chấp nhận các điều kiện về hủy đơn hàng và hoàn tiền</li>
                                </ul>
                            </div>

                            <!-- Phần 4: Điều khoản sở hữu trí tuệ -->
                            <div class="terms-section">
                                <h3>4. Quyền sở hữu trí tuệ</h3>
                                <p>Toàn bộ nội dung trên website bao gồm logo, hình ảnh, văn bản đều thuộc quyền sở hữu của JOLIBEE. Bạn không được phép sao chép, phân phối mà không có sự cho phép bằng văn bản.</p>
                            </div>

                            <!-- Phần 5: Điều khoản giới hạn trách nhiệm -->
                            <div class="terms-section">
                                <h3>5. Giới hạn trách nhiệm</h3>
                                <p>JOLIBEE không chịu trách nhiệm cho bất kỳ thiệt hại nào phát sinh từ việc sử dụng website hoặc không thể sử dụng website.</p>
                            </div>

                            <!-- Phần 6: Điều khoản thay đổi -->
                            <div class="terms-section">
                                <h3>6. Thay đổi điều khoản</h3>
                                <p>Chúng tôi có quyền thay đổi các điều khoản này vào bất kỳ lúc nào. Việc tiếp tục sử dụng website sau khi có thay đổi được xem như bạn đã chấp nhận các thay đổi đó.</p>
                            </div>

                            <!-- Phần 7: Thông tin liên hệ -->
                            <div class="terms-section">
                                <h3>7. Liên hệ</h3>
                                <p>Nếu bạn có bất kỳ câu hỏi nào về các điều khoản này, vui lòng liên hệ:</p>
                                <ul>
                                    <li>Email: support@jolibee.com</li>
                                    <li>Hotline: 1900 1234</li>
                                    <li>Địa chỉ: 123 Nguyễn Văn Linh, Quận 7, TP.HCM</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Liên kết file CSS và JavaScript cho trang điều khoản -->
<link rel="stylesheet" href="css/dieukhoan.css">
<script src="js/dieukhoan.js"></script>

<?php include_once "includes/footer.php"; ?>