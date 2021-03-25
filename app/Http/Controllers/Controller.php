<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

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
}
