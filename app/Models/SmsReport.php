<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SmsReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'message_id',
        'number',
        'message',
        'send_time',
    ];

    protected $guarded = [
        'user_id',
        'message_id',
        'send_time',
    ];

    public $timestamps = false;


    public function user(){
      return  $this->belongsTo(User::class);
    }

}
