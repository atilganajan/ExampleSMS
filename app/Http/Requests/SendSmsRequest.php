<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SendSmsRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            "message"=>"required|max:6000",
        ];
    }
}
