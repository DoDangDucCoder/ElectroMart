<?php

namespace App\Http\Controllers;

use App\Application\Shared\Services\CacheService;
use App\Application\Shared\Services\PublicUploadService;
use App\Http\Requests\Admin\Legacy\Products\StoreProductAttributeRequest;
use App\Infrastructure\Persistence\Eloquent\Catalog\Attribute as CatalogAttribute;
use App\Infrastructure\Persistence\Eloquent\Catalog\Product as CatalogProduct;
use App\Infrastructure\Persistence\Eloquent\Catalog\ProductVariant;
use Illuminate\Support\Facades\Schema;

class ProductAttributeController extends Controller
{
    public function __construct(
        private readonly PublicUploadService $publicUploadService,
        private readonly CacheService $cacheService,
    ) {
    }

    public function store(StoreProductAttributeRequest $request)
    {
        if (! Schema::hasTable((new CatalogAttribute())->getTable())) {
            return redirect()->back()->with('message', 'Bảng thuộc tính nền chưa sẵn sàng trên môi trường này.');
        }

        if (! Schema::hasTable((new ProductVariant())->getTable())) {
            return redirect()->back()->with('message', 'Bảng biến thể sản phẩm chưa sẵn sàng trên môi trường này.');
        }

        $validated = $request->validated();

        $newImage = $this->publicUploadService->storeLegacyImage($request->file('image'), 'uploads/attribute');

        $variant = ProductVariant::create([
            'product_id' => $validated['product_id'],
            'name' => $validated['name'],
            'price' => $validated['price'],
            'stock_quantity' => $validated['quantity'],
            'image_path' => 'uploads/attribute/'.$newImage,
            'attributes' => [
                'attribute_id' => (int) $validated['attribute_id'],
                'description' => $validated['description'],
                'label' => $validated['name'],
            ],
            'is_active' => true,
        ]);

        $this->cacheService->invalidateProductCache((int) $variant->product_id);

        return redirect()->back()->with('message', 'Đã thêm thuộc tính sản phẩm thành công.');
    }

    public function show(string $id)
    {
        $product = CatalogProduct::query()->find($id);

        if (! $product) {
            return redirect('/admin/products-management')->with('message', 'Không tìm thấy sản phẩm cần quản lý thuộc tính.');
        }

        $attributeTableReady = Schema::hasTable((new CatalogAttribute())->getTable());
        $productAttributeTableReady = Schema::hasTable((new ProductVariant())->getTable());

        $attribute = $attributeTableReady
            ? CatalogAttribute::query()->orderByDesc('id')->get()
            : collect();

        $attributesById = $attribute->keyBy('id');
        $product_attribute = $productAttributeTableReady
            ? ProductVariant::query()
                ->where('product_id', $product->id)
                ->orderByDesc('id')
                ->get()
                ->map(fn (ProductVariant $variant) => $this->mapVariantToLegacyAttributeShape(
                    $variant,
                    $product,
                    $attributesById
                ))
            : collect();

        return view('admin.product_attribute.create', compact(
            'product',
            'attribute',
            'product_attribute',
            'attributeTableReady',
            'productAttributeTableReady'
        ));
    }
    public function deleteProductAttribute($id_attribute)
    {
        if (! Schema::hasTable((new ProductVariant())->getTable())) {
            return redirect()->back()->with('message', 'Bảng thuộc tính sản phẩm chưa sẵn sàng trên môi trường này.');
        }

        $productAttribute = ProductVariant::find($id_attribute);

        if (! $productAttribute) {
            return redirect()->back()->with('message', 'Không tìm thấy thuộc tính sản phẩm.');
        }

        $productId = (int) $productAttribute->product_id;

        $this->publicUploadService->deletePublicFile($productAttribute->image_path);
        $productAttribute->delete();
        $this->cacheService->invalidateProductCache($productId);

        return redirect()->back()->with('message', 'Đã xóa thuộc tính sản phẩm thành công.');
    }

    protected function mapVariantToLegacyAttributeShape(ProductVariant $variant, CatalogProduct $product, $attributesById): object
    {
        $variantAttributes = is_array($variant->attributes) ? $variant->attributes : [];
        $attributeId = (int) ($variantAttributes['attribute_id'] ?? 0);
        $imagePath = trim((string) $variant->image_path);

        return (object) [
            'id' => $variant->id,
            'product' => $product,
            'product_id' => $variant->product_id,
            'name' => $variant->name,
            'attribute' => $attributeId > 0 ? $attributesById->get($attributeId) : null,
            'image' => $imagePath !== '' ? basename($imagePath) : null,
            'quantity' => (int) $variant->stock_quantity,
            'price' => (float) ($variant->sale_price ?: $variant->price),
            'description' => $variantAttributes['description'] ?? null,
        ];
    }
}
