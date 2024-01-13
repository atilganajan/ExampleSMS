<?php

namespace App\Http\Controllers;

use App\Http\Requests\SendSmsRequest;
use App\Http\Requests\SmsReportsRequest;
use App\Http\Resources\SmsReportResource;
use App\Jobs\SendBulkSmsJob;
use App\Models\SmsReport;
use Illuminate\Support\Facades\Cache;

class SmsController extends Controller
{
    public function sendSms(SendSmsRequest $request)
    {
        try {
            $data = $request->only("message");
            $user = auth()->user();

            $user->messages()->create([
                'message' => $data['message'],
            ]);

            $smsCount = Cache::increment('sms_count', 1);

            if ($smsCount == 500) {
                dispatch(new SendBulkSmsJob());
                Cache::forever('sms_count', 0);
            }

            return response()->json([
                'success' => [
                    'code' => 200,
                    'message' => 'successful',
                ]
            ]);
        }catch (\Exception $e){
            return response()->json([
                'success' => [
                    'code' => 500,
                    'message' => 'Unexpected error'.$e->getMessage(),
                ]
            ], 500);
        }
    }

    public function getSmsReports(SmsReportsRequest $request)
    {
        try {
            $start = $request->input('start');
            $end = $request->input('end');

            $user = auth()->user();

            $query = $user->smsReports()->getQuery();

            if ($start && $end) {
                $query->whereBetween('send_time', [$start, $end]);
            }

            $smsReports = $query->get();

            return response()->json([
                'success' => [
                    'code' => 200,
                    'message' => 'successful',
                    'data' => SmsReportResource::collection($smsReports)
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => [
                    'code' => 500,
                    'message' => 'Unexpected error',
                ]
            ], 500);
        }
    }

    public function getSmsReportDetail(SmsReport $smsReport){
        return response()->json([
            'success' => [
                'code' => 200,
                'message' => 'successful',
                'data' => new SmsReportResource($smsReport)
            ]
        ]);
    }

}
