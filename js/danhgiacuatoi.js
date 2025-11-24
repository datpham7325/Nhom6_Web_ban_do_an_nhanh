// JavaScript cho trang đánh giá của tôi
document.addEventListener('DOMContentLoaded', function() {
    console.log('Trang đánh giá đã tải xong');
    initializePage();
});

function initializePage() {
    addReviewCardAnimations();
}

// Hiệu ứng cho thẻ đánh giá
function addReviewCardAnimations() {
    const reviewCards = document.querySelectorAll('.review-card');
    
    reviewCards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-5px)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
        });
    });
    
    // Hiệu ứng xuất hiện cho các card
    reviewCards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        
        setTimeout(() => {
            card.style.transition = 'all 0.5s ease';
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, index * 200);
    });
}

// MODAL XÁC NHẬN XÓA ĐÁNH GIÁ
function showDeleteConfirmModal(reviewId) {
    const modal = document.createElement('div');
    modal.className = 'modal-overlay';
    modal.innerHTML = `
        <div class="modal-content confirm-modal">
            <div class="modal-header">
                <h3>Xác nhận xóa đánh giá</h3>
                <button class="modal-close" onclick="closeModal(this)">×</button>
            </div>
            <div class="modal-body">
                <div class="warning-icon">⚠️</div>
                <p>Bạn có chắc chắn muốn xóa đánh giá này?</p>
                <p class="warning-text">Hành động này không thể hoàn tác!</p>
            </div>
            <div class="modal-actions">
                <button class="btn-delete-confirm" onclick="confirmDeleteReview(${reviewId}, this)">
                    <span class="btn-icon">🗑️</span>
                    Xác nhận xóa
                </button>
                <button class="btn-back" onclick="closeModal(this)">
                    <span class="btn-icon">↩️</span>
                    Quay lại
                </button>
            </div>
        </div>
    `;
    
    document.body.appendChild(modal);
    document.body.style.overflow = 'hidden';
}

// Đóng modal
function closeModal(button) {
    const modal = button.closest('.modal-overlay');
    if (modal) {
        modal.remove();
        document.body.style.overflow = '';
    }
}

// Xác nhận xóa đánh giá
function confirmDeleteReview(reviewId, button) {
    console.log('Xác nhận xóa đánh giá:', reviewId);
    
    // Hiển thị loading trong modal
    addLoadingEffect(button);
    
    // Gửi request xóa đánh giá
    fetch('ajax/xoa_danh_gia.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'maDanhGia=' + reviewId
    })
    .then(response => {
        console.log('Response status:', response.status);
        return response.text();
    })
    .then(text => {
        console.log('Raw response từ server:', text);
        
        try {
            const data = JSON.parse(text);
            console.log('Dữ liệu JSON:', data);
            
            if(data.success) {
                // Hiển thị thông báo thành công
                showNotification('✅ Xóa đánh giá thành công!', 'success');
                closeModal(button);
                
                // Reload trang sau 1.5 giây
                setTimeout(() => {
                    location.reload();
                }, 1500);
            } else {
                // Hiển thị thông báo lỗi
                showNotification('❌ ' + data.message, 'error');
                removeLoadingEffect(button, '<span class="btn-icon">🗑️</span> Xác nhận xóa');
            }
        } catch (e) {
            console.error('Lỗi parse JSON:', e);
            console.error('Nội dung response:', text);
            showNotification('❌ Lỗi xử lý dữ liệu từ server', 'error');
            removeLoadingEffect(button, '<span class="btn-icon">🗑️</span> Xác nhận xóa');
        }
    })
    .catch(error => {
        console.error('Lỗi fetch:', error);
        showNotification('❌ Lỗi kết nối: ' + error.message, 'error');
        removeLoadingEffect(button, '<span class="btn-icon">🗑️</span> Xác nhận xóa');
    });
}

// Hàm xóa đánh giá (gọi modal)
function deleteReview(reviewId) {
    showDeleteConfirmModal(reviewId);
}

// Hàm sửa đánh giá
function editReview(reviewId) {
    // Thêm hiệu ứng loading
    const button = event.target.closest('.btn-edit');
    const originalText = button.innerHTML;
    button.innerHTML = '<span class="btn-icon">⏳</span> Đang chuyển...';
    button.disabled = true;
    button.classList.add('btn-loading');
    
    // Chuyển hướng đến trang chỉnh sửa đánh giá
    setTimeout(() => {
        window.location.href = 'SuaDanhGia.php?id=' + reviewId;
    }, 500);
}

// Thêm hiệu ứng loading cho button
function addLoadingEffect(button) {
    const originalText = button.innerHTML;
    button.setAttribute('data-original-text', originalText);
    
    button.innerHTML = '<span class="btn-icon">⏳</span> Đang xử lý...';
    button.classList.add('btn-loading');
    button.disabled = true;
}

// Xóa hiệu ứng loading
function removeLoadingEffect(button, originalText = null) {
    const text = originalText || button.getAttribute('data-original-text');
    if (text) {
        button.innerHTML = text;
    }
    button.classList.remove('btn-loading');
    button.disabled = false;
}

// Hiển thị thông báo
function showNotification(message, type = 'info') {
    // Xóa thông báo cũ nếu có
    const oldNotifications = document.querySelectorAll('.notification');
    oldNotifications.forEach(notif => notif.remove());
    
    // Tạo thông báo mới
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.innerHTML = `
        <div class="notification-content">
            <span class="notification-message">${message}</span>
            <button class="notification-close" onclick="this.parentElement.parentElement.remove()">×</button>
        </div>
    `;
    
    document.body.appendChild(notification);
    
    // Tự động xóa sau 5s
    setTimeout(() => {
        if (notification.parentElement) {
            notification.remove();
        }
    }, 5000);
}

// Xử lý phím ESC để đóng modal
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        const modals = document.querySelectorAll('.modal-overlay');
        modals.forEach(modal => {
            modal.remove();
            document.body.style.overflow = '';
        });
    }
});

// Xử lý click outside modal để đóng
document.addEventListener('click', function(e) {
    if (e.target.classList.contains('modal-overlay')) {
        e.target.remove();
        document.body.style.overflow = '';
    }
});