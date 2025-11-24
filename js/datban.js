/**
 * FILE: datban.js
 * MÔ TẢ: Xử lý các tương tác JavaScript cho trang đặt bàn
 * TÁC GIẢ: [Your Name]
 * NGÀY: [Current Date]
 */

// Chờ cho DOM load xong
document.addEventListener('DOMContentLoaded', function() {
    console.log('Trang đặt bàn đã được tải thành công');
    
    // Khởi tạo các hàm
    initFormValidation();
    initDateTimeConstraints();
    initInteractiveElements();
    initPrintFunctionality();
});

/**
 * KHỞI TẠO VALIDATION CHO FORM
 * Kiểm tra dữ liệu nhập vào form trước khi submit
 */
function initFormValidation() {
    const form = document.querySelector('.booking-form');
    
    if (form) {
        form.addEventListener('submit', function(e) {
            console.log('Đang xử lý submit form...');
            
            // Kiểm tra số điện thoại
            const sdtInput = document.querySelector('input[name="sdt"]');
            if (sdtInput && !isValidPhoneNumber(sdtInput.value)) {
                e.preventDefault();
                showError('Số điện thoại không hợp lệ. Vui lòng nhập số điện thoại Việt Nam (10-11 số)');
                sdtInput.focus();
                return;
            }
            
            // Kiểm tra ngày giờ đặt
            if (!validateBookingDateTime()) {
                e.preventDefault();
                return;
            }
            
            // Hiển thị loading khi submit
            showLoadingState();
        });
    }
}

/**
 * KIỂM TRA SỐ ĐIỆN THOẠI
 * @param {string} phone - Số điện thoại cần kiểm tra
 * @returns {boolean} - True nếu số điện thoại hợp lệ
 */
function isValidPhoneNumber(phone) {
    // Regex cho số điện thoại Việt Nam
    const phoneRegex = /^(0[3|5|7|8|9])+([0-9]{8,9})$/;
    return phoneRegex.test(phone.replace(/\s/g, ''));
}

/**
 * KIỂM TRA NGÀY GIỜ ĐẶT BÀN
 * Đảm bảo ngày giờ đặt hợp lệ (không trong quá khứ, trong giờ mở cửa)
 */
function validateBookingDateTime() {
    const ngayDatInput = document.querySelector('input[name="ngaydat"]');
    const gioDatInput = document.querySelector('input[name="giodat"]');
    
    if (!ngayDatInput || !gioDatInput) return true;
    
    const selectedDate = new Date(ngayDatInput.value);
    const selectedTime = gioDatInput.value;
    const now = new Date();
    
    // Kiểm tra ngày không trong quá khứ
    if (selectedDate < new Date(now.toDateString())) {
        showError('Không thể đặt bàn trong quá khứ. Vui lòng chọn ngày trong tương lai.');
        ngayDatInput.focus();
        return false;
    }
    
    // Kiểm tra giờ đặt trong giờ mở cửa
    if (selectedTime < '07:00' || selectedTime > '22:00') {
        showError('Giờ đặt bàn phải trong khoảng 7:00 - 22:00');
        gioDatInput.focus();
        return false;
    }
    
    // Kiểm tra đặt trước 2 giờ nếu là ngày hôm nay
    if (selectedDate.toDateString() === now.toDateString()) {
        const selectedDateTime = new Date(selectedDate.toDateString() + ' ' + selectedTime);
        const timeDiff = (selectedDateTime - now) / (1000 * 60 * 60); // Chênh lệch giờ
        
        if (timeDiff < 2) {
            showError('Cần đặt bàn trước ít nhất 2 giờ. Vui lòng chọn giờ muộn hơn.');
            gioDatInput.focus();
            return false;
        }
    }
    
    return true;
}

/**
 * KHỞI TẠO RÀNG BUỘC CHO NGÀY VÀ GIỜ
 * Thiết lập min/max cho các input date và time
 */
function initDateTimeConstraints() {
    // Thiết lập ngày tối thiểu là hôm nay
    const ngayDatInput = document.querySelector('input[name="ngaydat"]');
    if (ngayDatInput) {
        const today = new Date().toISOString().split('T')[0];
        ngayDatInput.min = today;
        
        // Tự động cập nhật min time khi ngày thay đổi
        ngayDatInput.addEventListener('change', function() {
            updateTimeConstraints(this.value);
        });
    }
    
    // Khởi tạo constraints cho time
    updateTimeConstraints();
}

/**
 * CẬP NHẬT RÀNG BUỘC CHO TRƯỜNG THỜI GIAN
 * @param {string} selectedDate - Ngày được chọn (YYYY-MM-DD)
 */
