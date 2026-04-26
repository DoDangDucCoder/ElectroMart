<?php

namespace App\Application\Shared\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;

class UserExperienceEnhancementService
{
    private array $uxConfig = [];
    private array $userPreferences = [];
    private array $analytics = [];

    public function __construct()
    {
        $this->initializeUXConfig();
    }

    private function initializeUXConfig(): void
    {
        $this->uxConfig = [
            'accessibility' => [
                'high_contrast' => false,
                'large_text' => false,
                'screen_reader' => false,
                'keyboard_navigation' => true,
                'focus_indicators' => true,
            ],
            'performance' => [
                'lazy_loading' => true,
                'preloading' => true,
                'caching' => true,
                'compression' => true,
            ],
            'interaction' => [
                'smooth_scrolling' => true,
                'hover_effects' => true,
                'animations' => true,
                'transitions' => true,
            ],
            'personalization' => [
                'theme_selection' => true,
                'language_selection' => true,
                'layout_preferences' => true,
                'content_filtering' => true,
            ],
            'notifications' => [
                'toast_notifications' => true,
                'push_notifications' => false,
                'email_notifications' => true,
                'browser_notifications' => false,
            ],
        ];
    }

    public function enhanceUserExperience(array $userData = []): array
    {
        Log::info('Enhancing user experience...');

        $results = [
            'accessibility' => $this->enhanceAccessibility($userData),
            'performance' => $this->enhancePerformance($userData),
            'interaction' => $this->enhanceInteraction($userData),
            'personalization' => $this->enhancePersonalization($userData),
            'notifications' => $this->enhanceNotifications($userData),
        ];

        Log::info('User experience enhancement completed');
        return $results;
    }

    private function enhanceAccessibility(array $userData): array
    {
        $results = [];

        try {
            // High contrast mode
            if ($this->uxConfig['accessibility']['high_contrast']) {
                $results['high_contrast'] = $this->enableHighContrast();
            }

            // Large text mode
            if ($this->uxConfig['accessibility']['large_text']) {
                $results['large_text'] = $this->enableLargeText();
            }

            // Screen reader support
            if ($this->uxConfig['accessibility']['screen_reader']) {
                $results['screen_reader'] = $this->enableScreenReaderSupport();
            }

            // Keyboard navigation
            if ($this->uxConfig['accessibility']['keyboard_navigation']) {
                $results['keyboard_navigation'] = $this->enableKeyboardNavigation();
            }

            // Focus indicators
            if ($this->uxConfig['accessibility']['focus_indicators']) {
                $results['focus_indicators'] = $this->enableFocusIndicators();
            }

        } catch (\Exception $e) {
            Log::error('Accessibility enhancement failed', [
                'error' => $e->getMessage()
            ]);
            $results['error'] = $e->getMessage();
        }

        return $results;
    }

    private function enhancePerformance(array $userData): array
    {
        $results = [];

        try {
            // Lazy loading
            if ($this->uxConfig['performance']['lazy_loading']) {
                $results['lazy_loading'] = $this->enableLazyLoading();
            }

            // Preloading
            if ($this->uxConfig['performance']['preloading']) {
                $results['preloading'] = $this->enablePreloading();
            }

            // Caching
            if ($this->uxConfig['performance']['caching']) {
                $results['caching'] = $this->enableCaching();
            }

            // Compression
            if ($this->uxConfig['performance']['compression']) {
                $results['compression'] = $this->enableCompression();
            }

        } catch (\Exception $e) {
            Log::error('Performance enhancement failed', [
                'error' => $e->getMessage()
            ]);
            $results['error'] = $e->getMessage();
        }

        return $results;
    }

    private function enhanceInteraction(array $userData): array
    {
        $results = [];

        try {
            // Smooth scrolling
            if ($this->uxConfig['interaction']['smooth_scrolling']) {
                $results['smooth_scrolling'] = $this->enableSmoothScrolling();
            }

            // Hover effects
            if ($this->uxConfig['interaction']['hover_effects']) {
                $results['hover_effects'] = $this->enableHoverEffects();
            }

            // Animations
            if ($this->uxConfig['interaction']['animations']) {
                $results['animations'] = $this->enableAnimations();
            }

            // Transitions
            if ($this->uxConfig['interaction']['transitions']) {
                $results['transitions'] = $this->enableTransitions();
            }

        } catch (\Exception $e) {
            Log::error('Interaction enhancement failed', [
                'error' => $e->getMessage()
            ]);
            $results['error'] = $e->getMessage();
        }

        return $results;
    }

