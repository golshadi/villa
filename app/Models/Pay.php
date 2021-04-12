<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pay extends Model
{
    use HasFactory;
    protected $fillable=['amount','authority','villa_reservation_id','user_reservation_id',
    'name','mobile','email','status','refid'];

    public function villa(){
        return $this->belongsTo(ReservedDate::class,'villa_reservation_id');
    }


    public function user(){
        return $this->belongsTo(Reservation::class,'user_reservation_id');
    }


}
