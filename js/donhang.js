// JavaScript cho trang đơn hàng
// 🔥 LƯU Ý: Hàm openReview đã được sửa để nhận tham số e (event)
document.addEventListener('DOMContentLoaded', function() {
    console.log('Trang đơn hàng đã tải xong');
    initializePage();
});

function initializePage() {
    addOrderCardAnimations();
}

// Hiệu ứng cho thẻ đơn hàng
function addOrderCardAnimations() {
    const orderCards = document.querySelectorAll('.order-card');
    
    orderCards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-5px)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
        });
    });
}

// MODAL XÁC NHẬN HỦY ĐƠN HÀNG
function showCancelConfirmModal(orderId) {
    const modal = document.createElement('div');
    modal.className = 'modal-overlay';
    modal.innerHTML = `
        <div class="modal-content confirm-modal">
            <div class="modal-header">
                <h3>Xác nhận hủy đơn hàng</h3>
                <button class="modal-close" onclick="closeModal(this)">×</button>
            </div>
            <div class="modal-body">
                <div class="warning-icon">⚠️</div>
                <p>Bạn có chắc chắn muốn hủy đơn hàng <strong>#${orderId}</strong>?</p>
                <p class="warning-text">Hành động này không thể hoàn tác!</p>
            </div>
            <div class="modal-actions">
                <button class="btn-cancel-confirm" onclick="confirmCancelOrder(${orderId}, this)">
                    <span class="btn-icon">❌</span>
                    Xác nhận hủy
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

// Xác nhận hủy đơn hàng
function confirmCancelOrder(orderId, button) {
    console.log('Xác nhận hủy đơn hàng:', orderId);
    
    // Hiển thị loading trong modal
    addLoadingEffect(button);
    
    // Gửi request hủy đơn hàng
    fetch('ajax/huyDonHang.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'madonhang=' + orderId
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
                showNotification('✅ Hủy đơn hàng thành công!', 'success');
                closeModal(button);
                
                // Reload trang sau 1.5 giây
                setTimeout(() => {
                    location.reload();
                }, 1500);
            } else {
                // Hiển thị thông báo lỗi
                showNotification('❌ ' + data.message, 'error');
                removeLoadingEffect(button, '<span class="btn-icon">❌</span> Xác nhận hủy');
            }
        } catch (e) {
            console.error('Lỗi parse JSON:', e);
            console.error('Nội dung response:', text);
            showNotification('❌ Lỗi xử lý dữ liệu từ server', 'error');
            removeLoadingEffect(button, '<span class="btn-icon">❌</span> Xác nhận hủy');
        }
    })
    .catch(error => {
        console.error('Lỗi fetch:', error);
        showNotification('❌ Lỗi kết nối: ' + error.message, 'error');
        removeLoadingEffect(button, '<span class="btn-icon">❌</span> Xác nhận hủy');
    });
}

// Hàm hủy đơn hàng (gọi modal)
function cancelOrder(orderId) {
    showCancelConfirmModal(orderId);
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

// 🔥 LƯU Ý: Hàm openReview đã được định nghĩa lại trong DonHang.php
// Nếu bạn muốn dùng hàm này, hãy đảm bảo nó nhận 2 tham số: openReview(orderId, e)
/*
function openReview(orderId, e) {
    const button = e.target.closest('.btn-review');
    button.innerHTML = '<span class="btn-icon">⏳</span> Đang tải...';
    button.disabled = true;
    
    setTimeout(() => {
        window.location.href = 'TaoDanhGia.php?id=' + orderId;
    }, 200);
}
*/

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