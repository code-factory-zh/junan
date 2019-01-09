<?php

/**
 * 子帐户模块
 * @Auther QiuXiangCheng
 * @Date 2018/12/08
 */
namespace Manage\Controller;
use Common\Controller\BaseController;
class PayController extends BaseController {

	public function _initialize() {

		parent::_initialize();
	}


	public function setpay() {

		vendor("Wxpay.lib.WxPayData");
		$notify = new \WxPayNotifyReply;
		$notify -> SetReturn_code("SUCCESS");
		$notify -> SetReturn_msg("OK");
		$xml = $notify -> ToXml();
		// echo $xml;


		$notifiedData = file_get_contents('php://input');
		$xmlObj = simplexml_load_string($notifiedData, 'SimpleXMLElement', LIBXML_NOCDATA);

		M('tmp') -> add(['str' => json_encode($xmlObj)]);
        $xmlObj = json_decode(json_encode($xmlObj), true);

		M('tmp') -> add(['str' => $notifiedData]);
	}


	/**
	 * 显示二维码 返回PNG资源
	 * @Author   邱湘城
	 * @DateTime 2019-01-08T23:06:59+0800
	 */
	public function show_wxpay_pic() {

		vendor('Wxpay.example.phpqrcode.phpqrcode');
		$url = urldecode($_GET["data"]);
		if(substr($url, 0, 6) == "weixin") {
			\QRcode::png($url);
		} else {
			header('HTTP/1.1 404 Not Found');
		}
		return ;
	}


	/**
	 * 根据当前订单生成二维码
	 * @Author   邱湘城
	 * @DateTime 2019-01-08T23:57:11+0800
	 */
	public function getCodeUrl() {

		$session_key = 'company_id:' . $this -> userinfo['id'];
		$list = session($session_key);
		if (is_null($list) || !count($list)) {
			$this -> e('没有订单信息');
		}

		$totalPrice = 0.00;
		foreach ($list as $items) {
			$totalPrice += intval($items['price']);
		}
		$totalPrice = bcmul($totalPrice, 100, 2);

$totalPrice = 1;

		vendor("Wxpay.lib.WxPayApi");
		vendor("Wxpay.example.WxPayNativePay");
		vendor("Wxpay.example.log");

		$tmpInfo = $this -> fetch_order_num();
		$notify = new \NativePay();
		$input = new \WxPayUnifiedOrder();
		$input -> SetBody(C('BODY_NAME'));
		$input -> SetAttach("BUY");
		$input -> SetOut_trade_no($tmpInfo['orderNum']);
		$input -> SetTotal_fee($totalPrice);
		$input -> SetTime_start(date("YmdHis"));
		$input -> SetTime_expire(date("YmdHis", time() + 300));

		// $input -> SetGoods_tag("test");
		$input -> SetNotify_url(C('CALL_BACK_URL'));
		$input -> SetTrade_type(C('TRADE_TYPE'));
		$input -> SetProduct_id("123456789");
		$result = $notify -> GetPayUrl($input);
		$url = '/manage/pay/show_wxpay_pic?data=' . urlencode($result["code_url"]);
		$this -> rel(['url' => $url]) -> e(0, 'Success');
	}
}