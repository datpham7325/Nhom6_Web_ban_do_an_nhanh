<?php include_once "includes/header.php"; ?>

<!-- Hero Section - Phần giới thiệu chính -->
<div class="hero-section">
    <div class="hero-content">
        <h1>GÀ GIÒN VUI VẺ</h1>
        <p>Giòn tan – Ngon khó cưỡng – Vui vẻ từng miếng!</p>
        <div class="cta-buttons">
            <!-- Nút CTA chính dẫn đến danh mục gà giòn vui vẻ -->
            <a href="ThucDon.php?maloaimonan=1" class="btn btn-primary">GÀ GIÒN VUI VẺ</a>
            <!-- Nút CTA phụ dẫn đến toàn bộ menu -->
            <a href="ThucDon.php" class="btn btn-secondary">XEM TOÀN BỘ MENU</a>
        </div>
    </div>
</div>

<div class="container">
    <!-- Section danh mục nổi bật -->
    <section style="margin: 4rem 0;">
        <h2 style="text-align:center; color:#d32f2f; font-size: 2.5rem; margin-bottom: 2rem; font-weight: 900;">DANH MỤC NỔI BẬT</h2>
        <div class="menu-loai">
            <!-- Danh mục 1: Gà giòn vui vẻ -->
            <a href="ThucDon.php?maloaimonan=1">
                <img src="img/gagionvuive/gagionvuive1.jpg" alt="Gà giòn vui vẻ">
                <p style="color: #5d4037;">GÀ GIÒN VUI VẺ</p>
            </a>
            <!-- Danh mục 2: Mì Ý Jolly -->
            <a href="ThucDon.php?maloaimonan=2">
                <img src="img/miy/miy1.jpg" alt="Mì Ý Jolly">
                <p style="color: #5d4037;">MÌ Ý JOLLY</p>
            </a>
            <!-- Danh mục 3: Burger & Cơm -->
            <a href="ThucDon.php?maloaimonan=4">
                <img src="img/burger/burger1.jpg" alt="Burger/Cơm">
                <p style="color: #5d4037;">BURGER & CƠM</p>
            </a>
            <!-- Danh mục 4: Tráng miệng -->
            <a href="ThucDon.php?maloaimonan=5">
                <img src="img/trangmieng/trangmieng1.webp" alt="Tráng miệng">
                <p style="color: #5d4037;">TRÁNG MIỆNG</p>
            </a>
        </div>
    </section>

    <!-- Section khuyến mãi nổi bật -->
    <section style="margin: 4rem 0;">
        <h2 style="text-align:center; color:#d32f2f; font-size: 2.5rem; margin-bottom: 2rem; font-weight: 900;">KHUYẾN MÃI NỔI BẬT</h2>
        <div class="content-container">
            <div style="text-align: center;">
                <!-- Hình ảnh khuyến mãi chính -->
                <img src="img/khuyenmai/banner_khuyen_mai.jpg" alt="Khuyến mãi" style="width: 100%; max-width: 600px; border-radius: 15px; margin-bottom: 2rem;">
                <!-- Tiêu đề khuyến mãi -->
                <h3 style="color: #f57c00; margin-bottom: 1rem;">COMBO GIA ĐÌNH - TIẾT KIỆM 30%</h3>
                <!-- Mô tả khuyến mãi -->
                <p style="margin-bottom: 1.5rem; font-size: 1.1rem;">Ưu đãi đặc biệt cho gia đình bạn</p>
                <!-- Nút dẫn đến trang khuyến mãi -->
                <a href="KhuyenMai.php" class="btn-add">XEM NGAY</a>
            </div>
        </div>
    </section>
</div>

<?php include_once "includes/footer.php"; ?>