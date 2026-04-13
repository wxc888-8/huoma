<?php
include("includes/common.php");
$act = isset($_GET['act']) ? daddslashes($_GET['act']) : null;
header('Content-Type: application/json; charset=UTF-8');
switch ($act) {
    case 'creat1':
        if ($_REQUEST['url'] == "") {
            exit('{"code":-1,"msg":"请填写需要缩短的网址"}');
        }
        $url = $_REQUEST['url'];
        $preg = "#^http(s)?://(www.)?(\w+(\.)?)+#";
        if (!preg_match($preg, $url)) {
            exit('{"code":-1,"msg":"请填写正确的网址"}');
        }
        if ($islogin2 != 1) {
            if ($conf['forcelogin'] == 1) {
                exit('{"code":-1,"msg":"请登录后再生成网址"}');
            }
            $ip = real_ip();
            $today = date('Y-m-d') . ' 00:00:00';
            if ($conf['limit_url'] != '') {
                if ($DB->count("select count(*) from dwz_url where ip='$ip' and addtime>'$today'") >= $conf['limit_url']) {
                    exit('{"code":-1,"msg":"已超过每日生成限制，请登录后再生成"}');
                }
            }
            $url=base64_encode($url);     
            $res = $DB->get_row("select * from dwz_url where ip='$ip' and url='$url' and uid='10000'");
            if ($res) {
                if ($conf['is_https'] == 1) {
                    $data = 'https://' . $conf['domain'] . '/data.php?id=' . $res['id'];
                } else {
                    $data = 'http://' . $conf['domain'] . '/data.php?id=' . $res['id'];
                }
                //$url=base64_encode($url);
                $result = array("code" => 0, "dwz" => $res['dwz'], "data" => $data, "url" => $url);
                exit(json_encode($result));
            }
            $uid = 10000;
        }
        $pattern = isset($_REQUEST['pattern']) ? $_REQUEST['pattern'] : $conf['pattern'];
        $type = isset($_REQUEST['type']) ? $_REQUEST['type'] : $conf['dwz_type'];
        $id = getCode($conf['link_length']);
        if ($conf['is_https'] == 1) {
            $data = 'https://' . $conf['domain'] . '/data.php?id=' . $id;
        } else {
            $data = 'http://' . $conf['domain'] . '/data.php?id=' . $id;
        }
       // $url=base64_encode($url);
        $str = createUrl($type, $id, $uid,$url, $pattern);
        if ($str['code'] == 0) {
          //  $url=base64_encode($url);
            $result = array("code" => 0, "dwz" => $str['msg'], "data" => $data, "url" => $url);
            exit(json_encode($result));
        } else {
            exit('{"code":-1,"msg":"' . $str['msg'] . '"}');
        }
        break;
    case 'creat2':
        if ($_REQUEST['url'] == "") {
            exit('{"code":-1,"msg":"请填写需要还原的网址"}');
        }
        $url = $_REQUEST['url'];
        $preg = "#^http(s)?://(www.)?(\w+(\.)?)+#";
        if (!preg_match($preg, $url)) {
            exit('{"code":-1,"msg":"请填写正确的网址"}');
        }
       // $url=base64_encode($url);
        if ($DB->count("select count(*) from dwz_url where dwz='$url'") > 0) {
            $rs = $DB->query("select * from dwz_url where dwz='$url'");
            $res = $DB->fetch($rs);
            $tzurl = $res['url'];
            $url=$url;
            $result = array("code" => 0, "url" => $url, "tzurl" => $tzurl);
            exit(json_encode($result));
        } else {
            $tzurl = dwz($url, 'long');
            if ($tzurl == '生成失败') {
                $tzurl = '还原失败';
            }
            $url=$url;
            $result = array("code" => 0, "url" => $url, "tzurl" => $tzurl);
            exit(json_encode($result));
        }
    case 'tongji':
        $id = $_REQUEST['id'];
        $day = date("Y-m-d", strtotime("-6 day"));
        $data1 = array();
        $rs = $DB->query("select * from dwz_visitors where urlid='$id' and addtime>='$day'");
        while ($res = $DB->fetch($rs)) {
            $data1[] = array(date('Y-m-d', strtotime($res['addtime'])), $res['ip']);
        }
        $data2 = array_unique($data1, SORT_REGULAR);
        $a = getday();
        for ($i = 0; $i < 7; $i++) {
            $ip = sjtj($data2, $a[$i]);
            $pv = sjtj($data1, $a[$i]);
            $data[] = array($a[$i], $ip, $pv);
        }
        $srs = $DB->query("select system from dwz_visitors where urlid='$id'");
        while ($sres = $DB->fetch($srs)) {
            $data3[] = array($sres['system']);
        }
        $windows = systemtj($data3, 'Windows');
        $iphone = systemtj($data3, 'IPhone');
        $android = systemtj($data3, 'Android');
        if ($windows == 0 && $android == 0 && $iphone == 0) {
            $windows = 2;
            $iphone = 1;
            $android = 1;
        }
        $all = $windows + $android + $iphone;
        $system = array(ceil($windows / $all * 100), ceil($android / $all * 100), ceil($iphone / $all * 100));
        $result = array("week" => $data, "system" => $system);
        exit(json_encode($result));
        break;
}
