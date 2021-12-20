<?php

namespace App\Http\Controllers;

use App\Models\AnticollectorUserModel;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;
use Illuminate\Http\Request;

class PersonalCabinet extends Controller
{
    public function getLeadData(Request $request){
        $token = $request->input('token');
        $result['success'] = false;

        do{
            if (!$token){
                $result['message'] = 'Не передан токен';
                break;
            }
            $data = $this->getTokenData($token);
            if (!$data){
                $result['message'] = 'Не найден пользователь';
                break;
            }
            $http = new Client(['verify' => false]);
            $link = 'https://nash-crm.kz/api/site/getDataBitrix.php';
            $query = ['bitrix_id' => $data];
            try {
                $response = $http->get($link, [
                    'query' => $query,
                ]);
                $response = $response->getBody()->getContents();
                $response = json_decode($response, true);
                if (isset($response)) {
                    $result['success'] = $response['success'];
                    $result['step'] = $response['step'];
                    break;
                } else {
                    $result['success'] = false;
                    break;
                }
            } catch (BadResponseException $e) {
                info($e);
            }

        }while(false);
        return response()->json($result);
    }

    public function getTokenData($token){
        if (!$token){
            return false;
        }
        $user = AnticollectorUserModel::where('token',$token)->pluck('bitrix_id')->first();
        if (!$user){
            return false;
        }
        return $user;
    }
}
