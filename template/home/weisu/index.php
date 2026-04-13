<html>

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <link href="static/weisu/css/style.css" rel='stylesheet' type='text/css' />
    <title><?php echo $conf['web_name'] . '-' . $conf['title']; ?></title>
    <meta name="keywords" content="<?php echo $conf['keywords']; ?>" />
    <meta name="description" content="<?php echo $conf['description']; ?>" />
    <link href='https://fonts.googleapis.com/css?family=Open+Sans:300italic,400italic,600italic,700italic,400,300,600,700,800' rel='stylesheet' type='text/css'>
    <script src="https://cdn.bootcss.com/jquery/2.1.4/jquery.min.js"></script>
    <script src="https://cdn.bootcss.com/jquery.qrcode/1.0/jquery.qrcode.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#start').click(function() {
                var url = $("input[id='longurl']").val();
                url = url.replace(/\+/g, "%2B");
                url = url.replace(/\&/g, "%26");
                $.ajax({
                    type: "post",
                    url: "ajax.php?act=creat1",
                    dataType: "json",
                    data: {
                        url: url
                    },
                    async: true,
                    success: function(a) {
                        var strJson = JSON.stringify(a)
                        var data = $.parseJSON(strJson);
                        console.log(data);
                        if (data.dwz) {
                            $('#dwzdate').html(data.dwz);
                            $('#qrcode').html('<img class="qrimg" width="200px" src="includes/libs/qrcode.php?size=300&text=' + data.dwz + '" />');
                        } else {
                            alert(data.msg);
                        }
                    },
                    error: function(a) {
                        alert("失败！！");
                    }
                });
            });
        });
    </script>
</head>

<body style="background-image: url(https://ae01.alicdn.com/kf/Hc1fc384692604fc8b251683d10c7a9a9W.jpg);background-attachment: fixed;background-repeat: no-repeat;background-size: cover;-moz-background-size: cover;">
    <h1></h1>
    <div class="app-location">
        <h2><?php echo $conf['web_name']; ?></h2>
        <input type="text" class="text" id="longurl" placeholder="http://<?php echo $conf['domain']; ?>">
        <div class="submit"><input type="button" id="start" value="生成短链接"></div>
        <div class="clear"></div>
        <p id="dwzdate">您还没有生成数据</p>
        <hr>
        <p id="qrcode"></p>
    </div>
</body>

</html>