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
      define('PI',3.14);
      $bk = $_POST["bk"];
      if(!is_numeric($bk) || $bk <= 0){
        echo "Bán kính phải là số dương";
        $cv = "";
        $dt = "";
      }else{
        $cv = 2 * PI * $bk;
        $dt = PI * pow($bk,2);
      }  
    }
  ?>
  <form action="./Chuvi&DientichHT.php" method="post">
    <table style="background-color: #FFFADA;">
      <tr style="background-color: #FBE376;">
        <td colspan="2" style="color: #A96C24; text-align: center;">
          DIỆN TÍCH HÌNH CHỮ NHẬT
        </td>
      </tr>
      <tr>
        <td>
          Bán kính:
        </td>
        <td>
          <input type="text" name="bk" value="<?php echo isset($bk) ? $bk : "" ?>">
        </td>
      </tr>
      <tr>
        <td>
          Chu vi:
        </td>
        <td>
          <input type="text" style="background-color: #FFDCDC;" name="cv" readonly value="<?php echo isset($cv) ? $cv : "" ?>">
        </td>
      </tr>
      <tr>
        <td>
          Diện tích:
        </td>
        <td>
          <input style="background-color: #FFDCDC;" type="text" name="dt" readonly value="<?php echo isset($dt) ? $dt : "" ?>">
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