<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="./css/duyet_don.css">
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
    ?>
    
    <div class="table-container">
        <div class="page-header"><h2>DUYỆT ĐƠN HÀNG</h2></div>
        <table>
            <tr>
                <th class="col-order-id">Mã đơn</th>
                <th>Khách hàng</th>
                <th>Địa chỉ giao</th>
                <th>SĐT giao</th>
                <th>Tổng tiền</th>
                <th>Phương thức</th>
                <th>Ngày đặt</th>
                <th>Trạng thái</th>
                <th class="col-actions">Hành động</th>
            </tr>

            <?php
                // Xử lý hành động gửi từ form (sử dụng POST cho 'reject' để tránh thao tác qua GET)
                if ($_SERVER['REQUEST_METHOD'] === 'POST')
                {
                    if (isset($_POST['action']) && $_POST['action'] === 'reject' && isset($_POST['order_id']))
                    {
                        $orderId = (int)$_POST['order_id'];

                        // Đánh dấu đơn đã hủy (không lưu lý do)
                        $upd = "UPDATE DonHang SET TrangThai = 'da_huy' WHERE MaDonHang = $orderId";
                        mysqli_query($conn, $upd);

                        // reload để cập nhật trạng thái
                        header('Location: duyet_don.php');
                        exit();
                    }
                }

                // Hỗ trợ chuyển trạng thái nhanh qua GET (ví dụ: accept, ship, complete)
                if (isset($_GET['action']) && isset($_GET['order_id']))
                {
                    $action = $_GET['action'];
                    $orderId = (int)$_GET['order_id'];
                    $upd = "";

                    // 1. Duyệt đơn: Chuyển từ 'cho_xac_nhan' -> 'dang_xu_ly'
                    if ($action === 'accept')
                    {
                        $upd = "UPDATE DonHang SET TrangThai = 'dang_xu_ly' WHERE MaDonHang = $orderId";
                    }
                    // 2. Giao hàng: Chuyển từ 'dang_xu_ly' -> 'dang_giao'
                    elseif ($action === 'ship')
                    {
                        $upd = "UPDATE DonHang SET TrangThai = 'dang_giao' WHERE MaDonHang = $orderId";
                    }
                    // 3. Hoàn thành: Chuyển từ 'dang_giao' -> 'hoan_thanh'
                    elseif ($action === 'complete')
                    {
                        $upd = "UPDATE DonHang SET TrangThai = 'hoan_thanh' WHERE MaDonHang = $orderId";
                    }

                    if ($upd != "")
                    {
                        mysqli_query($conn, $upd);
                        header('Location: duyet_don.php');
                        exit();
                    }
                }

                // Lấy danh sách đơn hàng kèm thông tin user (không ánh xạ, cùng cách truy vấn như `home.php`)
                $sql = "SELECT dh.*, u.Ho, u.Ten, u.Email
                    FROM DonHang dh
                    LEFT JOIN Users u ON dh.MaUser = u.MaUser
                    ORDER BY dh.NgayDat DESC";
                $result = mysqli_query($conn, $sql);

                // Mảng ánh xạ trạng thái sang tiếng Việt cho dễ đọc
                $statusMapping = [
                    'cho_xac_nhan' => 'Chờ xác nhận',
                    'dang_xu_ly'   => 'Đang xử lý',
                    'dang_giao'    => 'Đang giao',
                    'hoan_thanh'   => 'Hoàn thành',
                    'da_huy'       => 'Đã hủy'
                ];

                if ($result && mysqli_num_rows($result) > 0)
                {
                    while ($row = mysqli_fetch_assoc($result))
                    {
                        $fullname = trim($row['Ho'] . ' ' . $row['Ten']);
                        $orderId = $row['MaDonHang'];
                        $tongTien = number_format($row['TongTien'], 0, ',', '.');
                        $phuongThuc = $row['PhuongThucThanhToan'];
                        $ngayDat = $row['NgayDat'];
                        $trangThai = $row['TrangThai'];
                        
                        // Lấy tên hiển thị tiếng Việt, nếu không có thì lấy nguyên gốc
                        $hienThiTrangThai = isset($statusMapping[$trangThai]) ? $statusMapping[$trangThai] : $trangThai;

                        echo "<tr>";
                        echo "<td class='col-order-id'>" . $orderId . "</td>";
                        echo "<td>" . ($fullname ? $fullname : 'Khách vãng lai') . "</td>";
                        echo "<td>" . htmlspecialchars($row['DiaChiGiaoHang']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['SDTGiaoHang']) . "</td>";
                        echo "<td>" . $tongTien . " VND</td>";
                        echo "<td>" . htmlspecialchars($phuongThuc) . "</td>";
                        echo "<td>" . $ngayDat . "</td>";
                        echo "<td>" . htmlspecialchars($hienThiTrangThai) . "</td>";
                        echo "<td class='actions'>";
                        echo "<div class='btn-group'>";

                        // Logic hiển thị nút bấm FULL trạng thái
                        switch ($trangThai) {
                            case 'cho_xac_nhan':
                                // Nút Duyệt (Xanh) và Hủy (Đỏ)
                                echo "<a class='btn-accept' href='duyet_don.php?order_id=$orderId&action=accept' onclick=\"return confirm('Xác nhận duyệt đơn #$orderId?')\">Duyệt</a>";
                                echo "<button type='button' class='btn-reject' onclick=\"startReject($orderId)\">Hủy</button>";
                                break;
                            
                            case 'dang_xu_ly':
                                // Nút Giao hàng (Xanh dương - style inline tạm thời hoặc thêm vào css)
                                echo "<a class='btn-accept' style='background-color: #17a2b8;' href='duyet_don.php?order_id=$orderId&action=ship' onclick=\"return confirm('Bắt đầu giao đơn #$orderId?')\">Giao hàng</a>";
                                break;

                            case 'dang_giao':
                                // Nút Hoàn thành (Xanh lá đậm)
                                echo "<a class='btn-accept' style='background-color: #28a745;' href='duyet_don.php?order_id=$orderId&action=complete' onclick=\"return confirm('Xác nhận hoàn thành đơn #$orderId?')\">Hoàn thành</a>";
                                break;

                            case 'hoan_thanh':
                                // Không có nút hành động, chỉ xem chi tiết
                                break;

                            case 'da_huy':
                                // Không có nút hành động, chỉ xem chi tiết
                                break;

                            default:
                                break;
                        }

                        echo "<a class='btn-detail' href='chi_tiet_don.php?order_id=$orderId'>Chi tiết</a>";
                        echo "</div>";
                        echo "</td>";
                        echo "</tr>";
                    }
                }
                else
                {
                    echo "<tr><td colspan='9'>Không có đơn hàng nào.</td></tr>";
                }

                mysqli_close($conn);
            ?>
        </table>
    </div>
    <script>
        // Tạo và submit form POST động để gửi yêu cầu hủy (kèm lý do)
        function startReject(orderId)
        {
            if (!confirm('Xác nhận hủy đơn #' + orderId + '?'))
            {
                return;
            }

            // Tạo form động
            var form = document.createElement('form');
            form.method = 'POST';
            form.action = 'duyet_don.php';

            var inputId = document.createElement('input');
            inputId.type = 'hidden';
            inputId.name = 'order_id';
            inputId.value = orderId;
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