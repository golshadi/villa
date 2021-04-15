<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use App\Jobs\SendSmsAuthJob;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        // Validation Data
        $dataValidation = $this->validate($request, [
            'fullname' => 'required|max:255',
            'phone_number' => 'required|min:11|max:11|unique:users'
        ]);

        $user = User::create([
            'fullname' => $dataValidation['fullname'],
            'phone_number' => $dataValidation['phone_number'],
            'sms_expire' => time() + (3 * 60)
        ]);

        return $this->sendRegisterSms($request->phone_number, $user);
    }


    public function sendRegisterSms($phone_number, $user)

    {
            $code = mt_rand(100000, 999999);
            SendSmsAuthJob::dispatch($phone_number, $code);
            $user->update([
                'sms_code' => $code
            ]);
            return response()->json(['status' => 2, 'message' => 'SMS sent']);
    }

    public function sendNormalSms($phone_number)
    {
        $user = User::where('phone_number', $phone_number)->first();
        if ($user) {
            $codeExpire = $user->sms_expire;
            if (time() > $codeExpire) {
                $code = mt_rand(100000, 999999);
                SendSmsAuthJob::dispatch($phone_number, $code);
                $user->update([
                    'sms_code' => $code,
                    'sms_expire' => time() + (60 * 3)
                ]);
                return response()->json(['status' => 2, 'message' => 'SMS sent']);
            } else {
                return response()->json(['status' => 1, 'message' => 'SMS has been sent. Try again in 3 minutes']);
            }
        } else {
            return response()->json(['status' => 0, 'message' => 'There is no user with this phone number']);
        }
    }

    public function verifySmsCode(Request $request)
    {
        $this->validate($request,[
            'phone_number'=>'required|min:11|max:11|exists:users,phone_number',
            'sms_code'=>'required|min:6|max:6'
        ]);
        $userCode = $request->sms_code;
        $phone_number = $request->phone_number;
        $user = User::where('phone_number', $phone_number)->first();
        $smsExpire = (int)$user->sms_expire;
        $realCode = $user->sms_code;

        if (($userCode == $realCode) and (time() <= $smsExpire)) {
            Auth::loginUsingId($user->id);
                auth()->user()->tokens()->delete();
                $token = auth()->user()->createToken('Api Token on Website')->accessToken;
                return response()->json(['data' => ['user' => auth()->user(), 'token' => $token]]);
        }

        return response()->json(['status' => 4, 'message' => 'Code is invalid or SmsCode timedout!']);
    }

    public function login(Request $request)
    {
        $validData = $this->validate($request, [
            'phone_number' => 'required|min:11|max:11|exists:users'
        ]);
       return $this->sendNormalSms($request->phone_number);
    }
}