<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Verta;

class User extends Authenticatable
{
    use HasApiTokens,HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = [
        'id',
        'isAdmin'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function villas(){
        return $this->hasMany(Villa::class);
    }
    
    public function comments(){
        return $this->hasMany(Comment::class);
    }

    public function reservations(){
        return $this->hasMany(Reservation::class);
    }
    public function transactions(){
        return $this->hasMany(Transaction::class);
    }

    public function requestedReserves(){
        return $this->hasMany(ReservedDate::class);
    }
    public function withdrawals(){
        return $this->hasMany(Withdrawal::class);
    }
    public function favorites(){
        return $this->hasMany(Favorite::class);
    }

    public static function saveDates($dates,$special_price='',$villaId,$userId,$status=''){
        $dates=explode(',',$dates);
        $dataArray=[];
        $filedKey=$special_price!='' ? 'special_price' : 'status';
        $filedValue=$special_price!='' ? $special_price : $status;
        $datesArray=[];

        foreach($dates as $key=>$value){
            $date=explode('/',$value);
            $datesArray[$key]=$date;
            $dataArray[$key]=
            [
            'villa_id'=>$villaId,
            'user_id'=>$userId,
            'date'=>str_replace(',','-',implode(',',Verta::getGregorian($date[0],$date[1],$date[2]))),
            $filedKey=>$filedValue
            ];
        }
        // Date::insert($arr);
        Date::updateOrCreate(
            ['date'=>'2021-03-24'],
            [$dataArray]
        );
    }

}
