<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Đăng ký tài khoản</title>
  <link rel="stylesheet" href="css/DangKy.css">
</head>

<body>

  <?php
  // XÓA session_start() — KHÔNG DÙNG SESSION

  $loi = $thanhcong = "";
  $ho = $ten = $sdt = $email = "";

  if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['btnsend'])) {
    $ho = trim($_POST['ho']);
    $ten = trim($_POST['ten']);
    $sdt = trim($_POST['sdt']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirmP = $_POST['confirmP'];

    if (empty($ho) || empty($ten) || empty($sdt) || empty($password) || empty($confirmP)) {
      $loi = "Vui lòng điền đầy đủ các trường bắt buộc!";
    } elseif ($password !== $confirmP) {
      $loi = "Mật khẩu xác nhận không khớp!";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
      $loi = "Email không hợp lệ!";
    } elseif (!preg_match("/^[0-9]{10,11}$/", $sdt)) {
      $loi = "Số điện thoại phải 10-11 số!";
    } else {
      include_once("myenv.php");
      $mysqli = new mysqli($db_host, $db_user, $db_password, $db_db, $db_port);

      if ($mysqli->connect_error) {
        $loi = "Lỗi kết nối CSDL: " . $mysqli->connect_error;
      } else {
        $check = $mysqli->prepare("SELECT MaUser FROM `Users` WHERE Email = ? OR SDT = ?");
        $check->bind_param("ss", $email, $sdt);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
          $loi = "Email hoặc số điện thoại đã được sử dụng!";
        } else {
          $hashed_password = password_hash($password, PASSWORD_DEFAULT);

          $insert = $mysqli->prepare("
                    INSERT INTO `Users` (Ho, Ten, SDT, Email, MatKhau, QuyenHan) 
                    VALUES (?, ?, ?, ?, ?, 'khachhang')
                ");
          $insert->bind_param("sssss", $ho, $ten, $sdt, $email, $hashed_password);

          if ($insert->execute()) {
            $thanhcong = "Đăng ký thành công! Vui lòng đăng nhập.";
            $ho = $ten = $sdt = $email = "";
          } else {
            $loi = "Lỗi: " . $insert->error;
          }
          $insert->close();
        }
        $check->close();
        $mysqli->close();
      }
    }
  }
  ?>

  <div>
    <h1>ĐĂNG KÝ TÀI KHOẢN</h1>

    <?php if ($loi): ?>
      <p class="error"><?php echo $loi; ?></p>
    <?php endif; ?>

    <?php if ($thanhcong): ?>
      <p class="success"><?php echo $thanhcong; ?></p>
    <?php endif; ?>

    <form method="POST" action="">
      <table>
        <tr>
          <td colspan="2">
            <input type="text" name="ho" placeholder="Họ*"
              value="<?php echo isset($ho) ? htmlspecialchars($ho) : ''; ?>" required>
          </td>
        </tr>
        <tr>
          <td colspan="2">
            <input type="text" name="ten" placeholder="Tên*"
              value="<?php echo isset($ten) ? htmlspecialchars($ten) : ''; ?>" required>
          </td>
        </tr>
        <tr>
          <td colspan="2">
            <input type="text" name="sdt" placeholder="Số điện thoại*"
              value="<?php echo isset($sdt) ? htmlspecialchars($sdt) : ''; ?>" required>
          </td>
        </tr>
        <tr>
          <td colspan="2">
            <input type="email" name="email" placeholder="E-mail*"
              value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>" required>
          </td>
        </tr>
        <tr>
          <td colspan="2">
            <input type="password" name="password" placeholder="Mật khẩu*" required>
          </td>
        </tr>
        <tr>
          <td colspan="2">
            <input type="password" name="confirmP" placeholder="Xác nhận mật khẩu*" required>
          </td>
        </tr>
        <tr>
          <td>
            <input type="submit" value="Đăng ký" name="btnsend">
          </td>
          <td>
            Bạn đã có tài khoản? <a href="DangNhap.php">Đăng nhập</a>
          </td>
        </tr>
      </table>
    </form>
  </div>

  <script src="js/ChuyenFile.js" defer></script>

</body>

</html>