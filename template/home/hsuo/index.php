<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title><?php echo $conf['web_name'] . '-' . $conf['title']; ?></title>
    <meta name="description" content="<?php echo $conf['description']; ?>">
    <meta name="keywords" content="<?php echo $conf['keywords']; ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, user-scalable=no">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <script src='./static/hsuo/js/qrcode.min.js'></script>
    <script src="./static/hsuo/js/main.js" data-turbolinks-track="reload"></script>
    <link rel="stylesheet" media="screen" href="./static/hsuo/css/style.css" data-turbolinks-track="reload" />
    <link rel="stylesheet" href="./static/hsuo/css/font_1206364_f2wove1olvg.css">
    <link rel="shortcut icon" href="./static/picture/favicon.ico">
</head>

<body>
    <nav id='topnav' class="navbar navbar-expand-lg bg-white fixed-top navbar-trans">
        <div class="container">
            <h1>
                <a class="navbar-brand" href="/">
                    <img style="max-width: 200px; max-height: 50px" src="./static/picture/logo.png" alt="图片加载失败请刷新重试" />
                </a>
            </h1>
            <ul id='navBtns'>
                <!-- <li><a href="/">首页</a></li>
                <li><a href="#">导航1</a></li>
                <li><a href="#">导航2</a></li> -->
                <?php
                if ($islogin2 == 1) {
                    echo '<li class="short-li nav-item"><a href="/user">用户中心</a></li>';
                } else {
                    echo '<li class="long-li nav-item"><a href="/user/reg.php" id="btn-register">免费注册</a></li>
                    <li class="short-li nav-item"><a href="/user/login.php">登录</a></li>';
                }
                ?>
            </ul>
            <div class="navBtn" data-controller="toggle-menu">
                <div data-target='toggle-menu.openmenu' data-action='click->toggle-menu#open'>
                    <img class="menu-btn" src="./static/hsuo/picture/menu-2ee3055d219369deb5b0d46e4c3c93c0.png" alt="图片加载失败请刷新重试" />
                </div>
                <div data-target='toggle-menu.closemenu' data-action='click->toggle-menu#close'>
                    <img class="close-btn" src="./static/hsuo/picture/close_menu-68cccce5a475fce5ae693ef84203f47a.png" alt="图片加载失败请刷新重试" />
                </div>
                <ul class="menu-list">
                    <!-- <li><a href="#">手机导航</a></li> -->
                    <?php
                    if ($islogin2 == 1) {
                        echo '<li><a href="/user">用户中心</a></li>';
                    } else {
                        echo '<li><a href="/user/reg.php" class="resigter">免费注册</a></li>
                        <li><a href="/user/login.php">登录</a></li>';
                    }
                    ?>
                </ul>
            </div>
        </div>
    </nav>
    <div id="body-head">
        <div class="container">
            <h2>简单易用的短链接营销推广工具</h2>
            <div class="create-cube-err-tip">请输入 http:// 或 https:// 开头的链接</div>
            <div id='dashboard-url-box'>
                <input type="text" name='url' id="url" placeholder="请输入 http:// 或 https:// 开头的网址"><button id="dwz-btn">生成短链</button>

                <div id="temp-qr-info" class='d-none' data-target="box.cubeList">
                    <div id='q-details'>
                        <div class="pc-details-des">想要随时查看统计数据以及更多高级功能?</div>
                        <div class="mobile-details-des">统计数据以及更多功能</div>
                        <div class="q-details-r">马上<a href="/user/reg.php"> 登录/注册</a></div>
                    </div>
                    <div class="qr-list" id="dwz-list">
                    </div>
                    <div id='anonymous-warning'>
                        <div>注：匿名链接仅保存7天, <a href='/user/login.php'>登录</a>生成无限制链接 / 查看</div>
                    </div>
                    <div class="fixed-wrap-code">
                        <div class="fixed-wrap-content">
                            <div id='mobileQrCode'></div>
                            <div class="fixed-wrap-content-text">长按保存图片</div>
                        </div>
                        <a href="javascript:void(0)" data-target='box.closeCodeBtn' data-action='click->box#closeCode'>
                            <img class="close-fixed" src="./static/hsuo/picture/close-fixed-4e0067bd5f93e8d69d74090d5abadb64.png" alt="图片加载失败请刷新重试" />
                        </a>
                    </div>
                </div>
            </div>
            <div class="slider-btn">下滑查看更多</div>
            <img style="max-width: 16px; max-height: 16px" src="./static/hsuo/picture/slider_down-6bda93f6ea9ccd3237a288b9c05521ea.png" alt="图片加载失败请刷新重试" />
        </div>
    </div>
    <div id="images-screen">
        <div class="container">
            <div id="pic-1" data-aos="flip-up">
                <div class="row">
                    <div class="col-12 col-sm-6 order-1 order-sm-0">
                        <img class="img" src="./static/hsuo/picture/f-1-3221231ad9c4a389ecdc445b9bb26f3f.png" alt="图片加载失败请刷新重试" />
                    </div>
                    <div class="col-12 col-sm-6 order-0 order-sm-1">
                        <div class='center-box'>
                            <h2>便于线上线下分发</h2>
                            <p>压缩长连接, 不再让链接占据大量有用篇幅。<br />使用短链/二维码分发，便于分析线上/线下的用户数据。</p>
                        </div>
                    </div>
                </div>
            </div>
            <div id="pic-2" data-aos="flip-up">
                <div class="row">
                    <div class="col-12 col-sm-6">
                        <div class='center-box'>
                            <h2>支持多类型分发</h2>
                            <p>根据地域/时间/设备的特性来跳转不同的链接，让你的用户看到最感兴趣的链接，从而流量收益最大化。<br />不需要开发成本就可以实现A/B Test，挑选出最受用户欢迎的方案。
                            </p>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6">
                        <img class="img" src="./static/hsuo/picture/f-2-6759c4fe671ad14f5ff58c4140607364.png" alt="图片加载失败请刷新重试" />
                    </div>
                </div>
            </div>
            <div id="pic-3" data-aos="flip-up">
                <div class="row">
                    <div class="col-12 col-sm-6 order-1 order-sm-0">
                        <img class="img" src="./static/hsuo/picture/f-3-2dc1ca612c48d9fa322683607fa3e023.png" alt="图片加载失败请刷新重试" />
                    </div>
                    <div class="col-12 col-sm-6 order-0 order-sm-1">
                        <div class='center-box'>
                            <h2>多维度数据统计</h2>
                            <p>支持查看短链/二维码的访问量PV，访客量UV，IP，来源，地域，设备等多维度数据， 便于绘制用户画像让后续的分发更有价值。<br />如果访问量骤增，不确定渠道转化还是刷量，可通过查看热门IP来判断，<br />若是刷量，可过滤该高危IP移除虚假数据。真实的数据才能带来真实的用户。</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="invite-register">
                <h1>开启高效分发新时代</h1>
                <a href="/user/reg.php">马上免费注册</a>
            </div>
            <div class="invite-register">
                <h1>免责申明</h1>
                <br>
                <p>本站禁止赌博、色情、暴力、诈骗等违法违规内容、支付页面或未经备案的网站生成的短网址，如有发现立即封停！</p>
                <p>本站仅提供待统计的短网址服务，短网址由用户生成，所有跳转的网站内容均与本站无关，访客请自行记录并核对跳转后的网站，谨防受骗！</p>
            </div>
        </div>
    </div>

    <style>
        .alink {
            text-align: center;
            padding-top: 30px;
            margin-bottom: -20px;
        }

        .alink a {
            color: #999999;
        }

        @media screen and (max-width: 479px) {
            #footer {
                height: 87px;
            }
        }
    </style>
    <div id='footer'>
        <!-- <div class='container alink'>
            <p>友情链接：<a href="#" target="_blank">友情链接1</a>｜<a href="#" target="_blank">友情链接2</a></p>
        </div> -->
        <div id='footer-content' class='container'>
            <div id='copyright'>
                Copyright © 2019 - 2020 <span class="des"><?php echo $conf['web_name']; ?></span>
            </div>
            <div class="record_num">
                <a href="http://beian.miit.gov.cn/" target="_blank" rel="noopener noreferrer">
                    <?php echo $conf['icp'] ?></a>
            </div>
            <div id='footer-links'>
                <ul>
                    <li>
                        <a href="https://api.btstu.cn/qqtalk/api.php?qq=<?php echo $conf['kf_qq'] ?>">联系我们</a>
                    </li>
                    <li>
                        <a href="/">条款</a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</body>
