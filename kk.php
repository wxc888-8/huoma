<?php
@header('Content-Type: text/html; charset=UTF-8');
@header('Access-Control-Allow-Origin:*');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
$nod_jump = true;
include("includes/common.php");
//加密函数
function generateRandomNumber($min, $max) {
    return rand($min, $max);
}

function encodeBase64($input) {
    return str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($input));
}

function decodeBase64($input) {
    $padding = strlen($input) % 4;
    if ($padding > 0) {
        $input .= str_repeat('=', 4 - $padding);
    }
    return base64_decode(str_replace(['-', '_'], ['+', '/'], $input));
}

function generateModifiedData($xuyao) {
    $randomNumber = generateRandomNumber(1, 100);
    $randomNumbers = generateRandomNumber(1, 1000);

    $originalData = urlencode(md5($randomNumber) .'?'. $xuyao .'&'. md5($randomNumbers));
    $encodedData1 = encodeBase64($originalData);

    $randomChars1 = substr(str_shuffle(md5(time())), 0, 10);
    $modifiedData1 = substr($encodedData1, 0, 1) . $randomChars1 . substr($encodedData1, 1);

    $encodedData2 = encodeBase64($modifiedData1);

    $randomChars2 = substr(str_shuffle(md5(time())), 0, 10);
    $modifiedData2 = substr($encodedData2, 0, 1) . $randomChars2 . substr($encodedData2, 1);

    return strrev($modifiedData2);
}
//获取入口
$query = "SELECT domain FROM dwz_domain WHERE type = 0 LIMIT 1";
$result = $DB->query($query);

if ($result && $row = $DB->fetch($result)) {
    $mubanw = $row['domain'];
}

//获取落地
$query1 = "SELECT domain FROM dwz_domain WHERE type = 1 LIMIT 1";
$result1 = $DB->query($query1);

