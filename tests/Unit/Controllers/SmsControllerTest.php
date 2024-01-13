<?php

namespace Tests\Unit\Controllers;

use App\Http\Requests\SendSmsRequest;
use App\Jobs\SendBulkSmsJob;
use App\Models\Message;
use App\Models\SmsReport;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Queue\Queue;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class SmsControllerTest extends TestCase
{
    use RefreshDatabase;


    public function test_send_sms_passes()
    {
        $user = User::create([
            'username' => 'testuser',
            'password' => 'testpassword',
        ]);

        $this->actingAs($user);

        $message = 'Test message';
        $response = $this->postJson('/api/send-sms', ['message' => $message]);


        $response->assertStatus(200);
        $response->assertJson(['success' => ['code' => 200, 'message' => 'successful']]);

        $this->assertDatabaseHas('messages', ['user_id' => $user->id, 'message' => $message]);

        $this->assertEquals(1, Cache::get('sms_count'));

        Bus::fake();

        Bus::assertNotDispatched(SendBulkSmsJob::class);

        for ($i = 1; $i <= 498; $i++) {
           $this->postJson('/api/send-sms', ['message' => 'Test message']);
        }

        Bus::assertNotDispatched(SendBulkSmsJob::class);

        $this->postJson('/api/send-sms', ['message' => 'Test message 500']);

        Bus::assertDispatched(SendBulkSmsJob::class);
        $this->assertEquals(0, Cache::get('sms_count'));
    }

    public function test_sendSms_unexpectedError()
    {
        $user = User::create([
            'username' => 'testuser',
            'password' => 'testpassword',
        ]);

        $this->actingAs($user);

        $this->mock(SendSmsRequest::class, function ($mock) {
            $mock->shouldReceive('only')->once()->andThrow(new \Exception('Test exception'));
        });


        $response = $this->postJson('/api/send-sms', ['message' => 'Test message']);

        $response->assertStatus(500);

        $response->assertJson(['error' => ['code' => 500, 'message' => 'Unexpected error']]);
    }

    public function test_get_sms_reports()
    {

        $user = User::create([
            'username' => 'testuser',
            'password' => 'testpassword',
        ]);


        $this->actingAs($user);

        $message = $user->messages()->create([
            'message' => 'test message',
        ]);

        $smsReport = $message->smsReport()->create([
            'user_id' => $user->id,
            'number' => 123456,
            'message' => $message->message,
            'send_time' => '2024-01-12 12:30:00',
        ]);


        $response = $this->getJson('/api/sms-reports');


        $response->assertStatus(200)
            ->assertJson([
                'success' => [
                    'code' => 200,
                    'message' => 'successful',
                    'data' => [
                        [
                            'id' => $smsReport->id,
                            'user_id'=>$smsReport->user_id,
                            'message_id'=>$smsReport->message_id,
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



        $message = $user->messages()->create([
            'message' => 'test message',
        ]);

        $smsReport = $message->smsReport()->create([
            'user_id' => $user->id,
            'number' => 123456,
            'message' => $message->message,
            'send_time' => '2024-01-12 12:30:00',
        ]);


        $messageSecond = $user->messages()->create([
            'message' => 'test message second',
        ]);

        $smsReportSecond = $messageSecond->smsReport()->create([
            'user_id' => $user->id,
            'number' => 1234567,
            'message' => $messageSecond->message,
            'send_time' => '2024-01-12 13:30:00',
        ]);


        $response =$this->json('GET', '/api/sms-reports', [
            'start' => '2024-01-12 12:30:00',
            'end' => '2024-01-12 13:00:00',
        ]);

        $response->assertJsonMissing([
            'id' => $smsReportSecond->id,
            'message_id'=>$smsReportSecond->message_id,
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
                            'user_id'=>$smsReport->user_id,
                            'message_id'=>$smsReport->message_id,
                            'number' => $smsReport->number,
                            'message' => $smsReport->message,
                            'send_time' => $smsReport->send_time
                        ],
                    ],
                ],
            ]);
    }

    public function test_get_sms_report_detail_not_found(){

        $user = User::create([
            'username' => 'testuser',
            'password' => 'testpassword',
        ]);

        $this->actingAs($user);

        $response = $this->getJson( '/api/sms-reports/9999');

        $response->assertStatus(404);
    }

    public function test_get_sms_report_detail_passes(){

        $user = User::create([
            'username' => 'testuser',
            'password' => 'testpassword',
        ]);

        $this->actingAs($user);

        $message = $user->messages()->create([
            'message' => 'test message',
        ]);

        $smsReport = $message->smsReport()->create([
            'user_id' => $user->id,
            'number' => 123456,
            'message' => $message->message,
            'send_time' => '2024-01-12 12:30:00',
        ]);

        $response = $this->getJson( '/api/sms-reports/'.$smsReport->id);

        $response->assertStatus(200)
            ->assertJson([
                'success' => [
                    'code' => 200,
                    'message' => 'successful',
                    'data' => [
                        'id' => $smsReport->id,
                        'number' => $smsReport->number,
                        'message' => $smsReport->message,
                        'send_time' => $smsReport->send_time
                    ],
                ],
            ]);
    }


}
