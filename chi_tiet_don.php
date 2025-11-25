<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chi Tiết Đơn Hàng</title>
    <link rel="stylesheet" href="../css/chi_tiet_don.css">
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;700;900&display=swap" rel="stylesheet">
</head>
<body>
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

        $orderId = isset($_GET['order_id']) ? (int)$_GET['order_id'] : 0;

        if ($orderId <= 0)
        {
            echo "<p class='error-message'>Mã đơn hàng không hợp lệ.</p>";
            exit();
        }

        // Lấy thông tin đơn hàng và khách
        $sqlOrder = "SELECT dh.*, u.Ho, u.Ten, u.Email
                     FROM DonHang dh
                     LEFT JOIN Users u ON dh.MaUser = u.MaUser
                     WHERE dh.MaDonHang = $orderId";
        $resOrder = mysqli_query($conn, $sqlOrder);

        if (!$resOrder || mysqli_num_rows($resOrder) == 0)
        {
            echo "<p class='error-message'>Không tìm thấy đơn hàng.</p>";
            exit();
        }

        $order = mysqli_fetch_assoc($resOrder);

        // Lấy chi tiết món trong đơn
        $sqlItems = "SELECT c.MaChiTiet, c.MaBienThe, c.SoLuong, c.DonGia, c.ThanhTien,
                            b.MaMonAn, m.TenMonAn, m.HinhAnh, kt.TenSize
                     FROM ChiTietDonHang c
                     LEFT JOIN BienTheMonAn b ON c.MaBienThe = b.MaBienThe
                     LEFT JOIN MonAn m ON b.MaMonAn = m.MaMonAn
                     LEFT JOIN KichThuoc kt ON b.MaSize = kt.MaSize
                     WHERE c.MaDonHang = $orderId";

        $resItems = mysqli_query($conn, $sqlItems);
    ?>

    <div class="table-container" style="width: 100%; display: flex; flex-direction: column; align-items: center;">
        <div class="page-header" style="width: 95%; margin-bottom: 10px; text-align: center;">
            <h2>CHI TIẾT ĐƠN HÀNG #<?php echo $orderId; ?></h2>
        </div>

    <form class="form1">
        <table>
            <tr>
                <td colspan="4"><h2>THÔNG TIN ĐƠN HÀNG</h2></td>
            </tr>
            <tr class="tieude">
                <td>Mã đơn</td>
                <td>Khách hàng</td>
                <td>SĐT</td>
                <td>Địa chỉ giao</td>
            </tr>
            <tr class="noidung">
                <td><?php echo $order['MaDonHang']; ?></td>
                <td><?php echo htmlspecialchars(trim($order['Ho'] . ' ' . $order['Ten'])); ?></td>
                <td><?php echo htmlspecialchars($order['SDTGiaoHang']); ?></td>
                <td><?php echo htmlspecialchars($order['DiaChiGiaoHang']); ?></td>
            </tr>

            <tr class="tieude">
                <td>Phương thức</td>
                <td>Trạng thái</td>
                <td>Tổng tiền</td>
                <td>Ngày đặt</td>
            </tr>
            <tr class="noidung">
                <td><?php echo htmlspecialchars($order['PhuongThucThanhToan']); ?></td>
                <td><?php echo htmlspecialchars($order['TrangThai']); ?></td>
                <td><?php echo number_format($order['TongTien'], 0, ',', '.'); ?> VND</td>
                <td><?php echo $order['NgayDat']; ?></td>
            </tr>

            <tr>
                <td class="label-cell">Ghi chú</td>
                <td colspan="3"><?php echo nl2br(htmlspecialchars($order['GhiChu'])); ?></td>
            </tr>
        </table>
    </form>

    <form class="form2">
        <table>
            <tr>
                <td colspan="6"><h2>CHI TIẾT ĐƠN</h2></td>
            </tr>
            <tr class="tieude">
                <td>Mã chi tiết</td>
                <td>Tên món</td>
                <td>Size</td>
                <td>Số lượng</td>
                <td>Đơn giá</td>
                <td>Thành tiền</td>
            </tr>
            <?php
                if ($resItems && mysqli_num_rows($resItems) > 0)
                {
                    while ($item = mysqli_fetch_assoc($resItems))
                    {
                        echo "<tr class='noidung'>";
                        echo "<td>" . $item['MaChiTiet'] . "</td>";
                        echo "<td>" . htmlspecialchars($item['TenMonAn']) . "</td>";
                        echo "<td>" . htmlspecialchars($item['TenSize']) . "</td>";
                        echo "<td>" . (int)$item['SoLuong'] . "</td>";
                        echo "<td>" . number_format($item['DonGia'], 0, ',', '.') . "</td>";
                        echo "<td>" . number_format($item['ThanhTien'], 0, ',', '.') . "</td>";
                        echo "</tr>";
                    }
                }
                else
                {
                    echo "<tr><td colspan='6'>Không có sản phẩm trong đơn.</td></tr>";
                }
            ?>
            <tr>
                <td colspan="6" class="tdbtn">
                    <a href="duyet_don.php" class="btnBack">Quay lại</a>
                </td>
            </tr>
        </table>
    </form>

    <?php mysqli_close($conn); ?>

    </div> </body>
</html>