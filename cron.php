<?php
if (preg_match('/Baiduspider/', $_SERVER['HTTP_USER_AGENT'])) exit;
include("./includes/common.php");
if (function_exists("set_time_limit")) {
    @set_time_limit(0);
}
if (function_exists("ignore_user_abort")) {
    @ignore_user_abort(true);
}

@header('Content-Type: text/html; charset=UTF-8');

$do = isset($_GET['do']) ? daddslashes($_GET['do']) : null;
if (empty($conf['cronkey'])) exit("请先设置好监控密钥");
if ($conf['cronkey'] != $_GET['key']) exit("监控密钥不正确");

if ($do == 'userInfo') {
    $a1 = 0;
    $a2 = 0;
    $rs = $DB->query("select * from dwz_user");
    while ($res = $DB->fetch($rs)) {
        $name = qq_img($res['qq'])['name'];
        if ($name == '') {
            $name = '空白昵称';
        }
        $img = qq_img($res['qq'])['imgurl'];
        if ($res['name'] == '') {
            $DB->query("update dwz_user set name='$name' where id='{$res['id']}'");
            $a1++;
        }
        if ($res['img'] != $img) {
            $DB->query("update dwz_user set img='$img' where id='{$res['id']}'");
            $a2++;
        }
    }
    exit('执行完毕,共更新' . $a1 . '个昵称和' . $a2 . '个头像');
} elseif ($do == 'checkdomain') {
    if ($conf['qqdomaincheck'] == 1) {
        $nump = $DB->count("select count(*) from dwz_domain where qqsafe=1 order by addtime");
        $size = 50;
        $xz = ceil($nump / $size);
        if ($xz > 1) {
            for ($i = 0; $i <= $xz; $i++) {
                $start = $i * $size;
                curl_run($siteurl . 'cron.php?do=checkDomain_qq&key=' . $_GET['key'] . '&multi=on&start=' . $start . '&num=' . $size);
            }
            echo "QQ成功打开 {$xz} 个线程!<br>";
        } else {
            $rs = $DB->query("select id,domain from dwz_domain where qqsafe=1");
        }
        if ($rs) {
            while ($res = $DB->fetch($rs)) {
                $url = $siteurl . 'cron.php?id=' . $res['id'] . '&domain=' . $res['domain'] . '&type=qqsafe&do=checkdomains&key=' . $_GET['key'];
                asynch($url);
            }
            echo 'QQ域名检测执行成功<br>';
        }
    }
    if ($conf['wxdomaincheck'] == 1) {
        $nump = $DB->count("select count(*) from dwz_domain where wxsafe=1 order by addtime");
        $size = 50;
        $xz = ceil($nump / $size);
        if ($xz > 1) {
            for ($i = 0; $i <= $xz; $i++) {
                $start = $i * $size;
                curl_run($siteurl . 'cron.php?do=checkDomain_wx&key=' . $_GET['key'] . '&multi=on&start=' . $start . '&num=' . $size);
            }
            echo "微信成功打开 {$xz} 个线程!<br>";
        } else {
            $rs = $DB->query("select id,domain from dwz_domain where wxsafe=1");
        }
        if ($rs) {
            while ($res = $DB->fetch($rs)) {
                $url = $siteurl . 'cron.php?id=' . $res['id'] . '&domain=' . $res['domain'] . '&type=wxafe&do=checkdomains&key=' . $_GET['key'];
                asynch($url);
            }
            echo '微信域名检测执行成功<br>';
        }
    }
} elseif ($do == 'checkDomain_qq') {
    if (isset($_GET['multi'])) {
        $start = intval($_GET['start']);
        $num = intval($_GET['num']);
        $rs = $DB->query("select id,domain from dwz_domain where qqsafe=1 order by addtime limit {$start},{$num}");
    } else {
        $nump = $DB->count("select count(*) from dwz_domain where qqsafe=1 order by addtime");
        $size = 50;
        $xz = ceil($nump / $size);
        if ($xz > 1) {
            for ($i = 0; $i <= $xz; $i++) {
                $start = $i * $size;
                curl_run($siteurl . 'cron.php?do=checkDomain_qq&key=' . $_GET['key'] . '&multi=on&start=' . $start . '&num=' . $size);
            }
            exit("成功打开 {$xz} 个线程!");
        } else {
            $rs = $DB->query("select id,domain from dwz_domain where qqsafe=1");
        }
    }
    while ($res = $DB->fetch($rs)) {
        $url = $siteurl . 'cron.php?id=' . $res['id'] . '&domain=' . $res['domain'] . '&type=qqsafe&do=checkdomains&key=' . $_GET['key'];
        asynch($url);
    }
    echo 'QQ域名检测执行成功<br>';
} elseif ($do == 'checkDomain_wx') {
    if (isset($_GET['multi'])) {
        $start = intval($_GET['start']);
        $num = intval($_GET['num']);
        $rs = $DB->query("select id,domain from dwz_domain where wxsafe=1 order by addtime limit {$start},{$num}");
    } else {
        $nump = $DB->count("select count(*) from dwz_domain where wxsafe=1 order by addtime");
        $size = 50;
        $xz = ceil($nump / $size);
        if ($xz > 1) {
            for ($i = 0; $i <= $xz; $i++) {
                $start = $i * $size;
                curl_run($siteurl . 'cron.php?do=checkDomain_wx&key=' . $_GET['key'] . '&multi=on&start=' . $start . '&num=' . $size);
            }
            exit("成功打开 {$xz} 个线程!");
        } else {
            $rs = $DB->query("select id,domain from dwz_domain where wxsafe=1");
        }
    }
    while ($res = $DB->fetch($rs)) {
        $url = $siteurl . 'cron.php?id=' . $res['id'] . '&domain=' . $res['domain'] . '&type=wxsafe&do=checkdomains&key=' . $_GET['key'];
        asynch($url);
    }
    echo '微信域名检测执行成功<br>';
} elseif ($do == 'checkdomains') {
    ignore_user_abort(true);
    $id = isset($_GET['id']) ? $_GET['id'] : '';
    $domain = isset($_GET['domain']) ? $_GET['domain'] : '';
    $type = isset($_GET['type']) ? $_GET['type'] : '';
    $domains = preg_replace("/\*/", getrand2(5), $domain);
    $ret = checkDomain($domains, $type);
    if ($ret['code'] == 201) {
        if ($type == 'qqsafe') {
            if ($conf['outqqdomain'] == 1) {
                $DB->query("update dwz_domain set state=0,qqsafe=0 where id='$id'");
            } else {
                $DB->query("update dwz_domain set qqsafe=0 where id='$id'");
            }
            $info = $domain . '已被QQ拦截<br>';
            $to = $conf['mail_recv'] ? $conf['mail_recv'] : $conf['mail_name'];
            if ($conf['domainsetemail'] == 1) {
                send_mail($to, '域名监控', $info);
            }
        } elseif ($type == 'wxsafe') {
            if ($conf['outwxdomain'] == 1) {
                $DB->query("update dwz_domain set state=0,wxsafe=0 where id='$id'");
            } else {
                $DB->query("update dwz_domain set wxsafe=0 where id='$id'");
            }
            $info = $domain . '已被微信拦截<br>';
            $to = $conf['mail_recv'] ? $conf['mail_recv'] : $conf['mail_name'];
            if ($conf['domainsetemail'] == 1) {
                send_mail($to, '域名监控', $info);
            }
        }
        echo $info;
    }
} elseif ($do == 'cleanData') {
    $DB->query("delete from dwz_visitors where addtime<'" . date("Y-m-d H:i:s", strtotime("-7 days")) . "'");
    $num = $DB->affected();
    $DB->query("optimize table dwz_visitors");
    exit('已清理' . $num . '条数据');
} elseif ($do == 'daily') {
    $DB->query("delete from dwz_pay where addtime<'" . date("Y-m-d H:i:s", strtotime("-12 hours")) . "' and status=0");
    $sq1 = $DB->affected();
    $DB->query("OPTIMIZE TABLE `dwz_pay`");
    exit('日常维护任务已成功执行，本次共清理' . $sq1 . '条数据<br/>');
} elseif ($do == 'urlcheck1') {
    // 获取检测所需积分数
    $check_points = intval($conf['check_points']) > 0 ? intval($conf['check_points']) : 1;
    
    $urs = $DB->query("select * from dwz_user where points>=$check_points");
    while ($ures = $DB->fetch($urs)) {
        $available_checks = floor($ures['points'] / $check_points);
        $crs = $DB->query("select * from dwz_check where switch=1 and status=1 and pl=0 and uid='{$ures['id']}'");
        $info = '';
        while ($cres = $DB->fetch($crs)) {
            if ($available_checks > 0) {
                $url = $siteurl . 'cron.php?id=' . $cres['id'] . '&uid=' . $ures['id'] . '&mail=' . $ures['mail'] . '&url=' . $cres['url'] . '&type=' . $cres['type'] . '&do=urlchecks&key=' . $_GET['key'];
                asynch($url);
                $available_checks--;
            }
        }
    }
    echo '检测完毕';
} elseif ($do == 'urlcheck2') {
    // 获取检测所需积分数
    $check_points = intval($conf['check_points']) > 0 ? intval($conf['check_points']) : 1;
    
    $urs = $DB->query("select * from dwz_user where points>=$check_points");
    while ($ures = $DB->fetch($urs)) {
        $available_checks = floor($ures['points'] / $check_points);
        $crs = $DB->query("select * from dwz_check where switch=1 and status=1 and pl=1 and uid='{$ures['id']}'");
        $info = '';
        while ($cres = $DB->fetch($crs)) {
            if ($available_checks > 0) {
                $url = $siteurl . 'cron.php?id=' . $cres['id'] . '&uid=' . $ures['id'] . '&mail=' . $ures['mail'] . '&url=' . $cres['url'] . '&type=' . $cres['type'] . '&do=urlchecks&key=' . $_GET['key'];
                asynch($url);
                $available_checks--;
            }
        }
    }
    echo '检测完毕';
} elseif ($do == 'urlcheck3') {
    // 获取检测所需积分数
    $check_points = intval($conf['check_points']) > 0 ? intval($conf['check_points']) : 1;
    
    $urs = $DB->query("select * from dwz_user where points>=$check_points");
    while ($ures = $DB->fetch($urs)) {
        $available_checks = floor($ures['points'] / $check_points);
        $crs = $DB->query("select * from dwz_check where switch=1 and status=1 and pl=2 and uid='{$ures['id']}'");
        $info = '';
        while ($cres = $DB->fetch($crs)) {
            if ($available_checks > 0) {
                $url = $siteurl . 'cron.php?id=' . $cres['id'] . '&uid=' . $ures['id'] . '&mail=' . $ures['mail'] . '&url=' . $cres['url'] . '&type=' . $cres['type'] . '&do=urlchecks&key=' . $_GET['key'];
                asynch($url);
                $available_checks--;
            }
        }
    }
    echo '检测完毕';
} elseif ($do == 'urlcheck4') {
    // 获取检测所需积分数
    $check_points = intval($conf['check_points']) > 0 ? intval($conf['check_points']) : 1;
    
    $urs = $DB->query("select * from dwz_user where points>=$check_points");
    while ($ures = $DB->fetch($urs)) {
        $available_checks = floor($ures['points'] / $check_points);
        $crs = $DB->query("select * from dwz_check where switch=1 and status=1 and pl=3 and uid='{$ures['id']}'");
        $info = '';
        while ($cres = $DB->fetch($crs)) {
            if ($available_checks > 0) {
                $url = $siteurl . 'cron.php?id=' . $cres['id'] . '&uid=' . $ures['id'] . '&mail=' . $ures['mail'] . '&url=' . $cres['url'] . '&type=' . $cres['type'] . '&do=urlchecks&key=' . $_GET['key'];
                asynch($url);
                $available_checks--;
            }
        }
    }
    echo '检测完毕';
} elseif ($do == 'urlchecks') {
    ignore_user_abort(true);
    $id = isset($_GET['id']) ? $_GET['id'] : '';
    $uid = isset($_GET['uid']) ? $_GET['uid'] : '';
    $url = isset($_GET['url']) ? $_GET['url'] : '';
    $type = isset($_GET['type']) ? $_GET['type'] : '';
    $mail = isset($_GET['mail']) ? $_GET['mail'] : '';
    $ret = $type == 0 ? checkDomain($url, 'wxsafe') : checkDomain($url, 'qqsafe');
    if ($ret['code'] == 201) {
        $DB->query("update dwz_check set status=0,num=num+1,lasttime='$date' where id='$id'");
        $type = $type == 0 ? '微信' : 'QQ';
        $info = $url . '已被' . $type . '拦截<br>';
        send_mail($mail, '网址监控', $info);
    } else {
        $DB->query("update dwz_check set num=num+1,lasttime='$date' where id='$id'");
    }
    
    // 获取检测所需积分数
    $check_points = intval($conf['check_points']) > 0 ? intval($conf['check_points']) : 1;
    
    // 扣除用户积分
    $DB->query("update dwz_user set points=points-$check_points where id='$uid'");
    
    // 记录积分消费日志
    $DB->query("INSERT INTO dwz_points_log(uid, points, type, description, addtime) VALUES('$uid', -$check_points, 'url_check', '网址监控检测', '$date')");
}