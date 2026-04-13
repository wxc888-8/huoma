<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1,user-scalable=no">
    <title><?php echo $conf['web_name'] . '-' . $conf['title']; ?></title>
    <meta name="keywords" content="<?php echo $conf['keywords']; ?>" />
    <meta name="description" content="<?php echo $conf['description']; ?>" />
    <link rel="stylesheet" href="static/redurl/bootstrap.min.css">
    <style type="text/css">
        .row {
            margin-top: 120px
        }

        .page-header {
            margin-bottom: 40px
        }

        .expand-transition {
            -webkit-transition: all .5s ease;
            transition: all .5s ease
        }

        .expand-enter,
        .expand-leave {
            opacity: 0
        }

        @media (max-width:768px) {
            .h3-xs {
                font-size: 20px
            }

            .row-xs {
                margin-top: 80px
            }
        }

        .modal {
            display: block
        }

        .alert.top {
            position: fixed;
            top: 30px;
            margin: 0 auto;
            left: 0;
            right: 0;
            width: 50%;
            z-index: 1050
        }

        @media (max-width:768px) {
            .alert.top-xs {
                width: 80%
            }
        }

        .en-markup-crop-options {
            top: 18px !important;
            left: 50% !important;
            margin-left: -100px !important;
            width: 200px !important;
            border: 2px rgba(255, 255, 255, .38) solid !important;
            border-radius: 4px !important
        }

        .en-markup-crop-options div div:first-of-type {
            margin-left: 0 !important
        }
    </style>
</head>

<body>
    <div id="app" class="container">
        <div class="alert top top-xs alert-dismissible alert-danger expand-transition" style="display:none" id="error-tips">
        </div>
        <div class="row row-xs">
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-10 col-xs-offset-1 col-sm-offset-3 col-md-offset-3 col-lg-offset-3">
                <div class="page-header">
                    <h3 class="text-center h3-xs"><?php echo $conf['web_name']; ?></h3>
                </div>
                <div class="form-group" id="input-wrap">
                    <label class="control-label" for="inputContent">请输入长网址:</label><input type="text" id="inputContent" class="form-control" placeholder="请输入地址...">
                </div>
                <div class="text-right">
                    <div class="input_group_addon btn btn-primary" id="shortify" onclick="checkUrl(document.getElementById('inputContent').value)">缩短网址</div>
                </div>
            </div>
            <div class="modal expand-transition" id="result-wrap" style="display:none">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" onclick="closeWrapper()" aria-hidden="true">×</button>
                            <h4 class="modal-title">生成成功！</h4>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <textarea type="text" rows="1" class="form-control" id="gen_result_url"></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <a target="_blank" id="preViewBtn" href=""><button type="button" class="btn btn-success">点击预览</button></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div>
    </div>
    <script>
        function sendAJAX(hasHttp) {
            var xhr = new XMLHttpRequest();
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4) {
                    if (xhr.status === 200) {
                        var result = JSON.parse(xhr.responseText);
                        if (result.code == 0) {
                            var resultWrap = document.getElementById('result-wrap');
                            resultWrap.style.display = 'block';
                            var preViewBtn = document.getElementById('preViewBtn'),
                                genResultUrl = document.getElementById('gen_result_url');
                            /*location.protocol + "//" + location.host + "/" +*/
                            preViewBtn.setAttribute('href', result.url);
                            var ae_url = result.dwz;
                            //ae_url = ae_url.replace(/<br \/>/g, '\r\n');
                            genResultUrl.value = ae_url;
                        } else {
                            msgAlert('返回错误')
                        }
                    } else {
                        msgAlert('返回错误')
                    }
                }
            };
            var urlVal = document.getElementById('inputContent').value;
            var urlParam = hasHttp ? urlVal : location.protocol + '//' + urlVal;
            xhr.open('GET', 'ajax.php?act=creat1&url=' + encodeURIComponent(urlParam));
            xhr.send();
        }

        function msgAlert(txt, input) {
            var tips = document.getElementById('error-tips');
            tips.style.display = "block";
            tips.innerHTML = txt;
            input && (document.getElementById('input-wrap').classList.add('has-error'))
            setTimeout(function() {
                tips.style.display = 'none';
            }, 3000)
        }

        function closeWrapper() {
            document.getElementById('result-wrap').style.display = 'none'
        }

        function checkUrl(text) {
            var hasHttp = /^([hH][tT]{2}[pP]:\/\/|[hH][tT]{2}[pP][sS]:\/\/)\w+([-.]\w+)*\.\w+([-.]\w+)*/.test(text),
                notHasHttp = /^\w+([-.]\w+)*\.\w+([-.]\w+)*/.test(text);
            if (!hasHttp && !notHasHttp) {
                msgAlert('输入的url有误，请重新输入!', true);
            } else {
                document.getElementById('input-wrap').classList.remove('has-error')
                sendAJAX(hasHttp);
            }
        }
    </script>
</body>

</html>