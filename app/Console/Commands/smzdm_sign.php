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
                        "Cookie" => '__jsluid_h=3b7a8c7316294092b877d299075d7071; __ckguid=F9N5kjwgpAPGKJ2RHHSKJ; r_sort_type=score; __jsluid_s=c89337a6be3d7fe5b816b90569ecf954; shequ_pc_sug=b; smzdm_user_source=BC67D90B2AA432146F9282571BD39CAC; device_id=989201938161588500766950803b4249cc4623999d2cca370f13b421c; userId=user:6179053634|6179053634; homepage_sug=b; _ga=GA1.1.612226504.1614595040; _ga_09SRZM2FDD=GS1.1.1636961831.122.0.1636961837.0; __gads=ID=456d0e7eceb147ab:T=1614595039:S=ALNI_Mathm2tEIWXYlJB3AlUuxSzZHD-Sw; footer_floating_layer=0; _zdmA.vid=*; ad_date=21; ad_json_feed={}; Hm_lvt_9b7ac3d38f30fe89ff0b8a0546904e58=1644808888,1644980646,1645066442,1645413250; sensorsdata2015jssdkcross={"distinct_id":"6179053634","first_id":"177ed5d24411dc-06c40ab2ce41b3-7d657361-2073600-177ed5d2442b85","props":{"$latest_traffic_source_type":"直接流量","$latest_search_keyword":"未取到值_直接打开","$latest_referrer":"","$latest_utm_source":"baidu","$latest_utm_medium":"cpc","$latest_utm_campaign":"0011"},"$device_id":"177ed5d24411dc-06c40ab2ce41b3-7d657361-2073600-177ed5d2442b85"}; sess=AT-n8S98m3TjjPZ4/PclA1QkpSsRTb/rhSxjG/UdSgGRxaBJb+4DHRewylTGTx+tOAzhkUxjKBQA3QX6zlICwkkf4bYoIiP3DkontDFr4Ci+veW2aG9qpeSuPM=; user=user:6179053634|6179053634; smzdm_id=6179053634; _zdmA.uid=ZDMA.IA7JDx7n2.1645414049.2419200; Hm_lpvt_9b7ac3d38f30fe89ff0b8a0546904e58=1645414049; bannerCounter=[{"number":0,"surplus":1},{"number":0,"surplus":1},{"number":2,"surplus":1},{"number":0,"surplus":1},{"number":2,"surplus":1},{"number":0,"surplus":1}]; amvid=1395ba7cf4334fe749403da52a1af29f; _zdmA.time=1645414050632.0.https://www.smzdm.com/',
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
