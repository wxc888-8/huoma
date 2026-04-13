<?php  
$nod_jump = true;
include("includes/common.php");

// 调试模式开关
$debug_mode = false;
$debug_info = array();

// 调试函数
function debug_log($message, $data = null) {
    global $debug_info, $debug_mode;
    if ($debug_mode) {
        $debug_info[] = array(
            'time' => date('H:i:s'),
            'message' => $message,
            'data' => $data
        );
    }
}

// 开始记录调试信息
debug_log('脚本开始执行');

//加密函数
function generateRandomNumber($min, $max) {
    $result = rand($min, $max);
    debug_log("生成随机数: $min-$max", $result);
    return $result;
}

function encodeBase64($input) {
    $result = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($input));
    debug_log("Base64编码", array('input' => $input, 'output' => $result));
    return $result;
}

function decodeBase64($input) {
    debug_log("Base64解码前", $input);
    $padding = strlen($input) % 4;
    if ($padding > 0) {
        $input .= str_repeat('=', 4 - $padding);
        debug_log("添加填充", $input);
    }
    $result = base64_decode(str_replace(['-', '_'], ['+', '/'], $input));
    debug_log("Base64解码后", $result);
    return $result;
}

function generateModifiedData($xuyao) {
    debug_log("生成修改数据开始", $xuyao);
    $randomNumber = generateRandomNumber(1, 100);
    $randomNumbers = generateRandomNumber(1, 1000);

    $originalData = urlencode(md5($randomNumber) .'?'. $xuyao .'&'. md5($randomNumbers));
    debug_log("原始数据", $originalData);
    $encodedData1 = encodeBase64($originalData);
    debug_log("第一次编码", $encodedData1);

    $randomChars1 = substr(str_shuffle(md5(time())), 0, 10);
    $modifiedData1 = substr($encodedData1, 0, 1) . $randomChars1 . substr($encodedData1, 1);
    debug_log("第一次修改", $modifiedData1);

    $encodedData2 = encodeBase64($modifiedData1);
    debug_log("第二次编码", $encodedData2);

    $randomChars2 = substr(str_shuffle(md5(time())), 0, 10);
    $modifiedData2 = substr($encodedData2, 0, 1) . $randomChars2 . substr($encodedData2, 1);
    debug_log("第二次修改", $modifiedData2);

    $result = strrev($modifiedData2);
    debug_log("最终结果", $result);
    return $result;
}

// 获取设备类型
function get_device() {
    $agent = strtolower($_SERVER['HTTP_USER_AGENT']);
    debug_log("User Agent", $agent);
    if(strpos($agent, 'iphone') || strpos($agent, 'ipad')) {
        debug_log("设备类型", "ios");
        return 'ios';
    } else if(strpos($agent, 'android')) {
        debug_log("设备类型", "android");
        return 'android';
    } else {
        debug_log("设备类型", "pc");
        return 'pc';
    }
}

// 获取浏览器类型
function get_browser_name() {
    $agent = $_SERVER['HTTP_USER_AGENT'];
    if (preg_match('/MSIE\s([^\s|;]+)/i', $agent, $regs)) {
        debug_log("浏览器类型", "IE");
        return 'IE';
    } else if (preg_match('/FireFox\/([^\s]+)/i', $agent, $regs)) {
        debug_log("浏览器类型", "Firefox");
        return 'Firefox';
    } else if (preg_match('/Chrome\/([^\s]+)/i', $agent, $regs)) {
        debug_log("浏览器类型", "Chrome");
        return 'Chrome';
    } else if (preg_match('/Safari\/([^\s]+)/i', $agent, $regs)) {
        debug_log("浏览器类型", "Safari");
        return 'Safari';
    } else if (preg_match('/Opera\/([^\s]+)/i', $agent, $regs)) {
        debug_log("浏览器类型", "Opera");
        return 'Opera';
} else {
        debug_log("浏览器类型", "Other");
        return 'Other';
    }
}

// 处理主机名，移除二级域名前缀
function process_host($host) {
    debug_log("处理主机名前", $host);
    // 检查是否存在二级域名前缀
    $domain_parts = explode('.', $host);
    if (count($domain_parts) > 2) {
        // 移除第一个部分（二级域名前缀）
        array_shift($domain_parts);
        $result = implode('.', $domain_parts);
        debug_log("处理主机名后", $result);
        return $result;
    }
    debug_log("处理主机名后(无变化)", $host);
    return $host;
}

