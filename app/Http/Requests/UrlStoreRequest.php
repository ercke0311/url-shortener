<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UrlStoreRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'long_url'   => ['required', 'url', 'max:2048'],
            'expired_at' => ['nullable', 'date', 'after:now'],
        ];
    }
}