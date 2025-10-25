<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Application\Shared\Services\BrowserAutomationTestingService;
use App\Application\Shared\Services\UserJourneyTestingService;
use App\Application\Shared\Services\CrossBrowserCompatibilityTestingService;

class EndToEndTestingCommand extends Command
{
    protected $signature = 'e2e:test 
                            {--browser : Run browser automation tests only}
                            {--journey : Run user journey tests only}
                            {--compatibility : Run cross-browser compatibility tests only}
                            {--all : Run all end-to-end tests}
                            {--report : Generate detailed test report}
                            {--export= : Export test results to file (json|csv)}';

    protected $description = 'Run comprehensive end-to-end tests for the Laravel Shop Website';

    private BrowserAutomationTestingService $browserAutomationService;
    private UserJourneyTestingService $userJourneyService;
    private CrossBrowserCompatibilityTestingService $crossBrowserService;

    public function __construct(
        BrowserAutomationTestingService $browserAutomationService,
        UserJourneyTestingService $userJourneyService,
        CrossBrowserCompatibilityTestingService $crossBrowserService
    ) {
        parent::__construct();
        $this->browserAutomationService = $browserAutomationService;
        $this->userJourneyService = $userJourneyService;
        $this->crossBrowserService = $crossBrowserService;
    }

