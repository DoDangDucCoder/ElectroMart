<?php

namespace Tests\Feature;

use App\Application\Shared\Services\FrontendOptimizationService;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class FrontendOptimizationServiceTest extends TestCase
{
    protected function tearDown(): void
    {
        foreach ([
            public_path('frontend/css/frontend-optimization.css'),
            public_path('frontend/css/frontend-optimization.min.css'),
            public_path('frontend/css/frontend-optimization.purged.css'),
            public_path('frontend/css/frontend-optimization.critical.css'),
            public_path('frontend/css/cleanup-source.css'),
            public_path('frontend/css/cleanup-source.min.css'),
            public_path('frontend/css/cleanup-source.critical.css'),
            public_path('frontend/css/cleanup-source.purged.css'),
            public_path('frontend/css/cleanup-source.min.min.css'),
            public_path('frontend/js/frontend-optimization.js'),
            public_path('frontend/js/frontend-optimization.min.js'),
            public_path('frontend/js/frontend-optimization.shaken.js'),
            public_path('frontend/js/cleanup-source.js'),
            public_path('frontend/js/cleanup-source.min.js'),
            public_path('frontend/js/cleanup-source.shaken.js'),
            public_path('frontend/js/cleanup-source.min.min.js'),
            public_path('frontend/fonts/frontend-optimization.otf'),
            public_path('frontend/fonts/frontend-optimization.woff2'),
            public_path('uploads/testing/nested/frontend-optimization.png'),
            public_path('uploads/testing/nested/frontend-optimization.webp'),
            public_path('uploads/testing/nested/frontend-optimization_320.png'),
            resource_path('views/testing/frontend-optimization/fixture.blade.php'),
        ] as $path) {
            File::delete($path);
        }

        File::deleteDirectory(public_path('uploads/testing/nested'));
        File::deleteDirectory(resource_path('views/testing/frontend-optimization'));

        parent::tearDown();
    }

    public function test_asset_optimization_scans_nested_views_and_uploads_recursively(): void
    {
        $cssDirectory = public_path('frontend/css');
        $jsDirectory = public_path('frontend/js');
        $fontDirectory = public_path('frontend/fonts');
        $uploadDirectory = public_path('uploads/testing/nested');
        $viewDirectory = resource_path('views/testing/frontend-optimization');

        File::ensureDirectoryExists($cssDirectory);
        File::ensureDirectoryExists($jsDirectory);
        File::ensureDirectoryExists($fontDirectory);
        File::ensureDirectoryExists($uploadDirectory);
        File::ensureDirectoryExists($viewDirectory);

        File::put($cssDirectory . '/frontend-optimization.css', ".hero-banner {\n  color: red;\n}\n.unused-banner {\n  color: blue;\n}\n");
        File::put($cssDirectory . '/frontend-optimization.min.css', '.artifact{display:none;}');
        File::put($jsDirectory . '/frontend-optimization.js', "function usedAction() {\n  return true;\n}\nfunction unusedAction() {\n  return false;\n}\n");
        File::put($jsDirectory . '/frontend-optimization.shaken.js', 'function artifact() { return null; }');
        File::put($fontDirectory . '/frontend-optimization.otf', 'font');
        File::put($fontDirectory . '/frontend-optimization.woff2', 'artifact');
        File::put($uploadDirectory . '/frontend-optimization.png', 'png');
        File::put($uploadDirectory . '/frontend-optimization_320.png', 'png');
        File::put($viewDirectory . '/fixture.blade.php', '<div class="hero-banner" onclick="usedAction()"></div>');

        $results = app(FrontendOptimizationService::class)->optimizeAssets();

        $this->assertArrayHasKey('frontend-optimization.css', $results['css']);
        $this->assertArrayHasKey('frontend-optimization.js', $results['js']);
        $this->assertArrayHasKey('frontend-optimization.otf', $results['fonts']);
        $this->assertArrayHasKey('frontend-optimization.png', $results['images']);
        $this->assertArrayNotHasKey('frontend-optimization.min.css', $results['css']);
        $this->assertArrayNotHasKey('frontend-optimization.shaken.js', $results['js']);
        $this->assertArrayNotHasKey('frontend-optimization.woff2', $results['fonts']);
        $this->assertSame('Skipped', $results['fonts']['frontend-optimization.otf']['preload']);
        $this->assertSame('Skipped', $results['fonts']['frontend-optimization.otf']['woff2']);
        $this->assertSame('Skipped', $results['images']['frontend-optimization.png']['optimized']);
        $this->assertSame('Skipped', $results['images']['frontend-optimization.png']['webp']);
        $this->assertSame('Skipped', $results['images']['frontend-optimization.png']['responsive']);
        $this->assertTrue(File::exists($cssDirectory . '/frontend-optimization.purged.css'));
        $this->assertStringContainsString('.hero-banner', File::get($cssDirectory . '/frontend-optimization.purged.css'));
        $this->assertStringNotContainsString('.unused-banner', File::get($cssDirectory . '/frontend-optimization.purged.css'));
        $this->assertFalse(collect($results['manifest']['css'] ?? [])->contains(
            fn (array $asset): bool => ($asset['file'] ?? null) === 'frontend-optimization.min.css'
        ));
        $this->assertFalse(collect($results['manifest']['images'] ?? [])->contains(
            fn (array $image): bool => ($image['file'] ?? null) === 'frontend-optimization_320.png'
        ));
        $this->assertTrue(collect($results['manifest']['images'] ?? [])->contains(
            fn (array $image): bool => ($image['path'] ?? null) === '/uploads/testing/nested/frontend-optimization.png'
        ));
    }

    public function test_cleanup_generated_artifacts_only_removes_whitelisted_frontend_outputs(): void
    {
        $cssDirectory = public_path('frontend/css');
        $jsDirectory = public_path('frontend/js');

        File::ensureDirectoryExists($cssDirectory);
        File::ensureDirectoryExists($jsDirectory);

        File::put($cssDirectory . '/cleanup-source.css', 'body{}');
        File::put($cssDirectory . '/cleanup-source.min.css', 'body{}');
        File::put($cssDirectory . '/cleanup-source.critical.css', 'body{}');
        File::put($cssDirectory . '/cleanup-source.purged.css', 'body{}');
        File::put($cssDirectory . '/cleanup-source.min.min.css', 'body{}');

        File::put($jsDirectory . '/cleanup-source.js', 'console.log(1);');
        File::put($jsDirectory . '/cleanup-source.min.js', 'console.log(1);');
        File::put($jsDirectory . '/cleanup-source.shaken.js', 'console.log(1);');
        File::put($jsDirectory . '/cleanup-source.min.min.js', 'console.log(1);');

        $deleted = app(FrontendOptimizationService::class)->cleanupGeneratedArtifacts();

        $this->assertContains('cleanup-source.critical.css', $deleted['css']);
        $this->assertContains('cleanup-source.purged.css', $deleted['css']);
        $this->assertContains('cleanup-source.min.min.css', $deleted['css']);
        $this->assertContains('cleanup-source.shaken.js', $deleted['js']);
        $this->assertContains('cleanup-source.min.min.js', $deleted['js']);

        $this->assertTrue(File::exists($cssDirectory . '/cleanup-source.css'));
        $this->assertTrue(File::exists($cssDirectory . '/cleanup-source.min.css'));
        $this->assertFalse(File::exists($cssDirectory . '/cleanup-source.critical.css'));
        $this->assertFalse(File::exists($cssDirectory . '/cleanup-source.purged.css'));
        $this->assertFalse(File::exists($cssDirectory . '/cleanup-source.min.min.css'));

        $this->assertTrue(File::exists($jsDirectory . '/cleanup-source.js'));
        $this->assertTrue(File::exists($jsDirectory . '/cleanup-source.min.js'));
        $this->assertFalse(File::exists($jsDirectory . '/cleanup-source.shaken.js'));
        $this->assertFalse(File::exists($jsDirectory . '/cleanup-source.min.min.js'));
    }
}
