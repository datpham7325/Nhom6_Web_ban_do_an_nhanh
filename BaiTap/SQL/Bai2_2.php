<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Thông tin khách hàng</title>
    <style>
    div{
      text-align: center;
      width: 60%;
    }
    table {
      /* border-collapse: collapse; */
      border-spacing: 2px;
      width: 60%;
      border: 2px solid #ddd;
    }

    td {
      text-align: left;
      padding: 8px;
      border: 1px solid #ddd;
    }

    tr:nth-child(even) {
      background-color: #FEE0C1;
    }
  </style>
</head>
<body>
  <div>
    <h1>THÔNG TIN KHÁCH HÀNG</h1>
  </div>
  <table>
    <tr>
      <td style="text-align:center; color:#BE3C0D;">
        Mã KH
      </td>
      <td style="text-align:center; color:#BE3C0D;">
        Tên khách hàng
      </td>
      <td style="text-align:center; color:#BE3C0D;">
        Giới tính
      </td>
      <td style="text-align:center; color:#BE3C0D;">
        Địa chỉ
      </td>
      <td style="text-align:center; color:#BE3C0D;">
        Số điện thoại
      </td>
    </tr>
    <?php
      require("myenv.php");
      $mysqli = new mysqli($db_host,$db_user,$db_password,$db_db,$db_port);
      // Kiểm tra kết nối db
      if($mysqli -> connect_error){
        echo "Errno: " . $mysqli -> connect_errno;
        echo "<br>";
        echo "Error: " . $mysqli -> connect_error;
        exit();
      }
      // else echo "Kết nối thành công";
      // echo "<br><hr>";
      // Chuẩn bị câu truy vấn
      $sql = "
        SELECT Ma_Khach_Hang, Ten_Khach_Hang, Phai, Dia_Chi, Dien_Thoai
        FROM KHACH_HANG
      ";
      // Thực hiện câu truy vấn
      $result = $mysqli -> query($sql);
      // Kiểm tra truy vấn
      if(!$result) die("Syntax error");
      // else echo "<br>Truy vấn thành công";
      // Xử lý kết quả trả về
      $num_rows = $result -> num_rows;
      while($rows = $result -> fetch_array()){
        echo "<tr>";
        for($i = 0; $i < $result -> field_count; $i++){
          if($rows[$i] == 0 || $rows[$i] == 1){
            echo "<td style=\"text-align:center;\">". $rows[$i] ."</td>";
          }else echo "<td >". $rows[$i] ."</td>";
        }
      }
      echo "</tr>";
      // Giải phóng bộ nhớ
      $result -> free();
      // Ngắt kết nối 
      $mysqli -> close();
    ?>
  </table>
</body>
</html>