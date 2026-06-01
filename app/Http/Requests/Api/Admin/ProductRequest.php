<?php

namespace App\Http\Requests\Api\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $productId = $this->route('product') ?? $this->route('id');

        return [
            'name' => ['required', 'string', 'max:255'],
            'slug' => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-z0-9-]+$/',
                Rule::unique('products', 'slug')->ignore($productId),
            ],
            'sku' => [
                'nullable',
                'string',
                'max:100',
                Rule::unique('products', 'sku')->ignore($productId),
            ],
            'barcode' => [
                'nullable',
                'string',
                'max:50',
                Rule::unique('products', 'barcode')->ignore($productId),
            ],
            'unit' => ['nullable', 'string', 'max:20'],
            'price' => ['required', 'numeric', 'gt:0'],
            'cost_price' => ['required', 'numeric', 'gt:0'],
            'sale_type' => ['nullable', 'in:percent,fixed'],
            'sale_value' => ['nullable', 'numeric', 'min:0'],
            'sale_price' => ['nullable', 'numeric', 'min:0'],
            'stock_quantity' => ['required', 'integer', 'min:0'],
            'stock_alert_threshold' => ['required', 'integer', 'min:0'],
            'is_active' => ['boolean'],
            'is_featured' => ['boolean'],
            'display_order' => ['integer', 'min:0'],
            'description' => ['nullable', 'string'],
            'content' => ['nullable', 'string'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:500'],
            'brand_id' => ['nullable', 'exists:brands,id'],
            'primary_category_id' => ['nullable', 'exists:product_categories,id'],
            'category_ids' => ['nullable', 'array'],
            'category_ids.*' => ['exists:product_categories,id'],
            'tags' => ['nullable', 'array'],
            'tags.*' => ['string', 'max:50'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:2048'],
            'document' => ['nullable', 'file', 'mimes:pdf,doc,docx,txt', 'max:5120'],
        ];
    }

    public function messages(): array
    {
        return [
            'slug.regex' => 'Đường dẫn chỉ được chứa chữ thường không dấu, số và dấu gạch ngang.',
            'sale_price.lt' => 'Giá khuyến mãi phải nhỏ hơn giá bán gốc.',
            'image.max' => 'Kích thước hình ảnh không được vượt quá 2MB.',
            'document.max' => 'Kích thước tài liệu không được vượt quá 5MB.',
            'sale_type.in' => 'Kiểu khuyến mãi phải là phần trăm hoặc số tiền cố định.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $saleType = $this->input('sale_type');
        $saleValue = $this->input('sale_value');

        if ($this->has('tags') && is_string($this->tags)) {
            $this->merge([
                'tags' => array_filter(array_map('trim', explode(',', $this->tags))),
            ]);
        }

        if (! $this->filled('slug') && $this->filled('name')) {
            $this->merge([
                'slug' => Str::slug((string) $this->input('name')),
            ]);
        }

        if ($saleType === '' || $saleType === 'none') {
            $saleType = null;
        }

        if (is_string($saleValue)) {
            $normalized = preg_replace('/[^0-9]/', '', $saleValue);
            $saleValue = $normalized === '' ? null : (float) $normalized;
        }

        $this->merge([
            'sale_type' => $saleType,
            'sale_value' => $saleValue,
        ]);
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $price = (float) $this->input('price', 0);
            $costPrice = (float) $this->input('cost_price', 0);
            $saleType = $this->input('sale_type');
            $saleValue = $this->input('sale_value');

            if (! $saleType && ($saleValue === null || $saleValue === '' || (float) $saleValue <= 0)) {
                if ($costPrice >= $price) {
                    $validator->errors()->add('cost_price', 'Giá nhập phải nhỏ hơn giá bán để sản phẩm có lãi.');
                }

                return;
            }

            if (! $saleType || $saleValue === null || $saleValue === '') {
                $validator->errors()->add('sale_value', 'Kiểu khuyến mãi và giá trị khuyến mãi phải đi cùng nhau.');

                return;
            }

            $saleValue = (float) $saleValue;

            if ($saleType === 'percent' && $saleValue > 100) {
                $validator->errors()->add('sale_value', 'Khuyến mãi theo phần trăm không được vượt quá 100.');
            }

            if ($saleType === 'fixed' && $saleValue >= $price) {
                $validator->errors()->add('sale_value', 'Số tiền khuyến mãi phải nhỏ hơn giá bán gốc.');
            }

            $finalPrice = $saleType === 'percent'
                ? max(0, $price - (($price * min(100, $saleValue)) / 100))
                : max(0, $price - $saleValue);

            if ($costPrice >= $finalPrice) {
                $validator->errors()->add('cost_price', 'Giá nhập phải nhỏ hơn giá sau sale để sản phẩm có lãi.');
            }
        });
    }
}
