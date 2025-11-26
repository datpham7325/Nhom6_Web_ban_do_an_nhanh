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
      $fullname = $_POST["fullname"];
      $address = $_POST["address"];
      $phone = $_POST["phone"];
      $sex = $_POST["sex"];
      $country = $_POST["country"];
      $note = $_POST["note"];
      echo "Bạn đã nhập thành công, dưới đây là thông tin bạn đã nhập: </br>";
      echo "Họ tên: $fullname </br>";
      echo "Address: $address </br>";
      echo "Phone: $phone </br>";
      echo "Gender: $sex </br>";
      echo "Country: $country </br>";
      echo "Note: $note";
    }
  ?>
</body>
</html>