<?php
include('../includes/common.php');
if ($islogin2 == 1) {
} else exit("<script language='javascript'>window.location.href='./login.php';</script>");

// 设置页面标识符
$_GET['mod'] = 'qr-center';
$_GET['subpage'] = 'qr-stats';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if (!$id) {
    exit("<script language='javascript'>alert('参数错误');window.location.href='./qr-list.php';</script>");
}

// 获取活码信息
$qr = $DB->get_row("SELECT * FROM dwz_qrcode WHERE id='$id' AND uid='$uid'");
if (!$qr) {
    exit("<script language='javascript'>alert('活码不存在或已被删除');window.location.href='./qr-list.php';</script>");
}

$title = '活码统计 - ' . $qr['name'];
include('head.php');
?>

<section id="main-content">
    <div class="wrapper">
        <!-- 页面头部 -->
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title"><i class="fa fa-bar-chart"></i> 活码统计 - <?php echo $qr['name']; ?></h3>
                    </div>
                    <div class="panel-body">
                        <!-- 数据筛选 -->
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <div class="panel panel-default">
                                    <div class="panel-body" style="padding: 10px;">
                                        <form class="form-inline" id="statFilterForm">
                                            <input type="hidden" name="id" value="<?php echo $id; ?>">
                                            <div class="form-group mr-3">
                                                <label>统计类型：</label>
                                                <select class="form-control input-sm" name="type" id="statType">
                                                    <option value="day">按天统计</option>
                                                    <option value="month">按月统计</option>
                                                    <option value="year">按年统计</option>
                                                </select>
                                            </div>
                                            <div class="form-group mr-3">
                                                <label>时间范围：</label>
                                                <div class="input-daterange input-group">
                                                    <input type="date" class="form-control input-sm" name="start_date" id="startDate" value="<?php echo date('Y-m-d', strtotime('-7 days')); ?>">
                                                    <span class="input-group-addon">至</span>
                                                    <input type="date" class="form-control input-sm" name="end_date" id="endDate" value="<?php echo date('Y-m-d'); ?>">
                                                </div>
                                            </div>
                                            <button type="button" class="btn btn-primary btn-sm" onclick="loadStats()">
                                                <i class="fa fa-search"></i> 查询
                                            </button>
                                            <button type="button" class="btn btn-info btn-sm" onclick="exportData()">
                                                <i class="fa fa-download"></i> 导出数据
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- 数据概览 -->
                        <div class="row">
                            <div class="col-md-3">
                                <div class="mini-stat clearfix">
                                    <span class="mini-stat-icon bg-info"><i class="fa fa-eye"></i></span>
                                    <div class="mini-stat-info">
                                        <span id="totalViews"><?php echo $qr['views']; ?></span>
                                        总访问量
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mini-stat clearfix">
                                    <span class="mini-stat-icon bg-success"><i class="fa fa-users"></i></span>
                                    <div class="mini-stat-info">
                                        <span id="totalIp"><?php echo $qr['ip_count']; ?></span>
                                        访问IP数
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mini-stat clearfix">
                                    <span class="mini-stat-icon bg-warning"><i class="fa fa-clock-o"></i></span>
                                    <div class="mini-stat-info">
                                        <span id="todayViews">0</span>
                                        今日访问量
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mini-stat clearfix">
                                    <span class="mini-stat-icon bg-danger"><i class="fa fa-calendar"></i></span>
                                    <div class="mini-stat-info">
                                        <span id="yesterdayViews">0</span>
                                        昨日访问量
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- 统计图表 -->
                        <div class="row mt-4">
                            <div class="col-md-12">
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h3 class="panel-title">访问趋势</h3>
                                    </div>
                                    <div class="panel-body">
                                        <div id="visitChart" style="height: 350px;"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h3 class="panel-title">设备分布</h3>
                                    </div>
                                    <div class="panel-body">
                                        <div id="deviceChart" style="height: 300px;"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h3 class="panel-title">浏览器分布</h3>
                                    </div>
                                    <div class="panel-body">
                                        <div id="browserChart" style="height: 300px;"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- 最近访问记录 -->
                        <div class="row mt-4">
                            <div class="col-md-12">
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h3 class="panel-title">最近访问记录</h3>
                                    </div>
                                    <div class="panel-body">
                                        <div class="table-responsive">
                                            <table class="table table-striped table-bordered table-hover">
                                                <thead>
                                                    <tr>
                                                        <th width="60px">序号</th>
                                                        <th>访问IP</th>
                                                        <th>所在地区</th>
                                                        <th>设备类型</th>
                                                        <th>浏览器</th>
                                                        <th>来源网址</th>
                                                        <th width="160px">访问时间</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="visitLogBody">
                                                    <tr>
                                                        <td colspan="7" class="text-center">加载中...</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
