<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Application\Shared\Services\APITestingService;
use App\Application\Shared\Services\DatabaseTestingService;
use App\Application\Shared\Services\SecurityTestingService;
use App\Application\Shared\Services\PerformanceTestingService;

class IntegrationTestingCommand extends Command
{
    protected $signature = 'integration:test 
                            {--api : Run API tests only}
                            {--database : Run database tests only}
                            {--security : Run security tests only}
                            {--performance : Run performance tests only}
                            {--all : Run all integration tests}
                            {--report : Generate detailed test report}
                            {--export= : Export test results to file (json|csv)}';

    protected $description = 'Run comprehensive integration tests for the Laravel Shop Website';

    private APITestingService $apiTestingService;
    private DatabaseTestingService $databaseTestingService;
    private SecurityTestingService $securityTestingService;
    private PerformanceTestingService $performanceTestingService;

    public function __construct(
        APITestingService $apiTestingService,
        DatabaseTestingService $databaseTestingService,
        SecurityTestingService $securityTestingService,
        PerformanceTestingService $performanceTestingService
    ) {
        parent::__construct();
        $this->apiTestingService = $apiTestingService;
        $this->databaseTestingService = $databaseTestingService;
        $this->securityTestingService = $securityTestingService;
        $this->performanceTestingService = $performanceTestingService;
    }

    public function handle(): int
    {
        $this->info('🧪 Starting Integration Testing Suite...');
        $this->newLine();

        $startTime = microtime(true);
        $testResults = [];

        try {
            // Determine which tests to run
            $runAll = $this->option('all') || (!$this->option('api') && !$this->option('database') && !$this->option('security') && !$this->option('performance'));

            if ($runAll || $this->option('api')) {
                $testResults['api'] = $this->runAPITests();
            }

            if ($runAll || $this->option('database')) {
                $testResults['database'] = $this->runDatabaseTests();
            }

            if ($runAll || $this->option('security')) {
                $testResults['security'] = $this->runSecurityTests();
            }

            if ($runAll || $this->option('performance')) {
                $testResults['performance'] = $this->runPerformanceTests();
            }

            $endTime = microtime(true);
            $totalTime = ($endTime - $startTime) * 1000;

            // Generate summary
            $this->generateSummary($testResults, $totalTime);

            // Generate detailed report if requested
            if ($this->option('report')) {
                $this->generateDetailedReport($testResults);
            }

            // Export results if requested
            if ($this->option('export')) {
                $this->exportResults($testResults, $this->option('export'));
            }

            $this->newLine();
            $this->info('✅ Integration testing completed successfully!');

            return Command::SUCCESS;

        } catch (\Exception $e) {
            $this->error('❌ Integration testing failed: ' . $e->getMessage());
            $this->error('Stack trace: ' . $e->getTraceAsString());
            return Command::FAILURE;
        }
    }

    private function runAPITests(): array
    {
        $this->info('🔌 Running API Tests...');
        
        try {
            $results = $this->apiTestingService->runAPITests();
            $report = $this->apiTestingService->generateTestReport();
            
            $this->displayAPITestResults($results, $report);
            
            return [
                'results' => $results,
                'report' => $report,
                'success' => true,
            ];
        } catch (\Exception $e) {
            $this->error('API tests failed: ' . $e->getMessage());
            return [
                'error' => $e->getMessage(),
                'success' => false,
            ];
        }
    }

    private function runDatabaseTests(): array
    {
        $this->info('🗄️ Running Database Tests...');
        
        try {
            $results = $this->databaseTestingService->runDatabaseTests();
            $report = $this->databaseTestingService->generateTestReport();
            
            $this->displayDatabaseTestResults($results, $report);
            
            return [
                'results' => $results,
                'report' => $report,
                'success' => true,
            ];
        } catch (\Exception $e) {
            $this->error('Database tests failed: ' . $e->getMessage());
            return [
                'error' => $e->getMessage(),
                'success' => false,
            ];
        }
    }

    private function runSecurityTests(): array
    {
        $this->info('🔒 Running Security Tests...');
        
        try {
            $results = $this->securityTestingService->runSecurityTests();
            $report = $this->securityTestingService->generateSecurityReport();
            
            $this->displaySecurityTestResults($results, $report);
            
            return [
                'results' => $results,
                'report' => $report,
                'success' => true,
            ];
        } catch (\Exception $e) {
            $this->error('Security tests failed: ' . $e->getMessage());
            return [
                'error' => $e->getMessage(),
                'success' => false,
            ];
        }
    }

    private function runPerformanceTests(): array
    {
        $this->info('⚡ Running Performance Tests...');
        
        try {
            $results = $this->performanceTestingService->runComprehensiveTests();
            
            $this->displayPerformanceTestResults($results);
            
            return [
                'results' => $results,
                'success' => true,
            ];
        } catch (\Exception $e) {
            $this->error('Performance tests failed: ' . $e->getMessage());
            return [
                'error' => $e->getMessage(),
                'success' => false,
            ];
        }
    }

