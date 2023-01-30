<?php
/**
 * SmsXuntao.php
 * ==============================================
 * Copy right 2014-2021  by Gaorrunqiao
 * ----------------------------------------------
 * This is not a free software, without any authorization is not allowed to use and spread.
 * ==============================================
 * @desc : 讯淘短信
 * @author: goen<goen88@163.com>
 * @date: 2021/4/14
 * @version: v2.0.0
 * @since: 2021/4/14 7:10 下午
 */

namespace app\util;

class SmsXuntao {


    const ERROR_CODES = [
        '101' => '缺少name参数',
        '102' => '缺少seed参数',
        '103' => '缺少key参数',
        '104' => '缺少dest参数',
        '105' => '缺少content参数',
        '106' => 'seed错误',
        '107' => 'key错误',
        '108' => 'ext错误',
        '109' => '内容超长',
        '110' => '模板未备案',
        '111' => '内容不符合要求，格式：【签名】你好 回T退订',
        '201' => '无对应账户',
        '202' => '账户暂停',
        '203' => '账户删除',
        '204' => '账户IP没备案',
        '205' => '账户无余额',
        '206' => '密码错误',
        '301' => '无对应产品',
        '302' => '产品暂停',
        '303' => '产品删除',
        '304' => '产品不在服务时间',
        '305' => '无匹配通道',
        '306' => '通道暂停',
        '307' => '通道已删除',
        '308' => '通道不在服务时间',
        '309' => '未提供短信服务',
        '310' => '未提供彩信服务',
        '401' => '屏蔽词',
        '500' => '查询间隔太短',
        '999' => '其他错误',
    ];


    /*
     * 短信对接平台
     */
    private $sms_provider;

    /*
     * 短信对接平台
     */
    private $sms_items;

    /**
     * 错误信息
     * @var string
     */
    private $error='';

    /**
     * 第三方平台反馈ID
     * @var string 三方平台ID
     */
    private $pt_msg_id="";

    /**
     * 用户中心数据，通过sms_id获取和存储用户中心 user_id和 company_id
     * @var array
     */
    private $ucenter_data=array();


    public function __construct(){
        $this->sms_provider = \Yii::$app->params['sms_provider'];
        $this->sms_items = \Yii::$app->params['sms_items'];
    }



    /**
     * 发送短信（XunTao）
     *
     * @param string $mobile 手机号码
     * @param string $msg 短信内容
     * @param array $params 短信内容
     * @param string $taskid 产品id，可选
     * @param string $extnum   扩展码，可选
     * @param string $reference   扩展码，可选
     * @example "您的验证码为" . $map['F_VOICECODE'] . "，请于30分钟内正确输入验证码";
     * @return mixed
     * @throws \Exception
     */
    public function sendSMSXuntao( $mobile, $msg,$params=array()) {
        $send_rlt=array(
            'flag'=>false,//发送结果：true-成功;false-失败;
            'id'=>"",
            'error'=>"",
        );

        //设置提供商为Xuntao
        $sms_provider="Xuntao";

        $taskid=!empty($params['taskid'])?$params['taskid']:"";
        $extnum = !empty($params['extnum'])?$params['extnum']:"";
        $reference = !empty($params['reference'])?$params['reference']:"";

        $sms_items=$this->sms_items;
        if (!empty($sms_items[$sms_provider])){
            $api_url=$sms_items[$sms_provider]['apiUrl'];
            $account=$sms_items[$sms_provider]['account'];
            $password=$sms_items[$sms_provider]['password'];

            $date = new \DateTime(null, new \DateTimeZone('Asia/Shanghai'));
            $seed =  $date->format('YmdHis');
            //echo $send."\r";
            $postArr = array(
                'name' => $account, //帐号
                'seed' => $seed, //当前时间，格式：YYYYMMDD HHMISS，例如：20130806102030
                'key' => $this->_getXunTaoKey($seed,$password), //密码
                'taskid' => $taskid, //任务ID
                'ext' => $extnum, //扩展号码（视通道是否支持扩展，可以为空或不填）
                'dest' => $mobile, //接收号码，多个用逗号分隔
                'content' =>  $msg , //短信内容
                'reference' => $reference , //参考值
            );
            $result = $this->curlPost( $api_url."send.do" , $postArr);
            error_log(var_export($result ,true),3,'/tmp/dddd.log');
            if (!empty($result) && is_string($result)){
                $result=explode(":",$result);

                if (!empty($result[0])){
                    if($result[0]=='error'){
                        if (!empty($result[1])){
                            $send_rlt['error']=$result[1];
                        }
                    }elseif($result[0]=='success'){
                        $send_rlt['flag']=true;
                        //Xuntao短信平台返回的短信标识
                        if (!empty($result[1])){
                            $send_rlt['id']=$result[1];
                        }
                    }
                }
            }
        }else{
            $send_rlt['error']="获取短信发送配置失败({$sms_provider}})";
        }

        return $send_rlt;
    }

