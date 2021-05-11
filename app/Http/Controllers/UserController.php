<?php

namespace App\Http\Controllers;

use App\Models\User;
use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function identification(Request $request)
    {
        $companyName = $request->input('companyName');
        $fio = $request->input('fio');
        $position = $request->input('position');
        $phone = $request->input('phone');
        $email = $request->input('email');
        $iin = $request->input('iin');
        $result['success'] = false;
        do {
            if (!$fio) {
                $result['message'] = 'Не передан ФИО';
                break;
            }
            if (!$phone) {
                $result['message'] = 'Не передан телефон';
                break;
            }
            if (!$email) {
                $result['message'] = 'Не передан почта';
                break;
            }
            $user = User::where('phone', $phone)->where('email', $email)->first();
            if (!$user) {
                $result['message'] = 'Такой пользователь уже зарегистрован';
                break;
            }
            $http = new Client(['verify' => false]);
            $link = 'http://37.18.30.37/api/identificationNash';
            try {
                $response = $http->get($link, [
                    'query' => [
                        'phone' => $phone,
                    ]
                ]);
                $response = $response->getBody();
                $response = json_decode($response, true);
                if ($response['success'] == true) {
                    $result['success'] = true;
                    break;
                } else if ($response['success'] == false) {
                    $result['message'] = 'Попробуйте позже';
                    break;
                }

            } catch (BadResponseException $e) {
                info($e);
            }

        } while (false);

        return response()->json($result);
    }

    public function stepOne(Request $request){
        $companyName = $request->input('companyName');
        $fio = $request->input('fio');
        $position = $request->input('position');
        $phone = $request->input('phone');
        $email = $request->input('email');
        $iin = $request->input('iin');
        $type = $request->input('type');
        $result['success'] = false;
        do {
            if (!$fio) {
                $result['message'] = 'Не передан ФИО';
                break;
            }
            if (!$phone) {
                $result['message'] = 'Не передан телефон';
                break;
            }
            if (!$email) {
                $result['message'] = 'Не передан почта';
                break;
            }
            $user = User::where('phone', $phone)->where('email', $email)->first();
            if (!$user) {
                $result['message'] = 'Такой пользователь уже зарегистрован';
                break;
            }
            DB::beginTransaction();
            $user = DB::table('users')->insertGetId([
                'companyName' => $companyName,
                'fio' => $fio,
                'position' => $position,
                'phone' => $phone,
                'email' => $email,
                'iin' => $iin,
                'type' => $type,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);

            if (!$user){
                $result['message'] = 'Попробуйте позже';
                break;
            }
            $http = new Client(['verify' => false]);
            $link = 'http://178.170.221.46/api/site/step1.php';
            try {
                $response = $http->get($link, [
                    'query' => [
                        'type' => $type,
                        'fio' => $fio,
                        'position' => $position,
                        'phone' => $phone,
                        'email' => $email,
                        'companyName' => $companyName,
                        'iin' => $iin,
                    ]
                ]);
                $response = $response->getBody();
                $response = json_decode($response, true);
                if ($response['success'] == true) {
                    $result['success'] = true;
                    break;
                } else if ($response['success'] == false) {
                    $result['message'] = 'Попробуйте позже';
                    break;
                }

            } catch (BadResponseException $e) {
                info($e);
            }
            DB::commit();

        } while (false);

        return response()->json($result);
    }
}
