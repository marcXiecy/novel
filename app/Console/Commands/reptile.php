<?php
namespace App\Console\Commands;

use App\Functions\simple_html_dom;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

set_time_limit(0); //解除PHP脚本时间30s限制
// ini_set('memory_limit','128M');//修改内存值
class reptile extends Command
{ 
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:reptile {param?} {--func=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description: 调用示例,联系人同步:
php artisan command:reptile contacts or php artisan command:sync --func=contacts ';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // 入口方法
        $param = $this->argument('param'); // argument不指定参数名的情况下用 file
        $func = $this->option('func'); // option用--开头指定参数名 --func=file
        $method =  isset($param) ? $param : $func;//兼容两种传参方式调用
        //本类中是否存在传来的方法
          if(!method_exists(new self,$method)){
            echo '不存在的方法，请确认输入是否有误！';
          }else{
            self::$method();
       }
    }

    const novel_urls = [];
    private function getCatalog(){
        $catalog_url = self::novel_urls[0];
        $catalog = app()->make('CommonService')->curl($catalog_url);

        $htmlObj = new simple_html_dom();	//工具类对象初始化
        $htmlObj->load($catalog);
        $title = $htmlObj->find('div[id=info] h1', 0);
        $title = $title->plaintext;
        $author = $htmlObj->find('div[id=info] p', 0);
        $author = $author->plaintext;
        $author = explode("：", $author);
        $author = $author[1];
        $list = $htmlObj->find('div[id=list] a');
        $image = $htmlObj->find('div[id=fmimg] img', 0);
        $image = $image->src;
        $result = [];
        $result['title'] = $title;
        $result['author'] = $author;
        $result['image'] = $image;
        foreach ($list as $ele) {
            $temp = [];
            $temp['title'] = $ele->plaintext;
            $temp['href'] = $this->siteUrl . $ele->href;
            $result['catalog'][] = $temp;
        }
    }
}