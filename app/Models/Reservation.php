<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    use HasFactory;
    protected $guarded=['id'];
    protected $table='user_reservations';

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function villa(){
        return $this->belongsTo(Villa::class);
    }

    
    public function pay()
    {
        return $this->hasOne(Pay::class);
    }

    
}