<?php
header("Content-Type: application/javascript; charset=utf-8");
define('SYSTEM_ROOT', (__DIR__ . '/../../../includes/'));
?>
var longurl = document.getElementById("dwz").href;
var Turl = longurl;

<?php
$useragent = strtolower($_SERVER['HTTP_USER_AGENT']);
if ((strpos($useragent, 'iphone') !== false || strpos($useragent, 'ipod') !== false) && $_SERVER['HTTP_REFERER'] != false) {
    $browser_ios = include(SYSTEM_ROOT . 'libs/browser_ios.php');
    foreach ($browser_ios as $key => $value) {
        if ($value[2] == 'yes') echo 'openios("' . $value[0] . '" + ' . (($value[1] == 2) ? 'Turl' : 'longurl') . ');';
    }
} elseif (strpos($useragent, 'android') !== false) {
    $browser_an = include(SYSTEM_ROOT . 'libs/browser_an.php');
    foreach ($browser_an as $key => $value) {
        if ($value[2] == 'yes') echo 'openurl("' . $value[0] . '" + ' . (($value[1] == 2) ? 'Turl' : 'longurl') . ');';
    }
} ?>

window.onresize = function () {setRootFontSize();}
function openurl(url) {var a = document.createElement('iframe');a.setAttribute('src', url);a.setAttribute('style', 'display:none;position:relative;z-index:-1;');document.body.appendChild(a);}
function openios(url) {var a = document.createElement('a');a.setAttribute('href', url);a.setAttribute('style', 'display:none');document.body.appendChild(a);a.click();a.parentNode.removeChild(a);}
function ob(u){document.getElementById(rid).href= u;document.getElementById(rid).click();}