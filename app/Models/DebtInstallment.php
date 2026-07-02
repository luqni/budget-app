<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DebtInstallment extends Model
{
    protected $fillable = [
        'debt_id',
        'amount',
        'payment_date',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'payment_date' => 'date',
    ];

    public function debt()
    {
        return $this->belongsTo(Debt::class);
    }
}
