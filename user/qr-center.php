<?php
include('../includes/common.php');
if ($islogin2 == 1) {
} else exit("<script language='javascript'>window.location.href='./login.php';</script>");

// 设置页面标识符
$_GET['mod'] = 'qr-center';
$_GET['subpage'] = 'qr-center';

$title = '活码中心';
include('head.php');

// 获取统计数据
$total_qr = $DB->count("SELECT COUNT(*) FROM dwz_qrcode WHERE uid='$uid'");
$active_qr = $DB->count("SELECT COUNT(*) FROM dwz_qrcode WHERE uid='$uid' AND state=1");
$blocked_qr = $DB->count("SELECT COUNT(*) FROM dwz_qrcode WHERE uid='$uid' AND wechat_status=0");
$total_views = $DB->count("SELECT SUM(views) FROM dwz_qrcode WHERE uid='$uid'");
if(!$total_views) $total_views = 0;

// 获取今日数据
$today = date('Y-m-d');
$today_views = $DB->count("SELECT COUNT(*) FROM dwz_qrcode_visit WHERE qid IN (SELECT id FROM dwz_qrcode WHERE uid='$uid') AND DATE(addtime)='$today'");

// 获取最近创建的活码
$recent_qrs = array();
$rs = $DB->query("SELECT * FROM dwz_qrcode WHERE uid='$uid' ORDER BY addtime DESC LIMIT 5");
while($res = $DB->fetch($rs)) {
    $recent_qrs[] = $res;
}

// 获取热门活码
$hot_qrs = array();
$rs = $DB->query("SELECT * FROM dwz_qrcode WHERE uid='$uid' ORDER BY views DESC LIMIT 5");
while($res = $DB->fetch($rs)) {
    $hot_qrs[] = $res;
}
?>

<!-- 引入Bootstrap 5和Bootstrap Icons -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">

<style>
:root {
    --wechat-green: #07C160;
    --wechat-dark: #1A1A1A;
    --light-bg: #F7F7F7;
    --border-color: rgba(0, 0, 0, 0.05);
    --text-primary: #333;
    --text-secondary: #666;
    --card-shadow: 0 2px 12px rgba(0, 0, 0, 0.05);
}

body {
    background-color: var(--light-bg);
    color: var(--text-primary);
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
}

.dashboard-card {
    background-color: #fff;
    border-radius: 10px;
    box-shadow: var(--card-shadow);
    border: none;
    overflow: hidden;
    height: 100%;
    transition: transform 0.2s;
}

.dashboard-card:hover {
    transform: translateY(-3px);
}

.stat-card {
    display: flex;
    flex-direction: column;
    padding: 1.5rem;
    height: 100%;
}

.stat-icon {
    width: 50px;
    height: 50px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 10px;
    margin-bottom: 15px;
    font-size: 24px;
    color: white;
}

.icon-blue {
    background-color: #3598dc;
}

.icon-green {
    background-color: var(--wechat-green);
}

.icon-purple {
    background-color: #8e44ad;
}

.icon-red {
    background-color: #e7505a;
}

.stat-value {
    font-size: 28px;
    font-weight: 700;
    margin-bottom: 5px;
}

.stat-label {
    color: var(--text-secondary);
    font-size: 14px;
}

.stat-link {
    color: var(--text-primary);
    margin-top: auto;
    text-decoration: none;
    font-size: 14px;
    display: flex;
    align-items: center;
}

.stat-link:hover {
    color: var(--wechat-green);
}

.stat-link i {
    margin-left: 5px;
    transition: transform 0.2s;
}

.stat-link:hover i {
    transform: translateX(3px);
}

.card-header {
    background-color: white;
    border-bottom: 1px solid var(--border-color);
    padding: 15px 20px;
}

.card-title {
    margin-bottom: 0;
    color: var(--text-primary);
    font-weight: 600;
    font-size: 16px;
    display: flex;
    align-items: center;
}

.card-title i {
    margin-right: 10px;
    color: var(--wechat-green);
}

.table {
    margin-bottom: 0;
}

.table th {
    font-weight: 500;
    color: var(--text-secondary);
    border-bottom-width: 1px;
    background-color: rgba(0, 0, 0, 0.02);
}

.alert-custom {
    background-color: rgba(7, 193, 96, 0.1);
    border-color: rgba(7, 193, 96, 0.2);
    color: var(--wechat-green);
}

.badge-success {
    background-color: var(--wechat-green);
}

.badge-danger {
    background-color: #e7505a;
}

