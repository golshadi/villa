<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use App\Http\Resources\v1\user\FinancialReportsCollection;
use App\Http\Resources\v1\User\UserReservationsCollection;
use App\Http\Resources\v1\User\UserTransactionsCollection;
use App\Http\Resources\v1\User\UserVillaDatesCollection;
use App\Http\Resources\v1\User\UserVillaReservationsCollection;
use App\Http\Resources\v1\User\UserVillasCollection;
use App\Http\Resources\v1\user\VillaIncomeCollection;
use App\Models\FinancialReport;
use App\Models\Pay;
use App\Models\Reservation;
use App\Models\ReservedDate;
use App\Models\User;
use App\Models\Villa;
use App\Models\Withdrawal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PHPUnit\Framework\Constraint\IsEmpty;

class UserController extends Controller
{

    public function getUserInfo()
    {
        return Auth::user();
    }

    public function updateInfo(Request $request)
    {

        $user = Auth::user();

        $validator = $this->validate($request, [
            'fullname' => 'required',
            'phone_number' => 'required|max:11|min:11',
            'email' => 'email',
            'notional_code' => 'max:10|min:10',
            'job' => 'max:100',
            'education' => 'max:100',
            'foreign_language' => 'max:100',
            'card_number' => 'max:16|min:16',
            'shaba_number' => 'max:24|min:24'
        ]);

        if ($request->hasFile('avatar')) {
            $this->validate($request, [
                'avatar' => 'max:2048|mimes:jpg,png,bmp,jpeg|image'
            ]);
            $img_name = 'User_' . time() . rand(0, 10000) . '.' . $request->file('avatar')->getClientOriginalExtension();
            request()->avatar->move(public_path('images/user'), $img_name);
            $user->avatar = $img_name;
        }

        $user->update($validator);
        return response()->json(['data' => 'User data updated']);
    }

    public function reserves()
    {
        $user = Auth::user();
        $reserves = $user->reservations;
        return new UserReservationsCollection($reserves);
    }

    public function transactions()
    {
        $user = Auth::user();
        $userTransactions = Pay::where([['user_id',$user->id],['refid','!=',null]])->get();
        $villaTransactions = Pay::where('refid','!=',null)
        ->whereIn('villa_id',$user->villas->pluck('id')->toArray())->get();
        $data=$userTransactions->merge($villaTransactions);
        return new UserTransactionsCollection($data,$user->id);
    }

    public function villas()
    {
        $user = Auth::user();
        $villas = $user->villas;
        return new UserVillasCollection($villas);
    }

    public function villaDates($id)
    {
        $userId=Auth::user()->id;
        $villa = Villa::where([['id',$id],['user_id',$userId]])->first();
        $dates = $villa->dates;
        $rules = $villa->rule;
        $reserves = ReservedDate::where([['villa_id', $id],['status',2]])->get();
        return new UserVillaDatesCollection($dates, $rules,$reserves);
    }

    public function changeDatesCost(Request $request, $id)
    {
        $this->validate($request, [
            'dates' => 'required',
            'special_price' => 'required|numeric'
        ]);
        return User::saveDates($request->dates, $request->special_price, $id);
    }

    public function changeDatesStatus(Request $request, $id)
    {
        $this->validate($request, [
            'dates' => 'required',
            'status' => 'required'
        ]);
        return User::saveDates($request->dates, '', $id, $request->status);
    }

    public function allReservationsRequested()
    {
        $user = Auth::user();
        $villas = $user->villas;
        $villaIds = [];
        foreach ($villas as $key => $value) {
            $villaIds[$key] = $value->id;
        }
        $requests = ReservedDate::whereIn('villa_id', $villaIds)->get();
        return new UserVillaReservationsCollection($requests);
    }

    public function reservationsRequested($id)
    {
        $userId = Auth::user()->id;
        $userVillaIds=Villa::where('user_id',$userId)->pluck('id')->toArray();
        if(in_array($id,$userVillaIds)){
        $requests = ReservedDate::where('villa_id', $id)->get();
        return new UserVillaReservationsCollection($requests);
        }
        return response()->json(['data','Villa not found']);
    }

    public function changeReserveStatus(Request $request, $id)
    {
        $this->validate($request,['status'=>'required|numeric']);
        $reserve = ReservedDate::findOrFail($id);
        $reserve->update([
            'status' => $request->status
        ]);
        $reserve2 = Reservation::findOrFail($id);
        $reserve2->update([
            'status' => $request->status
        ]);
        return response()->json(['data' => 'Status updated']);
    }

    public function withdrawal(Request $request)
    {
        $user = Auth::user();
        $this->validate($request,['requested_amount'=>'required|numeric']);
        Withdrawal::create([
            'user_id' => $user->id,
            'requested_amount' => $request->requested_amount
        ]);
        return response()->json(['data' => 'Withdrawal request created']);
    }

    public function editVilla($id)
    {
        $user = Auth::user();
        $villa = Villa::where([['user_id', $user->id], ['id', $id]])->with(['detail', 'rule', 'info', 'images'])->get();
        if (!$villa->IsEmpty()) {
            return response()->json(['data' => $villa]);
        }
        return response()->json(['status' => 404, 'data' => 'Villa not found']);
    }

    public function getFinancialReports(){
        $user=Auth::user();
        $financialReports=$user->financialStatements;
        $trappIncome=ReservedDate::whereIn('villa_id',$user->villas->pluck('id')->toArray())
        ->where('status',2)
        ->sum('final_cost');
        $otherIncome=FinancialReport::where('user_id',$user->id)->sum('amount');
        return new FinancialReportsCollection($financialReports,[$trappIncome,$otherIncome]);
        // return response()->json(['data'=>$otherIncome]);

    }
    
    public function setFinancialReports(Request $request){
        $user=Auth::user();
        $validation=$this->validate($request,[
            'date'=>'required',
            'src'=>'required',
            'description'=>'required',
            'amount'=>'required|numeric'
        ]);

        $user->financialStatements()->create($validation);

        return response()->json(['data'=>'Done']);
    }

    public function villaIncome($id){
        $user=Auth::user();
        $villa=$user->villas->where('id',$id)->first();
        return new VillaIncomeCollection($villa->reserves->where('status',2));
    }

}