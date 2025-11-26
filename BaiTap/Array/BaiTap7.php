<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Document</title>
</head>

<body>
  <?php
  if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["btnsend"])) {
    $arr_can = array("Quý", "Giáp", "Ất", "Bính", "Đinh", "Mậu", "Kỷ", "Canh", "Tân", "Nhâm");
    $arr_chi = array("Hợi", "Tý", "Sửu", "Dần", "Mão", "Thìn", "Tỵ", "Ngọ", "Mùi", "Thân", "Dậu", "Tuất");
    $arr_img = array("Hoi.png", "Ty.png", "Suu.png", "Dan.png", "Mao.png", "Thin.png", "Tyj.png", "Ngo.png", "Mui.png", "Than.png", "Dau.png", "Tuat.png");
    $namDL = $_POST["namDL"];
    if ($namDL < 0 || !is_numeric($namDL)) {
      echo "Năm nhập vào phải là số dương!";
    }
    $namDL = $namDL - 3;
    $can = $namDL % 10;
    $chi = $namDL % 12;
    $nam_al = $arr_can[$can] . " " . $arr_chi[$chi];
    $hinh = $arr_img[$chi];
    $hinhanh = "<img src= \"12con_giap/$hinh\">";
  }
  ?>
  <form action="./BaiTap7.php" method="post">
    <table style="background-color: #B9EEFF;">
      <tr style="background-color:#2161A5; color:white;">
        <td colspan="3">
          TÍNH NĂM ÂM LỊCH
        </td>
      </tr>
      <tr>
        <td colspan="2">
          Năm dương lịch
        </td>
        <td>
          Năm âm lịch
        </td>
      </tr>
      <tr>
        <td>
          <input type="text" name="namDL" value="<?php echo isset($namDL) ? $namDL + 3 : " " ?>">
        </td>
        <td>
          <input style="background-color:#FEFDDA; color: red; margin:0 30px 0 30px;" type="submit" name="btnsend" value="=>">
        </td>
        <td>
          <input style="background-color:#FEFDDA; color: red;" type="text" name="namAL" value="<?php echo isset($nam_al) ? $nam_al : "" ?>">
        </td>
      </tr>
      <tr style="text-align:center;">
        <td colspan="3">
          <p><?php echo isset($hinhanh) ? $hinhanh : "" ?></p>
        </td>
      </tr>
    </table>
  </form>
  
</body>

</html>