<?php

namespace App\Http\Controllers;

use App\Models\AnticollectorUserModel;
use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AnticollectorController extends Controller
{
    public function firstStep(Request $request)
    {
        $fio = $request->input('fio');
        $phone = $request->input('phone');
        $iin = $request->input('iin');
        $email = $request->input('email');
        $password = $request->input('password');

        $result['success'] = false;
        do {
            if (!$fio) {
                $result['message'] = 'Не передан фио';
                break;
            }
            if (!$phone) {
                $result['message'] = 'Не передан телефон';
                break;
            }
            if (!$iin) {
                $result['message'] = 'Не передан ИИН';
                break;
            }
            if (!$email) {
                $result['message'] = 'Не передан почта';
                break;
            }
            if (!$password) {
                $result['message'] = 'Не передан пароль';
                break;
            }
            $token = Str::random(60);
            $token = sha1($token . time());
            $password = bcrypt($password);
            DB::beginTransaction();

            $userID = AnticollectorUserModel::create([
                'fio' => $fio,
                'phone' => $phone,
                'iin' => $iin,
                'email' => $email,
                'password' => $password,
                'token' => $token,
            ]);
            if (!$userID) {
                $result['message'] = 'Попробуйте позже';
                DB::rollBack();
                break;
            }
            $code = rand(1000,9999);
            $s = DB::table('code')->insertGetId([
                'phone' => $phone,
                'code' => $code,
            ]);
            $http = new Client(['verify' => false]);
            $link = 'http://37.18.30.37/api/typeOne';
            try {
                $response = $http->get($link, [
                    'query' => [
                        'phone' => $phone,
                        'code' => $code,
                        'source' => 'anticollector.kz',
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

            DB::commit();
            $result['success'] = true;
            $result['token'] = $token;
        } while (false);
        return response()->json($result);
    }

    public function secondStep(Request $request)
    {
        $organization = $request->input('organization');
        $token = $request->input('token');
        $result['success'] = false;
        do {
            if (!$organization) {
                $result['message'] = 'Параметры не передан';
                break;
            }
            $user = $this->checkToken($token);
            if (!$user) {
                $result['message'] = 'Не найден пользователь';
                break;
            }
            DB::beginTransaction();
            $org = '';
            foreach ($organization as $o) {
                $org .= $o.',';
            }
            substr($org, 0, -1);

            $userID = DB::table('anticollector_users')->where('id',$user)->update([
                'organization' => $org,
            ]);
            if (!$userID){
                DB::rollBack();
                $result['message'] = 'Попробуйте позже';
                break;
            }

            DB::commit();
            $result['success'] = true;
        } while (false);
        return response()->json($result);
    }

    public function sendMessage(Request $request){
        $token = $request->input('token');
        $phone = $request->input('phone');
        $result['success'] = false;
        do{
            $user = $this->checkToken($token);
            if (!$user){
                $result['message'] = 'Не найден пользователь';
                break;
            }
            $code = rand(1000,9999);


            $http = new Client(['verify' => false]);
            $link = 'http://37.18.30.37/api/typeOne';
            try {
                $response = $http->get($link, [
                    'query' => [
                        'phone' => $phone,
                        'code' => $code,
                        'source' => 'anticollector.kz',
                    ]
                ]);
                $response = $response->getBody()->getContents();
                $response = json_decode($response, true);

                    if ($response['success'] == true) {
                        DB::table('code')->insertGetId([
                            'phone' => $phone,
                            'code' => $code,
                        ]);
                        $result['success'] = true;
                        break;
                    } else if ($response['success'] == false) {
                        $result['message'] = 'Попробуйте позже';
                        break;
                    }

            } catch (BadResponseException $e) {
                info($e);
            }

        }while(false);
        return response()->json($result);
    }

    public function lastStep(Request $request){
        $token = $request->input('token');
        $type = $request->input('type');
        $result['success'] = false;
        do{
            if (!$token){
                $result['message'] = 'Не передан токен';
                break;
            }
            if (!$type){
                $result['message'] = 'Не передан тип';
                break;
            }
            $user = $this->checkToken($token);
            if (!$user){
                $result['message'] = 'Не найден пользователь';
                break;
            }
            DB::beginTransaction();
            $userID = DB::table('anticollector_users')->where('id',$user)->update([
                'type' => $type,
            ]);
            if (!$userID){
                DB::rollBack();
                $result['message'] = 'Попробуйте позже';
                break;
            }

            DB::commit();
            $result['success'] = true;
        }while(false);
        return response()->json($result);

    }

    public function checkToken($token)
    {
        $user = DB::table('anticollector_users')->where('token', $token)->first();
        if ($user) {
            return $user->id;
        }
        return false;
    }

    public function getDocumentLink(Request $request){
        $token = $request->input('token');
        $result['success']  = false;
        do{
            if (!$token){
                $result['message'] = 'Не передан токен';
                break;
            }
            $user = $this->checkToken($token);
            if (!$user){
                $result['message'] = 'Не найден пользователь';
                break;
            }
            $result['success'] = true;
            $result['doc1'] = 'https://google.com';
            $result['doc2'] = 'https://google.com';
            $result['doc3'] = 'https://google.com';
        }while(false);
        return response()->json($result);
    }

    public function getPush(Request $request){
        $token = $request->input('token');
        $result['success']  = false;
        do{
            if (!$token){
                $result['message'] = 'Не передан токен';
                break;
            }
            $user = $this->checkToken($token);
            if (!$user){
                $result['message'] = 'Не найден пользователь';
                break;
            }
            $result['success'] = true;
            $result['data'] = [
                0 => [
                    'status' => 0,
                    'message' => 'Мы отправили письмо к МФО',
                ],
                1 => [
                    'status' => 1,
                    'message' => 'Мы отправили письмо к ЧСИ'
                ]
            ];
        }while(false);
        return response()->json($result);
    }

    public function uploadDocuments(Request $request){
        $files = $request->input('files');
        $token = $request->input('token');
        $result['success'] = false;
        do{
            if(!$files){
                $result['message'] = 'Не передан файлы';
                break;
            }
            if (!$token){
                $result['message'] = 'Не передан токен';
                break;
            }
            $user = $this->checkToken($token);
            if (!$user){
                $result['message'] = 'Пользователь не найден';
                break;
            }
            foreach ($files as $file){
                $name = $file->getClientOriginalName();
                $name = sha1(time() . $name) . '.' . $request->file('file')->extension();;

                $destinationPath = public_path('/images/');
                $file->move($destinationPath, $name);
                DB::table('user_docs')->insertGetId([
                    'doc' => $name,
                    'user_id' => $user->id,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);
            }
        }while(false);
        return response()->json($result);
    }
}
