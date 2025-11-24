<?php
session_start();
include_once "../includes/myenv.php";

if(!isset($_SESSION['loggedin'])) {
    echo json_encode(['success' => false, 'message' => 'Vui lòng đăng nhập']);
    exit();
}

$maGioHang = $_POST['magiohang'];

$conn = mysqli_connect($db_host, $db_user, $db_password, $db_db, $db_port);

$deleteSQL = "DELETE FROM GioHang WHERE MaGioHang = ?";
$stmt = mysqli_prepare($conn, $deleteSQL);
mysqli_stmt_bind_param($stmt, "i", $maGioHang);

if(mysqli_stmt_execute($stmt)) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Lỗi xóa giỏ hàng']);
}

mysqli_close($conn);
?>