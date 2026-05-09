<?php

namespace App\Console\Commands;

use App\Infrastructure\Persistence\Eloquent\Geo\VietnamProvince;
use App\Infrastructure\Persistence\Eloquent\Geo\VietnamWard;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class ImportVietnam2025LocationsCommand extends Command
{
    protected $signature = 'locations:import-vietnam-2025
                            {--path=database/data/vietnam_2025_locations.json : Local JSON file to import}';

    protected $description = 'Import Vietnam 2025 two-level administrative locations into local tables';

    public function handle(): int
    {
        if (! Schema::hasTable('vietnam_provinces') || ! Schema::hasTable('vietnam_wards')) {
            $this->error('Missing vietnam_provinces or vietnam_wards table. Run php artisan migrate first.');

            return self::FAILURE;
        }

        $path = base_path((string) $this->option('path'));
        if (! is_file($path)) {
            $this->error("Location data file not found: {$path}");

            return self::FAILURE;
        }

        $items = json_decode((string) file_get_contents($path), true);
        if (! is_array($items)) {
            $this->error('Location data file is not valid JSON.');

            return self::FAILURE;
        }

        $now = now();
        $provinceRows = [];
        $wardRows = [];

        foreach ($items as $province) {
            $provinceCode = (string) ($province['code'] ?? '');
            if ($provinceCode === '') {
                continue;
            }

            $provinceRows[] = [
                'code' => $provinceCode,
                'name' => (string) ($province['name'] ?? ''),
                'type' => $province['division_type'] ?? null,
                'slug' => (string) ($province['codename'] ?? Str::slug((string) ($province['name'] ?? ''))),
                'created_at' => $now,
                'updated_at' => $now,
            ];

            foreach (($province['wards'] ?? []) as $ward) {
                $wardCode = (string) ($ward['code'] ?? '');
                if ($wardCode === '') {
                    continue;
                }

                $wardRows[] = [
                    'code' => $wardCode,
                    'province_code' => $provinceCode,
                    'name' => (string) ($ward['name'] ?? ''),
                    'type' => $ward['division_type'] ?? null,
                    'slug' => (string) ($ward['codename'] ?? Str::slug((string) ($ward['name'] ?? ''))),
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }

        try {
            DB::statement('SET FOREIGN_KEY_CHECKS=0');
            VietnamWard::query()->truncate();
            VietnamProvince::query()->truncate();
        } finally {
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
        }

        foreach (array_chunk($provinceRows, 500) as $chunk) {
            VietnamProvince::query()->insert($chunk);
        }

        foreach (array_chunk($wardRows, 500) as $chunk) {
            VietnamWard::query()->insert($chunk);
        }

        $provinceCount = VietnamProvince::query()->count();
        $wardCount = VietnamWard::query()->count();

        $this->info("Imported provinces: {$provinceCount}");
        $this->info("Imported wards: {$wardCount}");

        VietnamProvince::query()
            ->with(['wards' => fn ($query) => $query->orderBy('name')->limit(3)])
            ->orderBy('code')
            ->limit(3)
            ->get()
            ->each(function (VietnamProvince $province) {
                $wards = $province->wards->pluck('name')->implode(', ');
                $this->line("- {$province->name}: {$wards}");
            });

        if ($provinceCount !== 34 || $wardCount < 3300 || $wardCount > 3350) {
            $this->warn('Imported counts are outside the expected Vietnam 2025 range.');
        }

        return self::SUCCESS;
    }
}
