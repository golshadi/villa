<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use App\Http\Resources\v1\User\UserReservationsCollection;
use App\Http\Resources\v1\User\UserTransactionsCollection;
use App\Http\Resources\v1\User\UserVillaCommentsCollection;
use App\Http\Resources\v1\User\UserVillaDatesCollection;
use App\Http\Resources\v1\User\UserVillasCollection;
use App\Models\Comment;
use App\Models\Villa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

  


}
