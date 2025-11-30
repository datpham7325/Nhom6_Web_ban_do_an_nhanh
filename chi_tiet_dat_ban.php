<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chi Tiết Đặt Bàn</title>
    <link rel="stylesheet" href="./css/chi_tiet_dat_ban.css">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;700;900&display=swap" rel="stylesheet">
</head>

<body>
    <?php include_once "includes/header2.php"; ?>

    <!-- Kiểm tra user -->
    <?php
    // Kiểm tra xem session đã có MaUser chưa
    if (isset($_SESSION['MaUser'])) {
        $maUserCheck = $_SESSION['MaUser'];

        // Truy vấn lấy QuyenHan hiện tại từ CSDL
        $sqlRole = "SELECT QuyenHan FROM Users WHERE MaUser = ?";
        $stmtRole = mysqli_prepare($conn, $sqlRole);

        if ($stmtRole) {
            mysqli_stmt_bind_param($stmtRole, "i", $maUserCheck);
            mysqli_stmt_execute($stmtRole);
            $resultRole = mysqli_stmt_get_result($stmtRole);
            $userRole = mysqli_fetch_assoc($resultRole);

            // Kiểm tra logic:
            // 1. Không tìm thấy user trong DB
            // 2. Hoặc QuyenHan không phải là 'admin'
            if (!$userRole || $userRole['QuyenHan'] !== 'admin') {
                header("Location: index.php");
                exit();
            }
        } else {
            // Lỗi câu lệnh SQL thì cũng cho về index để an toàn
            header("Location: DangNhap.php");
            exit();
        }
    } else {
        // Chưa đăng nhập thì chuyển hướng về index
        header("Location: DangNhap.php");
        exit();
    }
    ?>

    <?php
    include_once("includes/myenv.php");
    $conn = mysqli_connect($db_host, $db_user, $db_password, $db_db, $db_port);
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    $bookingId = isset($_GET['booking_id']) ? (int)$_GET['booking_id'] : 0;

    if ($bookingId <= 0) {
        echo "<p class='error-message'>Mã đặt bàn không hợp lệ.</p>";
        echo "<div style='text-align:center'><a href='duyet_dat_ban.php' class='btnBack'>Quay lại</a></div>";
        exit();
    }

    // Lấy thông tin đặt bàn
    $sql = "SELECT * FROM DatBan WHERE MaDatBan = $bookingId";
    $result = mysqli_query($conn, $sql);

    if (!$result || mysqli_num_rows($result) == 0) {
        echo "<p class='error-message'>Không tìm thấy thông tin đặt bàn.</p>";
        echo "<div style='text-align:center'><a href='duyet_dat_ban.php' class='btnBack'>Quay lại</a></div>";
        exit();
    }

    $row = mysqli_fetch_assoc($result);

    // Map trạng thái sang tiếng Việt
    $statusMapping = [
        'cho_xac_nhan' => 'Chờ xác nhận',
        'da_xac_nhan'  => 'Đã xác nhận',
        'da_huy'       => 'Đã hủy'
    ];
    $trangThaiHienThi = isset($statusMapping[$row['TrangThai']]) ? $statusMapping[$row['TrangThai']] : $row['TrangThai'];
    ?>

    <div class="table-container">
        <div class="page-header" style="width: 95%; margin-bottom: 10px; text-align: center;">
            <h2>CHI TIẾT ĐẶT BÀN #<?php echo $bookingId; ?></h2>
        </div>

        <form class="form1">
            <table>
                <tr>
                    <td colspan="4">
                        <h2>THÔNG TIN KHÁCH HÀNG</h2>
                    </td>
                </tr>
                <tr class="tieude">
                    <td>Họ tên khách</td>
                    <td>Số điện thoại</td>
                    <td>Ngày tạo đơn</td>
                    <td>Trạng thái hiện tại</td>
                </tr>
                <tr class="noidung">
                    <td><?php echo htmlspecialchars($row['HoTen']); ?></td>
                    <td><?php echo htmlspecialchars($row['SDT']); ?></td>
                    <td><?php echo date('d/m/Y H:i', strtotime($row['NgayTao'])); ?></td>
                    <td style="font-weight:bold; color: #d32f2f;"><?php echo $trangThaiHienThi; ?></td>
                </tr>
            </table>
        </form>

        <form class="form2">
            <table>
                <tr>
                    <td colspan="4">
                        <h2>THÔNG TIN ĐẶT CHỖ</h2>
                    </td>
                </tr>
                <tr class="tieude">
                    <td>Ngày đến</td>
                    <td>Giờ đến</td>
                    <td>Số người</td>
                    <td>Mã User</td>
                </tr>
                <tr class="noidung">
                    <td><?php echo date('d/m/Y', strtotime($row['NgayDat'])); ?></td>
                    <td><?php echo date('H:i', strtotime($row['GioDat'])); ?></td>
                    <td><?php echo $row['SoNguoi']; ?> người</td>
                    <td><?php echo $row['MaUser'] ? $row['MaUser'] : 'Khách vãng lai'; ?></td>
                </tr>

                <tr>
                    <td class="label-cell">Ghi chú</td>
                    <td colspan="3" style="padding: 15px;">
                        <?php
                        if (!empty($row['GhiChu'])) {
                            echo nl2br(htmlspecialchars($row['GhiChu']));
                        } else {
                            echo "<span style='color:#999;'>Không có ghi chú</span>";
                        }
                        ?>
                    </td>
                </tr>

                <tr>
                    <td colspan="4" class="tdbtn">
                        <?php if ($row['TrangThai'] == 'cho_xac_nhan'): ?>
                            <a href="duyet_dat_ban.php?booking_id=<?php echo $bookingId; ?>&action=approve"
                                onclick="return confirm('Xác nhận lịch này?')"
                                class="btnBack" style="background-color: #28a745; margin-right: 10px;">Duyệt ngay</a>
                        <?php endif; ?>

                        <a href="duyet_dat_ban.php" class="btnBack">Quay lại danh sách</a>
                    </td>
                </tr>
            </table>
        </form>

        <?php mysqli_close($conn); ?>

    </div>
</body>

</html>