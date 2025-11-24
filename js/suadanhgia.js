// Khởi tạo khi trang load
document.addEventListener('DOMContentLoaded', function() {
    initStarRating();
    initTextareaCounter();
    initFormValidation();
});

// Khởi tạo star rating - HOÀN CHỈNH
function initStarRating() {
    const stars = document.querySelectorAll('.star-rating input');
    const ratingText = document.getElementById('ratingText');
    
    const ratingTexts = {
        1: 'Rất tệ',
        2: 'Tệ',
        3: 'Bình thường', 
        4: 'Tốt',
        5: 'Rất tốt'
    };
    
    // Cập nhật text khi star thay đổi
    stars.forEach(star => {
        star.addEventListener('change', function() {
            const rating = parseInt(this.value);
            ratingText.textContent = ratingTexts[rating] || 'Chọn số sao';
            updateStarDisplay();
            
            // Thêm hiệu ứng
            ratingText.style.transform = 'scale(1.1)';
            setTimeout(() => {
                ratingText.style.transform = 'scale(1)';
            }, 200);
        });
    });
    
    // Khởi tạo trạng thái ban đầu
    updateStarDisplay();
}

// Cập nhật hiển thị sao - HOÀN CHỈNH
function updateStarDisplay() {
    const checkedStar = document.querySelector('.star-rating input:checked');
    if (checkedStar) {
        const rating = parseInt(checkedStar.value);
        const labels = document.querySelectorAll('.star-label');
        
        labels.forEach(label => {
            const labelFor = label.getAttribute('for');
            const labelRating = parseInt(labelFor.replace('star', ''));
            
            // Logic đúng: sao từ trái sang phải (1-5)
            // Chọn sao X thì tất cả sao từ 1 đến X đều sáng
            if (labelRating <= rating) {
                label.style.filter = 'grayscale(0)';
                label.style.opacity = '1';
                label.style.transform = 'scale(1.3)';
            } else {
                label.style.filter = 'grayscale(1)';
                label.style.opacity = '0.6';
                label.style.transform = 'scale(1)';
            }
        });
    }
}

// Khởi tạo đếm ký tự textarea
function initTextareaCounter() {
    const textarea = document.getElementById('noiDung');
    const charCount = document.getElementById('charCount');
    
    if (!textarea || !charCount) return;
    
    // Cập nhật số ký tự ban đầu
    updateCharCount(textarea, charCount);
    
    // Theo dõi thay đổi
    textarea.addEventListener('input', function() {
        updateCharCount(this, charCount);
    });
}

function updateCharCount(textarea, charCount) {
    const count = textarea.value.length;
    charCount.textContent = count;
    
    // Thay đổi màu sắc theo số ký tự
    charCount.className = '';
    if (count >= 450) {
        charCount.classList.add('error');
    } else if (count >= 400) {
        charCount.classList.add('warning');
    }
}

// Khởi tạo validation form
function initFormValidation() {
    const form = document.getElementById('reviewForm');
    const submitBtn = document.getElementById('submitBtn');
    
    form.addEventListener('submit', function(e) {
        const soSao = document.querySelector('input[name="soSao"]:checked');
        const noiDung = document.getElementById('noiDung');
        
        let isValid = true;
        let errorMessage = '';
        
        // Validate số sao
        if (!soSao) {
            isValid = false;
            errorMessage = 'Vui lòng chọn số sao đánh giá';
        }
        // Validate nội dung
        else if (!noiDung.value.trim()) {
            isValid = false;
            errorMessage = 'Vui lòng nhập nội dung đánh giá';
        }
        else if (noiDung.value.trim().length < 10) {
            isValid = false;
            errorMessage = 'Nội dung đánh giá phải có ít nhất 10 ký tự';
        }
        else if (noiDung.value.length > 500) {
            isValid = false;
            errorMessage = 'Nội dung đánh giá không được vượt quá 500 ký tự';
        }
        
        if (!isValid) {
            e.preventDefault();
            showError(errorMessage);
            return false;
        }
        
        // Hiển thị loading
        showLoading(submitBtn);
    });
}

// Hiển thị loading
function showLoading(button) {
    const originalText = button.innerHTML;
    button.innerHTML = '<span class="btn-icon">⏳</span> Đang lưu...';
    button.disabled = true;
    button.classList.add('btn-loading');
}

// Hiển thị lỗi
function showError(message) {
    // Xóa alert cũ nếu có
    const oldAlert = document.querySelector('.alert-danger');
    if (oldAlert) {
        oldAlert.remove();
    }
    
    // Tạo alert mới
    const alert = document.createElement('div');
    alert.className = 'alert alert-danger';
    alert.textContent = message;
    
    // Chèn vào sau form header
    const formHeader = document.querySelector('.form-header');
    formHeader.parentNode.insertBefore(alert, formHeader.nextSibling);
    
    // Tự động xóa sau 5s
    setTimeout(() => {
        if (alert.parentNode) {
            alert.remove();
        }
    }, 5000);
    
    // Hiệu ứng rung form
    const formCard = document.querySelector('.review-form-card');
    formCard.classList.add('shake');
    setTimeout(() => {
        formCard.classList.remove('shake');
    }, 500);
}

// Xác nhận xóa đánh giá
function confirmDelete() {
    if (confirm('Bạn có chắc muốn xóa đánh giá này? Hành động này không thể hoàn tác.')) {
        const maDanhGia = new URLSearchParams(window.location.search).get('id');
        
        if (maDanhGia) {
            // Thêm hiệu ứng loading
            const deleteBtn = document.querySelector('.btn-danger');
            const originalText = deleteBtn.innerHTML;
            deleteBtn.innerHTML = '<span class="btn-icon">⏳</span> Đang xóa...';
            deleteBtn.disabled = true;
            deleteBtn.classList.add('btn-loading');
            
            // Gửi request xóa
            fetch('ajax/xoa_danh_gia.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `maDanhGia=${maDanhGia}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Xóa đánh giá thành công!');
                    window.location.href = 'DanhGiaCuaToi.php';
                } else {
                    alert('Lỗi khi xóa đánh giá: ' + (data.message || 'Không xác định'));
                    resetButton(deleteBtn, originalText);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Lỗi kết nối: ' + error.message);
                resetButton(deleteBtn, originalText);
            });
        }
    }
}

function resetButton(button, originalText) {
    button.innerHTML = originalText;
    button.disabled = false;
    button.classList.remove('btn-loading');
}

// Hiệu ứng hover cho star rating
document.addEventListener('mouseover', function(e) {
    if (e.target.closest('.star-label')) {
        const hoveredLabel = e.target.closest('.star-label');
        const hoveredRating = parseInt(hoveredLabel.getAttribute('for').replace('star', ''));
        const labels = document.querySelectorAll('.star-label');
        
        labels.forEach(label => {
            const labelRating = parseInt(label.getAttribute('for').replace('star', ''));
            if (labelRating <= hoveredRating) {
                label.style.filter = 'grayscale(0)';
                label.style.opacity = '1';
                label.style.transform = 'scale(1.2)';
            }
        });
    }
});

document.addEventListener('mouseout', function(e) {
    if (e.target.closest('.star-label')) {
        updateStarDisplay();
    }
});