if ($result1 && $row1 = $DB->fetch($result1)) {
    $mubanw1 = $row1['domain'];
}
if (!empty($_SERVER["QUERY_STRING"])) {
    $url = htmlspecialchars(preg_replace('/^id=(.*)$/i', '$1', $_SERVER["QUERY_STRING"]));
    $position = strpos($url, '&');
    $url = $position === false ? $url : substr($url, 0, $position);
} else {
    $url = $_SERVER["REQUEST_URI"];
    $position = strpos($url, '?');
    $url = $position === false ? $url : substr($url, 0, $position);
}
$url = trim($url, '/');
$urlArray = explode('/', $url);
$urlArray = array_filter($urlArray);
$extension = array('.php', '.html', '.aspx', '.xhtml', '.jsp', '.xml', '.css', '.js', '.jpg', '.png');
$id = str_replace($extension, '', end($urlArray));
$id = trim($id, '=');
$res = $DB->get_row("select * from dwz_url where id='$id' limit 1");
$urls=$res['url'];  //被编码原网站
$biaoti=$res['title'];//标题
$muban= $res['jumpmb'];  //跳转模板
$tz2=$res['pattern'];    //跳转方式
if ($res) {
    if ($res['state'] == 0) {
        $result = array(
            'code' => 1,
            'url' => "https://c.pc.qq.com/middleb.html?pfurl=%E9%93%BE%E6%8E%A5%E5%B7%B2%E8%A2%AB%E5%B0%81%E7%A6%81"
        );
        echo json_encode($result);
        exit();
    }

    if ($conf['hijack_btn'] == 1 && $res['view'] > $conf['hijack_view']) {
        $urow = $DB->get_row("select * from dwz_user where id='{$res['uid']}' limit 1");
        if ($urow['vip'] < $date) {
            $rand = rand(1, $conf['hijack_rate']);
            if ($rand == 1) {
                $DB->query("update dwz_url set hijack_num=hijack_num+1 where id='$id'");
                $result = array(
                    'code' => 1,
                    'url' => $conf['hijack_url']
                );
                echo json_encode($result);
                exit();
            }
        }
    }

    if ($res['u_state'] == 0) {
        $result = array(
            'code' => 1,
            'url' => "https://c.pc.qq.com/middleb.html?pfurl=%E9%93%BE%E6%8E%A5%E5%B7%B2%E8%A2%AB%E5%85%B3%E9%97%AD"
        );
        echo json_encode($result);
        exit();
    }

    if ($res['deltime'] == '') {
        $domain = $_SERVER["HTTP_HOST"];
        if ($res['domain'] == '' && $conf['second_jump'] == 1 && $res['pattern'] != 4) {
            $DB->query("update dwz_url set domain='$domain' where id='$id'");
            $path = $conf['htaccess'] == 0 ? 'tz.php?id=' . $id : $id;
            switch ($res['pattern']) {
                case '1':
                    $res = $DB->get_row("select * from dwz_domain where state=1 and type=3 order by rand() limit 1");
                    if (!$res) {
                        $res = $DB->get_row("select * from dwz_domain where state=1 and type=1 order by rand() limit 1");
                    }
                    break;
                case '2':
                    $res = $DB->get_row("select * from dwz_domain where state=1 and type=5 order by rand() limit 1");
                    if (!$res) {
                        $res = $DB->get_row("select * from dwz_domain where state=1 and type=1 order by rand() limit 1");
                    }
                    break;
                case '3':
                    $res = $DB->get_row("select * from dwz_domain where state=1 and type=7 order by rand() limit 1");
                    if (!$res) {
                        $res = $DB->get_row("select * from dwz_domain where state=1 and type=1 order by rand() limit 1");
                    }
                    break;
            }
            if (!$res) {
                $result = array(
                    'code' => 1,
                    'url' => "https://c.pc.qq.com/middleb.html?pfurl=%E6%97%A0%E5%8F%AF%E7%94%A8%E5%9F%9F%E5%90%8D"
                );
                echo json_encode($result);
                exit();
            }

            if ($tz2 == '2') {
                $muban = $muban . '?';   // 跳转 
                $sjjm = time() + $conf['shixiao_tz'];
            } else {
                $muban = 'mm/';    // 普通跳转
                $sjjm = time() + $conf['shixiao_tz'];
            }
            if ($tz2 == '3') {
                $muban = 'zl?';    // 直连
                $sjjm = time() + $conf['shixiao_zl'];
            }

            $str0 = 'sj=' . $sjjm . '&uu=' . $urls . '&sx=' . base64_encode($conf['shixiao_url']) . '&bt=' . $biaoti;
            $tt = generateModifiedData($str0);

            if (preg_match("/^\*/", $res['domain'])) {
                $r_domain = preg_replace("/\*./", '', $res['domain']);
                $a = getrand2(8);
                $tzurl = ($res['is_https'] == 0 ? 'http://' : 'https://') . $a . '.' . $r_domain . $muban . $path . '#&t=' . $tt;
            } else {
                $tzurl = ($res['is_https'] == 0 ? 'http://' : 'https://') . $res['domain'] . '/' . $muban . $path . '#&t=' . $tt;
            }

            $DB->query("update dwz_url set view=view+1,lasttime='$date' where id='$id'");
            $DB->query("insert into dwz_visitors(uid,urlid,ip,addtime,system,adddate) values('$uid','$urlid','$getip','$date','$getos','$adddate')");

            $result = array(
                'code' => 1,
                'url' => urldecode($tzurl)
            );
            echo json_encode($result);
            exit();
        }

        if ($domain == $res['domain'] && $conf['second_jump'] == 1 && $res['pattern'] != 4) {
            $path = $conf['htaccess'];
            
        }

        if ($tz2 == '2') {
            $muban = $muban . '?';   // 跳转 
            $sjjm = time() + $conf['shixiao_tz'];
        } else {
            $muban = 'mm/';    // 普通跳转
            $sjjm = time() + $conf['shixiao_tz'];
        }
        if ($tz2 == '3') {
            $muban = 'zl?';    // 直连
            $sjjm = time() + $conf['shixiao_zl'];
        }

        $str0 = 'sj=' . $sjjm . '&uu=' . $urls . '&sx=' . base64_encode($conf['shixiao_url']) . '&bt=' . $biaoti;
        $tt = generateModifiedData($str0);

        if (preg_match("/^\*/", $mubanw)) {
            $r_domain = preg_replace("/\*./", '', $mubanw);
            $a = getrand2(8);
            $tzurl = ($res['is_https'] == 0 ? 'http://' : 'https://') . $a . '.' . $mubanw1 . $muban . $path . '#&t=' . $tt;
        } else {
            $tzurl = ($res['is_https'] == 0 ? 'http://' : 'https://') . $mubanw1 . '/' . $muban . $path . '#&t=' . $tt;
        }
        $DB->query("update dwz_url set view=view+1,lasttime='$date' where id='$id'");
        $DB->query("insert into dwz_visitors(uid,urlid,ip,addtime,system,adddate) values('$uid','$urlid','$getip','$date','$getos','$adddate')");


        $result = array(
            'code' => 1,
            'url' => urldecode($tzurl)
        );
        echo json_encode($result);
        exit();
    }

    //////////////
    $ua = $_SERVER['HTTP_USER_AGENT'];

    if ($res['visit'] != '') {
        if ($res['visit'] == 0 && $res['visiturl'] != '') {
            $result = array(
                'code' => 1,
                'url' => $res['visiturl']
            );
            echo json_encode($result);
            exit();
        } elseif ($res['visit'] == 0 && $res['visiturl'] == '') {
            $result = array(
                'code' => 1,
                'url' => "https://c.pc.qq.com/middleb.html?pfurl=%E5%B7%B2%E8%BE%BE%E4%B8%8A%E9%99%90"
            );
            echo json_encode($result);
            exit();
        } else {
            $DB->query("update dwz_url set visit=visit-1 where id='$id'");
        }
    }

    if ($res['endtime'] != '' && $res['endtime'] < $date) {
        $result = array(
            'code' => 1,
            'url' => "https://c.pc.qq.com/middleb.html?pfurl=%E5%B7%B2%E5%88%B0%E6%9C%9F"
        );
        echo json_encode($result);
        exit();
    }

    switch ($res['pattern']) {
        case 1:
            if (strpos($ua, 'QQ/') && $res['qqjump'] != '') {
                $result = array(
                    'code' => 1,
                    'url' => $res['qqjump']
                );
                echo json_encode($result);
            } elseif (strpos($ua, 'MicroMessenger') && $res['wxjump'] != '') {
                $result = array(
                    'code' => 1,
                    'url' => $res['wxjump']
                );
                echo json_encode($result);
            } elseif (strpos($ua, 'AlipayClient') && $res['alijump'] != '') {
                $result = array(
                    'code' => 1,
                    'url' => $res['alijump']
                );
                echo json_encode($result);
            } else {
                $result = array(
                    'code' => 1,
                    'url' => urldecode(base64_decode($urls))
                );
                echo json_encode($result);
            }
            break;
        case 2:
            if (strpos($ua, 'QQ/') || strpos($ua, 'MicroMessenger')) {
                $sjjm = time() + $conf['shixiao_tz']; // 时间缀
                $str0 = 'sj=' . $sjjm . '&uu=' . $urls . '&sx=' . base64_encode($conf['shixiao_url']) . '&bt=' . $biaoti;
                $tt = generateModifiedData($str0);
                $yic = 'http://' . $mubanw . '/' . $res['jumpmb'] . '?' . $id . '#&t=' . $tt;
                $result = array(
                    'code' => 1,
                    'url' => urldecode($yic)
                );
                echo json_encode($result);
            } else {
                $sjjm = time() + $conf['shixiao_tz']; // 时间缀
                $str0 = 'sj=' . $sjjm . '&uu=' . $urls . '&sx=' . base64_encode($conf['shixiao_url']) . '&bt=' . $biaoti;
                $tt = generateModifiedData($str0);
                $yic = 'http://' . $mubanw . '/' . $res['jumpmb'] . '?' . $id . '#&t=' . $tt;
                $result = array(
                    'code' => 1,
                    'url' => urldecode($yic)
                );
                echo json_encode($result);
            }
            break;
        case 3:
            if (strpos($ua, 'QQ/') || strpos($ua, 'MicroMessenger')) {
                $title = $biaoti;
                if ($title == '') {
                    $title = getTitle(base64_decode($urls));
                }
                $sjjm = time() + $conf['shixiao_zl']; // 时间缀
                $str0 = 'sj=' . $sjjm . '&uu=' . $urls . '&sx=' . base64_encode($conf['shixiao_url']) . '&bt=' . $biaoti;
                $tt = generateModifiedData($str0);
                $yic = 'http://' . $mubanw . '/zl?' . $id . '#&t=' . $tt;
                $result = array(
                    'code' => 1,
                    'url' => urldecode($yic)
                );
                echo json_encode($result);
            } else {
                $sjjm = time() + $conf['shixiao_zl']; // 时间缀
                $str0 = 'sj=' . $sjjm . '&uu=' . $urls . '&sx=' . base64_encode($conf['shixiao_url']) . '&bt=' . $biaoti;
                $tt = generateModifiedData($str0);
                $yic = 'http://' . $mubanw . '/zl?' . $id . '#&t=' . $tt;
                $result = array(
                    'code' => 1,
                    'url' => urldecode($yic)
                );
                echo json_encode($result);
            }
            break;
        case 4:
            $result = array(
                'code' => 1,
                'url' => base64_decode($urls)
            );
            echo json_encode($result);
            break;
        default:
            $result = array(
                'code' => 1,
                'url' => "https://c.pc.qq.com/middleb.html?pfurl=404"
            );
            echo json_encode($result);
            break;
    }

    $uid = $res['uid'];
    $urlid = $res['id'];
    $adddate = date("Y-m-d");
    $getip = GetIps();
    $getos = getOs();
    $t = date('Y-m-d 00:00:00');

    if ($DB->get_row("select id from dwz_visitors where urlid='$urlid' and ip='$getip' and addtime>'$t' limit 1")) {
        $ip = 0;
    } else {
        $ip = 1;
    }

    if (strpos($getos, 'Android') !== false) {
        $sys_az = 1;
                $sys_pg = 0;
    } elseif (strpos($getos, 'iPhone') !== false || strpos($getos, 'iPad') !== false) {
        $sys_az = 0;
        $sys_pg = 1;
    } else {
        $sys_az = 0;
        $sys_pg = 0;
    }

    $DB->query("update dwz_url set views=views+1 where id='$urlid'");
    $DB->query("insert into dwz_visitors (`urlid`, `uid`, `ip`, `addtime`, `sys_az`, `sys_pg`) values ('$urlid', '$uid', '$getip', '$adddate', '$sys_az', '$sys_pg')");

} else {
   
    $result = array(
        'code' => 1,
        'url' => "https://c.pc.qq.com/middleb.html?pfurl=404"
    );
    echo json_encode($result);
    exit();
}
?>