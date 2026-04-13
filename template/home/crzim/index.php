<!DOCTYPE HTML>
<html>

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <title><?php echo $conf['web_name'] . '-' . $conf['title']; ?></title>
    <meta name="description" content="<?php echo $conf['description']; ?>" />
    <meta name="keyword" content="<?php echo $conf['keywords']; ?>" />
    <style type="text/css">
        * {
            margin: 0;
            padding: 0;
        }

        html,
        body,
        input,
        text,
        textarea {
            outline: none;
            font-family: 'Arial', 'Microsoft YaHei', '黑体', '宋体', sans-serif;
            font-size: 12px;
        }

        html,
        body {
            background: #fff;
        }

        a {
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }

        .wrap {
            text-align: center;
            overflow: hidden;
        }

        .wrap .meta {
            margin: 160px 0 0 0;
            opacity: 0;
            transform: translateY(-150px);
            transition: .5s all ease;
        }

        .on .wrap .meta {
            opacity: 1;
            transform: translateY(0);
        }

        .wrap .meta .title {
            line-height: 1em;
            color: #ff4665;
            font-size: 42px;
            text-transform: uppercase;
        }

        .wrap .meta .description {
            margin: 10px 0 0 0;
            line-height: 1em;
            color: #7e7e7e;
            font-size: 16px;
            font-weight: normal;
        }

        .wrap .link-area {
            margin: 50px 0 0 0;
            opacity: 0;
            transition: .5s opacity ease;
        }

        .on .wrap .link-area {
            opacity: 1;
        }

        .wrap .link-area input {
            display: inline-block;
            vertical-align: middle;
        }

        .wrap .link-area #url {
            width: 320px;
            height: 32px;
            line-height: 32px;
            padding: 0 10px;
            border: 3px solid #bdc3c7;
            border-radius: 5px;
            color: #333;
        }

        .wrap .link-area #url.focus,
        .wrap .link-area #url:focus {
            border-color: #ff4665;
            transition: .2s border ease;
        }

        .wrap .link-area #submit {
            width: 90px;
            height: 38px;
            margin: 0 0 0 5px;
            background: #ff4665;
            border-radius: 5px;
            color: #fff;
            border: none;
            cursor: pointer;
            transition: .2s opacity ease;
        }

        .wrap .link-area #submit:hover {
            opacity: .75;
        }

        .wrap .link-area #submit:active {
            opacity: .9;
        }

        .wrap .footer {
            width: 100%;
            bottom: 80px;
            left: 0;
            position: absolute;
            color: #7e7e7e;
        }

        .wrap .footer a {
            color: #ff4665;
        }
    </style>
</head>

<body>
    <div class="wrap">
        <div class="meta">
            <h2 class="title"><?php echo $conf['web_name']; ?></h2>
            <h3 class="description"><?php echo $conf['description']; ?></h3>
        </div>
        <div class="link-area">
            <input id="url" type="text" placeholder="http(s)://" />
            <input id="submit" type="button" value="Generate" onclick="APP.dwz.setUrl(this)" />
        </div>
        <div class="footer">
            Copyright &copy; <a href="http://beian.miit.gov.cn" target="_blank"><?php echo $conf['web_name']; ?></a>.
        </div>
    </div>
</body>
<!-- JS -->
<script type="text/javascript">
    var APP = (function() {

        var dwz = {

                // 生成短地址
                setUrl: function(self) {
                    var urlEl = document.getElementById('url'),
                        tips = 'https://',
                        request = {
                            url: urlEl.value
                        };
                    dwz.getJson('ajax.php?act=creat1', true, dwz.build_query(request), function(res) {
                        if (res.code == 0) {
                            urlEl.className = 'focus';
                            urlEl.value = res.dwz;
                        } else {
                            urlEl.className = '';
                            urlEl.value = '';
                            urlEl.setAttribute('placeholder', res.msg);
                            setTimeout(function() {
                                urlEl.setAttribute('placeholder', tips);
                            }, 2000);
                        }
                    });
                },
                build_query: function(obj) {
                    var str = [];
                    for (var p in obj) {
                        str.push(encodeURIComponent(p) + "=" + encodeURIComponent(obj[p]));
                    }
                    return str.join("&");
                },
                // 获取 JSON 数据
                getJson: function(url, post, data, callback) {
                    var xhr = new XMLHttpRequest(),
                        type = (post) ? 'POST' : 'GET';
                    xhr.onreadystatechange = function() {
                        if (xhr.readyState == 4 && xhr.status == 200) {
                            var json = JSON.parse(xhr.responseText);
                            callback(json);
                        } else if (xhr.readyState == 4) {
                            callback(false);
                        }
                    }
                    xhr.open(type, url, true);
                    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                    xhr.send(data);
                }

            },

            init = function() {
                setTimeout(function() {
                    var el = document.getElementsByTagName('html')[0];
                    el.className = 'on';
                }, 10);
            };

        return {
            dwz: dwz,
            init: init
        }

    })();
    document.addEventListener('DOMContentLoaded', function() {
        APP.init();
    })
</script>

</html>