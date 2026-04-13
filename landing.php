<?php
/**
 * 活码落地页面
 */
include('includes/common.php');

// 开启错误显示，方便调试
ini_set('display_errors', 1);
error_reporting(E_ALL);

// 定义获取客户端设备类型函数（如果common.php中未定义）
if (!function_exists('get_device')) {
    function get_device() {
        $agent = isset($_SERVER['HTTP_USER_AGENT']) ? strtolower($_SERVER['HTTP_USER_AGENT']) : '';
        if (strpos($agent, 'iphone') || strpos($agent, 'ipad')) {
            return 'iOS';
        } else if (strpos($agent, 'android')) {
            return 'Android';
        } else {
            return 'PC';
        }
    }
}

// 定义获取浏览器名称函数（如果common.php中未定义）
if (!function_exists('get_browser_name')) {
    function get_browser_name() {
        $agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
        if (strpos($agent, 'MSIE') !== false || strpos($agent, 'rv:11.0')) {
            return 'IE';
        } else if (strpos($agent, 'Edge') !== false) {
            return 'Edge';
        } else if (strpos($agent, 'Chrome') !== false) {
            return 'Chrome';
        } else if (strpos($agent, 'Firefox') !== false) {
            return 'Firefox';
        } else if (strpos($agent, 'Safari') !== false) {
            return 'Safari';
        } else if (strpos($agent, 'Opera') !== false) {
            return 'Opera';
        } else {
            return 'Unknown';
        }
    }
}

