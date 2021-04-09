<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Favorite extends Model
{
    use HasFactory;
    protected $fillable=['villa_id','user_id'];
    protected $table='favorite_user';
    public $timestamps = false;

    public function users(){
        return $this->belongsToMany(User::class);
        //,'favorite_user','villa_id','user_id'
    }
}
