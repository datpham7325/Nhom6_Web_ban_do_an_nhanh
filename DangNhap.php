<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Đăng nhập</title>
  <link rel="stylesheet" href="css/DangNhap.css">
</head>

<body>

  <?php
  session_start(); // Bắt đầu session để lưu trạng thái đăng nhập

  $loi = "";
  $email = ""; // Khai báo trước để tránh warning khi chưa có dữ liệu

  // Xử lý khi form được submit (người dùng nhấn nút đăng nhập)
  if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['btnlogin'])) {
    $email = trim($_POST['email']); // Lấy và làm sạch email
    $password = $_POST['password']; // Lấy mật khẩu

    // Kiểm tra các trường bắt buộc không được để trống
    if (empty($email) || empty($password)) {
      $loi = "Vui lòng nhập đầy đủ email và mật khẩu!";
    }
    // Kiểm tra định dạng email có hợp lệ không
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
      $loi = "Email không hợp lệ!";
    }
    // Nếu validation pass, tiến hành kiểm tra đăng nhập
    else {
      include_once("includes/myenv.php"); // Kết nối database
      $mysqli = new mysqli($db_host, $db_user, $db_password, $db_db, $db_port);

      // Kiểm tra kết nối database có thành công không
      if ($mysqli->connect_error) {
        $loi = "Lỗi kết nối CSDL: " . $mysqli->connect_error;
      } else {
        // SỬA: Dùng bảng Users (có 's') - Truy vấn tìm user theo email
        $stmt = $mysqli->prepare("SELECT MaUser, Ho, Ten, Email, MatKhau, QuyenHan FROM `Users` WHERE Email = ?");

        // Kiểm tra prepared statement có tạo thành công không
        if ($stmt === false) {
          $loi = "Lỗi SQL: " . $mysqli->error;
        } else {
          $stmt->bind_param("s", $email); // Bind tham số email
          $stmt->execute();
          $stmt->store_result(); // Lưu kết quả để đếm số bản ghi

          // Kiểm tra có tìm thấy user nào với email này không
          if ($stmt->num_rows == 1) {
            $stmt->bind_result($maUser, $ho, $ten, $db_email, $db_hash, $quyenHan);
            $stmt->fetch();

            // SỬA: DÙNG SHA-256 BINARY SO SÁNH TRỰC TIẾP - Xác thực mật khẩu
            if (hash('sha256', $password, true) === $db_hash) {
              // ĐĂNG NHẬP THÀNH CÔNG - Lưu thông tin vào session
              $_SESSION['loggedin'] = true;        // Cờ đánh dấu đã đăng nhập
              $_SESSION['MaUser'] = $maUser;       // Mã user để nhận dạng
              $_SESSION['HoTen'] = $ho . " " . $ten; // Họ tên đầy đủ
              $_SESSION['Email'] = $db_email;      // Email user
              $_SESSION['QuyenHan'] = $quyenHan;   // Quyền hạn (admin/nhanvien/khachhang)

              // Chuyển hướng theo quyền hạn của user
              if ($quyenHan === 'admin') {
                header("Location: Admin.php");      // Chuyển đến trang Admin
              } elseif ($quyenHan === 'nhanvien') {
                header("Location: NhanVien.php");   // Chuyển đến trang Nhân viên
              } else {
                header("Location: index.php");      // Chuyển đến trang chủ cho khách hàng
              }
              exit(); // Dừng thực thi kịch bản sau khi chuyển hướng
            } else {
              $loi = "Mật khẩu không đúng!"; // Mật khẩu không khớp
            }
          } else {
            $loi = "Email không tồn tại!"; // Không tìm thấy user với email này
          }
          $stmt->close(); // Đóng prepared statement
        }
        $mysqli->close(); // Đóng kết nối database
      }
    }
  }
  ?>

  <div>
    <h1>ĐĂNG NHẬP</h1>

    <?php if ($loi): ?>
      <p class="error"><?php echo $loi; ?></p> <!-- Hiển thị thông báo lỗi nếu có -->
    <?php endif; ?>

    <form method="POST" action="">
      <table>
        <tr>
          <td>
            <!-- Trường nhập email - giữ lại giá trị khi có lỗi -->
            <input type="email" name="email" placeholder="E-mail*"
              value="<?php echo $email; ?>" required>
          </td>
        </tr>
        <tr>
          <td>
            <!-- Trường nhập mật khẩu -->
            <input type="password" name="password" placeholder="Mật khẩu*" required>
          </td>
        </tr>
        <tr>
          <td>
            <!-- Nút submit form đăng nhập -->
            <input type="submit" value="Đăng nhập" name="btnlogin">
          </td>
        </tr>
      </table>
    </form>

    <div class="link">
      Chưa có tài khoản? <a href="DangKy.php">Đăng ký ngay</a><br> <!-- Link đăng ký -->
      <a href="QuenMK.php">Quên mật khẩu?</a> <!-- Link quên mật khẩu -->
    </div>
  </div>

  <script src="js/ChuyenFile.js" defer></script>

</body>

</html>