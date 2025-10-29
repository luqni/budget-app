<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExpenseDetail extends Model
{
    protected $fillable = ['expense_id', 'name', 'qty', 'price'];

    public function expense()
    {
        return $this->belongsTo(Expense::class);
    }
}
