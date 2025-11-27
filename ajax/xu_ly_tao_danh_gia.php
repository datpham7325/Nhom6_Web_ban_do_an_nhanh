<?php
// 🔥 BẮT ĐẦU ĐỆM ĐẦU RA (OUTPUT BUFFERING)
ob_start();

// SỬA LỖI JSON: Tự xử lý Session và KẾT NỐI DB để tránh include header.php có HTML.
session_start();
include_once "../includes/myenv.php"; 
include_once "../function/functions.php"; 

// 1. TẠO KẾT NỐI CSDL TRỰC TIẾP
$conn = mysqli_connect($db_host, $db_user, $db_password, $db_db, $db_port);
if ($conn === false) {
    ob_end_clean();
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Lỗi kết nối CSDL. Vui lòng kiểm tra myenv.php.']);
    exit;
}
mysqli_set_charset($conn, "utf8mb4");


// Hàm tiện ích để gửi lỗi
function sendError($message, $conn) {
    ob_end_clean();
    header('Content-Type: application/json');
    if ($conn && $conn !== false) {
        mysqli_rollback($conn);
        mysqli_close($conn);
    }
    echo json_encode(['success' => false, 'message' => $message]);
    exit;
}

// 2. Cấu hình phản hồi
header('Content-Type: application/json');

// 3. Kiểm tra phương thức và trạng thái đăng nhập
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendError("Yêu cầu không hợp lệ.", $conn);
}

if (!isset($_SESSION['loggedin']) || !isset($_SESSION['MaUser'])) {
    sendError("Bạn cần đăng nhập để thực hiện đánh giá.", $conn);
}

$maUser = $_SESSION['MaUser'];

// 4. Lấy và kiểm tra dữ liệu POST
$maDonHang = $_POST['maDonHang'] ?? null;
$reviewsJson = $_POST['reviews'] ?? null;

if (empty($maDonHang) || !is_numeric($maDonHang)) {
    sendError("Mã đơn hàng không hợp lệ.", $conn);
}

if (empty($reviewsJson)) {
    sendError("Không có đánh giá nào được gửi.", $conn);
}

$reviews = json_decode($reviewsJson, true);

if (json_last_error() !== JSON_ERROR_NONE) {
    sendError("Dữ liệu đánh giá bị lỗi định dạng.", $conn);
}

if (!is_array($reviews) || count($reviews) === 0) {
    sendError("Không có đánh giá hợp lệ nào.", $conn);
}

// 5. Bắt đầu Transaction
mysqli_begin_transaction($conn);
$allSuccess = true;
$messages = [];

try {
    // 🔥 ĐÃ SỬA LỖI: Sử dụng tên cột chính xác 'Diem' (Tên cột chính xác trong DB)
    $stmt_insert = mysqli_prepare($conn, "INSERT INTO DanhGia (MaUser, MaMonAn, Diem, NoiDung, TrangThai) VALUES (?, ?, ?, ?, 'cho_duyet')");
    
    if (!$stmt_insert) {
        throw new Exception("Lỗi chuẩn bị truy vấn: " . mysqli_error($conn));
    }
    
    foreach ($reviews as $review) {
        $maMonAn = $review['maMonAn'];
        $diem = $review['diem']; 
        $noiDung = trim($review['noidung']);

        // Ràng buộc tham số: iiis (MaUser, MaMonAn, Diem, NoiDung)
        mysqli_stmt_bind_param($stmt_insert, "iiis", $maUser, $maMonAn, $diem, $noiDung);
        
        if (!mysqli_stmt_execute($stmt_insert)) {
            $allSuccess = false;
            // Kiểm tra lỗi trùng lặp
            if (mysqli_errno($conn) == 1062) {
                 $messages[] = "Món ID {$maMonAn} đã được đánh giá trước đó.";
            } else {
                 $messages[] = "Món ID {$maMonAn}: " . mysqli_stmt_error($stmt_insert);
            }
        }
    }

    // Đóng statement
    mysqli_stmt_close($stmt_insert);

    // 6. Kết thúc Transaction
    if ($allSuccess) {
        mysqli_commit($conn);
        ob_end_clean();
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'message' => 'Đã gửi thành công ' . count($reviews) . ' đánh giá.']);
    } else {
        mysqli_rollback($conn);
        sendError("Có lỗi xảy ra trong quá trình chèn đánh giá. Chi tiết: " . implode(" | ", $messages), $conn);
    }

} catch (Exception $e) {
    sendError("Lỗi hệ thống: " . $e->getMessage(), $conn);
}

mysqli_close($conn);
exit; 
?>