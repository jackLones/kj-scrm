<?php
	/**
	 * Created by PhpStorm.
	 * User: Dove Chen
	 * Date: 19-09-07
	 * Time: 上午09:04
	 */

	namespace app\util;

	class StringUtil
	{

		public static function randomStr ($lng = 6, $onlyNum = false)
		{
			$chars       = ["a", "b", "c", "d", "e", "f", "g",
				"h", "i", "j", "k", "l", "m", "n",
				"o", "p", "q", "r", "s", "t", "u",
				"v", "w", "x", "y", "z",
				"A", "B", "C", "D", "E", "F", "G",
				"H", "I", "J", "K", "L", "M", "N",
				"O", "P", "Q", "R", "S", "T", "U",
				"V", "W", "X", "Y", "Z",
				"0", "1", "2", "3", "4", "5", "6", "7", "8", "9"];
			$numChars    = ["0", "1", "2", "3", "4", "5", "6", "7", "8", "9"];
			$ret         = '';
			$targetChars = $onlyNum ? $numChars : $chars;
			for ($i = 0; $i < $lng; $i++) {
				$keys = array_rand($targetChars, 1);
				$ret  .= $targetChars[$keys];
			}

			return $ret;
		}

		public static function randomNoNumberStr ($lng = 6)
		{
			$chars = [
				"a", "b", "c", "d", "e", "f", "g",
				"h", "i", "j", "k", "l", "m", "n",
				"o", "p", "q", "r", "s", "t",
				"u", "v", "w", "x", "y", "z"];
			$ret   = '';
			for ($i = 0; $i < $lng; $i++) {
				$keys = array_rand($chars, 1);
				$ret  .= $chars[$keys];
			}

			return $ret;
		}

		public static function isEmail ($str)
		{
			return strlen($str) > 6 && preg_match("/^[\w\-\.]+@[\w\-]+(\.\w+)+$/", $str);
		}

		public static function isPhoneNumber ($str)
		{
			return preg_match("/1[34578]{1}\d{9}$/", $str);
		}

		public static function encodePassword ($salt, $password)
		{
			return hash('sha256', $salt . $password);
		}

		public static function uuid ()
		{
			$chars = md5(uniqid(rand(), true));

			return str_replace('-', '', $chars);
		}

		public static function getImgString ($filePath)
		{
			$imgString = '';
			if ($fileP = fopen($filePath, "r")) {
				$file     = file_get_contents($filePath);
				$fileInfo = getimagesize($filePath);
				$fileTemp = fread($fileP, strlen($file));
				fclose($fileP);

				$base64 = chunk_split(base64_encode($fileTemp));

				$imgString = "data:" . $fileInfo['mime'] . ";base64," . $base64;
			}

			return $imgString;
		}

		/**
		 * @param int $size //字节数
		 * @param int $dec  //精确位数
		 * @param int $pos  //起始字节单位 0="B", 1="KB", 2="MB", 3="GB", 4="TB", 5="PB", 6="EB", 7="ZB", 8="YB"
		 *
		 * @return string
		 */
		public static function geByteFormat ($size, $dec = 2, $pos = 0)
		{
			$a = ["B", "KB", "MB", "GB", "TB", "PB", "EB", "ZB", "YB"];

			while ($size >= 1024) {
				$size /= 1024;
				$pos++;
			}

			return round($size, $dec) . " " . $a[$pos];
		}

		/**
		 *
		 * 获取文本文件内容
		 *
		 * @param $filePath //文件真实路径
		 *
		 * @return false|string $content
		 *
		 * @remark Create by PhpStorm. User: beenlee. Date: 2020/12/3 4:11 下午
		 */
		public static function getTxtContent ($filePath)
		{
			if ($fname = $filePath) {
				//获取文件的编码方式
				$contents = file_get_contents($fname);
				$encoding = mb_detect_encoding($contents, ['GB2312', 'CP936', 'GBK', 'UTF-16', 'UCS-2', 'UTF-8', 'BIG5', 'ASCII']);
				$fp       = fopen($fname, "r");//以只读的方式打开文件
				$text     = "";
				$num      = 0;
				if (!(feof($fp))) {
					$num++;
					$str = trim(fgets($fp));
					if ($encoding != false) {
						if ($encoding == "CP936") {
							$str = $str;//iconv('latin1', 'UTF-8//IGNORE', $str);
						} else {
							$str = iconv($encoding, 'UTF-8', $str);
						}

						if ($str != "" && $str != NULL) {
							$text = $str;
						}
					} else {
						$str = mb_convert_encoding($str, 'UTF-8', 'Unicode');
						if ($str != "" && $str != NULL) {
							$text = $str;
						}
					}
				}

				while (!(feof($fp))) {
				$str1 = trim(fgets($fp));
				
					$str = '';
					$str .= $str1;
					if ($encoding != false) {
						if ($encoding == "CP936") {
							$str = $str;//iconv('latin1', 'UTF-8//IGNORE', $str);
						} else {
							$str = iconv($encoding, 'UTF-8', $str);
						}
						if ($str != "" && $str != NULL) {
							$text .= PHP_EOL. '<br/>' . $str;
						}
					} else {
						$str = mb_convert_encoding($str, 'UTF-8', 'Unicode');
						if ($str != "" && $str != NULL) {
							$text .= PHP_EOL. '<br/>' . $str;
						}
					}
				}
			}

			/*$text = file_get_contents($filePath);
			define('UTF32_BIG_ENDIAN_BOM', chr(0x00) . chr(0x00) . chr(0xFE) . chr(0xFF));
			define('UTF32_LITTLE_ENDIAN_BOM', chr(0xFF) . chr(0xFE) . chr(0x00) . chr(0x00));
			define('UTF16_BIG_ENDIAN_BOM', chr(0xFE) . chr(0xFF));
			define('UTF16_LITTLE_ENDIAN_BOM', chr(0xFF) . chr(0xFE));
			define('UTF8_BOM', chr(0xEF) . chr(0xBB) . chr(0xBF));
			$first2    = substr($text, 0, 2);
			$first3    = substr($text, 0, 3);
			$first4    = substr($text, 0, 3);
			$encodType = "";
			if ($first3 == UTF8_BOM) {
				$encodType = 'UTF-8 BOM';
			} else if ($first4 == UTF32_BIG_ENDIAN_BOM) {
				$encodType = 'UTF-32BE';
			} else if ($first4 == UTF32_LITTLE_ENDIAN_BOM) {
				$encodType = 'UTF-32LE';
			} else if ($first2 == UTF16_BIG_ENDIAN_BOM) {
				$encodType = 'UTF-16BE';
			} else if ($first2 == UTF16_LITTLE_ENDIAN_BOM) {
				$encodType = 'UTF-16LE';
			}

			try {
				//下面的判断主要还是判断ANSI编码的
				if ($encodType == '') {
					//即默认创建的txt文本-ANSI编码的
					$content = iconv("gbk", 'utf-8', $text);
				} else if ($encodType == 'UTF-8 BOM') {
					//本来就是utf-8不用转换
					$content = $text;
				} else {
					//其他的格式都转化为utf-8就可以了
					$content = iconv($encodType, 'utf-8', $text);
				}
			} catch (\Exception $e) {
				$content = $text;
			}*/

			return $text;
		}
	}