    public function handle(): int
    {
        $this->info('🧪 Starting End-to-End Testing Suite...');
        $this->newLine();

        $startTime = microtime(true);
        $testResults = [];

        try {
            // Determine which tests to run
            $runAll = $this->option('all') || (!$this->option('browser') && !$this->option('journey') && !$this->option('compatibility'));

            if ($runAll || $this->option('browser')) {
                $testResults['browser_automation'] = $this->runBrowserAutomationTests();
            }

            if ($runAll || $this->option('journey')) {
                $testResults['user_journey'] = $this->runUserJourneyTests();
            }

            if ($runAll || $this->option('compatibility')) {
                $testResults['cross_browser'] = $this->runCrossBrowserTests();
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
            $this->info('✅ End-to-end testing completed successfully!');

            return Command::SUCCESS;

        } catch (\Exception $e) {
            $this->error('❌ End-to-end testing failed: ' . $e->getMessage());
            $this->error('Stack trace: ' . $e->getTraceAsString());
            return Command::FAILURE;
        }
    }

    private function runBrowserAutomationTests(): array
    {
        $this->info('🌐 Running Browser Automation Tests...');
        
        try {
            $results = $this->browserAutomationService->runBrowserTests();
            $report = $this->browserAutomationService->generateTestReport();
            
            $this->displayBrowserTestResults($results, $report);
            
            return [
                'results' => $results,
                'report' => $report,
                'success' => true,
            ];
        } catch (\Exception $e) {
            $this->error('Browser automation tests failed: ' . $e->getMessage());
            return [
                'error' => $e->getMessage(),
                'success' => false,
            ];
        }
    }

    private function runUserJourneyTests(): array
    {
        $this->info('👤 Running User Journey Tests...');
        
        try {
            $results = $this->userJourneyService->runUserJourneyTests();
            $report = $this->userJourneyService->generateTestReport();
            
            $this->displayUserJourneyTestResults($results, $report);
            
            return [
                'results' => $results,
                'report' => $report,
                'success' => true,
            ];
        } catch (\Exception $e) {
            $this->error('User journey tests failed: ' . $e->getMessage());
            return [
                'error' => $e->getMessage(),
                'success' => false,
            ];
        }
    }

    private function runCrossBrowserTests(): array
    {
        $this->info('🌍 Running Cross-Browser Compatibility Tests...');
        
        try {
            $results = $this->crossBrowserService->runCrossBrowserTests();
            $report = $this->crossBrowserService->generateTestReport();
            
            $this->displayCrossBrowserTestResults($results, $report);
            
            return [
                'results' => $results,
                'report' => $report,
                'success' => true,
            ];
        } catch (\Exception $e) {
            $this->error('Cross-browser compatibility tests failed: ' . $e->getMessage());
            return [
                'error' => $e->getMessage(),
                'success' => false,
            ];
        }
    }

    private function displayBrowserTestResults(array $results, array $report): void
    {
        $this->newLine();
        $this->info('📊 Browser Automation Test Results:');
        
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
                        
                        if (isset($result['load_time_ms'])) {
                            $this->line("      Load Time: {$result['load_time_ms']}ms");
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
            $this->warn('💡 Browser Automation Recommendations:');
            foreach ($report['recommendations'] as $recommendation) {
                $this->line("  • {$recommendation}");
            }
        }
    }

    private function displayUserJourneyTestResults(array $results, array $report): void
    {
        $this->newLine();
        $this->info('📊 User Journey Test Results:');
        
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

        // Display journey results
        foreach ($results as $journey => $journeyResults) {
            if (is_array($journeyResults)) {
                $this->line("  📁 {$journey}:");
                
                foreach ($journeyResults as $step => $result) {
                    if (is_array($result) && isset($result['success'])) {
                        $status = $result['success'] ? '✅' : '❌';
                        $this->line("    {$status} {$step}");
                        
                        if (isset($result['load_time_ms'])) {
                            $this->line("      Load Time: {$result['load_time_ms']}ms");
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
            $this->warn('💡 User Journey Recommendations:');
            foreach ($report['recommendations'] as $recommendation) {
                $this->line("  • {$recommendation}");
            }
        }
    }

    private function displayCrossBrowserTestResults(array $results, array $report): void
    {
        $this->newLine();
        $this->info('📊 Cross-Browser Compatibility Test Results:');
        
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

        // Display browser compatibility results
        if (isset($results['browser_compatibility'])) {
            $this->line("  📁 Browser Compatibility:");
            
            foreach ($results['browser_compatibility'] as $browser => $browserResults) {
                if (is_array($browserResults)) {
                    $compatibilityScore = $browserResults['compatibility_score'] ?? 0;
                    $status = $compatibilityScore >= 80 ? '✅' : '❌';
                    $this->line("    {$status} {$browser}: {$compatibilityScore}%");
                }
            }
        }

        // Display feature compatibility results
        if (isset($results['feature_compatibility'])) {
            $this->line("  📁 Feature Compatibility:");
            
            foreach ($results['feature_compatibility'] as $feature => $featureResults) {
                if (is_array($featureResults) && isset($featureResults['success'])) {
                    $status = $featureResults['success'] ? '✅' : '❌';
                    $this->line("    {$status} {$feature}");
                }
            }
        }

        // Display recommendations
        if (!empty($report['recommendations'])) {
            $this->newLine();
            $this->warn('💡 Cross-Browser Compatibility Recommendations:');
            foreach ($report['recommendations'] as $recommendation) {
                $this->line("  • {$recommendation}");
            }
        }
    }

    private function generateSummary(array $testResults, float $totalTime): void
    {
        $this->newLine();
        $this->info('📋 End-to-End Test Summary:');
        
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
            $this->info('🎉 Excellent! End-to-end tests are performing well.');
        } elseif ($successRate >= 75) {
            $this->warn('⚠️ Good, but there are some issues to address.');
        } else {
            $this->error('🚨 Critical issues found! Immediate attention required.');
        }
    }

    private function generateDetailedReport(array $testResults): void
    {
        $this->newLine();
        $this->info('📄 Generating Detailed End-to-End Test Report...');

        $reportPath = storage_path('app/reports/e2e_test_report_' . date('Y-m-d_H-i-s') . '.json');
        
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
                'base_url' => config('app.url'),
                'testing_framework' => 'Laravel E2E Testing Suite',
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
            'browser_compatibility_score' => 0,
            'user_journey_score' => 0,
            'cross_browser_score' => 0,
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

            // Calculate category-specific scores
            if ($category === 'browser_automation') {
                $summary['browser_compatibility_score'] = $categorySummary['success_rate'] ?? 0;
            } elseif ($category === 'user_journey') {
                $summary['user_journey_score'] = $categorySummary['success_rate'] ?? 0;
            } elseif ($category === 'cross_browser') {
                $summary['cross_browser_score'] = $categorySummary['success_rate'] ?? 0;
            }
        }

        $summary['overall_success_rate'] = $summary['total_tests'] > 0 ? 
            round(($summary['passed_tests'] / $summary['total_tests']) * 100, 2) : 0;

        return $summary;
    }

    private function exportResults(array $testResults, string $format): void
    {
        $this->newLine();
        $this->info("📤 Exporting end-to-end test results in {$format} format...");

        $exportPath = storage_path('app/exports/e2e_test_results_' . date('Y-m-d_H-i-s') . '.' . $format);
        
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

        $this->info("📤 End-to-end test results exported to: {$exportPath}");
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
