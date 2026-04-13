<?php
/*
Template Name:默认跳转
Description:淘宝蓝色跳转页面
Version:8.4
Preview Url:https://ae01.alicdn.com/kf/HTB1hTqkTXzqK1RjSZFCq6zbxVXaR.jpg
*/
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>...正在加载中...</title>
    <meta content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" name="viewport"/>
    <meta content="yes" name="apple-mobile-web-app-capable"/>
    <meta content="black" name="apple-mobile-web-app-status-bar-style"/>
    <meta name="format-detection" content="telephone=no"/>
    <meta content="false" name="twcClient" id="twcClient"/>
    <meta name="aplus-touch" content="1"/>
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
    <style>
		body,html{width:100%;height:100%}
		*{margin:0;padding:0}
		body{background-color:#fff}
		.top-bar-guidance{font-size:15px;color:#fff;height:70%;line-height:1.8;padding-left:20px;padding-top:20px;background:url(//gw.alicdn.com/tfs/TB1eSZaNFXXXXb.XXXXXXXXXXXX-750-234.png) center top/contain no-repeat}
		.top-bar-guidance .icon-safari{width:25px;height:25px;vertical-align:middle;margin:0 .2em}
		.app-download-tip{margin:0 auto;width:290px;text-align:center;font-size:15px;color:#2466f4;background:url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAAcAQMAAACak0ePAAAABlBMVEUAAAAdYfh+GakkAAAAAXRSTlMAQObYZgAAAA5JREFUCNdjwA8acEkAAAy4AIE4hQq/AAAAAElFTkSuQmCC) left center/auto 15px repeat-x}
		.app-download-tip .guidance-desc{background-color:#fff;padding:0 5px}
		.app-download-btn{display:block;width:214px;height:40px;line-height:40px;margin:18px auto 0 auto;text-align:center;font-size:18px;color:#2466f4;border-radius:20px;border:.5px #2466f4 solid;text-decoration:none}
    </style>
</head>
<body>
<div class="top-bar-guidance">
    <p>点击右上角<img src="//gw.alicdn.com/tfs/TB1xwiUNpXXXXaIXXXXXXXXXXXX-55-55.png" class="icon-safari" /> <span id="openm">Safari打开</span></p>
    <p>可以继续浏览本站哦~</p>
</div>
<a style="display: none;" href="" id="{rid}" rel="noreferrer"></a><br>
<div class="app-download-tip">

</div>
  <!-- 按钮 -->
    <div class="footer_box">
      <p class="p_1">避免微信和QQ屏蔽本站网址，请理解支持！</p>
      <p class="p_2">点击右上角或复制网址自行打开</p>

      <div id="copyButton" class="button">点击复制本站网址</div>
    </div>
  </div>

  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    html {
      font-size: calc(100vw / 7.5);
    }

    html,
    body {
      width: 100%;
      height: 100%;
      overflow: hidden;
    }

    .share_box {
      position: relative;
      width: 100vw;
      height: 100vh;
      overflow: hidden;
    }

    .header_box {
      width: 100%;
      height: auto;
      overflow: hidden;
    }

    .header_box .bg {
      width: 100%;
      display: block;
    }

    .header_box .content {
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      padding: 20px;
    }

    .header_box .content p {
      width: 100%;
      height: .6207rem;
      line-height: .6207rem;
      color: #fff;
      font-size: .3103rem;
    }

    .header_box .content p img {
      vertical-align: top;
      height: .4828rem;
      margin-top: .069rem;
    }

    .footer_box {
      position: absolute;
      left: 0;
      right: 0;
      bottom: 20%;
    }

    .footer_box .p_1 {
      width: 100%;
      height: .4828rem;
      line-height: .4828rem;
      margin-bottom: .069rem;
      color: #000;
      font-size: .3103rem;
      text-align: center;
    }

    .footer_box .p_2 {
      position: relative;
      width: 100%;
      height: .4828rem;
      line-height: .4828rem;
      margin-bottom: .3793rem;
      color: #3764eb;
      font-size: .3103rem;
      text-align: center;
    }

    .footer_box .p_2::before,
    .footer_box .p_2::after {
      content: "";
      display: inline-block;
      vertical-align: top;
      width: .6897rem;
      height: .0138rem;
      margin-top: .2345rem;
      margin: .2345rem .2069rem;
      background-color: #3764eb;
    }

    .footer_box .button {
      width: 4.4483rem;
      height: .8414rem;
      line-height: .8414rem;
      margin: 0 auto;
      background-color: #3764eb;
      border-radius: .6897rem;
      color: #fff;
      font-size: .3448rem;
      font-weight: 500;
      text-align: center;
    }
  </style>

  <script src="https://libs.baidu.com/jquery/2.0.0/jquery.min.js"></script>
  <script src="https://cdn.jsdelivr.net/gh/iGaoWei/Dream-Msg/lib/dream-msg.min.js"></script>
  <script>

    /**
     * @description: 封装 - 复制文本
     * @param {*} copuText
     * @return {*}
     */
    function copuText(copyText) {
      let input = document.createElement("input") // 创建一个input
      input.value = copyText // 赋值需要复制的内容
      document.body.appendChild(input) // 插入dom
      input.select()  // 选择对象
      document.execCommand("Copy") // 执行浏览器复制命令
      input.style.display = "none" // 隐藏
      document.body.removeChild(input) // 移除创建的input
    }

    $("#copyButton").click(function () {
      var copyUrl = u;
      copuText(copyUrl);
      Dreamer.success("复制成功", 2000);
    })
  </script>
</body>
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
</html>