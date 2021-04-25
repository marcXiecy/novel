<?php

namespace App\Http\Controllers\api;

use App\Functions\simple_html_dom;
use App\Http\Controllers\Controller;
use App\Models\bookmill;
use App\Models\NovelDetail;
use App\Models\shelf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

// 小程序代码上传密钥 wx563fec61a0a7f915
class NovelBiqugeController extends Controller
{
    private $siteUrl = "http://www.xbiquge.la/";
    public function search(Request $request)
    {
        $keyword = $request->input('keyword');
        $books = app()->make('CommonService')->curl($this->siteUrl . 'modules/article/waps.php', ['searchkey' => $keyword], true);
        $htmlObj = new simple_html_dom();    //工具类对象初始化
        $htmlObj->load($books);
        $tr = $htmlObj->find('.grid tr');
        $result = [];
        foreach ($tr as $k => $ele) {
            if ($k == 0) {
                continue;
            }
            $temp = [];

            $temp['title'] = $ele->find('.even a', 0)->plaintext;
            $temp['href'] = $ele->find('.even a', 0)->href;
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
        if($book_id && !$catalog_url){
            $book = bookmill::where(['id' => $book_id])->first();
            $catalog_url = $book->url;
        }
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
            $temp['href'] = $this->siteUrl . $ele->href;
            $result['catalog'][] = $temp;
        }
        $newest = array_pop($result['catalog'])['title'];
        $book_id = $this->addBookToMill($title, $author, $catalog_url, $image, $newest);
        return $this->apiOut($result,1,'',$book_id);
    }

    public function article(Request $request)
    {
        $article_url = $request->input('article_url');
        $title = $request->input('title');
        $author = $request->input('author');
        $book = bookmill::where(['title' => $title, 'author' => $author])->first();

        if ($book) {
            $user = Session::get('wxUser');
            if ($user) {
                $shelf = shelf::where('book_id', $book->id)->where('user_id', $user->id)->first();
                if ($shelf) {
                    $shelf = shelf::where('book_id', $book->id)->where('user_id', $user->id)->update(['current_page_url' => $article_url]);
                }
            }
        }
        $article = app()->make('CommonService')->curl($article_url, 0, 0, 0, 1);
        $htmlObj = new simple_html_dom();    //工具类对象初始化
        $htmlObj->load($article);
        $bookname = $htmlObj->find('div[class=con_top] a', 1);
        $bookname = $bookname->plaintext;
        $delete = $htmlObj->find('div[id=content] p', 0);

        $content = $htmlObj->find('div[id=content]');
        $preview = $htmlObj->find('div[class=bottem2] a', 1);
        $next = $htmlObj->find('div[class=bottem2] a', 3);
        $title = $htmlObj->find('div[class=bookname] h1', 0);
        $result = [];

        $temp = [];
        $temp['text'] = $title->plaintext;
        $temp['type'] = "title";
        $result['article'][] = $temp;
        $result['title'] = $title->plaintext;

        $texts = str_replace($delete->plaintext, '', $content[0]->plaintext);
        $texts = str_replace('&nbsp;&nbsp;&nbsp;&nbsp;', '|||', $texts);
        $texts = explode('|||', $texts);
        foreach ($texts as $ele) {
            $temp = [];
            $temp['text'] = $ele;
            $temp['type'] = "content";
            $result['article'][] = $temp;
        }
        $result['preview'] = $this->siteUrl . $preview->href;
        $result['next'] = $this->siteUrl .  $next->href;
        $detail_log = NovelDetail::where('source_href',$article_url)->first();
        $result['c_title'] = $detail_log->title;
        return $this->apiOut($result);
    }

    public function book_info(Request $request)
    {
        $author = $request->input('author');
        $title = $request->input('title');
        $book = bookmill::where(['title' => $title, 'author' => $author])->first();
        if ($book) {
            return $this->apiOut($book);
        } else {
            return $this->apiOut('', 0);
        }
    }

    public function shelf(Request $request)
    {
        $user = Session::get('wxUser');
        if (empty($user)) {
            return $this->apiOut('', 0, '需要重新登陆');
        }
        if ($user) {
            $shelf = shelf::with('book')->where('user_id', $user->id)->get();
            return $this->apiOut($shelf);
        } else {
            return $this->apiOut('', 0, '失败');
        }
    }

    private function addBookToMill($title, $author, $url, $image,$newest)
    {
        $condition = [
            'title' => $title,
            'author' => $author,
            'url' => $url,
            'image' => $image,
            'newest' => $newest,
        ];
        $book = bookmill::where(['title' => $title, 'author' => $author])->first();
        if ($book) {
            $result = bookmill::where('id', $book->id)->update($condition);
            $book_id = $book->id;
        } else {
            $result = bookmill::create($condition);
            $book_id = $result['id'];
        }
        return $book_id;
    }

    public function addBookToShelf(Request $request)
    {
        $book_id = $request->input('book_id');
        $book = bookmill::where('id', $book_id)->first();
        if (!$book) {
            return $this->apiOut('', 0);
        }
        $user = Session::get('wxUser');
        if (!$user) {
            return $this->apiOut('', 0);
        }
        $shelf = shelf::where(['user_id' => $user->id, 'book_id' => $book->id])->first();
        if ($shelf) {
            return $this->apiOut('', 0);
        }
        $r = shelf::create(['user_id' => $user->id, 'book_id' => $book->id, 'url' => $book->url]);
        return $this->apiOut($r, $r ? 1 : 0);
    }

    public function removeBookFromShelf(Request $request)
    {
        $id = $request->input('id');
        $r = shelf::where(['id' => $id])->delete();
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
        $shelf = shelf::where(['user_id' => $user->id, 'book_id' => $book->id])->first();
        if ($shelf) {
            return $this->apiOut($shelf);
        } else {
            return $this->apiOut('', 0);
        }
    }




    // const novel_hrefs = ['http://www.xbiquge.la/10/10489/'];

    public function saveCatalog(Request $request)
    {
        $catalog_url = $request->input('catalog_url');
        $book_id = $request->input('book_id');
        if($book_id && !$catalog_url){
            $book = bookmill::where(['id' => $book_id])->first();
            $catalog_url = $book->url;
        }
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
        $book = bookmill::where('title', $title)->where('author', $author)->first();
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
            ])->first();
       
            if (!$nd) {
                $add++;
                NovelDetail::create([
                    'book_id' => $book->id,
                    'catalog_id' => $key,
                    'title' => $ele->plaintext,
                    'source_href' => $this->siteUrl . $ele->href,
                ]);
            }
            else{
                $uns[] = $ele->plaintext;
            }
        }
        return $this->apiOut(['add' => $add, 'total' => $total,'uns'=>$uns]);
    }
}
