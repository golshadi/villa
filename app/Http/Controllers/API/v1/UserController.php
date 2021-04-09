<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use App\Http\Resources\v1\User\UserReservationsCollection;
use App\Http\Resources\v1\User\UserTransactionsCollection;
use App\Http\Resources\v1\User\UserVillaDatesCollection;
use App\Http\Resources\v1\User\UserVillaReservationsCollection;
use App\Http\Resources\v1\User\UserVillasCollection;
use App\Models\Reservation;
use App\Models\ReservedDate;
use App\Models\User;
use App\Models\Villa;
use App\Models\Withdrawal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Hekmatinasser\Verta\Verta;

class UserController extends Controller
{

    public function getUserInfo(){
        return Auth::user();
    }

    public function updateInfo(Request $request){

        $user=Auth::user();

        $validator = $this->validate($request, [
            'fullname' => 'required',
            'phone_number'=>'required|max:11',
            'email'=>'email',
            'notional_code'=>'max:10',
            'job'=>'max:100',
            'education'=>'max:100',
            'foreign_language'=>'max:100',
            'card_number'=>'max:16',
            'shaba_number'=>'max:24'
        ]);

        if($request->hasFile('avatar')){
            $this->validate($request, [
                'avatar' => 'max:2048|mimes:jpg,png,bmp,jpeg|image'
            ]);
            $img_name = 'User_' . time() . rand(0,10000) . '.' . $request->file('avatar')->getClientOriginalExtension();
            request()->avatar->move(public_path('images/user'), $img_name);
            $user->avatar=$img_name;
        }

        $user->update($validator);
        return response()->json(['data'=>'User data updated.']);
    }

    public function reserves(){
        $user=Auth::user();
        $reserves=$user->reservations;
        return new UserReservationsCollection($reserves);
    }

    public function transactions(){
        $user=Auth::user();
        $transactions=$user->transactions;
        return new UserTransactionsCollection($transactions);
    }

    public function villas(){
        $user=Auth::user();
        $villas=$user->villas;
        return new UserVillasCollection($villas);
    }

    public function villaDates($id){
        $villa=Villa::findOrFail($id);
        $dates=$villa->dates;
        $rules=$villa->rule;
        return new UserVillaDatesCollection($dates,$rules);
    }

    public function changeDatesCost(Request $request,$id){
        $userId=Auth::user()->id;
        $villa=Villa::where([['id',$id],['user_id',$userId]])->first();
        // User::saveDates($request->dates,$request->special_price,$villa->id,$userId);
        $dates=$request->dates;
        $dates=explode(',',$dates);
        $dataArray=[];
      
        $datesArray=[];

        foreach($dates as $key=>$value){
            $date=explode('/',$value);
            $datesArray[$key]=$date;
            $dataArray[$key]=
            [
            'villa_id'=>$villa->id,
            'user_id'=>$userId,
            'date'=>str_replace(',','-',implode(',',Verta::getGregorian($date[0],$date[1],$date[2]))),
            'special_price'=>$request->special_price
            ];
            // $dataArray2[]=$dataArray[$key]->toArray();
        }
        // Date::insert($arr);
        // Date::updateOrCreate(
        //     ['date'=>'2021-03-24'],
        //     [$dataArray]
        // );
        // Date::firstOrCreate(
        //     $dataArray
        // );
        // return response()->json(['data'=>'Special prices saved.']);

        return (array) $dataArray;

    }
    
    public function changeDatesStatus(Request $request,$id){
        $userId=Auth::user()->id;
        $villa=Villa::where([['id',$id],['user_id',$userId]])->first();
        User::saveDates($request->dates,'',$villa->id,$userId,$request->status);
        return response()->json(['data'=>'Status saved.']);
    }


    public function reservationsRequested($id){
        $user=Auth::user();
        $villas=$user->villas;
        $villaIds=[];
        foreach($villas as $key=>$value){
            $villaIds[$key]=$value->id;
        }
        $requests=ReservedDate::whereIn('villa_id',$villaIds)->get();
        return new UserVillaReservationsCollection($requests);
    }

    public function changeReserveStatus(Request $request,$id){
        $reserve=ReservedDate::findOrFail($id);
        $reserve->update([
            'status'=>$request->status
        ]);
        $reserve2=Reservation::findOrFail($id);
        $reserve2->update([
            'status'=>$request->status
        ]);
        return response()->json(['data'=>'Status updated']);
    }

    public function withdrawal(Request $request){
        $user=Auth::user();
        Withdrawal::create([
            'user_id'=>$user->id,
            'requested_amount'=>$request->requested_amount
        ]);
        return response()->json(['data'=>'Withdrawal request created']);
    }

}