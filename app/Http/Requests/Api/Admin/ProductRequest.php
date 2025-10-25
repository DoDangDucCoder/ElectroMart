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
            'sale_price' => ['nullable', 'numeric', 'min:0', 'lt:price'],
            'cost_price' => ['nullable', 'numeric', 'min:0'],
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
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('tags') && is_string($this->tags)) {
            $this->merge([
                'tags' => array_filter(array_map('trim', explode(',', $this->tags))),
            ]);
        }
    }
}
