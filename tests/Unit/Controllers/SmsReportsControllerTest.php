<?php

namespace Tests\Unit\Controllers;

use App\Models\SmsReport;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SmsReportsControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_get_sms_reports()
    {

        $user = User::create([
            'username' => 'testuser',
            'password' => 'testpassword',
        ]);


        $this->actingAs($user);


        $smsReport = SmsReport::create([
            'user_id' => $user->id,
            'number' => 123456,
            'message' => 'test message',
            'send_time' => '2024-01-12 12:30:00',
        ]);

        $response = $this->json('GET', '/api/sms-reports');


        $response->assertStatus(200)
            ->assertJson([
                'success' => [
                    'code' => 200,
                    'message' => 'successful',
                    'data' => [
                        [
                            'id' => $smsReport->id,
                            'number' => $smsReport->number,
                            'message' => $smsReport->message,
                            'send_time' => $smsReport->send_time
                        ],
                    ],
                ],
            ]);
    }


    public function test_get_sms_reports_with_filter()
    {

        $user = User::create([
            'username' => 'testuser',
            'password' => 'testpassword',
        ]);


        $this->actingAs($user);


        $smsReport = SmsReport::create([
            'user_id' => $user->id,
            'number' => 123456,
            'message' => 'test message',
            'send_time' => '2024-01-12 12:30:00',
        ]);

        $smsReportSecond = SmsReport::create([
            'user_id' => $user->id,
            'number' => 123457,
            'message' => 'another test message',
            'send_time' => '2024-01-12 13:30:00',
        ]);


        $response = $this->json('GET', '/api/sms-reports', [
            'start' => '2024-01-12 12:30:00',
            'end' => '2024-01-12 13:00:00',
        ]);

        $response->assertJsonMissing([
            'user_id' => $smsReportSecond->user_id,
            'number' => $smsReportSecond->number,
            'message' => $smsReportSecond->message,
            'send_time' => $smsReportSecond->send_time,
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => [
                    'code' => 200,
                    'message' => 'successful',
                    'data' => [
                        [
                            'id' => $smsReport->id,
                            'number' => $smsReport->number,
                            'message' => $smsReport->message,
                            'send_time' => $smsReport->send_time
                        ],
                    ],
                ],
            ]);


    }


}
