<!DOCTYPE html>
<html lang="vi">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Đăng ký tài khoản - Jollibee</title>
  <!-- Link đến file CSS riêng cho trang Đăng Ký -->
  <link rel="stylesheet" href="css/DangKy.css">
</head>

<body>

  <?php
  // KHỞI TẠO CÁC BIẾN ĐỂ LƯU THÔNG BÁO VÀ DỮ LIỆU FORM
  $loi = $thanhcong = ""; // Biến lưu thông báo lỗi và thành công
  $ho = $ten = $sdt = $email = ""; // Biến lưu giá trị form để giữ lại khi có lỗi

  // XỬ LÝ KHI FORM ĐƯỢC SUBMIT (NGƯỜI DÙNG NHẤN NÚT ĐĂNG KÝ)
  if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['btnsend'])) {

    // LẤY VÀ LÀM SẠCH DỮ LIỆU TỪ FORM
    $ho = trim($_POST['ho']); // trim() để xóa khoảng trắng thừa
    $ten = trim($_POST['ten']);
    $sdt = trim($_POST['sdt']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirmP = $_POST['confirmP'];

    // ===== KIỂM TRA VALIDATION =====

    // KIỂM TRA CÁC TRƯỜNG BẮT BUỘC KHÔNG ĐƯỢC ĐỂ TRỐNG
    if (empty($ho) || empty($ten) || empty($sdt) || empty($password) || empty($confirmP)) {
      $loi = "Vui lòng điền đầy đủ các trường bắt buộc!";
    }
    // KIỂM TRA MẬT KHẨU XÁC NHẬN CÓ KHỚP VỚI MẬT KHẨU KHÔNG
    elseif ($password !== $confirmP) {
      $loi = "Mật khẩu xác nhận không khớp!";
    }
    // KIỂM TRA ĐỊNH DẠNG EMAIL CÓ HỢP LỆ KHÔNG
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
      $loi = "Email không hợp lệ!";
    }
    // KIỂM TRA SỐ ĐIỆN THOẠI CÓ ĐÚNG ĐỊNH DẠNG 10-11 SỐ KHÔNG
    elseif (!preg_match("/^[0-9]{10,11}$/", $sdt)) {
      $loi = "Số điện thoại phải 10-11 số!";
    }
    // NẾU TẤT CẢ VALIDATION ĐỀU PASS, TIẾN HÀNH ĐĂNG KÝ
    else {
      // KẾT NỐI DATABASE
      include_once("includes/myenv.php");
      $mysqli = new mysqli($db_host, $db_user, $db_password, $db_db, $db_port);

      // KIỂM TRA KẾT NỐI DATABASE CÓ THÀNH CÔNG KHÔNG
      if ($mysqli->connect_error) {
        $loi = "Lỗi kết nối CSDL: " . $mysqli->connect_error;
      } else {
        // ===== KIỂM TRA EMAIL VÀ SĐT ĐÃ TỒN TẠI CHƯA =====
        $check = $mysqli->prepare("SELECT MaUser FROM `Users` WHERE Email = ? OR SDT = ?");
        $check->bind_param("ss", $email, $sdt); // Bind 2 tham số string
        $check->execute();
        $check->store_result(); // Lưu kết quả để đếm số bản ghi

        // NẾU ĐÃ CÓ USER VỚI EMAIL HOẶC SĐT NÀY
        if ($check->num_rows > 0) {
          $loi = "Email hoặc số điện thoại đã được sử dụng!";
        } else {
          // ===== MÃ HÓA MẬT KHẨU TRƯỚC KHI LƯU VÀO DATABASE =====
          $hashed_password = password_hash($password, PASSWORD_DEFAULT);

          // ===== THỰC HIỆN INSERT USER MỚI VÀO DATABASE =====
          $insert = $mysqli->prepare("
                    INSERT INTO `Users` (Ho, Ten, SDT, Email, MatKhau, QuyenHan) 
                    VALUES (?, ?, ?, ?, ?, 'khachhang')
                ");
          $insert->bind_param("sssss", $ho, $ten, $sdt, $email, $hashed_password);

          // THỰC THI CÂU LỆNH INSERT
          if ($insert->execute()) {
            $thanhcong = "Đăng ký thành công! Vui lòng đăng nhập.";
            // XÓA DỮ LIỆU FORM SAU KHI ĐĂNG KÝ THÀNH CÔNG
            $ho = $ten = $sdt = $email = "";
          } else {
            $loi = "Lỗi: " . $insert->error;
          }
          $insert->close(); // Đóng prepared statement
        }
        $check->close(); // Đóng prepared statement
        $mysqli->close(); // Đóng kết nối database
      }
    }
  }
  ?>

  <!-- PHẦN GIAO DIỆN HTML -->
  <div class="register-container">
    <!-- TIÊU ĐỀ TRANG -->
    <h1>ĐĂNG KÝ TÀI KHOẢN</h1>

    <!-- HIỂN THỊ THÔNG BÁO LỖI NẾU CÓ -->
    <?php if ($loi): ?>
      <div class="alert alert-error">
        <?php echo $loi; ?>
      </div>
    <?php endif; ?>

    <!-- HIỂN THỊ THÔNG BÁO THÀNH CÔNG NẾU CÓ -->
    <?php if ($thanhcong): ?>
      <div class="alert alert-success">
        <?php echo $thanhcong; ?>
      </div>
    <?php endif; ?>

    <!-- FORM ĐĂNG KÝ -->
    <form method="POST" action="" class="register-form">
      <table class="form-table">
        <!-- TRƯỜNG NHẬP HỌ -->
        <tr>
          <td colspan="2">
            <input type="text" name="ho" placeholder="Họ*"
              value="<?php echo isset($ho) ? htmlspecialchars($ho) : ''; ?>" required>
          </td>
        </tr>

        <!-- TRƯỜNG NHẬP TÊN -->
        <tr>
          <td colspan="2">
            <input type="text" name="ten" placeholder="Tên*"
              value="<?php echo isset($ten) ? htmlspecialchars($ten) : ''; ?>" required>
          </td>
        </tr>

        <!-- TRƯỜNG NHẬP SỐ ĐIỆN THOẠI -->
        <tr>
          <td colspan="2">
            <input type="text" name="sdt" placeholder="Số điện thoại*"
              value="<?php echo isset($sdt) ? htmlspecialchars($sdt) : ''; ?>" required>
          </td>
        </tr>

        <!-- TRƯỜNG NHẬP EMAIL -->
        <tr>
          <td colspan="2">
            <input type="email" name="email" placeholder="E-mail*"
              value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>" required>
          </td>
        </tr>

        <!-- TRƯỜNG NHẬP MẬT KHẨU -->
        <tr>
          <td colspan="2">
            <input type="password" name="password" placeholder="Mật khẩu*" required>
          </td>
        </tr>

        <!-- TRƯỜNG XÁC NHẬN MẬT KHẨU -->
        <tr>
          <td colspan="2">
            <input type="password" name="confirmP" placeholder="Xác nhận mật khẩu*" required>
          </td>
        </tr>

        <!-- HÀNG CHỨA NÚT ĐĂNG KÝ VÀ LINK ĐĂNG NHẬP -->
        <tr>
          <td>
            <!-- NÚT SUBMIT FORM ĐĂNG KÝ -->
            <input type="submit" value="Đăng ký" name="btnsend" class="btn-register">
          </td>
          <td>
            <!-- LINK CHUYỂN ĐẾN TRANG ĐĂNG NHẬP -->
            Bạn đã có tài khoản? <a href="DangNhap.php" class="login-link">Đăng nhập</a>
          </td>
        </tr>
      </table>
    </form>
  </div>

  <!-- Link đến file JavaScript xử lý tương tác -->
  <script src="js/DangKy.js" defer></script>

</body>

</html>