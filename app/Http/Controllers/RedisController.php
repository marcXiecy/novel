<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redis;

class RedisController extends Controller
{
    public function index()
    {
        $array = array(
            'user1' => '张三',
            'user2' => '李四',
            'user3' => '王五'
        );
        redis::mset($array); // 存储多个 key 对应的 valu
    }

    public function index2()
    {

        var_dump(Redis::get('user1'));
    }
}
