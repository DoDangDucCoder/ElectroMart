<?php

namespace App\Http\Controllers;

use App\Http\Requests\Admin\Legacy\Products\StoreAttributeRequest;
use App\Http\Requests\Admin\Legacy\Products\UpdateAttributeRequest;
use App\Infrastructure\Persistence\Eloquent\Catalog\Attribute;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

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
        $attribute->slug = $this->uniqueAttributeSlug($validated['name']);
        $attribute->type = 'select';
        $attribute->is_active = true;
        $attribute->config = [
            'description' => $validated['description'],
        ];
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
        $attribute->slug = $this->uniqueAttributeSlug($validated['name'], (int) $attribute->id);
        $attribute->config = array_filter(array_merge($attribute->config ?? [], [
            'description' => $validated['description'],
        ]), static fn ($value) => $value !== null && $value !== '');

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

    protected function uniqueAttributeSlug(string $name, ?int $ignoreId = null): string
    {
        $base = Str::slug($name) ?: Str::random(8);
        $slug = $base;
        $counter = 2;

        while (Attribute::query()
            ->where('slug', $slug)
            ->when($ignoreId, fn ($query) => $query->whereKeyNot($ignoreId))
            ->exists()
        ) {
            $slug = $base.'-'.$counter;
            $counter++;
        }

        return $slug;
    }
}
