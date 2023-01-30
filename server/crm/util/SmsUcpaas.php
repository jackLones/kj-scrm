<?php


namespace app\util;

/**
 * Created by PhpStorm.
 * User: UCPAAS JackZhao
 * Date: 2014/10/22
 * Time: 12:04
 * Dec : ucpass php sdk
 */
class SmsUcpaas
{

    /**
     *  云之讯REST API版本号。当前版本号为：2014-06-30
     */
    const SoftVersion = "2014-06-30";
    /**
     * API请求地址
     */
    const BaseUrl = "https://api.ucpaas.com/";
    //语音验证码请求新地址
    const BaseMessageUrl = "http://message.ucpaas.com/";
    /**
     * @var string
     * 开发者账号ID。由32个英文字母和阿拉伯数字组成的开发者账号唯一标识符。
     */
    private $accountSid;
    /**
     * @var string
     * 开发者账号TOKEN
     */
    private $token;
    /**
     * @var string
     * 时间戳
     */
    private $timestamp;
    
    /**
     * appID
     * @var string
     */
    private $appId;

    /**
     * 显示号码
     * @var string
     */
    private $displayNum;

    // template_id 6852
    public function  __construct()
    {
        $ucpassParams =  \Yii::$app->params['ucpass'];

        $this->accountSid = $ucpassParams['accountSid'];
        $this->token = $ucpassParams['token'];
        $this->appId = $ucpassParams['appID'];
        $this->displayNum = $ucpassParams['displayNum'];
//        $this->timestamp = date("YmdHis") + 7200;
        $this->timestamp = date("YmdHis");
    }

    /**
     * @return string
     * 包头验证信息,使用Base64编码（账户Id:时间戳）
     */
    private function getAuthorization()
    {
        $data = $this->accountSid . ":" . $this->timestamp;
        return trim(base64_encode($data));
    }

    /**
     * @return string
     * 验证参数,URL后必须带有sig参数，sig= MD5（账户Id + 账户授权令牌 + 时间戳，共32位）(注:转成大写)
     */
    private function getSigParameter()
    {
        $sig = $this->accountSid . $this->token . $this->timestamp;
        return strtoupper(md5($sig));
    }

    /**
     * @param $url
     * @param string $type
     * @return mixed|string
     */
    private function getResult($url, $body = null, $type = 'json',$method)
    {
        $data = $this->connection($url,$body,$type,$method);
        if (isset($data) && !empty($data)) {
            $result = $data;
        } else {
            $result = '没有返回数据';
        }
        return $result;
    }

    /**
     * @param $url
     * @param $type
     * @param $body  post数据
     * @param $method post或get
     * @return mixed|string
     */
    private function connection($url, $body, $type,$method)
    {
        if ($type == 'json') {
            $mine = 'application/json';
        } else {
            $mine = 'application/xml';
        }
        if (function_exists("curl_init")) {
            $header = array(
                'Accept:' . $mine,
                'Content-Type:' . $mine . ';charset=utf-8',
                'Authorization:' . $this->getAuthorization(),
            );
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
            if($method == 'post'){
                curl_setopt($ch,CURLOPT_POST,1);
                curl_setopt($ch,CURLOPT_POSTFIELDS,$body);
            }
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            $result = curl_exec($ch);
            curl_close($ch);
        } else {
            $opts = array();
            $opts['http'] = array();
            $headers = array(
                "method" => strtoupper($method),
            );
            $headers[]= 'Accept:'.$mine;
            $headers['header'] = array();
            $headers['header'][] = "Authorization: ".$this->getAuthorization();
            $headers['header'][]= 'Content-Type:'.$mine.';charset=utf-8';

            if(!empty($body)) {
                $headers['header'][]= 'Content-Length:'.strlen($body);
                $headers['content']= $body;
            }

            $opts['http'] = $headers;
            $result = file_get_contents($url, false, stream_context_create($opts));
        }
        return $result;
    }

    /**
     * @param string $type 默认json,也可指定xml,否则抛出异常
     * @return mixed|string 返回指定$type格式的数据
     * @throws Exception
     */
    public function getDevinfo($type = 'json')
    {
        if ($type == 'json') {
            $type = 'json';
        } elseif ($type == 'xml') {
            $type = 'xml';
        } else {
            throw new Exception("只能json或xml，默认为json");
        }
        $url = self::BaseUrl . self::SoftVersion . '/Accounts/' . $this->accountSid . '?sig=' . $this->getSigParameter();
        $data = $this->getResult($url,null,$type,'get');
        return $data;
    }


