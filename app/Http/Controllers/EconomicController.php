<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\EconomicCalendar;
use App\Models\EconomicJiedu;

class EconomicController extends Controller
{
    public function getDates(Request $request) {
        $date = $request->input("d", date("Y-m-d"));
        $limit = $request->input("limit");
        $reg = $request->input("reg");
        $ci = $request->input("ci", 0);

        $calendars = EconomicCalendar::whereDate('pub_time', $date);
        if(!is_null($reg)) {
            $calendars = $calendars->where('country', $reg);
        }

        if($ci == 1) {
            $calendars = $calendars->where('importance', '>', 2);
        }
        elseif($ci == 2) {
            $calendars = $calendars->where('pub_time', '>', date('Y-m-d H:i:s'));
        }

        if(!is_null($limit)) {
            $calendars = $calendars->take($limit);
        }

        $calendars = $calendars->get()->toArray();
        return $this->dataToData($calendars);
    }

    public function getPastorWillFd(Request $request) {
        $date = date('Y-m-d');
        $now = date('Y-m-d H:i:s');

        $limit1 = $request->input('limit1');
        $limit2 = $request->input('limit2');

        $past = EconomicCalendar::whereDate('pub_time', $date)->where('pub_time', '<=', $now)->orderBy("pub_time", "desc");
        if(!is_null($limit1)) {
            $past = $past->take($limit1);
        }

        $past = $past->get()->toArray();

        $will = EconomicCalendar::whereDate('pub_time', $date)->where('pub_time', '>', $now)->orderBy("pub_time", "asc");
        if(!is_null($limit2)) {
            $will = $will->take($limit2);
        }

        $will = $will->get()->toArray();

        return $this->dataToData(array_merge($past, $will));
    }

    public function getWeekData(Request $request) {
        $date = strtotime($request->input('d', date('Y-m-d')));
        if(!$date) {
            $date = time();
        }


        $weeks = ['星期一', '星期二', '星期三', '星期四', '星期五', '星期六', '星期日'];
        $all_weeks = [];

        $week_now = date('w', $date);
        for($index = 1; $index <= 7; $index ++) {
            $timestmp = $date + ($index - $week_now) * 24 * 3600;
            $all_weeks[] = [
                'd'     => date("Y-m-d", $timestmp),
                'z'     => $weeks[$index-1],
                'r'     => date("m-d", $timestmp),
                'dz'    => date("m-d", $timestmp) == date("m-d", $date)?1 : 0
            ];
        }

        $result['pre'] = date("Y-m-d", strtotime( '+'. 1 - $week_now .' days' ));
        $result['next'] = date("Y-m-d", strtotime( '+'. 3 - $week_now .' days' ));
        $result['w'] = $all_weeks;

        return $result;
    }

    public function getjiedu(Request $request) {
        $id = $request->input('idx_id');
        if(!is_null($id)) {
            $all_data = EconomicCalendar::where("dataname_id", $id)
                ->where('pub_time', '<=', date('Y-m-d H:i:s'))
                ->orderBy('pub_time', 'desc')
                ->take(9)
                ->get();

            $next_data = EconomicCalendar::where("dataname_id", $id)
                ->where('pub_time', '>', date('Y-m-d H:i:s'))
                ->orderBy('pub_time', 'asc')
                ->take(1)
                ->get();

            $x_data = [];
            $y_data = [];

            foreach($all_data as $val) {
                $x_data[] = substr($val['pub_time'], 0, 10);
                $y_data[] = $val['published_value'];
            }

            $result = [
                "riliData"  =>  $this->dataToData(array_merge($next_data->toArray(), $all_data->toArray())),
                "xdata"     =>  $x_data,
                "ydata"     =>  $y_data
            ];

            return $result;
        }
    }

    public function getjiedudata(Request $request) {
        $id = $request->input('idx_id');
        if(!is_null($id)) {
            $data = EconomicJiedu::where("dataname_id", $id)
                ->first()
                ->toArray();

            $data_info = EconomicCalendar::where('dataname_id', $id)->select('dataname', 'country', 'unit')->first()->toArray();
            $data['dataname'] = $data_info['dataname'];
            $data['country_cn'] = $data_info['country'];
            $data['unit'] = $data_info['unit'];

            return $this->dataToData([$data]);
        }
    }

    private function dataToData($data){
        $key_map = [
            'id'                => 'id',
            'dataname_id'       => 'IDX_ID',
            'pub_time'          => 'stime',
            'quota_name'        => 'title',
            'country'           => 'country_cn',
            'importance'        => 'idx_relevance',
            'former_value'      => 'previous_price',
            'predicted_value'   => 'surver_price',
            'published_value'   => 'actual_price',
            'unit'              => 'UNIT',
            'pub_frequency'     => 'UPDATE_PERIOD',
            'data_influence'    => 'PARAGHRASE',
            'pub_agent'         => 'PUBLISH_ORG',
            'data_define'       => 'PARAGHRASE',
            'count_way'         => 'PIC_INTERPRET',
            'country_cn'        => 'COUNTRY_CN',
            'dataname'          => 'IDX_DESC_CN'
        ];

        $ret = [];
        foreach($data as $d) {
            $r = [];
            foreach($d as $k=>$v) {
                if(in_array($k, array_keys($key_map))) {
                    $r[$key_map[$k]] = $v;
                }
            }

            $ret[] = $r;
        }

        return $ret;
    }
}