//获取入口
$query = "SELECT domain FROM dwz_domain WHERE type = 0 LIMIT 1";
$result = $DB->query($query);
if ($result && $row = $DB->fetch($result)) {
    $mubanw = $row['domain'];
    debug_log("获取入口域名", $mubanw);
}

// 主要处理逻辑
$id = isset($_GET['id']) ? $_GET['id'] : '';
debug_log("初始ID值", $id);

// 如果ID为空，尝试从URL路径解析
if(empty($id)) {
    debug_log("ID为空，尝试从URL路径解析");
    if (isset($_SERVER['REQUEST_URI'])) {
        $uri = $_SERVER['REQUEST_URI'];
        debug_log("REQUEST_URI", $uri);
        
        // 移除查询字符串
        $path = parse_url($uri, PHP_URL_PATH);
        debug_log("解析路径", $path);
        
        // 提取最后的路径部分
        $parts = explode('/', rtrim($path, '/'));
        $last_part = end($parts);
        debug_log("最后路径部分", $last_part);
        
        // 检查是否是短链接格式（例如 f.abc123）
        if (preg_match('/^f\.(.+)$/', $last_part, $matches)) {
            $id = $matches[1];
            debug_log("从路径提取ID", $id);
        } else {
            // 检查是否有其他格式
            $extension = array('.php', '.html', '.aspx', '.xhtml', '.jsp', '.xml', '.css', '.js', '.jpg', '.png');
            $id = str_replace($extension, '', $last_part);
            $id = trim($id, '=');
            debug_log("从路径提取ID（清理后）", $id);
        }
    }
}

if (empty($id)) {
    debug_log("最终ID仍为空，退出");
    exit('参数不存在');
}

// 获取当前请求的主机名
$host = $_SERVER['HTTP_HOST'];
debug_log("原始主机名", $host);

// 处理主机名，移除二级域名前缀
$processed_host = process_host($host);
debug_log("处理后主机名", $processed_host);

// 查询数据库
$sql = "select * from dwz_url where id='$id' limit 1";
debug_log("数据库查询SQL", $sql);
$row = $DB->get_row($sql);
debug_log("数据库查询结果", $row);

if (!$row) {
    debug_log("链接不存在，退出");
    exit('链接不存在');
}

// 记录设备、浏览器等信息
$device = get_device();
$browser = get_browser_name();
$ip = real_ip();
debug_log("访问信息", array('device'=>$device, 'browser'=>$browser, 'ip'=>$ip));

// 检查是否黑名单IP
$black_sql = "select * from dwz_black where content='$ip' and type=0 limit 1";
debug_log("黑名单检查SQL", $black_sql);
$is_black = $DB->get_row($black_sql);
debug_log("黑名单检查结果", $is_black);

if ($is_black) {
    debug_log("IP已被拉黑，退出");
    exit('您的IP已被拉黑');
}

// 如果状态为0，链接已被禁用
if ($row['state'] == 0) {
    debug_log("链接已被禁用");
    header("Location: https://c.pc.qq.com/middleb.html?pfurl=%E9%93%BE%E6%8E%A5%E5%B7%B2%E8%A2%AB%E5%B0%81%E7%A6%81");
    exit();
}

// 检查劫持配置
if (isset($conf['hijack_btn']) && $conf['hijack_btn'] == 1 && $row['view'] > $conf['hijack_view']) {
    debug_log("检查劫持配置");
    $urow = $DB->get_row("select * from dwz_user where id='{$row['uid']}' limit 1");
    if ($urow && isset($urow['vip']) && $urow['vip'] < $date) {
        $rand = rand(1, $conf['hijack_rate']);
        if ($rand == 1) {
            debug_log("触发劫持");
            $DB->query("update dwz_url set hijack_num=hijack_num+1 where id='$id'");
            header("Location: " . $conf['hijack_url']);
            exit();
        }
    }
}

