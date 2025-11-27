<?php
// 🔥 BẮT ĐẦU ĐỆM ĐẦU RA (OUTPUT BUFFERING)
ob_start(); 

session_start();
include_once "../includes/myenv.php";

// Hàm tiện ích để gửi phản hồi JSON và thoát
function sendResponse($data, $conn) {
    ob_end_clean(); // Xóa sạch mọi output không mong muốn
    header('Content-Type: application/json');
    echo json_encode($data);
    if ($conn && $conn !== false) {
        mysqli_close($conn);
    }
    exit();
}

// 1. Kiểm tra đăng nhập
if(!isset($_SESSION['loggedin'])) {
    sendResponse(['success' => false, 'message' => 'Vui lòng đăng nhập'], null);
}

$maUser = $_SESSION['MaUser'];
// 🔥 SỬA: Nhận MaGioHang (đã sửa trong JS)
$maGioHang = $_POST['magiohang'] ?? null; 
$soLuong = $_POST['soluong'] ?? null;

// 2. Validate input
if(empty($maGioHang) || !is_numeric($maGioHang)) {
    sendResponse(['success' => false, 'message' => 'ID Giỏ hàng không hợp lệ'], null);
}
// Kiểm tra số lượng phải là số và không được trống
if (!is_numeric($soLuong) || trim($soLuong) === '') {
    sendResponse(['success' => false, 'message' => 'Số lượng phải là một giá trị số'], null);
}

// Chuyển đổi số lượng sang kiểu integer
$soLuong = (int)$soLuong;
$maGioHang = (int)$maGioHang; // Ép kiểu MaGioHang

// 3. Kết nối Database
$conn = mysqli_connect($db_host, $db_user, $db_password, $db_db, $db_port);
if(!$conn) {
    sendResponse(['success' => false, 'message' => 'Lỗi kết nối database'], null);
}
mysqli_set_charset($conn, "utf8mb4");

$stmt = null;
$query_type = '';

// 4. Logic cập nhật / xóa
if($soLuong <= 0) {
    // Xóa item khỏi giỏ hàng
    // 🔥 SỬA SQL: Dùng MaGioHang làm khóa chính để xóa
    $deleteSQL = "DELETE FROM GioHang WHERE MaUser = ? AND MaGioHang = ?"; 
    $stmt = mysqli_prepare($conn, $deleteSQL);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "ii", $maUser, $maGioHang);
        $query_type = 'DELETE';
    }
} else {
    // Cập nhật số lượng
    // 🔥 SỬA SQL: Dùng MaGioHang làm khóa chính để cập nhật
    $updateSQL = "UPDATE GioHang SET SoLuong = ? WHERE MaUser = ? AND MaGioHang = ?";
    $stmt = mysqli_prepare($conn, $updateSQL);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "iii", $soLuong, $maUser, $maGioHang);
        $query_type = 'UPDATE';
    }
}

// 5. Thực thi truy vấn cuối cùng
if($stmt === false) {
    sendResponse(['success' => false, 'message' => 'Lỗi chuẩn bị truy vấn cuối cùng.'], $conn);
}

if(mysqli_stmt_execute($stmt)) {
    mysqli_stmt_close($stmt);
    sendResponse(['success' => true, 'action' => $query_type], $conn);
} else {
    $error_message = 'Lỗi cập nhật giỏ hàng: ' . mysqli_stmt_error($stmt);
    mysqli_stmt_close($stmt);
    sendResponse(['success' => false, 'message' => $error_message], $conn);
}
?>