<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Document</title>
  <style>
    table {
      border-collapse: collapse;
      border-spacing: 2px;
      width: 45%;
      border: 2px solid #ddd;
      margin: auto;
    }

    td {
      text-align: left;
      padding: 8px;
      border: 1px solid #ddd;
      vertical-align: top;
    }

    img {
      width: 130px;
      height: auto;
    }

    .ten-sua {
      font-weight: bold;
      font-size: 25px;
    }

    .mo-ta {
      font-size: 18px;
      margin-top: 10px;
    }
  </style>
</head>
<body>
  <table>
    <tr style="background-color:#FFEEE6;">
      <td colspan="2" style="text-align:center; color:#FC6500; font-weight:bolder;font-size:25px;">
        <h2>THÔNG TIN CÁC SẢN PHẨM</h2>
      </td>
    </tr>
    <?php
      require("myenv.php");
      $mysqli = new mysqli($db_host,$db_user,$db_password,$db_db,$db_port);
      // Kiểm tra kết nối
      if($mysqli -> connect_error){
        echo "Errno: " . $mysqli -> connect_errno;
        echo "<br>";
        echo "Error: " . $mysqli -> connect_error;
        exit();
      }

      // Chuẩn bị truy vấn
      $sql = "
        SELECT a.Ten_Sua, b.Ten_Hang_Sua, c.Ten_Loai, a.Trong_Luong, a.Don_Gia,a.Hinh
        FROM sua a, hang_sua b, loai_sua c
        WHERE a.Ma_Hang_Sua = b.Ma_Hang_Sua AND a.Ma_Loai_Sua = c.Ma_Loai_Sua
      ";

      // Thực hiện truy vấn
      $result = $mysqli -> query($sql);

      // Kiểm tra truy vấn
      if(!$result) die("Syntax error");
      // else echo "Truy vấn thành công";

      // Xử lý kết quả hiển thị
      $num_rows = $result -> num_rows;
      while($rows = $result -> fetch_array())
      {
        echo "<tr>";
        echo "<td style = \"text-align:center;\"><img src=\"".$rows[4]."\" onerror=\"this.src='./san_pham/NutiIQ.jpg'\"  width=\"120\"></td>";
        echo "<td style = \"padding-top: 25px;\">
          <span class='ten-sua'>" . $rows[0] . "</span><br>
          <div class='mo-ta'>
            Nhà sản xuất: " . $rows[1] . "<br>" . $rows[2] . " - " . $rows[3] . " gr - " . $rows[4] . " VNĐ
          </div>
        </td>";
        echo "</tr>";
      }
      $mysqli -> close();
    ?>
  </table>
</body>
</html>