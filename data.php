<?php
$is_defend = true;
include("./includes/common.php");
if (!isset($_GET['id'])) {
    exit("<script language='javascript'>alert('非法访问');window.location.href='./';</script>");
}
if ($conf['statistics'] == 1) {
    if ($islogin2 == 1) {
    } else exit("<script language='javascript'>alert('登录后才能查看');window.location.href='./user/login.php';</script>");
}
if ($conf['vip_tj'] == 1) {
    if ($islogin2 == 1) {
        if ($userrow['vip'] < $date) {
            exit("<script language='javascript'>alert('会员才可查看统计');window.location.href='./';</script>");
        }
    } else {
        exit("<script language='javascript'>alert('登录后才能查看');window.location.href='./user/login.php';</script>");
    }
}

$id = $_GET['id'];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>网址统计</title>
    <link href="./static/user/css/bootstrap.min.css" rel="stylesheet">
    <link href="./static/user/css/bootstrap-reset.css" rel="stylesheet">
    <link href="./static/user/font-awesome/css/font-awesome.css" rel="stylesheet">
    <link href="https://cdn.staticfile.org/font-awesome/4.7.0/css/font-awesome.css" rel="stylesheet">
    <link href="./static/user/css/style.css" rel="stylesheet">
    <link href="./static/user/css/style-responsive.css" rel="stylesheet">
    <link href="./static/plugin/morris/morris.css" rel="stylesheet">
    <link href="./static/preview-photo/preview-photo.css" rel="stylesheet">
</head>

<body>
    <section class="wrapper tab-container">
        <div class="row state-overview">
            <div class="col-lg-3 col-sm-6">
                <section class="panel">
                    <div class="symbol terques">
                        <i class="fa fa-product-hunt"></i>
                    </div>
                    <div class="value">
                        <h1 id="count1">获取中..</h1>
                        <p>今日IP</p>
                    </div>
                </section>
            </div>
            <div class="col-lg-3 col-sm-6">
                <section class="panel">
                    <div class="symbol yellow">
                        <i class="fa fa-vine"></i>
                    </div>
                    <div class="value">
                        <h1 id="count2">获取中..</h1>
                        <p>今日PV</p>
                    </div>
                </section>
            </div>
            <div class="col-lg-3 col-sm-6">
                <section class="panel">
                    <div class="symbol red">
                        <i class="fa fa-product-hunt"></i>
                    </div>
                    <div class="value">
                        <h1 id="count3">获取中</h1>
                        <p>昨日IP</p>
                    </div>
                </section>
            </div>
            <div class="col-lg-3 col-sm-6">
                <section class="panel">
                    <div class="symbol blue">
                        <i class="fa fa-vine"></i>
                    </div>
                    <div class="value">
                        <h1 id="count4">获取中</h1>
                        <p>昨日PV</p>
                    </div>
                </section>
            </div>
        </div>
        <div class="tab-content">
            <div class="tab-pane active" id="morris">
                <div class="row">
                    <div class="col-lg-12">
                        <section class="panel">
                            <header class="panel-heading">
                                近七日访问数据
                            </header>
                            <div class="panel-body">
                                <div id="hero-area" class="graph"></div>
                            </div>
                        </section>
                    </div>
                    <div class="col-lg-12">
                        <section class="panel">
                            <header class="panel-heading">
                                客户端
                            </header>
                            <div class="panel-body">
                                <div id="hero-donut" class="graph"></div>
                            </div>
                        </section>
                    </div>
                </div>
            </div>
        </div>
    </section>
</body>
<script src="./static/user/js/jquery.js" type="text/javascript"></script>
<script src="./static/user/js/bootstrap.min.js" type="text/javascript"></script>
<script src="./static/user/js/jquery.nicescroll.js" type="text/javascript"></script>
<script src="https://cdn.staticfile.org/layer/2.3/layer.js" type="text/javascript"></script>
<script src="./static/preview-photo/preview-photo.js" type="text/javascript"></script>
<script src="./static/plugin/morris/morris.min.js" type="text/javascript"></script>
<script src="./static/plugin/morris/raphael-min.js" type="text/javascript"></script>
<script src="./static/user/js/Chart.js"></script>
<script src="./static/user/js/common-scripts.js"></script>
<script src="./static/user/js/all-chartjs.js" type="text/javascript"></script>
<script src="./static/user/js/sparkline-chart.js" type="text/javascript"></script>
<script>
    $(document).ready(function() {
        $.ajax({
            url: "ajax.php?act=tongji&id=<?php echo $id ?>",
            type: "GET",
            dataType: "json",
            success: function(result) {
                week = result.week;
                system = result.system;
                $('#count1').html(week[0][1]);
                $('#count2').html(week[0][2]);
                $('#count3').html(week[1][1]);
                $('#count4').html(week[1][2]);
                var Script = function() {
                    $(function() {
                        Morris.Area({
                            element: 'hero-area',
                            data: [{
                                    period: week[6][0],
                                    ip: week[6][1],
                                    pv: week[6][2]
                                },
                                {
                                    period: week[5][0],
                                    ip: week[5][1],
                                    pv: week[5][2]
                                },
                                {
                                    period: week[4][0],
                                    ip: week[4][1],
                                    pv: week[4][2]
                                },
                                {
                                    period: week[3][0],
                                    ip: week[3][1],
                                    pv: week[3][2]
                                },
                                {
                                    period: week[2][0],
                                    ip: week[2][1],
                                    pv: week[2][2]
                                },
                                {
                                    period: week[1][0],
                                    ip: week[1][1],
                                    pv: week[1][2]
                                },
                                {
                                    period: week[0][0],
                                    ip: week[0][1],
                                    pv: week[0][2]
                                }
                            ],

                            xkey: 'period',
                            ykeys: ['ip', 'pv'],
                            labels: ['ip', 'pv'],
                            hideHover: 'auto',
                            lineWidth: 1,
                            pointSize: 5,
                            lineColors: ['#4a8bc2', '#ff6c60'],
                            fillOpacity: 0.5,
                            smooth: true
                        });

                        Morris.Donut({
                            element: 'hero-donut',
                            data: [{
                                    label: 'Window',
                                    value: system[0]
                                },
                                {
                                    label: 'iPhone',
                                    value: system[2]
                                },
                                {
                                    label: 'Android',
                                    value: system[1]
                                }
                            ],
                            colors: ['#41cac0', '#49e2d7', '#34a39b'],
                            formatter: function(y) {
                                return y + "%"
                            }
                        });
                    });
                }();
            }
        });
    })
</script>