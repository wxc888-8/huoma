<?php
include('../includes/common.php');
if ($islogin2 == 1) {
} else exit("<script language='javascript'>window.location.href='./login.php';</script>");

?>
<?php
$act = isset($_GET['act']) ? daddslashes($_GET['act']) : null;
@header('Content-Type: application/json; charset=UTF-8');
switch ($act) {
    case 'getcount':
        $alertgg = $conf['gg3'];
        if (isset($_COOKIE['user_alert']) && $alertgg == $_COOKIE['user_alert']) {
            $alertgg = '';
        } else {
            setcookie("user_alert", $alertgg, time() + 3600 * 24);
        }
        $yesterday = date("Y-m-d", strtotime("-1 day"));
        $allUrl = $DB->count("select count(1) from dwz_url where uid='$uid' and deltime is null");
        $allView = $DB->count("select sum(view) from dwz_url where uid='$uid' and deltime is null");
        if ($allView == '') {
            $allView = 0;
        }
        $yView = tongjiPv1($yesterday, $uid);
        $yIp = tongjiIp1($yesterday, $uid);
        $result = array("code" => 0, "gg1" => $conf['gg1'], "gg3" => $alertgg, "count1" => $allUrl, "count2" => $allView, "count3" => $yIp, "count4" => $yView);
        exit(json_encode($result));
        break;
    case 'creatUrls':
        $urls = $_POST['urls'];
        $type = $_POST['type'];
        $pattern = $_POST['pattern'];
        if (count($urls) > 50) {
            $result = array("code" => -1, "msg" => '一次最多生成50个短网址');
            exit(json_encode($result));
        }
        $data = array();
        foreach ($urls as $v) {
            if ($v != '') {
                $preg = "#^http(s)?://(www.)?(\w+(\.)?)+#";
                if (!preg_match($preg, $v)) {
                    array_push($data, '网址格式有误');
                } else {
                    $id = getCode($conf['link_length']);
                    $str = createUrl($type, $id, $uid, base64_encode($v), $pattern);
                    array_push($data, $str['msg']);
                }
            }
        }
        $result = array("code" => 0, "data" => $data);
        exit(json_encode($result));
        break;
    case 'addUrl':
        $id =  $_POST['id'];     //id编码加密
        $url = $_POST['url'];
$url404=substr($url,0,4);
if($url404!='http'){
    $result = array("code" => -1, "msg" => "网址不正确");
            exit(json_encode($result));
    exit();
}        
        $type = $_POST['type'];
        $pattern = $_POST['pattern'];
        $pwd = trim($_POST['pwd']);
        $qqjump = $_POST['qqjump'];
        $wxjump = $_POST['wxjump'];
        $alijump = $_POST['alijump'];
        $remarks = $_POST['remarks'];
        $visit = $_POST['visit'];
        $visiturl = $_POST['visiturl'];
        $title = $_POST['title'];
        $jumpmb = $_POST['jumpmb'];
        if (($url == '')) {
            $result = array("code" => -1, "msg" => "跳转网址不能为空");
            exit(json_encode($result));
        }
        if ($id == '') {
            $id = getCode($conf['link_length']);
        } else {
            if ($id == '0') {
                $result = array("code" => -1, "msg" => "后缀不能设置为0");
                exit(json_encode($result));
            }
            if (!preg_match("/^[0-9a-zA-Z]{1,8}$/", $id)) {
                $result = array("code" => -1, "msg" => "后缀只能为字母或数字，且长度为1-8之间");
                exit(json_encode($result));
            } else {
                if ($DB->get_row("select id from dwz_url where id='$id' limit 1")) {
                    $result = array("code" => -1, "msg" => "该后缀已存在");
                    exit(json_encode($result));
                }
            }
        }
        $str = createUrl($type, $id, $uid, base64_encode($url), $pattern, $title, $remarks, $visit, $visiturl, $pwd, $jumpmb, $qqjump, $wxjump, $alijump);
        if ($str['code'] == 0) {
            $result = array("code" => 0, "msg" => "生成成功，短链地址：" . $str['msg']);
            exit(json_encode($result));
        } else {
            $result = array("code" => -1, "msg" => $str['msg']);
            exit(json_encode($result));
        }
        break;
    case 'getUrlInfo':
        $id =  $_REQUEST['id'];
        $res = $DB->get_row("select * from dwz_url where id='$id' and uid='$uid' limit 1");
        if ($res) {
            $result = array(
                "code" => 0,
                "id" => $id,
                "dwz" => $res['dwz'],
                "url" =>  base64_decode($res['url']),  //url编码解密
                "pattern" => $res['pattern'],
                "title" => $res['title'],
                "remarks" => $res['remarks'],
                "visit" => $res['visit'],
                "visiturl" => $res['visiturl'],
                "pwd" => $res['pwd'],
                "qqjump" => $res['qqjump'],
                "wxjump" => $res['wxjump'],
                "alijump" => $res['alijump'],
                "view" => $res['view'],
                "addtime" => $res['addtime'],
                "jumpmb" => $res['jumpmb']
            );
            exit(json_encode($result));
        } else {
            $result = array("code" => -1, "msg" => "网址信息不存在");
            exit(json_encode($result));
        }
        break;
    case 'upUrl':
        $id = trim($_REQUEST['id']);
        $url = $_POST['url'];
        $url404=substr($url,0,4);
        if($url404!='http'){
            $result = array("code" => -1, "msg" => "网址不正确");
            exit(json_encode($result));
        }        
        $remarks = $_POST['remarks'];
        $visit = $_POST['visit'];
        $visiturl = $_POST['visiturl'];
        $pwd = trim($_POST['pwd']);
        $qqjump = $_POST['qqjump'];
        $wxjump = $_POST['wxjump'];
        $alijump = $_POST['alijump'];
        $pattern = intval($_POST['pattern']); // 强制转换为整数
        $title = $_POST['title'];
        $jumpmb = $_POST['jumpmb'];
        
        // 添加调试日志
        error_log("准备更新URL: ID=$id, URL=$url, Pattern=$pattern（整数类型）");
        
        // 在更新前检查当前pattern值
        $current = $DB->get_row("SELECT pattern FROM dwz_url WHERE id='$id' LIMIT 1");
        if ($current) {
            error_log("更新前数据库中的pattern值: {$current['pattern']}");
        }
        
        $ret = editUrl($uid, $id, $vip, base64_encode($url), $pattern, $remarks, $title, $visit, $visiturl, $pwd, $jumpmb, $qqjump, $wxjump, $alijump);
        
        // 记录返回结果
        error_log("更新URL结果: " . json_encode($ret));
        
        // 验证更新是否成功
        $updated = $DB->get_row("SELECT pattern FROM dwz_url WHERE id='$id' LIMIT 1");
        if ($updated) {
            error_log("更新后数据库中的pattern值: {$updated['pattern']}");
        }
        
        exit(json_encode($ret));
        break;
    case 'delUrl':
        $id =  $_POST['id'];
        if ($DB->count("select count(id) from dwz_url where id='$id' and uid='$uid' and deltime is null") == 0) {
            $result = array("code" => -1, "msg" => "网址不存在");
            exit(json_encode($result));
        }
        $rs = $DB->query("update dwz_url set deltime = '$date' where id = '$id'");
        if ($rs) {
            $result = array("code" => 0);
            exit(json_encode($result));
        } else {
            $result = array("code" => -1, "msg" => "删除失败");
            exit(json_encode($result));
        }
        break;
    case 'delSelect':
        $id = explode('&', $_POST['str']);
        $i = 0;
        while ($i < count($id)) {
            $DB->query("update dwz_url set deltime = '$date' where id = '$id[$i]'");
            $i++;
        }
        $result = array("code" => 0);
        exit(json_encode($result));
        break;
    case 'delSelect2':
        $id = explode('&', $_POST['str']);
        $i = 0;
        while ($i < count($id)) {
            $DB->query("delete from dwz_check where id = '$id[$i]'");
            $i++;
        }
        $result = array("code" => 0);
        exit(json_encode($result));
        break;
    case 'setUrlState':
        $id =   $_GET['id'];
        $state = intval($_GET['state']);
        $rs = $DB->query("update dwz_url set u_state='$state' where id='{$id}'");
        if ($rs) {
            $result = array("code" => 0, "msg" => "修改成功");
            exit(json_encode($result));
        } else {
            $result = array("code" => -1, "msg" => "修改失败");
            exit(json_encode($result));
        }
        break;
    case 'setCheckState':
        $id = $_GET['id'];
        $switch = intval($_GET['state']);
        $rs = $DB->query("update dwz_check set switch='$switch' where id='{$id}'");
        if ($rs) {
            $result = array("code" => 0, "msg" => "修改成功");
            exit(json_encode($result));
        } else {
            $result = array("code" => -1, "msg" => "修改失败");
            exit(json_encode($result));
        }
        break;
    case 'setUrlStateAll':
        $id = explode('&', $_POST['str']);
        $i = 0;
        $state = $_POST['state'];
        while ($i < count($id)) {
            $DB->query("update dwz_url set u_state = '$state' where id = '$id[$i]'");
            $i++;
        }
        $result = array("code" => 0);
        exit(json_encode($result));
        break;
    case 'setCheckStateAll':
        $id = explode('&', $_POST['str']);
        $i = 0;
        $switch = $_POST['state'];
        while ($i < count($id)) {
            $DB->query("update dwz_check set switch = '$switch' where id = '$id[$i]'");
            $i++;
        }
        $result = array("code" => 0);
        exit(json_encode($result));
        break;
    case 'upSelect':
        if ($vip == 0) {
            $result = array("code" => -1, "msg" => "会员功能，非会员无法修改网址");
            exit(json_encode($result));
        }
        $id = explode('&', $_POST['str']);
        $i = 0;
        $url = $_POST['url'];
$url404=substr($url,0,4);
if($url404!='http'){
    $result = array("code" => -1, "msg" => "网址不正确");
            exit(json_encode($result));
    exit();
}        
        while ($i < count($id)) {
            $res = $DB->get_row("select * from dwz_url where id = '$id[$i]'");
            if ($url != base64_decode($res['url'])) {
                $DB->query("insert into dwz_modify(uid,urlid,addtime,url1,url2,dwz) values('$uid','$id[$i]','$date','$url','{".base64_decode($res['url'])."}','{$res["dwz"]}')");
            }
            $DB->query("update dwz_url set url = '".base64_encode($url)."' where id = '$id[$i]'");
            $i++;
        }
        $result = array("code" => 0);
        exit(json_encode($result));
        break;
    case 'upUser':
        $qq = daddslashes(strip_tags($_POST['qq']));
        $name = daddslashes(strip_tags($_POST['name']));
        $pattern = daddslashes($_POST['pattern']);
        $dwz_type = daddslashes($_POST['dwz_type']);
        $mail = daddslashes(strip_tags($_POST['mail']));
        $pwd = strip_tags($_POST['pwd']);
        if (!empty($pwd) && !preg_match('/^[a-zA-Z0-9\_\!\@\#\$~\%\^\&\*.,]+$/', $pwd)) {
            $result = array("code" => -1, "msg" => "密码只能为英文与数字！");
            exit(json_encode($result));
        } elseif (!preg_match('#^[0-9]{5,11}+$#', $qq)) {
            $result = array("code" => -1, "msg" => "QQ格式不正确！");
            exit(json_encode($result));
        } else {
            if ($name == '' || $mail == '') {
                $result = array("code" => -1, "msg" => "昵称与邮箱不能为空");
                exit(json_encode($result));
            }
            if ($DB->count("select count(*) from dwz_user where qq='$qq' and id <> '$id'") > 0) {
                $result = array("code" => -1, "msg" => "该QQ号已被其他账号使用！");
                exit(json_encode($result));
            }
            $DB->query("update dwz_user set qq='$qq',name='$name',pattern='$pattern',dwz_type='$dwz_type',mail='$mail' where id='$id'");
            if (!empty($pwd)) {
                $pwd_hash = password_hash($pwd, PASSWORD_DEFAULT);
                if ($pwd_hash) $DB->query("update dwz_user set pwd='" . daddslashes($pwd_hash) . "' where id='$id'");
            }
            $result = array("code" => 0);
            exit(json_encode($result));
        }
        break;
    case 'setToken':
        $token = md5($userrow['user'] . date('Ymd') . time() . rand(11111, 99999));
        if ($DB->query("update dwz_user set token = '$token' where id = '$id'")) {
            $result = array("code" => 0, "token" => $token);
            exit(json_encode($result));
        } else {
            $result = array("code" => -1, "msg" => "重置失败");
            exit(json_encode($result));
        }
        break;
    case 'reg':
        $id = $_REQUEST['id'];
        getReg($id);
        $result = array("code" => 0);
        exit(json_encode($result));
        break;
    case 'usekm':
        $km = trim(daddslashes($_POST['km']));
        $myrow = $DB->get_row("SELECT * FROM dwz_km WHERE km='$km' LIMIT 1");
        if (!$myrow) {
            exit('{"code":-1,"msg":"此卡密不存在！"}');
        } elseif ($myrow['status'] == 1) {
            exit('{"code":-1,"msg":"此卡密已被使用！"}');
        }
        $num = $myrow['days'];
        switch ($myrow['type']) {
            case 0:
                if ($vip == 1) {
                    $DB->query("update dwz_user set vip=date_add(vip,interval '$num' day) where id='$uid'");
                } else {
                    $vtime = date('Y-m-d H:i:s', strtotime("$date + $num day"));
                    $DB->query("update dwz_user set vip='$vtime' where id='$uid'");
                }
                $bz = '你使用卡密充值了' . $num . '天会员';
                $msg = '成功充值' . $num . '天会员！';
                break;
            case 1:
                $DB->query("update dwz_user set create_num=create_num+$num where id='$uid'");
                $bz = '你使用卡密充值了' . $num . '点短网址点数';
                $msg = '成功充值' . $num . '点短网址点数！';
                break;
            case 2:
                if ($userrow['trial_vip'] >= 1) {
                    exit('{"code":-1,"msg":"您已使用过会员试用卡密，不可再使用"}');
                }
                if ($vip == 1) {
                    $DB->query("update dwz_user set vip=date_add(vip,interval '$num' day),trial_vip=1 where id='$uid'");
                } else {
                    $vtime = date('Y-m-d H:i:s', strtotime("$date + $num day"));
                    $DB->query("update dwz_user set vip='$vtime' where id='$uid'");
                }
                $bz = '你使用会员试用卡密充值了' . $num . '天会员';
                $msg = '成功充值' . $num . '天会员！';
                break;
            case 3:
                if ($userrow['trial_create'] >= 1) {
                    exit('{"code":-1,"msg":"您已使用过额度试用卡密，不可再使用"}');
                }
                $DB->query("update dwz_user set create_num=create_num+$num,trial_create=1 where id='$uid'");
                $bz = '你使用额度试用卡密充值了' . $num . '点短网址点数';
                $msg = '成功充值' . $num . '点短网址点数！';
                break;
            case 4:
                $DB->query("update dwz_user set check_num=check_num+$num where id='$uid'");
                $bz = '你使用卡密充值了' . $num . '次监控次数';
                $msg = '成功充值' . $num . '次监控次数！';
                break;
        }
        if ($DB->query("UPDATE `dwz_km` SET `status`=1 WHERE `id`='{$myrow['id']}'")) {
            $DB->query("UPDATE `dwz_km` SET `uid` ='$uid',`usetime` ='" . $date . "' WHERE `id`='{$myrow['id']}'");
            $rs = $DB->query("insert into dwz_points(uid,action,number,point,bz,addtime,status) values('$uid','卡密','$num',0,'$bz','$date',1)");
            if ($rs) {
                exit('{"code":0,"msg":"' . $msg . '"}');
            }
        }
        exit('{"code":-1,"msg":"充值失败' . $DB->error() . '"}');
        break;
    case 'recharge':
        $value = daddslashes($_GET['value']);
        $num = daddslashes($_GET['num']);
        if ($value == 1) {
            $name = '会员月卡';
            $money = $conf['vip_month'] * $num;
        } elseif ($value == 2) {
            $name = '会员季卡';
            $money = $conf['vip_quarter'] * $num;
        } elseif ($value == 3) {
            $name = '会员年卡';
            $money = $conf['vip_year'] * $num;
        } elseif ($value == 4) {
            $name = '短网址点数（100次）';
            if ($vip == 1) {
                $money = $conf['dwz_price'] * $num * $conf['discount'];
            } else {
                $money = $conf['dwz_price'] * $num;
            }
        } elseif ($value == 5) {
            $name = '网址监控（100次）';
            if ($vip == 1) {
                $money = $conf['check_price'] * $num * $conf['discount'];
            } else {
                $money = $conf['check_price'] * $num;
            }
        } elseif ($value == 6) {
            // 处理积分充值
            $package = $DB->get_row("SELECT * FROM `dwz_points_package` WHERE `id`='$num' AND `status`='1' LIMIT 1");
            if (!$package) {
                exit('{"code":-1,"msg":"积分套餐不存在或已下架！"}');
            }
            $name = "积分充值 - " . $package['name'];
            $money = $package['price'];
            $num = $package['points_num']; // 使用套餐中的积分数量
        } else {
            exit('{"code":-1,"msg":"提交订单失败！"}');
        }
        $trade_no = date("YmdHis") . rand(111, 999);
        $sql = "insert into dwz_pay(trade_no,uid,num,name,money,ip,addtime,status) values('$trade_no','$uid','$num','$name','$money','$clientip','$date',0)";
        if ($DB->query($sql)) {
            exit('{"code":0,"msg":"提交订单成功！","trade_no":"' . $trade_no . '","money":"' . $money . '","name":"' . $name . '"}');
        } else {
            exit('{"code":-1,"msg":"提交订单失败！' . $DB->error() . '"}');
        }
        break;
    case 'addCheck':
        $url = $_POST['url'];
$url404=substr($url,0,4);
if($url404!='http'){
    $result = array("code" => -1, "msg" => "网址不正确");
            exit(json_encode($result));
    exit();
}        
        $type = $_POST['type'];
        $pl = $_POST['pl'];
        if (($url == '')) {
            $result = array("code" => -1, "msg" => "监控网址不能为空");
            exit(json_encode($result));
        }
        if ($DB->count("select count(1) from dwz_check where url='".base64_encode($url)."' and uid='$uid' and type='$type'") > 0) {
            $result = array("code" => -1, "msg" => "该监控网址已存在");
            exit(json_encode($result));
        }
        $rs = $DB->query("insert into dwz_check(url,type,pl,addtime,lasttime,uid) values('".base64_encode($url)."','$type','$pl','$date','$date','$uid')");
        if ($rs) {
            $result = array("code" => 0, "msg" => "添加成功");
            exit(json_encode($result));
        } else {
            $result = array("code" => -1, "msg" => "添加失败");
            exit(json_encode($result));
        }
        break;
    case 'upCheck':
        $id = trim($_REQUEST['id']);
        if ($DB->count("select count(1) from dwz_check where id = '$id'") == 0) {
            $result = array("code" => -1, "msg" => "监控信息不存在");
            exit(json_encode($result));
        }
        $url = $_POST['url'];
$url404=substr($url,0,4);
if($url404!='http'){
    $result = array("code" => -1, "msg" => "网址不正确");
            exit(json_encode($result));
    exit();
}        
        $type = $_POST['type'];
        $pl = $_POST['pl'];
        if ($url == '') {
            $result = array("code" => -1, "msg" => "监控网址不能为空");
            exit(json_encode($result));
        }
        if ($DB->count("select count(1) from dwz_check where uid='$uid' and type='$type' and url='".base64_encode($url)."' and id<>'$id'") > 0) {
            $result = array("code" => -1, "msg" => "监控网址已存在");
            exit(json_encode($result));
        }
        $rs = $DB->query("update dwz_check set url='".base64_encode($url)."',type='$type',pl='$pl' where id='$id'");
        if ($rs) {
            $result = array("code" => 0, "msg" => "修改成功");
            exit(json_encode($result));
        } else {
            $result = array("code" => -1, "msg" => "修改失败");
            exit(json_encode($result));
        }
        break;
    case 'getCheckInfo':
        $id = $_REQUEST['id'];
        $res = $DB->get_row("select * from dwz_check where id='$id' and uid='$uid' limit 1");
        if ($res) {
            $result = array(
                "code" => 0,
                "id" => $id,
                "url" => base64_decode($res['url']),
                "type" => $res['type'],
                "pl" => $res['pl']
            );
            exit(json_encode($result));
        } else {
            $result = array("code" => -1, "msg" => "监控信息不存在");
            exit(json_encode($result));
        }
        break;
    case 'delCheck':
        $id = $_POST['id'];
        if ($DB->count("select count(1) from dwz_check where id='$id' and uid='$uid'") == 0) {
            $result = array("code" => -1, "msg" => "监控不存在");
            exit(json_encode($result));
        }
        $rs = $DB->query("delete from dwz_check where id = '$id'");
        if ($rs) {
            $result = array("code" => 0);
            exit(json_encode($result));
        } else {
            $result = array("code" => -1, "msg" => "删除失败");
            exit(json_encode($result));
        }
        break;
    case 'urllist':
        $callback = $_GET['callback'];
        $kw = isset($_GET['kw']) ? $_GET['kw'] : '';
        if ($kw != '') {
            $sql = " (id like '%$kw%' or dwz like '%$kw%' or remarks like '%$kw%' or url like '%$kw%')";
        } else {
            $sql = "1";
        }
        $totle = $DB->count("select count(*) from dwz_url where({$sql} and uid='$uid' and deltime is null)");
        $pagesize = $_GET['limit'];
        $pages = ceil($totle / $pagesize);
        $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
        $offset = $_GET['offset'];
        $sort = isset($_GET['sort']) ? $_GET['sort'] : 'addtime';
        $sortOrder = $_GET['sortOrder'];
        $rs = $DB->query("select * from dwz_url where({$sql} and uid='$uid' and deltime is null) order by $sort $sortOrder limit $offset,$pagesize");
        $i = 0;
        $rows = array();
        while ($res = $DB->fetch($rs)) {
            $rows[$i] = array(
                'id' =>  $res['id'], 'dwz' => $res['dwz'], 'u_state' => $res['u_state'], 'remarks' => $res['remarks'],
                'view' => $res['view'], 'pattern' => $res['pattern'], 'addtime' => $res['addtime'], 'url' => base64_decode($res['url'])
            );   //编码解密
            $i++;
        }
        $result = array('rows' => $rows, 'total' => $totle);
        exit($callback . '(' . json_encode($result) . ')');
        break;
    case 'urlcheck':
        $callback = $_GET['callback'];
        $kw = isset($_GET['kw']) ? $_GET['kw'] : '';
        if ($kw != '') {
            $sql = " (id like '%$kw%' or url like '%$kw%')";
        } else {
            $sql = "1";
        }
        $totle = $DB->count("select count(*) from dwz_check where({$sql} and uid='$uid')");
        $pagesize = $_GET['limit'];
        $pages = ceil($totle / $pagesize);
        $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
        $offset = $_GET['offset'];
        $sort = isset($_GET['sort']) ? $_GET['sort'] : 'addtime';
        $sortOrder = $_GET['sortOrder'];
        $rs = $DB->query("select * from dwz_check where({$sql} and uid='$uid') order by $sort $sortOrder limit $offset,$pagesize");
        $i = 0;
        $rows = array();
        while ($res = $DB->fetch($rs)) {
            $rows[$i] = array(
                'id' =>  $res['id'], 'url' => base64_decode($res['url']), 'type' => $res['type'], 'switch' => $res['switch'], 'pl' => $res['pl'],
                'status' => $res['status'], 'num' => $res['num'], 'addtime' => $res['addtime'], 'lasttime' => $res['lasttime']
            );
            $i++;
        }
        $result = array('rows' => $rows, 'total' => $totle);
        exit($callback . '(' . json_encode($result) . ')');
        break;
    
    // 活码系统API接口
    case 'qrcode_list':
        $callback = isset($_GET['callback']) ? $_GET['callback'] : '';
        $kw = isset($_GET['kw']) ? daddslashes($_GET['kw']) : '';
        $wechat_status = isset($_GET['wechat_status']) ? intval($_GET['wechat_status']) : -1;
        $time_range = isset($_GET['time']) ? daddslashes($_GET['time']) : '';
        
        // 调试uid
        $debug_info = array('uid' => $uid);
        
        // 构建查询条件，先不使用复杂条件，直接查询该用户所有活码
        $where = "uid='{$uid}'";
        
        // 日志一下debug信息
        file_put_contents('../logs/qrcode_debug.log', date('Y-m-d H:i:s').': uid='.$uid.', SQL=SELECT * FROM dwz_qrcode WHERE '.$where."\n", FILE_APPEND);
        
        // 先尝试直接查询数据
        $rs_debug = $DB->query("SELECT * FROM dwz_qrcode WHERE uid='{$uid}'");
        $debug_rows = array();
        while ($row = $DB->fetch($rs_debug)) {
            $debug_rows[] = $row;
        }
        $debug_info['direct_rows'] = $debug_rows;
        
        if (empty($debug_rows)) {
            // 如果没有数据，尝试查询所有活码，不带条件
            $rs_all = $DB->query("SELECT * FROM dwz_qrcode LIMIT 10");
            $all_rows = array();
            while ($row = $DB->fetch($rs_all)) {
                $all_rows[] = $row;
            }
            $debug_info['all_rows'] = $all_rows;
            
            // 返回调试信息
            exit(json_encode(['total' => 0, 'rows' => [], 'debug' => $debug_info]));
        }
        
        // 如果找到数据，继续正常处理
        // 添加筛选条件
        if ($kw) {
            $where .= " AND (name LIKE '%{$kw}%' OR entry_domain LIKE '%{$kw}%' OR landing_domain LIKE '%{$kw}%' OR remarks LIKE '%{$kw}%')";
        }
        if ($wechat_status != -1) {
            $where .= " AND wechat_status='{$wechat_status}'";
        }
        
        // 时间筛选
        if ($time_range) {
            $today = date("Y-m-d");
            $yesterday = date("Y-m-d", strtotime("-1 day"));
            $week_start = date("Y-m-d", strtotime("this week Monday"));
            $month_start = date("Y-m-d", strtotime("first day of this month"));
            
            switch ($time_range) {
                case 'today':
                    $where .= " AND DATE(addtime)='{$today}'";
                    break;
                case 'yesterday':
                    $where .= " AND DATE(addtime)='{$yesterday}'";
                    break;
                case 'week':
                    $where .= " AND DATE(addtime)>='{$week_start}'";
                    break;
                case 'month':
                    $where .= " AND DATE(addtime)>='{$month_start}'";
                    break;
            }
        }
        
        // 计算总数
        $total = count($debug_rows); // 直接使用已查询到的数量
        
        // 分页参数
        $pagesize = isset($_GET['limit']) ? intval($_GET['limit']) : 10;
        $offset = isset($_GET['offset']) ? intval($_GET['offset']) : 0;
        $sort = isset($_GET['sort']) ? $_GET['sort'] : 'id';
        $order = isset($_GET['order']) ? $_GET['order'] : 'DESC';
        
        // 使用已经查询出的数据，跳过二次查询
        $rows = array();
        foreach ($debug_rows as $res) {
            $rows[] = array(
                'id' => $res['id'],
                'name' => $res['name'],
                'code' => $res['code'], // 添加code字段到返回数据
                'qr_url' => $res['qr_url'],
                'entry_domain' => $res['entry_domain'],
                'landing_domain' => $res['landing_domain'],
                'views' => $res['views'],
                'ip_count' => $res['ip_count'],
                'wechat_status' => $res['wechat_status'],
                'state' => $res['state'],
                'addtime' => $res['addtime'],
                'updatetime' => $res['updatetime'],
                'remarks' => $res['remarks']
            );
        }
        
        $result = array('total' => $total, 'rows' => $rows, 'debug' => $debug_info);
        
        if ($callback) {
            exit($callback . '(' . json_encode($result) . ')');
        } else {
            exit(json_encode($result));
        }
        break;
    
    case 'save_qr':
        // 添加错误显示，便于调试
        error_reporting(E_ALL);
        ini_set('display_errors', 1);
        
        // 添加调试日志文件
        $debug_log_file = '../logs/qr_debug.log';
        file_put_contents($debug_log_file, date('Y-m-d H:i:s') . " - AJAX Save QR Started\n", FILE_APPEND);
        file_put_contents($debug_log_file, "POST Data Keys: " . print_r(array_keys($_POST), true) . "\n", FILE_APPEND);
        
        try {
            $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
            
            $stmt = $DB->query("SELECT * FROM `dwz_user` WHERE `id` = '".$uid."' ORDER BY `id` ASC");
            $userPoints = $stmt->fetch_assoc()['points']; // 获取结果集中的第一行作为关联数组
            if ($userPoints <= 0) {
                file_put_contents($debug_log_file, "错误: 积分不足 (当前积分: {$userPoints})\n", FILE_APPEND);
                exit(json_encode(array('code' => -1, 'msg' => '积分不足，无法创建新活码')));
            }
        
            // 日志记录收到的参数
            file_put_contents($debug_log_file, "处理参数: id={$id}\n", FILE_APPEND);
            // 验证参数
            if (empty($_POST['name'])) {
                file_put_contents($debug_log_file, "错误: 活码名称为空\n", FILE_APPEND);
                exit(json_encode(array('code' => -1, 'msg' => '活码名称不能为空')));
            }
            if (empty($_POST['entry_domain'])) {
                file_put_contents($debug_log_file, "错误: 入口域名为空\n", FILE_APPEND);
                exit(json_encode(array('code' => -1, 'msg' => '入口域名不能为空')));
            }
            if (empty($_POST['landing_domain'])) {
                file_put_contents($debug_log_file, "错误: 落地页域名为空\n", FILE_APPEND);
                exit(json_encode(array('code' => -1, 'msg' => '落地页域名不能为空')));
            }
            if (empty($_POST['jump_url'])) {
                file_put_contents($debug_log_file, "错误: 跳转地址为空\n", FILE_APPEND);
                exit(json_encode(array('code' => -1, 'msg' => '至少需要一个跳转地址')));
            }
            
            // 获取POST数据
            $name = daddslashes($_POST['name']);
            $entry_domain = daddslashes($_POST['entry_domain']);
            $landing_domain = daddslashes($_POST['landing_domain']);
            $jump_time = isset($_POST['jump_time']) ? intval($_POST['jump_time']) : 0;
            $show_safe = isset($_POST['show_safe']) ? intval($_POST['show_safe']) : 1;
            $threshold_type = isset($_POST['threshold_type']) ? intval($_POST['threshold_type']) : 1;
            $infinite_loop = isset($_POST['infinite_loop']) ? intval($_POST['infinite_loop']) : 0;
            $remarks = isset($_POST['remarks']) ? daddslashes($_POST['remarks']) : '';
            
            // 记录处理后的参数
            file_put_contents($debug_log_file, "处理后基本参数: name={$name}, entry_domain={$entry_domain}\n", FILE_APPEND);
            
            // 处理跳转URL数据
            $jump_urls = isset($_POST['jump_url']) ? $_POST['jump_url'] : array();
            if (!is_array($jump_urls)) {
                $jump_urls = array($jump_urls);
                file_put_contents($debug_log_file, "转换jump_urls为数组\n", FILE_APPEND);
            }
            file_put_contents($debug_log_file, "跳转URL数量: " . count($jump_urls) . "\n", FILE_APPEND);
            
            $image_urls = isset($_POST['image_url']) ? $_POST['image_url'] : array();
            if (!is_array($image_urls)) {
                $image_urls = array($image_urls);
                file_put_contents($debug_log_file, "转换image_urls为数组\n", FILE_APPEND);
            }
            file_put_contents($debug_log_file, "图片数据数量: " . count($image_urls) . "\n", FILE_APPEND);
            
            // 检查图片数据
            for ($i = 0; $i < count($image_urls); $i++) {
                $img_data = $image_urls[$i];
                if (!empty($img_data)) {
                    $img_length = strlen($img_data);
                    $img_type = substr($img_data, 0, 10) == 'data:image' ? 'Base64' : 'URL/Other';
                    file_put_contents($debug_log_file, "图片[{$i}]类型: {$img_type}, 长度: {$img_length}字节\n", FILE_APPEND);
                }
            }
            
            $thresholds = isset($_POST['threshold']) ? $_POST['threshold'] : array();
            if (!is_array($thresholds)) {
                $thresholds = array($thresholds);
                file_put_contents($debug_log_file, "转换thresholds为数组\n", FILE_APPEND);
            }
            
            // 开启事务
            $DB->query("START TRANSACTION");
            file_put_contents($debug_log_file, "开始事务处理\n", FILE_APPEND);
            
            try {
                if ($id) { // 更新
                    file_put_contents($debug_log_file, "更新模式: ID={$id}\n", FILE_APPEND);
                    // 验证权限
                    $check_qr = $DB->get_row("SELECT * FROM dwz_qrcode WHERE id = '{$id}' AND uid = '{$uid}'");
                    if (!$check_qr) {
                        file_put_contents($debug_log_file, "错误: 权限验证失败，未找到ID={$id}的活码或无权限\n", FILE_APPEND);
                        throw new Exception("您没有权限修改此活码");
                    }
                    
                    // 改进域名处理逻辑：优先使用原始域名，除非前端明确提供了新的完整域名
                    $entry_domain_original = $check_qr['entry_domain'];
                    $landing_domain_original = $check_qr['landing_domain'];
                    
                    // 检查是否来自original_entry_domain和original_landing_domain的表单字段
                    $original_entry_domain = isset($_POST['original_entry_domain']) ? daddslashes($_POST['original_entry_domain']) : '';
                    $original_landing_domain = isset($_POST['original_landing_domain']) ? daddslashes($_POST['original_landing_domain']) : '';
                    
                    file_put_contents($debug_log_file, "原始入口域名: {$entry_domain_original}, 提交的入口域名: {$entry_domain}\n", FILE_APPEND);
                    file_put_contents($debug_log_file, "原始落地域名: {$landing_domain_original}, 提交的落地域名: {$landing_domain}\n", FILE_APPEND);
                    
                    // 检查是否明确要求保留原始域名
                    $keep_original_entry = isset($_POST['keep_original_entry']) && $_POST['keep_original_entry'] == '1';
                    $keep_original_landing = isset($_POST['keep_original_landing']) && $_POST['keep_original_landing'] == '1';
                    
                    // 确定最终使用的域名值
                    if ($keep_original_entry || $entry_domain == $original_entry_domain || empty($entry_domain)) {
                        // 保留原始入口域名
                        $entry_domain = $entry_domain_original;
                        file_put_contents($debug_log_file, "保留原始入口域名: {$entry_domain}\n", FILE_APPEND);
                    } else {
                        file_put_contents($debug_log_file, "使用新的入口域名: {$entry_domain}\n", FILE_APPEND);
                    }
                    
                    if ($keep_original_landing || $landing_domain == $original_landing_domain || empty($landing_domain)) {
                        // 保留原始落地域名
                        $landing_domain = $landing_domain_original;
                        file_put_contents($debug_log_file, "保留原始落地域名: {$landing_domain}\n", FILE_APPEND);
                    } else {
                        file_put_contents($debug_log_file, "使用新的落地域名: {$landing_domain}\n", FILE_APPEND);
                    }
                    
                    // 检查入口域名和落地域名是否真的发生变化
                    $domain_changed = ($entry_domain_original != $entry_domain || $landing_domain_original != $landing_domain);
                    file_put_contents($debug_log_file, "域名是否变更: " . ($domain_changed ? "是" : "否") . 
                                     " (原入口域名: {$entry_domain_original}, 新入口域名: {$entry_domain}, " . 
                                     "原落地域名: {$landing_domain_original}, 新落地域名: {$landing_domain})\n", FILE_APPEND);
                    
                    // 更新主表
                    $sql = "UPDATE dwz_qrcode SET 
                            name = '{$name}',
                            entry_domain = '{$entry_domain}',
                            landing_domain = '{$landing_domain}',
                            jump_time = '{$jump_time}',
                            show_safe = '{$show_safe}',
                            threshold_type = '{$threshold_type}',
                            infinite_loop = '{$infinite_loop}',
                            remarks = '{$remarks}',
                            updatetime = '{$date}'
                            WHERE id = '{$id}' AND uid = '{$uid}'";
                    
                    file_put_contents($debug_log_file, "执行SQL: {$sql}\n", FILE_APPEND);
                    if (!$DB->query($sql)) {
                        file_put_contents($debug_log_file, "错误: 更新活码信息失败: " . $DB->error() . "\n", FILE_APPEND);
                        throw new Exception("更新活码信息失败: " . $DB->error());
                    }
                    
                    // 删除原有数据
                    $del_sql = "DELETE FROM dwz_qrcode_data WHERE qid = '{$id}'";
                    file_put_contents($debug_log_file, "执行SQL: {$del_sql}\n", FILE_APPEND);
                    if (!$DB->query($del_sql)) {
                        file_put_contents($debug_log_file, "错误: 删除原数据失败: " . $DB->error() . "\n", FILE_APPEND);
                        throw new Exception("删除原数据失败: " . $DB->error());
                    }
                    
                    // 只有当域名发生变化时，才重新生成二维码
                    if ($domain_changed) {
                        file_put_contents($debug_log_file, "域名已变更，重新生成二维码\n", FILE_APPEND);
                        require_once(dirname(__FILE__) . '/phpqrcode.php');
                        
                        // 创建qrcode目录(如果不存在)
                        $qrcode_dir = dirname(__FILE__) . '/qrcode';
                        if (!file_exists($qrcode_dir)) {
                            mkdir($qrcode_dir, 0755, true);
                        }
                        
                        // 确保目录可写
                        if (!is_writable($qrcode_dir)) {
                            file_put_contents($debug_log_file, "错误: 二维码目录不可写，请检查权限\n", FILE_APPEND);
                            throw new Exception("二维码目录不可写，请检查权限");
                        }
                        
                        // 检查entry_domain是否已包含二级域名前缀
                        $entry_domain_parts = explode('.', $entry_domain);
                        if (count($entry_domain_parts) <= 2) {
                            // 如果没有二级域名前缀，生成一个
                            $random_subdomain = getrand2(mt_rand(5, 8));
                            file_put_contents($debug_log_file, "生成随机二级域名: {$random_subdomain}\n", FILE_APPEND);
                            
                            // 检查域名是否已包含协议前缀
                            if (preg_match('#^https?://#i', $entry_domain)) {
                                // 已包含前缀，直接使用
                                $qr_content = $entry_domain . '/' . $check_qr['code'];
                                file_put_contents($debug_log_file, "域名已包含前缀，直接使用: {$qr_content}\n", FILE_APPEND);
                            } else {
                                // 没有前缀，添加http://和随机二级域名
                                $qr_content = 'http://' . $random_subdomain . '.' . $entry_domain . '/' . $check_qr['code'];
                                file_put_contents($debug_log_file, "域名添加前缀和随机二级域名: {$qr_content}\n", FILE_APPEND);
                            }
                        } else {
                            // 已经有二级域名前缀，检查是否有http前缀
                            if (preg_match('#^https?://#i', $entry_domain)) {
                                // 已包含前缀，直接使用
                                $qr_content = $entry_domain . '/' . $check_qr['code'];
                                file_put_contents($debug_log_file, "域名已包含前缀，直接使用: {$qr_content}\n", FILE_APPEND);
                            } else {
                                // 没有前缀，添加http://
                                $qr_content = 'http://' . $entry_domain . '/' . $check_qr['code'];
                                file_put_contents($debug_log_file, "域名添加前缀: {$qr_content}\n", FILE_APPEND);
                            }
                        }
                        
                        // 如果前端传递了完整的URL，则使用前端提供的URL
                        if (isset($_POST['full_entry_domain']) && !empty($_POST['full_entry_domain'])) {
                            $qr_content = $_POST['full_entry_domain'] . '/' . $check_qr['code'];
                            file_put_contents($debug_log_file, "使用前端提供的完整URL生成二维码: {$qr_content}\n", FILE_APPEND);
                        }
                        
                        $qr_file = 'qrcode/'.$id.'.png';
                        $qr_filepath = $qrcode_dir.'/'.$id.'.png';
                        
                        file_put_contents($debug_log_file, "更新模式生成二维码，内容为: {$qr_content}\n", FILE_APPEND);
                        
                        try {
                            // 使用phpqrcode库生成二维码图片
                            QRcode::png($qr_content, $qr_filepath, 'L', 10, 2);
                            
                            // 更新二维码图片地址
                            $DB->query("UPDATE dwz_qrcode SET qr_url = '{$qr_file}' WHERE id = '{$id}'");
                        } catch (Exception $e) {
                            file_put_contents($debug_log_file, "警告: 二维码生成失败: " . $e->getMessage() . "\n", FILE_APPEND);
                            // 继续执行不中断流程
                        }
                    } else {
                        file_put_contents($debug_log_file, "域名未变更，保留原有二维码\n", FILE_APPEND);
                    }
                    
                } else { // 新增
                    file_put_contents($debug_log_file, "新增模式\n", FILE_APPEND);
                    
                    // 生成一个随机唯一的标识符code
                    $qr_code = '';
                    // 检查前端是否已提供随机code
                    if (isset($_POST['qr_code']) && !empty($_POST['qr_code'])) {
                        $qr_code = daddslashes($_POST['qr_code']);
                        file_put_contents($debug_log_file, "使用前端提供的标识符: {$qr_code}\n", FILE_APPEND);
                    } else {
                        // 生成8位随机字符串（包含字母、数字和特殊符号）
                        // 修改字符集，使其包含更多特殊符号，参考格式G.U$YC3U
                        $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789.$!@#$%^&*()-_=+';
                        $string_part = '';
                        for ($i = 0; $i < 8; $i++) {
                            $string_part .= $chars[mt_rand(0, strlen($chars) - 1)];
                        }
                        
                        // 生成8位随机数字
                        $number_part = '';
                        for ($i = 0; $i < 8; $i++) {
                            $number_part .= mt_rand(0, 9);
                        }
                        
                        // 组合为最终格式：随机字符串/随机数字
                        $qr_code = $string_part . '/' . $number_part;
                        file_put_contents($debug_log_file, "后端生成的随机标识符: {$qr_code}\n", FILE_APPEND);
                    }
                    
                    // 确保标识符唯一
                    $check_code = $DB->get_row("SELECT id FROM dwz_qrcode WHERE code='{$qr_code}' LIMIT 1");
                    if ($check_code) {
                        file_put_contents($debug_log_file, "标识符已存在，重新生成\n", FILE_APPEND);
                        // 如果已存在，重新生成
                        $qr_code = '';
                        for ($i = 0; $i < 10; $i++) { // 尝试最多10次
                            // 生成8位随机字符串（字母、数字和部分符号）
                            $string_part = '';
                            for ($j = 0; $j < 8; $j++) {
                                $string_part .= $chars[mt_rand(0, strlen($chars) - 1)];
                            }
                            
                            // 生成8位随机数字
                            $number_part = '';
                            for ($j = 0; $j < 8; $j++) {
                                $number_part .= mt_rand(0, 9);
                            }
                            
                            // 组合为最终格式：随机字符串/随机数字
                            $new_code = $string_part . '/' . $number_part;
                            
                            $check_new = $DB->get_row("SELECT id FROM dwz_qrcode WHERE code='{$new_code}' LIMIT 1");
                            if (!$check_new) {
                                $qr_code = $new_code;
                                file_put_contents($debug_log_file, "重新生成的标识符: {$qr_code}\n", FILE_APPEND);
                                break;
                            }
                        }
                        
                        // 如果10次都生成不出唯一的，使用当前时间戳和随机数的组合
                        if (empty($qr_code)) {
                            $string_part = substr(md5(time()), 0, 8);
                            $number_part = mt_rand(10000000, 99999999);
                            $qr_code = $string_part . '/' . $number_part;
                            file_put_contents($debug_log_file, "使用备选方案生成标识符: {$qr_code}\n", FILE_APPEND);
                        }
                    }
                    
                    // 插入主表，添加code字段
                    $sql = "INSERT INTO dwz_qrcode (uid, name, code, entry_domain, landing_domain, jump_time, show_safe, 
                            threshold_type, infinite_loop, remarks, addtime, updatetime, state, wechat_status) 
                            VALUES ('{$uid}', '{$name}', '{$qr_code}', '{$entry_domain}', '{$landing_domain}', '{$jump_time}', 
                            '{$show_safe}', '{$threshold_type}', '{$infinite_loop}', '{$remarks}', '{$date}', '{$date}', 1, 1)";
                    
                    file_put_contents($debug_log_file, "执行SQL: {$sql}\n", FILE_APPEND);
                    if (!$DB->query($sql)) {
                        file_put_contents($debug_log_file, "错误: 创建活码失败: " . $DB->error() . "\n", FILE_APPEND);
                        throw new Exception("创建活码失败: " . $DB->error());
                    }
                    
                    // 获取最后插入的ID
                    $id = 0;
                    // 尝试以下几种可能的方式获取ID
                    if (method_exists($DB, 'lastInsertId')) {
                        $id = $DB->lastInsertId();
                    } else if (isset($DB->insert_id)) {
                        $id = $DB->insert_id;
                    } else {
                        // 如果上述方法都不可用，尝试手动查询获取最后插入的ID
                        $recent = $DB->get_row("SELECT MAX(id) as id FROM dwz_qrcode WHERE uid='$uid' ORDER BY id DESC LIMIT 1");
                        if (is_array($recent) && isset($recent['id'])) {
                            $id = $recent['id'];
                        } else if (is_object($recent) && isset($recent->id)) {
                            $id = $recent->id;
                        }
                    }
                    file_put_contents($debug_log_file, "最终获取新插入ID: {$id}\n", FILE_APPEND);
                    
                    if (!$id) {
                        throw new Exception("无法获取新插入的活码ID");
                    }
                    
                    // 生成5-8位的随机字符串作为二级域名
                    $random_subdomain = getrand2(mt_rand(5, 8)); 
                    file_put_contents($debug_log_file, "生成随机二级域名: {$random_subdomain}\n", FILE_APPEND);
                    
                    // 使用phpqrcode库生成二维码图片
                    require_once(dirname(__FILE__) . '/phpqrcode.php');
                    
                    // 创建qrcode目录(如果不存在)
                    $qrcode_dir = dirname(__FILE__) . '/qrcode';
                    if (!file_exists($qrcode_dir)) {
                        mkdir($qrcode_dir, 0755, true);
                    }
                    
                    // 确保目录可写
                    if (!is_writable($qrcode_dir)) {
                        file_put_contents($debug_log_file, "错误: 二维码目录不可写，请检查权限\n", FILE_APPEND);
                        throw new Exception("二维码目录不可写，请检查权限");
                    }
                    
                    // 检查域名是否已包含协议前缀
                    if (preg_match('#^https?://#i', $entry_domain)) {
                        // 已包含前缀，直接使用
                        if (count(explode('.', str_replace(array('http://', 'https://'), '', $entry_domain))) <= 2) {
                            // 需要添加二级域名
                            $domain_without_prefix = str_replace(array('http://', 'https://'), '', $entry_domain);
                            $qr_content = str_replace($domain_without_prefix, $random_subdomain . '.' . $domain_without_prefix, $entry_domain) . '/qr/' . $id;
                        } else {
                            // 已有二级域名
                            $qr_content = $entry_domain . '/qr/' . $id;
                        }
                        file_put_contents($debug_log_file, "域名已包含前缀，生成URL: {$qr_content}\n", FILE_APPEND);
                    } else {
                        // 检查是否需要添加二级域名
                        if (count(explode('.', $entry_domain)) <= 2) {
                            // 添加二级域名
                    $qr_content = 'http://' . $random_subdomain . '.' . $entry_domain . '/qr/' . $id;
                            file_put_contents($debug_log_file, "添加随机二级域名和前缀: {$qr_content}\n", FILE_APPEND);
                        } else {
                            // 已有二级域名，只添加前缀
                            $qr_content = 'http://' . $entry_domain . '/qr/' . $id;
                            file_put_contents($debug_log_file, "域名已有二级域名，添加前缀: {$qr_content}\n", FILE_APPEND);
                        }
                    }
                    
                    // 如果前端传递了完整的URL，则使用前端提供的URL
                    if (isset($_POST['full_entry_domain']) && !empty($_POST['full_entry_domain'])) {
                        // 检查前端提供的URL是否包含http前缀
                        if (!preg_match('#^https?://#i', $_POST['full_entry_domain'])) {
                            $qr_content = 'http://' . $_POST['full_entry_domain'] . '/qr/' . $id;
                        } else {
                        $qr_content = $_POST['full_entry_domain'] . '/qr/' . $id;
                        }
                        file_put_contents($debug_log_file, "使用前端提供的完整URL生成二维码: {$qr_content}\n", FILE_APPEND);
                    }
                    
                    $qr_file = 'qrcode/' . $id . '.png';
                    $qr_filepath = $qrcode_dir . '/' . $id . '.png';
                    
                    try {
                        // 使用phpqrcode库生成二维码图片
                        QRcode::png($qr_content, $qr_filepath, 'L', 10, 2);
                        
                        // 更新二维码图片地址
                        $DB->query("UPDATE dwz_qrcode SET qr_url = '{$qr_file}' WHERE id = '{$id}'");
                    } catch (Exception $e) {
                        file_put_contents($debug_log_file, "警告: 二维码生成失败: " . $e->getMessage() . "\n", FILE_APPEND);
                        // 继续执行不中断流程
                    }
                }
                
                // 插入数据表
                file_put_contents($debug_log_file, "开始处理跳转链接数据，数量: " . count($jump_urls) . "\n", FILE_APPEND);
                for ($i = 0; $i < count($jump_urls); $i++) {
                    $jump_url = daddslashes($jump_urls[$i]);
                    file_put_contents($debug_log_file, "处理第{$i}个链接: {$jump_url}\n", FILE_APPEND);
                    
                    if (empty($jump_url)) {
                        file_put_contents($debug_log_file, "跳过空链接\n", FILE_APPEND);
                        continue;
                    }
                    
                    $image_url = isset($image_urls[$i]) ? $image_urls[$i] : '';
                    // 图片数据可能很长，不记录到日志
                    $img_length = strlen($image_url);
                    file_put_contents($debug_log_file, "图片[{$i}]长度: {$img_length}字节\n", FILE_APPEND);
                    
                    $threshold = isset($thresholds[$i]) ? intval($thresholds[$i]) : 100;
                    
                    // 使用真实转义来处理Base64数据，避免直接将这种字符串放入SQL语句
                    // $image_url_escaped = $DB->real_escape_string($image_url);
                    
                    // 更安全的替代方案 - 使用daddslashes函数(已在common.php中定义)
                    $image_url_escaped = daddslashes($image_url);
                    
                    $sql = "INSERT INTO dwz_qrcode_data (qid, jump_url, image_url, threshold, sort, addtime) 
                            VALUES ('{$id}', '{$jump_url}', '{$image_url_escaped}', '{$threshold}', '{$i}', '{$date}')";
                    
                    // 不记录完整SQL，可能太大
                    file_put_contents($debug_log_file, "执行插入跳转URL SQL (不显示完整SQL以避免日志过大)\n", FILE_APPEND);
                    if (!$DB->query($sql)) {
                        file_put_contents($debug_log_file, "错误: 添加跳转地址失败: " . $DB->error() . "\n", FILE_APPEND);
                        throw new Exception("添加跳转地址失败: " . $DB->error());
                    }
                }
                // 提交事务
                file_put_contents($debug_log_file, "提交事务\n", FILE_APPEND);
                $DB->query("COMMIT");
                
                file_put_contents($debug_log_file, "保存成功，返回ID: {$id}\n", FILE_APPEND);
                exit(json_encode(array('code' => 0, 'msg' => '保存成功', 'id' => $id)));
                
            } catch (Exception $e) {
                // 回滚事务
                file_put_contents($debug_log_file, "事务出错，进行回滚: " . $e->getMessage() . "\n", FILE_APPEND);
                $DB->query("ROLLBACK");
                exit(json_encode(array('code' => -1, 'msg' => $e->getMessage())));
            }
        } catch (Exception $outer_e) {
            file_put_contents($debug_log_file, "外层异常: " . $outer_e->getMessage() . "\n", FILE_APPEND);
            exit(json_encode(array('code' => -1, 'msg' => '系统错误: ' . $outer_e->getMessage())));
        }
        break;
    
    case 'get_qr_info':
        $id = intval($_GET['id']);
        $qr = $DB->get_row("SELECT * FROM dwz_qrcode WHERE id='{$id}' AND uid='{$uid}'");
        
        if (!$qr) {
            exit(json_encode(array('code' => -1, 'msg' => '活码不存在或无权限查看')));
        }
        
        $data = array(
            'id' => $qr['id'],
            'name' => $qr['name'],
            'code' => $qr['code'],
            'entry_domain' => $qr['entry_domain'],
            'landing_domain' => $qr['landing_domain'],
            'jump_time' => $qr['jump_time'],
            'show_safe' => $qr['show_safe'],
            'threshold_type' => $qr['threshold_type'],
            'infinite_loop' => $qr['infinite_loop'],
            'qr_url' => $qr['qr_url'],
            'wechat_status' => $qr['wechat_status'],
            'state' => $qr['state'],
            'views' => $qr['views'],
            'ip_count' => $qr['ip_count'],
            'addtime' => $qr['addtime'],
            'updatetime' => $qr['updatetime'],
            'remarks' => $qr['remarks']
        );
        
        exit(json_encode(array('code' => 0, 'data' => $data)));
        break;

    // 获取积分充值套餐列表
    case 'get_points_packages':
        $sql = "SELECT * FROM `dwz_points_package` WHERE `status`='1' ORDER BY `sort` ASC, `id` ASC";
        $packages = array();
        $rs = $DB->query($sql);
        while ($res = $DB->fetch($rs)) {
            $packages[] = $res;
        }
        exit(json_encode(array('code' => 0, 'packages' => $packages)));
        break;

    // 积分充值支付下单
    case 'recharge_points':
        $package_id = intval($_GET['package_id']);
        $type = htmlspecialchars(strip_tags($_GET['type']));
        
        if (!in_array($type, ['alipay', 'wxpay', 'qqpay'])) {
            exit(json_encode(array('code' => -1, 'msg' => '支付类型不存在')));
        }
        
        // 查询套餐信息
        $package = $DB->get_row("SELECT * FROM `dwz_points_package` WHERE `id`='$package_id' AND `status`='1' LIMIT 1");
        if (!$package) {
            exit(json_encode(array('code' => -1, 'msg' => '套餐不存在或已下架')));
        }
        
        $trade_no = date("YmdHis") . rand(111, 999);
        $name = "积分充值 - " . $package['name'];
        $money = $package['price'];
        $points = $package['points_num'];
        
        $sql = "INSERT INTO `dwz_pay` (`trade_no`, `uid`, `num`, `money`, `ip`, `addtime`, `status`, `type`, `name`) VALUES (:trade_no, :uid, :num, :money, :ip, NOW(), 0, :type, :name)";
        $sth = $DB->prepare($sql);
        $sth->bindValue(':trade_no', $trade_no);
        $sth->bindValue(':uid', $uid);
        $sth->bindValue(':num', $points);
        $sth->bindValue(':money', $money);
        $sth->bindValue(':ip', $clientip);
        $sth->bindValue(':type', $type);
        $sth->bindValue(':name', $name);
        
        if (!$sth->execute()) {
            exit(json_encode(array('code' => -1, 'msg' => '创建订单失败，请稍后再试')));
        }
        
        exit(json_encode(array('code' => 0, 'trade_no' => $trade_no)));
        break;

    case 'delete_qr':
        $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
        
        if (!$id) {
            exit(json_encode(array('code' => -1, 'msg' => '参数错误')));
        }
        
        // 验证权限
        if (!$DB->get_row("SELECT id FROM dwz_qrcode WHERE id = '{$id}' AND uid = '{$uid}'")) {
            exit(json_encode(array('code' => -1, 'msg' => '活码不存在或已被删除')));
        }
        
        // 开启事务
        $DB->query("START TRANSACTION");
        
        try {
            // 删除主表
            if (!$DB->query("DELETE FROM dwz_qrcode WHERE id = '{$id}'")) {
                throw new Exception("删除活码失败");
            }
            
            // 删除数据表
            $DB->query("DELETE FROM dwz_qrcode_data WHERE qid = '{$id}'");
            
            // 提交事务
            $DB->query("COMMIT");
            
            exit(json_encode(array('code' => 0, 'msg' => '删除成功')));
            
        } catch (Exception $e) {
            // 回滚事务
            $DB->query("ROLLBACK");
            exit(json_encode(array('code' => -1, 'msg' => $e->getMessage())));
        }
        break;
    
    // 域名检测接口
    case 'check_domain':
        // 获取用户提交的域名数据
        $domains = isset($_POST['domains']) ? $_POST['domains'] : [];
        
        // 如果传入的是字符串，按行分割为数组
        if (!is_array($domains) && is_string($domains)) {
            $domains = explode("\n", trim($domains));
            // 过滤空行
            $domains = array_filter($domains, function($domain) {
                return !empty(trim($domain));
            });
        }
        
        // 验证域名数量
        if (empty($domains)) {
            exit(json_encode(['code' => -1, 'msg' => '请输入至少一个域名']));
        }
        
        if (count($domains) > 100) {
            exit(json_encode(['code' => -1, 'msg' => '一次最多检测100个域名']));
        }
        
        // 获取系统配置
        $check_api_key = $conf['check_api_key']; // 检测API密钥
        $check_points = $conf['check_points']; // 每次检测消耗积分
        $check_mode = $conf['check_mode']; // 检测模式：0会员模式，1积分模式
        
        // 验证API密钥是否配置
        if (empty($check_api_key)) {
            // 更友好的错误提示，不直接报错
            $results = [];
            foreach ($domains as $domain) {
                $domain = trim($domain);
                if (!empty($domain)) {
                    $results[$domain] = [
                        'domain' => $domain,
                        'status' => 'error',
                        'message' => '检测失败：系统未配置API密钥，请联系管理员'
                    ];
                }
            }
            
            exit(json_encode([
                'code' => 0,
                'msg' => '检测完成，但由于系统未配置API密钥，无法进行实际检测',
                'total' => count($domains),
                'success_count' => 0,
                'failed_count' => count($domains),
                'results' => array_values($results)
            ]));
        }
        
        // 根据检测模式验证权限
        if ($check_mode == 0) { // 会员模式
            if ($vip == 0) { // 非会员
                // 更友好的错误提示，不直接报错
                $results = [];
                foreach ($domains as $domain) {
                    $domain = trim($domain);
                    if (!empty($domain)) {
                        $results[$domain] = [
                            'domain' => $domain,
                            'status' => 'error',
                            'message' => '检测失败：您不是会员，无法使用域名检测功能'
                        ];
                    }
                }
                
                exit(json_encode([
                    'code' => 0,
                    'msg' => '检测完成，但由于您不是会员，无法使用域名检测功能',
                    'total' => count($domains),
                    'success_count' => 0,
                    'failed_count' => count($domains),
                    'results' => array_values($results),
                    'upgrade_tip' => '开通会员后可使用此功能'
                ]));
            }
        } else { // 积分模式
            // 计算所需总积分
            $total_points_needed = $check_points * count($domains);
            
            // 查询用户当前积分
            $user_points = $DB->get_row("SELECT points FROM dwz_user WHERE id='$uid' LIMIT 1");
            
            if (!$user_points || $user_points['points'] < $total_points_needed) {
                // 更友好的错误提示，不直接报错
                $results = [];
                foreach ($domains as $domain) {
                    $domain = trim($domain);
                    if (!empty($domain)) {
                        $results[$domain] = [
                            'domain' => $domain,
                            'status' => 'error',
                            'message' => '检测失败：积分不足'
                        ];
                    }
                }
                
                exit(json_encode([
                    'code' => 0,
                    'msg' => '检测完成，但由于积分不足无法进行实际检测',
                    'total' => count($domains),
                    'success_count' => 0,
                    'failed_count' => count($domains),
                    'results' => array_values($results),
                    'warning' => '积分不足，检测' . count($domains) . '个域名需要' . $total_points_needed . '积分，您当前积分：' . ($user_points ? $user_points['points'] : 0),
                    'recharge_tip' => '请充值积分后再试'
                ]));
            }
        }
        
        // 准备批量检测结果
        $results = [];
        $success_count = 0;
        $failed_count = 0;
        
        // API请求URL
        $api_url = "https://weixin.cadf.top/api/check_domain/api_check";
        
        // 批量检测域名
        foreach ($domains as $domain) {
            $domain = trim($domain);
            
            // 跳过空域名
            if (empty($domain)) {
                continue;
            }
            
            // 检测前记录到结果数组
            $results[$domain] = [
                'domain' => $domain,
                'status' => 'pending',
                'message' => '等待检测'
            ];
            
            try {
                // 准备请求数据
                $request_data = [
                    'key' => $check_api_key,
                    'domain' => $domain,
                    'type' => 1, // 默认类型为1
                ];
                
                // 发送API请求
                $options = [
                    'http' => [
                        'header' => "Content-type: application/json",
                        'method' => 'POST',
                        'content' => json_encode($request_data),
                        'timeout' => 15  // 设置超时时间为15秒
                    ]
                ];
                
                $context = stream_context_create($options);
                $result = @file_get_contents($api_url, false, $context);
                
                if ($result === false) {
                    // API请求失败
                    $failed_count++;
                    $results[$domain] = [
                        'domain' => $domain,
                        'status' => 'error',
                        'message' => '检测失败：API请求超时或网络问题'
                    ];
                    continue;
                }
                
                // 尝试解析JSON响应
                $response = json_decode($result, true);
                if ($response === null && json_last_error() !== JSON_ERROR_NONE) {
                    // JSON解析错误
                    $failed_count++;
                    $results[$domain] = [
                        'domain' => $domain,
                        'status' => 'error',
                        'message' => '检测失败：API返回的数据格式错误'
                    ];
                    continue;
                }
                
                // 修改解析逻辑：只要API返回了data字段就算检测成功
                if (isset($response['data'])) {
                    $success_count++;
                    
                    // 优先使用data.code作为HTTP状态码，如果不存在则根据data.status映射
                    if (isset($response['data']['code'])) {
                        $status_code = intval($response['data']['code']);
                    } else if (isset($response['data']['status'])) {
                        // 将API的status字段映射到HTTP状态码
                        $status = intval($response['data']['status']);
                        switch ($status) {
                            case 4:
                                $status_code = 403; // 已停止访问
                                break;
                            default:
                                $status_code = 0;
                        }
                    } else {
                        $status_code = 0;
                    }
                    
                    $status_message = '';
                    
                    // 解析状态码
                    switch ($status_code) {
                        case 200:
                            $status_message = '正常访问';
                            break;
                        case 400:
                            $status_message = '检测失败';
                            break;
                        case 401:
                            $status_message = '继续访问';
                            break;
                        case 402:
                            $status_message = '复制浏览器';
                            break;
                        case 403:
                            $status_message = '已停止访问';
                            break;
                        case 404:
                            $status_message = '即将前往以下网址（落地跳转拦截）';
                            break;
                        default:
                            $status_message = isset($response['data']['msg']) ? $response['data']['msg'] : '未知状态';
                    }
                    
                    $results[$domain] = [
                        'domain' => $domain,
                        'status' => 'success',
                        'status_code' => $status_code,
                        'message' => $status_message,
                        'raw_response' => isset($response['data']) ? $response['data'] : $response
                    ];
                } else {
                    $failed_count++;
                    // 检查是否有错误消息
                    $error_msg = isset($response['msg']) ? $response['msg'] : '检测失败，未知错误';
                    // 检查是否为密钥错误
                    if (stripos($error_msg, '密钥') !== false || stripos($error_msg, 'key') !== false) {
                        $error_msg = '检测失败：API密钥无效或已过期，请联系管理员';
                    }
                    
                    $results[$domain] = [
                        'domain' => $domain,
                        'status' => 'error',
                        'message' => $error_msg,
                        'raw_response' => $response
                    ];
                }
            } catch (Exception $e) {
                $failed_count++;
                $results[$domain] = [
                    'domain' => $domain,
                    'status' => 'error',
                    'message' => '检测异常：' . $e->getMessage()
                ];
            }
        }
        
        // 检测模式为积分模式且检测成功，扣除积分
        if ($check_mode == 1 && $success_count > 0) {
            // 重新查询用户当前积分，确保有足够的积分
            $user_points = $DB->get_row("SELECT points FROM dwz_user WHERE id='$uid' LIMIT 1");
            $points_to_deduct = $check_points * $success_count;
            
            if (!$user_points || $user_points['points'] < $points_to_deduct) {
                // 积分不足，不扣除并返回警告
                exit(json_encode([
                    'code' => 0,
                    'msg' => "检测完成，但积分不足无法扣除。成功：{$success_count}，失败：{$failed_count}",
                    'warning' => "积分不足，需要{$points_to_deduct}积分，您当前积分：" . ($user_points ? $user_points['points'] : 0),
                    'total' => count($domains),
                    'success_count' => $success_count,
                    'failed_count' => $failed_count,
                    'results' => array_values($results)
                ]));
            }
            
            // 扣除用户积分
            $DB->query("UPDATE dwz_user SET points = points - {$points_to_deduct} WHERE id = '{$uid}'");
            
            // 记录积分变动
            $description = "域名检测消耗积分，共检测{$success_count}个域名";
            $DB->query("INSERT INTO dwz_points_log (uid, points, type, description, addtime) 
                        VALUES ('{$uid}', '-{$points_to_deduct}', 'domain_check', '{$description}', '{$date}')");
            
            // 添加积分扣除成功的提示
            $points_msg = "，已扣除{$points_to_deduct}积分";
        } else {
            $points_msg = "";
        }
        
        // 返回检测结果
        exit(json_encode([
            'code' => 0,
            'msg' => "检测完成，成功：{$success_count}，失败：{$failed_count}" . $points_msg,
            'total' => count($domains),
            'success_count' => $success_count,
            'failed_count' => $failed_count,
            'results' => array_values($results) // 转为数值索引数组，避免JSON键名问题
        ]));
        break;
        
    case 'batch_delete_qr':
        $ids = isset($_POST['ids']) ? $_POST['ids'] : '';
        
        // 处理ids参数，支持数组或逗号分隔的字符串
        if (is_array($ids)) {
            // 已经是数组，保持不变
            $id_array = $ids;
        } else if (is_string($ids) && !empty($ids)) {
            // 逗号分隔的字符串，转换为数组
            $id_array = explode(',', $ids);
        } else {
            $id_array = array();
        }
        
        if (empty($id_array)) {
            exit(json_encode(array('code' => -1, 'msg' => '请选择要删除的活码')));
        }
        
        // 记录日志，辅助调试
        error_log("批量删除ID: " . print_r($id_array, true));
        
        // 验证权限
        $id_str = implode(',', array_map('intval', $id_array));
        $count = $DB->count("SELECT COUNT(*) FROM dwz_qrcode WHERE id IN ({$id_str}) AND uid = '{$uid}'");
        
        if ($count != count($id_array)) {
            exit(json_encode(array('code' => -1, 'msg' => '部分活码不存在或无权限操作')));
        }
        
        // 开启事务
        $DB->query("START TRANSACTION");
        
        try {
            // 删除主表
            if (!$DB->query("DELETE FROM dwz_qrcode WHERE id IN ({$id_str})")) {
                throw new Exception("批量删除活码失败");
            }
            
            // 删除数据表
            $DB->query("DELETE FROM dwz_qrcode_data WHERE qid IN ({$id_str})");
            
            // 提交事务
            $DB->query("COMMIT");
            
            exit(json_encode(array('code' => 0, 'msg' => '批量删除成功')));
            
        } catch (Exception $e) {
            // 回滚事务
            $DB->query("ROLLBACK");
            exit(json_encode(array('code' => -1, 'msg' => $e->getMessage())));
        }
        break;
    
    case 'update_qr_status':
        $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
        $field = isset($_POST['field']) ? $_POST['field'] : '';
        $value = isset($_POST['value']) ? intval($_POST['value']) : 0;
        
        if (!$id || !in_array($field, array('state', 'wechat_status'))) {
            exit(json_encode(array('code' => -1, 'msg' => '参数错误')));
        }
        
        // 验证权限
        if (!$DB->get_row("SELECT id FROM dwz_qrcode WHERE id = '{$id}' AND uid = '{$uid}'")) {
            exit(json_encode(array('code' => -1, 'msg' => '活码不存在或已被删除')));
        }
        
        // 更新状态
        if ($DB->query("UPDATE dwz_qrcode SET {$field} = '{$value}' WHERE id = '{$id}'")) {
            exit(json_encode(array('code' => 0, 'msg' => '更新成功')));
        } else {
            exit(json_encode(array('code' => -1, 'msg' => '更新失败')));
        }
        break;
    
    case 'get_qr_stats':
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        $type = isset($_GET['type']) ? $_GET['type'] : 'day';
        $start_date = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-d', strtotime('-7 days'));
        $end_date = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-d');
        
        if (!$id) {
            exit(json_encode(array('code' => -1, 'msg' => '参数错误')));
        }
        
        // 验证权限
        if (!$DB->get_row("SELECT id FROM dwz_qrcode WHERE id = '{$id}' AND uid = '{$uid}'")) {
            exit(json_encode(array('code' => -1, 'msg' => '活码不存在或已被删除')));
        }
        
        // 日期格式和分组
        $group_format = '';
        $date_format = '';
        
        switch ($type) {
            case 'day':
                $group_format = 'DATE(addtime)';
                $date_format = '%Y-%m-%d';
                break;
            case 'month':
                $group_format = 'DATE_FORMAT(addtime, "%Y-%m")';
                $date_format = '%Y-%m';
                break;
            case 'year':
                $group_format = 'YEAR(addtime)';
                $date_format = '%Y';
                break;
            default:
                $group_format = 'DATE(addtime)';
                $date_format = '%Y-%m-%d';
        }
        
        // 查询访问数据
        $sql = "SELECT 
                {$group_format} AS date,
                COUNT(*) AS views,
                COUNT(DISTINCT ip) AS unique_ips
                FROM dwz_qrcode_visit
                WHERE qid = '{$id}' AND DATE(addtime) BETWEEN '{$start_date}' AND '{$end_date}'
                GROUP BY {$group_format}
                ORDER BY date ASC";
        
        $rs = $DB->query($sql);
        
        $stats = array();
        while ($row = $DB->fetch($rs)) {
            $stats[] = array(
                'date' => $row['date'],
                'views' => $row['views'],
                'unique_ips' => $row['unique_ips']
            );
        }
        
        // 查询设备数据
        $device_sql = "SELECT 
                    device,
                    COUNT(*) AS count
                    FROM dwz_qrcode_visit
                    WHERE qid = '{$id}' AND DATE(addtime) BETWEEN '{$start_date}' AND '{$end_date}'
                    GROUP BY device
                    ORDER BY count DESC";
        
        $device_rs = $DB->query($device_sql);
        
        $devices = array();
        while ($row = $DB->fetch($device_rs)) {
            $devices[] = array(
                'name' => $row['device'] ? $row['device'] : '未知',
                'value' => $row['count']
            );
        }
        
        // 查询浏览器数据
        $browser_sql = "SELECT 
                    browser,
                    COUNT(*) AS count
                    FROM dwz_qrcode_visit
                    WHERE qid = '{$id}' AND DATE(addtime) BETWEEN '{$start_date}' AND '{$end_date}'
                    GROUP BY browser
                    ORDER BY count DESC";
        
        $browser_rs = $DB->query($browser_sql);
        
        $browsers = array();
        while ($row = $DB->fetch($browser_rs)) {
            $browsers[] = array(
                'name' => $row['browser'] ? $row['browser'] : '未知',
                'value' => $row['count']
            );
        }
        
        $result = array(
            'code' => 0,
            'data' => array(
                'stats' => $stats,
                'devices' => $devices,
                'browsers' => $browsers
            )
        );
        
        exit(json_encode($result));
        break;
    
    case 'get_qr_visit_logs':
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        $limit = isset($_GET['limit']) ? intval($_GET['limit']) : 20;
        
        if (!$id) {
            exit(json_encode(array('code' => -1, 'msg' => '参数错误')));
        }
        
        // 验证权限
        if (!$DB->get_row("SELECT id FROM dwz_qrcode WHERE id = '{$id}' AND uid = '{$uid}'")) {
            exit(json_encode(array('code' => -1, 'msg' => '活码不存在或已被删除')));
        }
        
        // 获取访问日志
        $sql = "SELECT * FROM dwz_qrcode_visit 
                WHERE qid = '{$id}'
                ORDER BY addtime DESC 
                LIMIT {$limit}";
        
        $rs = $DB->query($sql);
        
        $logs = array();
        while ($row = $DB->fetch($rs)) {
            $logs[] = array(
                'id' => $row['id'],
                'ip' => $row['ip'],
                'device' => $row['device'],
                'browser' => $row['browser'],
                'address' => $row['address'],
                'referer' => $row['referer'],
                'addtime' => $row['addtime']
            );
        }
        
        exit(json_encode(array('code' => 0, 'data' => $logs)));
        break;
    
    case 'export_qr_stats':
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        $type = isset($_GET['type']) ? $_GET['type'] : 'day';
        $start_date = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-d', strtotime('-7 days'));
        $end_date = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-d');
        
        if (!$id) {
            exit('参数错误');
        }
        
        // 验证权限
        $qr = $DB->get_row("SELECT * FROM dwz_qrcode WHERE id = '{$id}' AND uid = '{$uid}'");
        if (!$qr) {
            exit('活码不存在或已被删除');
        }
        
        // 日期格式和分组
        $group_format = '';
        $date_format = '';
        
        switch ($type) {
            case 'day':
                $group_format = 'DATE(addtime)';
                $date_format = '%Y-%m-%d';
                $title_type = '日';
                break;
            case 'month':
                $group_format = 'DATE_FORMAT(addtime, "%Y-%m")';
                $date_format = '%Y-%m';
                $title_type = '月';
                break;
            case 'year':
                $group_format = 'YEAR(addtime)';
                $date_format = '%Y';
                $title_type = '年';
                break;
            default:
                $group_format = 'DATE(addtime)';
                $date_format = '%Y-%m-%d';
                $title_type = '日';
        }
        
        // 查询访问数据
        $sql = "SELECT 
                {$group_format} AS date,
                COUNT(*) AS views,
                COUNT(DISTINCT ip) AS unique_ips
                FROM dwz_qrcode_visit
                WHERE qid = '{$id}' AND DATE(addtime) BETWEEN '{$start_date}' AND '{$end_date}'
                GROUP BY {$group_format}
                ORDER BY date ASC";
        
        $rs = $DB->query($sql);
        
        // 输出CSV文件头
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="活码统计_' . $qr['name'] . '_' . date('YmdHis') . '.csv"');
        
        // 创建输出流
        $output = fopen('php://output', 'w');
        
        // 写入BOM头，解决中文乱码问题
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
        
        // 写入CSV头
        fputcsv($output, array('日期', '访问量', '独立IP数'));
        
        // 写入数据
        while ($row = $DB->fetch($rs)) {
            fputcsv($output, array(
                $row['date'],
                $row['views'],
                $row['unique_ips']
            ));
        }
        
        fclose($output);
        exit;
        break;
    
    case 'get_week_stats':
        $startDate = date('Y-m-d', strtotime('-6 days'));
        $endDate = date('Y-m-d');
        
        // 查询所有活码访问数据统计
        $sql = "SELECT 
                DATE(addtime) AS date,
                COUNT(*) AS views,
                COUNT(DISTINCT ip) AS unique_ips
                FROM dwz_qrcode_visit
                WHERE qid IN (SELECT id FROM dwz_qrcode WHERE uid='$uid')
                AND DATE(addtime) BETWEEN '$startDate' AND '$endDate'
                GROUP BY DATE(addtime)
                ORDER BY date ASC";
        
        $rs = $DB->query($sql);
        
        $stats = array();
        while ($row = $DB->fetch($rs)) {
            $stats[] = array(
                'date' => $row['date'],
                'views' => $row['views'],
                'unique_ips' => $row['unique_ips']
            );
        }
        
        // 填充缺失的日期
        $allDates = array();
        $current = strtotime($startDate);
        $end = strtotime($endDate);
        
        while ($current <= $end) {
            $date = date('Y-m-d', $current);
            $found = false;
            
            foreach ($stats as $stat) {
                if ($stat['date'] == $date) {
                    $allDates[] = $stat;
                    $found = true;
                    break;
                }
            }
            
            if (!$found) {
                $allDates[] = array(
                    'date' => $date,
                    'views' => 0,
                    'unique_ips' => 0
                );
            }
            
            $current = strtotime('+1 day', $current);
        }
        
        exit(json_encode(array('code' => 0, 'data' => array('stats' => $allDates))));
        break;
    
    case 'check_wechat_status':
        // 微信屏蔽检测功能
        // 这里需要实现一个实际的检测逻辑，检测活码是否被微信屏蔽
        // 此处为示例，实际应根据具体检测方式实现
        
        $blocked = 0;
        $rs = $DB->query("SELECT id FROM dwz_qrcode WHERE uid='$uid'");
        
        while ($row = $DB->fetch($rs)) {
            $id = $row['id'];
            
            // 假设通过某种方式检测活码状态
            // 这里简单随机模拟，实际中应该有真实的检测逻辑
            $is_blocked = rand(0, 10) > 8 ? 1 : 0;
            
            // 如果检测到被屏蔽
            if ($is_blocked) {
                $DB->query("UPDATE dwz_qrcode SET wechat_status=0 WHERE id='$id'");
                $blocked++;
            } else {
                $DB->query("UPDATE dwz_qrcode SET wechat_status=1 WHERE id='$id'");
            }
        }
        
        exit(json_encode(array('code' => 0, 'blocked' => $blocked, 'msg' => '检测完成')));
        break;
    
    case 'batch_qr_operation':
        $type = isset($_POST['type']) ? $_POST['type'] : '';
        
        if (empty($type)) {
            exit(json_encode(array('code' => -1, 'msg' => '参数错误')));
        }
        
        switch ($type) {
            case 'wechat_check':
                // 微信屏蔽检测
                $blocked = 0;
                $rs = $DB->query("SELECT id FROM dwz_qrcode WHERE uid='$uid'");
                
                while ($row = $DB->fetch($rs)) {
                    $id = $row['id'];
                    
                    // 假设检测逻辑，实际中应有真实的检测方法
                    $is_blocked = rand(0, 10) > 8 ? 1 : 0;
                    
                    if ($is_blocked) {
                        $DB->query("UPDATE dwz_qrcode SET wechat_status=0 WHERE id='$id'");
                        $blocked++;
                    } else {
                        $DB->query("UPDATE dwz_qrcode SET wechat_status=1 WHERE id='$id'");
                    }
                }
                
                exit(json_encode(array('code' => 0, 'msg' => "检测完成，发现{$blocked}个活码被屏蔽")));
                break;
                
            case 'reset_data':
                // 重置访问数据
                $DB->query("UPDATE dwz_qrcode SET views=0, ip_count=0 WHERE uid='$uid'");
                $DB->query("DELETE FROM dwz_qrcode_visit WHERE qid IN (SELECT id FROM dwz_qrcode WHERE uid='$uid')");
                exit(json_encode(array('code' => 0, 'msg' => '访问数据已重置')));
                break;
                
            case 'enable_all':
                // 启用所有活码
                $DB->query("UPDATE dwz_qrcode SET state=1 WHERE uid='$uid'");
                exit(json_encode(array('code' => 0, 'msg' => '所有活码已启用')));
                break;
                
            case 'disable_all':
                // 禁用所有活码
                $DB->query("UPDATE dwz_qrcode SET state=0 WHERE uid='$uid'");
                exit(json_encode(array('code' => 0, 'msg' => '所有活码已禁用')));
                break;
                
            default:
                exit(json_encode(array('code' => -1, 'msg' => '未知操作类型')));
        }
        break;
    
    case 'get_today_hourly_stats':
        $today = date('Y-m-d');
        
        // 查询今日按小时统计的访问量
        $sql = "SELECT 
                HOUR(addtime) AS hour,
                COUNT(*) AS views
                FROM dwz_qrcode_visit
                WHERE qid IN (SELECT id FROM dwz_qrcode WHERE uid='$uid')
                AND DATE(addtime)='$today'
                GROUP BY HOUR(addtime)
                ORDER BY hour ASC";
        
        $rs = $DB->query($sql);
        
        $hours = array();
        while ($row = $DB->fetch($rs)) {
            $hours[] = array(
                'hour' => $row['hour'],
                'views' => $row['views']
            );
        }
        
        exit(json_encode(array('code' => 0, 'data' => array('hours' => $hours))));
        break;
    
    case 'get_today_top_qr':
        $today = date('Y-m-d');
        
        // 查询今日热门活码
        $sql = "SELECT 
                q.id,
                q.name,
                q.wechat_status,
                COUNT(v.id) AS today_views,
                COUNT(DISTINCT v.ip) AS today_ips
                FROM dwz_qrcode q
                JOIN dwz_qrcode_visit v ON q.id = v.qid
                WHERE q.uid='$uid' AND DATE(v.addtime)='$today'
                GROUP BY q.id
                ORDER BY today_views DESC
                LIMIT 10";
        
        $rs = $DB->query($sql);
        
        $qrcodes = array();
        while ($row = $DB->fetch($rs)) {
            $qrcodes[] = array(
                'id' => $row['id'],
                'name' => $row['name'],
                'wechat_status' => $row['wechat_status'],
                'today_views' => $row['today_views'],
                'today_ips' => $row['today_ips']
            );
        }
        
        exit(json_encode(array('code' => 0, 'data' => array('qrcodes' => $qrcodes))));
        break;
    
    case 'debug_check_data':
        // 调试接口，检查是否有数据
        error_reporting(E_ALL);
        ini_set('display_errors', 1);
        
        $debug_data = array(
            'uid' => $uid,
            'islogin2' => $islogin2,
            'direct_query' => array()
        );
        
        // 直接查询数据库
        $direct_result = $DB->query("SELECT * FROM dwz_qrcode LIMIT 10");
        $all_rows = array();
        while($row = $DB->fetch($direct_result)) {
            $all_rows[] = $row;
        }
        $debug_data['direct_query'] = $all_rows;
        
        // 检查用户数据
        $user_result = $DB->query("SELECT id, user FROM dwz_user WHERE id='$uid' LIMIT 1");
        $user_data = $DB->fetch($user_result);
        $debug_data['user_data'] = $user_data;
        
        // 检查session和cookie
        $debug_data['cookie'] = $_COOKIE;
        $debug_data['session'] = session_id();
        
        exit(json_encode($debug_data));
        break;
    
    case 'get_domains':
        // 获取域名列表API，支持获取入口域名或落地域名
        $type = isset($_GET['type']) ? trim($_GET['type']) : '';
        
        // 验证类型
        if (!in_array($type, ['entry', 'landing'])) {
            exit(json_encode(['code' => -1, 'msg' => '无效的域名类型']));
        }
        
        try {
            // 根据类型确定表名
            $table = ($type == 'entry') ? 'dwz_entry_domain' : 'dwz_landing_domain';
            
            // 先获取免费域名，状态为正常(state=1)
            $free_domains = [];
            $rs = $DB->query("SELECT id, domain FROM {$table} WHERE state=1 AND is_paid=0 ORDER BY id ASC");
            while ($row = $DB->fetch($rs)) {
                $free_domains[] = [
                    'id' => $row['id'],
                    'domain' => $row['domain']
                ];
            }
            
            // 再获取付费域名，状态为正常(state=1)，且属于当前用户
            $paid_domains = [];
            $rs = $DB->query("SELECT id, domain FROM {$table} WHERE state=1 AND is_paid=1 AND uid='{$uid}' ORDER BY id ASC");
            while ($row = $DB->fetch($rs)) {
                $paid_domains[] = [
                    'id' => $row['id'],
                    'domain' => $row['domain']
                ];
            }
            
            // 组合结果
            $result = [
                'code' => 0,
                'msg' => '获取成功',
                'data' => [
                    'free' => $free_domains,
                    'paid' => $paid_domains
                ]
            ];
            
            exit(json_encode($result));
        } catch (Exception $e) {
            exit(json_encode(['code' => -1, 'msg' => '获取域名列表失败：' . $e->getMessage()]));
        }
        break;

    // 获取商店中可购买的域名
    case 'get_store_domains':
        $type = isset($_GET['type']) ? trim($_GET['type']) : '';
        
        if (!in_array($type, ['entry', 'landing'])) {
            exit(json_encode(['code' => -1, 'msg' => '参数错误']));
        }
        
        $table = $type == 'entry' ? 'dwz_entry_domain' : 'dwz_landing_domain';
        
        // 查询付费且未绑定用户的域名（包括uid为NULL或0的域名）
        $domains = [];
        $rs = $DB->query("SELECT * FROM {$table} WHERE is_paid = 1 AND (uid IS NULL OR uid = 0) ORDER BY id DESC");
        while ($row = $DB->fetch($rs)) {
            $domains[] = [
                'id' => $row['id'],
                'domain' => $row['domain'],
                'qqsafe' => $row['qqsafe'],
                'wxsafe' => $row['wxsafe'],
                'price' => $row['price'],
                'remark' => $row['remark'],
                'addtime' => $row['addtime']
            ];
        }
        
        exit(json_encode(['code' => 0, 'domains' => $domains]));
        break;

    // 获取用户已购买的域名
    case 'get_my_domains':
        $domains = [];
        
        // 查询用户绑定的入口域名
        $rs = $DB->query("SELECT * FROM dwz_entry_domain WHERE uid = '{$uid}' ORDER BY id DESC");
        while ($row = $DB->fetch($rs)) {
            $domains[] = [
                'id' => $row['id'],
                'domain' => $row['domain'],
                'qqsafe' => $row['qqsafe'],
                'wxsafe' => $row['wxsafe'],
                'type' => 'entry',
                'addtime' => $row['addtime']
            ];
        }
        
        // 查询用户绑定的落地域名
        $rs = $DB->query("SELECT * FROM dwz_landing_domain WHERE uid = '{$uid}' ORDER BY id DESC");
        while ($row = $DB->fetch($rs)) {
            $domains[] = [
                'id' => $row['id'],
                'domain' => $row['domain'],
                'qqsafe' => $row['qqsafe'],
                'wxsafe' => $row['wxsafe'],
                'type' => 'landing',
                'addtime' => $row['addtime']
            ];
        }
        
        exit(json_encode(['code' => 0, 'domains' => $domains]));
        break;

    // 购买域名
    case 'buy_domain':
        $domain_id = isset($_POST['domain_id']) ? intval($_POST['domain_id']) : 0;
        $domain_type = isset($_POST['domain_type']) ? trim($_POST['domain_type']) : '';
        
        if (!$domain_id || !in_array($domain_type, ['entry', 'landing'])) {
            exit(json_encode(['code' => -1, 'msg' => '参数错误']));
        }
        
        $table = $domain_type == 'entry' ? 'dwz_entry_domain' : 'dwz_landing_domain';
        
        // 检查域名是否存在且未被绑定（uid为NULL或0）
        $domain = $DB->get_row("SELECT * FROM {$table} WHERE id = '{$domain_id}' AND is_paid = 1 AND (uid IS NULL OR uid = 0) LIMIT 1");
        if (!$domain) {
            exit(json_encode(['code' => -1, 'msg' => '域名不存在或已被购买']));
        }
        
        // 检查用户积分是否足够
        $points = $DB->get_row("SELECT points FROM dwz_user WHERE id = '{$uid}' LIMIT 1")['points'];
        if ($points < $domain['price']) {
            exit(json_encode(['code' => -1, 'msg' => '积分不足，请先充值积分']));
        }
        
        // 开始事务
        $DB->query("START TRANSACTION");
        
        try {
            // 扣除用户积分
            if (!$DB->query("UPDATE dwz_user SET points = points - {$domain['price']} WHERE id = '{$uid}'")) {
                throw new Exception("扣除积分失败");
            }
            
            // 记录积分消费记录
            $domain_name = $domain['domain'];
            $desc = "购买" . ($domain_type == 'entry' ? '入口' : '落地') . "域名：{$domain_name}";
            $addtime = date('Y-m-d H:i:s');
            
            if (!$DB->query("INSERT INTO dwz_points_log (uid, points, type, description, addtime) VALUES ('{$uid}', '-{$domain['price']}', 'buy_domain', '{$desc}', '{$addtime}')")) {
                throw new Exception("记录积分消费失败");
            }
            
            // 绑定域名到用户
            if (!$DB->query("UPDATE {$table} SET uid = '{$uid}' WHERE id = '{$domain_id}'")) {
                throw new Exception("绑定域名失败");
            }
            
            // 提交事务
            $DB->query("COMMIT");
            
            exit(json_encode(['code' => 0, 'msg' => '购买成功']));
            
        } catch (Exception $e) {
            // 回滚事务
            $DB->query("ROLLBACK");
            exit(json_encode(['code' => -1, 'msg' => $e->getMessage()]));
        }
        break;

    // 释放域名（解除绑定）
    case 'release_domain':
        $domain_id = isset($_POST['domain_id']) ? intval($_POST['domain_id']) : 0;
        $domain_type = isset($_POST['domain_type']) ? trim($_POST['domain_type']) : '';
        
        if (!$domain_id || !in_array($domain_type, ['entry', 'landing'])) {
            exit(json_encode(['code' => -1, 'msg' => '参数错误']));
        }
        
        $table = $domain_type == 'entry' ? 'dwz_entry_domain' : 'dwz_landing_domain';
        
        // 检查域名是否存在且属于当前用户
        $domain = $DB->get_row("SELECT * FROM {$table} WHERE id = '{$domain_id}' AND uid = '{$uid}' LIMIT 1");
        if (!$domain) {
            exit(json_encode(['code' => -1, 'msg' => '域名不存在或不属于您']));
        }
        
        // 检查域名是否正在被活码使用
        $field = $domain_type == 'entry' ? 'entry_domain' : 'landing_domain';
        $domain_name = $domain['domain'];
        $is_using = $DB->get_row("SELECT id FROM dwz_qrcode WHERE {$field} = '{$domain_name}' AND uid = '{$uid}' LIMIT 1");
        
        if ($is_using) {
            exit(json_encode(['code' => -1, 'msg' => '该域名正在被活码使用，请先修改相关活码']));
        }
        
        // 解除绑定（设置uid为0而不是NULL）
        if ($DB->query("UPDATE {$table} SET uid = 0 WHERE id = '{$domain_id}'")) {
            exit(json_encode(['code' => 0, 'msg' => '域名释放成功']));
        } else {
            exit(json_encode(['code' => -1, 'msg' => '域名释放失败: ' . $DB->error()]));
        }
        break;
    
    case 'copy_domain':
        $domain = daddslashes($_GET['domain']);
        exit(json_encode(array('code' => 0, 'domain' => $domain)));
        break;
        
    // 获取活码入口域名列表
    case 'get_entry_domains':
        // 查询系统配置的入口域名
        $sql = "SELECT * FROM dwz_entry_domain WHERE state=1 ORDER BY is_paid DESC";
        $result = $DB->query($sql);
        $domains = array();
        
        if ($result) {
            while($row = $DB->fetch($result)) {
                $domains[] = array(
                    'id' => $row['id'],
                    'domain' => $row['domain'],
                    'state' => $row['state'],
                    'qqsafe' => $row['qqsafe'],
                    'wxsafe' => $row['wxsafe'],
                    'is_paid' => $row['is_paid'],
                    'price' => $row['price'],
                    'remark' => $row['remark']
                );
            }
        }
        
        // 查询用户自己添加的入口域名（如果有）
        $user_domains_sql = "SELECT * FROM dwz_entry_domain WHERE uid='{$uid}' AND state=1";
        $user_result = $DB->query($user_domains_sql);
        
        if ($user_result) {
            while($row = $DB->fetch($user_result)) {
                // 检查是否已包含该域名
                $exists = false;
                foreach($domains as $domain) {
                    if ($domain['domain'] === $row['domain']) {
                        $exists = true;
                        break;
                    }
                }
                
                if (!$exists) {
                    $domains[] = array(
                        'id' => $row['id'],
                        'domain' => $row['domain'],
                        'state' => $row['state'],
                        'qqsafe' => $row['qqsafe'],
                        'wxsafe' => $row['wxsafe'],
                        'is_paid' => $row['is_paid'],
                        'price' => $row['price'],
                        'remark' => $row['remark']
                    );
                }
            }
        }
        
        exit(json_encode(array('code' => 0, 'data' => $domains)));
        break;
        
    // 生成二维码
    case 'generate_qrcode':
        $url = isset($_GET['url']) ? trim($_GET['url']) : '';
        
        if (empty($url)) {
            exit(json_encode(array('code' => -1, 'msg' => 'URL不能为空')));
        }
        
        // 检查URL格式
        if (!preg_match('#^https?://#i', $url)) {
            exit(json_encode(array('code' => -1, 'msg' => 'URL格式不正确')));
        }
        
        // 生成二维码图片
        require_once(dirname(__FILE__) . '/phpqrcode.php');
        
        // 创建临时文件
        $temp_dir = dirname(__FILE__) . '/temp';
        if (!file_exists($temp_dir)) {
            mkdir($temp_dir, 0755, true);
        }
        
        // 生成一个唯一的文件名
        $filename = 'temp_' . md5($url . time() . rand(1000, 9999)) . '.png';
        $filepath = $temp_dir . '/' . $filename;
        
        // 使用phpqrcode库生成二维码图片
        QRcode::png($url, $filepath, 'L', 10, 2);
        
        // 检查图片是否生成成功
        if (!file_exists($filepath)) {
            exit(json_encode(array('code' => -1, 'msg' => '二维码生成失败')));
        }
        
        // 返回图片URL
        $qr_url = './temp/' . $filename;
        exit(json_encode(array('code' => 0, 'data' => array('qr_img' => $qr_url))));
        break;
        
    // 获取短网址API列表
    case 'get_short_url_apis':
        // 查询所有可用的短网址API
        $sql = "SELECT * FROM dwz_api WHERE status=1 ORDER BY id ASC";
        $result = $DB->query($sql);
        $apis = array();
        
        if ($result) {
            while($row = $DB->fetch($result)) {
                $apis[] = array(
                    'id' => $row['id'],
                    'name' => $row['name'],
                    'type' => $row['type'],
                    'domain' => $row['domain'],
                    'num' => $row['num'] // 消耗的积分数
                );
            }
        }
        
        exit(json_encode(array('code' => 0, 'data' => $apis)));
        break;
        
    // 生成短网址
    case 'generate_short_url':
        $url = isset($_POST['url']) ? trim($_POST['url']) : '';
        $api_id = isset($_POST['api_id']) ? intval($_POST['api_id']) : 0;
        $remarks = isset($_POST['remarks']) ? trim($_POST['remarks']) : '';
        
        if (empty($url)) {
            exit(json_encode(array('code' => -1, 'msg' => 'URL不能为空')));
        }
        
        if ($api_id <= 0) {
            exit(json_encode(array('code' => -1, 'msg' => '请选择一个有效的短网址API')));
        }
        
        // 检查URL格式
        if (!preg_match('#^https?://#i', $url)) {
            exit(json_encode(array('code' => -1, 'msg' => 'URL格式不正确')));
        }
        
        // 查询API信息
        $api_info = $DB->get_row("SELECT * FROM dwz_api WHERE id='{$api_id}' AND status=1 LIMIT 1");
        if (!$api_info) {
            exit(json_encode(array('code' => -1, 'msg' => '所选API不存在或已禁用')));
        }
        
        // 检查用户积分是否足够
        if ($api_info['num'] > 0) {
            $user_info = $DB->get_row("SELECT * FROM dwz_user WHERE id='{$uid}' LIMIT 1");
            if ($user_info['points'] < $api_info['num']) {
                exit(json_encode(array('code' => -1, 'msg' => '您的积分不足，需要'.$api_info['num'].'积分')));
            }
        }
        
        // 根据API类型处理短网址生成
        $short_url = '';
        
        switch ($api_info['type']) {
            case 0: // 本站域名接口
                // 生成一个随机的短网址ID
                $short_id = getCode($conf['link_length']);
                
                // 检查ID是否已存在
                while ($DB->get_row("SELECT id FROM dwz_url WHERE id='{$short_id}' LIMIT 1")) {
                    $short_id = getCode($conf['link_length']);
                }
                
                // 插入数据库
                $domain = empty($api_info['domain']) ? $conf['domain'] : $api_info['domain'];
                
                // 检查域名是否已包含协议前缀
                if (preg_match('#^https?://#i', $domain)) {
                    // 已包含前缀，直接使用
                    $short_url = "{$domain}/f.{$short_id}";
                    $dwz = "{$domain}/f.{$short_id}";
                } else {
                    // 没有前缀，添加http://
                    $short_url = "http://{$domain}/f.{$short_id}";
                    $dwz = "http://{$domain}/f.{$short_id}";
                }
                
                $insert_sql = "INSERT INTO dwz_url (id, uid, addtime, url, dwz, ip, state, remarks, pattern, domain) 
                              VALUES ('{$short_id}', '{$uid}', NOW(), '".base64_encode($url)."', 
                              '{$dwz}', '".real_ip()."', 1, '{$remarks}', 1, '{$domain}')";
                
                if ($DB->query($insert_sql)) {
                    // 扣除积分
                    if ($api_info['num'] > 0) {
                        $DB->query("UPDATE dwz_user SET points=points-{$api_info['num']} WHERE id='{$uid}'");
                        
                        // 记录积分变动
                        $DB->query("INSERT INTO dwz_points_log (uid, points, type, description, addtime) 
                                    VALUES ('{$uid}', '-{$api_info['num']}', 'short_url', '生成短网址消耗积分', NOW())");
                    }
                } else {
                    exit(json_encode(array('code' => -1, 'msg' => '短网址生成失败，请重试')));
                }
                break;
                
            case 1: // 第三方接口
                // 使用第三方API生成短网址
                $api_url = $api_info['token'];
                
                // 如果token中包含{url}占位符，替换为实际URL
                $api_url = str_replace('{url}', urlencode($url), $api_url);
                
                // 调用API
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $api_url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_TIMEOUT, 10);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
                $response = curl_exec($ch);
                curl_close($ch);
                
                // 解析响应
                $response_data = json_decode($response, true);
                
                if (isset($response_data['url']) && !empty($response_data['url'])) {
                    $short_url = $response_data['url'];
                    
                    // 扣除积分
                    if ($api_info['num'] > 0) {
                        $DB->query("UPDATE dwz_user SET points=points-{$api_info['num']} WHERE id='{$uid}'");
                        
                        // 记录积分变动
                        $DB->query("INSERT INTO dwz_points_log (uid, points, type, description, addtime) 
                                    VALUES ('{$uid}', '-{$api_info['num']}', 'short_url', '生成短网址消耗积分', NOW())");
                    }
                } else {
                    exit(json_encode(array('code' => -1, 'msg' => '第三方API返回错误，请尝试其他接口')));
                }
                break;
                
            case 2: // 同程序接口
                // 构建API请求参数
                $api_params = array(
                    'url' => $url,
                    'key' => $api_info['keyname'],
                    'token' => $api_info['token']
                );
                
                // 构建请求URL
                $api_url = $api_info['domain'] . '/api.php?act=create';
                
                // 调用API
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $api_url);
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($api_params));
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_TIMEOUT, 10);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
                $response = curl_exec($ch);
                curl_close($ch);
                
                // 解析响应
                $response_data = json_decode($response, true);
                
                if (isset($response_data['code']) && $response_data['code'] == 0 && isset($response_data['url'])) {
                    $short_url = $response_data['url'];
                    
                    // 扣除积分
                    if ($api_info['num'] > 0) {
                        $DB->query("UPDATE dwz_user SET points=points-{$api_info['num']} WHERE id='{$uid}'");
                        
                        // 记录积分变动
                        $DB->query("INSERT INTO dwz_points_log (uid, points, type, description, addtime) 
                                    VALUES ('{$uid}', '-{$api_info['num']}', 'short_url', '生成短网址消耗积分', NOW())");
                    }
                } else {
                    exit(json_encode(array('code' => -1, 'msg' => '同程序API返回错误，请尝试其他接口')));
                }
                break;
                
            default:
                exit(json_encode(array('code' => -1, 'msg' => '不支持的API类型')));
        }
        
        if (!empty($short_url)) {
            exit(json_encode(array('code' => 0, 'data' => array('short_url' => $short_url))));
        } else {
            exit(json_encode(array('code' => -1, 'msg' => '短网址生成失败，请重试')));
        }
        break;
    
    case 'get_invite_code':
        // 查询用户是否已有邀请码
        $invite_code = $DB->get_row("SELECT * FROM dwz_invite_code WHERE uid='$uid' AND status=1 ORDER BY id DESC LIMIT 1");
        
        if (!$invite_code) {
            // 如果没有邀请码，则生成一个
            $new_code = strtoupper(substr(md5($uid.$userrow['user'].time().rand(1000,9999)), 0, 8));
            $DB->query("INSERT INTO dwz_invite_code(uid, code, addtime, status) VALUES('$uid', '$new_code', '$date', 1)");
            $code = $new_code;
        } else {
            $code = $invite_code['code'];
        }
        
        // 生成邀请链接
        $site_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https://" : "http://").$_SERVER['HTTP_HOST'];
        $invite_url = $site_url.'/user/reg.php?invite='.$code;
        
        exit(json_encode(array(
            'code' => 0, 
            'invite_code' => $code,
            'invite_url' => $invite_url
        )));
        break;
        
    case 'get_invite_stats':
        // 查询邀请人数
        $invite_count = $DB->count("SELECT COUNT(*) FROM dwz_invite_record WHERE inviter_uid='$uid'");
        
        // 查询邀请的用户信息
        $invited_users = array();
        $rs = $DB->query("SELECT r.*, u.user, u.name, u.img, u.addtime AS reg_time 
                           FROM dwz_invite_record r 
                           LEFT JOIN dwz_user u ON r.invited_uid = u.id 
                           WHERE r.inviter_uid='$uid' 
                           ORDER BY r.addtime DESC 
                           LIMIT 10");
        
        while ($row = $DB->fetch($rs)) {
            $invited_users[] = array(
                'user' => $row['user'],
                'name' => $row['name'],
                'avatar' => $row['img'],
                'invite_time' => $row['addtime'],
                'reg_time' => $row['reg_time']
            );
        }
        
        // 获取返现比例设置
        $consume_percent = isset($conf['invite_consume_percent']) ? floatval($conf['invite_consume_percent']) : 5;
        
        // 查询已获得的返现金额
        $reward_total = $DB->count("SELECT SUM(reward_amount) FROM dwz_consume_reward WHERE inviter_uid='$uid'");
        if (!$reward_total) $reward_total = 0;
        
        exit(json_encode(array(
            'code' => 0,
            'invite_count' => $invite_count,
            'invited_users' => $invited_users,
            'consume_percent' => $consume_percent,
            'reward_total' => $reward_total
        )));
        break;
    
    case 'getInviteCode':
        // 获取用户邀请码
        $invite_code_row = $DB->get_row("SELECT * FROM dwz_invite_code WHERE uid='$uid' AND state=1 ORDER BY id DESC LIMIT 1");
        if (!$invite_code_row) {
            // 检查是否有任何状态的邀请码存在
            $any_code_row = $DB->get_row("SELECT * FROM dwz_invite_code WHERE uid='$uid' ORDER BY id DESC LIMIT 1");
            
            if ($any_code_row) {
                // 用户已有邀请码，但状态不是启用状态
                $invite_code = $any_code_row['code'];
                // 如果邀请码状态为禁用，可以选择性地启用它
                if ($any_code_row['state'] == 0) {
                    $DB->query("UPDATE dwz_invite_code SET state=1 WHERE id='{$any_code_row['id']}'");
                }
                $result = array("code" => 0, "inviteCode" => $invite_code);
            } else {
                // 如果确实没有邀请码，则生成一个
                $new_code = strtoupper(substr(md5($uid.$userrow['user'].time().rand(1000,9999)), 0, 8));
                $res = $DB->query("INSERT INTO dwz_invite_code(uid, code, create_time, state, use_num) VALUES('$uid', '$new_code', '$date', 1, 0)");
                if ($res === false) {
                    error_log("创建邀请码失败: " . $DB->error());
                    // 再次检查是否有邀请码，以防在此过程中其他进程创建了邀请码
                    $final_check = $DB->get_row("SELECT * FROM dwz_invite_code WHERE uid='$uid' ORDER BY id DESC LIMIT 1");
                    $invite_code = $final_check ? $final_check['code'] : null;
                    
                    if ($invite_code) {
                        $result = array("code" => 0, "inviteCode" => $invite_code);
                    } else {
                        $result = array("code" => -1, "msg" => "生成邀请码失败");
                    }
                } else {
                    $result = array("code" => 0, "inviteCode" => $new_code);
                }
            }
        } else {
            $result = array("code" => 0, "inviteCode" => $invite_code_row['code']);
        }
        exit(json_encode($result));
        break;
    case 'getInviteRecords':
        // 获取用户邀请记录
        $rs = $DB->query("SELECT r.*, u.user as username 
                         FROM dwz_invite_record r 
                         LEFT JOIN dwz_user u ON r.invited_uid = u.id 
                         WHERE r.invite_uid='$uid' 
                         ORDER BY r.create_time DESC 
                         LIMIT 10");
        
        $records = array();
        while ($row = $DB->fetch($rs)) {
            $records[] = $row;
        }
        
        $result = array("code" => 0, "records" => $records);
        exit(json_encode($result));
        break;
      case 'getInviteConsumePercent':
        // 从数据库获取充值返现比例
        $percent = isset($conf['invite_consume_percent']) ? floatval($conf['invite_consume_percent']) : 5;
        $result = array("code" => 0, "percent" => $percent);
        exit(json_encode($result));
        break;
        
    case 'withdraw':
        if($islogin2 != 1){
            exit('{"code":-1,"msg":"未登录"}');
        }
        
        $amount = intval($_POST['amount']);
        $alipay_account = trim(htmlspecialchars($_POST['alipay_account']));
        $alipay_name = trim(htmlspecialchars($_POST['alipay_name']));
        
        if($amount < 1){
            exit('{"code":-1,"msg":"提现金额最低1积分"}');
        }
        
        if(empty($alipay_account) || empty($alipay_name)){
            exit('{"code":-1,"msg":"请填写完整的支付宝信息"}');
        }
        
        // 验证用户积分是否足够
        $user = $DB->get_row("SELECT points FROM dwz_user WHERE id='$uid' LIMIT 1");
        if(!$user || $user['points'] < $amount){
            exit('{"code":-1,"msg":"可用积分不足"}');
        }
        
        // 添加提现申请
        $sql = "INSERT INTO dwz_withdraw (uid, amount, alipay_account, alipay_name, create_time, status) 
                VALUES ('$uid', '$amount', '$alipay_account', '$alipay_name', '$date', 0)";
        
        if($DB->query($sql)){
            // 扣除用户积分
            $DB->query("UPDATE dwz_consume_reward SET reward_points=reward_points-$amount WHERE invite_uid='$uid'");
            
            // 记录积分变动
            $DB->query("INSERT INTO dwz_points_log (uid, points, type, description, addtime) 
                       VALUES ('$uid', -$amount, 'withdraw', '申请提现', '$date')");
            
            exit('{"code":0,"msg":"提现申请提交成功"}');
        } else {
            exit('{"code":-1,"msg":"提现申请提交失败'.$DB->error().'"}');
        }
        break;
    
    default:
        exit('{"code":-4,"msg":"No Act"}');
}
