<?php

namespace App\Http\Controllers\api;

use App\Functions\simple_html_dom;
use App\Http\Controllers\Controller;
use App\Models\bookmill;
use App\Models\NovelDetail;
use App\Models\shelf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class NovelGonbController extends Controller
{
    public $siteUrl = "https://www.gonb.org";
    public $source = 'gonb';
    public function search(Request $request)
    {
        $keyword = $request->input('keyword');
        $books = app()->make('CommonService')->httpClient($this->siteUrl . '/search.php', ['keyword' => $keyword]);
        $htmlObj = new simple_html_dom();    //工具类对象初始化
        $htmlObj->load($books);
        $tr = $htmlObj->find('#main li');
        $result = [];
        foreach ($tr as $k => $ele) {
            if ($k == 0) {
                continue;
            }
            $temp = [];

            $temp['title'] = $ele->find('.s2 a', 0)->plaintext;
            $temp['href'] = $this->siteUrl . $ele->find('.s2 a', 0)->href;
            $result[] = $temp;
        }
        if (!$result) {
            return $this->apiOut($result, 0, '无结果');
        }
        return $this->apiOut($result);
    }

    public function catalog(Request $request)
    {
        $catalog_url = $request->input('catalog_url');
        $book_id = $request->input('book_id');
        if ($book_id && !$catalog_url) {
            $book = bookmill::where(['id' => $book_id])->where('source', $this->source)->first();
            $catalog_url = $book->url;
        }
        $catalog = app()->make('CommonService')->httpClient($catalog_url);
        $htmlObj = new simple_html_dom();    //工具类对象初始化
        $htmlObj->load($catalog);
        $title = $htmlObj->find('#info h1', 0);
        $title = $title->plaintext;
        $author = $htmlObj->find('#info p a', 0);
        $author = $author->plaintext;
        // $author = explode("：", $author);
        // $author = $author[1];
        $list = $htmlObj->find('#list dd a');

        $image = $htmlObj->find('#fmimg img', 0);
        $image = $this->siteUrl . $image->src;
        $result = [];
        $result['title'] = $title;
        $result['author'] = $author;
        $result['image'] = $image;
        foreach ($list as $ele) {
            $temp = [];
            $temp['title'] = $ele->plaintext;
            $temp['href'] =  $this->siteUrl . $ele->href;
            $result['catalog'][] = $temp;
        }
        $newest = end($result['catalog'])['title'];
        $book_id = $this->addBookToMill($title, $author, $catalog_url, $image, $newest);
        return $this->apiOut($result, 1, '', $book_id);
    }

    public function article(Request $request)
    {
        $article_url = $request->input('article_url');

        if (substr($article_url, -4) != 'html') {
            return $this->apiOut('', 0, '已到最新');
        }
        $title = $request->input('title');
        $author = $request->input('author');
        $book = bookmill::where(['title' => $title, 'author' => $author])->where('source', $this->source)->first();

        if ($book) {
            $user = Session::get('wxUser');
            if ($user) {
                $shelf = shelf::where('book_id', $book->id)->where('user_id', $user->id)->where('source', $this->source)->first();
                if ($shelf) {
                    $shelf = shelf::where('book_id', $book->id)->where('user_id', $user->id)->where('source', $this->source)->update(['current_page_url' => $article_url]);
                }
            }
        }
        $article = app()->make('CommonService')->httpClient($article_url);
        // $encode = mb_detect_encoding($article, array("ASCII", 'UTF-8', "GB2312", "GBK", 'BIG5'));
        // if ($encode == 'UTF-8') {
        // } elseif ($encode == 'CP936') {
        //     $article = iconv('UTF-8', 'latin1//IGNORE', $article);
        // } else {
        //     $article = mb_convert_encoding($article, 'UTF-8', $encode);
        // }
        $htmlObj = new simple_html_dom();    //工具类对象初始化
        $htmlObj->load($article);


        // $bookname = $htmlObj->find('div[class=con_top] a', 1);
        // $bookname = $bookname->plaintext;

        $title = $htmlObj->find('.bookname h1',0);
        $preview = $htmlObj->find('div[class=bottem2] a', 1);
        $next = $htmlObj->find('div[class=bottem2] a', 3);

        $content = $htmlObj->find('div[id=content]',0);
        $result = [];

        $temp = [];
        $temp['text'] = $title->plaintext;
        $temp['type'] = "title";
        $result['article'][] = $temp;
        $result['title'] = $title->plaintext;
        $content = explode("\r\n\r\n",$content->plaintext);
        array_pop($content);
        foreach ($content as $ele) {
            if(strstr($ele,'有的人死了，但没有完全死')){
                break;
            }
            $temp = [];
            $temp['text'] = str_replace('&nbsp;', '', $ele);
            $temp['text'] = str_replace('　　', '', $temp['text']);
            $temp['type'] = "content";
            $result['article'][] = $temp;
        }

        $result['preview'] = $this->siteUrl . $preview->href;
        $result['next'] = $this->siteUrl . $next->href;
        //详情页title和目录页title有时候不一样，需要返回目录页title进行定位
        $detail_log = NovelDetail::where('source_href', $article_url)->where('source', $this->source)->first();
        if ($detail_log) {
            $result['c_title'] = $detail_log->title;
        }
        return $this->apiOut($result);
    }

    // const novel_hrefs = ['http://www.xbiquge.la/10/10489/'];

    public function saveCatalog(Request $request)
    {
        $catalog_url = $request->input('catalog_url');
        $book_id = $request->input('book_id');
        if ($book_id && !$catalog_url) {
            $book = bookmill::where(['id' => $book_id])->where('source', $this->source)->first();
            $catalog_url = $book->url;
        }
        $catalog = app()->make('CommonService')->httpClient($catalog_url);
        $htmlObj = new simple_html_dom();    //工具类对象初始化
        $htmlObj->load($catalog);
        $title = $htmlObj->find('#info h1', 0);
        $title = $title->plaintext;
        $author = $htmlObj->find('#info p a', 0);
        $author = $author->plaintext;
        $list = $htmlObj->find('#list dd a');

        $image = $htmlObj->find('#fmimg img', 0);
        $image = $this->siteUrl . $image->src;
        $book = bookmill::where('title', $title)->where('author', $author)->where('source', $this->source)->first();
        $result = [];
        $result['title'] = $title;
        $result['author'] = $author;
        $result['image'] = $image;
        $total = 0;
        $add = 0;
        $uns = [];
        foreach ($list as $key => $ele) {
            $total++;
            $nd = NovelDetail::where([
                'book_id' => $book->id,
                'title' => $ele->plaintext,
            ])->where('source', $this->source)->first();

            if (!$nd) {
                $add++;
                NovelDetail::create([
                    'book_id' => $book->id,
                    'catalog_id' => $key,
                    'title' => $ele->plaintext,
                    'source_href' => $ele->href,
                    'source' => $this->source,
                ]);
            } else {
                $uns[] = $ele->plaintext;
            }
        }
        return $this->apiOut(['add' => $add, 'total' => $total, 'uns' => $uns]);
    }
}
