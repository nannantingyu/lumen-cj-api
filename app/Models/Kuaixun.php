<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Kuaixun extends Model
{
    public function getKuaixun($page=1, $num=20) {

        $sql = 'select * from (';
        $sql .= '(select id, publish_time, importance, dateid as source_id, body, created_time as created_at, updated_time as updated_at, "jin10" as source_site from crawl_jin10_kuaixun where real_time is null order by publish_time desc limit '.$page*$num.')';
        $sql .= 'union ';
        $sql .= '(select id, publish_time, importance, dateid as source_id, body, created_time as created_at, updated_time as updated_at, "fx678" as source_site from crawl_fx678_kuaixun where calendar_id is null order by publish_time desc limit '.$page*$num.') ';
        $sql .= 'union ';
        $sql .= '(select id, publish_time, importance, dateid as source_id, body, created_time as created_at, updated_time as updated_at, "wallstreetcn" as source_site from crawl_wallstreetcn_kuaixun order by publish_time desc limit '.$page*$num.')';
        $sql .= ')all_tb order by publish_time desc limit '.($page-1)*$num .',' . $num . ';';

        $ret = DB::connection()->select($sql);
        return $ret;
    }
}