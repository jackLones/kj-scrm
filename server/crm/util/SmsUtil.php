<?php
	/**
	 * 短信
	 * User: xcy
	 * Date: 19-09-16
	 * Time: 16:00
	 */

	namespace app\util;

	class SmsUtil
	{

		/**
		 *
		 * 批量发送短信
		 *
		 * @param string $mobile  手机号码
		 * @param string $content 短信内容
		 * @param array  $extra   额外数据
		 */
		public static function sendSms ($mobile = '', $content = '', $extra = [])
		{

			//==采用云之讯的方式发送短信，业务短信==================
			$template_id = 374755;
			$smsApi = new SmsUcpaas();
	    	$rlt = $smsApi->templateSMS($mobile, $template_id,$extra['code']);

	    	$arr = json_decode($rlt,true);
			if(isset($arr['resp']['respCode'])&&$arr['resp']['respCode']==='000000'){
	    		return 0;
	    	}else{
	    		return '发送短信验证码失败['.$arr['resp']['respCode'].']';
	    	}
       
		   	//==采用讯淘的方式，讯淘能用用于发送营销短信==================
		    /*$smsXT =  new SmsXuntao();
			if (empty($mobile)) {
				return '手机号不能为空';
			}
			$content    = SmsUtil::_safe_replace($content);
            $rlt = $smsXT->sendSMS($mobile,"【钮扣信科】".$content.",回T退订");//"【日思夜想营销云】 【钮扣信科】节日期间注意数据安全备份，数据恢复紧急保修电话16628511980 lyx0.cn/0dJk 回T退订".

            if($rlt===true){
                return 0;
            }else{
               return  $smsXT->getError();
            }
            */

            //==原始逻辑==================
            /*
			$sms_config = \Yii::$app->params['sms'];
			if (empty($sms_config)) {
				return '发送短信数据未配置';
			}
			$token = 'cashier';
			$data  = [
				'topdomain' => $sms_config['sms_topdomain'],
				'key'       => $sms_config['sms_key'],
				'token'     => $token,
				'content'   => $content,
				'mobile'    => $mobile,
				'sign'      => $sms_config['sms_sign']
			];

			foreach ($data as $k => $v) {
				$post .= $k . '=' . $v . '&';
			}

			$url    = 'http://up.pigcms.cn/oa/admin.php?m=sms&c=sms&a=send';
			$return = SmsUtil::_post($url, 0, $post);

			return $return;
            */
		}

		/**
		 *  post数据
		 *
		 * @param string $url     post的url
		 * @param int    $limit   返回的数据的长度
		 * @param string $post    post数据，字符串形式username='dalarge'&password='123456'
		 * @param string $cookie  模拟 cookie，字符串形式username='dalarge'&password='123456'
		 * @param string $ip      ip地址
		 * @param int    $timeout 连接超时时间
		 * @param bool   $block   是否为阻塞模式
		 *
		 * @return string            返回字符串
		 */

		private static function _post ($url, $limit = 0, $post = '', $cookie = '', $ip = '', $timeout = 15, $block = true)
		{
			$return  = '';
			$url     = str_replace('&amp;', '&', $url);
			$matches = parse_url($url);
			$host    = $matches['host'];
			$path    = $matches['path'] ? $matches['path'] . ($matches['query'] ? '?' . $matches['query'] : '') : '/';
			$port    = !empty($matches['port']) ? $matches['port'] : 80;
			$siteurl = SmsUtil::_get_url();
			if ($post) {
				$out = "POST $path HTTP/1.1\r\n";
				$out .= "Accept: */*\r\n";
				$out .= "Referer: " . $siteurl . "\r\n";
				$out .= "Accept-Language: zh-cn\r\n";
				$out .= "Content-Type: application/x-www-form-urlencoded\r\n";
				$out .= "User-Agent: $_SERVER[HTTP_USER_AGENT]\r\n";
				$out .= "Host: $host\r\n";
				$out .= 'Content-Length: ' . strlen($post) . "\r\n";
				$out .= "Connection: Close\r\n";
				$out .= "Cache-Control: no-cache\r\n";
				$out .= "Cookie: $cookie\r\n\r\n";
				$out .= $post;
			} else {
				$out = "GET $path HTTP/1.1\r\n";
				$out .= "Accept: */*\r\n";
				$out .= "Referer: " . $siteurl . "\r\n";
				$out .= "Accept-Language: zh-cn\r\n";
				$out .= "User-Agent: $_SERVER[HTTP_USER_AGENT]\r\n";
				$out .= "Host: $host\r\n";
				$out .= "Connection: Close\r\n";
				$out .= "Cookie: $cookie\r\n\r\n";
			}
			$fp = @fsockopen(($ip ? $ip : $host), $port, $errno, $errstr, $timeout);
			if (!$fp) {
				return '';
			}
			\Yii::info($out);
			stream_set_blocking($fp, $block);
			stream_set_timeout($fp, $timeout);
			@fwrite($fp, $out);
			$status = stream_get_meta_data($fp);

			if ($status['timed_out']) {
				return '';
			}
			while (!feof($fp)) {
				if (($header = @fgets($fp)) && ($header == "\r\n" || $header == "\n")) {
					break;
				}
			}

			$stop = false;
			while (!feof($fp) && !$stop) {
				$data   = fread($fp, ($limit == 0 || $limit > 8192 ? 8192 : $limit));
				$return .= $data;
				if ($limit) {
					$limit -= strlen($data);
					$stop  = $limit <= 0;
				}
			}
			@fclose($fp);

			//部分虚拟主机返回数值有误，暂不确定原因，过滤返回数据格式
			$return_arr = explode("\n", $return);

			if (isset($return_arr[1])) {
				$return = trim($return_arr[1]);
			}
			unset($return_arr);

			return $return;
		}

		/**
		 * 获取当前页面完整URL地址
		 */
		private static function _get_url ()
		{
			$sys_protocal = isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == '443' ? 'https://' : 'http://';
			$php_self     = $_SERVER['PHP_SELF'] ? SmsUtil::_safe_replace($_SERVER['PHP_SELF']) : SmsUtil::_safe_replace($_SERVER['SCRIPT_NAME']);
			$path_info    = isset($_SERVER['PATH_INFO']) ? SmsUtil::_safe_replace($_SERVER['PATH_INFO']) : '';
			$relate_url   = isset($_SERVER['REQUEST_URI']) ? SmsUtil::_safe_replace($_SERVER['REQUEST_URI']) : $php_self . (isset($_SERVER['QUERY_STRING']) ? '?' . SmsUtil::_safe_replace($_SERVER['QUERY_STRING']) : $path_info);

			return $sys_protocal . (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '') . $relate_url;
		}

		/**
		 * 安全过滤函数
		 *
		 * @param $string
		 *
		 * @return string
		 */
		private static function _safe_replace ($string)
		{
			$string = str_replace('%20', '', $string);
			$string = str_replace('%27', '', $string);
			$string = str_replace('%2527', '', $string);
			$string = str_replace('*', '', $string);
			$string = str_replace('"', '&quot;', $string);
			$string = str_replace("'", '', $string);
			$string = str_replace('"', '', $string);
			$string = str_replace(';', '', $string);
			$string = str_replace('<', '&lt;', $string);
			$string = str_replace('>', '&gt;', $string);
			$string = str_replace("{", '', $string);
			$string = str_replace('}', '', $string);
			$string = str_replace('\\', '', $string);

			return $string;
		}
	}