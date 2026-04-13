<?php
if (!defined('IN_CRONLITE')) exit();

$clientip = real_ip();

if (isset($_COOKIE["admin_token"])) {
	$token = authcode(daddslashes($_COOKIE['admin_token']), 'DECODE', SYS_KEY);
	list($user, $sid) = explode("\t", $token);
	$session = md5($conf['admin_user'] . $conf['admin_pwd'] . $password_hash);
	if ($session === $sid) {
		$islogin = 1;
	}
}

if (isset($_COOKIE["user_token"])) {
	$token = authcode(daddslashes($_COOKIE['user_token']), 'DECODE', SYS_KEY);
	if ($token) {
		$tokenarr = explode("\t", $token);
		if (count($tokenarr) >= 2) {
			$id = $tokenarr[0];
			$sid = $tokenarr[1];
			$userrs = $DB->query("select * from dwz_user where id='" . intval($id) . "' limit 1");
			if ($userrow = $DB->fetch($userrs)) {
				$session = md5($userrow['user'] . $userrow['pwd'] . $password_hash);
				if ($session == $sid && $userrow['state'] == 1) {
					$islogin2 = 1;
					$uid = $userrow['id'];
					$vip = $userrow['vip'] > $date ? 1 : 0;
				}
			}
		}
	}
}
