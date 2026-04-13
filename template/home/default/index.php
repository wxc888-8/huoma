<?php
if (!defined('IN_CRONLITE')) exit();
?>
<!DOCTYPE html>
<html class="js cssanimations" lang="zh-cn">

<head>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8">
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <title><?php echo $conf['web_name'] . '-' . $conf['title']; ?></title>
    <meta name="keywords" content="<?php echo $conf['keywords']; ?>">
    <meta name="description" content="<?php echo $conf['description']; ?>">
    <link rel="shortcut icon" href="./static/picture/favicon.ico">
    <link rel="stylesheet" href="https://lib.baomitu.com/amazeui/2.7.2/css/amazeui.min.css">
    <style>
        .bg {
            background-image: url(<?php echo $background_image; ?>);
            background-repeat: no-repeat;
            background-attachment: fixed;
            background-position: center
        }

        .footer {
            position: relative;
            left: 0;
            bottom: 0;
            width: 100%;
            overflow: hidden
        }

        .footer p {
            color: #7f8c8d;
            margin: 0;
            padding: 15px;
            text-align: center;
            background: #17263e
        }

        .footer p a {
            color: #7f8c8d
        }

        .footer p a:hover {
            color: #bbb
        }
    </style>
</head>
<html>

<body class="am-with-topbar-fixed-top">
    <header class="am-topbar am-topbar-fixed-top am-sans-serif">
        <div class="am-container">
            <h1 class="am-topbar-brand">
                <a href="./">
                    <font color="#19a7f0"><i class="am-icon-diamond am-icon-sm"></i></font><?php echo $conf['web_name'] ?>
                </a>
            </h1>
            <button class="am-topbar-btn am-topbar-toggle am-btn am-btn-sm am-btn-secondary am-show-sm-only am-collapsed" data-am-collapse="{target: '#collapse-head'}">
                <span class="am-sr-only">导航切换</span>
                <span class="am-icon-bars"></span>
            </button>
            <nav class="am-topbar-collapse am-fr am-collapse" id="collapse-head" style="height: 20px;">
                <ul class="am-nav am-nav-pills am-topbar-nav">
                    <li><a href="./">首页</a></li>
                    <li><a href="./user">用户中心</a></li>
                </ul>
            </nav>
        </div>
    </header>
    <p>
        <center>
            <font color="#FF0000"><?php echo $conf['gg2'] ?></font>
        </center>
    </p>
    <div class="am-u-lg-12 am-padding-vertical">
        <hr>
        <div class="am-u-md-12 am-u-sm-centered">
            <div class="am-form-group am-form-select">
                <select id="dwz-type" name="dwz-type" class="am-form-field am-round">
                    <?php
                    echo dwzList();
                    ?>
                </select>
            </div>
            <div class="am-form-group am-form-select">
                <select id="dwz-pattern" name="dwz-pattern" class="am-form-field am-round">
                    <?php
                    echo pattern_list();
                    ?>
                </select>
            </div>
            <div class="am-form-group">
                <input type="url" name="url" id="url" class="am-form-field am-radius am-text-center am-round" required="">
            </div>
            <button type="submit" class="am-btn am-btn-success am-btn-sm am-btn-block am-round" id="start">生成</button>
        </div>
    </div>

    <div class="am-modal am-modal-no-btn" tabindex="-1" id="your-modal">
        <div class="am-modal-dialog">
            <div class="am-modal-hd">
                <a href="javascript: void(0)" class="am-close am-close-spin" data-am-modal-close="">×</a>
            </div>
            <div class="am-modal-bd">
                <p id="dwz"></p>
                <p id="data"></p>
                <p id="tzurl"></p>
                <p id="qrcode"></p>
            </div>
        </div>
    </div>

    <footer class="footer">
        <center>
            <p class="am-text-sm"><b>免责声明：</b>短网址由用户生成，所跳转的内容和本站无关，如违法，本站概不承担任何法律责任。本站严禁钓鱼，诈骗等违法站。</p>
            <p class="am-text-sm">Powered by <a href="./" target="_blank" rel="author"><?php echo $conf['web_name'] ?></a> © 2020-2021</p>
            <p><a href="http://beian.miit.gov.cn" target="_blank" style="text-decoration:none;"><?php echo $conf['icp'] ?></a></p>
        </center>
    </footer>
</body>

</html>

