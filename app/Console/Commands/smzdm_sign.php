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
                        "Cookie" => '__ckguid=F9N5kjwgpAPGKJ2RHHSKJ; r_sort_type=score; __jsluid_s=8d814158789fd99f00d44a25be59ee82; shequ_pc_sug=b; smzdm_user_source=BC67D90B2AA432146F9282571BD39CAC; device_id=989201938161588500766950803b4249cc4623999d2cca370f13b421c; homepage_sug=b; _ga=GA1.1.612226504.1614595040; _ga_09SRZM2FDD=GS1.1.1636961831.122.0.1636961837.0; __gads=ID=456d0e7eceb147ab:T=1614595039:S=ALNI_Mathm2tEIWXYlJB3AlUuxSzZHD-Sw; ss_ab=ss51; sess=AT-n8S98m3TjjPYtwfpKKT/Z1uSl0HjCJ88aFtkOGIrsofLYoBEg1lyy+wOucbSZ1gCkmdux/XnlU77/JipUT4qBuOG0S7YX5LrvyzcoE4ZC7GqrhxMKczPd28=; user=user:6179053634|6179053634; smzdm_id=6179053634; s_his=公道杯; sensorsdata2015jssdkcross={"distinct_id":"6179053634","first_id":"177ed5d24411dc-06c40ab2ce41b3-7d657361-2073600-177ed5d2442b85","props":{"$latest_traffic_source_type":"直接流量","$latest_search_keyword":"未取到值_直接打开","$latest_referrer":"","$latest_utm_source":"baidu","$latest_utm_medium":"cpc","$latest_utm_campaign":"0011","$latest_landing_page":"https://www.smzdm.com/"},"$device_id":"177ed5d24411dc-06c40ab2ce41b3-7d657361-2073600-177ed5d2442b85"}; Hm_lvt_9b7ac3d38f30fe89ff0b8a0546904e58=1640227904,1640312749,1640589529,1640672151; _zdmA.uid=ZDMA.IA7JDx7n2.1640673399.2419200; Hm_lpvt_9b7ac3d38f30fe89ff0b8a0546904e58=1640673399',
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
