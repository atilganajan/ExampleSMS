<?php

namespace App\Http\Controllers;

use App\Http\Requests\SendSmsRequest;
use App\Http\Requests\SmsRequest;
use App\Http\Resources\SmsReportResource;
use App\Jobs\SendBulkSmsJob;
use App\Models\SmsReport;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class SmsController extends Controller
{


    /**
     * @OA\Post(
     *     path="/api/send-sms",
     *     summary="Send SMS",
     *     description="Endpoint to send SMS messages.",
     *     operationId="sendSms",
     *     tags={"SMS"},
     *     security={
     *         {"bearerAuth": {}}
     *     },
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             type="object",
     *             required={"message"},
     *             properties={
     *                 @OA\Property(property="message", type="string", example="Your SMS message content.")
     *             }
     *         )
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="object",
     *                 @OA\Property(property="code", type="integer", example=200),
     *                 @OA\Property(property="message", type="string", example="successful")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Unauthorized")
     *         )
     *     ),
     *     @OA\Response(
     *         response="500",
     *         description="Unexpected error",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="object",
     *                 @OA\Property(property="code", type="integer", example=500),
     *                 @OA\Property(property="message", type="string", example="Unexpected error")
     *             )
     *         )
     *     ),
     *     security={
     *         {"bearerAuth": {}}
     *     }
     * )
     */


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
            Log::error("File: " . $e->getFile() . " Line: " . $e->getLine() . " Error: " . $e->getMessage());
            return response()->json([
                'error' => [
                    'code' => 500,
                    'message' => 'Unexpected error',
                ]
            ], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/sms-reports",
     *     summary="sms-reports",
     *     description="Endpoint to send SMS messages.",
     *     operationId="sms-reports",
     *     tags={"SMS"},
     *     security={
     *         {"bearerAuth": {}}
     *     },
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             type="object",
     *             required={"message"},
     *             properties={
     *                 @OA\Property(property="start", type="date", example="2024-01-13 01:53:20"),
     *                 @OA\Property(property="end", type="date", example="2024-01-13 01:53:50")
     *             }
     *         )
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="object",
     *                 @OA\Property(property="code", type="integer", example=200),
     *                 @OA\Property(property="message", type="string", example="successful"),
     *     @OA\Property(property="data", type="array",
     *                     @OA\Items(
     *                         @OA\Property(property="id", type="integer", example=1),
     *                         @OA\Property(property="user_id", type="integer", example=1),
     *                         @OA\Property(property="message_id", type="integer", example=1),
     *                         @OA\Property(property="number", type="string", example=123),
     *                         @OA\Property(property="message", type="string", example="test message"),
     *                         @OA\Property(property="send_time", type="string", example="2024-01-13 01:53:20")
     *                     )
     *                 )
     *
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Unauthorized")
     *         )
     *     ),
     *     @OA\Response(
     *         response="500",
     *         description="Unexpected error",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="object",
     *                 @OA\Property(property="code", type="integer", example=500),
     *                 @OA\Property(property="message", type="string", example="Unexpected error")
     *             )
     *         )
     *     ),
     *     security={
     *         {"bearerAuth": {}}
     *     }
     * )
     */

    public function getSmsReports(SmsRequest $request)
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
            Log::error("File: " . $e->getFile() . " Line: " . $e->getLine() . " Error: " . $e->getMessage());
            return response()->json([
                'error' => [
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
