<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'username' => 'required|min:6|max:20',
            'password' =>'required|string|min:6|max:20'
        ];
    }
}