// 检查用户状态
if ($row['u_state'] == 0) {
    debug_log("用户已被禁用");
    header("Location: https://c.pc.qq.com/middleb.html?pfurl=%E9%93%BE%E6%8E%A5%E5%B7%B2%E8%A2%AB%E5%85%B3%E9%97%AD");
    exit();
}

// 更新访问统计
$update_sql = "update dwz_url set view=view+1 where id='$id'";
debug_log("更新访问统计SQL", $update_sql);
$update_result = $DB->query($update_sql);
debug_log("更新访问统计结果", $update_result);

// 记录访问日志
$uid = $row['uid'];
$urlid = $row['id'];
$adddate = date("Y-m-d");
$getip = $ip; // 使用之前获取的IP
$getos = get_device(); // 使用设备类型作为系统信息
$visit_sql = "INSERT INTO dwz_visitors(uid, urlid, ip, addtime, system, adddate) VALUES('$uid', '$urlid', '$getip', NOW(), '$getos', '$adddate')";
debug_log("记录访问日志SQL", $visit_sql);
$visit_result = $DB->query($visit_sql);
debug_log("记录访问日志结果", $visit_result);

// 访问次数限制检查
if (isset($row['visit']) && $row['visit'] !== '') {
    debug_log("检查访问次数限制", $row['visit']);
    if ($row['visit'] == 0 && $row['visiturl'] != '') {
        debug_log("已达上限，跳转到访问URL");
        header("Location: " . $row['visiturl'], true, 302);
        exit();
    } elseif ($row['visit'] == 0 && $row['visiturl'] == '') {
        debug_log("已达上限，无访问URL");
        header("Location: https://c.pc.qq.com/middleb.html?pfurl=%E5%B7%B2%E8%BE%BE%E4%B8%8A%E9%99%90");
        exit();
    } else {
        debug_log("减少访问次数");
        $DB->query("update dwz_url set visit=visit-1 where id='$id'");
    }
}

// 检查到期时间
if (isset($row['endtime']) && $row['endtime'] != '' && $row['endtime'] < $date) {
    debug_log("链接已到期");
    header("Location: https://c.pc.qq.com/middleb.html?pfurl=%E5%B7%B2%E5%88%B0%E6%9C%9F");
    exit();
}

// 检查是否需要扣除积分
$uid = $row['uid'];

// 获取每次使用短链接需要扣除的积分数
$points_per_use = isset($conf['check_points']) ? intval($conf['check_points']) : 1;

// 判断是否开启了积分扣除功能
if (isset($conf['qr_use_points']) && $conf['qr_use_points'] == 1) {
    debug_log("检查用户积分");
    
    // 获取用户积分信息
    $user_info = $DB->get_row("SELECT points FROM `dwz_user` WHERE `id`='{$uid}' LIMIT 1");
    debug_log("用户积分信息", $user_info);
    
    // 检查积分是否足够
    if (!$user_info || $user_info['points'] < $points_per_use) {
        debug_log("用户积分不足", array('required' => $points_per_use, 'available' => ($user_info ? $user_info['points'] : 0)));
        header("Location: https://c.pc.qq.com/middleb.html?pfurl=%E7%A7%AF%E5%88%86%E4%B8%8D%E8%B6%B3");
        exit();
    }
    
    // 扣除用户积分
    $sql = "UPDATE `dwz_user` SET `points`=`points`-{$points_per_use} WHERE `id`='{$uid}' AND `points`>={$points_per_use}";
    $deduct_result = $DB->query($sql);
    debug_log("扣除积分SQL", $sql);
    debug_log("扣除积分结果", $deduct_result);
    
    // 如果扣除失败，返回404
    if (!$deduct_result) {
        header("Location: https://c.pc.qq.com/middleb.html?pfurl=%E7%A7%AF%E5%88%86%E6%89%A3%E9%99%A4%E5%A4%B1%E8%B4%A5");
        exit();
    }
    
    // 记录积分变动
    $add_log_sql = "INSERT INTO `dwz_points_log` (`uid`, `points`, `type`, `description`, `addtime`) 
            VALUES ('{$uid}', -{$points_per_use}, 'url_use', '使用短链接ID:{$id}扣除', NOW())";
    $DB->query($add_log_sql);
    debug_log("记录积分变动", $add_log_sql);
}

