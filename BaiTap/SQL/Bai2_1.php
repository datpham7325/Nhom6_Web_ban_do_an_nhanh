<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Thông tin hãng sữa</title>
  <style>
    div{
      text-align: center;
      width: 60%;
    }
    table {
      border-collapse: collapse;
      border-spacing: 0;
      width: 60%;
      border: 2px solid #ddd;
    }

    td {
      text-align: left;
      padding: 8px;
    }

    tr:nth-child(even) {
      background-color: #f2f2f2
    }
  </style>
</head>

<body>
  <div>
    <h1 style="color:#2A7FAA;">
    THÔNG TIN HÃNG SỮA
  </h1>
  </div>
  <table>
    <tr>
      <td>
        Mã HS
      </td>
      <td>
        Tên hãng sữa
      </td>
      <td>
        Địa chỉ
      </td>
      <td>
        Điện thoại
      </td>
      <td>
        Email
      </td>
    </tr>
    <?php
    require("myenv.php");
    $mysqli = new mysqli($db_host, $db_user, $db_password, $db_db, $db_port);
    // Kiểm tra kết nối database
    if ($mysqli->connect_error) {
      echo 'Errno: ' . $mysqli->connect_errno;
      echo '<br>';
      echo 'Error: ' . $mysqli->connect_error;
      exit();
    } 
    // else echo "Kết nối thành công";
    // echo "<hr>";
    // Chuẩn bị câu truy vấn
    $sql = "
    SELECT Ma_Hang_Sua, Ten_Hang_Sua, Dia_Chi, Dien_Thoai, Email
    FROM hang_sua
  ";
    // Thực hiện câu truy vấn
    $result = $mysqli->query($sql);
    // Kiểm tra truy vấn
    if (!$result) die('syntax error');
    // else echo "<br>truy vấn thành công";
    // echo "<br><hr>";
    // Xử lý kết quả trả về
    $num_rows = $result->num_rows;
    while ($rows = $result->fetch_array()) {
      echo "<tr>";
      for ($i = 0; $i < $result->field_count; $i++) {
        echo "<td>" . $rows[$i] . "</td>";
      }
      echo "</tr>";
    }
    // Giải phóng bộ nhớ
    $result->free();
    // Đóng kết nối 
    $mysqli->close();
    ?>
  </table>
</body>

</html>