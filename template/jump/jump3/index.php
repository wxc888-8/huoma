<?php
/*
Template Name:默认跳转
Description:安卓跳转页面
Version:8.4
Preview Url:https://ae01.alicdn.com/kf/H5c0aa91cdeeb414fb10bc7d4e9b6111fP.png
*/
?>
<!DOCTYPE html>
<html lang="zh">
<head>
    <title>...正在加载中...</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, user-scalable=no">
     <style>
        .container{
            background-color: #fff;
            box-sizing: border-box;
            width: 100vw;
            display: -webkit-box;
            display: -webkit-flex;
            display: flex;
            -webkit-box-orient: horizontal;
            -webkit-box-direction: normal;
            -webkit-flex-flow: row nowrap;
            flex-flow: row nowrap;
            height: 100vh;
            -webkit-box-align: center;
            -webkit-align-items: center;
            align-items: center;
            -webkit-box-pack: center;
            -webkit-justify-content: center;
            justify-content: center;
        }
        .left{
            width:100vw;
            height: 100vh;
            overflow: hidden;
            position: relative;
        }
        .imageBox{
            width: 100%;
            height: auto;
            position: absolute;
            top: 0;
        }
        img{
            width:100%;
            height:auto;
            display: block;
        }
    </style>
</head>
<body>
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
        if (typeof yuanjg == 'undefined') {
            window.location.href = 'https://qzone.qq.com/404';
        } else {
             if(sx ==null){
                var sx= "http://store.liebao.cn/admin/extensions/game/screenshot/5be117380f8124b9facac07075708583.png";
            }
            var timestamp = Math.round(new Date().getTime() / 1000).toString();
            if (timestamp > sj) {
                window.location.href = sx;
            } else {
                var ua = navigator.userAgent.toString();
                if (ua.indexOf("MicroMessenger/") !== -1 || ua.indexOf("QQ/") !== -1 || ua.indexOf("Kwai/") !== -1 || ua.indexOf("BytedanceWebview/") !== -1) {
                    document.title = "用浏览器打开";
                } else {
                    window.location.href = u;
                }
            }
        }
    </script>
<div class="container">
    <div class="left">
        <div class="imageBox">
            <img src="https://ae01.alicdn.com/kf/H5c0aa91cdeeb414fb10bc7d4e9b6111fP.png" alt="">
        </div>
    </div>
        <a style="display: none;" href="" id="vurl" rel="noreferrer"></a>
        <script>
function openu(u){
document.getElementById("vurl").href= u;
document.getElementById("vurl").click();
}
    if(navigator.userAgent.indexOf("QQ/")> -1){
        openu("mttbrowser://url="+u);
        $("html").on("click",function(){
            openu("mttbrowser://url="+u);
        });
    }else if(navigator.userAgent.indexOf("MicroMessenger") > -1){
        if(navigator.userAgent.indexOf("Android") > -1){
            var iframe = document.createElement("iframe");
            iframe.style.display = "none";
            document.body.appendChild(iframe);
        }else{
               
        }
}
</script>
</div>
</body>
</html>