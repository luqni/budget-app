<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MonthlyIncome extends Model
{
    protected $fillable = ['user_id', 'month', 'income', 'is_recurring'];
}