    private function displayAPITestResults(array $results, array $report): void
    {
        $this->newLine();
        $this->info('📊 API Test Results:');
        
        $summary = $report['summary'];
        $this->table(
            ['Metric', 'Value'],
            [
                ['Total Tests', $summary['total_tests']],
                ['Passed Tests', $summary['passed_tests']],
                ['Failed Tests', $summary['failed_tests']],
                ['Success Rate', $summary['success_rate'] . '%'],
            ]
        );

        // Display category results
        foreach ($results as $category => $categoryResults) {
            if (is_array($categoryResults)) {
                $this->line("  📁 {$category}:");
                
                foreach ($categoryResults as $test => $result) {
                    if (is_array($result) && isset($result['success'])) {
                        $status = $result['success'] ? '✅' : '❌';
                        $this->line("    {$status} {$test}");
                        
                        if (isset($result['response_time_ms'])) {
                            $this->line("      Response Time: {$result['response_time_ms']}ms");
                        }
                        
                        if (isset($result['message'])) {
                            $this->line("      Message: {$result['message']}");
                        }
                    }
                }
            }
        }

        // Display recommendations
        if (!empty($report['recommendations'])) {
            $this->newLine();
            $this->warn('💡 API Test Recommendations:');
            foreach ($report['recommendations'] as $recommendation) {
                $this->line("  • {$recommendation}");
            }
        }
    }

    private function displayDatabaseTestResults(array $results, array $report): void
    {
        $this->newLine();
        $this->info('📊 Database Test Results:');
        
        $summary = $report['summary'];
        $this->table(
            ['Metric', 'Value'],
            [
                ['Total Tests', $summary['total_tests']],
                ['Passed Tests', $summary['passed_tests']],
                ['Failed Tests', $summary['failed_tests']],
                ['Success Rate', $summary['success_rate'] . '%'],
            ]
        );

        // Display category results
        foreach ($results as $category => $categoryResults) {
            if (is_array($categoryResults)) {
                $this->line("  📁 {$category}:");
                
                foreach ($categoryResults as $test => $result) {
                    if (is_array($result) && isset($result['success'])) {
                        $status = $result['success'] ? '✅' : '❌';
                        $this->line("    {$status} {$test}");
                        
                        if (isset($result['message'])) {
                            $this->line("      Message: {$result['message']}");
                        }
                    }
                }
            }
        }

        // Display recommendations
        if (!empty($report['recommendations'])) {
            $this->newLine();
            $this->warn('💡 Database Test Recommendations:');
            foreach ($report['recommendations'] as $recommendation) {
                $this->line("  • {$recommendation}");
            }
        }
    }

    private function displaySecurityTestResults(array $results, array $report): void
    {
        $this->newLine();
        $this->info('📊 Security Test Results:');
        
        $summary = $report['summary'];
        $this->table(
            ['Metric', 'Value'],
            [
                ['Total Tests', $summary['total_tests']],
                ['Passed Tests', $summary['passed_tests']],
                ['Failed Tests', $summary['failed_tests']],
                ['Success Rate', $summary['success_rate'] . '%'],
                ['Vulnerabilities Found', $summary['vulnerabilities_found']],
                ['Security Score', $report['security_score'] . '%'],
            ]
        );

        // Display vulnerabilities
        if (!empty($report['vulnerabilities'])) {
            $this->newLine();
            $this->error('🚨 Security Vulnerabilities Found:');
            foreach ($report['vulnerabilities'] as $vulnerability) {
                $this->line("  • {$vulnerability}");
            }
        }

        // Display recommendations
        if (!empty($report['recommendations'])) {
            $this->newLine();
            $this->warn('💡 Security Recommendations:');
            foreach ($report['recommendations'] as $recommendation) {
                $this->line("  • {$recommendation}");
            }
        }
    }

    private function displayPerformanceTestResults(array $results): void
    {
        $this->newLine();
        $this->info('📊 Performance Test Results:');
        
        if (isset($results['overall_score'])) {
            $this->line("Overall Performance Score: {$results['overall_score']}%");
        }

        if (isset($results['categories'])) {
            foreach ($results['categories'] as $category => $metrics) {
                $this->line("  📁 {$category}:");
                
                if (is_array($metrics)) {
                    foreach ($metrics as $metric => $value) {
                        if (is_numeric($value)) {
                            $this->line("    {$metric}: {$value}");
                        }
                    }
                }
            }
        }

        if (isset($results['recommendations'])) {
            $this->newLine();
            $this->warn('💡 Performance Recommendations:');
            foreach ($results['recommendations'] as $recommendation) {
                $this->line("  • {$recommendation}");
            }
        }
    }