    private function enhancePersonalization(array $userData): array
    {
        $results = [];

        try {
            // Theme selection
            if ($this->uxConfig['personalization']['theme_selection']) {
                $results['theme_selection'] = $this->enableThemeSelection($userData);
            }

            // Language selection
            if ($this->uxConfig['personalization']['language_selection']) {
                $results['language_selection'] = $this->enableLanguageSelection($userData);
            }

            // Layout preferences
            if ($this->uxConfig['personalization']['layout_preferences']) {
                $results['layout_preferences'] = $this->enableLayoutPreferences($userData);
            }

            // Content filtering
            if ($this->uxConfig['personalization']['content_filtering']) {
                $results['content_filtering'] = $this->enableContentFiltering($userData);
            }

        } catch (\Exception $e) {
            Log::error('Personalization enhancement failed', [
                'error' => $e->getMessage()
            ]);
            $results['error'] = $e->getMessage();
        }

        return $results;
    }

    private function enhanceNotifications(array $userData): array
    {
        $results = [];

        try {
            // Toast notifications
            if ($this->uxConfig['notifications']['toast_notifications']) {
                $results['toast_notifications'] = $this->enableToastNotifications();
            }

            // Push notifications
            if ($this->uxConfig['notifications']['push_notifications']) {
                $results['push_notifications'] = $this->enablePushNotifications();
            }

            // Email notifications
            if ($this->uxConfig['notifications']['email_notifications']) {
                $results['email_notifications'] = $this->enableEmailNotifications($userData);
            }

            // Browser notifications
            if ($this->uxConfig['notifications']['browser_notifications']) {
                $results['browser_notifications'] = $this->enableBrowserNotifications();
            }

        } catch (\Exception $e) {
            Log::error('Notifications enhancement failed', [
                'error' => $e->getMessage()
            ]);
            $results['error'] = $e->getMessage();
        }

        return $results;
    }

    private function enableHighContrast(): array
    {
        $css = "
/* High Contrast Mode */
.high-contrast {
    filter: contrast(150%) brightness(120%);
}

.high-contrast * {
    background-color: #000 !important;
    color: #fff !important;
    border-color: #fff !important;
}

.high-contrast a {
    color: #ffff00 !important;
}

.high-contrast button {
    background-color: #000 !important;
    color: #fff !important;
    border: 2px solid #fff !important;
}
";

        return [
            'css' => $css,
            'enabled' => true,
        ];
    }

    private function enableLargeText(): array
    {
        $css = "
/* Large Text Mode */
.large-text {
    font-size: 1.25rem !important;
    line-height: 1.8 !important;
}

.large-text h1 { font-size: 2.5rem !important; }
.large-text h2 { font-size: 2rem !important; }
.large-text h3 { font-size: 1.75rem !important; }
.large-text h4 { font-size: 1.5rem !important; }
.large-text h5 { font-size: 1.25rem !important; }
.large-text h6 { font-size: 1.125rem !important; }
";

        return [
            'css' => $css,
            'enabled' => true,
        ];
    }

    private function enableScreenReaderSupport(): array
    {
        $html = '
<!-- Screen Reader Support -->
<div class="sr-only" aria-live="polite" aria-atomic="true">
    <span id="screen-reader-announcements"></span>
</div>

<!-- Skip Links -->
<a href="#main-content" class="skip-link">Skip to main content</a>
<a href="#navigation" class="skip-link">Skip to navigation</a>
';

        return [
            'html' => $html,
            'enabled' => true,
        ];
    }

    private function enableKeyboardNavigation(): array
    {
        $css = "
/* Keyboard Navigation */
.skip-link {
    position: absolute;
    top: -40px;
    left: 6px;
    background: #000;
    color: #fff;
    padding: 8px;
    text-decoration: none;
    z-index: 1000;
}

.skip-link:focus {
    top: 6px;
}

.sr-only {
    position: absolute;
    width: 1px;
    height: 1px;
    padding: 0;
    margin: -1px;
    overflow: hidden;
    clip: rect(0, 0, 0, 0);
    white-space: nowrap;
    border: 0;
}

/* Focus indicators */
*:focus {
    outline: 2px solid #007bff;
    outline-offset: 2px;
}

button:focus,
a:focus,
input:focus,
select:focus,
textarea:focus {
    outline: 2px solid #007bff;
    outline-offset: 2px;
}
";

        return [
            'css' => $css,
            'enabled' => true,
        ];
    }

