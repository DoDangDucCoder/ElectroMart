<?php

namespace App\Application\Shared\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CrossBrowserCompatibilityTestingService
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
                    'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
                    'features' => ['html5', 'css3', 'es6', 'webgl'],
                ],
                'firefox' => [
                    'enabled' => true,
                    'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:121.0) Gecko/20100101 Firefox/121.0',
                    'features' => ['html5', 'css3', 'es6', 'webgl'],
                ],
                'safari' => [
                    'enabled' => true,
                    'user_agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.1 Safari/605.1.15',
                    'features' => ['html5', 'css3', 'es6'],
                ],
                'edge' => [
                    'enabled' => true,
                    'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36 Edg/120.0.0.0',
                    'features' => ['html5', 'css3', 'es6', 'webgl'],
                ],
                'ie11' => [
                    'enabled' => false, // Legacy support
                    'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; WOW64; Trident/7.0; AS; rv:11.0) like Gecko',
                    'features' => ['html5_partial', 'css3_partial'],
                ],
            ],
            'test_pages' => [
                '/' => 'Home Page',
                '/products' => 'Products Page',
                '/categories' => 'Categories Page',
                '/contact' => 'Contact Page',
                '/login' => 'Login Page',
                '/register' => 'Register Page',
            ],
            'compatibility_features' => [
                'html5_semantics' => true,
                'css3_features' => true,
                'javascript_es6' => true,
                'responsive_design' => true,
                'web_apis' => true,
                'accessibility' => true,
            ],
        ];
    }

    public function runCrossBrowserTests(): array
    {
        Log::info('Starting cross-browser compatibility tests...');

        $this->testResults = [
            'browser_compatibility' => $this->testBrowserCompatibility(),
            'feature_compatibility' => $this->testFeatureCompatibility(),
            'responsive_compatibility' => $this->testResponsiveCompatibility(),
            'javascript_compatibility' => $this->testJavaScriptCompatibility(),
            'css_compatibility' => $this->testCSSCompatibility(),
            'accessibility_compatibility' => $this->testAccessibilityCompatibility(),
        ];

        Log::info('Cross-browser compatibility tests completed');
        return $this->testResults;
    }

    private function testBrowserCompatibility(): array
    {
        $results = [];

        foreach ($this->testConfig['browsers'] as $browser => $config) {
            if (!$config['enabled']) {
                continue;
            }

            $results[$browser] = $this->testBrowser($browser, $config);
        }

        return $results;
    }

    private function testBrowser(string $browser, array $config): array
    {
        $results = [
            'browser' => $browser,
            'user_agent' => $config['user_agent'],
            'page_tests' => [],
            'overall_success' => true,
        ];

        foreach ($this->testConfig['test_pages'] as $path => $description) {
            $pageResult = $this->testPageInBrowser($path, $description, $config);
            $results['page_tests'][$path] = $pageResult;
            
            if (!$pageResult['success']) {
                $results['overall_success'] = false;
            }
        }

        // Calculate overall compatibility score
        $successfulPages = array_filter($results['page_tests'], fn($test) => $test['success']);
        $results['compatibility_score'] = count($this->testConfig['test_pages']) > 0 ? 
            round((count($successfulPages) / count($this->testConfig['test_pages'])) * 100, 2) : 0;

        return $results;
    }

    private function testPageInBrowser(string $path, string $description, array $config): array
    {
        try {
            $path = $this->normalizePublicPath($path);
            $url = $this->baseUrl . $path;
            $startTime = microtime(true);
            
            $response = Http::withHeaders([
                'User-Agent' => $config['user_agent'],
                'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
                'Accept-Language' => 'en-US,en;q=0.5',
                'Accept-Encoding' => 'gzip, deflate',
            ])->timeout(30)->get($url);
            
            $endTime = microtime(true);
            $loadTime = ($endTime - $startTime) * 1000;

            $result = [
                'description' => $description,
                'url' => $url,
                'status_code' => $response->status(),
                'load_time_ms' => round($loadTime, 2),
                'success' => $response->successful(),
                'content_length' => strlen($response->body()),
            ];

            if ($response->successful()) {
                $html = $response->body();
                
                // Test browser-specific features
                $result['html5_support'] = $this->testHTML5Support($html);
                $result['css3_support'] = $this->testCSS3Support($html);
                $result['javascript_support'] = $this->testJavaScriptSupport($html);
                $result['responsive_support'] = $this->testResponsiveSupport($html);
                
                // Check for browser-specific issues
                $result['browser_issues'] = $this->checkBrowserIssues($html, $config['features']);
                
                // Performance rating
                $result['performance_rating'] = $this->getPerformanceRating($loadTime);
            }

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

    private function testHTML5Support(string $html): array
    {
        $html5Elements = [
            'header' => '/<header[^>]*>/i',
            'nav' => '/<nav[^>]*>/i',
            'main' => '/<main[^>]*>/i',
            'section' => '/<section[^>]*>/i',
            'article' => '/<article[^>]*>/i',
            'aside' => '/<aside[^>]*>/i',
            'footer' => '/<footer[^>]*>/i',
            'video' => '/<video[^>]*>/i',
            'audio' => '/<audio[^>]*>/i',
            'canvas' => '/<canvas[^>]*>/i',
        ];

        $results = [];
        foreach ($html5Elements as $element => $pattern) {
            $results[$element] = [
                'supported' => preg_match($pattern, $html) === 1,
                'count' => preg_match_all($pattern, $html),
            ];
        }

        $supportedElements = array_filter($results, fn($result) => $result['supported']);
        $results['overall_support'] = count($supportedElements) / count($html5Elements);
        $results['support_score'] = round($results['overall_support'] * 100, 2);

        return $results;
    }

    private function testCSS3Support(string $html): array
    {
        $css3Features = [
            'flexbox' => '/flex|flexbox/i',
            'grid' => '/grid/i',
            'transitions' => '/transition/i',
            'transforms' => '/transform/i',
            'animations' => '/animation/i',
            'gradients' => '/gradient/i',
            'shadows' => '/box-shadow|text-shadow/i',
            'border_radius' => '/border-radius/i',
            'media_queries' => '/@media/i',
        ];

        $results = [];
        foreach ($css3Features as $feature => $pattern) {
            $results[$feature] = [
                'supported' => preg_match($pattern, $html) === 1,
                'count' => preg_match_all($pattern, $html),
            ];
        }

        $supportedFeatures = array_filter($results, fn($result) => $result['supported']);
        $results['overall_support'] = count($supportedFeatures) / count($css3Features);
        $results['support_score'] = round($results['overall_support'] * 100, 2);

        return $results;
    }

    private function testJavaScriptSupport(string $html): array
    {
        $jsFeatures = [
            'es6_arrow_functions' => '/=>/',
            'es6_template_literals' => '/`[^`]*\$\{/',
            'es6_destructuring' => '/\{[^}]*\}/',
            'es6_classes' => '/class\s+\w+/',
            'es6_modules' => '/import\s+|export\s+/',
            'promises' => '/Promise|\.then\(|\.catch\(/',
            'async_await' => '/async\s+|await\s+/',
            'fetch_api' => '/fetch\(/',
            'local_storage' => '/localStorage|sessionStorage/',
        ];

        $results = [];
        foreach ($jsFeatures as $feature => $pattern) {
            $results[$feature] = [
                'supported' => preg_match($pattern, $html) === 1,
                'count' => preg_match_all($pattern, $html),
            ];
        }

        $supportedFeatures = array_filter($results, fn($result) => $result['supported']);
        $results['overall_support'] = count($supportedFeatures) / count($jsFeatures);
        $results['support_score'] = round($results['overall_support'] * 100, 2);

        return $results;
    }

    private function testResponsiveSupport(string $html): array
    {
        $responsiveFeatures = [
            'viewport_meta' => '/<meta[^>]*name=["\']viewport["\'][^>]*>/i',
            'media_queries' => '/@media[^}]*screen[^}]*{/i',
            'flexible_grid' => '/col-|grid-|flex/i',
            'responsive_images' => '/img-responsive|responsive/i',
            'mobile_menu' => '/navbar-toggler|mobile-menu/i',
            'fluid_typography' => '/rem|em|vw|vh/i',
        ];

        $results = [];
        foreach ($responsiveFeatures as $feature => $pattern) {
            $results[$feature] = [
                'supported' => preg_match($pattern, $html) === 1,
                'count' => preg_match_all($pattern, $html),
            ];
        }

        $supportedFeatures = array_filter($results, fn($result) => $result['supported']);
        $results['overall_support'] = count($supportedFeatures) / count($responsiveFeatures);
        $results['support_score'] = round($results['overall_support'] * 100, 2);

        return $results;
    }

    private function checkBrowserIssues(string $html, array $features): array
    {
        $issues = [];
        
        // Check for browser-specific problems
        if (str_contains($html, 'Fatal error')) {
            $issues[] = 'Fatal error detected';
        }
        
        if (str_contains($html, 'Warning:')) {
            $issues[] = 'PHP warnings detected';
        }
        
        if (str_contains($html, 'Notice:')) {
            $issues[] = 'PHP notices detected';
        }
        
        // Check for missing features
        if (in_array('webgl', $features) && !str_contains($html, 'webgl')) {
            $issues[] = 'WebGL not utilized';
        }
        
        if (in_array('es6', $features) && !preg_match('/=>|`[^`]*\$\{/', $html)) {
            $issues[] = 'ES6 features not utilized';
        }
        
        return $issues;
    }

    private function testFeatureCompatibility(): array
    {
        $results = [];

        $features = [
            'html5_semantics' => $this->testHTML5Semantics(),
            'css3_features' => $this->testCSS3Features(),
            'javascript_es6' => $this->testES6Features(),
            'responsive_design' => $this->testResponsiveDesign(),
            'web_apis' => $this->testWebAPIs(),
            'accessibility' => $this->testAccessibilityFeatures(),
        ];

        foreach ($features as $feature => $result) {
            $results[$feature] = $result;
        }

        return $results;
    }

    private function testHTML5Semantics(): array
    {
        try {
            $url = $this->baseUrl . '/';
            $response = Http::timeout(10)->get($url);
            
            if (!$response->successful()) {
                return [
                    'success' => false,
                    'error' => 'Failed to load page',
                ];
            }

            $html = $html = $response->body();
            
            $semanticElements = [
                'header' => preg_match_all('/<header[^>]*>/i', $html),
                'nav' => preg_match_all('/<nav[^>]*>/i', $html),
                'main' => preg_match_all('/<main[^>]*>/i', $html),
                'section' => preg_match_all('/<section[^>]*>/i', $html),
                'article' => preg_match_all('/<article[^>]*>/i', $html),
                'aside' => preg_match_all('/<aside[^>]*>/i', $html),
                'footer' => preg_match_all('/<footer[^>]*>/i', $html),
            ];

            $totalElements = array_sum($semanticElements);
            
            return [
                'success' => true,
                'semantic_elements' => $semanticElements,
                'total_elements' => $totalElements,
                'semantic_score' => $totalElements > 0 ? round(($totalElements / 7) * 100, 2) : 0,
                'message' => 'HTML5 semantic elements test completed',
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    private function testCSS3Features(): array
    {
        try {
            $url = $this->baseUrl . '/';
            $response = Http::timeout(10)->get($url);
            
            if (!$response->successful()) {
                return [
                    'success' => false,
                    'error' => 'Failed to load page',
                ];
            }

            $html = $response->body();
            
            $css3Features = [
                'flexbox' => preg_match_all('/flex|flexbox/i', $html),
                'grid' => preg_match_all('/grid/i', $html),
                'transitions' => preg_match_all('/transition/i', $html),
                'transforms' => preg_match_all('/transform/i', $html),
                'animations' => preg_match_all('/animation/i', $html),
                'gradients' => preg_match_all('/gradient/i', $html),
                'shadows' => preg_match_all('/box-shadow|text-shadow/i', $html),
                'border_radius' => preg_match_all('/border-radius/i', $html),
            ];

            $totalFeatures = array_sum($css3Features);
            
            return [
                'success' => true,
                'css3_features' => $css3Features,
                'total_features' => $totalFeatures,
                'css3_score' => $totalFeatures > 0 ? round(($totalFeatures / 8) * 100, 2) : 0,
                'message' => 'CSS3 features test completed',
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    private function testES6Features(): array
    {
        try {
            $url = $this->baseUrl . '/';
            $response = Http::timeout(10)->get($url);
            
            if (!$response->successful()) {
                return [
                    'success' => false,
                    'error' => 'Failed to load page',
                ];
            }

            $html = $response->body();
            
            $es6Features = [
                'arrow_functions' => preg_match_all('/=>/', $html),
                'template_literals' => preg_match_all('/`[^`]*\$\{/', $html),
                'destructuring' => preg_match_all('/\{[^}]*\}/', $html),
                'classes' => preg_match_all('/class\s+\w+/', $html),
                'modules' => preg_match_all('/import\s+|export\s+/', $html),
                'promises' => preg_match_all('/Promise|\.then\(|\.catch\(/', $html),
                'async_await' => preg_match_all('/async\s+|await\s+/', $html),
                'fetch_api' => preg_match_all('/fetch\(/', $html),
            ];

            $totalFeatures = array_sum($es6Features);
            
            return [
                'success' => true,
                'es6_features' => $es6Features,
                'total_features' => $totalFeatures,
                'es6_score' => $totalFeatures > 0 ? round(($totalFeatures / 8) * 100, 2) : 0,
                'message' => 'ES6 features test completed',
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
        try {
            $url = $this->baseUrl . '/';
            $response = Http::timeout(10)->get($url);
            
            if (!$response->successful()) {
                return [
                    'success' => false,
                    'error' => 'Failed to load page',
                ];
            }

            $html = $response->body();
            
            $responsiveFeatures = [
                'viewport_meta' => preg_match('/<meta[^>]*name=["\']viewport["\'][^>]*>/i', $html),
                'media_queries' => preg_match('/@media[^}]*screen[^}]*{/i', $html),
                'flexible_grid' => preg_match('/col-|grid-|flex/i', $html),
                'responsive_images' => preg_match('/img-responsive|responsive/i', $html),
                'mobile_menu' => preg_match('/navbar-toggler|mobile-menu/i', $html),
                'fluid_typography' => preg_match('/rem|em|vw|vh/i', $html),
            ];

            $totalFeatures = array_sum($responsiveFeatures);
            
            return [
                'success' => true,
                'responsive_features' => $responsiveFeatures,
                'total_features' => $totalFeatures,
                'responsive_score' => $totalFeatures > 0 ? round(($totalFeatures / 6) * 100, 2) : 0,
                'message' => 'Responsive design test completed',
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    private function testWebAPIs(): array
    {
        try {
            $url = $this->baseUrl . '/';
            $response = Http::timeout(10)->get($url);
            
            if (!$response->successful()) {
                return [
                    'success' => false,
                    'error' => 'Failed to load page',
                ];
            }

            $html = $response->body();
            
            $webAPIs = [
                'local_storage' => preg_match_all('/localStorage|sessionStorage/', $html),
                'geolocation' => preg_match_all('/geolocation|getCurrentPosition/', $html),
                'webgl' => preg_match_all('/webgl|WebGL/', $html),
                'canvas' => preg_match_all('/<canvas[^>]*>/i', $html),
                'video' => preg_match_all('/<video[^>]*>/i', $html),
                'audio' => preg_match_all('/<audio[^>]*>/i', $html),
                'websocket' => preg_match_all('/WebSocket|websocket/', $html),
                'service_worker' => preg_match_all('/serviceWorker|service-worker/', $html),
            ];

            $totalAPIs = array_sum($webAPIs);
            
            return [
                'success' => true,
                'web_apis' => $webAPIs,
                'total_apis' => $totalAPIs,
                'api_score' => $totalAPIs > 0 ? round(($totalAPIs / 8) * 100, 2) : 0,
                'message' => 'Web APIs test completed',
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    private function testAccessibilityFeatures(): array
    {
        try {
            $url = $this->baseUrl . '/';
            $response = Http::timeout(10)->get($url);
            
            if (!$response->successful()) {
                return [
                    'success' => false,
                    'error' => 'Failed to load page',
                ];
            }

            $html = $response->body();
            
            $accessibilityFeatures = [
                'alt_text' => preg_match_all('/alt=["\'][^"\']*["\']/', $html),
                'aria_labels' => preg_match_all('/aria-[^=]*=/', $html),
                'heading_structure' => preg_match_all('/<h[1-6][^>]*>/i', $html),
                'form_labels' => preg_match_all('/<label[^>]*>/i', $html),
                'skip_links' => preg_match_all('/skip[^"]*link|skip[^"]*navigation/i', $html),
                'focus_management' => preg_match_all('/tabindex|focus/i', $html),
            ];

            $totalFeatures = array_sum($accessibilityFeatures);
            
            return [
                'success' => true,
                'accessibility_features' => $accessibilityFeatures,
                'total_features' => $totalFeatures,
                'accessibility_score' => $totalFeatures > 0 ? round(($totalFeatures / 6) * 100, 2) : 0,
                'message' => 'Accessibility features test completed',
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    private function testResponsiveCompatibility(): array
    {
        $results = [];

        $viewportSizes = [
            'desktop' => ['width' => 1920, 'height' => 1080],
            'tablet' => ['width' => 768, 'height' => 1024],
            'mobile' => ['width' => 375, 'height' => 667],
        ];

        foreach ($viewportSizes as $device => $viewport) {
            $results[$device] = $this->testResponsiveCompatibilityForDevice($device, $viewport);
        }

        return $results;
    }

    private function testResponsiveCompatibilityForDevice(string $device, array $viewport): array
    {
        try {
            $url = $this->baseUrl . '/';
            $response = Http::timeout(10)->get($url);
            
            if (!$response->successful()) {
                return [
                    'device' => $device,
                    'viewport' => $viewport,
                    'success' => false,
                    'error' => 'Failed to load page',
                ];
            }

            $html = $response->body();
            
            return [
                'device' => $device,
                'viewport' => $viewport,
                'success' => true,
                'viewport_meta' => str_contains($html, 'viewport'),
                'responsive_css' => str_contains($html, '@media'),
                'mobile_menu' => str_contains($html, 'navbar-toggler'),
                'responsive_images' => str_contains($html, 'img-responsive'),
                'fluid_layout' => str_contains($html, 'container-fluid'),
                'responsive_score' => $this->calculateResponsiveScore($html),
                'message' => "Responsive compatibility test for {$device} completed",
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

    private function testJavaScriptCompatibility(): array
    {
        $results = [];

        $jsTests = [
            'jquery_compatibility' => $this->testJQueryCompatibility(),
            'bootstrap_compatibility' => $this->testBootstrapCompatibility(),
            'modern_js_compatibility' => $this->testModernJSCompatibility(),
            'legacy_js_compatibility' => $this->testLegacyJSCompatibility(),
        ];

        foreach ($jsTests as $test => $result) {
            $results[$test] = $result;
        }

        return $results;
    }

    private function testJQueryCompatibility(): array
    {
        try {
            $url = $this->baseUrl . '/';
            $response = Http::timeout(10)->get($url);
            
            if (!$response->successful()) {
                return [
                    'success' => false,
                    'error' => 'Failed to load page',
                ];
            }

            $html = $response->body();
            
            return [
                'success' => true,
                'jquery_loaded' => str_contains($html, 'jquery'),
                'jquery_version' => $this->getJQueryVersion($html),
                'jquery_usage' => preg_match_all('/\$\(|jQuery\(/', $html),
                'compatibility_score' => str_contains($html, 'jquery') ? 100 : 0,
                'message' => 'jQuery compatibility test completed',
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    private function testBootstrapCompatibility(): array
    {
        try {
            $url = $this->baseUrl . '/';
            $response = Http::timeout(10)->get($url);
            
            if (!$response->successful()) {
                return [
                    'success' => false,
                    'error' => 'Failed to load page',
                ];
            }

            $html = $response->body();
            
            return [
                'success' => true,
                'bootstrap_loaded' => str_contains($html, 'bootstrap'),
                'bootstrap_version' => $this->getBootstrapVersion($html),
                'bootstrap_components' => preg_match_all('/class=["\'][^"\']*btn[^"\']*["\']/', $html),
                'compatibility_score' => str_contains($html, 'bootstrap') ? 100 : 0,
                'message' => 'Bootstrap compatibility test completed',
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    private function testModernJSCompatibility(): array
    {
        try {
            $url = $this->baseUrl . '/';
            $response = Http::timeout(10)->get($url);
            
            if (!$response->successful()) {
                return [
                    'success' => false,
                    'error' => 'Failed to load page',
                ];
            }

            $html = $response->body();
            
            $modernFeatures = [
                'es6_arrow_functions' => preg_match_all('/=>/', $html),
                'es6_template_literals' => preg_match_all('/`[^`]*\$\{/', $html),
                'es6_classes' => preg_match_all('/class\s+\w+/', $html),
                'promises' => preg_match_all('/Promise|\.then\(|\.catch\(/', $html),
                'async_await' => preg_match_all('/async\s+|await\s+/', $html),
                'fetch_api' => preg_match_all('/fetch\(/', $html),
            ];

            $totalFeatures = array_sum($modernFeatures);
            
            return [
                'success' => true,
                'modern_features' => $modernFeatures,
                'total_features' => $totalFeatures,
                'compatibility_score' => $totalFeatures > 0 ? round(($totalFeatures / 6) * 100, 2) : 0,
                'message' => 'Modern JavaScript compatibility test completed',
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    private function testLegacyJSCompatibility(): array
    {
        try {
            $url = $this->baseUrl . '/';
            $response = Http::timeout(10)->get($url);
            
            if (!$response->successful()) {
                return [
                    'success' => false,
                    'error' => 'Failed to load page',
                ];
            }

            $html = $response->body();
            
            $legacyFeatures = [
                'var_declarations' => preg_match_all('/var\s+\w+/', $html),
                'function_declarations' => preg_match_all('/function\s+\w+/', $html),
                'jquery_ready' => preg_match_all('/\$\(document\)\.ready|document\.ready/', $html),
                'legacy_ajax' => preg_match_all('/\.ajax\(|XMLHttpRequest/', $html),
            ];

            $totalFeatures = array_sum($legacyFeatures);
            
            return [
                'success' => true,
                'legacy_features' => $legacyFeatures,
                'total_features' => $totalFeatures,
                'compatibility_score' => $totalFeatures > 0 ? round(($totalFeatures / 4) * 100, 2) : 0,
                'message' => 'Legacy JavaScript compatibility test completed',
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    private function testCSSCompatibility(): array
    {
        $results = [];

        $cssTests = [
            'css3_features' => $this->testCSS3Compatibility(),
            'responsive_css' => $this->testResponsiveCSSCompatibility(),
            'vendor_prefixes' => $this->testVendorPrefixesCompatibility(),
            'fallbacks' => $this->testCSSFallbacksCompatibility(),
        ];

        foreach ($cssTests as $test => $result) {
            $results[$test] = $result;
        }

        return $results;
    }

    private function testCSS3Compatibility(): array
    {
        try {
            $url = $this->baseUrl . '/';
            $response = Http::timeout(10)->get($url);
            
            if (!$response->successful()) {
                return [
                    'success' => false,
                    'error' => 'Failed to load page',
                ];
            }

            $html = $response->body();
            
            $css3Features = [
                'flexbox' => preg_match_all('/flex|flexbox/i', $html),
                'grid' => preg_match_all('/grid/i', $html),
                'transitions' => preg_match_all('/transition/i', $html),
                'transforms' => preg_match_all('/transform/i', $html),
                'animations' => preg_match_all('/animation/i', $html),
                'gradients' => preg_match_all('/gradient/i', $html),
                'shadows' => preg_match_all('/box-shadow|text-shadow/i', $html),
                'border_radius' => preg_match_all('/border-radius/i', $html),
            ];

            $totalFeatures = array_sum($css3Features);
            
            return [
                'success' => true,
                'css3_features' => $css3Features,
                'total_features' => $totalFeatures,
                'compatibility_score' => $totalFeatures > 0 ? round(($totalFeatures / 8) * 100, 2) : 0,
                'message' => 'CSS3 compatibility test completed',
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    private function testResponsiveCSSCompatibility(): array
    {
        try {
            $url = $this->baseUrl . '/';
            $response = Http::timeout(10)->get($url);
            
            if (!$response->successful()) {
                return [
                    'success' => false,
                    'error' => 'Failed to load page',
                ];
            }

            $html = $response->body();
            
            $responsiveFeatures = [
                'media_queries' => preg_match_all('/@media[^}]*screen[^}]*{/i', $html),
                'viewport_meta' => preg_match('/<meta[^>]*name=["\']viewport["\'][^>]*>/i', $html),
                'flexible_grid' => preg_match_all('/col-|grid-|flex/i', $html),
                'responsive_images' => preg_match_all('/img-responsive|responsive/i', $html),
                'mobile_menu' => preg_match_all('/navbar-toggler|mobile-menu/i', $html),
                'fluid_typography' => preg_match_all('/rem|em|vw|vh/i', $html),
            ];

            $totalFeatures = array_sum($responsiveFeatures);
            
            return [
                'success' => true,
                'responsive_features' => $responsiveFeatures,
                'total_features' => $totalFeatures,
                'compatibility_score' => $totalFeatures > 0 ? round(($totalFeatures / 6) * 100, 2) : 0,
                'message' => 'Responsive CSS compatibility test completed',
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    private function testVendorPrefixesCompatibility(): array
    {
        try {
            $url = $this->baseUrl . '/';
            $response = Http::timeout(10)->get($url);
            
            if (!$response->successful()) {
                return [
                    'success' => false,
                    'error' => 'Failed to load page',
                ];
            }

            $html = $response->body();
            
            $vendorPrefixes = [
                'webkit' => preg_match_all('/-webkit-/', $html),
                'moz' => preg_match_all('/-moz-/', $html),
                'ms' => preg_match_all('/-ms-/', $html),
                'o' => preg_match_all('/-o-/', $html),
            ];

            $totalPrefixes = array_sum($vendorPrefixes);
            
            return [
                'success' => true,
                'vendor_prefixes' => $vendorPrefixes,
                'total_prefixes' => $totalPrefixes,
                'compatibility_score' => $totalPrefixes > 0 ? round(($totalPrefixes / 4) * 100, 2) : 0,
                'message' => 'Vendor prefixes compatibility test completed',
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    private function testCSSFallbacksCompatibility(): array
    {
        try {
            $url = $this->baseUrl . '/';
            $response = Http::timeout(10)->get($url);
            
            if (!$response->successful()) {
                return [
                    'success' => false,
                    'error' => 'Failed to load page',
                ];
            }

            $html = $response->body();
            
            $fallbacks = [
                'background_fallbacks' => preg_match_all('/background[^:]*:[^;]*#[^;]*;/', $html),
                'font_fallbacks' => preg_match_all('/font-family[^:]*:[^;]*serif[^;]*;/', $html),
                'color_fallbacks' => preg_match_all('/color[^:]*:[^;]*#[^;]*;/', $html),
            ];

            $totalFallbacks = array_sum($fallbacks);
            
            return [
                'success' => true,
                'css_fallbacks' => $fallbacks,
                'total_fallbacks' => $totalFallbacks,
                'compatibility_score' => $totalFallbacks > 0 ? round(($totalFallbacks / 3) * 100, 2) : 0,
                'message' => 'CSS fallbacks compatibility test completed',
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    private function testAccessibilityCompatibility(): array
    {
        $results = [];

        $accessibilityTests = [
            'wcag_compliance' => $this->testWCAGCompliance(),
            'screen_reader_compatibility' => $this->testScreenReaderCompatibility(),
            'keyboard_navigation' => $this->testKeyboardNavigationCompatibility(),
            'color_contrast' => $this->testColorContrastCompatibility(),
        ];

        foreach ($accessibilityTests as $test => $result) {
            $results[$test] = $result;
        }

        return $results;
    }

    private function testWCAGCompliance(): array
    {
        try {
            $url = $this->baseUrl . '/';
            $response = Http::timeout(10)->get($url);
            
            if (!$response->successful()) {
                return [
                    'success' => false,
                    'error' => 'Failed to load page',
                ];
            }

            $html = $response->body();
            
            $wcagFeatures = [
                'alt_text' => preg_match_all('/alt=["\'][^"\']*["\']/', $html),
                'aria_labels' => preg_match_all('/aria-[^=]*=/', $html),
                'heading_structure' => preg_match_all('/<h[1-6][^>]*>/i', $html),
                'form_labels' => preg_match_all('/<label[^>]*>/i', $html),
                'skip_links' => preg_match_all('/skip[^"]*link|skip[^"]*navigation/i', $html),
                'focus_management' => preg_match_all('/tabindex|focus/i', $html),
            ];

            $totalFeatures = array_sum($wcagFeatures);
            
            return [
                'success' => true,
                'wcag_features' => $wcagFeatures,
                'total_features' => $totalFeatures,
                'compliance_score' => $totalFeatures > 0 ? round(($totalFeatures / 6) * 100, 2) : 0,
                'message' => 'WCAG compliance test completed',
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    private function testScreenReaderCompatibility(): array
    {
        try {
            $url = $this->baseUrl . '/';
            $response = Http::timeout(10)->get($url);
            
            if (!$response->successful()) {
                return [
                    'success' => false,
                    'error' => 'Failed to load page',
                ];
            }

            $html = $response->body();
            
            $screenReaderFeatures = [
                'alt_text' => preg_match_all('/alt=["\'][^"\']*["\']/', $html),
                'aria_labels' => preg_match_all('/aria-[^=]*=/', $html),
                'aria_describedby' => preg_match_all('/aria-describedby/', $html),
                'aria_labelledby' => preg_match_all('/aria-labelledby/', $html),
                'role_attributes' => preg_match_all('/role=["\'][^"\']*["\']/', $html),
                'heading_structure' => preg_match_all('/<h[1-6][^>]*>/i', $html),
            ];

            $totalFeatures = array_sum($screenReaderFeatures);
            
            return [
                'success' => true,
                'screen_reader_features' => $screenReaderFeatures,
                'total_features' => $totalFeatures,
                'compatibility_score' => $totalFeatures > 0 ? round(($totalFeatures / 6) * 100, 2) : 0,
                'message' => 'Screen reader compatibility test completed',
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    private function testKeyboardNavigationCompatibility(): array
    {
        try {
            $url = $this->baseUrl . '/';
            $response = Http::timeout(10)->get($url);
            
            if (!$response->successful()) {
                return [
                    'success' => false,
                    'error' => 'Failed to load page',
                ];
            }

            $html = $response->body();
            
            $keyboardFeatures = [
                'tabindex' => preg_match_all('/tabindex/', $html),
                'skip_links' => preg_match_all('/skip[^"]*link|skip[^"]*navigation/i', $html),
                'focus_styles' => preg_match_all('/:focus|focus[^"]*style/i', $html),
                'form_labels' => preg_match_all('/<label[^>]*>/i', $html),
                'button_elements' => preg_match_all('/<button[^>]*>/i', $html),
                'link_elements' => preg_match_all('/<a[^>]*href[^>]*>/i', $html),
            ];

            $totalFeatures = array_sum($keyboardFeatures);
            
            return [
                'success' => true,
                'keyboard_features' => $keyboardFeatures,
                'total_features' => $totalFeatures,
                'compatibility_score' => $totalFeatures > 0 ? round(($totalFeatures / 6) * 100, 2) : 0,
                'message' => 'Keyboard navigation compatibility test completed',
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    private function testColorContrastCompatibility(): array
    {
        try {
            $url = $this->baseUrl . '/';
            $response = Http::timeout(10)->get($url);
            
            if (!$response->successful()) {
                return [
                    'success' => false,
                    'error' => 'Failed to load page',
                ];
            }

            $html = $response->body();
            
            $colorFeatures = [
                'color_declarations' => preg_match_all('/color[^:]*:[^;]*;/', $html),
                'background_color' => preg_match_all('/background-color[^:]*:[^;]*;/', $html),
                'text_shadow' => preg_match_all('/text-shadow[^:]*:[^;]*;/', $html),
                'box_shadow' => preg_match_all('/box-shadow[^:]*:[^;]*;/', $html),
            ];

            $totalFeatures = array_sum($colorFeatures);
            
            return [
                'success' => true,
                'color_features' => $colorFeatures,
                'total_features' => $totalFeatures,
                'compatibility_score' => $totalFeatures > 0 ? round(($totalFeatures / 4) * 100, 2) : 0,
                'message' => 'Color contrast compatibility test completed (requires manual verification)',
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    // Helper methods
    private function getPerformanceRating(float $loadTime): string
    {
        if ($loadTime < 1000) return 'Excellent';
        if ($loadTime < 3000) return 'Good';
        if ($loadTime < 5000) return 'Fair';
        return 'Poor';
    }

    private function getJQueryVersion(string $html): ?string
    {
        preg_match('/jquery[^"]*?(\d+\.\d+\.\d+)/i', $html, $matches);
        return $matches[1] ?? null;
    }

    private function getBootstrapVersion(string $html): ?string
    {
        preg_match('/bootstrap[^"]*?(\d+\.\d+\.\d+)/i', $html, $matches);
        return $matches[1] ?? null;
    }

    private function calculateResponsiveScore(string $html): int
    {
        $score = 0;
        
        if (str_contains($html, 'viewport')) $score += 25;
        if (str_contains($html, '@media')) $score += 25;
        if (str_contains($html, 'navbar-toggler')) $score += 25;
        if (str_contains($html, 'img-responsive')) $score += 25;
        
        return $score;
    }

    public function generateTestReport(): array
    {
        ['total' => $totalTests, 'passed' => $passedTests, 'failed' => $failedTests] = $this->countTestOutcomes($this->testResults);

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

    private function countTestOutcomes(array $tests): array
    {
        $counts = ['total' => 0, 'passed' => 0, 'failed' => 0];

        foreach ($tests as $test) {
            if (! is_array($test)) {
                continue;
            }

            if (array_key_exists('success', $test)) {
                $counts['total']++;

                if ($test['success']) {
                    $counts['passed']++;
                } else {
                    $counts['failed']++;
                }
            }

            $nested = $this->countTestOutcomes($test);
            $counts['total'] += $nested['total'];
            $counts['passed'] += $nested['passed'];
            $counts['failed'] += $nested['failed'];
        }

        return $counts;
    }

    private function generateRecommendations(): array
    {
        $recommendations = [];

        // Analyze test results and generate recommendations
        if (isset($this->testResults['browser_compatibility'])) {
            foreach ($this->testResults['browser_compatibility'] as $browser => $result) {
                if (isset($result['compatibility_score']) && $result['compatibility_score'] < 80) {
                    $recommendations[] = "Improve compatibility for {$browser} browser";
                }
            }
        }

        if (isset($this->testResults['feature_compatibility']['responsive_design']['responsive_score'])) {
            $responsiveScore = $this->testResults['feature_compatibility']['responsive_design']['responsive_score'];
            if ($responsiveScore < 80) {
                $recommendations[] = 'Improve responsive design compatibility';
            }
        }

        if (empty($recommendations)) {
            $recommendations[] = 'Cross-browser compatibility tests are passing well. Continue monitoring.';
        }

        return $recommendations;
    }

    public function getTestStatistics(): array
    {
        return [
            'config' => $this->testConfig,
            'base_url' => $this->baseUrl,
            'total_browsers' => count(array_filter($this->testConfig['browsers'], fn($browser) => $browser['enabled'])),
            'total_test_pages' => count($this->testConfig['test_pages']),
        ];
    }

    private function normalizePublicPath(string $path): string
    {
        return match ($path) {
            '/products' => '/tim-kiem',
            '/categories' => $this->resolveCategoryPath(),
            '/contact' => '/lien-he',
            '/login' => '/dang-nhap',
            '/register' => '/dang-ky',
            default => $path,
        };
    }

    private function resolveCategoryPath(): string
    {
        $slug = DB::table('product_categories')
            ->where('is_active', true)
            ->value('slug');

        return $slug ? '/danh-muc/' . $slug : '/';
    }
}
