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
      $tench = $_POST["tenchuho"];
      $cscu = $_POST["cscu"];
      $csmoi = $_POST["csmoi"];
      $dongia = $_POST["dongia"];
      if(!is_numeric($cscu) || !is_numeric($csmoi) || !is_numeric($dongia) || $cscu < 0 || $csmoi < 0 || $dongia < 0){
        echo "Chỉ số điện và đơn giá phải là số dương";
      }else{
        if($cscu > $csmoi){
          echo "Chỉ số cũ không được lớn hơn chỉ số mới";
        }else $thanhtien = ($csmoi - $cscu) * $dongia;
      }
    }
  ?>
  <form action="./Tiendien.php" method="post">
    <table style="background-color:#FFF4D4;">
      <tr style="background-color:#F8D965;">
        <td colspan="2" style="text-align: center; color:#B8812F; ">
          THANH TOÁN TIỀN ĐIỆN
        </td>
      </tr>
      <tr>
        <td>
          Tên chủ hộ:
        </td>
        <td>
          <input type="text" name="tenchuho" value="<?php echo isset($tench) ? $tench : "" ?>">
        </td>
      </tr>
      <tr>
        <td>
          Chỉ số cũ:
        </td>
        <td>
          <input type="text" name="cscu" value="<?php echo isset($cscu) ? $cscu : "" ?>">(Kw)
        </td>
      </tr>
      <tr>
        <td>
          Chỉ số mới:
        </td>
        <td>
          <input type="text" name="csmoi" value="<?php echo isset($csmoi) ? $csmoi : "" ?>">(Kw)
        </td>
      </tr>
      <tr>
        <td>
          Đơn giá:
        </td>
        <td>
          <input type="text" name="dongia" value="<?php echo isset($dongia) ? $dongia : "20000" ?>">(VNĐ)
        </td>
      </tr>
      <tr>
        <td>
          Số tiền thanh toán:
        </td>
        <td>
          <input type="text" readonly style="background-color:#F9D1D9;" name="tientt" value="<?php echo isset($thanhtien) ? $thanhtien : "" ?>">(VNĐ)
        </td>
      </tr>
      <tr style="text-align:center;">
        <td colspan="2">
            <input type="submit" value="Tính" name="btnsend">
        </td>
      </tr>
    </table>
  </form>
</body>
</html>