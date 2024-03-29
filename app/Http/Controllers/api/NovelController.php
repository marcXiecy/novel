<?php

namespace App\Http\Controllers\api;

use App\Functions\simple_html_dom;
use App\Http\Controllers\Controller;
use App\Models\bookmill;
use App\Models\shelf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Log;
use PDO;

// 小程序代码上传密钥 wx563fec61a0a7f915
class NovelController extends Controller
{
    private $siteUrl = "http://www.31xiaoshuo.com/";
    public function search(Request $request)
    {
        $keyword = $request->input('keyword');
        $books = app()->make('CommonService')->curl($this->siteUrl . 'search.php', ['keyword'=>$keyword], true);
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
        if (!$result) {
            return $this->apiOut($result, 0, '无结果');
        }
        return $this->apiOut($result);
    }

    public function catalog(Request $request)
    {
        $catalog_url = $request->input('catalog_url');
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
        // $this->addBookToMill($title, $author, $catalog_url, $image);
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
        return $this->apiOut($result);
    }

    public function article(Request $request)
    {
        $article_url = $request->input('article_url');
        $title = $request->input('title');
        $author = $request->input('author');
        $book = bookmill::where(['title'=>$title,'author'=>$author])->first();
 
        if ($book) {
            $user = Session::get('wxUser');
            if ($user) {
                $shelf = shelf::where('book_id', $book->id)->where('user_id', $user->id)->first();
                if ($shelf) {
                    $shelf = shelf::where('book_id', $book->id)->where('user_id', $user->id)->update(['current_page_url'=>$article_url]);
                }
            }
        }
        $article = app()->make('CommonService')->curl($article_url);
        $htmlObj = new simple_html_dom();	//工具类对象初始化
        $htmlObj->load($article);
        $bookname = $htmlObj->find('div[class=con_top] a', 1);
        $bookname = $bookname->plaintext;
        
        $content = $htmlObj->find('div[id=content] p');
        $preview = $htmlObj->find('div[class=bottem2] a', 0);
        $next = $htmlObj->find('div[class=bottem2] a', -1);
        $title = $htmlObj->find('div[class=bookname] h1', 0);
        $result = [];

        $temp = [];
        $temp['text'] = $title->plaintext;
        $temp['type'] = "title";
        $result['article'][] = $temp;

        foreach ($content as $ele) {
            $temp = [];
            $temp['text'] = $ele->plaintext;
            $temp['type'] = "content";
            $result['article'][] = $temp;
        }
        $result['preview'] = $this->siteUrl . $preview->href;
        $result['next'] = $this->siteUrl .  $next->href;
        return $this->apiOut($result);
    }

    public function book_info(Request $request)
    {
        $author = $request->input('author');
        $title = $request->input('title');
        $book = bookmill::where(['title'=>$title,'author'=>$author])->first();
        if ($book) {
            return $this->apiOut($book);
        } else {
            return $this->apiOut('', 0);
        }
    }


    public function addBookToShelf(Request $request)
    {
        $url = $request->input('url');
        $book = bookmill::where('url', $url)->first();
        if (!$book) {
            return $this->apiOut('', 0);
        }
        $user = Session::get('wxUser');
        if (!$user) {
            return $this->apiOut('', 0);
        }
        $shelf = shelf::where(['user_id'=>$user->id,'book_id'=>$book->id])->first();
        if ($shelf) {
            return $this->apiOut('', 0);
        }
        $r = shelf::create(['user_id'=>$user->id,'book_id'=>$book->id,'url'=>$url]);
        return $this->apiOut($r, $r ? 1 : 0);
    }

    public function removeBookFromShelf(Request $request)
    {
        $id = $request->input('id');
        $r = shelf::where(['id'=>$id])->delete();
        return $this->apiOut('', $r ? 1 : 0);
    }

    public function checkBookInShelf(Request $request)
    {
        $url = $request->input('url');
        $book = bookmill::where('url', $url)->first();
        if (!$book) {
            return $this->apiOut('', 0);
        }
        $user = Session::get('wxUser');
        $shelf = shelf::where(['user_id'=>$user->id,'book_id'=>$book->id])->first();
        if ($shelf) {
            return $this->apiOut($shelf);
        } else {
            return $this->apiOut('', 0);
        }
    }
}
