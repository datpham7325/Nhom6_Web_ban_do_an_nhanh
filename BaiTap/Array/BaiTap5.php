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
      $mangN = $_POST["mangN"];
      $gtcanthaythe = $_POST["gtcanthaythe"];
      $gtthaythe = $_POST["gtthaythe"];

      if(strpos($mangN,',') === false){
        echo "Các phần tử trong mảng phải cách nhau bằng dấu \",\"";
      } else {
        $arrC = explode(',', $mangN);
        $arrC = array_map('trim', $arrC); // loại bỏ các khoảng trắng trong mảng
        $arrM = $arrC; // sao chép mảng cũ sang mảng mới

        $found = false;
        foreach($arrM as $key => $val){
          if(trim($val) == $gtcanthaythe){
            $arrM[$key] = $gtthaythe; // thay thế
            $found = true;
          }
        }

        if(!$found){
          $kq = "Không có giá trị cần thay thế trong mảng cũ";
        } else {
          $kq = implode(',', $arrM);
        }
      }
    }
  ?>
  <form action="./BaiTap5.php" method="post">
    <table style="background-color: #FFF5FF; width: 450px;">
      <tr style="background-color: #9F0B6E; color:white; text-align:center">
        <td colspan="2">THAY THẾ</td>
      </tr>
      <tr style="background-color: #FDD5F1;">
        <td>Nhập các phần tử:</td>
        <td><input style="width:250px" type="text" name="mangN" value="<?php echo isset($mangN) ? $mangN : "" ?>"></td>
      </tr>
      <tr style="background-color: #FDD5F1;">
        <td>Giá trị cần thay thế:</td>
        <td><input type="text" name="gtcanthaythe" value="<?php echo isset($gtcanthaythe) ? $gtcanthaythe : "" ?>"></td>
      </tr>
      <tr style="background-color: #FDD5F1;">
        <td>Giá trị thay thế:</td>
        <td><input type="text" name="gtthaythe" value="<?php echo isset($gtthaythe) ? $gtthaythe : "" ?>"></td>
      </tr>
      <tr style="background-color: #FDD5F1;">
        <td></td>
        <td><input style="background-color: #F7FAA2;" type="submit" value="Thay thế" name="btnsend"></td>
      </tr>
      <tr>
        <td>Mảng cũ:</td>
        <td><input style="background-color:#FDA4A2; width:250px;" type="text" readonly value="<?php echo isset($arrC) ? implode(',', $arrC) : "" ?>"></td>
      </tr>
      <tr>
        <td>Mảng sau khi thay thế:</td>
        <td><input style="background-color:#FDA4A2; width:250px;" type="text" readonly value="<?php echo isset($kq) ? $kq : "" ?>"></td>
      </tr>
      <tr>
        <td colspan="2">( <span style="color: red;">Ghi chú:</span> Các phần tử trong mảng sẽ cách nhau bằng dấu "," )</td>
      </tr>
    </table>
  </form>
</body>
</html>