    private function generateSummary(array $testResults, float $totalTime): void
    {
        $this->newLine();
        $this->info('📋 Integration Test Summary:');
        
        $totalTests = 0;
        $passedTests = 0;
        $failedTests = 0;
        $categories = [];

        foreach ($testResults as $category => $result) {
            if (isset($result['report']['summary'])) {
                $summary = $result['report']['summary'];
                $totalTests += $summary['total_tests'];
                $passedTests += $summary['passed_tests'];
                $failedTests += $summary['failed_tests'];
                $categories[] = $category;
            }
        }

        $successRate = $totalTests > 0 ? round(($passedTests / $totalTests) * 100, 2) : 0;

        $this->table(
            ['Metric', 'Value'],
            [
                ['Test Categories', implode(', ', $categories)],
                ['Total Tests', $totalTests],
                ['Passed Tests', $passedTests],
                ['Failed Tests', $failedTests],
                ['Success Rate', $successRate . '%'],
                ['Total Execution Time', round($totalTime, 2) . 'ms'],
            ]
        );

        // Overall status
        if ($successRate >= 90) {
            $this->info('🎉 Excellent! Integration tests are performing well.');
        } elseif ($successRate >= 75) {
            $this->warn('⚠️ Good, but there are some issues to address.');
        } else {
            $this->error('🚨 Critical issues found! Immediate attention required.');
        }
    }

    private function generateDetailedReport(array $testResults): void
    {
        $this->newLine();
        $this->info('📄 Generating Detailed Test Report...');

        $reportPath = storage_path('app/reports/integration_test_report_' . date('Y-m-d_H-i-s') . '.json');
        
        // Ensure directory exists
        $reportDir = dirname($reportPath);
        if (!is_dir($reportDir)) {
            mkdir($reportDir, 0755, true);
        }

        $report = [
            'timestamp' => now()->toISOString(),
            'test_results' => $testResults,
            'summary' => $this->generateOverallSummary($testResults),
            'environment' => [
                'laravel_version' => app()->version(),
                'php_version' => PHP_VERSION,
                'database' => config('database.default'),
                'cache_driver' => config('cache.default'),
            ],
        ];

        file_put_contents($reportPath, json_encode($report, JSON_PRETTY_PRINT));
        
        $this->info("📄 Detailed report saved to: {$reportPath}");
    }

    private function generateOverallSummary(array $testResults): array
    {
        $summary = [
            'total_categories' => count($testResults),
            'successful_categories' => 0,
            'failed_categories' => 0,
            'total_tests' => 0,
            'passed_tests' => 0,
            'failed_tests' => 0,
            'vulnerabilities_found' => 0,
            'security_score' => 0,
        ];

        foreach ($testResults as $category => $result) {
            if ($result['success']) {
                $summary['successful_categories']++;
            } else {
                $summary['failed_categories']++;
            }

            if (isset($result['report']['summary'])) {
                $categorySummary = $result['report']['summary'];
                $summary['total_tests'] += $categorySummary['total_tests'];
                $summary['passed_tests'] += $categorySummary['passed_tests'];
                $summary['failed_tests'] += $categorySummary['failed_tests'];
            }

            if (isset($result['report']['vulnerabilities_found'])) {
                $summary['vulnerabilities_found'] += $result['report']['vulnerabilities_found'];
            }

            if (isset($result['report']['security_score'])) {
                $summary['security_score'] = max($summary['security_score'], $result['report']['security_score']);
            }
        }

        $summary['overall_success_rate'] = $summary['total_tests'] > 0 ? 
            round(($summary['passed_tests'] / $summary['total_tests']) * 100, 2) : 0;

        return $summary;
    }

    private function exportResults(array $testResults, string $format): void
    {
        $this->newLine();
        $this->info("📤 Exporting test results in {$format} format...");

        $exportPath = storage_path('app/exports/integration_test_results_' . date('Y-m-d_H-i-s') . '.' . $format);
        
        // Ensure directory exists
        $exportDir = dirname($exportPath);
        if (!is_dir($exportDir)) {
            mkdir($exportDir, 0755, true);
        }

        switch (strtolower($format)) {
            case 'json':
                $this->exportToJson($testResults, $exportPath);
                break;
            case 'csv':
                $this->exportToCsv($testResults, $exportPath);
                break;
            default:
                $this->error("Unsupported export format: {$format}");
                return;
        }

        $this->info("📤 Test results exported to: {$exportPath}");
    }

    private function exportToJson(array $testResults, string $path): void
    {
        $exportData = [
            'timestamp' => now()->toISOString(),
            'test_results' => $testResults,
            'summary' => $this->generateOverallSummary($testResults),
        ];

        file_put_contents($path, json_encode($exportData, JSON_PRETTY_PRINT));
    }

    private function exportToCsv(array $testResults, string $path): void
    {
        $csvData = [];
        $csvData[] = ['Category', 'Test', 'Status', 'Message', 'Details'];

        foreach ($testResults as $category => $result) {
            if (isset($result['results'])) {
                foreach ($result['results'] as $testCategory => $tests) {
                    if (is_array($tests)) {
                        foreach ($tests as $test => $testResult) {
                            if (is_array($testResult)) {
                                $status = $testResult['success'] ?? false ? 'PASS' : 'FAIL';
                                $message = $testResult['message'] ?? '';
                                $details = json_encode($testResult);
                                
                                $csvData[] = [$category, $test, $status, $message, $details];
                            }
                        }
                    }
                }
            }
        }

        $file = fopen($path, 'w');
        foreach ($csvData as $row) {
            fputcsv($file, $row);
        }
        fclose($file);
    }
}
