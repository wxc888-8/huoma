<?php
include('../includes/common.php');
header('Access-Control-Allow-Origin:*');
if (isset($_REQUEST['token'])) {
    $token = $_REQUEST['token'];
    $ures = $DB->get_row("select * from dwz_user where token='$token' limit 1");
    if (!$ures) {
        exit(json_encode(['code' => 202, 'msg' => '无效token'], JSON_UNESCAPED_UNICODE));
    }
    $result = array("code" => 200, "num" => $ures['create_num']);
    exit(json_encode($result, JSON_UNESCAPED_UNICODE));
} else {
    exit(json_encode(['code' => 201, 'msg' => '缺少token参数'], JSON_UNESCAPED_UNICODE));
}