    /**
     * @param $appId 应用ID
     * @param $clientType 计费方式。0  开发者计费；1 云平台计费。默认为0.
     * @param $charge 充值的金额
     * @param $friendlyName 昵称
     * @param $mobile 手机号码
     * @return json/xml
     */
    public function applyClient($clientType, $charge, $friendlyName, $mobile, $type = 'json')
    {
        $url = self::BaseUrl . self::SoftVersion . '/Accounts/' . $this->accountSid . '/Clients?sig=' . $this->getSigParameter();
        if ($type == 'json') {
            $body_json = array();
            $body_json['client'] = array();
            $body_json['client']['appId'] = $this->appId;
            $body_json['client']['clientType'] = $clientType;
            $body_json['client']['charge'] = $charge;
            $body_json['client']['friendlyName'] = $friendlyName;
            $body_json['client']['mobile'] = $mobile;
            $body = json_encode($body_json);
        } elseif ($type == 'xml') {
            $body_xml = '<?xml version="1.0" encoding="utf-8"?>
                        <client><appId>'.$this->appId.'</appId>
                        <clientType>'.$clientType.'</clientType>
                        <charge>'.$charge.'</charge>
                        <friendlyName>'.$friendlyName.'</friendlyName>
                        <mobile>'.$mobile.'</mobile>
                        </client>';
            $body = trim($body_xml);
        } else {
            throw new Exception("只能json或xml，默认为json");
        }
        $data = $this->getResult($url, $body, $type,'post');
        return $data;
    }

    /**
     * @param $clientNumber
     * @param $appId
     * @param string $type
     * @return mixed|string
     * @throws Exception
     */
    public function releaseClient($clientNumber,$type = 'json'){
        $url = self::BaseUrl . self::SoftVersion . '/Accounts/' . $this->accountSid . '/dropClient?sig=' . $this->getSigParameter();
        if($type == 'json'){
            $body_json = array();
            $body_json['client'] = array();
            $body_json['client']['clientNumber'] = $clientNumber;
            $body_json['client']['appId'] = $this->appId;
            $body = json_encode($body_json);
        }elseif($type == 'xml'){
            $body_xml = '<?xml version="1.0" encoding="utf-8"?>
                        <client>
                        <clientNumber>'.$clientNumber.'</clientNumber>
                        <appId>'.$this->appId.'</appId >
                        </client>';
            $body = trim($body_xml);
        }else {
            throw new Exception("只能json或xml，默认为json");
        }
        $data = $this->getResult($url, $body, $type,'post');
        return $data;
    }

    /**
     * @param $appId
     * @param $start
     * @param $limit
     * @param string $type
     * @return mixed|string
     * @throws Exception
     */
    public function getClientList($start,$limit,$type = 'json'){
        $url = self::BaseUrl . self::SoftVersion . '/Accounts/' . $this->accountSid . '/clientList?sig=' . $this->getSigParameter();
        if($type == 'json'){
            $body_json = array('client'=>array(
                'appId'=>$this->appId,
                'start'=>$start,
                'limit'=>$limit
            ));
            $body = json_encode($body_json);
        }elseif($type == 'xml'){
            $body_xml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
                        <client>
                            <appId>'.$this->appId.'</appId>
                            <start>'.$start.'</start>
                            <limit>'.$limit.'</limit>
                        </client>';
            $body = trim($body_xml);
        }else {
            throw new Exception("只能json或xml，默认为json");
        }
        $data = $this->getResult($url, $body, $type,'post');
        return $data;
    }

    /**
     * @param $appId
     * @param $clientNumber
     * @param string $type
     * @return mixed|string
     * @throws Exception
     */
    public function getClientInfo($clientNumber,$type = 'json'){
        if ($type == 'json') {
            $type = 'json';
        } elseif ($type == 'xml') {
            $type = 'xml';
        } else {
            throw new Exception("只能json或xml，默认为json");
        }
        $url = self::BaseUrl . self::SoftVersion . '/Accounts/' . $this->accountSid . '?sig=' . $this->getSigParameter(). '&clientNumber='.$clientNumber.'&appId='.$this->appId;
        $data = $this->getResult($url,null,$type,'get');
        return $data;
    }

    /**
     * @param $appId
     * @param $mobile
     * @param string $type
     * @return mixed|string
     * @throws Exception
     */
    public function getClientInfoByMobile($mobile,$type = 'json'){
        if ($type == 'json') {
            $type = 'json';
        } elseif ($type == 'xml') {
            $type = 'xml';
        } else {
            throw new Exception("只能json或xml，默认为json");
        }
        $url = self::BaseUrl . self::SoftVersion . '/Accounts/' . $this->accountSid . '/ClientsByMobile?sig=' . $this->getSigParameter(). '&mobile='.$mobile.'&appId='.$this->appId;
        $data = $this->getResult($url,null,$type,'get');
        return $data;
    }

    /**
     * @param $appId
     * @param $date
     * @param string $type
     * @return mixed|string
     * @throws Exception
     */
    public function getBillList($date,$type = 'json'){
        $url = self::BaseUrl . self::SoftVersion . '/Accounts/' . $this->accountSid . '/billList?sig=' . $this->getSigParameter();
        if($type == 'json'){
            $body_json = array('appBill'=>array(
                'appId'=>$this->appId,
                'date'=>$date,
            ));
            $body = json_encode($body_json);
        }elseif($type == 'xml'){
            $body_xml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
                        <appBill>
                            <appId>'.$this->appId.'</appId>
                            <date>'.$date.'</date>
                        </appBill>';
            $body = trim($body_xml);
        }else {
            throw new Exception("只能json或xml，默认为json");
        }
        $data = $this->getResult($url, $body, $type,'post');
        return $data;
    }

