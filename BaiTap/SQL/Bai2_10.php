<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Document</title>
</head>

<body>
  <form action="" method="post">
    <table>
      <tr>
        <td colspan="2">
          TÌM KIẾM THÔNG TIN SỮA
        </td>
      </tr>
      <tr>
        <td colspan="2">
          <span>Loại sửa:</span>
          <select name="loaiSua">
            <?php
            include_once("./myenv.php");
            $mysqli = new mysqli($db_host, $db_user, $db_password, $db_db, $db_port);
            if ($mysqli->connect_error) {
              echo "Errno: " . $mysqli->connect_errno;
              echo "<br>";
              echo "Error: " . $mysqli->connect_error;
              exit();
            }
            $sql = "
                  select Ten_loai
                  from loai_sua
                ";
            $result = $mysqli->query($sql);
            if (!$result) die("Syntax error!");
            while ($rows = $result->fetch_array()) {
              for ($i = 0; $i < $result->field_count; $i++) {
                echo "<option value=\"$rows[$i]\">" . $rows[$i] . "</option>";
              }
            }
            ?>
          </select>
          <span>Hãng sữa:</span>
          <select name="hangSua">
            <?php
            include_once("./myenv.php");
            $mysqli = new mysqli($db_host, $db_user, $db_password, $db_db, $db_port);
            if ($mysqli->connect_error) {
              echo "Errno: " . $mysqli->connect_errno;
              echo "<br>";
              echo "Error: " . $mysqli->connect_error;
              exit();
            }
            $sql = "
                  select Ten_hang_sua
                  from hang_sua
                ";
            $result = $mysqli->query($sql);
            if (!$result) die("Syntax error!");
            while ($rows = $result->fetch_array()) {
              for ($i = 0; $i < $result->field_count; $i++) {
                echo "<option value=\"$rows[$i]\">" . $rows[$i] . "</option>";
              }
            }
            ?>
          </select>
        </td>
      </tr>
      <tr>
        <td colspan="2">
          <span>Tên sữa:</span>
          <input type="text" name="tenSua">
          <input type="submit" value="Tìm kiếm" name="btnsend">
        </td>
      </tr>
      <tr>
        <td colspan="2">
            <?php
              if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["btnsend"])){
                $tenSua = $_POST["tenSua"];
                include_once("./myenv.php");
                $mysqli = new mysqli($db_host,$db_user,$db_password,$db_db,$db_port);
                if($mysqli -> connect_error){
                  echo "Kết nối lỗi: " . $mysqli -> connect_error;
                  exit(); 
                }
                $sql = "
                  select a.Ten_sua, b.Ten_hang_sua, a.Hinh, a.TP_Dinh_Duong, a.Loi_ich, a.Trong_luong, a.Don_gia
                  from sua a, hang_sua b
                  where Ten_sua like \"%$tenSua%\"
                ";
                $result = $mysqli -> query($sql);
                if(!$result) die("Syntax error!");
                $num_rows = $result -> num_rows;
                if($num_rows == 0){
                  echo "<p style=\"text-align:center\">Không tìm thấy sản phẩm này</p>";
                }else{
                  while($rows = $result -> fetch_array()){
                    echo "<table>";
                    for($i = 0; $i < $result -> field_count; $i++){
                      echo "<tr>
                        <td colspan = \"2\" style =\"text-align:center;color:orange\">
                          ".$rows['Ten_sua']." - ". $rows['Ten_hang_sua'] ."
                        </td>
                      </tr>
                      <tr>
                        <td>
                          <img style =\"width=120px; \" src='./san_pham/{$row['Hinh']}' 
                          onerror=\"this.src='./san_pham/NutiIQ.jpg'\"/>
                        </td>
                        <td>
                          <span></span>
                        </td>
                      </tr>
                      ";
                    }
                    echo "</table>";
                  }
                  
                }
              }
            ?>
        </td>
      </tr>
    </table>
  </form>
</body>

</html>