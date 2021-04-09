<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Villa extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function detail()
    {
        return $this->hasOne(Detail::class);
    }

    public function rule()
    {
        return $this->hasOne(Rule::class);
    }

    public function images()
    {
        return $this->hasMany(Image::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function info()
    {
        return $this->hasOne(Info::class);
    }

    public function comments(){
        return $this->hasMany(Comment::class);
    }

    public function dates(){
        return $this->hasMany(Date::class);
    }
    
    public function reserves(){
        return $this->hasMany(ReservedDate::class);
    }

    public function searches()
    {
        return $this->hasMany(Search::class);
    }
 

}
