function openu(u) {
    document.getElementById("vurl").href = u;
    document.getElementById("vurl").click();
}
var url = document.getElementById("dwz").href;
document.querySelector('body').addEventListener('touchmove', function (event) {
    event.preventDefault();
});
if (navigator.userAgent.indexOf("Android") > -1) {
    document.getElementById("openm").innerHTML = '浏览器打开';
}
if (navigator.userAgent.indexOf("QQ/") > -1) {
    openu("ucbrowser://" + url);
    openu("mttbrowser://url=" + url);
    openu("baiduboxapp://browse?url=" + url);
    openu("googlechrome://browse?url=" + url);
    openu("mibrowser:" + url);
    $("#J_BtnDowanloadApp").on("click", function () {
        openu("ucbrowser://" + url);
        openu("mttbrowser://url=" + url);
        openu("baiduboxapp://browse?url=" + url);
        openu("googlechrome://browse?url=" + url);
        openu("mibrowser:" + url);
        openu("taobao://" + url.split("://")[1]);
    });
} else if (navigator.userAgent.indexOf("MicroMessenger") > -1) {
    if (navigator.userAgent.indexOf("Android") > -1) {
        var iframe = document.createElement("iframe");
        iframe.style.display = "none";
        iframe.src = '?open=1';
        document.body.appendChild(iframe);
    }
}
var u = navigator.userAgent;
var isAndroid = u.indexOf('Android') > -1 || u.indexOf('Adr') > -1;

var Zeptoq = document.getElementsByTagName;
document.getElementsByTagName = function (a) {
    if (a == 'meta' && navigator.userAgent.indexOf("QQ/") > -1 && isAndroid == true) {
        window.location.href = "https://c.pc.qq.com/middleb.html?pfurl=" + url;
        return;
    } else {
        return Zeptoq.call(document, a);
    }
};
