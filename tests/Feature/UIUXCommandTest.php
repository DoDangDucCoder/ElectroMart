<?php

namespace Tests\Feature;

use App\Application\Shared\Services\FrontendOptimizationService;
use App\Application\Shared\Services\ModernUIComponentsService;
use App\Application\Shared\Services\ResponsiveDesignService;
use App\Application\Shared\Services\UserExperienceEnhancementService;
use Illuminate\Support\Facades\File;
use Mockery;
use Tests\TestCase;

class UIUXCommandTest extends TestCase
{
    protected function tearDown(): void
    {
        File::deleteDirectory(storage_path('app/uiux-demo'));
        File::deleteDirectory(storage_path('app/uiux-generated'));

        Mockery::close();

        parent::tearDown();
    }

    public function test_optimize_assets_command_reports_success_and_skipped_counts_separately(): void
    {
        $service = Mockery::mock(FrontendOptimizationService::class);
        $service->shouldReceive('optimizeAssets')->once()->andReturn([
            'css' => [
                'app.css' => [
                    'minified' => 'Success',
                    'purged' => 'Success',
                    'critical' => 'Success',
                ],
            ],
            'images' => [
                'hero.png' => [
                    'optimized' => 'Skipped',
                    'webp' => 'Skipped',
                    'responsive' => 'Skipped',
                ],
            ],
            'manifest' => [
                'css' => [],
                'js' => [],
            ],
        ]);
        $service->shouldReceive('getOptimizationStatistics')->once()->andReturn([
            'optimization_enabled' => [
                'css_minify' => true,
                'js_minify' => true,
                'image_optimize' => true,
                'font_preload' => true,
            ],
        ]);

        $this->app->instance(FrontendOptimizationService::class, $service);

        $this->artisan('uiux:manage --optimize-assets')
            ->expectsOutputToContain('css: 1 files, 3 success, 0 skipped, 0 failed')
            ->expectsOutputToContain('images: 1 files, 0 success, 3 skipped, 0 failed')
            ->assertExitCode(0);
    }

    public function test_create_demo_writes_to_storage_instead_of_public_repo_tracked_paths(): void
    {
        File::delete(public_path('ui-demo.html'));
        File::deleteDirectory(public_path('component-examples'));
        File::deleteDirectory(storage_path('app/uiux-demo'));

        $this->artisan('uiux:manage --create-demo')
            ->expectsOutputToContain(storage_path('app/uiux-demo/ui-demo.html'))
            ->assertExitCode(0);

        $this->assertFalse(File::exists(public_path('ui-demo.html')));
        $this->assertFalse(File::isDirectory(public_path('component-examples')));
        $this->assertTrue(File::exists(storage_path('app/uiux-demo/ui-demo.html')));
        $this->assertTrue(File::exists(storage_path('app/uiux-demo/component-examples/buttons.html')));
    }

    public function test_enhance_ux_command_reports_config_payloads_instead_of_claiming_runtime_enhancement(): void
    {
        $service = Mockery::mock(UserExperienceEnhancementService::class);
        $service->shouldReceive('enhanceUserExperience')->once()->andReturn([
            'accessibility' => [
                'keyboard_navigation' => ['enabled' => true, 'css' => '.focus{}'],
                'screen_reader' => ['enabled' => false],
            ],
        ]);
        $service->shouldReceive('getUXStatistics')->once()->andReturn([
            'features_enabled' => [
                'accessibility' => ['keyboard_navigation' => true, 'screen_reader' => false],
                'performance' => [],
                'interaction' => [],
                'personalization' => [],
                'notifications' => [],
            ],
        ]);

        $this->app->instance(UserExperienceEnhancementService::class, $service);

        $this->artisan('uiux:manage --enhance-ux')
            ->expectsOutputToContain('accessibility: Config payload generated')
            ->expectsOutputToContain('keyboard_navigation: payload generated')
            ->expectsOutputToContain('screen_reader: unsupported')
            ->assertExitCode(0);
    }

    public function test_generate_components_command_reports_payload_generation_only(): void
    {
        $service = Mockery::mock(ModernUIComponentsService::class);
        $service->shouldReceive('generateModernComponents')->once()->andReturn([
            'buttons' => [
                'css' => '.btn{}',
                'js' => 'console.log("btn")',
                'html_examples' => ['basic' => '<button class="btn">Button</button>'],
            ],
        ]);
        $service->shouldReceive('getComponentStatistics')->once()->andReturn([
            'total_components' => 1,
            'component_types' => ['buttons'],
            'config' => [
                'buttons' => [
                    'variants' => ['primary', 'secondary'],
                    'responsive' => true,
                ],
            ],
        ]);

        $this->app->instance(ModernUIComponentsService::class, $service);

        $this->artisan('uiux:manage --generate-components')
            ->expectsOutputToContain('buttons: Component payload generated')
            ->expectsOutputToContain('CSS: 6 characters')
            ->expectsOutputToContain('JavaScript: 18 characters')
            ->expectsOutputToContain('HTML Examples: 1 examples')
            ->assertExitCode(0);
    }

    public function test_generate_responsive_command_writes_to_storage_directory(): void
    {
        $outputDirectory = storage_path('app/uiux-generated/responsive');
        $service = Mockery::mock(ResponsiveDesignService::class);
        $service->shouldReceive('generateResponsiveCSS')
            ->once()
            ->with($outputDirectory)
            ->andReturnUsing(function (string $path) {
                File::ensureDirectoryExists($path);
                File::put($path . '/command-only.css', 'body{}');

                return [
                    'base_css' => 'Generated',
                ];
            });
        $service->shouldReceive('getResponsiveStatistics')->once()->andReturn([
            'total_breakpoints' => 6,
            'grid_columns' => 12,
            'image_sizes' => 6,
            'breakpoints' => [
                'md' => ['min' => 768, 'max' => 991, 'name' => 'Medium'],
            ],
        ]);
        $service->shouldReceive('validateResponsiveDesign')
            ->once()
            ->with($outputDirectory)
            ->andReturn([
                'is_valid' => true,
                'issues' => [],
            ]);

        $this->app->instance(ResponsiveDesignService::class, $service);

        $this->artisan('uiux:manage --generate-responsive')
            ->expectsOutputToContain('base_css: Generated in storage')
            ->expectsOutputToContain($outputDirectory)
            ->assertExitCode(0);

        $this->assertTrue(File::exists($outputDirectory . '/command-only.css'));
        $this->assertFalse(File::exists(public_path('frontend/css/command-only.css')));
    }
}
