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
      $toan = $_POST["toan"];
      $ly = $_POST["ly"];
      $hoa = $_POST["hoa"];
      $diemchuan = $_POST["diemchuan"];
      if( !is_numeric($toan) || !is_numeric($ly) || !is_numeric($hoa) || !is_numeric($diemchuan) || !($toan >= 0 && $toan <= 10) || !($ly >= 0 && $ly <= 10) || !($hoa >= 0 && $hoa <= 10) || $diemchuan < 0){
        echo "Điểm các môn học phải là số lớn hơn hoặc bằng 0 và nhỏ hơn hoặc bằng 10";
        echo "</br> Điểm chuẩn phải là số lớn hơn hoặc bằng 0";
      }else{
        $tongdiem = $toan + $ly + $hoa;
        if($toan > 0 && $ly > 0 && $hoa > 0 && $tongdiem >= $diemchuan){
          $kq = "Đậu";
        }else $kq = "Rớt";
      }
    }
  ?>
  <form action="./Ketquathi.php" method="post">
    <table style="background-color:#FFE8FA;">
      <tr style="background-color: #E14E80;">
        <td colspan="2" style="color: white; text-align:center">
          KẾT QUẢ THI ĐẠI HỌC
        </td>
      </tr>
      <tr>
        <td>
          Toán:
        </td>
        <td>
          <input type="text" name="toan" value="<?php echo isset($toan) ? $toan : "" ?>">
        </td>
      </tr>
      <tr>
        <td>
          Lý:
        </td>
        <td>
          <input type="text" name="ly" value="<?php echo isset($ly) ? $ly : "" ?>">
        </td>
      </tr>
      <tr>
        <td>
          Hóa:
        </td>
        <td>
          <input type="text" name="hoa" value="<?php echo isset($hoa) ? $hoa : "" ?>">
        </td>
      </tr>
      <tr>
        <td>
          Điểm chuẩn:
        </td>
        <td>
          <input style="color: red;" type="text" name="diemchuan" value="<?php echo isset($diemchuan) ? $diemchuan : "" ?>">
        </td>
      </tr>
      <tr>
        <td>
          Tổng điểm:
        </td>
        <td>
          <input style="background-color: #FFFEE8;" type="text" name="tongdiem" readonly value="<?php echo isset($tongdiem) ? $tongdiem : "" ?>">
        </td>
      </tr>
      <tr>
        <td>
          Kết quả thi:
        </td>
        <td>
          <input style="background-color: #FFFEE8;" type="text" name="ketqua" readonly value="<?php echo isset($kq) ? $kq : "" ?>">
        </td>
      </tr>
      <tr style="text-align: center;">
        <td colspan="2">
          <input type="submit" value="Xem kết quả" name="btnsend">
        </td>
      </tr>
    </table>
  </form>
</body>
</html>