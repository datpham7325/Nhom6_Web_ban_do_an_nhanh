<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đặt lại mật khẩu</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/QuenMK.css">
    <link rel="stylesheet" href="css/transition.css">
    <script src="js/common.js" defer></script>
</head>

<body>

    <?php
    // XÓA session_start() — KHÔNG DÙNG SESSION

    $loi = $thanhcong = $info = "";
    $step = $_GET['step'] ?? 'email';
    $email = '';

    // === DÙNG PHPMailer ===
    require_once 'vendor/autoload.php';

    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;

    // Hàm gửi email OTP
    function sendOTPEmail($to, $otp)
    {
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'tranhaithien2000@gmail.com';
            $mail->Password   = 'bjki ojtc wnfy vrua';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;
            $mail->CharSet    = 'UTF-8';

            $mail->setFrom('tranhaithien2000@gmail.com', 'Quán Ăn Vui Vẻ');
            $mail->addAddress($to);

            $mail->isHTML(true);
            $mail->Subject = 'Mã OTP đặt lại mật khẩu';
            $mail->Body    = "
        <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 20px auto; padding: 20px; border: 1px solid #eee; border-radius: 12px; background: #f9f9f9;'>
            <h2 style='color: #e60000; text-align: center;'>ĐẶT LẠI MẬT KHẨU</h2>
            <p>Xin chào,</p>
            <p>Yêu cầu đặt lại mật khẩu đã được gửi.</p>
            <div style='text-align: center; margin: 30px 0;'>
                <h1 style='display: inline-block; background: #e60000; color: white; padding: 15px 30px; border-radius: 10px; font-size: 28px; letter-spacing: 8px;'>
                    <strong>$otp</strong>
                </h1>
            </div>
            <p>Mã OTP có hiệu lực trong <strong>5 phút</strong>.</p>
            <hr>
            <p style='color: #777; font-size: 12px; text-align: center;'>
                © 2025 Quán Ăn Vui Vẻ
            </p>
        </div>";

            $mail->send();
            return true;
        } catch (Exception $e) {
            error_log("Lỗi gửi email: " . $mail->ErrorInfo);
            return false;
        }
    }

    include_once("includes/myenv.php");
    $mysqli = new mysqli($db_host, $db_user, $db_password, $db_db, $db_port);
    if ($mysqli->connect_error) die("Lỗi kết nối CSDL!");

    // === BƯỚC 1: NHẬP EMAIL ===
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['btnemail'])) {
        $email = trim($_POST['email']);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $loi = "Email không hợp lệ!";
        } else {
            // Kiểm tra email có tồn tại trong database không
            $stmt = $mysqli->prepare("SELECT SDT FROM Users WHERE Email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows == 0) {
                $loi = "Email không tồn tại!";
            } else {
                // Tạo mã OTP và lưu vào database
                $otp = sprintf("%06d", mt_rand(0, 999999));
                $expires = date("Y-m-d H:i:s", time() + 300);
                $otpData = json_encode(['otp' => $otp, 'expires' => $expires]);

                $update = $mysqli->prepare("UPDATE Users SET DiaChi = ? WHERE Email = ?");
                $update->bind_param("ss", $otpData, $email);
                $update->execute();
                $update->close();

                if (sendOTPEmail($email, $otp)) {
                    $info = "Mã OTP đã gửi đến <strong>$email</strong>!<br>Kiểm tra hộp thư (và Spam).";
                } else {
                    $loi = "Gửi email thất bại!";
                }
                $step = 'otp';
            }
            $stmt->close();
        }
    }

    // === BƯỚC 2: NHẬP OTP ===
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['btnverify'])) {
        $email = $_POST['email'];
        $otp_input = trim($_POST['otp']);

        // Kiểm tra mã OTP từ database
        $stmt = $mysqli->prepare("SELECT DiaChi FROM Users WHERE Email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows == 0) {
            $loi = "Lỗi hệ thống!";
        } else {
            $stmt->bind_result($diaChiJson);
            $stmt->fetch();
            $otpData = json_decode($diaChiJson, true);

            $now = date("Y-m-d H:i:s");
            // Xác thực mã OTP và thời gian hết hạn
            if (!is_array($otpData) || $otpData['otp'] !== $otp_input || $otpData['expires'] < $now) {
                $loi = "Mã OTP không đúng hoặc đã hết hạn!";
            } else {
                $step = 'reset';
            }
        }
        $stmt->close();
    }

    // === BƯỚC 3: ĐẶT LẠI MẬT KHẨU ===
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['btnsave'])) {
        $email = $_POST['email'];
        $password = $_POST['password'];
        $confirmP = $_POST['confirmP'];

        // Kiểm tra tính hợp lệ của mật khẩu mới
        if ($password !== $confirmP) {
            $loi = "Mật khẩu xác nhận không khớp!";
        } elseif (strlen($password) < 6) {
            $loi = "Mật khẩu phải ít nhất 6 ký tự!";
        } else {
            // Mã hóa và cập nhật mật khẩu mới
            $hashed = hash('sha256', $password, true);
            $update = $mysqli->prepare("UPDATE Users SET MatKhau = ?, DiaChi = NULL WHERE Email = ?");
            $update->bind_param("ss", $hashed, $email);
            if ($update->execute()) {
                $thanhcong = "Đặt lại mật khẩu thành công! <a href='DangNhap.php'>Đăng nhập ngay</a>";
            } else {
                $loi = "Lỗi hệ thống!";
            }
            $update->close();
        }
    }
    $mysqli->close();
    ?>

    <div class="container">
        <h1>ĐẶT LẠI MẬT KHẨU</h1>

        <!-- Hiển thị thông báo lỗi, thành công và thông tin -->
        <?php if ($loi): ?><div class="error"><?php echo $loi; ?></div><?php endif; ?>
        <?php if ($thanhcong): ?><div class="success"><?php echo $thanhcong; ?></div><?php endif; ?>
        <?php if ($info): ?><div class="info"><?php echo $info; ?></div><?php endif; ?>

        <!-- BƯỚC 1: Form nhập email -->
        <?php if ($step === 'email'): ?>
            <p class="subtitle">Nhập email để nhận mã OTP.</p>
            <form method="POST">
                <div class="input-group">
                    <input type="email" name="email" placeholder="Email *" value="<?php echo htmlspecialchars($email); ?>" required>
                </div>
                <button type="submit" name="btnemail" class="btn">TIẾP THEO</button>
            </form>

            <!-- BƯỚC 2: Form nhập mã OTP -->
        <?php elseif ($step === 'otp'): ?>
            <p class="subtitle">Nhập mã OTP 6 số đã gửi đến email.</p>
            <form method="POST">
                <input type="hidden" name="email" value="<?php echo htmlspecialchars($email); ?>">
                <!-- Input OTP với 6 ô nhập số -->
                <div class="otp-input">
                    <?php for ($i = 0; $i < 6; $i++): ?>
                        <input type="text" maxlength="1" name="otp_digit[]" required
                            oninput="this.value=this.value.replace(/[^0-9]/g,''); if(this.value) this.nextElementSibling?.focus();">
                    <?php endfor; ?>
                </div>
                <button type="submit" name="btnverify" class="btn">XÁC NHẬN OTP</button>
            </form>

            <!-- BƯỚC 3: Form đặt lại mật khẩu -->
        <?php elseif ($step === 'reset'): ?>
            <p class="subtitle">Nhập mật khẩu mới.</p>
            <form method="POST">
                <input type="hidden" name="email" value="<?php echo htmlspecialchars($email); ?>">
                <div class="input-group">
                    <input type="password" id="password" name="password" placeholder="Mật khẩu mới *" required>
                    <!-- Icon toggle hiển thị mật khẩu -->
                    <i class="fas fa-eye eye" onclick="togglePass('password')"></i>
                </div>
                <div class="input-group">
                    <input type="password" id="confirmP" name="confirmP" placeholder="Xác nhận mật khẩu *" required>
                    <i class="fas fa-eye eye" onclick="togglePass('confirmP')"></i>
                </div>
                <button type="submit" name="btnsave" class="btn">LƯU MẬT KHẨU</button>
            </form>
        <?php endif; ?>

        <!-- Link quay lại trang đăng nhập -->
        <div class="back-link">
            <a href="DangNhap.php">Quay lại đăng nhập</a>
        </div>
    </div>

    <script>
        // Hàm toggle hiển thị/ẩn mật khẩu
        function togglePass(id) {
            const input = document.getElementById(id);
            const icon = input.nextElementSibling;
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.replace('fa-eye', 'fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.replace('fa-eye-slash', 'fa-eye');
            }
        }

        // Xử lý nhập OTP tự động chuyển ô
        document.querySelectorAll('input[name^="otp_digit"]').forEach((input, index, inputs) => {
            input.addEventListener('input', () => {
                if (input.value && index < inputs.length - 1) inputs[index + 1].focus();
            });
            input.addEventListener('keydown', (e) => {
                if (e.key === 'Backspace' && !input.value && index > 0) inputs[index - 1].focus();
            });
        });

        // Xử lý submit form OTP - gom các ô OTP thành một chuỗi
        document.querySelector('form')?.addEventListener('submit', function(e) {
            if (this.querySelector('input[name="otp_digit[]"]')) {
                const digits = Array.from(this.querySelectorAll('input[name="otp_digit[]"]')).map(i => i.value).join('');
                const hidden = document.createElement('input');
                hidden.type = 'hidden';
                hidden.name = 'otp';
                hidden.value = digits;
                this.appendChild(hidden);
            }
        });
    </script>

</body>

</html>