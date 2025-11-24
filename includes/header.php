<?php
session_start();
include_once("function/functions.php");

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
              <a href="?logout"><i class="fas fa-sign-out-alt"></i> Đăng xuất</a>
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
          <img src="img/gagionvuive/gagionvuive1.jpg" alt="Tổng hợp">
          <p>TỔNG HỢP</p>
        </a>
        <a href="ThucDon.php?maloaimonan=1" class="<?php echo $maLoai == 1 ? 'active' : ''; ?>">
          <img src="img/gagionvuive/gagionvuive1.jpg" alt="Gà giòn vui vẻ">
          <p>GÀ GIÒN VUI VẺ</p>
        </a>
        <a href="ThucDon.php?maloaimonan=2" class="<?php echo $maLoai == 2 ? 'active' : ''; ?>">
          <img src="img/miy/miy1.jpg" alt="Mì Ý Jolly">
          <p>MÌ Ý JOLLY</p>
        </a>
        <a href="ThucDon.php?maloaimonan=3" class="<?php echo $maLoai == 3 ? 'active' : ''; ?>">
          <img src="img/gasot/gasot1.jpg" alt="Gà sốt cay">
          <p>GÀ SỐT</p>
        </a>
        <a href="ThucDon.php?maloaimonan=4" class="<?php echo $maLoai == 4 ? 'active' : ''; ?>">
          <img src="img/burger/burger1.jpg" alt="Burger/Cơm">
          <p>BURGER/CƠM</p>
        </a>
        <a href="ThucDon.php?maloaimonan=5" class="<?php echo $maLoai == 5 ? 'active' : ''; ?>">
          <img src="img/trangmieng/trangmieng1.webp" alt="Tráng miệng">
          <p>TRÁNG MIỆNG</p>
        </a>
        <a href="ThucDon.php?maloaimonan=6" class="<?php echo $maLoai == 6 ? 'active' : ''; ?>">
          <img src="img/nuoc/nuoc1.webp" alt="Nước">
          <p>NƯỚC</p>
        </a>
      </div>
    </nav>
  <?php endif; ?>

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
    // Xử lý logout
    <?php if (isset($_GET['logout'])): ?>
      if (confirm('Bạn có chắc muốn đăng xuất?')) {
        window.location.href = 'DangNhap.php';
      } else {
        const url = new URL(window.location.href);
        url.searchParams.delete('DangXuat');
        window.history.replaceState({}, '', url);
      }
    <?php endif; ?>

    document.addEventListener('DOMContentLoaded', function() {
        const mobileMenuToggle = document.getElementById('mobileMenuToggle');
        const mainNav = document.getElementById('mainNav');
        const userDropdowns = document.querySelectorAll('.user-dropdown, .booking-dropdown');
        const floatingContact = document.getElementById('floatingContact');
        const contactBubble = document.getElementById('contactBubble');

        // Tạo overlay cho dropdown mobile
        const dropdownOverlay = document.createElement('div');
        dropdownOverlay.className = 'dropdown-overlay';
        document.body.appendChild(dropdownOverlay);

        // Biến để theo dõi dropdown timeout
        let dropdownTimeout;
        let contactHoverTimeout;

        // Đảm bảo tất cả dropdown ẩn khi load trang
        floatingContact.classList.remove('active');
        userDropdowns.forEach(dropdown => {
            dropdown.classList.remove('active');
        });

        // Mobile menu toggle - slide từ bên PHẢI
        mobileMenuToggle.addEventListener('click', function(e) {
            e.stopPropagation();
            mainNav.classList.toggle('active');
            mobileMenuToggle.classList.toggle('active');
            
            // Đóng tất cả dropdown khi mở menu
            if (mainNav.classList.contains('active')) {
                userDropdowns.forEach(dropdown => {
                    dropdown.classList.remove('active');
                });
                floatingContact.classList.remove('active');
                dropdownOverlay.classList.remove('active');
                clearTimeout(dropdownTimeout);
                clearTimeout(contactHoverTimeout);
            }
        });

        // Xử lý dropdown cho cả desktop và mobile
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
                
                // Toggle current dropdown
                const isActive = dropdown.classList.contains('active');
                dropdown.classList.toggle('active');
                
                // Xử lý overlay cho mobile
                if (window.innerWidth <= 768) {
                    if (dropdown.classList.contains('active')) {
                        dropdownOverlay.classList.add('active');
                    } else {
                        dropdownOverlay.classList.remove('active');
                    }
                }
                
                // Reset timeout khi click
                clearTimeout(dropdownTimeout);
                
                // Nếu dropdown được mở, set timeout để đóng sau 5s
                if (dropdown.classList.contains('active') && !isActive) {
                    dropdownTimeout = setTimeout(() => {
                        dropdown.classList.remove('active');
                        if (window.innerWidth <= 768) {
                            dropdownOverlay.classList.remove('active');
                        }
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

        // Xử lý contact bubble dropdown
        // Hover để mở dropdown
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
                
                // Xử lý overlay cho mobile
                if (floatingContact.classList.contains('active')) {
                    dropdownOverlay.classList.add('active');
                } else {
                    dropdownOverlay.classList.remove('active');
                }
            }
        });

        // Đóng dropdown khi click overlay
        dropdownOverlay.addEventListener('click', function() {
            userDropdowns.forEach(dropdown => {
                dropdown.classList.remove('active');
            });
            floatingContact.classList.remove('active');
            dropdownOverlay.classList.remove('active');
            clearTimeout(dropdownTimeout);
            clearTimeout(contactHoverTimeout);
        });

        // Đóng menu và dropdown khi click ra ngoài
        document.addEventListener('click', function(e) {
            // Đóng mobile menu
            if (!mainNav.contains(e.target) && !mobileMenuToggle.contains(e.target)) {
                mainNav.classList.remove('active');
                mobileMenuToggle.classList.remove('active');
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
                
                if (!clickedInsideDropdown && !floatingContact.contains(e.target) && !dropdownOverlay.contains(e.target)) {
                    userDropdowns.forEach(dropdown => {
                        dropdown.classList.remove('active');
                    });
                    floatingContact.classList.remove('active');
                    dropdownOverlay.classList.remove('active');
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
                    mainNav.classList.remove('active');
                    mobileMenuToggle.classList.remove('active');
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
                mainNav.classList.remove('active');
                mobileMenuToggle.classList.remove('active');
                dropdownOverlay.classList.remove('active');
            } else {
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

        // Close with Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                userDropdowns.forEach(dropdown => {
                    dropdown.classList.remove('active');
                });
                floatingContact.classList.remove('active');
                dropdownOverlay.classList.remove('active');
                clearTimeout(dropdownTimeout);
                clearTimeout(contactHoverTimeout);
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
    });
  </script>