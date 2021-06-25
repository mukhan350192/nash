<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class UrlController extends Controller
{
    public function getDataSign(Request $request)
    {
        $token = $request->input('token');
        $result = DB::table('short_url')->where('token',$token)->where('status',1)->first();
        return response()->json($result);
    }

    public function signDoc(Request $request)
    {
        $leadID = $request->input('leadID');
        $amount = $request->input('amount');
        $iin = $request->input('iin');
        $amountPayment = $request->input('amountPayment');
        $client_type = $request->input('client_type');
        $companyName = $request->input('companyName');
        $fio = $request->input('fio');
        $code = $request->input('code');
        $phone = $request->input('phone');

        $result['success'] = false;
        do {
            if (!$leadID) {
                $result['message'] = 'Не передан лид айди';
                break;
            }
            if (!$amount) {
                $result['message'] = 'Не передан сумма';
                break;
            }
            $token = Str::random(16);
            DB::table('short_url')->insertGetId([
                'leadID' => $leadID,
                'iin' => $iin,
                'token' => $token,
                'amount' => $amount,
                'amountPayment' => $amountPayment,
                'client_type' => $client_type,
                'companyName' => $companyName,
                'fio' => $fio,
                'phone' => $phone,
                'code' => $code,
                'status' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
            $url = "https://nashcompany.kz/aggrement?token=$token";
            $short_url = file_get_contents("https://clck.ru/--?url=$url");
            $result['url'] = $short_url;
            $result['success'] = true;
        } while (false);
        return response()->json($result);
    }

    public function removeShortUrl(Request $request){
        $id = $request->input('id');
        $typePayment = $request->input('typePayment');
        $amountPayment = $request->input('amountPayment');
        $date_payment = $request->input('date_payment');
        $period = $request->input('period');

        DB::table('short_url')->where('id',$id)->update(['status'=>2]);
        $data = DB::table('short_url')->where('id',$id)->first();
        $user = new UserController();
        $user->sendThree($data->leadID,$typePayment,$amountPayment,'','',$date_payment,$period);
    }
}
