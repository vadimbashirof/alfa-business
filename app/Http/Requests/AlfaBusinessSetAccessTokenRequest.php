<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AlfaBusinessSetAccessTokenRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'code' => 'required|string',
            'state' => 'required|string',
        ];
    }

    public function getCode(): string
    {
        return (string)$this->get('code');
    }

    public function getState(): string
    {
        return (string)$this->get('state');
    }
}
