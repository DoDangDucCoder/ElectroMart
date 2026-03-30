<?php

namespace App\Http\Controllers;

use App\Http\Requests\Admin\Legacy\Products\StoreAttributeRequest;
use App\Http\Requests\Admin\Legacy\Products\UpdateAttributeRequest;
use App\Models\Attribute;

class AttributeController extends Controller
{
    public function index()
    {
        $attributes = Attribute::all();

        return view('admin.attribute.index', compact('attributes'));
    }

    public function create()
    {
        return view('admin.attribute.create');
    }

    public function store(StoreAttributeRequest $request)
    {
        $validated = $request->validated();

        $attribute = new Attribute();
        $attribute->name = $validated['name'];
        $attribute->description = $validated['description'];
        $attribute->save();

        return redirect()->route('attribute.index')->with('message', 'Attribute added successfully.');
    }
    public function show(string $id)
    {
        //
    }

    public function edit($id)
    {
        $attribute = Attribute::findOrFail($id);

        return view('admin.attribute.edit', compact('attribute'));
    }

    public function update(UpdateAttributeRequest $request, $id)
    {
        $validated = $request->validated();

        $attribute = Attribute::findOrFail($id);

        $attribute->name = $validated['name'];
        $attribute->description = $validated['description'];

        $attribute->save();

        return redirect()->route('attribute.index')->with('message', 'Attribute updated successfully.');
    }
    public function destroy($id)
    {
        $attribute = Attribute::findOrFail($id);

        $attribute->delete();

        return redirect()->route('attribute.index')->with('message', 'Attribute deleted successfully.');
    }
}
