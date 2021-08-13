<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    public function apiOut($data = '', $flag = 1, $msg = 'success',$book_id = null)
    {
        return [
            'data' => $data,
            'flag' => $flag,
            'msg' => $msg,
            'book_id' => $book_id,
        ];
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

    public function addBookToMill($title, $author, $url, $image,$newest)
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

}
