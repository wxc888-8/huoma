<?php
/**
 * 数据库调试工具
 * 此文件应当直接访问，不要包含到现有系统中
 */

// 设置错误报告和日志
error_reporting(E_ALL);
ini_set('display_errors', 1); // 显示错误以便调试
ini_set('log_errors', 1);
ini_set('error_log', 'db_debug_error.log');

// 数据库连接信息 - 需要根据实际情况填写
$db_host = 'localhost'; // 数据库主机
$db_user = 'gd_1p4p0kbh_gaap'; // 数据库用户名
$db_pass = 'Y7AiKnJtYDc5yCC4'; // 数据库密码
$db_name = 'gd_1p4p0kbh_gaap'; // 数据库名

// 自定义函数防止冲突
function my_log($message) {
    echo "<div style='margin:5px;padding:10px;border:1px solid #eee;background:#f9f9f9;'>";
    echo htmlspecialchars($message);
    echo "</div>";
    error_log($message, 3, 'db_debug_error.log');
}

// 尝试直接连接数据库
function test_db_connection() {
    global $db_host, $db_user, $db_pass, $db_name;
    
    if (empty($db_host) || empty($db_user) || empty($db_name)) {
        my_log("错误: 请先在此文件中填写数据库连接信息");
        return false;
    }
    
    my_log("尝试连接到数据库: {$db_host}, 用户名: {$db_user}, 数据库: {$db_name}");
    
    try {
        $conn = new mysqli($db_host, $db_user, $db_pass, $db_name);
        
        if ($conn->connect_error) {
            my_log("数据库连接失败: " . $conn->connect_error);
            return false;
        }
        
        my_log("数据库连接成功!");
        
        // 测试执行简单查询
        $result = $conn->query("SHOW TABLES");
        if (!$result) {
            my_log("执行查询失败: " . $conn->error);
        } else {
            my_log("查询成功，发现表:");
            $tables = [];
            while ($row = $result->fetch_array()) {
                $tables[] = $row[0];
            }
            my_log(implode(", ", $tables));
        }
        
        $conn->close();
        return true;
    } catch (Exception $e) {
        my_log("连接过程中出现异常: " . $e->getMessage());
        return false;
    }
}

// 获取数据库结构信息
function analyze_db_structure() {
    global $db_host, $db_user, $db_pass, $db_name;
    
    if (empty($db_host) || empty($db_user) || empty($db_name)) {
        return;
    }
    
    try {
        $conn = new mysqli($db_host, $db_user, $db_pass, $db_name);
        
        if ($conn->connect_error) {
            return;
        }
        
        my_log("分析数据库结构:");
        
        // 获取所有表
        $result = $conn->query("SHOW TABLES");
        if (!$result) {
            my_log("无法获取表列表: " . $conn->error);
            return;
        }
        
        while ($row = $result->fetch_array()) {
            $table = $row[0];
            my_log("表: {$table}");
            
            // 获取表结构
            $structure = $conn->query("DESCRIBE {$table}");
            if (!$structure) {
                my_log("无法获取表结构: " . $conn->error);
                continue;
            }
            
            echo "<div style='margin-left:20px;'>";
            echo "<table border='1' cellpadding='3' cellspacing='0' style='border-collapse:collapse;'>";
            echo "<tr><th>字段</th><th>类型</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
            
            while ($field = $structure->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($field['Field']) . "</td>";
                echo "<td>" . htmlspecialchars($field['Type']) . "</td>";
                echo "<td>" . htmlspecialchars($field['Null']) . "</td>";
                echo "<td>" . htmlspecialchars($field['Key']) . "</td>";
                echo "<td>" . htmlspecialchars($field['Default'] ?? 'NULL') . "</td>";
                echo "<td>" . htmlspecialchars($field['Extra']) . "</td>";
                echo "</tr>";
            }
            
            echo "</table>";
            echo "</div><br>";
        }
        
        $conn->close();
    } catch (Exception $e) {
        my_log("分析过程中出现异常: " . $e->getMessage());
    }
}

// 测试特定问题查询
function test_specific_queries() {
    global $db_host, $db_user, $db_pass, $db_name;
    
    if (empty($db_host) || empty($db_user) || empty($db_name)) {
        return;
    }
    
    try {
        $conn = new mysqli($db_host, $db_user, $db_pass, $db_name);
        
        if ($conn->connect_error) {
            return;
        }
        
        my_log("测试特定查询:");
        
        // 测试统计查询
        $queries = [
            "SELECT count(*) from dwz_url" => "统计URL总数",
            "SELECT sum(view) from dwz_url" => "统计总访问量",
            "SELECT count(*) from dwz_user" => "统计用户总数",
            "SELECT count(*) from dwz_domain" => "统计域名总数"
        ];
        
        foreach ($queries as $query => $description) {
            my_log("测试查询: " . $description);
            $result = $conn->query($query);
            
            if (!$result) {
                my_log("查询失败: " . $conn->error);
            } else {
                $row = $result->fetch_array();
                my_log("结果: " . $row[0]);
            }
        }
        
        $conn->close();
    } catch (Exception $e) {
        my_log("查询测试过程中出现异常: " . $e->getMessage());
    }
}

