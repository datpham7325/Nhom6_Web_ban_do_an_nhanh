<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Document</title>

  <style>
    div {
      text-align: center;
      width: 60%;
      margin: auto;
    }

    table {
      /* border-collapse: collapse; */
      border-spacing: 2px;
      width: 60%;
      border: 2px solid #ddd;
      margin: auto;
    }

    td {
      text-align: left;
      padding: 8px;
      border: 1px solid #ddd;
    }

    tr:nth-child(odd) {
      background-color: #FEE0C1;
    }

    tr:first-child td {
      color: #CE4A3B;
      font-weight: bolder;
      text-align: center;
    }

    tr:nth-child(even) {
      color: red;
    }
  </style>
</head>

<body>
  <div>
    <h1 style="color: #AA1F00;">
      THÔNG TIN SỮA
    </h1>
  </div>
  <table>
    <tr>
      <td>Số TT</td>
      <td>Tên sữa</td>
      <td>Hãng sữa</td>
      <td>Loại sữa</td>
      <td>Trọng lượng</td>
      <td>Đơn giá</td>
    </tr>
    <?php
    require("myenv.php");
    require("Pager.php");
    $mysqli = new mysqli($db_host, $db_user, $db_password, $db_db, $db_port);
    // Kiểm tra kết nối db
    if ($mysqli->connect_error) {
      echo "Errno: " . $mysqli->connect_errno;
      echo "<br>";
      echo "Error: " . $mysqli->connect_error;
      exit();
    }
    // else echo "Kết nối thành công";
    // echo "<br><hr>";

    // Đếm tổng số dòng dữ liệu để phân trang
    $count_sql = "SELECT COUNT(*) FROM sua";
    $count_result = $mysqli->query($count_sql);
    $total_rows = $count_result->fetch_row()[0];

    // Khởi tạo phân trang
    $pager = new Pager($total_rows, 2); // mỗi trang có 2 dòng dữ liệu

    // Chuẩn bị câu truy vấn có phân trang
    $sql = "
        SELECT a.Ten_Sua, b.Ten_Hang_Sua, c.Ten_Loai, a.Trong_Luong, a.Don_Gia
        FROM sua a, hang_sua b, loai_sua c
        WHERE a.Ma_Hang_Sua = b.Ma_Hang_Sua AND a.Ma_Loai_Sua = c.Ma_Loai_Sua
        LIMIT {$pager->start}, {$pager->limit}
      ";
    // Thực hiện truy vấn
    $result = $mysqli->query($sql);
    // Kiểm tra truy vấn
    if (!$result) die("Syntax error");
    // else echo "<br>Truy vấn thành công";

    // Xử lý kết quả trả về
    $stt = $pager->start + 1; // số thứ tự bắt đầu từ vị trí phân trang
    while ($rows = $result->fetch_array()) {
      echo "<tr>";
      echo "<td>" . $stt . "</td>";
      for ($i = 0; $i < $result->field_count; $i++) {
        // Định dạng đơn giá theo kiểu tiền tệ
        if ($i == 4) {
          echo "<td>" . number_format($rows[$i], 0, ',', '.') . " VNĐ</td>";
        } else {
          echo "<td>" . $rows[$i] . "</td>";
        }
      }
      echo "</tr>";
      $stt++;
    }

    // Hiển thị danh sách trang theo dạng số
    echo "<tr><td colspan='6'>" . $pager->createLinks() . "</td></tr>";

    // Giải phóng bộ nhớ
    $result->free();
    // Đóng kết nối
    $mysqli->close();
    ?>
  </table>
</body>

</html>