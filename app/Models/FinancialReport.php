<?php

namespace App\Models;

use Hekmatinasser\Verta\Facades\Verta;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FinancialReport extends Model
{
    use HasFactory;
    protected $guarded=['id'];
    protected $table='user_financial_statements';

    public function getDateAttribute($value)
    {
        return Verta::instance($value)->format('Y/n/j');
    }

    public function setDateAttribute($value)
    {
        $this->attributes['date']= convertToGregorian($value);
    }

    public function user(){
        return $this->belongsTo(User::class);
    }

}
