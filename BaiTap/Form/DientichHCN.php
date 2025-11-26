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
      $cd = $_POST["cd"];
      $cr = $_POST["cr"];
      if(!is_numeric($cd) || !is_numeric($cr) || $cd < 0 || $cr < 0){
        echo "Chiều dài và chiều rộng của hình chữ nhật phải là số dương";
        $kq = "";
      }else{
        if($cd < $cr){
          echo "Chiều dài không được nhỏ hơn chiều rộng";
          $kq = "";
        }else $kq = $cd * $cr;
      }
    }
  ?>
  <form action="./DientichHCN.php" method="post">
    <table style="background-color: #FFFADA;">
      <tr style="background-color: #FBE376;">
        <td colspan="2" style="color: #A96C24; text-align: center;">
          DIỆN TÍCH HÌNH CHỮ NHẬT
        </td>
      </tr>
      <tr>
        <td>
          Chiều dài:
        </td>
        <td>
          <input type="text" name="cd" value="<?php echo isset($cd) ? $cd : "" ?>">
        </td>
      </tr>
      <tr>
        <td>
          Chiều rộng:
        </td>
        <td>
          <input type="text" name="cr" value="<?php echo isset($cr) ? $cr : "" ?>">
        </td>
      </tr>
      <tr>
        <td>
          Diện tích:
        </td>
        <td>
          <input style="background-color: #FFDCDC;" type="text" name="dt" readonly value="<?php echo isset($kq) ? $kq : "" ?>">
        </td>
      </tr>
      <tr>
        <td colspan="2" style="text-align: center;">
          <input type="submit" value="Tính" name="btnsend">
        </td>
      </tr>
    </table>
  </form>
</body>
</html>