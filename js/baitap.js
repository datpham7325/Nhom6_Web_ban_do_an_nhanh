document.addEventListener('DOMContentLoaded', function() {
    // Thêm hiệu ứng khi cuộn đến các mục bài tập
    const exerciseItems = document.querySelectorAll('.exercise-item');
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, {
        threshold: 0.1
    });

    exerciseItems.forEach(item => {
        item.style.opacity = '0';
        item.style.transform = 'translateY(20px)';
        item.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
        observer.observe(item);
    });

    // Thêm hiệu ứng click cho các link bài tập
    const exerciseLinks = document.querySelectorAll('.exercise-link');
    exerciseLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            // Thêm hiệu ứng loading khi click
            const originalText = this.innerHTML;
            this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang tải...';
            this.style.pointerEvents = 'none';
            
            setTimeout(() => {
                this.innerHTML = originalText;
                this.style.pointerEvents = 'auto';
            }, 1500);
        });
    });

    // Thêm hiệu ứng hover cho các category
    const categories = document.querySelectorAll('.exercise-category');
    categories.forEach(category => {
        category.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-5px)';
            this.style.boxShadow = '0 10px 30px rgba(0,0,0,0.15)';
        });
        
        category.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
            this.style.boxShadow = '0 5px 20px rgba(0,0,0,0.1)';
        });
    });

    // Hiệu ứng cho progress stats
    const statNumbers = document.querySelectorAll('.stat-number');
    statNumbers.forEach(stat => {
        const originalText = stat.textContent;
        const [completed, total] = originalText.split('/').map(Number);
        
        // Reset để tạo hiệu ứng đếm
        stat.textContent = '0/0';
        
        setTimeout(() => {
            let currentCompleted = 0;
            let currentTotal = 0;
            
            const animateNumbers = () => {
                if (currentCompleted <= completed) {
                    stat.textContent = `${currentCompleted}/${currentTotal}`;
                    currentCompleted++;
                    currentTotal = Math.min(currentTotal + 1, total);
                    setTimeout(animateNumbers, 50);
                } else {
                    stat.textContent = originalText;
                }
            };
            
            animateNumbers();
        }, 500);
    });
});