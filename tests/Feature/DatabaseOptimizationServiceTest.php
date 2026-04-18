<?php

namespace Tests\Feature;

use App\Application\Shared\Services\DatabaseOptimizationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class DatabaseOptimizationServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_missing_indexes_skips_equivalent_existing_index_with_different_name(): void
    {
        DB::statement('DROP INDEX IF EXISTS idx_customers_email');
        DB::statement('CREATE INDEX custom_customers_email_idx ON customers (email)');

        $service = app(DatabaseOptimizationService::class);
        $results = $service->createMissingIndexes();

        $this->assertSame('Equivalent index already exists', $results['customers']['email']);

        $indexes = array_map(
            fn ($row) => $row->name,
            DB::select('PRAGMA index_list(\'customers\')')
        );

        $this->assertContains('custom_customers_email_idx', $indexes);
        $this->assertNotContains('idx_customers_email', $indexes);
    }
}
