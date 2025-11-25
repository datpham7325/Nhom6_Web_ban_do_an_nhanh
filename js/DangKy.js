// JavaScript for DangKy.php

document.addEventListener('DOMContentLoaded', function() {
    initRegisterPage();
    initFormValidation();
    initSuccessModal();
    initInputEffects();
});

// Khởi tạo trang đăng ký
function initRegisterPage() {
    console.log('📝 Đang khởi tạo trang đăng ký...');
    
    // Thêm hiệu ứng loading cho nút đăng ký
    const registerBtn = document.getElementById('btnRegister');
    if (registerBtn) {
        registerBtn.addEventListener('click', function() {
            // Kiểm tra form hợp lệ trước khi thêm loading
            if (validateForm()) {
                this.classList.add('btn-loading');
                setTimeout(() => {
                    this.classList.remove('btn-loading');
                }, 2000);
            }
        });
    }
}

// Khởi tạo validation cho form
function initFormValidation() {
    const form = document.getElementById('registerForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            if (!validateForm()) {
                e.preventDefault();
            }
        });
    }
    
    // Real-time validation
    const inputs = form.querySelectorAll('input[required]');
    inputs.forEach(input => {
        input.addEventListener('blur', validateField);
        input.addEventListener('input', clearFieldError);
    });
}

// Validate toàn bộ form
function validateForm() {
    let isValid = true;
    const form = document.getElementById('registerForm');
    const inputs = form.querySelectorAll('input[required]');
    
    inputs.forEach(input => {
        if (!validateField({ target: input })) {
            isValid = false;
        }
    });
    
    // Kiểm tra mật khẩu trùng khớp
    const password = form.querySelector('input[name="password"]');
    const confirmP = form.querySelector('input[name="confirmP"]');
    
    if (password && confirmP && password.value !== confirmP.value) {
        showFieldError(confirmP, 'Mật khẩu xác nhận không trùng khớp');
        isValid = false;
    }
    
    return isValid;
}

// Validate từng field
function validateField(e) {
    const field = e.target;
    const value = field.value.trim();
    let isValid = true;
    
    clearFieldError({ target: field });
    
    // Kiểm tra trường bắt buộc
    if (!value) {
        showFieldError(field, 'Trường này là bắt buộc');
        return false;
    }
    
    // Kiểm tra theo từng loại field
    switch(field.name) {
        case 'email':
            if (!isValidEmail(value)) {
                showFieldError(field, 'Email không hợp lệ');
                isValid = false;
            }
            break;
            
        case 'sdt':
            if (!isValidPhone(value)) {
                showFieldError(field, 'Số điện thoại phải từ 10-11 số');
                isValid = false;
            }
            break;
            
        case 'password':
            if (value.length < 6) {
                showFieldError(field, 'Mật khẩu phải có ít nhất 6 ký tự');
                isValid = false;
            }
            break;
    }
    
    return isValid;
}

// Hiển thị lỗi cho field
function showFieldError(field, message) {
    // Xóa lỗi cũ
    clearFieldError({ target: field });
    
    // Thêm style lỗi
    field.style.borderColor = '#dc3545';
    field.style.background = '#fff5f5';
    
    // Tạo element thông báo lỗi
    const errorDiv = document.createElement('div');
    errorDiv.className = 'field-error';
    errorDiv.textContent = message;
    
    // Chèn sau field
    field.parentNode.insertBefore(errorDiv, field.nextSibling);
}

// Xóa lỗi field
function clearFieldError(e) {
    const field = e.target;
    field.style.borderColor = '';
    field.style.background = '';
    
    // Xóa thông báo lỗi
    const errorDiv = field.nextSibling;
    if (errorDiv && errorDiv.className === 'field-error') {
        errorDiv.remove();
    }
}

// Kiểm tra email hợp lệ
function isValidEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

// Kiểm tra số điện thoại hợp lệ
function isValidPhone(phone) {
    const phoneRegex = /^[0-9]{10,11}$/;
    return phoneRegex.test(phone);
}

// Khởi tạo modal thành công
function initSuccessModal() {
    const modal = document.getElementById('successModal');
    if (modal && modal.classList.contains('show')) {
        console.log('✅ Hiển thị modal thành công');
        
        // Tự động chuyển hướng sau 5 giây
        const redirectTimer = setTimeout(() => {
            redirectToLogin();
        }, 5000);
        
        // Đóng modal khi click bên ngoài
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                clearTimeout(redirectTimer);
                redirectToLogin();
            }
        });
        
        // Cập nhật thời gian đếm ngược
        updateCountdown(5);
    }
}

// Cập nhật đếm ngược
function updateCountdown(seconds) {
    const modalMessage = document.querySelector('.modal-message');
    if (modalMessage) {
        const originalMessage = 'Tài khoản của bạn đã được tạo thành công. Bạn sẽ được chuyển đến trang đăng nhập.';
        modalMessage.textContent = `${originalMessage} (${seconds}s)`;
        
        if (seconds > 0) {
            setTimeout(() => {
                updateCountdown(seconds - 1);
            }, 1000);
        }
    }
}

// Chuyển hướng đến trang đăng nhập
function redirectToLogin() {
    console.log('🔄 Đang chuyển hướng đến trang đăng nhập...');
    window.location.href = 'DangNhap.php';
}

// Hiệu ứng cho các input
function initInputEffects() {
    const inputs = document.querySelectorAll('input');
    inputs.forEach(input => {
        input.addEventListener('focus', function() {
            this.style.transform = 'scale(1.02)';
            this.style.boxShadow = '0 0 10px rgba(102, 126, 234, 0.3)';
        });
        
        input.addEventListener('blur', function() {
            this.style.transform = 'scale(1)';
            this.style.boxShadow = 'none';
        });
    });
}

// Xử lý sự kiện trước khi rời trang
window.addEventListener('beforeunload', function() {
    const modal = document.getElementById('successModal');
    if (modal && modal.classList.contains('show')) {
        console.log('🚪 Người dùng đang rời trang đăng ký');
    }
});

console.log('🚀 JavaScript cho trang đăng ký đã sẵn sàng!');