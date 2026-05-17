/**
 * Performance Optimization Utilities
 * Lazy loading, image optimization, and performance monitoring
 */

class PerformanceOptimizer {
    constructor() {
        this.init();
    }

    init() {
        this.setupLazyLoading();
        this.setupImageOptimization();
        this.setupPerformanceMonitoring();
        this.setupPreloading();
        this.setupResourceHints();
    }

    // Lazy Loading Implementation
    setupLazyLoading() {
        if ('IntersectionObserver' in window) {
            const lazyImages = document.querySelectorAll('img[data-src]');
            const lazyElements = document.querySelectorAll('[data-lazy]');
            
            const imageObserver = new IntersectionObserver((entries, observer) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const img = entry.target;
                        
                        // Load image
                        if (img.dataset.src) {
                            img.src = img.dataset.src;
                            img.classList.remove('lazy');
                            img.classList.add('loaded');
                        }
                        
                        // Load lazy content
                        if (img.dataset.lazy) {
                            this.loadLazyContent(img);
                        }
                        
                        observer.unobserve(img);
                    }
                });
            }, {
                rootMargin: '50px 0px',
                threshold: 0.01
            });

            lazyImages.forEach(img => imageObserver.observe(img));
            lazyElements.forEach(el => imageObserver.observe(el));
        }
    }

    // Image Optimization
    setupImageOptimization() {
        // WebP Support Detection
        const supportsWebP = this.checkWebPSupport();
        
        // Responsive Images
        this.setupResponsiveImages();
        
        // Image Compression
        this.setupImageCompression();
        
        // Blur to Sharp Loading
        this.setupBlurToSharp();
    }

    checkWebPSupport() {
        return new Promise((resolve) => {
            const webP = new Image();
            webP.onload = webP.onerror = () => {
                resolve(webP.height === 2);
            };
            webP.src = 'data:image/webp;base64,UklGRjoAAABXRUJQVlA4IC4AAACyAgCdASoCAAIALmk0mk0iIiIiIgBoSygABc6WWgAA/veff/0PP8bA//LwYAAA';
        });
    }

    setupResponsiveImages() {
        const images = document.querySelectorAll('img[data-srcset]');
        
        images.forEach(img => {
            const srcset = img.dataset.srcset;
            if (srcset) {
                img.srcset = srcset;
            }
        });
    }

    setupImageCompression() {
        // Compress images before upload
        const fileInputs = document.querySelectorAll('input[type="file"][accept*="image"]');
        
        fileInputs.forEach(input => {
            input.addEventListener('change', (e) => {
                const files = Array.from(e.target.files);
                files.forEach(file => {
                    if (file.type.startsWith('image/')) {
                        this.compressImage(file, 0.8, 1920).then(compressedFile => {
                            // Replace original file with compressed version
                            const dataTransfer = new DataTransfer();
                            dataTransfer.items.add(compressedFile);
                            input.files = dataTransfer.files;
                        });
                    }
                });
            });
        });
    }

    compressImage(file, quality = 0.8, maxWidth = 1920) {
        return new Promise((resolve) => {
            const canvas = document.createElement('canvas');
            const ctx = canvas.getContext('2d');
            const img = new Image();
            
            img.onload = () => {
                // Calculate new dimensions
                let { width, height } = img;
                if (width > maxWidth) {
                    height = (height * maxWidth) / width;
                    width = maxWidth;
                }
                
                canvas.width = width;
                canvas.height = height;
                
                // Draw and compress
                ctx.drawImage(img, 0, 0, width, height);
                canvas.toBlob(resolve, file.type, quality);
            };
            
            img.src = URL.createObjectURL(file);
        });
    }

    setupBlurToSharp() {
        const images = document.querySelectorAll('img[data-blur]');
        
        images.forEach(img => {
            // Add blur effect initially
            img.style.filter = 'blur(5px)';
            img.style.transition = 'filter 0.3s ease';
            
            img.addEventListener('load', () => {
                // Remove blur when loaded
                img.style.filter = 'blur(0px)';
            });
        });
    }

    // Performance Monitoring
    setupPerformanceMonitoring() {
        // Core Web Vitals
        this.measureCoreWebVitals();
        
        // Resource Timing
        this.measureResourceTiming();
        
        // User Timing
        this.measureUserTiming();
        
        // Memory Usage
        this.monitorMemoryUsage();
    }

    measureCoreWebVitals() {
        // Largest Contentful Paint (LCP)
        new PerformanceObserver((entryList) => {
            const entries = entryList.getEntries();
            const lastEntry = entries[entries.length - 1];
            console.log('LCP:', lastEntry.startTime);
            this.sendMetric('LCP', lastEntry.startTime);
        }).observe({ entryTypes: ['largest-contentful-paint'] });

        // First Input Delay (FID)
        new PerformanceObserver((entryList) => {
            const entries = entryList.getEntries();
            entries.forEach(entry => {
                console.log('FID:', entry.processingStart - entry.startTime);
                this.sendMetric('FID', entry.processingStart - entry.startTime);
            });
        }).observe({ entryTypes: ['first-input'] });

        // Cumulative Layout Shift (CLS)
        let clsValue = 0;
        new PerformanceObserver((entryList) => {
            const entries = entryList.getEntries();
            entries.forEach(entry => {
                if (!entry.hadRecentInput) {
                    clsValue += entry.value;
                }
            });
            console.log('CLS:', clsValue);
            this.sendMetric('CLS', clsValue);
        }).observe({ entryTypes: ['layout-shift'] });
    }

    measureResourceTiming() {
        window.addEventListener('load', () => {
            const resources = performance.getEntriesByType('resource');
            
            resources.forEach(resource => {
                const timing = {
                    name: resource.name,
                    duration: resource.duration,
                    size: resource.transferSize,
                    type: resource.initiatorType
                };
                
                // Log slow resources
                if (resource.duration > 1000) {
                    console.warn('Slow resource:', timing);
                }
            });
        });
    }

    measureUserTiming() {
        // Mark important milestones
        performance.mark('page-start');
        
        window.addEventListener('load', () => {
            performance.mark('page-loaded');
            performance.measure('page-load-time', 'page-start', 'page-loaded');
            
            const measure = performance.getEntriesByName('page-load-time')[0];
            console.log('Page load time:', measure.duration);
            this.sendMetric('PageLoadTime', measure.duration);
        });
    }

    monitorMemoryUsage() {
        if ('memory' in performance) {
            setInterval(() => {
                const memory = performance.memory;
                const usage = {
                    used: memory.usedJSHeapSize,
                    total: memory.totalJSHeapSize,
                    limit: memory.jsHeapSizeLimit
                };
                
                // Log high memory usage
                if (usage.used / usage.limit > 0.8) {
                    console.warn('High memory usage:', usage);
                }
            }, 30000); // Check every 30 seconds
        }
    }

    // Preloading
    setupPreloading() {
        // Preload critical resources
        this.preloadCriticalResources();
        
        // Preload on hover
        this.setupHoverPreloading();
        
        // Preload next page
        this.setupNextPagePreloading();
    }

    preloadCriticalResources() {
        const criticalResources = [
            '/frontend/css/storefront-tokens.css',
            '/frontend/css/storefront-components.css'
        ];
        
        criticalResources.forEach(resource => {
            const link = document.createElement('link');
            link.rel = 'preload';
            link.href = resource;
            link.as = resource.endsWith('.css') ? 'style' : 'script';
            document.head.appendChild(link);
        });
    }

    setupHoverPreloading() {
        const links = document.querySelectorAll('a[href^="/"]');
        
        links.forEach(link => {
            let timeout;
            
            link.addEventListener('mouseenter', () => {
                timeout = setTimeout(() => {
                    this.preloadPage(link.href);
                }, 200); // Preload after 200ms hover
            });
            
            link.addEventListener('mouseleave', () => {
                clearTimeout(timeout);
            });
        });
    }

    setupNextPagePreloading() {
        // Preload pagination links
        const paginationLinks = document.querySelectorAll('.pagination a');
        paginationLinks.forEach(link => {
            this.preloadPage(link.href);
        });
    }

    preloadPage(url) {
        if (!this.preloadedPages) {
            this.preloadedPages = new Set();
        }
        
        if (!this.preloadedPages.has(url)) {
            const link = document.createElement('link');
            link.rel = 'prefetch';
            link.href = url;
            document.head.appendChild(link);
            this.preloadedPages.add(url);
        }
    }

    // Resource Hints
    setupResourceHints() {
        // DNS Prefetch
        this.addDNSPrefetch([
            'fonts.googleapis.com',
            'fonts.gstatic.com',
            'images.unsplash.com'
        ]);
        
        // Preconnect
        this.addPreconnect([
            'https://fonts.googleapis.com',
            'https://fonts.gstatic.com'
        ]);
    }

    addDNSPrefetch(domains) {
        domains.forEach(domain => {
            const link = document.createElement('link');
            link.rel = 'dns-prefetch';
            link.href = `//${domain}`;
            document.head.appendChild(link);
        });
    }

    addPreconnect(urls) {
        urls.forEach(url => {
            const link = document.createElement('link');
            link.rel = 'preconnect';
            link.href = url;
            link.crossOrigin = 'anonymous';
            document.head.appendChild(link);
        });
    }

    // Utility Methods
    loadLazyContent(element) {
        const content = element.dataset.lazy;
        if (content) {
            element.innerHTML = content;
            element.classList.add('loaded');
        }
    }

    sendMetric(name, value) {
        // Send to analytics service
        if (typeof gtag !== 'undefined') {
            gtag('event', name, {
                event_category: 'Performance',
                value: Math.round(value),
                non_interaction: true
            });
        }
        
        // Send to custom analytics
        fetch('/api/analytics/metrics', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                name: name,
                value: value,
                timestamp: Date.now()
            })
        }).catch(error => {
            console.error('Failed to send metric:', error);
        });
    }

    // Debounce function
    debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    // Throttle function
    throttle(func, limit) {
        let inThrottle;
        return function() {
            const args = arguments;
            const context = this;
            if (!inThrottle) {
                func.apply(context, args);
                inThrottle = true;
                setTimeout(() => inThrottle = false, limit);
            }
        };
    }
}

// Initialize Performance Optimizer
document.addEventListener('DOMContentLoaded', () => {
    window.performanceOptimizer = new PerformanceOptimizer();
});

// Export for use in other scripts
if (typeof module !== 'undefined' && module.exports) {
    module.exports = PerformanceOptimizer;
}
