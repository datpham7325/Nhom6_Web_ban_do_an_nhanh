let itemToDelete = null;

// Hàm mở modal confirm xóa
function openConfirmModal(maGioHang, tenMon) {
    itemToDelete = maGioHang;
    const message = tenMon ? 
        `Bạn có chắc muốn xóa "<strong>${tenMon}</strong>" khỏi giỏ hàng?` : 
        'Bạn có chắc muốn xóa món này khỏi giỏ hàng?';
    
    document.getElementById('confirmMessage').innerHTML = message;
    document.getElementById('confirmModal').style.display = 'block';
    document.querySelector('.overlay').classList.add('show');
    document.body.style.overflow = 'hidden';
}

// Hàm đóng modal confirm
function closeConfirmModal() {
    document.getElementById('confirmModal').style.display = 'none';
    document.querySelector('.overlay').classList.remove('show');
    document.body.style.overflow = '';
    itemToDelete = null;
}

// Hàm xác nhận xóa
document.getElementById('btnConfirmDelete').addEventListener('click', function() {
    if (itemToDelete) {
        removeFromCart(itemToDelete);
        closeConfirmModal();
    }
});

// Hàm hiển thị confirm xóa
function showRemoveConfirm(maGioHang, tenMon) {
    openConfirmModal(maGioHang, tenMon);
}

// Hàm cập nhật số lượng trong giỏ hàng
function updateCart(maGioHang, soLuong, inputElement) {
    // Ép kiểu số lượng về số nguyên
    soLuong = parseInt(soLuong);
    
    if (soLuong < 1) {
        // Nếu số lượng âm hoặc 0, ta không cập nhật mà chuyển sang xác nhận xóa
        showRemoveConfirm(maGioHang, inputElement.closest('tr').querySelector('.item-name').textContent);
        inputElement.value = 1; // Giữ nguyên giá trị cũ trên UI
        return;
    }
    
    const input = inputElement;
    const originalValue = input.value;
    input.disabled = true;
    
    fetch('ajax/capnhatgiohang.php', { 
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        // 🔥 GỬI DỮ LIỆU ĐỒNG BỘ: magiohang và soluong
        body: 'magiohang=' + maGioHang + '&soluong=' + soLuong 
    })
    .then(response => {
        if (!response.ok) {
            // Đọc phản hồi text để debug lỗi HTTP
            return response.text().then(text => { 
                console.error('Server Text Response:', text);
                throw new Error('Lỗi HTTP (' + response.status + '). Vui lòng kiểm tra log.');
            });
        }
        return response.json();
    })
    .then(data => {
        if(data.success) {
            // Tải lại trang sau khi cập nhật thành công để refresh giá và tổng tiền
            location.reload();
        } else {
            // Lỗi validation từ PHP
            alert('Lỗi cập nhật giỏ hàng: ' + (data.message || 'Lỗi không xác định'));
            input.value = originalValue; // Khôi phục giá trị cũ
        }
    })
    .catch(error => {
        // Lỗi kết nối hoặc lỗi JSON parse
        console.error('Lỗi AJAX:', error);
        alert('Lỗi kết nối hoặc xử lý dữ liệu: ' + error.message);
        input.value = originalValue; // Khôi phục giá trị cũ
    })
    .finally(() => {
        input.disabled = false;
    });
}

// Hàm xóa item khỏi giỏ hàng
function removeFromCart(maGioHang) {
    // Hiển thị loading trên nút xóa
    const button = document.getElementById('btnConfirmDelete');
    const originalText = button.innerHTML;
    button.innerHTML = '<span class="loading"></span> Đang xóa...';
    button.disabled = true;
    
    fetch('ajax/xoagiohang.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'magiohang=' + maGioHang
    })
    .then(response => {
        if (!response.ok) {
            return response.text().then(text => { throw new Error(text); });
        }
        return response.json();
    })
    .then(data => {
        if(data.success) {
            location.reload();
        } else {
            alert('Lỗi xóa giỏ hàng: ' + (data.message || ''));
            button.innerHTML = originalText;
            button.disabled = false;
        }
    })
    .catch(error => {
        alert('Lỗi kết nối: ' + error);
        button.innerHTML = originalText;
        button.disabled = false;
    });
}

// Hàm hiển thị thông báo
function showNotification(message, type = 'success') {
    const notification = document.createElement('div');
    notification.textContent = message;
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background: ${type === 'error' ? '#dc3545' : '#28a745'};
        color: white;
        padding: 12px 20px;
        border-radius: 6px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        z-index: 1003;
        font-weight: 600;
        transform: translateX(150%);
        transition: transform 0.3s ease;
    `;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.style.transform = 'translateX(0)';
    }, 100);
    
    setTimeout(() => {
        notification.style.transform = 'translateX(150%)';
        setTimeout(() => {
            document.body.removeChild(notification);
        }, 300);
    }, 3000);
}

// Đóng modal khi click ra ngoài
document.addEventListener('click', function(event) {
    const confirmModal = document.getElementById('confirmModal');
    if (event.target === confirmModal) {
        closeConfirmModal();
    }
});

// Đóng modal khi nhấn ESC
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closeConfirmModal();
    }
});

// Xử lý sự kiện cho input số lượng
document.addEventListener('DOMContentLoaded', function() {
    const quantityInputs = document.querySelectorAll('.item-quantity input');
    
    quantityInputs.forEach(input => {
        input.addEventListener('blur', function() {
            // Nếu giá trị là rỗng hoặc không phải số, đặt lại thành 1
            if (this.value === '' || this.value < 1 || isNaN(parseInt(this.value))) {
                this.value = 1;
                // Kích hoạt cập nhật nếu giá trị bị sửa thành 1
                if (this.value !== this.defaultValue) {
                    this.dispatchEvent(new Event('change'));
                }
            }
        });
        
        input.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                this.blur();
                // Kích hoạt onchange nếu giá trị thay đổi
                if (this.value !== this.defaultValue) {
                    this.dispatchEvent(new Event('change'));
                }
            }
        });
    });
    
    console.log('🚀 Trang giỏ hàng đã tải xong');
});