.mr-3 {
    margin-right: 15px;
}
.mb-3 {
    margin-bottom: 15px;
}
.mt-4 {
    margin-top: 20px;
}
.mini-stat {
    background: #fff;
    padding: 20px;
    border-radius: 4px;
    margin-bottom: 20px;
    border: 1px solid #ddd;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}
.mini-stat-icon {
    width: 60px;
    height: 60px;
    display: inline-block;
    line-height: 60px;
    text-align: center;
    font-size: 30px;
    border-radius: 100%;
    float: left;
    margin-right: 10px;
    color: #fff;
}
.mini-stat-info {
    font-size: 14px;
    padding-top: 2px;
    color: #777;
}
.mini-stat-info span {
    display: block;
    font-size: 24px;
    font-weight: 600;
    color: #333;
    margin-bottom: 5px;
}
.bg-info {
    background-color: #5bc0de;
}
.bg-success {
    background-color: #5cb85c;
}
.bg-warning {
    background-color: #f0ad4e;
}
.bg-danger {
    background-color: #d9534f;
}
.panel-heading {
    padding: 12px 15px;
}
.panel-title {
    margin: 0;
    font-size: 16px;
    font-weight: bold;
    color: #333;
}
table > thead > tr > th {
    text-align: center;
    vertical-align: middle;
    background-color: #f5f5f5;
    border-bottom: 2px solid #ddd;
    padding: 12px 8px;
}
table > tbody > tr > td {
    text-align: center;
    vertical-align: middle;
    padding: 12px 8px;
}
.input-daterange {
    max-width: 300px;
}
</style>

<!-- 引入ECharts图表库 -->
<script src="https://cdn.jsdelivr.net/npm/echarts@5.4.0/dist/echarts.min.js"></script>

<script>
// 图表实例
var visitChart = null;
var deviceChart = null;
var browserChart = null;

$(document).ready(function() {
    // 初始化图表
    initCharts();
    
    // 加载统计数据
    loadStats();
    
    // 加载访问记录
    loadVisitLogs();
});

// 初始化图表
function initCharts() {
    visitChart = echarts.init(document.getElementById('visitChart'));
    deviceChart = echarts.init(document.getElementById('deviceChart'));
    browserChart = echarts.init(document.getElementById('browserChart'));
    
    // 设置图表初始显示
    visitChart.setOption({
        title: {
            text: '访问量统计',
            left: 'center'
        },
        tooltip: {
            trigger: 'axis'
        },
        legend: {
            data: ['访问量', '独立IP'],
            top: 'bottom'
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
            data: []
        },
        yAxis: {
            type: 'value'
        },
        series: [
            {
                name: '访问量',
                type: 'line',
                data: [],
                smooth: true,
                lineStyle: {
                    width: 3,
                    color: '#5bc0de'
                },
                areaStyle: {
                    color: {
                        type: 'linear',
                        x: 0, y: 0, x2: 0, y2: 1,
                        colorStops: [
                            { offset: 0, color: 'rgba(91,192,222,0.5)' },
                            { offset: 1, color: 'rgba(91,192,222,0.1)' }
                        ]
                    }
                }
            },
            {
                name: '独立IP',
                type: 'line',
                data: [],
                smooth: true,
                lineStyle: {
                    width: 3,
                    color: '#5cb85c'
                },
                areaStyle: {
                    color: {
                        type: 'linear',
                        x: 0, y: 0, x2: 0, y2: 1,
                        colorStops: [
                            { offset: 0, color: 'rgba(92,184,92,0.5)' },
                            { offset: 1, color: 'rgba(92,184,92,0.1)' }
                        ]
                    }
                }
            }
        ]
    });
    
    deviceChart.setOption({
        title: {
            text: '设备类型分布',
            left: 'center'
        },
        tooltip: {
            trigger: 'item',
            formatter: '{a} <br/>{b}: {c} ({d}%)'
        },
        legend: {
            orient: 'vertical',
            left: 'left',
            data: []
        },
        series: [
            {
                name: '设备类型',
                type: 'pie',
                radius: '70%',
                center: ['50%', '50%'],
                data: [],
                emphasis: {
                    itemStyle: {
                        shadowBlur: 10,
                        shadowOffsetX: 0,
                        shadowColor: 'rgba(0, 0, 0, 0.5)'
                    }
                }
            }
        ]
    });
    
    browserChart.setOption({
        title: {
            text: '浏览器分布',
            left: 'center'
        },
        tooltip: {
            trigger: 'item',
            formatter: '{a} <br/>{b}: {c} ({d}%)'
        },
        legend: {
            orient: 'vertical',
            left: 'left',
            data: []
        },
        series: [
            {
                name: '浏览器类型',
                type: 'pie',
                radius: '70%',
                center: ['50%', '50%'],
                data: [],
                emphasis: {
                    itemStyle: {
                        shadowBlur: 10,
                        shadowOffsetX: 0,
                        shadowColor: 'rgba(0, 0, 0, 0.5)'
                    }
                }
            }
        ]
    });
}

// 加载统计数据
function loadStats() {
    var id = <?php echo $id; ?>;
    var type = $('#statType').val();
    var startDate = $('#startDate').val();
    var endDate = $('#endDate').val();
    
    $.ajax({
        type: 'GET',
        url: 'ajax.php?act=get_qr_stats',
        data: {
            id: id,
            type: type,
            start_date: startDate,
            end_date: endDate
        },
        dataType: 'json',
        success: function(res) {
            if (res.code == 0) {
                updateCharts(res.data);
                updateTodayStats();
            } else {
                layer.msg(res.msg || '加载统计数据失败', {icon: 2});
            }
        },
        error: function() {
            layer.msg('服务器错误', {icon: 2});
        }
    });
}

// 更新图表数据
function updateCharts(data) {
    var stats = data.stats || [];
    var devices = data.devices || [];
    var browsers = data.browsers || [];
    
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
    
    // 更新设备分布图表
    deviceChart.setOption({
        legend: {
            data: devices.map(function(item) { return item.name; })
        },
        series: [
            {
                data: devices
            }
        ]
    });
    
    // 更新浏览器分布图表
    browserChart.setOption({
        legend: {
            data: browsers.map(function(item) { return item.name; })
        },
        series: [
            {
                data: browsers
            }
        ]
    });
}

// 获取今日和昨日统计
function updateTodayStats() {
    var id = <?php echo $id; ?>;
    var today = new Date().toISOString().slice(0, 10);
    var yesterday = new Date(Date.now() - 86400000).toISOString().slice(0, 10);
    
    $.ajax({
        type: 'GET',
        url: 'ajax.php?act=get_qr_stats',
        data: {
            id: id,
            type: 'day',
            start_date: yesterday,
            end_date: today
        },
        dataType: 'json',
        success: function(res) {
            if (res.code == 0) {
                var stats = res.data.stats || [];
                var todayViews = 0;
                var yesterdayViews = 0;
                
                // 查找今日和昨日数据
                for (var i = 0; i < stats.length; i++) {
                    if (stats[i].date === today) {
                        todayViews = stats[i].views;
                    } else if (stats[i].date === yesterday) {
                        yesterdayViews = stats[i].views;
                    }
                }
                
                // 更新显示
                $('#todayViews').text(todayViews);
                $('#yesterdayViews').text(yesterdayViews);
            }
        }
    });
}

// 加载访问日志
function loadVisitLogs() {
    var id = <?php echo $id; ?>;
    
    $.ajax({
        type: 'GET',
        url: 'ajax.php?act=get_qr_visit_logs',
        data: {
            id: id,
            limit: 20
        },
        dataType: 'json',
        success: function(res) {
            if (res.code == 0) {
                var logs = res.data || [];
                var html = '';
                
                if (logs.length > 0) {
                    for (var i = 0; i < logs.length; i++) {
                        var log = logs[i];
                        html += '<tr>';
                        html += '<td>' + (i + 1) + '</td>';
                        html += '<td>' + (log.ip || '-') + '</td>';
                        html += '<td>' + (log.address || '-') + '</td>';
                        html += '<td>' + (log.device || '-') + '</td>';
                        html += '<td>' + (log.browser || '-') + '</td>';
                        html += '<td>' + (log.referer || '-') + '</td>';
                        html += '<td>' + log.addtime + '</td>';
                        html += '</tr>';
                    }
                } else {
                    html = '<tr><td colspan="7" class="text-center">暂无访问记录</td></tr>';
                }
                
                $('#visitLogBody').html(html);
            } else {
                $('#visitLogBody').html('<tr><td colspan="7" class="text-center">加载失败</td></tr>');
            }
        },
        error: function() {
            $('#visitLogBody').html('<tr><td colspan="7" class="text-center">服务器错误</td></tr>');
        }
    });
}

// 导出数据
function exportData() {
    var id = <?php echo $id; ?>;
    var type = $('#statType').val();
    var startDate = $('#startDate').val();
    var endDate = $('#endDate').val();
    
    var url = 'ajax.php?act=export_qr_stats&id=' + id + '&type=' + type + 
              '&start_date=' + startDate + '&end_date=' + endDate;
    
    window.open(url, '_blank');
}

// 窗口大小变化时重绘图表
$(window).resize(function() {
    if (visitChart) visitChart.resize();
    if (deviceChart) deviceChart.resize();
    if (browserChart) browserChart.resize();
});
</script>

<script src="../static/user/js/common-scripts.js"></script> 