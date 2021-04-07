<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Detail extends Model
{
    use HasFactory;
    protected $table='villa_details';
    protected $guarded=['id'];

    public function villa(){
        return $this->belongsTo(Villa::class);
    }
}
