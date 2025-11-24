<?php
// Hàm lấy tên size từ MaSize
function getSizeName($maSize) {
    global $conn;
    
    $sql = "SELECT TenSize FROM KichThuoc WHERE MaSize = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $maSize);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if($row = mysqli_fetch_assoc($result)) {
        return $row['TenSize'];
    }
    
    return 'Mặc định';
}

// Hàm format tiền
function formatCurrency($amount) {
    return number_format($amount, 0, ',', '.') . '₫';
}

// Hàm kiểm tra sản phẩm có trong giỏ hàng không
function isInCart($maBienThe) {
    if(!isset($_SESSION['loggedin'])) return false;
    
    global $conn;
    $maUser = $_SESSION['MaUser'];
    
    $sql = "SELECT COUNT(*) as count FROM GioHang WHERE MaUser = ? AND MaBienThe = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ii", $maUser, $maBienThe);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);
    
    return $row['count'] > 0;
}
?>