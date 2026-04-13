<?php
if ($nosecu == true) return;
$tx_ua = file_get_contents(SYSTEM_ROOT . 'libs/txspdier.ua');
foreach (explode(PHP_EOL, $tx_ua) as $UaRes) {
	if (strtolower($_SERVER['HTTP_USER_AGENT']) == strtolower($UaRes)) {
		TxSpdier('欢迎使用！');
	}
}

$tx_ipdb = file_get_contents(SYSTEM_ROOT . 'libs/txspdier.db');

$RemoteIp = bindec(decbin(ip2long(x_real_ip())));

foreach (explode(PHP_EOL, $tx_ipdb) as $IpRes) {
	if ($RemoteIp == bindec(decbin(ip2long($IpRes))) && !is_array($IpRes))	TxSpdier('欢迎使用！');
	$iprange	=	explode('-', $IpRes);
	if (is_array($iprange)) {
		if ($RemoteIp >= bindec(decbin(ip2long($iprange[0]))) && $RemoteIp <= bindec(decbin(ip2long($iprange[1]))))	TxSpdier('欢迎使用！');
	}
}

function TxSpdier($val = 0)
{
	include ROOT . 'template/page/txprotect.php';
	exit();
}
