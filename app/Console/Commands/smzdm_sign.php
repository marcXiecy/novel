<?php

namespace App\Console\Commands;

use App\Services\CommandService;
use Carbon\Carbon;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Console\Command;
use Log;

class smzdm_sign extends Command
{

    protected $signature = 'smzdm:sign';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        Log::channel('daily')->info('smzdm: sign - ' . Carbon::now());
        try {
            $httpClient = new Client();
            $url = 'https://zhiyou.smzdm.com/user/checkin/jsonp_checkin?callback=callbackmethod&_=' . time() * 1000;
            $res = $httpClient->get(
                $url,
                [
                    'headers' => [
                        "User-Agent" => "Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:92.0) Gecko/20100101 Firefox/92.0",
                        "Referer" => "https://www.smzdm.com/",
                        "Cookie" => 'r_sort_type=score; shequ_pc_sug=b; smzdm_user_source=BC67D90B2AA432146F9282571BD39CAC; device_id=989201938161588500766950803b4249cc4623999d2cca370f13b421c; __ckguid=6k55QB2XP58jetu7iSXO8K3; __jsluid_s=2f85a6afaf5ca7fae83dc75c8a51c1fa; homepage_sug=d; ssxmod_itna=QqROYKDICD8Uq0LHlDj24m2zT5i=zDyehlax057eGzDAxn40iDtPxyDGTbWFbFAC+iP8iP4/Wp7v3moir04xWNgox0aDbqGkqReoHeDxrq0rD74irDDxD3Db8dDSDWKD9D0RSBc6yKGWDmRzDYHFDQ5Gq4DFn6FOe4i7DDvdOx075UKDepPhn1ECqjoeLxG1540Hsm84L=EfjIGzkt3ex+bODlIUDCI1tKypDB+kl1HGZnD3Onielmr7sA44giiqQ7hu5YbtiD2KF=0577hY3j1NalQDiEGemCGDD===; ssxmod_itna2=QqROYKDICD8Uq0LHlDj24m2zT5i=zDyehlxn9g52xDsDB04jbuyvl0yGBik4/DO5egTFIGqkxApQ4XpmE0sHFKn+iddNGTYrpkrS1MBjs=9aLinrvFUgnHdkAIfRg=LVCZc2zXhXQ/FdAEl3YDHbe8FLE0f82SIu2AIjoe=LX+Kdtu8TTjbL0NNYUEC36Q=6U27mdA0qWpifcQpZjSYC6gf1drMC6z=3LyC+ALearNTKnwrMPOepDSRb8raD8nfKnpTSKrcdo+T8tNu=CjERC+evYRx=YQobZFGvjFCNoBreV9rHzVkhBRb0b4xob042wGTNjq1Qnn4P0DAgb7uARa+Hdl9MDYM475ry0DYx5YDVrkYRop2d7BxGyh0irecNQwqnAQ/4OtxDKq4+Bq6B8+iekoeYOYDP4R7APpxRGPxZ5+DqnBPNhDo1dTZ3=0fNpDr1EeAoLBOt04XYt3PLy7P3SD=YUhpD=GjncYqYxD7=DYF4eD==; __jsluid_h=3ae210d57f08e23c18896395b0e19ba8; Hm_lvt_9b7ac3d38f30fe89ff0b8a0546904e58=1668652879,1668738017,1669005536,1669103974; sess=BA-hy%2BpT0FCS0UQB%2FKjRNklsSr3GzurB2yfS%2BiiMcahtj3zY4RWpo08s%2BnJf5fzmfLNW%2BcXjWAnfhQfyqymXtkVt0TXwO3VscHhcAPgTNP23u2ip6MK7j941ZY%3D; user=user%3A6179053634%7C6179053634; smzdm_id=6179053634; smzdm_user_view=BE2F9E3F8ECB64377E3D7DA0A9C351F0; ss_ab=ss100; ssmx_ab=mxss41; s_his=ticwatch%2CTicwatch%20GTA%2C%E7%94%B5%E5%AD%90%E4%B9%A6%2C%E5%A2%A8%E6%B0%B4%E5%B1%8F; sensorsdata2015jssdkcross=%7B%22distinct_id%22%3A%226179053634%22%2C%22first_id%22%3A%22177ed5d24411dc-06c40ab2ce41b3-7d657361-2073600-177ed5d2442b85%22%2C%22props%22%3A%7B%22%24latest_traffic_source_type%22%3A%22%E7%9B%B4%E6%8E%A5%E6%B5%81%E9%87%8F%22%2C%22%24latest_search_keyword%22%3A%22%E6%9C%AA%E5%8F%96%E5%88%B0%E5%80%BC_%E7%9B%B4%E6%8E%A5%E6%89%93%E5%BC%80%22%2C%22%24latest_referrer%22%3A%22%22%2C%22%24latest_utm_source%22%3A%22baidu%22%2C%22%24latest_utm_medium%22%3A%22cpc%22%2C%22%24latest_utm_campaign%22%3A%220011%22%2C%22%24latest_landing_page%22%3A%22https%3A%2F%2Fwww.smzdm.com%2F%22%7D%2C%22%24device_id%22%3A%22177ed5d24411dc-06c40ab2ce41b3-7d657361-2073600-177ed5d2442b85%22%7D; _zdmA.uid=ZDMA.cRAszo4ZD.1672388625.2419200',
                    ]
                ]
            );
            $res = $res->getBody()->getContents();
            $res = mb_substr($res, 9, -1);
            $res = json_decode($res, true);
            if ($res['error_code'] == 0) {
                app(CommandService::class)->sc_send('今日张大妈签到成功 - ' . Carbon::now());
            } else {
                app(CommandService::class)->sc_send('今日张大妈签到失败,请前往查看 - ' . Carbon::now());
                Log::channel('daily_error')->error('smzdm: sign error - 签到失败');
            }
        } catch (Exception $e) {
            Log::channel('daily_error')->error('smzdm: sign error- ' . $e->getMessage());
            app(CommandService::class)->sc_send('今日张大妈签到失败,请前往查看： ' . $e->getMessage() . ' - ' . Carbon::now());
        }
    }
}
