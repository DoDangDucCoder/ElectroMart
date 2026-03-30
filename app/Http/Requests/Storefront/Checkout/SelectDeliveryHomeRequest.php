<?php

namespace App\Http\Requests\Storefront\Checkout;

use App\Http\Requests\BaseFormRequest;
use Illuminate\Validation\Rule;

class SelectDeliveryHomeRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'action' => ['required', 'string', Rule::in(['city', 'province'])],
            'ma_id' => ['required', 'integer', 'min:1', 'exists:locations,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'action.required' => 'Thiếu loại địa chỉ cần tải.',
            'action.in' => 'Loại địa chỉ cần tải không hợp lệ.',
            'ma_id.required' => 'Thiếu địa chỉ cha để tải danh sách.',
            'ma_id.integer' => 'Mã địa chỉ cha không hợp lệ.',
            'ma_id.min' => 'Mã địa chỉ cha không hợp lệ.',
            'ma_id.exists' => 'Địa chỉ cha đã chọn không tồn tại.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'action' => trim((string) $this->input('action')),
            'ma_id' => $this->input('ma_id'),
        ]);
    }
}
