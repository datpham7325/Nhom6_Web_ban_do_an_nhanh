document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('profileForm');
    const inputs = form.querySelectorAll('.form-input');
    const submitBtn = form.querySelector('button[type="submit"]');

    // Validation rules
    const validationRules = {
        hoten: {
            required: true,
            minLength: 2,
            maxLength: 50,
            message: 'Họ tên phải từ 2 đến 50 ký tự'
        },
        email: {
            required: true,
            pattern: /^[^\s@]+@[^\s@]+\.[^\s@]+$/,
            message: 'Email không hợp lệ'
        },
        sodienthoai: {
            required: true,
            pattern: /^[0-9]{10,11}$/,
            message: 'Số điện thoại phải có 10-11 chữ số'
        },
        diachi: {
            required: false,
            maxLength: 200,
            message: 'Địa chỉ không được quá 200 ký tự'
        }
    };

    // Real-time validation
    inputs.forEach(input => {
        input.addEventListener('blur', function() {
            validateField(this);
        });

        input.addEventListener('input', function() {
            // Clear error khi user bắt đầu nhập
            if (this.classList.contains('error')) {
                clearError(this);
            }
        });
    });

    // Form submission
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        let isValid = true;
        inputs.forEach(input => {
            if (!validateField(input)) {
                isValid = false;
            }
        });

        if (isValid) {
            submitForm();
        } else {
            // Hiệu ứng shake cho form khi có lỗi
            form.classList.add('shake');
            setTimeout(() => {
                form.classList.remove('shake');
            }, 500);
        }
    });

    function validateField(field) {
        const fieldName = field.name;
        const value = field.value.trim();
        const rules = validationRules[fieldName];
        const errorElement = document.getElementById(`${fieldName}-error`);

        // Clear previous error
        clearError(field);

        // Check required field
        if (rules.required && !value) {
            showError(field, errorElement, 'Trường này là bắt buộc');
            return false;
        }

        // Check min length
        if (rules.minLength && value.length < rules.minLength) {
            showError(field, errorElement, rules.message);
            return false;
        }

        // Check max length
        if (rules.maxLength && value.length > rules.maxLength) {
            showError(field, errorElement, rules.message);
            return false;
        }

        // Check pattern
        if (rules.pattern && value && !rules.pattern.test(value)) {
            showError(field, errorElement, rules.message);
            return false;
        }

        // Valid field
        field.classList.add('success');
        field.classList.remove('error');
        return true;
    }

    function showError(field, errorElement, message) {
        field.classList.add('error');
        field.classList.remove('success');
        
        // Hiệu ứng shake cho field bị lỗi
        field.classList.add('shake');
        setTimeout(() => {
            field.classList.remove('shake');
        }, 500);
        
        if (errorElement) {
            errorElement.textContent = message;
            errorElement.classList.add('show');
        }
    }

    function clearError(field) {
        field.classList.remove('error');
        field.classList.remove('success');
        const errorElement = document.getElementById(`${field.name}-error`);
        if (errorElement) {
            errorElement.classList.remove('show');
            errorElement.textContent = '';
        }
    }

    function submitForm() {
        // Disable submit button and show loading
        const originalText = submitBtn.innerHTML;
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="loading"></span> Đang cập nhật...';

        // Submit the form thực tế (không có timeout giả)
        form.submit();
    }

    // Add input formatting
    const phoneInput = form.querySelector('input[name="sodienthoai"]');
    if (phoneInput) {
        phoneInput.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            e.target.value = value;
            
            // Auto validate khi nhập
            if (value.length === 10 || value.length === 11) {
                validateField(this);
            }
        });
    }

    const nameInput = form.querySelector('input[name="hoten"]');
    if (nameInput) {
        nameInput.addEventListener('input', function(e) {
            // Allow only letters, spaces, and Vietnamese characters
            let value = e.target.value.replace(/[^a-zA-ZÀ-ỹ\s]/g, '');
            e.target.value = value;
        });
    }

    // Auto validate email khi mất focus
    const emailInput = form.querySelector('input[name="email"]');
    if (emailInput) {
        emailInput.addEventListener('blur', function() {
            if (this.value.trim()) {
                validateField(this);
            }
        });
    }

    // Hiển thị success message với hiệu ứng nếu có
    const successAlert = document.querySelector('.alert-success');
    if (successAlert) {
        // Thêm hiệu ứng cho success message
        successAlert.style.animation = 'slideIn 0.5s ease-out';
        
        // Tạo hiệu ứng confetti cho thành công
        createConfetti();
        
        // Tự động ẩn success message sau 5 giây
        setTimeout(() => {
            successAlert.style.opacity = '0';
            successAlert.style.transform = 'translateY(-20px)';
            successAlert.style.transition = 'all 0.5s ease';
            setTimeout(() => {
                if (successAlert.parentNode) {
                    successAlert.parentNode.removeChild(successAlert);
                }
            }, 500);
        }, 5000);
    }

    // Confetti effect cho thành công
    function createConfetti() {
        const celebration = document.createElement('div');
        celebration.className = 'success-celebration';
        document.body.appendChild(celebration);

        const colors = ['#667eea', '#764ba2', '#48bb78', '#ed8936', '#e53e3e', '#38b2ac'];
        
        for (let i = 0; i < 50; i++) {
            const confetti = document.createElement('div');
            confetti.className = 'confetti';
            confetti.style.left = Math.random() * 100 + 'vw';
            confetti.style.backgroundColor = colors[Math.floor(Math.random() * colors.length)];
            confetti.style.animationDuration = (Math.random() * 3 + 2) + 's';
            confetti.style.opacity = Math.random() + 0.5;
            celebration.appendChild(confetti);
        }

        // Xóa confetti sau khi animation kết thúc
        setTimeout(() => {
            if (celebration.parentNode) {
                celebration.parentNode.removeChild(celebration);
            }
        }, 3000);
    }
});