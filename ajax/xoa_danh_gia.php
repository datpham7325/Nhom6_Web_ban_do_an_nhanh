<?php
// Sử dụng JSON để trả về kết quả
header('Content-Type: application/json');

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
$maDanhGia = $_POST['maDanhGia'] ?? '';
$maUser = $_SESSION['MaUser'];

if (empty($maDanhGia) || !is_numeric($maDanhGia)) {
    echo '{"success":false,"message":"Mã đánh giá không hợp lệ"}';
    exit();
}

// Kết nối database
$conn = mysqli_connect("localhost", "root", "", "quanly_cua_hang");
if (!$conn) {
    echo '{"success":false,"message":"Lỗi kết nối database: ' . mysqli_connect_error() . '"}';
    exit();
}

mysqli_set_charset($conn, "utf8mb4");

try {
    // 1. Kiểm tra đánh giá tồn tại và thuộc về user
    $check_sql = "SELECT MaDanhGia FROM DanhGia WHERE MaDanhGia = ? AND MaUser = ?";
    $check_stmt = mysqli_prepare($conn, $check_sql);
    
    if (!$check_stmt) {
        throw new Exception("Lỗi chuẩn bị truy vấn kiểm tra: " . mysqli_error($conn));
    }
    
    mysqli_stmt_bind_param($check_stmt, "ii", $maDanhGia, $maUser);
    
    if (!mysqli_stmt_execute($check_stmt)) {
        throw new Exception("Lỗi thực thi truy vấn kiểm tra");
    }
    
    $result = mysqli_stmt_get_result($check_stmt);

    if (mysqli_num_rows($result) === 0) {
        throw new Exception("Đánh giá không tồn tại hoặc không thuộc về bạn");
    }
    mysqli_stmt_close($check_stmt);

    // 2. XÓA ĐÁNH GIÁ
    $delete_sql = "DELETE FROM DanhGia WHERE MaDanhGia = ?";
    $delete_stmt = mysqli_prepare($conn, $delete_sql);
    
    if (!$delete_stmt) {
        throw new Exception("Lỗi chuẩn bị xóa: " . mysqli_error($conn));
    }
    
    mysqli_stmt_bind_param($delete_stmt, "i", $maDanhGia);

    if (!mysqli_stmt_execute($delete_stmt)) {
        throw new Exception("Lỗi xóa đánh giá: " . mysqli_stmt_error($delete_stmt));
    }

    if (mysqli_stmt_affected_rows($delete_stmt) > 0) {
        // Xóa thành công
        echo '{"success":true,"message":"Xóa đánh giá thành công"}';
    } else {
        throw new Exception("Không có đánh giá nào bị xóa (có thể đã được xóa bởi người dùng khác)");
    }

} catch (Exception $e) {
    // Xử lý lỗi
    $errorMessage = $e->getMessage();
    error_log("Xóa đánh giá lỗi: " . $errorMessage);
    echo '{"success":false,"message":"' . htmlspecialchars($errorMessage) . '"}';
} finally {
    // Đóng kết nối
    if (isset($delete_stmt)) mysqli_stmt_close($delete_stmt);
    if (isset($conn)) mysqli_close($conn);
}
?>