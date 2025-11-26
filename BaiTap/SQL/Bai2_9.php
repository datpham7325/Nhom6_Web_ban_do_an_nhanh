<!DOCTYPE html>
<html lang="vi">

<head>
  <meta charset="UTF-8">
  <title>Tìm kiếm thông tin sữa</title>
  <style>
    body {
      font-family: Arial;
    }

    table {
      margin: auto;
    }
  </style>
</head>

<body>

  <form method="GET">
    <table style="margin-top:20px; width: 750px">
      <tr style="background-color: #FFCACD;text-align:center;">
        <td colspan="2">
          <h1 style="color:#B22222;">TÌM KIẾM THÔNG TIN SỮA</h1>
        </td>
      </tr>
      <tr style="background-color: #FFCACD;text-align:center;">
        <td colspan="2">
          <span style="color: #812616;">Tên sữa:</span>
          <input type="text" name="keyword" size="30" value="<?= isset($_GET['keyword']) ? ($_GET['keyword']) : '' ?>">
          <input style="background-color: #FFCACD;" type="submit" value="Tìm kiếm">
        </td>
      </tr>
      <tr style="background-color: #FDFEF0;">
        <td colspan="2">
          <?php
          include_once("./myenv.php");

          $mysqli = new mysqli($db_host, $db_user, $db_password, $db_db, $db_port);
          if ($mysqli->connect_errno) {
            die("Lỗi kết nối: " . $mysqli->connect_error);
          }

          $keyword = isset($_GET["keyword"]) ? trim($_GET["keyword"]) : "";

          if ($keyword != "") {

            $key = "%" . $mysqli->real_escape_string($keyword) . "%";
            $sql = "SELECT sua.Ten_sua, sua.Trong_luong, sua.Don_gia, sua.Hinh,
                    sua.Loi_ich, sua.TP_Dinh_Duong, hang_sua.Ten_hang_sua
            FROM sua 
            INNER JOIN hang_sua ON sua.Ma_hang_sua = hang_sua.Ma_hang_sua
            WHERE sua.Ten_sua LIKE '$key'";

            $result = $mysqli->query($sql);
            $count = $result->num_rows;

            echo "<p style=\"text-align:center;\"><b>Có $count sản phẩm được tìm thấy</b></p>";
            
            if ($count > 0) {
              echo "<table style = \"width:650px; border:5px solid #812616;border-collapse:collapse; \">";
              while ($row = $result->fetch_array()) {

                echo "
                  <tr style = \"text-align:center;background-color:#FFEEE6; color:#DF8027; border:1px solid black;\">
                    <th style =\" border:1px solid black; \" colspan='2'>{$row['Ten_sua']} - {$row['Ten_hang_sua']}</th>
                  </tr>
                  <tr>
                    <td style =\" border:1px solid black; \">
                    <img style =\"width=120px; \" src='./san_pham/{$row['Hinh']}' 
                   onerror=\"this.src='./san_pham/NutiIQ.jpg'\"/>
                  </td>
                  <td style =\" border:1px solid black; \">
                    <b>Thành phần dinh dưỡng:</b><br>
                    {$row['TP_Dinh_Duong']}<br>
                    <b>Lợi ích:</b><br>
                    {$row['Loi_ich']}<br>
                    <b>Trọng lượng:</b> 
                    <span style=\"color:red;\">{$row['Trong_luong']} gr -</span>
                    <span style=\"color:red;\" class='note'>Đơn giá: " . number_format($row['Don_gia']) . " VNĐ</span>
                  </td>
                </tr>
                "; 
              }
              echo "</table>";
            } else {
              echo "<p class='center' style='color:red;'>Không tìm thấy sản phẩm này!</p>";
            }
          }
          $mysqli->close();
          ?>
        </td>
      </tr>
    </table>
  </form>
</body>

</html>