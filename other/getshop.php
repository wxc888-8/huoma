<?php
require './inc.php';

$trade_no=isset($_GET['trade_no'])?daddslashes($_GET['trade_no']):exit('No trade_no!');

@header('Content-Type: text/html; charset=UTF-8');

$row=$DB->get_row("SELECT * FROM dwz_pay WHERE trade_no='{$trade_no}' limit 1");
$link = '../user/pay.php?buyok=1';

if($row['status']>=1){
	exit('{"code":1,"msg":"付款成功","backurl":"'.$link.'"}');
}else{
	exit('{"code":-1,"msg":"未付款"}');
}
?>