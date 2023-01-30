<?php
/**
*
* example目录下为简单的支付样例，仅能用于搭建快速体验微信支付使用
* 样例的作用仅限于指导如何使用sdk，在安全上面仅做了简单处理， 复制使用样例代码时请慎重
* 请勿直接直接使用样例对外提供服务
* 
**/
namespace app\util\WxPay;

use app\components\InvalidDataException;

require_once "WxPay.Api.php";
require_once "WxPay.Config.php";
require_once 'WxPay.Data.php';
require_once 'log.php';

/**
 * 
 * 刷卡支付实现类
 * @author widyhu
 *
 */
class NativePay
{
	/**
	 * 
	 * 生成扫描支付URL,模式一
	 * @param BizPayUrlInput $bizUrlInfo
	 */
	public function GetPrePayUrl($productId)
	{
		$biz = new WxPayBizPayUrl();
		$biz->SetProduct_id($productId);
		try{
			$config = new WxPayConfig();
			$values = WxpayApi::bizpayurl($config, $biz);
		} catch(\Exception $e) {
			throw new InvalidDataException($e->getMessage());
		}
		$url = "weixin://wxpay/bizpayurl?" . $this->ToUrlParams($values);
		return $url;
	}
	
	/**
	 * 
	 * 参数数组转换为url参数
	 * @param array $urlObj
	 */
	private function ToUrlParams($urlObj)
	{
		$buff = "";
		foreach ($urlObj as $k => $v)
		{
			$buff .= $k . "=" . $v . "&";
		}
		
		$buff = trim($buff, "&");
		return $buff;
	}
	
	/**
	 * 
	 * 生成直接支付url，支付url有效期为2小时,模式二
	 * @param UnifiedOrderInput $input
	 */
	public function GetPayUrl($orderData)
	{
		$input      = new WxPayUnifiedOrder();
		$input->SetBody($orderData['goods_name']);
		$input->SetAttach($orderData['pay_way']);
		$input->SetOut_trade_no($orderData['order_id']);
		$input->SetTotal_fee($orderData['goods_price'] * 100);
		$input->SetNotify_url($orderData['notify_url']);
		$input->SetTrade_type("NATIVE");
		$input->SetProduct_id($orderData['uid']. '_' .$orderData['order_id']);
		if($input->GetTrade_type() == "NATIVE"){
			try{
				$config = new WxPayConfig();
				$result = WxPayApi::unifiedOrder($config, $input);
				return $result;
			} catch(\Exception $e) {
				throw new InvalidDataException($e->getMessage());
			}
		}
		return false;
	}
}