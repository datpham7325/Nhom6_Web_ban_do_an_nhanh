<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Document</title>
</head>
<body>
  <?php
    if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["btnsend"])){
      $str = $_POST["mangN"];
      $socantim = $_POST["socantim"];
      if(strpos($str,',') === false){
        echo "Các phần tử trong mảng phải cách nhau bằng dấu \",\"";
      }else{
        $arr = explode(",",$str);
        $arr = array_map('trim', $arr);
        if(is_null($socantim)){
          echo "Hãy nhập số cần tìm!";
        }else if(!is_numeric($socantim)){
          echo "Dữ liệu nhập vào không hợp lệ!";
        }
        $i = 1;
        $found = false;
        foreach($arr as $val){
          if($socantim == $val){
            $kq = "Tìm thấy $socantim tại vị trí thứ $i của mảng";
            $found = true;
          }
          $i++;
          if(!$found) $kq = "Không tìm thấy $socantim trong mảng";
        }
      }
    }
  ?>
  <form action="./BaiTap4.php" method="post">
    <table style="background-color: #D1DED4; width:500px">
      <tr style="background-color: #369799; color:#D1DED4; text-align:center">
        <td colspan="2">
          TÌM KIẾM
        </td>
      </tr>
      <tr>
        <td>
          Nhập mảng:
        </td>
        <td>
          <input style="width:290px" type="text" name="mangN" value="<?php echo isset($str) ? $str : "" ?>">
        </td>
      </tr>
      <tr>
        <td>
          Nhập số cần tìm:
        </td>
        <td>
          <input style="width:290px" type="text" name="socantim" value="<?php echo isset($socantim) ? $socantim : "" ?>">
        </td>
      </tr>
      <tr style="text-align:center">
        <td colspan="2">
          <input style="background-color: #90CDFF;text-align:center;" type="submit" value="Tìm kiếm" name="btnsend">
        </td>
      </tr>
      <tr>
        <td>
          Mảng:
        </td>
        <td>
          <input style="width:290px" type="text" name="mang" value="<?php echo isset($str) ? implode(", ",$arr) : "" ?>">
        </td>
      </tr>
      <tr>
        <td>
          Kết quả tìm kiếm:
        </td>
        <td>
          <input style="background-color: #CCFBFD; color: red ;width:290px;" type="text" name="kq" value="<?php echo isset($kq) ? $kq : "" ?>">
        </td>
      </tr>
      <tr>
        <td colspan="2" style="background-color: #369799;text-align:center;">
          (Các phần tử trong mảng sẽ cách nhau bằng dấu ",")
        </td>
      </tr>
    </table>
  </form>
</body>
</html>