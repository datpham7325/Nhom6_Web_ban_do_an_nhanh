<?php
session_start();

// Kiểm tra đăng nhập
if (!isset($_SESSION['loggedin'])) {
    echo '{"success":false,"message":"Chưa đăng nhập"}';
    exit();
}

// Kiểm tra phương thức
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo '{"success":false,"message":"Phương thức không hợp lệ"}';
    exit();
}

// Lấy dữ liệu
$maDonHang = $_POST['madonhang'] ?? '';
$maUser = $_SESSION['MaUser'];

if (empty($maDonHang) || !is_numeric($maDonHang)) {
    echo '{"success":false,"message":"Mã đơn hàng không hợp lệ"}';
    exit();
}

// Kết nối database
$conn = mysqli_connect("localhost", "root", "", "quanly_cua_hang");
if (!$conn) {
    echo '{"success":false,"message":"Lỗi kết nối database"}';
    exit();
}

mysqli_set_charset($conn, "utf8mb4");

try {
    // Kiểm tra đơn hàng tồn tại và thuộc về user
    $check_sql = "SELECT TrangThai FROM DonHang WHERE MaDonHang = ? AND MaUser = ?";
    $check_stmt = mysqli_prepare($conn, $check_sql);
    
    if (!$check_stmt) {
        throw new Exception("Lỗi chuẩn bị truy vấn");
    }
    
    mysqli_stmt_bind_param($check_stmt, "ii", $maDonHang, $maUser);
    
    if (!mysqli_stmt_execute($check_stmt)) {
        throw new Exception("Lỗi thực thi truy vấn");
    }
    
    $result = mysqli_stmt_get_result($check_stmt);

    if (mysqli_num_rows($result) === 0) {
        throw new Exception("Đơn hàng không tồn tại hoặc không thuộc về bạn");
    }

    $order = mysqli_fetch_assoc($result);
    $trangThai = $order['TrangThai'];

    // Kiểm tra trạng thái có thể hủy
    if (!in_array($trangThai, ['cho_xac_nhan', 'dang_xu_ly'])) {
        throw new Exception("Chỉ có thể hủy đơn hàng ở trạng thái 'Chờ xác nhận' hoặc 'Đang xử lý'");
    }

    // CẬP NHẬT TRẠNG THÁI THÀNH ĐÃ HỦY
    $update_sql = "UPDATE DonHang SET TrangThai = 'da_huy', NgayCapNhat = NOW() WHERE MaDonHang = ?";
    $update_stmt = mysqli_prepare($conn, $update_sql);
    
    if (!$update_stmt) {
        throw new Exception("Lỗi chuẩn bị cập nhật");
    }
    
    mysqli_stmt_bind_param($update_stmt, "i", $maDonHang);

    if (!mysqli_stmt_execute($update_stmt)) {
        throw new Exception("Lỗi cập nhật đơn hàng");
    }

    if (mysqli_stmt_affected_rows($update_stmt) > 0) {
        echo '{"success":true,"message":"Hủy đơn hàng thành công"}';
    } else {
        throw new Exception("Không thể cập nhật đơn hàng");
    }

} catch (Exception $e) {
    echo '{"success":false,"message":"' . $e->getMessage() . '"}';
} finally {
    // Đóng kết nối
    if (isset($check_stmt)) mysqli_stmt_close($check_stmt);
    if (isset($update_stmt)) mysqli_stmt_close($update_stmt);
    if (isset($conn)) mysqli_close($conn);
}
?>