<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SmsReportResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'user_id'=>$this->user_id,
            'message_id'=>$this->message_id,
            'number' => $this->number,
            'message' => $this->message,
            'send_time' => $this->send_time,
        ];
    }
}
