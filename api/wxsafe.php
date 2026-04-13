<?php
include('../includes/common.php');
// 返回JSON
header('Content-type: application/json;charset=utf-8');

$domain = $_REQUEST['url'];

function checkWxDomain($domain, $tryCnt = 0) {
    $domain = str_replace(['http://', 'https://'], '', $domain);
    $strr = "https://mp.weixinbridge.com/mp/wapredirect?url=http://" . $domain;
    $headers = [
        'Host: mp.weixinbridge.com',
        'User-Agent: Mozilla/5.0 (Windows NT 6.1; WOW64; rv:67.0) Gecko/20100101 Firefox/67.0',
        'Referer: https://mp.weixin.qq.com/s/LnOGsZs5c0gJsqZ8qYLT8w'
    ];

    $curjc = curl_init();
    curl_setopt($curjc, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($curjc, CURLOPT_URL, $strr);
    curl_setopt($curjc, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curjc, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:67.0) Gecko/20100101 Firefox/67.0');
    curl_setopt($curjc, CURLOPT_HEADER, 1);
    curl_setopt($curjc, CURLOPT_NOBODY, 0);
    curl_setopt($curjc, CURLOPT_TIMEOUT, 15);
    $res = curl_exec($curjc);

    $status = 0;
    //$msg = '域名正常';
    $res = '{"code":200,"url":"'.$domain.'","msg":"域名正常"}';
    if (strpos($res, 'weixin110.qq.com') !== false) {
        $status = 1;
        //$msg = '域名异常';
        $res = '{"code":201,"url":"'.$domain.'","msg":"域名异常"}';
    } else if ($tryCnt < 3) {
        return checkWxDomain($domain, $tryCnt + 1);
    }

   // $res = '{"code":201,"url":"'.$domain.'","msg":"域名异常"}';

    return $res;
}

$result = checkWxDomain($domain);
//echo json_encode($result);
echo $result;