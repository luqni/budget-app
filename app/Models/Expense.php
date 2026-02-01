<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    protected $fillable = ['user_id', 'note', 'amount', 'month', 'category_id', 'date', 'is_recurring', 'is_realized'];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    protected $casts = [
        'date' => 'date',
        'is_realized' => 'boolean',
    ];

    public function details()
    {
        return $this->hasMany(ExpenseDetail::class);
    }
}

