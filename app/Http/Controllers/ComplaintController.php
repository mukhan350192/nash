<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ComplaintController extends Controller
{
    public function addFeedback(Request $request)
    {
        $fio = $request->input('fio');
        $iin = $request->input('iin');
        $comment = $request->input('comment');
        $phone = $request->input('phone');
        $result['success'] = false;
        do {
            if (!$fio) {
                $result['message'] = 'Не передан фио';
                break;
            }
            if (!$iin) {
                $result['message'] = 'Не передан иин';
                break;
            }
            if (!$comment) {
                $result['message'] = 'Не передан коммент';
                break;
            }
            if (!$phone) {
                $result['message'] = 'Не передан телефон';
                break;
            }
            $feedback = DB::table('feedback')->insertGetId([
                'fio' => $fio,
                'iin' => $iin,
                'phone' => $phone,
                'comment' => $comment,
                'status' => 1,
            ]);
            if (!$feedback) {
                $result['message'] = 'Попробуйте позже';
                break;
            }
            $result['success'] = true;
        } while (false);
        return response()->json($result);
    }

    public function getFeedback(Request $request)
    {
        $page = $request->input('page');
        if (!$page) {
            $page = 1;
        }
        $skip = ($page - 1) * 10;
        $limit = 10;
        $feedback = DB::table('feedback')->where('status', 2)->skip($skip)->limit($limit)->get();
        $result['data'] = $feedback;
    }
}
