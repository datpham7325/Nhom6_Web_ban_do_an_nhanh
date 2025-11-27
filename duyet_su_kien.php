<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Duyệt Sự Kiện</title>
    <link rel="stylesheet" href="./css/duyet_don.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;700;900&display=swap" rel="stylesheet">
    <style>
        /* CSS bổ sung riêng cho các nút */
        .btn-approve { background-color: #28a745; color: white; padding: 5px 10px; text-decoration: none; border-radius: 4px; font-size: 14px; }
        .btn-cancel { background-color: #dc3545; color: white; border: none; padding: 5px 10px; border-radius: 4px; cursor: pointer; font-size: 14px; }
        .btn-detail { background-color: #17a2b8; color: white; padding: 5px 10px; text-decoration: none; border-radius: 4px; font-size: 14px; }
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
        <div class="page-header"><h2>DUYỆT SỰ KIỆN</h2></div>
        <table>
            <tr>
                <th class="col-order-id">Mã #</th>
                <th>Tên sự kiện</th>
                <th>Người đại diện</th>
                <th>SĐT</th>
                <th>Thời gian</th>
                <th>Loại sự kiện</th>
                <th>Trạng thái</th>
                <th class="col-actions">Hành động</th>
            </tr>

            <?php
                // 1. XỬ LÝ POST (HỦY SỰ KIỆN)
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    if (isset($_POST['action']) && $_POST['action'] === 'reject' && isset($_POST['event_id'])) {
                        $eventId = (int)$_POST['event_id'];
                        // Cập nhật trạng thái thành 'da_huy'
                        $upd = "UPDATE DatSuKien SET TrangThai = 'da_huy' WHERE MaSuKien = $eventId";
                        mysqli_query($conn, $upd);
                        
                        header('Location: duyet_su_kien.php');
                        exit();
                    }
                }

                // 2. XỬ LÝ GET (DUYỆT SỰ KIỆN)
                if (isset($_GET['action']) && isset($_GET['event_id'])) {
                    $action = $_GET['action'];
                    $eventId = (int)$_GET['event_id'];
                    $upd = "";

                    // Chuyển từ 'cho_xac_nhan' -> 'da_xac_nhan'
                    if ($action === 'approve') {
                        $upd = "UPDATE DatSuKien SET TrangThai = 'da_xac_nhan' WHERE MaSuKien = $eventId";
                    }

                    if ($upd != "") {
                        mysqli_query($conn, $upd);
                        header('Location: duyet_su_kien.php');
                        exit();
                    }
                }

                // 3. LẤY DANH SÁCH SỰ KIỆN
                // Sắp xếp theo ngày sự kiện giảm dần
                $sql = "SELECT * FROM DatSuKien ORDER BY NgaySuKien DESC, GioBatDau ASC";
                $result = mysqli_query($conn, $sql);

                // Mảng map trạng thái
                $statusMapping = [
                    'cho_xac_nhan' => 'Chờ xác nhận',
                    'da_xac_nhan'  => 'Đã xác nhận',
                    'da_huy'       => 'Đã hủy'
                ];

                // Mảng map loại sự kiện
                $eventTypeMapping = [
                    'sinh_nhat' => 'Sinh nhật',
                    'hoi_nghi'  => 'Hội nghị',
                    'tiec_cuoi' => 'Tiệc cưới',
                    'gia_dinh'  => 'Gia đình',
                    'khac'      => 'Khác'
                ];

                if ($result && mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        $eventId = $row['MaSuKien'];
                        $tenSuKien = htmlspecialchars($row['TenSuKien']);
                        $nguoiDaiDien = htmlspecialchars($row['HoTenNguoiDaiDien']);
                        $sdt = htmlspecialchars($row['SDT']);
                        
                        // Format thời gian: Giờ BĐ - Giờ KT | Ngày
                        $gioBD = date('H:i', strtotime($row['GioBatDau']));
                        $gioKT = date('H:i', strtotime($row['GioKetThuc']));
                        $ngay = date('d/m/Y', strtotime($row['NgaySuKien']));
                        $thoiGianHienThi = "$gioBD - $gioKT<br><small>$ngay</small>";
                        
                        // Xử lý loại sự kiện
                        $loaiCode = $row['LoaiSuKien'];
                        $loaiHienThi = isset($eventTypeMapping[$loaiCode]) ? $eventTypeMapping[$loaiCode] : $loaiCode;

                        // Xử lý trạng thái
                        $trangThaiCode = $row['TrangThai'];
                        $trangThaiHienThi = isset($statusMapping[$trangThaiCode]) ? $statusMapping[$trangThaiCode] : $trangThaiCode;

                        echo "<tr>";
                        echo "<td class='col-order-id'>" . $eventId . "</td>";
                        echo "<td><strong>" . $tenSuKien . "</strong></td>";
                        echo "<td>" . $nguoiDaiDien . "</td>";
                        echo "<td>" . $sdt . "</td>";
                        echo "<td>" . $thoiGianHienThi . "</td>";
                        echo "<td>" . $loaiHienThi . "</td>";
                        
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
                                echo "<a class='btn-approve' href='duyet_su_kien.php?event_id=$eventId&action=approve' onclick=\"return confirm('Xác nhận duyệt sự kiện #$eventId?')\">Duyệt</a>";
                                echo "<button type='button' class='btn-cancel' onclick=\"startReject($eventId)\">Hủy</button>";
                                break;
                            
                            case 'da_xac_nhan':
                                echo "<button type='button' class='btn-cancel' style='background-color: #6c757d;' onclick=\"startReject($eventId)\">Hủy lịch</button>";
                                break;
                            
                            default:
                                echo "<span style='color: #ccc;'>---</span>";
                                break;
                        }
                        
                        // Link tới trang chi tiết (bạn có thể tạo thêm chi_tiet_su_kien.php sau)
                        echo "<a class='btn-detail' href='chi_tiet_su_kien.php?event_id=$eventId'>Chi tiết</a>";

                        echo "</div>";
                        echo "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='8' style='text-align:center'>Chưa có yêu cầu đặt sự kiện nào.</td></tr>";
                }

                mysqli_close($conn);
            ?>
        </table>
    </div>

    <script>
        // Script xử lý form hủy
        function startReject(eventId) {
            if (!confirm('Bạn chắc chắn muốn hủy sự kiện #' + eventId + '?')) {
                return;
            }

            var form = document.createElement('form');
            form.method = 'POST';
            form.action = 'duyet_su_kien.php';

            var inputId = document.createElement('input');
            inputId.type = 'hidden';
            inputId.name = 'event_id';
            inputId.value = eventId;
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