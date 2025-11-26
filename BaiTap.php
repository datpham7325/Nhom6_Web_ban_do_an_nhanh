<?php
// Đã bỏ session_start() vì đã có trong includes/header.php
include_once("function/functions.php");
include_once("includes/myenv.php");

// Kết nối database để lấy số lượng giỏ hàng
$conn = mysqli_connect($db_host, $db_user, $db_password, $db_db, $db_port);

$cart_count = 0;
if (isset($_SESSION['loggedin']) && isset($_SESSION['MaUser'])) {
    $maUser = $_SESSION['MaUser'];
    $cart_sql = "SELECT SUM(SoLuong) as total FROM GioHang WHERE MaUser = ?";
    $stmt = mysqli_prepare($conn, $cart_sql);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $maUser);
        mysqli_stmt_execute($stmt);
        $cart_result = mysqli_stmt_get_result($stmt);
        if ($cart_result && mysqli_num_rows($cart_result) > 0) {
            $cart_data = mysqli_fetch_assoc($cart_result);
            $cart_count = $cart_data['total'] ?? 0;
        }
        mysqli_stmt_close($stmt);
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bài Tập - Jollibee Việt Nam</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/baitap.css">
</head>
<body>
    <!-- HEADER -->
    <?php include_once('includes/header.php'); ?>

    <!-- MAIN CONTENT -->
    <div class="main-content">
        <div class="page-header">
            <h1><i class="fas fa-book"></i> BÀI TẬP THỰC HÀNH</h1>
            <p>Hoàn thành đầy đủ các bài tập PHP & MySQL</p>
        </div>

        <div class="exercises-container">
            <!-- Progress Section -->
            <div class="progress-section">
                <h2 style="color: #d32f2f; margin-bottom: 1rem;">TIẾN ĐỘ HOÀN THÀNH</h2>
                <div class="progress-stats">
                    <div class="stat-item">
                        <div class="stat-number">8/8</div>
                        <div class="stat-label">PHP & FORM</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-number">7/7</div>
                        <div class="stat-label">MẢNG, CHUỖI & HÀM</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-number">9/9</div>
                        <div class="stat-label">PHP & MYSQL</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-number">24/24</div>
                        <div class="stat-label">TỔNG SỐ BÀI</div>
                    </div>
                </div>
            </div>

            <!-- PHP & FORM Exercises -->
            <div class="exercise-category">
                <h2><i class="fas fa-file-alt"></i> PHP & FORM (8 bài)</h2>
                <div class="exercise-list">
                    <!-- Bài 1 -->
                    <div class="exercise-item completed">
                        <div class="exercise-number">1</div>
                        <div class="exercise-title">Tính diện tích hình chữ nhật</div>
                        <div class="exercise-description">Thiết kế form tính diện tích hình chữ nhật với chiều dài và chiều rộng</div>
                        <div class="exercise-links">
                            <a href="BaiTap/Form/DientichHCN.php" class="exercise-link">
                                <i class="fas fa-external-link-alt"></i> Xem bài làm
                            </a>
                        </div>
                    </div>

                    <!-- Bài 2 -->
                    <div class="exercise-item completed">
                        <div class="exercise-number">2</div>
                        <div class="exercise-title">Tính chu vi và diện tích hình tròn</div>
                        <div class="exercise-description">Form tính chu vi và diện tích hình tròn với bán kính nhập vào</div>
                        <div class="exercise-links">
                            <a href="BaiTap/Form/Chuvi&DientichHT.php" class="exercise-link">
                                <i class="fas fa-external-link-alt"></i> Xem bài làm
                            </a>
                        </div>
                    </div>

                    <!-- Bài 3 -->
                    <div class="exercise-item completed">
                        <div class="exercise-number">3</div>
                        <div class="exercise-title">Tính tiền điện</div>
                        <div class="exercise-description">Tính số tiền điện phải thanh toán dựa trên chỉ số cũ và mới</div>
                        <div class="exercise-links">
                            <a href="BaiTap/Form/Tiendien.php" class="exercise-link">
                                <i class="fas fa-external-link-alt"></i> Xem bài làm
                            </a>
                        </div>
                    </div>

                    <!-- Bài 4 -->
                    <div class="exercise-item completed">
                        <div class="exercise-number">4</div>
                        <div class="exercise-title">Kết quả thi đại học</div>
                        <div class="exercise-description">Xác định kết quả thi đại học dựa trên điểm các môn và điểm chuẩn</div>
                        <div class="exercise-links">
                            <a href="BaiTap/Form/Ketquathi.php" class="exercise-link">
                                <i class="fas fa-external-link-alt"></i> Xem bài làm
                            </a>
                        </div>
                    </div>

                    <!-- Bài 5 -->
                    <div class="exercise-item completed">
                        <div class="exercise-number">5</div>
                        <div class="exercise-title">Tính tiền Karaoke</div>
                        <div class="exercise-description">Tính tiền Karaoke theo khung giờ và quy cách tính tiền</div>
                        <div class="exercise-links">
                            <a href="BaiTap/Form/TienKaraoke.php" class="exercise-link">
                                <i class="fas fa-external-link-alt"></i> Xem bài làm
                            </a>
                        </div>
                    </div>

                    <!-- Bài 6 -->
                    <div class="exercise-item completed">
                        <div class="exercise-number">6</div>
                        <div class="exercise-title">Phép tính trên 2 số</div>
                        <div class="exercise-description">Thực hiện các phép tính cộng, trừ, nhân, chia trên 2 số</div>
                        <div class="exercise-links">
                            <a href="BaiTap/Form/pheptinh.php" class="exercise-link">
                                <i class="fas fa-external-link-alt"></i> Trang nhập liệu
                            </a>
                            <a href="BaiTap/Form/ketquapheptinh.php" class="exercise-link secondary">
                                <i class="fas fa-calculator"></i> Trang kết quả
                            </a>
                        </div>
                    </div>

                    <!-- Bài 7 -->
                    <div class="exercise-item completed">
                        <div class="exercise-number">7</div>
                        <div class="exercise-title">Phép tính (Mở rộng)</div>
                        <div class="exercise-description">Phép tính với kiểm tra dữ liệu và xử lý số thực</div>
                        <div class="exercise-links">
                            <a href="BaiTap/Form/pheptinhV2.php" class="exercise-link">
                                <i class="fas fa-external-link-alt"></i> Trang nhập liệu
                            </a>
                            <a href="BaiTap/Form/ketquapheptinhV2.php" class="exercise-link secondary">
                                <i class="fas fa-calculator"></i> Trang kết quả
                            </a>
                        </div>
                    </div>

                    <!-- Bài 8 -->
                    <div class="exercise-item completed">
                        <div class="exercise-number">8</div>
                        <div class="exercise-title">Form và xử lý form</div>
                        <div class="exercise-description">Tạo form và xử lý thông tin khách hàng</div>
                        <div class="exercise-links">
                            <a href="BaiTap/Form/form.htm" class="exercise-link">
                                <i class="fas fa-external-link-alt"></i> Trang form
                            </a>
                            <a href="BaiTap/Form/config.php" class="exercise-link secondary">
                                <i class="fas fa-cog"></i> Trang xử lý
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- MẢNG, CHUỖI & HÀM Exercises -->
            <div class="exercise-category">
                <h2><i class="fas fa-code"></i> MẢNG, CHUỖI & HÀM (7 bài)</h2>
                <div class="exercise-list">
                    <!-- Bài 1 -->
                    <div class="exercise-item completed">
                        <div class="exercise-number">1</div>
                        <div class="exercise-title">Tạo mảng và thao tác</div>
                        <div class="exercise-description">Tạo mảng ngẫu nhiên và thực hiện các thao tác thống kê</div>
                        <div class="exercise-links">
                            <a href="BaiTap/Array/BaiTap1.php" class="exercise-link">
                                <i class="fas fa-external-link-alt"></i> Xem bài làm
                            </a>
                        </div>
                    </div>

                    <!-- Bài 2 -->
                    <div class="exercise-item completed">
                        <div class="exercise-number">2</div>
                        <div class="exercise-title">Tính tổng dãy số</div>
                        <div class="exercise-description">Tính tổng dãy số được nhập vào, các số cách nhau bằng dấu phẩy</div>
                        <div class="exercise-links">
                            <a href="BaiTap/Array/BaiTap2.php" class="exercise-link">
                                <i class="fas fa-external-link-alt"></i> Xem bài làm
                            </a>
                        </div>
                    </div>

                    <!-- Bài 3 -->
                    <div class="exercise-item completed">
                        <div class="exercise-number">3</div>
                        <div class="exercise-title">Phát sinh mảng và tính toán</div>
                        <div class="exercise-description">Phát sinh mảng ngẫu nhiên và tìm GTLN, GTNN, tính tổng</div>
                        <div class="exercise-links">
                            <a href="BaiTap/Array/BaiTap3.php" class="exercise-link">
                                <i class="fas fa-external-link-alt"></i> Xem bài làm
                            </a>
                        </div>
                    </div>

                    <!-- Bài 4 -->
                    <div class="exercise-item completed">
                        <div class="exercise-number">4</div>
                        <div class="exercise-title">Tìm kiếm trong mảng</div>
                        <div class="exercise-description">Tìm kiếm giá trị trong mảng và hiển thị vị trí tìm thấy</div>
                        <div class="exercise-links">
                            <a href="BaiTap/Array/BaiTap4.php" class="exercise-link">
                                <i class="fas fa-external-link-alt"></i> Xem bài làm
                            </a>
                        </div>
                    </div>

                    <!-- Bài 5 -->
                    <div class="exercise-item completed">
                        <div class="exercise-number">5</div>
                        <div class="exercise-title">Thay thế trong mảng</div>
                        <div class="exercise-description">Thay thế các giá trị cũ bằng giá trị mới trong mảng</div>
                        <div class="exercise-links">
                            <a href="BaiTap/Array/BaiTap5.php" class="exercise-link">
                                <i class="fas fa-external-link-alt"></i> Xem bài làm
                            </a>
                        </div>
                    </div>

                    <!-- Bài 6 -->
                    <div class="exercise-item completed">
                        <div class="exercise-number">6</div>
                        <div class="exercise-title">Sắp xếp mảng</div>
                        <div class="exercise-description">Sắp xếp mảng theo thứ tự tăng dần và giảm dần</div>
                        <div class="exercise-links">
                            <a href="BaiTap/Array/BaiTap6.php" class="exercise-link">
                                <i class="fas fa-external-link-alt"></i> Xem bài làm
                            </a>
                        </div>
                    </div>

                    <!-- Bài 7 -->
                    <div class="exercise-item completed">
                        <div class="exercise-number">7</div>
                        <div class="exercise-title">Tìm năm âm lịch</div>
                        <div class="exercise-description">Chuyển đổi năm dương lịch sang năm âm lịch và hiển thị con giáp</div>
                        <div class="exercise-links">
                            <a href="BaiTap/Array/BaiTap7.php" class="exercise-link">
                                <i class="fas fa-external-link-alt"></i> Xem bài làm
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- KẾT HỢP PHP & MYSQL Exercises -->
            <div class="exercise-category">
                <h2><i class="fas fa-database"></i> KẾT HỢP PHP & MYSQL (9 bài)</h2>
                <div class="exercise-list">
                    <!-- Bài 1 -->
                    <div class="exercise-item completed">
                        <div class="exercise-number">1</div>
                        <div class="exercise-title">Hiển thị lưới</div>
                        <div class="exercise-description">Kết nối MySQL và hiển thị dữ liệu dạng lưới thô</div>
                        <div class="exercise-links">
                            <a href="BaiTap/SQL/Bai2_1.php" class="exercise-link">
                                <i class="fas fa-external-link-alt"></i> Xem bài làm
                            </a>
                        </div>
                    </div>

                    <!-- Bài 2 -->
                    <div class="exercise-item completed">
                        <div class="exercise-number">2</div>
                        <div class="exercise-title">Lưới định dạng</div>
                        <div class="exercise-description">Hiển thị dữ liệu có định dạng với màu sắc và canh lề</div>
                        <div class="exercise-links">
                            <a href="BaiTap/SQL/Bai2_2.php" class="exercise-link">
                                <i class="fas fa-external-link-alt"></i> Xem bài làm
                            </a>
                        </div>
                    </div>

                    <!-- Bài 3 -->
                    <div class="exercise-item completed">
                        <div class="exercise-number">3</div>
                        <div class="exercise-title">Lưới tùy biến</div>
                        <div class="exercise-description">Hiển thị dữ liệu với cột giới tính được tùy biến bằng icon</div>
                        <div class="exercise-links">
                            <a href="BaiTap/SQL/Bai2_3.php" class="exercise-link">
                                <i class="fas fa-external-link-alt"></i> Xem bài làm
                            </a>
                        </div>
                    </div>

                    <!-- Bài 4 -->
                    <div class="exercise-item completed">
                        <div class="exercise-number">4</div>
                        <div class="exercise-title">Lưới phân trang</div>
                        <div class="exercise-description">Hiển thị dữ liệu có phân trang sử dụng class Pager</div>
                        <div class="exercise-links">
                            <a href="BaiTap/SQL/Bai2_4.php" class="exercise-link">
                                <i class="fas fa-external-link-alt"></i> Xem bài làm
                            </a>
                        </div>
                    </div>

                    <!-- Bài 5 -->
                    <div class="exercise-item completed">
                        <div class="exercise-number">5</div>
                        <div class="exercise-title">List đơn giản</div>
                        <div class="exercise-description">Hiển thị dữ liệu dạng list đơn giản với hình ảnh và thông tin</div>
                        <div class="exercise-links">
                            <a href="BaiTap/SQL/Bai2_5.php" class="exercise-link">
                                <i class="fas fa-external-link-alt"></i> Xem bài làm
                            </a>
                        </div>
                    </div>

                    <!-- Bài 6 -->
                    <div class="exercise-item completed">
                        <div class="exercise-number">6</div>
                        <div class="exercise-title">List dạng cột</div>
                        <div class="exercise-description">Hiển thị dữ liệu dạng list cột với nhiều sản phẩm trên một dòng</div>
                        <div class="exercise-links">
                            <a href="BaiTap/SQL/Bai2_6.php" class="exercise-link">
                                <i class="fas fa-external-link-alt"></i> Xem bài làm
                            </a>
                        </div>
                    </div>

                    <!-- Bài 7 -->
                    <div class="exercise-item completed">
                        <div class="exercise-number">7</div>
                        <div class="exercise-title">List dạng cột có link</div>
                        <div class="exercise-description">Hiển thị list có link đến trang chi tiết sản phẩm</div>
                        <div class="exercise-links">
                            <a href="BaiTap/SQL/Bai2_7.php" class="exercise-link">
                                <i class="fas fa-external-link-alt"></i> Trang list
                            </a>
                            <a href="BaiTap/SQL/Bai2_7_HienThi.php" class="exercise-link secondary">
                                <i class="fas fa-info-circle"></i> Trang chi tiết
                            </a>
                        </div>
                    </div>

                    <!-- Bài 8 -->
                    <div class="exercise-item completed">
                        <div class="exercise-number">8</div>
                        <div class="exercise-title">List chi tiết có phân trang</div>
                        <div class="exercise-description">Hiển thị list chi tiết sản phẩm có phân trang</div>
                        <div class="exercise-links">
                            <a href="BaiTap/SQL/Bai2_8.php" class="exercise-link">
                                <i class="fas fa-external-link-alt"></i> Xem bài làm
                            </a>
                        </div>
                    </div>

                    <!-- Bài 9 -->
                    <div class="exercise-item completed">
                        <div class="exercise-number">9</div>
                        <div class="exercise-title">Tìm kiếm đơn giản</div>
                        <div class="exercise-description">Thực hiện tìm kiếm sản phẩm theo tên và hiển thị kết quả</div>
                        <div class="exercise-links">
                            <a href="BaiTap/SQL/Bai2_9.php" class="exercise-link">
                                <i class="fas fa-external-link-alt"></i> Xem bài làm
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- FOOTER -->
    <?php include_once('includes/footer.php'); ?>

    <!-- JavaScript -->
    <script src="js/baitap.js"></script>
</body>
</html>