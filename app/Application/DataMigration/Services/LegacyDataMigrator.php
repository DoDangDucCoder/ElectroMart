<?php

namespace App\Application\DataMigration\Services;

use App\Infrastructure\Persistence\Eloquent\Catalog\Brand;
use App\Infrastructure\Persistence\Eloquent\Catalog\Product;
use App\Infrastructure\Persistence\Eloquent\Catalog\ProductCategory;
use App\Infrastructure\Persistence\Eloquent\Cms\BlogCategory;
use App\Infrastructure\Persistence\Eloquent\Cms\BlogPost;
use App\Infrastructure\Persistence\Eloquent\Customer\Address;
use App\Infrastructure\Persistence\Eloquent\Customer\Customer;
use App\Infrastructure\Persistence\Eloquent\Marketing\Coupon;
use App\Infrastructure\Persistence\Eloquent\Marketing\CouponRedemption;
use App\Infrastructure\Persistence\Eloquent\Order\Order;
use App\Infrastructure\Persistence\Eloquent\Order\OrderItem;
use App\Infrastructure\Persistence\Eloquent\Order\OrderShipping;
use App\Infrastructure\Persistence\Eloquent\Order\Payment;
use App\Infrastructure\Persistence\Eloquent\Catalog\ProductReview;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class LegacyDataMigrator
{
    protected array $brandMap = [];
    protected array $categoryMap = [];
    protected array $productMap = [];
    protected array $customerMap = [];
    protected array $addressCache = [];
    protected array $couponMap = [];
    protected array $orderMap = [];
    protected array $reviewMap = [];
    protected array $blogCategoryMap = [];
    protected array $blogPostMap = [];

    public function migrate(): void
    {
        DB::transaction(function () {
            $this->migrateTaxonomies();
            $this->migrateProducts();
            $this->migrateBlogPosts();
            $this->migrateCoupons();
            $this->migrateCustomers();
            $this->migrateProductReviews();
            $this->migrateOrders();
            $this->refreshCouponUsageCounts();
        });
    }

    protected function migrateTaxonomies(): void
    {
        $this->migrateBrands();
        $this->migrateCategories();
        $this->migrateBlogCategories();
    }

    protected function migrateBrands(): void
    {
        $legacyBrands = DB::table('tbl_brand')->orderBy('brand_id')->get();

        foreach ($legacyBrands as $legacy) {
            $brand = $this->upsertBrand($legacy);
            $this->brandMap[$legacy->brand_id] = $brand->id;
        }
    }

    protected function migrateCategories(): void
    {
        $legacyCategories = DB::table('tbl_category_product')->orderBy('category_id')->get();

        foreach ($legacyCategories as $legacy) {
            $category = $this->upsertCategory($legacy);
            $this->categoryMap[$legacy->category_id] = $category->id;
        }

        foreach ($legacyCategories as $legacy) {
            if (! $legacy->category_parent) {
                continue;
            }

            $childId = $this->categoryMap[$legacy->category_id] ?? null;
            $parentId = $this->categoryMap[$legacy->category_parent] ?? null;

            if ($childId && $parentId) {
                ProductCategory::whereKey($childId)->update(['parent_id' => $parentId]);
            }
        }
    }

    protected function migrateBlogCategories(): void
    {
        if (! DB::getSchemaBuilder()->hasTable('tbl_category_post')) {
            return;
        }

        $legacyCategories = DB::table('tbl_category_post')->orderBy('cate_post_id')->get();

        foreach ($legacyCategories as $legacy) {
            $category = $this->upsertBlogCategory($legacy);
            $this->blogCategoryMap[$legacy->cate_post_id] = $category->id;
        }
    }

    protected function migrateBlogPosts(): void
    {
        if (! DB::getSchemaBuilder()->hasTable('tbl_posts')) {
            return;
        }

        $legacyPosts = DB::table('tbl_posts')->orderBy('post_id')->get();

        foreach ($legacyPosts as $legacy) {
            $post = $this->upsertBlogPost($legacy);
            $this->blogPostMap[$legacy->post_id] = $post->id;
        }
    }

    protected function migrateProducts(): void
    {
        $legacyProducts = DB::table('tbl_product')->orderBy('product_id')->get();

        foreach ($legacyProducts as $legacy) {
            $product = $this->upsertProduct($legacy);
            $this->productMap[$legacy->product_id] = $product->id;
        }
    }

    protected function migrateCoupons(): void
    {
        $legacyCoupons = DB::table('tbl_coupon')->orderBy('coupon_id')->get();

        foreach ($legacyCoupons as $legacy) {
            $coupon = Coupon::firstOrNew(['code' => $legacy->coupon_code]);
            $coupon->name = $legacy->coupon_name ?: $legacy->coupon_code;
            $coupon->type = ((int) $legacy->coupon_condition === 1) ? 'percent' : 'fixed';
            $coupon->value = (float) ($legacy->coupon_number ?? 0);
            $coupon->max_discount = null;
            $coupon->min_order_total = null;
            $coupon->usage_limit = $legacy->coupon_time ?: null;
            $coupon->usage_per_customer = null;
            $coupon->used_count = 0;
            $coupon->starts_at = $this->parseDateTime($legacy->coupon_date_start);
            $coupon->expires_at = $this->parseDateTime($legacy->coupon_date_end);
            $coupon->is_active = ((int) $legacy->coupon_status) === 1;
            $coupon->applicable_segments = null;
            $coupon->meta = [
                'legacy_used' => $legacy->coupon_used,
                'legacy_condition' => $legacy->coupon_condition,
            ];
            $coupon->save();

            $this->couponMap[$coupon->code] = $coupon->id;
        }
    }

    protected function migrateCustomers(): void
    {
        $legacyCustomers = DB::table('tbl_customers')->orderBy('customer_id')->get();

        foreach ($legacyCustomers as $legacy) {
            $customer = $this->upsertCustomer($legacy);
            $this->customerMap[$legacy->customer_id] = $customer->id;
        }
    }

    protected function migrateProductReviews(): void
    {
        $legacyComments = DB::table('tbl_comment')->orderBy('comment_id')->get();

        foreach ($legacyComments as $legacy) {
            $review = $this->upsertReviewFromComment($legacy);
            if ($review) {
                $this->reviewMap[$legacy->comment_id] = $review->id;
            }
        }

        foreach ($legacyComments as $legacy) {
            $parentLegacyId = $legacy->comment_parent_comment ?? 0;
            if ($parentLegacyId > 0) {
                $childId = $this->reviewMap[$legacy->comment_id] ?? null;
                $parentId = $this->reviewMap[$parentLegacyId] ?? null;
                if ($childId && $parentId) {
                    ProductReview::whereKey($childId)->update(['parent_id' => $parentId]);
                }
            }
        }

        if (DB::getSchemaBuilder()->hasTable('tbl_rating')) {
            $legacyRatings = DB::table('tbl_rating')->orderBy('rating_id')->get();

            foreach ($legacyRatings as $legacy) {
                $this->applyLegacyRating($legacy);
            }
        }
    }

    protected function migrateOrders(): void
    {
        $legacyOrders = DB::table('tbl_order')->orderBy('order_id')->get();

        foreach ($legacyOrders as $legacy) {
            $order = $this->upsertOrder($legacy);
            $this->orderMap[$legacy->order_id] = $order->id;
        }
    }

    protected function upsertBrand(object $legacy): Brand
    {
        $brand = Brand::where('slug', $legacy->brand_slug)->first()
            ?? Brand::find($this->brandMap[$legacy->brand_id] ?? null)
            ?? new Brand();

        $brand->name = $legacy->brand_name ?: 'Legacy Brand '.$legacy->brand_id;
        $brand->slug = $this->generateUniqueSlug(
            $legacy->brand_slug ?: $legacy->brand_name ?: ('brand-'.$legacy->brand_id),
            'brands',
            $brand->exists ? $brand->id : null
        );
        $brand->description = $legacy->brand_desc;
        $brand->is_active = ((int) $legacy->brand_status) === 0;
        $brand->display_order = (int) ($legacy->brand_id ?? 0);
        $brand->meta = null;
        $brand->save();

        return $brand;
    }

    protected function upsertCategory(object $legacy): ProductCategory
    {
        $category = ProductCategory::where('slug', $legacy->slug_category_product)->first()
            ?? ProductCategory::find($this->categoryMap[$legacy->category_id] ?? null)
            ?? new ProductCategory();

        $category->name = $legacy->category_name ?: 'Legacy Category '.$legacy->category_id;
        $category->slug = $this->generateUniqueSlug(
            $legacy->slug_category_product ?: $legacy->category_name ?: ('category-'.$legacy->category_id),
            'product_categories',
            $category->exists ? $category->id : null
        );
        $category->code = null;
        $category->description = $legacy->category_desc;
        $category->thumbnail_path = null;
        $category->is_active = ((int) $legacy->category_status) === 0;
        $category->display_order = (int) ($legacy->category_order ?? 0);
        $category->meta_title = $legacy->category_name;
        $category->meta_description = $legacy->category_desc;
        $category->meta = [
            'meta_keywords' => $legacy->meta_keywords ?? null,
        ];
        $category->save();

        return $category;
    }

    protected function upsertBlogCategory(object $legacy): BlogCategory
    {
        $category = BlogCategory::where('slug', $legacy->cate_post_slug)->first()
            ?? BlogCategory::find($this->blogCategoryMap[$legacy->cate_post_id] ?? null)
            ?? new BlogCategory();

        $category->name = $legacy->cate_post_name ?: 'Legacy Blog Category '.$legacy->cate_post_id;
        $category->slug = $this->generateUniqueSlug(
            $legacy->cate_post_slug ?: $legacy->cate_post_name ?: ('blog-category-'.$legacy->cate_post_id),
            'blog_categories',
            $category->exists ? $category->id : null
        );
        $category->description = $legacy->cate_post_desc;
        $category->is_active = ((int) $legacy->cate_post_status) === 0;
        $category->display_order = (int) ($legacy->cate_post_id ?? 0);
        $category->save();

        return $category;
    }

    protected function upsertBlogPost(object $legacy): BlogPost
    {
        $post = BlogPost::where('slug', $legacy->post_slug)->first()
            ?? BlogPost::find($this->blogPostMap[$legacy->post_id] ?? null)
            ?? new BlogPost();

        $title = $legacy->post_title ?: 'Legacy Blog Post '.$legacy->post_id;
        $slugBase = $legacy->post_slug ?: $title ?: ('blog-post-'.$legacy->post_id);

        $post->blog_category_id = $this->blogCategoryMap[$legacy->cate_post_id] ?? null;
        $post->title = $title;
        $post->slug = $this->generateUniqueSlug(
            $slugBase,
            'blog_posts',
            $post->exists ? $post->id : null
        );
        $post->excerpt = $legacy->post_desc;
        $post->content = $legacy->post_content;
        if (! empty($legacy->post_image)) {
            $post->featured_image_path = 'uploads/post/'.$legacy->post_image;
        }
        $post->status = ((int) $legacy->post_status) === 0 ? 'published' : 'draft';
        if ($post->status === 'published' && ! $post->published_at) {
            $post->published_at = now();
        }
        $post->views_count = (int) ($legacy->post_views ?? $post->views_count ?? 0);
        $post->meta_title = $legacy->post_title ?: $post->meta_title;
        $post->meta_description = $legacy->post_meta_desc ?: $legacy->post_desc ?: $post->meta_description;

        $meta = $post->meta ?? [];
        if (! empty($legacy->post_meta_keywords)) {
            $meta['keywords'] = $legacy->post_meta_keywords;
        }
        $meta['legacy_post_id'] = $legacy->post_id;
        $post->meta = $meta;

        $post->save();

        return $post;
    }

    protected function upsertProduct(object $legacy): Product
    {
        $product = Product::where('slug', $legacy->product_slug)->first()
            ?? Product::find($this->productMap[$legacy->product_id] ?? null)
            ?? new Product();

        $brandId = isset($legacy->brand_id) ? ($this->brandMap[$legacy->brand_id] ?? null) : null;
        $categoryId = isset($legacy->category_id) ? ($this->categoryMap[$legacy->category_id] ?? null) : null;

        $product->brand_id = $brandId;
        $product->primary_category_id = $categoryId;
        $product->name = $legacy->product_name ?: 'Legacy Product '.$legacy->product_id;
        $product->slug = $this->generateUniqueSlug(
            $legacy->product_slug ?: $legacy->product_name ?: ('product-'.$legacy->product_id),
            'products',
            $product->exists ? $product->id : null
        );
        $product->sku = $legacy->product_code ?? null;
        $product->barcode = null;
        $product->unit = null;
        $product->price = (float) ($legacy->product_price ?? 0);
        $product->sale_price = isset($legacy->price_cost) ? (float) $legacy->price_cost : null;
        $product->cost_price = isset($legacy->price_cost) ? (float) $legacy->price_cost : null;
        $product->stock_quantity = (int) ($legacy->product_quantity ?? 0);
        $product->stock_alert_threshold = 0;
        $product->is_active = ((int) $legacy->product_status) === 0;
        $product->is_featured = false;
        $product->display_order = (int) ($legacy->product_id ?? 0);
        $product->thumbnail_path = $legacy->product_image ? 'uploads/product/'.$legacy->product_image : null;
        $product->description = $legacy->product_desc;
        $product->content = $legacy->product_content;
        $product->views_count = (int) ($legacy->product_views ?? 0);
        $product->meta = [
            'tags' => $legacy->product_tags ?? null,
        ];
        $product->meta_title = $legacy->product_name;
        $product->meta_description = $legacy->product_desc;
        $product->save();

        if ($categoryId) {
            $product->categories()->syncWithoutDetaching([$categoryId]);
        }

        $this->syncProductImage($product, $legacy);

        return $product;
    }

    protected function upsertCustomer(object $legacy): Customer
    {
        $email = $legacy->customer_email ?: 'legacy-'.$legacy->customer_id.'@example.test';

        $customer =
            Customer::where('email', $email)->first()
            ?? Customer::find($this->customerMap[$legacy->customer_id] ?? null)
            ?? new Customer();

        $customer->name = $legacy->customer_name ?: 'Legacy Customer '.$legacy->customer_id;
        $customer->email = $email;

        $phone = $legacy->customer_phone ? trim($legacy->customer_phone) : null;
        if ($phone) {
            $duplicateQuery = Customer::where('phone', $phone);
            if ($customer->exists) {
                $duplicateQuery->where('id', '!=', $customer->id);
            }
            if ($duplicateQuery->exists()) {
                $phone = null;
            }
        }
        $customer->phone = $phone;
        $customer->password = Hash::make($legacy->customer_password ?: Str::random(12));
        $customer->avatar_path = null;
        $customer->is_active = true;
        $customer->meta = [
            'legacy_id' => $legacy->customer_id,
            'legacy_password_hash' => $legacy->customer_password ?? null,
            'legacy_vip' => $legacy->customer_vip ?? null,
            'legacy_token' => $legacy->customer_token ?? null,
        ];
        $customer->save();

        return $customer;
    }

    protected function upsertOrder(object $legacy): Order
    {
        $order = Order::firstOrNew([
            'code' => $legacy->order_code ?: ('ORDER-'.$legacy->order_id),
        ]);

        $customerId = isset($legacy->customer_id) ? ($this->customerMap[$legacy->customer_id] ?? null) : null;
        $customer = $customerId ? Customer::find($customerId) : null;

        $shippingRecord = $legacy->shipping_id ? DB::table('tbl_shipping')->where('shipping_id', $legacy->shipping_id)->first() : null;
        $address = $this->getOrCreateAddress($shippingRecord, $customer);

        $details = DB::table('tbl_order_details')->where('order_code', $legacy->order_code)->get();
        $firstDetail = $details->first();
        $subtotal = $details->sum(function ($detail) {
            return (float) ($detail->product_price ?? 0) * (int) ($detail->product_sales_quantity ?? 0);
        });
        $shippingFee = (float) ($firstDetail->product_feeship ?? 0);
        $total = (float) ($legacy->order_total ?? ($subtotal + $shippingFee));

        $couponCode = $this->normalizeCouponCode($firstDetail->product_coupon ?? null);
        $discount = max(0.0, ($subtotal + $shippingFee) - $total);

        $order->fill([
            'customer_id' => $customer?->id,
            'customer_address_id' => $address?->id,
            'status' => $this->mapOrderStatus($legacy->order_status ?? null),
            'payment_status' => 'pending',
            'fulfillment_status' => $this->mapFulfillmentStatus($legacy->order_status ?? null),
            'currency' => 'VND',
            'subtotal' => $subtotal,
            'discount_total' => $discount,
            'shipping_total' => $shippingFee,
            'tax_total' => 0,
            'total' => $total,
            'coupon_code' => $couponCode,
            'coupon_discount' => $discount,
            'source' => 'legacy',
            'note' => $shippingRecord->shipping_notes ?? null,
            'placed_at' => $this->parseDateTime($legacy->created_at ?? $legacy->order_date),
            'confirmed_at' => $this->parseDateTime($legacy->updated_at ?? null),
            'shipped_at' => null,
            'completed_at' => $this->mapCompletionDate($legacy),
            'cancelled_at' => $this->mapCancelledDate($legacy),
        ]);
        $order->save();

        $this->orderMap[$legacy->order_id] = $order->id;

        $order->items()->delete();
        foreach ($details as $detail) {
            $productId = $this->productMap[$detail->product_id] ?? null;
            $quantity = (int) ($detail->product_sales_quantity ?? 0);
            $price = (float) ($detail->product_price ?? 0);

            $order->items()->create([
                'product_id' => $productId,
                'product_variant_id' => null,
                'product_name' => $detail->product_name ?? 'Unknown product',
                'sku' => null,
                'attributes' => null,
                'price' => $price,
                'sale_price' => null,
                'discount_amount' => 0,
                'quantity' => $quantity,
                'subtotal' => $price * $quantity,
                'tax_amount' => 0,
            ]);
        }

        $order->shipping()->delete();
        if ($shippingRecord) {
            $order->shipping()->create([
                'recipient_name' => $shippingRecord->shipping_name ?? $customer?->name ?? 'Legacy',
                'recipient_phone' => $shippingRecord->shipping_phone ?? $customer?->phone,
                'email' => $shippingRecord->shipping_email ?? $customer?->email,
                'country' => 'Vietnam',
                'city_id' => null,
                'province_id' => null,
                'ward_id' => null,
                'postal_code' => null,
                'address_line1' => $shippingRecord->shipping_address ?? '',
                'address_line2' => $shippingRecord->shipping_notes ?? null,
                'shipping_method' => $shippingRecord->shipping_method ?? null,
                'tracking_number' => null,
                'shipping_fee' => $shippingFee,
                'insurance_fee' => 0,
                'meta' => [
                    'legacy_shipping_id' => $shippingRecord->shipping_id,
                ],
            ]);
        }

        $order->payments()->delete();
        $this->createPaymentFromLegacy($order, $legacy);

        if ($couponCode && isset($this->couponMap[$couponCode])) {
            CouponRedemption::updateOrCreate(
                [
                    'coupon_id' => $this->couponMap[$couponCode],
                    'order_id' => $order->id,
                ],
                [
                    'customer_id' => $customer?->id,
                    'discount_amount' => $discount,
                ]
            );
        }

        return $order;
    }

    protected function createPaymentFromLegacy(Order $order, object $legacy): void
    {
        $legacyPayment = isset($legacy->payment_id)
            ? DB::table('tbl_payment')->where('payment_id', $legacy->payment_id)->first()
            : null;

        $payment = new Payment([
            'order_id' => $order->id,
            'provider' => $this->mapPaymentProvider($legacyPayment->payment_method ?? null),
            'reference' => null,
            'status' => $this->mapPaymentStatus($legacyPayment->payment_status ?? null),
            'currency' => 'VND',
            'amount' => $order->total,
            'fee' => 0,
            'paid_at' => $this->parseDateTime($legacyPayment->updated_at ?? null),
            'meta' => [
                'legacy_payment_id' => $legacyPayment->payment_id ?? null,
                'legacy_payment_status' => $legacyPayment->payment_status ?? null,
            ],
        ]);

        $payment->save();

        $payment->transactions()->create([
            'event' => 'legacy_import',
            'status' => $payment->status,
            'reference' => null,
            'payload' => [
                'legacy_payment' => $legacyPayment,
            ],
            'ip_address' => null,
        ]);
    }

    protected function getOrCreateAddress(?object $shipping, ?Customer $customer): ?Address
    {
        if (! $shipping) {
            return null;
        }

        $hash = md5(implode('|', [
            $customer?->id,
            $shipping->shipping_name ?? '',
            $shipping->shipping_phone ?? '',
            $shipping->shipping_email ?? '',
            $shipping->shipping_address ?? '',
            $shipping->shipping_method ?? '',
        ]));

        if (isset($this->addressCache[$hash])) {
            return Address::find($this->addressCache[$hash]);
        }

        $address = Address::firstOrCreate(
            [
                'customer_id' => $customer?->id,
                'address_line1' => $shipping->shipping_address ?? '',
                'recipient_name' => $shipping->shipping_name ?? $customer?->name ?? 'Legacy',
                'recipient_phone' => $shipping->shipping_phone ?? $customer?->phone,
            ],
            [
                'label' => 'Legacy',
                'email' => $shipping->shipping_email ?? $customer?->email,
                'country' => 'Vietnam',
                'location_city_id' => null,
                'location_province_id' => null,
                'location_ward_id' => null,
                'postal_code' => null,
                'address_line2' => $shipping->shipping_notes ?? null,
                'is_default' => ! Address::where('customer_id', $customer?->id)->exists(),
                'meta' => [
                    'legacy_shipping_id' => $shipping->shipping_id ?? null,
                ],
            ]
        );

        $this->addressCache[$hash] = $address->id;

        return $address;
    }

    protected function upsertReviewFromComment(object $legacy): ?ProductReview
    {
        $productId = $this->productMap[$legacy->comment_product_id] ?? null;
        if (! $productId) {
            return null;
        }

        $review = ProductReview::where('meta->legacy_comment_id', $legacy->comment_id)->first()
            ?? new ProductReview();

        $customerId = isset($legacy->customer_id) ? ($this->customerMap[$legacy->customer_id] ?? null) : null;
        $customer = $customerId ? Customer::find($customerId) : null;

        $status = $this->mapReviewStatus($legacy->comment_status ?? null);

        $review->product_id = $productId;
        $review->customer_id = $customer?->id;
        $review->author_name = $legacy->comment_name ?? $customer?->name ?? 'Anonymous';
        $review->author_email = $legacy->comment_email ?? $customer?->email;
        $review->rating = $review->rating ?? null;
        $review->title = $review->title ?? null;
        $review->content = $legacy->comment ?? $review->content ?? '';
        $review->status = $status;
        $review->is_public = $status === 'approved';
        $review->approved_at = $review->is_public
            ? ($review->approved_at ?? $this->parseDateTime($legacy->comment_date ?? null))
            : null;
        $review->meta = array_merge($review->meta ?? [], [
            'legacy_comment_id' => $legacy->comment_id,
            'legacy_parent_id' => $legacy->comment_parent_comment ?? null,
        ]);
        $review->save();

        return $review;
    }

    protected function applyLegacyRating(object $legacy): void
    {
        $productId = $this->productMap[$legacy->product_id] ?? null;
        if (! $productId) {
            return;
        }

        $customerId = isset($legacy->customer_id) ? ($this->customerMap[$legacy->customer_id] ?? null) : null;

        $review = ProductReview::where('meta->legacy_rating_id', $legacy->rating_id)->first();

        if (! $review && $customerId) {
            $review = ProductReview::where('product_id', $productId)
                ->where('customer_id', $customerId)
                ->orderByDesc('created_at')
                ->first();
        }

        if (! $review) {
            $review = new ProductReview([
                'product_id' => $productId,
                'customer_id' => $customerId,
                'author_name' => null,
                'author_email' => null,
                'title' => null,
                'content' => '',
                'status' => 'approved',
                'is_public' => true,
                'approved_at' => $this->parseDateTime($legacy->created_at ?? null),
            ]);
        }

        $review->rating = (int) ($legacy->rating ?? 0);
        $review->meta = array_merge($review->meta ?? [], [
            'legacy_rating_id' => $legacy->rating_id,
        ]);
        $review->save();
    }

    protected function mapReviewStatus(?int $status): string
    {
        return match ($status) {
            0 => 'approved',
            1 => 'pending',
            2 => 'rejected',
            default => 'pending',
        };
    }

    protected function syncProductImage(Product $product, object $legacy): void
    {
        if (! $legacy->product_image) {
            return;
        }

        $product->media()->firstOrCreate(
            ['file_path' => 'uploads/product/'.$legacy->product_image],
            [
                'disk' => 'public',
                'collection' => 'gallery',
                'display_order' => 0,
                'meta' => null,
            ]
        );
    }

    protected function mapOrderStatus(?int $status): string
    {
        return match ($status) {
            3 => 'cancelled',
            2 => 'completed',
            1 => 'confirmed',
            0 => 'pending',
            default => 'pending',
        };
    }

    protected function mapFulfillmentStatus(?int $status): string
    {
        return match ($status) {
            2 => 'fulfilled',
            default => 'unfulfilled',
        };
    }

    protected function mapPaymentProvider($method): string
    {
        return match ((int) $method) {
            1 => 'bank_transfer',
            2 => 'cod',
            3 => 'momo',
            default => 'manual',
        };
    }

    protected function mapPaymentStatus(?string $status): string
    {
        if (! $status) {
            return 'pending';
        }

        $statusLower = Str::lower($status);

        return match (true) {
            str_contains($statusLower, 'thành') || str_contains($statusLower, 'success') => 'succeeded',
            str_contains($statusLower, 'hoàn') || str_contains($statusLower, 'refund') => 'refunded',
            str_contains($statusLower, 'hủy') || str_contains($statusLower, 'fail') => 'failed',
            default => 'pending',
        };
    }

    protected function normalizeCouponCode(?string $code): ?string
    {
        if (! $code) {
            return null;
        }

        $normalized = Str::lower(trim($code));

        if (in_array($normalized, ['0', 'no', 'khong', 'không', 'none', 'null'])) {
            return null;
        }

        return Str::upper(trim($code));
    }

    protected function parseDateTime($value): ?Carbon
    {
        if (empty($value)) {
            return null;
        }

        try {
            return Carbon::parse($value);
        } catch (\Throwable $e) {
            Log::warning('Unable to parse datetime', ['value' => $value]);

            return null;
        }
    }

    protected function mapCompletionDate(object $legacy): ?Carbon
    {
        if (($legacy->order_status ?? null) === 2) {
            return $this->parseDateTime($legacy->updated_at ?? $legacy->order_date ?? null);
        }

        return null;
    }

    protected function mapCancelledDate(object $legacy): ?Carbon
    {
        if (($legacy->order_status ?? null) === 3) {
            return $this->parseDateTime($legacy->updated_at ?? null);
        }

        return null;
    }

    protected function refreshCouponUsageCounts(): void
    {
        Coupon::query()->each(function (Coupon $coupon) {
            $coupon->used_count = $coupon->redemptions()->count();
            $coupon->save();
        });
    }

    protected function generateUniqueSlug(string $value, string $table, ?int $ignoreId = null): string
    {
        $baseSlug = Str::slug($value) ?: Str::slug(Str::random(8));
        $slug = $baseSlug;
        $counter = 1;

        while (
            DB::table($table)
                ->when($ignoreId, fn ($query) => $query->where('id', '!=', $ignoreId))
                ->where('slug', $slug)
                ->exists()
        ) {
            $slug = $baseSlug.'-'.$counter;
            $counter++;
        }

        return $slug;
    }
}




