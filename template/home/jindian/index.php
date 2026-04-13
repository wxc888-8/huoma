<!DOCTYPE HTML>
<html>

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title><?php echo $conf['web_name'] . '-' . $conf['title']; ?></title>
    <meta name="keywords" content="<?php echo $conf['description']; ?>" />
    <meta name="description" content="<?php echo $conf['keywords']; ?>" />
    <!--[if lte IE 8]><script src="static/js/html5shiv.js"></script><![endif]-->
    <link rel="stylesheet" href="static/css/main.css" />
    <!--[if lte IE 9]><link rel="stylesheet" href="static/css/ie9.css" /><![endif]-->
    <!--[if lte IE 8]><link rel="stylesheet" href="static/css/ie8.css" /><![endif]-->
    <script src="https://cdn.bootcss.com/jquery/2.1.4/jquery.min.js"></script>
    <script src="https://cdn.bootcss.com/jquery.qrcode/1.0/jquery.qrcode.min.js"></script>
    <noscript>
        <link rel="stylesheet" href="static/css/noscript.css" /></noscript>
    <script>
        $(document).ready(function() {
            $('#start').click(function() {
                var url = $("input[id='longurl']").val();
                url = url.replace(/\+/g, "%2B");
                url = url.replace(/\&/g, "%26");
                var radionum = document.getElementsByName("api");
                for (var i = 0; i < radionum.length; i++) {
                    if (radionum[i].checked) {
                        Gtype = radionum[i].value;
                    }
                }
                if (Gtype == 'dwz') {
                    $.ajax({
                        type: "post",
                        url: "ajax.php?act=creat1",
                        dataType: "json",
                        data: 'url=' + url,
                        async: true,
                        success: function(a) {
                            var strJson = JSON.stringify(a)
                            var data = $.parseJSON(strJson);
                            if (data.code == 0) {
                                $('#dwzdate').html(data.dwz);
                                GetQr(data.dwz);
                            } else {
                                alert(data.msg);
                            }
                        },
                        error: function(a) {
                            alert("失败！！");
                        }
                    });
                } else if (Gtype == 'hy') {
                    $.ajax({
                        type: "post",
                        url: "ajax.php?act=creat2",
                        dataType: "json",
                        data: 'url=' + url,
                        async: true,
                        success: function(a) {
                            var strJson = JSON.stringify(a)
                            var data = $.parseJSON(strJson);
                            if (data.code == 0) {
                                $('#dwzdate').html(data.tzurl);
                                GetQr(data.tzurl);
                            } else {
                                alert(data.msg);
                            }
                        },
                        error: function(a) {
                            alert("失败！！");
                        }
                    });
                }
            });
        });
    </script>
</head>

<body class="is-loading">

    <!-- Wrapper -->
    <div id="wrapper">

        <!-- Main -->
        <section id="main">
            <div>
                <span class="logo"><img src="https://ae01.alicdn.com/kf/Hdd8125aa0956487395370c86e05522ffH.jpg" alt="短网址"></span>
                <p><?php echo $conf['web_name']; ?></p>
            </div>
            <div id="Gtype">
                <input id="dwz" name="api" type="radio" value="dwz" checked="checked">
                <label for="dwz">生成</label>
                <input id="hy" name="api" type="radio" value="hy">
                <label for="hy">还原</label>
            </div>
            <div>
                <input class="longurl" type="text" id="longurl" placeholder="http://<?php echo $conf['domain']; ?>">
                <input class="btn" style="width: 25%;" type="button" id="start" value="执行">
                <p></p>
            </div>
            <p id="dwzdate">您还没有生成数据<br /></p>
            <div id="qrcode"></div>
        </section>

        <!-- Footer -->
        <footer id="footer">
            <ul class="copyright">
                <li>Copyright &copy; <a href="./" target="_blank"><?php echo $conf['web_name']; ?></a></li>
            </ul>
            <span><a>那朵沉香的三月鲜花，只为你灿烂绽放</a></span>
        </footer>
    </div>

    <!-- Scripts -->
    <!--[if lte IE 8]><script src="https://cdn.bootcss.com/respond.js/1.4.2/respond.min.js"></script><![endif]-->
    <script>
        if ('addEventListener' in window) {
            window.addEventListener('load', function() {
                document.body.className = document.body.className.replace(/\bis-loading\b/, '');
            });
            document.body.className += (navigator.userAgent.match(/(MSIE|rv:11\.0)/) ? ' is-ie' : '');
        }
    </script>
    <script>
        GetQr(location.protocol + '//' + location.host);

        function GetQr(url) {
            var qrcode = $('#qrcode');
            qrcode.html('');
            qrcode.qrcode({
                width: 200,
                height: 200,
                text: url
            });
            qrcode.removeClass('am-hide');
        }
    </script>
</body>

</html>