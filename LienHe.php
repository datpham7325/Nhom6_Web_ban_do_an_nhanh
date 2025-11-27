<?php 
// Include header
include_once "includes/header.php"; 

// 🔥 KẾT NỐI DATABASE
include_once("includes/myenv.php");
$conn = mysqli_connect($db_host, $db_user, $db_password, $db_db, $db_port);

if (!$conn) {
    die("❌ Lỗi kết nối database: " . mysqli_connect_error());
}

$success = "";
$error = "";

// 🔥 XỬ LÝ FORM LIÊN HỆ
// Kiểm tra hidden field 'is_submit' để tránh lỗi khi nút bị disable bởi JS
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['is_submit'])) {
    
    // Lấy dữ liệu
    $hoTen = $_POST['hoten'] ?? '';
    $email = $_POST['email'] ?? '';
    $sdt = $_POST['sdt'] ?? '';
    $noiDung = $_POST['noidung'] ?? '';

    // Validate cơ bản
    if (empty($hoTen) || empty($email) || empty($sdt) || empty($noiDung)) {
        $error = "❌ Vui lòng điền đầy đủ thông tin.";
    } else {
        try {
            // SQL Insert
            $sql = "INSERT INTO LienHe (HoTen, Email, SDT, NoiDung) VALUES (?, ?, ?, ?)";
            
            $stmt = mysqli_prepare($conn, $sql);
            
            if ($stmt) {
                // Bind param: ssss (4 strings)
                mysqli_stmt_bind_param($stmt, "ssss", $hoTen, $email, $sdt, $noiDung);
                
                if (mysqli_stmt_execute($stmt)) {
                    $success = "✅ Cảm ơn bạn đã liên hệ! Chúng tôi sẽ phản hồi sớm nhất.";
                    // Reset form
                    $_POST = array();
                } else {
                    $error = "❌ Lỗi gửi tin nhắn: " . mysqli_stmt_error($stmt);
                }
                mysqli_stmt_close($stmt);
            } else {
                $error = "❌ Lỗi hệ thống: " . mysqli_error($conn);
            }
        } catch (Exception $e) {
            $error = "❌ Lỗi: " . $e->getMessage();
        }
    }
}
?>

<style>
    .alert { padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem; border: 2px solid; font-weight: bold; }
    .alert-success { background: #e8f5e8; color: #2e7d32; border-color: #c8e6c9; }
    .alert-error { background: #ffebee; color: #d32f2f; border-color: #ffcdd2; }
    .submit-btn:disabled { background: #ccc !important; cursor: not-allowed; }
</style>

<div class="container">
    <div class="page-header">
        <h1>LIÊN HỆ</h1>
        <p>Chúng tôi luôn lắng nghe bạn!</p>
    </div>

    <div class="content-container">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 3rem;">
            <div>
                <h3 style="color: #d32f2f; margin-bottom: 2rem;">THÔNG TIN LIÊN HỆ</h3>

                <div style="margin-bottom: 2rem;">
                    <h4 style="color: #f57c00; margin-bottom: 0.5rem;">📍 ĐỊA CHỈ</h4>
                    <p>123 Nguyễn Văn Linh, Quận 7, TP. Hồ Chí Minh</p>
                </div>

                <div style="margin-bottom: 2rem;">
                    <h4 style="color: #f57c00; margin-bottom: 0.5rem;">📞 HOTLINE</h4>
                    <p>1900 1234</p>
                </div>

                <div style="margin-bottom: 2rem;">
                    <h4 style="color: #f57c00; margin-bottom: 0.5rem;">📧 EMAIL</h4>
                    <p>contact@jollibee.vn</p>
                </div>

                <div style="margin-bottom: 2rem;">
                    <h4 style="color: #f57c00; margin-bottom: 0.5rem;">🕒 GIỜ MỞ CỬA</h4>
                    <p>Thứ 2 - Chủ Nhật: 7:00 - 22:00</p>
                </div>
            </div>

            <div>
                <h3 style="color: #d32f2f; margin-bottom: 2rem;">GỬI TIN NHẮN CHO CHÚNG TÔI</h3>

                <?php if (!empty($success)): ?>
                    <div class="alert alert-success"><?php echo $success; ?></div>
                <?php elseif (!empty($error)): ?>
                    <div class="alert alert-error"><?php echo $error; ?></div>
                <?php endif; ?>

                <form method="POST" action="" style="display: flex; flex-direction: column; gap: 1rem;" id="contactForm">
                    
                    <input type="hidden" name="is_submit" value="1">

                    <input type="text" name="hoten" placeholder="Họ và tên *" required
                        value="<?php echo htmlspecialchars($_POST['hoten'] ?? $_SESSION['HoTen'] ?? ''); ?>"
                        style="padding: 1rem; border: 2px solid #ffe0b2; border-radius: 8px;">

                    <input type="email" name="email" placeholder="Email *" required
                        value="<?php echo htmlspecialchars($_POST['email'] ?? $_SESSION['Email'] ?? ''); ?>"
                        style="padding: 1rem; border: 2px solid #ffe0b2; border-radius: 8px;">

                    <input type="tel" name="sdt" placeholder="Số điện thoại *" required
                        value="<?php echo htmlspecialchars($_POST['sdt'] ?? ''); ?>"
                        style="padding: 1rem; border: 2px solid #ffe0b2; border-radius: 8px;">

                    <textarea name="noidung" placeholder="Nội dung tin nhắn *" rows="5" required
                        style="padding: 1rem; border: 2px solid #ffe0b2; border-radius: 8px; font-family: inherit;"><?php echo htmlspecialchars($_POST['noidung'] ?? ''); ?></textarea>

                    <button type="submit" class="submit-btn" id="submitBtn"
                        style="padding: 1rem; background: #d32f2f; color: white; border: none; border-radius: 8px; font-weight: bold; cursor: pointer; transition: 0.3s;">
                        GỬI TIN NHẮN
                    </button>
                </form>
            </div>
        </div>

        <div style="margin-top: 3rem;">
            <h3 style="color: #d32f2f; margin-bottom: 1rem; text-align: center;">BẢN ĐỒ</h3>
            <div style="background: #f5f5f5; padding: 2rem; border-radius: 15px; text-align: center;">
                <p>📍 Bản đồ sẽ được hiển thị tại đây</p>
                <p style="color: #666; margin-top: 1rem;">Cửa hàng Jollibee Quận 7, TP. Hồ Chí Minh</p>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Ẩn thông báo sau 5s
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 500);
        }, 5000);
    });

    // Hiệu ứng nút khi click
    const form = document.getElementById('contactForm');
    const btn = document.getElementById('submitBtn');
    
    if(form) {
        form.addEventListener('submit', function() {
            btn.innerHTML = '⏳ Đang gửi...';
            // Không cần disable btn ở đây vì đã có hidden input 'is_submit' xử lý ở PHP
            // Nhưng để UX tốt hơn thì có thể disable nhẹ
            btn.style.opacity = '0.7';
        });
    }
});
</script>

<?php 
// Đóng kết nối
if(isset($conn)) mysqli_close($conn);
include_once "includes/footer.php"; 
?>