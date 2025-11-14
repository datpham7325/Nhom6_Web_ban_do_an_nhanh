<?php include_once "includes/header.php"; ?>

<div class="container">
    <div class="page-header">
        <h1>LIÊN HỆ</h1>
        <p>Chúng tôi luôn lắng nghe bạn!</p>
    </div>

    <div class="content-container">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 3rem;">
            <!-- Thông tin liên hệ -->
            <div>
                <h3 style="color: #d32f2f; margin-bottom: 2rem;">THÔNG TIN LIÊN HỆ</h3>
                
                <div style="margin-bottom: 2rem;">
                    <h4 style="color: #f57c00; margin-bottom: 0.5rem;">📍 ĐỊA CHỈ</h4>
                    <p>123 Nguyễn Văn Linh, Quận 7, TP. Hồ Chí Minh</p>
                </div>

                <div style="margin-bottom: 2rem;">
                    <h4 style="color: #f57c00; margin-bottom: 0.5rem;">📞 HOTLINE</h4>
                    <p>1900 1234</p>
                </div>

                <div style="margin-bottom: 2rem;">
                    <h4 style="color: #f57c00; margin-bottom: 0.5rem;">📧 EMAIL</h4>
                    <p>contact@jollibee.vn</p>
                </div>

                <div style="margin-bottom: 2rem;">
                    <h4 style="color: #f57c00; margin-bottom: 0.5rem;">🕒 GIỜ MỞ CỬA</h4>
                    <p>Thứ 2 - Chủ Nhật: 7:00 - 22:00</p>
                </div>
            </div>

            <!-- Form liên hệ -->
            <div>
                <h3 style="color: #d32f2f; margin-bottom: 2rem;">GỬI TIN NHẮN</h3>
                
                <form style="display: flex; flex-direction: column; gap: 1rem;">
                    <input type="text" placeholder="Họ và tên" style="padding: 1rem; border: 2px solid #ffe0b2; border-radius: 8px;">
                    <input type="email" placeholder="Email" style="padding: 1rem; border: 2px solid #ffe0b2; border-radius: 8px;">
                    <input type="tel" placeholder="Số điện thoại" style="padding: 1rem; border: 2px solid #ffe0b2; border-radius: 8px;">
                    <textarea placeholder="Nội dung tin nhắn" rows="5" style="padding: 1rem; border: 2px solid #ffe0b2; border-radius: 8px;"></textarea>
                    <button type="submit" style="padding: 1rem; background: #d32f2f; color: white; border: none; border-radius: 8px; font-weight: bold; cursor: pointer;">
                        GỬI TIN NHẮN
                    </button>
                </form>
            </div>
        </div>

        <!-- Bản đồ -->
        <div style="margin-top: 3rem;">
            <h3 style="color: #d32f2f; margin-bottom: 1rem; text-align: center;">BẢN ĐỒ</h3>
            <div style="background: #f5f5f5; padding: 2rem; border-radius: 15px; text-align: center;">
                <p>📍 Bản đồ sẽ được hiển thị tại đây</p>
                <p style="color: #666; margin-top: 1rem;">Cửa hàng Jollibee Quận 7, TP. Hồ Chí Minh</p>
            </div>
        </div>
    </div>
</div>

<?php include_once "includes/footer.php"; ?>