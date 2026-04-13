<?php
/* *
 * 支付宝同步通知页面
 */

require_once("./inc.php");

@header('Content-Type: text/html; charset=UTF-8');

require_once(SYSTEM_ROOT."alipay/AlipayTradeService.php");

//计算得出通知验证结果
$alipaySevice = new AlipayTradeService($config); 
//$alipaySevice->writeLog(var_export($_POST,true));
$verify_result = $alipaySevice->check($_GET);

if($verify_result && ($conf['alipay_api']==1||$conf['alipay_api']==3)) {//验证成功
	//商户订单号

	$out_trade_no = daddslashes($_GET['out_trade_no']);

	//支付宝交易号

	$trade_no = daddslashes($_GET['trade_no']);

	//交易金额
	$total_amount = $_GET['total_amount'];

	$srow=$DB->get_row("SELECT * FROM dwz_pay WHERE trade_no='{$out_trade_no}' LIMIT 1");

    if ($srow['status']==0) {
		//付款完成后，支付宝系统发送该交易状态通知
		if($DB->query("UPDATE `dwz_pay` SET `status` ='1' WHERE `trade_no`='{$out_trade_no}'")){
			$DB->query("UPDATE `dwz_pay` SET `endtime` ='$date' WHERE `trade_no`='{$out_trade_no}'");
			processOrder($srow);
		}
		showalert('您所购买的商品已付款成功，感谢购买！');
    }else{
		showalert('您所购买的商品已付款成功，感谢购买！');
	}

}
else {
    //验证失败
	showalert('验证失败！',4,'shop');
}
?>