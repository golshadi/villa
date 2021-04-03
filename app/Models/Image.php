<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    use HasFactory;
    protected $table='villa_images';

    public function villa(){
        return $this->belongsTo(Villa::class);
    }
}
