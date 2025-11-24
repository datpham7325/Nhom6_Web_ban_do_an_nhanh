// JavaScript for DieuKhoan.php

document.addEventListener('DOMContentLoaded', function() {
    initTermsPage();
    initScrollAnimations();
    initInteractiveElements();
    addImportantNotice();
});

// Khởi tạo trang điều khoản sử dụng
function initTermsPage() {
    console.log('📄 Đang khởi tạo trang điều khoản sử dụng...');
    
    // Thêm lớp loading ban đầu
    document.body.classList.add('page-loading');
    
    // Simulate loading time
    setTimeout(() => {
        document.body.classList.remove('page-loading');
        console.log('✅ Trang điều khoản sử dụng đã tải xong');
    }, 500);
}

// Khởi tạo hiệu ứng scroll
function initScrollAnimations() {
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('section-visible');
            }
        });
    }, observerOptions);
    
    // Quan sát các section
    const sections = document.querySelectorAll('.terms-section');
    sections.forEach(section => {
        section.classList.add('section-hidden');
        observer.observe(section);
    });
}

// Khởi tạo các phần tử tương tác
function initInteractiveElements() {
    initSectionHighlights();
    initQuickNavigation();
    initPrintButton();
    initAcceptButton();
}

// Hiệu ứng highlight khi hover section
function initSectionHighlights() {
    const sections = document.querySelectorAll('.terms-section');
    
    sections.forEach(section => {
        section.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-2px)';
            this.style.boxShadow = '0 4px 15px rgba(0,0,0,0.1)';
        });
        
        section.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
            this.style.boxShadow = 'none';
        });
    });
}

// Tạo quick navigation cho các section
function initQuickNavigation() {
    const sections = document.querySelectorAll('.terms-section h3');
    if (sections.length === 0) return;
    
    // Tạo navigation menu
    const quickNav = document.createElement('div');
    quickNav.className = 'quick-navigation';
    quickNav.innerHTML = `
        <div class="quick-nav-header">
            <h4>📋 Mục lục nhanh</h4>
        </div>
        <div class="quick-nav-items"></div>
    `;
    
    // Thêm các mục navigation
    const navItems = quickNav.querySelector('.quick-nav-items');
    sections.forEach((section, index) => {
        const sectionId = `terms-section-${index + 1}`;
        section.parentElement.id = sectionId;
        
        const navItem = document.createElement('a');
        navItem.href = `#${sectionId}`;
        navItem.className = 'quick-nav-item';
        navItem.textContent = section.textContent.replace(/^\d+\.\s/, ''); // Remove numbers
        navItem.addEventListener('click', smoothScroll);
        
        navItems.appendChild(navItem);
    });
    
    // Chèn navigation vào trang
    const termsContent = document.querySelector('.terms-content');
    if (termsContent) {
        termsContent.insertBefore(quickNav, termsContent.firstChild);
    }
}

// Smooth scroll function
function smoothScroll(e) {
    e.preventDefault();
    const targetId = this.getAttribute('href');
    const targetElement = document.querySelector(targetId);
    
    if (targetElement) {
        const offsetTop = targetElement.offsetTop - 100;
        window.scrollTo({
            top: offsetTop,
            behavior: 'smooth'
        });
        
        // Highlight section khi scroll đến
        targetElement.style.background = 'rgba(102, 126, 234, 0.1)';
        setTimeout(() => {
            targetElement.style.background = '';
        }, 2000);
    }
}

// Nút in trang
function initPrintButton() {
    const printButton = document.createElement('button');
    printButton.className = 'btn-print';
    printButton.innerHTML = '🖨️ In điều khoản';
    printButton.addEventListener('click', printTerms);
    
    // Thêm nút vào trang
    const pageHeader = document.querySelector('.page-header');
    if (pageHeader) {
        pageHeader.style.position = 'relative';
        printButton.style.position = 'absolute';
        printButton.style.top = '20px';
        printButton.style.right = '20px';
        printButton.style.padding = '10px 15px';
        printButton.style.background = 'rgba(255,255,255,0.2)';
        printButton.style.color = 'white';
        printButton.style.border = '1px solid rgba(255,255,255,0.3)';
        printButton.style.borderRadius = '6px';
        printButton.style.cursor = 'pointer';
        printButton.style.backdropFilter = 'blur(10px)';
        printButton.style.transition = 'all 0.3s ease';
        printButton.style.fontSize = '14px';
        printButton.style.fontWeight = '600';
        
        printButton.addEventListener('mouseenter', function() {
            this.style.background = 'rgba(255,255,255,0.3)';
            this.style.transform = 'translateY(-2px)';
        });
        
        printButton.addEventListener('mouseleave', function() {
            this.style.background = 'rgba(255,255,255,0.2)';
            this.style.transform = 'translateY(0)';
        });
        
        pageHeader.appendChild(printButton);
    }
}

