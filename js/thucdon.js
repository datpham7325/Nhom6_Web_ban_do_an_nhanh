let cart = [];
let currentModalItem = null;
let itemToDelete = null;

// Hàm đồng bộ giỏ hàng từ database
async function syncCartFromDatabase() {
    try {
        console.log('🔄 Đang đồng bộ giỏ hàng từ database...');
        const response = await fetch('ajax/laygiohang.php');
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const result = await response.json();
        console.log('📦 Kết quả đồng bộ giỏ hàng:', result);
        
        if (result.success) {
            cart = result.cart || [];
            console.log('🛒 Dữ liệu giỏ hàng:', cart);
            updateCart();
        } else {
            console.error('❌ Lỗi đồng bộ giỏ hàng:', result.message);
            cart = [];
            updateCart();
        }
    } catch (error) {
        console.error('❌ Lỗi khi đồng bộ giỏ hàng:', error);
        cart = [];
        updateCart();
    }
}

// Hàm xử lý lỗi AJAX
function handleAjaxError(error, operation) {
    console.error(`❌ Lỗi AJAX trong ${operation}:`, error);
    showNotification('Lỗi kết nối. Vui lòng thử lại.', 'error');
}

function openModal(maBienThe, tenMon, imageSrc, gia, moTa) {
    currentModalItem = {
        maBienThe: maBienThe,
        tenMon: tenMon,
        imageSrc: imageSrc,
        gia: gia,
        moTa: moTa || 'Món ăn hấp dẫn với hương vị đặc biệt từ Jollibee.'
    };
    
    document.getElementById('modalImage').src = imageSrc;
    document.getElementById('modalName').textContent = tenMon;
    document.getElementById('modalDescription').textContent = currentModalItem.moTa;
    document.getElementById('modalQuantity').value = 1;
    
    updateModalPrice();
    
    document.getElementById('foodModal').style.display = 'block';
    document.querySelector('.overlay').classList.add('show');
    document.body.style.overflow = 'hidden';
}

function closeModal() {
    document.getElementById('foodModal').style.display = 'none';
    document.querySelector('.overlay').classList.remove('show');
    document.body.style.overflow = '';
    currentModalItem = null;
}

// Hàm mở modal confirm xóa
function openConfirmModal(maBienThe) {
    itemToDelete = maBienThe;
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

function increaseQuantity() {
    const quantityInput = document.getElementById('modalQuantity');
    if (parseInt(quantityInput.value) < 10) {
        quantityInput.value = parseInt(quantityInput.value) + 1;
        updateModalPrice();
    }
}

function decreaseQuantity() {
    const quantityInput = document.getElementById('modalQuantity');
    if (parseInt(quantityInput.value) > 1) {
        quantityInput.value = parseInt(quantityInput.value) - 1;
        updateModalPrice();
    }
}

function updateModalPrice() {
    if (!currentModalItem) return;
    
    const quantity = parseInt(document.getElementById('modalQuantity').value);
    const totalPrice = currentModalItem.gia * quantity;
    document.getElementById('modalTotalPrice').textContent = formatPrice(totalPrice) + ' VND';
}

async function addToCartFromModal() {
    if (!currentModalItem) return;
    
    const quantity = parseInt(document.getElementById('modalQuantity').value);
    
    try {
        console.log(`🛒 Đang thêm vào giỏ hàng: ${currentModalItem.maBienThe}, số lượng: ${quantity}`);
        
        const response = await fetch('ajax/themgiohang.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `mabienthe=${currentModalItem.maBienThe}&soluong=${quantity}`
        });
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const result = await response.json();
        console.log('✅ Kết quả thêm vào giỏ hàng:', result);
        
        if (result.success) {
            // Đồng bộ lại giỏ hàng từ database
            await syncCartFromDatabase();
            showNotification(`✅ Đã thêm ${quantity} ${currentModalItem.tenMon} vào giỏ hàng`);
            closeModal();
        } else {
            showNotification(result.message || '❌ Có lỗi xảy ra khi thêm vào giỏ hàng', 'error');
        }
    } catch (error) {
        console.error('❌ Lỗi khi thêm vào giỏ hàng:', error);
        handleAjaxError(error, 'addToCart');
        showNotification('❌ Có lỗi xảy ra khi thêm vào giỏ hàng', 'error');
    }
}

function updateCart() {
    updateCartCount();
    updateCartDisplay();
}

function updateCartCount() {
    const cartCount = document.getElementById('cartCount');
    const totalItems = cart.reduce((sum, item) => sum + item.quantity, 0);
    cartCount.textContent = totalItems;
    
    // Cập nhật cả navbar cart count nếu có
    const navCartCount = document.querySelector('.nav-cart-count');
    if (navCartCount) {
        navCartCount.textContent = totalItems;
    }
}

