<footer>
    <div class="container">
        <p><strong>JOLLIBEE VIỆT NAM</strong> - Hương vị vui vẻ từ Philippines</p>
        <p>© 2025 Trần Hải Thiện - Nha Trang, Khánh Hòa</p>
        <div class="footer-links">
            <a href="#">Về chúng tôi</a>
            <a href="#">Liên hệ</a>
            <a href="#">Điều khoản sử dụng</a>
            <a href="#">Chính sách bảo mật</a>
        </div>
    </div>
</footer>

<script>
// JavaScript cho active state menu
document.addEventListener('DOMContentLoaded', function() {
    // Thêm active class cho menu item được chọn
    const currentPage = '<?php echo $current_page; ?>';
    const maLoai = '<?php echo $maLoai; ?>';
    
    // Highlight menu loại món
    if (currentPage === 'ThucDon.php') {
        const menuItems = document.querySelectorAll('.menu-loai a');
        menuItems.forEach(item => {
            const href = item.getAttribute('href');
            if (href.includes('maloaimonan=' + maLoai)) {
                item.classList.add('active');
                item.querySelector('img').style.borderColor = '#ffeb3b';
                item.querySelector('p').style.color = '#ffeb3b';
            }
        });
    }
});
</script>
</body>
</html>