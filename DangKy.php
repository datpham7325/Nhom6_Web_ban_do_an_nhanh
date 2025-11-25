<!DOCTYPE html>
<html lang="vi">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Đăng ký tài khoản - Jollibee</title>
  <link rel="stylesheet" href="css/DangKy.css">
</head>

<body>

  <?php
  // KHỞI TẠO CÁC BIẾN
  $loi = $thanhcong = ""; 
  $ho = $ten = $sdt = $email = ""; 

  // XỬ LÝ KHI FORM ĐƯỢC SUBMIT
  if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['btnsend'])) {

    // LẤY DỮ LIỆU TỪ FORM
    $ho = trim($_POST['ho']);
    $ten = trim($_POST['ten']);
    $sdt = trim($_POST['sdt']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirmP = $_POST['confirmP'];

    // ===== PHẦN VALIDATION (KIỂM TRA DỮ LIỆU) =====

    // 1. Kiểm tra để trống
    if (empty($ho) || empty($ten) || empty($sdt) || empty($password) || empty($confirmP)) {
      $loi = "Vui lòng điền đầy đủ các trường bắt buộc!";
    }
    // 2. Kiểm tra độ dài mật khẩu (MỚI THÊM)
    elseif (strlen($password) < 6) {
      $loi = "Mật khẩu phải có ít nhất 6 ký tự!";
    }
    // 3. So khớp mật khẩu xác nhận (MỚI THÊM)
    elseif ($password !== $confirmP) {
      $loi = "Mật khẩu xác nhận không trùng khớp!";
    }
    // 4. Kiểm tra định dạng Email
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
      $loi = "Email không hợp lệ!";
    }
    // 5. Kiểm tra định dạng Số điện thoại
    elseif (!preg_match("/^[0-9]{10,11}$/", $sdt)) {
      $loi = "Số điện thoại phải từ 10-11 số!";
    }
    // ===== NẾU DỮ LIỆU HỢP LỆ, TIẾN HÀNH ĐĂNG KÝ =====
    else {
      include_once("includes/myenv.php");
      $mysqli = new mysqli($db_host, $db_user, $db_password, $db_db, $db_port);

      if ($mysqli->connect_error) {
        $loi = "Lỗi kết nối CSDL: " . $mysqli->connect_error;
      } else {
        // Kiểm tra xem Email hoặc SĐT đã tồn tại chưa
        $check = $mysqli->prepare("SELECT MaUser FROM `Users` WHERE Email = ? OR SDT = ?");
        $check->bind_param("ss", $email, $sdt);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
          $loi = "Email hoặc số điện thoại đã được sử dụng!";
        } else {
          // ===== MÃ HÓA MẬT KHẨU SHA-256 (ĐỂ KHỚP VỚI LOGIN) =====
          $hashed_password = hash('sha256', $password, true);

          // Thêm người dùng mới vào Database
          $insert = $mysqli->prepare("
                    INSERT INTO `Users` (Ho, Ten, SDT, Email, MatKhau, QuyenHan) 
                    VALUES (?, ?, ?, ?, ?, 'khachhang')
                ");
          $insert->bind_param("sssss", $ho, $ten, $sdt, $email, $hashed_password);

          if ($insert->execute()) {
            // ===== ĐĂNG KÝ THÀNH CÔNG -> CHUYỂN HƯỚNG =====
            echo "<script>
                alert('Đăng ký thành công! Bạn sẽ được chuyển đến trang đăng nhập.');
                window.location.href = 'DangNhap.php';
            </script>";
            exit();
          } else {
            $loi = "Lỗi hệ thống: " . $insert->error;
          }
          $insert->close();
        }
        $check->close();
        $mysqli->close();
      }
    }
  }
  ?>

  <div class="register-container">
    <h1>ĐĂNG KÝ TÀI KHOẢN</h1>

    <?php if ($loi): ?>
      <div class="alert alert-error"><?php echo $loi; ?></div>
    <?php endif; ?>

    <form method="POST" action="" class="register-form">
      <table class="form-table">
        <tr>
          <td colspan="2"><input type="text" name="ho" placeholder="Họ*" value="<?php echo htmlspecialchars($ho); ?>" required></td>
        </tr>
        <tr>
          <td colspan="2"><input type="text" name="ten" placeholder="Tên*" value="<?php echo htmlspecialchars($ten); ?>" required></td>
        </tr>
        <tr>
          <td colspan="2"><input type="text" name="sdt" placeholder="Số điện thoại*" value="<?php echo htmlspecialchars($sdt); ?>" required></td>
        </tr>
        <tr>
          <td colspan="2"><input type="email" name="email" placeholder="E-mail*" value="<?php echo htmlspecialchars($email); ?>" required></td>
        </tr>
        <tr>
          <td colspan="2"><input type="password" name="password" placeholder="Mật khẩu (Tối thiểu 6 ký tự)*" required></td>
        </tr>
        <tr>
          <td colspan="2"><input type="password" name="confirmP" placeholder="Xác nhận mật khẩu*" required></td>
        </tr>
        <tr>
          <td><input type="submit" value="Đăng ký" name="btnsend" class="btn-register"></td>
          <td>Bạn đã có tài khoản? <a href="DangNhap.php" class="login-link">Đăng nhập</a></td>
        </tr>
      </table>
    </form>
  </div>

  <script src="js/DangKy.js" defer></script>
</body>
</html>