.btn-sm {
    padding: 0.25rem 0.5rem;
    font-size: 0.75rem;
}

.btn-warning {
    background-color: #F39C12;
    border-color: #F39C12;
    color: white;
}

.btn-info {
    background-color: #3598dc;
    border-color: #3598dc;
    color: white;
}

.chart-container {
    height: 350px;
    width: 100%;
}
</style>

<div class="container-fluid py-4">
    <!-- 欢迎提示 -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="alert alert-custom alert-dismissible fade show" role="alert">
                <i class="bi bi-info-circle me-2"></i>
                <strong>欢迎使用活码系统！</strong> 
                活码系统可以帮您管理和防止微信出现"已被屏蔽"问题，支持多种跳转方式和数据统计功能。
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
    </div>
    
    <!-- 总览数据 -->
    <div class="row g-4 mb-4">
        <div class="col-lg-3 col-md-6">
            <div class="dashboard-card">
                <div class="stat-card">
                    <div class="stat-icon icon-blue">
                        <i class="bi bi-qr-code"></i>
                    </div>
                    <div class="stat-value"><?php echo $total_qr; ?></div>
                    <div class="stat-label">活码总数</div>
                    <a href="qr-list.php" class="stat-link mt-3">
                        查看全部 <i class="bi bi-arrow-right-circle ms-1"></i>
                    </a>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="dashboard-card">
                <div class="stat-card">
                    <div class="stat-icon icon-green">
                        <i class="bi bi-eye"></i>
                    </div>
                    <div class="stat-value"><?php echo $total_views; ?></div>
                    <div class="stat-label">总访问量</div>
                    <a href="qr-list.php" class="stat-link mt-3">
                        查看详情 <i class="bi bi-arrow-right-circle ms-1"></i>
                    </a>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="dashboard-card">
                <div class="stat-card">
                    <div class="stat-icon icon-purple">
                        <i class="bi bi-clock"></i>
                    </div>
                    <div class="stat-value"><?php echo $today_views; ?></div>
                    <div class="stat-label">今日访问</div>
                    <a href="javascript:void(0);" onclick="showTodayStats()" class="stat-link mt-3">
                        查看图表 <i class="bi bi-arrow-right-circle ms-1"></i>
                    </a>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="dashboard-card">
                <div class="stat-card">
                    <div class="stat-icon icon-red">
                        <i class="bi bi-shield-x"></i>
                    </div>
                    <div class="stat-value"><?php echo $blocked_qr; ?></div>
                    <div class="stat-label">微信屏蔽数</div>
                    <a href="qr-list.php?wechat_status=0" class="stat-link mt-3">
                        查看详情 <i class="bi bi-arrow-right-circle ms-1"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <!-- 两列布局 -->
    <div class="row g-4 mb-4">
        <!-- 最近创建的活码 -->
        <div class="col-md-6">
            <div class="dashboard-card">
                <div class="card-header">
                    <h5 class="card-title">
                        <i class="bi bi-clock-history"></i> 最近创建的活码
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>活码名称</th>
                                    <th>入口域名</th>
                                    <th>访问量</th>
                                    <th>创建时间</th>
                                    <th>操作</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if(count($recent_qrs) > 0) {
                                    foreach($recent_qrs as $qr) {
                                        echo '<tr>';
                                        echo '<td>'.$qr['name'].'</td>';
                                        echo '<td>'.$qr['entry_domain'].'</td>';
                                        echo '<td>'.$qr['views'].'</td>';
                                        echo '<td>'.substr($qr['addtime'], 0, 10).'</td>';
                                        echo '<td>';
                                        echo '<a href="qr-edit.php?id='.$qr['id'].'" class="btn btn-warning btn-sm me-1"><i class="bi bi-pencil"></i></a>';
                                        echo '<a href="qr-stats.php?id='.$qr['id'].'" class="btn btn-info btn-sm"><i class="bi bi-bar-chart"></i></a>';
                                        echo '</td>';
                                        echo '</tr>';
                                    }
                                } else {
                                    echo '<tr><td colspan="5" class="text-center">暂无数据</td></tr>';
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- 热门活码 -->
        <div class="col-md-6">
            <div class="dashboard-card">
                <div class="card-header">
                    <h5 class="card-title">
                        <i class="bi bi-fire"></i> 热门活码
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>活码名称</th>
                                    <th>入口域名</th>
                                    <th>访问量</th>
                                    <th>微信状态</th>
                                    <th>操作</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if(count($hot_qrs) > 0) {
                                    foreach($hot_qrs as $qr) {
                                        $statusClass = $qr['wechat_status'] == 1 ? 'bg-success' : 'bg-danger';
                                        $statusText = $qr['wechat_status'] == 1 ? '正常' : '已屏蔽';
                                        $statusIcon = $qr['wechat_status'] == 1 ? 'bi-check' : 'bi-x';
                                        
                                        echo '<tr>';
                                        echo '<td>'.$qr['name'].'</td>';
                                        echo '<td>'.$qr['entry_domain'].'</td>';
                                        echo '<td>'.$qr['views'].'</td>';
                                        echo '<td><span class="badge '.$statusClass.'"><i class="bi '.$statusIcon.'"></i> '.$statusText.'</span></td>';
                                        echo '<td>';
                                        echo '<a href="qr-edit.php?id='.$qr['id'].'" class="btn btn-warning btn-sm me-1"><i class="bi bi-pencil"></i></a>';
                                        echo '<a href="qr-stats.php?id='.$qr['id'].'" class="btn btn-info btn-sm"><i class="bi bi-bar-chart"></i></a>';
                                        echo '</td>';
                                        echo '</tr>';
                                    }
                                } else {
                                    echo '<tr><td colspan="5" class="text-center">暂无数据</td></tr>';
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- 图表统计 -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="dashboard-card">
                <div class="card-header">
                    <h5 class="card-title">
                        <i class="bi bi-graph-up"></i> 近7天访问统计
                    </h5>
                </div>
                <div class="card-body">
                    <div id="visitChart" class="chart-container"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- 引入ECharts图表库 -->
<script src="https://cdn.jsdelivr.net/npm/echarts@5.4.0/dist/echarts.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
// 图表实例
var visitChart = null;

$(document).ready(function() {
    // 初始化图表
    initVisitChart();
    
    // 加载近7天统计数据
    loadWeekStats();
});

// 初始化图表
function initVisitChart() {
    visitChart = echarts.init(document.getElementById('visitChart'));
    
    // 设置图表初始显示
    visitChart.setOption({
        title: {
            text: '近7天访问趋势',
            left: 'center',
            textStyle: {
                color: '#333',
                fontWeight: 'normal',
                fontSize: 16
            }
        },
        tooltip: {
            trigger: 'axis',
            backgroundColor: 'rgba(255, 255, 255, 0.9)',
            borderColor: '#eee',
            borderWidth: 1,
            textStyle: {
                color: '#333'
            },
            axisPointer: {
                type: 'shadow',
                shadowStyle: {
                    color: 'rgba(0, 0, 0, 0.03)'
                }
            }
        },
        legend: {
            data: ['访问量', '独立IP'],
            top: 'bottom',
            textStyle: {
                color: '#666'
            }
        },
        grid: {
            left: '3%',
            right: '4%',
            bottom: '10%',
            top: '10%',
            containLabel: true
        },
        xAxis: {
            type: 'category',
            boundaryGap: false,
            data: [],
            axisLine: {
                lineStyle: {
                    color: '#ddd'
                }
            },
            axisLabel: {
                color: '#666'
            }
        },
        yAxis: {
            type: 'value',
            axisLine: {
                show: false
            },
            axisTick: {
                show: false
            },
            axisLabel: {
                color: '#666'
            },
            splitLine: {
                lineStyle: {
                    color: '#f5f5f5'
                }
            }
        },
        series: [
            {
                name: '访问量',
                type: 'line',
                data: [],
                smooth: true,
                lineStyle: {
                    width: 3,
                    color: '#07C160'
                },
                areaStyle: {
                    color: {
                        type: 'linear',
                        x: 0, y: 0, x2: 0, y2: 1,
                        colorStops: [
                            { offset: 0, color: 'rgba(7,193,96,0.5)' },
                            { offset: 1, color: 'rgba(7,193,96,0.1)' }
                        ]
                    }
                },
                itemStyle: {
                    color: '#07C160'
                }
            },
            {
                name: '独立IP',
                type: 'line',
                data: [],
                smooth: true,
                lineStyle: {
                    width: 3,
                    color: '#3598dc'
                },
                areaStyle: {
                    color: {
                        type: 'linear',
                        x: 0, y: 0, x2: 0, y2: 1,
                        colorStops: [
                            { offset: 0, color: 'rgba(53,152,220,0.5)' },
                            { offset: 1, color: 'rgba(53,152,220,0.1)' }
                        ]
                    }
                },
                itemStyle: {
                    color: '#3598dc'
                }
            }
        ]
    });
}

// 加载近7天统计数据
function loadWeekStats() {
    var startDate = new Date();
    startDate.setDate(startDate.getDate() - 6);
    var start = startDate.toISOString().slice(0, 10);
    var end = new Date().toISOString().slice(0, 10);
    
    $.ajax({
        type: 'GET',
        url: 'ajax.php?act=get_week_stats',
        dataType: 'json',
        success: function(res) {
            if (res.code == 0) {
                updateWeekChart(res.data);
            } else {
                layer.msg(res.msg || '加载统计数据失败', {icon: 2});
            }
        },
        error: function() {
            layer.msg('服务器错误', {icon: 2});
        }
    });
}

// 更新周统计图表
function updateWeekChart(data) {
    var stats = data.stats || [];
    
    // 访问趋势图表数据
    var dates = [];
    var viewsData = [];
    var ipData = [];
    
    // 处理数据
    for (var i = 0; i < stats.length; i++) {
        dates.push(stats[i].date);
        viewsData.push(stats[i].views);
        ipData.push(stats[i].unique_ips);
    }
    
    // 更新访问趋势图表
    visitChart.setOption({
        xAxis: {
            data: dates
        },
        series: [
            {
                name: '访问量',
                data: viewsData
            },
            {
                name: '独立IP',
                data: ipData
            }
        ]
    });
}

// 微信屏蔽检测
function checkWechatStatus() {
    layer.confirm('确定要检测所有活码的微信屏蔽状态吗？此操作可能需要较长时间。', {
        btn: ['确定','取消'],
        title: '微信屏蔽检测'
    }, function(){
        layer.msg('开始检测微信屏蔽状态，请稍候...', {icon: 16, time: 0});
        
        $.ajax({
            type: 'POST',
            url: 'ajax.php?act=check_wechat_status',
            dataType: 'json',
            success: function(res) {
                layer.closeAll();
                if (res.code == 0) {
                    layer.msg('检测完成！发现 ' + res.blocked + ' 个活码被微信屏蔽', {icon: 1});
                    setTimeout(function() {
                        window.location.reload();
                    }, 1500);
                } else {
                    layer.msg(res.msg || '检测失败', {icon: 2});
                }
            },
            error: function() {
                layer.closeAll();
                layer.msg('服务器错误', {icon: 2});
            }
        });
    });
}

// 批量更新状态
function batchCheckQR() {
    layer.open({
        type: 1,
        title: '批量更新活码状态',
        area: ['400px', '300px'],
        content: '<div class="p-4">' +
                '<form id="batchForm">' +
                '<div class="mb-3">' +
                '<label class="form-label">选择操作类型：</label>' +
                '<select class="form-select" name="type">' +
                '<option value="wechat_check">微信屏蔽检测</option>' +
                '<option value="reset_data">重置访问数据</option>' +
                '<option value="enable_all">启用所有活码</option>' +
                '<option value="disable_all">禁用所有活码</option>' +
                '</select>' +
                '</div>' +
                '<div class="mb-3">' +
                '<button type="button" onclick="submitBatchOperation()" class="btn btn-primary w-100">开始处理</button>' +
                '</div>' +
                '</form>' +
                '</div>',
        success: function(layero, index) {
            // 添加CSS样式
            $('<style>.p-4 { padding: 1.5rem; }</style>').appendTo('head');
        }
    });
}

// 提交批量操作
function submitBatchOperation() {
    var type = $('select[name="type"]').val();
    
    layer.closeAll();
    layer.msg('正在处理，请稍候...', {icon: 16, time: 0});
    
    $.ajax({
        type: 'POST',
        url: 'ajax.php?act=batch_qr_operation',
        data: {type: type},
        dataType: 'json',
        success: function(res) {
            layer.closeAll();
            if (res.code == 0) {
                layer.msg('操作成功！', {icon: 1});
                setTimeout(function() {
                    window.location.reload();
                }, 1500);
            } else {
                layer.msg(res.msg || '操作失败', {icon: 2});
            }
        },
        error: function() {
            layer.closeAll();
            layer.msg('服务器错误', {icon: 2});
        }
    });
}

// 今日统计弹窗
function showTodayStats() {
    layer.open({
        type: 2,
        title: '今日访问统计',
        shadeClose: true,
        shade: 0.8,
        area: ['90%', '90%'],
        content: 'qr-today-stats.php'
    });
}

// 窗口大小变化时重绘图表
$(window).resize(function() {
    if (visitChart) visitChart.resize();
});
</script>

<script src="../static/user/js/common-scripts.js"></script> 