    /**
     * 通过短信服务发送短信
     *
     * @param $mobile
     * @param $msg
     * @param $params
     * @author zhanglx<zhnaglx@51lick.cn>
     * @date 2020/05/25
     * @return array|mixed
     */
    public function sendSMSCenter($mobile, $msg,$params){

        $sms_provider="sms_center";

        $send_rlt=array(
            'flag'=>false,//发送结果：true-成功;false-失败;
            'id'=>"",
            'error'=>"",
        );

        $ucenter_user_id=0;
        $ucenter_company_id=0;
        if (!empty($params['sms_id'])){
            $sms_id=$params['sms_id'];
            //用户中心数据，通过sms_id获取和存储用户中心 user_id和 company_id
            $ucenter_data=$this->ucenter_data;
            if (isset($ucenter_data[$sms_id])){

                if (!empty($ucenter_data[$sms_id]['company_id'])){
                    $ucenter_company_id=$ucenter_data[$sms_id]['company_id'];
                }

                if (!empty($ucenter_data[$sms_id]['user_id'])){
                    $ucenter_user_id=$ucenter_data[$sms_id]['user_id'];
                }
            }else{
                /**@var SmsModel $smsModel*/
                $smsModel=$this->container->get("xbam.sms.model.sms");
                //通过短信ID获取用户中心的user_id和company_id
                $ucenter_data=$smsModel->getUCenterDataBySmsId($sms_id);

                if (!empty($ucenter_data['company_id'])){
                    $ucenter_company_id=$ucenter_data['company_id'];
                }

                if (!empty($ucenter_data['user_id'])){
                    $ucenter_user_id=$ucenter_data['user_id'];
                }

                $this->ucenter_data[$sms_id]=$ucenter_data;

            }
        }


        $sms_items=$this->sms_items;

        if (!empty($sms_items[$sms_provider])){
            $url=$sms_items[$sms_provider]['url']."/sms/customSmsSend";

            $reference="";
            if (!empty($params['reference'])){
                $reference=$params['reference'];
            }

            $postArr=array(
                'userId'=>$ucenter_user_id,
                'companyId'=>$ucenter_company_id,
                'content'=>$msg,
                'businessType'=>4,//短信业务类型 1小宝招商 2小宝销售 3小名片 4自动化营销
                'dest'=>$mobile,
                'reference'=>$reference,
            );
            $result=$this->curlPostSmsCenter( $url , json_encode($postArr));
            if (is_string($result) && !empty($result)){
                $result=json_decode($result,true);
                if (is_array($result) && !empty($result)){
                    if (!empty($result['data']['items']['id'])){
                        $send_rlt['flag']=true;
                        $send_rlt['id']=$result['data']['items']['id'];
                    }else if (!empty($result['data']['alert_msg'])){
                        $send_rlt['error']=$result['data']['alert_msg'];
                    }

                }
            }
        }else{
            $send_rlt['error']="获取短信发送配置失败({$sms_provider})";
        }

        return $send_rlt;
    }

    /**
     *
     * 查询Xuntao 额度
     *
     * @since File available since Version 1.0
     * @author goen<goen88@163.com>
     * @return mixed
     */
    public function queryBalanceXuntao() {

        //设置提供商为Xuntao
        $sms_provider="Xuntao";


        $account="";
        $password="";

        $sms_items=$this->sms_items;
        if (!empty($sms_items[$sms_provider])){
            $account=$sms_items[$sms_provider]['account'];
            $password=$sms_items[$sms_provider]['password'];
        }

        //接口参数
        $seed = date("YmdHis");
        $postArr = array(
            'name' => $account, //帐号
            'seed' => $seed, //当前时间，格式：YYYYMMDD HHMISS，例如：20130806102030
            'key' => $this->_getXunTaoKey($seed,$password), //密码
        );

        $result = $this->curlPost( $this->apiUrl."balance.do" , $postArr);
        return $result;
    }

