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
      $n = $_POST["soN"];
      $kq = "";
      if(!is_numeric($n)){
        $kq .= "Dữ liệu nhập vào không hợp lệ!";
      }
      $arr = array();
      if($n > 0){
        $so_c = 0;
        $so_n = 0;
        $tong = 0;
        for($i = 0; $i < $n; $i++){
          $rand = rand(-100000000,100000000);
          $kq .= $rand . " | ";
          array_push($arr,$rand);
          if($rand % 2 == 0) $so_c ++;
          if($rand < 100) $so_n ++;
          if($rand < 0) $tong += $rand;
        }
        $kq .= "<br>Số phần tử chẳn trong mảng là: $so_c";
        $kq .= "<br>Số lượng phần tử nhỏ hơn 100 là $so_n";
        $kq .= "<br>Tổng các phần tử có giá trị âm là: $tong";
        $i = 1;
        $kq .= "<br>Vị trí các phần tử trong mảng có giá trị bằng 0 là: ";
        foreach($arr as $val){
          if($val == 0) $kq .= $i . ", ";
        }
        sort($arr);
        $kq .= "<br>Mảng sau khi sắp xếp tăng dần là: ";
        foreach($arr as $val){
          $kq .= $val. " ";
        }
      }else{
        $kq .= "Số nhập vào phải là số dương";
      }
    }
  ?>
  <form action="./BaiTap1.php" method="post">
    <table>
      <tr>
        <td>
          Nhập n:
        </td>
        <td>
          <input type="text" name="soN">
        </td>
      </tr>
      <tr style="text-align:center;">
        <td colspan="2">
          <input type="submit" value="Thực hiện" name="btnsend">
        </td>
      </tr>
    </table>
    <p> <?php echo $kq ?> </p>
  </form>
</body>
</html>