<?php

	namespace app\util\WxPay;

	use app\components\InvalidDataException;
	use app\models\MoneyPayconfig;

	require_once "WxPay.Api.php";
	require_once "WxPay.Config.php";
	require_once 'WxPay.Data.php';
	require_once 'log.php';

	/**
	 * 发送红包实现类
	 * fulu
	 */
	class RedPacketPay
	{
		public $payConfig;
		private $packetUrl = 'https://api.mch.weixin.qq.com/mmpaymkttransfers/promotion/transfers';

		/**
		 * @param $corpId
		 * @param $orderData
		 *
		 * @return mixed
		 *
		 * @throws InvalidDataException
		 * @throws WxPayException
		 */
		public function RedPacketSend ($corpId, $orderData,$appid='')
		{
			$this->payConfig = $this->PayConfig($corpId);
			\Yii::error($this->payConfig, 'payConfig');

			$postData = [
				'mch_appid'        => empty($appid)?$this->payConfig['appid']:$appid,//商户账号appid
				'mchid'            => $this->payConfig['mchid'],//商户号
				'nonce_str'        => $this->getRandNumber(32),//随机字符串
				'partner_trade_no' => $orderData['partner_trade_no'],//商户订单号
				'openid'           => $orderData['openid'],//用户openid
				'check_name'       => 'NO_CHECK',//校验用户姓名选项
				'amount'           => $orderData['amount'],//金额
				'desc'             => $orderData['desc'],//企业付款备注
			];

			/*$input      = new WxPayUnifiedOrder();
			$postData['sign'] = $input->MakeSign($postData);*/

			$postData['sign'] = $this->getSign($postData, $this->payConfig['key']);

			$postXml = $this->arrayToXml($postData);

			//发送红包
			$responseXml = $this->curl_post_ssl($this->packetUrl, $postXml, 30, 1);
			if (!$responseXml) {
				throw new WxPayException("curl出错");
			}

			\Yii::error($responseXml, '$responseXml');
			return json_decode(json_encode(simplexml_load_string($responseXml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
		}

		/**
		 * 微信支付配置信息
		 *
		 * @param $corpId
		 *
		 * @return MoneyPayconfig
		 *
		 * @throws InvalidDataException
		 */
		private function PayConfig ($corpId)
		{
			$payData = MoneyPayconfig::findOne(['corp_id' => $corpId, 'status' => 1]);
			if (empty($payData)) {
				throw new InvalidDataException('未获取到微信支付配置信息！');
			}
			if (empty($payData->appid)) {
				throw new InvalidDataException('appid不能为空！');
			}
			if (empty($payData->mchid)) {
				throw new InvalidDataException('微支付商户号不能为空！');
			}
			if (empty($payData->key)) {
				throw new InvalidDataException('API密钥不能为空！');
			}
			if (empty($payData->apiclient_cert)) {
				throw new InvalidDataException('apiclient_cert私钥文件不能为空！');
			}
			if (empty($payData->apiclient_key)) {
				throw new InvalidDataException('apiclient_key公钥文件不能为空！');
			}

			return $payData;
		}

		/**
		 * 生成随机数
		 *
		 * @param int $bit
		 *
		 * @return string
		 */
		private function getRandNumber ($bit = 30)
		{
			$str = '1234567890abcdefghijklmnopqrstuvwxyz';
			$t1  = '';
			for ($i = 0; $i < $bit; $i++) {
				$j  = rand(0, 35);
				$t1 .= $str[$j];
			}

			return $t1;
		}

		/**
		 * 获取签名
		 *
		 * @param $parameters
		 * @param $key
		 *
		 * @return string
		 */
		private function getSign ($parameters, $key)
		{
			//按照键名排序
			ksort($parameters);
			//获取未加密签名
			$unCodedSign = $this->formatSign($parameters, false);

			return $this->codedSign($unCodedSign, $key);
		}

		/**
		 * 签名格式化
		 *
		 * @param $paraMap
		 * @param $urlencode
		 *
		 * @return false|string
		 *
		 */
		private function formatSign ($paraMap, $urlencode)
		{
			$buff = "";
			//按照键名排序
			ksort($paraMap);
			//拼接
			foreach ($paraMap as $k => $v) {
				if (NULL != $v && "null" != $v && "sign" != $k) {
					if ($urlencode) {
						//编码 URL 字符串
						$v = urlencode($v);
					}
					$buff .= $k . "=" . $v . "&";
				}
			}
			$reqPar = "";
			if (strlen($buff) > 0) {
				$reqPar = substr($buff, 0, strlen($buff) - 1);
			}

			return $reqPar;
		}

		/**
		 * 签名加密
		 *
		 * @param $content
		 * @param $key
		 *
		 * @return string
		 *
		 */
		private function codedSign ($content, $key)
		{
			$signStr = $content . "&key=" . $key;

			return strtoupper(md5($signStr));
		}

		/**
		 * 数组转xml
		 *
		 * @param $arr
		 *
		 * @return string
		 */
		private function arrayToXml ($arr)
		{
			$xml = "<xml>";
			foreach ($arr as $key => $val) {
				if (is_numeric($val)) {
					$xml .= "<" . $key . ">" . $val . "</" . $key . ">";

				} else {
					$xml .= "<" . $key . "><![CDATA[" . $val . "]]></" . $key . ">";
				}
			}
			$xml .= "</xml>";

			return $xml;
		}

		/**
		 * 以post方式提交xml到对应的接口url
		 *
		 * @param       $url
		 * @param       $xml
		 * @param int   $second
		 * @param bool  $useCert
		 * @param array $aHeader
		 *
		 * @return bool|string
		 *
		 * @throws WxPayException
		 */
		private function curl_post_ssl ($url, $xml, $second = 30, $useCert = false, $aHeader = [])
		{
			$ch = curl_init();
			//超时时间
			curl_setopt($ch, CURLOPT_TIMEOUT, $second);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			//这里设置代理，如果有的话
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);

			if ($useCert == true) {
				//cert 与 key 分别属于两个.pem文件
				curl_setopt($ch, CURLOPT_SSLCERTTYPE, 'PEM');
				curl_setopt($ch, CURLOPT_SSLCERT, str_replace('/util/WxPay', '', __DIR__ . $this->payConfig['apiclient_cert']));
				curl_setopt($ch, CURLOPT_SSLKEYTYPE, 'PEM');
				curl_setopt($ch, CURLOPT_SSLKEY, str_replace('/util/WxPay', '', __DIR__ . $this->payConfig['apiclient_key']));
			}

			if (count($aHeader) >= 1) {
				curl_setopt($ch, CURLOPT_HTTPHEADER, $aHeader);
			}
			//post提交方式
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
			//运行curl
			$data = curl_exec($ch);
			//返回结果
			if ($data) {
				curl_close($ch);

				return $data;
			} else {
				$error = curl_errno($ch);
				curl_close($ch);
				throw new WxPayException("curl出错，错误码:$error");
			}
		}

	}