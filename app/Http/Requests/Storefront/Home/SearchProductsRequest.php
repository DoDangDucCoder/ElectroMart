<?php

namespace App\Http\Requests\Storefront\Home;

use App\Http\Requests\BaseFormRequest;

class SearchProductsRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'keywords_submit' => ['nullable', 'string', 'max:100'],
            'product_code' => ['nullable', 'string', 'max:100'],
            'category_id' => ['nullable', 'integer', 'exists:product_categories,id'],
            'price_min' => ['nullable', 'numeric', 'min:0'],
            'price_max' => ['nullable', 'numeric', 'min:0'],
            'sort' => ['nullable', 'in:popular,price_asc,price_desc,name_asc,name_desc'],
        ];
    }

    public function messages(): array
    {
        return [
            'keywords_submit.max' => 'Từ khóa tìm kiếm không được vượt quá 100 ký tự.',
            'product_code.max' => 'Mã sản phẩm không được vượt quá 100 ký tự.',
            'category_id.exists' => 'Danh mục tìm kiếm không hợp lệ.',
            'price_min.numeric' => 'Giá từ phải là số hợp lệ.',
            'price_max.numeric' => 'Giá đến phải là số hợp lệ.',
            'price_min.min' => 'Giá từ không được nhỏ hơn 0.',
            'price_max.min' => 'Giá đến không được nhỏ hơn 0.',
            'sort.in' => 'Kiểu sắp xếp không hợp lệ.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'keywords_submit' => $this->normalizeNullableString($this->input('keywords_submit')),
            'product_code' => $this->normalizeNullableString($this->input('product_code')),
            'category_id' => $this->normalizeNullableInteger($this->input('category_id')),
            'price_min' => $this->normalizeNullableNumber($this->input('price_min')),
            'price_max' => $this->normalizeNullableNumber($this->input('price_max')),
            'sort' => $this->normalizeNullableString($this->input('sort')),
        ]);
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $min = $this->input('price_min');
            $max = $this->input('price_max');

            if ($min !== null && $max !== null && (float) $max < (float) $min) {
                $validator->errors()->add('price_max', 'Giá đến phải lớn hơn hoặc bằng giá từ.');
            }
        });
    }

    protected function normalizeNullableString(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }

    protected function normalizeNullableInteger(mixed $value): ?int
    {
        if ($value === null || $value === '') {
            return null;
        }

        return is_numeric($value) ? (int) $value : null;
    }

    protected function normalizeNullableNumber(mixed $value): ?float
    {
        if ($value === null || $value === '') {
            return null;
        }

        $normalized = preg_replace('/[^0-9.,]/', '', (string) $value);

        if ($normalized === '') {
            return null;
        }

        if (str_contains($normalized, ',') && str_contains($normalized, '.')) {
            $normalized = str_replace('.', '', $normalized);
            $normalized = str_replace(',', '.', $normalized);
        } elseif (substr_count($normalized, '.') > 1) {
            $normalized = str_replace('.', '', $normalized);
        } elseif (substr_count($normalized, ',') > 1) {
            $normalized = str_replace(',', '', $normalized);
        } else {
            $normalized = str_replace(',', '.', $normalized);
        }

        return $normalized === '' ? null : (float) $normalized;
    }
}
