document.addEventListener('DOMContentLoaded', function() {
    const voucherForms = document.querySelectorAll('.voucher-form');
    const manualForm = document.querySelector('.voucher-input-form');
    
    // Xử lý click vào các nút voucher
    voucherForms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const button = this.querySelector('.btn-voucher');
            const originalText = button.innerHTML;
            
            // Hiển thị loading
            button.innerHTML = '<span class="loading"></span> Đang áp dụng...';
            button.disabled = true;
            
            // Tự động submit sau 1 giây để hiển thị loading
            setTimeout(() => {
                this.submit();
            }, 1000);
        });
    });
    
    // Xử lý form nhập mã thủ công
    if (manualForm) {
        manualForm.addEventListener('submit', function(e) {
            const input = this.querySelector('input[name="voucher_code"]');
            const button = this.querySelector('button');
            const voucherCode = input.value.trim();
            
            if (!voucherCode) {
                e.preventDefault();
                input.classList.add('error');
                showError(input, 'Vui lòng nhập mã giảm giá');
                return;
            }
            
            // Hiển thị loading
            const originalText = button.innerHTML;
            button.innerHTML = '<span class="loading"></span> Đang kiểm tra...';
            button.disabled = true;
        });
        
        // Clear error khi user bắt đầu nhập
        const manualInput = manualForm.querySelector('input[name="voucher_code"]');
        manualInput.addEventListener('input', function() {
            this.classList.remove('error');
            clearError(this);
        });
    }
    
    // Hiệu ứng cho active voucher
    const activeVoucher = document.querySelector('.active-voucher');
    if (activeVoucher) {
        // Thêm hiệu ứng hover cho active voucher
        activeVoucher.addEventListener('mouseenter', function() {
            this.style.transform = 'scale(1.02)';
        });
        
        activeVoucher.addEventListener('mouseleave', function() {
            this.style.transform = 'scale(1)';
        });
    }
    
    // Hiển thị thông báo lỗi
    function showError(input, message) {
        // Xóa lỗi cũ
        clearError(input);
        
        // Tạo element lỗi mới
        const errorElement = document.createElement('div');
        errorElement.className = 'error-message';
        errorElement.textContent = message;
        errorElement.style.color = '#d32f2f';
        errorElement.style.fontSize = '0.9rem';
        errorElement.style.marginTop = '0.5rem';
        errorElement.style.textAlign = 'left';
        
        input.parentNode.insertBefore(errorElement, input.nextSibling);
    }
    
    // Xóa thông báo lỗi
    function clearError(input) {
        const existingError = input.parentNode.querySelector('.error-message');
        if (existingError) {
            existingError.remove();
        }
    }
});