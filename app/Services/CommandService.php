<?php
/**
 * User: Marc
 * Date: 2021/12/28
 * Time: 21:20
 */

namespace App\Services;

class CommandService extends BaseService
{
    function sc_send($title, $desp = '', $key = 'SCT60192TMcbFGI5wjcQY1IO7jxCMIQe7')
    {
        $postdata = http_build_query(array('title' => $title, 'desp' => $desp));
        $opts = array('http' =>
        array(
            'method'  => 'POST',
            'header'  => 'Content-type: application/x-www-form-urlencoded',
            'content' => $postdata
        ));

        $context  = stream_context_create($opts);
        return $result = file_get_contents('https://sctapi.ftqq.com/' . $key . '.send', false, $context);
    }
}
