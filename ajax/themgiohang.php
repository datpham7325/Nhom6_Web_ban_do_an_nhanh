<?php
session_start();
include_once "../includes/myenv.php";

if(!isset($_SESSION['loggedin'])) {
    echo json_encode(['success' => false, 'message' => 'Vui lòng đăng nhập']);
    exit();
}

$maUser = $_SESSION['MaUser'];
$maBienThe = $_POST['mabienthe'];
$soLuong = $_POST['soluong'] ?? 1;

$conn = mysqli_connect($db_host, $db_user, $db_password, $db_db, $db_port);

// Kiểm tra đã có trong giỏ chưa
$checkSQL = "SELECT * FROM GioHang WHERE MaUser = $maUser AND MaBienThe = $maBienThe";
$checkResult = mysqli_query($conn, $checkSQL);

if(mysqli_num_rows($checkResult) > 0) {
    // Cập nhật số lượng
    $updateSQL = "UPDATE GioHang SET SoLuong = SoLuong + $soLuong WHERE MaUser = $maUser AND MaBienThe = $maBienThe";
    mysqli_query($conn, $updateSQL);
} else {
    // Thêm mới
    $insertSQL = "INSERT INTO GioHang (MaUser, MaBienThe, SoLuong) VALUES ($maUser, $maBienThe, $soLuong)";
    mysqli_query($conn, $insertSQL);
}

echo json_encode(['success' => true]);
?>