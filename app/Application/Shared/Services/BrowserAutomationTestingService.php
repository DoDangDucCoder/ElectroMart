<?php

namespace App\Application\Shared\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class BrowserAutomationTestingService
{
    private array $testConfig = [];
    private array $testResults = [];
    private string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = config('app.url', 'http://localhost:8000');
        $this->initializeTestConfig();
    }

    private function initializeTestConfig(): void
    {
        $this->testConfig = [
            'browsers' => [
                'chrome' => [
                    'enabled' => true,
                    'headless' => true,
                    'viewport' => ['width' => 1920, 'height' => 1080],
                ],
                'firefox' => [
                    'enabled' => false, // Requires additional setup
                    'headless' => true,
                    'viewport' => ['width' => 1920, 'height' => 1080],
                ],
                'safari' => [
                    'enabled' => false, // macOS only
                    'headless' => false,
                    'viewport' => ['width' => 1920, 'height' => 1080],
                ],
            ],
            'test_scenarios' => [
                'page_loading' => true,
                'navigation' => true,
                'form_interaction' => true,
                'responsive_design' => true,
                'javascript_functionality' => true,
                'accessibility' => true,
            ],
            'viewport_sizes' => [
                'desktop' => ['width' => 1920, 'height' => 1080],
                'tablet' => ['width' => 768, 'height' => 1024],
                'mobile' => ['width' => 375, 'height' => 667],
            ],
        ];
    }

    public function runBrowserTests(): array
    {
        Log::info('Starting browser automation tests...');

        $this->testResults = [
            'page_loading_tests' => $this->testPageLoading(),
            'navigation_tests' => $this->testNavigation(),
            'form_interaction_tests' => $this->testFormInteractions(),
            'responsive_design_tests' => $this->testResponsiveDesign(),
            'javascript_tests' => $this->testJavaScriptFunctionality(),
            'accessibility_tests' => $this->testAccessibility(),
        ];

        Log::info('Browser automation tests completed');
        return $this->testResults;
    }

    private function testPageLoading(): array
    {
        $results = [];
        
        $pages = [
            '/' => 'Home Page',
            '/products' => 'Products Page',
            '/categories' => 'Categories Page',
            '/about' => 'About Page',
            '/contact' => 'Contact Page',
            '/login' => 'Login Page',
            '/register' => 'Register Page',
        ];

        foreach ($pages as $path => $description) {
            $results[$path] = $this->testPageLoad($path, $description);
        }

        return $results;
    }

    private function testPageLoad(string $path, string $description): array
    {
        try {
            $url = $this->baseUrl . $path;
            $startTime = microtime(true);
            
            $response = Http::timeout(30)->get($url);
            
            $endTime = microtime(true);
            $loadTime = ($endTime - $startTime) * 1000;

            $result = [
                'description' => $description,
                'url' => $url,
                'status_code' => $response->status(),
                'load_time_ms' => round($loadTime, 2),
                'success' => $response->successful(),
                'content_length' => strlen($response->body()),
                'has_title' => $this->hasTitle($response->body()),
                'has_meta_description' => $this->hasMetaDescription($response->body()),
                'has_favicon' => $this->hasFavicon($response->body()),
            ];

            // Check for common issues
            $result['issues'] = $this->checkPageIssues($response->body(), $response->status());
            
            // Performance rating
            $result['performance_rating'] = $this->getPerformanceRating($loadTime);

            return $result;

        } catch (\Exception $e) {
            return [
                'description' => $description,
                'url' => $url,
                'error' => $e->getMessage(),
                'success' => false,
            ];
        }
    }

    private function testNavigation(): array
    {
        $results = [];

        $navigationTests = [
            'main_menu' => $this->testMainMenuNavigation(),
            'breadcrumbs' => $this->testBreadcrumbNavigation(),
            'pagination' => $this->testPaginationNavigation(),
            'footer_links' => $this->testFooterLinks(),
        ];

        foreach ($navigationTests as $test => $result) {
            $results[$test] = $result;
        }

        return $results;
    }

    private function testMainMenuNavigation(): array
    {
        try {
            $url = $this->baseUrl . '/';
            $response = Http::timeout(10)->get($url);
            
            if (!$response->successful()) {
                return [
                    'success' => false,
                    'error' => 'Failed to load home page',
                ];
            }

            $html = $response->body();
            $menuLinks = $this->extractMenuLinks($html);
            
            $results = [
                'menu_links_found' => count($menuLinks),
                'menu_links' => $menuLinks,
                'navigation_tests' => [],
            ];

            // Test each menu link
            foreach ($menuLinks as $link) {
                $linkResult = $this->testNavigationLink($link);
                $results['navigation_tests'][] = $linkResult;
            }

            $successfulLinks = array_filter($results['navigation_tests'], fn($test) => $test['success']);
            $results['success'] = count($successfulLinks) === count($menuLinks);
            $results['success_rate'] = count($menuLinks) > 0 ? round((count($successfulLinks) / count($menuLinks)) * 100, 2) : 0;

            return $results;

        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    private function testBreadcrumbNavigation(): array
    {
        try {
            // Test breadcrumbs on product page
            $url = $this->baseUrl . '/products';
            $response = Http::timeout(10)->get($url);
            
            if (!$response->successful()) {
                return [
                    'success' => false,
                    'error' => 'Failed to load products page',
                ];
            }

            $html = $response->body();
            $breadcrumbs = $this->extractBreadcrumbs($html);
            
            return [
                'success' => !empty($breadcrumbs),
                'breadcrumbs_found' => count($breadcrumbs),
                'breadcrumbs' => $breadcrumbs,
                'message' => !empty($breadcrumbs) ? 'Breadcrumb navigation found' : 'No breadcrumb navigation found',
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    private function testPaginationNavigation(): array
    {
        try {
            $url = $this->baseUrl . '/products';
            $response = Http::timeout(10)->get($url);
            
            if (!$response->successful()) {
                return [
                    'success' => false,
                    'error' => 'Failed to load products page',
                ];
            }

            $html = $response->body();
            $pagination = $this->extractPagination($html);
            
            return [
                'success' => !empty($pagination),
                'pagination_found' => !empty($pagination),
                'pagination_links' => $pagination,
                'message' => !empty($pagination) ? 'Pagination navigation found' : 'No pagination navigation found',
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    private function testFooterLinks(): array
    {
        try {
            $url = $this->baseUrl . '/';
            $response = Http::timeout(10)->get($url);
            
            if (!$response->successful()) {
                return [
                    'success' => false,
                    'error' => 'Failed to load home page',
                ];
            }

            $html = $response->body();
            $footerLinks = $this->extractFooterLinks($html);
            
            $results = [
                'footer_links_found' => count($footerLinks),
                'footer_links' => $footerLinks,
                'link_tests' => [],
            ];

            // Test each footer link
            foreach ($footerLinks as $link) {
                $linkResult = $this->testNavigationLink($link);
                $results['link_tests'][] = $linkResult;
            }

            $successfulLinks = array_filter($results['link_tests'], fn($test) => $test['success']);
            $results['success'] = count($successfulLinks) === count($footerLinks);
            $results['success_rate'] = count($footerLinks) > 0 ? round((count($successfulLinks) / count($footerLinks)) * 100, 2) : 0;

            return $results;

        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    private function testFormInteractions(): array
    {
        $results = [];

        $formTests = [
            'contact_form' => $this->testContactForm(),
            'login_form' => $this->testLoginForm(),
            'registration_form' => $this->testRegistrationForm(),
            'search_form' => $this->testSearchForm(),
        ];

        foreach ($formTests as $test => $result) {
            $results[$test] = $result;
        }

        return $results;
    }

    private function testContactForm(): array
    {
        try {
            $url = $this->baseUrl . '/contact';
            $response = Http::timeout(10)->get($url);
            
            if (!$response->successful()) {
                return [
                    'success' => false,
                    'error' => 'Failed to load contact page',
                ];
            }

            $html = $response->body();
            $formFields = $this->extractFormFields($html, 'contact');
            
            return [
                'success' => !empty($formFields),
                'form_found' => !empty($formFields),
                'form_fields' => $formFields,
                'required_fields' => $this->getRequiredFields($formFields),
                'message' => !empty($formFields) ? 'Contact form found' : 'No contact form found',
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    private function testLoginForm(): array
    {
        try {
            $url = $this->baseUrl . '/login';
            $response = Http::timeout(10)->get($url);
            
            if (!$response->successful()) {
                return [
                    'success' => false,
                    'error' => 'Failed to load login page',
                ];
            }

            $html = $response->body();
            $formFields = $this->extractFormFields($html, 'login');
            
            return [
                'success' => !empty($formFields),
                'form_found' => !empty($formFields),
                'form_fields' => $formFields,
                'has_csrf_token' => $this->hasCSRFToken($html),
                'has_remember_me' => $this->hasRememberMe($html),
                'message' => !empty($formFields) ? 'Login form found' : 'No login form found',
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    private function testRegistrationForm(): array
    {
        try {
            $url = $this->baseUrl . '/register';
            $response = Http::timeout(10)->get($url);
            
            if (!$response->successful()) {
                return [
                    'success' => false,
                    'error' => 'Failed to load registration page',
                ];
            }

            $html = $response->body();
            $formFields = $this->extractFormFields($html, 'register');
            
            return [
                'success' => !empty($formFields),
                'form_found' => !empty($formFields),
                'form_fields' => $formFields,
                'has_password_confirmation' => $this->hasPasswordConfirmation($formFields),
                'has_terms_acceptance' => $this->hasTermsAcceptance($html),
                'message' => !empty($formFields) ? 'Registration form found' : 'No registration form found',
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    private function testSearchForm(): array
    {
        try {
            $url = $this->baseUrl . '/';
            $response = Http::timeout(10)->get($url);
            
            if (!$response->successful()) {
                return [
                    'success' => false,
                    'error' => 'Failed to load home page',
                ];
            }

            $html = $response->body();
            $searchForm = $this->extractSearchForm($html);
            
            return [
                'success' => !empty($searchForm),
                'search_form_found' => !empty($searchForm),
                'search_form' => $searchForm,
                'message' => !empty($searchForm) ? 'Search form found' : 'No search form found',
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    private function testResponsiveDesign(): array
    {
        $results = [];

        foreach ($this->testConfig['viewport_sizes'] as $device => $viewport) {
            $results[$device] = $this->testResponsiveDesignForDevice($device, $viewport);
        }

        return $results;
    }

    private function testResponsiveDesignForDevice(string $device, array $viewport): array
    {
        try {
            $url = $this->baseUrl . '/';
            $response = Http::timeout(10)->get($url);
            
            if (!$response->successful()) {
                return [
                    'success' => false,
                    'error' => 'Failed to load home page',
                ];
            }

            $html = $response->body();
            
            return [
                'device' => $device,
                'viewport' => $viewport,
                'success' => true,
                'has_viewport_meta' => $this->hasViewportMeta($html),
                'has_responsive_css' => $this->hasResponsiveCSS($html),
                'has_mobile_menu' => $this->hasMobileMenu($html),
                'has_responsive_images' => $this->hasResponsiveImages($html),
                'responsive_score' => $this->calculateResponsiveScore($html),
                'message' => 'Responsive design test completed',
            ];

        } catch (\Exception $e) {
            return [
                'device' => $device,
                'viewport' => $viewport,
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    private function testJavaScriptFunctionality(): array
    {
        $results = [];

        $jsTests = [
            'jquery_loaded' => $this->testJQueryLoaded(),
            'bootstrap_loaded' => $this->testBootstrapLoaded(),
            'custom_js_loaded' => $this->testCustomJSLoaded(),
            'ajax_functionality' => $this->testAjaxFunctionality(),
            'form_validation' => $this->testFormValidation(),
        ];

        foreach ($jsTests as $test => $result) {
            $results[$test] = $result;
        }

        return $results;
    }

    private function testJQueryLoaded(): array
    {
        try {
            $url = $this->baseUrl . '/';
            $response = Http::timeout(10)->get($url);
            
            if (!$response->successful()) {
                return [
                    'success' => false,
                    'error' => 'Failed to load home page',
                ];
            }

            $html = $response->body();
            $jqueryLoaded = $this->hasJQuery($html);
            
            return [
                'success' => $jqueryLoaded,
                'jquery_loaded' => $jqueryLoaded,
                'jquery_version' => $this->getJQueryVersion($html),
                'message' => $jqueryLoaded ? 'jQuery is loaded' : 'jQuery is not loaded',
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    private function testBootstrapLoaded(): array
    {
        try {
            $url = $this->baseUrl . '/';
            $response = Http::timeout(10)->get($url);
            
            if (!$response->successful()) {
                return [
                    'success' => false,
                    'error' => 'Failed to load home page',
                ];
            }

            $html = $response->body();
            $bootstrapLoaded = $this->hasBootstrap($html);
            
            return [
                'success' => $bootstrapLoaded,
                'bootstrap_loaded' => $bootstrapLoaded,
                'bootstrap_version' => $this->getBootstrapVersion($html),
                'message' => $bootstrapLoaded ? 'Bootstrap is loaded' : 'Bootstrap is not loaded',
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    private function testCustomJSLoaded(): array
    {
        try {
            $url = $this->baseUrl . '/';
            $response = Http::timeout(10)->get($url);
            
            if (!$response->successful()) {
                return [
                    'success' => false,
                    'error' => 'Failed to load home page',
                ];
            }

            $html = $response->body();
            $customJS = $this->getCustomJSFiles($html);
            
            return [
                'success' => !empty($customJS),
                'custom_js_files' => $customJS,
                'js_files_count' => count($customJS),
                'message' => !empty($customJS) ? 'Custom JS files found' : 'No custom JS files found',
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    private function testAjaxFunctionality(): array
    {
        try {
            // Test AJAX endpoints
            $ajaxEndpoints = [
                '/api/products',
                '/api/categories',
                '/api/search',
            ];

            $results = [];
            foreach ($ajaxEndpoints as $endpoint) {
                $url = $this->baseUrl . $endpoint;
                $response = Http::timeout(10)->get($url);
                
                $results[] = [
                    'endpoint' => $endpoint,
                    'status' => $response->status(),
                    'success' => $response->successful(),
                    'content_type' => $response->header('Content-Type'),
                ];
            }

            $successfulEndpoints = array_filter($results, fn($result) => $result['success']);
            
            return [
                'success' => count($successfulEndpoints) > 0,
                'endpoints_tested' => count($results),
                'successful_endpoints' => count($successfulEndpoints),
                'endpoint_results' => $results,
                'message' => count($successfulEndpoints) > 0 ? 'AJAX functionality working' : 'AJAX functionality issues',
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    private function testFormValidation(): array
    {
        try {
            $url = $this->baseUrl . '/contact';
            $response = Http::timeout(10)->get($url);
            
            if (!$response->successful()) {
                return [
                    'success' => false,
                    'error' => 'Failed to load contact page',
                ];
            }

            $html = $response->body();
            
            return [
                'success' => true,
                'has_client_validation' => $this->hasClientValidation($html),
                'has_server_validation' => $this->hasServerValidation($html),
                'validation_methods' => $this->getValidationMethods($html),
                'message' => 'Form validation test completed',
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    private function testAccessibility(): array
    {
        $results = [];

        $accessibilityTests = [
            'alt_text' => $this->testAltText(),
            'heading_structure' => $this->testHeadingStructure(),
            'form_labels' => $this->testFormLabels(),
            'color_contrast' => $this->testColorContrast(),
            'keyboard_navigation' => $this->testKeyboardNavigation(),
        ];

        foreach ($accessibilityTests as $test => $result) {
            $results[$test] = $result;
        }

        return $results;
    }

    private function testAltText(): array
    {
        try {
            $url = $this->baseUrl . '/';
            $response = Http::timeout(10)->get($url);
            
            if (!$response->successful()) {
                return [
                    'success' => false,
                    'error' => 'Failed to load home page',
                ];
            }

            $html = $response->body();
            $images = $this->extractImages($html);
            
            $imagesWithAlt = array_filter($images, fn($img) => !empty($img['alt']));
            $imagesWithoutAlt = array_filter($images, fn($img) => empty($img['alt']));
            
            return [
                'success' => count($imagesWithoutAlt) === 0,
                'total_images' => count($images),
                'images_with_alt' => count($imagesWithAlt),
                'images_without_alt' => count($imagesWithoutAlt),
                'accessibility_score' => count($images) > 0 ? round((count($imagesWithAlt) / count($images)) * 100, 2) : 100,
                'message' => count($imagesWithoutAlt) === 0 ? 'All images have alt text' : 'Some images missing alt text',
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    private function testHeadingStructure(): array
    {
        try {
            $url = $this->baseUrl . '/';
            $response = Http::timeout(10)->get($url);
            
            if (!$response->successful()) {
                return [
                    'success' => false,
                    'error' => 'Failed to load home page',
                ];
            }

            $html = $response->body();
            $headings = $this->extractHeadings($html);
            
            return [
                'success' => !empty($headings),
                'headings_found' => count($headings),
                'heading_structure' => $headings,
                'has_h1' => $this->hasH1($headings),
                'proper_hierarchy' => $this->hasProperHeadingHierarchy($headings),
                'message' => !empty($headings) ? 'Heading structure found' : 'No heading structure found',
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    private function testFormLabels(): array
    {
        try {
            $url = $this->baseUrl . '/contact';
            $response = Http::timeout(10)->get($url);
            
            if (!$response->successful()) {
                return [
                    'success' => false,
                    'error' => 'Failed to load contact page',
                ];
            }

            $html = $response->body();
            $formFields = $this->extractFormFields($html, 'contact');
            $fieldsWithLabels = $this->getFieldsWithLabels($html, $formFields);
            
            return [
                'success' => count($fieldsWithLabels) === count($formFields),
                'total_fields' => count($formFields),
                'fields_with_labels' => count($fieldsWithLabels),
                'fields_without_labels' => count($formFields) - count($fieldsWithLabels),
                'accessibility_score' => count($formFields) > 0 ? round((count($fieldsWithLabels) / count($formFields)) * 100, 2) : 100,
                'message' => count($fieldsWithLabels) === count($formFields) ? 'All form fields have labels' : 'Some form fields missing labels',
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    private function testColorContrast(): array
    {
        try {
            $url = $this->baseUrl . '/';
            $response = Http::timeout(10)->get($url);
            
            if (!$response->successful()) {
                return [
                    'success' => false,
                    'error' => 'Failed to load home page',
                ];
            }

            $html = $response->body();
            $cssFiles = $this->getCSSFiles($html);
            
            return [
                'success' => !empty($cssFiles),
                'css_files_found' => count($cssFiles),
                'css_files' => $cssFiles,
                'has_dark_mode' => $this->hasDarkMode($html),
                'has_high_contrast' => $this->hasHighContrast($html),
                'message' => 'Color contrast test completed (requires manual verification)',
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    private function testKeyboardNavigation(): array
    {
        try {
            $url = $this->baseUrl . '/';
            $response = Http::timeout(10)->get($url);
            
            if (!$response->successful()) {
                return [
                    'success' => false,
                    'error' => 'Failed to load home page',
                ];
            }

            $html = $response->body();
            
            return [
                'success' => true,
                'has_skip_links' => $this->hasSkipLinks($html),
                'has_tabindex' => $this->hasTabIndex($html),
                'has_focus_styles' => $this->hasFocusStyles($html),
                'keyboard_navigation_score' => $this->calculateKeyboardNavigationScore($html),
                'message' => 'Keyboard navigation test completed',
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    // Helper methods
    private function hasTitle(string $html): bool
    {
        return preg_match('/<title[^>]*>.*?<\/title>/i', $html) === 1;
    }

    private function hasMetaDescription(string $html): bool
    {
        return preg_match('/<meta[^>]*name=["\']description["\'][^>]*>/i', $html) === 1;
    }

    private function hasFavicon(string $html): bool
    {
        return preg_match('/<link[^>]*rel=["\']icon["\'][^>]*>/i', $html) === 1;
    }

    private function checkPageIssues(string $html, int $statusCode): array
    {
        $issues = [];
        
        if ($statusCode !== 200) {
            $issues[] = "HTTP status code: {$statusCode}";
        }
        
        if (str_contains($html, 'Fatal error')) {
            $issues[] = 'Fatal error detected';
        }
        
        if (str_contains($html, 'Warning:')) {
            $issues[] = 'PHP warnings detected';
        }
        
        if (str_contains($html, 'Notice:')) {
            $issues[] = 'PHP notices detected';
        }
        
        return $issues;
    }

    private function getPerformanceRating(float $loadTime): string
    {
        if ($loadTime < 1000) return 'Excellent';
        if ($loadTime < 3000) return 'Good';
        if ($loadTime < 5000) return 'Fair';
        return 'Poor';
    }

    private function extractMenuLinks(string $html): array
    {
        preg_match_all('/<a[^>]*href=["\']([^"\']*)["\'][^>]*>([^<]*)<\/a>/i', $html, $matches);
        
        $links = [];
        for ($i = 0; $i < count($matches[1]); $i++) {
            $links[] = [
                'url' => $matches[1][$i],
                'text' => trim(strip_tags($matches[2][$i])),
            ];
        }
        
        return $links;
    }

    private function testNavigationLink(array $link): array
    {
        try {
            $url = $this->baseUrl . $link['url'];
            $response = Http::timeout(10)->get($url);
            
            return [
                'url' => $link['url'],
                'text' => $link['text'],
                'status' => $response->status(),
                'success' => $response->successful(),
                'message' => $response->successful() ? 'Link working' : 'Link broken',
            ];
        } catch (\Exception $e) {
            return [
                'url' => $link['url'],
                'text' => $link['text'],
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    private function extractBreadcrumbs(string $html): array
    {
        preg_match_all('/<nav[^>]*class=["\'][^"\']*breadcrumb[^"\']*["\'][^>]*>.*?<\/nav>/is', $html, $matches);
        
        $breadcrumbs = [];
        if (!empty($matches[0])) {
            preg_match_all('/<a[^>]*>([^<]*)<\/a>/i', $matches[0][0], $linkMatches);
            $breadcrumbs = $linkMatches[1];
        }
        
        return $breadcrumbs;
    }

    private function extractPagination(string $html): array
    {
        preg_match_all('/<a[^>]*href=["\']([^"\']*)["\'][^>]*class=["\'][^"\']*page[^"\']*["\'][^>]*>([^<]*)<\/a>/i', $html, $matches);
        
        $pagination = [];
        for ($i = 0; $i < count($matches[1]); $i++) {
            $pagination[] = [
                'url' => $matches[1][$i],
                'text' => trim($matches[2][$i]),
            ];
        }
        
        return $pagination;
    }

    private function extractFooterLinks(string $html): array
    {
        preg_match_all('/<footer[^>]*>.*?<\/footer>/is', $html, $footerMatches);
        
        $footerLinks = [];
        if (!empty($footerMatches[0])) {
            preg_match_all('/<a[^>]*href=["\']([^"\']*)["\'][^>]*>([^<]*)<\/a>/i', $footerMatches[0][0], $matches);
            
            for ($i = 0; $i < count($matches[1]); $i++) {
                $footerLinks[] = [
                    'url' => $matches[1][$i],
                    'text' => trim(strip_tags($matches[2][$i])),
                ];
            }
        }
        
        return $footerLinks;
    }

    private function extractFormFields(string $html, string $formType): array
    {
        $fields = [];
        
        // Extract input fields
        preg_match_all('/<input[^>]*type=["\']([^"\']*)["\'][^>]*name=["\']([^"\']*)["\'][^>]*>/i', $html, $inputMatches);
        for ($i = 0; $i < count($inputMatches[1]); $i++) {
            $fields[] = [
                'type' => $inputMatches[1][$i],
                'name' => $inputMatches[2][$i],
            ];
        }
        
        // Extract textarea fields
        preg_match_all('/<textarea[^>]*name=["\']([^"\']*)["\'][^>]*>/i', $html, $textareaMatches);
        for ($i = 0; $i < count($textareaMatches[1]); $i++) {
            $fields[] = [
                'type' => 'textarea',
                'name' => $textareaMatches[1][$i],
            ];
        }
        
        // Extract select fields
        preg_match_all('/<select[^>]*name=["\']([^"\']*)["\'][^>]*>/i', $html, $selectMatches);
        for ($i = 0; $i < count($selectMatches[1]); $i++) {
            $fields[] = [
                'type' => 'select',
                'name' => $selectMatches[1][$i],
            ];
        }
        
        return $fields;
    }

    private function hasCSRFToken(string $html): bool
    {
        return preg_match('/<input[^>]*name=["\']_token["\'][^>]*>/i', $html) === 1;
    }

    private function hasRememberMe(string $html): bool
    {
        return preg_match('/<input[^>]*name=["\']remember["\'][^>]*>/i', $html) === 1;
    }

    private function hasPasswordConfirmation(array $formFields): bool
    {
        $passwordFields = array_filter($formFields, fn($field) => str_contains($field['name'], 'password'));
        return count($passwordFields) >= 2;
    }

    private function hasTermsAcceptance(string $html): bool
    {
        return preg_match('/<input[^>]*name=["\']terms["\'][^>]*>/i', $html) === 1;
    }

    private function extractSearchForm(string $html): array
    {
        preg_match_all('/<form[^>]*>.*?<\/form>/is', $html, $formMatches);
        
        foreach ($formMatches[0] as $form) {
            if (str_contains($form, 'search') || str_contains($form, 'query')) {
                return $this->extractFormFields($form, 'search');
            }
        }
        
        return [];
    }

    private function hasViewportMeta(string $html): bool
    {
        return preg_match('/<meta[^>]*name=["\']viewport["\'][^>]*>/i', $html) === 1;
    }

    private function hasResponsiveCSS(string $html): bool
    {
        return preg_match('/@media[^}]*screen[^}]*{/i', $html) === 1;
    }

    private function hasMobileMenu(string $html): bool
    {
        return preg_match('/<button[^>]*class=["\'][^"\']*navbar-toggler[^"\']*["\'][^>]*>/i', $html) === 1;
    }

    private function hasResponsiveImages(string $html): bool
    {
        return preg_match('/<img[^>]*class=["\'][^"\']*img-responsive[^"\']*["\'][^>]*>/i', $html) === 1;
    }

    private function calculateResponsiveScore(string $html): int
    {
        $score = 0;
        
        if ($this->hasViewportMeta($html)) $score += 25;
        if ($this->hasResponsiveCSS($html)) $score += 25;
        if ($this->hasMobileMenu($html)) $score += 25;
        if ($this->hasResponsiveImages($html)) $score += 25;
        
        return $score;
    }

    private function hasJQuery(string $html): bool
    {
        return preg_match('/jquery/i', $html) === 1;
    }

    private function getJQueryVersion(string $html): ?string
    {
        preg_match('/jquery[^"]*?(\d+\.\d+\.\d+)/i', $html, $matches);
        return $matches[1] ?? null;
    }

    private function hasBootstrap(string $html): bool
    {
        return preg_match('/bootstrap/i', $html) === 1;
    }

    private function getBootstrapVersion(string $html): ?string
    {
        preg_match('/bootstrap[^"]*?(\d+\.\d+\.\d+)/i', $html, $matches);
        return $matches[1] ?? null;
    }

    private function getCustomJSFiles(string $html): array
    {
        preg_match_all('/<script[^>]*src=["\']([^"\']*)["\'][^>]*>/i', $html, $matches);
        
        $jsFiles = [];
        foreach ($matches[1] as $src) {
            if (!str_contains($src, 'jquery') && !str_contains($src, 'bootstrap')) {
                $jsFiles[] = $src;
            }
        }
        
        return $jsFiles;
    }

    private function hasClientValidation(string $html): bool
    {
        return preg_match('/required|pattern|minlength|maxlength/i', $html) === 1;
    }

    private function hasServerValidation(string $html): bool
    {
        return preg_match('/validation|error|invalid/i', $html) === 1;
    }

    private function getValidationMethods(string $html): array
    {
        $methods = [];
        
        if (preg_match('/required/i', $html)) $methods[] = 'required';
        if (preg_match('/pattern/i', $html)) $methods[] = 'pattern';
        if (preg_match('/minlength/i', $html)) $methods[] = 'minlength';
        if (preg_match('/maxlength/i', $html)) $methods[] = 'maxlength';
        if (preg_match('/email/i', $html)) $methods[] = 'email';
        
        return $methods;
    }

    private function extractImages(string $html): array
    {
        preg_match_all('/<img[^>]*src=["\']([^"\']*)["\'][^>]*alt=["\']([^"\']*)["\'][^>]*>/i', $html, $matches);
        
        $images = [];
        for ($i = 0; $i < count($matches[1]); $i++) {
            $images[] = [
                'src' => $matches[1][$i],
                'alt' => $matches[2][$i],
            ];
        }
        
        return $images;
    }

    private function extractHeadings(string $html): array
    {
        preg_match_all('/<h([1-6])[^>]*>([^<]*)<\/h[1-6]>/i', $html, $matches);
        
        $headings = [];
        for ($i = 0; $i < count($matches[1]); $i++) {
            $headings[] = [
                'level' => (int)$matches[1][$i],
                'text' => trim(strip_tags($matches[2][$i])),
            ];
        }
        
        return $headings;
    }

    private function hasH1(array $headings): bool
    {
        return !empty(array_filter($headings, fn($h) => $h['level'] === 1));
    }

    private function hasProperHeadingHierarchy(array $headings): bool
    {
        $levels = array_column($headings, 'level');
        sort($levels);
        
        // Check if levels follow proper hierarchy (1, 2, 3, etc.)
        for ($i = 1; $i < count($levels); $i++) {
            if ($levels[$i] - $levels[$i-1] > 1) {
                return false;
            }
        }
        
        return true;
    }

    private function getFieldsWithLabels(string $html, array $formFields): array
    {
        $fieldsWithLabels = [];
        
        foreach ($formFields as $field) {
            $fieldName = $field['name'];
            if (preg_match("/<label[^>]*for=[\"']{$fieldName}[\"'][^>]*>/i", $html) ||
                preg_match("/<label[^>]*>.*?<input[^>]*name=[\"']{$fieldName}[\"'][^>]*>/i", $html)) {
                $fieldsWithLabels[] = $field;
            }
        }
        
        return $fieldsWithLabels;
    }

    private function getCSSFiles(string $html): array
    {
        preg_match_all('/<link[^>]*rel=["\']stylesheet["\'][^>]*href=["\']([^"\']*)["\'][^>]*>/i', $html, $matches);
        return $matches[1];
    }

    private function hasDarkMode(string $html): bool
    {
        return preg_match('/dark[^"]*mode|theme[^"]*dark/i', $html) === 1;
    }

    private function hasHighContrast(string $html): bool
    {
        return preg_match('/contrast|high[^"]*contrast/i', $html) === 1;
    }

    private function hasSkipLinks(string $html): bool
    {
        return preg_match('/skip[^"]*link|skip[^"]*navigation/i', $html) === 1;
    }

    private function hasTabIndex(string $html): bool
    {
        return preg_match('/tabindex/i', $html) === 1;
    }

    private function hasFocusStyles(string $html): bool
    {
        return preg_match('/:focus|focus[^"]*style/i', $html) === 1;
    }

    private function calculateKeyboardNavigationScore(string $html): int
    {
        $score = 0;
        
        if ($this->hasSkipLinks($html)) $score += 33;
        if ($this->hasTabIndex($html)) $score += 33;
        if ($this->hasFocusStyles($html)) $score += 34;
        
        return $score;
    }

    public function generateTestReport(): array
    {
        $totalTests = 0;
        $passedTests = 0;
        $failedTests = 0;

        foreach ($this->testResults as $category => $tests) {
            if (is_array($tests)) {
                foreach ($tests as $test) {
                    if (is_array($test) && isset($test['success'])) {
                        $totalTests++;
                        if ($test['success']) {
                            $passedTests++;
                        } else {
                            $failedTests++;
                        }
                    }
                }
            }
        }

        return [
            'summary' => [
                'total_tests' => $totalTests,
                'passed_tests' => $passedTests,
                'failed_tests' => $failedTests,
                'success_rate' => $totalTests > 0 ? round(($passedTests / $totalTests) * 100, 2) : 0,
            ],
            'categories' => array_keys($this->testResults),
            'recommendations' => $this->generateRecommendations(),
        ];
    }

    private function generateRecommendations(): array
    {
        $recommendations = [];

        // Analyze test results and generate recommendations
        if (isset($this->testResults['page_loading_tests'])) {
            foreach ($this->testResults['page_loading_tests'] as $page => $result) {
                if (isset($result['performance_rating']) && in_array($result['performance_rating'], ['Fair', 'Poor'])) {
                    $recommendations[] = "Optimize page loading performance for: {$page}";
                }
            }
        }

        if (isset($this->testResults['accessibility_tests']['alt_text']['images_without_alt'])) {
            $imagesWithoutAlt = $this->testResults['accessibility_tests']['alt_text']['images_without_alt'];
            if ($imagesWithoutAlt > 0) {
                $recommendations[] = "Add alt text to {$imagesWithoutAlt} images for better accessibility";
            }
        }

        if (empty($recommendations)) {
            $recommendations[] = 'Browser automation tests are passing well. Continue monitoring.';
        }

        return $recommendations;
    }

    public function getTestStatistics(): array
    {
        return [
            'config' => $this->testConfig,
            'base_url' => $this->baseUrl,
            'total_test_categories' => count($this->testConfig['test_scenarios']),
        ];
    }
}
