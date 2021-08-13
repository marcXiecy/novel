<?php

namespace App\Http\Controllers\api;

use App\Events\refreshBookEvent;
use App\Functions\simple_html_dom;
use App\Http\Controllers\Controller;
use App\Models\bookmill;
use App\Models\NovelDetail;
use App\Models\shelf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

// 小程序代码上传密钥 wx563fec61a0a7f915
class NovelDingDianController extends Controller
{
    private $siteUrl = "https://www.booktxt.net";
    private $source = 'dingdian'; 
    public function search(Request $request)
    {
        $keyword = $request->input('keyword');
        $books = app()->make('CommonService')->curl('https://so.biqusoso.com/s1.php?ie=utf-8&siteid=booktxt.net&q='.$keyword);
        $htmlObj = new simple_html_dom();    //工具类对象初始化
        $htmlObj->load($books);
        $tr = $htmlObj->find('.s2 a');
        $result = [];
        foreach ($tr as $k => $ele) {
            $temp = [];
            $temp['title'] = $ele->plaintext;
            $temp['href'] = $ele->href;
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
            $book = bookmill::where('source',$this->source)->where(['id' => $book_id])->first();
            $catalog_url = $book->url;
        }
        $catalog = app()->make('CommonService')->curl($catalog_url, 0, 0, 0, 1);

        $htmlObj = new simple_html_dom();    //工具类对象初始化
        $htmlObj->load($catalog);

        $location = $htmlObj->find("meta[property=og:novel:read_url]")[0]->attr['content']; 

        
        $title = $htmlObj->find('div[id=info] h1', 0);
        $title = $title->plaintext;
        $author = $htmlObj->find('div[id=info] p', 0);
        $author = $author->plaintext;
        $author = explode("：", $author);
        $author = $author[1];
        $list = $htmlObj->find('div[id=list] a');
        $image = $htmlObj->find('div[id=fmimg] img', 0);
        $image = $this->siteUrl . $image->src;
      
        $result = [];
        $result['title'] = $title;
        $result['author'] = $author;
        $result['image'] = $image;
        foreach ($list as $ele) {
            $temp = [];
            $temp['title'] = $ele->plaintext;
            $temp['href'] =  $location . $ele->href;
            $result['catalog'][] = $temp;
        }
        $newest = end($result['catalog'])['title'];
        $book_id = $this->addBookToMill($title, $author, $catalog_url, $image, $newest);
        return $this->apiOut($result,1,'',$book_id);
    }

    public function article(Request $request)
    {
        $article_url = $request->input('article_url');
        
        if(substr($article_url,-4) != 'html'){
            return $this->apiOut('',0,'已到最新');
        }
        $title = $request->input('title');
        $author = $request->input('author');
        $book = bookmill::where('source',$this->source)->where(['title' => $title, 'author' => $author])->first();

        if ($book) {
            $user = Session::get('wxUser');
            if ($user) {
                $shelf = shelf::where('source',$this->source)->where('book_id', $book->id)->where('source',$this->source)->where('user_id', $user->id)->first();
                if ($shelf) {
                    $shelf = shelf::where('source',$this->source)->where('book_id', $book->id)->where('source',$this->source)->where('user_id', $user->id)->update(['current_page_url' => $article_url]);
                }
            }
        }
        $article = app()->make('CommonService')->curl($article_url, 0, 0, 0, 1);
        $htmlObj = new simple_html_dom();    //工具类对象初始化
        $htmlObj->load($article);
        $bookname = $htmlObj->find('div[class=con_top] a', 1);
        $bookname = $bookname->plaintext;


        $content = $htmlObj->find('div[id=content]');
        $preview = $htmlObj->find('div[class=bottem2] a', 0);
        $next = $htmlObj->find('div[class=bottem2] a', 2);
        $title = $htmlObj->find('div[class=bookname] h1', 0);
        $result = [];
        $temp = [];
        $temp['text'] = $title->plaintext;
        $temp['type'] = "title";
        $result['article'][] = $temp;
        $result['title'] = $title->plaintext;

        $texts = $content[0];
        $texts = str_replace('<br />', '|', $texts);
        $texts = strip_tags($texts);
        $texts = explode('||', $texts);
        foreach ($texts as $ele) {
            if(empty($ele))
                continue;
            $temp = [];
            $temp['text'] = str_replace('　　','',$ele);
            $temp['type'] = "content";
            $result['article'][] = $temp;
        }
        $result['preview'] = $this->siteUrl . $preview->href;
        $result['next'] = $this->siteUrl .  $next->href;
        //详情页title和目录页title有时候不一样，需要返回目录页title进行定位
        $detail_log = NovelDetail::where('source',$this->source)->where('source_href',$article_url)->first();
        if($detail_log){
            $result['c_title'] = $detail_log->title;
        }
        return $this->apiOut($result);
    }

