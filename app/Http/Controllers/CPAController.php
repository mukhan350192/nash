<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CPAController extends Controller
{
    public function leadgid(Request $request)
    {
        $click_id = $request->input('click_id');
        $id = $request->input('id');
        $url = "http://go.leadgid.ru/aff_lsr?goal_id=4605&adv_sub=$id&transaction_id=$click_id";
        $s = file_get_contents($url);
        info($url . " " . $s);
    }

    public function leadgidFree(Request $request)
    {
        $click_id = $request->input('click_id');
        $id = $request->input('id');
        $url = "http://go.leadgid.ru/aff_lsr?offer_id=5428&adv_sub=$id&transaction_id=$click_id";
        $s = file_get_contents($url);
        info($s);
    }
}
