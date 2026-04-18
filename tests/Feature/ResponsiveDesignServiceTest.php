<?php

namespace Tests\Feature;

use App\Application\Shared\Services\ResponsiveDesignService;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class ResponsiveDesignServiceTest extends TestCase
{
    protected function tearDown(): void
    {
        File::deleteDirectory(storage_path('framework/testing/responsive'));

        parent::tearDown();
    }

    public function test_generate_responsive_images_skips_placeholder_output_when_generation_is_unsupported(): void
    {
        $directory = storage_path('framework/testing/responsive');
        File::ensureDirectoryExists($directory);

        $source = $directory . '/fixture.jpg';
        File::put($source, 'image');

        $results = app(ResponsiveDesignService::class)->generateResponsiveImages($source);

        $this->assertSame('skipped', $results['xs']['status']);
        $this->assertFalse($results['xs']['created']);
        $this->assertSame('skipped', $results['xs_webp']['status']);
        $this->assertFalse($results['xs_webp']['created']);
        $this->assertFalse(File::exists($directory . '/fixture_xs_320w.jpg'));
        $this->assertFalse(File::exists($directory . '/fixture_xs_320w.webp'));
    }
}
