<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chi Tiết Sự Kiện</title>
    <link rel="stylesheet" href="./css/chi_tiet_su_kien.css">
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;700;900&display=swap" rel="stylesheet">
</head>
<body>
    <?php include_once "includes/header2.php"; ?>

    <?php
        $hostname = "localhost";
        $username = "root";
        $password = "";
        $dbname = "quanly_cua_hang";

        $conn = mysqli_connect($hostname, $username, $password, $dbname);
        if (!$conn)
        {
            die("Connection failed: " . mysqli_connect_error());
        }

        // Lấy event_id từ URL
        $eventId = isset($_GET['event_id']) ? (int)$_GET['event_id'] : 0;

        if ($eventId <= 0)
        {
            echo "<p class='error-message'>Mã sự kiện không hợp lệ.</p>";
            echo "<div style='text-align:center'><a href='duyet_su_kien.php' class='btnBack'>Quay lại</a></div>";
            exit();
        }

        // Truy vấn dữ liệu từ bảng DatSuKien
        $sql = "SELECT * FROM DatSuKien WHERE MaSuKien = $eventId";
        $result = mysqli_query($conn, $sql);

        if (!$result || mysqli_num_rows($result) == 0)
        {
            echo "<p class='error-message'>Không tìm thấy thông tin sự kiện.</p>";
            echo "<div style='text-align:center'><a href='duyet_su_kien.php' class='btnBack'>Quay lại</a></div>";
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

        // Map loại sự kiện sang tiếng Việt
        $eventTypeMapping = [
            'sinh_nhat' => 'Sinh nhật',
            'hoi_nghi'  => 'Hội nghị',
            'tiec_cuoi' => 'Tiệc cưới',
            'gia_dinh'  => 'Gia đình',
            'khac'      => 'Khác'
        ];
        $loaiSuKienHienThi = isset($eventTypeMapping[$row['LoaiSuKien']]) ? $eventTypeMapping[$row['LoaiSuKien']] : $row['LoaiSuKien'];
    ?>

    <div class="table-container">
        <div class="page-header" style="width: 95%; margin-bottom: 10px; text-align: center;">
            <h2>CHI TIẾT SỰ KIỆN #<?php echo $eventId; ?></h2>
        </div>

    <form class="form1">
        <table>
            <tr>
                <td colspan="4"><h2>THÔNG TIN LIÊN HỆ</h2></td>
            </tr>
            <tr class="tieude">
                <td>Người đại diện</td>
                <td>Số điện thoại</td>
                <td>Email</td>
                <td>Ngày tạo đơn</td>
            </tr>
            <tr class="noidung">
                <td><?php echo htmlspecialchars($row['HoTenNguoiDaiDien']); ?></td>
                <td><?php echo htmlspecialchars($row['SDT']); ?></td>
                <td><?php echo htmlspecialchars($row['Email'] ? $row['Email'] : '---'); ?></td>
                <td><?php echo date('d/m/Y H:i', strtotime($row['NgayTao'])); ?></td>
            </tr>
            
            <tr class="tieude">
                <td colspan="2">Trạng thái hiện tại</td>
                <td colspan="2">Tài khoản đặt (User ID)</td>
            </tr>
            <tr class="noidung">
                <td colspan="2" style="font-weight:bold; color: #d32f2f; font-size: 16px;">
                    <?php echo $trangThaiHienThi; ?>
                </td>
                <td colspan="2"><?php echo $row['MaUser']; ?></td>
            </tr>
        </table>
    </form>

    <form class="form2">
        <table>
            <tr>
                <td colspan="4"><h2>THÔNG TIN TỔ CHỨC</h2></td>
            </tr>
            
            <tr class="tieude">
                <td colspan="4">Tên sự kiện</td>
            </tr>
            <tr class="noidung">
                <td colspan="4" style="font-weight: bold; font-size: 1.1em; text-transform: uppercase;">
                    <?php echo htmlspecialchars($row['TenSuKien']); ?>
                </td>
            </tr>

            <tr class="tieude">
                <td>Ngày tổ chức</td>
                <td>Thời gian</td>
                <td>Số khách</td>
                <td>Loại sự kiện</td>
            </tr>
            <tr class="noidung">
                <td><?php echo date('d/m/Y', strtotime($row['NgaySuKien'])); ?></td>
                <td>
                    <?php 
                        echo date('H:i', strtotime($row['GioBatDau'])) . ' - ' . date('H:i', strtotime($row['GioKetThuc'])); 
                    ?>
                </td>
                <td><?php echo $row['SoNguoi']; ?> người</td>
                <td><?php echo $loaiSuKienHienThi; ?></td>
            </tr>

            <tr>
                <td class="label-cell">Yêu cầu đặc biệt</td>
                <td colspan="3" style="padding: 15px; text-align: left;">
                    <?php 
                        if (!empty($row['YeuCauDacBiet'])) {
                            echo nl2br(htmlspecialchars($row['YeuCauDacBiet'])); 
                        } else {
                            echo "<span style='color:#999;'>Không có yêu cầu đặc biệt</span>";
                        }
                    ?>
                </td>
            </tr>
            
            <tr>
                <td colspan="4" class="tdbtn">
                    <?php if($row['TrangThai'] == 'cho_xac_nhan'): ?>
                        <a href="duyet_su_kien.php?event_id=<?php echo $eventId; ?>&action=approve" 
                           onclick="return confirm('Xác nhận duyệt sự kiện này?')" 
                           class="btnBack" style="background-color: #28a745; margin-right: 10px;">Duyệt ngay</a>
                    <?php endif; ?>

                    <a href="duyet_su_kien.php" class="btnBack">Quay lại danh sách</a>
                </td>
            </tr>
        </table>
    </form>

    <?php mysqli_close($conn); ?>

    </div> 
</body>
</html>