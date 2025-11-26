<!DOCTYPE html>
<html lang="vi">

<head>
  <meta charset="UTF-8">
  <title>THÔNG TIN CHI TIẾT CÁC LOẠI SỮA</title>
  <style>
    table {
      border-collapse: collapse;
      width: 700px;
      margin: auto;
      font-family: 'Times New Roman', serif;
    }

    td {
      border: 1px solid #ccc;
      padding: 10px;
      vertical-align: top;
    }

    .header-title {
      text-align: center;
      padding: 10px;
      color: #ff3366;
      font-size: 26px;
      font-weight: bold;
    }

    .milk-name {
      background-color: #FFEDE2;
      text-align: center;
      color: #FF6600;
      font-size: 20px;
      font-style: italic;
      font-weight: bold;
    }

    img {
      width: 150px;
      height: auto;
    }

    .pagination {
      text-align: center;
      padding: 10px;
    }
  </style>
</head>

<body>
  <?php
  include_once("./myenv.php");
  include_once("./Pager.php");

  // Kết nối database
  $mysqli = new mysqli($db_host, $db_user, $db_password, $db_db, $db_port);
  if ($mysqli->connect_error) {
    die("Kết nối thất bại: " . $mysqli->connect_error);
  }

  // Đếm tổng sản phẩm
  $sqlCount = "SELECT COUNT(*) AS total FROM sua";
  $resultCount = $mysqli->query($sqlCount);
  $total = $resultCount->fetch_assoc()['total'];

  // Gọi phân trang: 2 sản phẩm mỗi trang
  $pager = new Pager($total, 2);

  // Truy vấn dữ liệu JOIN bảng hãng sữa
  $sql = "
    SELECT s.Ma_sua, s.Ten_sua, h.Ten_hang_sua, s.Trong_luong, s.Don_gia, 
           s.TP_Dinh_Duong, s.Loi_ich, s.Hinh
    FROM sua s JOIN hang_sua h ON s.Ma_hang_sua = h.Ma_hang_sua
    LIMIT {$pager->start}, {$pager->limit}
  ";
  $result = $mysqli->query($sql);
  ?>

  <table>
    <tr>
      <td colspan="2" class="header-title">THÔNG TIN CHI TIẾT CÁC LOẠI SỮA</td>
    </tr>

    <?php while ($row = $result->fetch_assoc()) { ?>
      <tr>
        <td colspan="2" class="milk-name">
          <?= $row['Ten_sua'] ?> - <?= $row['Ten_hang_sua'] ?>
        </td>
      </tr>

      <tr>
        <td align="center">
          <img src="./san_pham/<?= $row['Hinh'] ?>"
               onerror="this.src='./san_pham/NutiIQ.jpg'">
        </td>

        <td>
          <b>Thành phần dinh dưỡng:</b><br>
          <?= $row['TP_Dinh_Duong'] ?><br>

          <b>Lợi ích:</b><br>
          <?= $row['Loi_ich'] ?><br>

          <b>Trọng lượng:</b> <span style="color: red;"><?= $row['Trong_luong'] ?> gr</span> -
          <b>Đơn giá:</b>
          <span style="color: red;"><?= number_format($row['Don_gia'], 0, ',', '.') ?> VNĐ</span>
        </td>
      </tr>
    <?php } ?>

    <tr>
      <td style="border: none;" colspan="2" class="pagination">
        <?= $pager->createLinks() ?>
      </td>
    </tr>
  </table>

</body>
</html>
