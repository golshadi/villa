<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Info extends Model
{
    use HasFactory;
    protected $table='villa_info';

    public function villa(){
        return $this->belongsTo(Villa::class);
    }
    
}
