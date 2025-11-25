<?php
// Include file header để có kết nối database và session
include_once "includes/header.php";

// KIỂM TRA XEM USER ĐÃ ĐĂNG NHẬP CHƯA
// (Trang này có thể cho phép cả user chưa đăng nhập xem)
if (!isset($_SESSION['loggedin'])) {
    // Có thể chuyển hướng đến trang đăng nhập hoặc để xem bình thường
    // header("Location: DangNhap.php");
    // exit();
}
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chính Sách Bảo Mật - Jollibee</title>
    <!-- Link đến file CSS riêng cho trang Bảo Mật -->
    <link rel="stylesheet" href="css/baomat.css">
</head>

<body>
    <div class="container">
        <!-- HEADER CHÍNH CỦA TRANG -->
        <div class="page-header">
            <h1>Chính Sách Bảo Mật</h1>
            <p>Bảo vệ thông tin cá nhân của bạn</p>
        </div>

        <!-- PHẦN NỘI DUNG CHÍNH -->
        <div class="content-container">
            <!-- LAYOUT 2 CỘT: SIDEBAR VÀ NỘI DUNG -->
            <div class="profile-layout">
                <!-- ===== SIDEBAR MENU ĐIỀU HƯỚNG ===== -->
                <div class="profile-sidebar">
                    <div class="sidebar-menu">
                        <!-- MENU ITEM: THÔNG TIN TÀI KHOẢN -->
                        <a href="ThongTinTaiKhoan.php" class="menu-item">
                            <span class="menu-icon">👤</span>
                            Thông tin tài khoản
                        </a>

                        <!-- MENU ITEM: ĐƠN HÀNG -->
                        <a href="DonHang.php" class="menu-item">
                            <span class="menu-icon">📦</span>
                            Đơn hàng của tôi
                        </a>

                        <!-- MENU ITEM: ĐÁNH GIÁ -->
                        <a href="DanhGia.php" class="menu-item">
                            <span class="menu-icon">⭐</span>
                            Đánh giá
                        </a>

                        <!-- MENU ITEM: ĐIỀU KHOẢN -->
                        <a href="DieuKhoan.php" class="menu-item">
                            <span class="menu-icon">📄</span>
                            Điều khoản sử dụng
                        </a>

                        <!-- MENU ITEM: BẢO MẬT (ACTIVE) -->
                        <a href="BaoMat.php" class="menu-item active">
                            <span class="menu-icon">🔒</span>
                            Chính sách bảo mật
                        </a>
                    </div>
                </div>

                <!-- ===== NỘI DUNG CHÍNH CHÍNH SÁCH BẢO MẬT ===== -->
                <div class="profile-content">
                    <div class="profile-card">
                        <div class="card-body">
                            <!-- PHẦN NỘI DUNG CHÍNH SÁCH BẢO MẬT -->
                            <div class="privacy-content">
                                <!-- TIÊU ĐỀ CHÍNH -->
                                <h2>Chính Sách Bảo Mật Thông Tin</h2>

                                <!-- THÔNG BÁO CẬP NHẬT TỰ ĐỘNG -->
                                <p class="last-updated">Cập nhật lần cuối: <?php echo date('d/m/Y'); ?></p>

                                <!-- MỤC 1: THU THẬP THÔNG TIN -->
                                <div class="privacy-section">
                                    <h3>1. Thu thập thông tin</h3>
                                    <p>Chúng tôi thu thập các thông tin sau khi bạn sử dụng dịch vụ:</p>
                                    <ul>
                                        <!-- THÔNG TIN CÁ NHÂN CƠ BẢN -->
                                        <li><strong>Thông tin cá nhân:</strong> Họ tên, email, số điện thoại, địa chỉ</li>

                                        <!-- THÔNG TIN GIAO DỊCH -->
                                        <li><strong>Thông tin giao dịch:</strong> Lịch sử đơn hàng, phương thức thanh toán</li>

                                        <!-- THÔNG TIN KỸ THUẬT -->
                                        <li><strong>Thông tin kỹ thuật:</strong> Địa chỉ IP, loại trình duyệt, thiết bị truy cập</li>
                                    </ul>
                                </div>

                                <!-- MỤC 2: MỤC ĐÍCH SỬ DỤNG -->
                                <div class="privacy-section">
                                    <h3>2. Mục đích sử dụng</h3>
                                    <p>Thông tin của bạn được sử dụng để:</p>
                                    <ul>
                                        <!-- CÁC MỤC ĐÍCH SỬ DỤNG CHÍNH -->
                                        <li>Xử lý đơn hàng và giao hàng</li>
                                        <li>Cung cấp dịch vụ hỗ trợ khách hàng</li>
                                        <li>Cải thiện chất lượng dịch vụ</li>
                                        <li>Gửi thông tin khuyến mãi (nếu bạn đồng ý)</li>
                                    </ul>
                                </div>

                                <!-- MỤC 3: BIỆN PHÁP BẢO VỆ -->
                                <div class="privacy-section">
                                    <h3>3. Bảo vệ thông tin</h3>
                                    <p>Chúng tôi cam kết bảo vệ thông tin của bạn bằng các biện pháp:</p>
                                    <ul>
                                        <!-- CÁC BIỆN PHÁP BẢO MẬT -->
                                        <li>Mã hóa dữ liệu nhạy cảm</li>
                                        <li>Giới hạn quyền truy cập</li>
                                        <li>Bảo mật vật lý tại data center</li>
                                        <li>Kiểm tra bảo mật định kỳ</li>
                                    </ul>
                                </div>

                                <!-- MỤC 4: CHIA SẺ THÔNG TIN -->
                                <div class="privacy-section">
                                    <h3>4. Chia sẻ thông tin</h3>
                                    <p>Chúng tôi không bán hoặc cho thuê thông tin cá nhân của bạn. Thông tin chỉ được chia sẻ trong các trường hợp:</p>
                                    <ul>
                                        <!-- CÁC TRƯỜNG HỢP CHIA SẺ THÔNG TIN -->
                                        <li>Đối tác giao hàng (chỉ thông tin cần thiết cho giao hàng)</li>
                                        <li>Nhà cung cấp dịch vụ thanh toán</li>
                                        <li>Theo yêu cầu pháp luật</li>
                                    </ul>
                                </div>

                                <!-- MỤC 5: QUYỀN CỦA NGƯỜI DÙNG -->
                                <div class="privacy-section">
                                    <h3>5. Quyền của bạn</h3>
                                    <p>Bạn có quyền:</p>
                                    <ul>
                                        <!-- CÁC QUYỀN CỦA NGƯỜI DÙNG -->
                                        <li>Truy cập và chỉnh sửa thông tin cá nhân</li>
                                        <li>Yêu cầu xóa tài khoản</li>
                                        <li>Ngừng nhận thông tin marketing</li>
                                        <li>Khiếu nại về việc xử lý thông tin</li>
                                    </ul>
                                </div>

                                <!-- MỤC 6: CHÍNH SÁCH COOKIE -->
                                <div class="privacy-section">
                                    <h3>6. Cookie</h3>
                                    <p>Website sử dụng cookie để:</p>
                                    <ul>
                                        <!-- CÁC MỤC ĐÍCH SỬ DỤNG COOKIE -->
                                        <li>Ghi nhớ đăng nhập</li>
                                        <li>Lưu giỏ hàng</li>
                                        <li>Phân tích truy cập</li>
                                        <li>Cá nhân hóa trải nghiệm</li>
                                    </ul>
                                    <p>Bạn có thể tắt cookie trong trình duyệt, nhưng một số tính năng có thể không hoạt động.</p>
                                </div>

                                <!-- MỤC 7: THÔNG TIN LIÊN HỆ -->
                                <div class="privacy-section">
                                    <h3>7. Liên hệ</h3>
                                    <p>Nếu có bất kỳ câu hỏi về chính sách bảo mật, vui lòng liên hệ:</p>
                                    <ul>
                                        <!-- THÔNG TIN LIÊN HỆ BỘ PHẬN BẢO MẬT -->
                                        <li>Bộ phận Bảo mật: security@jolibee.com</li>
                                        <li>Hotline: 1900 1234</li>
                                        <li>Thời gian làm việc: 8:00 - 17:00 các ngày trong tuần</li>
                                    </ul>
                                </div>

                                <!-- THÔNG BÁO QUAN TRỌNG -->
                                <div class="privacy-notice">
                                    <h4>📢 Lưu ý quan trọng</h4>
                                    <p>Chúng tôi có thể cập nhật chính sách bảo mật này. Thay đổi sẽ được thông báo trên website. Việc tiếp tục sử dụng dịch vụ sau khi có thay đổi được xem như bạn đã chấp nhận.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Link đến file JavaScript xử lý tương tác -->
    <script src="js/baomat.js"></script>
</body>

</html>

<?php
// Include file footer để đóng kết nối và hiển thị phần chân trang
include_once "includes/footer.php";
?>