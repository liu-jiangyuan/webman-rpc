<?php
namespace app\api\extend;

use app\api\extend\curl\Curl;

class Wxmini
{
    private $app_id;
    private $app_secret;
    /**
     * 通过code换取用户的session_key,open_id,unionid
     *
     * @access private
     * @param  string
     * @return void
     */
    private function _getAccessToken($code = '') {
        $data = [
            'appid'      => $this->app_id,
            'secret'     => $this->app_secret,
            'js_code'       => $code,
            'grant_type' => 'authorization_code',
        ];

        $result = json_decode($this->sendRequest("{$this->protocol}://{$this->server_name}/sns/jscode2session", $data,'GET',false,false),true);

        if(isset($result['errcode'])){
            switch ($result['errcode']){
                case -1:

                case 40029;

                case 45011;
                    return false;
            }
        }

        if(!isset($result['openid'])) return false;

        return [
            'open_id'  => $result['openid'],
        ];
    }


    /**
     * 发送请求
     *
     * @access public
     * @param  array  $params 参数
     * @param  string $request_method 请求方法 GET|POST
     * @param  bool   $return_data 是否返回数据中的data字段
     * @return void
     */
    private function sendRequest($url, array $params = [], $request_method = 'GET', $return_data = true, $handleData = true) {
        $log_str = json_encode($params);

        $curl = new Curl();
        $curl->setOpt(CURLOPT_SSL_VERIFYPEER, FALSE);
        $curl->setOpt(CURLOPT_SSL_VERIFYHOST, FALSE);
        switch (strtoupper($request_method)) {
            case 'POST':
                $response_body = $curl->post($url, $params);
                break;

            case 'GET':
            default:
                $response_body = $curl->get($url, $params);
                break;
        }

        if ($curl->error) {
            $logStr = date('Y-m-d H:i:s') . PHP_EOL .
                'URL:' . $url . PHP_EOL .
                'METHOD:' . $request_method . PHP_EOL .
                'PARAMS:' . json_encode($params) . PHP_EOL .
                'curlErrorMessage:' . $curl->curlErrorMessage . PHP_EOL .
                'curlError:' . $curl->curlError . PHP_EOL .
                'httpStatusCode:' . $curl->httpStatusCode . PHP_EOL .
                'httpError:' . $curl->httpError . PHP_EOL .
                'error:' . $curl->error . PHP_EOL .
                'errorCode:' . $curl->errorCode . PHP_EOL .
                'effectiveUrl:' . $curl->effectiveUrl . PHP_EOL;

            Yii::error($logStr);
        }

        return $handleData ? $this->handleData($response_body, $return_data) : $response_body;
    }

    /**
     * 处理数据
     *
     * @access public
     * @param  string $response_body
     * @return void
     */
    private function handleData($response_body = '', $return_data) {
        $response_arr = json_decode($response_body, true);
        if (!isset($response_arr['errcode']) || $response_arr['errcode'] == '0') {
            if ($return_data) {
                return $response_arr;
            }

            return true;
        }

        Yii::error('mina api handle data error.data:' . $response_body);

        return false;
    }
}