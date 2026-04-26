<?php

namespace Tests\Unit;

use App\Support\AssetUrl;
use Tests\TestCase;

class AssetUrlTest extends TestCase
{
    public function test_it_preserves_absolute_urls(): void
    {
        $url = 'https://cdn.example.com/product.webp';

        $this->assertSame($url, AssetUrl::product($url));
    }

    public function test_it_resolves_existing_public_paths(): void
    {
        $this->assertSame(
            asset('uploads/product/no-image.svg'),
            AssetUrl::product('uploads/product/no-image.svg')
        );
    }

    public function test_it_falls_back_for_missing_paths(): void
    {
        $this->assertSame(
            asset('uploads/product/no-image.svg'),
            AssetUrl::product('uploads/product/does-not-exist.png')
        );
    }
}
