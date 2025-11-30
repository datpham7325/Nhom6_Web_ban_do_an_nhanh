<?php
// Thiết lập timezone
date_default_timezone_set('Asia/Ho_Chi_Minh');
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thống Kê Doanh Thu</title>
    <link rel="stylesheet" href="css/home.css">
    <link rel="stylesheet" href="css/thong_ke.css">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;700;900&display=swap" rel="stylesheet">
</head>

<body>
    <?php include_once "includes/header2.php"; ?>

    <?php
    include_once("includes/myenv.php");
    $conn = mysqli_connect($db_host, $db_user, $db_password, $db_db, $db_port);
    if (!$conn) {
        die("Kết nối thất bại: " . mysqli_connect_error());
    }
    ?>

    <?php
        if (isset($_SESSION['MaUser'])) {
            $maUserCheck = $_SESSION['MaUser'];
            $sqlRole = "SELECT QuyenHan FROM Users WHERE MaUser = ?";
            $stmtRole = mysqli_prepare($conn, $sqlRole);
            
            if ($stmtRole) {
                mysqli_stmt_bind_param($stmtRole, "i", $maUserCheck);
                mysqli_stmt_execute($stmtRole);
                $resultRole = mysqli_stmt_get_result($stmtRole);
                $userRole = mysqli_fetch_assoc($resultRole);

                if (!$userRole || $userRole['QuyenHan'] !== 'admin') {
                    header("Location: index.php");
                    exit();
                }
            } else {
                header("Location: DangNhap.php");
                exit();
            }
        } else {
            header("Location: DangNhap.php");
            exit();
        }
    ?>

    <?php
    // --- XỬ LÝ LỌC NGÀY ---
    // Mặc định: Lấy ngày đầu tháng và ngày hiện tại
    $dateFrom = isset($_GET['date_from']) ? $_GET['date_from'] : date('Y-m-01');
    $dateTo = isset($_GET['date_to']) ? $_GET['date_to'] : date('Y-m-d');

    // Thêm giờ vào để truy vấn chính xác (00:00:00 đến 23:59:59)
    $sqlFrom = $dateFrom . " 00:00:00";
    $sqlTo = $dateTo . " 23:59:59";

    // --- TRUY VẤN 1: LẤY TỔNG SỐ LIỆU ---
    // Chỉ tính đơn hàng có trạng thái 'hoan_thanh'
    $sqlSummary = "SELECT 
                    SUM(TongTien) as TongDoanhThu, 
                    COUNT(MaDonHang) as TongDon, 
                    AVG(TongTien) as TrungBinhDon 
                   FROM DonHang 
                   WHERE TrangThai = 'hoan_thanh' 
                   AND NgayDat BETWEEN '$sqlFrom' AND '$sqlTo'";
    
    $resSummary = mysqli_query($conn, $sqlSummary);
    $rowSummary = mysqli_fetch_assoc($resSummary);

    $tongDoanhThu = $rowSummary['TongDoanhThu'] ?? 0;
    $tongDon = $rowSummary['TongDon'] ?? 0;
    $trungBinhDon = $rowSummary['TrungBinhDon'] ?? 0;

    // --- TRUY VẤN 2: LẤY CHI TIẾT THEO NGÀY ---
    $sqlDetails = "SELECT 
                    DATE(NgayDat) as Ngay, 
                    SUM(TongTien) as DoanhThuNgay, 
                    COUNT(MaDonHang) as SoDonNgay
                   FROM DonHang 
                   WHERE TrangThai = 'hoan_thanh' 
                   AND NgayDat BETWEEN '$sqlFrom' AND '$sqlTo'
                   GROUP BY DATE(NgayDat)
                   ORDER BY DATE(NgayDat) DESC";
    
    $resDetails = mysqli_query($conn, $sqlDetails);
    ?>

    <nav>
        <form class="search-bar date-filter" method="get">
            <span class="label-date">Từ ngày:</span>
            <input type="date" name="date_from" class="date-input" value="<?php echo $dateFrom; ?>">
            
            <span class="label-date">Đến ngày:</span>
            <input type="date" name="date_to" class="date-input" value="<?php echo $dateTo; ?>">
            
            <button type="submit" class="btnTim"><i class="fas fa-filter"></i> Lọc dữ liệu</button>
        </form>
    </nav>

    <div class="stat-container">
        <div class="card">
            <i class="fas fa-coins"></i>
            <h3>Tổng Doanh Thu</h3>
            <p><?php echo number_format($tongDoanhThu, 0, ',', '.'); ?> ₫</p>
        </div>

        <div class="card">
            <i class="fas fa-shopping-bag"></i>
            <h3>Tổng Đơn Hoàn Thành</h3>
            <p><?php echo $tongDon; ?> đơn</p>
        </div>

        <div class="card">
            <i class="fas fa-chart-line"></i>
            <h3>Giá Trị Trung Bình/Đơn</h3>
            <p><?php echo number_format($trungBinhDon, 0, ',', '.'); ?> ₫</p>
        </div>
    </div>

    <form style="margin-top: 20px;">
        <table>
            <tr>
                <td class="tdtrai" colspan="4">
                    <h2>CHI TIẾT DOANH THU THEO NGÀY</h2>
                </td>
            </tr>
            <tr class="tieude">
                <td>Ngày tháng</td>
                <td>Số lượng đơn hàng</td>
                <td>Tổng tiền (VNĐ)</td>
                <td>Hành động</td>
            </tr>

            <?php
            if (mysqli_num_rows($resDetails) > 0) {
                while ($row = mysqli_fetch_assoc($resDetails)) {
                    $ngayHienThi = date("d/m/Y", strtotime($row['Ngay']));
                    echo "<tr class='noidung'>";
                    echo "<td>" . $ngayHienThi . "</td>";
                    echo "<td>" . $row['SoDonNgay'] . "</td>";
                    echo "<td style='color: #d32f2f; font-weight:bold;'>" . number_format($row['DoanhThuNgay'], 0, ".", ",") . "</td>";
                    echo "<td>";
                    // Link này có thể dẫn đến trang danh sách đơn hàng lọc theo ngày cụ thể đó (tùy chọn mở rộng)
                    echo "<a href='duyet_don.php?ngay=" . $row['Ngay'] . "' title='Xem đơn hàng ngày này'><i class='fas fa-eye' style='color: #f57c00;'></i></a>";
                    echo "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='4' style='text-align:center; padding: 20px;'>Không có doanh thu trong khoảng thời gian này.</td></tr>";
            }
            ?>
        </table>
    </form>

</body>
</html>