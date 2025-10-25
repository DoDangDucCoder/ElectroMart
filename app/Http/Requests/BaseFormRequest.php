<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

abstract class BaseFormRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * Override in child class when authorization logic is required.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Default validation rules.
     *
     * Child class must implement its own rules.
     */
    abstract public function rules(): array;

    /**
     * Shared messages stub to keep responses consistent.
     */
    public function messages(): array
    {
        return [];
    }
}
