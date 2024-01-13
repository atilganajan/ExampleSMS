<?php

namespace Tests\Unit\Requests;

use App\Http\Requests\SendSmsRequest;
use App\Http\Requests\SmsRequest;
use Illuminate\Support\Str;
use Tests\TestCase;

class SendRequestSmsTest extends TestCase
{
    public function test_message_request_validation_passes()
    {
        $request = new SendSmsRequest([
            'message' => 'test message',
        ]);

        $validator = $this->app['validator']->make($request->all(), $request->rules());

        $this->assertFalse($validator->fails());
    }

    public function test_message_request_validation_fails_on_missing_message()
    {
        $request = new SendSmsRequest([
            'message' => null,
        ]);

        $validator = $this->app['validator']->make($request->all(), $request->rules());

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('message', $validator->errors()->toArray());
    }

    public function test_message_request_validation_fails_on_long_message()
    {
        $request = new SendSmsRequest([
            'message' => Str::random(6001),
        ]);

        $validator = $this->app['validator']->make($request->all(), $request->rules());

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('message', $validator->errors()->toArray());
    }




}
