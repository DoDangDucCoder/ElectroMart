<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $seeders = [
            AdminSeeder::class,
            BrandSeeder::class,
            CategorySeeder::class,
            ProductSeeder::class,
            CustomerSeeder::class,
            BlogCategorySeeder::class,
            BlogPostSeeder::class,
            CouponSeeder::class,
            SettingSeeder::class,
        ];

        if ($this->shouldSeedDemoAccountData()) {
            $seeders[] = CustomerAccountDemoSeeder::class;
        }

        $this->call($seeders);
    }

    private function shouldSeedDemoAccountData(): bool
    {
        return app()->environment(['local', 'testing'])
            || filter_var(env('SEED_CUSTOMER_ACCOUNT_DEMO', false), FILTER_VALIDATE_BOOLEAN);
    }
}
