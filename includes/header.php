<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include_once("function/functions.php");
include_once("includes/myenv.php");

// --- [THÊM] TỰ ĐỘNG ĐĂNG NHẬP NẾU CÓ COOKIE ---
// Nếu chưa có Session NHƯNG lại có Cookie 'user_login'
if (!isset($_SESSION['loggedin']) && isset($_COOKIE['user_login'])) 
{
    
    $maUserCookie = $_COOKIE['user_login'];
    
    // Kết nối database để lấy lại thông tin User
    $conn_check = mysqli_connect($db_host, $db_user, $db_password, $db_db, $db_port);
    
    if ($conn_check) {
        // Dùng Prepared Statement để bảo mật
        $sql_check = "SELECT MaUser, Ho, Ten, Email, QuyenHan FROM Users WHERE MaUser = ?";
        $stmt_check = mysqli_prepare($conn_check, $sql_check);
        
        if ($stmt_check) {
            mysqli_stmt_bind_param($stmt_check, "i", $maUserCookie);
            mysqli_stmt_execute($stmt_check);
            $res_check = mysqli_stmt_get_result($stmt_check);
            
            if ($res_check && mysqli_num_rows($res_check) == 1) 
            {
                $row = mysqli_fetch_assoc($res_check);
                
                // TÁI TẠO SESSION
                $_SESSION['loggedin'] = true;
                $_SESSION['MaUser'] = $row['MaUser'];
                $_SESSION['HoTen'] = $row['Ho'] . " " . $row['Ten'];
                $_SESSION['Email'] = $row['Email'];
                $_SESSION['QuyenHan'] = $row['QuyenHan'];
            }
            mysqli_stmt_close($stmt_check);
        }
        mysqli_close($conn_check);
    }
}
// ----------------------------------------------

// Xác định trang hiện tại và mã loại
$current_page = basename($_SERVER['PHP_SELF']);
$maLoai = $_GET['maloaimonan'] ?? 0;

// Kết nối database để lấy số lượng giỏ hàng
include_once("includes/myenv.php");
$conn = mysqli_connect($db_host, $db_user, $db_password, $db_db, $db_port);

$cart_count = 0;
if (isset($_SESSION['loggedin']) && isset($_SESSION['MaUser'])) {
  $maUser = $_SESSION['MaUser'];
  $cart_sql = "SELECT SUM(SoLuong) as total FROM GioHang WHERE MaUser = ?";
  $stmt = mysqli_prepare($conn, $cart_sql);
  if ($stmt) {
    mysqli_stmt_bind_param($stmt, "i", $maUser);
    mysqli_stmt_execute($stmt);
    $cart_result = mysqli_stmt_get_result($stmt);
    if ($cart_result && mysqli_num_rows($cart_result) > 0) {
      $cart_data = mysqli_fetch_assoc($cart_result);
      $cart_count = $cart_data['total'] ?? 0;
    }
    mysqli_stmt_close($stmt);
  }
}
?>

<!DOCTYPE html>
<html lang="vi">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Jollibee Việt Nam - Gà Giòn Vui Vẻ</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
  <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;700;900&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="css/LienHe.css">
  <link rel="stylesheet" href="css/style.css">
  <link rel="stylesheet" href="css/contact_bubble.css">
</head>

