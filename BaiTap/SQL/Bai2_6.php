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
      width: 100%;
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
      
      if($mysqli -> connect_error){
        echo "Errno: " . $mysqli -> connect_errno . "<br>";
        echo "Error: " . $mysqli -> connect_error;
        exit();
      }

      // Truy vấn dữ liệu sữa
      $sql = "SELECT Ten_sua, Trong_luong, Don_gia, Hinh FROM sua";
      $result = $mysqli -> query($sql);
      if(!$result) die("Syntax error");

      $count = 0; // Biến đếm sản phẩm

      // In dữ liệu
      while($rows = $result -> fetch_array()){
        
        // Nếu là sản phẩm đầu dòng thì mở <tr>
        if($count % 5 == 0){
          echo "<tr>";
        }
        echo "<td>
                <h2>$rows[0]</h2>
                <p>{$rows['Trong_luong']} gr - " . number_format($rows[2], 0, ',', '.') . " VNĐ</p>
                <img src=\"".$rows[3]."\" onerror=\"this.src='./san_pham/NutiIQ.jpg'\"  width=\"100px\">
              </td>";

        // Nếu đủ 5 sản phẩm thì đóng dòng
        if($count % 5 == 4){
          echo "</tr>";
        }

        $count++;
      }

      $mysqli -> close();
    ?>
  </table>
</body>
</html>
