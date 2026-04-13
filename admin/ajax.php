<?php
// 确保能显示所有错误，即使服务器配置禁用了错误显示
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

// 捕获PHP致命错误并以JSON形式输出
function fatal_error_handler() {
    $error = error_get_last();
    if ($error !== null && in_array($error['type'], array(E_ERROR, E_PARSE, E_COMPILE_ERROR, E_CORE_ERROR))) {
        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode(array(
            'code' => -1,
            'msg' => '致命错误: ' . $error['message'],
            'file' => $error['file'],
            'line' => $error['line'],
            'type' => $error['type']
        ));
        exit;
    }
}
register_shutdown_function('fatal_error_handler');

// 如果发生PHP异常，输出错误到日志文件
function log_error($message) {
    $log_file = dirname(__FILE__) . '/error_log.txt';
    $timestamp = date('Y-m-d H:i:s');
    $log_message = "[$timestamp] $message\n";
    error_log($log_message, 3, $log_file);
}

try {
    // 设置全局调试模式
    define('DEBUG_MODE', true); // 设置为true开启调试模式，false关闭

    // 如果开启调试模式，显示所有错误
    if (DEBUG_MODE) {
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);
    }

    // 以下是原始代码
    require '../includes/common.php';
    header('Content-Type: application/json; charset=UTF-8');

    if($islogin==1){}else exit(json_encode(array('code'=>-1,'msg'=>'未登录')));

    // 添加get_url和get_did函数定义
    function get_url($type, $id) {
        global $DB, $conf;
        $api = $DB->get_row("select * from dwz_api where keyname='$type' limit 1");
        if(!$api) return false;
        
        $path = $conf['htaccess'] == 0 ? 'tz.php?id='.$id : 'f.'.$id;
        return $api['domain'].'/'.$path;
    }

    function get_did($type) {
        $len = 6; // 默认ID长度
        $id = getCode($len); // 使用现有的getCode函数生成ID
        return $id;
    }

    // 设置错误处理，确保总是返回JSON响应
    function handleError($errno, $errstr, $errfile, $errline) {
        $error_type = 'UNKNOWN';
        switch ($errno) {
            case E_ERROR: $error_type = 'E_ERROR'; break;
            case E_WARNING: $error_type = 'E_WARNING'; break;
            case E_PARSE: $error_type = 'E_PARSE'; break;
            case E_NOTICE: $error_type = 'E_NOTICE'; break;
            case E_CORE_ERROR: $error_type = 'E_CORE_ERROR'; break;
            case E_CORE_WARNING: $error_type = 'E_CORE_WARNING'; break;
            case E_COMPILE_ERROR: $error_type = 'E_COMPILE_ERROR'; break;
            case E_COMPILE_WARNING: $error_type = 'E_COMPILE_WARNING'; break;
            case E_USER_ERROR: $error_type = 'E_USER_ERROR'; break;
            case E_USER_WARNING: $error_type = 'E_USER_WARNING'; break;
            case E_USER_NOTICE: $error_type = 'E_USER_NOTICE'; break;
            case E_STRICT: $error_type = 'E_STRICT'; break;
            case E_RECOVERABLE_ERROR: $error_type = 'E_RECOVERABLE_ERROR'; break;
            case E_DEPRECATED: $error_type = 'E_DEPRECATED'; break;
            case E_USER_DEPRECATED: $error_type = 'E_USER_DEPRECATED'; break;
        }
        
        error_log("PHP错误: [$error_type:$errno] $errstr in $errfile on line $errline");
        log_error("PHP错误: [$error_type:$errno] $errstr in $errfile on line $errline");
        
        $error_details = DEBUG_MODE ? "[$error_type:$errno] $errstr in $errfile on line $errline" : "服务器内部错误";
        
        if(!headers_sent()) {
            header('Content-Type: application/json; charset=UTF-8');
            echo json_encode(array(
                "code" => -1, 
                "msg" => $error_details,
                "debug" => DEBUG_MODE ? array(
                    "type" => $error_type,
                    "errno" => $errno,
                    "errstr" => $errstr,
                    "errfile" => $errfile,
                    "errline" => $errline,
                    "backtrace" => debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS)
                ) : null
            ));
        }
        return true; // 阻止PHP默认的错误处理
    }
    set_error_handler("handleError", E_ALL);

    // 确保任何未捕获的异常也被正确处理
    function handleException($e) {
        $trace = $e->getTraceAsString();
        error_log("未捕获异常: " . $e->getMessage() . "\n" . $trace);
        log_error("未捕获异常: " . $e->getMessage() . "\n" . $trace);
        
        $error_details = DEBUG_MODE ? $e->getMessage() . " in " . $e->getFile() . " on line " . $e->getLine() : "服务器异常";
        
        if(!headers_sent()) {
            header('Content-Type: application/json; charset=UTF-8');
            echo json_encode(array(
                "code" => -1, 
                "msg" => $error_details,
                "debug" => DEBUG_MODE ? array(
                    "message" => $e->getMessage(),
                    "code" => $e->getCode(),
                    "file" => $e->getFile(),
                    "line" => $e->getLine(),
                    "trace" => explode("\n", $trace)
                ) : null
            ));
        }
    }
    set_exception_handler("handleException");

    // 设置关闭时回调，确保在脚本意外终止时也返回响应
    register_shutdown_function(function() {
        $error = error_get_last();
        if($error !== null && in_array($error['type'], array(E_ERROR, E_PARSE, E_COMPILE_ERROR, E_CORE_ERROR))) {
            $error_type_map = array(
                E_ERROR => 'E_ERROR',
                E_PARSE => 'E_PARSE',
                E_COMPILE_ERROR => 'E_COMPILE_ERROR',
                E_CORE_ERROR => 'E_CORE_ERROR'
            );
            $error_type = isset($error_type_map[$error['type']]) ? $error_type_map[$error['type']] : 'FATAL_ERROR';
            
            error_log("致命错误: " . json_encode($error));
            log_error("致命错误: " . json_encode($error));
            
            $error_details = DEBUG_MODE ? "[$error_type] {$error['message']} in {$error['file']} on line {$error['line']}" : "服务器致命错误";
            
            if(!headers_sent()) {
                header('Content-Type: application/json; charset=UTF-8');
                echo json_encode(array(
                    "code" => -1, 
                    "msg" => $error_details,
                    "debug" => DEBUG_MODE ? $error : null
                ));
            }
        }
    });

    // 添加一个公共函数用于返回JSON格式的错误信息
    function returnError($message, $code = -1, $extra = array()) {
        $response = array_merge(array(
            "code" => $code,
            "msg" => $message
        ), $extra);
        
        if (DEBUG_MODE && isset($extra['debug_info'])) {
            $response['debug'] = $extra['debug_info'];
            unset($response['debug_info']);
        }
        
        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode($response);
        exit;
    }

    // 下面是域名处理相关函数，移动到switch语句前
    // 获取域名信息函数
    function getDomainInfo($id) {
        global $DB;
        
        if (empty($id) || !is_numeric($id)) {
            return array("code" => -1, "msg" => "域名ID无效");
        }
        
        try {
            // 查询域名信息
            $domain = $DB->get_row("select * from dwz_domain where id='$id' limit 1");
            
            if ($domain) {
                return array(
                    "code" => 0, 
                    "domain" => $domain['domain'],
                    "type" => $domain['type'],
                    "is_https" => $domain['is_https'],
                    "qqsafe" => $domain['qqsafe'],
                    "wxsafe" => $domain['wxsafe'],
                    "state" => $domain['state'],
                    "addtime" => $domain['addtime']
                );
            } else {
                return array("code" => -1, "msg" => "域名不存在");
            }
        } catch (Exception $e) {
            error_log("获取域名信息异常: " . $e->getMessage());
            return array("code" => -1, "msg" => "服务器处理异常: " . $e->getMessage());
        }
    }
    
    // 处理域名状态变更
    function setDomainState($id, $state) {
        global $DB;
        
        if (empty($id) || !is_numeric($id)) {
            return array("code" => -1, "msg" => "域名ID无效");
        }
        
        try {
            // 检查域名是否存在
            if ($DB->count("select count(id) from dwz_domain where id='$id'") == 0) {
                return array("code" => -1, "msg" => "域名不存在");
            }
            
            // 更新状态
            if ($DB->query("update dwz_domain set state='$state' where id='$id'")) {
                return array("code" => 0, "msg" => "状态更新成功");
            } else {
                return array("code" => -1, "msg" => "更新失败: " . $DB->error());
            }
        } catch (Exception $e) {
            error_log("设置域名状态异常: " . $e->getMessage());
            return array("code" => -1, "msg" => "服务器处理异常: " . $e->getMessage());
        }
    }
    
    function setDomainStateAll($ids, $state) {
        global $DB;
        
        if (empty($ids)) {
            return array("code" => -1, "msg" => "未选择域名");
        }
        
        try {
            // 处理ID列表
            $id_array = explode('&', $ids);
            $success = 0;
            
            foreach ($id_array as $id) {
                $id = intval($id);
                if ($id > 0 && $DB->query("update dwz_domain set state='$state' where id='$id'")) {
                    $success++;
                }
            }
            
            return array("code" => 0, "msg" => "成功处理 {$success} 个域名");
        } catch (Exception $e) {
            error_log("批量设置域名状态异常: " . $e->getMessage());
            return array("code" => -1, "msg" => "服务器处理异常: " . $e->getMessage());
        }
    }
    
    function delDomainAll($ids) {
        global $DB;
        
        if (empty($ids)) {
            return array("code" => -1, "msg" => "未选择域名");
        }
        
        try {
            // 处理ID列表
            $id_array = explode('&', $ids);
            $success = 0;
            
            foreach ($id_array as $id) {
                $id = intval($id);
                if ($id > 0 && $DB->query("delete from dwz_domain where id='$id'")) {
                    $success++;
                }
            }
            
            return array("code" => 0, "msg" => "成功删除 {$success} 个域名");
        } catch (Exception $e) {
            error_log("批量删除域名异常: " . $e->getMessage());
            return array("code" => -1, "msg" => "服务器处理异常: " . $e->getMessage());
        }
    }

    $act = isset($_GET['act']) ? daddslashes($_GET['act']) : null;

    @header('Content-Type: application/json; charset=UTF-8');
    switch ($act) {
        case 'withdraw_verify':
            $id = intval($_POST['id']);
            $status = intval($_POST['status']);
            $remark = trim($_POST['remark']);
            
            // 参数验证
            if($id <= 0){
                $result = array('code' => -1, 'msg' => '无效的提现记录ID');
                exit(json_encode($result));
            }
            if(!in_array($status, [1,2])){
                $result = array('code' => -1, 'msg' => '无效的审核状态');
                exit(json_encode($result));
            }
            // 参数验证
            if($remark == ''){
                $result = array('code' => -1, 'msg' => '审核理由拒绝为空！');
                exit(json_encode($result));
            }
            // 查询提现记录
            $withdraw = $DB->get_row("SELECT * FROM dwz_withdraw WHERE id='$id' LIMIT 1");
            if(!$withdraw){
                $result = array('code' => -1, 'msg' => '提现记录不存在');
                exit(json_encode($result));
            }
            if($withdraw['status'] != 0){
                $result = array('code' => -1, 'msg' => '该提现记录已处理');
                exit(json_encode($result));
            }
            
            // 获取当前时间的datetime格式
            $now = date('Y-m-d H:i:s');
            $sqluid = "SELECT uid,amount FROM dwz_withdraw WHERE id = '$id'";
            $resultuid = $DB->query($sqluid);
            $rowuid = $resultuid->fetch_assoc();
            $uid = $rowuid['uid'];
            $amount = $rowuid['amount'];
            if($status== 1){
               $sqls = $DB->query("UPDATE dwz_withdraw SET status=1,remark='$remark',process_time='$now' WHERE id='$id'"); 
               $result = array('code' => 1, 'msg' => '提现已通过！');
            }elseif($status== 2){
               $sqls = $DB->query("UPDATE dwz_withdraw SET status=2,remark='$remark',process_time='$now' WHERE id='$id'"); 
               $DB->query("UPDATE dwz_consume_reward SET reward_points=reward_points+$amount WHERE invite_uid='$uid'");
               $result = array('code' => 1, 'msg' => '提现已拒绝！');
            }
            exit(json_encode($result));
            break;
        case 'getcount':
            $thtime = date("Y-m-d") . ' 00:00:00';
            $count1 = $DB->count("select count(*) from dwz_user");
            $count2 = $DB->count("select count(*) from dwz_user where vip > '$date'");
            $count3 = $DB->count("select count(*) from dwz_pay where status=1");
            $count4 = $DB->count("select sum(money) from dwz_pay where status=1");
            $count5 = $DB->count("select count(*) from dwz_url");
            $count6 = $DB->count("select count(*) from dwz_url where pattern=1");
            $count7 = $DB->count("select count(*) from dwz_url where pattern=2");
            $count8 = $DB->count("select count(*) from dwz_url where pattern=3");
            $count9 = $DB->count("select count(*) from dwz_check where type=0");
            $count10 = $DB->count("select count(*) from dwz_check where type=1");

            $today = date("Y-m-d");
            $day1 = date("Y-m-d", strtotime("-1 day"));
            $day2 = date("Y-m-d", strtotime("-2 day"));
            $day3 = date("Y-m-d", strtotime("-3 day"));
            $day4 = date("Y-m-d", strtotime("-4 day"));
            $day5 = date("Y-m-d", strtotime("-5 day"));
            $day6 = date("Y-m-d", strtotime("-6 day"));

            $u1 = $DB->count("select count(1) from dwz_user where addtime>'$today'");
            $url1 = $DB->count("select count(1) from dwz_url where addtime>'$today'");
            $order1 = $DB->count("select count(1) from dwz_pay where status=1 and addtime>'$today'");
            $money1 = $DB->count("select sum(money) from dwz_pay where status=1 and addtime>'$today'");
            $check1 = $DB->count("select count(1) from dwz_check where addtime>'$today'");

            $u2 = $DB->count("select count(1) from dwz_user where addtime>'$day1' and addtime<'$today'");
            $url2 = $DB->count("select count(1) from dwz_url where addtime>'$day1' and addtime<'$today'");
            $order2 = $DB->count("select count(1) from dwz_pay where status=1 and addtime>'$day1' and addtime<'$today'");
            $money2 = $DB->count("select sum(money) from dwz_pay where status=1 and addtime>'$day1' and addtime<'$today'");
            $check2 = $DB->count("select count(1) from dwz_check where addtime>'$day1' and addtime<'$today'");

            $u3 = $DB->count("select count(1) from dwz_user where addtime>'$day2' and addtime<'$day1'");
            $url3 = $DB->count("select count(1) from dwz_url where addtime>'$day2' and addtime<'$day1'");
            $order3 = $DB->count("select count(1) from dwz_pay where status=1 and addtime>'$day2' and addtime<'$day1'");
            $money3 = $DB->count("select sum(money) from dwz_pay where status=1 and addtime>'$day2' and addtime<'$day1'");
            $check3 = $DB->count("select count(1) from dwz_check where addtime>'$day2' and addtime<'$day1'");

            $u4 = $DB->count("select count(1) from dwz_user where addtime>'$day3' and addtime<'$day2'");
            $url4 = $DB->count("select count(1) from dwz_url where addtime>'$day3' and addtime<'$day2'");
            $order4 = $DB->count("select count(1) from dwz_pay where status=1 and addtime>'$day3' and addtime<'$day2'");
            $money4 = $DB->count("select sum(money) from dwz_pay where status=1 and addtime>'$day3' and addtime<'$day2'");
            $check4 = $DB->count("select count(1) from dwz_check where addtime>'$day3' and addtime<'$day2'");

            $u5 = $DB->count("select count(1) from dwz_user where addtime>'$day4' and addtime<'$day3'");
            $url5 = $DB->count("select count(1) from dwz_url where addtime>'$day4' and addtime<'$day3'");
            $order5 = $DB->count("select count(1) from dwz_pay where status=1 and addtime>'$day4' and addtime<'$day3'");
            $money5 = $DB->count("select sum(money) from dwz_pay where status=1 and addtime>'$day4' and addtime<'$day3'");
            $check5 = $DB->count("select count(1) from dwz_check where addtime>'$day4' and addtime<'$day3'");

            $u6 = $DB->count("select count(1) from dwz_user where addtime>'$day5' and addtime<'$day4'");
            $url6 = $DB->count("select count(1) from dwz_url where addtime>'$day5' and addtime<'$day4'");
            $order6 = $DB->count("select count(1) from dwz_pay where status=1 and addtime>'$day5' and addtime<'$day4'");
            $money6 = $DB->count("select sum(money) from dwz_pay where status=1 and addtime>'$day5' and addtime<'$day4'");
            $check6 = $DB->count("select count(1) from dwz_check where addtime>'$day5' and addtime<'$day4'");

            $u7 = $DB->count("select count(1) from dwz_user where addtime>'$day6' and addtime<'$day5'");
            $url7 = $DB->count("select count(1) from dwz_url where addtime>'$day6' and addtime<'$day5'");
            $order7 = $DB->count("select count(1) from dwz_pay where status=1 and addtime>'$day6' and addtime<'$day5'");
            $money7 = $DB->count("select sum(money) from dwz_pay where status=1 and addtime>'$day6' and addtime<'$day5'");
            $check7 = $DB->count("select count(1) from dwz_check where addtime>'$day6' and addtime<'$day5'");

            $chart = array(
                "user" => array($u1, $u2, $u3, $u4, $u5, $u6, $u7),
                "url" => array($url1, $url2, $url3, $url4, $url5, $url6, $url7),
                "order" => array($order1, $order2, $order3, $order4, $order5, $order6, $order7),
                "money" => array(round($money1, 2), round($money2, 2), round($money3, 2), round($money4, 2), round($money5, 2), round($money6, 2), round($money7, 2)),
                "check" => array($check1, $check2, $check3, $check4, $check5, $check6, $check7),
            );
            $result = array("code" => 0, "count1" => $count1, "count2" => $count2, "count3" => $count3, "count4" => round($count4, 2), "count5" => $count5, "count6" => $count6, "count7" => $count7, "count8" => $count8, "count9" => $count9, "count10" => $count10, "chart" => $chart);
            exit(json_encode($result));
            break;
        case 'getHotUrl':
            $rs = $DB->query("select * from dwz_url where deltime is null order by view desc limit 5");
            $i = 0;
            $rows = array();
            while ($res = $DB->fetch($rs)) {
                $rows[$i] = array(
                    'dwz' => $res['dwz'], 'view' => $res['view'], 'addtime' => $res['addtime'], 'lasttime' => $res['lasttime']
                );
                $i++;
            }
            break;
        case 'update':
            $result = array("code" => -1, "msg" => "不支持");
            exit(json_encode($result));
            exit();
        case 'editAdmin':
            $admin_user = trim($_REQUEST['admin_user']);
            $pwd_old = trim($_REQUEST['pwd_old']);
            $pwd_new = trim($_REQUEST['pwd_new']);
            $pwd_new2 = trim($_REQUEST['pwd_new2']);
            if ($admin_user == '') {
                $msg = '用户名不能为空！';
                $result = array("code" => -1, "msg" => $msg);
                exit(json_encode($result));
            }
            saveSetting('admin_user', $admin_user);
            if (!empty($pwd_new) && !empty($pwd_new2)) {
                if ($pwd_old != $conf['admin_pwd']) {
                    $msg = '旧密码不正确！';
                    $result = array("code" => -1, "msg" => $msg);
                    exit(json_encode($result));
                }
                if ($pwd_new != $pwd_new2) {
                    $msg = '俩次输入的密码不一致！';
                    $result = array("code" => -1, "msg" => $msg);
                    exit(json_encode($result));
                }
                saveSetting('admin_pwd', $pwd_new);
            }
            $ad = $CACHE->clear();
            if ($ad) {
                $result = array("code" => 0, "msg" => "修改成功");
            } else {
                $msg = '修改失败';
                $result = array("code" => -1, "msg" => $msg);
            }
            exit(json_encode($result));
            break;
        case 'cleanCache':
            $CACHE->clear();
            $result = array('code' => 0, 'msg' => '清理成功');
            exit(json_encode($result));
            break;
        case 'ulist':
            $callback = $_GET['callback'];
            $kw = isset($_GET['kw']) ? $_GET['kw'] : '';
            if ($kw != '') {
                $sql = " (id like '%$kw%' or user like '%$kw%' or qq like '%$kw%' or mail like '%$kw%' or addip like '%$kw%' or lastip like '%$kw%')";
            } else {
                $sql = "1";
            }
            $totle = $DB->count("select count(*) from dwz_user where({$sql})");
            $pagesize = $_GET['limit'];
            $pages = ceil($totle / $pagesize);
            $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
            $offset = $_GET['offset'];
            $sort = isset($_GET['sort']) ? $_GET['sort'] : 'id';
            $sortOrder = $_GET['sortOrder'];
            $rs = $DB->query("select * from dwz_user where({$sql}) order by $sort $sortOrder limit $offset,$pagesize");
            $i = 0;
            $rows = array();
            while ($res = $DB->fetch($rs)) {
                $rows[$i] = array(
                    'id' => $res['id'], 'user' => $res['user'], 'addtime' => $res['addtime'], 'vip' => $res['vip'],
                    'qq' => $res['qq'], 'state' => $res['state'], 'lasttime' => $res['lasttime'], 'mail' => $res['mail'],
                    'token' => $res['token'], 'check_num' => isset($res['check_num']) ? $res['check_num'] : null, 'addip' => $res['addip'],
                    'lastip' => $res['lastip'],'points' => $res['points']
                );
                $i++;
            }
            $result = array('rows' => $rows, 'total' => $totle);
            exit($callback . '(' . json_encode($result) . ')');
            break;
        case 'dlist':
            $callback = $_GET['callback'];
            $kw = isset($_GET['kw']) ? $_GET['kw'] : '';
            if ($kw != '') {
                $sql = " (id like '%$kw%' or domain like '%$kw%')";
            } else {
                $sql = "1";
            }
            $totle = $DB->count("select count(*) from dwz_domain where({$sql})");
            $pagesize = $_GET['limit'];
            $pages = ceil($totle / $pagesize);
            $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
            $offset = $_GET['offset'];
            $sort = isset($_GET['sort']) ? $_GET['sort'] : 'id';
            $sortOrder = $_GET['sortOrder'];
            $rs = $DB->query("select * from dwz_domain where({$sql}) order by $sort $sortOrder limit $offset,$pagesize");
            $i = 0;
            $rows = array();
            while ($res = $DB->fetch($rs)) {
                $rows[$i] = array(
                    'id' => $res['id'], 'domain' => $res['domain'], 'addtime' => $res['addtime'], 'type' => $res['type'],
                    'qqsafe' => $res['qqsafe'], 'state' => $res['state'], 'wxsafe' => $res['wxsafe'], 'dysafe' => $res['dysafe'],'is_https' => $res['is_https']
                );
                $i++;
            }
            $result = array('rows' => $rows, 'total' => $totle);
            exit($callback . '(' . json_encode($result) . ')');
            break;
        case 'urllist':
            $callback = $_GET['callback'];
            $kw = isset($_GET['kw']) ? $_GET['kw'] : '';
            if ($kw != '') {
                $sql = " (url like '%$kw%' or dwz like '%$kw%' or id like '%$kw%' or uid like '%$kw%' or remarks like '%$kw%' or ip like '%$kw%')";
            } else {
                $sql = "1";
            }
            $totle = $DB->count("select count(*) from dwz_url where({$sql} and deltime is null)");
            $pagesize = $_GET['limit'];
            $pages = ceil($totle / $pagesize);
            $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
            $offset = $_GET['offset'];
            $sort = isset($_GET['sort']) ? $_GET['sort'] : 'addtime';
            $sortOrder = $_GET['sortOrder'];
            $rs = $DB->query("select * from dwz_url where({$sql} and deltime is null) order by $sort $sortOrder limit $offset,$pagesize");
            $i = 0;
            $rows = array();
            while ($res = $DB->fetch($rs)) {
                $rows[$i] = array(
                    'id' => $res['id'], 'uid' => $res['uid'], 'dwz' => $res['dwz'], 'state' => $res['state'], 'lasttime' => $res['lasttime'],
                    'remarks' => $res['remarks'], 'view' => $res['view'], 'pattern' => $res['pattern'], 'addtime' => $res['addtime'],
                    'url' => base64_decode($res['url']), 'qqjump' => $res['qqjump'], 'wxjump' => $res['wxjump'], 'alijump' =>$res['alijump'], 'ip' => $res['ip']
                );
                $i++;
            }
            $result = array('rows' => $rows, 'total' => $totle);
            exit($callback . '(' . json_encode($result) . ')');
            break;
        case 'urldel':
            $callback = $_GET['callback'];
            $kw = isset($_GET['kw']) ? $_GET['kw'] : '';
            if ($kw != '') {
                $sql = " (id like '%$kw%' or uid like '%$kw%' or url like '%$kw%' or remarks like '%$kw%' or dwz like '%$kw%' or ip like '%$kw%')";
            } else {
                $sql = "1";
            }
            $totle = $DB->count("select count(*) from dwz_url where({$sql} and deltime is not null)");
            $pagesize = $_GET['limit'];
            $pages = ceil($totle / $pagesize);
            $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
            $offset = $_GET['offset'];
            $sort = isset($_GET['sort']) ? $_GET['sort'] : 'deltime';
            $sortOrder = $_GET['sortOrder'];
            $rs = $DB->query("select * from dwz_url where({$sql} and deltime is not null) order by $sort $sortOrder limit $offset,$pagesize");
            $i = 0;
            $rows = array();
            while ($res = $DB->fetch($rs)) {
                $rows[$i] = array(
                    'id' => $res['id'], 'uid' => $res['uid'], 'dwz' => $res['dwz'], 'state' => $res['state'], 'lasttime' => $res['lasttime'],
                    'remarks' => $res['remarks'], 'view' => $res['view'], 'pattern' => $res['pattern'], 'deltime' => $res['deltime'],
                    'url' => base64_decode($res['url']), 'qqjump' => $res['qqjump'], 'wxjump' => $res['wxjump'], 'alijump' => $res['alijump'], 'ip' => $res['ip']
                );
                $i++;
            }
            $result = array('rows' => $rows, 'total' => $totle);
            exit($callback . '(' . json_encode($result) . ')');
            break;
        case 'urlmodify':
            $callback = $_GET['callback'];
            $kw = isset($_GET['kw']) ? $_GET['kw'] : '';
            if ($kw != '') {
                $sql = " (id like '%$kw%' or uid like '%$kw%' or dwz like '%$kw%' or url1 like '%$kw%' or url2 like '%$kw%')";
            } else {
                $sql = "1";
            }
            $totle = $DB->count("select count(*) from dwz_modify where({$sql})");
            $pagesize = $_GET['limit'];
            $pages = ceil($totle / $pagesize);
            $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
            $offset = $_GET['offset'];
            $sort = isset($_GET['sort']) ? $_GET['sort'] : 'id';
            $sortOrder = $_GET['sortOrder'];
            $rs = $DB->query("select * from dwz_modify where({$sql}) order by $sort $sortOrder limit $offset,$pagesize");
            $i = 0;
            $rows = array();
            while ($res = $DB->fetch($rs)) {
                $rows[$i] = array(
                    'id' => $res['id'], 'uid' => $res['uid'], 'urlid' => $res['urlid'], 'url1' => base64_decode($res['url1']),
                    'url2' => base64_decode($res['url2']), 'dwz' => $res['dwz'], 'addtime' => $res['addtime']
                );
                $i++;
            }
            $result = array('rows' => $rows, 'total' => $totle);
            exit($callback . '(' . json_encode($result) . ')');
            break;
        case 'urlcheck':
            $callback = $_GET['callback'];
            $kw = isset($_GET['kw']) ? $_GET['kw'] : '';
            if ($kw != '') {
                $sql = " (id like '%$kw%' or uid like '%$kw%' or url like '%$kw%')";
            } else {
                $sql = "1";
            }
            $totle = $DB->count("select count(*) from dwz_check where({$sql})");
            $pagesize = $_GET['limit'];
            $pages = ceil($totle / $pagesize);
            $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
            $offset = $_GET['offset'];
            $sort = isset($_GET['sort']) ? $_GET['sort'] : 'id';
            $sortOrder = $_GET['sortOrder'];
            $rs = $DB->query("select * from dwz_check where({$sql}) order by $sort $sortOrder limit $offset,$pagesize");
            $i = 0;
            $rows = array();
            while ($res = $DB->fetch($rs)) {
                $rows[$i] = array(
                    'id' => $res['id'], 'uid' => $res['uid'], 'url' => base64_decode($res['url']), 'type' => $res['type'],
                    'switch' => $res['switch'], 'status' => $res['status'], 'pl' => $res['pl'],
                    'num' => $res['num'], 'lasttime' => $res['lasttime'], 'addtime' => $res['addtime']
                );
                $i++;
            }
            $result = array('rows' => $rows, 'total' => $totle);
            exit($callback . '(' . json_encode($result) . ')');
            break;
        case 'blacklist':
            $callback = $_GET['callback'];
            $kw = isset($_GET['kw']) ? $_GET['kw'] : '';
            if ($kw != '') {
                $sql = " (id like '%$kw%' or type like '%$kw%' or content like '%$kw%')";
            } else {
                $sql = "1";
            }
            $totle = $DB->count("select count(*) from dwz_black where({$sql})");
            $pagesize = $_GET['limit'];
            $pages = ceil($totle / $pagesize);
            $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
            $offset = $_GET['offset'];
            $sort = isset($_GET['sort']) ? $_GET['sort'] : 'id';
            $sortOrder = $_GET['sortOrder'];
            $rs = $DB->query("select * from dwz_black where({$sql}) order by $sort $sortOrder limit $offset,$pagesize");
            $i = 0;
            $rows = array();
            while ($res = $DB->fetch($rs)) {
                $rows[$i] = array(
                    'id' => $res['id'], 'type' => $res['type'], 'content' => $res['content'], 'addtime' => $res['addtime']
                );
                $i++;
            }
            $result = array('rows' => $rows, 'total' => $totle);
            exit($callback . '(' . json_encode($result) . ')');
            break;
        case 'record':
            $callback = $_GET['callback'];
            $kw = isset($_GET['kw']) ? $_GET['kw'] : '';
            if ($kw != '') {
                $sql = " (id like '%$kw%' or uid like '%$kw%' or bz like '%$kw%')";
            } else {
                $sql = "1";
            }
            $totle = $DB->count("select count(*) from dwz_points where({$sql})");
            $pagesize = $_GET['limit'];
            $pages = ceil($totle / $pagesize);
            $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
            $offset = $_GET['offset'];
            $sort = isset($_GET['sort']) ? $_GET['sort'] : 'id';
            $sortOrder = $_GET['sortOrder'];
            $rs = $DB->query("select * from dwz_points where({$sql}) order by $sort $sortOrder limit $offset,$pagesize");
            $i = 0;
            $rows = array();
            while ($res = $DB->fetch($rs)) {
                $rows[$i] = array(
                    'id' => $res['id'], 'uid' => $res['uid'], 'action' => $res['action'], 'number' => $res['number'],
                    'point' => $res['point'], 'bz' => $res['bz'], 'addtime' => $res['addtime']
                );
                $i++;
            }
            $result = array('rows' => $rows, 'total' => $totle);
            exit($callback . '(' . json_encode($result) . ')');
            break;
        case 'kmlist':
            $callback = $_GET['callback'];
            $kw = isset($_GET['kw']) ? $_GET['kw'] : '';
            if ($kw != '') {
                $sql = " (id like '%$kw%' or km like '%$kw%' or uid like '%$kw%' or days like '%$kw%')";
            } else {
                $sql = "1";
            }
            $totle = $DB->count("select count(*) from dwz_km where({$sql})");
            $pagesize = $_GET['limit'];
            $pages = ceil($totle / $pagesize);
            $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
            $offset = $_GET['offset'];
            $sort = isset($_GET['sort']) ? $_GET['sort'] : 'addtime';
            $sortOrder = $_GET['sortOrder'];
            $rs = $DB->query("select * from dwz_km where({$sql}) order by $sort $sortOrder limit $offset,$pagesize");
            $i = 0;
            $rows = array();
            while ($res = $DB->fetch($rs)) {
                $rows[$i] = array(
                    'id' => $res['id'], 'km' => $res['km'], 'status' => $res['status'], 'usetime' => $res['usetime'],
                    'days' => $res['days'], 'uid' => $res['uid'], 'addtime' => $res['addtime'], 'type' => $res['type']
                );
                $i++;
            }
            $result = array('rows' => $rows, 'total' => $totle);
            exit($callback . '(' . json_encode($result) . ')');
            break;
        case 'addUser':
            $user = trim($_POST['user']);
            $pwd = trim($_POST['pwd']);
            $vip = trim($_POST['vip']);
            $qq = trim($_POST['qq']);
            if ($user == '' || $pwd == '' || $vip == '' || $qq == '') {
                $result = array("code" => -1, "msg" => "请确保所有选项不为空");
                exit(json_encode($result));
            }
            if ($DB->count("select count(id) from dwz_user where user='$user'") > 0) {
                $result = array("code" => -1, "msg" => "用户名已存在");
                exit(json_encode($result));
            }
            if ($DB->count("select count(id) from dwz_user where qq='$qq'") > 0) {
                $result = array("code" => -1, "msg" => "该QQ已被注册");
                exit(json_encode($result));
            }
            
            // 获取QQ信息并安全处理返回值
            $qqInfo = qq_img($qq);
            // 确保$qqInfo是数组类型
            if (!is_array($qqInfo)) {
                $qqInfo = array(); // 如果不是数组，初始化为空数组
            }
            
            // 安全获取数组值
            $name = isset($qqInfo['name']) ? $qqInfo['name'] : '';
            $img = isset($qqInfo['imgurl']) ? $qqInfo['imgurl'] : '';
            
            if (empty($name)) {
                $name = '空白昵称';
            }
            
            if (empty($img)) {
                $img = 'https://q.qlogo.cn/headimg_dl?dst_uin=123456&spec=100';
            }
            
            $mail = $qq . '@qq.com';
            $ip = real_ip();
            $pwd_hash = password_hash($pwd, PASSWORD_DEFAULT);
            if (!$pwd_hash) {
                $result = array("code" => -1, "msg" => "密码加密失败");
                exit(json_encode($result));
            }
            $pwd_hash = daddslashes($pwd_hash);
            $rs = $DB->query("insert into dwz_user(user,pwd,vip,addtime,qq,mail,name,img,addip) values('$user','$pwd_hash','$vip','$date','$qq','$mail','$name','$img','$ip')");
            if ($rs) {
                $result = array("code" => 0, "msg" => "添加成功");
                exit(json_encode($result));
            } else {
                $result = array("code" => -1, "msg" => "添加失败");
                exit(json_encode($result));
            }
            break;
        case 'addDomain':
            // 域名添加接口
            try {
                // 记录所有请求参数，用于调试
                error_log("域名添加请求原始参数: " . json_encode($_POST));
                
                // 确保这里不会出现未定义变量错误
                $domain = isset($_POST['domain']) ? trim($_POST['domain']) : '';
                if(isset($_POST['add_domain']) && !empty($_POST['add_domain'])) {
                    // 兼容新表单字段
                    $domain = trim($_POST['add_domain']);
                }
                
                // 特殊处理type参数
                $type = isset($_POST['type']) ? $_POST['type'] : '';
                
                $is_https = isset($_POST['is_https']) ? intval($_POST['is_https']) : 0;
                $qqsafe = isset($_POST['qqsafe']) ? intval($_POST['qqsafe']) : 1;
                $wxsafe = isset($_POST['wxsafe']) ? intval($_POST['wxsafe']) : 1;
                $state = isset($_POST['state']) ? intval($_POST['state']) : 1;
                
                // 记录处理后的请求数据
                error_log("添加域名处理后参数: domain=$domain, type=$type, is_https=$is_https");
                
                if (empty($domain)) {
                    exit(json_encode(array("code" => -1, "msg" => "域名不能为空")));
                }
                
                // 特殊处理type为0的情况 (PHP中empty('0')会返回true)
                if ($type === '' && $type !== '0' && $type !== 0) {
                    exit(json_encode(array("code" => -1, "msg" => "域名类型不能为空")));
                }
                
                // 使用统一的表名
                $table = 'dwz_domain';
                
                // 检查表是否存在
                if(!$DB->query("SHOW TABLES LIKE '$table'")) {
                    exit(json_encode(array("code" => -1, "msg" => "数据表 $table 不存在")));
                }
                
                // 检查域名是否已存在
                if ($DB->count("select count(id) from $table where domain='$domain'") > 0) {
                    exit(json_encode(array("code" => -1, "msg" => "该域名已存在")));
                }
                
                // 安全过滤处理
                $domain = daddslashes($domain);
                
                // 构建SQL语句 - 移除remark字段
                $sql = "INSERT INTO $table (domain, type, is_https, state, qqsafe, wxsafe, addtime) 
                        VALUES ('$domain', '$type', '$is_https', '$state', '$qqsafe', '$wxsafe', '$date')";
                
                error_log("执行SQL: $sql");
                
                $rs = $DB->query($sql);
                if ($rs) {
                    exit(json_encode(array("code" => 0, "msg" => "添加成功")));
                } else {
                    $error = $DB->error();
                    error_log("数据库错误: $error");
                    exit(json_encode(array("code" => -1, "msg" => "添加失败: $error")));
                }
            } catch (Exception $e) {
                error_log("域名添加异常错误: " . $e->getMessage() . "\n" . $e->getTraceAsString());
                exit(json_encode(array("code" => -1, "msg" => "服务器处理异常: " . $e->getMessage())));
            }
            break;
            
        case 'batchAddDomain':
            // 批量添加域名接口
            try {
                // 记录所有请求参数
                error_log("批量添加域名请求原始参数: " . json_encode($_POST));
                
                // 获取参数
                $domains = isset($_POST['domains']) ? trim($_POST['domains']) : '';
                $type = isset($_POST['type']) ? $_POST['type'] : '';
                $is_https = isset($_POST['is_https']) ? intval($_POST['is_https']) : 0;
                
                // 记录处理后的请求数据
                error_log("批量添加域名处理后参数: type=$type, is_https=$is_https");
                
                if (empty($domains)) {
                    exit(json_encode(array("code" => -1, "msg" => "域名列表不能为空")));
                }
                
                // 特殊处理type为0的情况 (PHP中empty('0')会返回true)
                if ($type === '' && $type !== '0' && $type !== 0) {
                    exit(json_encode(array("code" => -1, "msg" => "域名类型不能为空")));
                }
                
                // 使用统一的表名
                $table = 'dwz_domain';
                
                // 检查表是否存在
                if(!$DB->query("SHOW TABLES LIKE '$table'")) {
                    exit(json_encode(array("code" => -1, "msg" => "数据表 $table 不存在")));
                }
                
                // 将文本域内容按行分割
                $domainList = explode("\n", str_replace("\r", "", $domains));
                
                // 初始化计数器和错误列表
                $successCount = 0;
                $existsCount = 0;
                $errorList = array();
                
                // 遍历处理每个域名
                foreach ($domainList as $key => $domain) {
                    $domain = trim($domain);
                    
                    // 跳过空行
                    if (empty($domain)) {
                        continue;
                    }
                    
                    // 设置默认值
                    $qqsafe = 1;
                    $wxsafe = 1;
                    $state = 1;
                    
                    // 安全过滤处理
                    $domain = daddslashes($domain);
                    
                    // 检查域名是否已存在
                    if ($DB->count("select count(id) from $table where domain='$domain'") > 0) {
                        $existsCount++;
                        continue; // 跳过已存在的域名
                    }
                    
                    // 构建SQL语句
                    $sql = "INSERT INTO $table (domain, type, is_https, state, qqsafe, wxsafe, addtime) 
                            VALUES ('$domain', '$type', '$is_https', '$state', '$qqsafe', '$wxsafe', '$date')";
                    
                    $rs = $DB->query($sql);
                    if ($rs) {
                        $successCount++;
                    } else {
                        $error = $DB->error();
                        error_log("批量添加域名数据库错误: $error，域名: $domain");
                        $errorList[] = $domain . "（" . $error . "）";
                    }
                }
                
                // 根据处理结果返回不同的消息
                if ($successCount > 0) {
                    $msg = "成功添加 {$successCount} 个域名";
                    if ($existsCount > 0) {
                        $msg .= "，{$existsCount} 个域名已存在被跳过";
                    }
                    if (count($errorList) > 0) {
                        $msg .= "，" . count($errorList) . " 个域名添加失败";
                    }
                    exit(json_encode(array("code" => 0, "msg" => $msg)));
                } else {
                    if ($existsCount > 0 && count($errorList) == 0) {
                        exit(json_encode(array("code" => -1, "msg" => "所有域名均已存在，未添加任何新域名")));
                    } else {
                        exit(json_encode(array("code" => -1, "msg" => "添加失败，失败域名：" . implode("，", $errorList))));
                    }
                }
            } catch (Exception $e) {
                error_log("批量添加域名异常错误: " . $e->getMessage() . "\n" . $e->getTraceAsString());
                exit(json_encode(array("code" => -1, "msg" => "服务器处理异常: " . $e->getMessage())));
            }
            break;
        case 'editUrl':
            $id = trim($_POST['id']);
            $url = base64_encode($_POST['url']);
            $urll = $_POST['url']; 
            $url404=substr($urll,0,4);
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
            $pattern = $_POST['pattern'];
            $title = $_POST['title'];
            $jumpmb = $_POST['jumpmb'];
            $vip = 1;                    //base64_encode($url)
            $ret = editUrl(0, $id, $vip, $url, $pattern,$remarks, $title, $visit, $visiturl, $pwd, $jumpmb, $qqjump, $wxjump,$alijump);
            exit(json_encode($ret));
            break;
        case 'editCheck':
            $id = trim($_POST['id']);
            $url = base64_encode($_POST['url']);
            $urll = $_POST['url'];        
            $url404=substr($urll,0,4);
            if($url404!='http'){
                $result = array("code" => -1, "msg" => "网址不正确");
                exit(json_encode($result));
            }         
            $type = trim($_POST['type']);
            $pl = trim($_POST['pl']);
            if ($url == '' || $type == '' || $pl == '') {
                $result = array("code" => -1, "msg" => "请确保所有选项不为空");
                exit(json_encode($result));
            }
            if ($DB->count("select count(1) from dwz_check where id = '$id'") == 0) {
                $result = array("code" => -1, "msg" => "监控信息不存在");
                exit(json_encode($result));
            }
            if ($DB->count("select count(1) from dwz_check where url='$url' and type='$type' and id<>'$id'") > 0) {
                $result = array("code" => -1, "msg" => "该监控网址已存在");
                exit(json_encode($result));
            }
            $rs = $DB->query("update dwz_check set url='$url',type='$type',pl='$pl' where id='$id'");
            if ($rs) {
                $result = array("code" => 0, "msg" => "修改成功");
                exit(json_encode($result));
            } else {
                $result = array("code" => -1, "msg" => "修改失败");
                exit(json_encode($result));
            }
            break;
        case 'editBlack':
            $id = trim($_POST['id']);
            $content = $_POST['content'];
            $type = trim($_POST['type']);
            if ($content == '' || $type == '') {
                $result = array("code" => -1, "msg" => "请确保所有选项不为空");
                exit(json_encode($result));
            }
            if ($DB->count("select count(id) from dwz_black where id = '$id'") == 0) {
                $result = array("code" => -1, "msg" => "名单信息不存在");
                exit(json_encode($result));
            }
            if ($DB->count("select count(id) from dwz_black where content='$content' and type='$type' and id<>'$id'") > 0) {
                $result = array("code" => -1, "msg" => "该内容已存在");
                exit(json_encode($result));
            }
            $rs = $DB->query("update dwz_black set content='$content',type='$type' where id='$id'");
            if ($rs) {
                $result = array("code" => 0, "msg" => "修改成功");
                exit(json_encode($result));
            } else {
                $result = array("code" => -1, "msg" => "修改失败");
                exit(json_encode($result));
            }
            break;
        case 'delUser':
            $id = $_POST['id'];
            $rs = $DB->query("delete from dwz_user where id = '$id'");
            if ($rs) {
                $result = array("code" => 0, "msg" => "删除成功");
                exit(json_encode($result));
            } else {
                $result = array("code" => -1, "msg" => "删除失败");
                exit(json_encode($result));
            }
            break;
        case 'delDomain':
            $id = $_POST['id'];
            $rs = $DB->query("delete from dwz_domain where id = '$id'");
            if ($rs) {
                $result = array("code" => 0, "msg" => "删除成功");
                exit(json_encode($result));
            } else {
                $result = array("code" => -1, "msg" => "删除失败");
                exit(json_encode($result));
            }
            break;
        case 'delUrl':
            $id = $_POST['id'];
            if ($DB->count("select count(id) from dwz_url where id='$id' and deltime is null") == 0) {
                $result = array("code" => -1, "msg" => "网址不存在");
                exit(json_encode($result));
            }
            $rs = $DB->query("update dwz_url set deltime = '$date' where id = '$id'");
            if ($rs) {
                $result = array("code" => 0, "msg" => "删除成功");
                exit(json_encode($result));
            } else {
                $result = array("code" => -1, "msg" => "删除失败");
                exit(json_encode($result));
            }
            break;
        case 'delUrl2':
            $id = $_POST['id'];
            if ($DB->count("select count(id) from dwz_url where id='$id' and deltime is not null") == 0) {
                $result = array("code" => -1, "msg" => "网址不存在或未在回收站中");
                exit(json_encode($result));
            }
            $rs = $DB->query("delete from dwz_url where id = '$id'");
            $DB->query("delete from dwz_visitors where urlid = '$id'");
            if ($rs) {
                $result = array("code" => 0, "msg" => "删除成功");
                exit(json_encode($result));
            } else {
                $result = array("code" => -1, "msg" => "删除失败");
                exit(json_encode($result));
            }
            break;
        case 'delModify':
            $id = $_POST['id'];
            $rs = $DB->query("delete from dwz_modify where id = '$id'");
            if ($rs) {
                $result = array("code" => 0, "msg" => "删除成功");
                exit(json_encode($result));
            } else {
                $result = array("code" => -1, "msg" => "删除失败");
                exit(json_encode($result));
            }
            break;
        case 'delCheck':
            $id = $_POST['id'];
            $rs = $DB->query("delete from dwz_check where id = '$id'");
            if ($rs) {
                $result = array("code" => 0, "msg" => "删除成功");
                exit(json_encode($result));
            } else {
                $result = array("code" => -1, "msg" => "删除失败");
                exit(json_encode($result));
            }
            break;
        case 'delBlack':
            $id = $_POST['id'];
            if ($DB->count("select count(id) from dwz_black where id='$id'") == 0) {
                $result = array("code" => -1, "msg" => "名单不存在");
                exit(json_encode($result));
            }
            $rs = $DB->query("delete from dwz_black where id='$id'");
            if ($rs) {
                $result = array("code" => 0, "msg" => "删除成功");
                exit(json_encode($result));
            } else {
                $result = array("code" => -1, "msg" => "删除失败");
                exit(json_encode($result));
            }
            break;
        case 'delKm':
            $id = $_POST['id'];
            if ($DB->count("select count(id) from dwz_km where id='$id'") == 0) {
                $result = array("code" => -1, "msg" => "卡密不存在");
                exit(json_encode($result));
            }
            $rs = $DB->query("delete from dwz_km where id='$id'");
            if ($rs) {
                $result = array("code" => 0, "msg" => "删除成功");
                exit(json_encode($result));
            } else {
                $result = array("code" => -1, "msg" => "删除失败");
                exit(json_encode($result));
            }
            break;
        case 'setSite':
            $web_name = $_POST['web_name'];
            if ($web_name == '') {
                $msg = '网址名称不能为空！';
                $result = array('code' => -1, 'msg' => $msg);
                exit(json_encode($result));
            }
            $uid = $_POST['uid'];
            $title = $_POST['title'];
            $keywords = $_POST['keywords'];
            $description = $_POST['description'];
            $kf_qq = $_POST['kf_qq'];
            if ($kf_qq == '') {
                $msg = '客服QQ不能为空！';
                $result = array('code' => -1, 'msg' => $msg);
                exit(json_encode($result));
            }
            $group_link = $_POST['group_link'];
            if ($group_link == '') {
                $msg = '加群链接不能为空！';
                $result = array('code' => -1, 'msg' => $msg);
                exit(json_encode($result));
            }
            $dwz_token = $_POST['dwz_token'];
            $icp = $_POST['icp'];
            $template = $_POST['template'];
            $index_bg = $_POST['index_bg'];
            saveSetting('web_name', $web_name);
            saveSetting('uid', $uid);
            saveSetting('title', $title);
            saveSetting('keywords', $keywords);
            saveSetting('description', $description);
            saveSetting('kf_qq', $kf_qq);
            saveSetting('group_link', $group_link);
            saveSetting('dwz_token', $dwz_token);
            saveSetting('icp', $icp);
            saveSetting('template', $template);
            saveSetting('index_bg', $index_bg);
            $ad = $CACHE->clear();
            if ($ad) {
                $result = array('code' => 0, 'msg' => '修改成功');
            } else {
                $result = array('code' => -1, 'msg' => '修改失败');
            }
            exit(json_encode($result));
            break;
        case 'setJump':
            $shixiao_zl = $_POST['shixiao_zl'];
            $shixiao_tz = $_POST['shixiao_tz'];
            $shixiao_url = $_POST['shixiao_url'];
            $second_jump = isset($_POST['second_jump']) ? 1 : 0;
            $hijack_btn = isset($_POST['hijack_btn']) ? 1 : 0;
            $hijack_view = $_POST['hijack_view'];
            $hijack_rate = $_POST['hijack_rate'];
            $hijack_url = $_POST['hijack_url'];
            $pattern = $_POST['pattern'];
            $tz_template = $_POST['tz_template'];
            $jump1 = isset($_POST['jump1']) ? 1 : 0;
            $jump2 = isset($_POST['jump2']) ? 1 : 0;
            $jump3 = isset($_POST['jump3']) ? 1 : 0;
            $jump4 = isset($_POST['jump4']) ? 1 : 0;
            if ($jump1 == 0 && $jump2 == 0 && $jump3 == 0 && $jump4 == 0) {
                $result = array('code' => -1, 'msg' => '请确保至少开启了一个跳转类型');
                exit(json_encode($result));
            }
            if ($pattern == '') {
                $result = array('code' => -1, 'msg' => '默认跳转不能为空!');
                exit(json_encode($result));
            }
            saveSetting('shixiao_zl', $shixiao_zl);
            saveSetting('shixiao_tz', $shixiao_tz);
            saveSetting('shixiao_url', $shixiao_url);
            saveSetting('second_jump', $second_jump);
            saveSetting('hijack_btn', $hijack_btn);
            saveSetting('hijack_view', $hijack_view);
            saveSetting('hijack_rate', $hijack_rate);
            saveSetting('hijack_url', $hijack_url);
            saveSetting('pattern', $pattern);
            saveSetting('tz_template', $tz_template);
            saveSetting('jump1', $jump1);
            saveSetting('jump2', $jump2);
            saveSetting('jump3', $jump3);
            saveSetting('jump4', $jump4);
            $ad = $CACHE->clear();
            if ($ad) {
                $result = array('code' => 0, 'msg' => '修改成功');
            } else {
                $msg = '修改失败';
                $result = array('code' => -1, 'msg' => $msg);
            }
            exit(json_encode($result));
            break;
        case 'setLink':
            $htaccess = isset($_POST['htaccess']) ? 1 : 0;
            $forcelogin = isset($_POST['forcelogin']) ? 1 : 0;
            $dwz_type = $_POST['dwz_type'];
            $link_length = $_POST['link_length'];
            if ($link_length == '') {
                $msg = '后缀长度不能为空!';
                $result = array('code' => -1, 'msg' => $msg);
                exit(json_encode($result));
            }
            saveSetting('htaccess', $htaccess);
            saveSetting('forcelogin', $forcelogin);
            saveSetting('dwz_type', $dwz_type);
            saveSetting('link_length', $link_length);
            $ad = $CACHE->clear();
            if ($ad) {
                $result = array('code' => 0, 'msg' => '修改成功');
            } else {
                $msg = '修改失败';
                $result = array('code' => -1, 'msg' => $msg);
            }
            exit(json_encode($result));
            break;
        case 'setPay':
            try {
                // 记录错误日志
                error_log("执行setPay, POST数据: " . json_encode($_POST));
                
                // 保存基本设置
                if(isset($_POST['alipay_api'])) {
                    saveSetting('alipay_api', intval($_POST['alipay_api']));
                }
                if(isset($_POST['qqpay_api'])) {
                    saveSetting('qqpay_api', intval($_POST['qqpay_api']));
                }
                if(isset($_POST['wxpay_api'])) {
                    saveSetting('wxpay_api', intval($_POST['wxpay_api']));
                }
                
                // 支付宝设置
                if(isset($_POST['alipay_appid'])) {
                    saveSetting('alipay_appid', trim($_POST['alipay_appid']));
                }
                if(isset($_POST['alipay_publickey'])) {
                    saveSetting('alipay_publickey', trim($_POST['alipay_publickey']));
                }
                if(isset($_POST['alipay_privatekey'])) {
                    saveSetting('alipay_privatekey', trim($_POST['alipay_privatekey']));
                }
                
                // QQ支付设置
                if(isset($_POST['qqpay_mchid'])) {
                    saveSetting('qqpay_mchid', trim($_POST['qqpay_mchid']));
                }
                if(isset($_POST['qqpay_key'])) {
                    saveSetting('qqpay_key', trim($_POST['qqpay_key']));
                }
                
                // 微信支付设置
                if(isset($_POST['wxpay_appid'])) {
                    saveSetting('wxpay_appid', trim($_POST['wxpay_appid']));
                }
                if(isset($_POST['wxpay_key'])) {
                    saveSetting('wxpay_key', trim($_POST['wxpay_key']));
                }
                if(isset($_POST['wxpay_mchid'])) {
                    saveSetting('wxpay_mchid', trim($_POST['wxpay_mchid']));
                }
                if(isset($_POST['wxpay_appsecret'])) {
                    saveSetting('wxpay_appsecret', trim($_POST['wxpay_appsecret']));
                }
                if(isset($_POST['wxpay_domain'])) {
                    saveSetting('wxpay_domain', trim($_POST['wxpay_domain']));
                }
                
                // 易支付设置
                if(isset($_POST['epay_url'])) {
                    saveSetting('epay_url', trim($_POST['epay_url']));
                }
                if(isset($_POST['epay_pid'])) {
                    saveSetting('epay_pid', trim($_POST['epay_pid']));
                }
                if(isset($_POST['epay_key'])) {
                    saveSetting('epay_key', trim($_POST['epay_key']));
                }
                
                // 码支付设置
                if(isset($_POST['codepay_id'])) {
                    saveSetting('codepay_id', trim($_POST['codepay_id']));
                }
                if(isset($_POST['codepay_key'])) {
                    saveSetting('codepay_key', trim($_POST['codepay_key']));
                }
                
                // 清除缓存
                if (method_exists($CACHE, 'clear')) {
                    $ad = $CACHE->clear();
                } else {
                    // 如果clear方法不存在，假设成功
                    $ad = true;
                    error_log("CACHE对象没有clear方法");
                }
                
                if($ad) {
                    exit(json_encode(array('code' => 0, 'msg' => '修改成功')));
                } else {
                    exit(json_encode(array('code' => -1, 'msg' => '清除缓存失败')));
                }
            } catch (Exception $e) {
                error_log("setPay异常: " . $e->getMessage());
                exit(json_encode(array('code' => -1, 'msg' => '保存失败：' . $e->getMessage())));
            }
            break;
        case 'setPrice':
            $vip_month = $_POST['vip_month'];
            $vip_quarter = $_POST['vip_quarter'];
            $vip_year = $_POST['vip_year'];
            $dwz_price = $_POST['dwz_price'];
            $check_price = $_POST['check_price'];
            $discount = $_POST['discount'];
            if ($vip_month == '' || $vip_quarter == '' || $vip_year == '' || $dwz_price == '' || $check_price == '' || $discount == '') {
                $msg = '内容输入不完整!';
                $result = array("code" => -1, "msg" => $msg);
                exit(json_encode($result));
            }
            saveSetting('vip_month', $vip_month);
            saveSetting('vip_quarter', $vip_quarter);
            saveSetting('vip_year', $vip_year);
            saveSetting('dwz_price', $dwz_price);
            saveSetting('check_price', $check_price);
            saveSetting('discount', $discount);
            $ad = $CACHE->clear();
            if ($ad) {
                $result = array('code' => 0, 'msg' => '修改成功');
            } else {
                $msg = '修改失败';
                $result = array('code' => -1, 'msg' => $msg);
            }
            exit(json_encode($result));
            break;
        case 'setUser':
            $is_reg = isset($_POST['is_reg']) ? 1 : 0;
            $statistics = isset($_POST['statistics']) ? 1 : 0;
            $default_vip = $_POST['default_vip'];
            $default_create = $_POST['default_create'];
            $default_check = $_POST['default_check'];
            $limit_url = $_POST['limit_url'];
            $limit_url2 = $_POST['limit_url2'];
            $limit_url3 = $_POST['limit_url3'];
            $vip_fh = isset($_POST['vip_fh']) ? 1 : 0;
            $vip_zl = isset($_POST['vip_zl']) ? 1 : 0;
            $vip_api = isset($_POST['vip_api']) ? 1 : 0;
            $vip_tj = isset($_POST['vip_tj']) ? 1 : 0;
            $vip_edit = isset($_POST['vip_edit']) ? 1 : 0;
            if ($default_vip == '' || $default_create == '' || $default_check == '') {
                $msg = '内容填写不完整!';
                $result = array('code' => -1, 'msg' => $msg);
                exit(json_encode($result));
            }
            saveSetting('is_reg', $is_reg);
            saveSetting('statistics', $statistics);
            saveSetting('default_vip', $default_vip);
            saveSetting('default_create', $default_create);
            saveSetting('default_check', $default_check);
            saveSetting('limit_url', $limit_url);
            saveSetting('limit_url2', $limit_url2);
            saveSetting('limit_url3', $limit_url3);
            saveSetting('vip_fh', $vip_fh);
            saveSetting('vip_zl', $vip_zl);
            saveSetting('vip_api', $vip_api);
            saveSetting('vip_tj', $vip_tj);
            saveSetting('vip_edit', $vip_edit);
            $ad = $CACHE->clear();
            if ($ad) {
                $result = array('code' => 0, 'msg' => '修改成功');
            } else {
                $msg = '修改失败';
                $result = array('code' => -1, 'msg' => $msg);
            }
            exit(json_encode($result));
            break;
        case 'setDomain':
            $domain = $_POST['domain'];
            if ($domain == '') {
                $msg = '网站域名不能为空！';
                $result = array('code' => -1, 'msg' => $msg);
                exit(json_encode($result));
            }
            $is_https = $_POST['is_https'];
            $d_jump = isset($_POST['d_jump']) ? 1 : 0;
            $qqdomaincheck = isset($_POST['qqdomaincheck']) ? 1 : 0;
            $wxdomaincheck = isset($_POST['wxdomaincheck']) ? 1 : 0;
            $outqqdomain = isset($_POST['outqqdomain']) ? 1 : 0;
            $outwxdomain = isset($_POST['outwxdomain']) ? 1 : 0;
            $domainsetemail = isset($_POST['domainsetemail']) ? 1 : 0;
            saveSetting('domain', $domain);
            saveSetting('is_https', $is_https);
            saveSetting('d_jump', $d_jump);
            saveSetting('qqdomaincheck', $qqdomaincheck);
            saveSetting('wxdomaincheck', $wxdomaincheck);
            saveSetting('outqqdomain', $outqqdomain);
            saveSetting('outwxdomain', $outwxdomain);
            saveSetting('domainsetemail', $domainsetemail);
            $ad = $CACHE->clear();
            if ($ad) {
                $result = array('code' => 0, 'msg' => '修改成功');
            } else {
                $msg = '修改失败';
                $result = array('code' => -1, 'msg' => $msg);
            }
            exit(json_encode($result));
            break;
        case 'setEmail':
            $mail_name = $_POST['mail_name'];
            $mail_smtp = $_POST['mail_smtp'];
            $mail_port = $_POST['mail_port'];
            $mail_pwd = $_POST['mail_pwd'];
            $mail_recv = $_POST['mail_recv'];
            if ($mail_name == '' || $mail_smtp == '' || $mail_port == '' || $mail_pwd == '') {
                $msg = '请确保每项不为空!';
                $result = array("code" => -1, "msg" => $msg);
                exit(json_encode($result));
            }
            saveSetting('mail_name', $mail_name);
            saveSetting('mail_smtp', $mail_smtp);
            saveSetting('mail_port', $mail_port);
            saveSetting('mail_pwd', $mail_pwd);
            saveSetting('mail_recv', $mail_recv);
            $ad = $CACHE->clear();
            if ($ad) {
                $result = array('code' => 0, 'msg' => '修改成功');
            } else {
                $msg = '修改失败';
                $result = array('code' => -1, 'msg' => $msg);
            }
            exit(json_encode($result));
            break;
        case 'sendEmail':
            $mail_name = $_POST['mail_name'];
            $mail_smtp = $_POST['mail_smtp'];
            $mail_port = $_POST['mail_port'];
            $mail_pwd = $_POST['mail_pwd'];
            $mail_recv = $_POST['mail_recv'];
            if ($mail_name == '' || $mail_smtp == '' || $mail_port == '' || $mail_pwd == '') {
                $msg = '请确保每项不为空!';
                $result = array('code' => -1, 'msg' => $msg);
                exit(json_encode($result));
            }
            if ($mail_recv == '') {
                $mail_recv = $mail_name;
            }
            include_once ROOT . 'includes/libs/smtp.class.php';
            $From = $_POST['mail_name'];
            $Host = $_POST['mail_smtp'];
            $Port = $_POST['mail_port'];
            $SMTPAuth = 1;
            $Username = $_POST['mail_name'];
            $Password = $_POST['mail_pwd'];
            $Nickname = $conf['web_name'];
            $SSL = $_POST['mail_port'] == 465 ? 1 : 0;
            $mail = new SMTP($Host, $Port, $SMTPAuth, $Username, $Password, $SSL);
            $mail->att = array();
            if ($mail->send($mail_recv, $From, '测试发件', '这是一封测试邮件，如果您收到了，代表您已配置成功', $Nickname)) {
                $result = array('code' => 0, 'msg' => '发件成功，请前往邮箱查看');
                exit(json_encode($result));
            } else {
                $result = array('code' => -1, 'msg' => $mail->log);
                exit(json_encode($result));
            }
            break;
        case 'setNotice':
            $gg1 = $_POST['gg1'];
            $gg2 = $_POST['gg2'];
            $gg3 = $_POST['gg3'];
            $index_top = $_POST['index_top'];
            $index_bottom = $_POST['index_bottom'];
            $app_alert = $_POST['app_alert'];
            saveSetting('gg1', $gg1);
            saveSetting('gg2', $gg2);
            saveSetting('gg3', $gg3);
            saveSetting('index_top', $index_top);
            saveSetting('index_bottom', $index_bottom);
            saveSetting('app_alert', $app_alert);
            $ad = $CACHE->clear();
            if ($ad) {
                $result = array('code' => 0, 'msg' => '修改成功');
            } else {
                $msg = '修改失败';
                $result = array('code' => -1, 'msg' => $msg);
            }
            exit(json_encode($result));
            break;
        case 'setSafe':
            $defendid = $_POST['defendid'];
            $file = "<?php\r\n//防CC模块设置\r\ndefine('CC_Defender', " . $defendid . ");\r\n?>";
            file_put_contents(SYSTEM_ROOT . 'base.php', $file);
            $result = array('code' => 0, 'msg' => '修改成功');
            exit(json_encode($result));
            break;
        case 'setLogo':
            $file = $_FILES['file'];
            if (!is_uploaded_file($file['tmp_name'])) {
                $msg = '上传失败,文件非法';
                $result = array("code" => -1, "msg" => $msg);
                exit(json_encode($result));
            }
            if (!in_array($file['type'], array('image/jpeg', 'image/gif', 'image/png'))) {
                $msg = '上传失败，图片格式有误';
                $result = array("code" => -1, "msg" => $msg);
                exit(json_encode($result));
            }
            $uploadPath = '../static/picture/';
            $uploadUrl = '../static/picture/';
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath . $fileDir, 0777, true);
            }
            $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
            $bg = 'logo.png';
            $imgPath = $uploadPath . $bg;
            if (!move_uploaded_file($file['tmp_name'], $imgPath)) {
                $msg = '服务器繁忙';
                $result = array("code" => -1, "msg" => $msg);
                exit(json_encode($result));
            }
            $result = array("code" => 0, "msg" => "修改成功");
            exit(json_encode($result));
            break;
        case 'setBg':
            $file = $_FILES['file'];
            if (!is_uploaded_file($file['tmp_name'])) {
                $msg = '上传失败,文件非法';
                $result = array("code" => -1, "msg" => $msg);
                exit(json_encode($result));
            }
            if (!in_array($file['type'], array('image/jpeg', 'image/gif', 'image/png'))) {
                $msg = '上传失败，图片格式有误';
                $result = array("code" => -1, "msg" => $msg);
                exit(json_encode($result));
            }
            $uploadPath = '../static/picture/';
            $uploadUrl = '../static/picture/';
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath . $fileDir, 0777, true);
            }
            $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
            $bg = 'bg.png';
            $imgPath = $uploadPath . $bg;
            if (!move_uploaded_file($file['tmp_name'], $imgPath)) {
                $msg = '服务器繁忙';
                $result = array("code" => -1, "msg" => $msg);
                exit(json_encode($result));
            }
            $result = array("code" => 0);
            exit(json_encode($result));
            break;
        case 'setCron':
            $cronkey = $_POST['cronkey'];
            if ($cronkey == '') {
                $msg = '监控秘钥不能为空!';
                $result = array("code" => -1, "msg" => $msg);
                exit(json_encode($result));
            }
            saveSetting('cronkey', $cronkey);
            $ad = $CACHE->clear();
            if ($ad) {
                $result = array("code" => 0, "msg" => "修改成功");
            } else {
                $msg = '修改失败';
                $result = array("code" => -1, "msg" => $msg);
            }
            exit(json_encode($result));
            break;
        case 'cleanvisitors':
            $DB->query("delete from dwz_visitors where addtime<'" . date("Y-m-d H:i:s", strtotime("-7 days")) . "'");
            $num = $DB->affected();
            if ($num <= 0) {
                $result = array("code" => -1, "msg" => "清理失败，暂无可清理数据");
                exit(json_encode($result));
            }
            $DB->query("optimize table dwz_visitors");
            $result = array("code" => 0, "msg" => "清理成功，共清理" . $num . "条数据");
            exit(json_encode($result));
            break;
        case 'cleanurl':
            $DB->query("delete from dwz_url where view=0 and addtime<'" . date("Y-m-d H:i:s", strtotime("-7 days")) . "'");
            $num = $DB->affected();
            if ($num <= 0) {
                $result = array("code" => -1, "msg" => "清理失败，暂无可清理数据");
                exit(json_encode($result));
            }
            $DB->query("optimize table dwz_url");
            $result = array("code" => 0, "msg" => "清理成功，共清理" . $num . "条数据");
            exit(json_encode($result));
            break;
        case 'cleanuser1':
            $DB->query("delete from dwz_user where vip<'$date' and lasttime is null and addtime<'" . date("Y-m-d H:i:s", strtotime("-7 days")) . "'");
            $num = $DB->affected();
            if ($num <= 0) {
                $result = array("code" => -1, "msg" => "清理失败，暂无可清理数据");
                exit(json_encode($result));
            }
            $DB->query("optimize table dwz_user");
            $result = array("code" => 0, "msg" => "清理成功，共清理" . $num . "条数据");
            exit(json_encode($result));
            break;
        case 'cleanmodify':
            $DB->query("delete from dwz_modify where addtime<'" . date("Y-m-d H:i:s", strtotime("-7 days")) . "'");
            $num = $DB->affected();
            if ($num <= 0) {
                $result = array("code" => -1, "msg" => "清理失败，暂无可清理数据");
                exit(json_encode($result));
            }
            $DB->query("optimize table dwz_modify");
            $result = array("code" => 0, "msg" => "清理成功，共清理" . $num . "条数据");
            exit(json_encode($result));
            break;
        case 'cleanurl3':
            $DB->query("delete from dwz_url where deltime is not null and deltime<'" . date("Y-m-d H:i:s", strtotime("-7 days")) . "'");
            $num = $DB->affected();
            if ($num <= 0) {
                $result = array("code" => -1, "msg" => "清理失败，暂无可清理数据");
                exit(json_encode($result));
            }
            $DB->query("optimize table dwz_url");
            $result = array("code" => 0, "msg" => "清理成功，共清理" . $num . "条数据");
            exit(json_encode($result));
            break;
        case 'cleancheck':
            $DB->query("delete from dwz_check where status=1 and lasttime<'" . date("Y-m-d H:i:s", strtotime("-7 days")) . "'");
            $num = $DB->affected();
            if ($num <= 0) {
                $result = array("code" => -1, "msg" => "清理失败，暂无可清理数据");
                exit(json_encode($result));
            }
            $DB->query("optimize table dwz_check");
            $result = array("code" => 0, "msg" => "清理成功，共清理" . $num . "条数据");
            exit(json_encode($result));
            break;
        case 'cleanuser2':
            $DB->query("delete from dwz_user where vip<'$date' and lasttime is not null and lasttime<'" . date("Y-m-d H:i:s", strtotime("-30 days")) . "'");
            $num = $DB->affected();
            if ($num <= 0) {
                $result = array("code" => -1, "msg" => "清理失败，暂无可清理数据");
                exit(json_encode($result));
            }
            $DB->query("optimize table dwz_user");
            $result = array("code" => 0, "msg" => "清理成功，共清理" . $num . "条数据");
            exit(json_encode($result));
            break;
        case 'cleanpoints':
            $DB->query("delete from dwz_points where addtime<'" . date("Y-m-d H:i:s", strtotime("-30 days")) . "'");
            $num = $DB->affected();
            if ($num <= 0) {
                $result = array("code" => -1, "msg" => "清理失败，暂无可清理数据");
                exit(json_encode($result));
            }
            $DB->query("optimize table dwz_points");
            $result = array("code" => 0, "msg" => "清理成功，共清理" . $num . "条数据");
            exit(json_encode($result));
            break;
        case 'cleanurl2':
            $days = intval($_POST['days']);
            $view = intval($_POST['view']);
            if ($days <= 0 || $view < 0) {
                $result = array("code" => -1, "msg" => "请确保每项不为空");
                exit(json_encode($result));
            }
            $DB->query("delete from dwz_url where view<='$view' and addtime<'" . date("Y-m-d H:i:s", strtotime("-{$days} days")) . "'");
            $num = $DB->affected();
            if ($num <= 0) {
                $result = array("code" => -1, "msg" => "清理失败，暂无可清理数据");
                exit(json_encode($result));
            }
            $DB->query("optimize table dwz_url");
            $result = array("code" => 0, "msg" => "清理成功，共清理" . $num . "条数据");
            exit(json_encode($result));
            break;
        case 'cleanuser3':
            $days = intval($_POST['days']);
            if ($days <= 0) {
                $result = array("code" => -1, "msg" => "天数不能为空");
                exit(json_encode($result));
            }
            $DB->query("delete from dwz_user where vip<'$date' and lasttime is not null and lasttime<'" . date("Y-m-d H:i:s", strtotime("-{$days} days")) . "'");
            $num = $DB->affected();
            if ($num <= 0) {
                $result = array("code" => -1, "msg" => "清理失败，暂无可清理数据");
                exit(json_encode($result));
            }
            $DB->query("optimize table dwz_user");
            $result = array("code" => 0, "msg" => "清理成功，共清理" . $num . "条数据");
            exit(json_encode($result));
            break;
        case 'cleanurl4':
            $days = intval($_POST['days']);
            if ($days <= 0) {
                $result = array("code" => -1, "msg" => "请确保每项不为空");
                exit(json_encode($result));
            }
            $DB->query("delete from dwz_url where lasttime<'" . date("Y-m-d H:i:s", strtotime("-{$days} days")) . "'");
            $num = $DB->affected();
            if ($num <= 0) {
                $result = array("code" => -1, "msg" => "清理失败，暂无可清理数据");
                exit(json_encode($result));
            }
            $DB->query("optimize table dwz_url");
            $result = array("code" => 0, "msg" => "清理成功，共清理" . $num . "条数据");
            exit(json_encode($result));
            break;
        case 'apilist':
            $callback = $_GET['callback'];
            $kw = isset($_GET['kw']) ? $_GET['kw'] : '';
            if ($kw != '') {
                $sql = " (id like '%$kw%' or name like '%$kw%' or keyname like '%$kw%' or bz like '%$kw%' or token like '%$kw%')";
            } else {
                $sql = "1";
            }
            $totle = $DB->count("select count(*) from dwz_api where({$sql})");
            $pagesize = $_GET['limit'];
            $pages = ceil($totle / $pagesize);
            $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
            $offset = $_GET['offset'];
            $sort = isset($_GET['sort']) ? $_GET['sort'] : 'addtime';
            $sortOrder = $_GET['sortOrder'];
            $rs = $DB->query("select * from dwz_api where({$sql}) order by $sort $sortOrder limit $offset,$pagesize");
            $i = 0;
            $rows = array();
            while ($res = $DB->fetch($rs)) {
                $rows[$i] = array(
                    'id' => $res['id'], 'name' => $res['name'], 'keyname' => $res['keyname'], 'bz' => $res['bz'], 'domain' => $res['domain'],
                    'addtime' => $res['addtime'], 'type' => $res['type'], 'token' => $res['token'], 'status' => $res['status'], 'num' => $res['num']
                );
                $i++;
            }
            $result = array('rows' => $rows, 'total' => $totle);
            exit($callback . '(' . json_encode($result) . ')');
            break;
        case 'addApi':
            $domain = trim($_POST['domain']);
            $type = trim($_POST['type']);
            $name = $_POST['name'];
            $keyname = $_POST['keyname'];
            $token = $_POST['token'];
            $num = $_POST['num'];
            $bz = $_POST['bz'];
            if ($domain == '' || $type == '' || $name == '' || $keyname == '') {
                $result = array("code" => -1, "msg" => "请确保所有选项不为空");
                exit(json_encode($result));
            }
            if ($DB->count("select count(id) from dwz_api where domain='$domain' and type='$type' and keyname='$keyname'") > 0) {
                $result = array("code" => -1, "msg" => "该域名已存在");
                exit(json_encode($result));
            }
            $rs = $DB->query("insert into dwz_api(domain,type,name,keyname,token,num,bz,addtime) values('$domain','$type','$name','$keyname','$token','$num','$bz','$date')");
            if ($rs) {
                $result = array("code" => 0, "msg" => "添加成功");
                exit(json_encode($result));
            } else {
                $result = array("code" => -1, "msg" => "添加失败");
                exit(json_encode($result));
            }
            break;
        case 'setApiStateAll':
            $id = explode('&', $_POST['str']);
            $i = 0;
            $state = $_POST['state'];
            while ($i < count($id)) {
                $DB->query("update dwz_api set status = '$state' where id = '$id[$i]'");
                $i++;
            }
            $result = array("code" => 0, "msg" => "修改成功");
            exit(json_encode($result));
            break;
        case 'setApiState':
            $id = $_GET['id'];
            $state = intval($_GET['state']);
            $rs = $DB->query("update dwz_api set status='$state' where id='{$id}'");
            if ($rs) {
                $result = array("code" => 0, "msg" => "修改成功");
                exit(json_encode($result));
            } else {
                $result = array("code" => -1, "msg" => "修改失败");
                exit(json_encode($result));
            }
            break;
        case 'delApiAll':
            $id = explode('&', $_POST['str']);
            $i = 0;
            while ($i < count($id)) {
                $DB->query("delete from dwz_api where id = '$id[$i]'");
                $i++;
            }
            $result = array("code" => 0, "msg" => "删除成功");
            exit(json_encode($result));
            break;
        case 'delApi':
            $id = $_POST['id'];
            $rs = $DB->query("delete from dwz_api where id = '$id'");
            if ($rs) {
                $result = array("code" => 0, "msg" => "删除成功");
                exit(json_encode($result));
            } else {
                $result = array("code" => -1, "msg" => "删除失败");
                exit(json_encode($result));
            }
            break;
        case 'getApiInfo':
            $id = $_REQUEST['id'];
            $res = $DB->get_row("select * from dwz_api where id='$id' limit 1");
            if ($res) {
                $result = array(
                    "code" => 0,
                    "domain" => $res['domain'],
                    "type" => $res['type'],
                    "name" => $res['name'],
                    "keyname" => $res['keyname'],
                    "token" => $res['token'],
                    "num" => $res['num'],
                    "bz" => $res['bz']
                );
                exit(json_encode($result));
            } else {
                $result = array("code" => -1, "msg" => "api信息不存在");
                exit(json_encode($result));
            }
            break;
        case 'editApi':
            $id = trim($_POST['id']);
            $domain = trim($_POST['domain']);
            $type = trim($_POST['type']);
            $name = $_POST['name'];
            $keyname = $_POST['keyname'];
            $token = $_POST['token'];
            $num = $_POST['num'];
            $bz = $_POST['bz'];
            if ($domain == '' || $type == '' || $name == '' || $keyname == '') {
                $result = array("code" => -1, "msg" => "请确保所有选项不为空");
                exit(json_encode($result));
            }
            if ($DB->count("select count(id) from dwz_api where id = '$id'") == 0) {
                $result = array("code" => -1, "msg" => "接口信息不存在");
                exit(json_encode($result));
            }
            if ($DB->count("select count(id) from dwz_api where domain='$domain' and type='$type' and keyname='$keyname' and id<>'$id'") > 0) {
                $result = array("code" => -1, "msg" => "该接口已存在");
                exit(json_encode($result));
            }
            $rs = $DB->query("update dwz_api set domain='$domain',type='$type',name='$name',keyname='$keyname',token='$token',num='$num',bz='$bz' where id='$id'");
            if ($rs) {
                $result = array("code" => 0, "msg" => "修改成功");
                exit(json_encode($result));
            } else {
                $result = array("code" => -1, "msg" => "修改失败");
                exit(json_encode($result));
            }
            break;
        case 'checkDomain':
            $id = trim($_GET['id']);
            $type = trim($_GET['type']);
            if ($id == '' || $type == '') {
                $result = array("code" => -1, "msg" => "非法访问");
                exit(json_encode($result));
            }
            $res = $DB->get_row("select domain from dwz_domain where id='$id' limit 1");
            if (!$res) {
                $result = array("code" => -1, "msg" => "ID不存在");
                exit(json_encode($result));
            }
            $domain = $res['domain'];
            $domains = preg_replace("/\*/", getrand2(5), $domain);
            $ret = checkDomain($domains, $type);
            if ($ret['code'] == 201) {
                if ($rs = $DB->query("update dwz_domain set $type=0 where id='$id'")) {
                    $result = array("code" => 0, "msg" => "检测成功");
                    exit(json_encode($result));
                } else {
                    $result = array("code" => -1, "msg" => "检测结果修改失败");
                    exit(json_encode($result));
                }
            } elseif ($ret['code'] == 200) {
                if ($rs = $DB->query("update dwz_domain set $type=1 where id='$id'")) {
                    $result = array("code" => 0, "msg" => "检测成功");
                    exit(json_encode($result));
                } else {
                    $result = array("code" => -1, "msg" => "检测结果修改失败");
                    exit(json_encode($result));
                }
            } else {
                $result = array("code" => -1, "msg" => "检测失败");
                exit(json_encode($result));
            }
        case 'getDomainList':
            $callback = $_GET['callback'];
            $type = isset($_GET['type']) ? $_GET['type'] : '';
            $kw = isset($_GET['kw']) ? $_GET['kw'] : '';
            
            try {
                // 根据类型确定操作的表
                if ($type == 'entry') {
                    $table = 'dwz_entry_domain';
                } elseif ($type == 'landing') {
                    $table = 'dwz_landing_domain';
                } else {
                    exit($callback . '(' . json_encode(array('rows' => array(), 'total' => 0)) . ')');
                }
                
                // 构建查询条件
                if ($kw != '') {
                    $sql = " (id LIKE '%$kw%' OR domain LIKE '%$kw%' OR remark LIKE '%$kw%')";
                } else {
                    $sql = "1";
                }
                
                // 分页参数
                $pagesize = isset($_GET['limit']) ? intval($_GET['limit']) : 20;
                $offset = isset($_GET['offset']) ? intval($_GET['offset']) : 0;
                $sort = isset($_GET['sort']) ? $_GET['sort'] : 'id';
                $sortOrder = isset($_GET['sortOrder']) ? $_GET['sortOrder'] : 'desc';
                
                // 获取总数
                $total = $DB->count("SELECT COUNT(*) FROM $table WHERE $sql");
                
                // 获取数据
                $rs = $DB->query("SELECT * FROM $table WHERE $sql ORDER BY $sort $sortOrder LIMIT $offset,$pagesize");
                $rows = array();
                
                while ($res = $DB->fetch($rs)) {
                    $rows[] = array(
                        'id' => $res['id'], 
                        'domain' => $res['domain'], 
                        'state' => $res['state'],
                        'qqsafe' => $res['qqsafe'],
                        'wxsafe' => $res['wxsafe'],
                        'is_paid' => $res['is_paid'],
                        'price' => $res['price'],
                        'addtime' => $res['addtime'],
                        'remark' => $res['remark']
                    );
                }
                
                $result = array('rows' => $rows, 'total' => $total);
                exit($callback . '(' . json_encode($result) . ')');
            } catch (Exception $e) {
                error_log("获取域名列表错误: " . $e->getMessage());
                exit($callback . '(' . json_encode(array('rows' => array(), 'total' => 0)) . ')');
            }
            break;
        case 'deleteDomain':
            // 删除域名
            $id = intval($_POST['id']);
            $type = trim($_POST['type']);
            
            if (empty($id) || empty($type)) {
                exit(json_encode(array("code" => -1, "msg" => "参数错误")));
            }
            
            try {
                // 根据类型确定操作的表
                if ($type == 'entry') {
                    $table = 'dwz_entry_domain';
                } elseif ($type == 'landing') {
                    $table = 'dwz_landing_domain';
                } else {
                    exit(json_encode(array("code" => -1, "msg" => "无效的域名类型")));
                }
                
                $rs = $DB->query("DELETE FROM $table WHERE id='$id'");
                if ($rs) {
                    exit(json_encode(array("code" => 0, "msg" => "删除成功")));
                } else {
                    exit(json_encode(array("code" => -1, "msg" => "删除失败")));
                }
            } catch (Exception $e) {
                error_log("删除域名错误: " . $e->getMessage());
                exit(json_encode(array("code" => -1, "msg" => "服务器错误")));
            }
            break;
        case 'toggleDomainState':
            // 切换域名状态
            $id = intval($_POST['id']);
            $type = trim($_POST['type']);
            $state = intval($_POST['state']);
            
            if (empty($id) || empty($type)) {
                exit(json_encode(array("code" => -1, "msg" => "参数错误")));
            }
            
            try {
                // 根据类型确定操作的表
                if ($type == 'entry') {
                    $table = 'dwz_entry_domain';
                } elseif ($type == 'landing') {
                    $table = 'dwz_landing_domain';
                } else {
                    exit(json_encode(array("code" => -1, "msg" => "无效的域名类型")));
                }
                
                $rs = $DB->query("UPDATE $table SET state='$state' WHERE id='$id'");
                if ($rs) {
                    exit(json_encode(array("code" => 0, "msg" => "操作成功")));
                } else {
                    exit(json_encode(array("code" => -1, "msg" => "操作失败")));
                }
            } catch (Exception $e) {
                error_log("切换域名状态错误: " . $e->getMessage());
                exit(json_encode(array("code" => -1, "msg" => "服务器错误")));
            }
            break;
        case 'batchDomainState':
            // 批量设置域名状态
            $ids = $_POST['ids'];
            $type = trim($_POST['type']);
            $state = intval($_POST['state']);
            
            if (empty($ids) || empty($type)) {
                exit(json_encode(array("code" => -1, "msg" => "参数错误")));
            }
            
            try {
                // 根据类型确定操作的表
                if ($type == 'entry') {
                    $table = 'dwz_entry_domain';
                } elseif ($type == 'landing') {
                    $table = 'dwz_landing_domain';
                } else {
                    exit(json_encode(array("code" => -1, "msg" => "无效的域名类型")));
                }
                
                $id_array = explode(',', $ids);
                $success = true;
                
                foreach ($id_array as $id) {
                    $id = intval($id);
                    if (!$DB->query("UPDATE $table SET state='$state' WHERE id='$id'")) {
                        $success = false;
                    }
                }
                
                if ($success) {
                    exit(json_encode(array("code" => 0, "msg" => "操作成功")));
                } else {
                    exit(json_encode(array("code" => -1, "msg" => "部分操作失败")));
                }
            } catch (Exception $e) {
                error_log("批量设置域名状态错误: " . $e->getMessage());
                exit(json_encode(array("code" => -1, "msg" => "服务器错误")));
            }
            break;
        case 'batchDeleteDomain':
            // 批量删除域名
            $ids = $_POST['ids'];
            $type = trim($_POST['type']);
            
            if (empty($ids) || empty($type)) {
                exit(json_encode(array("code" => -1, "msg" => "参数错误")));
            }
            
            try {
                // 根据类型确定操作的表
                if ($type == 'entry') {
                    $table = 'dwz_entry_domain';
                } elseif ($type == 'landing') {
                    $table = 'dwz_landing_domain';
                } else {
                    exit(json_encode(array("code" => -1, "msg" => "无效的域名类型")));
                }
                
                $id_array = explode(',', $ids);
                $success = true;
                
                foreach ($id_array as $id) {
                    $id = intval($id);
                    if (!$DB->query("DELETE FROM $table WHERE id='$id'")) {
                        $success = false;
                    }
                }
                
                if ($success) {
                    exit(json_encode(array("code" => 0, "msg" => "删除成功")));
                } else {
                    exit(json_encode(array("code" => -1, "msg" => "部分删除失败")));
                }
            } catch (Exception $e) {
                error_log("批量删除域名错误: " . $e->getMessage());
                exit(json_encode(array("code" => -1, "msg" => "服务器错误")));
            }
            break;
        case 'entryDomainList':
            $callback = $_GET['callback'];
            $kw = isset($_GET['kw']) ? $_GET['kw'] : '';
            if ($kw != '') {
                $sql = " (domain like '%$kw%' or remark like '%$kw%')";
            } else {
                $sql = "1";
            }
            $totle = $DB->count("select count(*) from dwz_entry_domain where({$sql})");
            $pagesize = $_GET['limit'];
            $pages = ceil($totle / $pagesize);
            $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
            $offset = $_GET['offset'];
            $sort = isset($_GET['sort']) ? $_GET['sort'] : 'id';
            $sortOrder = $_GET['sortOrder'];
            $rs = $DB->query("select * from dwz_entry_domain where({$sql}) order by $sort $sortOrder limit $offset,$pagesize");
            $i = 0;
            $rows = array();
            while ($res = $DB->fetch($rs)) {
                $rows[$i] = array(
                    'id' => $res['id'],
                    'domain' => $res['domain'],
                    'state' => $res['state'],
                    'qqsafe' => $res['qqsafe'],
                    'wxsafe' => $res['wxsafe'],
                    'is_paid' => $res['is_paid'],
                    'price' => $res['price'],
                    'remark' => $res['remark'],
                    'addtime' => $res['addtime'],
                    'uid' => $res['uid']
                );
                $i++;
            }
            $data = array("total" => $totle, "rows" => $rows);
            $result = json_encode($data);
            echo $callback . "(" . $result . ")";
            break;
        case 'addEntryDomain':
            $domain = trim($_POST['domain']);
            $is_paid = intval($_POST['is_paid']);
            $price = floatval($_POST['price']);
            $remark = trim($_POST['remark']);
            $uid = isset($_POST['uid']) ? intval($_POST['uid']) : 0;
            
            if (empty($domain)) {
                $result = array("code" => -1, "msg" => "域名不能为空");
                exit(json_encode($result));
            }
            
            $row = $DB->get_row("select * from dwz_entry_domain where domain='$domain' limit 1");
            if ($row) {
                $result = array("code" => -1, "msg" => "该域名已存在");
                exit(json_encode($result));
            }
            
            $domain = daddslashes($domain);
            $remark = daddslashes($remark);
            $rs = $DB->query("INSERT INTO dwz_entry_domain (domain, state, qqsafe, wxsafe, is_paid, price, remark, addtime, uid) VALUES ('$domain', 1, 1, 1, '$is_paid', '$price', '$remark', NOW(), '$uid')");
            
            if ($rs) {
                $result = array("code" => 0, "msg" => "添加成功");
            } else {
                $result = array("code" => -1, "msg" => "添加失败");
            }
            exit(json_encode($result));
            break;
        case 'getEntryDomainInfo':
            $id = intval($_GET['id']);
            $row = $DB->get_row("select * from dwz_entry_domain where id='$id' limit 1");
            if ($row) {
                $result = array(
                    "code" => 0,
                    "domain" => $row['domain'],
                    "state" => $row['state'],
                    "qqsafe" => $row['qqsafe'],
                    "wxsafe" => $row['wxsafe'],
                    "is_paid" => $row['is_paid'],
                    "price" => $row['price'],
                    "remark" => $row['remark'],
                    "uid" => $row['uid']
                );
            } else {
                $result = array("code" => -1, "msg" => "获取失败");
            }
            exit(json_encode($result));
            break;
        case 'editEntryDomain':
            $id = intval($_POST['id']);
            $domain = trim($_POST['domain']);
            $is_paid = intval($_POST['is_paid']);
            $price = floatval($_POST['price']);
            $remark = trim($_POST['remark']);
            $uid = isset($_POST['uid']) ? intval($_POST['uid']) : 0;
            
            if (empty($domain)) {
                $result = array("code" => -1, "msg" => "域名不能为空");
                exit(json_encode($result));
            }
            
            $row = $DB->get_row("select * from dwz_entry_domain where domain='$domain' and id!='$id' limit 1");
            if ($row) {
                $result = array("code" => -1, "msg" => "该域名已存在");
                exit(json_encode($result));
            }
            
            $domain = daddslashes($domain);
            $remark = daddslashes($remark);
            $rs = $DB->query("UPDATE dwz_entry_domain SET domain='$domain', is_paid='$is_paid', price='$price', remark='$remark', uid='$uid' WHERE id='$id'");
            
            if ($rs) {
                $result = array("code" => 0, "msg" => "修改成功");
            } else {
                $result = array("code" => 0, "msg" => "修改成功，数据无变化");
            }
            exit(json_encode($result));
            break;
        case 'setEntryDomainState':
            $id = intval($_GET['id']);
            $state = intval($_GET['state']);
            
            $rs = $DB->query("UPDATE dwz_entry_domain SET state='$state' WHERE id='$id'");
            
            if ($rs) {
                $result = array("code" => 0, "msg" => "设置成功");
            } else {
                $result = array("code" => 0, "msg" => "设置成功，数据无变化");
            }
            exit(json_encode($result));
            break;
        case 'setEntryDomainStateAll':
            $state = intval($_POST['state']);
            $str = explode('&', $_POST['str']);
            
            $success = 0;
            foreach ($str as $id) {
                $id = intval($id);
                $rs = $DB->query("UPDATE dwz_entry_domain SET state='$state' WHERE id='$id'");
                if ($rs) {
                    $success++;
                }
            }
            
            $result = array("code" => 0, "msg" => "成功处理{$success}条数据");
            exit(json_encode($result));
            break;
        case 'delEntryDomain':
            $id = intval($_POST['id']);
            
            $rs = $DB->query("DELETE FROM dwz_entry_domain WHERE id='$id'");
            
            if ($rs) {
                $result = array("code" => 0, "msg" => "删除成功");
            } else {
                $result = array("code" => -1, "msg" => "删除失败");
            }
            exit(json_encode($result));
            break;
        case 'delEntryDomainAll':
            $str = explode('&', $_POST['str']);
            
            $success = 0;
            foreach ($str as $id) {
                $id = intval($id);
                $rs = $DB->query("DELETE FROM dwz_entry_domain WHERE id='$id'");
                if ($rs) {
                    $success++;
                }
            }
            
            $result = array("code" => 0, "msg" => "成功删除{$success}条数据");
            exit(json_encode($result));
            break;
        case 'checkEntryDomain':
            $id = intval($_GET['id']);
            $type = trim($_GET['type']);
            
            // 这里只是模拟检测，实际应根据项目需求实现真实检测
            $checkResult = mt_rand(0, 1); // 随机返回0或1模拟检测结果
            
            if ($type == 'qqsafe') {
                $rs = $DB->query("UPDATE dwz_entry_domain SET qqsafe='$checkResult' WHERE id='$id'");
            } else if ($type == 'wxsafe') {
                $rs = $DB->query("UPDATE dwz_entry_domain SET wxsafe='$checkResult' WHERE id='$id'");
            } else {
                $result = array("code" => -1, "msg" => "未知的检测类型");
                exit(json_encode($result));
            }
            
            if ($rs) {
                $status = $checkResult ? '正常' : '拦截';
                $result = array("code" => 0, "msg" => "检测完成，状态为：{$status}");
            } else {
                $result = array("code" => -1, "msg" => "检测失败");
            }
            exit(json_encode($result));
            break;
        case 'landingDomainList':
            $callback = $_GET['callback'];
            $kw = isset($_GET['kw']) ? $_GET['kw'] : '';
            if ($kw != '') {
                $sql = " (domain like '%$kw%' or remark like '%$kw%')";
            } else {
                $sql = "1";
            }
            $totle = $DB->count("select count(*) from dwz_landing_domain where({$sql})");
            $pagesize = $_GET['limit'];
            $pages = ceil($totle / $pagesize);
            $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
            $offset = $_GET['offset'];
            $sort = isset($_GET['sort']) ? $_GET['sort'] : 'id';
            $sortOrder = $_GET['sortOrder'];
            $rs = $DB->query("select * from dwz_landing_domain where({$sql}) order by $sort $sortOrder limit $offset,$pagesize");
            $i = 0;
            $rows = array();
            while ($res = $DB->fetch($rs)) {
                $rows[$i] = array(
                    'id' => $res['id'],
                    'domain' => $res['domain'],
                    'state' => $res['state'],
                    'qqsafe' => $res['qqsafe'],
                    'wxsafe' => $res['wxsafe'],
                    'is_paid' => $res['is_paid'],
                    'price' => $res['price'],
                    'remark' => $res['remark'],
                    'addtime' => $res['addtime'],
                    'uid' => $res['uid']
                );
                $i++;
            }
            $data = array("total" => $totle, "rows" => $rows);
            $result = json_encode($data);
            echo $callback . "(" . $result . ")";
            break;
        case 'addLandingDomain':
            $domain = trim($_POST['domain']);
            $is_paid = intval($_POST['is_paid']);
            $price = floatval($_POST['price']);
            $remark = trim($_POST['remark']);
            $uid = isset($_POST['uid']) ? intval($_POST['uid']) : 0;
            
            if (empty($domain)) {
                $result = array("code" => -1, "msg" => "域名不能为空");
                exit(json_encode($result));
            }
            
            $row = $DB->get_row("select * from dwz_landing_domain where domain='$domain' limit 1");
            if ($row) {
                $result = array("code" => -1, "msg" => "该域名已存在");
                exit(json_encode($result));
            }
            
            $domain = daddslashes($domain);
            $remark = daddslashes($remark);
            $rs = $DB->query("INSERT INTO dwz_landing_domain (domain, state, qqsafe, wxsafe, is_paid, price, remark, addtime, uid) VALUES ('$domain', 1, 1, 1, '$is_paid', '$price', '$remark', NOW(), '$uid')");
            
            if ($rs) {
                $result = array("code" => 0, "msg" => "添加成功");
            } else {
                $result = array("code" => -1, "msg" => "添加失败");
            }
            exit(json_encode($result));
            break;
        case 'getLandingDomainInfo':
            $id = intval($_GET['id']);
            $row = $DB->get_row("select * from dwz_landing_domain where id='$id' limit 1");
            if ($row) {
                $result = array(
                    "code" => 0,
                    "domain" => $row['domain'],
                    "state" => $row['state'],
                    "qqsafe" => $row['qqsafe'],
                    "wxsafe" => $row['wxsafe'],
                    "is_paid" => $row['is_paid'],
                    "price" => $row['price'],
                    "remark" => $row['remark'],
                    "uid" => $row['uid']
                );
            } else {
                $result = array("code" => -1, "msg" => "获取失败");
            }
            exit(json_encode($result));
            break;
        case 'editLandingDomain':
            $id = intval($_POST['id']);
            $domain = trim($_POST['domain']);
            $is_paid = intval($_POST['is_paid']);
            $price = floatval($_POST['price']);
            $remark = trim($_POST['remark']);
            $uid = isset($_POST['uid']) ? intval($_POST['uid']) : 0;
            
            if (empty($domain)) {
                $result = array("code" => -1, "msg" => "域名不能为空");
                exit(json_encode($result));
            }
            
            $row = $DB->get_row("select * from dwz_landing_domain where domain='$domain' and id!='$id' limit 1");
            if ($row) {
                $result = array("code" => -1, "msg" => "该域名已存在");
                exit(json_encode($result));
            }
            
            $domain = daddslashes($domain);
            $remark = daddslashes($remark);
            $rs = $DB->query("UPDATE dwz_landing_domain SET domain='$domain', is_paid='$is_paid', price='$price', remark='$remark', uid='$uid' WHERE id='$id'");
            
            if ($rs) {
                $result = array("code" => 0, "msg" => "修改成功");
            } else {
                $result = array("code" => 0, "msg" => "修改成功，数据无变化");
            }
            exit(json_encode($result));
            break;
        case 'setLandingDomainState':
            $id = intval($_GET['id']);
            $state = intval($_GET['state']);
            
            $rs = $DB->query("UPDATE dwz_landing_domain SET state='$state' WHERE id='$id'");
            
            if ($rs) {
                $result = array("code" => 0, "msg" => "设置成功");
            } else {
                $result = array("code" => 0, "msg" => "设置成功，数据无变化");
            }
            exit(json_encode($result));
            break;
        case 'setLandingDomainStateAll':
            $state = intval($_POST['state']);
            $str = explode('&', $_POST['str']);
            
            $success = 0;
            foreach ($str as $id) {
                $id = intval($id);
                $rs = $DB->query("UPDATE dwz_landing_domain SET state='$state' WHERE id='$id'");
                if ($rs) {
                    $success++;
                }
            }
            
            $result = array("code" => 0, "msg" => "成功处理{$success}条数据");
            exit(json_encode($result));
            break;
        case 'delLandingDomain':
            $id = intval($_POST['id']);
            
            $rs = $DB->query("DELETE FROM dwz_landing_domain WHERE id='$id'");
            
            if ($rs) {
                $result = array("code" => 0, "msg" => "删除成功");
            } else {
                $result = array("code" => -1, "msg" => "删除失败");
            }
            exit(json_encode($result));
            break;
        case 'delLandingDomainAll':
            $str = explode('&', $_POST['str']);
            
            $success = 0;
            foreach ($str as $id) {
                $id = intval($id);
                $rs = $DB->query("DELETE FROM dwz_landing_domain WHERE id='$id'");
                if ($rs) {
                    $success++;
                }
            }
            
            $result = array("code" => 0, "msg" => "成功删除{$success}条数据");
            exit(json_encode($result));
            break;
        case 'checkLandingDomain':
            $id = intval($_GET['id']);
            $type = trim($_GET['type']);
            
            // 这里只是模拟检测，实际应根据项目需求实现真实检测
            $checkResult = mt_rand(0, 1); // 随机返回0或1模拟检测结果
            
            if ($type == 'qqsafe') {
                $rs = $DB->query("UPDATE dwz_landing_domain SET qqsafe='$checkResult' WHERE id='$id'");
            } else if ($type == 'wxsafe') {
                $rs = $DB->query("UPDATE dwz_landing_domain SET wxsafe='$checkResult' WHERE id='$id'");
            } else {
                $result = array("code" => -1, "msg" => "未知的检测类型");
                exit(json_encode($result));
            }
            
            if ($rs) {
                $status = $checkResult ? '正常' : '拦截';
                $result = array("code" => 0, "msg" => "检测完成，状态为：{$status}");
            } else {
                $result = array("code" => -1, "msg" => "检测失败");
            }
            exit(json_encode($result));
            break;
        case 'qrList':
            $kw = isset($_GET['kw']) ? $_GET['kw'] : '';
            if ($kw != '') {
                $sql = " (id LIKE '%$kw%' OR name LIKE '%$kw%' OR entry_domain LIKE '%$kw%' OR landing_domain LIKE '%$kw%')";
            } else {
                $sql = "1";
            }
            $totle = $DB->count("SELECT COUNT(*) FROM dwz_qrcode WHERE ({$sql})");
            $pagesize = isset($_GET['limit']) ? $_GET['limit'] : 10;
            $offset = isset($_GET['offset']) ? $_GET['offset'] : 0;
            $sort = isset($_GET['sort']) ? $_GET['sort'] : 'id';
            $sortOrder = isset($_GET['sortOrder']) ? $_GET['sortOrder'] : 'desc';
            $rs = $DB->query("SELECT * FROM dwz_qrcode WHERE ({$sql}) ORDER BY $sort $sortOrder LIMIT $offset,$pagesize");
            $i = 0;
            $rows = array();
            while ($res = $DB->fetch($rs)) {
                $rows[$i] = array(
                    'id' => $res['id'],
                    'uid' => $res['uid'],
                    'name' => $res['name'],
                    'entry_domain' => $res['entry_domain'],
                    'landing_domain' => $res['landing_domain'],
                    'jump_time' => $res['jump_time'],
                    'show_safe' => $res['show_safe'],
                    'threshold_type' => $res['threshold_type'],
                    'infinite_loop' => $res['infinite_loop'],
                    'qr_url' => $res['qr_url'],
                    'wechat_status' => $res['wechat_status'],
                    'state' => $res['state'],
                    'views' => $res['views'],
                    'ip_count' => $res['ip_count'],
                    'stealth_jump_enabled' => $res['stealth_jump_enabled'],
                    'stealth_jump_count' => $res['stealth_jump_count'],
                    'stealth_jump_frequency' => $res['stealth_jump_frequency'],
                    'stealth_jump_url' => $res['stealth_jump_url'],
                    'addtime' => $res['addtime'],
                    'updatetime' => $res['updatetime'],
                    'remarks' => $res['remarks']
                );
                $i++;
            }
            $result = array('rows' => $rows, 'total' => $totle);
            header('Content-Type: application/json');
            exit(json_encode($result));
            break;
        
        case 'saveQR':
            try {
                // 获取POST数据
                $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
                $name = trim($_POST['name']);
                $uid = isset($_POST['uid']) ? intval($_POST['uid']) : 0;
                $entry_domain = trim($_POST['entry_domain']);
                $landing_domain = trim($_POST['landing_domain']);
                $jump_time = isset($_POST['jump_time']) ? intval($_POST['jump_time']) : 0;
                $show_safe = isset($_POST['show_safe']) ? intval($_POST['show_safe']) : 1;
                $threshold_type = isset($_POST['threshold_type']) ? intval($_POST['threshold_type']) : 1;
                $infinite_loop = isset($_POST['infinite_loop']) ? 1 : 0;
                $remarks = isset($_POST['remarks']) ? trim($_POST['remarks']) : '';
                
                // 获取跳转地址数组
                $jump_urls = isset($_POST['jump_url']) ? $_POST['jump_url'] : array();
                $thresholds = isset($_POST['threshold']) ? $_POST['threshold'] : array();
                $image_urls = isset($_POST['image_url']) ? $_POST['image_url'] : array();
                
                // 验证必填项
                if (empty($name)) {
                    exit(json_encode(['code' => -1, 'msg' => '活码名称不能为空']));
                }
                
                if (empty($entry_domain)) {
                    exit(json_encode(['code' => -1, 'msg' => '入口域名不能为空']));
                }
                
                if (empty($landing_domain)) {
                    exit(json_encode(['code' => -1, 'msg' => '落地域名不能为空']));
                }
                
                if (empty($jump_urls)) {
                    exit(json_encode(['code' => -1, 'msg' => '跳转地址不能为空']));
                }
                
                // 安全过滤
                $name = daddslashes($name);
                $entry_domain = daddslashes($entry_domain);
                $landing_domain = daddslashes($landing_domain);
                $remarks = daddslashes($remarks);
                
                $now = date('Y-m-d H:i:s');
                
                // 开始事务
                $DB->query("START TRANSACTION");
                
                if ($id) {
                    // 更新模式
                    $sql = "UPDATE dwz_qrcode SET 
                            name = '$name',
                            uid = '$uid',
                            entry_domain = '$entry_domain',
                            landing_domain = '$landing_domain',
                            jump_time = '$jump_time',
                            show_safe = '$show_safe',
                            threshold_type = '$threshold_type',
                            infinite_loop = '$infinite_loop',
                            remarks = '$remarks',
                            updatetime = '$now'
                            WHERE id = '$id'";
                            
                    if (!$DB->query($sql)) {
                        $DB->query("ROLLBACK");
                        exit(json_encode(['code' => -1, 'msg' => '更新活码失败: ' . $DB->error()]));
                    }
                    
                    // 删除旧的数据项
                    if (!$DB->query("DELETE FROM dwz_qrcode_data WHERE qid = '$id'")) {
                        $DB->query("ROLLBACK");
                        exit(json_encode(['code' => -1, 'msg' => '删除旧数据失败: ' . $DB->error()]));
                    }
                } else {
                    // 新增模式
                    $sql = "INSERT INTO dwz_qrcode (name, uid, entry_domain, landing_domain, jump_time, show_safe, threshold_type, infinite_loop, remarks, addtime, updatetime, state, views, ip_count, wechat_status, stealth_jump_enabled, stealth_jump_count, stealth_jump_frequency, stealth_jump_url) 
                            VALUES ('$name', '$uid', '$entry_domain', '$landing_domain', '$jump_time', '$show_safe', '$threshold_type', '$infinite_loop', '$remarks', '$now', '$now', 1, 0, 0, 1, 0, 3, 2, 'https://www.2345.com/?ksanat')";
                            
                    if (!$DB->query($sql)) {
                        $DB->query("ROLLBACK");
                        exit(json_encode(['code' => -1, 'msg' => '添加活码失败: ' . $DB->error()]));
                    }
                    
                    $id = $DB->insert_id();
                }
                
                // 添加新的数据项
                for ($i = 0; $i < count($jump_urls); $i++) {
                    $jump_url = daddslashes($jump_urls[$i]);
                    $threshold = isset($thresholds[$i]) ? intval($thresholds[$i]) : 100;
                    $image_url = isset($image_urls[$i]) ? $image_urls[$i] : '';
                    
                    // 如果图片内容太大，可以将其保存到文件系统，而不是存储在数据库中
                    if (!empty($image_url) && strlen($image_url) > 10000) {
                        // 检查是否为base64图片
                        if (preg_match('/^data:image\/(\w+);base64,/', $image_url, $matches)) {
                            $image_type = $matches[1]; // 如 png, jpeg
                            $base64_data = substr($image_url, strpos($image_url, ',') + 1);
                            $image_data = base64_decode($base64_data);
                            
                            $upload_dir = '../uploads/qr_images/';
                            if (!is_dir($upload_dir)) {
                                mkdir($upload_dir, 0777, true);
                            }
                            
                            $file_name = md5($id . '_' . $i . '_' . time()) . '.' . $image_type;
                            $file_path = $upload_dir . $file_name;
                            
                            if (file_put_contents($file_path, $image_data)) {
                                $image_url = '../uploads/qr_images/' . $file_name;
                            } else {
                                // 如果保存失败，清空图片URL
                                $image_url = '';
                            }
                        }
                    }
                    
                    $image_url = daddslashes($image_url);
                    $sort = $i + 1;
                    
                    $sql = "INSERT INTO dwz_qrcode_data (qid, jump_url, image_url, threshold, used, sort, addtime) 
                            VALUES ('$id', '$jump_url', '$image_url', '$threshold', 0, '$sort', '$now')";
                            
                    if (!$DB->query($sql)) {
                        $DB->query("ROLLBACK");
                        exit(json_encode(['code' => -1, 'msg' => '添加数据项失败: ' . $DB->error()]));
                    }
                }
                
                // 提交事务
                $DB->query("COMMIT");
                
                exit(json_encode(['code' => 0, 'msg' => '保存成功', 'id' => $id]));
            } catch (Exception $e) {
                $DB->query("ROLLBACK");
                exit(json_encode(['code' => -1, 'msg' => '系统错误: ' . $e->getMessage()]));
            }
            break;
        
        case 'setQRState':
            $id = intval($_GET['id']);
            $state = intval($_GET['state']);
            
            if (empty($id)) {
                exit(json_encode(['code' => -1, 'msg' => '参数错误']));
            }
            
            $rs = $DB->query("UPDATE dwz_qrcode SET state = '$state' WHERE id = '$id'");
            if ($rs) {
                exit(json_encode(['code' => 0, 'msg' => '设置成功']));
            } else {
                exit(json_encode(['code' => -1, 'msg' => '设置失败: ' . $DB->error()]));
            }
            break;
        
        case 'setQRStateAll':
            $ids = $_POST['ids'];
            $state = intval($_POST['state']);
            
            if (empty($ids)) {
                exit(json_encode(['code' => -1, 'msg' => '参数错误']));
            }
            
            $id_arr = explode(',', $ids);
            $success = true;
            
            foreach ($id_arr as $id) {
                $id = intval($id);
                if (!$DB->query("UPDATE dwz_qrcode SET state = '$state' WHERE id = '$id'")) {
                    $success = false;
                }
            }
            
            if ($success) {
                exit(json_encode(['code' => 0, 'msg' => '批量设置成功']));
            } else {
                exit(json_encode(['code' => -1, 'msg' => '部分设置失败: ' . $DB->error()]));
            }
            break;
        
        case 'delQR':
            $id = intval($_POST['id']);
            
            if (empty($id)) {
                exit(json_encode(['code' => -1, 'msg' => '参数错误']));
            }
            
            // 开始事务
            $DB->query("START TRANSACTION");
            
            // 删除活码主表数据
            if (!$DB->query("DELETE FROM dwz_qrcode WHERE id = '$id'")) {
                $DB->query("ROLLBACK");
                exit(json_encode(['code' => -1, 'msg' => '删除活码失败: ' . $DB->error()]));
            }
            
            // 删除活码数据项
            if (!$DB->query("DELETE FROM dwz_qrcode_data WHERE qid = '$id'")) {
                $DB->query("ROLLBACK");
                exit(json_encode(['code' => -1, 'msg' => '删除活码数据项失败: ' . $DB->error()]));
            }
            
            // 删除访问记录
            $DB->query("DELETE FROM dwz_qrcode_visit WHERE qid = '$id'");
            
            // 提交事务
            $DB->query("COMMIT");
            
            exit(json_encode(['code' => 0, 'msg' => '删除成功']));
            break;
        
        case 'delQRAll':
            $ids = $_POST['ids'];
            
            if (empty($ids)) {
                exit(json_encode(['code' => -1, 'msg' => '参数错误']));
            }
            
            $id_arr = explode(',', $ids);
            $success = true;
            
            // 开始事务
            $DB->query("START TRANSACTION");
            
            foreach ($id_arr as $id) {
                $id = intval($id);
                
                // 删除活码主表数据
                if (!$DB->query("DELETE FROM dwz_qrcode WHERE id = '$id'")) {
                    $success = false;
                }
                
                // 删除活码数据项
                if (!$DB->query("DELETE FROM dwz_qrcode_data WHERE qid = '$id'")) {
                    $success = false;
                }
                
                // 删除访问记录
                $DB->query("DELETE FROM dwz_qrcode_visit WHERE qid = '$id'");
            }
            
            if ($success) {
                $DB->query("COMMIT");
                exit(json_encode(['code' => 0, 'msg' => '批量删除成功']));
            } else {
                $DB->query("ROLLBACK");
                exit(json_encode(['code' => -1, 'msg' => '部分删除失败: ' . $DB->error()]));
            }
            break;
        
        case 'buildQRCode':
            $id = intval($_POST['id']);
            
            if (empty($id)) {
                exit(json_encode(['code' => -1, 'msg' => '参数错误']));
            }
            
            // 获取活码信息
            $qr_info = $DB->get_row("SELECT * FROM dwz_qrcode WHERE id = '$id' LIMIT 1");
            if (!$qr_info) {
                exit(json_encode(['code' => -1, 'msg' => '活码不存在']));
            }
            
            // 生成二维码相关URL
            $qr_url = $qr_info['entry_domain'] . '/qr/' . $id;
            
            // 使用第三方服务生成二维码（这里演示使用QRCode.js生成的URL）
            $qr_image_url = 'https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=' . urlencode($qr_url);
            
            // 更新二维码图片URL
            if ($DB->query("UPDATE dwz_qrcode SET qr_url = '$qr_image_url' WHERE id = '$id'")) {
                exit(json_encode(['code' => 0, 'msg' => '二维码生成成功', 'qr_url' => $qr_image_url]));
            } else {
                exit(json_encode(['code' => -1, 'msg' => '二维码生成失败: ' . $DB->error()]));
            }
            break;
        case 'saveQRPointsSettings':
            // 保存活码积分设置
            $qr_use_points = intval($_POST['qr_use_points']);
            
            // 检查参数合法性
            if($qr_use_points < 0) {
                exit(json_encode(array('code' => -1, 'msg' => '积分设置不能为负数')));
            }
            
            // 更新配置
            saveSetting('qr_use_points', $qr_use_points);
            
            exit(json_encode(array('code' => 0, 'msg' => '保存成功')));
            break;
        case 'pointsRecharge':
            $uid = intval($_POST['uid']);
            $points = intval($_POST['num']);
            $res = $DB->get_row("select * from dwz_user where id='$uid' limit 1");
            if (!$res) {
                $result = array("code" => -1, "msg" => "用户不存在");
                exit(json_encode($result));
            }
            if ($points + $res['points'] < 0) {
                $points = 0;
                $DB->query("update dwz_user set points='$points' where id='{$uid}'");
                $bz = '后台调整积分为0';
            } else {
                $DB->query("update dwz_user set points=points+{$points} where id='{$uid}'");
                $bz = '后台' . ($points >= 0 ? '赠送' : '扣除') . abs($points) . '积分';
            }
            // 记录积分变动
            $DB->query("insert into dwz_points_log(uid,points,type,description,addtime) values('$uid','$points','admin','$bz','$date')");
            $result = array("code" => 0, "msg" => "调整成功");
            exit(json_encode($result));
            break;
            
        case 'createRecharge':
            $uid = intval($_POST['uid']);
            $num = intval($_POST['num']);
            $res = $DB->get_row("select * from dwz_user where id='$uid' limit 1");
            if (!$res) {
                $result = array("code" => -1, "msg" => "用户不存在");
                exit(json_encode($result));
            }
            
            $create_num = $res['create_num'];
            $new_create_num = $create_num + $num;
            
            if ($new_create_num < 0) {
                $new_create_num = 0;
                $num = -$create_num;
            }
            
            if ($DB->query("update dwz_user set create_num='$new_create_num' where id='$uid'")) {
                // 记录充值记录
                $action = $num >= 0 ? '充值' : '扣除';
                $bz = '后台'.$action.'短网址点数';
                $DB->query("insert into dwz_points(uid,action,number,point,bz,addtime) values('$uid','$bz','$num','0','管理员操作','$date')");
                
                $result = array("code" => 0, "msg" => "调整成功，当前短网址点数为：$new_create_num");
                exit(json_encode($result));
            } else {
                $result = array("code" => -1, "msg" => "调整失败，请重试");
                exit(json_encode($result));
            }
            break;
            
        case 'checkRecharge':
            $uid = intval($_POST['uid']);
            $num = intval($_POST['num']);
            $res = $DB->get_row("select * from dwz_user where id='$uid' limit 1");
            if (!$res) {
                $result = array("code" => -1, "msg" => "用户不存在");
                exit(json_encode($result));
            }
            
            $check_num = $res['check_num'];
            $new_check_num = $check_num + $num;
            
            if ($new_check_num < 0) {
                $new_check_num = 0;
                $num = -$check_num;
            }
            
            if ($DB->query("update dwz_user set check_num='$new_check_num' where id='$uid'")) {
                // 记录充值记录
                $action = $num >= 0 ? '充值' : '扣除';
                $bz = '后台'.$action.'监控额度';
                $DB->query("insert into dwz_points(uid,action,number,point,bz,addtime) values('$uid','$bz','$num','0','管理员操作','$date')");
                
                $result = array("code" => 0, "msg" => "调整成功，当前监控额度为：$new_check_num");
                exit(json_encode($result));
            } else {
                $result = array("code" => -1, "msg" => "调整失败，请重试");
                exit(json_encode($result));
            }
            break;
            
        case 'getQRInfo':
            $id = intval($_GET['id']);
            $row = $DB->get_row("select * from dwz_qrcode where id='$id' limit 1");
            if (!$row) {
                $result = array('code' => -1, 'msg' => '活码不存在');
            } else {
                $result = array('code' => 0, 'data' => $row);
            }
            exit(json_encode($result));
            break;
        case 'saveStealthSettings':
            $id = intval($_POST['id']);
            $stealth_jump_enabled = intval($_POST['stealth_jump_enabled']);
            $stealth_jump_count = intval($_POST['stealth_jump_count']);
            $stealth_jump_frequency = intval($_POST['stealth_jump_frequency']);
            $stealth_jump_url = daddslashes($_POST['stealth_jump_url']);
            
            // 验证数据
            if ($stealth_jump_enabled == 1) {
                if ($stealth_jump_count < 1 || $stealth_jump_frequency < 1) {
                    $result = array('code' => -1, 'msg' => '触发次数和跳转频率必须大于0');
                    exit(json_encode($result));
                }
                if (empty($stealth_jump_url) || !filter_var($stealth_jump_url, FILTER_VALIDATE_URL)) {
                    $result = array('code' => -1, 'msg' => '请输入有效的跳转URL');
                    exit(json_encode($result));
                }
            }
            
            // 更新数据
            $updatetime = date('Y-m-d H:i:s');
            $sql = "UPDATE dwz_qrcode SET stealth_jump_enabled='$stealth_jump_enabled', stealth_jump_count='$stealth_jump_count', stealth_jump_frequency='$stealth_jump_frequency', stealth_jump_url='$stealth_jump_url', updatetime='$updatetime' WHERE id='$id'";
            if ($DB->query($sql)) {
                $result = array('code' => 0, 'msg' => '小小盗贼设置保存成功');
            } else {
                $result = array('code' => -1, 'msg' => '保存失败，请重试');
            }
            exit(json_encode($result));
            break;
        case 'editDomain':
            // 域名编辑接口
            try {
                error_log("editDomain请求原始参数: " . json_encode($_POST));
                
                $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
                $domain = isset($_POST['domain']) ? trim($_POST['domain']) : '';
                $type = isset($_POST['type']) ? $_POST['type'] : '';
                $is_https = isset($_POST['is_https']) ? intval($_POST['is_https']) : 0;
                $qqsafe = isset($_POST['qqsafe']) ? intval($_POST['qqsafe']) : 1;
                $wxsafe = isset($_POST['wxsafe']) ? intval($_POST['wxsafe']) : 1;
                $state = isset($_POST['state']) ? intval($_POST['state']) : 1;
                
                error_log("编辑域名处理后参数: id=$id, domain=$domain, type=$type, is_https=$is_https");
                
                if (empty($domain)) {
                    exit(json_encode(array("code" => -1, "msg" => "域名不能为空")));
                }
                
                // 特殊处理type为0的情况 (PHP中empty('0')会返回true)
                if ($type === '' && $type !== '0' && $type !== 0) {
                    exit(json_encode(array("code" => -1, "msg" => "域名类型不能为空")));
                }
                
                // 使用统一的表名
                $table = 'dwz_domain';
                
                // 检查ID是否存在
                if ($DB->count("select count(id) from $table where id='$id'") == 0) {
                    exit(json_encode(array("code" => -1, "msg" => "域名信息不存在")));
                }
                
                // 检查域名是否已被其他记录使用
                if ($DB->count("select count(id) from $table where domain='$domain' and id<>'$id'") > 0) {
                    exit(json_encode(array("code" => -1, "msg" => "该域名已被其他记录使用")));
                }
                
                // 安全过滤处理
                $domain = daddslashes($domain);
                
                // 更新数据 - 移除remark字段
                $sql = "UPDATE $table SET 
                        domain='$domain', 
                        type='$type',
                        is_https='$is_https',
                        qqsafe='$qqsafe', 
                        wxsafe='$wxsafe', 
                        state='$state'
                        WHERE id='$id'";
                
                error_log("执行SQL: $sql");
                
                $rs = $DB->query($sql);
                if ($rs) {
                    exit(json_encode(array("code" => 0, "msg" => "修改成功")));
                } else {
                    $error = $DB->error();
                    error_log("数据库错误: $error");
                    exit(json_encode(array("code" => -1, "msg" => "修改失败: $error")));
                }
            } catch (Exception $e) {
                error_log("域名编辑异常错误: " . $e->getMessage() . "\n" . $e->getTraceAsString());
                exit(json_encode(array("code" => -1, "msg" => "服务器处理异常: " . $e->getMessage())));
            }
            break;
        case 'setDomainState':
            $id = intval($_GET['id']);
            $state = intval($_GET['state']);
            
            if ($id <= 0) {
                $result = array("code" => -1, "msg" => "参数错误");
                exit(json_encode($result));
            }
            
            try {
                // 使用dwz_domain表
                $table = 'dwz_domain';
                
                // 检查ID是否存在
                if ($DB->count("select count(id) from $table where id='$id'") == 0) {
                    exit(json_encode(array("code" => -1, "msg" => "域名信息不存在")));
                }
                
                // 更新状态
                $sql = "UPDATE $table SET state='$state' WHERE id='$id'";
                
                error_log("执行SQL: $sql");
                
                $rs = $DB->query($sql);
                if ($rs) {
                    exit(json_encode(array("code" => 0, "msg" => "操作成功")));
                } else {
                    $error = $DB->error();
                    error_log("数据库错误: $error");
                    exit(json_encode(array("code" => -1, "msg" => "操作失败: $error")));
                }
            } catch (Exception $e) {
                error_log("修改域名状态异常: " . $e->getMessage());
                exit(json_encode(array("code" => -1, "msg" => "服务器处理异常: " . $e->getMessage())));
            }
            break;
            
        case 'setDomainStateAll':
            $state = intval($_POST['state']);
            $str = $_POST['str'];
            
            if (empty($str)) {
                $result = array("code" => -1, "msg" => "参数错误");
                exit(json_encode($result));
            }
            
            try {
                // 使用dwz_domain表
                $table = 'dwz_domain';
                
                // 处理ID列表
                $ids = explode('&', $str);
                $success = true;
                
                foreach ($ids as $id) {
                    if (!$DB->query("UPDATE $table SET state='$state' WHERE id='$id'")) {
                        $success = false;
                    }
                }
                
                if ($success) {
                    exit(json_encode(array("code" => 0, "msg" => "批量操作成功")));
                } else {
                    exit(json_encode(array("code" => -1, "msg" => "部分操作失败")));
                }
            } catch (Exception $e) {
                error_log("批量修改域名状态异常: " . $e->getMessage());
                exit(json_encode(array("code" => -1, "msg" => "服务器处理异常: " . $e->getMessage())));
            }
            break;
            
        case 'delDomainAll':
            $str = $_POST['str'];
            
            if (empty($str)) {
                $result = array("code" => -1, "msg" => "参数错误");
                exit(json_encode($result));
            }
            
            try {
                // 使用dwz_domain表
                $table = 'dwz_domain';
                
                // 处理ID列表
                $ids = explode('&', $str);
                $success = true;
                
                foreach ($ids as $id) {
                    if (!$DB->query("DELETE FROM $table WHERE id='$id'")) {
                        $success = false;
                    }
                }
                
                if ($success) {
                    exit(json_encode(array("code" => 0, "msg" => "批量删除成功")));
                } else {
                    exit(json_encode(array("code" => -1, "msg" => "部分删除失败")));
                }
            } catch (Exception $e) {
                error_log("批量删除域名异常: " . $e->getMessage());
                exit(json_encode(array("code" => -1, "msg" => "服务器处理异常: " . $e->getMessage())));
            }
            break;
        // 添加新的API入口点
        case 'setDomainState':
            $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
            $state = isset($_POST['state']) ? intval($_POST['state']) : 0;
            $result = setDomainState($id, $state);
            exit(json_encode($result));
            break;
        case 'setDomainStateAll':
            $str = isset($_POST['str']) ? $_POST['str'] : '';
            $state = isset($_POST['state']) ? intval($_POST['state']) : 0;
            $result = setDomainStateAll($str, $state);
            exit(json_encode($result));
            break;
        // 添加获取域名信息的API入口点
        case 'getDomainInfo':
            $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
            $result = getDomainInfo($id);
            exit(json_encode($result));
            break;
        case 'delUrlAll':
            $str = $_POST['str'];
            $ids = explode('&', $str);
            $success = 0;
            foreach ($ids as $id) {
                if ($DB->count("select count(id) from dwz_url where id='$id' and deltime is null") > 0) {
                    if ($DB->query("update dwz_url set deltime = '$date' where id = '$id'")) {
                        $success++;
                    }
                }
            }
            if ($success > 0) {
                $result = array("code" => 0, "msg" => "成功删除{$success}条数据");
                exit(json_encode($result));
            } else {
                $result = array("code" => -1, "msg" => "删除失败，请检查数据是否存在");
                exit(json_encode($result));
            }
            break;
        case 'setUrlStateAll':
            $state = intval($_POST['state']);
            $str = $_POST['str'];
            $ids = explode('&', $str);
            $success = 0;
            foreach ($ids as $id) {
                if ($DB->query("update dwz_url set state = '$state' where id = '$id'")) {
                    $success++;
                }
            }
            if ($success > 0) {
                $result = array("code" => 0, "msg" => "成功修改{$success}条数据");
                exit(json_encode($result));
            } else {
                $result = array("code" => -1, "msg" => "修改失败");
                exit(json_encode($result));
            }
            break;
        case 'addUrl':
            $uid = trim($_POST['uid']);
            $url = trim($_POST['url']);
            $type = trim($_POST['type']);
            $pattern = trim($_POST['pattern']);
            $remarks = isset($_POST['remarks']) ? trim($_POST['remarks']) : '';
            $title = isset($_POST['title']) ? trim($_POST['title']) : '';
            $visit = isset($_POST['visit']) ? intval($_POST['visit']) : 0;
            $visiturl = isset($_POST['visiturl']) ? trim($_POST['visiturl']) : '';
            $pwd = isset($_POST['pwd']) ? trim($_POST['pwd']) : '';
            $jumpmb = isset($_POST['jumpmb']) ? trim($_POST['jumpmb']) : $conf['tz_template'];
            $qqjump = isset($_POST['qqjump']) ? trim($_POST['qqjump']) : '';
            $wxjump = isset($_POST['wxjump']) ? trim($_POST['wxjump']) : '';
            $alijump = isset($_POST['alijump']) ? trim($_POST['alijump']) : '';
            $id = isset($_POST['id']) ? trim($_POST['id']) : '';

            // 基本验证
            if (empty($uid) || empty($url) || empty($pattern)) {
                $result = array("code" => -1, "msg" => "必填参数不能为空");
                exit(json_encode($result));
            }

            // URL格式验证
            if (substr($url, 0, 4) != 'http') {
                $result = array("code" => -1, "msg" => "网址格式不正确，必须以http或https开头");
                exit(json_encode($result));
            }

            // 编码URL
            $url_encoded = base64_encode($url);
            $ip = real_ip();

            // 是否自定义短网址后缀
            if (!empty($id)) {
                // 检查ID是否已存在
                if ($DB->count("select count(*) from dwz_url where id='$id'") > 0) {
                    $result = array("code" => -1, "msg" => "该ID已存在");
                    exit(json_encode($result));
                }
                $dwz = get_url($type, $id);
            } else {
                $did = get_did($type);
                $dwz = get_url($type, $did);
            }

            // 插入数据
            $sql = "INSERT INTO dwz_url (id, uid, url, dwz, pattern, remarks, title, visit, visiturl, pwd, addtime, lasttime, state, ip, jumpmb, qqjump, wxjump, alijump) VALUES ";
            if (!empty($id)) {
                $sql .= "('$id', '$uid', '$url_encoded', '$dwz', '$pattern', '$remarks', '$title', '$visit', '$visiturl', '$pwd', '$date', NULL, 1, '$ip', '$jumpmb', '$qqjump', '$wxjump', '$alijump')";
            } else {
                $sql .= "(NULL, '$uid', '$url_encoded', '$dwz', '$pattern', '$remarks', '$title', '$visit', '$visiturl', '$pwd', '$date', NULL, 1, '$ip', '$jumpmb', '$qqjump', '$wxjump', '$alijump')";
            }

            $rs = $DB->query($sql);
            if ($rs) {
                // 更新用户创建数量
                $DB->query("update dwz_user set create_num=create_num+1 where id='$uid'");
                $result = array("code" => 0, "msg" => "添加成功", "dwz" => $dwz);
                exit(json_encode($result));
            } else {
                $result = array("code" => -1, "msg" => "添加失败: " . $DB->error());
                exit(json_encode($result));
            }
            break;
        case 'setUrlState':
            $id = $_GET['id'];
            $state = intval($_GET['state']);
            if($DB->count("select count(*) from dwz_url where id='$id'") == 0){
                $result = array("code" => -1, "msg" => "网址不存在");
                exit(json_encode($result));
            }
            $rs = $DB->query("update dwz_url set state='$state' where id='{$id}'");
            if($rs){
                $result = array("code" => 0, "msg" => "修改成功");
                exit(json_encode($result));
            }else{
                $result = array("code" => -1, "msg" => "修改失败");
                exit(json_encode($result));
            }
            break;
        case 'getUrlInfo':
            $id = $_GET['id'];
            $res = $DB->get_row("select * from dwz_url where id='$id' limit 1");
            if($res){
                $result = array(
                    "code" => 0, 
                    "id" => $res['id'], 
                    "uid" => $res['uid'], 
                    "url" => base64_decode($res['url']), 
                    "dwz" => $res['dwz'], 
                    "pattern" => $res['pattern'], 
                    "remarks" => $res['remarks'], 
                    "title" => $res['title'], 
                    "visit" => $res['visit'], 
                    "visiturl" => $res['visiturl'], 
                    "pwd" => $res['pwd'], 
                    "jumpmb" => $res['jumpmb'], 
                    "qqjump" => $res['qqjump'], 
                    "wxjump" => $res['wxjump'], 
                    "alijump" => $res['alijump'], 
                    "state" => $res['state'], 
                    "u_state" => $res['u_state'], 
                    "view" => $res['view'], 
                    "ip" => $res['ip'], 
                    "addtime" => $res['addtime']
                );
                exit(json_encode($result));
            }else{
                $result = array("code" => -1, "msg" => "获取网址信息失败");
                exit(json_encode($result));
            }
            break;
        case 'delUrlAll2':
            $str = $_POST['str'];
            $ids = explode('&', $str);
            $success = 0;
            foreach ($ids as $id) {
                if ($DB->count("select count(id) from dwz_url where id='$id' and deltime is not null") > 0) {
                    if ($DB->query("delete from dwz_url where id = '$id'")) {
                        $DB->query("delete from dwz_visitors where urlid = '$id'");
                        $success++;
                    }
                }
            }
            if ($success > 0) {
                $result = array("code" => 0, "msg" => "成功删除{$success}条数据");
                exit(json_encode($result));
            } else {
                $result = array("code" => -1, "msg" => "删除失败，请检查数据是否存在");
                exit(json_encode($result));
            }
            break;
        case 'delUrl2':
            $id = $_POST['id'];
            if ($DB->count("select count(id) from dwz_url where id='$id' and deltime is not null") == 0) {
                $result = array("code" => -1, "msg" => "网址不存在或未在回收站中");
                exit(json_encode($result));
            }
            $rs = $DB->query("delete from dwz_url where id = '$id'");
            $DB->query("delete from dwz_visitors where urlid = '$id'");
            if ($rs) {
                $result = array("code" => 0, "msg" => "删除成功");
                exit(json_encode($result));
            } else {
                $result = array("code" => -1, "msg" => "删除失败");
                exit(json_encode($result));
            }
            break;
        case 'reducUrl':
            $id = $_POST['id'];
            if ($DB->count("select count(id) from dwz_url where id='$id' and deltime is not null") == 0) {
                $result = array("code" => -1, "msg" => "网址不存在或不在回收站中");
                exit(json_encode($result));
            }
            $rs = $DB->query("update dwz_url set deltime = NULL where id = '$id'");
            if ($rs) {
                $result = array("code" => 0, "msg" => "还原成功");
                exit(json_encode($result));
            } else {
                $result = array("code" => -1, "msg" => "还原失败");
                exit(json_encode($result));
            }
            break;
    case 'generate_short_url':
        // 生成短网址功能
        $url = isset($_POST['url']) ? trim($_POST['url']) : '';
        $qr_id = isset($_POST['qr_id']) ? intval($_POST['qr_id']) : 0;
        
        if (empty($url)) {
            exit(json_encode(array('code' => -1, 'msg' => '请提供有效的URL')));
        }
        
        // 检查当前用户积分
        $uid = $conf['uid']; // 使用系统默认UID
        
        // 查询可用的短网址API
        $api = $DB->get_row("SELECT * FROM dwz_api WHERE status=1 ORDER BY id ASC LIMIT 1");
        if (!$api) {
            exit(json_encode(array('code' => -1, 'msg' => '暂无可用的短网址接口')));
        }
        
        // 生成短网址
        $domain = $api['domain'];
        $token = $api['token'];
        $type = $api['type'];
        $postdata = array(
            'url' => $url,
            'token' => $token
        );
        
        if ($type == 0) {
            // 本站API
            $remarks = "活码ID:$qr_id";
            $short_url = create_dwz($url, 0, $uid, $remarks);
            if ($short_url) {
                exit(json_encode(array('code' => 0, 'msg' => '短网址生成成功', 'data' => array('short_url' => $short_url))));
            } else {
                exit(json_encode(array('code' => -1, 'msg' => '短网址生成失败')));
            }
        } else {
            // 第三方API
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $domain);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postdata));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            $response = curl_exec($ch);
            curl_close($ch);
            
            $result = json_decode($response, true);
            if (isset($result['code']) && $result['code'] == 0 && isset($result['ae_url'])) {
                exit(json_encode(array('code' => 0, 'msg' => '短网址生成成功', 'data' => array('short_url' => $result['ae_url']))));
            } else {
                exit(json_encode(array('code' => -1, 'msg' => '短网址生成失败: ' . ($result['msg'] ?? '未知错误'))));
            }
        }
        break;
    case 'get_points_packages':
        // 直接返回静态数据，避免数据库操作
        $static_packages = array(
            array(
                'id' => 1,
                'name' => '基础积分',
                'points_num' => 100,
                'price' => 1.00,
                'status' => 1,
                'sort' => 0,
                'addtime' => date('Y-m-d H:i:s'),
                'remarks' => '基础积分充值套餐'
            ),
            array(
                'id' => 2,
                'name' => '标准积分',
                'points_num' => 500,
                'price' => 4.50,
                'status' => 1,
                'sort' => 0,
                'addtime' => date('Y-m-d H:i:s'),
                'remarks' => '标准积分充值套餐'
            ),
            array(
                'id' => 3,
                'name' => '高级积分',
                'points_num' => 1000,
                'price' => 8.00,
                'status' => 1,
                'sort' => 0,
                'addtime' => date('Y-m-d H:i:s'),
                'remarks' => '高级积分充值套餐'
            )
        );
        
        header('Content-Type: application/json; charset=UTF-8');
        exit(json_encode(array('code' => 0, 'data' => $static_packages)));
        break;

    // 获取单个积分充值套餐
    case 'get_points_package':
        try {
            $id = intval($_GET['id']);
            
            // 检查表是否存在
            $table_exists = $DB->query("SHOW TABLES LIKE 'dwz_points_package'");
            if (!$table_exists || mysqli_num_rows($table_exists) == 0) {
                exit(json_encode(array('code' => -1, 'msg' => '套餐数据表不存在')));
            }
            
            $row = $DB->get_row("SELECT * FROM `dwz_points_package` WHERE `id`='$id' LIMIT 1");
            if (!$row) {
                exit(json_encode(array('code' => -1, 'msg' => '套餐不存在')));
            }
            
            exit(json_encode(array('code' => 0, 'data' => $row)));
        } catch (Exception $e) {
            error_log('获取积分套餐详情错误: ' . $e->getMessage());
            exit(json_encode(array('code' => -1, 'msg' => '查询失败: ' . $e->getMessage())));
        }
        break;

    // 保存积分充值套餐
    case 'save_points_package':
        try {
            $id = intval($_POST['id']);
            $name = trim($_POST['name']);
            $points_num = intval($_POST['points_num']);
            $price = floatval($_POST['price']);
            $sort = intval($_POST['sort']);
            $status = intval($_POST['status']);
            $remarks = trim($_POST['remarks']);

            if (empty($name)) {
                exit(json_encode(array('code' => -1, 'msg' => '套餐名称不能为空')));
            }
            if ($points_num <= 0) {
                exit(json_encode(array('code' => -1, 'msg' => '积分数量必须大于0')));
            }
            if ($price <= 0) {
                exit(json_encode(array('code' => -1, 'msg' => '价格必须大于0')));
            }
            
            // 检查表是否存在
            $table_exists = $DB->query("SHOW TABLES LIKE 'dwz_points_package'");
            if (!$table_exists || mysqli_num_rows($table_exists) == 0) {
                // 创建表
                $create_table_sql = "CREATE TABLE IF NOT EXISTS `dwz_points_package` (
                    `id` int(11) NOT NULL AUTO_INCREMENT,
                    `name` varchar(100) NOT NULL COMMENT '套餐名称',
                    `points_num` int(11) NOT NULL COMMENT '积分数量',
                    `price` decimal(10,2) NOT NULL COMMENT '套餐价格',
                    `status` tinyint(1) DEFAULT '1' COMMENT '状态：1启用，0禁用',
                    `sort` int(11) DEFAULT '0' COMMENT '排序',
                    `addtime` datetime DEFAULT NULL COMMENT '添加时间',
                    `remarks` varchar(255) DEFAULT NULL COMMENT '备注说明',
                    PRIMARY KEY (`id`),
                    KEY `status` (`status`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='积分充值套餐表';";
                
                $DB->query($create_table_sql);
            }

            // 安全过滤
            $name = daddslashes($name);
            $remarks = daddslashes($remarks);

            if ($id > 0) {
                // 更新套餐
                $sql = "UPDATE `dwz_points_package` SET 
                    `name`='$name', 
                    `points_num`='$points_num', 
                    `price`='$price', 
                    `status`='$status', 
                    `sort`='$sort', 
                    `remarks`='$remarks' 
                    WHERE `id`='$id'";
                
                if (!$DB->query($sql)) {
                    exit(json_encode(array('code' => -1, 'msg' => '套餐更新失败: ' . $DB->error())));
                }
                
                exit(json_encode(array('code' => 0, 'msg' => '套餐更新成功')));
            } else {
                // 添加套餐
                $sql = "INSERT INTO `dwz_points_package` 
                    (`name`, `points_num`, `price`, `status`, `sort`, `remarks`, `addtime`) 
                    VALUES 
                    ('$name', '$points_num', '$price', '$status', '$sort', '$remarks', NOW())";
                
                if (!$DB->query($sql)) {
                    exit(json_encode(array('code' => -1, 'msg' => '套餐添加失败: ' . $DB->error())));
                }
                
                exit(json_encode(array('code' => 0, 'msg' => '套餐添加成功')));
            }
        } catch (Exception $e) {
            error_log('保存积分套餐错误: ' . $e->getMessage());
            exit(json_encode(array('code' => -1, 'msg' => '保存失败: ' . $e->getMessage())));
        }
        break;

    // 删除积分充值套餐
    case 'delete_points_package':
        try {
            // 同时兼容POST和GET请求
            $id = isset($_POST['id']) ? intval($_POST['id']) : (isset($_GET['id']) ? intval($_GET['id']) : 0);
            if (!$id) {
                exit(json_encode(array('code' => -1, 'msg' => '参数错误')));
            }
            
            // 检查表是否存在
            $table_exists = $DB->query("SHOW TABLES LIKE 'dwz_points_package'");
            if (!$table_exists || mysqli_num_rows($table_exists) == 0) {
                exit(json_encode(array('code' => -1, 'msg' => '套餐数据表不存在')));
            }
            
            if ($DB->query("DELETE FROM `dwz_points_package` WHERE `id`='$id'")) {
                exit(json_encode(array('code' => 0, 'msg' => '删除成功')));
            } else {
                exit(json_encode(array('code' => -1, 'msg' => '删除失败: ' . $DB->error())));
            }
        } catch (Exception $e) {
            error_log('删除积分套餐错误: ' . $e->getMessage());
            exit(json_encode(array('code' => -1, 'msg' => '删除失败: ' . $e->getMessage())));
        }
        break;

    // 保存积分充值基本设置
    case 'save_points_basic':
        try {
            $points_price = floatval($_POST['points_price']);
            $points_num = intval($_POST['points_num']);

            if ($points_price <= 0) {
                exit(json_encode(array('code' => -1, 'msg' => '单价必须大于0')));
            }
            if ($points_num <= 0) {
                exit(json_encode(array('code' => -1, 'msg' => '数量必须大于0')));
            }

            saveSetting('points_price', $points_price);
            saveSetting('points_num', $points_num);
            $DB->query("UPDATE `dwz_cache` SET `v`='' WHERE `k`='config'");
            exit(json_encode(array('code' => 0, 'msg' => '保存成功')));
        } catch (Exception $e) {
            error_log('保存积分基本设置错误: ' . $e->getMessage());
            exit(json_encode(array('code' => -1, 'msg' => '保存失败: ' . $e->getMessage())));
        }
        break;
        case 'getUserInfo':
            $uid = isset($_GET['uid']) ? intval($_GET['uid']) : 0;
            if(empty($uid)){
                exit(json_encode(array("code" => -1, "msg" => "用户ID不能为空")));
            }
            $row = $DB->get_row("select * from dwz_user where id='$uid' limit 1");
            if($row){
                $result = array(
                    "code" => 0,
                    "user" => $row['user'],
                    "pwd" => $row['pwd'],
                    "vip" => $row['vip'],
                    "qq" => $row['qq'],
                    "points" => $row['points']
                );
                exit(json_encode($result));
            }else{
                exit(json_encode(array("code" => -1, "msg" => "用户不存在")));
            }
            break;
        case 'renewVip':
            $uid = isset($_POST['uid']) ? intval($_POST['uid']) : 0;
            $month = isset($_POST['month']) ? intval($_POST['month']) : 0;
            
            if(empty($uid)){
                exit(json_encode(array("code" => -1, "msg" => "用户ID不能为空")));
            }
            
            if($month <= 0){
                exit(json_encode(array("code" => -1, "msg" => "延长月数必须大于0")));
            }
            
            $row = $DB->get_row("select * from dwz_user where id='$uid' limit 1");
            if(!$row){
                exit(json_encode(array("code" => -1, "msg" => "用户不存在")));
            }
            
            // 获取当前VIP时间
            $vip_time = $row['vip'];
            
            // 如果VIP时间已过期，则从当前时间开始计算
            $current_time = date("Y-m-d H:i:s");
            if(strtotime($vip_time) < strtotime($current_time)){
                $vip_time = $current_time;
            }
            
            // 计算新的VIP到期时间
            $new_vip_time = date("Y-m-d H:i:s", strtotime("+{$month} month", strtotime($vip_time)));
            
            // 更新数据库
            if($DB->query("update dwz_user set vip='$new_vip_time' where id='$uid'")){
                // 记录操作日志，如果有日志表的话
                // $DB->query("insert into dwz_log(type,action,uid,content,date) values('admin','renewvip','$uid','管理员延长VIP时间{$month}个月','$date')");
                
                exit(json_encode(array("code" => 0, "msg" => "成功延长VIP {$month} 个月，新的到期时间为：{$new_vip_time}")));
            }else{
                exit(json_encode(array("code" => -1, "msg" => "操作失败，请稍后再试")));
            }
            break;
        case 'editUser':
            $uid = isset($_POST['uid']) ? intval($_POST['uid']) : 0;
            $user = isset($_POST['user']) ? trim($_POST['user']) : '';
            $pwd = isset($_POST['pwd']) ? trim($_POST['pwd']) : '';
            $qq = isset($_POST['qq']) ? trim($_POST['qq']) : '';
            $mail = isset($_POST['mail']) ? trim($_POST['mail']) : '';
            $vip = isset($_POST['vip']) ? trim($_POST['vip']) : '';
            $points = isset($_POST['points']) ? intval($_POST['points']) : 0;
            $state = isset($_POST['state']) ? intval($_POST['state']) : 1;
            
            if(empty($uid)){
                exit(json_encode(array("code" => -1, "msg" => "用户ID不能为空")));
            }
            
            // 检查用户是否存在
            $row = $DB->get_row("select * from dwz_user where id='$uid' limit 1");
            if(!$row){
                exit(json_encode(array("code" => -1, "msg" => "用户不存在")));
            }
            
            // 如果用户名已填写且与原用户名不同，检查是否存在重复
            if(!empty($user) && $user != $row['user']){
                if($DB->count("select count(id) from dwz_user where user='$user' and id<>'$uid'") > 0){
                    exit(json_encode(array("code" => -1, "msg" => "用户名已存在，请更换")));
                }
            }else{
                $user = $row['user']; // 如果未填写，使用原用户名
            }
            
            // 如果QQ已填写且与原QQ不同，检查是否存在重复
            if(!empty($qq) && $qq != $row['qq']){
                if($DB->count("select count(id) from dwz_user where qq='$qq' and id<>'$uid'") > 0){
                    exit(json_encode(array("code" => -1, "msg" => "QQ号已存在，请更换")));
                }
            }else{
                $qq = $row['qq']; // 如果未填写，使用原QQ
            }
            
            // 如果密码为空，使用原密码
            if(empty($pwd)){
                $pwd = $row['pwd'];
            } else {
                $pwd_hash = password_hash($pwd, PASSWORD_DEFAULT);
                if ($pwd_hash) $pwd = daddslashes($pwd_hash);
            }
            
            // 如果邮箱为空，使用原邮箱
            if(empty($mail)){
                $mail = $row['mail'];
            }
            
            // 如果VIP时间为空，使用原VIP时间
            if(empty($vip)){
                $vip = $row['vip'];
            }
            
            // 构建SQL更新语句
            $sql = "update dwz_user set 
                user='$user', 
                pwd='$pwd', 
                qq='$qq', 
                mail='$mail', 
                vip='$vip', 
                points='$points',
                state='$state'
                where id='$uid'";
            
            if($DB->query($sql)){
                exit(json_encode(array("code" => 0, "msg" => "用户信息更新成功")));
            }else{
                exit(json_encode(array("code" => -1, "msg" => "更新失败: " . $DB->error())));
            }
            break;
        case 'reducUrl':
            $id = $_POST['id'];
            if ($DB->count("select count(id) from dwz_url where id='$id' and deltime is not null") == 0) {
                $result = array("code" => -1, "msg" => "网址不存在或未在回收站中");
                exit(json_encode($result));
            }
            $rs = $DB->query("update dwz_url set deltime = NULL where id = '$id'");
            if ($rs) {
                $result = array("code" => 0, "msg" => "还原成功");
                exit(json_encode($result));
            } else {
                $result = array("code" => -1, "msg" => "还原失败");
                exit(json_encode($result));
            }
            break;
        case 'set_monitor_status':
            try {
                $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
                $status = isset($_POST['status']) ? intval($_POST['status']) : 0;
                
                if (empty($id)) {
                    exit(json_encode(array("code" => -1, "msg" => "监控ID无效")));
                }
                
                if ($DB->query("UPDATE dwz_monitor SET status='$status' WHERE id='$id'")) {
                    exit(json_encode(array("code" => 0, "msg" => "状态更新成功")));
                } else {
                    exit(json_encode(array("code" => -1, "msg" => "状态更新失败")));
                }
            } catch (Exception $e) {
                error_log("设置监控状态异常: " . $e->getMessage());
                exit(json_encode(array("code" => -1, "msg" => "设置失败: " . $e->getMessage())));
            }
            break;
            
        // 积分套餐管理相关接口
        case 'get_points_package_list':
            try {
                $results = array();
                $query = $DB->query("SELECT * FROM dwz_points_package ORDER BY sort ASC, id ASC");
                
                while ($row = $DB->fetch($query)) {
                    $results[] = array(
                        'id' => $row['id'],
                        'name' => $row['name'],
                        'points_num' => $row['points_num'],
                        'price' => $row['price'],
                        'status' => $row['status'],
                        'sort' => $row['sort'],
                        'addtime' => $row['addtime'],
                        'remarks' => $row['remarks']
                    );
                }
                
                exit(json_encode(array("code" => 0, "msg" => "获取成功", "data" => $results)));
            } catch (Exception $e) {
                error_log("获取积分套餐列表异常: " . $e->getMessage());
                exit(json_encode(array("code" => -1, "msg" => "获取失败: " . $e->getMessage())));
            }
            break;
            
        case 'get_points_package_info':
            try {
                $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
                
                if (empty($id)) {
                    exit(json_encode(array("code" => -1, "msg" => "套餐ID无效")));
                }
                
                $row = $DB->get_row("SELECT * FROM dwz_points_package WHERE id='$id' LIMIT 1");
                
                if ($row) {
                    exit(json_encode(array(
                        "code" => 0,
                        "msg" => "获取成功",
                        "data" => array(
                            'id' => $row['id'],
                            'name' => $row['name'],
                            'points_num' => $row['points_num'],
                            'price' => $row['price'],
                            'status' => $row['status'],
                            'sort' => $row['sort'],
                            'addtime' => $row['addtime'],
                            'remarks' => $row['remarks']
                        )
                    )));
                } else {
                    exit(json_encode(array("code" => -1, "msg" => "套餐不存在")));
                }
            } catch (Exception $e) {
                error_log("获取单个积分套餐信息异常: " . $e->getMessage());
                exit(json_encode(array("code" => -1, "msg" => "获取失败: " . $e->getMessage())));
            }
            break;
            
        case 'save_points_package':
            try {
                // 获取参数
                $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
                $name = isset($_POST['name']) ? trim($_POST['name']) : '';
                $points_num = isset($_POST['points_num']) ? intval($_POST['points_num']) : 0;
                $price = isset($_POST['price']) ? floatval($_POST['price']) : 0;
                $status = isset($_POST['status']) ? intval($_POST['status']) : 1;
                $sort = isset($_POST['sort']) ? intval($_POST['sort']) : 0;
                $remarks = isset($_POST['remarks']) ? trim($_POST['remarks']) : '';
                
                // 校验必要参数
                if (empty($name)) {
                    exit(json_encode(array("code" => -1, "msg" => "套餐名称不能为空")));
                }
                
                if ($points_num <= 0) {
                    exit(json_encode(array("code" => -1, "msg" => "积分数量必须大于0")));
                }
                
                if ($price < 0) {
                    exit(json_encode(array("code" => -1, "msg" => "套餐价格不能为负数")));
                }
                
                // 防SQL注入处理
                $name = $DB->real_escape_string($name);
                $remarks = $DB->real_escape_string($remarks);
                $now = date("Y-m-d H:i:s");
                
                // 检查是否为更新操作
                if ($id > 0) {
                    // 检查套餐是否存在
                    if (!$DB->get_row("SELECT id FROM dwz_points_package WHERE id='$id' LIMIT 1")) {
                        exit(json_encode(array("code" => -1, "msg" => "套餐不存在")));
                    }
                    
                    // 更新套餐
                    $sql = "UPDATE dwz_points_package SET 
                            name='$name', 
                            points_num='$points_num', 
                            price='$price', 
                            status='$status', 
                            sort='$sort', 
                            remarks='$remarks' 
                            WHERE id='$id'";
                            
                    if ($DB->query($sql)) {
                        exit(json_encode(array("code" => 0, "msg" => "更新成功", "id" => $id)));
                    } else {
                        exit(json_encode(array("code" => -1, "msg" => "数据库更新失败")));
                    }
                } else {
                    // 添加新套餐
                    $sql = "INSERT INTO dwz_points_package (name, points_num, price, status, sort, addtime, remarks) 
                            VALUES ('$name', '$points_num', '$price', '$status', '$sort', '$now', '$remarks')";
                            
                    if ($DB->query($sql)) {
                        $id = $DB->insert_id();
                        exit(json_encode(array("code" => 0, "msg" => "添加成功", "id" => $id)));
                    } else {
                        exit(json_encode(array("code" => -1, "msg" => "数据库插入失败")));
                    }
                }
            } catch (Exception $e) {
                error_log("保存积分套餐异常: " . $e->getMessage());
                exit(json_encode(array("code" => -1, "msg" => "保存失败: " . $e->getMessage())));
            }
            break;

        case 'batch_delete_points_package':
            try {
                $ids = isset($_POST['ids']) ? $_POST['ids'] : '';
                
                if (empty($ids)) {
                    exit(json_encode(array("code" => -1, "msg" => "参数错误")));
                }
                
                // 将字符串转为数组
                $id_arr = explode(',', $ids);
                $id_str = implode(',', array_map('intval', $id_arr));
                
                // 删除套餐
                if ($DB->query("DELETE FROM dwz_points_package WHERE id IN ($id_str)")) {
                    exit(json_encode(array("code" => 0, "msg" => "批量删除成功")));
                } else {
                    exit(json_encode(array("code" => -1, "msg" => "批量删除失败")));
                }
            } catch (Exception $e) {
                error_log("批量删除积分套餐异常: " . $e->getMessage());
                exit(json_encode(array("code" => -1, "msg" => "批量删除失败: " . $e->getMessage())));
            }
            break;
            
        case 'batch_update_points_package_status':
            try {
                $ids = isset($_POST['ids']) ? $_POST['ids'] : '';
                $status = isset($_POST['status']) ? intval($_POST['status']) : -1;
                
                if (empty($ids) || !in_array($status, [0, 1])) {
                    exit(json_encode(array("code" => -1, "msg" => "参数错误")));
                }
                
                // 将字符串转为数组
                $id_arr = explode(',', $ids);
                $id_str = implode(',', array_map('intval', $id_arr));
                
                // 更新状态
                if ($DB->query("UPDATE dwz_points_package SET status='$status' WHERE id IN ($id_str)")) {
                    exit(json_encode(array("code" => 0, "msg" => "状态更新成功")));
                } else {
                    exit(json_encode(array("code" => -1, "msg" => "状态更新失败")));
                }
            } catch (Exception $e) {
                error_log("批量更新积分套餐状态异常: " . $e->getMessage());
                exit(json_encode(array("code" => -1, "msg" => "状态更新失败: " . $e->getMessage())));
            }
            break;

        case 'checkLandingDomain':
            $id = intval($_GET['id']);
            $type = trim($_GET['type']);
            
            // 这里只是模拟检测，实际应根据项目需求实现真实检测
            $checkResult = mt_rand(0, 1); // 随机返回0或1模拟检测结果
            
            if ($type == 'qqsafe') {
                $rs = $DB->query("UPDATE dwz_landing_domain SET qqsafe='$checkResult' WHERE id='$id'");
            } else if ($type == 'wxsafe') {
                $rs = $DB->query("UPDATE dwz_landing_domain SET wxsafe='$checkResult' WHERE id='$id'");
            } else {
                $result = array("code" => -1, "msg" => "未知的检测类型");
                exit(json_encode($result));
            }
            
            if ($rs) {
                $status = $checkResult ? '正常' : '拦截';
                $result = array("code" => 0, "msg" => "检测完成，状态为：{$status}");
            } else {
                $result = array("code" => -1, "msg" => "检测失败");
            }
            exit(json_encode($result));
            break;
            
        case 'saveInviteSettings':
            try {
                // 获取表单数据
                $invite_consume_percent = isset($_POST['invite_consume_percent']) ? floatval($_POST['invite_consume_percent']) : 5;
                
                // 数值检查
                if($invite_consume_percent < 0 || $invite_consume_percent > 100) {
                    exit(json_encode(array("code" => -1, "msg" => "返现比例必须在0-100之间")));
                }
                
                // 保存设置到数据库
                saveSetting('invite_consume_percent', $invite_consume_percent);
                
                // 清除缓存
                $CACHE->clear();
                
                // 返回成功消息
                exit(json_encode(array("code" => 0, "msg" => "邀请设置保存成功")));
            } catch (Exception $e) {
                error_log("保存邀请设置异常: " . $e->getMessage());
                exit(json_encode(array("code" => -1, "msg" => "保存失败: " . $e->getMessage())));
            }
            break;
            
        default:
            exit(json_encode(array('code' => -4, 'msg' => '没有此方法')));
            break;
    }
} catch (Exception $e) {
    // 捕获全局异常
    header('Content-Type: application/json; charset=UTF-8');
    echo json_encode(array(
        'code' => -1,
        'msg' => 'PHP异常错误: ' . $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine()
    ));
    exit;
}
