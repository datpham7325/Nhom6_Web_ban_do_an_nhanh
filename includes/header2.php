<?php
session_start();
include_once("includes/myenv.php");
?>

<!DOCTYPE html>
<html lang="vi">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
  <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;700;900&display=swap" rel="stylesheet">
  
  <link rel="stylesheet" href="css/header2.css">
</head>

<body>

  <header class="header-wrapper">
    <div class="header-user">
      <?php if (isset($_SESSION['loggedin'])): ?>
        
        <div class="user-auth" id="userAuthDropdown">
          <div class="dropdown-toggle">
            <i class="fas fa-user-circle" style="font-size: 1.5rem; color: #d32f2f;"></i>
            <span><?php echo htmlspecialchars($_SESSION['HoTen'] ?? 'Admin'); ?></span>
            <i class="fas fa-caret-down" style="font-size: 0.8rem; color: #999;"></i>
          </div>

          <div class="dropdown-menu">
            <a href="home.php"><i class="fas fa-utensils"></i> Quản lý món ăn</a>
            <a href="duyet_don.php"><i class="fas fa-check-circle"></i> Duyệt đơn hàng</a>
            <a href="DangNhap.php?logout=true" onclick="return confirm('Bạn có chắc muốn đăng xuất?');">
              <i class="fas fa-sign-out-alt"></i> Đăng xuất
            </a>
          </div>
        </div>

      <?php else: ?>
        <a href="DangNhap.php" style="text-decoration: none; color: #d32f2f; font-weight: bold; padding: 5px 10px;">
          <i class="fas fa-sign-in-alt"></i> ĐĂNG NHẬP
        </a>
      <?php endif; ?>
    </div>
  </header>

  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const authContainer = document.getElementById('userAuthDropdown');
      
      if (authContainer) {
        authContainer.addEventListener('click', function(e) {
          this.classList.toggle('active');
          e.stopPropagation();
        });

        document.addEventListener('click', function() {
          authContainer.classList.remove('active');
        });
      }
    });
  </script>

</body>
</html>