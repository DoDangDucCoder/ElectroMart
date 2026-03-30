<?php

namespace App\Http\Requests\Api\Admin;

use Illuminate\Foundation\Http\FormRequest;
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
            'price' => ['required', 'numeric', 'min:0'],
            'cost_price' => ['nullable', 'numeric', 'min:0'],
            'sale_type' => ['nullable', 'in:percent,fixed'],
            'sale_value' => ['nullable', 'numeric', 'min:0'],
            'sale_price' => ['nullable', 'numeric', 'min:0', 'lt:price'],
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
            'slug.regex' => 'Slug must contain only lowercase letters, numbers, and hyphens.',
            'sale_price.lt' => 'Sale price must be less than regular price.',
            'image.max' => 'Image size must not exceed 2MB.',
            'document.max' => 'Document size must not exceed 5MB.',
            'sale_type.in' => 'Sale type must be percent or fixed.',
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
            $saleType = $this->input('sale_type');
            $saleValue = $this->input('sale_value');

            if (! $saleType && ($saleValue === null || $saleValue === '')) {
                return;
            }

            if (! $saleType || $saleValue === null || $saleValue === '') {
                $validator->errors()->add('sale_value', 'Sale type và giá trị sale phải đi cùng nhau.');

                return;
            }

            $saleValue = (float) $saleValue;

            if ($saleType === 'percent' && $saleValue > 100) {
                $validator->errors()->add('sale_value', 'Sale phần trăm không được vượt quá 100.');
            }

            if ($saleType === 'fixed' && $saleValue >= $price) {
                $validator->errors()->add('sale_value', 'Sale số tiền phải nhỏ hơn giá bán gốc.');
            }
        });
    }
}
