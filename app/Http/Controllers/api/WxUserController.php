<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\shelf;
use App\Models\wxUser;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class WxUserController extends Controller
{
    private $_urlCode2Session = "https://api.weixin.qq.com/sns/jscode2session?appid=@@APPID@@&secret=@@SECRET@@&js_code=@@JSCODE@@&grant_type=authorization_code";
    private $_appId="wx563fec61a0a7f915";
    private $_appSecret="86dc8067521e15d4e8cd62ddc432cbad";
    private $sessionKey="";

    public function Code2Session($code)
    {
        $url = str_replace('@@APPID@@', $this->_appId, $this->_urlCode2Session);
        $url = str_replace('@@SECRET@@', $this->_appSecret, $url);
        $url = str_replace('@@JSCODE@@', $code, $url);
  
        try {
            $httpClient = new Client();
            $res = $httpClient->get($url);
        } catch (GuzzleException $e) {
            return $this->apiOut('', 0, '请求微信服务器失败：' . $e->getCode() . ' => ' . $e->getMessage());
        }
        if ($res->getStatusCode() != 200) {
            return $this->apiOut('', 0, '解析Code请求失败');
        }
        $res = json_decode($res->getBody()->getContents(), true);
        if (isset($res['errcode']) && $res['errcode'] != 0) {
            return $this->apiOut('', 0, '解析Code失败：' . $res['errcode'] . ' => ' . $res['errmsg']);
        }

        $sessionKey = $res['session_key'];
        $openId = $res['openid'];

        Session::put('wxSession', [
            'sessionKey'    => $sessionKey,
            'openId'        => $openId
        ]);

        $currentUser = wxUser::where('wx_mini_openid', $openId)->first();
        if (!empty($currentUser)) {
            Session::put('wxUser', $currentUser);
            $currentUser->sessionKey = $sessionKey;
            return $this->apiOut($currentUser);
        } else {
            $this->registerByOpenId();
        }
    }

    public function checkSession()
    {
        $session = Session::get('wxSession');
        if (empty($session)) {
            return $this->apiOut('', 0, '需要重新登陆');
        } else {
            $existedUser = wxUser::where('wx_mini_openid', $session['openId'])->first();
            if (!empty($existedUser)) {
                Session::put('wxUser', $existedUser);
                return $this->apiOut($existedUser);
            } else {
                $this->registerByOpenId();
            }
        }
    }

    public function registerByOpenId(): array
    {
        $openId = Session::get('wxSession')['openId'];
        if (empty($openId)) {
            return $this->apiOut('', 0, '需要重新登陆');
        }
        // 检查用户是否存在
        $existedUser = wxUser::where('wx_mini_openid', $openId)->first();
        if (!empty($existedUser)) {
            Session::put('wxUser', $existedUser);
            return $this->apiOut('', 0, '注册失败，用户已存在');
        }
        $newUser = new wxUser();
        $newUser->wx_mini_openid = $openId;
        if ($newUser->save()) {
            Session::put('wxUser', $newUser);
            $newUser->sessionKey = $openId = Session::get('wxSession')['sessionKey'];
            return $this->apiOut($newUser);
        } else {
            return $this->apiOut('', 0, '用户注册失败');
        }
    }

    public function getCurrentUser(){
        $session = Session::get('wxSession');
        if (empty($session)) {
            return $this->apiOut('', 0, '需要重新登陆');
        }
        $openId = Session::get('wxSession')['openId'];
        $currentUser = wxUser::where('wx_mini_openid', $openId)->first();
        if($currentUser){
            $currentUser->book_num = shelf::where('user_id',$currentUser->id)->count();
        }
        return $this->apiOut($currentUser);
    }
    public function updateUser(Request $request): array
    {
        $openId =  Session::get('wxSession')['openId'];
        if (empty($openId)) {
            return $this->outputError('需要重新登陆');
        }
        $user = wxUser::where('wx_mini_openid', $openId)->first();
        if (empty($user)) {
            return $this->outputError('更新失败，用户不存在');
        }
        if (!empty($request->input('avatar'))) {
            $user->avatar = $request->input('avatar');
        }
        if (!empty($request->input('name'))) {
            $user->name = $request->input('name');
        }
        if (!empty($request->input('phone'))) {
            $user->phone = $request->input('phone');
        }
        if (!empty($request->input('gender'))) {
            $gender = 0;
            switch (strtolower($request->input('gender'))) {
                case 'male':
                    $gender = 1;
                    break;
                case 'female':
                    $gender = 2;
                    break;
                default:
                    break;
            }
            $user->gender = $gender;
        }
        if ($user->save()) {
            Session::put('wxUser', $user);
            return $this->apiOut($user);
        } else {
            return $this->apiOut('', 0, '用户信息更新失败');
        }
    }

    public function updateUserPhone(Request $request): array
    {
        $cypher = $request->input('encryptedData');
        $iv = $request->input('iv');
        $this->sessionKey = Session::get('wxSession')['sessionKey'];
        if (empty($sessionKey)) {
            return $this->outputError('会话已结束，需要重新登录');
        }
        $errcode = $this->decrypt($cypher, $iv, $plaintext);
        if ($errcode != 0) {
            return $this->outputError("手机号解析失败[$errcode]");
        }
        $phoneObj = json_decode($plaintext, true);
        $phoneNum = $phoneObj['purePhoneNumber'];
        $currentUser = Session::get('wxUser');
        // $isNewUser = false;
        if ($currentUser) {
            $currentUser = wxUser::where('id', $currentUser->id)->first();
            if (!empty($currentUser)) {
                // if (empty($currentUser->phone)) {
                //     $isNewUser = true;
                // }
                $currentUser->phone = $phoneNum;
                $currentUser->save();
                Session::put('wxUser', $currentUser);
            }
        } else {
            Log::error('用户绑定手机号失败，Session中无用户信息');
        }
        return $this->output($phoneNum);
    }

    public function decrypt(string $cypher, string $iv, &$data): int
    {
        if (strlen($this->sessionKey) != 24) {
            return -41001;
        }
        $aesKey = base64_decode($this->sessionKey);
        if (strlen($iv) != 24) {
            return -41002;
        }
        $aesIV = base64_decode($iv);
        $aesCypher = base64_decode($cypher);
        $res = openssl_decrypt($aesCypher, "AES-128-CBC", $aesKey, 1, $aesIV);
        $dataObj = json_decode($res);
        if ($dataObj == null) {
            return -41003;
        }
        if ($dataObj->watermark->appid != $this->appId) {
            return -41003;
        }
        $data = $res;
        return 0;
    }
}
