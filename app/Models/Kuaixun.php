<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Kuaixun extends Model
{
    public function getKuaixun($page=1, $num=20, $start_date=null) {

        if(is_null($start_date)) {
            $start_date = date("Y-m-d 00:00:00", strtotime("-1 day"));
        }

        $where_date = " publish_time >= '".$start_date."' ";
        $columns = ' id, publish_time, importance, dateid as source_id, body, created_time as created_at, updated_time as updated_at ';
        $limit = ' order by publish_time desc limit '.$page*$num;

        $sql = 'select * from (';
        $sql .= '(select '.$columns.' ,"jin10" as source_site from crawl_jin10_kuaixun where real_time is null and'.$where_date.$limit.") ";
        $sql .= 'union ';
        $sql .= '(select '.$columns.', "fx678" as source_site from crawl_fx678_kuaixun where calendar_id is null and '.$where_date.$limit.") ";
        $sql .= 'union ';
        $sql .= '(select '.$columns.', "wallstreetcn" as source_site from crawl_wallstreetcn_kuaixun where'.$where_date.$limit.')';
        $sql .= ')all_tb order by publish_time desc limit '.($page-1)*$num .',' . $num . ';';

        $ret = DB::connection()->select($sql);
        return $ret;
    }
}