<script src="https://lib.baomitu.com/jquery/1.10.2/jquery.min.js"></script>
<script src="https://lib.baomitu.com/clipboard.js/2.0.6/clipboard.min.js"></script>
<script>
    $(document).ready(function() {

        $('#dwz-btn').click(function() {
            var url = $("input[id='url']").val();
            url = url.replace(/\+/g, "%2B");
            url = url.replace(/\&/g, "%26");
            $.ajax({
                type: "post",
                url: "ajax.php?act=creat1",
                dataType: "json",
                data: 'url=' + url,
                success: function(obj) {
                    if (obj.code == 0) {
                        $('#temp-qr-info').removeClass('d-none');
                        $("#dwz-list").append(
                            '<div class="item"><div class="qr-code"><div class="qrcode-img"><a href="includes/libs/qrcode.php?size=300&text=' + obj.dwz + '" target="_blank"><img class="code" width="54px" src="includes/libs/qrcode.php?size=300&text=' + obj.dwz + '" /></a></div></div><div class="qr-code-info"><div class="complex"> <a target="_blank" href="' + obj.dwz + '">' + obj.dwz + ' </a><span style="padding-left: 13px;"> <a class="cube-copy clipboard" href="javascript:void(0)" onclick="msg()" data-clipboard-text="' + obj.dwz + '">复制 </a><a class="mobile-cube-copy clipboard" href="javascript:void(0)" onclick="msg()" data-clipboard-text="' + obj.dwz + '"><i class="iconfont iconfuzhi"></i></a> </span> </div> <div class="original-url">' + url + '</div></div><div class="cube-statis"><div class="align-gutter"></div> <a href="' + obj.data + '" target="_blank"> 0 <i class="iconfont iconGroup5"></i> </a></div></div>'
                        );
                        new ClipboardJS('.clipboard');
                    } else {
                        alert(obj.msg);
                    }
                },
                error: function(a) {}
            });
        });
    });

    function msg() {
        alert('已复制')
    }
</script>

</html>