<?php

namespace App\Http\Controllers;

use App\Http\Requests\Admin\Legacy\Products\StoreAttributeRequest;
use App\Http\Requests\Admin\Legacy\Products\UpdateAttributeRequest;
use App\Models\Attribute;
use Illuminate\Support\Facades\Schema;

class AttributeController extends Controller
{
    public function index()
    {
        $attributeTableReady = $this->attributeTableReady();
        $attributes = $attributeTableReady
            ? Attribute::query()->orderByDesc('id')->get()
            : collect();

        return view('admin.attribute.index', compact('attributes', 'attributeTableReady'));
    }

    public function create()
    {
        return view('admin.attribute.create', [
            'attributeTableReady' => $this->attributeTableReady(),
        ]);
    }

    public function store(StoreAttributeRequest $request)
    {
        if (! $this->attributeTableReady()) {
            return redirect()->route('attribute.index')->with('error', 'Bảng thuộc tính chưa sẵn sàng trên môi trường này.');
        }

        $validated = $request->validated();

        $attribute = new Attribute();
        $attribute->name = $validated['name'];
        $attribute->description = $validated['description'];
        $attribute->save();

        return redirect()->route('attribute.index')->with('message', 'Thêm thuộc tính thành công.');
    }
    public function show(string $id)
    {
        //
    }

    public function edit($id)
    {
        if (! $this->attributeTableReady()) {
            return redirect()->route('attribute.index')->with('error', 'Bảng thuộc tính chưa sẵn sàng trên môi trường này.');
        }

        $attribute = Attribute::find($id);

        if (! $attribute) {
            return redirect()->route('attribute.index')->with('error', 'Không tìm thấy thuộc tính cần cập nhật.');
        }

        return view('admin.attribute.edit', compact('attribute'));
    }

    public function update(UpdateAttributeRequest $request, $id)
    {
        if (! $this->attributeTableReady()) {
            return redirect()->route('attribute.index')->with('error', 'Bảng thuộc tính chưa sẵn sàng trên môi trường này.');
        }

        $validated = $request->validated();

        $attribute = Attribute::find($id);

        if (! $attribute) {
            return redirect()->route('attribute.index')->with('error', 'Không tìm thấy thuộc tính cần cập nhật.');
        }

        $attribute->name = $validated['name'];
        $attribute->description = $validated['description'];

        $attribute->save();

        return redirect()->route('attribute.index')->with('message', 'Cập nhật thuộc tính thành công.');
    }
    public function destroy($id)
    {
        if (! $this->attributeTableReady()) {
            return redirect()->route('attribute.index')->with('error', 'Bảng thuộc tính chưa sẵn sàng trên môi trường này.');
        }

        $attribute = Attribute::find($id);

        if (! $attribute) {
            return redirect()->route('attribute.index')->with('error', 'Không tìm thấy thuộc tính cần xóa.');
        }

        $attribute->delete();

        return redirect()->route('attribute.index')->with('message', 'Xóa thuộc tính thành công.');
    }

    protected function attributeTableReady(): bool
    {
        return Schema::hasTable((new Attribute())->getTable());
    }
}
