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
      // Kiểm tra dữ liệu có hợp lệ không
      if (empty($stime) || empty($etime)) {
        echo "<p style='color:red;'>Vui lòng nhập đủ thời gian!</p>";
      } else {
        list($h1,$m1) = explode(':',$stime);
        list($h2,$m2) = explode(':',$etime);

        // Chuyển về dạng số giờ thập phân
        $start = $h1 + $m1 / 60;
        $end = $h2 + $m2 / 60;

        // Kiểm tra điều kiện hợp lệ
        if ($start < 10 || $end > 24 || $end <= $start) {
          echo "<p style='color:red;'>Thời gian hoạt động chỉ từ 10h đến 24h và giờ kết thúc phải lớn hơn giờ bắt đầu!</p>";
        } 
        else {
          if ($end <= 17) {
            $thanhtien = ($end - $start) * 20000;
          } else {
            if ($start < 17) {
              $thanhtien = (17 - $start) * 20000 + ($end - 17) * 45000;
            } else {
              $thanhtien = ($end - $start) * 45000;
            }
          }
        }
      }
    }
  ?>
  <form action="./TienKaraokeV2.php" method="post">
    <table style="background-color: #00FFFF;">
      <tr style="background-color:#6A00FF">
        <td colspan="2" style="text-align:center; color:white">
          TÍNH TIỀN KARAOKE
        </td>
      </tr>
      <tr>
        <td>
          Giờ bắt đầu:
        </td>
        <td>
          <input type="time" name="stime" value="<?php echo isset($stime) ? $stime : "" ?>">(h)
        </td>
      </tr>
      <tr>
        <td>
          Giờ kết thúc:
        </td>
        <td>
          <input type="time" name="etime" value="<?php echo isset($etime) ? $etime : "" ?>">(h)
        </td>
      </tr>
      <tr>
        <td>
          Tiền thanh toán:
        </td>
        <td>
          <input type="text" name="thanhtien" readonly style="background-color:#EEFF00" value="<?php echo isset($thanhtien) ? $thanhtien : "" ?>">(VNĐ)
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