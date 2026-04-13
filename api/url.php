<?php
include('../includes/common.php');
header('Access-Control-Allow-Origin:*');
header('Content-type:application/json; charset=utf-8');
if (isset($_REQUEST['token'])) {
    $token = $_REQUEST['token'];
    $ures = $DB->get_row("select * from dwz_user where token='$token' limit 1");
    if (!$ures) {
        exit(json_encode(['code' => 202, 'msg' => '无效token'], JSON_UNESCAPED_UNICODE));
    }
    if ($conf['vip_api'] == 1 && $ures['vip'] < $date) {
        exit(json_encode(['code' => 205, 'msg' => '您的会员已过期，请充值后使用'], JSON_UNESCAPED_UNICODE));
    }
    $uid = $ures['id'];
    if (isset($_REQUEST['url'])) {
        $url = $_REQUEST['url'];
        $preg = "#^http(s)?://(www.)?(\w+(\.)?)+#";
        if (!preg_match($preg, $url)) {
            exit(json_encode(['code' => 203, 'msg' => '网址格式有误'], JSON_UNESCAPED_UNICODE));
        }
        $id = isset($_REQUEST['id']) ? $_REQUEST['id'] : '';
        $type = isset($_REQUEST['type']) ? $_REQUEST['type'] : $conf['dwz_type'];
        if ($type == 'suiji') {
            $res = $DB->get_row("select keyname from dwz_api where status=1 order by rand() limit 1");
            $type = $res['keyname'];
        }
        $pattern = isset($_REQUEST['pattern']) ? $_REQUEST['pattern'] : $conf['pattern'];
        $title = isset($_REQUEST['title']) ? $_REQUEST['title'] : '';
        $bz = isset($_REQUEST['bz']) ? $_REQUEST['bz'] : '';
        $pwd = isset($_REQUEST['pwd']) ? $_REQUEST['pwd'] : '';
        $visit = isset($_REQUEST['visit']) ? $_REQUEST['visit'] : '';
        $visiturl = isset($_REQUEST['visiturl']) ? $_REQUEST['visiturl'] : '';
        if ($id == '') {
            $id = getCode($conf['link_length']);
        } else {
            $plen = strlen($id);
            if (!preg_match("/^[a-za-z0-9]+$/i", $id) || $plen < 2 || $plen > 8 || $id == 0) {
                $result = array("code" => -1, "msg" => "后缀只能为字母或数字，且长度为1-8之间");
                exit(json_encode($result));
            } else {
                if ($DB->get_row("select id from dwz_url where id='$id' limit 1")) {
                    $result = array("code" => -1, "msg" => "该后缀已存在");
                    exit(json_encode($result));
                }
            }
        }
        $str = createUrl($type, $id, $uid, base64_encode($url), $pattern, $title, $bz, $visit, $visiturl, $pwd);
        if ($str['code'] == 0) {
            $result = array("code" => 200, "dwz" => $str['msg']);
            exit(json_encode($result, JSON_UNESCAPED_UNICODE));
        } else {
            exit(json_encode(['code' => 204, 'msg' => $str['msg']], JSON_UNESCAPED_UNICODE));
        }
    } else {
        exit(json_encode(['code' => 201, 'msg' => '缺少url参数'], JSON_UNESCAPED_UNICODE));
    }
} else {
    exit(json_encode(['code' => 201, 'msg' => '缺少token参数'], JSON_UNESCAPED_UNICODE));
}
