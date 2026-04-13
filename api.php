<?php
include("./includes/common.php");
@header('Content-Type: text/html; charset=UTF-8');
@header('Access-Control-Allow-Origin:*');
$act = isset($_GET['act']) ? $_GET['act'] : null;
switch ($act) {
    case 'get_title':
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $_REQUEST['url']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1); // 302 redirect
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (compatible; Baiduspider-render/2.0; +http://www.baidu.com/search/spider.html)");
        $ret = curl_exec($ch);
        curl_close($ch);
        $ret = mb_convert_encoding($ret, 'UTF-8', 'UTF-8,GBK,GB2312,BIG5');
        preg_match('/<title>(.*)<\/title>/i', $ret, $title);
        $title = str_replace(array("\r\n", "\r", "\n", ',', ' '), '', $title[1]);
        $result = array(
            'code' => 1,
            'title' => $title

        );
        break;
    case 'siteinfo':
        $count1 = $DB->count("select count(1) from dwz_user");
        $count2 = $DB->count("select count(1) from dwz_url");
        $count3 = $DB->count("select count(1) from dwz_pay where status=1");
        $count4 = $DB->count("select sum(money) from dwz_pay where status=1");
        $count5 = $DB->count("select count(1) from dwz_domain where type !=8");
        $result = array('sitename' => $conf['web_name'], 'kf_qq' => $conf['kf_qq'], 'pattern' => $conf['pattern'], 'group_link' => $conf['group_link'], 'dwz_type' => $conf['dwz_type'], 'app_alert' => $conf['app_alert'], 'version' => VERSION, 'build' => $conf['build'], 'user' => $count1, 'url' => $count2, 'orders' => $count3, 'price' => $count4, 'domain' => $count5);
        break;
    case 'geturl':
        $id = isset($_REQUEST['id']) ? daddslashes($_REQUEST['id']) : '';
        if ($id == '') {
            $url = 'https://www.baidu.com';
        } else {
            $res = $DB->get_row("select * from dwz_url where id='$id'");
            if (!$res) {
                $url = 'https://www.baidu.com';
            } else {
                $url = base64_decode($res['url']);
            }
        }
        $result = array(
            'code' => 1,
            'url' => $url
        );
        break;
}
echo json_encode($result);
$DB->close();
