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
            'phone_number' => 'required|max:11',
            'email' => 'email',
            'notional_code' => 'max:10',
            'job' => 'max:100',
            'education' => 'max:100',
            'foreign_language' => 'max:100',
            'card_number' => 'max:16',
            'shaba_number' => 'max:24'
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
        return response()->json(['data' => 'User data updated.']);
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
        $transactions = $user->transactions;
        return new UserTransactionsCollection($transactions);
    }

    public function villas()
    {
        $user = Auth::user();
        $villas = $user->villas;
        return new UserVillasCollection($villas);
    }

    public function villaDates($id)
    {
        $villa = Villa::findOrFail($id);
        $dates = $villa->dates;
        $rules = $villa->rule;
        return new UserVillaDatesCollection($dates, $rules);
    }

    public function changeDatesCost(Request $request, $id)
    {
        $this->validate($request, [
            'dates' => 'required',
            'special_price' => 'required'
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
        $requests = ReservedDate::where('villa_id', $id)->get();
        return new UserVillaReservationsCollection($requests);
    }

    public function changeReserveStatus(Request $request, $id)
    {
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
        Withdrawal::create([
            'user_id' => $user->id,
            'requested_amount' => $request->requested_amount
        ]);
        return response()->json(['data' => 'Withdrawal request created']);
    }

    public function editVilla($id)
    {
        $user = Auth::loginUsingId(7);
        $villa = Villa::where([['user_id', $user->id], ['id', $id]])->with(['detail', 'rule', 'info', 'images'])->get();
        if (!$villa->IsEmpty()) {
            return response()->json(['data' => $villa]);
        }
        return response()->json(['status' => 404, 'data' => 'Villa not found']);
    }
}
