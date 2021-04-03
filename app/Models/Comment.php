<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;
    protected $guarded=['id'];
    protected $table='villa_comments';

    public function villa(){
        return $this->belongsTo(Villa::class);
    }

    public function user(){
        return $this->belongsTo(User::class);
    }

}
