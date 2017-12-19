<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Article;

class ArticleController extends Controller
{
    /**
     * 文章列表
     * @param cat 文章分类
     * @param page 页码
     * @param number 每页数量
     * @return array
     */
    public function articlelist(Request $request) {
        $catname = $request->input('cat', null);
        $page = $request->input('page', 1);
        $num = $request->input('number', 10);

        $article = Article::orderBy('publish_time', 'desc');
        if(!is_null($catname)) {
            $article = $article->where('type', $catname);
        }

        $skip = ($page-1) * $num;
        $article = $article
            ->skip($skip)
            ->take($num)
            ->select('id', 'title', 'publish_time as created', 'type as catTitle', 'description as metadesc', 'image as imgurl', 'author')
            ->get();

        return ['success'=>1, 'value'=>$article];
    }

    /**
     * 文章详情
     * @param id 文章id
     */
    public function articledetail(Request $request) {
        $id = $request->input('id', null);

        if(!is_null($id)) {
            $article = Article::where('id', $id)
                ->select('id', 'title', 'publish_time as created', 'description as metadesc', 'body as introtext', 'keywords as metakey', 'author', 'image as imgurl')
                ->first();

            if($article) {
                $article->introtext = str_replace("width: 100%; height: 100%;", "", $article->introtext);
                return ['success'=>1, 'value'=>$article];
            }
        }
    }
}