    /**
     * @param $appId
     * @param $clientNumber
     * @param $chargeType
     * @param $charge
     * @param string $type
     * @return mixed|string
     * @throws Exception
     */
    public function chargeClient($clientNumber,$chargeType,$charge,$type = 'json'){
        $url = self::BaseUrl . self::SoftVersion . '/Accounts/' . $this->accountSid . '/chargeClient?sig=' . $this->getSigParameter();
        if($type == 'json'){
            $body_json = array('client'=>array(
                'appId'=>$this->appId,
                'clientNumber'=>$clientNumber,
                'chargeType'=>$chargeType,
                'charge'=>$charge
            ));
            $body = json_encode($body_json);
        }elseif($type == 'xml'){
            $body_xml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
                        <client>
                            <clientNumber>'.$clientNumber.'</clientNumber>
                            <chargeType>'.$chargeType.'</chargeType>
                            <charge>'.$charge.'</charge>
                            <appId>'.$this->appId.'</appId>
                        </client>';
            $body = trim($body_xml);
        }else {
            throw new Exception("只能json或xml，默认为json");
        }
        $data = $this->getResult($url, $body, $type,'post');
        return $data;

    }

    /**
     * @param $appId
     * @param $fromClient
     * @param $to
     * @param null $fromSerNum
     * @param null $toSerNum
     * @param string $type
     * @return mixed|string
     * @throws Exception
     */
    public function callBack($fromClient,$to,$fromSerNum=null,$toSerNum=null,$type = 'json'){
        $url = self::BaseUrl . self::SoftVersion . '/Accounts/' . $this->accountSid . '/Calls/callBack?sig=' . $this->getSigParameter();
        if($type == 'json'){
            $body_json = array('callback'=>array(
                'appId'=>$this->appId,
                'fromClient'=>$fromClient,
                'fromSerNum'=>$fromSerNum,
                'to'=>$to,
                'toSerNum'=>$toSerNum,
                'ringtoneID' => 1361
            ));
            $body = json_encode($body_json);
        }elseif($type == 'xml'){
            $body_xml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
                        <callback>
                            <fromClient>'.$fromClient.'</clientNumber>
                            <fromSerNum>'.$fromSerNum.'</chargeType>
                            <to>'.$to.'</charge>
                            <toSerNum>'.$toSerNum.'</toSerNum>
                            <appId>'.$this->appId.'</appId>
                        </callback>';
            $body = trim($body_xml);
        }else {
            throw new Exception("只能json或xml，默认为json");
        }
        $data = $this->getResult($url, $body, $type,'post');
        return $data;
    }

    /**
     * @param $appId
     * @param $verifyCode
     * @param $to
     * @param string $type
     * @return mixed|string
     * @throws Exception
     */
    public function voiceCode($verifyCode,$to,$type = 'json'){
        $url = self::BaseMessageUrl . self::SoftVersion . '/Accounts/' . $this->accountSid . '/Calls/voiceVerify?sig=' . $this->getSigParameter();
        if($type == 'json'){
            $body_json = array('voiceVerify'=>array(
                'appId'=>$this->appId,
                'captchaCode'=>$verifyCode,
                'displayNum'=>$this->displayNum,
                'to'=>$to
            ));
            $body = json_encode($body_json);
        }elseif($type == 'xml'){
            $body_xml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
                        <voiceVerify>
                            <captchaCode>'.$verifyCode.'</captchaCode>
                            <to>'.$to.'</to>
                            <appId>'.$this->appId.'</appId>
                            <displayNum>'.$this->displayNum.'</displayNum>
                        </voiceVerify>';
            $body = trim($body_xml);
        }else {
            throw new Exception("只能json或xml，默认为json");
        }
        $data = $this->getResult($url, $body, $type,'post');
        return $data;
    }

    /**
     * @param $appId
     * @param $to
     * @param $templateId
     * @param null $param
     * @param string $type
     * @return mixed|string
     * @throws Exception
     */
    public function templateSMS($to,$templateId,$param=null,$type = 'json'){
        $url = self::BaseUrl . self::SoftVersion . '/Accounts/' . $this->accountSid . '/Messages/templateSMS?sig=' . $this->getSigParameter();
        if($type == 'json'){
            $body_json = array('templateSMS'=>array(
                'appId'=>$this->appId,
                'templateId'=>$templateId,
                'to'=>$to,
                'param'=>$param
            ));
            $body = json_encode($body_json);
        }elseif($type == 'xml'){
            $body_xml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
                        <templateSMS>
                            <templateId>'.$templateId.'</templateId>
                            <to>'.$to.'</to>
                            <param>'.$param.'</param>
                            <appId>'.$this->appId.'</appId>
                        </templateSMS>';
            $body = trim($body_xml);
        }else {
            throw new Exception("只能json或xml，默认为json");
        }
        $data = $this->getResult($url, $body, $type,'post');
        return $data;
    }
} 