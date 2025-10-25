<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Application\Shared\Services\FrontendOptimizationService;
use App\Application\Shared\Services\ResponsiveDesignService;
use App\Application\Shared\Services\UserExperienceEnhancementService;
use App\Application\Shared\Services\ModernUIComponentsService;

class UIUXCommand extends Command
{
    protected $signature = 'uiux:manage 
                            {--optimize-assets : Optimize frontend assets}
                            {--generate-responsive : Generate responsive CSS}
                            {--enhance-ux : Enhance user experience}
                            {--generate-components : Generate modern UI components}
                            {--create-demo : Create UI demo page}
                            {--full-setup : Run full UI/UX setup}';

    protected $description = 'Manage UI/UX improvements and generate modern components';

    public function handle(
        FrontendOptimizationService $frontendService,
        ResponsiveDesignService $responsiveService,
        UserExperienceEnhancementService $uxService,
        ModernUIComponentsService $componentsService
    ): int {
        $this->info('Starting UI/UX management...');

        try {
            if ($this->option('optimize-assets')) {
                $this->optimizeAssets($frontendService);
            } elseif ($this->option('generate-responsive')) {
                $this->generateResponsive($responsiveService);
            } elseif ($this->option('enhance-ux')) {
                $this->enhanceUX($uxService);
            } elseif ($this->option('generate-components')) {
                $this->generateComponents($componentsService);
            } elseif ($this->option('create-demo')) {
                $this->createDemo($frontendService, $responsiveService, $uxService, $componentsService);
            } elseif ($this->option('full-setup')) {
                $this->runFullSetup($frontendService, $responsiveService, $uxService, $componentsService);
            } else {
                $this->showHelp();
            }

            $this->info('UI/UX management completed successfully!');
            return 0;

        } catch (\Exception $e) {
            $this->error('UI/UX management failed: ' . $e->getMessage());
            return 1;
        }
    }

    private function optimizeAssets(FrontendOptimizationService $frontendService): void
    {
        $this->info('Optimizing frontend assets...');
        
        $results = $frontendService->optimizeAssets();
        
        $this->info('Asset Optimization Results:');
        $this->line('');
        
        foreach ($results as $type => $result) {
            if (is_array($result) && !isset($result['error'])) {
                $this->info("✓ {$type}: " . count($result) . " files processed");
            } elseif (isset($result['error'])) {
                $this->error("❌ {$type}: {$result['error']}");
            } else {
                $this->line("• {$type}: {$result}");
            }
        }
        
        // Show statistics
        $stats = $frontendService->getOptimizationStatistics();
        $this->line('');
        $this->info('Optimization Configuration:');
        $this->table(['Feature', 'Enabled'], [
            ['CSS Minify', $stats['optimization_enabled']['css_minify'] ? 'Yes' : 'No'],
            ['JS Minify', $stats['optimization_enabled']['js_minify'] ? 'Yes' : 'No'],
            ['Image Optimize', $stats['optimization_enabled']['image_optimize'] ? 'Yes' : 'No'],
            ['Font Preload', $stats['optimization_enabled']['font_preload'] ? 'Yes' : 'No'],
        ]);
    }

