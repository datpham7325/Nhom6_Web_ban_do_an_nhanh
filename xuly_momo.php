<?php
session_start();
// --- CẤU HÌNH KẾT NỐI DATABASE ---
// Bạn hãy thay dòng này bằng đường dẫn tới file kết nối CSDL của bạn
// Ví dụ: include 'includes/db_connect.php'; 
// Giả sử biến kết nối là $conn
// --- BẮT ĐẦU ĐOẠN CODE KẾT NỐI DATABASE ---
include_once("includes/myenv.php");
$conn = mysqli_connect($db_host, $db_user, $db_password, $db_db, $db_port);

// Kiểm tra kết nối
if (!$conn) {
    die("Kết nối database thất bại: " . mysqli_connect_error());
}
// --- KẾT THÚC ĐOẠN CODE KẾT NỐI ---

// --- CẤU HÌNH API MOMO ---
$config = [
    'endpoint' => "https://test-payment.momo.vn/v2/gateway/api/create",
    'partnerCode' => 'MOMOBKUN20180529',
    'accessKey' => 'klm05TvNBzhg7h7j',
    'secretKey' => 'at67qH6mk8w5Y1nAyMoYKMWACiEi2bsa'
];

// Hàm hỗ trợ gửi request
function execPostRequest($url, $data)
{
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
    $result = curl_exec($ch);
    curl_close($ch);
    return $result;
}

// ==============================================================================
// PHẦN 1: GỬI YÊU CẦU THANH TOÁN (Khi người dùng bấm nút Thanh Toán trên web)
// ==============================================================================
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['phuongthuc']) && $_POST['phuongthuc'] == 'momo') {

    if (!isset($_SESSION['MaUser'])) {
        die("Vui lòng đăng nhập.");
    }

    $maUser = $_SESSION['MaUser'];
    $diaChi = $_POST['diachi'];
    $sdt = $_POST['sdt'];
    $ghiChu = $_POST['ghichu'];

    // 1. Tính tổng tiền lại từ Database (Để bảo mật, không lấy từ POST)
    $sqlCart = "SELECT gh.SoLuong, bto.DonGia 
                FROM GioHang gh 
                JOIN bienthemonan bto ON gh.MaBienThe = bto.MaBienThe 
                WHERE gh.MaUser = ?";
    $stmt = mysqli_prepare($conn, $sqlCart);
    mysqli_stmt_bind_param($stmt, "i", $maUser);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    $tongTien = 0;
    while ($row = mysqli_fetch_assoc($result)) {
        $tongTien += $row['SoLuong'] * $row['DonGia'];
    }
    mysqli_stmt_close($stmt);

    if ($tongTien == 0) {
        die("Giỏ hàng trống.");
    }

    // 2. Chuẩn bị dữ liệu gửi sang MoMo
    $orderInfo = "Thanh toan don hang cua User " . $maUser;
    $amount = (string)$tongTien;
    $orderId = time() . "_" . $maUser;
    $requestId = time() . "";

    // URL trả về: Trỏ lại chính file này nhưng có thêm tham số action=return
    // Bạn nhớ thay 'http://localhost/duan/' thành tên miền thực tế của bạn
    $domain = "http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . "/";
    $redirectUrl = $domain . "xuly_momo.php?action=return";
    $ipnUrl = $domain . "xuly_momo.php?action=return";

    // Đóng gói thông tin giao hàng vào extraData (để dùng lại khi MoMo trả về)
    $extraDataArr = [
        'diachi' => $diaChi,
        'sdt' => $sdt,
        'ghichu' => $ghiChu
    ];
    $extraData = base64_encode(json_encode($extraDataArr));

    // Tạo chữ ký
    $rawHash = "accessKey=" . $config['accessKey'] . "&amount=" . $amount . "&extraData=" . $extraData . "&ipnUrl=" . $ipnUrl . "&orderId=" . $orderId . "&orderInfo=" . $orderInfo . "&partnerCode=" . $config['partnerCode'] . "&redirectUrl=" . $redirectUrl . "&requestId=" . $requestId . "&requestType=captureWallet";
    $signature = hash_hmac("sha256", $rawHash, $config['secretKey']);

    $data = [
        'partnerCode' => $config['partnerCode'],
        'partnerName' => "Web Ban Hang",
        "storeId" => "MomoTestStore",
        'requestId' => $requestId,
        'amount' => $amount,
        'orderId' => $orderId,
        'orderInfo' => $orderInfo,
        'redirectUrl' => $redirectUrl,
        'ipnUrl' => $ipnUrl,
        'lang' => 'vi',
        'extraData' => $extraData,
        'requestType' => 'captureWallet',
        'signature' => $signature
    ];

    $result = execPostRequest($config['endpoint'], json_encode($data));
    $jsonResult = json_decode($result, true);

    if (isset($jsonResult['payUrl'])) {
        header('Location: ' . $jsonResult['payUrl']);
        exit;
    } else {
        echo "Lỗi API MoMo: " . ($jsonResult['message'] ?? 'Unknown');
    }
}

