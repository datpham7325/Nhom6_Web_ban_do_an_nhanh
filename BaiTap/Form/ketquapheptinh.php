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
    $so_a = $_POST["so_a"];
    $so_b = $_POST["so_b"];
    $pt = $_POST["pheptinh"];
    $kq = "";
    if (!is_numeric($so_a) || !is_numeric($so_b)) {
      echo "Dữ liệu nhập vào không hợp lệ!";
    } else if (!isset($pt)) {
      echo "Vui lòng chọn phép tính!";
    } else {
      switch ($pt) {
        case "Cộng":
          $kq = $so_a + $so_b;
          break;
        case "Trừ":
          $kq = $so_a - $so_b;
          break;
        case "Nhân":
          $kq = $so_a * $so_b;
          break;
        case "Chia":
          if ($so_b == 0) echo "Không thể chia cho 0";
          else $kq = $so_a / $so_b;
          break;
      }
    }
  }
  ?>
  <form action="./ketquapheptinh.php" method="post">
    <table>
      <tr>
        <td colspan="2" style="text-align: center; color:#96B1CA">
          PHÉP TÍNH TRÊN HAI SỐ
        </td>
      </tr>
      <tr>
        <td style="text-align: end; color:#A44B1E;">
          Chọn phép tính:
        </td>
        <td style="color:#FF0000;">
          <span><?php echo $pt ?></span>
        </td>
      </tr>
      <tr>
        <td style="color:#5B7CFF; text-align:end;">
          Số 1:
        </td>
        <td>
          <input type="text" readonly name="so1" value="<?php echo $so_a ?>">
        </td>
      </tr>
      <tr>
        <td style="color:#5B7CFF; text-align:end;">
          Số 2:
        </td>
        <td>
          <input type="text" readonly name="so2" value="<?php echo $so_b ?>">
        </td>
      </tr>
      <tr>
        <td style="color:#5B7CFF; text-align:end;">
          Kết quả:
        </td>
        <td>
          <input type="text" name="kq" readonly value="<?php echo $kq ?>">
        </td>
      </tr>
      <tr>
        <td colspan="2" style="text-align: center;">
            <a href="./pheptinh.php">Quay lại trang trước</a>
        </td>
      </tr>
    </table>
  </form>
</body>

</html>