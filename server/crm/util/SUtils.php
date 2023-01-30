<?php
	/**
	 * Create by PhpStorm
	 * User: dovechen
	 * Date: 2019/9/16
	 * Time: 19:06
	 */

	namespace app\util;

	use Exception;
	use yii\base\Model;

	class SUtils
	{
		const FISSION_WEBSOCKET_TYPE = 'fission';
		const AWARD_WEBSOCKET_TYPE = 'award';
		const RED_WEBSOCKET_TYPE = 'red';
		const KEYWORD_WEBSOCKET_TYPE = 'keyword';
		const AWARD_ADD_NUMBER = 'award_add';

		const IS_WORK_USER = 1;
		const IS_EXTERNAL_USER = 2;
		const IS_ROBOT_USER = 3;

		const IS_WX_EXTERNAL = 1;
		const IS_WORK_WX_EXTERNAL = 2;

		/**
		 * Return the first occurred validation error.
		 *
		 * @param Model $model
		 *
		 * @return string false will be returned if the model has no error
		 */
		public static function modelError ($model)
		{
			foreach ($model->getErrors() as $errorInfo) {
				foreach ($errorInfo as $msg) {
					return $msg;
				}
			}

			return false;
		}

		/**
		 * 数组 转 对象
		 *
		 * @param array $arr 数组
		 *
		 * @return object|void
		 */
		public static function Array2Object ($arr)
		{
			if (gettype($arr) != 'array') {
				return;
			}
			foreach ($arr as $k => $v) {
				if (gettype($v) == 'array' || getType($v) == 'object') {
					$arr[$k] = (object) self::Array2Object($v);
				}
			}

			return (object) $arr;
		}

		/**
		 * 对象 转 数组
		 *
		 * @param object $object 对象
		 *
		 * @return object|array
		 */
		public static function Object2Array ($object)
		{
			if (is_object($object) || is_array($object)) {
				$array = [];
				foreach ($object as $key => $value) {
					$array[$key] = self::Object2Array($value);
				}

				return $array;
			} else {
				return $object;
			}
		}

		/**
		 * 数组转XML
		 *
		 * @param       $rootName
		 * @param array $arr
		 *
		 * @return string
		 */
		public static function Array2Xml ($rootName, $arr)
		{
			$xml = "<" . $rootName . ">";
			foreach ($arr as $key => $val) {
				if (is_numeric($val)) {
					$xml .= "<" . $key . ">" . $val . "</" . $key . ">";
				} else {
					$xml .= "<" . $key . "><![CDATA[" . $val . "]]></" . $key . ">";
				}
			}
			$xml .= "</" . $rootName . ">";

			return $xml;
		}

		/**
		 * 将XML转为array
		 *
		 * @param $xml
		 *
		 * @return array
		 */
		public static function Xml2Array ($xml)
		{
			//禁止引用外部xml实体
			libxml_disable_entity_loader(true);
			$array = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);

			return $array;
		}

		/**
		 * @param array $arr
		 * @param int   $case
		 */
		public static function arrayCase (&$arr, $case = CASE_LOWER)
		{
			$arr = array_change_key_case($arr, $case);

			foreach ($arr as $key => $value) {
				if (is_array($value)) {
					static::arrayCase($arr[$key], $case);
				}
			}
		}

		/**
		 * 二维数组取差集
		 *
		 * @param array $arr1
		 * @param array $arr2
		 */
		public static function arrayDiff (&$arr1, $arr2)
		{
			foreach ($arr1 as $k => $v) {
				if (in_array($v, $arr2)) {
					unset($arr1[$k]);
				}
			}
		}

		/**
		 * 生成一个Gravatar头像
		 *
		 * @param string $key
		 * @param int    $size
		 *
		 * @return string
		 */
		public static function makeGravatar ($key, $size = 120)
		{
			$hash = md5($key);

			return "https://www.gravatar.com/avatar/{$hash}?s={$size}&d=identicon";
		}

		/**
		 * 二维数组根据某个字段去重
		 *
		 * @param $arr
		 * @param $key
		 *
		 * @return array
		 *
		 */
		public static function array_unset_tt ($arr, $key)
		{
			//建立一个目标数组
			$res = [];
			foreach ($arr as $value) {
				//查看有没有重复项
				if (isset($res[$value[$key]])) {
					//有：销毁
					unset($value[$key]);
				} else {
					$res[$value[$key]] = $value;
				}
			}

			return $res;
		}

		/**
		 * 二维数组去重
		 *
		 * @param $data
		 *
		 * @return array
		 *
		 */
		public static function array_unique_two ($data)
		{
			$resData = [];
			foreach ($data as $key => $value) {
				//重新排序value
				ksort($value);
				//获取key ，判断是否存在的依据
				$key = implode("_", $value);
				//md5 为了防止字段内容过长特殊字符等
				$resData[md5($key)] = $value;
			}
			$resData = array_values($resData);

			return $resData;
		}

		/**
		 * 二维数组根据某个字段去重
		 *
		 * @param $array
		 *
		 * @return array
		 *
		 */
		public static function uniquArr ($array)
		{
			$result = [];
			foreach ($array as $k => $val) {
				$code = false;
				foreach ($result as $_val) {
					if ($_val['id'] == $val['id']) {
						$code = true;
						break;
					}
				}
				if (!$code) {
					$result[] = $val;
				}
			}

			return $result;
		}

		/**
		 *
		 * @param $url
		 * @param $data
		 *
		 * @return mixed
		 *
		 */
		public static function postUrl ($url, $data)
		{
			$ch     = curl_init();
			$header = ["Accept-Charset: utf-8"];
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
			curl_setopt($ch, CURLOPT_HTTPHEADER, $header);

			if (!empty($data)) {
				curl_setopt($ch, CURLOPT_POST, 1);
				curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
			}

			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

			$output = curl_exec($ch);
			curl_close($ch);

			return json_decode($output, true);
		}

		/**
		 * @param $userId
		 *
		 * @return int
		 */
		public static function getUserType ($userId)
		{
			if (strpos($userId, 'wm') === 0 || strpos($userId, 'wo') === 0) {
				return self::IS_EXTERNAL_USER;
			} elseif (strpos($userId, 'wb') === 0) {
				return self::IS_ROBOT_USER;
			} else {
				return self::IS_WORK_USER;
			}
		}

		/**
		 * @param $externalId
		 *
		 * @return int
		 */
		public static function getExternalType ($externalId)
		{
			if (strpos($externalId, 'wm') === 0) {
				return self::IS_WX_EXTERNAL;
			} elseif (strpos($externalId, 'wo') === 0) {
				return self::IS_WORK_WX_EXTERNAL;
			} else {
				return 0;
			}
		}

		/**
		 * 判断二维数组是否有重复值
		 *
		 * @param array  $list  二维数组
		 * @param string $param 字段
		 *
		 * @return bool true 有重复值 false 没有
		 *
		 */
		public static function checkRepeatArray ($list, $param)
		{
			$list = array_column($list, $param);
			if (count($list) != count(array_unique($list))) {
				return true;
			} else {
				return false;
			}
		}

		/**
		 * @param        $uid
		 * @param        $fileName
		 * @param        $fileData
		 * @param string $msgDate
		 *
		 * @return array|string
		 *
		 */
		public static function saveMsgAuditFile ($uid, $fileName, $fileData, $msgDate = '', $mode = '')
		{
			if (empty($msgDate)) {
				$msgDate = DateUtil::getCurrentYMD();
			}
			$saveDir  = "/msg-audit/${uid}/${msgDate}/";
			$savePath = \Yii::getAlias("@upload") . $saveDir;

			if (!is_dir($savePath) && !mkdir($savePath, 0755, true)) {
				return ['error' => 1, 'msg' => '无法创建目录'];
			}

			if (empty($mode)) {
				file_put_contents($savePath . $fileName, base64_decode($fileData));
			} else {
				file_put_contents($savePath . $fileName, base64_decode($fileData), $mode);
			}

			unset($fileData);

			return '/upload' . $saveDir . $fileName;
		}

		/**
		 * 二维数组根据某个字段排序
		 *
		 * @param $arr
		 * @param $key
		 *
		 * @return array
		 *
		 */
		public static function arrayGroupBy ($arr, $key)
		{
			$grouped = [];
			foreach ($arr as $value) {
				$grouped[$value[$key]][] = $value;
			}
			if (func_num_args() > 2) {
				$args = func_get_args();
				foreach ($grouped as $key => $value) {
					$parms         = array_merge([$value], array_slice($args, 2, func_num_args()));
					$grouped[$key] = call_user_func_array('arrayGroupBy', $parms);
				}
			}

			return $grouped;
		}

		/**
		 * 获取汉字的首字母
		 *
		 * @param $s
		 *
		 * @return string
		 *
		 */
		public static function getFirstChar ($s)
		{
			//判断是否是汉字
			$char = substr($s, 0, 1);
			if (preg_match('/^[\x7f-\xff]+$/', $char)) {
				try {
					$s0 = mb_substr($s, 0, 1, 'utf-8');//获取名字的姓
					$s  = iconv('UTF-8', 'GBK', $s0);//将UTF-8转换成GB2312编码
					if (ord($s0) > 128) {//汉字开头，汉字没有以U、V开头的
						$asc = ord($s{0}) * 256 + ord($s{1}) - 65536;
						if ($asc >= -20319 and $asc <= -20284)
							return "A";
						if ($asc >= -20283 and $asc <= -19776)
							return "B";
						if ($asc >= -19775 and $asc <= -19219)
							return "C";
						if ($asc >= -19218 and $asc <= -18711)
							return "D";
						if ($asc >= -18710 and $asc <= -18527)
							return "E";
						if ($asc >= -18526 and $asc <= -18240)
							return "F";
						if ($asc >= -18239 and $asc <= -17760)
							return "G";
						if ($asc >= -17759 and $asc <= -17248)
							return "H";
						if ($asc >= -17247 and $asc <= -17418)
							return "I";
						if ($asc >= -17417 and $asc <= -16475)
							return "J";
						if ($asc >= -16474 and $asc <= -16213)
							return "K";
						if ($asc >= -16212 and $asc <= -15641)
							return "L";
						if ($asc >= -15640 and $asc <= -15166)
							return "M";
						if ($asc >= -15165 and $asc <= -14923)
							return "N";
						if ($asc >= -14922 and $asc <= -14915)
							return "O";
						if ($asc >= -14914 and $asc <= -14631)
							return "P";
						if ($asc >= -14630 and $asc <= -14150)
							return "Q";
						if ($asc >= -14149 and $asc <= -14091)
							return "R";
						if ($asc >= -14090 and $asc <= -13319)
							return "S";
						if ($asc >= -13318 and $asc <= -12839)
							return "T";
						if ($asc >= -12838 and $asc <= -12557)
							return "W";
						if ($asc >= -12556 and $asc <= -11848)
							return "X";
						if ($asc >= -11847 and $asc <= -11056)
							return "Y";
						if ($asc >= -11055 and $asc <= -10247)
							return "Z";
					}
				} catch (\Exception $e) {
					return "#";
				}
			} else {
				return strtoupper($char);
			}

		}

		/**
		 * @return array|false|string
		 */
		public static function getClientIP ()
		{
			if (getenv("HTTP_X_REAL_IP")) {
				$ip = getenv("HTTP_X_REAL_IP");
			} elseif (getenv("HTTP_CLIENT_IP")) {
				$ip = getenv("HTTP_CLIENT_IP");
			} elseif (getenv("HTTP_X_FORWARDED_FOR")) {
				$ip = getenv("HTTP_X_FORWARDED_FOR");
			} elseif (getenv("REMOTE_ADDR")) {
				$ip = getenv("REMOTE_ADDR");
			} else {
				$ip = \Yii::$app->request->getUserIP();
			}

			return $ip;
		}

		/**
		 * @param string $localString
		 * @param int    $offset
		 * @param int    $length
		 * @param false  $force
		 * @param string $replaceStr
		 *
		 * @return mixed|string|string[]
		 *
		 */
		public static function hideString ($localString = "", $offset = 3, $length = 4, $force = false, $replaceStr = '*')
		{
			$newString = $localString;
			if (!empty($localString)) {
				if ($force || \Yii::$app->params['hide_str']) {
					$localStringLength = mb_strlen($localString, 'utf-8');

					$offset  = $localStringLength - $offset > 0 ? $offset : 0;
					$length  = $localStringLength >= ($offset + $length) ? $length : $localStringLength - $offset;
					$replace = '';
					for ($i = 0; $i < $length; $i++) {
						$replace .= $replaceStr;
					}
					$searchString = mb_substr($localString, $offset, $length, 'utf-8');

					$newString = str_replace($searchString, $replace, $localString);
				}
			}

			return $newString;
		}

		/**
		 * @param        $localString
		 * @param false  $force
		 * @param string $replaceStr
		 *
		 * @return string
		 */
		public static function deepHideString ($localString, $force = false, $replaceStr = '*')
		{
			$localString = trim($localString);
			$newString   = $localString;
			if (!empty($localString)) {
				if ($force || \Yii::$app->params['hide_str']) {
					$localStringLength = mb_strlen($localString, 'utf-8');
					$firstString       = mb_substr($localString, 0, 1, 'utf-8');
					$endString         = $localStringLength > 1 ? mb_substr($localString, $localStringLength - 1, 1, 'utf-8') : $firstString;

					$newString = $firstString . $replaceStr . $endString;
				}
			}

			return $newString;
		}

		/**
		 * @param        $exception
		 * @param string $msg
		 *
		 * @throws Exception
		 */
		public static function throwException ($exception, $msg = '异常报错，请检查')
		{
			throw new $exception($msg);
		}

		public static function dd ()
		{
			$data = func_get_args();
			foreach ($data as $v) {
				var_dump($v);
			}
			exit;
		}
	}