function updateTimeConstraints(selectedDate) {
    const gioDatInput = document.querySelector('input[name="giodat"]');
    const now = new Date();
    const today = now.toISOString().split('T')[0];
    
    if (!gioDatInput) return;
    
    // Nếu chọn ngày hôm nay, set min time là thời gian hiện tại + 2 tiếng
    if (selectedDate === today) {
        const minTime = new Date(now.getTime() + 2 * 60 * 60 * 1000);
        const minTimeString = minTime.toTimeString().substring(0, 5);
        gioDatInput.min = minTimeString > '07:00' ? minTimeString : '07:00';
    } else {
        gioDatInput.min = '07:00';
    }
    
    gioDatInput.max = '22:00';
}

/**
 * KHỞI TẠO CÁC PHẦN TỬ TƯƠNG TÁC
 * Thêm hiệu ứng và tương tác cho các element
 */
function initInteractiveElements() {
    // Hiệu ứng hover cho các item dịch vụ
    const occasionItems = document.querySelectorAll('.occasion-item');
    occasionItems.forEach(item => {
        item.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-5px) scale(1.05)';
        });
        
        item.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0) scale(1)';
        });
    });
    
    // Auto-format số điện thoại
    const sdtInput = document.querySelector('input[name="sdt"]');
    if (sdtInput) {
        sdtInput.addEventListener('input', function(e) {
            this.value = this.value.replace(/[^0-9]/g, '');
        });
    }
    
    // Thêm real-time validation
    addRealTimeValidation();
}

/**
 * THÊM REAL-TIME VALIDATION
 * Hiển thị feedback ngay lập tức khi user nhập
 */
function addRealTimeValidation() {
    const sdtInput = document.querySelector('input[name="sdt"]');
    
    if (sdtInput) {
        sdtInput.addEventListener('blur', function() {
            if (this.value && !isValidPhoneNumber(this.value)) {
                this.style.borderColor = '#d32f2f';
                this.style.boxShadow = '0 0 0 3px rgba(211, 47, 47, 0.1)';
            } else {
                this.style.borderColor = '#4caf50';
                this.style.boxShadow = '0 0 0 3px rgba(76, 175, 80, 0.1)';
            }
        });
    }
}

/**
 * HIỂN THỊ TRẠNG THÁI LOADING
 * Hiển thị loading khi form đang được xử lý
 */
function showLoadingState() {
    const submitBtn = document.querySelector('.submit-btn');
    if (submitBtn) {
        const originalText = submitBtn.innerHTML;
        submitBtn.innerHTML = '⏳ Đang xử lý...';
        submitBtn.disabled = true;
        
        // Khôi phục trạng thái ban đầu sau 5 giây (phòng trường hợp có lỗi)
        setTimeout(() => {
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        }, 5000);
    }
}

/**
 * HIỂN THỊ THÔNG BÁO LỖI
 * @param {string} message - Nội dung thông báo lỗi
 */
function showError(message) {
    // Tạo element thông báo lỗi
    const errorDiv = document.createElement('div');
    errorDiv.className = 'alert alert-error';
    errorDiv.innerHTML = message;
    errorDiv.style.animation = 'fadeIn 0.3s ease-out';
    
    // Thêm vào trước form
    const form = document.querySelector('.booking-form');
    if (form) {
        const existingAlert = form.querySelector('.alert');
        if (existingAlert) {
            existingAlert.remove();
        }
        form.insertBefore(errorDiv, form.firstChild);
        
        // Tự động xóa sau 5 giây
        setTimeout(() => {
            if (errorDiv.parentNode) {
                errorDiv.style.animation = 'fadeOut 0.3s ease-out';
                setTimeout(() => errorDiv.remove(), 300);
            }
        }, 5000);
    }
}

/**
 * KHỞI TẠO CHỨC NĂNG IN
 * Thêm chức năng in thông tin đặt bàn
 */
function initPrintFunctionality() {
    // Có thể thêm nút in và xử lý in trang
    console.log('Chức năng in đã sẵn sàng');
}

/**
 * FORMAT SỐ ĐIỆN THOẠI
 * @param {string} phone - Số điện thoại cần format
 * @returns {string} - Số điện thoại đã được format
 */
function formatPhoneNumber(phone) {
    const cleaned = phone.replace(/\D/g, '');
    const match = cleaned.match(/^(0[3|5|7|8|9])(\d{4})(\d{4})$/);
    if (match) {
        return match[1] + ' ' + match[2] + ' ' + match[3];
    }
    return phone;
}

// Thêm CSS animation cho fadeOut
const style = document.createElement('style');
style.textContent = `
    @keyframes fadeOut {
        from { opacity: 1; transform: translateY(0); }
        to { opacity: 0; transform: translateY(-20px); }
    }
`;
document.head.appendChild(style);

// Export các hàm để sử dụng trong console (cho mục đích debug)
window.datbanUtils = {
    isValidPhoneNumber,
    formatPhoneNumber,
    validateBookingDateTime
};

console.log('JavaScript cho trang đặt bàn đã được tải thành công!');