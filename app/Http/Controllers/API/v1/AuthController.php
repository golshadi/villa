<?php

namespace App\Http\Controllers\api\v1;

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
            'phone_number' => 'required|max:11|unique:users'
        ]);

        $user = User::create([
            'fullname' => $dataValidation['fullname'],
            'phone_number' => $dataValidation['phone_number'],
            'sms_expire' => time() + (2 * 60)
        ]);

        return $this->sendRegisterSms($request->phone_number, $user);
    }


    public function sendRegisterSms($phone_number, $user)

    {
        $codeExpire = $user->sms_expire;
        $send_sms_time = $codeExpire - (2 * 60);
        if (($send_sms_time + 5 <= $codeExpire) or (time() >= $codeExpire)) {
            $code = mt_rand(100000, 999999);
            SendSmsAuthJob::dispatch($phone_number, $code);
            $user->update([
                'sms_code' => $code
            ]);
            return response()->json(['status' => 2, 'message' => 'SMS sent']);
        } else {
            return response()->json(['status' => 1, 'message' => 'SMS has been sent. Try again in 2 minutes']);
        }
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
                    'sms_expire' => time() + (60 * 2)
                ]);
                return response()->json(['status' => 2, 'message' => 'SMS sent']);
            } else {
                return response()->json(['status' => 1, 'message' => 'SMS has been sent. Try again in 2 minutes']);
            }
        } else {
            return response()->json(['status' => 0, 'message' => 'There is no user with this phone number']);
        }
    }

    public function verifySmsCode(Request $request)
    {

        $userCode = $request->sms_code;
        $phone_number = $request->phone_number;
        $user = User::where('phone_number', $phone_number)->first();
        $smsExpire = (int)$user->sms_expire;
        $realCode = $user->sms_code;

        if (($userCode == $realCode) and (time() <= $smsExpire)) {
            Auth::loginUsingId($user->id);
            // if (Auth::attempt(['phone_number' => $phone_number, 'sms_code' => $userCode])) {
                auth()->user()->tokens()->delete();
                $token = auth()->user()->createToken('Api Token on Website')->accessToken;
                return response()->json(['data' => ['user' => auth()->user(), 'token' => $token]]);
            // }else{
                // return response()->json(['status' => 5, 'message' => 'Password incorrect or Phone number is invalid']);
            // }
        }

        return response()->json(['status' => 4, 'message' => 'Code is invalid or SmsCode timedout!']);
    }

    public function login(Request $request)
    {
        $validData = $this->validate($request, [
            'phone_number' => 'required|exists:users'
        ]);
        $this->sendNormalSms($request->phone_number);
    }
}
