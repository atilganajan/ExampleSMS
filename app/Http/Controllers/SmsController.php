<?php

namespace App\Http\Controllers;

use App\Http\Requests\SmsReportsRequest;
use App\Http\Resources\SmsReportResource;
use Illuminate\Http\Request;

class SmsController extends Controller
{
    public function sendSms(Request $request)
    {
        $data = $request->only("message");

        $user = auth()->user();

        $smsReport = $user->smsReports()->create([
            "message" => $data["message"],
            "number" => rand(1, 100),
        ]);

        return response()->json("success");

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

}
