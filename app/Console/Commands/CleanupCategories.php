<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CleanupCategories extends Command
{
    protected $signature = 'app:cleanup-categories';
    protected $description = 'Remove all categories and prepare for fresh seeding';

    public function handle()
    {
        $this->info('Deleting all product categories...');
        
        // Disable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        // Delete all categories
        DB::table('product_categories')->truncate();
        
        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        
        $this->info('✓ All categories deleted. Run: php artisan db:seed --class=CategorySeeder');
        $this->info('✓ Categories will be created with only: Điện thoại, Laptop, Phụ kiện');
    }
}
