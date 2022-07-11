<?php

/**
 * User: Marc
 * Date: 2021/02/04
 * Time: 21:20
 */

namespace App\Services;

use GuzzleHttp\Client;

class CommonService
{
    /**
     * @param $url 请求网址
     * @param bool $params 请求参数
     * @param int $ispost 请求方式
     * @param int $https https协议
     * @return bool|mixed
     */
    public static function curl($url, $params = false, $ispost = 0, $https = 0, $gzip = 0, $host = '', $referer = '')
    {

        $httpInfo = array();
        $ch = curl_init();
        // curl_setopt($ch, CURLOPT_HTTPHEADER,[]);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/103.0.5060.66 Safari/537.36 Edg/103.0.1264.44');
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $header = array('text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9');
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        if ($host)
            curl_setopt($ch, CURLOPT_HTTPHEADER, ["Host: " . $host]);
        if ($referer)
            curl_setopt($ch, CURLOPT_REFERER, $referer);
        if ($gzip)
            curl_setopt($ch, CURLOPT_ENCODING, 'gzip, deflate');
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
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        $response = curl_exec($ch);

        if ($response === false) {
            // echo "cURL Error: " . curl_error($ch);
            return false;
        }
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $httpInfo = array_merge($httpInfo, curl_getinfo($ch));
        curl_close($ch);
        return $response;
    }

    public function httpClient($url, $params = [], $ispost = 0)
    {
        $client = new Client();
        if ($ispost) {
            $res = $client->post($url, $params);
        } else {
            if ($params) {
                if (is_array($params)) {
                    $params = http_build_query($params);
                }
                $url = $url . '?' . $params;
            }
            $res = $client->get($url);
        }
        return $res->getBody()->getContents();
    }
}
