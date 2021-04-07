<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Withdrawal extends Model
{
    use HasFactory;
    protected $guarded=['id'];
    protected $table='user_withdrawal_requests';

    public function user(){
        return $this->belongsTo(User::class);
    }
}
