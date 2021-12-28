<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Console\Command;
use Log;

class Jisilu extends Command
{

    protected $signature = 'stock:jisilu-kezhuanzhai';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        Log::channel('daily')->info('Jisilu: start' . Carbon::now()->toDateTime());
        $httpClient = new Client();
        $url = 'https://www.jisilu.cn/data/cbnew/pre_list/?___jsl=LST___t=' . time();
        $res = $httpClient->get(
            $url,
            [
                'headers' => [
                    'Content-type' => 'application/json',
                    "Accept" => "application/json"
                ]
            ]

        );
        $res = json_decode($res->getBody()->getContents(), true);
        $todays = [];
        foreach ($res['rows'] as $item) {
            if (
                Carbon::now()->format('Y-m-d') == $item['cell']['progress_dt'] &&
                mb_substr($item['cell']['progress_nm'], 10, 2) == '申购'
            ) {
                $todays[] = '- ' . $item['cell']['bond_nm'] . ' 评级：' . $item['cell']['rating_cd'] . ' 股转比：' .$item['cell']['pma_rt'] . '%';
            }
        }
        if(count($todays) > 0){
            $desc = implode('\n', $todays);
        }
        else{
            $desc = '今日无可转债申购';
        }
        echo $this->sc_send('今日可转债概况', $desc);
    }

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
