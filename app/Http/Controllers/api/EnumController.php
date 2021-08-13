<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;


class EnumController extends Controller
{
    public function sources(){
        return $this->apiOut([
            ['value' => 'biquge','title'=>'新笔趣阁'],
            ['value' => 'biquge5200','title'=>'新笔趣阁5200'],
            ['value' => 'dingdian','title'=>'顶点小说'],
        ]);
    }
}