    private function generateResponsive(ResponsiveDesignService $responsiveService): void
    {
        $this->info('Generating responsive design...');
        
        $results = $responsiveService->generateResponsiveCSS();
        
        $this->info('Responsive CSS Generation Results:');
        $this->line('');
        
        foreach ($results as $type => $result) {
            if ($result === 'Generated') {
                $this->line("✓ {$type}");
            } elseif (isset($result['error'])) {
                $this->error("❌ {$type}: {$result['error']}");
            } else {
                $this->line("• {$type}: {$result}");
            }
        }
        
        // Show statistics
        $stats = $responsiveService->getResponsiveStatistics();
        $this->line('');
        $this->info('Responsive Design Statistics:');
        $this->table(['Metric', 'Value'], [
            ['Total Breakpoints', $stats['total_breakpoints']],
            ['Grid Columns', $stats['grid_columns']],
            ['Image Sizes', $stats['image_sizes']],
        ]);
        
        // Show breakpoints
        $this->line('');
        $this->info('Breakpoints:');
        $this->table(['Breakpoint', 'Min Width', 'Max Width', 'Name'], 
            collect($stats['breakpoints'])->map(function ($config, $breakpoint) {
                return [
                    $breakpoint,
                    $config['min'] . 'px',
                    $config['max'] ? $config['max'] . 'px' : '∞',
                    $config['name'],
                ];
            })->toArray()
        );
        
        // Validate responsive design
        $validation = $responsiveService->validateResponsiveDesign();
        if (!$validation['is_valid']) {
            $this->line('');
            $this->warn('Responsive Design Issues:');
            foreach ($validation['issues'] as $issue) {
                $this->line("• {$issue}");
            }
        }
    }

    private function enhanceUX(UserExperienceEnhancementService $uxService): void
    {
        $this->info('Enhancing user experience...');
        
        $results = $uxService->enhanceUserExperience();
        
        $this->info('UX Enhancement Results:');
        $this->line('');
        
        foreach ($results as $category => $result) {
            $this->info("✓ {$category}: Enhanced");
            if (is_array($result)) {
                foreach ($result as $feature => $status) {
                    if (is_array($status) && isset($status['enabled'])) {
                        $icon = $status['enabled'] ? '✓' : '❌';
                        $this->line("  {$icon} {$feature}");
                    }
                }
            }
        }
        
        // Show statistics
        $stats = $uxService->getUXStatistics();
        $this->line('');
        $this->info('UX Enhancement Statistics:');
        $this->table(['Category', 'Features'], [
            ['Accessibility', count($stats['features_enabled']['accessibility'])],
            ['Performance', count($stats['features_enabled']['performance'])],
            ['Interaction', count($stats['features_enabled']['interaction'])],
            ['Personalization', count($stats['features_enabled']['personalization'])],
            ['Notifications', count($stats['features_enabled']['notifications'])],
        ]);
        
        // Show enabled features
        $this->line('');
        $this->info('Enabled Features:');
        foreach ($stats['features_enabled'] as $category => $features) {
            $this->line("• {$category}:");
            foreach ($features as $feature => $enabled) {
                $icon = $enabled ? '✓' : '❌';
                $this->line("  {$icon} {$feature}");
            }
        }
    }

    private function generateComponents(ModernUIComponentsService $componentsService): void
    {
        $this->info('Generating modern UI components...');
        
        $results = $componentsService->generateModernComponents();
        
        $this->info('UI Components Generation Results:');
        $this->line('');
        
        foreach ($results as $component => $result) {
            $this->line("✓ {$component}: Generated");
            if (isset($result['css'])) {
                $this->line("  • CSS: " . strlen($result['css']) . " characters");
            }
            if (isset($result['js'])) {
                $this->line("  • JavaScript: " . strlen($result['js']) . " characters");
            }
            if (isset($result['html_examples'])) {
                $this->line("  • HTML Examples: " . count($result['html_examples']) . " examples");
            }
        }
        
        // Show statistics
        $stats = $componentsService->getComponentStatistics();
        $this->line('');
        $this->info('Component Statistics:');
        $this->table(['Metric', 'Value'], [
            ['Total Components', $stats['total_components']],
            ['Component Types', implode(', ', $stats['component_types'])],
        ]);
        
        // Show component details
        $this->line('');
        $this->info('Component Details:');
        foreach ($stats['config'] as $component => $config) {
            $this->line("• {$component}:");
            foreach ($config as $key => $value) {
                if (is_array($value)) {
                    $this->line("  - {$key}: " . implode(', ', $value));
                } else {
                    $this->line("  - {$key}: " . ($value ? 'Yes' : 'No'));
                }
            }
        }
    }

