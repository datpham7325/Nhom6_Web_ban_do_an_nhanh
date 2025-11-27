<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Duyệt Đặt Bàn</title>
    <link rel="stylesheet" href="./css/duyet_don.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;700;900&display=swap" rel="stylesheet">
    <style>
        /* CSS bổ sung riêng cho các nút của đặt bàn */
        .btn-approve { background-color: #28a745; color: white; padding: 5px 10px; text-decoration: none; border-radius: 4px; font-size: 14px; }
        .btn-cancel { background-color: #dc3545; color: white; border: none; padding: 5px 10px; border-radius: 4px; cursor: pointer; font-size: 14px; }
        .btn-group { display: flex; gap: 5px; }
    </style>
</head>
<body>
    <?php include_once "includes/header2.php"; ?>

    <?php
        $hostname = "localhost";
        $username = "root";
        $password = "";
        $dbname = "quanly_cua_hang";

        $conn = mysqli_connect($hostname, $username, $password, $dbname);
        if (!$conn) {
            die("Connection failed: " . mysqli_connect_error());
        }
    ?>
    
    <div class="table-container">
        <div class="page-header"><h2>DUYỆT ĐẶT BÀN</h2></div>
        <table>
            <tr>
                <th class="col-order-id">Mã #</th>
                <th>Khách hàng</th>
                <th>SĐT</th>
                <th>Số người</th>
                <th>Thời gian đặt</th>
                <th>Ghi chú</th>
                <th>Trạng thái</th>
                <th class="col-actions">Hành động</th>
            </tr>

            <?php
                // 1. XỬ LÝ POST (HỦY ĐẶT BÀN)
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    if (isset($_POST['action']) && $_POST['action'] === 'reject' && isset($_POST['booking_id'])) {
                        $bookingId = (int)$_POST['booking_id'];
                        // Cập nhật trạng thái thành 'da_huy'
                        $upd = "UPDATE DatBan SET TrangThai = 'da_huy' WHERE MaDatBan = $bookingId";
                        mysqli_query($conn, $upd);
                        
                        header('Location: duyet_dat_ban.php');
                        exit();
                    }
                }

                // 2. XỬ LÝ GET (DUYỆT ĐẶT BÀN)
                if (isset($_GET['action']) && isset($_GET['booking_id'])) {
                    $action = $_GET['action'];
                    $bookingId = (int)$_GET['booking_id'];
                    $upd = "";

                    // Chuyển từ 'cho_xac_nhan' -> 'da_xac_nhan'
                    if ($action === 'approve') {
                        $upd = "UPDATE DatBan SET TrangThai = 'da_xac_nhan' WHERE MaDatBan = $bookingId";
                    }

                    if ($upd != "") {
                        mysqli_query($conn, $upd);
                        header('Location: duyet_dat_ban.php');
                        exit();
                    }
                }

                // 3. LẤY DANH SÁCH ĐẶT BÀN
                // Sắp xếp theo ngày đặt giảm dần để thấy lịch mới nhất
                $sql = "SELECT * FROM DatBan ORDER BY NgayDat DESC, GioDat DESC";
                $result = mysqli_query($conn, $sql);

                // Mảng map trạng thái sang tiếng Việt
                $statusMapping = [
                    'cho_xac_nhan' => 'Chờ xác nhận',
                    'da_xac_nhan'  => 'Đã xác nhận',
                    'da_huy'       => 'Đã hủy'
                ];

                if ($result && mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        $bookingId = $row['MaDatBan'];
                        $hoTen = htmlspecialchars($row['HoTen']);
                        $sdt = htmlspecialchars($row['SDT']);
                        $soNguoi = $row['SoNguoi'];
                        
                        // Format thời gian hiển thị (Giờ + Ngày)
                        $thoiGianDat = date('H:i', strtotime($row['GioDat'])) . ' - ' . date('d/m/Y', strtotime($row['NgayDat']));
                        
                        $ghiChu = htmlspecialchars($row['GhiChu']);
                        $trangThaiCode = $row['TrangThai'];
                        $trangThaiHienThi = isset($statusMapping[$trangThaiCode]) ? $statusMapping[$trangThaiCode] : $trangThaiCode;

                        echo "<tr>";
                        echo "<td class='col-order-id'>" . $bookingId . "</td>";
                        echo "<td><strong>" . $hoTen . "</strong></td>";
                        echo "<td>" . $sdt . "</td>";
                        echo "<td>" . $soNguoi . " người</td>";
                        echo "<td>" . $thoiGianDat . "</td>";
                        echo "<td style='max-width: 200px; font-size: 0.9em;'>" . ($ghiChu ? $ghiChu : '---') . "</td>";
                        
                        // Màu trạng thái
                        $colorStyle = "";
                        if ($trangThaiCode == 'da_xac_nhan') $colorStyle = "color: #28a745;"; // Xanh lá
                        elseif ($trangThaiCode == 'da_huy') $colorStyle = "color: #dc3545;"; // Đỏ
                        elseif ($trangThaiCode == 'cho_xac_nhan') $colorStyle = "color: #ffc107; font-weight: bold;"; // Vàng

                        echo "<td style='$colorStyle'>" . $trangThaiHienThi . "</td>";
                        
                        echo "<td class='actions'>";
                        echo "<div class='btn-group'>";

                        // LOGIC HIỂN THỊ NÚT
                        switch ($trangThaiCode) {
                            case 'cho_xac_nhan':
                                echo "<a class='btn-approve' href='duyet_dat_ban.php?booking_id=$bookingId&action=approve' onclick=\"return confirm('Xác nhận lịch đặt bàn #$bookingId?')\">Duyệt</a>";
                                echo "<button type='button' class='btn-cancel' onclick=\"startReject($bookingId)\">Hủy</button>";
                                break;
                            
                            case 'da_xac_nhan':
                                // Đã xác nhận vẫn có thể hủy nếu khách không đến
                                echo "<button type='button' class='btn-cancel' style='background-color: #6c757d;' onclick=\"startReject($bookingId)\">Hủy lịch</button>";
                                break;
                            
                            default:
                                // Đã hủy thì không làm gì thêm
                                echo "<span style='color: #ccc;'>---</span>";
                                break;
                        }
                        echo "<a class='btn-detail' href='chi_tiet_dat_ban.php?booking_id=$bookingId'>Chi tiết</a>";

                        echo "</div>";
                        echo "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='8' style='text-align:center'>Chưa có yêu cầu đặt bàn nào.</td></tr>";
                }

                mysqli_close($conn);
            ?>
        </table>
    </div>

    <script>
        // Script xử lý form hủy
        function startReject(bookingId) {
            if (!confirm('Bạn chắc chắn muốn hủy lịch đặt bàn #' + bookingId + '?')) {
                return;
            }

            var form = document.createElement('form');
            form.method = 'POST';
            form.action = 'duyet_dat_ban.php';

            var inputId = document.createElement('input');
            inputId.type = 'hidden';
            inputId.name = 'booking_id';
            inputId.value = bookingId;
            form.appendChild(inputId);

            var inputAction = document.createElement('input');
            inputAction.type = 'hidden';
            inputAction.name = 'action';
            inputAction.value = 'reject';
            form.appendChild(inputAction);

            document.body.appendChild(form);
            form.submit();
        }
    </script>
</body>
</html>