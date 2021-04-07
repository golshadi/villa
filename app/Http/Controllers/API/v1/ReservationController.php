<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use App\Models\ReservedDate;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReservationController extends Controller
{

    public function __construct()
    {
        date_default_timezone_set('Asia/Tehran');
    }
    public function reserveRequest(Request $request){
        $user=Auth::loginUsingId(6);
        Reservation::create([
            'user_id'=>$user->id,
            'villa_title'=>$request->villa_title,
            'state'=>$request->state,
            'city'=>$request->city,
            'entry_date'=>convertToGregorian($request->entry_date),
            'exit_date'=>convertToGregorian($request->exit_date),
            'cost'=>$request->cost,
            'pay_status'=>0,
            'villa_id'=>$request->villa_id
        ]);

        ReservedDate::create([
            'villa_id'=>$request->villa_id,
            'reserve_date'=>Carbon::now()->toDateTimeString(),
            'user_id'=>$user->id,
            'passengers_number'=>$request->passengers_number,
            'final_cost'=>$request->cost,
            'start_date'=>convertToGregorian($request->entry_date),
            'end_date'=>convertToGregorian($request->exit_date),
            'status'=>0
        ]);

    return response()->json(['data'=>'The villa was successfully reserved']);
    }


}