    private function enableFocusIndicators(): array
    {
        $css = "
/* Enhanced Focus Indicators */
.focus-visible {
    outline: 3px solid #007bff;
    outline-offset: 2px;
    box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.25);
}

.focus-ring {
    position: relative;
}

.focus-ring::after {
    content: '';
    position: absolute;
    top: -2px;
    left: -2px;
    right: -2px;
    bottom: -2px;
    border: 2px solid transparent;
    border-radius: inherit;
    pointer-events: none;
}

.focus-ring:focus::after {
    border-color: #007bff;
}
";

        return [
            'css' => $css,
            'enabled' => true,
        ];
    }

    private function enableLazyLoading(): array
    {
        $js = "
// Lazy Loading Implementation
document.addEventListener('DOMContentLoaded', function() {
    const lazyImages = document.querySelectorAll('img[data-src]');
    
    const imageObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const img = entry.target;
                img.src = img.dataset.src;
                img.classList.remove('lazy');
                img.classList.add('loaded');
                observer.unobserve(img);
            }
        });
    });
    
    lazyImages.forEach(img => imageObserver.observe(img));
});
";

        return [
            'js' => $js,
            'enabled' => true,
        ];
    }

    private function enablePreloading(): array
    {
        $html = '
<!-- Preload Critical Resources -->
<link rel="preload" href="/frontend/css/app.css" as="style">
<link rel="preload" href="/frontend/js/app.js" as="script">
<link rel="preload" href="/frontend/fonts/app.woff2" as="font" type="font/woff2" crossorigin>

<!-- Prefetch Next Page -->
<link rel="prefetch" href="/products">
<link rel="prefetch" href="/categories">
';

        return [
            'html' => $html,
            'enabled' => true,
        ];
    }

    private function enableCaching(): array
    {
        $config = [
            'browser_cache' => [
                'css' => '1 year',
                'js' => '1 year',
                'images' => '1 year',
                'fonts' => '1 year',
            ],
            'service_worker' => true,
            'cache_strategy' => 'cache_first',
        ];

        return [
            'config' => $config,
            'enabled' => true,
        ];
    }

    private function enableCompression(): array
    {
        $config = [
            'gzip' => true,
            'brotli' => true,
            'minify_html' => true,
            'minify_css' => true,
            'minify_js' => true,
        ];

        return [
            'config' => $config,
            'enabled' => true,
        ];
    }

    private function enableSmoothScrolling(): array
    {
        $css = "
/* Smooth Scrolling */
html {
    scroll-behavior: smooth;
}

/* Custom Smooth Scrolling */
.smooth-scroll {
    scroll-behavior: smooth;
}

/* Smooth Scroll for Specific Elements */
.scroll-container {
    overflow-y: auto;
    scroll-behavior: smooth;
}
";

        return [
            'css' => $css,
            'enabled' => true,
        ];
    }

    private function enableHoverEffects(): array
    {
        $css = "
/* Hover Effects */
.hover-lift {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.hover-lift:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.hover-scale {
    transition: transform 0.3s ease;
}

.hover-scale:hover {
    transform: scale(1.05);
}

.hover-fade {
    transition: opacity 0.3s ease;
}

.hover-fade:hover {
    opacity: 0.8;
}

.hover-glow {
    transition: box-shadow 0.3s ease;
}

.hover-glow:hover {
    box-shadow: 0 0 20px rgba(0, 123, 255, 0.5);
}
";

        return [
            'css' => $css,
            'enabled' => true,
        ];
    }

    private function enableAnimations(): array
    {
        $css = "
/* Animations */
@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

@keyframes slideInUp {
    from {
        transform: translateY(30px);
        opacity: 0;
    }
    to {
        transform: translateY(0);
        opacity: 1;
    }
}

@keyframes slideInDown {
    from {
        transform: translateY(-30px);
        opacity: 0;
    }
    to {
        transform: translateY(0);
        opacity: 1;
    }
}

@keyframes pulse {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.05); }
}

