<?php
require 'inc.php';
@header('Content-Type: text/html; charset=UTF-8');
?>
<!DOCTYPE html>
<html>

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>正在为您跳转到支付页面，请稍候...</title>
	<style type="text/css">
		body {
			margin: 0;
			padding: 0;
		}

		p {
			position: absolute;
			left: 50%;
			top: 50%;
			width: 330px;
			height: 30px;
			margin: -35px 0 0 -160px;
			padding: 20px;
			font: bold 14px/30px "宋体", Arial;
			background: #f9fafc url(../assets/load.gif) no-repeat 20px 26px;
			text-indent: 22px;
			border: 1px solid #c5d0dc;
		}

		#waiting {
			font-family: Arial;
		}
	</style>
	<script>
		function open_without_referrer(link) {
			document.body.appendChild(document.createElement('iframe')).src = 'javascript:"<script>top.location.replace(\'' + link + '\')<\/script>"';
		}
	</script>
</head>

<body>
	<?php

	$type = isset($_GET['type']) ? daddslashes($_GET['type']) : exit('No type!');
	$orderid = isset($_GET['orderid']) ? daddslashes($_GET['orderid']) : exit('No orderid!');
	if (!is_numeric($orderid)) exit('订单号不符合要求!');
	$row = $DB->get_row("SELECT * FROM dwz_pay WHERE trade_no='{$orderid}' limit 1");
	if (!$row['trade_no']) exit('该订单号不存在，请返回来源地重新发起请求！');
	if ($row['money'] == '0' || !preg_match('/^[0-9.]+$/', $row['money'])) exit('订单金额不合法');
	if ($row['status'] >= 1) exit('该订单已支付完成，请<a href="/">返回重新生成订单</a>');

	$DB->query("update `dwz_pay` set `type` ='$type' where `trade_no`='{$orderid}'");

	if ($type == 'alipay' && $conf['alipay_api'] == 5 || $type == 'qqpay' && $conf['qqpay_api'] == 5 || $type == 'wxpay' && $conf['wxpay_api'] == 5) {
		echo "<script>window.location.href='./codepay.php?type={$type}&trade_no={$orderid}';</script>";
		exit;
	} elseif ($type == 'alipay' && $conf['alipay_api'] == 2 || $type == 'qqpay' && $conf['qqpay_api'] == 2 || $type == 'wxpay' && $conf['wxpay_api'] == 2) {
		require_once(SYSTEM_ROOT . "epay/epay.config.php");
		require_once(SYSTEM_ROOT . "epay/epay_submit.class.php");
		$parameter = array(
			"pid" => trim($alipay_config['partner']),
			"type" => $type,
			"notify_url"	=> $siteurl . 'epay_notify.php',
			"return_url"	=> $siteurl . 'epay_return.php',
			"out_trade_no"	=> $orderid,
			"name"	=> $row['name'],
			"money"	=> $row['money'],
			"sitename"	=> $conf['web_name']
		);
		//建立请求
		$alipaySubmit = new AlipaySubmit($alipay_config);
		$html_text = $alipaySubmit->buildRequestForm($parameter, "POST", "正在跳转");
		echo $html_text;
	} elseif ($type == 'alipay' && $conf['alipay_api'] == 3) {
		if (checkmobile() == true) {
			echo "<script>window.location.href='./alipaywap.php?trade_no={$orderid}';</script>";
		} else {
			echo "<script>window.location.href='./alipay.php?trade_no={$orderid}';</script>";
		}
	} elseif ($type == 'alipay' && $conf['alipay_api'] == 1) {
		$ordername = $row['name'];

		if (checkmobile() == true) {
			require_once(SYSTEM_ROOT . "alipay/model/builder/AlipayTradeWapPayContentBuilder.php");
			require_once(SYSTEM_ROOT . "alipay/AlipayTradeService.php");

			//构造参数
			$payRequestBuilder = new AlipayTradeWapPayContentBuilder();
			$payRequestBuilder->setSubject($ordername);
			$payRequestBuilder->setTotalAmount($row['money']);
			$payRequestBuilder->setOutTradeNo($orderid);

			$aop = new AlipayTradeService($config);
			echo $aop->wapPay($payRequestBuilder);
		} else {
			require_once(SYSTEM_ROOT . "alipay/model/builder/AlipayTradePagePayContentBuilder.php");
			require_once(SYSTEM_ROOT . "alipay/AlipayTradeService.php");

			//构造参数
			$payRequestBuilder = new AlipayTradePagePayContentBuilder();
			$payRequestBuilder->setSubject($ordername);
			$payRequestBuilder->setTotalAmount($row['money']);
			$payRequestBuilder->setOutTradeNo($orderid);

			$aop = new AlipayTradeService($config);
			echo $aop->pagePay($payRequestBuilder);
		}
	} elseif ($type == 'wxpay' && ($conf['wxpay_api'] == 1 || $conf['wxpay_api'] == 3)) {
		if (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false) {
			echo "<script>window.location.href='./wxjspay.php?trade_no={$orderid}&d=1';</script>";
		} elseif (checkmobile() == true) {
			echo "<script>window.location.href='./wxwappay.php?trade_no={$orderid}';</script>";
		} else {
			echo "<script>window.location.href='./wxpay.php?trade_no={$orderid}';</script>";
		}
	} elseif ($type == 'qqpay' && $conf['qqpay_api'] == 1) {
		if (checkmobile() == true) {
			echo "<script>window.location.href='./qqwappay.php?trade_no={$orderid}';</script>";
		} else {
			echo "<script>window.location.href='./qqpay.php?trade_no={$orderid}';</script>";
		}
	} else {
		exit('该支付方式已关闭');
	}

	?>
	<p>正在为您跳转到支付页面，请稍候...</p>
</body>

</html>