// ==============================================================================
// PHẦN 2: XỬ LÝ KẾT QUẢ TRẢ VỀ (Khi thanh toán xong và quay lại)
// ==============================================================================
if (isset($_GET['action']) && $_GET['action'] == 'return') {

    // Kiểm tra thành công (resultCode = 0)
    if (isset($_GET['resultCode']) && $_GET['resultCode'] == '0') {

        $maUser = $_SESSION['MaUser'];
        $amount = $_GET['amount'];
        $extraData = json_decode(base64_decode($_GET['extraData']), true);

        $diaChi = $extraData['diachi'];
        $sdt = $extraData['sdt'];
        $ghiChu = $extraData['ghichu'] . " (Đã thanh toán MoMo)";
        $phuongThuc = "momo";

        // --- LƯU VÀO DATABASE (Code lấy từ ThanhToan.php của bạn) ---

        // 1. Lấy lại giỏ hàng
        $cartSQL = "SELECT gh.MaBienThe, gh.SoLuong, bto.DonGia 
                    FROM GioHang gh 
                    JOIN bienthemonan bto ON gh.MaBienThe = bto.MaBienThe 
                    WHERE gh.MaUser = ?";
        $stmt = mysqli_prepare($conn, $cartSQL);
        mysqli_stmt_bind_param($stmt, "i", $maUser);
        mysqli_stmt_execute($stmt);
        $cartResult = mysqli_stmt_get_result($stmt);

        $cartItems = [];
        while ($item = mysqli_fetch_assoc($cartResult)) {
            $cartItems[] = $item;
        }
        mysqli_stmt_close($stmt);

        if (!empty($cartItems)) {
            // 2. Tạo đơn hàng
            $insertOrderSQL = "INSERT INTO DonHang (MaUser, TongTien, PhuongThucThanhToan, DiaChiGiaoHang, SDTGiaoHang, GhiChu) 
                              VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = mysqli_prepare($conn, $insertOrderSQL);
            mysqli_stmt_bind_param($stmt, "idssss", $maUser, $amount, $phuongThuc, $diaChi, $sdt, $ghiChu);

            if (mysqli_stmt_execute($stmt)) {
                $maDonHang = mysqli_insert_id($conn);

                // 3. Tạo chi tiết đơn hàng
                $insertDetailSQL = "INSERT INTO ChiTietDonHang (MaDonHang, MaBienThe, SoLuong, DonGia, ThanhTien) 
                                  VALUES (?, ?, ?, ?, ?)";
                $stmt2 = mysqli_prepare($conn, $insertDetailSQL);

                foreach ($cartItems as $item) {
                    $thanhtien = $item['DonGia'] * $item['SoLuong'];
                    mysqli_stmt_bind_param($stmt2, "iiidd", $maDonHang, $item['MaBienThe'], $item['SoLuong'], $item['DonGia'], $thanhtien);
                    mysqli_stmt_execute($stmt2);
                }
                mysqli_stmt_close($stmt2);

                // 4. Xóa giỏ hàng
                $deleteCartSQL = "DELETE FROM GioHang WHERE MaUser = ?";
                $stmt3 = mysqli_prepare($conn, $deleteCartSQL);
                mysqli_stmt_bind_param($stmt3, "i", $maUser);
                mysqli_stmt_execute($stmt3);
                mysqli_stmt_close($stmt3);

                // Chuyển hướng về trang đơn hàng
                $_SESSION['order_success'] = "Thanh toán thành công! Đơn hàng #$maDonHang";
                header("Location: DonHang.php");
                exit();
            }
        }
    } else {
        // Thất bại thì quay về trang thanh toán báo lỗi
        $msg = $_GET['message'] ?? "Giao dịch thất bại";
        echo "<script>alert('$msg'); window.location.href='ThanhToan.php';</script>";
    }
}
