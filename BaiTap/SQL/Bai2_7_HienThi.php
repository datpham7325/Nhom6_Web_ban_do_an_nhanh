<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Chi tiết sản phẩm</title>
  <style>
    table {
      border: 5px solid #C6845B;
      width: 1000px;
      margin: auto;
    }

    tr,
    td {
      border: 2px solid gray;
      padding: 10px;
      vertical-align: top;
    }
  </style>
</head>

<body>
  <table>
    <?php
    include_once("./myenv.php");
    // Kết nối db
    $mysqli = new mysqli($db_host, $db_user, $db_password, $db_db, $db_port);
    // Kiểm tra kết nối
    if ($mysqli->connect_error) {
      echo "Errno: " . $mysqli->connect_errno;
      echo "<br>";
      echo "Error: " . $mysqli->connect_error;
      exit();
    }

    $maSua = $_GET["id"];

    // Chuẩn bị câu query (ghép đúng điều kiện nối 2 bảng)
    // Chuẩn bị câu query
    $sql = "
    SELECT a.Ten_sua, b.Ten_hang_sua, a.Hinh, a.TP_Dinh_Duong, a.Loi_ich, a.Trong_luong, a.Don_gia
    FROM sua a, hang_sua b
    WHERE a.Ma_sua = '$maSua' AND a.Ma_hang_sua = b.Ma_hang_sua
    ";

    // Thực thi query
    $result = $mysqli->query($sql);
    // Kiểm tra câu query
    if (!$result) die("Syntax error!");
    // Xử lý kết quả
    if ($rows = $result->fetch_array()) {
      echo "<tr style=\"background-color:#FFEEE6;\">
          <td colspan = \"2\" style=\"text-align:center;color:orange;\">
            <h1>" . $rows[0] . " - " . $rows[1] . "</h1>
          </td>
        </tr>
        <tr>
          <td style='width:40%; text-align:center;'>
            <img <img src=\"" . $rows[2] . "\" onerror=\"this.src='./san_pham/NutiIQ.jpg'\" style='max-width:100%;'>
          </td>
          <td>
            <h3>Thành phần dinh dưỡng:</h3>
            <p>" . $rows[3] . "</p>
            <h3>Lợi ích:</h3>
            <p>" . $rows[4] . "</p>
            <p style=\"text-align: end;\">
              <b>Trọng lượng: </b> " . $rows[5] . " gr - <b>Đơn giá: </b> " . number_format($rows[6], 0, ',', '.') . " VNĐ
            </p>
          </td>
        </tr>";
      echo "<tr>
          <td style = \"text-align: end\">
           <a href=\"./Bai2_7.php\">Quay về</a>
          </td>
          <td></td>
        </tr>";
    } else {
      echo "<tr><td colspan='2'>Không tìm thấy sản phẩm. <a href='./Bai2_7.php'>Quay về</a></td></tr>";
    }

    $mysqli->close();
    ?>
  </table>
</body>

</html>