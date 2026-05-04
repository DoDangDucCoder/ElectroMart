<?php

namespace App\Http\Requests\Chatbot;

use App\Http\Requests\BaseFormRequest;

class StoreChatMessageRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'message' => ['required', 'string', 'max:500'],
            'session_token' => ['nullable', 'string', 'max:120'],
        ];
    }

    public function messages(): array
    {
        return [
            'message.required' => 'Vui lòng nhập nội dung cần hỗ trợ.',
            'message.max' => 'Tin nhắn không được vượt quá 500 ký tự.',
        ];
    }
}
