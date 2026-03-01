<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shift extends Model
{
    use HasFactory;

    protected $fillable = [
        'staff_id',
        'opened_at',
        'closed_at',
        'opening_cash',
        'closing_cash_actual',
        'closing_cash_expected',
        'total_mpesa_expected',
        'total_mobile_expected',
        'total_card_expected',
        'total_bank_expected',
        'total_online_expected',
        'notes',
        'status',
    ];

    protected $casts = [
        'opened_at' => 'datetime',
        'closed_at' => 'datetime',
        'opening_cash' => 'decimal:2',
        'closing_cash_actual' => 'decimal:2',
        'closing_cash_expected' => 'decimal:2',
        'total_mpesa_expected' => 'decimal:2',
        'total_mobile_expected' => 'decimal:2',
        'total_card_expected' => 'decimal:2',
        'total_bank_expected' => 'decimal:2',
        'total_online_expected' => 'decimal:2',
    ];

    public function staff()
    {
        return $this->belongsTo(Staff::class, 'staff_id');
    }
}
