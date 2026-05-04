<?php

namespace App\Http\Requests\Admin\Legacy\Products;

use App\Http\Requests\BaseFormRequest;
use Illuminate\Support\Facades\Schema;

class StoreAttributeRequest extends BaseFormRequest
{
    public function rules(): array
    {
        $nameRules = ['required', 'string', 'max:255'];

        if (Schema::hasTable('attributes')) {
            $nameRules[] = 'unique:attributes,name';
        }

        return [
            'name' => $nameRules,
            'description' => ['required', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Vui lòng nhập tên thuộc tính.',
            'name.max' => 'Tên thuộc tính không được vượt quá 255 ký tự.',
            'name.unique' => 'Tên thuộc tính đã tồn tại.',
            'description.required' => 'Vui lòng nhập mô tả thuộc tính.',
        ];
    }
}