.animate-fade-in {
    animation: fadeIn 0.5s ease-in-out;
}

.animate-slide-in-up {
    animation: slideInUp 0.5s ease-out;
}

.animate-slide-in-down {
    animation: slideInDown 0.5s ease-out;
}

.animate-pulse {
    animation: pulse 2s infinite;
}

/* Reduced Motion Support */
@media (prefers-reduced-motion: reduce) {
    * {
        animation-duration: 0.01ms !important;
        animation-iteration-count: 1 !important;
        transition-duration: 0.01ms !important;
    }
}
";

        return [
            'css' => $css,
            'enabled' => true,
        ];
    }

    private function enableTransitions(): array
    {
        $css = "
/* Transitions */
.transition-all {
    transition: all 0.3s ease;
}

.transition-colors {
    transition: color 0.3s ease, background-color 0.3s ease, border-color 0.3s ease;
}

.transition-opacity {
    transition: opacity 0.3s ease;
}

.transition-transform {
    transition: transform 0.3s ease;
}

.transition-shadow {
    transition: box-shadow 0.3s ease;
}

/* Custom Transitions */
.fade-transition {
    transition: opacity 0.3s ease-in-out;
}

.slide-transition {
    transition: transform 0.3s ease-in-out;
}

.scale-transition {
    transition: transform 0.3s ease-in-out;
}
";

        return [
            'css' => $css,
            'enabled' => true,
        ];
    }

    private function enableThemeSelection(array $userData): array
    {
        $themes = [
            'light' => [
                'name' => 'Light Theme',
                'colors' => [
                    'primary' => '#007bff',
                    'secondary' => '#6c757d',
                    'background' => '#ffffff',
                    'text' => '#333333',
                ],
            ],
            'dark' => [
                'name' => 'Dark Theme',
                'colors' => [
                    'primary' => '#0d6efd',
                    'secondary' => '#6c757d',
                    'background' => '#212529',
                    'text' => '#ffffff',
                ],
            ],
            'auto' => [
                'name' => 'Auto Theme',
                'colors' => 'system',
            ],
        ];

        return [
            'themes' => $themes,
            'current_theme' => $userData['theme'] ?? 'light',
            'enabled' => true,
        ];
    }

    private function enableLanguageSelection(array $userData): array
    {
        $languages = [
            'en' => 'English',
            'vi' => 'Tiếng Việt',
            'zh' => '中文',
            'ja' => '日本語',
            'ko' => '한국어',
        ];

        return [
            'languages' => $languages,
            'current_language' => $userData['language'] ?? 'en',
            'enabled' => true,
        ];
    }

    private function enableLayoutPreferences(array $userData): array
    {
        $layouts = [
            'grid' => 'Grid Layout',
            'list' => 'List Layout',
            'compact' => 'Compact Layout',
            'spacious' => 'Spacious Layout',
        ];

        return [
            'layouts' => $layouts,
            'current_layout' => $userData['layout'] ?? 'grid',
            'enabled' => true,
        ];
    }

    private function enableContentFiltering(array $userData): array
    {
        $filters = [
            'price_range' => 'Price Range',
            'category' => 'Category',
            'brand' => 'Brand',
            'rating' => 'Rating',
            'availability' => 'Availability',
        ];

        return [
            'filters' => $filters,
            'user_filters' => $userData['filters'] ?? [],
            'enabled' => true,
        ];
    }

    private function enableToastNotifications(): array
    {
        $js = "
// Toast Notifications
class ToastNotification {
    constructor(message, type = 'info', duration = 5000) {
        this.message = message;
        this.type = type;
        this.duration = duration;
        this.create();
    }
    
    create() {
        const toast = document.createElement('div');
        toast.className = `toast toast-\${this.type}`;
        toast.innerHTML = `
            <div class='toast-content'>
                <span class='toast-message'>\${this.message}</span>
                <button class='toast-close'>&times;</button>
            </div>
        `;
        
        document.body.appendChild(toast);
        
        // Auto remove
        setTimeout(() => this.remove(), this.duration);
        
        // Manual close
        toast.querySelector('.toast-close').addEventListener('click', () => this.remove());
    }
    
    remove() {
        const toast = document.querySelector('.toast');
        if (toast) {
            toast.remove();
        }
    }
}

// Usage: new ToastNotification('Message', 'success');
";

        $css = "
/* Toast Notifications */
.toast {
    position: fixed;
    top: 20px;
    right: 20px;
    background: #fff;
    border: 1px solid #ddd;
    border-radius: 4px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    z-index: 1000;
    min-width: 300px;
    max-width: 500px;
}

.toast-content {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 12px 16px;
}

.toast-message {
    flex: 1;
    margin-right: 10px;
}

.toast-close {
    background: none;
    border: none;
    font-size: 18px;
    cursor: pointer;
    color: #666;
}

.toast-success { border-left: 4px solid #28a745; }
.toast-error { border-left: 4px solid #dc3545; }
.toast-warning { border-left: 4px solid #ffc107; }
.toast-info { border-left: 4px solid #17a2b8; }
";

        return [
            'js' => $js,
            'css' => $css,
            'enabled' => true,
        ];
    }

    private function enablePushNotifications(): array
    {
        $js = "
// Push Notifications
if ('serviceWorker' in navigator && 'PushManager' in window) {
    let serviceWorkerRegistration;

    navigator.serviceWorker.register('/sw.js')
        .then(registration => {
            serviceWorkerRegistration = registration;
            return registration.pushManager.getSubscription();
        })
        .then(subscription => {
            if (!subscription) {
                return serviceWorkerRegistration.pushManager.subscribe({
                    userVisibleOnly: true,
                    applicationServerKey: 'YOUR_VAPID_PUBLIC_KEY'
                });
            }
            return subscription;
        })
        .then(subscription => {
            // Send subscription to server
            fetch('/api/push-subscription', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(subscription)
            });
        });
}
";

        return [
            'js' => $js,
            'enabled' => false, // Requires HTTPS and user permission
        ];
    }

    private function enableEmailNotifications(array $userData): array
    {
        $config = [
            'welcome_email' => true,
            'order_confirmation' => true,
            'shipping_updates' => true,
            'promotional_emails' => false,
            'newsletter' => false,
        ];

        return [
            'config' => $config,
            'user_preferences' => $userData['email_preferences'] ?? $config,
            'enabled' => true,
        ];
    }

    private function enableBrowserNotifications(): array
    {
        $js = "
// Browser Notifications
function requestNotificationPermission() {
    if ('Notification' in window) {
        Notification.requestPermission().then(permission => {
            if (permission === 'granted') {
                console.log('Notification permission granted');
            }
        });
    }
}

function showNotification(title, body, icon = '/favicon.ico') {
    if (Notification.permission === 'granted') {
        new Notification(title, { body, icon });
    }
}
";

        return [
            'js' => $js,
            'enabled' => false, // Requires user permission
        ];
    }

    public function trackUserInteraction(string $action, array $data = []): void
    {
        $interaction = [
            'action' => $action,
            'data' => $data,
            'timestamp' => now(),
            'user_id' => auth()->id(),
            'session_id' => session()->getId(),
        ];

        // Store in analytics
        $this->analytics[] = $interaction;

        // Cache for real-time analysis
        $cacheKey = 'user_interactions_' . now()->format('Y-m-d-H');
        $interactions = Cache::get($cacheKey, []);
        $interactions[] = $interaction;
        Cache::put($cacheKey, $interactions, 3600);

        Log::info('User interaction tracked', $interaction);
    }

    public function getUserPreferences(): array
    {
        return $this->userPreferences;
    }

    public function setUserPreferences(array $preferences): void
    {
        $this->userPreferences = array_merge($this->userPreferences, $preferences);
        
        // Store in session
        Session::put('user_preferences', $this->userPreferences);
        
        Log::info('User preferences updated', $preferences);
    }

    public function getUXStatistics(): array
    {
        return [
            'config' => $this->uxConfig,
            'user_preferences' => $this->userPreferences,
            'analytics_count' => count($this->analytics),
            'features_enabled' => [
                'accessibility' => $this->uxConfig['accessibility'],
                'performance' => $this->uxConfig['performance'],
                'interaction' => $this->uxConfig['interaction'],
                'personalization' => $this->uxConfig['personalization'],
                'notifications' => $this->uxConfig['notifications'],
            ],
        ];
    }
}