    public function book_info(Request $request)
    {
        $author = $request->input('author');
        $title = $request->input('title');
        return app()->make('NovelService')->book_info($author,$title,$this->source);
    }

    public function shelf()
    {
        return app()->make('NovelService')->shelf($this->source);
    }

    private function addBookToMill($title, $author, $url, $image,$newest)
    {
        return app()->make('NovelService')->addBookToMill($title, $author, $url, $image,$newest,$this->source);
    }

    public function addBookToShelf(Request $request)
    {
        $book_id = $request->input('book_id');
        return app()->make('NovelService')->addBookToShelf($book_id, $this->source);
    }

    public function removeBookFromShelf(Request $request)
    {
        $id = $request->input('id');
        return app()->make('NovelService')->removeBookFromShelf($id, $this->source);
    }

    public function checkBookInShelf(Request $request)
    {
        $url = $request->input('url');
        return app()->make('NovelService')->checkBookInShelf($url, $this->source);
    }

    public function saveCatalog(Request $request)
    {
        $catalog_url = $request->input('catalog_url');
        $book_id = $request->input('book_id');
        if($book_id && !$catalog_url){
            $book = bookmill::where('source',$this->source)->where(['id' => $book_id])->first();
            $catalog_url = $book->url;
        }
        $catalog = app()->make('CommonService')->curl($catalog_url, 0, 0, 0, 1);
        $htmlObj = new simple_html_dom();    //工具类对象初始化
        $htmlObj->load($catalog);
        
        $location = $htmlObj->find("meta[property=og:novel:read_url]")[0]->attr['content']; 

        $title = $htmlObj->find('div[id=info] h1', 0);
        $title = $title->plaintext;
        $author = $htmlObj->find('div[id=info] p', 0);
        $author = $author->plaintext;
        $author = explode("：", $author);
        $author = $author[1];
        $list = $htmlObj->find('div[id=list] a');
        $image = $htmlObj->find('div[id=fmimg] img', 0);
        $image = $this->siteUrl . $image->src;
        $book = bookmill::where('source',$this->source)->where('title', $title)->where('source',$this->source)->where('author', $author)->first();
        $result = [];
        $result['title'] = $title;
        $result['author'] = $author;
        $result['image'] = $image;

        $total = 0;
        $add = 0;
        $uns = [];
        foreach ($list as $key => $ele) {
            $total++;
            $nd = NovelDetail::where('source',$this->source)->where([
                'book_id' => $book->id,
                'title' => $ele->plaintext,
            ])->first();
       
            if (!$nd) {
                $add++;
                NovelDetail::create([
                    'book_id' => $book->id,
                    'catalog_id' => $key,
                    'title' => $ele->plaintext,
                    'source_href' => $location . $ele->href,
                    'source' => $this->source,
                ]);
            }
            else{
                $uns[] = $ele->plaintext;
            }
        }
        return $this->apiOut(['add' => $add, 'total' => $total,'uns'=>$uns]);
    }
}
