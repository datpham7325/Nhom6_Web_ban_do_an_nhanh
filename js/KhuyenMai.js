document.addEventListener('DOMContentLoaded', function() {
    const addToCartForms = document.querySelectorAll('.add-to-cart-form');
    
    // Xử lý thêm vào giỏ hàng với loading state
    addToCartForms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const button = this.querySelector('button[type="submit"]');
            const originalText = button.innerHTML;
            
            // Hiển thị loading
            button.innerHTML = '<span class="loading"></span> Đang thêm...';
            button.disabled = true;
            
            // Cho phép form submit sau khi hiển thị loading
            setTimeout(() => {
                button.innerHTML = originalText;
                button.disabled = false;
            }, 1500);
        });
    });
    
    // Hiệu ứng hover cho các card
    const promoCards = document.querySelectorAll('.promo-card');
    promoCards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-10px)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
        });
    });
    
    // Hiệu ứng cho featured promo
    const featuredPromo = document.querySelector('.featured-promo');
    if (featuredPromo) {
        featuredPromo.addEventListener('mouseenter', function() {
            this.style.transform = 'scale(1.02)';
        });
        
        featuredPromo.addEventListener('mouseleave', function() {
            this.style.transform = 'scale(1)';
        });
    }
    
    // Tự động ẩn thông báo sau 5 giây
    const alert = document.querySelector('.alert');
    if (alert) {
        setTimeout(() => {
            alert.style.opacity = '0';
            alert.style.transform = 'translateY(-20px)';
            alert.style.transition = 'all 0.5s ease';
            setTimeout(() => {
                if (alert.parentNode) {
                    alert.parentNode.removeChild(alert);
                }
            }, 500);
        }, 5000);
    }
});