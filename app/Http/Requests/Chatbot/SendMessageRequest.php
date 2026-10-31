<?php

namespace App\Http\Requests\Chatbot;

use Illuminate\Foundation\Http\FormRequest;

class SendMessageRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'conversation_id' => ['nullable', 'integer', 'exists:chat_conversations,id'],
            'message' => ['required', 'string', 'max:2000'],
            'language' => ['nullable', 'string', 'in:en,bn'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
        ];
    }

    /**
     * Custom messages for validation
     */
    public function messages(): array
    {
        return [
            'message.required' => 'Message text cannot be empty.',
            'message.max' => 'Message is too long. Please keep under 2000 characters.',
        ];
    }
}
