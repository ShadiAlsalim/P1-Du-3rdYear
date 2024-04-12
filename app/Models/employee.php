<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class employee extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id'  
    ];

    public function user(){  // telling phone model that he's related to user table
        return $this->belongsTo(User::class);
    }

}
