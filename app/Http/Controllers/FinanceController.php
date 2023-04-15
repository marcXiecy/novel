<?php

namespace App\Http\Controllers;

use App\Services\CommonExport;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Maatwebsite\Excel\Facades\Excel;

class FinanceController extends Controller
{
    
    public function __construct()
    {
    }

    public function index(Request $request)
    {
        $keys = Redis::keys('*finance_*');
        $prifix = config('database.redis.options.prefix');
        !$keys && $keys = [];
        foreach ($keys as $k => $v) {
            $key = str_replace($prifix, '', $v);
            $keys[$k] = $key;
        }
        return view(
            'finance.index',
            ['request' => $request, 'keys' => $keys]
        );
    }

    private function redisKey(string $pattern)
    {
        $prifix = config('database.redis.options.prefix');
        $key = Redis::keys($pattern)[0];
        $key = str_replace($prifix, '', $key);
        return $key;
    }

    public function analysis()
    {
        
        $daily = Redis::get($this->redisKey('*finance_daily*'));
        if (empty($daily)) {
            return $this->apiOut([], 0, '缺少日记账excel');
        }
        $daily = json_decode($daily);
        $daily = $daily[0];
        // dd($daily);
        $newDaily = [];
        $pattern = "/[\d_\-.]/";

        foreach ($daily as $v) {
            $temp = [];
            $name = $v[2];
            $name = str_replace('（未记账）', '', $name);
            $name = preg_replace($pattern, '', $name);
            $temp['date'] = $v[0];
            $temp['id'] = $v[1];
            $temp['debit'] = $v[3];
            $temp['credit'] = $v[4];
            $newDaily[$name][] = $temp;
        }
        // dd($newDaily);

        $bank = Redis::get($this->redisKey('*finance_bank*'));
        if (empty($bank)) {
            return $this->apiOut([], 0, '缺少银行对账单excel');
        }
        $bank = json_decode($bank);
        $bank = $bank[0];
        $newBank = [];
        foreach ($bank as $value) {
            $temp = [];
            $name = $value[3];
            $temp['date'] = $value[0];
            $temp['debit'] = $value[1];
            $temp['credit'] = $value[2];
            $newBank[$name][] = $temp;
        }
        //  dump($newBank);
        $result = [];
        foreach ($newDaily as $keyDaily => $itemDaily) {
            if (key_exists($keyDaily, $newBank)) {
                $itemRight = $newBank[$keyDaily];

                $tempDaily = $itemDaily;

                foreach ($itemDaily as $subKeyDaily => $subDaily) {
                    foreach ($itemRight as $subKeyBank => $subBank) {
                        if ($subDaily['debit']) {
                            if ($subBank['credit'] == $subDaily['debit']) {
                                unset($tempDaily[$subKeyDaily], $itemRight[$subKeyBank]);
                                break;
                            }
                        } else {
                            if ($subBank['debit'] == $subDaily['credit']) {
                                unset($tempDaily[$subKeyDaily], $itemRight[$subKeyBank]);
                                break;
                            }
                        }
                    }
                }
                if (!empty($tempDaily)) {
                    $result['unset']['daily'][$keyDaily] = $tempDaily;
                }
                if (!empty($itemRight)) {
                    $result['unset']['bank'][$keyDaily] = $itemRight;
                }
                unset($newBank[$keyDaily]);
            } else {
                $result['unset']['daily'][$keyDaily] = $itemDaily;
            }
        }
        $result['unset']['bank'] = array_merge($result['unset']['bank'], $newBank);
        // return $this->apiOut($result);
        $filePath = Carbon::now() . '.xlsx';
        $cellData = [
            [
                '日期', '公司名称', '借', '贷', '凭证字号'
            ],
        ];
        foreach ($result['unset']['daily'] as $key => $value) {
            foreach ($value as $val) {
                if (empty($val['debit']) && empty($val['credit'])) {
                    continue;
                }
                $cell = [];
                $cell[] = $val['date'];
                $cell[] = $key;
                $cell[] = $val['debit'];
                $cell[] = $val['credit'];
                $cell[] = $val['id'];
                $cellData[] = $cell;
            }
        }
        $cell = ['----', '----', '----', '----', '----'];
        $cellData[] = $cell;
        $cellData[] = $cell;
        $cellData[] = $cell;
        $cellData[] = $cell;
        $cellData[] = $cell;
        $cellData[] = $cell;
        $cellData[] = $cell;
        $cellData[] = $cell;
        $cellData[] = $cell;
        foreach ($result['unset']['bank'] as $key => $value) {
            foreach ($value as $val) {
                if (empty($val['debit']) && empty($val['credit'])) {
                    continue;
                }
                $cell = [];
                $cell[] = $val['date'];
                $cell[] = $key;
                $cell[] = $val['debit'];
                $cell[] = $val['credit'];
                $cell[] = '';
                $cellData[] = $cell;
            }
        }
        $collection = new CommonExport($cellData);
        return Excel::download($collection, $filePath);
    }

    public function importExcel(Request $request)
    {
        if ($request->hasFile('report') && $request->file('report')->isValid()) {
            $file = $request->file('report');
            $data = Excel::toArray([], $request->file('report'));
            Redis::set('finance_' . $request->type . '_' . $file->getClientOriginalName(), json_encode($data));
        } else {
            echo 'error';
            die;
        }
        return redirect('/finance');
    }

    public function clear()
    {
        $prifix = config('database.redis.options.prefix');
        $keys = Redis::keys('*finance_*');
        foreach ($keys as $v) {
            $key = str_replace($prifix, '', $v);
            Redis::del($key);
        }
        return $this->apiOut();
    }

    public function set()
    {
    }
}
