<?php

namespace App\Application\DataMigration\Services;

use App\Infrastructure\Persistence\Eloquent\Catalog\Attribute;
use App\Infrastructure\Persistence\Eloquent\Catalog\AttributeGroup;
use App\Infrastructure\Persistence\Eloquent\Catalog\AttributeValue;
use App\Infrastructure\Persistence\Eloquent\Catalog\Brand;
use App\Infrastructure\Persistence\Eloquent\Catalog\Product;
use App\Infrastructure\Persistence\Eloquent\Catalog\ProductCategory;
use App\Infrastructure\Persistence\Eloquent\Catalog\ProductMedia;
use App\Infrastructure\Persistence\Eloquent\Catalog\ProductReview;
use App\Infrastructure\Persistence\Eloquent\Catalog\ProductVariant;
use App\Infrastructure\Persistence\Eloquent\Catalog\ProductVariantAttributeValue;
use App\Infrastructure\Persistence\Eloquent\Cms\Banner;
use App\Infrastructure\Persistence\Eloquent\Cms\BlogCategory;
use App\Infrastructure\Persistence\Eloquent\Cms\BlogPost;
use App\Infrastructure\Persistence\Eloquent\Cms\SiteContact;
use App\Infrastructure\Persistence\Eloquent\Cms\SiteLink;
use App\Infrastructure\Persistence\Eloquent\Cms\Video;
use App\Infrastructure\Persistence\Eloquent\Customer\Address;
use App\Infrastructure\Persistence\Eloquent\Customer\Customer;
use App\Infrastructure\Persistence\Eloquent\Geo\Location;
use App\Infrastructure\Persistence\Eloquent\Marketing\Coupon;
use App\Infrastructure\Persistence\Eloquent\Marketing\CouponRedemption;
use App\Infrastructure\Persistence\Eloquent\Order\Order;
use App\Infrastructure\Persistence\Eloquent\Order\Payment;
use App\Infrastructure\Persistence\Eloquent\Shipping\ShippingFee;
use App\Support\StorefrontCustomerPassword;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class LegacyDataMigrator
{
    protected array $brandMap = [];
    protected array $categoryMap = [];
    protected array $attributeMap = [];
    protected array $attributeValueMap = [];
    protected array $productMap = [];
    protected array $productVariantMap = [];
    protected array $customerMap = [];
    protected array $addressCache = [];
    protected array $couponMap = [];
    protected array $orderMap = [];
    protected array $reviewMap = [];
    protected array $blogCategoryMap = [];
    protected array $blogPostMap = [];
    protected array $cityMap = [];
    protected array $provinceMap = [];
    protected array $wardMap = [];

    public function migrate(bool $confirmed = false): void
    {
        if (! $confirmed) {
            throw new \LogicException('Legacy data migration requires explicit confirmation.');
        }

        DB::transaction(function () {
            $this->migrateTaxonomies();
            $this->migrateAttributes();
            $this->migrateLocations();
            $this->migrateProducts();
            $this->migrateProductVariants();
            $this->migrateProductGallery();
            $this->migrateCms();
            $this->migrateBlogPosts();
            $this->migrateCoupons();
            $this->migrateCustomers();
            $this->migrateProductReviews();
            $this->migrateShippingFees();
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
        if (! $this->hasLegacyTable('tbl_brand')) {
            return;
        }

        DB::table('tbl_brand')->orderBy('brand_id')->get()->each(function (object $legacy): void {
            $brand = $this->upsertBrand($legacy);
            $this->brandMap[(int) $legacy->brand_id] = $brand->id;
        });
    }

    protected function migrateCategories(): void
    {
        if (! $this->hasLegacyTable('tbl_category_product')) {
            return;
        }

        $legacyCategories = DB::table('tbl_category_product')->orderBy('category_id')->get();

        foreach ($legacyCategories as $legacy) {
            $category = $this->upsertCategory($legacy);
            $this->categoryMap[(int) $legacy->category_id] = $category->id;
        }

        foreach ($legacyCategories as $legacy) {
            $parentLegacyId = (int) ($legacy->category_parent ?? 0);
            if ($parentLegacyId <= 0) {
                continue;
            }

            $childId = $this->categoryMap[(int) $legacy->category_id] ?? null;
            $parentId = $this->categoryMap[$parentLegacyId] ?? null;

            if ($childId && $parentId) {
                ProductCategory::whereKey($childId)->update(['parent_id' => $parentId]);
            }
        }
    }

    protected function migrateBlogCategories(): void
    {
        if (! $this->hasLegacyTable('tbl_category_post')) {
            return;
        }

        DB::table('tbl_category_post')->orderBy('cate_post_id')->get()->each(function (object $legacy): void {
            $category = $this->upsertBlogCategory($legacy);
            $this->blogCategoryMap[(int) $legacy->cate_post_id] = $category->id;
        });
    }

    protected function migrateAttributes(): void
    {
        if (! $this->hasLegacyTable('tbl_attribute')) {
            return;
        }

        $group = AttributeGroup::firstOrCreate(
            ['slug' => 'legacy-product-attributes'],
            [
                'name' => 'Legacy Product Attributes',
                'display_type' => 'select',
                'is_active' => true,
                'display_order' => 0,
            ]
        );

        DB::table('tbl_attribute')->orderBy('id')->get()->each(function (object $legacy) use ($group): void {
            $attribute = Attribute::where('code', 'legacy-attribute-'.$legacy->id)->first()
                ?? new Attribute();

            $attribute->attribute_group_id = $group->id;
            $attribute->name = $legacy->name ?: 'Legacy Attribute '.$legacy->id;
            $attribute->slug = $this->generateUniqueSlug(
                $legacy->name ?: 'legacy-attribute-'.$legacy->id,
                'attributes',
                $attribute->exists ? $attribute->id : null
            );
            $attribute->code = 'legacy-attribute-'.$legacy->id;
            $attribute->type = 'select';
            $attribute->is_filterable = true;
            $attribute->is_required = false;
            $attribute->is_active = ((int) ($legacy->status ?? 1)) === 1;
            $attribute->display_order = (int) $legacy->id;
            $attribute->config = [
                'legacy_id' => (int) $legacy->id,
                'description' => $legacy->description ?? null,
            ];
            $attribute->save();

            $this->attributeMap[(int) $legacy->id] = $attribute->id;
        });
    }

    protected function migrateLocations(): void
    {
        if ($this->hasLegacyTable('tbl_tinhthanhpho')) {
            DB::table('tbl_tinhthanhpho')->orderBy('matp')->get()->each(function (object $legacy): void {
                $location = $this->upsertLocation(
                    'city',
                    $legacy->matp,
                    $legacy->name_city,
                    null,
                    $legacy->type ?? null
                );

                $this->rememberLocation($this->cityMap, $legacy->matp, $location->id);
            });
        }

        if ($this->hasLegacyTable('tbl_quanhuyen')) {
            DB::table('tbl_quanhuyen')->orderBy('maqh')->get()->each(function (object $legacy): void {
                $parentId = $this->cityMap[(string) $legacy->matp] ?? null;
                $location = $this->upsertLocation(
                    'province',
                    $legacy->maqh,
                    $legacy->name_quanhuyen,
                    $parentId,
                    $legacy->type ?? null
                );

                $this->rememberLocation($this->provinceMap, $legacy->maqh, $location->id);
            });
        }

        if ($this->hasLegacyTable('tbl_xaphuongthitran')) {
            DB::table('tbl_xaphuongthitran')->orderBy('xaid')->get()->each(function (object $legacy): void {
                $parentId = $this->provinceMap[(string) $legacy->maqh] ?? null;
                $location = $this->upsertLocation(
                    'ward',
                    $legacy->xaid,
                    $legacy->name_xaphuong,
                    $parentId,
                    $legacy->type ?? null
                );

                $this->rememberLocation($this->wardMap, $legacy->xaid, $location->id);
            });
        }
    }

    protected function migrateProducts(): void
    {
        if (! $this->hasLegacyTable('tbl_product')) {
            return;
        }

        DB::table('tbl_product')->orderBy('product_id')->get()->each(function (object $legacy): void {
            $product = $this->upsertProduct($legacy);
            $this->productMap[(int) $legacy->product_id] = $product->id;
        });
    }

    protected function migrateProductVariants(): void
    {
        if (! $this->hasLegacyTable('tbl_product_attribute')) {
            return;
        }

        DB::table('tbl_product_attribute')->orderBy('id')->get()->each(function (object $legacy): void {
            $this->upsertProductVariant($legacy);
        });
    }

    protected function migrateProductGallery(): void
    {
        if (! $this->hasLegacyTable('tbl_gallery')) {
            return;
        }

        DB::table('tbl_gallery')->orderBy('gallery_id')->get()->each(function (object $legacy): void {
            $productId = $this->productMap[(int) $legacy->product_id] ?? null;
            if (! $productId || empty($legacy->gallery_image)) {
                return;
            }

            $this->upsertProductMedia(
                $productId,
                'uploads/gallery/'.$legacy->gallery_image,
                'gallery',
                (int) $legacy->gallery_id,
                [
                    'legacy_gallery_id' => (int) $legacy->gallery_id,
                    'legacy_gallery_name' => $legacy->gallery_name ?? null,
                ]
            );
        });
    }

    protected function migrateCms(): void
    {
        $this->migrateSliders();
        $this->migrateVideos();
        $this->migrateIcons();
        $this->migrateInformation();
    }

    protected function migrateSliders(): void
    {
        if (! $this->hasLegacyTable('tbl_slider')) {
            return;
        }

        DB::table('tbl_slider')->orderBy('slider_id')->get()->each(function (object $legacy): void {
            if (empty($legacy->slider_image)) {
                return;
            }

            $banner = $this->findByMeta(Banner::class, 'legacy_slider_id', (int) $legacy->slider_id)
                ?? Banner::where('slug', $this->generateUniqueSlugCandidate($legacy->slider_name ?: 'slider-'.$legacy->slider_id))->first()
                ?? new Banner();

            $banner->title = $legacy->slider_name ?: 'Legacy Slider '.$legacy->slider_id;
            $banner->slug = $this->generateUniqueSlug(
                $legacy->slider_name ?: 'legacy-slider-'.$legacy->slider_id,
                'banners',
                $banner->exists ? $banner->id : null
            );
            $banner->image_path = 'uploads/slider/'.$legacy->slider_image;
            $banner->link_url = null;
            $banner->placement = 'homepage';
            $banner->display_order = (int) $legacy->slider_id;
            $banner->is_active = ((int) ($legacy->slider_status ?? 0)) === 1;
            $banner->meta = $this->mergeMeta($banner, [
                'legacy_slider_id' => (int) $legacy->slider_id,
                'legacy_description' => $legacy->slider_desc ?? null,
            ]);
            $banner->save();
        });
    }

    protected function migrateVideos(): void
    {
        if (! $this->hasLegacyTable('tbl_videos')) {
            return;
        }

        DB::table('tbl_videos')->orderBy('video_id')->get()->each(function (object $legacy): void {
            $video = $this->findByMeta(Video::class, 'legacy_video_id', (int) $legacy->video_id)
                ?? Video::where('slug', $legacy->video_slug)->first()
                ?? new Video();

            $video->title = $legacy->video_title ?: 'Legacy Video '.$legacy->video_id;
            $video->slug = $this->generateUniqueSlug(
                $legacy->video_slug ?: $legacy->video_title ?: 'legacy-video-'.$legacy->video_id,
                'videos',
                $video->exists ? $video->id : null
            );
            $video->description = $legacy->video_desc ?? null;
            $video->video_url = $legacy->video_link ?? '';
            $video->thumbnail_path = $legacy->video_image ? 'uploads/videos/'.$legacy->video_image : null;
            $video->is_active = true;
            $video->display_order = (int) $legacy->video_id;
            $video->views_count = (int) ($video->views_count ?? 0);
            $video->meta = $this->mergeMeta($video, [
                'legacy_video_id' => (int) $legacy->video_id,
            ]);
            $video->save();
        });
    }

    protected function migrateIcons(): void
    {
        if (! $this->hasLegacyTable('tbl_icons')) {
            return;
        }

        DB::table('tbl_icons')->orderBy('id_icons')->get()->each(function (object $legacy): void {
            $category = $this->mapSiteLinkCategory($legacy->category ?? null);

            SiteLink::updateOrCreate(
                [
                    'name' => $legacy->name ?: 'Legacy Link '.$legacy->id_icons,
                    'category' => $category,
                    'url' => $legacy->link ?: '#',
                ],
                [
                    'icon_path' => $legacy->image ? 'uploads/icons/'.$legacy->image : null,
                    'display_order' => (int) $legacy->id_icons,
                    'is_active' => true,
                ]
            );
        });
    }

    protected function migrateInformation(): void
    {
        if (! $this->hasLegacyTable('tbl_infomation')) {
            return;
        }

        DB::table('tbl_infomation')->orderBy('info_id')->get()->each(function (object $legacy): void {
            $contact = $this->findByMeta(SiteContact::class, 'legacy_info_id', (int) $legacy->info_id)
                ?? new SiteContact();

            $contact->company_name = 'Legacy Shop';
            $contact->phone = null;
            $contact->email = null;
            $contact->address = null;
            $contact->logo_path = $legacy->info_logo ? 'uploads/contact/'.$legacy->info_logo : null;
            $contact->about = $legacy->info_contact ?? null;
            $contact->map_embed = $legacy->info_map ?? null;
            $contact->social_links = [
                'fanpage_embed' => $legacy->info_fanpage ?? null,
            ];
            $contact->meta = $this->mergeMeta($contact, [
                'legacy_info_id' => (int) $legacy->info_id,
                'slogan_logo' => $legacy->slogan_logo ?? null,
            ]);
            $contact->save();
        });
    }

    protected function migrateBlogPosts(): void
    {
        if (! $this->hasLegacyTable('tbl_posts')) {
            return;
        }

        DB::table('tbl_posts')->orderBy('post_id')->get()->each(function (object $legacy): void {
            $post = $this->upsertBlogPost($legacy);
            $this->blogPostMap[(int) $legacy->post_id] = $post->id;
        });
    }

    protected function migrateCoupons(): void
    {
        if (! $this->hasLegacyTable('tbl_coupon')) {
            return;
        }

        DB::table('tbl_coupon')->orderBy('coupon_id')->get()->each(function (object $legacy): void {
            $code = $this->normalizeCouponCode($legacy->coupon_code ?? null);
            if (! $code) {
                return;
            }

            $coupon = Coupon::firstOrNew(['code' => $code]);
            $coupon->name = $legacy->coupon_name ?: $code;
            $coupon->type = ((int) ($legacy->coupon_condition ?? 0) === 1) ? 'percent' : 'fixed';
            $coupon->value = (float) ($legacy->coupon_number ?? 0);
            $coupon->max_discount = null;
            $coupon->min_order_total = null;
            $coupon->usage_limit = $legacy->coupon_time ?: null;
            $coupon->usage_per_customer = null;
            $coupon->used_count = 0;
            $coupon->starts_at = $this->parseDateTime($legacy->coupon_date_start ?? null);
            $coupon->expires_at = $this->parseDateTime($legacy->coupon_date_end ?? null);
            $coupon->is_active = ((int) ($legacy->coupon_status ?? 0)) === 1;
            $coupon->applicable_segments = null;
            $coupon->meta = $this->mergeMeta($coupon, [
                'legacy_coupon_id' => (int) $legacy->coupon_id,
                'legacy_used' => $legacy->coupon_used ?? null,
                'legacy_condition' => $legacy->coupon_condition ?? null,
            ]);
            $coupon->save();

            $this->couponMap[$coupon->code] = $coupon->id;
        });
    }

    protected function migrateCustomers(): void
    {
        if (! $this->hasLegacyTable('tbl_customers')) {
            return;
        }

        DB::table('tbl_customers')->orderBy('customer_id')->get()->each(function (object $legacy): void {
            $customer = $this->upsertCustomer($legacy);
            $this->customerMap[(int) $legacy->customer_id] = $customer->id;
        });
    }

    protected function migrateProductReviews(): void
    {
        if ($this->hasLegacyTable('tbl_comment')) {
            $legacyComments = DB::table('tbl_comment')->orderBy('comment_id')->get();

            foreach ($legacyComments as $legacy) {
                $review = $this->upsertReviewFromComment($legacy);
                if ($review) {
                    $this->reviewMap[(int) $legacy->comment_id] = $review->id;
                }
            }

            foreach ($legacyComments as $legacy) {
                $parentLegacyId = (int) ($legacy->comment_parent_comment ?? 0);
                if ($parentLegacyId <= 0) {
                    continue;
                }

                $childId = $this->reviewMap[(int) $legacy->comment_id] ?? null;
                $parentId = $this->reviewMap[$parentLegacyId] ?? null;
                if ($childId && $parentId) {
                    ProductReview::whereKey($childId)->update(['parent_id' => $parentId]);
                }
            }
        }

        if ($this->hasLegacyTable('tbl_rating')) {
            DB::table('tbl_rating')->orderBy('rating_id')->get()->each(function (object $legacy): void {
                $this->applyLegacyRating($legacy);
            });
        }
    }

    protected function migrateShippingFees(): void
    {
        if (! $this->hasLegacyTable('tbl_feeship')) {
            return;
        }

        DB::table('tbl_feeship')->orderBy('fee_id')->get()->each(function (object $legacy): void {
            $cityId = $this->cityMap[(string) $legacy->fee_matp] ?? null;
            $provinceId = $this->provinceMap[(string) $legacy->fee_maqh] ?? null;
            $wardId = $this->wardMap[(string) $legacy->fee_xaid] ?? null;

            if (! $cityId && ! $provinceId && ! $wardId) {
                return;
            }

            ShippingFee::updateOrCreate(
                [
                    'city_id' => $cityId,
                    'province_id' => $provinceId,
                    'ward_id' => $wardId,
                ],
                [
                    'amount' => (float) ($legacy->fee_feeship ?? 0),
                    'currency' => 'VND',
                ]
            );
        });
    }

    protected function migrateOrders(): void
    {
        if (! $this->hasLegacyTable('tbl_order')) {
            return;
        }

        DB::table('tbl_order')->orderBy('order_id')->get()->each(function (object $legacy): void {
            $order = $this->upsertOrder($legacy);
            $this->orderMap[(int) $legacy->order_id] = $order->id;
        });
    }

    protected function upsertBrand(object $legacy): Brand
    {
        $brand = $this->findByMeta(Brand::class, 'legacy_brand_id', (int) $legacy->brand_id)
            ?? Brand::where('slug', $legacy->brand_slug)->first()
            ?? new Brand();

        $brand->name = $legacy->brand_name ?: 'Legacy Brand '.$legacy->brand_id;
        $brand->slug = $this->generateUniqueSlug(
            $legacy->brand_slug ?: $legacy->brand_name ?: 'brand-'.$legacy->brand_id,
            'brands',
            $brand->exists ? $brand->id : null
        );
        $brand->description = $legacy->brand_desc ?? null;
        $brand->is_active = ((int) ($legacy->brand_status ?? 1)) === 0;
        $brand->display_order = (int) ($legacy->brand_id ?? 0);
        $brand->meta = $this->mergeMeta($brand, [
            'legacy_brand_id' => (int) $legacy->brand_id,
        ]);
        $brand->save();

        return $brand;
    }

    protected function upsertCategory(object $legacy): ProductCategory
    {
        $category = $this->findByMeta(ProductCategory::class, 'legacy_category_id', (int) $legacy->category_id)
            ?? ProductCategory::where('slug', $legacy->slug_category_product)->first()
            ?? new ProductCategory();

        $category->name = $legacy->category_name ?: 'Legacy Category '.$legacy->category_id;
        $category->slug = $this->generateUniqueSlug(
            $legacy->slug_category_product ?: $legacy->category_name ?: 'category-'.$legacy->category_id,
            'product_categories',
            $category->exists ? $category->id : null
        );
        $category->code = null;
        $category->description = $legacy->category_desc ?? null;
        $category->thumbnail_path = null;
        $category->is_active = ((int) ($legacy->category_status ?? 1)) === 0;
        $category->display_order = (int) ($legacy->category_order ?? $legacy->category_id ?? 0);
        $category->meta_title = $legacy->category_name ?? null;
        $category->meta_description = $legacy->category_desc ?? null;
        $category->meta = $this->mergeMeta($category, [
            'legacy_category_id' => (int) $legacy->category_id,
            'meta_keywords' => $legacy->meta_keywords ?? null,
        ]);
        $category->save();

        return $category;
    }

    protected function upsertBlogCategory(object $legacy): BlogCategory
    {
        $category = $this->findByMeta(BlogCategory::class, 'legacy_blog_category_id', (int) $legacy->cate_post_id)
            ?? BlogCategory::where('slug', $legacy->cate_post_slug)->first()
            ?? new BlogCategory();

        $category->name = $legacy->cate_post_name ?: 'Legacy Blog Category '.$legacy->cate_post_id;
        $category->slug = $this->generateUniqueSlug(
            $legacy->cate_post_slug ?: $legacy->cate_post_name ?: 'blog-category-'.$legacy->cate_post_id,
            'blog_categories',
            $category->exists ? $category->id : null
        );
        $category->description = $legacy->cate_post_desc ?? null;
        $category->is_active = ((int) ($legacy->cate_post_status ?? 1)) === 0;
        $category->display_order = (int) ($legacy->cate_post_id ?? 0);
        $category->save();

        return $category;
    }

    protected function upsertBlogPost(object $legacy): BlogPost
    {
        $post = $this->findByMeta(BlogPost::class, 'legacy_post_id', (int) $legacy->post_id)
            ?? BlogPost::where('slug', $legacy->post_slug)->first()
            ?? new BlogPost();

        $title = $legacy->post_title ?: 'Legacy Blog Post '.$legacy->post_id;

        $post->blog_category_id = $this->blogCategoryMap[(int) ($legacy->cate_post_id ?? 0)] ?? null;
        $post->title = $title;
        $post->slug = $this->generateUniqueSlug(
            $legacy->post_slug ?: $title ?: 'blog-post-'.$legacy->post_id,
            'blog_posts',
            $post->exists ? $post->id : null
        );
        $post->excerpt = $legacy->post_desc ?? null;
        $post->content = $legacy->post_content ?? null;
        $post->featured_image_path = ! empty($legacy->post_image) ? 'uploads/post/'.$legacy->post_image : null;
        $post->status = ((int) ($legacy->post_status ?? 1)) === 0 ? 'published' : 'draft';
        $post->published_at = $post->status === 'published'
            ? ($post->published_at ?? now())
            : null;
        $post->views_count = (int) ($legacy->post_views ?? $post->views_count ?? 0);
        $post->meta_title = $legacy->post_title ?: $post->meta_title;
        $post->meta_description = $legacy->post_meta_desc ?: ($legacy->post_desc ?? $post->meta_description);
        $post->meta = $this->mergeMeta($post, [
            'legacy_post_id' => (int) $legacy->post_id,
            'keywords' => $legacy->post_meta_keywords ?? null,
        ]);
        $post->save();

        return $post;
    }

    protected function upsertProduct(object $legacy): Product
    {
        $sku = $this->legacyProductSku($legacy);
        $product = $this->findByMeta(Product::class, 'legacy_product_id', (int) $legacy->product_id)
            ?? ($sku ? Product::where('sku', $sku)->first() : null)
            ?? Product::where('slug', $legacy->product_slug)->first()
            ?? new Product();

        $brandId = $this->brandMap[(int) ($legacy->brand_id ?? 0)] ?? null;
        $categoryId = $this->categoryMap[(int) ($legacy->category_id ?? 0)] ?? null;
        $thumbnailPath = ! empty($legacy->product_image) ? 'uploads/product/'.$legacy->product_image : null;

        $product->brand_id = $brandId;
        $product->primary_category_id = $categoryId;
        $product->name = $legacy->product_name ?: 'Legacy Product '.$legacy->product_id;
        $product->slug = $this->generateUniqueSlug(
            $legacy->product_slug ?: $legacy->product_name ?: 'product-'.$legacy->product_id,
            'products',
            $product->exists ? $product->id : null
        );
        $product->sku = $sku;
        $product->barcode = null;
        $product->unit = null;
        $product->price = (float) ($legacy->product_price ?? 0);
        $product->sale_price = null;
        $product->cost_price = isset($legacy->price_cost) ? (float) $legacy->price_cost : null;
        $product->stock_quantity = (int) ($legacy->product_quantity ?? 0);
        $product->stock_alert_threshold = 0;
        $product->is_active = ((int) ($legacy->product_status ?? 1)) === 0;
        $product->is_featured = false;
        $product->display_order = (int) ($legacy->product_id ?? 0);
        $product->thumbnail_path = $thumbnailPath;
        $product->description = $legacy->product_desc ?? null;
        $product->content = $legacy->product_content ?? null;
        $product->views_count = (int) ($legacy->product_views ?? 0);
        $product->meta = $this->mergeMeta($product, [
            'legacy_product_id' => (int) $legacy->product_id,
            'tags' => $legacy->product_tags ?? null,
            'product_sold' => $legacy->product_sold ?? null,
            'product_file' => $legacy->product_file ?? null,
        ]);
        $product->meta_title = $legacy->product_name ?? null;
        $product->meta_description = $legacy->product_desc ?? null;
        $product->save();

        if ($categoryId) {
            $product->categories()->syncWithoutDetaching([$categoryId]);
        }

        if ($thumbnailPath) {
            $this->upsertProductMedia($product->id, $thumbnailPath, 'thumbnail', 0, [
                'legacy_product_id' => (int) $legacy->product_id,
                'source' => 'legacy_product_image',
            ]);
        }

        return $product;
    }

    protected function upsertProductVariant(object $legacy): ?ProductVariant
    {
        $productId = $this->productMap[(int) ($legacy->product_id ?? 0)] ?? null;
        if (! $productId) {
            return null;
        }

        $attributeId = $this->attributeMap[(int) ($legacy->attribute_id ?? 0)] ?? null;
        $attribute = $attributeId ? Attribute::find($attributeId) : null;
        $value = trim((string) ($legacy->name ?? ''));
        $sku = 'LEGACY-ATTR-'.$legacy->id;

        $variant = ProductVariant::where('sku', $sku)->first();

        if (! $variant && ! ProductVariant::whereKey((int) $legacy->id)->exists()) {
            $variant = new ProductVariant();
            $variant->id = (int) $legacy->id;
        }

        $variant ??= new ProductVariant();
        $variant->product_id = $productId;
        $variant->name = $value !== '' ? $value : 'Legacy Variant '.$legacy->id;
        $variant->sku = $sku;
        $variant->barcode = null;
        $variant->price = (float) ($legacy->price ?? 0);
        $variant->sale_price = null;
        $variant->cost_price = null;
        $variant->stock_quantity = max(0, (int) ($legacy->quantity ?? 0));
        $variant->stock_alert_threshold = 0;
        $variant->image_path = $legacy->image ? 'uploads/attribute/'.$legacy->image : null;
        $variant->attributes = $attribute
            ? [$attribute->name => $value]
            : ['legacy_attribute' => $value];
        $variant->is_active = true;
        $variant->save();

        $this->productVariantMap[(int) $legacy->id] = $variant->id;

        if ($attribute && $value !== '') {
            $attributeValue = $this->upsertAttributeValue($attribute, $legacy);
            ProductVariantAttributeValue::updateOrCreate(
                [
                    'product_variant_id' => $variant->id,
                    'attribute_id' => $attribute->id,
                ],
                [
                    'attribute_value_id' => $attributeValue->id,
                    'custom_value' => null,
                ]
            );
        }

        return $variant;
    }

    protected function upsertAttributeValue(Attribute $attribute, object $legacy): AttributeValue
    {
        $value = trim((string) ($legacy->name ?? 'Legacy Value '.$legacy->id));
        $key = $attribute->id.':'.$value;

        if (isset($this->attributeValueMap[$key])) {
            return AttributeValue::find($this->attributeValueMap[$key]);
        }

        $attributeValue = AttributeValue::firstOrNew([
            'attribute_id' => $attribute->id,
            'value' => $value,
        ]);
        $attributeValue->slug = Str::slug($value) ?: null;
        $attributeValue->display_order = (int) ($legacy->id ?? 0);
        $attributeValue->is_active = true;
        $attributeValue->meta = $this->mergeMeta($attributeValue, [
            'legacy_product_attribute_id' => (int) $legacy->id,
        ]);
        $attributeValue->save();

        $this->attributeValueMap[$key] = $attributeValue->id;

        return $attributeValue;
    }

    protected function upsertCustomer(object $legacy): Customer
    {
        $email = trim((string) ($legacy->customer_email ?? ''))
            ?: 'legacy-'.$legacy->customer_id.'@example.test';

        $customer = $this->findByMeta(Customer::class, 'legacy_customer_id', (int) $legacy->customer_id)
            ?? Customer::where('email', $email)->first()
            ?? new Customer();

        $meta = $this->mergeMeta($customer, [
            'legacy_customer_id' => (int) $legacy->customer_id,
            'legacy_password_hash' => $legacy->customer_password ?? null,
            'legacy_vip' => $legacy->customer_vip ?? null,
            'legacy_token' => $legacy->customer_token ?? null,
        ]);

        $customer->name = $legacy->customer_name ?: 'Legacy Customer '.$legacy->customer_id;
        $customer->email = $email;
        $customer->phone = $this->uniqueCustomerPhone($legacy->customer_phone ?? null, $customer);
        $this->fillCustomerPassword($customer, trim((string) ($legacy->customer_password ?? '')), $meta);
        $customer->avatar_path = $this->legacyCustomerAvatarPath($legacy->customer_picture ?? null);
        $customer->is_active = true;
        $customer->meta = $meta;
        $customer->save();

        return $customer;
    }

    protected function upsertOrder(object $legacy): Order
    {
        $order = Order::firstOrNew([
            'code' => $legacy->order_code ?: 'ORDER-'.$legacy->order_id,
        ]);

        $customerId = $this->customerMap[(int) ($legacy->customer_id ?? 0)] ?? null;
        $customer = $customerId ? Customer::find($customerId) : null;
        $shippingRecord = ($legacy->shipping_id ?? null) && $this->hasLegacyTable('tbl_shipping')
            ? DB::table('tbl_shipping')->where('shipping_id', $legacy->shipping_id)->first()
            : null;
        $address = $this->getOrCreateAddress($shippingRecord, $customer);
        $details = $this->hasLegacyTable('tbl_order_details')
            ? DB::table('tbl_order_details')->where('order_code', $legacy->order_code)->get()
            : collect();

        $firstDetail = $details->first();
        $subtotal = $details->sum(fn (object $detail): float => (float) ($detail->product_price ?? 0) * (int) ($detail->product_sales_quantity ?? 0));
        $shippingFee = (float) ($firstDetail->product_feeship ?? 0);
        $total = isset($legacy->order_total) ? (float) $legacy->order_total : ($subtotal + $shippingFee);
        $couponCode = $this->normalizeCouponCode($firstDetail->product_coupon ?? null);
        $discount = max(0.0, ($subtotal + $shippingFee) - $total);

        $order->fill([
            'customer_id' => $customer?->id,
            'customer_address_id' => $address?->id,
            'status' => $this->mapOrderStatus((int) ($legacy->order_status ?? 0)),
            'payment_status' => 'pending',
            'fulfillment_status' => $this->mapFulfillmentStatus((int) ($legacy->order_status ?? 0)),
            'currency' => 'VND',
            'subtotal' => $subtotal,
            'discount_total' => $discount,
            'shipping_total' => $shippingFee,
            'tax_total' => 0,
            'total' => $total,
            'coupon_code' => $couponCode,
            'coupon_discount' => $discount,
            'source' => 'legacy',
            'note' => $shippingRecord->shipping_notes ?? $legacy->order_destroy ?? null,
            'placed_at' => $this->parseDateTime($legacy->created_at ?? $legacy->order_date ?? null),
            'confirmed_at' => $this->parseDateTime($legacy->updated_at ?? null),
            'shipped_at' => null,
            'completed_at' => $this->mapCompletionDate($legacy),
            'cancelled_at' => $this->mapCancelledDate($legacy),
            'meta' => $this->mergeMeta($order, [
                'legacy_order_id' => (int) $legacy->order_id,
                'legacy_order_destroy' => $legacy->order_destroy ?? null,
            ]),
        ]);
        $order->save();

        $this->orderMap[(int) $legacy->order_id] = $order->id;

        $order->items()->delete();
        foreach ($details as $detail) {
            $productId = $this->productMap[(int) ($detail->product_id ?? 0)] ?? null;
            $variant = $this->findVariantForOrderDetail($detail, $productId);
            $quantity = (int) ($detail->product_sales_quantity ?? 0);
            $price = (float) ($detail->product_price ?? 0);

            $order->items()->create([
                'product_id' => $productId,
                'product_variant_id' => $variant?->id,
                'product_name' => $detail->product_name ?? 'Unknown product',
                'sku' => $variant?->sku,
                'attributes' => $detail->product_attribute
                    ? ['attribute' => $detail->product_attribute]
                    : null,
                'price' => $price,
                'sale_price' => null,
                'discount_amount' => 0,
                'quantity' => $quantity,
                'subtotal' => $price * $quantity,
                'tax_amount' => 0,
                'meta' => [
                    'legacy_order_detail_id' => $detail->order_details_id ?? null,
                ],
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
        $legacyPayment = isset($legacy->payment_id) && $this->hasLegacyTable('tbl_payment')
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
        if (! $shipping || ! $customer) {
            return null;
        }

        $hash = md5(implode('|', [
            $customer->id,
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
                'customer_id' => $customer->id,
                'address_line1' => $shipping->shipping_address ?? '',
                'recipient_name' => $shipping->shipping_name ?? $customer->name ?? 'Legacy',
                'recipient_phone' => $shipping->shipping_phone ?? $customer->phone,
            ],
            [
                'label' => 'Legacy',
                'email' => $shipping->shipping_email ?? $customer->email,
                'country' => 'Vietnam',
                'location_city_id' => null,
                'location_province_id' => null,
                'location_ward_id' => null,
                'postal_code' => null,
                'address_line2' => $shipping->shipping_notes ?? null,
                'is_default' => ! Address::where('customer_id', $customer->id)->exists(),
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
        $productId = $this->productMap[(int) ($legacy->comment_product_id ?? 0)] ?? null;
        if (! $productId) {
            return null;
        }

        $review = $this->findByMeta(ProductReview::class, 'legacy_comment_id', (int) $legacy->comment_id)
            ?? new ProductReview();
        $customerId = isset($legacy->customer_id) ? ($this->customerMap[(int) $legacy->customer_id] ?? null) : null;
        $customer = $customerId ? Customer::find($customerId) : null;
        $status = $this->mapReviewStatus((int) ($legacy->comment_status ?? 1));

        $review->product_id = $productId;
        $review->customer_id = $customer?->id;
        $review->author_name = $legacy->comment_name ?? $customer?->name ?? 'Anonymous';
        $review->author_email = $legacy->comment_email ?? $customer?->email;
        $review->rating = $review->rating ?: 5;
        $review->title = $review->title ?? null;
        $review->content = $legacy->comment ?? $review->content ?? '';
        $review->status = $status;
        $review->is_public = $status === 'approved';
        $review->approved_at = $review->is_public
            ? ($review->approved_at ?? $this->parseDateTime($legacy->comment_date ?? null))
            : null;
        $review->meta = $this->mergeMeta($review, [
            'legacy_comment_id' => (int) $legacy->comment_id,
            'legacy_parent_id' => $legacy->comment_parent_comment ?? null,
        ]);
        $review->save();

        return $review;
    }

    protected function applyLegacyRating(object $legacy): void
    {
        $productId = $this->productMap[(int) ($legacy->product_id ?? 0)] ?? null;
        if (! $productId) {
            return;
        }

        $customerId = isset($legacy->customer_id) ? ($this->customerMap[(int) $legacy->customer_id] ?? null) : null;
        $review = $this->findByMeta(ProductReview::class, 'legacy_rating_id', (int) $legacy->rating_id);

        if (! $review && $customerId) {
            $review = ProductReview::where('product_id', $productId)
                ->where('customer_id', $customerId)
                ->orderByDesc('created_at')
                ->first();
        }

        $review ??= new ProductReview([
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

        $review->rating = max(1, min(5, (int) ($legacy->rating ?? 5)));
        $review->meta = $this->mergeMeta($review, [
            'legacy_rating_id' => (int) $legacy->rating_id,
        ]);
        $review->save();
    }

    protected function upsertLocation(string $type, string|int $legacyCode, ?string $name, ?int $parentId, ?string $legacyType): Location
    {
        $rawCode = trim((string) $legacyCode);
        $location = Location::firstOrNew(['code' => 'legacy-'.$type.'-'.$rawCode]);
        $location->name = $name ?: 'Legacy '.Str::title($type).' '.$rawCode;
        $location->type = $type;
        $location->parent_id = $parentId;
        $location->display_order = (int) $rawCode;
        $location->is_active = true;
        $location->meta = $this->mergeMeta($location, [
            'legacy_code' => $rawCode,
            'legacy_type' => $legacyType,
        ]);
        $location->save();

        return $location;
    }

    protected function upsertProductMedia(int $productId, string $filePath, string $collection, int $displayOrder, array $meta): ProductMedia
    {
        $media = ProductMedia::firstOrNew([
            'product_id' => $productId,
            'file_path' => $filePath,
            'collection' => $collection,
        ]);

        $media->disk = 'public';
        $media->display_order = $displayOrder;
        $media->meta = $this->mergeMeta($media, $meta);
        $media->save();

        return $media;
    }

    protected function findVariantForOrderDetail(object $detail, ?int $productId): ?ProductVariant
    {
        if (! $productId) {
            return null;
        }

        $label = trim((string) ($detail->product_attribute ?? ''));
        if ($label === '') {
            return null;
        }

        return ProductVariant::where('product_id', $productId)
            ->where('name', $label)
            ->first();
    }

    protected function fillCustomerPassword(Customer $customer, string $legacyPassword, array $meta): void
    {
        if (($meta['legacy_password_upgraded_at'] ?? null) && $customer->exists) {
            return;
        }

        if (StorefrontCustomerPassword::isReusableLegacyHash($legacyPassword)) {
            $customer->password = $legacyPassword;
            return;
        }

        if (! $customer->exists || empty($customer->password)) {
            $customer->password = Hash::make(Str::random(32));
        }
    }

    protected function uniqueCustomerPhone(?string $phone, Customer $customer): ?string
    {
        $phone = trim((string) $phone);
        if ($phone === '') {
            return null;
        }

        $duplicateQuery = Customer::where('phone', $phone);
        if ($customer->exists) {
            $duplicateQuery->where('id', '!=', $customer->id);
        }

        return $duplicateQuery->exists() ? null : $phone;
    }

    protected function legacyCustomerAvatarPath(?string $value): ?string
    {
        $value = trim((string) $value);
        if ($value === '') {
            return null;
        }

        if (str_starts_with($value, 'http://') || str_starts_with($value, 'https://')) {
            return $value;
        }

        return 'uploads/customer/'.$value;
    }

    protected function legacyProductSku(object $legacy): ?string
    {
        $code = trim((string) ($legacy->product_code ?? ''));

        return $code !== '' ? $code : null;
    }

    protected function mapSiteLinkCategory(?string $category): string
    {
        $category = Str::lower(trim((string) $category));

        return match ($category) {
            'doitac', 'doi-tac', 'partner' => 'partner',
            'quick_link', 'quicklink' => 'quick_link',
            default => 'social',
        };
    }

    protected function mapReviewStatus(int $status): string
    {
        return match ($status) {
            0 => 'approved',
            2 => 'rejected',
            default => 'pending',
        };
    }

    protected function mapOrderStatus(int $status): string
    {
        return match ($status) {
            3 => 'cancelled',
            2 => 'completed',
            1 => 'confirmed',
            0 => 'pending',
            default => 'pending',
        };
    }

    protected function mapFulfillmentStatus(int $status): string
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
            str_contains($statusLower, 'thanh') || str_contains($statusLower, 'success') => 'succeeded',
            str_contains($statusLower, 'hoan') || str_contains($statusLower, 'refund') => 'refunded',
            str_contains($statusLower, 'huy') || str_contains($statusLower, 'fail') => 'failed',
            default => 'pending',
        };
    }

    protected function normalizeCouponCode(?string $code): ?string
    {
        $code = trim((string) $code);
        if ($code === '') {
            return null;
        }

        $normalized = Str::lower($code);
        if (in_array($normalized, ['0', 'no', 'khong', 'không', 'none', 'null'], true)) {
            return null;
        }

        return Str::upper($code);
    }

    protected function parseDateTime($value): ?Carbon
    {
        if (empty($value)) {
            return null;
        }

        try {
            return Carbon::parse($value);
        } catch (\Throwable) {
            Log::warning('Unable to parse legacy datetime', ['value' => $value]);

            return null;
        }
    }

    protected function mapCompletionDate(object $legacy): ?Carbon
    {
        return ((int) ($legacy->order_status ?? 0)) === 2
            ? $this->parseDateTime($legacy->updated_at ?? $legacy->order_date ?? null)
            : null;
    }

    protected function mapCancelledDate(object $legacy): ?Carbon
    {
        return ((int) ($legacy->order_status ?? 0)) === 3
            ? $this->parseDateTime($legacy->updated_at ?? null)
            : null;
    }

    protected function refreshCouponUsageCounts(): void
    {
        if (! Schema::hasTable('coupons') || ! Schema::hasTable('coupon_redemptions')) {
            return;
        }

        Coupon::query()->each(function (Coupon $coupon): void {
            $coupon->used_count = $coupon->redemptions()->count();
            $coupon->save();
        });
    }

    protected function rememberLocation(array &$map, string|int $legacyCode, int $locationId): void
    {
        $raw = trim((string) $legacyCode);
        $map[$raw] = $locationId;

        if (is_numeric($raw)) {
            $map[(string) ((int) $raw)] = $locationId;
        }
    }

    protected function hasLegacyTable(string $table): bool
    {
        return Schema::hasTable($table);
    }

    /**
     * @template T of Model
     * @param  class-string<T>  $modelClass
     * @return T|null
     */
    protected function findByMeta(string $modelClass, string $key, mixed $value): ?Model
    {
        $model = new $modelClass();

        if (! Schema::hasColumn($model->getTable(), 'meta')) {
            return null;
        }

        /** @var Model|null $model */
        $model = $modelClass::query()->where('meta->'.$key, $value)->first();

        return $model;
    }

    protected function mergeMeta(Model $model, array $values): array
    {
        if (! Schema::hasColumn($model->getTable(), 'meta')) {
            return array_filter($values, fn ($value) => $value !== null);
        }

        $existing = $model->getAttribute('meta');
        if (! is_array($existing)) {
            $existing = [];
        }

        return array_filter(array_merge($existing, $values), fn ($value) => $value !== null);
    }

    protected function generateUniqueSlug(string $value, string $table, ?int $ignoreId = null): string
    {
        $baseSlug = $this->generateUniqueSlugCandidate($value);
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

    protected function generateUniqueSlugCandidate(string $value): string
    {
        return Str::slug($value) ?: Str::slug(Str::random(8));
    }
}
