<?php
	/**
	 * Create by PhpStorm
	 * User: dovechen
	 * Date: 2020/5/27
	 * Time: 18:21
	 */

	namespace app\util;

	class PemUtils
	{
		/**
		 * 生成 2048 的私钥和公钥
		 *
		 * @param $uid
		 *
		 * @return array
		 *
		 */
		public static function create2048 ($uid)
		{

			$saveDir  = "/${uid}/" . DateUtil::getCurrentShortYMD() . "/";
			$savePath = \Yii::getAlias('@pem') . $saveDir;
			if (!is_dir($savePath) && !mkdir($savePath, 0755, true)) {
				return ['error' => 1, 'msg' => '无法创建目录'];
			}

			$privateFileName = 'private_' . DateUtil::getCurrentShortTime() . '.pem';
			$privateFilePath = $savePath . $privateFileName;
			$publicFileName  = 'public_' . DateUtil::getCurrentShortTime() . '.pem';
			$publicFilePath  = $savePath . $publicFileName;

			shell_exec("openssl genrsa -out ${privateFilePath} 2048");
			shell_exec("openssl rsa -in ${privateFilePath} -pubout -out ${publicFilePath}");

			$priKey = file_get_contents($privateFilePath);
			$pubKey = file_get_contents($publicFilePath);

			return [
				'error'        => 0,
				'private_key'  => $priKey,
				'private_path' => '/pem' . $saveDir . $privateFileName,
				'public_key'   => $pubKey,
				'public_path'  => '/pem' . $saveDir . $publicFileName,
			];
		}
	}