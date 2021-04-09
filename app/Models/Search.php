<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Search extends Model
{
    use HasFactory;
    protected $guarded=['id'];

    public function villa(){
        return $this->belongsTo(Villa::class);
    }

    
    public function detail()
    {
        return $this->hasOne(Detail::class,'villa_id','villa_id');
    }

    public function rule()
    {
        return $this->hasOne(Rule::class,'villa_id','villa_id');
    }
  
}
