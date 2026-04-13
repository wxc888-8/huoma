<?php
include('../includes/common.php');
if ($islogin2 == 1) {
} else exit("<script language='javascript'>window.location.href='./login.php';</script>");

// 获取今日数据
$today = date('Y-m-d');
$total_views = $DB->count("SELECT COUNT(*) FROM dwz_qrcode_visit WHERE qid IN (SELECT id FROM dwz_qrcode WHERE uid='$uid') AND DATE(addtime)='$today'");
$total_ips = $DB->count("SELECT COUNT(DISTINCT ip) FROM dwz_qrcode_visit WHERE qid IN (SELECT id FROM dwz_qrcode WHERE uid='$uid') AND DATE(addtime)='$today'");
$total_scanned = $DB->count("SELECT COUNT(DISTINCT qid) FROM dwz_qrcode_visit WHERE qid IN (SELECT id FROM dwz_qrcode WHERE uid='$uid') AND DATE(addtime)='$today'");
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>今日活码统计</title>
    <link href="../static/user/css/bootstrap.min.css" rel="stylesheet">
    <link href="../static/user/font-awesome/css/font-awesome.css" rel="stylesheet">
    <link href="https://lib.baomitu.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
    <script src="https://lib.baomitu.com/jquery/1.10.2/jquery.min.js"></script>
    <script src="https://lib.baomitu.com/twitter-bootstrap/3.0.0/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/echarts@5.4.0/dist/echarts.min.js"></script>
    <style>
    body {
        background-color: #f5f5f5;
        font-family: Arial, sans-serif;
        padding: 20px;
    }
    .panel {
        margin-bottom: 20px;
        background-color: #fff;
        border: 1px solid #ddd;
        border-radius: 4px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }
    .panel-heading {
        padding: 12px 15px;
        background-color: #f5f5f5;
        border-bottom: 1px solid #ddd;
    }
    .panel-title {
        margin: 0;
        font-size: 16px;
        font-weight: bold;
        color: #333;
    }
    .panel-body {
        padding: 20px;
    }
    .stat-box {
        background-color: #fff;
        border: 1px solid #ddd;
        border-radius: 4px;
        padding: 20px;
        text-align: center;
        box-shadow: 0 1px 2px rgba(0,0,0,0.1);
        margin-bottom: 20px;
    }
    .stat-box .number {
        font-size: 42px;
        font-weight: 300;
        color: #333;
        line-height: 1.2;
    }
    .stat-box .title {
        font-size: 16px;
        color: #777;
        margin-top: 5px;
    }
    .stat-icon {
        font-size: 24px;
        margin-bottom: 15px;
        color: #fff;
        width: 60px;
        height: 60px;
        line-height: 60px;
        border-radius: 50%;
        display: inline-block;
    }
    .bg-primary {
        background-color: #3598dc;
    }
    .bg-success {
        background-color: #26c281;
    }
    .bg-warning {
        background-color: #f0ad4e;
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
    .label {
        padding: 5px 10px;
        border-radius: 3px;
    }
    .label-success {
        background-color: #5cb85c;
    }
    .label-danger {
        background-color: #d9534f;
    }
    </style>
</head>
<body>
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="panel">
                    <div class="panel-heading">
                        <h3 class="panel-title"><i class="fa fa-calendar"></i> 今日活码统计 (<?php echo $today; ?>)</h3>
                    </div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="stat-box">
                                    <div class="stat-icon bg-primary"><i class="fa fa-eye"></i></div>
                                    <div class="number"><?php echo $total_views; ?></div>
                                    <div class="title">总访问量</div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="stat-box">
                                    <div class="stat-icon bg-success"><i class="fa fa-users"></i></div>
                                    <div class="number"><?php echo $total_ips; ?></div>
                                    <div class="title">独立IP数</div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="stat-box">
                                    <div class="stat-icon bg-warning"><i class="fa fa-qrcode"></i></div>
                                    <div class="number"><?php echo $total_scanned; ?></div>
                                    <div class="title">被扫码数</div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- 访问趋势 -->
                        <div class="panel">
                            <div class="panel-heading">
                                <h3 class="panel-title">今日小时访问趋势</h3>
                            </div>
                            <div class="panel-body">
                                <div id="hourlyChart" style="height: 350px;"></div>
                            </div>
                        </div>
                        
                        <!-- 热门活码 -->
                        <div class="panel">
                            <div class="panel-heading">
                                <h3 class="panel-title">今日热门活码</h3>
                            </div>
                            <div class="panel-body">
                                <div class="table-responsive">
                                    <table class="table table-striped table-hover">
                                        <thead>
                                            <tr>
                                                <th width="60px">排名</th>
                                                <th>活码名称</th>
                                                <th>访问量</th>
                                                <th>独立IP</th>
                                                <th>微信状态</th>
                                                <th>操作</th>
                                            </tr>
                                        </thead>
                                        <tbody id="topQrList">
                                            <tr>
                                                <td colspan="6" class="text-center">加载中...</td>
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
    
    <script>
    $(document).ready(function() {
        // 初始化小时趋势图表
        initHourlyChart();
        
        // 加载热门活码
        loadTopQrCodes();
    });
    
    // 初始化小时趋势图表
    function initHourlyChart() {
        var hourlyChart = echarts.init(document.getElementById('hourlyChart'));
        
        // 加载数据
        $.ajax({
            type: 'GET',
            url: 'ajax.php?act=get_today_hourly_stats',
            dataType: 'json',
            success: function(res) {
                if (res.code == 0) {
                    updateHourlyChart(hourlyChart, res.data);
                } else {
                    $('#hourlyChart').html('<div class="alert alert-danger">加载数据失败</div>');
                }
            },
            error: function() {
                $('#hourlyChart').html('<div class="alert alert-danger">服务器错误</div>');
            }
        });
    }
    
    // 更新小时趋势图表
    function updateHourlyChart(chart, data) {
        var hours = [];
        var views = [];
        
        // 创建0-23小时的数据结构
        for (var i = 0; i < 24; i++) {
            var hour = i < 10 ? '0' + i : '' + i;
            hours.push(hour + ':00');
            
            // 查找对应小时的数据
            var found = false;
            if (data.hours) {
                for (var j = 0; j < data.hours.length; j++) {
                    if (data.hours[j].hour == i) {
                        views.push(data.hours[j].views);
                        found = true;
                        break;
                    }
                }
            }
            
            if (!found) {
                views.push(0);
            }
        }
        
        // 设置图表
        chart.setOption({
            title: {
                text: '今日小时访问趋势',
                left: 'center'
            },
            tooltip: {
                trigger: 'axis',
                axisPointer: {
                    type: 'shadow'
                }
            },
            grid: {
                left: '3%',
                right: '4%',
                bottom: '3%',
                containLabel: true
            },
            xAxis: {
                type: 'category',
                data: hours,
                axisLabel: {
                    interval: 1
                }
            },
            yAxis: {
                type: 'value'
            },
            series: [
                {
                    name: '访问量',
                    type: 'bar',
                    data: views,
                    itemStyle: {
                        color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [
                            {offset: 0, color: '#83bff6'},
                            {offset: 0.5, color: '#188df0'},
                            {offset: 1, color: '#188df0'}
                        ])
                    },
                    emphasis: {
                        itemStyle: {
                            color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [
                                {offset: 0, color: '#2378f7'},
                                {offset: 0.7, color: '#2378f7'},
                                {offset: 1, color: '#83bff6'}
                            ])
                        }
                    }
                }
            ]
        });
    }
    
    // 加载热门活码
    function loadTopQrCodes() {
        $.ajax({
            type: 'GET',
            url: 'ajax.php?act=get_today_top_qr',
            dataType: 'json',
            success: function(res) {
                if (res.code == 0) {
                    updateTopQrList(res.data);
                } else {
                    $('#topQrList').html('<tr><td colspan="6" class="text-center">加载数据失败</td></tr>');
                }
            },
            error: function() {
                $('#topQrList').html('<tr><td colspan="6" class="text-center">服务器错误</td></tr>');
            }
        });
    }
    
    // 更新热门活码列表
    function updateTopQrList(data) {
        var html = '';
        
        if (data.qrcodes && data.qrcodes.length > 0) {
            for (var i = 0; i < data.qrcodes.length; i++) {
                var qr = data.qrcodes[i];
                var statusClass = qr.wechat_status == 1 ? 'label-success' : 'label-danger';
                var statusText = qr.wechat_status == 1 ? '正常' : '已屏蔽';
                var statusIcon = qr.wechat_status == 1 ? 'fa-check' : 'fa-ban';
                
                html += '<tr>';
                html += '<td>' + (i + 1) + '</td>';
                html += '<td>' + qr.name + '</td>';
                html += '<td>' + qr.today_views + '</td>';
                html += '<td>' + qr.today_ips + '</td>';
                html += '<td><span class="label ' + statusClass + '"><i class="fa ' + statusIcon + '"></i> ' + statusText + '</span></td>';
                html += '<td>';
                html += '<a href="qr-stats.php?id=' + qr.id + '" target="_blank" class="btn btn-xs btn-info"><i class="fa fa-bar-chart"></i> 详细统计</a>';
                html += '</td>';
                html += '</tr>';
            }
        } else {
            html = '<tr><td colspan="6" class="text-center">今日暂无活码访问数据</td></tr>';
        }
        
        $('#topQrList').html(html);
    }
    </script>
</body>
</html> 