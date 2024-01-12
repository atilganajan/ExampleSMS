<?php

namespace Tests\Unit\Requests;

use App\Http\Requests\SmsReportsRequest;
use App\Models\User;
use Tests\TestCase;

class SmsReportsRequestTest extends TestCase
{
    public function test_sms_reports_request_validation_passes()
    {
        $request = new SmsReportsRequest([
            'start' => '2024-01-12 12:00:00',
            'end' => '2024-01-12 13:00:00',
        ]);

        $validator = $this->app['validator']->make($request->all(), $request->rules());

        $this->assertFalse($validator->fails());
    }

    public function test_sms_reports_request_validation_without_filter_passes()
    {
        $request = new SmsReportsRequest([
            'start' => null,
            'end' => null,
        ]);

        $validator = $this->app['validator']->make($request->all(), $request->rules());

        $this->assertFalse($validator->fails());
    }

    public function test_sms_reports_request_validation_fails_on_missing_start()
    {
        $request = new SmsReportsRequest([
            'end' => '2024-01-12 13:00:00',
        ]);

        $validator = $this->app['validator']->make($request->all(), $request->rules());

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('end', $validator->errors()->toArray());
    }

    public function test_sms_reports_request_validation_fails_on_missing_end()
    {
        $request = new SmsReportsRequest([
            'start' => '2024-01-12 14:00:00',
        ]);

        $validator = $this->app['validator']->make($request->all(), $request->rules());

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('start', $validator->errors()->toArray());
    }

    public function test_sms_reports_request_validation_fails_on_invalid_start()
    {
        $request = new SmsReportsRequest([
            'start' => '2024-01-12 14:00',
            'end' => '2024-01-12 14:00:00',
        ]);

        $validator = $this->app['validator']->make($request->all(), $request->rules());

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('start', $validator->errors()->toArray());
    }


    public function test_sms_reports_request_validation_fails_on_invalid_end()
    {
        $request = new SmsReportsRequest([
            'start' => '2024-01-12 14:00',
            'end' => '2024-01-12 14:00',
        ]);

        $validator = $this->app['validator']->make($request->all(), $request->rules());

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('end', $validator->errors()->toArray());
    }



}
