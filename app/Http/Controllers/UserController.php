<?php

namespace App\Http\Controllers;

use App\Models\User;
use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

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
            if ($user) {
                $result['message'] = 'Такой пользователь уже зарегистрован';
                break;
            }
            $code = rand(1000, 9999);
            $http = new Client(['verify' => false]);
            $link = 'http://37.18.30.37/api/identification';
            try {
                $response = $http->get($link, [
                    'query' => [
                        'phone' => $phone,
                        'code' => $code,
                    ]
                ]);
                $response = $response->getBody()->getContents();
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

    public function stepOne(Request $request)
    {
        $companyName = $request->input('companyName');
        $fio = $request->input('fio');
        $position = $request->input('position');
        $phone = $request->input('phone');
        $email = $request->input('email');
        $iin = $request->input('iin');
        $type = $request->input('type');
        $code = $request->input('code');
        $password = $request->input('password');
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
            if ($user) {
                $result['message'] = 'Такой пользователь уже зарегистрован';
                break;
            }

            $token = Str::random(60);
            $token = sha1($token . time());
            $password = bcrypt($password);
            DB::beginTransaction();
            $user = DB::table('users')->insertGetId([
                'companyName' => $companyName,
                'fio' => $fio,
                'position' => $position,
                'phone' => $phone,
                'email' => $email,
                'iin' => $iin,
                'type' => $type,
                'token' => $token,
                'password' => $password,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);

            if (!$user) {
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
                $response = $response->getBody()->getContents();
                $response = json_decode($response, true);
                if ($response['success'] == true) {
                    $result['success'] = true;
                    $result['id'] = $response['id'];
                    $result['token'] = $token;
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

    public function stepTwo(Request $request)
    {
        $token = $request->input('token');
        $sphere = $request->input('sphere');
        $description = $request->input('description');
        $amount = $request->input('amount');
        $id = $request->input('id');
        $result['success'] = false;
        do {
            if (!$token) {
                $result['message'] = 'Не передан токен';
                break;
            }
            if (!$sphere) {
                $result['message'] = 'Не передан сфера судебного';
                break;
            }
            if (!$description) {
                $result['message'] = 'Не передан описание';
                break;
            }
            if (!$amount) {
                $result['message'] = 'Не передан сумма иска';
                break;
            }
            if (!$id) {
                $result['message'] = 'Не передан номер заявки';
                break;
            }
            $user = $this->checkUser($token);
            if (!$user) {
                $result['message'] = 'Не найден пользователь';
                break;
            }
            DB::table('users')->where('token', $token)->update([
                'description' => $description,
                'sphere' => $sphere,
                'amount' => $amount,
            ]);
            $send = $this->sendTwo($id, $sphere, $description, $amount);
            if ($send) {
                $result['success'] = true;
                break;
            } else {
                $result['message'] = 'Попробуйте позже';
                break;
            }

        } while (false);
        return response()->json($result);
    }

    public function sendTwo($id, $sphere, $description, $amount)
    {
        $http = new Client(['verify' => false]);
        $link = 'http://178.170.221.46/api/site/step2.php';
        try {
            $response = $http->get($link, [
                'query' => [
                    'id' => $id,
                    'sphere' => $sphere,
                    'description' => $description,
                    'amount' => $amount,
                ]
            ]);
            $response = $response->getBody()->getContents();
            $response = json_decode($response, true);
            if ($response['success'] == true) {
                $result['success'] = true;
                return true;
            } else if ($response['success'] == false) {
                $result['message'] = 'Попробуйте позже';
                return false;
            }

        } catch (BadResponseException $e) {
            info($e);
        }
        return false;
    }

    public function stepThree(Request $request)
    {
        $token = $request->input('token');
        $typePayment = $request->input('typePayment');
        $amountPayment = $request->input('amountPayment');
        $id = $request->input('id');

        $result['success'] = false;
        do {
            if (!$token) {
                $result['message'] = 'Не передан токен';
                break;
            }
            if (!$id) {
                $result['message'] = 'Не передан номер заявки';
                break;
            }
            if (!$typePayment) {
                $result['message'] = 'Не передан тип оплаты';
                break;
            }
            $user = $this->checkUser($token);
            if (!$user) {
                $result['message'] = 'Не найден пользователь';
                break;
            }
            DB::table('users')->where('token', $token)->update([
                'typePayment' => $typePayment,
                'amountPayment' => $amountPayment,
            ]);
            $send = $this->sendThree($id, $typePayment, $amountPayment);
            if ($send) {
                $result['success'] = true;
                break;
            } else {
                $result['message'] = 'Попробуйте позже';
                break;
            }

        } while (false);
        return response()->json($result);
    }


    public function checkUser($token)
    {
        $user = DB::table('users')->where('token', $token)->first();
        if ($user) {
            return true;
        } else {
            return false;
        }
    }

    public function sendThree($id, $typePayment, $amountPayment)
    {
        $http = new Client(['verify' => false]);
        $link = 'http://178.170.221.46/api/site/step3.php';
        try {
            $response = $http->get($link, [
                'query' => [
                    'id' => $id,
                    'typePayment' => $typePayment,
                    'amountPayment' => $amountPayment,
                ]
            ]);
            $response = $response->getBody()->getContents();
            $response = json_decode($response, true);
            if ($response['success'] == true) {
                $result['success'] = true;
                return true;
            } else if ($response['success'] == false) {
                $result['message'] = 'Попробуйте позже';
                return false;
            }

        } catch (BadResponseException $e) {
            info($e);
        }
        return false;
    }
}
