<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rule extends Model
{
    use HasFactory;
    protected $table='villa_rules';
    
    public function villa(){
        return $this->belongsTo(Villa::class);
    }
}
