// JavaScript cho form Tạo Đánh Giá
document.addEventListener('DOMContentLoaded', function() {
    console.log('Trang tạo đánh giá đã tải xong.');
    
    const reviewForm = document.getElementById('reviewForm');
    if (reviewForm) {
        reviewForm.addEventListener('submit', handleReviewSubmit);
    }
});

function initializeRatingInteractions() {
    // Logic tương tác UI rating
}

/**
 * Xử lý sự kiện submit form đánh giá.
 * @param {Event} e 
 */
function handleReviewSubmit(e) {
    e.preventDefault();
    
    const form = e.target;
    const submitBtn = document.getElementById('submitBtn');
    const originalBtnText = submitBtn.innerHTML; 
    const maDonHang = form.elements['maDonHang'].value;
    
    let hasReview = false;
    let reviewData = [];

    // Thu thập dữ liệu đánh giá
    document.querySelectorAll('.review-item:not(.reviewed)').forEach(item => {
        const maMonAn = item.getAttribute('data-ma-mon-an');
        const ratingElement = item.querySelector(`input[name="rating[${maMonAn}]"]:checked`);
        const noidungElement = item.querySelector(`textarea[name="noidung[${maMonAn}]"]`);

        if (ratingElement) {
            hasReview = true;
            const diem = parseInt(ratingElement.value);
            const noidung = noidungElement ? noidungElement.value.trim() : '';

            reviewData.push({
                maMonAn: maMonAn,
                diem: diem,
                noidung: noidung
            });
        }
    });
    
    if (!hasReview) {
        showNotification('⚠️ Vui lòng chọn số sao cho ít nhất một món ăn.', 'warning');
        return;
    }

    addLoadingEffect(submitBtn);

    const formData = new URLSearchParams();
    formData.append('maDonHang', maDonHang);
    formData.append('reviews', JSON.stringify(reviewData)); 

    // Gửi request AJAX
    fetch('ajax/xu_ly_tao_danh_gia.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: formData.toString()
    })
    .then(response => {
        const contentType = response.headers.get("content-type");
        if (contentType && contentType.indexOf("application/json") !== -1) {
            return response.json();
        } else {
            // Ném lỗi để bắt ở khối catch
            throw new Error("Phản hồi không phải định dạng JSON.");
        }
    })
    .then(data => {
        if(data.success) {
            showNotification('✅ Đã gửi đánh giá thành công! Đang chuyển hướng...', 'success');
            
            // 🔥 CHUYỂN HƯỚNG ĐẾN TRANG DANHGIA.PHP
            setTimeout(() => {
                // Thêm thông báo thành công vào session của DanhGia.php (cần SetSessionAndRedirect.php)
                // Hoặc đơn giản là chuyển hướng và rely vào code DanhGia.php để hiển thị đánh giá mới (cho_duyet)
                window.location.href = 'DanhGia.php'; 
            }, 1000); 
            
        } else {
            showNotification('❌ ' + (data.message || 'Lỗi không xác định khi gửi đánh giá.'), 'error');
            removeLoadingEffect(submitBtn, originalBtnText);
        }
    })
    .catch(error => {
        console.error('Lỗi fetch:', error);
        showNotification('❌ Lỗi kết nối mạng hoặc server không phản hồi JSON: ' + error.message, 'error');
        removeLoadingEffect(submitBtn, originalBtnText);
    });
}

// --- Các hàm phụ trợ ---
function addLoadingEffect(button) {
    const originalText = button.innerHTML;
    button.setAttribute('data-original-text', originalText);
    
    button.innerHTML = '<span class="btn-icon">⏳</span> Đang gửi...';
    button.classList.add('btn-loading');
    button.disabled = true;
}

function removeLoadingEffect(button, originalText = null) {
    const text = originalText || button.getAttribute('data-original-text');
    if (text) {
        button.innerHTML = text;
    }
    button.classList.remove('btn-loading');
    button.disabled = false;
}

function showNotification(message, type = 'info') {
    const oldNotifications = document.querySelectorAll('.notification');
    oldNotifications.forEach(notif => notif.remove());
    
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.innerHTML = `<div class="notification-content">
        <span class="notification-message">${message}</span>
        <button class="notification-close" onclick="this.parentElement.parentElement.remove()">×</button>
    </div>`;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        if (notification.parentElement) {
            notification.remove();
        }
    }, 5000);
}

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        const modals = document.querySelectorAll('.modal-overlay');
        modals.forEach(modal => {
            modal.remove();
            document.body.style.overflow = '';
        });
    }
});