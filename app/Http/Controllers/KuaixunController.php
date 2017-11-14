<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Kuaixun;

class KuaixunController extends Controller
{
    public function getkx(Request $request) {
        $kuaixun = new Kuaixun();

        $ret = $kuaixun->getKuaixun($request->input('page'), $request->input('num'));
        return ['success'=>1, 'value'=>$ret];
    }
}
