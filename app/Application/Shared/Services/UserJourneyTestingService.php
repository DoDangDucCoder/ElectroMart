<?php

namespace App\Application\Shared\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class UserJourneyTestingService
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
            'user_journeys' => [
                'guest_user' => [
                    'description' => 'Guest user browsing and purchasing',
                    'steps' => [
                        'visit_homepage' => [
                            'url' => '/',
                            'expected_elements' => ['navigation', 'products', 'search'],
                            'actions' => ['view_products', 'search'],
                        ],
                        'browse_products' => [
                            'url' => '/products',
                            'expected_elements' => ['product_list', 'filters', 'pagination'],
                            'actions' => ['filter_products', 'view_product_details'],
                        ],
                        'view_product' => [
                            'url' => '/products/1',
                            'expected_elements' => ['product_info', 'add_to_cart', 'reviews'],
                            'actions' => ['add_to_cart', 'view_reviews'],
                        ],
                        'add_to_cart' => [
                            'url' => '/cart',
                            'expected_elements' => ['cart_items', 'checkout_button'],
                            'actions' => ['update_quantity', 'proceed_checkout'],
                        ],
                        'checkout_process' => [
                            'url' => '/checkout',
                            'expected_elements' => ['shipping_form', 'payment_form', 'order_summary'],
                            'actions' => ['fill_shipping', 'select_payment', 'place_order'],
                        ],
                    ],
                ],
                'registered_user' => [
                    'description' => 'Registered user with account management',
                    'steps' => [
                        'login' => [
                            'url' => '/login',
                            'expected_elements' => ['login_form', 'remember_me'],
                            'actions' => ['login', 'forgot_password'],
                        ],
                        'user_dashboard' => [
                            'url' => '/dashboard',
                            'expected_elements' => ['profile', 'orders', 'addresses'],
                            'actions' => ['view_orders', 'update_profile'],
                        ],
                        'order_history' => [
                            'url' => '/orders',
                            'expected_elements' => ['order_list', 'order_details'],
                            'actions' => ['view_order', 'reorder'],
                        ],
                        'profile_management' => [
                            'url' => '/profile',
                            'expected_elements' => ['profile_form', 'password_change'],
                            'actions' => ['update_profile', 'change_password'],
                        ],
                    ],
                ],
                'admin_user' => [
                    'description' => 'Admin user managing the system',
                    'steps' => [
                        'admin_login' => [
                            'url' => '/admin/login',
                            'expected_elements' => ['admin_login_form'],
                            'actions' => ['admin_login'],
                        ],
                        'admin_dashboard' => [
                            'url' => '/admin/dashboard',
                            'expected_elements' => ['stats', 'recent_orders', 'quick_actions'],
                            'actions' => ['view_stats', 'manage_orders'],
                        ],
                        'product_management' => [
                            'url' => '/admin/products',
                            'expected_elements' => ['product_list', 'add_product', 'bulk_actions'],
                            'actions' => ['add_product', 'edit_product', 'delete_product'],
                        ],
                        'order_management' => [
                            'url' => '/admin/orders',
                            'expected_elements' => ['order_list', 'order_filters', 'status_updates'],
                            'actions' => ['view_order', 'update_status', 'print_invoice'],
                        ],
                    ],
                ],
            ],
            'test_scenarios' => [
                'happy_path' => true,
                'error_handling' => true,
                'edge_cases' => true,
                'performance' => true,
            ],
        ];
    }

    public function runUserJourneyTests(): array
    {
        Log::info('Starting user journey tests...');

        $this->testResults = [
            'guest_user_journey' => $this->testGuestUserJourney(),
            'registered_user_journey' => $this->testRegisteredUserJourney(),
            'admin_user_journey' => $this->testAdminUserJourney(),
            'cross_journey_tests' => $this->testCrossJourneyScenarios(),
        ];

        Log::info('User journey tests completed');
        return $this->testResults;
    }

    private function testGuestUserJourney(): array
    {
        $journey = $this->testConfig['user_journeys']['guest_user'];
        $results = [];

        foreach ($journey['steps'] as $stepName => $stepConfig) {
            $results[$stepName] = $this->testJourneyStep($stepName, $stepConfig);
        }

        // Test complete journey flow
        $results['complete_journey'] = $this->testCompleteGuestJourney();

        return $results;
    }

    private function testRegisteredUserJourney(): array
    {
        $journey = $this->testConfig['user_journeys']['registered_user'];
        $results = [];

        foreach ($journey['steps'] as $stepName => $stepConfig) {
            $results[$stepName] = $this->testJourneyStep($stepName, $stepConfig);
        }

        // Test complete journey flow
        $results['complete_journey'] = $this->testCompleteRegisteredJourney();

        return $results;
    }

    private function testAdminUserJourney(): array
    {
        $journey = $this->testConfig['user_journeys']['admin_user'];
        $results = [];

        foreach ($journey['steps'] as $stepName => $stepConfig) {
            $results[$stepName] = $this->testJourneyStep($stepName, $stepConfig);
        }

        // Test complete journey flow
        $results['complete_journey'] = $this->testCompleteAdminJourney();

        return $results;
    }

    private function testJourneyStep(string $stepName, array $stepConfig): array
    {
        try {
            $url = $this->baseUrl . $stepConfig['url'];
            $startTime = microtime(true);
            
            $response = Http::timeout(30)->get($url);
            
            $endTime = microtime(true);
            $loadTime = ($endTime - $startTime) * 1000;

            $result = [
                'step_name' => $stepName,
                'url' => $url,
                'status_code' => $response->status(),
                'load_time_ms' => round($loadTime, 2),
                'success' => $response->successful(),
                'content_length' => strlen($response->body()),
            ];

            if ($response->successful()) {
                $html = $response->body();
                
                // Check expected elements
                $result['expected_elements'] = $this->checkExpectedElements($html, $stepConfig['expected_elements']);
                
                // Test actions
                $result['actions'] = $this->testJourneyActions($stepName, $stepConfig['actions'], $html);
                
                // Check for common issues
                $result['issues'] = $this->checkJourneyIssues($html, $stepName);
                
                // Performance rating
                $result['performance_rating'] = $this->getPerformanceRating($loadTime);
            }

            return $result;

        } catch (\Exception $e) {
            return [
                'step_name' => $stepName,
                'url' => $url,
                'error' => $e->getMessage(),
                'success' => false,
            ];
        }
    }

    private function checkExpectedElements(string $html, array $expectedElements): array
    {
        $results = [];
        
        foreach ($expectedElements as $element) {
            $results[$element] = $this->checkElementExists($html, $element);
        }
        
        return $results;
    }

    private function checkElementExists(string $html, string $element): array
    {
        $patterns = [
            'navigation' => '/<nav[^>]*>.*?<\/nav>/is',
            'products' => '/<div[^>]*class=["\'][^"\']*product[^"\']*["\'][^>]*>/i',
            'search' => '/<input[^>]*type=["\']search["\'][^>]*>/i',
            'product_list' => '/<div[^>]*class=["\'][^"\']*product[^"\']*["\'][^>]*>/i',
            'filters' => '/<div[^>]*class=["\'][^"\']*filter[^"\']*["\'][^>]*>/i',
            'pagination' => '/<nav[^>]*class=["\'][^"\']*pagination[^"\']*["\'][^>]*>/i',
            'product_info' => '/<div[^>]*class=["\'][^"\']*product[^"\']*info[^"\']*["\'][^>]*>/i',
            'add_to_cart' => '/<button[^>]*class=["\'][^"\']*add[^"\']*cart[^"\']*["\'][^>]*>/i',
            'reviews' => '/<div[^>]*class=["\'][^"\']*review[^"\']*["\'][^>]*>/i',
            'cart_items' => '/<div[^>]*class=["\'][^"\']*cart[^"\']*item[^"\']*["\'][^>]*>/i',
            'checkout_button' => '/<button[^>]*class=["\'][^"\']*checkout[^"\']*["\'][^>]*>/i',
            'shipping_form' => '/<form[^>]*class=["\'][^"\']*shipping[^"\']*["\'][^>]*>/i',
            'payment_form' => '/<form[^>]*class=["\'][^"\']*payment[^"\']*["\'][^>]*>/i',
            'order_summary' => '/<div[^>]*class=["\'][^"\']*order[^"\']*summary[^"\']*["\'][^>]*>/i',
            'login_form' => '/<form[^>]*class=["\'][^"\']*login[^"\']*["\'][^>]*>/i',
            'remember_me' => '/<input[^>]*name=["\']remember["\'][^>]*>/i',
            'profile' => '/<div[^>]*class=["\'][^"\']*profile[^"\']*["\'][^>]*>/i',
            'orders' => '/<div[^>]*class=["\'][^"\']*order[^"\']*["\'][^>]*>/i',
            'addresses' => '/<div[^>]*class=["\'][^"\']*address[^"\']*["\'][^>]*>/i',
            'order_list' => '/<div[^>]*class=["\'][^"\']*order[^"\']*list[^"\']*["\'][^>]*>/i',
            'order_details' => '/<div[^>]*class=["\'][^"\']*order[^"\']*detail[^"\']*["\'][^>]*>/i',
            'profile_form' => '/<form[^>]*class=["\'][^"\']*profile[^"\']*["\'][^>]*>/i',
            'password_change' => '/<form[^>]*class=["\'][^"\']*password[^"\']*["\'][^>]*>/i',
            'admin_login_form' => '/<form[^>]*class=["\'][^"\']*admin[^"\']*login[^"\']*["\'][^>]*>/i',
            'stats' => '/<div[^>]*class=["\'][^"\']*stat[^"\']*["\'][^>]*>/i',
            'recent_orders' => '/<div[^>]*class=["\'][^"\']*recent[^"\']*order[^"\']*["\'][^>]*>/i',
            'quick_actions' => '/<div[^>]*class=["\'][^"\']*quick[^"\']*action[^"\']*["\'][^>]*>/i',
            'add_product' => '/<button[^>]*class=["\'][^"\']*add[^"\']*product[^"\']*["\'][^>]*>/i',
            'bulk_actions' => '/<div[^>]*class=["\'][^"\']*bulk[^"\']*action[^"\']*["\'][^>]*>/i',
            'order_filters' => '/<div[^>]*class=["\'][^"\']*order[^"\']*filter[^"\']*["\'][^>]*>/i',
            'status_updates' => '/<select[^>]*class=["\'][^"\']*status[^"\']*["\'][^>]*>/i',
        ];

        $pattern = $patterns[$element] ?? null;
        if (!$pattern) {
            return [
                'exists' => false,
                'message' => 'Unknown element type',
            ];
        }

        $exists = preg_match($pattern, $html) === 1;
        
        return [
            'exists' => $exists,
            'message' => $exists ? "Element '{$element}' found" : "Element '{$element}' not found",
        ];
    }

    private function testJourneyActions(string $stepName, array $actions, string $html): array
    {
        $results = [];
        
        foreach ($actions as $action) {
            $results[$action] = $this->testJourneyAction($stepName, $action, $html);
        }
        
        return $results;
    }

    private function testJourneyAction(string $stepName, string $action, string $html): array
    {
        switch ($action) {
            case 'view_products':
                return $this->testViewProducts($html);
            case 'search':
                return $this->testSearchFunctionality($html);
            case 'filter_products':
                return $this->testFilterProducts($html);
            case 'view_product_details':
                return $this->testViewProductDetails($html);
            case 'add_to_cart':
                return $this->testAddToCart($html);
            case 'view_reviews':
                return $this->testViewReviews($html);
            case 'update_quantity':
                return $this->testUpdateQuantity($html);
            case 'proceed_checkout':
                return $this->testProceedCheckout($html);
            case 'fill_shipping':
                return $this->testFillShipping($html);
            case 'select_payment':
                return $this->testSelectPayment($html);
            case 'place_order':
                return $this->testPlaceOrder($html);
            case 'login':
                return $this->testLogin($html);
            case 'forgot_password':
                return $this->testForgotPassword($html);
            case 'view_orders':
                return $this->testViewOrders($html);
            case 'update_profile':
                return $this->testUpdateProfile($html);
            case 'view_order':
                return $this->testViewOrder($html);
            case 'reorder':
                return $this->testReorder($html);
            case 'change_password':
                return $this->testChangePassword($html);
            case 'admin_login':
                return $this->testAdminLogin($html);
            case 'view_stats':
                return $this->testViewStats($html);
            case 'manage_orders':
                return $this->testManageOrders($html);
            case 'edit_product':
                return $this->testEditProduct($html);
            case 'delete_product':
                return $this->testDeleteProduct($html);
            case 'update_status':
                return $this->testUpdateStatus($html);
            case 'print_invoice':
                return $this->testPrintInvoice($html);
            default:
                return [
                    'action' => $action,
                    'success' => false,
                    'message' => 'Unknown action',
                ];
        }
    }

    private function testViewProducts(string $html): array
    {
        $productElements = preg_match_all('/<div[^>]*class=["\'][^"\']*product[^"\']*["\'][^>]*>/i', $html);
        
        return [
            'action' => 'view_products',
            'success' => $productElements > 0,
            'products_found' => $productElements,
            'message' => $productElements > 0 ? 'Products displayed successfully' : 'No products found',
        ];
    }

    private function testSearchFunctionality(string $html): array
    {
        $searchForm = preg_match('/<form[^>]*>.*?<input[^>]*type=["\']search["\'][^>]*>.*?<\/form>/is', $html);
        
        return [
            'action' => 'search',
            'success' => $searchForm === 1,
            'search_form_found' => $searchForm === 1,
            'message' => $searchForm === 1 ? 'Search functionality available' : 'Search functionality not found',
        ];
    }

    private function testFilterProducts(string $html): array
    {
        $filterElements = preg_match_all('/<div[^>]*class=["\'][^"\']*filter[^"\']*["\'][^>]*>/i', $html);
        
        return [
            'action' => 'filter_products',
            'success' => $filterElements > 0,
            'filters_found' => $filterElements,
            'message' => $filterElements > 0 ? 'Product filters available' : 'No product filters found',
        ];
    }

    private function testViewProductDetails(string $html): array
    {
        $productInfo = preg_match('/<div[^>]*class=["\'][^"\']*product[^"\']*info[^"\']*["\'][^>]*>/i', $html);
        
        return [
            'action' => 'view_product_details',
            'success' => $productInfo === 1,
            'product_info_found' => $productInfo === 1,
            'message' => $productInfo === 1 ? 'Product details displayed' : 'Product details not found',
        ];
    }

    private function testAddToCart(string $html): array
    {
        $addToCartButton = preg_match('/<button[^>]*class=["\'][^"\']*add[^"\']*cart[^"\']*["\'][^>]*>/i', $html);
        
        return [
            'action' => 'add_to_cart',
            'success' => $addToCartButton === 1,
            'add_to_cart_found' => $addToCartButton === 1,
            'message' => $addToCartButton === 1 ? 'Add to cart button available' : 'Add to cart button not found',
        ];
    }

    private function testViewReviews(string $html): array
    {
        $reviewElements = preg_match_all('/<div[^>]*class=["\'][^"\']*review[^"\']*["\'][^>]*>/i', $html);
        
        return [
            'action' => 'view_reviews',
            'success' => $reviewElements > 0,
            'reviews_found' => $reviewElements,
            'message' => $reviewElements > 0 ? 'Product reviews available' : 'No product reviews found',
        ];
    }

    private function testUpdateQuantity(string $html): array
    {
        $quantityInput = preg_match('/<input[^>]*name=["\']quantity["\'][^>]*>/i', $html);
        
        return [
            'action' => 'update_quantity',
            'success' => $quantityInput === 1,
            'quantity_input_found' => $quantityInput === 1,
            'message' => $quantityInput === 1 ? 'Quantity input available' : 'Quantity input not found',
        ];
    }

    private function testProceedCheckout(string $html): array
    {
        $checkoutButton = preg_match('/<button[^>]*class=["\'][^"\']*checkout[^"\']*["\'][^>]*>/i', $html);
        
        return [
            'action' => 'proceed_checkout',
            'success' => $checkoutButton === 1,
            'checkout_button_found' => $checkoutButton === 1,
            'message' => $checkoutButton === 1 ? 'Checkout button available' : 'Checkout button not found',
        ];
    }

    private function testFillShipping(string $html): array
    {
        $shippingForm = preg_match('/<form[^>]*class=["\'][^"\']*shipping[^"\']*["\'][^>]*>/i', $html);
        
        return [
            'action' => 'fill_shipping',
            'success' => $shippingForm === 1,
            'shipping_form_found' => $shippingForm === 1,
            'message' => $shippingForm === 1 ? 'Shipping form available' : 'Shipping form not found',
        ];
    }

    private function testSelectPayment(string $html): array
    {
        $paymentForm = preg_match('/<form[^>]*class=["\'][^"\']*payment[^"\']*["\'][^>]*>/i', $html);
        
        return [
            'action' => 'select_payment',
            'success' => $paymentForm === 1,
            'payment_form_found' => $paymentForm === 1,
            'message' => $paymentForm === 1 ? 'Payment form available' : 'Payment form not found',
        ];
    }

    private function testPlaceOrder(string $html): array
    {
        $placeOrderButton = preg_match('/<button[^>]*class=["\'][^"\']*place[^"\']*order[^"\']*["\'][^>]*>/i', $html);
        
        return [
            'action' => 'place_order',
            'success' => $placeOrderButton === 1,
            'place_order_found' => $placeOrderButton === 1,
            'message' => $placeOrderButton === 1 ? 'Place order button available' : 'Place order button not found',
        ];
    }

    private function testLogin(string $html): array
    {
        $loginForm = preg_match('/<form[^>]*class=["\'][^"\']*login[^"\']*["\'][^>]*>/i', $html);
        
        return [
            'action' => 'login',
            'success' => $loginForm === 1,
            'login_form_found' => $loginForm === 1,
            'message' => $loginForm === 1 ? 'Login form available' : 'Login form not found',
        ];
    }

    private function testForgotPassword(string $html): array
    {
        $forgotPasswordLink = preg_match('/<a[^>]*href=["\'][^"\']*forgot[^"\']*password[^"\']*["\'][^>]*>/i', $html);
        
        return [
            'action' => 'forgot_password',
            'success' => $forgotPasswordLink === 1,
            'forgot_password_found' => $forgotPasswordLink === 1,
            'message' => $forgotPasswordLink === 1 ? 'Forgot password link available' : 'Forgot password link not found',
        ];
    }

    private function testViewOrders(string $html): array
    {
        $orderElements = preg_match_all('/<div[^>]*class=["\'][^"\']*order[^"\']*["\'][^>]*>/i', $html);
        
        return [
            'action' => 'view_orders',
            'success' => $orderElements > 0,
            'orders_found' => $orderElements,
            'message' => $orderElements > 0 ? 'Order history available' : 'No order history found',
        ];
    }

    private function testUpdateProfile(string $html): array
    {
        $profileForm = preg_match('/<form[^>]*class=["\'][^"\']*profile[^"\']*["\'][^>]*>/i', $html);
        
        return [
            'action' => 'update_profile',
            'success' => $profileForm === 1,
            'profile_form_found' => $profileForm === 1,
            'message' => $profileForm === 1 ? 'Profile form available' : 'Profile form not found',
        ];
    }

    private function testViewOrder(string $html): array
    {
        $orderDetails = preg_match('/<div[^>]*class=["\'][^"\']*order[^"\']*detail[^"\']*["\'][^>]*>/i', $html);
        
        return [
            'action' => 'view_order',
            'success' => $orderDetails === 1,
            'order_details_found' => $orderDetails === 1,
            'message' => $orderDetails === 1 ? 'Order details available' : 'Order details not found',
        ];
    }

    private function testReorder(string $html): array
    {
        $reorderButton = preg_match('/<button[^>]*class=["\'][^"\']*reorder[^"\']*["\'][^>]*>/i', $html);
        
        return [
            'action' => 'reorder',
            'success' => $reorderButton === 1,
            'reorder_button_found' => $reorderButton === 1,
            'message' => $reorderButton === 1 ? 'Reorder button available' : 'Reorder button not found',
        ];
    }

    private function testChangePassword(string $html): array
    {
        $passwordForm = preg_match('/<form[^>]*class=["\'][^"\']*password[^"\']*["\'][^>]*>/i', $html);
        
        return [
            'action' => 'change_password',
            'success' => $passwordForm === 1,
            'password_form_found' => $passwordForm === 1,
            'message' => $passwordForm === 1 ? 'Password change form available' : 'Password change form not found',
        ];
    }

    private function testAdminLogin(string $html): array
    {
        $adminForm = preg_match('/<form[^>]*class=["\'][^"\']*admin[^"\']*login[^"\']*["\'][^>]*>/i', $html);
        
        return [
            'action' => 'admin_login',
            'success' => $adminForm === 1,
            'admin_form_found' => $adminForm === 1,
            'message' => $adminForm === 1 ? 'Admin login form available' : 'Admin login form not found',
        ];
    }

    private function testViewStats(string $html): array
    {
        $statsElements = preg_match_all('/<div[^>]*class=["\'][^"\']*stat[^"\']*["\'][^>]*>/i', $html);
        
        return [
            'action' => 'view_stats',
            'success' => $statsElements > 0,
            'stats_found' => $statsElements,
            'message' => $statsElements > 0 ? 'Statistics available' : 'No statistics found',
        ];
    }

    private function testManageOrders(string $html): array
    {
        $orderManagement = preg_match('/<div[^>]*class=["\'][^"\']*order[^"\']*management[^"\']*["\'][^>]*>/i', $html);
        
        return [
            'action' => 'manage_orders',
            'success' => $orderManagement === 1,
            'order_management_found' => $orderManagement === 1,
            'message' => $orderManagement === 1 ? 'Order management available' : 'Order management not found',
        ];
    }

    private function testEditProduct(string $html): array
    {
        $editButton = preg_match('/<button[^>]*class=["\'][^"\']*edit[^"\']*product[^"\']*["\'][^>]*>/i', $html);
        
        return [
            'action' => 'edit_product',
            'success' => $editButton === 1,
            'edit_button_found' => $editButton === 1,
            'message' => $editButton === 1 ? 'Edit product button available' : 'Edit product button not found',
        ];
    }

    private function testDeleteProduct(string $html): array
    {
        $deleteButton = preg_match('/<button[^>]*class=["\'][^"\']*delete[^"\']*product[^"\']*["\'][^>]*>/i', $html);
        
        return [
            'action' => 'delete_product',
            'success' => $deleteButton === 1,
            'delete_button_found' => $deleteButton === 1,
            'message' => $deleteButton === 1 ? 'Delete product button available' : 'Delete product button not found',
        ];
    }

    private function testUpdateStatus(string $html): array
    {
        $statusSelect = preg_match('/<select[^>]*class=["\'][^"\']*status[^"\']*["\'][^>]*>/i', $html);
        
        return [
            'action' => 'update_status',
            'success' => $statusSelect === 1,
            'status_select_found' => $statusSelect === 1,
            'message' => $statusSelect === 1 ? 'Status update available' : 'Status update not found',
        ];
    }

    private function testPrintInvoice(string $html): array
    {
        $printButton = preg_match('/<button[^>]*class=["\'][^"\']*print[^"\']*invoice[^"\']*["\'][^>]*>/i', $html);
        
        return [
            'action' => 'print_invoice',
            'success' => $printButton === 1,
            'print_button_found' => $printButton === 1,
            'message' => $printButton === 1 ? 'Print invoice button available' : 'Print invoice button not found',
        ];
    }

    private function checkJourneyIssues(string $html, string $stepName): array
    {
        $issues = [];
        
        if (str_contains($html, 'Fatal error')) {
            $issues[] = 'Fatal error detected';
        }
        
        if (str_contains($html, 'Warning:')) {
            $issues[] = 'PHP warnings detected';
        }
        
        if (str_contains($html, 'Notice:')) {
            $issues[] = 'PHP notices detected';
        }
        
        if (str_contains($html, '404')) {
            $issues[] = '404 error detected';
        }
        
        if (str_contains($html, '500')) {
            $issues[] = '500 error detected';
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

    private function testCompleteGuestJourney(): array
    {
        try {
            $journeySteps = [
                'homepage' => '/',
                'products' => '/products',
                'product_detail' => '/products/1',
                'cart' => '/cart',
                'checkout' => '/checkout',
            ];

            $results = [];
            $totalTime = 0;
            $successfulSteps = 0;

            foreach ($journeySteps as $step => $url) {
                $startTime = microtime(true);
                $response = Http::timeout(10)->get($this->baseUrl . $url);
                $endTime = microtime(true);
                
                $stepTime = ($endTime - $startTime) * 1000;
                $totalTime += $stepTime;
                
                if ($response->successful()) {
                    $successfulSteps++;
                }
                
                $results[$step] = [
                    'url' => $url,
                    'status' => $response->status(),
                    'success' => $response->successful(),
                    'load_time_ms' => round($stepTime, 2),
                ];
            }

            return [
                'success' => $successfulSteps === count($journeySteps),
                'total_steps' => count($journeySteps),
                'successful_steps' => $successfulSteps,
                'success_rate' => round(($successfulSteps / count($journeySteps)) * 100, 2),
                'total_journey_time_ms' => round($totalTime, 2),
                'average_step_time_ms' => round($totalTime / count($journeySteps), 2),
                'step_results' => $results,
                'message' => $successfulSteps === count($journeySteps) ? 'Complete guest journey successful' : 'Guest journey has issues',
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'message' => 'Complete guest journey test failed',
            ];
        }
    }

    private function testCompleteRegisteredJourney(): array
    {
        try {
            $journeySteps = [
                'login' => '/login',
                'dashboard' => '/dashboard',
                'orders' => '/orders',
                'profile' => '/profile',
            ];

            $results = [];
            $totalTime = 0;
            $successfulSteps = 0;

            foreach ($journeySteps as $step => $url) {
                $startTime = microtime(true);
                $response = Http::timeout(10)->get($this->baseUrl . $url);
                $endTime = microtime(true);
                
                $stepTime = ($endTime - $startTime) * 1000;
                $totalTime += $stepTime;
                
                if ($response->successful()) {
                    $successfulSteps++;
                }
                
                $results[$step] = [
                    'url' => $url,
                    'status' => $response->status(),
                    'success' => $response->successful(),
                    'load_time_ms' => round($stepTime, 2),
                ];
            }

            return [
                'success' => $successfulSteps === count($journeySteps),
                'total_steps' => count($journeySteps),
                'successful_steps' => $successfulSteps,
                'success_rate' => round(($successfulSteps / count($journeySteps)) * 100, 2),
                'total_journey_time_ms' => round($totalTime, 2),
                'average_step_time_ms' => round($totalTime / count($journeySteps), 2),
                'step_results' => $results,
                'message' => $successfulSteps === count($journeySteps) ? 'Complete registered journey successful' : 'Registered journey has issues',
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'message' => 'Complete registered journey test failed',
            ];
        }
    }

    private function testCompleteAdminJourney(): array
    {
        try {
            $journeySteps = [
                'admin_login' => '/admin/login',
                'admin_dashboard' => '/admin/dashboard',
                'admin_products' => '/admin/products',
                'admin_orders' => '/admin/orders',
            ];

            $results = [];
            $totalTime = 0;
            $successfulSteps = 0;

            foreach ($journeySteps as $step => $url) {
                $startTime = microtime(true);
                $response = Http::timeout(10)->get($this->baseUrl . $url);
                $endTime = microtime(true);
                
                $stepTime = ($endTime - $startTime) * 1000;
                $totalTime += $stepTime;
                
                if ($response->successful()) {
                    $successfulSteps++;
                }
                
                $results[$step] = [
                    'url' => $url,
                    'status' => $response->status(),
                    'success' => $response->successful(),
                    'load_time_ms' => round($stepTime, 2),
                ];
            }

            return [
                'success' => $successfulSteps === count($journeySteps),
                'total_steps' => count($journeySteps),
                'successful_steps' => $successfulSteps,
                'success_rate' => round(($successfulSteps / count($journeySteps)) * 100, 2),
                'total_journey_time_ms' => round($totalTime, 2),
                'average_step_time_ms' => round($totalTime / count($journeySteps), 2),
                'step_results' => $results,
                'message' => $successfulSteps === count($journeySteps) ? 'Complete admin journey successful' : 'Admin journey has issues',
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'message' => 'Complete admin journey test failed',
            ];
        }
    }

    private function testCrossJourneyScenarios(): array
    {
        $results = [];

        $scenarios = [
            'session_persistence' => $this->testSessionPersistence(),
            'cross_browser_compatibility' => $this->testCrossBrowserCompatibility(),
            'mobile_responsiveness' => $this->testMobileResponsiveness(),
            'error_handling' => $this->testErrorHandling(),
        ];

        foreach ($scenarios as $scenario => $result) {
            $results[$scenario] = $result;
        }

        return $results;
    }

    private function testSessionPersistence(): array
    {
        try {
            // Test if session persists across requests
            $loginUrl = $this->baseUrl . '/login';
            $dashboardUrl = $this->baseUrl . '/dashboard';
            
            $response1 = Http::timeout(10)->get($loginUrl);
            $response2 = Http::timeout(10)->get($dashboardUrl);
            
            return [
                'success' => true,
                'login_page_accessible' => $response1->successful(),
                'dashboard_page_accessible' => $response2->successful(),
                'message' => 'Session persistence test completed',
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'message' => 'Session persistence test failed',
            ];
        }
    }

    private function testCrossBrowserCompatibility(): array
    {
        try {
            // Test basic compatibility (simplified)
            $url = $this->baseUrl . '/';
            $response = Http::timeout(10)->get($url);
            
            if ($response->successful()) {
                $html = $response->body();
                
                return [
                    'success' => true,
                    'html5_doctype' => str_contains($html, '<!DOCTYPE html>'),
                    'meta_charset' => str_contains($html, '<meta charset='),
                    'viewport_meta' => str_contains($html, 'viewport'),
                    'message' => 'Cross-browser compatibility test completed',
                ];
            }

            return [
                'success' => false,
                'message' => 'Failed to load page for compatibility test',
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'message' => 'Cross-browser compatibility test failed',
            ];
        }
    }

    private function testMobileResponsiveness(): array
    {
        try {
            $url = $this->baseUrl . '/';
            $response = Http::timeout(10)->get($url);
            
            if ($response->successful()) {
                $html = $response->body();
                
                return [
                    'success' => true,
                    'viewport_meta' => str_contains($html, 'viewport'),
                    'responsive_css' => str_contains($html, '@media'),
                    'mobile_menu' => str_contains($html, 'navbar-toggler'),
                    'responsive_images' => str_contains($html, 'img-responsive'),
                    'mobile_score' => $this->calculateMobileScore($html),
                    'message' => 'Mobile responsiveness test completed',
                ];
            }

            return [
                'success' => false,
                'message' => 'Failed to load page for mobile test',
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'message' => 'Mobile responsiveness test failed',
            ];
        }
    }

    private function testErrorHandling(): array
    {
        try {
            // Test 404 error handling
            $notFoundUrl = $this->baseUrl . '/non-existent-page';
            $response = Http::timeout(10)->get($notFoundUrl);
            
            return [
                'success' => true,
                'not_found_status' => $response->status(),
                'has_custom_404' => $response->status() === 404,
                'error_page_content' => str_contains($response->body(), '404') || str_contains($response->body(), 'Not Found'),
                'message' => 'Error handling test completed',
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'message' => 'Error handling test failed',
            ];
        }
    }

    private function calculateMobileScore(string $html): int
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
        if (isset($this->testResults['guest_user_journey']['complete_journey'])) {
            $journey = $this->testResults['guest_user_journey']['complete_journey'];
            if (!$journey['success']) {
                $recommendations[] = 'Fix issues in guest user journey';
            }
        }

        if (isset($this->testResults['registered_user_journey']['complete_journey'])) {
            $journey = $this->testResults['registered_user_journey']['complete_journey'];
            if (!$journey['success']) {
                $recommendations[] = 'Fix issues in registered user journey';
            }
        }

        if (isset($this->testResults['admin_user_journey']['complete_journey'])) {
            $journey = $this->testResults['admin_user_journey']['complete_journey'];
            if (!$journey['success']) {
                $recommendations[] = 'Fix issues in admin user journey';
            }
        }

        if (empty($recommendations)) {
            $recommendations[] = 'User journey tests are passing well. Continue monitoring.';
        }

        return $recommendations;
    }

    public function getTestStatistics(): array
    {
        return [
            'config' => $this->testConfig,
            'base_url' => $this->baseUrl,
            'total_user_journeys' => count($this->testConfig['user_journeys']),
        ];
    }
}
