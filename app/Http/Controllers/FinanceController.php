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
        
        $left = Redis::get($this->redisKey('*finance_left*'));
        if (empty($left)) {
            return $this->apiOut([], 0, '缺少左侧excel');
        }
        $left = json_decode($left);
        $left = $left[0];
        // dd($left);
        $newLeft = [];
        foreach ($left as $value) {
            $temp = [];
            $name = $value[3];
            $temp['left'] = $value[1];
            $temp['right'] = $value[2];
            if ($value[0]) {
                $date = Carbon::createFromFormat('Ymd', $value[0]);
                $temp['date'] = $date->format('Y-m-d');
                // $temp['date'] = Carbon::parse($value[0])->format('Y-m-d');
            } else {
                $temp['date'] = null;
            }


            $newLeft[$name][] = $temp;
        }
        // dd($newLeft);

        $right = Redis::get($this->redisKey('*finance_right*'));
        if (empty($right)) {
            return $this->apiOut([], 0, '缺少右侧excel');
        }
        $right = json_decode($right);
        $right = $right[0];
        $newRight = [];
        $pattern = "/[\d_\-.]/";
        foreach ($right as $v) {
            $name = $v[2];
            $name = str_replace('（未记账）', '', $name);
            $name = preg_replace($pattern, '', $name);
            $temp = [];
            $temp['date'] = $v[0];
            $temp['id'] = $v[1];
            $temp['left'] = $v[3];
            $temp['right'] = $v[4];
            $newRight[$name][] = $temp;
        }
        //  dump($newRight);
        $result = [];
        foreach ($newLeft as $keyLeft => $itemLeft) {
            if (key_exists($keyLeft, $newRight)) {
                $itemRight = $newRight[$keyLeft];

                $tempLeft = $itemLeft;

                foreach ($itemLeft as $subkeyleft => $subLeft) {
                    foreach ($itemRight as $subkeyright => $subRight) {
                        if ($subLeft['left']) {
                            if ($subRight['right'] == $subLeft['left']) {
                                unset($tempLeft[$subkeyleft], $itemRight[$subkeyright]);
                                break;
                            }
                        } else {
                            if ($subRight['left'] == $subLeft['right']) {
                                unset($tempLeft[$subkeyleft], $itemRight[$subkeyright]);
                                break;
                            }
                        }
                    }
                }
                if (!empty($tempLeft)) {
                    $result['unset']['left'][$keyLeft] = $tempLeft;
                }
                if (!empty($itemRight)) {
                    $result['unset']['right'][$keyLeft] = $itemRight;
                }
                unset($newRight[$keyLeft]);
            } else {
                $result['unset']['left'][$keyLeft] = $itemLeft;
            }
        }
        $result['unset']['right'] = array_merge($result['unset']['right'], $newRight);
        // return $this->apiOut($result);
        $filePath = Carbon::now() . '.xlsx';
        $cellData = [
            [
                '日期', '公司名称', '借', '贷款', '凭证字号'
            ],
        ];
        foreach ($result['unset']['left'] as $key => $value) {
            foreach ($value as $val) {
                if (empty($val['left']) && empty($val['right'])) {
                    continue;
                }
                $cell = [];
                $cell[] = $val['date'];
                $cell[] = $key;
                $cell[] = $val['left'];
                $cell[] = $val['right'];
                $cell[] = '';
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
        foreach ($result['unset']['right'] as $key => $value) {
            foreach ($value as $val) {
                if (empty($val['left']) && empty($val['right'])) {
                    continue;
                }
                $cell = [];
                $cell[] = $val['date'];
                $cell[] = $key;
                $cell[] = $val['left'];
                $cell[] = $val['right'];
                $cell[] = $val['id'];
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
