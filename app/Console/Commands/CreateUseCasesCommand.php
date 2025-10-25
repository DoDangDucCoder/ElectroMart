<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class CreateUseCasesCommand extends Command
{
    protected $signature = 'make:use-cases';
    protected $description = 'Create additional Use Cases for Clean Architecture';

    public function handle()
    {
        $this->info('Creating Use Cases for Clean Architecture...');

        $useCases = [
            'Catalog' => [
                'UpdateProductStockUseCase',
                'SearchProductsUseCase',
                'GetProductRecommendationsUseCase',
                'ManageProductVariantsUseCase',
            ],
            'Customer' => [
                'RegisterCustomerUseCase',
                'UpdateCustomerProfileUseCase',
                'ManageCustomerAddressUseCase',
                'GetCustomerOrderHistoryUseCase',
            ],
            'Order' => [
                'ProcessOrderUseCase',
                'CancelOrderUseCase',
                'UpdateOrderStatusUseCase',
                'CalculateOrderTotalUseCase',
            ],
            'Checkout' => [
                'ValidateCouponUseCase',
                'CalculateShippingUseCase',
                'ProcessPaymentUseCase',
                'CreateOrderFromCartUseCase',
            ],
            'Marketing' => [
                'SendEmailCampaignUseCase',
                'CreatePromotionUseCase',
                'TrackCustomerBehaviorUseCase',
                'GenerateReportsUseCase',
            ],
        ];

        foreach ($useCases as $domain => $cases) {
            foreach ($cases as $useCase) {
                $this->createUseCase($domain, $useCase);
            }
        }

        $this->info('Use Cases created successfully!');
        return 0;
    }

    private function createUseCase(string $domain, string $useCase): void
    {
        $domainPath = app_path("Application/{$domain}/UseCases");
        
        if (!File::exists($domainPath)) {
            File::makeDirectory($domainPath, 0755, true);
        }

        $filePath = "{$domainPath}/{$useCase}.php";
        
        if (File::exists($filePath)) {
            $this->line("Skipping {$useCase} (already exists)");
            return;
        }

        $content = $this->generateUseCaseContent($domain, $useCase);
        File::put($filePath, $content);
        
        $this->info("✓ Created {$domain}/{$useCase}");
    }

    private function generateUseCaseContent(string $domain, string $useCase): string
    {
        $className = $useCase;
        $methodName = lcfirst(str_replace('UseCase', '', $useCase));
        
        return "<?php

namespace App\Application\\{$domain}\\UseCases;

use App\Application\\{$domain}\\Services\\{$domain}Service;

class {$className}
{
    public function __construct(
        private readonly {$domain}Service \${$domain}Service
    ) {
    }

    public function execute(array \$data): mixed
    {
        // TODO: Implement {$methodName} logic
        return \$this->{$domain}Service->{$methodName}(\$data);
    }
}
";
    }
}