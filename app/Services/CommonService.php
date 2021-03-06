<?php

/**
 * User: Marc
 * Date: 2021/02/04
 * Time: 21:20
 */

namespace App\Services;

class CommonService
{
    /**
     * @param $url 请求网址
     * @param bool $params 请求参数
     * @param int $ispost 请求方式
     * @param int $https https协议
     * @return bool|mixed
     */
    public static function curl($url, $params = false, $ispost = 0, $https = 0, $gzip = 0)
    {
        $httpInfo = array();
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2272.118 Safari/537.36');
        // curl_setopt($ch, CURLOPT_COOKIE, '_abcde_qweasd=0; BAIDU_SSP_lcr=https://www.baidu.com/link?url=IzYDuQ9mfyVUeuT4WfNk0zQtSiJNL5KvKLknC4pkWwNIRZ3nIrtTBT-U4YTp6_9jjxwqL0cjnBMHford0FjLwK&wd=&eqid=acda779a000461ff000000056059db65; _abcde_qweasd=0; Hm_lvt_169609146ffe5972484b0957bd1b46d6=1616501609; bdshare_firstime=1616501609381; Hm_lpvt_169609146ffe5972484b0957bd1b46d6=1616502430');
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        if ($gzip)
            curl_setopt($ch, CURLOPT_ENCODING, 'gzip');
        if ($https) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // 对认证证书来源的检查
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); // 从证书中检查SSL加密算法是否存在
        }
        if ($ispost) {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
            curl_setopt($ch, CURLOPT_URL, $url);
        } else {
            if ($params) {
                if (is_array($params)) {
                    $params = http_build_query($params);
                }
                curl_setopt($ch, CURLOPT_URL, $url . '?' . $params);
            } else {
                curl_setopt($ch, CURLOPT_URL, $url);
            }
        }

        $response = curl_exec($ch);

        if ($response === false) {
            //echo "cURL Error: " . curl_error($ch);
            return false;
        }
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $httpInfo = array_merge($httpInfo, curl_getinfo($ch));
        curl_close($ch);
        return $response;
    }
}
