<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use App\Http\Resources\v1\User\UserFactor;
use App\Models\Detail;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FactorController extends Controller
{
    public function getFactor($id){
        $user=Auth::user();
        $factor=Reservation::where([['id',$id],['user_id',$user->id]])
        ->with('villa')->first();
        $details=Detail::where('villa_id',$factor->villa->id)->first();
        return new UserFactor($factor,$details);
    }
}
