// JavaScript cho hiệu ứng validation
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('checkoutForm');
    
    // Validate form khi submit
    form.addEventListener('submit', function(e) {
        let isValid = true;
        
        // Validate số điện thoại
        const sdtField = form.querySelector('input[name="sdt"]');
        if (!validateField(sdtField)) isValid = false;
        
        // Validate địa chỉ
        const diachiField = form.querySelector('textarea[name="diachi"]');
        if (!validateField(diachiField)) isValid = false;
        
        if (!isValid) {
            e.preventDefault();
            // Hiệu ứng shake cho form
            form.style.animation = 'shake 0.5s ease';
            setTimeout(() => {
                form.style.animation = '';
            }, 500);
            
            // Scroll đến field lỗi đầu tiên
            const firstError = form.querySelector('.error');
            if (firstError) {
                firstError.scrollIntoView({ 
                    behavior: 'smooth', 
                    block: 'center' 
                });
            }
            
            // Hiển thị thông báo lỗi tổng
            showGeneralError('Vui lòng kiểm tra lại thông tin bên dưới');
        }
    });
    
    // Thêm sự kiện blur để validate real-time
    const sdtField = form.querySelector('input[name="sdt"]');
    const diachiField = form.querySelector('textarea[name="diachi"]');
    
    if (sdtField) {
        sdtField.addEventListener('blur', function() {
            validateField(this);
        });
        
        sdtField.addEventListener('input', function() {
            // Xóa lỗi khi user bắt đầu nhập
            if (this.classList.contains('error')) {
                this.classList.remove('error');
                const errorElement = this.parentNode.querySelector('.field-error');
                if (errorElement) {
                    errorElement.remove();
                }
            }
        });
    }
    
    if (diachiField) {
        diachiField.addEventListener('blur', function() {
            validateField(this);
        });
        
        diachiField.addEventListener('input', function() {
            // Xóa lỗi khi user bắt đầu nhập
            if (this.classList.contains('error')) {
                this.classList.remove('error');
                const errorElement = this.parentNode.querySelector('.field-error');
                if (errorElement) {
                    errorElement.remove();
                }
            }
        });
    }
    
    function validateField(field) {
        if (!field) return true;
        
        const value = field.value.trim();
        const fieldName = field.getAttribute('name');
        let isValid = true;
        let errorMessage = '';
        
        // Xóa lỗi cũ
        field.classList.remove('error');
        const oldError = field.parentNode.querySelector('.field-error');
        if (oldError) {
            oldError.remove();
        }
        
        // Validate theo từng loại field
        if (!value) {
            isValid = false;
            errorMessage = 'Trường này là bắt buộc';
        } else if (fieldName === 'sdt') {
            const phoneRegex = /^(0|\+84)[3|5|7|8|9][0-9]{8}$/;
            if (!phoneRegex.test(value)) {
                isValid = false;
                errorMessage = 'Số điện thoại không hợp lệ';
            }
        } else if (fieldName === 'diachi' && value.length < 10) {
            isValid = false;
            errorMessage = 'Địa chỉ phải chi tiết hơn';
        }
        
        // Hiển thị lỗi nếu có
        if (!isValid) {
            field.classList.add('error');
            const errorDiv = document.createElement('div');
            errorDiv.className = 'field-error';
            errorDiv.textContent = errorMessage;
            field.parentNode.appendChild(errorDiv);
            
            // Hiệu ứng cho field lỗi
            field.style.animation = 'shake 0.3s ease';
            setTimeout(() => {
                field.style.animation = '';
            }, 300);
        }
        
        return isValid;
    }
    
    function showGeneralError(message) {
        // Xóa thông báo lỗi cũ nếu có
        const oldNotification = document.getElementById('generalError');
        if (oldNotification) {
            oldNotification.remove();
        }
        
        // Tạo thông báo lỗi mới
        const errorDiv = document.createElement('div');
        errorDiv.id = 'generalError';
        errorDiv.className = 'error-notification';
        errorDiv.innerHTML = `
            <div class="error-icon">❌</div>
            <div class="error-message">${message}</div>
            <button class="error-close" onclick="closeError('generalError')">×</button>
        `;
        
        // Chèn thông báo vào đầu form
        const contentContainer = document.querySelector('.content-container');
        const form = document.getElementById('checkoutForm');
        contentContainer.insertBefore(errorDiv, form);
        
        // Tự động đóng sau 5 giây
        setTimeout(() => {
            closeError('generalError');
        }, 5000);
    }
});

function closeError(elementId) {
    const errorElement = document.getElementById(elementId);
    if (errorElement) {
        errorElement.style.animation = 'slideDown 0.3s ease reverse';
        setTimeout(() => {
            if (errorElement.parentNode) {
                errorElement.remove();
            }
        }, 300);
    }
}

// Tự động đóng thông báo lỗi sau 5 giây
setTimeout(() => {
    const errorNotifications = document.querySelectorAll('.error-notification');
    errorNotifications.forEach(notification => {
        closeError(notification.id);
    });
}, 5000);

// Real-time validation cho số điện thoại
document.querySelector('input[name="sdt"]')?.addEventListener('input', function(e) {
    let value = e.target.value.replace(/\D/g, '');
    if (value.startsWith('84')) {
        value = '0' + value.substring(2);
    }
    if (value.length > 10) {
        value = value.substring(0, 10);
    }
    e.target.value = value;
});