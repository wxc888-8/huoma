<?php
if (!defined('IN_CRONLITE')) exit();
?>
<!DOCTYPE html>
<html lang="zh-CN">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="renderer" content="webkit">
    <title><?php echo $conf['web_name'] . '-' . $conf['title']; ?></title>
    <meta name="Keywords" content="<?php echo $conf['keywords']; ?>" />
    <meta name="Description" content="<?php echo $conf['description']; ?>" />
    <link rel="stylesheet" href="static/rain/css/index.min.css">
    <link rel="stylesheet" href="static/rain/css/main.css">
    <link rel="stylesheet" href="static/rain/css/font-awesome.min.css">
    <link rel="shortcut icon" href="./static/picture/favicon.ico">

    <script src="https://lib.baomitu.com/jquery/2.2.4/jquery.min.js"></script>
    <script>
        $(document).ready(function() {
            $("#search").css("height", ($(window).height()) + "px");
            $("#search").css("margin-top", "-65px");
            $('#search-kw').click(function() {
                $("#dwz-type").removeClass("hide");
                $("#dwz-pattern").removeClass("hide");
            });
            $('#search-btn').click(function() {
                $("#dwzdate").hide();
                $('#search-btn').text('生成中...');
                var stype = $("select[id='dwz-type']").val();
                var pattern = $("select[id='dwz-pattern']").val();
                var url = $("input[id='search-kw']").val();
                url = url.replace(/\+/g, "%2B");
                url = url.replace(/\&/g, "%26");
                $.ajax({
                    type: "post",
                    url: "ajax.php?act=creat1",
                    dataType: "json",
                    data: 'url=' + url + '&type=' + stype + '&pattern=' + pattern,
                    success: function(obj) {
                        $('#search-btn').text('生成一下');
                        if (obj.code == 0) {
                            $("#dwzdate").html(
                                '短链地址：' + obj.dwz + '<br />' +
                                '数据统计：<a href="' + obj.data + '" target="_blank">点击查看</a><br />' +
                                '跳转网址：' + atob(obj.url) + '<br />' +
                                '<br /><img class="qrimg" width="200px" src="includes/libs/qrcode.php?size=300&text=' + obj.dwz + '" />'
                            );
                            $("#dwzdate").slideDown();
                        } else if (obj.code == -1) {
                            $("#dwzdate").html(
                                obj.msg
                            );
                            $("#dwzdate").slideDown();
                        } else {
                            alert(obj.msg);
                        }
                    },
                    error: function(a) {
                        $('#search-btn').text('生成失败');
                    }
                });
            });
            $("#search").css("background", "#000000 url('<?php echo $background_image; ?>') no-repeat right center");
            $("#search").css("background-size", "100% 100%");
        });
    </script>

</head>

<body id="page-index">

    <header id="masthead" data-login-status="0">
        <nav id="site-nav" class="navbar navbar-default">
            <a href="./" class="navbar-brand visible-xs-inline-block" title="<?php echo $conf['web_name'] ?>"><?php echo $conf['web_name'] ?></a>
            <input type="checkbox" id="navbar-toggle" class="hidden">
            <label for="navbar-toggle" class="visible-xs-inline-block"></label>

            <div class="nav-links">
                <ul class="nav list-unstyled">
                    <li><a href="./">首页</a></li>
                    <li><a href="https://api.btstu.cn/qqtalk/api.php?qq=<?php echo $conf['kf_qq']; ?>" target="_blank">联系站长</a></li>
                </ul>
                <hr class="visible-xs">
                <ul class="nav navbar-right list-unstyled">
                    <li id="message-link"><a href="javascript:void(0)">公告</a></li>
                    <li class="divider hidden-xs"><span>|</span></li>
                    <?php
                    if ($islogin2 == 1) {
                    ?>
                        <li><a href="./user">用户中心</a></li>
                    <?php
                    } else {
                    ?>
                        <li><a href="./user/login.php">登录</a></li>
                        <li class="divider hidden-xs"><span>|</span></li>
                        <li><a href="./user/reg.php">注册</a></li>
                    <?php
                    }
                    ?>
                </ul>
            </div>
        </nav>
    </header>

    <main>
        <section id="search">
            <div class="container">
                <div class="absolute-center">
                    <div class="logo center-block">
                        <h1>
                            <a href="./">
                                <img src="static/picture/logo.png" alt="&#39;zhidu logo">
                            </a>
                        </h1>
                    </div>
                    <div class="search-form form-inline">
                        <div class="form-group">
                            <label for="search-kw" class="hidden"></label>
                            <input type="search" id="search-kw" class="form-control" name="longurl" placeholder="http://www.baidu.com" autocomplete="off">
                        </div>
                        <button type="submit" id="search-btn" class="btn btn-default">生成一下</button>
                    </div>
                    <select name="dwz-type" id="dwz-type" class="form-control hide">
                        <?php
                        echo dwzList();
                        ?>
                    </select>
                    <select name="dwz-pattern" id="dwz-pattern" class="form-control hide">
                        <?php
                        echo pattern_list();
                        ?>
                    </select>
                    <div class="center-block" id="dwzdate" style="padding: 15px; border: 1px solid transparent;margin-bottom: 20px;margin-top: 20px;background: rgba(132, 131, 137, 0.67); color: #FFF; font-size:16px;text-align:center;display:none;"></div>
                </div>
            </div>
            <div id="sb_foot"><span>本站禁止违法违规内容的网站生成短网址，如有发现立刻封停！<a href="https://api.btstu.cn/qqtalk/api.php?qq=<?php echo $conf['kf_qq']; ?>" title="联系站长" target="_blank">举报短网址</a><br />© 2019-2020 <?php echo $conf['web_name']; ?> <a href="http://beian.miit.gov.cn" target="_blank" style="text-decoration:none;"><?php echo $conf['icp'] ?></a></span></div>
        </section>
</body>

</html>