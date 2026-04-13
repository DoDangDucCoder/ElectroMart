<?php

namespace Tests\Unit;

use App\Support\StorefrontLink;
use stdClass;
use Tests\TestCase;

class StorefrontLinkTest extends TestCase
{
    public function test_it_filters_invalid_href_items_and_applies_defaults(): void
    {
        $links = StorefrontLink::normalize([
            ['label' => 'Missing href'],
            ['href' => '/lien-he', 'label' => 'Lien he'],
            new stdClass(),
            (object) ['href' => '/gio-hang', 'label' => 'Gio hang', 'icon' => 'fa-cart-plus'],
        ]);

        $this->assertCount(2, $links);
        $this->assertSame('/lien-he', $links[0]['href']);
        $this->assertSame('Lien he', $links[0]['label']);
        $this->assertSame('fa-angle-right', $links[0]['icon']);
        $this->assertSame('', $links[0]['note']);
        $this->assertSame('/gio-hang', $links[1]['href']);
        $this->assertSame('fa-cart-plus', $links[1]['icon']);
    }

    public function test_it_supports_custom_url_keys(): void
    {
        $links = StorefrontLink::normalize([
            ['label' => 'Missing url'],
            ['url' => '/video-shop', 'label' => 'Video'],
        ], 'url');

        $this->assertCount(1, $links);
        $this->assertSame('/video-shop', $links[0]['url']);
        $this->assertSame('Video', $links[0]['label']);
    }
}