<script src="https://lib.baomitu.com/jquery/1.10.2/jquery.min.js"></script>
<script src="https://lib.baomitu.com/amazeui/2.3.0/js/amazeui.min.js"></script>
<script>
    $(document).ready(function() {
        $('#start').click(function() {
            $('#start').text('正在生成中，请耐心等待...');
            $("#start").addClass("am-btn-warning").removeClass("am-btn-success");
            var stype = $("select[id='dwz-type']").val();
            var pattern = $("select[id='dwz-pattern']").val();
            var url = $("input[id='url']").val();
            url = url.replace(/\+/g, "%2B");
            url = url.replace(/\&/g, "%26");
            $.ajax({
                type: "post",
                url: "ajax.php?act=creat1",
                dataType: "json",
                data: 'url=' + url + '&type=' + stype + '&pattern=' + pattern,
                async: true,
                success: function(obj) {
                    $('#start').text('一键生成');
                    $("#start").removeClass("am-btn-warning").addClass("am-btn-success");
                    if (obj.code == 0) {
                        var strJson = JSON.stringify(obj)
                        var data = $.parseJSON(strJson);
                        $('#dwz').html('短链地址：' + data.dwz);
                        $('#data').html('数据统计：<a href="' + data.data + '" target="_blank">点击查看</a>');
                        $('#tzurl').html('跳转链接：' + data.url);
                        $('#qrcode').html('<img class="qrimg" width="200px" src="includes/libs/qrcode.php?size=300&text=' + data.dwz + '" />');
                        var $modal = $('#your-modal');
                        $modal.modal();
                        $modal.css('margin-top', 0);
                    } else {
                        alert(obj.msg);
                    }
                },
                error: function(obj) {
                    $('#start').text('生成失败');
                    $("#start").removeClass("am-btn-warning").addClass("am-btn-danger");
                }
            });
        });
    });
</script>

<style type="text/css">
    * {
        margin: 0px;
        padding: 0px;
    }

    .login_alert {
        position: fixed;
        bottom: 0px;
        left: 0px;
        width: 100%;
        z-index: 9999;
    }

    .login_alert_close {
        position: absolute;
        top: -10px;
        right: 0px;
        z-index: 1;
        cursor: pointer;
    }

    .login_alert_box {
        width: 100%;
        text-align: center;
        background-color: rgba(61, 61, 61, 0.6);
        height: 60px;
    }

    .login_alert_box div {
        cursor: default;
        font-family: '微软雅黑';
        color: #66CCFF;
        font-size: 26px;
        font-weight: bold;
        line-height: 80px;
    }

    .login_alert_box div a {
        text-decoration: none;
        display: inline-block;
        padding: 5px 20px;
        font-size: 20px;
        color: #000;
        background-color: #00CC99;
        border-radius: 4px;
        line-height: 26px;
    }

    .login_alert_box div span {
        font-size: 18px;
    }

    .login_alert {
        animation: show_alert_left 1.6s;
        -webkit-animation: show_alert_left 1.6s;
        -moz-animation: show_alert_left 1.6s;
    }

    .login_alert_box {
        background-color: rgba(0, 0, 0, 0.6);
        height: 60px;
    }

    .login_alert_box:hover {
        background-color: rgba(0, 0, 0, 0.8);
    }

    .login_alert_box div {
        color: #66CCFF;
        font-size: 26px;
        line-height: 60px;
    }

    .login_alert_box div a {
        font-size: 20px;
        line-height: 26px;
        border-radius: 4px;
    }

    @keyframes show_alert_left {
        from {
            left: -100%;
        }

        to {
            left: 0px;
        }
    }

    @-webkit-keyframes show_alert_left {
        from {
            left: -100%;
        }

        to {
            left: 0px;
        }
    }

    @-moz-keyframes show_alert_left {
        from {
            left: -100%;
        }

        to {
            left: 0px;
        }
    }
</style>

<div class="login_alert" id="login_alert">
    <div class="login_alert_box">
        <div>
            联系：
            <a rel="nofollow" target="_blank" href="<?php echo $conf['group_link'] ?>">Q群</a>
            <span>|</span>
            <a rel="nofollow" target="_blank" style="color: #FF0000;background-color:#FFCC33;" href="https://api.btstu.cn/qqtalk/api.php?qq=<?php echo $conf['kf_qq'] ?>">QQ</a>
        </div>
    </div>
</div>