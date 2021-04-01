<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Villa extends Model
{
    use HasFactory;

    public function detail(){
        return $this->hasOne(Detail::class);
    }
    public function rule(){
        return $this->hasOne(Rule::class);
    }
}
