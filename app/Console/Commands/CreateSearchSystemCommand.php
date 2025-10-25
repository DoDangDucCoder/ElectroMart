<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class CreateSearchSystemCommand extends Command
{
    protected $signature = 'make:search-system';
    protected $description = 'Create advanced search and filter system';

    public function handle()
    {
        $this->info('Creating advanced search and filter system...');

        // Create Search Services
        $this->createSearchServices();
        
        // Create Filter Classes
        $this->createFilterClasses();
        
        // Create Search Indexes
        $this->createSearchIndexes();

        $this->info('Search system created successfully!');
        return 0;
    }

    private function createSearchServices(): void
    {
        $services = [
            'ProductSearchService',
            'CategorySearchService',
            'CustomerSearchService',
            'OrderSearchService',
        ];

        foreach ($services as $service) {
            $this->createService($service);
        }
    }

    private function createFilterClasses(): void
    {
        $filters = [
            'ProductFilter',
            'CategoryFilter',
            'OrderFilter',
            'CustomerFilter',
        ];

        foreach ($filters as $filter) {
            $this->createFilter($filter);
        }
    }

    private function createSearchIndexes(): void
    {
        $this->info('Creating search indexes...');
        
        // Create search indexes migration
        $this->call('make:migration', [
            'name' => 'create_search_indexes',
            '--create' => 'search_indexes'
        ]);
    }

    private function createService(string $serviceName): void
    {
        $servicePath = app_path("Application/Shared/Services/{$serviceName}.php");
        
        if (File::exists($servicePath)) {
            $this->line("Skipping {$serviceName} (already exists)");
            return;
        }

        $content = $this->generateServiceContent($serviceName);
        File::put($servicePath, $content);
        
        $this->info("✓ Created {$serviceName}");
    }

    private function createFilter(string $filterName): void
    {
        $filterPath = app_path("Application/Shared/Filters/{$filterName}.php");
        
        $filterDir = dirname($filterPath);
        if (!File::exists($filterDir)) {
            File::makeDirectory($filterDir, 0755, true);
        }
        
        if (File::exists($filterPath)) {
            $this->line("Skipping {$filterName} (already exists)");
            return;
        }

        $content = $this->generateFilterContent($filterName);
        File::put($filterPath, $content);
        
        $this->info("✓ Created {$filterName}");
    }

    private function generateServiceContent(string $serviceName): string
    {
        return "<?php

namespace App\Application\Shared\Services;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class {$serviceName}
{
    public function search(string \$query, array \$filters = []): Collection
    {
        // TODO: Implement search logic
        return collect();
    }

    public function filter(Builder \$query, array \$filters): Builder
    {
        // TODO: Implement filter logic
        return \$query;
    }

    public function getSuggestions(string \$query): Collection
    {
        // TODO: Implement suggestions logic
        return collect();
    }
}
";
    }

    private function generateFilterContent(string $filterName): string
    {
        return "<?php

namespace App\Application\Shared\Filters;

use Illuminate\Database\Eloquent\Builder;

class {$filterName}
{
    public function apply(Builder \$query, array \$filters): Builder
    {
        // TODO: Implement filter logic
        return \$query;
    }

    protected function applyPriceFilter(Builder \$query, array \$priceRange): Builder
    {
        if (isset(\$priceRange['min'])) {
            \$query->where('price', '>=', \$priceRange['min']);
        }
        
        if (isset(\$priceRange['max'])) {
            \$query->where('price', '<=', \$priceRange['max']);
        }
        
        return \$query;
    }

    protected function applyCategoryFilter(Builder \$query, array \$categories): Builder
    {
        if (!empty(\$categories)) {
            \$query->whereIn('category_id', \$categories);
        }
        
        return \$query;
    }

    protected function applyStatusFilter(Builder \$query, string \$status): Builder
    {
        if (\$status) {
            \$query->where('status', \$status);
        }
        
        return \$query;
    }
}
";
    }
}