    private function createDemo(
        FrontendOptimizationService $frontendService,
        ResponsiveDesignService $responsiveService,
        UserExperienceEnhancementService $uxService,
        ModernUIComponentsService $componentsService
    ): void {
        $this->info('Creating UI demo page...');
        
        // Generate demo HTML
        $demoHTML = $this->generateDemoHTML();
        
        // Save demo file
        $demoPath = public_path('/ui-demo.html');
        file_put_contents($demoPath, $demoHTML);
        
        $this->info("Demo page created at: {$demoPath}");
        $this->line("Access it at: " . url('/ui-demo.html'));
        
        // Generate component examples
        $this->generateComponentExamples();
        
        $this->info('UI demo page created successfully!');
    }

    private function runFullSetup(
        FrontendOptimizationService $frontendService,
        ResponsiveDesignService $responsiveService,
        UserExperienceEnhancementService $uxService,
        ModernUIComponentsService $componentsService
    ): void {
        $this->info('Running full UI/UX setup...');
        
        // Step 1: Optimize assets
        $this->info('Step 1: Optimizing assets...');
        $this->optimizeAssets($frontendService);
        
        // Step 2: Generate responsive design
        $this->info('Step 2: Generating responsive design...');
        $this->generateResponsive($responsiveService);
        
        // Step 3: Enhance UX
        $this->info('Step 3: Enhancing user experience...');
        $this->enhanceUX($uxService);
        
        // Step 4: Generate components
        $this->info('Step 4: Generating modern components...');
        $this->generateComponents($componentsService);
        
        // Step 5: Create demo
        $this->info('Step 5: Creating demo page...');
        $this->createDemo($frontendService, $responsiveService, $uxService, $componentsService);
        
        $this->info('Full UI/UX setup completed!');
    }

