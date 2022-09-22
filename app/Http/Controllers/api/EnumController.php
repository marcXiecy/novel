<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;


class EnumController extends Controller
{
    public function sources(){
        return $this->apiOut([
            // ['value' => 'biquge5200','title'=>'新笔趣阁5200'],
            // ['value' => 'xbiqugeb5200','title'=>'新笔趣阁B5200'],
            //  ['value' => 'qbiquge','title'=>'新笔趣阁Q'],
            //  ['value' => 'cv148','title'=>'新笔趣阁cv148'],
            //  ['value' => 'fyrsks','title'=>'笔趣阁F'],
             ['value' => '56shuku','title'=>'56书库'],
             ['value' => 'gonb','title'=>'中文阅读'],
            ['value' => 'biquge','title'=>'新笔趣阁'],
             ['value' => 'dingdian','title'=>'顶点小说'],

        ]);
    }
}
