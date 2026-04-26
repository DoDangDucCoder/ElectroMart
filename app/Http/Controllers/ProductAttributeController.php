<?php

namespace App\Http\Controllers;

use App\Application\Shared\Services\PublicUploadService;
use App\Http\Requests\Admin\Legacy\Products\StoreProductAttributeRequest;
use App\Infrastructure\Persistence\Eloquent\Catalog\Product as CatalogProduct;
use App\Models\Attribute;
use App\Models\Product as LegacyProduct;
use App\Models\ProductAttribute;
use Illuminate\Support\Facades\Schema;

class ProductAttributeController extends Controller
{
    public function __construct(
        private readonly PublicUploadService $publicUploadService,
    ) {
    }

    public function store(StoreProductAttributeRequest $request)
    {
        if (! Schema::hasTable((new Attribute())->getTable())) {
            return redirect()->back()->with('message', 'Bảng thuộc tính nền chưa sẵn sàng trên môi trường này.');
        }

        if (! Schema::hasTable((new ProductAttribute())->getTable())) {
            return redirect()->back()->with('message', 'Bảng biến thể sản phẩm chưa sẵn sàng trên môi trường này.');
        }

        $validated = $request->validated();

        $newImage = $this->publicUploadService->storeLegacyImage($request->file('image'), 'uploads/attribute');

        ProductAttribute::create([
            'product_id' => $validated['product_id'],
            'name' => $validated['name'],
            'price' => $validated['price'],
            'quantity' => $validated['quantity'],
            'image' => $newImage,
            'description' => $validated['description'],
            'attribute_id' => $validated['attribute_id'],
        ]);

        return redirect()->back()->with('message', 'Đã thêm thuộc tính sản phẩm thành công.');
    }

    public function show(string $id)
    {
        $product = Schema::hasTable((new LegacyProduct())->getTable())
            ? LegacyProduct::where('product_id', $id)->first()
            : null;

        if (! $product) {
            $product = CatalogProduct::query()->find($id);
        }

        if (! $product) {
            return redirect('/all-product')->with('message', 'Không tìm thấy sản phẩm cần quản lý thuộc tính.');
        }

        $usesModernProduct = $product instanceof CatalogProduct;
        $attributeTableReady = Schema::hasTable((new Attribute())->getTable());
        $productAttributeTableReady = Schema::hasTable((new ProductAttribute())->getTable());
        $legacyProductTableReady = Schema::hasTable((new LegacyProduct())->getTable());

        $attribute = $attributeTableReady
            ? Attribute::query()->orderByDesc('id')->get()
            : collect();

        if ($productAttributeTableReady) {
            $productAttributeQuery = ProductAttribute::query()
                ->where('product_id', $product->product_id)
                ->orderByDesc('id');

            if (! $usesModernProduct && $legacyProductTableReady) {
                $productAttributeQuery->with('product');
            }

            if ($attributeTableReady) {
                $productAttributeQuery->with('attribute');
            }

            $product_attribute = $productAttributeQuery->get();

            if ($usesModernProduct || ! $legacyProductTableReady) {
                $product_attribute->each(function (ProductAttribute $productAttribute) use ($product): void {
                    $productAttribute->setRelation('product', $product);
                });
            }

            if (! $attributeTableReady) {
                $product_attribute->each(function (ProductAttribute $productAttribute): void {
                    $productAttribute->setRelation('attribute', null);
                });
            }
        } else {
            $product_attribute = collect();
        }

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
        if (! Schema::hasTable((new ProductAttribute())->getTable())) {
            return redirect()->back()->with('message', 'Bảng thuộc tính sản phẩm chưa sẵn sàng trên môi trường này.');
        }

        $productAttribute = ProductAttribute::find($id_attribute);

        if (! $productAttribute) {
            return redirect()->back()->with('message', 'Không tìm thấy thuộc tính sản phẩm.');
        }

        $this->publicUploadService->deletePublicFile(
            $productAttribute->image ? 'uploads/attribute/' . $productAttribute->image : null
        );
        $productAttribute->delete();

        return redirect()->back()->with('message', 'Đã xóa thuộc tính sản phẩm thành công.');
    }
}
