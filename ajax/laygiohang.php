<?php
session_start();
include_once "../includes/myenv.php";

if(!isset($_SESSION['loggedin']) || !isset($_SESSION['MaUser'])) {
    echo json_encode(['success' => false, 'message' => 'Vui lòng đăng nhập', 'cart' => []]);
    exit();
}

$maUser = $_SESSION['MaUser'];

$conn = mysqli_connect($db_host, $db_user, $db_password, $db_db, $db_port);

if(!$conn) {
    echo json_encode(['success' => false, 'message' => 'Lỗi kết nối database', 'cart' => []]);
    exit();
}

// Lấy giỏ hàng với thông tin đầy đủ
$sql = "SELECT gh.*, ma.TenMonAn, ma.HinhAnh, bto.DonGia, bto.MaBienThe
        FROM GioHang gh 
        JOIN BienTheMonAn bto ON gh.MaBienThe = bto.MaBienThe 
        JOIN MonAn ma ON bto.MaMonAn = ma.MaMonAn 
        WHERE gh.MaUser = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $maUser);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$cart = [];
while ($row = mysqli_fetch_assoc($result)) {
    $cart[] = [
        'maBienThe' => $row['MaBienThe'],
        'tenMon' => $row['TenMonAn'],
        'imageSrc' => 'img/' . $row['HinhAnh'],
        'gia' => $row['DonGia'],
        'quantity' => $row['SoLuong']
    ];
}

echo json_encode(['success' => true, 'cart' => $cart]);

mysqli_close($conn);
?>