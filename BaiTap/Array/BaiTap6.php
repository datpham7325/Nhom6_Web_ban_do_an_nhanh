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
      $str = $_POST["mang"];
      if(strpos($str,',') === false){
        echo "Các số được nhập vào phải cách nhau bởi dấu \",\"";
      }else{
        $arr = explode(',',$str);
        $arr = array_map('trim', $arr);
        foreach($arr as $val){
          if(!is_numeric($val)) "Mảng nhập vào phải hoàn toàn là số";
        }
        $arrt = $arr;
        $arrg = $arr;
        sort($arrt);
        rsort($arrg);
      }
    }
  ?>
  <form action="./BaiTap6.php" method="post">
    <table style="background-color: #D1DED4;">
      <tr style="background-color:#309B99; color: #D0DFEA;">
        <td colspan="2">
          SẮP XẾP MẢNG
        </td>
      </tr>
      <tr>
        <td>
          Nhập mảng:
        </td>
        <td>
          <input type="text" name="mang" value="<?php echo isset($str) ? $str :"" ?>"> <span style="color: red;">(*)</span>
        </td>
      </tr>
      <tr style="background-color: #CBE2DF; text-align:center;">
        <td colspan="2">
          <input type="submit" value="Sắp xếp mảng tăng/giảm" name="btnsend">
        </td>
      </tr>
      <tr style="background-color: #CBE2DF;">
        <td colspan="2">
          <span style="color: red;">Sau khi sắp xếp:</span>
        </td>
      </tr>
      <tr style="background-color: #CBE2DF;">
        <td>
          Tăng dần:
        </td>
        <td>
          <input type="text" name="tangdan" readonly value="<?php echo isset($arrt) ? implode(", ",$arrt) :"" ?>">
        </td>
      </tr>
      <tr>
        <td>
          Giảm dần:
        </td>
        <td>
          <input type="text" name="giamdan" readonly value="<?php echo isset($arrg) ? implode(", ",$arrg) :"" ?>">
        </td>
      </tr>
      <tr>
        <td colspan="2">
          <span style="color:red;">(*)</span> Các số được nhập cách nhau bằng dấu ","
        </td>
      </tr>
    </table>
  </form>
</body>
</html>