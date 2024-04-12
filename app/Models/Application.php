<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Application extends Model
{
    use HasFactory;
    protected $fillable = ['user_id', 'job_id', 'status'];
    
    public function User()
    {
        return $this->belongsTo(User::class);
    }
}