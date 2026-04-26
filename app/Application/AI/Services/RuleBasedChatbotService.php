<?php

namespace App\Application\AI\Services;

use App\Application\Catalog\Services\CategoryService;
use App\Application\Catalog\Services\ProductService;
use App\Application\Shared\Services\NumericInputNormalizer;
use App\Infrastructure\Persistence\Eloquent\Catalog\Product;
use App\Support\AssetUrl;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class RuleBasedChatbotService
{
    public function __construct(
        private readonly ProductService $productService,
        private readonly CategoryService $categoryService,
        private readonly NumericInputNormalizer $numbers
    ) {
    }

    public function handle(string $message, ?int $customerId = null, ?string $ipAddress = null): ?array
    {
        $normalized = $this->normalize($message);

        if ($normalized === '') {
            return $this->fallback($customerId, $ipAddress);
        }

        if ($faq = $this->matchFaq($normalized)) {
            return $faq;
        }

        if ($this->isOrderIntent($normalized)) {
            return $this->orderHelpReply($customerId);
        }

        if ($this->isRecommendationIntent($normalized)) {
            return $this->recommendationReply($customerId, $ipAddress);
        }

        if ($searchReply = $this->productSearchReply($message, $normalized)) {
            return $searchReply;
        }

        return null;
    }

    public function fallback(?int $customerId = null, ?string $ipAddress = null): array
    {
        $recommendationData = $this->productService->getRecommendedProducts(
            $customerId,
            $ipAddress,
            null,
            3,
            'chatbot_fallback'
        );

        return [
            'intent' => 'fallback',
            'response_type' => 'fallback',
            'message' => (string) config('chatbot.fallback_message'),
            'products' => $this->serializeProducts($recommendationData['products'] ?? collect()),
            'actions' => $this->actionsFromConfig(config('chatbot.welcome_actions', [])),
            'source' => 'rule_based',
        ];
    }

    protected function matchFaq(string $normalizedMessage): ?array
    {
        foreach ((array) config('chatbot.faq', []) as $faq) {
            $keywords = collect((array) ($faq['keywords'] ?? []))
                ->map(fn ($keyword) => $this->normalize((string) $keyword))
                ->filter()
                ->all();

            if ($keywords !== [] && Str::contains($normalizedMessage, $keywords)) {
                return [
                    'intent' => (string) ($faq['intent'] ?? 'faq'),
                    'response_type' => 'faq',
                    'message' => (string) ($faq['message'] ?? ''),
                    'products' => [],
                    'actions' => $this->actionsFromConfig((array) ($faq['actions'] ?? [])),
                    'source' => 'rule_based',
                ];
            }
        }

        return null;
    }

    protected function productSearchReply(string $message, string $normalizedMessage): ?array
    {
        $filters = $this->extractSearchFilters($message, $normalizedMessage);
        $paginator = $this->productService->searchCatalog($filters, 4);
        $products = collect($paginator->items());

        if (
            $products->isEmpty()
            && ! empty($filters['keyword'])
            && (
                ! empty($filters['category_id'])
                || ! empty($filters['product_code'])
                || ! empty($filters['price_min'])
                || ! empty($filters['price_max'])
            )
        ) {
            $retryFilters = $filters;
            unset($retryFilters['keyword']);
            $paginator = $this->productService->searchCatalog($retryFilters, 4);
            $products = collect($paginator->items());
            $filters = $retryFilters;
        }

        if ($products->isEmpty()) {
            return null;
        }

        return [
            'intent' => 'product_search',
            'response_type' => 'product_list',
            'message' => $this->buildSearchSummary($filters, (int) $paginator->total()),
            'products' => $this->serializeProducts($products),
            'actions' => [
                [
                    'label' => 'Mở trang kết quả',
                    'url' => url('/tim-kiem?' . http_build_query(array_filter([
                        'keywords_submit' => $filters['keyword'] ?? null,
                        'product_code' => $filters['product_code'] ?? null,
                        'category_id' => $filters['category_id'] ?? null,
                        'price_min' => $filters['price_min'] ?? null,
                        'price_max' => $filters['price_max'] ?? null,
                    ]))),
                ],
                [
                    'label' => 'Xem gợi ý thêm',
                    'url' => url('/goi-y-san-pham'),
                ],
            ],
            'source' => 'rule_based',
        ];
    }

    protected function recommendationReply(?int $customerId, ?string $ipAddress): array
    {
        $recommendationData = $this->productService->getRecommendedProducts(
            $customerId,
            $ipAddress,
            null,
            4,
            'chatbot_widget'
        );

        $products = $this->serializeProducts($recommendationData['products'] ?? collect());
        $description = trim((string) ($recommendationData['description'] ?? ''));

        if ($products === []) {
            return $this->fallback($customerId, $ipAddress);
        }

        return [
            'intent' => 'product_recommendation',
            'response_type' => 'product_list',
            'message' => $description !== ''
                ? $description . ' Dưới đây là một vài sản phẩm phù hợp để bạn xem tiếp.'
                : 'Dưới đây là một vài sản phẩm phù hợp để bạn xem tiếp.',
            'products' => $products,
            'actions' => [
                [
                    'label' => 'Xem trang gợi ý',
                    'url' => url('/goi-y-san-pham'),
                ],
            ],
            'source' => 'rule_based',
        ];
    }

    protected function orderHelpReply(?int $customerId): array
    {
        if ($customerId && $customerId > 0) {
            return [
                'intent' => 'order_support',
                'response_type' => 'faq',
                'message' => 'Bạn có thể mở trang lịch sử đơn hàng để tra cứu trạng thái, xem chi tiết hoặc hủy đơn nếu đơn vẫn đang chờ xử lý.',
                'products' => [],
                'actions' => [
                    ['label' => 'Lịch sử đơn hàng', 'url' => url('/history')],
                ],
                'source' => 'rule_based',
            ];
        }

        return [
            'intent' => 'order_support',
            'response_type' => 'faq',
            'message' => 'Để tra cứu đơn hàng, bạn cần đăng nhập tài khoản khách hàng trước.',
            'products' => [],
            'actions' => [
                ['label' => 'Đăng nhập', 'url' => url('/dang-nhap')],
            ],
            'source' => 'rule_based',
        ];
    }

    protected function extractSearchFilters(string $message, string $normalizedMessage): array
    {
        $filters = [
            'keyword' => $this->extractKeyword($message),
            'sort' => 'popular',
        ];

        if ($productCode = $this->extractProductCode($message)) {
            $filters['product_code'] = $productCode;
        }

        if ($categoryId = $this->detectCategoryId($normalizedMessage)) {
            $filters['category_id'] = $categoryId;
        }

        $priceRange = $this->extractPriceRange($normalizedMessage);

        if ($priceRange['min'] !== null) {
            $filters['price_min'] = $priceRange['min'];
        }

        if ($priceRange['max'] !== null) {
            $filters['price_max'] = $priceRange['max'];
        }

        return $filters;
    }

    protected function extractKeyword(string $message): ?string
    {
        $cleaned = preg_replace(
            '/\b(tìm|tim|kiếm|kiem|sản phẩm|san pham|gợi ý|goi y|cho tôi|cho toi|giúp tôi|giup toi|tôi muốn|toi muon|tôi cần|toi can|cần mua|can mua|muốn mua|muon mua|shop có|shop co|có không|co khong|tư vấn|tu van|dưới|duoi|trên|tren|từ|tu|đến|den)\b/iu',
            ' ',
            $message
        );

        $cleaned = preg_replace('/[0-9\.,]+\s*(triệu|trieu|k|nghìn|nghin|ngàn|ngan|đ|dong)?/iu', ' ', (string) $cleaned);
        $cleaned = preg_replace('/\s+/', ' ', (string) $cleaned);
        $cleaned = trim($cleaned);

        return $cleaned !== '' ? $cleaned : trim($message);
    }

    protected function extractProductCode(string $message): ?string
    {
        if (preg_match('/\b([A-Z0-9]{2,}(?:-[A-Z0-9]{2,})+)\b/i', $message, $matches)) {
            return strtoupper((string) $matches[1]);
        }

        return null;
    }

    protected function detectCategoryId(string $normalizedMessage): ?int
    {
        foreach ($this->categoryService->listActive() as $category) {
            $keywords = array_filter([
                $this->normalize((string) ($category->name ?? $category->category_name ?? '')),
                $this->normalize((string) ($category->slug ?? $category->slug_category_product ?? '')),
            ]);

            if ($keywords !== [] && Str::contains($normalizedMessage, $keywords)) {
                return (int) ($category->id ?? $category->category_id);
            }
        }

        return null;
    }

    protected function extractPriceRange(string $normalizedMessage): array
    {
        $range = ['min' => null, 'max' => null];

        if (preg_match('/tu\s+([0-9\.,]+)\s*(trieu|k|nghin|ngan|dong)?\s+(?:den|-)\s+([0-9\.,]+)\s*(trieu|k|nghin|ngan|dong)?/u', $normalizedMessage, $matches)) {
            $range['min'] = $this->normalizeMoneyValue($matches[1], $matches[2] ?? null);
            $range['max'] = $this->normalizeMoneyValue($matches[3], $matches[4] ?? $matches[2] ?? null);

            return $range;
        }

        if (preg_match('/(duoi|nho hon)\s+([0-9\.,]+)\s*(trieu|k|nghin|ngan|dong)?/u', $normalizedMessage, $matches)) {
            $range['max'] = $this->normalizeMoneyValue($matches[2], $matches[3] ?? null);

            return $range;
        }

        if (preg_match('/(tren|lon hon|tu)\s+([0-9\.,]+)\s*(trieu|k|nghin|ngan|dong)?/u', $normalizedMessage, $matches)) {
            $range['min'] = $this->normalizeMoneyValue($matches[2], $matches[3] ?? null);
        }

        return $range;
    }

    protected function normalizeMoneyValue(string $rawValue, ?string $unit = null): int
    {
        $normalized = preg_replace('/[^0-9,\.]/', '', $rawValue);
        $normalized = str_replace(',', '.', (string) $normalized);

        if (substr_count($normalized, '.') > 1) {
            $normalized = str_replace('.', '', $normalized);
        }

        $value = (float) ($normalized ?: 0);
        $unit = trim((string) $unit);

        return match ($unit) {
            'trieu' => (int) round($value * 1000000),
            'k', 'nghin', 'ngan' => (int) round($value * 1000),
            default => $this->numbers->digitsOnly((string) $rawValue),
        };
    }

    protected function buildSearchSummary(array $filters, int $total): string
    {
        $parts = [];

        if (! empty($filters['keyword'])) {
            $parts[] = 'từ khóa "' . trim((string) $filters['keyword']) . '"';
        }

        if (! empty($filters['product_code'])) {
            $parts[] = 'mã sản phẩm ' . (string) $filters['product_code'];
        }

        if (! empty($filters['category_id'])) {
            $category = $this->categoryService->listActive()->firstWhere('id', (int) $filters['category_id']);

            if ($category) {
                $parts[] = 'danh mục ' . ($category->name ?? $category->category_name);
            }
        }

        if (! empty($filters['price_min']) || ! empty($filters['price_max'])) {
            $min = ! empty($filters['price_min']) ? number_format((float) $filters['price_min'], 0, ',', '.') . 'đ' : null;
            $max = ! empty($filters['price_max']) ? number_format((float) $filters['price_max'], 0, ',', '.') . 'đ' : null;

            if ($min && $max) {
                $parts[] = "mức giá từ {$min} đến {$max}";
            } elseif ($min) {
                $parts[] = "mức giá từ {$min} trở lên";
            } elseif ($max) {
                $parts[] = "mức giá dưới {$max}";
            }
        }

        $criteria = $parts !== [] ? implode(', ', $parts) : 'yêu cầu của bạn';

        return "Tôi tìm thấy {$total} sản phẩm phù hợp với {$criteria}. Dưới đây là các gợi ý gần nhất:";
    }

    protected function serializeProducts(Collection $products): array
    {
        return $products
            ->filter(fn ($product) => $product instanceof Product)
            ->map(function (Product $product) {
                return [
                    'id' => $product->product_id,
                    'name' => $product->product_name,
                    'url' => url('/chi-tiet/' . $product->product_slug),
                    'image' => AssetUrl::product($product->product_image),
                    'price' => (float) $product->final_price,
                    'price_formatted' => number_format((float) $product->final_price, 0, ',', '.') . 'đ',
                    'original_price_formatted' => $product->sale_amount > 0
                        ? number_format((float) $product->price, 0, ',', '.') . 'đ'
                        : null,
                    'sale_label' => $this->saleLabel($product),
                    'sale_explanation' => $product->sale_explanation,
                ];
            })
            ->values()
            ->all();
    }

    protected function saleLabel(Product $product): ?string
    {
        if ($product->sale_amount <= 0) {
            return null;
        }

        if ($product->sale_type === 'percent') {
            return '-' . rtrim(rtrim(number_format((float) $product->sale_value, 2, '.', ''), '0'), '.') . '%';
        }

        return '-' . number_format((float) $product->sale_value, 0, ',', '.') . 'đ';
    }

    protected function actionsFromConfig(array $actions): array
    {
        return collect($actions)
            ->map(function (array $action) {
                $path = (string) ($action['path'] ?? '#');

                return [
                    'label' => (string) ($action['label'] ?? 'Xem thêm'),
                    'url' => str_starts_with($path, 'http') ? $path : url($path),
                ];
            })
            ->values()
            ->all();
    }

    protected function isRecommendationIntent(string $normalizedMessage): bool
    {
        return Str::contains($normalizedMessage, [
            'goi y',
            'de xuat',
            'nen mua',
            'tu van san pham',
        ]);
    }

    protected function isOrderIntent(string $normalizedMessage): bool
    {
        return Str::contains($normalizedMessage, [
            'don hang',
            'lich su mua',
            'tra cuu don',
            'huy don',
            'trang thai don',
        ]);
    }

    protected function normalize(string $value): string
    {
        $ascii = Str::ascii(mb_strtolower(trim($value)));

        return trim(preg_replace('/\s+/', ' ', $ascii));
    }
}