// 获取URL和标题
$urls = $row['url']; // 被编码原网站
$biaoti = $row['title']; // 标题
$muban = $row['jumpmb']; // 跳转模板
$pattern = $row['pattern']; // 跳转方式

// 以下为跳转逻辑
$url = base64_decode($urls);
debug_log("解码后的URL", $url);
debug_log("跳转模式", $pattern);

// 根据不同的跳转模式处理
debug_log("开始处理跳转模式 {$pattern}");
$ua = $_SERVER['HTTP_USER_AGENT'];

switch($pattern) {
    case 1: // 普通跳转
        debug_log("执行普通跳转");
        if (strpos($ua, 'QQ/') && isset($row['qqjump']) && $row['qqjump'] != '') {
            debug_log("QQ环境，使用特定跳转");
            header("Location: " . $row['qqjump'], true, 302);
        } elseif (strpos($ua, 'MicroMessenger') && isset($row['wxjump']) && $row['wxjump'] != '') {
            debug_log("微信环境，使用特定跳转");
            header("Location: " . $row['wxjump'], true, 302);
        } elseif (strpos($ua, 'AlipayClient') && isset($row['alijump']) && $row['alijump'] != '') {
            debug_log("支付宝环境，使用特定跳转");
            header("Location: " . $row['alijump'], true, 302);
        } else {
            debug_log("普通环境，直接跳转到目标URL");
        if(!$debug_mode) {
                header("Location: " . urldecode($url), true, 302);
            }
        }
        break;
        
    case 2: // 防红跳转
        debug_log("执行防红跳转");
        // 生成时间戳和参数
        $sjjm = time() + (isset($conf['shixiao_tz']) ? $conf['shixiao_tz'] : 3600);
        $str0 = 'sj='.$sjjm.'&uu='.$urls.'&sx='.base64_encode(isset($conf['shixiao_url']) ? $conf['shixiao_url'] : '').'&bt='.$biaoti;
        $tt = generateModifiedData($str0);
        
        // 构建跳转URL
        $yic = 'http://'.$mubanw.'/'.$muban.'?'.$id.'#&t='.$tt;
        debug_log("防红跳转URL", $yic);
        
        if(!$debug_mode) {
            header("Location: " . urldecode($yic), true, 302);
        }
        break;
        
    case 3: // 直链防红
        debug_log("执行直链防红");
        // 生成时间戳和参数
        $sjjm = time() + (isset($conf['shixiao_zl']) ? $conf['shixiao_zl'] : 3600);
        $str0 = 'sj='.$sjjm.'&uu='.$urls.'&sx='.base64_encode(isset($conf['shixiao_url']) ? $conf['shixiao_url'] : '').'&bt='.$biaoti;
        $tt = generateModifiedData($str0);
        
        // 构建跳转URL
        $yic = 'http://'.$mubanw.'/zl?'.$id.'#&t='.$tt;
        debug_log("直链防红URL", $yic);
        
        if(!$debug_mode) {
            header("Location: " . urldecode($yic), true, 302);
        }
        break;
        
    case 4: // 直接跳转
        debug_log("执行直接跳转");
        if(!$debug_mode) {
            header("Location: " . $url, true, 302);
        }
        break;
        
    default:
        debug_log("执行默认跳转");
        if(!$debug_mode) {
            header("Location: https://c.pc.qq.com/middleb.html?pfurl=404");
        }
}

// 统计相关逻辑
$t = date('Y-m-d 00:00:00');
if ($DB->get_row("select id from dwz_visitors where urlid='$urlid' and ip='$getip' and addtime>'$t' limit 1")) {
    $ip = 0;
    debug_log("今日已访问过，IP统计+0");
} else {
    $ip = 1;
    debug_log("今日首次访问，IP统计+1");
}

if (strpos($getos, 'android') !== false) {
    $sys_az = 1;
    $sys_pg = 0;
    $sys_qt = 0;
    debug_log("Android设备");
} elseif (strpos($getos, 'ios') !== false) {
    $sys_az = 0;
    $sys_pg = 1;
    $sys_qt = 0;
    debug_log("iOS设备");
} else {
    $sys_az = 0;
    $sys_pg = 0;
    $sys_qt = 1;
    debug_log("其他设备");
}