// 获取PHP和系统信息
function show_php_info() {
    my_log("PHP版本: " . PHP_VERSION);
    my_log("操作系统: " . PHP_OS);
    my_log("Web服务器: " . $_SERVER['SERVER_SOFTWARE']);
    
    // 检查mysqli扩展
    if (extension_loaded('mysqli')) {
        my_log("MySQL客户端版本: " . mysqli_get_client_info());
    } else {
        my_log("警告: mysqli扩展未加载");
    }
    
    // 检查函数冲突
    my_log("检查函数冲突情况:");
    $conflicting_functions = ['getrand2', 'custom_error_handler'];
    
    foreach ($conflicting_functions as $func) {
        if (function_exists($func)) {
            my_log("函数 {$func} 已定义");
        } else {
            my_log("函数 {$func} 未定义");
        }
    }
}

// 系统文件冲突解决方案
function show_conflict_solution() {
    my_log("===== 函数冲突解决方案 =====");
    my_log("你的系统中存在函数名冲突，例如getrand2()函数在多个文件中被定义。");
    my_log("要解决这个问题，你需要修改其中一个文件，添加条件检查：");
    
    echo "<div style='background:#f5f5f5;padding:15px;border:1px solid #ddd;margin:10px 0;'>";
    echo "<p>在 <code>/www/wwwroot/gd-1p4p0kbh.gaapqcloud.com.cn/includes/core.func.php</code> 修改第270行左右:</p>";
    echo "<pre style='background:#fff;padding:10px;border:1px solid #eee;'>";
    echo "// 原代码:
function getrand2(\$length = 6) {
    \$str = null;
    \$strPol = \"0123456789abcdefghijklmnopqrstuvwxyz\";
    \$max = strlen(\$strPol) - 1;
    for(\$i = 0; \$i < \$length; \$i++) {
        \$str .= \$strPol[rand(0, \$max)];
    }
    return \$str;
}

// 修改为:
if (!function_exists('getrand2')) {
    function getrand2(\$length = 6) {
        \$str = null;
        \$strPol = \"0123456789abcdefghijklmnopqrstuvwxyz\";
        \$max = strlen(\$strPol) - 1;
        for(\$i = 0; \$i < \$length; \$i++) {
            \$str .= \$strPol[rand(0, \$max)];
        }
        return \$str;
    }
}</pre>";
    echo "</div>";
}

// 清理系统解决方案
function show_cleanup_solution() {
    my_log("===== 系统清理建议 =====");
    my_log("如果你无法直接编辑系统文件，建议：");
    my_log("1. 备份原始index.php文件");
    my_log("2. 恢复到简单的index.php版本，删除我们添加的调试代码");
    my_log("3. 只保留必要的错误日志记录");
    
    echo "<div style='background:#f5f5f5;padding:15px;border:1px solid #ddd;margin:10px 0;'>";
    echo "<p>简化版的index.php开头修改建议:</p>";
    echo "<pre style='background:#fff;padding:10px;border:1px solid #eee;'>";
    echo "<?php
// 只保留简单的错误日志记录
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', 'error.log');

\$is_defend = true;
include(\"./includes/common.php\");
@header('Content-Type: text/html; charset=UTF-8');
\$background_image = \"https://www.dmoe.cc/random.php\";

// 其余原始代码...</pre>";
    echo "</div>";
}

// 输出页面头部
header('Content-Type: text/html; charset=UTF-8');
echo "<!DOCTYPE html>
<html>
<head>
    <title>数据库调试工具</title>
    <meta charset='utf-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 20px; line-height: 1.6; }
        h1 { color: #333; }
        h2 { color: #555; margin-top: 30px; border-bottom: 1px solid #eee; padding-bottom: 10px; }
        .alert { background: #f8d7da; color: #721c24; padding: 15px; border-radius: 4px; margin-bottom: 20px; }
        .info { background: #d1ecf1; color: #0c5460; padding: 15px; border-radius: 4px; margin-bottom: 20px; }
        .success { background: #d4edda; color: #155724; padding: 15px; border-radius: 4px; margin-bottom: 20px; }
        pre { overflow-x: auto; }
    </style>
</head>
<body>
    <h1>数据库调试工具</h1>
    <div class='alert'>
        <strong>注意:</strong> 此工具仅用于调试目的。使用完毕后请从服务器删除。
    </div>
    <div class='info'>
        <p>此工具将帮助您诊断数据库连接问题和函数冲突问题。</p>
        <p>请在上方代码中填写您的数据库连接信息（db_host, db_user, db_pass, db_name）。</p>
    </div>
    
    <h2>系统信息</h2>
";

// 获取系统信息
show_php_info();

echo "<h2>数据库连接测试</h2>";
if (test_db_connection()) {
    echo "<div class='success'>数据库连接测试成功</div>";
    echo "<h2>数据库结构分析</h2>";
    analyze_db_structure();
    
    echo "<h2>特定查询测试</h2>";
    test_specific_queries();
} else {
    echo "<div class='alert'>数据库连接测试失败 - 请确保填写了正确的连接信息</div>";
}

echo "<h2>函数冲突解决方案</h2>";
show_conflict_solution();

echo "<h2>系统清理建议</h2>";
show_cleanup_solution();

echo "</body></html>"; 