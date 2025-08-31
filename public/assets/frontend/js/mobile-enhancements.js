/**
 * Mobile Enhancements for eForum
 * Optimized for Nigerian mobile users
 */

(function() {
    'use strict';

    // Check if mobile
    const isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);

    // Mobile Navigation Enhancement
    class MobileNav {
        constructor() {
            this.nav = document.querySelector('.mobile-nav');
            this.toggleBtn = document.querySelector('.mobile-nav-toggle');
            this.overlay = document.querySelector('.mobile-nav-overlay');
            this.init();
        }

        init() {
            if (!this.nav || !this.toggleBtn) return;

            // Create overlay if doesn't exist
            if (!this.overlay) {
                this.overlay = document.createElement('div');
                this.overlay.className = 'mobile-nav-overlay';
                document.body.appendChild(this.overlay);
            }

            // Toggle events
            this.toggleBtn.addEventListener('click', () => this.toggle());
            this.overlay.addEventListener('click', () => this.close());

            // Swipe to close
            if (isMobile) {
                this.enableSwipeGestures();
            }
        }

        toggle() {
            this.nav.classList.toggle('active');
            this.overlay.classList.toggle('active');
            document.body.classList.toggle('mobile-nav-open');
        }

        close() {
            this.nav.classList.remove('active');
            this.overlay.classList.remove('active');
            document.body.classList.remove('mobile-nav-open');
        }

        enableSwipeGestures() {
            let startX = 0;
            let currentX = 0;
            let translateX = 0;

            this.nav.addEventListener('touchstart', (e) => {
                startX = e.touches[0].clientX;
            });

            this.nav.addEventListener('touchmove', (e) => {
                currentX = e.touches[0].clientX;
                translateX = Math.min(0, currentX - startX);
                
                if (translateX < 0) {
                    this.nav.style.transform = `translateX(${translateX}px)`;
                }
            });

            this.nav.addEventListener('touchend', () => {
                if (translateX < -100) {
                    this.close();
                }
                this.nav.style.transform = '';
            });
        }
    }

    // Bottom Navigation Bar for Mobile
    class BottomNav {
        constructor() {
            this.createBottomNav();
            this.highlightActiveItem();
        }

        createBottomNav() {
            const bottomNav = document.createElement('nav');
            bottomNav.className = 'bottom-nav';
            bottomNav.innerHTML = `
                <a href="/" class="bottom-nav-item" data-page="home">
                    <i class="bi bi-house"></i>
                    <span>Home</span>
                </a>
                <a href="/categories/visa-immigration" class="bottom-nav-item" data-page="visa">
                    <i class="bi bi-passport"></i>
                    <span>Visa</span>
                </a>
                <a href="/posts/create" class="bottom-nav-item bottom-nav-create">
                    <i class="bi bi-plus-circle-fill"></i>
                </a>
                <a href="/jobs" class="bottom-nav-item" data-page="jobs">
                    <i class="bi bi-briefcase"></i>
                    <span>Jobs</span>
                </a>
                <a href="/user/dashboard" class="bottom-nav-item" data-page="profile">
                    <i class="bi bi-person"></i>
                    <span>Profile</span>
                </a>
            `;

            if (isMobile) {
                document.body.appendChild(bottomNav);
                document.body.classList.add('has-bottom-nav');
            }
        }

        highlightActiveItem() {
            const currentPath = window.location.pathname;
            const items = document.querySelectorAll('.bottom-nav-item');

            items.forEach(item => {
                const href = item.getAttribute('href');
                if (href && currentPath.startsWith(href) && href !== '/') {
                    item.classList.add('active');
                } else if (href === '/' && currentPath === '/') {
                    item.classList.add('active');
                }
            });
        }
    }

    // Pull to Refresh
    class PullToRefresh {
        constructor() {
            this.threshold = 80;
            this.isRefreshing = false;
            this.init();
        }

        init() {
            if (!isMobile) return;

            let startY = 0;
            let currentY = 0;
            let pulling = false;

            const refreshIndicator = document.createElement('div');
            refreshIndicator.className = 'pull-to-refresh';
            refreshIndicator.innerHTML = `
                <div class="pull-to-refresh-icon">
                    <i class="bi bi-arrow-clockwise"></i>
                </div>
            `;
            document.body.appendChild(refreshIndicator);

            document.addEventListener('touchstart', (e) => {
                if (window.scrollY === 0 && !this.isRefreshing) {
                    startY = e.touches[0].clientY;
                    pulling = true;
                }
            });

            document.addEventListener('touchmove', (e) => {
                if (!pulling || this.isRefreshing) return;

                currentY = e.touches[0].clientY;
                const diff = currentY - startY;

                if (diff > 0 && window.scrollY === 0) {
                    e.preventDefault();
                    refreshIndicator.style.transform = `translateY(${Math.min(diff, 100)}px)`;
                    
                    if (diff > this.threshold) {
                        refreshIndicator.classList.add('ready');
                    } else {
                        refreshIndicator.classList.remove('ready');
                    }
                }
            });

            document.addEventListener('touchend', () => {
                if (!pulling || this.isRefreshing) return;

                const diff = currentY - startY;
                
                if (diff > this.threshold) {
                    this.refresh();
                } else {
                    refreshIndicator.style.transform = '';
                    refreshIndicator.classList.remove('ready');
                }

                pulling = false;
            });
        }

        refresh() {
            this.isRefreshing = true;
            document.querySelector('.pull-to-refresh').classList.add('refreshing');

            // Simulate refresh - in real app, this would fetch new data
            setTimeout(() => {
                window.location.reload();
            }, 1000);
        }
    }

    // Image Lazy Loading with Nigerian bandwidth consideration
    class LazyImages {
        constructor() {
            this.images = document.querySelectorAll('img[data-src]');
            this.imageOptions = {
                threshold: 0.01,
                rootMargin: '0px 0px 50px 0px'
            };
            this.init();
        }

        init() {
            if ('IntersectionObserver' in window) {
                const imageObserver = new IntersectionObserver((entries, observer) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            this.loadImage(entry.target);
                            observer.unobserve(entry.target);
                        }
                    });
                }, this.imageOptions);

                this.images.forEach(img => imageObserver.observe(img));
            } else {
                // Fallback for older browsers
                this.loadAllImages();
            }
        }

        loadImage(img) {
            const src = img.getAttribute('data-src');
            if (!src) return;

            // Use smaller images for mobile on slow connections
            if (isMobile && navigator.connection && navigator.connection.effectiveType === '2g') {
                const mobileSrc = src.replace(/\.(jpg|png|webp)$/, '-mobile.$1');
                img.src = mobileSrc;
            } else {
                img.src = src;
            }

            img.classList.add('loaded');
            img.removeAttribute('data-src');
        }

        loadAllImages() {
            this.images.forEach(img => this.loadImage(img));
        }
    }

    // Offline Indicator
    class OfflineIndicator {
        constructor() {
            this.indicator = this.createIndicator();
            this.init();
        }

        createIndicator() {
            const indicator = document.createElement('div');
            indicator.className = 'offline-indicator';
            indicator.innerHTML = `
                <i class="bi bi-wifi-off"></i>
                <span>You are offline</span>
            `;
            document.body.appendChild(indicator);
            return indicator;
        }

        init() {
            window.addEventListener('online', () => {
                this.indicator.classList.remove('show');
                this.showToast('Back online! 🎉', 'success');
            });

            window.addEventListener('offline', () => {
                this.indicator.classList.add('show');
                this.showToast('You are offline. Some features may be limited.', 'warning');
            });

            // Check initial state
            if (!navigator.onLine) {
                this.indicator.classList.add('show');
            }
        }

        showToast(message, type = 'info') {
            const toast = document.createElement('div');
            toast.className = `toast-notification toast-${type}`;
            toast.textContent = message;
            document.body.appendChild(toast);

            setTimeout(() => {
                toast.classList.add('show');
            }, 100);

            setTimeout(() => {
                toast.classList.remove('show');
                setTimeout(() => toast.remove(), 300);
            }, 3000);
        }
    }

    // Initialize all mobile enhancements
    document.addEventListener('DOMContentLoaded', () => {
        new MobileNav();
        new BottomNav();
        new PullToRefresh();
        new LazyImages();
        new OfflineIndicator();

        // Register service worker
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register('/service-worker.js')
                .then(registration => console.log('Service Worker registered'))
                .catch(error => console.error('Service Worker registration failed:', error));
        }

        // Add install prompt for PWA
        let deferredPrompt;
        window.addEventListener('beforeinstallprompt', (e) => {
            e.preventDefault();
            deferredPrompt = e;

            // Show custom install button
            const installBtn = document.createElement('button');
            installBtn.className = 'pwa-install-btn';
            installBtn.innerHTML = '<i class="bi bi-download"></i> Install eForum App';
            installBtn.onclick = async () => {
                deferredPrompt.prompt();
                const { outcome } = await deferredPrompt.userChoice;
                
                if (outcome === 'accepted') {
                    console.log('User accepted the install prompt');
                }
                
                deferredPrompt = null;
                installBtn.remove();
            };

            document.body.appendChild(installBtn);
        });
    });
})();
