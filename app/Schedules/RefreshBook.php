<?php

namespace App\Schedules;

use App\Functions\simple_html_dom;
use App\Models\bookmill;


class RefreshBook
{
    public function __invoke()
    {

        $books = bookmill::all();
        foreach($books as $book){
            $catalog_url = $book->url;
            $catalog = app()->make('CommonService')->curl($catalog_url, 0, 0, 0, 1);
            $htmlObj = new simple_html_dom();    //工具类对象初始化
            $htmlObj->load($catalog);
            $title = $htmlObj->find('div[id=info] h1', 0);
            $title = $title->plaintext;
            $author = $htmlObj->find('div[id=info] p', 0);
            $author = $author->plaintext;
            $author = explode("：", $author);
            $author = $author[1];
            $list = $htmlObj->find('div[id=list] a');
            $image = $htmlObj->find('div[id=fmimg] img', 0);
            $image = $image->src;
            $result = [];
            $result['title'] = $title;
            $result['author'] = $author;
            $result['image'] = $image;
            foreach ($list as $ele) {
                $temp = [];
                $temp['title'] = $ele->plaintext;
                $result['catalog'][] = $temp;
            }
            $newest = end($result['catalog'])['title'];
            $condition = [
                'newest' => $newest,
            ];
            $result = bookmill::where('id', $book->id)->update($condition);
        }
    }
}
