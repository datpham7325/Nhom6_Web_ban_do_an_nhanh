<?php
session_start();
include_once "../includes/myenv.php";

if(!isset($_SESSION['loggedin'])) {
    echo json_encode(['success' => false, 'message' => 'Vui lòng đăng nhập']);
    exit();
}

$maUser = $_SESSION['MaUser'];
$maBienThe = $_POST['mabienthe'];
$soLuong = $_POST['soluong'];

// Validate input
if(empty($maBienThe) || !is_numeric($maBienThe) || !is_numeric($soLuong)) {
    echo json_encode(['success' => false, 'message' => 'Dữ liệu không hợp lệ']);
    exit();
}

$conn = mysqli_connect($db_host, $db_user, $db_password, $db_db, $db_port);

if(!$conn) {
    echo json_encode(['success' => false, 'message' => 'Lỗi kết nối database']);
    exit();
}

if($soLuong <= 0) {
    // Xóa item khỏi giỏ hàng
    $deleteSQL = "DELETE FROM GioHang WHERE MaUser = ? AND MaBienThe = ?";
    $stmt = mysqli_prepare($conn, $deleteSQL);
    mysqli_stmt_bind_param($stmt, "ii", $maUser, $maBienThe);
} else {
    // Kiểm tra item có tồn tại không
    $checkSQL = "SELECT * FROM GioHang WHERE MaUser = ? AND MaBienThe = ?";
    $stmt = mysqli_prepare($conn, $checkSQL);
    mysqli_stmt_bind_param($stmt, "ii", $maUser, $maBienThe);
    mysqli_stmt_execute($stmt);
    $checkResult = mysqli_stmt_get_result($stmt);
    
    if(mysqli_num_rows($checkResult) > 0) {
        // Cập nhật số lượng
        $updateSQL = "UPDATE GioHang SET SoLuong = ? WHERE MaUser = ? AND MaBienThe = ?";
        $stmt = mysqli_prepare($conn, $updateSQL);
        mysqli_stmt_bind_param($stmt, "iii", $soLuong, $maUser, $maBienThe);
    } else {
        // Thêm mới
        $insertSQL = "INSERT INTO GioHang (MaUser, MaBienThe, SoLuong) VALUES (?, ?, ?)";
        $stmt = mysqli_prepare($conn, $insertSQL);
        mysqli_stmt_bind_param($stmt, "iii", $maUser, $maBienThe, $soLuong);
    }
}

if(mysqli_stmt_execute($stmt)) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Lỗi cập nhật giỏ hàng']);
}

mysqli_close($conn);
?>