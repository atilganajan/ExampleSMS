<?php

namespace Tests\Unit\Requests;

use App\Http\Requests\SmsRequest;
use App\Models\User;
use Tests\TestCase;

class SmsRequestTest extends TestCase
{
    public function test_sms_reports_request_validation_passes()
    {
        $request = new SmsRequest([
            'start' => '2024-01-12 12:00:00',
            'end' => '2024-01-12 13:00:00',
        ]);

        $validator = $this->app['validator']->make($request->all(), $request->rules());

        $this->assertFalse($validator->fails());
    }

    public function test_sms_reports_request_validation_without_filter_passes()
    {
        $request = new SmsRequest([
            'start' => null,
            'end' => null,
        ]);

        $validator = $this->app['validator']->make($request->all(), $request->rules());

        $this->assertFalse($validator->fails());
    }

    public function test_sms_reports_request_validation_fails_on_missing_start()
    {
        $request = new SmsRequest([
            'end' => '2024-01-12 13:00:00',
        ]);

        $validator = $this->app['validator']->make($request->all(), $request->rules());

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('end', $validator->errors()->toArray());
    }

    public function test_sms_reports_request_validation_fails_on_missing_end()
    {
        $request = new SmsRequest([
            'start' => '2024-01-12 14:00:00',
        ]);

        $validator = $this->app['validator']->make($request->all(), $request->rules());

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('start', $validator->errors()->toArray());
    }

    public function test_sms_reports_request_validation_fails_on_invalid_start()
    {
        $request = new SmsRequest([
            'start' => '2024-01-12 14:00',
            'end' => '2024-01-12 14:00:00',
        ]);

        $validator = $this->app['validator']->make($request->all(), $request->rules());

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('start', $validator->errors()->toArray());
    }


    public function test_sms_reports_request_validation_fails_on_invalid_end()
    {
        $request = new SmsRequest([
            'start' => '2024-01-12 14:00',
            'end' => '2024-01-12 14:00',
        ]);

        $validator = $this->app['validator']->make($request->all(), $request->rules());

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('end', $validator->errors()->toArray());
    }



}
