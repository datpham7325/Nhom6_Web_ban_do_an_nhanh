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
session_start(); // Bắt đầu session

$loi = "";
$email = ""; // Khai báo trước để tránh warning

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['btnlogin'])) {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        $loi = "Vui lòng nhập đầy đủ email và mật khẩu!";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $loi = "Email không hợp lệ!";
    } else {
        include_once("myenv.php");
        $mysqli = new mysqli($db_host, $db_user, $db_password, $db_db, $db_port);

        if ($mysqli->connect_error) {
            $loi = "Lỗi kết nối CSDL: " . $mysqli->connect_error;
        } else {
            // SỬA: Dùng bảng Users (có 's')
            $stmt = $mysqli->prepare("SELECT MaUser, Ho, Ten, Email, MatKhau, QuyenHan FROM `Users` WHERE Email = ?");
            
            if ($stmt === false) {
                $loi = "Lỗi SQL: " . $mysqli->error;
            } else {
                $stmt->bind_param("s", $email);
                $stmt->execute();
                $stmt->store_result();

                if ($stmt->num_rows == 1) {
                    $stmt->bind_result($maUser, $ho, $ten, $db_email, $db_hash, $quyenHan);
                    $stmt->fetch();

                    // SỬA: DÙNG SHA-256 BINARY SO SÁNH TRỰC TIẾP
                    if (hash('sha256', $password, true) === $db_hash) {
                        $_SESSION['loggedin'] = true;
                        $_SESSION['MaUser'] = $maUser;
                        $_SESSION['HoTen'] = $ho . " " . $ten;
                        $_SESSION['Email'] = $db_email;
                        $_SESSION['QuyenHan'] = $quyenHan;

                        // Chuyển hướng theo quyền
                        if ($quyenHan === 'admin') {
                            header("Location: Admin.php");
                        } elseif ($quyenHan === 'nhanvien') {
                            header("Location: NhanVien.php");
                        } else {
                            header("Location: index.php");
                        }
                        exit();
                    } else {
                        $loi = "Mật khẩu không đúng!";
                    }
                } else {
                    $loi = "Email không tồn tại!";
                }
                $stmt->close();
            }
            $mysqli->close();
        }
    }
}
?>

<div>
    <h1>ĐĂNG NHẬP</h1>

    <?php if ($loi): ?>
      <p class="error"><?php echo $loi; ?></p>
    <?php endif; ?>

    <form method="POST" action="">
      <table>
        <tr>
          <td>
            <input type="email" name="email" placeholder="E-mail*"
              value="<?php echo $email; ?>" required>
          </td>
        </tr>
        <tr>
          <td>
            <input type="password" name="password" placeholder="Mật khẩu*" required>
          </td>
        </tr>
        <tr>
          <td>
            <input type="submit" value="Đăng nhập" name="btnlogin">
          </td>
        </tr>
      </table>
    </form>

    <div class="link">
      Chưa có tài khoản? <a href="DangKy.php">Đăng ký ngay</a><br>
      <a href="QuenMK.php">Quên mật khẩu?</a>
    </div>
</div>

<script src="js/ChuyenFile.js" defer></script>

</body>
</html>