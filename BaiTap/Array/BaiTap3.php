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
      $so_N = $_POST["so_N"];
      $arr = [];
      if(!is_numeric($so_N) || $so_N < 0){
        echo "Số phần tử phải là số dương";
      }else{
        for($i = 0; $i < $so_N; $i ++){
          $ran = rand(0,20);
          array_push($arr,$ran);
        }
        $max = $arr[0];
        $min = $arr[0];
        $tong = 0;
        foreach($arr as $val){
          if($max < $val) $max = $val;
          if($min > $val) $min = $val;
          $tong += $val;
        }
      }
    }
  ?>
  <form action="./BaiTap3.php" method="post">
    <table style="background-color: #FFFBFF; border: 1px solid black;">
      <tr style="background-color:#AB0C72; color: white;">
        <td colspan="2">
          PHÁT SINH MẢNG VÀ TÍNH TOÁN
        </td>
      </tr>
      <tr style="background-color: #FEDAF4;">
        <td>
          Nhập số phần tử:
        </td>
        <td>
          <input type="text" name="so_N">
        </td>
      </tr>
      <tr style="background-color: #FEDAF4; text-align:center">
        <td colspan="2">
          <input style="background-color:#FEFDA8;" type="submit" value="Phát sinh và tính toán" name="btnsend">
        </td>
      </tr>
      <tr>
        <td>
          Mảng:
        </td>
        <td>
          <input style="background-color: #FCA9A5;" readonly type="text" name="mang" value="<?php echo !empty($arr) ? implode(' ', $arr) : ''; ?>">
        </td>
      </tr>
      <tr>
        <td>
          GTLN (MAX) trong mảng:
        </td>
        <td>
          <input style="background-color: #FCA9A5;" readonly type="text" name="max" value="<?php echo isset($max) ? $max : "" ?>">
        </td>
      </tr>
      <tr>
        <td>
          GTNN (MIN) trong mảng:
        </td>
        <td>
          <input style="background-color: #FCA9A5;" readonly type="text" name="min" value="<?php echo isset($min) ? $min : "" ?>">
        </td>
      </tr>
      <tr>
        <td>
          Tổng mảng:
        </td>
        <td>
          <input style="background-color: #FCA9A5;" readonly type="text" name="tong" value="<?php echo isset($tong) ? $tong : "" ?>">
        </td>
      </tr>
      <tr>
        <td colspan="2">
          <p>(<span style="color: red;">Ghi chú:</span>: Các phần tử trong mảng có giá trị từ 0 đến 20)</p>
        </td>
      </tr>
    </table>
  </form>
</body>
</html>