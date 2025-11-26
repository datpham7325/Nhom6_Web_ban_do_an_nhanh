<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Document</title>
</head>
<body>
  <form action="./ketquapheptinh.php" method="post">
    <table>
      <tr>
        <td colspan="2" style="text-align: center; color:#96B1CA">
          PHÉP TÍNH TRÊN HAI SỐ
        </td>
      </tr>
      <tr>
        <td style="text-align:end; color:#A44B1E;">
          Chọn phép tính:
        </td>
        <td>
          <input type="radio" name="pheptinh" value="Cộng">Cộng
          <input type="radio" name="pheptinh" value="Trừ">Trừ
          <input type="radio" name="pheptinh" value="Nhân">Nhân
          <input type="radio" name="pheptinh" value="Chia">Chia
        </td>
      </tr>
      <tr>
        <td style="color:#5B7CFF; text-align:end;">
          Số thứ nhất:
        </td>
        <td>
          <input type="text" name="so_a">
        </td>
      </tr>
      <tr>
        <td style="color:#5B7CFF; text-align:end;">
          Số thứ nhì:
        </td>
        <td>
          <input type="text" name="so_b">
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