<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SmsReportRequest extends FormRequest
{
    public function rules()
    {
        return [
            'start' => [
                'nullable',
                'date_format:Y-m-d H:i:s',
                function ($attribute, $value, $fail) {
                    if ($this->filled('start') && !$this->filled('end')) {
                        $fail('When start is present, end is required.');
                    }
                },
            ],
            'end' => [
                'nullable',
                'date_format:Y-m-d H:i:s',
                function ($attribute, $value, $fail) {
                    if ($this->filled('end') && !$this->filled('start')) {
                        $fail('When end is present, start is required.');
                    }
                },
            ],
        ];
    }
}
