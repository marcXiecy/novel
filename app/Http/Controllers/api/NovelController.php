<?php

namespace App\Http\Controllers\api;

use App\Functions\simple_html_dom;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class NovelController extends Controller
{
    public function search(Request $request)
    {
        $books = app()->make('CommonService')->curl('http://www.31xs.com/search.php', ['keyword'=>'圣墟'], true);

        $htmlObj = new simple_html_dom();	//工具类对象初始化
        $htmlObj->load($books);
        $tr = $htmlObj->find('#bookcase_list tr');
        $result = [];
        foreach ($tr as $ele) {
            $temp = [];
            $temp['title'] = $ele->find('a', 0)->plaintext;
            $temp['href'] = $ele->find('a', 0)->href;
            $result[] = $temp;
        }
        return $result;
    }

    public function catalog(Request $request)
    {
        $catalog_url = $request->input('catalog_url');
        $catalog = app()->make('CommonService')->curl($catalog_url);
        $htmlObj = new simple_html_dom();	//工具类对象初始化
        $htmlObj->load($catalog);
        $list = $htmlObj->find('div[id=list] a');
        $result = [];
        foreach ($list as $ele) {
            $temp = [];
            $temp['title'] = $ele->plaintext;
            $temp['href'] = "http://www.31xs.com/" . $ele->href;
            $result[] = $temp;
        }
        return $result;
    }

    public function article(Request $request)
    {
        $article_url = $request->input('article_url');
        $article = app()->make('CommonService')->curl($article_url);
        $htmlObj = new simple_html_dom();	//工具类对象初始化
        $htmlObj->load($article);
        $content = $htmlObj->find('div[id=content] p');
        $preview = $htmlObj->find('div[class=bottem2] a', 0);
        $next = $htmlObj->find('div[class=bottem2] a', -1);
        $result = [];
        foreach ($content as $ele) {
            $result['content'][] = $ele->plaintext;
        }
        $result['preview'][] = "http://www.31xs.com/" . $preview->href;
        $result['next'][] = "http://www.31xs.com/" . $next->href;
        return $result;
    }
}
