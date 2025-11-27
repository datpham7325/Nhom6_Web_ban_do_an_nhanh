/**
 * FILE: datsukien.js
 * MÔ TẢ: Xử lý các tương tác JavaScript cho trang đặt sự kiện
 */

// Chờ cho DOM load xong
document.addEventListener('DOMContentLoaded', function() {
    console.log('Trang đặt sự kiện đã được tải thành công');
    
    // Khởi tạo các hàm
    initFormValidation();
    initDateTimeConstraints();
    initInteractiveElements();
    initEventTypeEffects();
});

/**
 * KHỞI TẠO VALIDATION CHO FORM
 * Kiểm tra dữ liệu nhập vào form trước khi submit
 */
function initFormValidation() {
    const form = document.querySelector('.event-form');
    
    if (form) {
        form.addEventListener('submit', function(e) {
            console.log('Đang xử lý submit form sự kiện...');
            
            // Kiểm tra số điện thoại
            const sdtInput = document.querySelector('input[name="sdt"]');
            if (sdtInput && !isValidPhoneNumber(sdtInput.value)) {
                e.preventDefault();
                showError('Số điện thoại không hợp lệ. Vui lòng nhập số điện thoại Việt Nam (10-11 số)');
                sdtInput.focus();
                return;
            }
            
            // Kiểm tra email (nếu có)
            const emailInput = document.querySelector('input[name="email"]');
            if (emailInput.value && !isValidEmail(emailInput.value)) {
                e.preventDefault();
                showError('Email không hợp lệ. Vui lòng kiểm tra lại.');
                emailInput.focus();
                return;
            }
            
            // Kiểm tra số người
            const soNguoiInput = document.querySelector('input[name="songuoi"]');
            if (soNguoiInput && (soNguoiInput.value < 10 || soNguoiInput.value > 100)) {
                e.preventDefault();
                showError('Số người tham dự phải từ 10 đến 100 người');
                soNguoiInput.focus();
                return;
            }
            
            // Kiểm tra ngày giờ sự kiện
            if (!validateEventDateTime()) {
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
 * KIỂM TRA EMAIL
 * @param {string} email - Email cần kiểm tra
 * @returns {boolean} - True nếu email hợp lệ
 */
function isValidEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

/**
 * KIỂM TRA NGÀY GIỜ SỰ KIỆN
 * Đảm bảo ngày giờ sự kiện hợp lệ
 */
function validateEventDateTime() {
    const ngaySuKienInput = document.querySelector('input[name="ngaysukien"]');
    const gioBatDauInput = document.querySelector('input[name="giobatdau"]');
    const gioKetThucInput = document.querySelector('input[name="gioketthuc"]');
    
    if (!ngaySuKienInput || !gioBatDauInput || !gioKetThucInput) {
        console.log('Missing date/time inputs - skipping validation');
        return true; // Không có input thì bỏ qua validation
    }
    
    const selectedDate = new Date(ngaySuKienInput.value);
    const selectedStartTime = gioBatDauInput.value;
    const selectedEndTime = gioKetThucInput.value;
    const now = new Date();
    
    // Kiểm tra ngày không trong quá khứ và phải cách hiện tại ít nhất 3 ngày
    const minDate = new Date();
    minDate.setDate(now.getDate() + 3);
    
    if (selectedDate < minDate) {
        showError('Sự kiện cần được đặt trước ít nhất 3 ngày. Vui lòng chọn ngày sau ' + 
                 minDate.toLocaleDateString('vi-VN'));
        ngaySuKienInput.focus();
        return false;
    }
    
    // Kiểm tra giờ bắt đầu và kết thúc hợp lệ
    if (selectedStartTime >= selectedEndTime) {
        showError('Giờ kết thúc phải sau giờ bắt đầu');
        gioBatDauInput.focus();
        return false;
    }
    
    // Kiểm tra thời lượng sự kiện tối thiểu 2 giờ
    const startMinutes = timeToMinutes(selectedStartTime);
    const endMinutes = timeToMinutes(selectedEndTime);
    const duration = endMinutes - startMinutes;
    
    if (duration < 120) { // 2 giờ = 120 phút
        showError('Thời lượng sự kiện tối thiểu là 2 giờ');
        gioKetThucInput.focus();
        return false;
    }
    
    return true;
}

/**
 * CHUYỂN ĐỔI THỜI GIAN THÀNH PHÚT
 * @param {string} timeString - Chuỗi thời gian (HH:MM)
 * @returns {number} - Tổng số phút
 */
function timeToMinutes(timeString) {
    if (!timeString) return 0;
    const [hours, minutes] = timeString.split(':').map(Number);
    return hours * 60 + minutes;
}

/**
 * KHỞI TẠO RÀNG BUỘC CHO NGÀY VÀ GIỜ
 * Thiết lập min/max cho các input date và time
 */
function initDateTimeConstraints() {
    // Thiết lập ngày tối thiểu là 3 ngày sau
    const ngaySuKienInput = document.querySelector('input[name="ngaysukien"]');
    if (ngaySuKienInput) {
        const minDate = new Date();
        minDate.setDate(minDate.getDate() + 3);
        ngaySuKienInput.min = minDate.toISOString().split('T')[0];
        
        // Tự động cập nhật constraints khi ngày thay đổi
        ngaySuKienInput.addEventListener('change', function() {
            updateTimeConstraintsBasedOnDate(this.value);
        });
    }
    
    // Khởi tạo constraints cho time
    updateTimeConstraintsBasedOnDate();
}

/**
 * CẬP NHẬT RÀNG BUỘC CHO TRƯỜNG THỜI GIAN DỰA TRÊN NGÀY
 * @param {string} selectedDate - Ngày được chọn (YYYY-MM-DD)
 */
function updateTimeConstraintsBasedOnDate(selectedDate) {
    const gioBatDauInput = document.querySelector('input[name="giobatdau"]');
    const gioKetThucInput = document.querySelector('input[name="gioketthuc"]');
    
    if (!gioBatDauInput || !gioKetThucInput) return;
    
    // Reset min/max
    gioBatDauInput.min = '07:00';
    gioBatDauInput.max = '20:00';
    gioKetThucInput.min = '09:00';
    gioKetThucInput.max = '22:00';
    
    // Tự động set giờ kết thúc khi giờ bắt đầu thay đổi
    gioBatDauInput.addEventListener('change', function() {
        if (this.value) {
            const startTime = new Date('1970-01-01T' + this.value + ':00');
            const minEndTime = new Date(startTime.getTime() + 2 * 60 * 60 * 1000); // +2 giờ
            const minEndTimeString = minEndTime.toTimeString().substring(0, 5);
            
            gioKetThucInput.min = minEndTimeString > '09:00' ? minEndTimeString : '09:00';
            
            // Nếu giờ kết thúc hiện tại nhỏ hơn min, tự động cập nhật
            if (gioKetThucInput.value && gioKetThucInput.value < minEndTimeString) {
                gioKetThucInput.value = minEndTimeString;
            }
        }
    });
}

/**
 * KHỞI TẠO HIỆU ỨNG CHO LOẠI SỰ KIỆN
 * Thêm tương tác khi chọn loại sự kiện
 */
function initEventTypeEffects() {
    const loaiSuKienSelect = document.querySelector('select[name="loaisukien"]');
    const yeuCauTextarea = document.querySelector('textarea[name="yeucau"]');
    
    if (loaiSuKienSelect && yeuCauTextarea) {
        loaiSuKienSelect.addEventListener('change', function() {
            const selectedValue = this.value;
            let suggestion = '';
            
            // Gợi ý tự động dựa trên loại sự kiện
            switch(selectedValue) {
                case 'sinh_nhat':
                    suggestion = 'Gợi ý: Trang trí theo chủ đề, bánh sinh nhật, quà tặng cho trẻ em, khu vực chụp ảnh...';
                    break;
                case 'hoi_nghi':
                    suggestion = 'Gợi ý: Máy chiếu, bàn ghế hội nghị, tea break, ghi âm, wifi tốc độ cao...';
                    break;
                case 'tiec_cuoi':
                    suggestion = 'Gợi ý: Hoa cưới, bánh cưới, trang trí lãng mạn, nhạc nền, photographer...';
                    break;
                case 'gia_dinh':
                    suggestion = 'Gợi ý: Menu gia đình, khu vực vui chơi trẻ em, không gian ấm cúng, âm nhạc nhẹ nhàng...';
                    break;
                default:
                    suggestion = 'Mô tả yêu cầu cụ thể của bạn...';
            }
            
            // Hiển thị gợi ý dưới dạng placeholder
            if (suggestion) {
                yeuCauTextarea.placeholder = suggestion;
            }
        });
    }
}

/**
 * KHỞI TẠO CÁC PHẦN TỬ TƯƠNG TÁC
 * Thêm hiệu ứng và tương tác cho các element
 */
function initInteractiveElements() {
    // Hiệu ứng hover cho các item thông tin
    const infoItems = document.querySelectorAll('.info-item');
    infoItems.forEach(item => {
        item.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-5px) scale(1.02)';
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
    const emailInput = document.querySelector('input[name="email"]');
    const soNguoiInput = document.querySelector('input[name="songuoi"]');
    
    // Validation số điện thoại
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
    
    // Validation email
    if (emailInput) {
        emailInput.addEventListener('blur', function() {
            if (this.value && !isValidEmail(this.value)) {
                this.style.borderColor = '#d32f2f';
                this.style.boxShadow = '0 0 0 3px rgba(211, 47, 47, 0.1)';
            } else {
                this.style.borderColor = '#4caf50';
                this.style.boxShadow = '0 0 0 3px rgba(76, 175, 80, 0.1)';
            }
        });
    }
    
    // Validation số người
    if (soNguoiInput) {
        soNguoiInput.addEventListener('blur', function() {
            if (this.value && (this.value < 10 || this.value > 100)) {
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
        submitBtn.innerHTML = '⏳ Đang gửi yêu cầu...';
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
    const form = document.querySelector('.event-form');
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

// Thêm CSS animation cho fadeOut
const style = document.createElement('style');
style.textContent = `
    @keyframes fadeOut {
        from { opacity: 1; transform: translateY(0); }
        to { opacity: 0; transform: translateY(-20px); }
    }
    
    /* Hiệu ứng pulse cho các info item */
    @keyframes pulse {
        0% { transform: scale(1); }
        50% { transform: scale(1.05); }
        100% { transform: scale(1); }
    }
    
    .info-item:hover {
        animation: pulse 2s infinite;
    }
`;
document.head.appendChild(style);

// Export các hàm để sử dụng trong console (cho mục đích debug)
window.sukienUtils = {
    isValidPhoneNumber,
    isValidEmail,
    validateEventDateTime,
    timeToMinutes
};

console.log('JavaScript cho trang đặt sự kiện đã được tải thành công!');