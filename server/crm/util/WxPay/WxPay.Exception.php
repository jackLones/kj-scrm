<?php
/**
 * 
 * 微信支付API异常类
 * @author widyhu
 *
 */
namespace app\util\WxPay;
 
use yii\base\Exception;
 
class WxPayException extends Exception {
	public function errorMessage()
	{
		return $this->getMessage();
	}
}