// 定义获取真实IP地址函数（如果common.php中未定义）
if (!function_exists('real_ip')) {
    function real_ip() {
        $ip = $_SERVER['REMOTE_ADDR'];
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && preg_match_all('#\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}#s', $_SERVER['HTTP_X_FORWARDED_FOR'], $matches)) {
            foreach ($matches[0] AS $xip) {
                if (!preg_match('#^(10|172\.16|192\.168)\.#', $xip)) {
                    $ip = $xip;
                    break;
                }
            }
        } elseif (isset($_SERVER['HTTP_CLIENT_IP']) && preg_match('/^([0-9]{1,3}\.){3}[0-9]{1,3}$/', $_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (isset($_SERVER['HTTP_CF_CONNECTING_IP']) && preg_match('/^([0-9]{1,3}\.){3}[0-9]{1,3}$/', $_SERVER['HTTP_CF_CONNECTING_IP'])) {
            $ip = $_SERVER['HTTP_CF_CONNECTING_IP'];
        } elseif (isset($_SERVER['HTTP_X_REAL_IP']) && preg_match('/^([0-9]{1,3}\.){3}[0-9]{1,3}$/', $_SERVER['HTTP_X_REAL_IP'])) {
            $ip = $_SERVER['HTTP_X_REAL_IP'];
        }
        return $ip;
    }
}

// 定义获取IP地址归属地函数（如果common.php中未定义）
if (!function_exists('get_ip_city')) {
    function get_ip_city($ip) {
        // 实际项目中可能需要调用IP地址库或API
        // 这里简化处理
        return '未知';
    }
}

// 从请求头获取数据 - Referer包含来源域名信息
$referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
$host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '';

// 获取当前访问域名
$current_domain = $host;

// 从HTTP_REFERER头中提取源域名
$source = '';
if (!empty($referer)) {
    $referer_parts = parse_url($referer);
    if (isset($referer_parts['host'])) {
        $source = $referer_parts['host'];
    }
}

// 如果HTTP_REFERER中无法获取来源，则尝试从URL参数获取（兼容旧版本）
if (empty($source) && isset($_GET['source'])) {
    $source = $_GET['source'];
}

// 从URL路径中提取活码ID (如 landing.php/123)
$qr_id = 0;
$qr_code = '';
$uri = $_SERVER['REQUEST_URI'];

// 尝试从URL查询参数获取活码标识符
if (isset($_GET['code'])) {
    $qr_code = urldecode($_GET['code']);
} 
// 尝试从URL路径中提取活码标识符
elseif (preg_match('/\/landing\.php\/([^?&\/]+)/i', $uri, $matches)) {
    $qr_code = urldecode($matches[1]);
} 
// 兼容旧版通过ID访问的方式
elseif (isset($_GET['id'])) {
    $qr_id = intval($_GET['id']);
}

// 基于当前域名查询活码列表
$qr_list = array();

if (!empty($qr_code)) {
    // 尝试多种解码方式
    $possible_codes = [
        $qr_code,
        urldecode($qr_code),
        rawurldecode($qr_code),
        str_replace(['(', ')', '&'], ['%28', '%29', '%26'], $qr_code)
    ];
    
    $found = false;
    foreach ($possible_codes as $test_code) {
        $escaped_code = $DB->escape($test_code);
        $qr_sql = "SELECT * FROM `dwz_qrcode` WHERE `code`='{$escaped_code}' AND `landing_domain`='{$DB->escape($current_domain)}' AND `state`=1 LIMIT 1";
        $qr = $DB->get_row($qr_sql);
        if ($qr) {
            $qr_list[] = $qr;
            $found = true;
            break;
        }
    }
    
    // 如果精确匹配未找到，尝试模糊匹配
    if (!$found) {
        $main_code_part = explode('/', $qr_code)[0];
        $escaped_like = $DB->escape($main_code_part) . '%';
        $qr_sql = "SELECT * FROM `dwz_qrcode` WHERE `code` LIKE '{$escaped_like}' AND `landing_domain`='{$DB->escape($current_domain)}' AND `state`=1 LIMIT 1";
        $qr = $DB->get_row($qr_sql);
        if ($qr) {
            $qr_list[] = $qr;
        }
    }
} elseif ($qr_id > 0) {
    // 兼容旧版：如果有指定ID，直接查询该ID
    $qr_sql = "SELECT * FROM `dwz_qrcode` WHERE `id`='{$qr_id}' AND `landing_domain`='{$DB->escape($current_domain)}' AND `state`=1 LIMIT 1";
    $qr = $DB->get_row($qr_sql);
    if ($qr) {
        $qr_list[] = $qr;
    }
} else {
    // 根据当前落地域名查找活码
    $qr_sql = "SELECT * FROM `dwz_qrcode` WHERE `landing_domain`='{$DB->escape($current_domain)}' AND `state`=1";
    if (!empty($source)) {
        $qr_sql .= " AND `entry_domain`='{$DB->escape($source)}'";
    }
    $qr_sql .= " ORDER BY `id` DESC LIMIT 10";
    
    $result = $DB->query($qr_sql);
    if ($result) {
        while($row = $DB->fetch($result)) {
            $qr_list[] = $row;
        }
    }
}

// 如果没有找到活码
if (empty($qr_list)) {
    // 记录错误日志
    error_log("无法找到该域名对应的活码: " . $current_domain . ", 来源: " . $source);
    
    // 内联显示错误页面，而不是包含模板文件
    header('HTTP/1.1 404 Not Found');
    echo '<!DOCTYPE html>
    <html lang="zh-CN">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
        <title>页面未找到</title>
        <style>
            body {
                font-family: "PingFang SC", "Microsoft YaHei", sans-serif;
                margin: 0;
                padding: 0;
                background-color: #f7f7f7;
                color: #333;
                display: flex;
                justify-content: center;
                align-items: center;
                min-height: 100vh;
            }
            .container {
                max-width: 600px;
                margin: 0 auto;
                padding: 20px;
                text-align: center;
            }
            .card {
                background-color: #fff;
                border-radius: 10px;
                padding: 30px;
                box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            }
            .icon {
                font-size: 64px;
                color: #d9d9d9;
                margin-bottom: 20px;
            }
            h1 {
                color: #434343;
                font-size: 24px;
                margin-bottom: 10px;
            }
            p {
                color: #8c8c8c;
                margin-bottom: 20px;
            }
            .button {
                display: inline-block;
                background-color: #1890ff;
                color: white;
                padding: 10px 20px;
                border-radius: 5px;
                text-decoration: none;
                transition: background-color 0.3s;
            }
            .button:hover {
                background-color: #40a9ff;
            }
            .footer {
                margin-top: 30px;
                font-size: 12px;
                color: #999;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="card">
                <div class="icon">⚠️</div>
                <h1>页面未找到</h1>
                <p>抱歉，您访问的活码不存在或已失效。</p>
                <a href="javascript:history.back();" class="button">返回上一页</a>
            </div>
        </div>
    </body>
    </html>';
    exit;
}

// 选择最合适的活码 (如果有多个，优先选择带有指定入口域名的，或者最新的)
$qr = $qr_list[0];  // 默认选择第一个
if (count($qr_list) > 1 && !empty($source)) {
    foreach ($qr_list as $item) {
        if ($item['entry_domain'] == $source) {
            $qr = $item;
            break;
        }
    }
}

$qr_id = $qr['id'];

// 验证来源 - 首先检查URL中的source参数，如果存在则优先使用
$url_source = isset($_GET['source']) ? $_GET['source'] : '';
if (!empty($url_source)) {
    $source = $url_source; // 使用URL中的source参数覆盖HTTP_REFERER中提取的值
}

// 强制验证入口域名 - 确保来源域名与活码的入口域名一致
// 没有来源信息或来源不匹配时拒绝访问
if (empty($source) || $source != $qr['entry_domain']) {
    // 记录非法访问尝试
    error_log("入口域名验证失败: " . (empty($source) ? "无来源域名" : "来源域名不匹配: " . $source . " vs " . $qr['entry_domain']));
    
    // 显示错误页面
    header('HTTP/1.1 403 Forbidden');
    echo '<!DOCTYPE html>
    <html lang="zh-CN">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
        <title>访问被拒绝</title>
        <style>
            body {
                font-family: "PingFang SC", "Microsoft YaHei", sans-serif;
                margin: 0;
                padding: 0;
                background-color: #f7f7f7;
                color: #333;
                display: flex;
                justify-content: center;
                align-items: center;
                min-height: 100vh;
            }
            .container {
                max-width: 600px;
                margin: 0 auto;
                padding: 20px;
                text-align: center;
            }
            .card {
                background-color: #fff;
                border-radius: 10px;
                padding: 30px;
                box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            }
            .icon {
                font-size: 64px;
                color: #ff4d4f;
                margin-bottom: 20px;
            }
            h1 {
                color: #ff4d4f;
                font-size: 24px;
                margin-bottom: 10px;
            }
            p {
                color: #8c8c8c;
                margin-bottom: 20px;
            }
            .button {
                display: inline-block;
                background-color: #1890ff;
                color: white;
                padding: 10px 20px;
                border-radius: 5px;
                text-decoration: none;
                transition: background-color 0.3s;
            }
            .button:hover {
                background-color: #40a9ff;
            }
            .footer {
                margin-top: 30px;
                font-size: 12px;
                color: #999;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="card">
                <div class="icon">❌</div>
                <h1>访问被拒绝</h1>
                <p>请勿直接访问落地页。</p>
            </div>
        </div>
    </body>
    </html>';
    exit;
}

// 检查落地域名是否付费
$landing_domain_sql = "SELECT * FROM `dwz_landing_domain` WHERE `domain`='".daddslashes($qr['landing_domain'])."' LIMIT 1";
$landing_domain_info = $DB->get_row($landing_domain_sql);
if ($landing_domain_info && $landing_domain_info['is_paid'] == 0) {
    // 是免费落地域名，检查是否需要扣除积分
    if (isset($conf['qr_use_points']) && $conf['qr_use_points'] == 1) {
        // 扣除用户积分
        $uid = intval($qr['uid']);
        $DB->query("UPDATE `dwz_user` SET `points`=`points`-1 WHERE `id`='{$uid}' AND `points`>0");
        
        // 记录积分变动
        $DB->query("INSERT INTO `dwz_points_log` (`uid`, `points`, `type`, `description`, `addtime`) VALUES ('{$uid}', -1, 'qr_landing', '落地页访问活码ID:{$qr_id}扣除', NOW())");
    }
}

// 根据活码阈值类型和无限循环设置选择目标URL
$threshold_type = $qr['threshold_type'];
$infinite_loop = $qr['infinite_loop'];

// 获取活码数据
$qr_data_sql = "SELECT * FROM `dwz_qrcode_data` WHERE `qid`='{$qr_id}' ORDER BY `sort` ASC";
$qr_data_result = $DB->query($qr_data_sql);
$qr_data_list = array();
while($row = $DB->fetch($qr_data_result)) {
    $qr_data_list[] = $row;
}

if (empty($qr_data_list)) {
    header('HTTP/1.1 404 Not Found');
    exit('活码数据为空');
}

// 选择跳转URL
$target_url = '';
$target_image = '';
$target_data_id = 0;

switch ($threshold_type) {
    case 1: // 顺序切换
        // 找到当前可用且未达到阈值的数据
        foreach ($qr_data_list as $data) {
            if ($infinite_loop || $data['used'] < $data['threshold']) {
                $target_url = $data['jump_url'];
                $target_image = $data['image_url'];
                $target_data_id = $data['id'];
                break;
            }
        }
        
        // 所有数据都达到阈值，且非无限循环模式
        if (empty($target_url) && $infinite_loop && !empty($qr_data_list)) {
            // 如果是无限循环模式，使用第一条数据
            $target_url = $qr_data_list[0]['jump_url'];
            $target_image = $qr_data_list[0]['image_url'];
            $target_data_id = $qr_data_list[0]['id'];
        }
        break;
        
    case 2: // 随机切换
        // 收集未达到阈值的数据
        $available_data = [];
        foreach ($qr_data_list as $data) {
            if ($infinite_loop || $data['used'] < $data['threshold']) {
                $available_data[] = $data;
            }
        }
        
        // 随机选择一条
        if (!empty($available_data)) {
            $random_index = array_rand($available_data);
            $target_url = $available_data[$random_index]['jump_url'];
            $target_image = $available_data[$random_index]['image_url'];
            $target_data_id = $available_data[$random_index]['id'];
        } elseif ($infinite_loop && !empty($qr_data_list)) {
            // 如果是无限循环模式，随机使用一条数据
            $random_index = array_rand($qr_data_list);
            $target_url = $qr_data_list[$random_index]['jump_url'];
            $target_image = $qr_data_list[$random_index]['image_url'];
            $target_data_id = $qr_data_list[$random_index]['id'];
        }
        break;
        
    case 3: // 轮询切换
        // 获取总使用次数最少的数据
        $min_used = PHP_INT_MAX;
        $min_used_data = null;
        
        foreach ($qr_data_list as $data) {
            if (($infinite_loop || $data['used'] < $data['threshold']) && $data['used'] < $min_used) {
                $min_used = $data['used'];
                $min_used_data = $data;
            }
        }
        
        if ($min_used_data) {
            $target_url = $min_used_data['jump_url'];
            $target_image = $min_used_data['image_url'];
            $target_data_id = $min_used_data['id'];
        } elseif ($infinite_loop && !empty($qr_data_list)) {
            // 如果是无限循环模式，使用总使用次数最少的数据
            $min_used = PHP_INT_MAX;
            foreach ($qr_data_list as $data) {
                if ($data['used'] < $min_used) {
                    $min_used = $data['used'];
                    $min_used_data = $data;
                }
            }
            
            $target_url = $min_used_data['jump_url'];
            $target_image = $min_used_data['image_url'];
            $target_data_id = $min_used_data['id'];
        }
        break;
}

// 如果没有找到可用URL或所有数据都已达到阈值
if (empty($target_url)) {
    header('HTTP/1.1 404 Not Found');
    exit('活码已达到阈值限制');
}

// 更新使用次数
if ($target_data_id > 0) {
    $DB->query("UPDATE `dwz_qrcode_data` SET `used`=`used`+1 WHERE `id`='{$target_data_id}'");
}

// 检查是否设置了自动跳转时间
$jump_time = isset($qr['jump_time']) ? intval($qr['jump_time']) : 0;
$auto_jump = ($jump_time > 0);

// 记录访问日志
$ip = real_ip();
if (function_exists('daddslashes')) {
    $ip = daddslashes($ip);
    $device = daddslashes(get_device());
    $browser = daddslashes(get_browser_name());
    $address = daddslashes(get_ip_city($ip));
    
    // 直接使用HTTP_REFERER请求头
    $referer = isset($_SERVER['HTTP_REFERER']) ? daddslashes($_SERVER['HTTP_REFERER']) : '';
} else {
    $device = get_device();
    $browser = get_browser_name();
    $address = get_ip_city($ip);
    
    // 直接使用HTTP_REFERER请求头
    $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
}

$DB->query("INSERT INTO `dwz_qrcode_visit` (`qid`, `ip`, `device`, `browser`, `address`, `referer`, `addtime`, `visit_date`) 
        VALUES ('{$qr_id}', '{$ip}', '{$device}', '{$browser}', '{$address}', '{$referer}', NOW(), CURDATE())");

// 展示落地页内容
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>NOTFOUND.</title>
    <style>
        body {
            font-family: 'PingFang SC', 'Microsoft YaHei', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f7f7f7;
            color: #333;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            text-align: center;
        }
        .header {
            margin-bottom: 20px;
        }
        .content {
            background-color: #fff;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        .image-container {
            margin: 20px 0;
        }
        .image-container img {
            max-width: 100%;
            height: auto;
            border-radius: 5px;
        }
        .button {
            display: inline-block;
            background-color: #1890ff;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            margin-top: 20px;
            transition: background-color 0.3s;
        }
        .button:hover {
            background-color: #40a9ff;
        }
        .footer {
            margin-top: 20px;
            font-size: 12px;
            color: #999;
        }
        .safe-tip {
            background-color: #f6ffed;
            border: 1px solid #b7eb8f;
            padding: 10px;
            border-radius: 5px;
            margin-top: 15px;
            font-size: 12px;
            color: #52c41a;
        }
        .countdown {
            font-weight: bold;
            color: #ff4d4f;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>LOADED COMPLETE</h1>
        </div>
        <div class="content">
            <?php if (!empty($target_image)): ?>
            <div class="image-container">
                <img src="<?php echo htmlspecialchars($target_image); ?>" alt="内容图片">
            </div>
            <?php endif; ?>
            
            <?php if ($auto_jump): ?>
            <p>系统将在 <span id="countdown" class="countdown"><?php echo $jump_time; ?></span> 秒后自动跳转</p>
            <?php endif; ?>
            
            <a href="<?php echo htmlspecialchars($target_url); ?>" class="button" id="jump-button">立即跳转</a>
            
            <?php if (isset($qr['show_safe']) && $qr['show_safe'] == 1): ?>
            <div class="safe-tip">
                <p>✓ 此页面已通过安全认证，可放心访问</p>
            </div>
            <?php endif; ?>
        </div>
    </div>
    
    <script>
        // 自动跳转倒计时
        var targetUrl = "<?php echo htmlspecialchars(addslashes($target_url)); ?>";
        var countdownElement = document.getElementById('countdown');
        var jumpButton = document.getElementById('jump-button');
        var secondsLeft = <?php echo $jump_time > 0 ? $jump_time : 3; ?>;
        
        function redirectToTarget() {
            window.location.href = targetUrl;
        }
        
        jumpButton.addEventListener('click', function(e) {
            e.preventDefault();
            redirectToTarget();
        });
        
        <?php if ($auto_jump): ?>
        var countdownInterval = setInterval(function() {
            secondsLeft -= 1;
            countdownElement.textContent = secondsLeft;
            
            if (secondsLeft <= 0) {
                clearInterval(countdownInterval);
                redirectToTarget();
            }
        }, 1000);
        <?php endif; ?>
    </script>
    <script src="http://res.wx.qq.com/open/js/jweixin-1.2.0.js"></script>
<script>
  function onBridgeReady() {
    WeixinJSBridge.call('hideOptionMenu');
  }

  if (typeof WeixinJSBridge == "undefined") {
    if (document.addEventListener) {
      document.addEventListener('WeixinJSBridgeReady', onBridgeReady, false);
    } else if (document.attachEvent) {
      document.attachEvent('WeixinJSBridgeReady', onBridgeReady);
      document.attachEvent('onWeixinJSBridgeReady', onBridgeReady);
    }
  } else {
    onBridgeReady();
  }
</script>
</body>
</html> 