    private function generateDemoHTML(): string
    {
        return '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UI/UX Demo - Laravel Shop</title>
    <link rel="stylesheet" href="/frontend/css/responsive-base.css">
    <link rel="stylesheet" href="/frontend/css/responsive-utilities.css">
    <link rel="stylesheet" href="/frontend/css/responsive-grid.css">
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; }
        .demo-section { margin: 2rem 0; padding: 2rem; border: 1px solid #dee2e6; border-radius: 0.375rem; }
        .demo-title { color: #007bff; margin-bottom: 1rem; }
        .component-showcase { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1rem; }
    </style>
</head>
<body>
    <div class="container">
        <header class="text-center py-5">
            <h1>UI/UX Demo Page</h1>
            <p class="lead">Modern components and responsive design showcase</p>
        </header>

        <section class="demo-section">
            <h2 class="demo-title">Buttons</h2>
            <div class="component-showcase">
                <button class="btn btn-primary">Primary</button>
                <button class="btn btn-secondary">Secondary</button>
                <button class="btn btn-success">Success</button>
                <button class="btn btn-danger">Danger</button>
                <button class="btn btn-outline btn-primary">Outline</button>
                <button class="btn btn-primary btn-sm">Small</button>
                <button class="btn btn-primary btn-lg">Large</button>
            </div>
        </section>

        <section class="demo-section">
            <h2 class="demo-title">Cards</h2>
            <div class="component-showcase">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Card Title</h5>
                        <p class="card-text">This is a basic card component.</p>
                        <button class="btn btn-primary">Action</button>
                    </div>
                </div>
                <div class="card card-elevated">
                    <div class="card-header">Header</div>
                    <div class="card-body">
                        <h5 class="card-title">Elevated Card</h5>
                        <p class="card-text">This card has elevation.</p>
                    </div>
                    <div class="card-footer">Footer</div>
                </div>
            </div>
        </section>

        <section class="demo-section">
            <h2 class="demo-title">Forms</h2>
            <div class="component-showcase">
                <div class="form-group">
                    <label class="form-label">Email</label>
                    <input type="email" class="form-control" placeholder="Enter email">
                </div>
                <div class="form-floating">
                    <input type="text" class="form-control" id="floatingInput" placeholder="Name">
                    <label for="floatingInput">Name</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="check1">
                    <label class="form-check-label" for="check1">Checkbox</label>
                </div>
            </div>
        </section>

        <section class="demo-section">
            <h2 class="demo-title">Alerts</h2>
            <div class="component-showcase">
                <div class="alert alert-success">Success alert</div>
                <div class="alert alert-danger">Danger alert</div>
                <div class="alert alert-warning">Warning alert</div>
                <div class="alert alert-info">Info alert</div>
            </div>
        </section>

        <section class="demo-section">
            <h2 class="demo-title">Loaders</h2>
            <div class="component-showcase">
                <div class="spinner"></div>
                <div class="progress">
                    <div class="progress-bar" style="width: 75%"></div>
                </div>
                <div class="pulse">Pulse animation</div>
            </div>
        </section>

        <section class="demo-section">
            <h2 class="demo-title">Responsive Grid</h2>
            <div class="row">
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Column 1</h5>
                            <p class="card-text">Responsive grid column.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Column 2</h5>
                            <p class="card-text">Responsive grid column.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Column 3</h5>
                            <p class="card-text">Responsive grid column.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <footer class="text-center py-5">
            <p>&copy; 2024 Laravel Shop - UI/UX Demo</p>
        </footer>
    </div>

    <script>
        // Add interactive functionality
        document.addEventListener("DOMContentLoaded", function() {
            // Add hover effects
            document.querySelectorAll(".card").forEach(card => {
                card.classList.add("hover-lift");
            });
            
            // Add click effects
            document.querySelectorAll(".btn").forEach(btn => {
                btn.addEventListener("click", function(e) {
                    e.preventDefault();
                    console.log("Button clicked:", this.textContent);
                });
            });
        });
    </script>
</body>
</html>';
    }

    private function generateComponentExamples(): void
    {
        $examplesPath = public_path('/component-examples');
        
        if (!is_dir($examplesPath)) {
            mkdir($examplesPath, 0755, true);
        }
        
        $examples = [
            'buttons.html' => $this->generateButtonExamples(),
            'cards.html' => $this->generateCardExamples(),
            'forms.html' => $this->generateFormExamples(),
            'navigation.html' => $this->generateNavigationExamples(),
        ];
        
        foreach ($examples as $filename => $content) {
            file_put_contents($examplesPath . '/' . $filename, $content);
        }
        
        $this->info('Component examples generated in: ' . $examplesPath);
    }

    private function generateButtonExamples(): string
    {
        return '<!DOCTYPE html>
<html>
<head>
    <title>Button Examples</title>
    <link rel="stylesheet" href="/frontend/css/responsive-base.css">
</head>
<body>
    <div class="container py-5">
        <h1>Button Components</h1>
        
        <section class="py-3">
            <h2>Button Variants</h2>
            <button class="btn btn-primary">Primary</button>
            <button class="btn btn-secondary">Secondary</button>
            <button class="btn btn-success">Success</button>
            <button class="btn btn-danger">Danger</button>
            <button class="btn btn-warning">Warning</button>
            <button class="btn btn-info">Info</button>
        </section>
        
        <section class="py-3">
            <h2>Button Sizes</h2>
            <button class="btn btn-primary btn-sm">Small</button>
            <button class="btn btn-primary">Medium</button>
            <button class="btn btn-primary btn-lg">Large</button>
        </section>
        
        <section class="py-3">
            <h2>Button Styles</h2>
            <button class="btn btn-primary">Solid</button>
            <button class="btn btn-outline btn-primary">Outline</button>
            <button class="btn btn-ghost btn-primary">Ghost</button>
            <button class="btn btn-link">Link</button>
        </section>
    </div>
</body>
</html>';
    }

    private function generateCardExamples(): string
    {
        return '<!DOCTYPE html>
<html>
<head>
    <title>Card Examples</title>
    <link rel="stylesheet" href="/frontend/css/responsive-base.css">
</head>
<body>
    <div class="container py-5">
        <h1>Card Components</h1>
        
        <div class="row">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Basic Card</h5>
                        <p class="card-text">This is a basic card component.</p>
                        <button class="btn btn-primary">Action</button>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">Header</div>
                    <div class="card-body">
                        <h5 class="card-title">Card with Header</h5>
                        <p class="card-text">This card has a header section.</p>
                    </div>
                    <div class="card-footer">Footer</div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card card-elevated">
                    <div class="card-body">
                        <h5 class="card-title">Elevated Card</h5>
                        <p class="card-text">This card has elevation effect.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>';
    }

    private function generateFormExamples(): string
    {
        return '<!DOCTYPE html>
<html>
<head>
    <title>Form Examples</title>
    <link rel="stylesheet" href="/frontend/css/responsive-base.css">
</head>
<body>
    <div class="container py-5">
        <h1>Form Components</h1>
        
        <form>
            <div class="form-group">
                <label class="form-label">Email</label>
                <input type="email" class="form-control" placeholder="Enter email">
            </div>
            
            <div class="form-floating">
                <input type="text" class="form-control" id="floatingInput" placeholder="Name">
                <label for="floatingInput">Name</label>
            </div>
            
            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="check1">
                <label class="form-check-label" for="check1">Checkbox</label>
            </div>
            
            <div class="form-check">
                <input class="form-check-input" type="radio" id="radio1" name="radio">
                <label class="form-check-label" for="radio1">Radio Button</label>
            </div>
            
            <button type="submit" class="btn btn-primary">Submit</button>
        </form>
    </div>
</body>
</html>';
    }

    private function generateNavigationExamples(): string
    {
        return '<!DOCTYPE html>
<html>
<head>
    <title>Navigation Examples</title>
    <link rel="stylesheet" href="/frontend/css/responsive-base.css">
</head>
<body>
    <div class="container py-5">
        <h1>Navigation Components</h1>
        
        <section class="py-3">
            <h2>Navbar</h2>
            <nav class="navbar">
                <a class="navbar-brand" href="#">Brand</a>
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link active" href="#">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">About</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Contact</a>
                    </li>
                </ul>
            </nav>
        </section>
        
        <section class="py-3">
            <h2>Breadcrumb</h2>
            <nav class="breadcrumb">
                <div class="breadcrumb-item"><a href="#">Home</a></div>
                <div class="breadcrumb-item"><a href="#">Library</a></div>
                <div class="breadcrumb-item active">Data</div>
            </nav>
        </section>
        
        <section class="py-3">
            <h2>Pagination</h2>
            <nav class="pagination">
                <div class="page-item disabled">
                    <span class="page-link">Previous</span>
                </div>
                <div class="page-item active">
                    <span class="page-link">1</span>
                </div>
                <div class="page-item">
                    <a class="page-link" href="#">2</a>
                </div>
                <div class="page-item">
                    <a class="page-link" href="#">3</a>
                </div>
                <div class="page-item">
                    <a class="page-link" href="#">Next</a>
                </div>
            </nav>
        </section>
    </div>
</body>
</html>';
    }

    private function showHelp(): void
    {
        $this->info('UI/UX Management Commands:');
        $this->line('');
        $this->line('Available options:');
        $this->line('  --optimize-assets     Optimize frontend assets (CSS, JS, images)');
        $this->line('  --generate-responsive Generate responsive CSS and breakpoints');
        $this->line('  --enhance-ux         Enhance user experience features');
        $this->line('  --generate-components Generate modern UI components');
        $this->line('  --create-demo        Create UI demo page');
        $this->line('  --full-setup         Run full UI/UX setup');
        $this->line('');
        $this->line('Examples:');
        $this->line('  php artisan uiux:manage --optimize-assets');
        $this->line('  php artisan uiux:manage --generate-responsive');
        $this->line('  php artisan uiux:manage --full-setup');
    }
}