// Nút chấp nhận điều khoản
function initAcceptButton() {
    const acceptButton = document.createElement('button');
    acceptButton.className = 'btn-accept';
    acceptButton.innerHTML = '✅ Tôi đã đọc và đồng ý';
    acceptButton.addEventListener('click', acceptTerms);
    
    // Thêm nút vào cuối nội dung
    const termsContent = document.querySelector('.terms-content');
    if (termsContent) {
        const acceptContainer = document.createElement('div');
        acceptContainer.style.cssText = `
            text-align: center;
            margin-top: 40px;
            padding-top: 20px;
            border-top: 2px solid #f0f0f0;
        `;
        
        acceptButton.style.cssText = `
            padding: 12px 30px;
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);
        `;
        
        acceptButton.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-2px)';
            this.style.boxShadow = '0 6px 20px rgba(40, 167, 69, 0.4)';
        });
        
        acceptButton.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
            this.style.boxShadow = '0 4px 15px rgba(40, 167, 69, 0.3)';
        });
        
        acceptContainer.appendChild(acceptButton);
        termsContent.appendChild(acceptContainer);
    }
}

// Hàm in điều khoản
function printTerms() {
    // Lưu trạng thái ban đầu
    const originalTitle = document.title;
    document.title = 'Điều Khoản Sử Dụng - JOLIBEE';
    
    window.print();
    
    // Khôi phục tiêu đề
    setTimeout(() => {
        document.title = originalTitle;
    }, 1000);
}

// Hàm chấp nhận điều khoản
function acceptTerms() {
    if (confirm('Bạn có chắc chắn đã đọc và đồng ý với tất cả các điều khoản sử dụng?')) {
        // Lưu trạng thái chấp nhận vào localStorage
        localStorage.setItem('termsAccepted', 'true');
        localStorage.setItem('termsAcceptedDate', new Date().toISOString());
        
        // Hiển thị thông báo
        showNotification('✅ Cảm ơn bạn đã chấp nhận điều khoản sử dụng!', 'success');
        
        // Vô hiệu hóa nút
        const acceptButton = document.querySelector('.btn-accept');
        acceptButton.disabled = true;
        acceptButton.innerHTML = '✅ Đã chấp nhận';
        acceptButton.style.background = '#6c757d';
        acceptButton.style.cursor = 'not-allowed';
    }
}

// Thêm thông báo quan trọng
function addImportantNotice() {
    const importantNotice = document.createElement('div');
    importantNotice.className = 'terms-important';
    importantNotice.innerHTML = `
        <h4>Lưu ý quan trọng</h4>
        <p>Vui lòng đọc kỹ các điều khoản trước khi sử dụng dịch vụ. Việc tiếp tục sử dụng website được xem như bạn đã chấp nhận toàn bộ điều khoản này.</p>
    `;
    
    const termsContent = document.querySelector('.terms-content');
    if (termsContent) {
        // Chèn sau tiêu đề
        const title = termsContent.querySelector('h2');
        if (title) {
            title.parentNode.insertBefore(importantNotice, title.nextSibling.nextSibling);
        }
    }
}

// Hiển thị thông báo
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

// Thêm CSS animations
function addAnimations() {
    const animationStyles = `
        .section-hidden {
            opacity: 0;
            transform: translateY(30px);
            transition: all 0.6s ease;
        }
        
        .section-visible {
            opacity: 1;
            transform: translateY(0);
        }
        
        .page-loading .terms-content {
            opacity: 0;
        }
        
        .terms-content {
            transition: opacity 0.3s ease;
        }
        
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.02); }
            100% { transform: scale(1); }
        }
        
        .terms-important {
            animation: pulse 3s ease-in-out infinite;
        }
    `;
    
    const styleSheet = document.createElement('style');
    styleSheet.textContent = animationStyles;
    document.head.appendChild(styleSheet);
}

// Kiểm tra nếu người dùng đã chấp nhận điều khoản trước đó
function checkPreviousAcceptance() {
    const termsAccepted = localStorage.getItem('termsAccepted');
    if (termsAccepted === 'true') {
        const acceptButton = document.querySelector('.btn-accept');
        if (acceptButton) {
            acceptButton.disabled = true;
            acceptButton.innerHTML = '✅ Đã chấp nhận';
            acceptButton.style.background = '#6c757d';
            acceptButton.style.cursor = 'not-allowed';
        }
    }
}

// Gọi hàm thêm animations
addAnimations();

// Kiểm tra chấp nhận điều khoản trước đó
checkPreviousAcceptance();

// Xử lý sự kiện resize window
window.addEventListener('resize', function() {
    console.log('🔄 Đang điều chỉnh layout cho kích thước màn hình mới...');
});

// Thêm sự kiện cho các link trong sidebar
document.querySelectorAll('.menu-item').forEach(item => {
    item.addEventListener('click', function(e) {
        if (!this.classList.contains('active')) {
            // Thêm hiệu ứng loading khi chuyển trang
            this.style.opacity = '0.7';
            setTimeout(() => {
                this.style.opacity = '1';
            }, 300);
        }
    });
});

console.log('🚀 JavaScript cho trang điều khoản sử dụng đã sẵn sàng!');