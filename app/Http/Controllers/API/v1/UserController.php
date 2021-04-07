<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use App\Http\Resources\v1\User\UserReservationsCollection;
use App\Http\Resources\v1\User\UserTransactionsCollection;
use App\Http\Resources\v1\User\UserVillaCommentsCollection;
use App\Http\Resources\v1\User\UserVillaDatesCollection;
use App\Http\Resources\v1\User\UserVillaReservationsCollection;
use App\Http\Resources\v1\User\UserVillasCollection;
use App\Models\Comment;
use App\Models\Date;
use App\Models\Reservation;
use App\Models\ReservedDate;
use App\Models\User;
use App\Models\Villa;
use App\Models\Withdrawal;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Verta;

class UserController extends Controller
{
    public function updateInfo(Request $request){

        $user=Auth::loginUsingId(4);

        $validator = $this->validate($request, [
            'fullname' => 'required',
            'phone_number'=>'required',
            'email'=>'email',
            'notional_code'=>'max:10',
            'job'=>'max:200',
            'education'=>'max:200',
            'foreign_language'=>'max:200',
            'card_number'=>'max:200',
            'shaba_number'=>'max:200'
        ]);

        if($request->hasFile('avatar')){
            $this->validate($request, [
                'avatar' => 'max:2048,required|mimes:jpg,png,bmp,jpeg|image'
            ]);
            $img_name = 'User_' . time() . rand(0,10000) . '.' . $request->file('avatar')->getClientOriginalExtension();
            request()->avatar->move(public_path('images/user'), $img_name);
            $user->avatar=$img_name;
        }

        $user->update($validator);
        return response()->json(['data'=>'User data updated.']);
    }

    public function reserves(){
        $user=Auth::loginUsingId(4);
        $reserves=$user->reservations;
        return new UserReservationsCollection($reserves);
    }

    public function transactions(){
        $user=Auth::loginUsingId(4);
        $transactions=$user->transactions;
        return new UserTransactionsCollection($transactions);
    }

    public function villas(){
        $user=Auth::loginUsingId(4);
        $villas=$user->villas;
        return new UserVillasCollection($villas);
    }

    public function comments($id){
        $user=Auth::loginUsingId(4);
        $villa=Villa::where([['id',$id],['user_id',$user->id]])->first();
        $comments=$villa->comments;
        return new UserVillaCommentsCollection($comments);
    }

    public function replayComment(Request $request,$villaId,$parentId){

        $this->validate($request, [
            'text' => 'required'
        ]);
        
        Comment::create([
            'villa_id'=>$villaId,
            'user_id'=>Auth::loginUsingId(4)->id,
            'parent_id'=>$parentId,
            'text'=>$request->text,
        ]);

        return response()->json(['data'=>'The comment was answered']);
    }

    public function villaDates($id){
        $villa=Villa::findOrFail($id);
        $dates=$villa->dates;
        $rules=$villa->rule;
        return new UserVillaDatesCollection($dates,$rules);
    }

    public function changeDatesCost(Request $request,$id){
        $userId=Auth::loginUsingId(4)->id;
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
        $userId=Auth::loginUsingId(4)->id;
        $villa=Villa::where([['id',$id],['user_id',$userId]])->first();
        User::saveDates($request->dates,'',$villa->id,$userId,$request->status);
        return response()->json(['data'=>'Status saved.']);
    }


    public function reservationsRequested($id){
        $user=Auth::loginUsingId(4);
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
        $user=Auth::loginUsingId(4);
        Withdrawal::create([
            'user_id'=>$user->id,
            'requested_amount'=>$request->requested_amount
        ]);
        return response()->json(['data'=>'Withdrawal request created']);
    }

    public function updateVilla(Request $request){
        
    }


}
