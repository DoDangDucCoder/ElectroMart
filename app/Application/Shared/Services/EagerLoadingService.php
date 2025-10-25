<?php

namespace App\Application\Shared\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Log;

class EagerLoadingService
{
    private array $relationshipMaps = [];

    public function __construct()
    {
        $this->initializeRelationshipMaps();
    }

    private function initializeRelationshipMaps(): void
    {
        $this->relationshipMaps = [
            // Product relationships
            'products' => [
                'default' => ['brand', 'primaryCategory'],
                'detailed' => ['brand', 'primaryCategory', 'media', 'reviews', 'variants'],
                'admin' => ['brand', 'primaryCategory', 'media', 'reviews', 'variants', 'categories'],
            ],
            
            // Order relationships
            'orders' => [
                'default' => ['customer', 'items'],
                'detailed' => ['customer', 'items.product', 'shipping', 'payment'],
                'admin' => ['customer', 'items.product', 'shipping', 'payment', 'customer.addresses'],
            ],
            
            // Customer relationships
            'customers' => [
                'default' => ['addresses'],
                'detailed' => ['addresses', 'orders'],
                'admin' => ['addresses', 'orders.items', 'loyaltyTier'],
            ],
            
            // Category relationships
            'categories' => [
                'default' => ['parent', 'children'],
                'detailed' => ['parent', 'children', 'products'],
                'admin' => ['parent', 'children', 'products.brand'],
            ],
            
            // Brand relationships
            'brands' => [
                'default' => [],
                'detailed' => ['products'],
                'admin' => ['products.category'],
            ],
        ];
    }

    public function applyEagerLoading(Builder $query, string $context = 'default'): Builder
    {
        $model = $query->getModel();
        $modelName = $this->getModelName($model);
        
        if (!isset($this->relationshipMaps[$modelName])) {
            return $query;
        }

        $relationships = $this->relationshipMaps[$modelName][$context] ?? 
                        $this->relationshipMaps[$modelName]['default'];

        if (!empty($relationships)) {
            $query->with($relationships);
            Log::debug('Eager loading applied', [
                'model' => $modelName,
                'context' => $context,
                'relationships' => $relationships
            ]);
        }

        return $query;
    }

    public function applySmartEagerLoading(Builder $query, array $requestedFields = []): Builder
    {
        $model = $query->getModel();
        $modelName = $this->getModelName($model);
        
        // Analyze requested fields to determine which relationships to load
        $relationships = $this->analyzeRequiredRelationships($modelName, $requestedFields);
        
        if (!empty($relationships)) {
            $query->with($relationships);
        }

        return $query;
    }

    private function analyzeRequiredRelationships(string $modelName, array $requestedFields): array
    {
        $relationships = [];
        
        foreach ($requestedFields as $field) {
            // Check if field requires a relationship
            $relationship = $this->getRelationshipForField($modelName, $field);
            if ($relationship && !in_array($relationship, $relationships)) {
                $relationships[] = $relationship;
            }
        }

        return $relationships;
    }

    private function getRelationshipForField(string $modelName, string $field): ?string
    {
        $fieldMappings = [
            'products' => [
                'brand_name' => 'brand',
                'category_name' => 'primaryCategory',
                'images' => 'media',
                'reviews_count' => 'reviews',
                'variants' => 'variants',
            ],
            'orders' => [
                'customer_name' => 'customer',
                'items' => 'items',
                'shipping_address' => 'shipping',
                'payment_method' => 'payment',
            ],
            'customers' => [
                'addresses' => 'addresses',
                'orders_count' => 'orders',
                'loyalty_tier' => 'loyaltyTier',
            ],
            'categories' => [
                'parent_name' => 'parent',
                'children' => 'children',
                'products_count' => 'products',
            ],
        ];

        return $fieldMappings[$modelName][$field] ?? null;
    }

    private function getModelName(Model $model): string
    {
        $className = get_class($model);
        $parts = explode('\\', $className);
        $modelName = end($parts);
        
        return strtolower($modelName);
    }

    public function optimizeNPlusOneQueries(Builder $query): Builder
    {
        $model = $query->getModel();
        $modelName = $this->getModelName($model);
        
        // Apply comprehensive eager loading to prevent N+1 queries
        $comprehensiveRelationships = $this->getComprehensiveRelationships($modelName);
        
        if (!empty($comprehensiveRelationships)) {
            $query->with($comprehensiveRelationships);
        }

        return $query;
    }

    private function getComprehensiveRelationships(string $modelName): array
    {
        $comprehensiveMaps = [
            'products' => [
                'brand:id,name,slug',
                'primaryCategory:id,name,slug',
                'media:id,product_id,type,path',
                'reviews:id,product_id,rating,comment',
                'variants:id,product_id,name,price',
            ],
            'orders' => [
                'customer:id,name,email',
                'items:id,order_id,product_id,quantity,price',
                'items.product:id,name,slug,price',
                'shipping:id,order_id,address',
                'payment:id,order_id,method,status',
            ],
            'customers' => [
                'addresses:id,customer_id,label,address_line1',
                'orders:id,customer_id,total,status',
                'loyaltyTier:id,name,benefits',
            ],
            'categories' => [
                'parent:id,name,slug',
                'children:id,parent_id,name,slug',
                'products:id,primary_category_id,name,price',
            ],
        ];

        return $comprehensiveMaps[$modelName] ?? [];
    }

