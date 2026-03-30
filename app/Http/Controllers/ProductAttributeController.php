<?php

namespace App\Http\Controllers;

use App\Application\Shared\Services\PublicUploadService;
use App\Http\Requests\Admin\Legacy\Products\StoreProductAttributeRequest;
use App\Models\ProductAttribute;
use App\Models\Attribute;
use App\Models\Product;

class ProductAttributeController extends Controller
{
    public function __construct(
        private readonly PublicUploadService $publicUploadService,
    ) {
    }

    public function store(StoreProductAttributeRequest $request)
    {
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
        $attribute = Attribute::orderBy('id', 'DESC')->get();
        $product_attribute = ProductAttribute::with('product', 'attribute')->where('product_id', $id)->orderBy('id', 'DESC')->get();

        return view('admin.product_attribute.create', compact('product', 'attribute', 'product_attribute'));
    }
    public function deleteProductAttribute($id_attribute)
    {
        $productAttribute = ProductAttribute::find($id_attribute);

        if (! $productAttribute) {
            return redirect()->back()->with('message', 'Product attribute not found.');
        }

        $productAttribute->delete();

        return redirect()->back()->with('message', 'Product attribute deleted successfully.');
    }
}