    /**
     * 短信平台供应商
     *
     * @return string
     */
    public function getSmsProvider(){
        return $this->sms_provider;
    }

    /**
     * 发送短信
     *
     * @param $mobile
     * @param $msg
     * @param $params
     * @return bool
     * @throws \Exception
     */
    public  function sendSMS($mobile, $msg,$params=array()){
        //清空上一次发送结果
        $this->clear();

        $sms_provider=$this->sms_provider;
        if (!empty($params['sms_provider'])){
            $sms_provider=$params['sms_provider'];
        }

        /**TODO::为配合测试，注释发送短信*/
        switch ($sms_provider){
            case "Xuntao":{
                $send_rlt=$this->sendSMSXuntao($mobile, $msg,$params);
                break;
            }

            case "sms_center":{
                $send_rlt=$this->sendSMSCenter($mobile, $msg,$params);
                break;
            }
        }

//        usleep(150000); //  模拟发短信请求网络io时间
//        usleep(15000); //  模拟发短信请求网络io时间
//        usleep(1500); //  模拟发短信请求网络io时间
//        $send_rlt=array(
//            'flag'=>true,
//            'id'=>'test_'.md5(uniqid().rand(10000,99999)),
//            'error'=>"",
//        );


        //发送结果
        if (isset($send_rlt['flag']) && $send_rlt['flag']===true){
            $rlt=true;
        }else{
            $rlt=false;

            //错误返回
            if (!empty($send_rlt['error'])){
                $this->setError($send_rlt['error']);
            }else{
                $this->setError("发送短信失败");
            }
        }


        //发送成功回执ID
        if (!empty($send_rlt['id'])){
            $this->setPtMsgId($send_rlt['id']);
        }

        return $rlt;
    }


    public function clear(){
        $this->setPtMsgId('');
        $this->setError('');
    }

    /**
     * 获取第三方平台反馈ID
     * @return string
     */
    public function getPtMsgId(){
        return $this->pt_msg_id;
    }

    /**
     * 设置第三方平台反馈ID
     *
     * @param $pt_msg_id
     * @return $this
     */
    public function setPtMsgId($pt_msg_id){
        $this->pt_msg_id=$pt_msg_id;
        return $this;
    }


    /**
     * 获取错误信息
     * @return string
     */
    public function getError(){
        return $this->error;
    }

    /**
     * 设置错误信息
     *
     * @param $error
     * @return $this
     */
    public function setError($error){
        $this->error=$error;
        return $this;
    }


    /**
     * 通过CURL发送HTTP请求
     * @param string $url  //请求URL
     * @param array $postFields //请求参数
     * @return mixed
     */
    private function curlPost($url,$postFields){
        $postFields = http_build_query($postFields);
        $ch = curl_init ();
        curl_setopt ( $ch, CURLOPT_POST, 1 );
        curl_setopt ( $ch, CURLOPT_HEADER, 0 );
        curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
        curl_setopt ( $ch, CURLOPT_URL, $url );
        curl_setopt ( $ch, CURLOPT_POSTFIELDS, $postFields );
        $result = curl_exec ( $ch );
        curl_close ( $ch );
        return $result;
    }


    /**
     * 通过CURL发送HTTP请求
     * @param string $url  //请求URL
     * @param array $postFields //请求参数
     * @return mixed
     */
    private function curlPostSmsCenter($url,$postFields){



        $ch = curl_init ();

        $header = array(
            'Accept:application/json',
            'Content-Type:application/json;charset=utf-8',
        );
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);

        curl_setopt ( $ch, CURLOPT_POST, 1 );
        curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
        curl_setopt ( $ch, CURLOPT_URL, $url );
        curl_setopt ( $ch, CURLOPT_POSTFIELDS, $postFields );
        $result = curl_exec ( $ch );
        curl_close ( $ch );
        return $result;
    }

    /**
     *
     * 获取xuntao的key
     *
     * @param $send 当前时间,格式：YYYYMMDD HHMISS，例如：20130806102030
     * @since File available since Version 1.0
     * @author goen<goen88@163.com>
     * @param $seed
     * @param $password
     * @return string
     */
    private function _getXunTaoKey($seed,$password){
        return md5(md5($password).$seed);
    }

    //魔术获取
    public function __get($name){
        return $this->$name;
    }

    //魔术设置
    public function __set($name,$value){
        $this->$name=$value;
    }
}