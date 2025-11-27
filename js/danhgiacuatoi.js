document.addEventListener('DOMContentLoaded', function() {
    console.log('Trang đánh giá đã tải xong');
    initializePage();
    // Bắt sự kiện click để đóng modal khi click ra ngoài
    document.addEventListener('click', handleOutsideClick);
    // Bắt sự kiện phím ESC để đóng modal
    document.addEventListener('keydown', handleEscapeKey);
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
            this.style.boxShadow = '0 6px 15px rgba(0, 0, 0, 0.1)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
            this.style.boxShadow = '0 4px 10px rgba(0, 0, 0, 0.05)';
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
        }, index * 100); // Giảm thời gian trễ cho mượt hơn
    });
}

// MODAL XÁC NHẬN XÓA ĐÁNH GIÁ (Hàm này được gọi từ deleteReview)
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

// Xác nhận xóa đánh giá (Gửi AJAX POST)
function confirmDeleteReview(reviewId, button) {
    // Hiển thị loading trong modal
    addLoadingEffect(button, '<span class="btn-icon">🗑️</span> Xác nhận xóa');
    
    // Gửi request xóa đánh giá (Đảm bảo đường dẫn đúng)
    fetch('ajax/xoa_danh_gia.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'maDanhGia=' + reviewId
    })
    .then(response => response.json())
    .then(data => {
        if(data.success) {
            // Hiển thị thông báo thành công
            showNotification('✅ ' + data.message, 'success');
            closeModal(button);
            
            // Reload trang sau 1.5 giây
            setTimeout(() => {
                location.reload();
            }, 1500);
        } else {
            // Hiển thị thông báo lỗi
            showNotification('❌ ' + data.message, 'error');
            removeLoadingEffect(button);
        }
    })
    .catch(error => {
        console.error('Lỗi fetch:', error);
        showNotification('❌ Lỗi kết nối hoặc xử lý dữ liệu: ' + error.message, 'error');
        removeLoadingEffect(button);
    });
}

// 🔥 Hàm xóa đánh giá (Đã sửa để gọi modal)
function deleteReview(reviewId) {
    showDeleteConfirmModal(reviewId);
}

// Hàm sửa đánh giá (Đã sửa để gọi modal)
function editReview(reviewId) {
    const card = event.target.closest('.review-card');
    const button = event.target.closest('.btn-edit');

    if (button) {
        // Thêm hiệu ứng loading
        addLoadingEffect(button, '<span class="btn-icon">✏️</span> Sửa');
    }
    
    // Chuyển hướng đến trang chỉnh sửa đánh giá
    setTimeout(() => {
        window.location.href = 'SuaDanhGia.php?id=' + reviewId;
    }, 300);
}

// Thêm hiệu ứng loading cho button
function addLoadingEffect(button, originalText) {
    if (!button.hasAttribute('data-original-text')) {
        button.setAttribute('data-original-text', originalText || button.innerHTML);
    }
    
    button.innerHTML = '<span class="btn-icon">⏳</span> Đang xử lý...';
    button.classList.add('btn-loading');
    button.disabled = true;
}

// Xóa hiệu ứng loading
function removeLoadingEffect(button) {
    const text = button.getAttribute('data-original-text');
    if (text) {
        button.innerHTML = text;
    }
    button.classList.remove('btn-loading');
    button.disabled = false;
    button.removeAttribute('data-original-text');
}

// Hiển thị thông báo
function showNotification(message, type = 'info') {
    // Xóa thông báo cũ nếu có
    document.querySelectorAll('.notification').forEach(notif => notif.remove());
    
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
function handleEscapeKey(e) {
    if (e.key === 'Escape') {
        const modals = document.querySelectorAll('.modal-overlay');
        modals.forEach(modal => {
            modal.remove();
            document.body.style.overflow = '';
        });
    }
}

// Xử lý click outside modal để đóng
function handleOutsideClick(e) {
    if (e.target.classList.contains('modal-overlay')) {
        e.target.remove();
        document.body.style.overflow = '';
    }
}