<?php

namespace App\Http\Requests\Storefront\Checkout;

use App\Http\Requests\BaseFormRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class SelectDeliveryHomeRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'action' => ['required', 'string', Rule::in(['city', 'province', 'vietnam_province'])],
            'ma_id' => $this->parentLocationRules(),
        ];
    }

    public function messages(): array
    {
        return [
            'action.required' => 'Thiếu loại địa chỉ cần tải.',
            'action.in' => 'Loại địa chỉ cần tải không hợp lệ.',
            'ma_id.required' => 'Thiếu địa chỉ cha để tải danh sách.',
            'ma_id.exists' => 'Địa chỉ cha đã chọn không tồn tại.',
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            if ($validator->errors()->isNotEmpty()) {
                return;
            }

            $table = $this->input('action') === 'vietnam_province' ? 'vietnam_provinces' : 'locations';
            if (! Schema::hasTable($table)) {
                return;
            }

            $exists = DB::table($table)->where('id', $this->input('ma_id'))->exists()
                || DB::table($table)->where('code', $this->input('ma_id'))->exists();

            if (! $exists) {
                $validator->errors()->add('ma_id', 'Địa chỉ cha đã chọn không tồn tại.');
            }
        });
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'action' => trim((string) $this->input('action')),
            'ma_id' => $this->normalizeNullableString($this->input('ma_id')),
        ]);
    }

    protected function normalizeNullableString(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }

    protected function parentLocationRules(): array
    {
        return ['required', 'string', 'max:20'];
    }
}
