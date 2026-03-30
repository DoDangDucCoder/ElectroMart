<?php

namespace Tests\Unit;

use App\Support\StorefrontLabel;
use PHPUnit\Framework\TestCase;

class StorefrontLabelTest extends TestCase
{
    public function test_category_label_is_normalized_from_legacy_ascii_name_or_slug(): void
    {
        $this->assertSame('Điện thoại', StorefrontLabel::category('Dien thoai'));
        $this->assertSame('Điện thoại', StorefrontLabel::category('Whatever', 'dien-thoai'));
        $this->assertSame('Phụ kiện', StorefrontLabel::category('Phu kien'));
    }

    public function test_blog_category_label_is_normalized_from_legacy_ascii_name_or_slug(): void
    {
        $this->assertSame('Tin tức', StorefrontLabel::blogCategory('Tin tuc'));
        $this->assertSame('Tin tức công nghệ', StorefrontLabel::blogCategory('Whatever', 'tin-tuc-cong-nghe'));
        $this->assertSame('Khuyến mãi', StorefrontLabel::blogCategory('Khuyen mai'));
    }
}
