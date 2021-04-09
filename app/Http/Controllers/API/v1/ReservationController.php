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

        $validatedData=$this->validate($request,[
            'villa_title'=>'required',
            'state'=>'required',
            'city'=>'required',
            'entry_date'=>'required',
            'exit_date'=>'required',
            'cost'=>'required|numeric',
            'villa_id'=>'required',
            'passengers_number'=>'required',
            'extra_people'=>'required',
            'length_stay'=>'required',
        ]);

        $user=Auth::user();
        $user_reserve=Reservation::create($validatedData);
        $user_reserve->user_id=$user->id;
        $user_reserve->entry_date=convertToGregorian($request->entry_date);
        $user_reserve->exit_date=convertToGregorian($request->exit_date);
        $user_reserve->pay_status=0;
        $user_reserve->save();

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
