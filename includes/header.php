<?php
session_start();
include_once("function/functions.php");

// Xác định trang hiện tại và mã loại
$current_page = basename($_SERVER['PHP_SELF']);
$maLoai = $_GET['maloaimonan'] ?? 0;
?>

<!DOCTYPE html>
<html lang="vi">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Jollibee Việt Nam - Gà Giòn Vui Vẻ</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
  <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;700;900&display=swap" rel="stylesheet">
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: 'Nunito', sans-serif;
      background: #fff8e1;
      color: #333;
      line-height: 1.6;
    }

    a {
      text-decoration: none;
      color: inherit;
    }

    header {
      background: linear-gradient(135deg, #d32f2f, #f57c00);
      color: #fff;
      padding: 1rem 0;
      position: sticky;
      top: 0;
      z-index: 1000;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }

    .header-top {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 0 2rem;
      font-size: 0.9rem;
    }

    .lang-switch a {
      color: #fff;
      font-weight: bold;
    }

    .auth a,
    .auth span {
      color: #fff;
      font-weight: bold;
    }

    .header-main {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 1rem 2rem;
    }

    .logo img {
      height: 60px;
    }

    nav ul {
      display: flex;
      list-style: none;
      gap: 1.5rem;
    }

    nav ul li a {
      color: #fff;
      font-weight: 900;
      font-size: 1.1rem;
      padding: 0.5rem 1rem;
      border-radius: 25px;
      transition: 0.3s;
    }

    nav ul li a:hover,
    nav ul li a.active {
      background: #fff;
      color: #d32f2f;
    }

    /* Menu loại món */
    .menu-loai {
      display: flex;
      justify-content: center;
      flex-wrap: wrap;
      gap: 1.5rem;
      margin: 2rem 0;
    }

    .menu-loai a {
      text-align: center;
    }

    .menu-loai img {
      width: 70px;
      transition: 0.3s;
      border-radius: 50%;
      border: 3px solid #fff;
    }

    .menu-loai img:hover {
      transform: scale(1.1);
      border-color: #ffeb3b;
    }

    .menu-loai p {
      font-weight: 900;
      font-size: 0.9rem;
      margin-top: 0.5rem;
      color: #fff;
    }

    /* Bảng món */
    .bang-mon {
      width: 100%;
      margin: 2rem auto;
      border-collapse: collapse;
    }

    .bang-mon td {
      text-align: center;
      padding: 1.5rem;
      vertical-align: top;
    }

    .bang-mon img {
      width: 180px;
      height: 180px;
      object-fit: cover;
      border-radius: 15px;
      transition: 0.3s;
      border: 3px solid #ffe0b2;
    }

    .bang-mon img:hover {
      transform: scale(1.1);
      border-color: #f57c00;
    }

    .tenmon {
      font-weight: 900;
      color: #d32f2f;
      margin: 0.5rem 0;
      font-size: 1.1rem;
    }

    .gia {
      color: #f57c00;
      font-weight: 900;
      font-size: 1.2rem;
      margin: 0.5rem 0;
    }

    .btn-edit {
      background: #d32f2f;
      color: #fff;
      padding: 0.5rem 1rem;
      border-radius: 20px;
      font-weight: bold;
      transition: 0.3s;
      display: inline-block;
      margin-top: 0.5rem;
    }

    .btn-edit:hover {
      background: #b71c1c;
      transform: translateY(-2px);
    }

    .container {
      max-width: 1200px;
      margin: 2rem auto;
      padding: 0 1rem;
    }

    .tab-content {
      display: none;
      padding: 2rem;
      background: #fff;
      border-radius: 15px;
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
      margin-bottom: 2rem;
    }

    .tab-content.active {
      display: block;
    }

    footer {
      background: #d32f2f;
      color: #fff;
      text-align: center;
      padding: 2.5rem 1rem;
      margin-top: 3rem;
    }

    .footer-links {
      display: flex;
      justify-content: center;
      gap: 1.5rem;
      margin: 1.5rem 0;
      flex-wrap: wrap;
    }

    .footer-links a {
      color: #fff;
      font-weight: bold;
    }

    /* Hero Section */
    .hero-section {
      background: url('https://jollibee.com.vn/media/wysiwyg/2024/10/ga-gion-vui-ve-banner.jpg') center/cover no-repeat;
      height: 500px;
      display: flex;
      align-items: center;
      justify-content: center;
      text-align: center;
      color: #fff;
      position: relative;
      margin-bottom: 3rem;
    }

    .hero-section::before {
      content: '';
      position: absolute;
      inset: 0;
      background: rgba(211, 47, 47, 0.7);
    }

    .hero-content {
      position: relative;
      z-index: 2;
    }

    .hero-content h1 {
      font-size: 3.5rem;
      font-weight: 900;
      text-shadow: 2px 2px 8px #000;
      margin-bottom: 1rem;
    }

    .hero-content p {
      font-size: 1.3rem;
      margin: 1rem 0;
      font-weight: 700;
    }

    .cta-buttons {
      margin-top: 2rem;
    }

    .btn {
      display: inline-block;
      padding: 15px 35px;
      margin: 0 15px;
      text-decoration: none;
      border-radius: 30px;
      font-weight: 900;
      font-size: 1.1rem;
      transition: all 0.3s ease;
      text-transform: uppercase;
      letter-spacing: 1px;
    }

    .btn-primary {
      background: #ffeb3b;
      color: #d32f2f;
      border: 3px solid #ffeb3b;
    }

    .btn-secondary {
      background: transparent;
      color: #ffeb3b;
      border: 3px solid #ffeb3b;
    }

    .btn:hover {
      transform: translateY(-3px);
      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.3);
    }

    .btn-primary:hover {
      background: #ffc107;
      border-color: #ffc107;
    }

    .btn-secondary:hover {
      background: #ffeb3b;
      color: #d32f2f;
    }

    /* Page Header */
    .page-header {
      text-align: center;
      margin: 2rem 0 3rem;
      padding: 3rem 2rem;
      background: linear-gradient(135deg, #d32f2f, #f57c00);
      color: white;
      border-radius: 20px;
      box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
    }

    .page-header h1 {
      font-size: 3rem;
      margin-bottom: 1rem;
      font-weight: 900;
      text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
    }

    .page-header p {
      font-size: 1.2rem;
      font-weight: 700;
      opacity: 0.9;
    }

    @media (max-width: 992px) {
      .header-main {
        flex-direction: column;
        gap: 1rem;
      }
    }

    @media (max-width: 768px) {
      .hero-content h1 {
        font-size: 2.5rem;
      }

      .btn {
        padding: 12px 25px;
        margin: 0 10px 10px 0;
        font-size: 1rem;
      }

      .page-header h1 {
        font-size: 2.2rem;
      }
    }

    @media (max-width: 576px) {
      .hero-content h1 {
        font-size: 2rem;
      }

      .cta-buttons {
        display: flex;
        flex-direction: column;
        align-items: center;
      }

      .btn {
        width: 80%;
        margin: 5px 0;
      }
    }
  </style>
</head>

<body>

  <!-- HEADER -->
  <header>
    <div class="header-top">
      <div class="lang-switch">
        <a href="#" class="active">VN</a> | <a href="#">EN</a>
      </div>
      <div class="auth">
        <?php if (isset($_SESSION['loggedin'])): ?>
          <span>Xin chào, <strong><?php echo htmlspecialchars($_SESSION['HoTen']); ?></strong> | <a href="?logout">Đăng xuất</a></span>
        <?php else: ?>
          <a href="DangNhap.php"><i class="fas fa-user"></i> ĐĂNG NHẬP</a>
        <?php endif; ?>
      </div>
    </div>
    <div class="header-main">
      <div class="logo">
        <a href="index.php"><img src="https://jollibee.com.vn/static/version1739434637/frontend/Jollibee/default/vi_VN/images/logo.png" alt="Jollibee"></a>
      </div>
      <!-- Cập nhật navigation trong header -->
      <nav>
        <ul>
          <li><a href="index.php" class="<?php echo $current_page == 'index.php' ? 'active' : ''; ?>">TRANG CHỦ</a></li>
          <li><a href="ThucDon.php" class="<?php echo $current_page == 'ThucDon.php' ? 'active' : ''; ?>">THỰC ĐƠN</a></li>
          <li><a href="KhuyenMai.php" class="<?php echo $current_page == 'KhuyenMai.php' ? 'active' : ''; ?>">KHUYẾN MÃI</a></li>
          <li><a href="UuDai.php" class="<?php echo $current_page == 'UuDai.php' ? 'active' : ''; ?>">ƯU ĐÃI</a></li>
          <li><a href="GioiThieu.php" class="<?php echo $current_page == 'GioiThieu.php' ? 'active' : ''; ?>">GIỚI THIỆU</a></li>
          <li><a href="LienHe.php" class="<?php echo $current_page == 'LienHe.php' ? 'active' : ''; ?>">LIÊN HỆ</a></li>
          <?php if (isset($_SESSION['loggedin'])): ?>
            <li><a href="profile.php" class="<?php echo $current_page == 'profile.php' ? 'active' : ''; ?>">THÔNG TIN</a></li>
          <?php endif; ?>
        </ul>
      </nav>
    </div>
  </header>

  <!-- MENU LOẠI MÓN (Chỉ hiển thị ở trang ThucDon.php) -->
  <?php if ($current_page == 'ThucDon.php'): ?>
    <nav>
      <div class="menu-loai">
        <a href="ThucDon.php?maloaimonan=0" class="<?php echo $maLoai == 0 ? 'active' : ''; ?>">
          <img src="img/gagionvuive1.jpg" alt="Tổng hợp">
          <p>TỔNG HỢP</p>
        </a>
        <a href="ThucDon.php?maloaimonan=1" class="<?php echo $maLoai == 1 ? 'active' : ''; ?>">
          <img src="img/gagionvuive1.jpg" alt="Gà giòn vui vẻ">
          <p>GÀ GIÒN VUI VẺ</p>
        </a>
        <a href="ThucDon.php?maloaimonan=2" class="<?php echo $maLoai == 2 ? 'active' : ''; ?>">
          <img src="img/miy1.jpg" alt="Mì Ý Jolly">
          <p>MÌ Ý JOLLY</p>
        </a>
        <a href="ThucDon.php?maloaimonan=3" class="<?php echo $maLoai == 3 ? 'active' : ''; ?>">
          <img src="img/gasot1.jpg" alt="Gà sốt cay">
          <p>GÀ SỐT</p>
        </a>
        <a href="ThucDon.php?maloaimonan=4" class="<?php echo $maLoai == 4 ? 'active' : ''; ?>">
          <img src="img/burger1.jpg" alt="Burger/Cơm">
          <p>BURGER/CƠM</p>
        </a>
        <a href="ThucDon.php?maloaimonan=5" class="<?php echo $maLoai == 5 ? 'active' : ''; ?>">
          <img src="img/trangmieng1.jpg" alt="Tráng miệng">
          <p>TRÁNG MIỆNG</p>
        </a>
        <a href="ThucDon.php?maloaimonan=6" class="<?php echo $maLoai == 6 ? 'active' : ''; ?>">
          <img src="img/nuoc1.jpg" alt="Nước">
          <p>NƯỚC</p>
        </a>
      </div>
    </nav>
  <?php endif; ?>