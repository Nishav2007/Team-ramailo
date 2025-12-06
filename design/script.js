// Smooth scrolling for navigation links
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            target.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }
    });
});

// Add scroll effect to navbar
window.addEventListener('scroll', () => {
    const navbar = document.querySelector('.navbar');
    if (window.scrollY > 50) {
        navbar.style.boxShadow = '0 2px 20px rgba(20, 184, 166, 0.2)';
    } else {
        navbar.style.boxShadow = '0 2px 20px rgba(20, 184, 166, 0.1)';
    }
});

// Animate stats on scroll
const observerOptions = {
    threshold: 0.5,
    rootMargin: '0px'
};

const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            const statNumber = entry.target.querySelector('.stat-number');
            if (statNumber) {
                const finalValue = statNumber.textContent;
                animateNumber(statNumber, finalValue);
                observer.unobserve(entry.target);
            }
        }
    });
}, observerOptions);

document.querySelectorAll('.stat-card').forEach(card => {
    observer.observe(card);
});

// Animate mini stats
document.querySelectorAll('.mini-stat-number').forEach(stat => {
    observer.observe(stat.closest('.mini-stat'));
});

function animateNumber(element, finalValue) {
    const isPercentage = finalValue.includes('%');
    const isK = finalValue.includes('K');
    const isPlus = finalValue.includes('+');
    const numericValue = parseInt(finalValue.replace(/[^0-9]/g, ''));
    let current = 0;
    const increment = numericValue / 50;
    const timer = setInterval(() => {
        current += increment;
        if (current >= numericValue) {
            element.textContent = finalValue;
            clearInterval(timer);
        } else {
            element.textContent = Math.floor(current) + (isK ? 'K' : '') + (isPlus ? '+' : '') + (isPercentage ? '%' : '');
        }
    }, 30);
}

// Parallax effect for hero visual
window.addEventListener('scroll', () => {
    const scrolled = window.pageYOffset;
    const heroVisual = document.querySelector('.hero-visual');
    if (heroVisual && scrolled < window.innerHeight) {
        heroVisual.style.transform = `translateY(${scrolled * 0.3}px)`;
    }
});

// Intersection Observer for fade-in animations
const fadeObserver = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.style.opacity = '1';
            entry.target.style.transform = 'translateY(0)';
        }
    });
}, {
    threshold: 0.1,
    rootMargin: '0px 0px -50px 0px'
});

document.querySelectorAll('.solution-card, .impact-card').forEach(el => {
    el.style.opacity = '0';
    el.style.transform = 'translateY(30px)';
    el.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
    fadeObserver.observe(el);
});

