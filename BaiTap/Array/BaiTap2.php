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
      $dayso = $_POST["dayso"];
      if(strpos($dayso,',') === false){
        echo "Các số được nhập phải cách nhau bằng dấu \",\"";
      }else{
        $arr = explode(',',$dayso);
        $arr = array_map('trim', $arr);
        $tong = 0;
        foreach($arr as $val){
          if(!is_numeric($val)){
            echo "Dữ liệu nhập vào không hợp lệ!";
            break;
          }else{
            $tong += $val;
          }
        }
      }

    }
  ?>
  <form action="./BaiTap2.php" method="post">
    <table style="background-color: #CCD9CF">
      <tr style="background-color:#299693; color:white;">
        <td colspan="2">
          NHẬP VÀ TÍNH TRÊN DÃY SỐ
        </td>
      </tr>
      <tr>
        <td>
          Nhập dãy số:
        </td>
        <td>
          <input type="text" name="dayso" value="<?php echo isset($dayso) ? $dayso : "" ?>" require>(*)
        </td>
      </tr>
      <tr style="text-align:center;">
        <td colspan="2">
          <input style="background-color: #FBF58F;" type="submit" value="Tổng dãy số" name="btnsend">
        </td>
      </tr>
      <tr>
        <td>
          Tổng dãy số:
        </td>
        <td>
          <input style="background-color: #C4FB97; color:red;" type="text" readonly name="$kq" value="<?php echo isset($tong) ? $tong : "" ?>">
        </td>
      </tr>
      <tr>
        <td colspan="2">
          <p>(*) Các số được nhập cách nhau bằng dấu ","</p>
        </td>
      </tr>
    </table>
  </form>
</body>
</html>