function updateCartDisplay() {
    const cartItems = document.getElementById('cartItems');
    const totalAmount = document.getElementById('totalAmount');
    const checkoutBtn = document.querySelector('.btn-checkout');
    
    if (cart.length === 0) {
        cartItems.innerHTML = `
            <div class="empty-cart">
                <div class="empty-icon">🛒</div>
                <p>Giỏ hàng trống</p>
                <small>Hãy thêm món ăn vào giỏ hàng!</small>
            </div>
        `;
        totalAmount.textContent = '0 VND';
        checkoutBtn.disabled = true;
        return;
    }
    
    let total = 0;
    let itemsHTML = '';
    
    cart.forEach((item, index) => {
        const itemTotal = item.gia * item.quantity;
        total += itemTotal;
        
        itemsHTML += `
            <div class="cart-item">
                <div class="cart-item-image">
                    <img src="${item.imageSrc}" alt="${item.tenMon}" onerror="this.src='img/default-food.jpg'">
                </div>
                <div class="cart-item-info">
                    <div class="cart-item-name">${item.tenMon}</div>
                    <div class="cart-item-details">
                        <div class="cart-item-price">${formatPrice(item.gia)}₫</div>
                        <div class="cart-item-total">${formatPrice(itemTotal)}₫</div>
                    </div>
                    <div class="cart-item-actions">
                        <button class="btn-quantity-small minus" onclick="updateCartItemQuantity('${item.maBienThe}', ${item.quantity - 1})">-</button>
                        <span class="cart-item-qty">${item.quantity}</span>
                        <button class="btn-quantity-small plus" onclick="updateCartItemQuantity('${item.maBienThe}', ${item.quantity + 1})">+</button>
                        <button class="btn-remove" onclick="openConfirmModal('${item.maBienThe}')" title="Xóa">🗑️</button>
                    </div>
                </div>
            </div>
        `;
    });
    
    cartItems.innerHTML = itemsHTML;
    totalAmount.textContent = formatPrice(total) + ' VND';
    checkoutBtn.disabled = false;
}

// Hàm cập nhật số lượng item trong giỏ hàng
async function updateCartItemQuantity(maBienThe, newQuantity) {
    if (newQuantity <= 0) {
        openConfirmModal(maBienThe);
        return;
    }
    
    try {
        console.log(`🔄 Đang cập nhật số lượng: ${maBienThe} -> ${newQuantity}`);
        
        const response = await fetch('ajax/capnhatgiohang.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `mabienthe=${maBienThe}&soluong=${newQuantity}`
        });
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const result = await response.json();
        console.log('✅ Kết quả cập nhật giỏ hàng:', result);
        
        if (result.success) {
            // Đồng bộ lại giỏ hàng từ database
            await syncCartFromDatabase();
        } else {
            showNotification(result.message || '❌ Có lỗi khi cập nhật giỏ hàng', 'error');
        }
    } catch (error) {
        console.error('❌ Lỗi khi cập nhật giỏ hàng:', error);
        handleAjaxError(error, 'updateCart');
        showNotification('❌ Lỗi kết nối khi cập nhật giỏ hàng', 'error');
    }
}

// Hàm xóa item khỏi giỏ hàng
async function removeFromCart(maBienThe) {
    try {
        console.log(`🗑️ Đang xóa khỏi giỏ hàng: ${maBienThe}`);
        
        const response = await fetch('ajax/capnhatgiohang.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `mabienthe=${maBienThe}&soluong=0`
        });
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const result = await response.json();
        console.log('✅ Kết quả xóa khỏi giỏ hàng:', result);
        
        if (result.success) {
            // Đồng bộ lại giỏ hàng từ database
            await syncCartFromDatabase();
            showNotification('✅ Đã xóa khỏi giỏ hàng');
        } else {
            showNotification(result.message || '❌ Có lỗi khi xóa khỏi giỏ hàng', 'error');
        }
    } catch (error) {
        console.error('❌ Lỗi khi xóa khỏi giỏ hàng:', error);
        handleAjaxError(error, 'removeFromCart');
        showNotification('❌ Lỗi kết nối khi xóa khỏi giỏ hàng', 'error');
    }
}

function formatPrice(price) {
    return price.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
}

function toggleCart() {
    const cartSidebar = document.querySelector('.cart-sidebar');
    const overlay = document.querySelector('.overlay');
    
    cartSidebar.classList.toggle('open');
    
    if (cartSidebar.classList.contains('open')) {
        document.body.style.overflow = 'hidden';
        overlay.classList.add('show');
    } else {
        closeCart();
    }
}

function closeCart() {
    const cartSidebar = document.querySelector('.cart-sidebar');
    const overlay = document.querySelector('.overlay');
    
    cartSidebar.classList.remove('open');
    document.body.style.overflow = '';
    overlay.classList.remove('show');
}

function checkout() {
    if (cart.length === 0) {
        showNotification('❌ Giỏ hàng trống!', 'error');
        return;
    }
    
    // Chuyển đến trang thanh toán
    window.location.href = 'ThanhToan.php';
}

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
    const modal = document.getElementById('foodModal');
    const confirmModal = document.getElementById('confirmModal');
    
    if (event.target === modal) {
        closeModal();
    }
    if (event.target === confirmModal) {
        closeConfirmModal();
    }
});

// Đóng giỏ hàng khi click ra ngoài
document.addEventListener('click', function(event) {
    const cartSidebar = document.querySelector('.cart-sidebar');
    const cartToggle = document.querySelector('.cart-toggle');
    
    if (cartSidebar.classList.contains('open') && 
        !cartSidebar.contains(event.target) && 
        !cartToggle.contains(event.target)) {
        closeCart();
    }
});

// Đóng modal khi nhấn ESC
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closeModal();
        closeConfirmModal();
        closeCart();
    }
});

// Khởi tạo
document.addEventListener('DOMContentLoaded', function() {
    console.log('🚀 Trang đã tải xong, đang đồng bộ giỏ hàng...');
    syncCartFromDatabase();
    
    // Đồng bộ lại mỗi 30 giây để đảm bảo dữ liệu luôn mới nhất
    setInterval(syncCartFromDatabase, 30000);
});