<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReservedDate extends Model
{
    use HasFactory;
    protected $guarded=['id'];
    protected $table='villa_reservation';

    public function villa(){
        return $this->belongsTo(Villa::class);
    }

}
