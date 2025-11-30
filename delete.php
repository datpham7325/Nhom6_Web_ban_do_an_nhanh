<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="css/delete.css">
    <!-- Thêm font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;700;900&display=swap" rel="stylesheet">
</head>

<body>
    <?php include_once "includes/header2.php"; ?>

    <!-- Kiểm tra user -->
    <?php
    // Kiểm tra xem session đã có MaUser chưa
    if (isset($_SESSION['MaUser'])) {
        $maUserCheck = $_SESSION['MaUser'];

        // Truy vấn lấy QuyenHan hiện tại từ CSDL
        $sqlRole = "SELECT QuyenHan FROM Users WHERE MaUser = ?";
        $stmtRole = mysqli_prepare($conn, $sqlRole);

        if ($stmtRole) {
            mysqli_stmt_bind_param($stmtRole, "i", $maUserCheck);
            mysqli_stmt_execute($stmtRole);
            $resultRole = mysqli_stmt_get_result($stmtRole);
            $userRole = mysqli_fetch_assoc($resultRole);

            // Kiểm tra logic:
            // 1. Không tìm thấy user trong DB
            // 2. Hoặc QuyenHan không phải là 'admin'
            if (!$userRole || $userRole['QuyenHan'] !== 'admin') {
                header("Location: index.php");
                exit();
            }
        } else {
            // Lỗi câu lệnh SQL thì cũng cho về index để an toàn
            header("Location: DangNhap.php");
            exit();
        }
    } else {
        // Chưa đăng nhập thì chuyển hướng về index
        header("Location: DangNhap.php");
        exit();
    }
    ?>

    <!-- Mở kết nối -->
    <?php
    include_once("includes/myenv.php");
    $conn = mysqli_connect($db_host, $db_user, $db_password, $db_db, $db_port);
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    if (isset($_GET['mabienthe'])) {
        $maBienThe = $_GET['mabienthe'];
        $strSQL = "SELECT * FROM loaimonan, kichthuoc, monan, bienthemonan 
                    WHERE loaimonan.MaLoai = monan.MaLoai 
                    AND kichthuoc.MaSize = bienthemonan.MaSize
                    AND monan.MaMonAn = bienthemonan.MaMonAn
                    AND bienthemonan.MaBienThe = '$maBienThe'";
        $result = mysqli_query($conn, $strSQL);

        // Lấy dữ liệu
        while ($row = mysqli_fetch_array($result)) {
            $maBienThe = ChuanHoaMa($row['MaBienThe']);
            $maBienThe2 = $row['MaBienThe'];
            $tenMonAn = $row['TenMonAn'];
            $hinhAnh = $row['HinhAnh'];
            $tenLoai = $row['TenLoai'];
            $tenSize = $row['TenSize'];
            $donGia = $row['DonGia'];
            $moTa = $row['MoTa'];
        }
    }
    ?>

    <!-- Xử lý các nút -->
    <?php
    if (isset($_GET['btnxacnhan'])) {
        $maBienThe = $_GET['mabienthe'];

        $strSQL = "DELETE FROM bienthemonan WHERE MaBienThe = '$maBienThe'";
        $result = mysqli_query($conn, $strSQL);

        if ($result) {
            // Chuyển hướng về trang home sau khi xóa
            header("Location: home.php");
            exit();
        } else {
            echo "Xóa thất bại. Vui lòng thử lại.";
        }
    } else if (isset($_GET['btnhome'])) {
        // Chuyển hướng về trang home
        $page = (int)$_GET['page'];
        header("Location: home.php?page=$page");
        exit();
    } else if (isset($_GET['btnchinhsua'])) {
        // Chuyển hướng về trang detail
        $page = (int)$_GET['page'];
        header("Location: detail.php?mabienthe=$maBienThe&page=$page");
        exit();
    }
    ?>

    <?php
    // Chuẩn hóa mã
    function ChuanHoaMa($mma)
    {
        $kq = str_pad($mma, 3, '0', STR_PAD_LEFT);
        return $kq;
    }
    ?>

    <form class="form1">
        <input type="hidden" name="mabienthe" value="<?php echo $maBienThe; ?>">
        <input type="hidden" name="page" value="<?php echo isset($_GET['page']) ? (int)$_GET['page'] : 1; ?>">
        <table>
            <tr>
                <td colspan="7">
                    <h2>XÁC NHẬN XÓA?!</h2>
                </td>
            </tr>
            <tr class="tieude">
                <td>Mã món ăn</td>
                <td>Tên</td>
                <td>Hình ảnh</td>
                <td>Loại món ăn</td>
                <td>Size</td>
                <td>Giá (VNĐ)</td>
                <td>Mô tả</td>
            </tr>

            <?php
            echo "<tr class='noidung'>";
            echo "<td>" . $maBienThe . "</td>";
            echo "<td>" . $tenMonAn . "</td>";

            $anh = "img/" . $hinhAnh;
            echo "<td>" . "<img src='$anh' width='50px' height='50px'>" . "</td>";

            echo "<td>" . $tenLoai . "</td>";
            echo "<td>" . $tenSize . "</td>";
            echo "<td>" . number_format($donGia, 0, ',', '.') . " VND" . "</td>";
            echo "<td>" . $moTa . "</td>";
            echo "</tr>";
            ?>

            <tr>
                <td class="tdbtn" colspan="7">
                    <input class="btnXacNhan" type="submit" name="btnxacnhan" value="Xác nhận">
                    <input class="btnChinhSua" type="submit" name="btnchinhsua" value="Chỉnh sửa">
                    <input class="btnHome" type="submit" name="btnhome" value="Trang chủ">
                </td>
            </tr>
        </table>
    </form>
</body>

</html>