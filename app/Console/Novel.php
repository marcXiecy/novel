<?php

namespace App\Console;

use App\Functions\simple_html_dom;

class NovelReptile
{
    const novel_urls = [];
    public function reptile()
    {
        $catalog_url = self::novel_urls[0];
        $catalog = app()->make('CommonService')->curl($catalog_url);

        $htmlObj = new simple_html_dom();	//工具类对象初始化
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
            $temp['href'] = $this->siteUrl . $ele->href;
            $result['catalog'][] = $temp;
        }

    }
}
