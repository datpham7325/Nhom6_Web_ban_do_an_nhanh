<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="css/home.css">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <!-- Thêm font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;700;900&display=swap" rel="stylesheet">

</head>

<body>
    <!-- Mở kết nối -->
    <?php
    include_once("includes/myenv.php");
    $conn = mysqli_connect($db_host, $db_user, $db_password, $db_db, $db_port);
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    ?>

    <!-- Phân trang -->
    <?php
    //Khởi tạo biến tìm kiếm
    $keyword = isset($_GET['keyword']) ? $_GET['keyword'] : "";

    //Tổng số dòng
    $conditions = [];

    if (!empty($keyword)) {
        $conditions[] = "monan.TenMonAn LIKE '%$keyword%'";
    }

    if (isset($_GET['maloai']) && $_GET['maloai'] != "") {
        $maloai = $_GET['maloai'];
        $conditions[] = "loaimonan.MaLoai = '$maloai'";
    }

    if (isset($_GET['dongia']) && $_GET['dongia'] != "") {
        $dongia = (float)$_GET['dongia'];
        $conditions[] = "bienthemonan.DonGia >= $dongia";
    }

    $whereCount = "WHERE loaimonan.MaLoai = monan.MaLoai 
                    AND kichthuoc.MaSize = bienthemonan.MaSize
                    AND monan.MaMonAn = bienthemonan.MaMonAn";

    if (!empty($conditions)) {
        $whereCount .= " AND " . implode(" AND ", $conditions);
    }

    // Đếm số dòng
    $sqlCount = "SELECT COUNT(*) FROM loaimonan, kichthuoc, monan, bienthemonan $whereCount";
    $count = mysqli_query($conn, $sqlCount);
    $row = mysqli_fetch_row($count);
    $maxRow = $row[0];


    //Số dòng mỗi trang
    $pageRow = 10;

    //Số trang tối đa
    $pageMax = ceil($maxRow / $pageRow);

    //Trang hiện tại
    $pageCurrent = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $pageCurrent = max(1, $pageCurrent); // đảm bảo >=1

    // Giới hạn page hợp lệ
    if ($pageCurrent < 1) $pageCurrent = 1;
    if ($pageCurrent > $pageMax) $pageCurrent = $pageMax;

    //Thứ tự bắt đầu trang
    $pageStart = ($pageCurrent - 1) * $pageRow;
    $pageStart = max(0, $pageStart); // đảm bảo không âm



    // Xử lý phần tìm kiếm
    $conditions = [];

    // Keyword
    if (!empty($keyword)) {
        $conditions[] = "monan.TenMonAn LIKE '%$keyword%'";
    }

    // Loại món ăn
    if (isset($_GET['maloai']) && $_GET['maloai'] != "") {
        $maloai = $_GET['maloai'];
        $conditions[] = "loaimonan.MaLoai = '$maloai'";
    }

    // Đơn giá
    if (isset($_GET['dongia']) && $_GET['dongia'] != "") {
        $dongia = (float)$_GET['dongia'];
        $conditions[] = "bienthemonan.DonGia >= $dongia";
    }

    // Gom điều kiện vào WHERE
    $where = "";
    if (!empty($conditions)) {
        $where = "WHERE loaimonan.MaLoai = monan.MaLoai 
                    AND kichthuoc.MaSize = bienthemonan.MaSize
                    AND monan.MaMonAn = bienthemonan.MaMonAn
                    AND " . implode(" AND ", $conditions);
    } else {
        $where = "WHERE loaimonan.MaLoai = monan.MaLoai 
                    AND kichthuoc.MaSize = bienthemonan.MaSize
                    AND monan.MaMonAn = bienthemonan.MaMonAn";
    }

    // Query cuối cùng
    $strSQL = "SELECT *
                FROM loaimonan, kichthuoc, monan, bienthemonan
                $where
                ORDER BY bienthemonan.MaBienThe ASC
                LIMIT $pageStart, $pageRow";

    $result = mysqli_query($conn, $strSQL);


    //Kiểm tra có tìm thấy keyword trong csdl không 
    if (mysqli_num_rows($result) == 0 && $keyword != "") {
        echo "<div id='message' class='noti'>" . "Không tìm thấy món ăn với từ khóa '$keyword'" . "</div>";
    }
    ?>


    <!-- Xử lý các nút -->
    <?php
    if (isset($_GET['btnnew'])) {
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        header("Location: insert.php?page=$page");
        exit();
    }
    ?>

    <?php
    function ChuanHoaMa($mma)
    {
        $kq = str_pad($mma, 3, '0', STR_PAD_LEFT);
        return $kq;
    }
    ?>



    <nav>
        <form class="search-bar" method="get">
            <input class="search" type="text" name="keyword" placeholder="Tìm kiếm món ăn..." value="<?php echo isset($keyword) ? $keyword : "" ?>">
            <input class="btnTim" name="btntim" type="submit" value="Tìm">

            <select name="maloai">
                <option value="">-- Tất cả --</option>
                <?php
                $sqlLoai = "SELECT MaLoai, TenLoai FROM loaimonan";
                $dsLoai = mysqli_query($conn, $sqlLoai);

                $selectedLoai = isset($_GET['maloai']) ? $_GET['maloai'] : "";

                while ($rowLoai = mysqli_fetch_assoc($dsLoai)) {
                    $sel = ($rowLoai['MaLoai'] == $selectedLoai) ? "selected" : "";
                    echo "<option value='" . $rowLoai['MaLoai'] . "' $sel>" . $rowLoai['TenLoai'] . "</option>";
                }
                ?>
            </select>

            <input class="donGia" name="dongia" type="text" placeholder="Giá từ... (vd: 60000)" value="<?php echo isset($_GET['dongia']) ? $_GET['dongia'] : "" ?>">
        </form>

    </nav>


    <form>
        <?php
        if (isset($_GET['deleted'])) {
            if ($_GET['deleted'] == 1)
                echo "<p id='message' class='success-message'>Xóa món ăn thành công!</p>";
            else
                echo "<p id='message' class='error-message'>Xóa món ăn thất bại. Vui lòng thử lại.</p>";
        }
        ?>

        <table>
            <tr>
                <td class="tdtrai" colspan="4">
                    <h2>DANH SÁCH CÁC MÓN ĂN</h2>
                </td>
                <td class="tdphai" colspan="4">
                    <input type="submit" class="btnNew" name="btnnew" value="Thêm Mới">
                </td>
            </tr>
            <tr class="tieude">
                <td>
                    Mã món ăn
                </td>
                <td>
                    Tên món
                </td>
                <td>
                    Hình
                </td>
                <td>
                    Loại
                </td>
                <td>
                    Size
                </td>
                <td>
                    Giá (VNĐ)
                </td>
                <td>
                    Mô tả
                </td>
                <td>
                    Chức năng
                </td>
            </tr>

            <?php

            if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_array($result)) {
                    echo "<tr class='noidung'>";
                    echo "<td>" . ChuanHoaMa($row['MaBienThe']) . "</td>";
                    echo "<td>" . $row['TenMonAn'] . "</td>";

                    $anh = "img/" . $row['HinhAnh'];
                    echo "<td>" . "<img src='$anh' width='50px' height='50px'>" . "</td>";

                    echo "<td>" . $row['TenLoai'] . "</td>";
                    echo "<td>" . $row['TenSize'] . "</td>";
                    echo "<td>" . number_format($row['DonGia'], 0, ".", ",") . "</td>";
                    echo "<td>" . $row['MoTa'] . "</td>";
                    echo "<td>";
                    echo "<a href='detail.php?mabienthe=" . $row['MaBienThe'] . "&page=" . $pageCurrent . "'>" . "<i class='bi bi-pencil-square'></i>" . "</a>";
                    echo "<a href='delete.php?mabienthe=" . $row['MaBienThe'] . "&page=" . $pageCurrent . "'>" . "<i class='bi bi-trash'></i>" . "</a>";
                    echo "</td>";


                    echo "</tr>";
                }
            }
            ?>
        </table>

        <!-- Các nút phân trang -->
        <div class="phantrang">
            <?php
            // Tạo chuỗi GET cho tất cả điều kiện tìm kiếm
            $params = [];
            if (!empty($keyword)) $params['keyword'] = $keyword;
            if (!empty($selectedLoai)) $params['maloai'] = $selectedLoai;
            if (!empty($_GET['dongia'])) $params['dongia'] = $_GET['dongia'];

            $queryString = http_build_query($params);

            if ($pageCurrent > 1) {
                echo "<a href='?$queryString&page=1'> &lt;&lt; </a>";
                $pre = $pageCurrent - 1;
                echo "<a href='?$queryString&page=$pre'> &lt; </a>";
            }

            // $start = max(1, $pageCurrent-5);
            // $end = min($pageMax, $pageCurrent+5);

            for ($i = 1; $i <= $pageMax; $i++) {
                if ($i == $pageCurrent)
                    echo "<b> [$i] </b>";
                else
                    echo "<a href='?$queryString&page=$i'> $i </a>";
            }

            if ($pageCurrent < $pageMax) {
                $next = $pageCurrent + 1;
                echo "<a href='?$queryString&page=$next'> &gt; </a>";
                echo "<a href='?$queryString&page=$pageMax'> &gt;&gt; </a>";
            }
            ?>
        </div>
    </form>
</body>

<script>
    // Ẩn thông báo sau 3 giây
    setTimeout(function() {
        var msg = document.getElementById('message');
        if (msg) {
            msg.style.display = 'none';
        }
    }, 3000);
</script>

</html>