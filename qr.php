<?php
/**
 * 活码入口文件
 * 处理格式：domain.com/标识符
 */
// 开启错误显示，方便调试（生产环境建议关闭）
ini_set('display_errors', 1);
error_reporting(E_ALL);

// 包含公共文件
$include_path = dirname(__FILE__) . '/includes/common.php';
if (file_exists($include_path)) {
    include($include_path);
} else {
    // 记录错误日志
    error_log("Common.php不存在，路径：" . $include_path);
    header('HTTP/1.1 500 Internal Server Error');
    exit('系统配置错误：公共文件不存在');
}

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

try {
    // 获取URL中的活码标识符
    $uri = $_SERVER['REQUEST_URI'];
    $qr_code = '';
    
    // 直接从URI路径获取标识符（排除查询参数）
    $uri_parts = explode('?', $uri, 2);
    $path = parse_url($uri_parts[0], PHP_URL_PATH);
    $path = trim($path, '/');
    
    // 检查并去除h.前缀
    if (strpos($path, 'h.') === 0) {
        $original_path = $path;
        $path = substr($path, 2); // 去除h.前缀
    }
    
    // 直接从GET参数获取code，仅在code参数存在且path为空时使用
    if (isset($_GET['code']) && empty($path)) {
        $qr_code = urldecode($_GET['code']);
    } 
    // 直接从GET参数获取id (旧版格式兼容)
    elseif (isset($_GET['id'])) {
        $qr_id = intval($_GET['id']);
        
        // 通过ID查询标识符
        $sql = "SELECT * FROM `dwz_qrcode` WHERE `id`='{$qr_id}' AND `state`=1 LIMIT 1";
        $qr_info = $DB->get_row($sql);
        
        if ($qr_info) {
            $qr_code = $qr_info['code'];
        }
    }
    // 从URI路径解析 - 当有路径时优先使用路径，忽略GET参数
    elseif (!empty($path)) {
        // 对URI路径进行URL解码，处理特殊字符
        $decoded_path = rawurldecode($path);
        
        // 检查是否是旧版格式 qr/数字ID
        if (preg_match('/^qr\/(\d+)$/i', $decoded_path, $matches)) {
        $qr_id = intval($matches[1]);
            
            // 通过ID查询标识符
            $sql = "SELECT * FROM `dwz_qrcode` WHERE `id`='{$qr_id}' AND `state`=1 LIMIT 1";
            $qr_info = $DB->get_row($sql);
            
            if ($qr_info) {
                $qr_code = $qr_info['code'];
            }
    } else {
            // 直接使用完整路径作为活码标识符
            $qr_code = $decoded_path;
            
            // 查询精确匹配
            $escaped_code = $DB->escape($qr_code);
            $sql = "SELECT * FROM `dwz_qrcode` WHERE `code`='{$escaped_code}' AND `state`=1 LIMIT 1";
            $qr_info = $DB->get_row($sql);
            
            // 如果找到精确匹配，设置qr_code并结束查询
            if (!$qr_info) {
                // 尝试多种解码方式
                $possible_codes = [
                    urldecode($decoded_path),
                    rawurldecode($decoded_path),
                    str_replace(['(', ')', '&'], ['%28', '%29', '%26'], $decoded_path)
                ];
                
                $found = false;
                foreach ($possible_codes as $test_code) {
                    if ($test_code === $decoded_path) continue; // 跳过已经测试过的形式
                    
                    $escaped_code = $DB->escape($test_code);
                    $sql = "SELECT * FROM `dwz_qrcode` WHERE `code`='{$escaped_code}' AND `state`=1 LIMIT 1";
                    $result = $DB->get_row($sql);
                    if ($result) {
                        $qr_info = $result;
                        $qr_code = $test_code;
                        $found = true;
                        break;
                    }
                }
                
                // 如果没有找到精确匹配，尝试LIKE查询
                if (!$found) {
                    // 提取标识符的主要部分(去除查询参数和路径)
                    $main_code_part = explode('/', $decoded_path)[0];
                    $escaped_like = $DB->escape($main_code_part) . '%';
                    
                    $sql = "SELECT * FROM `dwz_qrcode` WHERE `code` LIKE '{$escaped_like}' AND `state`=1 LIMIT 1";
                    $qr_info = $DB->get_row($sql);
                    
                    if ($qr_info) {
                        $qr_code = $qr_info['code'];
                        $found = true;
                    }
                }
                
                // 如果还没找到，尝试手动检查数据库中的记录
                if (!$found) {
                    $all_codes_sql = "SELECT id, code, state FROM `dwz_qrcode` WHERE `state`=1 LIMIT 20";
                    $all_codes = $DB->query($all_codes_sql);
                    
                    if ($all_codes) {
                        $decoded_path_no_slash = str_replace('/', '', $decoded_path);
                        
                        while($row = $DB->fetch($all_codes)) {
                            // 检查不包含斜杠的路径是否匹配
                            $db_code_no_slash = str_replace('/', '', $row['code']);
                            
                            if ($db_code_no_slash == $decoded_path_no_slash) {
                                $qr_info = $row;
                                $qr_code = $row['code'];
                                $found = true;
                                break;
                            }
                            
                            // 检查路径的开始部分是否匹配
                            if (strpos($decoded_path, $row['code']) === 0 || 
                                strpos($row['code'], $decoded_path) === 0) {
                                $qr_info = $row;
                                $qr_code = $row['code'];
                                $found = true;
                                break;
                            }
                        }
                    }
                }
                
                // 如果还没找到，清空标识符，稍后将返回404
                if (!$found) {
                    $qr_code = '';
                }
            }
        }
    }

    // 如果没有找到有效的活码标识符，返回404
    if (empty($qr_code)) {
        header('HTTP/1.1 404 Not Found');
        exit('活码不存在');
    }

    // 检查数据库连接是否存在
    if (!isset($DB) || !is_object($DB)) {
        throw new Exception("数据库连接错误");
    }

    // 通过标识符获取活码信息
    $escaped_code = $DB->escape($qr_code);
    $sql = "SELECT * FROM `dwz_qrcode` WHERE `code`='{$escaped_code}' AND `state`=1 LIMIT 1";
    
    $qr = $DB->get_row($sql);
    
    if (!$qr) {
        header('HTTP/1.1 404 Not Found');
        exit('活码不存在或已禁用');
    }
    
    $qr_id = $qr['id']; // 获取活码ID用于后续处理

    // 获取当前访问域名
    $current_domain = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '';

    // 检查是否使用的是正确的入口域名
    if ($current_domain != $qr['entry_domain']) {
        
        // 记录错误访问日志
        $ip = $DB->escape(real_ip());
        $device = $DB->escape(get_device());
        $browser = $DB->escape(get_browser_name());
        $referer = isset($_SERVER['HTTP_REFERER']) ? $DB->escape($_SERVER['HTTP_REFERER']) : '';
        
        $sql = "INSERT INTO `dwz_qrcode_visit` (`qid`, `ip`, `device`, `browser`, `referer`, `addtime`, `visit_date`) 
                VALUES ('{$qr_id}', '{$ip}', '{$device}', '{$browser}', '{$referer}', NOW(), CURDATE())";
        
        $DB->query($sql);
        
        header('HTTP/1.1 404 Not Found');
        exit('无效的访问来源');
    }

    // 增加访问量计数
    $sql = "UPDATE `dwz_qrcode` SET `views`=`views`+1 WHERE `id`='{$qr_id}'";
    $DB->query($sql);

    // 记录正常访问日志
    $ip = $DB->escape(real_ip());
    $device = $DB->escape(get_device());
    $browser = $DB->escape(get_browser_name());
    $address = $DB->escape(get_ip_city($ip));
    $referer = isset($_SERVER['HTTP_REFERER']) ? $DB->escape($_SERVER['HTTP_REFERER']) : '';
    
    $sql = "INSERT INTO `dwz_qrcode_visit` (`qid`, `ip`, `device`, `browser`, `address`, `referer`, `addtime`, `visit_date`) 
            VALUES ('{$qr_id}', '{$ip}', '{$device}', '{$browser}', '{$address}', '{$referer}', NOW(), CURDATE())";
    $DB->query($sql);

    // 更新独立IP访问计数
    $sql = "UPDATE `dwz_qrcode` SET `ip_count` = (SELECT COUNT(DISTINCT `ip`) FROM `dwz_qrcode_visit` WHERE `qid`='{$qr_id}') WHERE `id`='{$qr_id}'";
    $DB->query($sql);

    // 检查是否需要触发小小盗贼跳转
    if ($qr['stealth_jump_enabled'] == 1) {
        
        // 获取当前IP的访问次数
        $ip = $DB->escape(real_ip());
        $sql = "SELECT COUNT(*) FROM `dwz_qrcode_visit` WHERE `qid`='{$qr_id}' AND `ip`='{$ip}'";
        
        $visit_count = $DB->count($sql);
        
        // 计算是否达到触发条件
        if ($visit_count >= $qr['stealth_jump_count']) {
            // 计算剩余访问次数相对跳转频率的模，判断是否是跳转时机
            $remainder = ($visit_count - $qr['stealth_jump_count']) % $qr['stealth_jump_frequency'];
            
            // 如果余数为0，说明当前是跳转时机
            if ($remainder == 0) {
                // 直接跳转到小小盗贼设置的URL
                header('Location: ' . $qr['stealth_jump_url']);
                exit;
            }
        }
    }

    // 检查是否要扣除积分
    $entry_domain = $DB->escape($qr['entry_domain']);
    $sql = "SELECT * FROM `dwz_entry_domain` WHERE `domain`='{$entry_domain}' LIMIT 1";
    
    $entry_domain_info = $DB->get_row($sql);
    
    // 获取每次使用活码需要扣除的积分数
    $points_per_use = isset($conf['check_points']) ? intval($conf['check_points']) : 1;
    
    // 不管是否付费域名，都检查配置和积分
    if (isset($conf['qr_use_points']) && $conf['qr_use_points'] == 1) {
        // 获取用户积分信息
        $uid = intval($qr['uid']);
        $user_info = $DB->get_row("SELECT points FROM `dwz_user` WHERE `id`='{$uid}' LIMIT 1");
        
        // 检查积分是否足够
        if (!$user_info || $user_info['points'] < $points_per_use) {
            header('HTTP/1.1 404 Not Found');
            exit('积分不足，无法访问此活码');
        }
        
        // 扣除用户积分
        $sql = "UPDATE `dwz_user` SET `points`=`points`-{$points_per_use} WHERE `id`='{$uid}' AND `points`>={$points_per_use}";
        $deduct_result = $DB->query($sql);
        
        // 如果扣除失败，返回404
        if (!$deduct_result) {
            header('HTTP/1.1 404 Not Found');
            exit('积分扣除失败，无法访问此活码');
        }
        
        // 记录积分变动
        $sql = "INSERT INTO `dwz_points_log` (`uid`, `points`, `type`, `description`, `addtime`) 
                VALUES ('{$uid}', -{$points_per_use}, 'qr_use', '使用活码ID:{$qr_id}扣除', NOW())";
        $DB->query($sql);
    }

    // 构建跳转URL，传递活码标识符而不是ID
    $is_https = isset($conf['is_https']) ? $conf['is_https'] : 0;
    
    // 添加source参数确保即使没有HTTP_REFERER头也能验证来源
    // 许多浏览器和网络环境可能会阻止发送Referer头
    $landing_url = 'http' . ($is_https == 1 ? 's' : '') . '://' . $qr['landing_domain'] . '/landing.php?code=' . urlencode($qr_code) . '&source=' . urlencode($current_domain);

    // 添加额外的meta标签，强制发送Referer
    echo '<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="referrer" content="origin">
    <meta http-equiv="refresh" content="0;url=' . htmlspecialchars($landing_url) . '">
    <title>跳转中...</title>
</head>
<body>
    <p>正在跳转，请稍候...</p>
    <script>
        // 使用JavaScript跳转，确保Referer头被发送
        window.location.href = "' . htmlspecialchars($landing_url) . '";
    </script>
</body>
</html>';
    exit;
    
} catch (Exception $e) {
    // 记录错误日志
    error_log('活码系统错误: ' . $e->getMessage());
    
    // 返回500错误
    header('HTTP/1.1 500 Internal Server Error');
    exit('系统错误，请联系管理员');
} 