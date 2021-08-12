<?php
/**
 * User: Marc
 * Date: 2021/02/04
 * Time: 21:20
 */

namespace App\Services;

use App\Models\bookmill;
use App\Models\shelf;
use Request;
use Session;

class NovelService extends BaseService
{

    public function book_info($author,$title,string $source)
    {
        $book = bookmill::where('source',$this->source)->where(['title' => $title, 'author' => $author])->first();
        if ($book) {
            return $this->apiOut($book);
        } else {
            return $this->apiOut('', 0);
        }
    }



    public function shelf(string $source)
    {
        $user = Session::get('wxUser');
        if (empty($user)) {
            return $this->apiOut('', 0, '需要重新登陆');
        }
        if ($user) {
            $shelf = shelf::with('book')->where('source',$source)->where('user_id', $user->id)->orderBy('updated_at','DESC')->get();
            
            // $shelf->map(function(shelf $s){
            //     refreshBookEvent::dispatch($s);
            // });

            return $this->apiOut($shelf);
        } else {
            return $this->apiOut('', 0, '失败');
        }
    }

    public function addBookToMill($title, $author, $url, $image,$newest,string $source)
    {
        $condition = [
            'title' => $title,
            'author' => $author,
            'url' => $url,
            'image' => $image,
            'newest' => $newest,
            'source' => $this->source,
        ];
        $book = bookmill::where('source',$this->source)->where(['title' => $title, 'author' => $author])->first();
        if ($book) {
            $result = bookmill::where('source',$this->source)->where('id', $book->id)->update($condition);
            $book_id = $book->id;
        } else {
            $result = bookmill::create($condition);
            $book_id = $result['id'];
        }
        return $book_id;
    }

    public function addBookToShelf($book_id,string $source)
    {
        $book = bookmill::where('source',$source)->where('id', $book_id)->first();
        if (!$book) {
            return $this->apiOut('', 0);
        }
        $user = Session::get('wxUser');
        if (!$user) {
            return $this->apiOut('', 0);
        }
        $shelf = shelf::where('source',$source)->where(['user_id' => $user->id, 'book_id' => $book->id])->first();
        if ($shelf) {
            return $this->apiOut('', 0);
        }
        $r = shelf::create(['user_id' => $user->id, 'book_id' => $book->id, 'url' => $book->url, 'source' => $this->source,]);
        return $this->apiOut($r, $r ? 1 : 0);
    }

    public function removeBookFromShelf($id,string $source)
    {
        $r = shelf::where('source',$source)->where(['id' => $id])->delete();
        return $this->apiOut('', $r ? 1 : 0);
    }

    public function checkBookInShelf($url,string $source)
    {
        $book = bookmill::where('source',$source)->where('url', $url)->first();
        if (!$book) {
            return $this->apiOut('', 0);
        }
        $user = Session::get('wxUser');
        $shelf = shelf::where('source',$source)->where(['user_id' => $user->id, 'book_id' => $book->id])->first();
        if ($shelf) {
            return $this->apiOut($shelf);
        } else {
            return $this->apiOut('', 0);
        }
    }
}
