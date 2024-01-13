<?php

namespace App\Http\Controllers;

use App\Http\Requests\SendSmsRequest;
use App\Http\Requests\SmsReportRequest;
use App\Http\Resources\SmsReportResource;
use App\Jobs\SendBulkSmsJob;
use App\Models\SmsReport;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class SmsController extends Controller
{


    /**
     * @OA\Post(
     *     path="/api/send-sms",
     *     summary="Send sms",
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

    /**
     * @OA\GET(
     *     path="/api/sms-reports",
     *     summary=" Get sms reports",
     *     description="Endpoint to send SMS messages.",
     *     operationId="sms-reports",
     *     tags={"SMS"},
     *     security={
     *         {"bearerAuth": {}}
     *     },
     *     @OA\Parameter(
     *         name="start",
     *         in="query",
     *         description="Start parameter (2024-01-13 01:53:20)",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="end",
     *         in="query",
     *         description="End parameter (2024-01-14 01:53:20)",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *
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

    public function getSmsReports(SmsReportRequest $request)
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


    /**
     * @OA\GET(
     *     path="/api/sms-reports/{id}",
     *     summary="Get sms report detail",
     *     description="Endpoint to send SMS messages.",
     *     operationId="sms-report-detail",
     *     tags={"SMS"},
     *     security={
     *         {"bearerAuth": {}}
     *     },
     *     @OA\Parameter(
     *          name="id",
     *          description="ID of the SMS report",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     * @OA\Response(
     *      response=200,
     *      description="Successful operation",
     *      @OA\JsonContent(
     *          @OA\Property(property="success", type="object",
     *              @OA\Property(property="code", type="integer"),
     *              @OA\Property(property="message", type="string"),
     *              @OA\Property(property="data", type="object",
     *                         @OA\Property(property="id", type="integer", example=1),
     *                         @OA\Property(property="user_id", type="integer", example=1),
     *                         @OA\Property(property="message_id", type="integer", example=1),
     *                         @OA\Property(property="number", type="string", example=123),
     *                         @OA\Property(property="message", type="string", example="test message"),
     *                         @OA\Property(property="send_time", type="string", example="2024-01-13 01:53:20")
     *              )
     *          )
     *      )
     * ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Unauthorized")
     *         )
     *     ),
     *     @OA\Response(
     *         response="404",
     *         description="Unexpected error",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="object",
     *                 @OA\Property(property="code", type="integer", example=404),
     *                 @OA\Property(property="message", type="string", example="Not Found")
     *             )
     *         )
     *     ),
     *     security={
     *         {"bearerAuth": {}}
     *     }
     * )
     */


    public function getSmsReportDetail(Request $smsReport,$id)
    {
        try {
            $user = auth()->user();

            $smsReport = $user->smsReports()->findOrFail($id);

            return response()->json([
                'success' => [
                    'code' => 200,
                    'message' => 'successful',
                    'data' => new SmsReportResource($smsReport)
                ]
            ]);
        }catch (ModelNotFoundException $e){
            return response()->json([
                'error' => [
                    'code' => 404,
                    'message' => 'Unexpected error',
                ]
            ], 404);
        }
    }

}
