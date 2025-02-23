<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobOpportunity extends Model
{
    use HasFactory;
    protected $fillable = ['company_id', 'opp_name', 'status'];

    public function company()
    {
        return $this->belongsTo(company::class);
    }
}