<?php

namespace Tests\Unit;

use App\Application\Shared\Services\ProductSearchService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class ProductSearchServiceTest extends TestCase
{
    public function test_it_skips_mysql_fulltext_when_required_index_is_missing(): void
    {
        $service = new class extends ProductSearchService
        {
            public function canUseMysqlFullTextForTest(string $keyword, ?string $driver = null): bool
            {
                return $this->canUseMysqlFullText($keyword, $driver);
            }
        };

        Schema::shouldReceive('hasTable')
            ->once()
            ->with('products')
            ->andReturn(true);

        Schema::shouldReceive('hasIndex')
            ->once()
            ->with('products', ['name', 'description'], 'fulltext')
            ->andReturn(false);

        $this->assertFalse($service->canUseMysqlFullTextForTest('iphone', 'mysql'));
    }

    public function test_it_skips_fulltext_for_non_mysql_drivers(): void
    {
        $service = new class extends ProductSearchService
        {
            public function canUseMysqlFullTextForTest(string $keyword, ?string $driver = null): bool
            {
                return $this->canUseMysqlFullText($keyword, $driver);
            }
        };

        $this->assertFalse($service->canUseMysqlFullTextForTest('iphone', 'sqlite'));
    }
}
