var dwz = document.getElementById('dwz').innerHTML;
var url = document.getElementById('url').innerHTML;
var u = navigator.userAgent;
var isAndroid = u.indexOf('Android') > -1 || u.indexOf('Adr') > -1;
if (navigator.userAgent.indexOf("QQ/") > -1 && isAndroid == true) {
    var Zeptoq = document.getElementsByTagName;
    document.getElementsByTagName = function (a) {
        if (a == 'meta') {
            window.location.href = "https://c.pc.qq.com/middleb.html?pfurl=" + dwz;
            return;
        } else {
            return Zeptoq.call(document, a);
        }
    };
}
$('#Zl').html('<iframe width="100%" id="rid" src="' + url + '" frameborder="0"></iframe>');
$(document).ready(function () {
    $(window).resize(function () {
        fix_height();
    }).resize();
});
if (! /*@aijquery@*/ 0) { //如果不是IE，IE的条件注释  
    $("#rid").onload = function () {
        fix_height();
        $("#rid").contentWindow.focus();
        $("#rid").load(function () {
            $('body').css('background', '');
        });
    };
} else {
    $("#rid").onreadystatechange = function () { //IE下的节点都有onreadystatechange这个事件
        if ($("#rid").readyState == "complete") {
            fix_height();
            $("#rid").contentWindow.focus();
            $("#rid").load(function () {
                $('body').css('background', '');
            });
        }
    };
}


function fix_height() {
    $("#rid").attr("height", (($(window).height()) - 5) + "px");
}