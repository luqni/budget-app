<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = ['user_id', 'name', 'icon', 'color', 'budget_limit'];
    
    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }
    
    public function expenses()
    {
        return $this->hasMany(\App\Models\Expense::class);
    }
}
