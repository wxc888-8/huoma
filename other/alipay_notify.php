<?php
/* *
 * 支付宝异步通知页面
 */

require_once("./inc.php");
require_once(SYSTEM_ROOT."alipay/AlipayTradeService.php");

//计算得出通知验证结果
$alipaySevice = new AlipayTradeService($config); 
//$alipaySevice->writeLog(var_export($_POST,true));
$verify_result = $alipaySevice->check($_POST);

if($verify_result && ($conf['alipay_api']==1||$conf['alipay_api']==3)) {//验证成功
	//商户订单号

	$out_trade_no = daddslashes($_POST['out_trade_no']);

	//支付宝交易号

	$trade_no = daddslashes($_POST['trade_no']);

	//交易状态
	$trade_status = $_POST['trade_status'];

	//买家支付宝
	$buyer_id = daddslashes($_POST['buyer_id']);

	//交易金额
	$total_amount = $_POST['total_amount'];

	$srow=$DB->get_row("SELECT * FROM dwz_pay WHERE trade_no='{$out_trade_no}' LIMIT 1");

    if ($_POST['trade_status'] == 'TRADE_SUCCESS' && $srow['status']==0) {
		//付款完成后，支付宝系统发送该交易状态通知
		if($DB->query("UPDATE `dwz_pay` SET `status` ='1' WHERE `trade_no`='{$out_trade_no}'")){
			$DB->query("UPDATE `dwz_pay` SET `endtime` ='$date' WHERE `trade_no`='{$out_trade_no}'");
			processOrder($srow);
		}
    }

	echo "success";
}
else {
    //验证失败
    echo "fail";
}
?>