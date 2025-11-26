<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Document</title>
  <style>
    table{
      border: 1px solid black;
      border-collapse: collapse;
      width: 90%;
      margin: auto;
    }
    tr,td{
      border: 1px solid black;
      text-align: center;
    }
  </style>
</head>
<body>
  <table>
    <tr style="background-color: #FFEDE7;">
      <td colspan="5" style="color: #EE6E0E;">
        <h2>THÔNG TIN CÁC SẢN PHẨM</h2>
      </td>
    </tr>
    <?php
      include_once("./myenv.php");
      // Thực hiện kết nối db
      $mysqli = new mysqli($db_host,$db_user,$db_password,$db_db,$db_port);
      // Kiểm tra kết nối
      if($mysqli -> connect_error){
        echo "Errno: " . $mysqli -> connect_errno;
        echo "<br>";
        echo "Error: " . $mysqli -> connect_error;
        exit();
      }
      // Chuẩn bị câu truy vấn
      $sql = "
        select Ma_sua, Ten_sua, Trong_luong, Don_gia, Hinh
        from sua
      ";
      // Thực hiện truy vấn
      $result = $mysqli -> query($sql);
      // Kiểm tra truy vấn
      if(!$result) die("Syntax error");
      // Xử lý hiển thị
      $count = 0;
      while($rows = $result -> fetch_array()){
        // mở <tr> nếu là ô đầu của hàng (mỗi hàng 5 ô)
        if($count % 5 == 0){
          echo "<tr>";
        }

        // In ô sản phẩm — lưu ý nối chuỗi đúng cách trong echo
        echo "<td>
                <h2><a href=\"./Bai2_7_HienThi.php?id=".$rows[0]."\">".$rows[1]."</a></h2>
                <p>".$rows[2]." gr - ".number_format($rows[3], 0, ',', '.')." VNĐ</p>
                <img src=\"".$rows[4]."\" onerror=\"this.src='./san_pham/NutiIQ.jpg'\"  width=\"120\"> 
              </td>";

        // đóng </tr> nếu đã đủ 5 ô
        if($count % 5 == 4){
          echo "</tr>";
        }

        $count++;
      }

      // Nếu vòng lặp kết thúc nhưng hàng hiện tại chưa đóng (ví dụ tổng sp không chia hết cho 5)
      if($count % 5 != 0){
        echo "</tr>";
      }

      $mysqli -> close();
    ?>
  </table>
</body>
</html>
