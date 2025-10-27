<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Income extends Model {
    use HasFactory;
    protected $fillable = ['user_id','date','title','amount','note'];

    public function user() {
        return $this->belongsTo(User::class);
    }
}
