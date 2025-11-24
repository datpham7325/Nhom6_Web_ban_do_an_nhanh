// Hàm hủy đơn hàng
function cancelOrder(orderId) {
    if (confirm('Bạn có chắc chắn muốn hủy đơn hàng này?')) {
        // Hiển thị trạng thái loading
        const cancelBtn = document.querySelector('.btn-danger');
        const originalText = cancelBtn.innerHTML;
        cancelBtn.innerHTML = '<span class="btn-icon">⏳</span> Đang xử lý...';
        cancelBtn.classList.add('btn-loading');
        cancelBtn.disabled = true;

        // Gửi yêu cầu hủy đơn hàng
        fetch('ajax/huyDonHang.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `maDonHang=${orderId}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Đã hủy đơn hàng thành công!');
                location.reload();
            } else {
                alert('Có lỗi xảy ra: ' + data.message);
                cancelBtn.innerHTML = originalText;
                cancelBtn.classList.remove('btn-loading');
                cancelBtn.disabled = false;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Có lỗi xảy ra khi hủy đơn hàng');
            cancelBtn.innerHTML = originalText;
            cancelBtn.classList.remove('btn-loading');
            cancelBtn.disabled = false;
        });
    }
}

// Hàm mở modal đánh giá
function openReview(orderId) {
    // Tạo modal đánh giá
    const modalHTML = `
        <div class="modal-overlay active" id="reviewModal">
            <div class="modal-content">
                <div class="modal-header">
                    <h3>Đánh giá đơn hàng</h3>
                    <button class="modal-close" onclick="closeModal()">×</button>
                </div>
                <div class="modal-body">
                    <div class="rating-section">
                        <label>Đánh giá tổng quan:</label>
                        <div class="star-rating" id="overallRating">
                            <span class="star" data-rating="1">★</span>
                            <span class="star" data-rating="2">★</span>
                            <span class="star" data-rating="3">★</span>
                            <span class="star" data-rating="4">★</span>
                            <span class="star" data-rating="5">★</span>
                        </div>
                        <div class="rating-text" id="ratingText">Chọn số sao để đánh giá</div>
                    </div>
                    
                    <div class="review-form">
                        <label>Nhận xét của bạn:</label>
                        <textarea placeholder="Hãy chia sẻ trải nghiệm của bạn về đơn hàng này..." rows="4"></textarea>
                    </div>
                    
                    <div class="review-items">
                        <label>Đánh giá từng món:</label>
                        <div class="item-reviews" id="itemReviews">
                            <!-- Các món ăn sẽ được thêm bằng JavaScript -->
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn-secondary" onclick="closeModal()">Hủy</button>
                    <button class="btn-primary" onclick="submitReview(${orderId})">Gửi đánh giá</button>
                </div>
            </div>
        </div>
    `;
    
    // Thêm modal vào body
    document.body.insertAdjacentHTML('beforeend', modalHTML);
    
    // Khởi tạo rating stars
    initRatingStars();
    
    // Load danh sách món ăn để đánh giá
    loadOrderItemsForReview(orderId);
}

// Đóng modal
function closeModal() {
    const modal = document.getElementById('reviewModal');
    if (modal) {
        modal.remove();
    }
}

// Khởi tạo rating stars
function initRatingStars() {
    const stars = document.querySelectorAll('.star');
    stars.forEach(star => {
        star.addEventListener('click', function() {
            const rating = parseInt(this.getAttribute('data-rating'));
            const container = this.parentElement;
            
            // Cập nhật stars
            container.querySelectorAll('.star').forEach((s, index) => {
                if (index < rating) {
                    s.classList.add('active');
                } else {
                    s.classList.remove('active');
                }
            });
            
            // Cập nhật text
            const ratingText = container.nextElementSibling;
            const ratings = ['Rất tệ', 'Tệ', 'Bình thường', 'Tốt', 'Rất tốt'];
            ratingText.textContent = ratings[rating - 1] || 'Chọn số sao để đánh giá';
        });
    });
}

// Load danh sách món ăn để đánh giá
function loadOrderItemsForReview(orderId) {
    // Giả lập dữ liệu - trong thực tế sẽ gọi API
    const items = [
        { id: 1, name: 'Mì Ý Jolly Vừa' },
        { id: 2, name: 'Gà Giòn Vui Vẻ' },
        { id: 3, name: 'Khoai Tây Chiên Vừa' },
        { id: 4, name: 'Nước Ngọt' }
    ];
    
    const container = document.getElementById('itemReviews');
    container.innerHTML = items.map(item => `
        <div class="item-review">
            <div class="item-name">${item.name}</div>
            <div class="item-rating">
                <span class="star small" data-rating="1">★</span>
                <span class="star small" data-rating="2">★</span>
                <span class="star small" data-rating="3">★</span>
                <span class="star small" data-rating="4">★</span>
                <span class="star small" data-rating="5">★</span>
            </div>
        </div>
    `).join('');
    
    // Khởi tạo rating stars cho từng món
    document.querySelectorAll('#itemReviews .star-rating').forEach(container => {
        container.querySelectorAll('.star').forEach(star => {
            star.addEventListener('click', function() {
                const rating = parseInt(this.getAttribute('data-rating'));
                this.parentElement.querySelectorAll('.star').forEach((s, index) => {
                    if (index < rating) {
                        s.classList.add('active');
                    } else {
                        s.classList.remove('active');
                    }
                });
            });
        });
    });
}

// Gửi đánh giá
function submitReview(orderId) {
    const overallRating = document.querySelector('#overallRating .star.active')?.getAttribute('data-rating') || 0;
    const comment = document.querySelector('#reviewModal textarea').value;
    
    if (overallRating == 0) {
        alert('Vui lòng chọn số sao đánh giá tổng quan');
        return;
    }
    
    // Hiển thị loading
    const submitBtn = document.querySelector('#reviewModal .btn-primary');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<span class="btn-icon">⏳</span> Đang gửi...';
    submitBtn.disabled = true;
    
    // Gửi đánh giá (giả lập)
    setTimeout(() => {
        alert('Cảm ơn bạn đã đánh giá đơn hàng!');
        closeModal();
    }, 1000);
}

// Đóng modal khi click bên ngoài
document.addEventListener('click', function(event) {
    if (event.target.classList.contains('modal-overlay')) {
        closeModal();
    }
});

// Thêm hiệu ứng hover cho các card
document.addEventListener('DOMContentLoaded', function() {
    // Thêm hiệu ứng loading khi trang tải
    const cards = document.querySelectorAll('.order-summary-card');
    cards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        
        setTimeout(() => {
            card.style.transition = 'all 0.5s ease';
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, index * 200);
    });
});