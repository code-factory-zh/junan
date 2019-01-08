<?php

	/**
	 * 本模块配置
	 * @Date 2017/12/12
	 */
	return array(
		'TOKEN_OUT_TIME' => 3600, // TOKEN过期时间
		'VERIFY_TIME_OUT' => 60, // 验证码过期时间
		'PHONE_VERIFY_TIME_OUT' => 60, // 手机验证码过期时间

		// 微信
		'BODY_NAME' 	=> '购买课程订单确认',
		'TRADE_TYPE' 	=> "NATIVE",
		'APPID' 		=> '',
		'MERCHANT_ID' 	=> '',
		'KEY' 			=> '',
		'CALL_BACK_URL' => 'http://wxpay.joinersafe.com/manage/pay/setpay',
	);