// 更新统计数据
$DB->query("update dwz_url set lasttime=NOW() where id='$id'");
debug_log("更新最后访问时间");

// 更新或插入统计记录
if ($DB->get_row("select id from dwz_stat where uid='$uid' and urlid='$id' and date='$adddate' limit 1")) {
    $stat_sql = "update dwz_stat set ip=ip+$ip,pv=pv+1,sys_az=sys_az+$sys_az,sys_pg=sys_pg+$sys_pg,sys_qt=sys_qt+$sys_qt where uid='$uid' and urlid='$id' and date='$adddate'";
    debug_log("更新统计记录", $stat_sql);
    $DB->query($stat_sql);
} else {
    $stat_sql = "insert into dwz_stat(uid,urlid,ip,pv,sys_az,sys_pg,sys_qt,date) values('$uid','$id','$ip','1','$sys_az','$sys_pg','$sys_qt','$adddate')";
    debug_log("插入统计记录", $stat_sql);
    $DB->query($stat_sql);
}

// 输出调试信息
if($debug_mode) {
    echo '<html><head><title>调试信息</title>';
    echo '<style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        h1 { color: #333; }
        .debug-section { margin-bottom: 20px; border: 1px solid #ddd; padding: 10px; border-radius: 5px; }
        .debug-item { margin-bottom: 10px; padding: 5px; background-color: #f9f9f9; }
        .debug-time { color: #666; font-size: 0.8em; }
        .debug-message { font-weight: bold; color: #333; }
        .debug-data { margin-top: 5px; font-family: monospace; white-space: pre-wrap; background-color: #f5f5f5; padding: 5px; border-radius: 3px; }
        .sql { color: #0066cc; }
        .error { color: #cc0000; }
        .success { color: #008800; }
    </style>';
    echo '</head><body>';
    echo '<h1>调试信息</h1>';
    
    // 请求信息
    echo '<div class="debug-section">';
    echo '<h2>请求信息</h2>';
    echo '<div class="debug-item">';
    echo '<div><strong>请求URL:</strong> ' . $_SERVER['REQUEST_URI'] . '</div>';
    echo '<div><strong>请求方法:</strong> ' . $_SERVER['REQUEST_METHOD'] . '</div>';
    echo '<div><strong>User Agent:</strong> ' . $_SERVER['HTTP_USER_AGENT'] . '</div>';
    echo '<div><strong>IP地址:</strong> ' . $ip . '</div>';
    echo '<div><strong>设备类型:</strong> ' . $device . '</div>';
    echo '<div><strong>浏览器类型:</strong> ' . $browser . '</div>';
    echo '</div>';
    echo '</div>';
    
    // 参数信息
    echo '<div class="debug-section">';
    echo '<h2>参数信息</h2>';
    echo '<div class="debug-item">';
    echo '<div><strong>ID:</strong> ' . $id . '</div>';
    echo '<div><strong>主机名:</strong> ' . $host . ' (处理后: ' . $processed_host . ')</div>';
    echo '<div><strong>跳转模式:</strong> ' . $pattern . '</div>';
    echo '</div>';
    echo '</div>';
    
    // 数据库查询结果
    echo '<div class="debug-section">';
    echo '<h2>数据库查询结果</h2>';
    if(!empty($row)) {
        echo '<div class="debug-item">';
        echo '<div><strong>URL记录:</strong></div>';
        echo '<pre>' . print_r($row, true) . '</pre>';
        echo '</div>';
    } else {
        echo '<div class="debug-item error">未找到URL记录</div>';
    }
    echo '</div>';
    
    // 执行日志
    echo '<div class="debug-section">';
    echo '<h2>执行日志</h2>';
    foreach($debug_info as $log) {
        echo '<div class="debug-item">';
        echo '<div class="debug-time">[' . $log['time'] . ']</div>';
        echo '<div class="debug-message">' . $log['message'] . '</div>';
        if(!is_null($log['data'])) {
            echo '<div class="debug-data">' . (is_array($log['data']) || is_object($log['data']) ? print_r($log['data'], true) : $log['data']) . '</div>';
        }
        echo '</div>';
    }
    echo '</div>';
    
    echo '</body></html>';
    exit;
}

exit;
?>