    public function applySelectiveEagerLoading(Builder $query, array $selectFields = []): Builder
    {
        if (empty($selectFields)) {
            return $query;
        }

        $model = $query->getModel();
        $modelName = $this->getModelName($model);
        
        // Build selective relationships based on selected fields
        $selectiveRelationships = $this->buildSelectiveRelationships($modelName, $selectFields);
        
        if (!empty($selectiveRelationships)) {
            $query->with($selectiveRelationships);
        }

        return $query;
    }

    private function buildSelectiveRelationships(string $modelName, array $selectFields): array
    {
        $relationships = [];
        
        // Map select fields to required relationships
        $fieldToRelationshipMap = [
            'products' => [
                'brand_name' => 'brand:id,name',
                'category_name' => 'primaryCategory:id,name',
                'images' => 'media:id,product_id,path',
                'reviews_count' => 'reviews:id,product_id',
            ],
            'orders' => [
                'customer_name' => 'customer:id,name',
                'items_count' => 'items:id,order_id',
                'shipping_address' => 'shipping:id,order_id,address',
            ],
        ];

        $mapping = $fieldToRelationshipMap[$modelName] ?? [];
        
        foreach ($selectFields as $field) {
            if (isset($mapping[$field])) {
                $relationships[] = $mapping[$field];
            }
        }

        return array_unique($relationships);
    }

    public function getRelationshipStatistics(): array
    {
        $stats = [];
        
        foreach ($this->relationshipMaps as $model => $contexts) {
            $stats[$model] = [
                'contexts' => array_keys($contexts),
                'total_relationships' => count(array_unique(array_merge(...array_values($contexts)))),
                'most_used_context' => $this->getMostUsedContext($contexts),
            ];
        }

        return $stats;
    }

    private function getMostUsedContext(array $contexts): string
    {
        $maxRelationships = 0;
        $mostUsedContext = 'default';
        
        foreach ($contexts as $context => $relationships) {
            if (count($relationships) > $maxRelationships) {
                $maxRelationships = count($relationships);
                $mostUsedContext = $context;
            }
        }

        return $mostUsedContext;
    }

    public function addCustomRelationship(string $modelName, string $context, array $relationships): void
    {
        if (!isset($this->relationshipMaps[$modelName])) {
            $this->relationshipMaps[$modelName] = [];
        }

        $this->relationshipMaps[$modelName][$context] = $relationships;
        
        Log::info('Custom relationship added', [
            'model' => $modelName,
            'context' => $context,
            'relationships' => $relationships
        ]);
    }

    public function optimizeForApiResponse(Builder $query): Builder
    {
        $model = $query->getModel();
        $modelName = $this->getModelName($model);
        
        // API-optimized relationships (minimal data)
        $apiRelationships = $this->getApiOptimizedRelationships($modelName);
        
        if (!empty($apiRelationships)) {
            $query->with($apiRelationships);
        }

        return $query;
    }

    private function getApiOptimizedRelationships(string $modelName): array
    {
        $apiMaps = [
            'products' => [
                'brand:id,name,slug',
                'primaryCategory:id,name,slug',
                'media:id,product_id,type,path',
            ],
            'orders' => [
                'customer:id,name,email',
                'items:id,order_id,product_id,quantity,price',
            ],
            'customers' => [
                'addresses:id,customer_id,label,address_line1',
            ],
            'categories' => [
                'parent:id,name,slug',
                'children:id,parent_id,name,slug',
            ],
        ];

        return $apiMaps[$modelName] ?? [];
    }

    public function optimizeForAdminPanel(Builder $query): Builder
    {
        $model = $query->getModel();
        $modelName = $this->getModelName($model);
        
        // Admin-optimized relationships (comprehensive data)
        $adminRelationships = $this->getAdminOptimizedRelationships($modelName);
        
        if (!empty($adminRelationships)) {
            $query->with($adminRelationships);
        }

        return $query;
    }

    private function getAdminOptimizedRelationships(string $modelName): array
    {
        $adminMaps = [
            'products' => [
                'brand:id,name,slug,logo_path',
                'primaryCategory:id,name,slug,description',
                'media:id,product_id,type,path,alt_text',
                'reviews:id,product_id,rating,comment,created_at',
                'variants:id,product_id,name,sku,price,stock',
                'categories:id,name,slug',
            ],
            'orders' => [
                'customer:id,name,email,phone',
                'items:id,order_id,product_id,product_name,quantity,price',
                'items.product:id,name,slug,sku',
                'shipping:id,order_id,address,phone,notes',
                'payment:id,order_id,method,status,amount',
                'customer.addresses:id,customer_id,label,address_line1',
            ],
            'customers' => [
                'addresses:id,customer_id,label,address_line1,phone',
                'orders:id,customer_id,total,status,created_at',
                'orders.items:id,order_id,product_name,quantity',
                'loyaltyTier:id,name,benefits,min_points',
            ],
        ];

        return $adminMaps[$modelName] ?? [];
    }
}
