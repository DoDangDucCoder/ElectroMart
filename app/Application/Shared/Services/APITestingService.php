<?php

namespace App\Application\Shared\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class APITestingService
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
            'endpoints' => [
                'public' => [
                    'GET /api/products' => [
                        'description' => 'Get products list',
                        'expected_status' => 200,
                        'required_fields' => ['id', 'name', 'price'],
                    ],
                    'GET /api/categories' => [
                        'description' => 'Get categories list',
                        'expected_status' => 200,
                        'required_fields' => ['id', 'name', 'slug'],
                    ],
                    'GET /api/brands' => [
                        'description' => 'Get brands list',
                        'expected_status' => 200,
                        'required_fields' => ['id', 'name'],
                    ],
                ],
                'protected' => [
                    'POST /api/auth/login' => [
                        'description' => 'User login',
                        'expected_status' => 200,
                        'required_fields' => ['token', 'user'],
                        'test_data' => [
                            'email' => 'test@example.com',
                            'password' => 'password',
                        ],
                    ],
                    'GET /api/user/profile' => [
                        'description' => 'Get user profile',
                        'expected_status' => 200,
                        'required_fields' => ['id', 'name', 'email'],
                        'requires_auth' => true,
                    ],
                    'POST /api/orders' => [
                        'description' => 'Create order',
                        'expected_status' => 201,
                        'required_fields' => ['id', 'status', 'total'],
                        'requires_auth' => true,
                        'test_data' => [
                            'items' => [
                                ['product_id' => 1, 'quantity' => 2],
                            ],
                            'shipping_address' => [
                                'street' => '123 Test St',
                                'city' => 'Test City',
                                'postal_code' => '12345',
                            ],
                        ],
                    ],
                ],
                'admin' => [
                    'GET /api/admin/users' => [
                        'description' => 'Get users list (admin)',
                        'expected_status' => 200,
                        'required_fields' => ['data'],
                        'requires_auth' => true,
                        'requires_role' => 'admin',
                    ],
                    'POST /api/admin/products' => [
                        'description' => 'Create product (admin)',
                        'expected_status' => 201,
                        'required_fields' => ['id', 'name', 'price'],
                        'requires_auth' => true,
                        'requires_role' => 'admin',
                        'test_data' => [
                            'name' => 'Test Product',
                            'price' => 99.99,
                            'description' => 'Test product description',
                            'category_id' => 1,
                        ],
                    ],
                ],
            ],
            'test_scenarios' => [
                'success_cases' => true,
                'error_cases' => true,
                'validation_cases' => true,
                'security_cases' => true,
                'performance_cases' => true,
            ],
        ];
    }

    public function runAPITests(): array
    {
        Log::info('Starting API tests...');

        $this->testResults = [
            'public_endpoints' => $this->testPublicEndpoints(),
            'protected_endpoints' => $this->testProtectedEndpoints(),
            'admin_endpoints' => $this->testAdminEndpoints(),
            'security_tests' => $this->testSecurity(),
            'performance_tests' => $this->testPerformance(),
            'validation_tests' => $this->testValidation(),
        ];

        Log::info('API tests completed');
        return $this->testResults;
    }

    private function testPublicEndpoints(): array
    {
        $results = [];
        $endpoints = $this->testConfig['endpoints']['public'];

        foreach ($endpoints as $endpoint => $config) {
            $results[$endpoint] = $this->testEndpoint($endpoint, $config);
        }

        return $results;
    }

    private function testProtectedEndpoints(): array
    {
        $results = [];
        $endpoints = $this->testConfig['endpoints']['protected'];

        // First, get authentication token
        $token = $this->getAuthToken();
        if (!$token) {
            return ['error' => 'Failed to get authentication token'];
        }

        foreach ($endpoints as $endpoint => $config) {
            $config['auth_token'] = $token;
            $results[$endpoint] = $this->testEndpoint($endpoint, $config);
        }

        return $results;
    }

    private function testAdminEndpoints(): array
    {
        $results = [];
        $endpoints = $this->testConfig['endpoints']['admin'];

        // Get admin authentication token
        $adminToken = $this->getAdminToken();
        if (!$adminToken) {
            return ['error' => 'Failed to get admin authentication token'];
        }

        foreach ($endpoints as $endpoint => $config) {
            $config['auth_token'] = $adminToken;
            $results[$endpoint] = $this->testEndpoint($endpoint, $config);
        }

        return $results;
    }

    private function testEndpoint(string $endpoint, array $config): array
    {
        $url = $this->baseUrl . $endpoint;
        $method = $this->getMethodFromEndpoint($endpoint);
        
        $headers = ['Accept' => 'application/json'];
        
        if (isset($config['auth_token'])) {
            $headers['Authorization'] = 'Bearer ' . $config['auth_token'];
        }

        $data = $config['test_data'] ?? [];

        try {
            $startTime = microtime(true);
            
            $response = Http::withHeaders($headers)
                ->timeout(30)
                ->{$method}($url, $data);
            
            $endTime = microtime(true);
            $responseTime = ($endTime - $startTime) * 1000; // Convert to milliseconds

            $result = [
                'status' => $response->status(),
                'response_time_ms' => round($responseTime, 2),
                'success' => $response->status() === $config['expected_status'],
                'response_data' => $response->json(),
                'headers' => $response->headers(),
            ];

            // Validate required fields
            if ($response->successful() && isset($config['required_fields'])) {
                $result['field_validation'] = $this->validateRequiredFields(
                    $response->json(),
                    $config['required_fields']
                );
            }

            // Check response time
            if ($responseTime > 1000) { // More than 1 second
                $result['performance_warning'] = 'Slow response time';
            }

            return $result;

        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'error' => $e->getMessage(),
                'success' => false,
            ];
        }
    }

    private function testSecurity(): array
    {
        $results = [];

        // Test 1: Unauthorized access
        $results['unauthorized_access'] = $this->testUnauthorizedAccess();

        // Test 2: Invalid token
        $results['invalid_token'] = $this->testInvalidToken();

        // Test 3: SQL injection attempts
        $results['sql_injection'] = $this->testSQLInjection();

        // Test 4: XSS attempts
        $results['xss_attempts'] = $this->testXSSAttempts();

        // Test 5: Rate limiting
        $results['rate_limiting'] = $this->testRateLimiting();

        return $results;
    }

    private function testUnauthorizedAccess(): array
    {
        $protectedEndpoints = [
            'GET /api/user/profile',
            'POST /api/orders',
            'GET /api/admin/users',
        ];

        $results = [];
        foreach ($protectedEndpoints as $endpoint) {
            $url = $this->baseUrl . $endpoint;
            $method = $this->getMethodFromEndpoint($endpoint);

            try {
                $response = Http::timeout(10)->{$method}($url);
                $results[$endpoint] = [
                    'status' => $response->status(),
                    'success' => $response->status() === 401,
                    'message' => $response->status() === 401 ? 'Properly protected' : 'Security vulnerability',
                ];
            } catch (\Exception $e) {
                $results[$endpoint] = [
                    'status' => 'error',
                    'error' => $e->getMessage(),
                ];
            }
        }

        return $results;
    }

    private function testInvalidToken(): array
    {
        $url = $this->baseUrl . '/api/user/profile';
        
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer invalid_token_12345',
                'Accept' => 'application/json',
            ])->get($url);

            return [
                'status' => $response->status(),
                'success' => $response->status() === 401,
                'message' => $response->status() === 401 ? 'Properly rejected invalid token' : 'Security vulnerability',
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'error' => $e->getMessage(),
            ];
        }
    }

    private function testSQLInjection(): array
    {
        $maliciousInputs = [
            "'; DROP TABLE users; --",
            "1' OR '1'='1",
            "1' UNION SELECT * FROM users --",
            "admin'--",
        ];

        $results = [];
        foreach ($maliciousInputs as $input) {
            $url = $this->baseUrl . '/api/products?search=' . urlencode($input);
            
            try {
                $response = Http::timeout(10)->get($url);
                $results[] = [
                    'input' => $input,
                    'status' => $response->status(),
                    'success' => $response->status() !== 500, // Should not cause server error
                    'message' => $response->status() !== 500 ? 'Properly handled' : 'Potential SQL injection vulnerability',
                ];
            } catch (\Exception $e) {
                $results[] = [
                    'input' => $input,
                    'status' => 'error',
                    'error' => $e->getMessage(),
                ];
            }
        }

        return $results;
    }

    private function testXSSAttempts(): array
    {
        $xssPayloads = [
            '<script>alert("XSS")</script>',
            'javascript:alert("XSS")',
            '<img src=x onerror=alert("XSS")>',
            '"><script>alert("XSS")</script>',
        ];

        $results = [];
        foreach ($xssPayloads as $payload) {
            $url = $this->baseUrl . '/api/products?name=' . urlencode($payload);
            
            try {
                $response = Http::timeout(10)->get($url);
                $responseBody = $response->body();
                
                $results[] = [
                    'payload' => $payload,
                    'status' => $response->status(),
                    'success' => !str_contains($responseBody, $payload), // Payload should be sanitized
                    'message' => !str_contains($responseBody, $payload) ? 'Properly sanitized' : 'Potential XSS vulnerability',
                ];
            } catch (\Exception $e) {
                $results[] = [
                    'payload' => $payload,
                    'status' => 'error',
                    'error' => $e->getMessage(),
                ];
            }
        }

        return $results;
    }

    private function testRateLimiting(): array
    {
        $url = $this->baseUrl . '/api/products';
        $requests = [];
        $successCount = 0;

        // Make 100 rapid requests
        for ($i = 0; $i < 100; $i++) {
            try {
                $startTime = microtime(true);
                $response = Http::timeout(5)->get($url);
                $endTime = microtime(true);
                
                $requests[] = [
                    'request' => $i + 1,
                    'status' => $response->status(),
                    'response_time_ms' => round(($endTime - $startTime) * 1000, 2),
                ];

                if ($response->status() === 200) {
                    $successCount++;
                }
            } catch (\Exception $e) {
                $requests[] = [
                    'request' => $i + 1,
                    'status' => 'error',
                    'error' => $e->getMessage(),
                ];
            }
        }

        return [
            'total_requests' => 100,
            'successful_requests' => $successCount,
            'rate_limited' => $successCount < 100,
            'message' => $successCount < 100 ? 'Rate limiting is working' : 'Rate limiting may not be working',
            'sample_requests' => array_slice($requests, 0, 10), // First 10 requests
        ];
    }

    private function testPerformance(): array
    {
        $results = [];

        // Test response times for different endpoints
        $endpoints = [
            'GET /api/products',
            'GET /api/categories',
            'GET /api/brands',
        ];

        foreach ($endpoints as $endpoint) {
            $results[$endpoint] = $this->testEndpointPerformance($endpoint);
        }

        return $results;
    }

    private function testEndpointPerformance(string $endpoint): array
    {
        $url = $this->baseUrl . $endpoint;
        $times = [];
        $successCount = 0;

        // Make 10 requests and measure performance
        for ($i = 0; $i < 10; $i++) {
            try {
                $startTime = microtime(true);
                $response = Http::timeout(10)->get($url);
                $endTime = microtime(true);
                
                $responseTime = ($endTime - $startTime) * 1000;
                $times[] = $responseTime;
                
                if ($response->successful()) {
                    $successCount++;
                }
            } catch (\Exception $e) {
                $times[] = null; // Failed request
            }
        }

        $validTimes = array_filter($times, fn($time) => $time !== null);
        
        return [
            'total_requests' => 10,
            'successful_requests' => $successCount,
            'average_response_time_ms' => count($validTimes) > 0 ? round(array_sum($validTimes) / count($validTimes), 2) : 0,
            'min_response_time_ms' => count($validTimes) > 0 ? round(min($validTimes), 2) : 0,
            'max_response_time_ms' => count($validTimes) > 0 ? round(max($validTimes), 2) : 0,
            'performance_rating' => $this->getPerformanceRating($validTimes),
        ];
    }

    private function testValidation(): array
    {
        $results = [];

        // Test required field validation
        $results['required_fields'] = $this->testRequiredFieldValidation();

        // Test data type validation
        $results['data_types'] = $this->testDataTypeValidation();

        // Test format validation
        $results['formats'] = $this->testFormatValidation();

        return $results;
    }

    private function testRequiredFieldValidation(): array
    {
        $url = $this->baseUrl . '/api/auth/login';
        
        $testCases = [
            [
                'data' => ['email' => 'test@example.com'], // Missing password
                'expected_status' => 422,
                'description' => 'Missing password field',
            ],
            [
                'data' => ['password' => 'password'], // Missing email
                'expected_status' => 422,
                'description' => 'Missing email field',
            ],
            [
                'data' => [], // Missing both fields
                'expected_status' => 422,
                'description' => 'Missing both fields',
            ],
        ];

        $results = [];
        foreach ($testCases as $testCase) {
            try {
                $response = Http::timeout(10)->post($url, $testCase['data']);
                $results[] = [
                    'description' => $testCase['description'],
                    'status' => $response->status(),
                    'success' => $response->status() === $testCase['expected_status'],
                    'message' => $response->status() === $testCase['expected_status'] ? 'Properly validated' : 'Validation issue',
                ];
            } catch (\Exception $e) {
                $results[] = [
                    'description' => $testCase['description'],
                    'status' => 'error',
                    'error' => $e->getMessage(),
                ];
            }
        }

        return $results;
    }

    private function testDataTypeValidation(): array
    {
        $url = $this->baseUrl . '/api/products';
        
        $testCases = [
            [
                'data' => ['price' => 'not_a_number'],
                'expected_status' => 422,
                'description' => 'Invalid price data type',
            ],
            [
                'data' => ['category_id' => 'not_a_number'],
                'expected_status' => 422,
                'description' => 'Invalid category_id data type',
            ],
        ];

        $results = [];
        foreach ($testCases as $testCase) {
            try {
                $response = Http::timeout(10)->post($url, $testCase['data']);
                $results[] = [
                    'description' => $testCase['description'],
                    'status' => $response->status(),
                    'success' => $response->status() === $testCase['expected_status'],
                    'message' => $response->status() === $testCase['expected_status'] ? 'Properly validated' : 'Validation issue',
                ];
            } catch (\Exception $e) {
                $results[] = [
                    'description' => $testCase['description'],
                    'status' => 'error',
                    'error' => $e->getMessage(),
                ];
            }
        }

        return $results;
    }

    private function testFormatValidation(): array
    {
        $url = $this->baseUrl . '/api/auth/login';
        
        $testCases = [
            [
                'data' => ['email' => 'invalid_email', 'password' => 'password'],
                'expected_status' => 422,
                'description' => 'Invalid email format',
            ],
            [
                'data' => ['email' => 'test@example.com', 'password' => '123'], // Too short
                'expected_status' => 422,
                'description' => 'Password too short',
            ],
        ];

        $results = [];
        foreach ($testCases as $testCase) {
            try {
                $response = Http::timeout(10)->post($url, $testCase['data']);
                $results[] = [
                    'description' => $testCase['description'],
                    'status' => $response->status(),
                    'success' => $response->status() === $testCase['expected_status'],
                    'message' => $response->status() === $testCase['expected_status'] ? 'Properly validated' : 'Validation issue',
                ];
            } catch (\Exception $e) {
                $results[] = [
                    'description' => $testCase['description'],
                    'status' => 'error',
                    'error' => $e->getMessage(),
                ];
            }
        }

        return $results;
    }

    private function getMethodFromEndpoint(string $endpoint): string
    {
        if (str_starts_with($endpoint, 'GET ')) return 'get';
        if (str_starts_with($endpoint, 'POST ')) return 'post';
        if (str_starts_with($endpoint, 'PUT ')) return 'put';
        if (str_starts_with($endpoint, 'DELETE ')) return 'delete';
        if (str_starts_with($endpoint, 'PATCH ')) return 'patch';
        return 'get';
    }

    private function getAuthToken(): ?string
    {
        $url = $this->baseUrl . '/api/auth/login';
        
        try {
            $response = Http::timeout(10)->post($url, [
                'email' => 'test@example.com',
                'password' => 'password',
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return $data['token'] ?? null;
            }
        } catch (\Exception $e) {
            Log::error('Failed to get auth token', ['error' => $e->getMessage()]);
        }

        return null;
    }

    private function getAdminToken(): ?string
    {
        $url = $this->baseUrl . '/api/auth/login';
        
        try {
            $response = Http::timeout(10)->post($url, [
                'email' => 'admin@example.com',
                'password' => 'admin_password',
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return $data['token'] ?? null;
            }
        } catch (\Exception $e) {
            Log::error('Failed to get admin token', ['error' => $e->getMessage()]);
        }

        return null;
    }

    private function validateRequiredFields(array $data, array $requiredFields): array
    {
        $validation = [];
        
        foreach ($requiredFields as $field) {
            $validation[$field] = [
                'required' => true,
                'present' => isset($data[$field]) || (isset($data['data']) && isset($data['data'][0][$field])),
                'valid' => isset($data[$field]) || (isset($data['data']) && isset($data['data'][0][$field])),
            ];
        }

        return $validation;
    }

    private function getPerformanceRating(array $times): string
    {
        if (empty($times)) return 'Unknown';
        
        $average = array_sum($times) / count($times);
        
        if ($average < 100) return 'Excellent';
        if ($average < 500) return 'Good';
        if ($average < 1000) return 'Fair';
        return 'Poor';
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
        if (isset($this->testResults['security_tests'])) {
            $securityTests = $this->testResults['security_tests'];
            
            if (isset($securityTests['rate_limiting']['rate_limited']) && !$securityTests['rate_limiting']['rate_limited']) {
                $recommendations[] = 'Implement rate limiting for API endpoints';
            }
            
            if (isset($securityTests['sql_injection'])) {
                foreach ($securityTests['sql_injection'] as $test) {
                    if (!$test['success']) {
                        $recommendations[] = 'Strengthen SQL injection protection';
                        break;
                    }
                }
            }
        }

        if (isset($this->testResults['performance_tests'])) {
            foreach ($this->testResults['performance_tests'] as $endpoint => $perf) {
                if (isset($perf['performance_rating']) && in_array($perf['performance_rating'], ['Fair', 'Poor'])) {
                    $recommendations[] = "Optimize performance for {$endpoint}";
                }
            }
        }

        if (empty($recommendations)) {
            $recommendations[] = 'API tests are passing well. Continue monitoring.';
        }

        return $recommendations;
    }

    public function getTestStatistics(): array
    {
        return [
            'config' => $this->testConfig,
            'base_url' => $this->baseUrl,
            'total_endpoints' => count($this->testConfig['endpoints']['public']) + 
                                count($this->testConfig['endpoints']['protected']) + 
                                count($this->testConfig['endpoints']['admin']),
        ];
    }
}
