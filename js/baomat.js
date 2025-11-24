// JavaScript for BaoMat.php

document.addEventListener('DOMContentLoaded', function() {
    initPrivacyPage();
    initScrollAnimations();
    initInteractiveElements();
});

// Khởi tạo trang chính sách bảo mật
function initPrivacyPage() {
    console.log('🔒 Đang khởi tạo trang chính sách bảo mật...');
    
    // Thêm lớp loading ban đầu
    document.body.classList.add('page-loading');
    
    // Simulate loading time
    setTimeout(() => {
        document.body.classList.remove('page-loading');
        console.log('✅ Trang chính sách bảo mật đã tải xong');
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
    const sections = document.querySelectorAll('.privacy-section');
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
}

// Hiệu ứng highlight khi hover section
function initSectionHighlights() {
    const sections = document.querySelectorAll('.privacy-section');
    
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
    const sections = document.querySelectorAll('.privacy-section h3');
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
        const sectionId = `section-${index + 1}`;
        section.parentElement.id = sectionId;
        
        const navItem = document.createElement('a');
        navItem.href = `#${sectionId}`;
        navItem.className = 'quick-nav-item';
        navItem.textContent = section.textContent.replace(/^\d+\.\s/, ''); // Remove numbers
        navItem.addEventListener('click', smoothScroll);
        
        navItems.appendChild(navItem);
    });
    
    // Chèn navigation vào trang
    const privacyContent = document.querySelector('.privacy-content');
    if (privacyContent) {
        privacyContent.insertBefore(quickNav, privacyContent.firstChild);
        
        // Thêm CSS cho quick navigation
        addQuickNavStyles();
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
    }
}

// Thêm CSS cho quick navigation
function addQuickNavStyles() {
    const styles = `
        .quick-navigation {
            background: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 30px;
            position: sticky;
            top: 20px;
            z-index: 100;
        }
        
        .quick-nav-header h4 {
            margin: 0 0 15px 0;
            color: #667eea;
            font-size: 1.1em;
            font-weight: 600;
        }
        
        .quick-nav-items {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }
        
        .quick-nav-item {
            color: #333;
            text-decoration: none;
            padding: 8px 12px;
            border-radius: 6px;
            transition: all 0.3s ease;
            font-size: 0.9em;
            border-left: 3px solid transparent;
        }
        
        .quick-nav-item:hover {
            background: #667eea;
            color: white;
            border-left-color: #ffd700;
            transform: translateX(5px);
        }
        
        @media (max-width: 768px) {
            .quick-navigation {
                position: static;
            }
            
            .quick-nav-items {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 10px;
            }
            
            .quick-nav-item {
                text-align: center;
                border-left: none;
                border-bottom: 2px solid transparent;
            }
            
            .quick-nav-item:hover {
                transform: translateY(-2px);
                border-bottom-color: #ffd700;
            }
        }
        
        @media (max-width: 480px) {
            .quick-nav-items {
                grid-template-columns: 1fr;
            }
        }
    `;
    
    const styleSheet = document.createElement('style');
    styleSheet.textContent = styles;
    document.head.appendChild(styleSheet);
}

// Nút in trang
function initPrintButton() {
    const printButton = document.createElement('button');
    printButton.className = 'btn-print';
    printButton.innerHTML = '🖨️ In trang này';
    printButton.addEventListener('click', printPage);
    
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

// Hàm in trang
function printPage() {
    window.print();
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
        
        .page-loading .privacy-content {
            opacity: 0;
        }
        
        .privacy-content {
            transition: opacity 0.3s ease;
        }
        
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }
        
        .privacy-notice {
            animation: pulse 2s ease-in-out infinite;
        }
    `;
    
    const styleSheet = document.createElement('style');
    styleSheet.textContent = animationStyles;
    document.head.appendChild(styleSheet);
}

// Gọi hàm thêm animations
addAnimations();

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

console.log('🚀 JavaScript cho trang chính sách bảo mật đã sẵn sàng!');