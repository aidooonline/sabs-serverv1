<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SmsLog extends Model
{
    protected $table = 'sms_logs';

    protected $fillable = [
        'comp_id',
        'customer_id',
        'phone_number',
        'message',
        'status',
        'type',
        'api_response'
    ];

    public function customer()
    {
        return $this->belongsTo(Accounts::class, 'customer_id');
    }
}
