<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'username' => 'required|unique:users,username|min:6|max:20',
            'password' =>'required|string|min:6|max:20'
        ];
    }
}
