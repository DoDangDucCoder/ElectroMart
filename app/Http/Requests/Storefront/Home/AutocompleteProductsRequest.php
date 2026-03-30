<?php

namespace App\Http\Requests\Storefront\Home;

use App\Http\Requests\BaseFormRequest;

class AutocompleteProductsRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'query' => ['nullable', 'string', 'max:100'],
        ];
    }

    public function messages(): array
    {
        return [
            'query.max' => 'Từ khóa gợi ý không được vượt quá 100 ký tự.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'query' => $this->normalizeNullableString($this->input('query')),
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
}
