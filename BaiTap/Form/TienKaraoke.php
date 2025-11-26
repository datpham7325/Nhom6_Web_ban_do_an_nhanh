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
      $stime = $_POST["stime"];
      $etime = $_POST["etime"];
      if(!is_numeric($stime) || !is_numeric($etime) || $stime < 10 || $etime > 24){
        echo "Thời gian bắt đầu >= 10 và Thời gian kết thúc <= 24";
      }else{
        if($stime >= $etime) echo "Giờ kết thúc phải > Giờ bắt đầu";
        else{
          if($etime < 17){
            $thanhtien = ($etime - $stime) * 20000;
          }else if($stime >= 17){
            $thanhtien = ($etime - $stime) * 45000;
          }else $thanhtien = (17 - $stime) * 20000 + ($etime - 17) * 45000;
        }
      }
    }
  ?>
  <form action="./TienKaraoke.php" method="post">
    <table style="background-color: #03B0B6;">
      <tr style="background-color:#038A8E">
        <td colspan="2" style="text-align:center; color:white">
          TÍNH TIỀN KARAOKE
        </td>
      </tr>
      <tr>
        <td>
          Giờ bắt đầu:
        </td>
        <td>
          <input type="text" name="stime" value="<?php echo isset($stime) ? $stime : "" ?>">(h)
        </td>
      </tr>
      <tr>
        <td>
          Giờ kết thúc:
        </td>
        <td>
          <input type="text" name="etime" value="<?php echo isset($etime) ? $etime : "" ?>">(h)
        </td>
      </tr>
      <tr>
        <td>
          Tiền thanh toán:
        </td>
        <td>
          <input type="text" name="thanhtien" readonly style="background-color:#FBFEA8" value="<?php echo isset($thanhtien) ? $thanhtien : "" ?>">(VNĐ)
        </td>
      </tr>
      <tr>
        <td colspan="2" style="text-align: center;">
          <input type="submit" name="btnsend" value="Tính tiền" >
        </td>
      </tr>
    </table>
  </form>
</body>
</html>