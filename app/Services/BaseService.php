<?php
/**
 * User: Marc
 * Date: 2021/02/04
 * Time: 21:20
 */

namespace App\Services;

class BaseService
{
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
