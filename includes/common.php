<?php

if (defined('IN_CRONLITE')) {
    return;
}
define('CACHE_FILE', 0);
define('IN_CRONLITE', true);
define('SYSTEM_ROOT', dirname(__FILE__) . '/');
define('ROOT', dirname(SYSTEM_ROOT) . '/');
define('TEMPLATE_ROOT', ROOT . '/template/home/');
define('TZ_TEMPLATE_ROOT', ROOT . '/template/jump/');
date_default_timezone_set('PRC');
$date = date('Y-m-d H:i:s');

// 设置默认值，避免未定义变量警告
$is_defend = false;

include_once SYSTEM_ROOT . 'base.php';
session_start();
@header('Cache-Control: no-store, no-cache, must-revalidate');
@header('Pragma: no-cache');
if ($is_defend == true || CC_Defender == 3) {
    include_once SYSTEM_ROOT . 'libs/txprotect.php';
    if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest') {
    }
    if (CC_Defender == 1 && getspider() == false || CC_Defender == 2 || CC_Defender == 3) {
        cc_defender();
    }
}
if (is_file(SYSTEM_ROOT . '360safe/360webscan.php')) {
    require_once SYSTEM_ROOT . '360safe/360webscan.php';
}
$scriptpath = str_replace('\\', '/', $_SERVER['SCRIPT_NAME']);
$sitepath = substr($scriptpath, 0, strrpos($scriptpath, '/'));
$siteurl = ($_SERVER['SERVER_PORT'] == '443' ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . $sitepath . '/';
require ROOT . 'config.php';
require SYSTEM_ROOT . 'version.php';
if (!defined('SQLITE') && (!$dbconfig['user'] || !$dbconfig['pwd'] || !$dbconfig['dbname'])) {
    header('Content-type:text/html;charset=utf-8');
    echo '你还没安装！<a href="/install/">点此安装</a>';
    exit(0);
}
include_once SYSTEM_ROOT . 'db.class.php';
$DB = new DB($dbconfig['host'], $dbconfig['user'], $dbconfig['pwd'], $dbconfig['dbname'], $dbconfig['port']);
if ($DB->query('select * from dwz_config where 1') == false) {
    header('Content-type:text/html;charset=utf-8');
    echo '你还没安装！<a href="/install/">点此安装</a>';
    exit(0);
}

include SYSTEM_ROOT . 'libs/cache.class.php';
$CACHE = new CACHE();
$conf = unserialize($CACHE->read());
if (!$conf['version']) {
    $conf = $CACHE->update();
}
define('SYS_KEY', $conf['syskey']);
if ($conf['version'] < DB_VERSION) {
    if (!$install) {
        header('Content-type:text/html;charset=utf-8');
        echo '请先完成网站升级！<a href="/install/update.php"><font color=red>点此升级</font></a>';
        exit(0);
    }
}

$password_hash = '!@#%!s!0';
include_once SYSTEM_ROOT . 'authcode.php';
define('authcode', $authcode);

include_once SYSTEM_ROOT . 'libs/template.class.php';
include_once SYSTEM_ROOT . 'function.php';
include_once SYSTEM_ROOT . 'core.func.php';
include_once SYSTEM_ROOT . 'member.php';
if (!file_exists(ROOT . 'install/install.lock') && file_exists(ROOT . 'install/index.php')) {
    sysmsg('<h2>检测到无 install.lock 文件</h2><ul><li><font size="4">如果您尚未安装本程序，请<a href="./install/">前往安装</a></font></li><li><font size="4">如果您已经安装本程序，请手动放置一个空的 install.lock 文件到 /install 文件夹下，<b>为了您站点安全，在您完成它之前我们不会工作。</b></font></li></ul><br/><h4>为什么必须建立 install.lock 文件？</h4>它是短网址的保护文件，如果检测不到它，就会认为站点还没安装，此时任何人都可以安装/重装短网址。<br/><br/>', true);
    exit(0);
}

if (!defined('authcode')) {
    exit(0);
}


if (isset($_GET['djump']) && $_SESSION['djump'] == '') {
    setcookie("djump", 1, time() + 600);
}

if ($conf['d_jump'] == 1 && $nod_jump == false && !isset($_SESSION['djump'])) {
    if ($_SERVER["HTTP_HOST"] != $conf['domain']) {
       // include ROOT . 'template/page/404.php';
       header("Location:  https://c.pc.qq.com/middleb.html?pfurl=404");
        exit();
    }
}

function x_real_ip()
{
    $ip = $_SERVER['REMOTE_ADDR'];
    if (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && preg_match_all('#\\d{1,3}\\.\\d{1,3}\\.\\d{1,3}\\.\\d{1,3}#s', $_SERVER['HTTP_X_FORWARDED_FOR'], $matches)) {
        foreach ($matches[0] as $xip) {
            if (!preg_match('#^(10|172\\.16|192\\.168)\\.#', $xip)) {
                $ip = $xip;
                break;
            }
        }
    } else {
        if (isset($_SERVER['HTTP_CLIENT_IP']) && preg_match('/^([0-9]{1,3}\\.){3}[0-9]{1,3}$/', $_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } else {
            if (isset($_SERVER['HTTP_CF_CONNECTING_IP']) && preg_match('/^([0-9]{1,3}\\.){3}[0-9]{1,3}$/', $_SERVER['HTTP_CF_CONNECTING_IP'])) {
                $ip = $_SERVER['HTTP_CF_CONNECTING_IP'];
            } else {
                if (isset($_SERVER['HTTP_X_REAL_IP']) && preg_match('/^([0-9]{1,3}\\.){3}[0-9]{1,3}$/', $_SERVER['HTTP_X_REAL_IP'])) {
                    $ip = $_SERVER['HTTP_X_REAL_IP'];
                }
            }
        }
    }
    return $ip;
}
function getspider($useragent = '')
{
    global $conf;
    $value = 0;
    if (strpos($_SERVER['SCRIPT_NAME'], 'long.php') !== false) $value++;
    if (strpos($_SERVER['SCRIPT_NAME'], 'cron.php') !== false) $value++;
    if (strpos($_SERVER['SCRIPT_NAME'], 'dwz.php') !== false) $value++;
    if (strpos($_SERVER['SCRIPT_NAME'], 'api.php') !== false) $value++;
    if (strpos($_SERVER['SCRIPT_NAME'], 'qr.php') !== false) $value++;
    if (strpos($_SERVER['SCRIPT_NAME'], 't.php') !== false) $value++;
    if ($value > 0)   return true;
    if (!$useragent) {
        $useragent = $_SERVER['HTTP_USER_AGENT'];
    }
    $useragent = strtolower($useragent);
    if (strpos($useragent, 'baiduspider') !== false) {
        return 'baiduspider';
    }
    if (strpos($useragent, 'googlebot') !== false) {
        return 'googlebot';
    }
    if (strpos($useragent, '360spider') !== false) {
        return '360spider';
    }
    if (strpos($useragent, 'haosouspider') !== false) {
        return 'haosouspider';
    }
    if (strpos($useragent, 'soso') !== false) {
        return 'soso';
    }
    if (strpos($useragent, 'bing') !== false) {
        return 'bing';
    }
    if (strpos($useragent, 'yahoo') !== false) {
        return 'yahoo';
    }
    if (strpos($useragent, 'sohu-search') !== false) {
        return 'Sohubot';
    }
    if (strpos($useragent, 'sogou') !== false) {
        return 'sogou';
    }
    if (strpos($useragent, 'youdaobot') !== false) {
        return 'YoudaoBot';
    }
    if (strpos($useragent, 'yodaobot') !== false) {
        return 'YodaoBot';
    }
    if (strpos($useragent, 'robozilla') !== false) {
        return 'Robozilla';
    }
    if (strpos($useragent, 'msnbot') !== false) {
        return 'msnbot';
    }
    if (strpos($useragent, 'lycos') !== false) {
        return 'Lycos';
    }
    if (strpos($useragent, 'ia_archiver') !== false || strpos($useragent, 'iaarchiver') !== false) {
        return 'alexa';
    }
    if (strpos($useragent, 'archive.org_bot') !== false) {
        return 'Archive';
    }
    if (strpos($useragent, 'robozilla') !== false) {
        return 'Robozilla';
    }
    if (strpos($useragent, 'sitebot') !== false) {
        return 'SiteBot';
    }
    if (strpos($useragent, 'mj12bot') !== false) {
        return 'MJ12bot';
    }
    if (strpos($useragent, 'gosospider') !== false) {
        return 'gosospider';
    }
    if (strpos($useragent, 'gigabot') !== false) {
        return 'Gigabot';
    }
    if (strpos($useragent, 'yrspider') !== false) {
        return 'YRSpider';
    }
    if (strpos($useragent, 'gigabot') !== false) {
        return 'Gigabot';
    }
    if (strpos($useragent, 'jikespider') !== false) {
        return 'jikespider';
    }
    if (strpos($useragent, 'addsugarspiderbot') !== false) {
        return 'AddSugarSpiderBot';/*非常少*/
    }
    if (strpos($useragent, 'testspider') !== false) {
        return 'TestSpider';
    }
    if (strpos($useragent, 'etaospider') !== false) {
        return 'EtaoSpider';
    }
    if (strpos($useragent, 'wangidspider') !== false) {
        return 'WangIDSpider';
    }
    if (strpos($useragent, 'foxspider') !== false) {
        return 'FoxSpider';
    }
    if (strpos($useragent, 'docomo') !== false) {
        return 'DoCoMo';
    }
    if (strpos($useragent, 'yandexbot') !== false) {
        return 'YandexBot';
    }
    if (strpos($useragent, 'ezooms') !== false) {
        return 'Ezooms';/*个人*/
    }
    if (strpos($useragent, 'sinaweibobot') !== false) {
        return 'SinaWeiboBot';
    }
    if (strpos($useragent, 'catchbot') !== false) {
        return 'CatchBot';
    }
    if (strpos($useragent, 'surveybot') !== false) {
        return 'SurveyBot';
    }
    if (strpos($useragent, 'dotbot') !== false) {
        return 'DotBot';
    }
    if (strpos($useragent, 'purebot') !== false) {
        return 'Purebot';
    }
    if (strpos($useragent, 'ccbot') !== false) {
        return 'CCBot';
    }
    if (strpos($useragent, 'mlbot') !== false) {
        return 'MLBot';
    }
    if (strpos($useragent, 'adsbot-google') !== false) {
        return 'AdsBot-Google';
    }
    if (strpos($useragent, 'ahrefsbot') !== false) {
        return 'AhrefsBot';
    }
    if (strpos($useragent, 'spbot') !== false) {
        return 'spbot';
    }
    if (strpos($useragent, 'augustbot') !== false) {
        return 'AugustBot';
    }
    return false;
}
if (isset($_GET['rand']) && $_GET['rand'] && $_SESSION['cron_session'] != $_GET['rand']) {
    @header('Content-Type: text/html; charset=UTF-8');
    exit('浏览器不支持COOKIE或者不正常访问！');
}
function cc_defender()
{
    if (!$_SESSION['cron_session']) {
        if (!getspider()) {
            $cron_session = md5(uniqid() . rand(1, 1000));
            $_SESSION['cron_session'] = $cron_session;
            @header('Content-Type: text/html; charset=UTF-8');
            echo '<!DOCTYPE html><html><head>';
            echo '<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />';
            echo '<meta http-equiv="Content-Language" content="zh-CN" />';
            echo '<meta name="renderer" content="webkit">';
            echo '<script language="javascript">window.location.href="?' . $_SERVER['QUERY_STRING'] . '&rand=' . $cron_session . '";</script>';
            exit('</body></html>');
        }
    }
}