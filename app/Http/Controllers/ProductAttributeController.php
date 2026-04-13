<?php

namespace App\Http\Controllers;

use App\Application\Shared\Services\PublicUploadService;
use App\Http\Requests\Admin\Legacy\Products\StoreProductAttributeRequest;
use App\Models\ProductAttribute;
use App\Models\Attribute;
use App\Models\Product;
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

        return redirect()->back()->with('message', 'Product attribute added successfully.');
    }

    public function show(string $id)
    {
        $product = Product::where('product_id', $id)->first();
        if (! $product) {
            return redirect('/all-product')->with('message', 'Không tìm thấy sản phẩm cần quản lý thuộc tính.');
        }

        $attributeTableReady = Schema::hasTable((new Attribute())->getTable());
        $productAttributeTableReady = Schema::hasTable((new ProductAttribute())->getTable());

        $attribute = $attributeTableReady
            ? Attribute::query()->orderByDesc('id')->get()
            : collect();

        if ($productAttributeTableReady) {
            $productAttributeQuery = ProductAttribute::with('product')
                ->where('product_id', $id)
                ->orderByDesc('id');

            if ($attributeTableReady) {
                $productAttributeQuery->with('attribute');
            }

            $product_attribute = $productAttributeQuery->get();

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
            return redirect()->back()->with('message', 'Product attribute not found.');
        }

        $this->publicUploadService->deletePublicFile(
            $productAttribute->image ? 'uploads/attribute/' . $productAttribute->image : null
        );
        $productAttribute->delete();

        return redirect()->back()->with('message', 'Product attribute deleted successfully.');
    }
}
