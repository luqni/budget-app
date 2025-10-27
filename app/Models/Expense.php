<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    protected $fillable = ['user_id', 'note', 'amount', 'date', 'month'];

    protected $casts = [
        'date' => 'date',
    ];
}

