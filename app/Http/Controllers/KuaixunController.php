<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Kuaixun;

class KuaixunController extends Controller
{
    public function getkx(Request $request) {
        $kuaixun = new Kuaixun();
        $platform = $request->input('p', 'pc');
        $date = $request->input('st', null);

        $ret = $kuaixun->getKuaixun($request->input('page'), $request->input('num'), $date);

        foreach ($ret as $key=>$val) {
            if($this->inWords($val->body, ['jin10.com', 'fx678.com', 'wallstreetcn.com', '金十'])) {
                unset($ret[$key]);
            }
            elseif ($platform == 'app') {
                $ret[$key]->body = strip_tags($val->body);
            }
        }

        return ['success'=>1, 'value'=>array_values($ret)];
    }

    private function inWords($str, $keys) {
        foreach($keys as $key) {
            if (strstr($str, $key)) {
                return true;
            }
        }

        return false;
    }
}
