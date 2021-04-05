<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;
    protected $guarded=['id'];
    protected $table='user_transactions';

    public function user(){
        return $this->belongsTo(User::class);
    }
}