<body>

  <!-- HEADER -->
  <header>
    <!-- Header Top: Logo và Auth -->
    <div class="header-top">
      <div class="logo-section">
        <div class="logo">
          <a href="index.php">
            <img src="https://jollibee.com.vn/static/version1739434637/frontend/Jollibee/default/vi_VN/images/logo.png" alt="Jollibee">
          </a>
        </div>
      </div>

      <div class="auth">
        <?php if (isset($_SESSION['loggedin'])): ?>
          <div class="user-dropdown">
            <a href="#" class="dropdown-trigger">
              <i class="fas fa-user"></i>
              <?php echo htmlspecialchars($_SESSION['HoTen']); ?>
              <i class="fas fa-caret-down"></i>
            </a>
            <div class="user-dropdown-content">
              <a href="ThongTinTaiKhoan.php"><i class="fas fa-user-circle"></i> Thông tin tài khoản</a>
              <a href="DonHang.php"><i class="fas fa-clipboard-list"></i> Đơn hàng của tôi</a>
              <a href="DanhGia.php"><i class="fas fa-star"></i> Đánh giá của tôi</a>
              <a href="#" id="logoutTrigger"><i class="fas fa-sign-out-alt"></i> Đăng xuất</a>
            </div>
          </div>
        <?php else: ?>
          <a href="DangNhap.php"><i class="fas fa-sign-in-alt"></i> ĐĂNG NHẬP</a>
          <a href="DangKy.php"><i class="fas fa-user-plus"></i> ĐĂNG KÝ</a>
        <?php endif; ?>
      </div>
    </div>

    <!-- Header Bottom: Search và Mobile Menu -->
    <div class="header-bottom">
      <div class="search-container-mobile">
        <div class="search-box-mobile">
          <form method="GET" action="TimKiem.php">
            <input type="text" name="keyword" placeholder="Tìm món ăn..." required>
            <button type="submit">
              <i class="fas fa-search"></i>
            </button>
          </form>
        </div>
      </div>

      <!-- Mobile Menu Button -->
      <div class="mobile-menu-toggle" id="mobileMenuToggle">
        <i class="fas fa-bars"></i>
      </div>
    </div>

    <!-- Header Main: Navigation -->
    <div class="header-main">
      <nav class="main-nav" id="mainNav">
        <!-- NÚT ĐÓNG NẰM TRONG NAVBAR MOBILE -->
        <button class="mobile-menu-close" id="mobileMenuClose">
          <i class="fas fa-times"></i>
        </button>

        <ul>
          <li><a href="index.php" class="<?php echo $current_page == 'index.php' ? 'active' : ''; ?>">
              <i class="fas fa-home"></i> TRANG CHỦ
            </a></li>
          <li><a href="ThucDon.php" class="<?php echo $current_page == 'ThucDon.php' ? 'active' : ''; ?>">
              <i class="fas fa-utensils"></i> THỰC ĐƠN
            </a></li>
          <li><a href="KhuyenMai.php" class="<?php echo $current_page == 'KhuyenMai.php' ? 'active' : ''; ?>">
              <i class="fas fa-percent"></i> KHUYẾN MÃI
            </a></li>
          <li><a href="UuDai.php" class="<?php echo $current_page == 'UuDai.php' ? 'active' : ''; ?>">
              <i class="fas fa-gift"></i> ƯU ĐÃI
            </a></li>

          <?php if (isset($_SESSION['loggedin'])): ?>
            <li><a href="GioHang.php" class="<?php echo $current_page == 'GioHang.php' ? 'active' : ''; ?>">
                <i class="fas fa-shopping-cart"></i> GIỎ HÀNG
                <?php if ($cart_count > 0): ?>
                  <span class="cart-count"><?php echo $cart_count; ?></span>
                <?php endif; ?>
              </a></li>

            <li class="booking-dropdown">
              <a href="#" class="dropdown-trigger">
                <i class="fas fa-calendar-check"></i> ĐẶT CHỖ
                <i class="fas fa-caret-down"></i>
              </a>
              <div class="booking-dropdown-content">
                <a style="color: #5d4037;" href="DatBan.php" class="dropdown-item"><i class="fas fa-utensils"></i> Đặt bàn</a>
                <a style="color: #5d4037;" href="DatSuKien.php" class="dropdown-item"><i class="fas fa-birthday-cake"></i> Đặt sự kiện</a>
              </div>
            </li>
          <?php else: ?>
            <li><a href="DangNhap.php" class="<?php echo $current_page == 'DangNhap.php' ? 'active' : ''; ?>">
                <i class="fas fa-shopping-cart"></i> GIỎ HÀNG
              </a></li>
            <li><a href="DangNhap.php">
                <i class="fas fa-calendar-check"></i> ĐẶT CHỖ
              </a></li>
          <?php endif; ?>

          <li><a href="GioiThieu.php" class="<?php echo $current_page == 'GioiThieu.php' ? 'active' : ''; ?>">
              <i class="fas fa-info-circle"></i> GIỚI THIỆU
            </a></li>
          <!-- Thêm vào phần main-nav ul, sau mục GIỚI THIỆU -->
          <li><a href="BaiTap.php" class="<?php echo $current_page == 'BaiTap.php' ? 'active' : ''; ?>">
              <i class="fas fa-book"></i> BÀI TẬP
            </a></li>
        </ul>
      </nav>
    </div>

    <!-- Search Row: Thanh tìm kiếm riêng (cho desktop) -->
    <div class="search-row">
      <div class="search-container-full">
        <div class="search-box-full">
          <form method="GET" action="TimKiem.php">
            <input type="text" name="keyword" placeholder="Tìm món ăn..." required>
            <button type="submit">
              <i class="fas fa-search"></i> TÌM KIẾM
            </button>
          </form>
        </div>
      </div>
    </div>
  </header>

  <!-- MENU LOẠI MÓN (Chỉ hiển thị ở trang ThucDon.php) -->
  <?php if ($current_page == 'ThucDon.php'): ?>
    <nav class="category-nav">
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

  <!-- MODAL ĐĂNG XUẤT -->
  <div class="logout-modal" id="logoutModal">
    <div class="logout-modal-content">
      <div class="logout-modal-header">
        <h3><i class="fas fa-sign-out-alt"></i> ĐĂNG XUẤT</h3>
        <button class="logout-modal-close" id="logoutModalClose">
          <i class="fas fa-times"></i>
        </button>
      </div>
      <div class="logout-modal-body">
        <div class="logout-icon">
          <i class="fas fa-question-circle"></i>
        </div>
        <p>Bạn có chắc muốn đăng xuất khỏi tài khoản?</p>
      </div>
      <div class="logout-modal-footer">
        <button class="btn btn-secondary" id="logoutCancel">
          <i class="fas fa-times"></i> HỦY
        </button>
        <button class="btn btn-primary" id="logoutConfirm">
          <i class="fas fa-check"></i> ĐĂNG XUẤT
        </button>
      </div>
    </div>
  </div>

  <!-- Floating Contact Button với Dropdown -->
  <div class="floating-contact" id="floatingContact">
    <div class="contact-bubble" id="contactBubble">
      <i class="fas fa-comments"></i>
      <div class="contact-notification">!</div>
    </div>

    <!-- Contact Dropdown -->
    <div class="contact-dropdown">
      <div class="contact-dropdown-header">
        <h3>LIÊN HỆ NHANH</h3>
        <p>Chúng tôi luôn sẵn sàng hỗ trợ!</p>
      </div>

      <div class="contact-dropdown-methods">
        <a href="tel:19001234" class="contact-dropdown-method">
          <div class="contact-dropdown-icon">
            <i class="fas fa-phone"></i>
          </div>
          <div class="contact-dropdown-info">
            <h4>GỌI NGAY</h4>
            <p>Hotline: <span>1900 1234</span></p>
          </div>
        </a>

        <a href="https://zalo.me/0901234567" target="_blank" class="contact-dropdown-method">
          <div class="contact-dropdown-icon">
            <i class="fab fa-zalo"></i>
          </div>
          <div class="contact-dropdown-info">
            <h4>ZALO</h4>
            <p>Chat ngay: <span>090 1234 567</span></p>
          </div>
        </a>

        <a href="https://m.me/jollibeevietnam" target="_blank" class="contact-dropdown-method">
          <div class="contact-dropdown-icon">
            <i class="fab fa-facebook-messenger"></i>
          </div>
          <div class="contact-dropdown-info">
            <h4>MESSENGER</h4>
            <p>Nhắn tin Facebook</p>
          </div>
        </a>

        <a href="mailto:contact@jollibee.vn" class="contact-dropdown-method">
          <div class="contact-dropdown-icon">
            <i class="fas fa-envelope"></i>
          </div>
          <div class="contact-dropdown-info">
            <h4>EMAIL</h4>
            <p>contact@jollibee.vn</p>
          </div>
        </a>

        <a href="LienHe.php" class="contact-dropdown-method">
          <div class="contact-dropdown-icon">
            <i class="fas fa-map-marker-alt"></i>
          </div>
          <div class="contact-dropdown-info">
            <h4>ĐỊA CHỈ</h4>
            <p>Xem cửa hàng gần nhất</p>
          </div>
        </a>
      </div>
    </div>
  </div>

  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const mobileMenuToggle = document.getElementById('mobileMenuToggle');
      const mainNav = document.getElementById('mainNav');
      const mobileMenuClose = document.getElementById('mobileMenuClose');
      const userDropdowns = document.querySelectorAll('.user-dropdown, .booking-dropdown');
      const floatingContact = document.getElementById('floatingContact');
      const contactBubble = document.getElementById('contactBubble');

      // BIẾN CHO MODAL ĐĂNG XUẤT
      const logoutTrigger = document.getElementById('logoutTrigger');
      const logoutModal = document.getElementById('logoutModal');
      const logoutModalClose = document.getElementById('logoutModalClose');
      const logoutCancel = document.getElementById('logoutCancel');
      const logoutConfirm = document.getElementById('logoutConfirm');

      // Biến để theo dõi dropdown timeout
      let dropdownTimeout;
      let contactHoverTimeout;

      // Đảm bảo tất cả dropdown ẩn khi load trang
      floatingContact.classList.remove('active');
      userDropdowns.forEach(dropdown => {
        dropdown.classList.remove('active');
      });

      // ===== XỬ LÝ MODAL ĐĂNG XUẤT =====
      // Mở modal khi click đăng xuất
      if (logoutTrigger) {
        logoutTrigger.addEventListener('click', function(e) {
          e.preventDefault();
          e.stopPropagation();
          logoutModal.classList.add('active');

          // Đóng tất cả dropdown khi mở modal
          userDropdowns.forEach(dropdown => {
            dropdown.classList.remove('active');
          });
          floatingContact.classList.remove('active');
        });
      }

      // Đóng modal khi click nút đóng
      logoutModalClose.addEventListener('click', function() {
        logoutModal.classList.remove('active');
      });

      // Đóng modal khi click hủy
      logoutCancel.addEventListener('click', function() {
        logoutModal.classList.remove('active');
      });

      // Xác nhận đăng xuất
      logoutConfirm.addEventListener('click', function() {
        window.location.href = 'DangNhap.php?logout=true';
      });

      // Đóng modal khi click ra ngoài
      logoutModal.addEventListener('click', function(e) {
        if (e.target === logoutModal) {
          logoutModal.classList.remove('active');
        }
      });

      // Đóng modal khi nhấn Escape
      document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && logoutModal.classList.contains('active')) {
          logoutModal.classList.remove('active');
        }
      });

      // ===== XỬ LÝ MOBILE MENU =====
      // Mobile menu toggle - slide từ bên PHẢI
      mobileMenuToggle.addEventListener('click', function(e) {
        e.stopPropagation();
        openMobileMenu();
      });

      // Nút đóng mobile menu - NÚT X NẰM TRONG NAVBAR
      mobileMenuClose.addEventListener('click', function(e) {
        e.stopPropagation();
        closeMobileMenu();
      });

      function openMobileMenu() {
        mainNav.classList.add('active');
        mobileMenuToggle.style.display = 'none'; // ẨN DẤU 3 GẠCH
        mobileMenuClose.style.display = 'flex'; // HIỆN NÚT X

        // Đóng tất cả dropdown khi mở menu
        userDropdowns.forEach(dropdown => {
          dropdown.classList.remove('active');
        });
        floatingContact.classList.remove('active');
        clearTimeout(dropdownTimeout);
        clearTimeout(contactHoverTimeout);
      }

      function closeMobileMenu() {
        mainNav.classList.remove('active');
        mobileMenuToggle.style.display = 'flex'; // HIỆN LẠI DẤU 3 GẠCH
        mobileMenuClose.style.display = 'none'; // ẨN NÚT X

        // Đóng tất cả dropdown khi đóng menu
        userDropdowns.forEach(dropdown => {
          dropdown.classList.remove('active');
        });
        floatingContact.classList.remove('active');
        clearTimeout(dropdownTimeout);
        clearTimeout(contactHoverTimeout);
      }

      // ===== XỬ LÝ DROPDOWN =====
      userDropdowns.forEach(dropdown => {
        const trigger = dropdown.querySelector('.dropdown-trigger');

        // Click để toggle dropdown
        trigger.addEventListener('click', function(e) {
          e.preventDefault();
          e.stopPropagation();

          // Đóng các dropdown khác
          userDropdowns.forEach(other => {
            if (other !== dropdown) {
              other.classList.remove('active');
            }
          });
          floatingContact.classList.remove('active');

          // Toggle current dropdown - DROPDOWN CÓ Z-INDEX CAO HƠN
          const isActive = dropdown.classList.contains('active');
          dropdown.classList.toggle('active');

          // Reset timeout khi click
          clearTimeout(dropdownTimeout);

          // Nếu dropdown được mở, set timeout để đóng sau 5s
          if (dropdown.classList.contains('active') && !isActive) {
            dropdownTimeout = setTimeout(() => {
              dropdown.classList.remove('active');
            }, 5000); // 5 giây
          }
        });

        // Hover để mở dropdown (chỉ desktop)
        if (window.innerWidth > 768) {
          dropdown.addEventListener('mouseenter', function() {
            clearTimeout(dropdownTimeout);
            clearTimeout(contactHoverTimeout);
            // Đóng các dropdown khác
            userDropdowns.forEach(other => {
              if (other !== dropdown) {
                other.classList.remove('active');
              }
            });
            floatingContact.classList.remove('active');
            dropdown.classList.add('active');
          });

          dropdown.addEventListener('mouseleave', function() {
            dropdownTimeout = setTimeout(() => {
              dropdown.classList.remove('active');
            }, 5000); // 5 giây
          });

          // Giữ dropdown mở khi hover vào content
          const content = dropdown.querySelector('.user-dropdown-content, .booking-dropdown-content');
          if (content) {
            content.addEventListener('mouseenter', function() {
              clearTimeout(dropdownTimeout);
            });

            content.addEventListener('mouseleave', function() {
              dropdownTimeout = setTimeout(() => {
                dropdown.classList.remove('active');
              }, 5000);
            });
          }
        }
      });

      // ===== XỬ LÝ CONTACT BUBBLE =====
      contactBubble.addEventListener('mouseenter', function() {
        clearTimeout(contactHoverTimeout);
        clearTimeout(dropdownTimeout);
        // Đóng các dropdown khác
        userDropdowns.forEach(dropdown => {
          dropdown.classList.remove('active');
        });
        floatingContact.classList.add('active');
      });

      contactBubble.addEventListener('mouseleave', function() {
        contactHoverTimeout = setTimeout(() => {
          if (!floatingContact.querySelector('.contact-dropdown').matches(':hover')) {
            floatingContact.classList.remove('active');
          }
        }, 300);
      });

      // Giữ dropdown mở khi hover vào dropdown
      const contactDropdown = floatingContact.querySelector('.contact-dropdown');
      contactDropdown.addEventListener('mouseenter', function() {
        clearTimeout(contactHoverTimeout);
      });

      contactDropdown.addEventListener('mouseleave', function() {
        contactHoverTimeout = setTimeout(() => {
          floatingContact.classList.remove('active');
        }, 300);
      });

      // Click để toggle trên mobile
      contactBubble.addEventListener('click', function() {
        if (window.innerWidth <= 768) {
          // Đóng các dropdown khác
          userDropdowns.forEach(dropdown => {
            dropdown.classList.remove('active');
          });
          floatingContact.classList.toggle('active');
        }
      });

      // ===== XỬ LÝ SỰ KIỆN CLICK NGOÀI =====
      document.addEventListener('click', function(e) {
        // Đóng mobile menu
        if (!mainNav.contains(e.target) &&
          !mobileMenuToggle.contains(e.target) &&
          !mobileMenuClose.contains(e.target)) {
          closeMobileMenu();
        }

        // Đóng dropdown trên desktop khi click ra ngoài
        if (window.innerWidth > 768) {
          let clickedInsideDropdown = false;
          userDropdowns.forEach(dropdown => {
            if (dropdown.contains(e.target)) {
              clickedInsideDropdown = true;
            }
          });

          if (!clickedInsideDropdown && !floatingContact.contains(e.target)) {
            userDropdowns.forEach(dropdown => {
              dropdown.classList.remove('active');
            });
            floatingContact.classList.remove('active');
            clearTimeout(dropdownTimeout);
            clearTimeout(contactHoverTimeout);
          }
        }

        // Đóng dropdown trên mobile khi click ra ngoài
        if (window.innerWidth <= 768) {
          let clickedInsideDropdown = false;
          userDropdowns.forEach(dropdown => {
            if (dropdown.contains(e.target)) {
              clickedInsideDropdown = true;
            }
          });

          if (!clickedInsideDropdown && !floatingContact.contains(e.target)) {
            userDropdowns.forEach(dropdown => {
              dropdown.classList.remove('active');
            });
            floatingContact.classList.remove('active');
            clearTimeout(dropdownTimeout);
            clearTimeout(contactHoverTimeout);
          }
        }
      });

      // Close mobile menu when clicking on links (except dropdown triggers)
      const navLinks = mainNav.querySelectorAll('a');
      navLinks.forEach(link => {
        link.addEventListener('click', function(e) {
          if (!this.classList.contains('dropdown-trigger')) {
            closeMobileMenu();
          }
        });
      });

      // Prevent form submission if search is empty
      const searchForms = document.querySelectorAll('form');
      searchForms.forEach(form => {
        const input = form.querySelector('input[name="keyword"]');
        if (input) {
          form.addEventListener('submit', function(e) {
            if (input.value.trim() === '') {
              e.preventDefault();
              alert('Vui lòng nhập từ khóa tìm kiếm');
            }
          });
        }
      });

      // Handle window resize
      window.addEventListener('resize', function() {
        if (window.innerWidth > 768) {
          closeMobileMenu();
          mobileMenuToggle.style.display = 'none';
        } else {
          mobileMenuToggle.style.display = 'flex';
          // Trên mobile, đóng tất cả dropdown khi resize
          userDropdowns.forEach(dropdown => {
            dropdown.classList.remove('active');
          });
          floatingContact.classList.remove('active');
        }
        clearTimeout(dropdownTimeout);
        clearTimeout(contactHoverTimeout);
      });

      // Auto-hide notification after first interaction
      const notification = document.querySelector('.contact-notification');
      contactBubble.addEventListener('mouseenter', function() {
        if (notification) {
          notification.style.display = 'none';
        }
      });

      // Đóng tất cả dropdown khi scroll (tuỳ chọn)
      window.addEventListener('scroll', function() {
        if (window.innerWidth > 768) {
          userDropdowns.forEach(dropdown => {
            dropdown.classList.remove('active');
          });
          floatingContact.classList.remove('active');
          clearTimeout(dropdownTimeout);
          clearTimeout(contactHoverTimeout);
        }
      });

      // Đảm bảo hiển thị đúng khi load trang
      if (window.innerWidth > 768) {
        mobileMenuToggle.style.display = 'none';
        mobileMenuClose.style.display = 'none';
      } else {
        mobileMenuToggle.style.display = 'flex';
        mobileMenuClose.style.display = 'none';
      }
    });
  </script>