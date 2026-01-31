<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RecurringLog extends Model
{
    protected $fillable = ['source_expense_id', 'target_month'];
}
