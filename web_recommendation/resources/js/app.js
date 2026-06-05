import './bootstrap';

// Import AOS (Animate On Scroll)
import AOS from 'aos';
import 'aos/dist/aos.css';

// Import SweetAlert2
import Swal from 'sweetalert2';

// Initialize AOS
document.addEventListener('DOMContentLoaded', function () {
    // AOS Configuration
    AOS.init({
        duration: 800,
        easing: 'ease-out-cubic',
        once: true,
        offset: 50,
        delay: 0,
        anchorPlacement: 'top-bottom',
    });

    // Refresh AOS on dynamic content load
    window.addEventListener('load', function () {
        AOS.refresh();
    });
});

// Make SweetAlert2 globally available
window.Swal = Swal;

// Toast configuration for quick notifications
window.Toast = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 3000,
    timerProgressBar: true,
    didOpen: (toast) => {
        toast.addEventListener('mouseenter', Swal.stopTimer);
        toast.addEventListener('mouseleave', Swal.resumeTimer);
    },
    customClass: {
        popup: 'rounded-xl shadow-2xl',
    }
});

// Success notification helper
window.showSuccess = (message) => {
    Toast.fire({
        icon: 'success',
        title: message,
        background: '#0f172a',
        color: '#fff',
        iconColor: '#22c55e'
    });
};

// Error notification helper
window.showError = (message) => {
    Toast.fire({
        icon: 'error',
        title: message,
        background: '#0f172a',
        color: '#fff',
        iconColor: '#ef4444'
    });
};

// Info notification helper
window.showInfo = (message) => {
    Toast.fire({
        icon: 'info',
        title: message,
        background: '#0f172a',
        color: '#fff',
        iconColor: '#3b82f6'
    });
};

// Smooth scroll for anchor links
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

// Navbar scroll effect
const navbar = document.querySelector('.navbar-glass');
if (navbar) {
    window.addEventListener('scroll', () => {
        if (window.scrollY > 50) {
            navbar.classList.add('shadow-lg', 'border-b', 'border-white/10');
        } else {
            navbar.classList.remove('shadow-lg', 'border-b', 'border-white/10');
        }
    });
}

// Image lazy loading with fade effect
document.addEventListener('DOMContentLoaded', () => {
    const images = document.querySelectorAll('img[data-src]');
    const imageObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const img = entry.target;
                img.src = img.dataset.src;
                img.classList.add('opacity-100');
                observer.unobserve(img);
            }
        });
    });

    images.forEach(img => {
        img.classList.add('opacity-0', 'transition-opacity', 'duration-500');
        imageObserver.observe(img);
    });
});
