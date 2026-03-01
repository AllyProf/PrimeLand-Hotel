<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SmsLog extends Model
{
    protected $fillable = [
        'recipient',
        'message',
        'status',
        'target_type',
        'target_id',
        'sender_id',
        'sms_count',
        'api_response'
    ];

    public function sender()
    {
        return $this->belongsTo(Staff::class, 'sender_id');
    }

    public function target()
    {
        if ($this->target_type === 'guest') {
            return $this->belongsTo(Guest::class, 'target_id');
        }
        return $this->belongsTo(Staff::class, 'target_id');
    }
}
