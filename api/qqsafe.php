<?php

header('Access-Control-Allow-Origin:*');
header('Content-type:application/json; charset=utf-8');
include('../includes/common.php');
if (isset($_POST['url'])) {
$urld = $_POST['url'];
}
if (isset($_GET['url'])) {
$urld = $_GET['url'];
}
if($urld==''){
   exit('检测失败') ;
}
    if (preg_match('/(http:\/\/)|(https:\/\/)/i', $urld)) {
   $urld= preg_replace('/(http:\/\/)|(https:\/\/)/i', '', $urld);
    // 去掉 h t t p: //和 h t t ps: //前缀
}
$shortUrl='https://www.urlshare.cn/umirror_url_check?url=http%3A%2F%2F'.$urld;
//@$type = sqlfzr(trim($_REQUEST["type"]));
 /***

     * 万能短网址还原函数

     * @param $shortUrl 短网址

     * @return 原始网址 | 空（还原失败或非短网址）

     */

    function restoreUrl($shortUrl) {

        $curl = curl_init();

        curl_setopt($curl, CURLOPT_URL, $shortUrl);

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:70.0) Gecko/20100101 Firefox/70.0');

        curl_setopt($curl, CURLOPT_HEADER, true);

        curl_setopt($curl, CURLOPT_NOBODY, false);

        curl_setopt($curl, CURLOPT_TIMEOUT, 15);

        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);

        curl_setopt($curl, CURLOPT_ENCODING, 'gzip');

        $data = curl_exec($curl);

        $curlInfo = curl_getinfo($curl);

        curl_close($curl);

        if($curlInfo['http_code'] == 301 || $curlInfo['http_code'] == 302) {

            return $curlInfo['redirect_url'];

        }

        return '';

    }
    
      // $shortUrl = 'https://url.cn/54VbB8h';    // 要还原的短网址

    $orinalUrl = restoreUrl($shortUrl);

    if($orinalUrl) {

    //    echo "短网址 {$shortUrl} 的还原结果：{$orinalUrl}";
     if (preg_match('/(http:\/\/)|(https:\/\/)/i', $orinalUrl)) {
   $orinalUrl = preg_replace('/(http:\/\/)|(https:\/\/)/i', '', $orinalUrl);
    // 去掉 h t t p: //和 h t t ps: //前缀
}
$url01=strpos($orinalUrl,"/");
$url2=substr($orinalUrl,0,$url01);
if($url2=='c.pc.qq.com'){
    exit('{"code":201,"url":"'.$urld.'","msg":"域名异常"}');
}else {
 exit('{"code":200,"url":"'.$urld.'","msg":"域名正常"}');
}

    }    