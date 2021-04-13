<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\Pay;
use App\Models\Reservation;
use App\Models\ReservedDate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use SaeedVaziry\Payir\Exceptions\SendException;
use SaeedVaziry\Payir\Exceptions\VerifyException;
use SaeedVaziry\Payir\PayirPG;

class PaymentController extends Controller
{

    public function pay(Request $request)
    {
        $payir = new PayirPG();
        $payir->amount = $request->amount; 
        $payir->factorNumber = 'Factor-Number'; 
        $payir->description = 'Some Description'; 
        $payir->mobile = '0912XXXXXXX'; 
        $payir->validCardNumber = '6219860000000000'; 

        try {
            $pay = $payir->send();

            Pay::create([
                'amount' => $request->amount,
                'authority' => $pay['token'],
                'villa_reservation_id' => $request->reservation_id,
                'user_reservation_id' => $request->reservation_id,
                'name' => 'Trapp',
                'mobile' => '0999999999',
                'email' => 'info@trapp.com',
                'user_id'=>Auth::user()->id,
                'villa_id'=>$request->villa_id
            ]);

            if ($pay['status'] == 1) {
                // return redirect($pay['payment_url']);
                return response()->json(['status' => 1, 'payment_url' => $pay['payment_url']]);
            }
        } catch (SendException $e) {
            return response()->json(['status' => 0, 'data' => 'مشکلی در ارتباط با درگاه رخ داد']);
        }
    }

    public function verify(Request $request)
    {
        $payir = new PayirPG();
        $payir->token = $request->token;

        try {
            $verify = $payir->verify(); 
            if (isset($_GET['status'])) {
                if ($verify['status'] == 1) {
                    $factor = Pay::where('authority', $_GET['token'])->first();
                    if ($factor->refid == null) {
                        $factor->update(['status' => 1,'refid'=>$verify['transId']]);
                        ReservedDate::where('id', $factor->villa_reservation_id)->update(['status' => 2]);
                        Reservation::where('id', $factor->user_reservation_id)->update(['pay_status' => 2]);
                        return redirect('http://trapp.com/verify?token='.$request->token);
                    } else {
                        return redirect('http://trapp.com/verify?token='.$request->token);
                    }
                }
                return redirect('http://trapp.com/verify?token='.$request->token);
            }
        } catch (VerifyException $e) {
            // throw $e;
            return response()->json(['status' => -2, 'data' => 'پرداخت ناموفق بود']);
        }
    }

    public function getPayStatus(Request $request){
        $validation=$this->validate($request,[
            'token'=>'required|exists:pays,authority'
        ]);
        $pay=Pay::where('authority',$request->token)->first();
        return response()->json(['amount'=>$pay->amount,'status'=>$pay->status]);
    }
    

}