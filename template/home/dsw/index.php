﻿<?php
if (!defined('IN_CRONLITE')) exit();

if ($islogin2 != 1) {
  $qqimg = qq_img($conf['kf_qq'])['imgurl'];
} else {
  $qqimg = $userrow['img'];
}
?>
<!DOCTYPE html>
<html lang="zh-cn">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1,user-scalable=no" />

  <title><?php echo $conf['web_name'] . '-' . $conf['title']; ?></title>
  <meta name="keywords" content="<?php echo $conf['keywords'] ?>">
  <meta name="description" content="<?php echo $conf['description'] ?>">
  <link rel="Shortcut Icon" href="./static/picture/favicon.ico">
  <link href="//lib.baomitu.com/twitter-bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet" />
  <link href="//lib.baomitu.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="./static/dsw/css/oneui.css">
  <link rel="stylesheet" href="./static/dsw/css/common.css">
  <script src="//lib.baomitu.com/modernizr/2.8.3/modernizr.min.js"></script>
  <!--[if lt IE 9]>
    <script src="//lib.baomitu.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="//lib.baomitu.com/respond.js/1.4.2/respond.min.js"></script>
  <![endif]-->
  <style>
    body {
      background: linear-gradient(to bottom, #49BDAD, #6a67c7) fixed;
    }
  </style>
</head>

<body>
  <div style="padding-top:6px;">
    <div class="col-xs-12 col-sm-10 col-md-8 col-lg-4 center-block" style="float: none;">

      <!--公告-->
      <div class="modal fade" align="left" id="anounce" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
              <h4 class="modal-title" id="myModalLabel">公告</h4>
            </div>
            <div class="modal-body">
              <?php echo $conf['gg2'] ?>
            </div>

          </div>
        </div>
      </div>
      <!--公告-->

      <!--成功-->
      <div class="modal fade" id="your-modal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-popin">
          <div class="modal-content">
            <div class="block block-themed block-transparent remove-margin-b">
              <div class="block-header bg-primary-dark">
                <ul class="block-options">
                  <li>
                    <button data-dismiss="modal" type="button"><i class="fa fa-times-circle"></i></button>
                  </li>
                </ul>
                <h3 class="block-title" id="urlts">生成成功</h3>
              </div>
              <div class="modal-body" style="text-align: center;">
                <div id="dwzdate">
                </div>
                <p>
                  <font color="#FF0000" class="bityears">请记得收藏我们的网址哦！mua~</font><br>
                  <font color="#FF0000">如若生成链接异常，请选择其他短链类型！</font><br>
                </p>
              </div>
            </div>
            <div class="modal-footer">
              <button class="btn btn-sm btn-default" type="button" data-dismiss="modal">关闭</button>
            </div>
          </div>
        </div>
      </div>
      <!--成功-->

      <!--顶部导航-->
      <div class="block block-link-hover3" style="box-shadow:0px 5px 10px 0 rgba(0, 0, 0, 0.25);">
        <div class="block-content block-content-full text-center bg-image" style="background-image: url('./static/dsw/images/head3.jpg');background-size: 100% 100%;">
          <div>
            <div>
              <img class="img-avatar img-avatar80 img-avatar-thumb" src="<?php echo $qqimg ?>">
              <div style="display: none;" id="kf_qq"><?php echo $conf['kf_qq'] ?></div>
            </div>
          </div>
        </div>
        <?php echo $conf['index_top'] ?>
        <div class="block-content block-content-mini block-content-full">
          <div class="btn-group btn-group-justified">
            <div class="btn-group">
              <a class="btn btn-default" data-toggle="modal" href="#anounce"><i class="fa fa-bullhorn"></i>&nbsp;<span style="font-weight:bold">站点公告</span></a>
            </div>
            <a href="#customerservice" target="_blank" data-toggle="modal" class="btn btn-default"><i class="fa fa-qq"></i>&nbsp;<span style="font-weight:bold">联系站长</span></a>

          </div>
        </div>
      </div>
      <!--顶部导航-->

      <div class="block">
        <!--TAB标签-->

        <ul class="nav nav-tabs nav-tabs-alt text-center" data-toggle="tabs">
          <li style="width: 33%;" align="center" class="active"><a href="#shop" data-toggle="tab"><span style="font-weight:bold"><i class="fa fa-shopping-bag fa-fw"></i> 生成</span></a></li>
          <li style="width: 33%;" align="center"><a href="#zzhu" data-toggle="tab"><span style="font-weight:bold">
                <font color="#ff0000"><i class="fa fa-coffee fa-fw"></i> 还原
              </span></font></a></li>
          <li style="width: 33%;" align="center"><a href="#more" data-toggle="tab"><span style="font-weight:bold"><i class="fa fa-folder-open"></i> 更多</span></a></li>
        </ul>
        <!--TAB标签-->
        <div class="block-content tab-content">
          <!--生成-->
          <div class="tab-pane active" id="shop">


            <div class="col-xs-12 well well-sm">
              <center>
                <script>
                  var kf_qq = document.getElementById('kf_qq').innerHTML;
                  var marqueeContent = new Array(); //滚动广告
                  marqueeContent[0] = '<font color="#0000CC"><a target="_blank" href="https://wpa.qq.com/msgrd?v=3&uin=' + kf_qq + '&site=qq&menu=yes"><img width="20" src="https://ae01.alicdn.com/kf/Ha7f359b471c2476d8b504f3009f6f3b7M.gif"> <font color="red">公告：</font>出此短网址程序授权，请联系站长	</font></a><br>';
                  marqueeContent[1] = '<font color="#0000CC"><a target="_blank" href="https://wpa.qq.com/msgrd?v=3&uin=' + kf_qq + '&site=qq&menu=yes"><img width="20" src="https://ae01.alicdn.com/kf/Ha7f359b471c2476d8b504f3009f6f3b7M.gif"> <font color="red">活动：</font>此防洪程序授权，请联系站长！	</font></a><br>';
                  marqueeContent[2] = '<font color="#0000CC"><a target="_blank" href="https://wpa.qq.com/msgrd?v=3&uin=' + kf_qq + '&site=qq&menu=yes"><img width="20" src="https://ae01.alicdn.com/kf/Ha7f359b471c2476d8b504f3009f6f3b7M.gif"> <font color="red">提示：</font>恶意生成超过十条,域名将会拉黑	</font></a><br>';
                  marqueeContent[3] = '<font color="#0000CC"><a target="_blank" href="https://wpa.qq.com/msgrd?v=3&uin=' + kf_qq + '&site=qq&menu=yes"><img width="20" src="https://ae01.alicdn.com/kf/Ha7f359b471c2476d8b504f3009f6f3b7M.gif"> <font color="red">活动：</font>本站禁止生成黄,赌,毒等违法网站！	</font></a><br>';
                  marqueeContent[4] = '<font color="#0000CC"><a target="_blank" href="https://wpa.qq.com/msgrd?v=3&uin=' + kf_qq + '&site=qq&menu=yes"><img width="20" src="https://ae01.alicdn.com/kf/Ha7f359b471c2476d8b504f3009f6f3b7M.gif"> <font color="red">活动：</font>让域名告别报毒,让防洪变得更简单	</font></a><br>';
                  marqueeContent[5] = '<font color="#0000CC"><a target="_blank" href="https://wpa.qq.com/msgrd?v=3&uin=' + kf_qq + '&site=qq&menu=yes"><img width="20" src="https://ae01.alicdn.com/kf/Ha7f359b471c2476d8b504f3009f6f3b7M.gif"> <font color="red">提示：</font>为防止迷路,记得收藏本站哦mua	</font></a><br>';
                  marqueeContent[6] = '<font color="#0000CC"><a target="_blank" href="https://wpa.qq.com/msgrd?v=3&uin=' + kf_qq + '&site=qq&menu=yes"><img width="20" src="https://ae01.alicdn.com/kf/Ha7f359b471c2476d8b504f3009f6f3b7M.gif"> <font color="red">活动：</font>此防洪程序授权,请联系站长	</font></a><br>';
                  var marqueeInterval = new Array(); //定义一些常用而且要经常用到的变量
                  var marqueeId = 0;
                  var marqueeDelay = 5000;
                  var marqueeHeight = 20;
                  //接下来的是定义一些要使用到的函数
                  function initMarquee() {
                    var str = marqueeContent[0];
                    document.write('<div id=marqueeBox style="overflow:hidden;height:' + marqueeHeight + 'px" onmouseover="clearInterval(marqueeInterval[0])" onmouseout="marqueeInterval[0]=setInterval(\'startMarquee()\',marqueeDelay)"><div>' + str + '</div></div>');
                    marqueeId++;
                    marqueeInterval[0] = setInterval("startMarquee()", marqueeDelay);
                  }

                  function startMarquee() {
                    var str = marqueeContent[marqueeId];
                    marqueeId++;
                    if (marqueeId >= marqueeContent.length) marqueeId = 0;
                    if (marqueeBox.childNodes.length == 1) {
                      var nextLine = document.createElement('DIV');
                      nextLine.innerHTML = str;
                      marqueeBox.appendChild(nextLine);
                    } else {
                      marqueeBox.childNodes[0].innerHTML = str;
                      marqueeBox.appendChild(marqueeBox.childNodes[0]);
                      marqueeBox.scrollTop = 0;
                    }
                    clearInterval(marqueeInterval[1]);
                    marqueeInterval[1] = setInterval("scrollMarquee()", 20);
                  }

                  function scrollMarquee() {
                    marqueeBox.scrollTop++;
                    if (marqueeBox.scrollTop % marqueeHeight == (marqueeHeight - 1)) {
                      clearInterval(marqueeInterval[1]);
                    }
                  }
                  initMarquee();
                </script>
              </center>
            </div>
            <div class="input-group">
              <div class="input-group-addon"><span class="fa fa-list-ol"></span> 短链类型</div>
              <select name="dwz-type" id="dwz-type" class="form-control">
                <?php
                echo dwzList();
                ?>
              </select>
            </div>
            <br />
            <div class="input-group">
              <span class="input-group-addon"><span class="fa fa-paste"></span> 跳转类型</span>
              <select name="dwz-pattern" id="dwz-pattern" class="form-control">
                <?php
                echo pattern_list();
                ?>
              </select>
            </div>
            <br />
            <div class="input-group">
              <span class="input-group-addon"><span class="fa fa-chain"></span> 你的网址</span>
              <input type="url" name="longurl" id="longurl" class="form-control" placeholder="请输入网址">
            </div>
            <center>
              <div class="shuaibi-tip animated tada  text-center"><i class="fa fa-heart text-danger"></i> <b>生成过程中请耐心等待几秒...</b></div>
            </center>
            <center>
              <div class="btn-group btn-group-justified" role="group" aria-label="...">
                <div class="btn-group" role="group">
                  <button type="radio" name="type" onclick="sub()" class="btn btn-success" style="background: linear-gradient(to right,#00E3E3,#02C874)">在线生成</button>
                </div>
              </div>
            </center> <br>
            </form>
          </div>
          <!--生成-->

          <!--还原-->
          <div class="tab-pane fade fade-up" id="zzhu">
            <div class="input-group">
              <span class="input-group-addon"><span class="fa fa-chain"></span> 你的网址</span>
              <input type="url" name="dwzurl" id="dwzurl" class="form-control" placeholder="请输入网址">
            </div>
            <center>
              <div class="shuaibi-tip animated tada  text-center"><i class="fa fa-heart text-danger"></i> <b>还原过程中请耐心等待几秒...</b></div>
            </center>
            <center>
              <div class="btn-group btn-group-justified" role="group" aria-label="...">
                <div class="btn-group" role="group">
                  <button type="radio" name="type" onclick="hy()" class="btn btn-success" style="background: linear-gradient(to right,#00E3E3,#02C874)">还原链接</button>
                </div>
              </div>
            </center> <br>
          </div>
          <!--还原-->

          <!--更多-->
          <div class="tab-pane fade fade-right" id="more">
            <div class="col-xs-6 col-sm-4 col-lg-4">
              <a class="block block-link-hover2 text-center" href="./user" target="_blank" data-toggle="modal">
                <div class="block-content block-content-full bg-warning" clearfix" style="background: linear-gradient(to right,#f10d3105,#fda085);color:#fff;">
                  <i class="fa fa-check-square-o fa-3x text-white"></i>
                  <div class="font-w1000 text-white-op push-15-t">登录</div>
                </div>
              </a>
            </div>

            <div class="col-xs-6 col-sm-4 col-lg-4">
              <a class="block block-link-hover2 text-center" href="./user/reg.php" target="_blank" data-toggle="modal">
                <div class="block-content block-content-full bg-flat" clearfix style="background: linear-gradient(to right,#f093fb,#f55710c);color:#fff;">
                  <i class="fa fa-cloud-download fa-3x text-white"></i>
                  <div class="font-w1000 text-white-op push-15-t">注册</div>
                </div>
              </a>
            </div>
          </div>
          <!--更多-->


        </div>
      </div>

      <!--站长-->
      <div class="modal fade" align="left" id="customerservice" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
              <h4 class="modal-title" id="myModalLabel">联系站长</h4>
            </div>
            <div class="modal-body">
              <div class="tab-pane" id="searc">
                <ul class="list-group animated bounceIn">
                  <li class="list-group-item">
                    <div class="media">
                      <span class="pull-left thumb-sm"><img src="<?php echo $qqimg ?>" class="img-circle img-thumbnail img-avatar"></span>
                      <div class="pull-right push-15-t">
                        <a href="http://wpa.qq.com/msgrd?v=3&uin=<?php echo $conf['kf_qq'] ?>&site=qq&menu=yes" target="_blank" data-toggle="modal" class="btn btn-sm btn-info">联系站长</a>
                      </div>
                      <div class="pull-left push-10-t">
                        <div class="font-w600 push-5"><?php echo $conf['web_name'] ?></div>
                        <div class="text-muted">
                          <i class="fa fa-clock-o" aria-hidden="true"></i>&nbsp;8:00 - 21:00</div>
                      </div>
                    </div>
                    <hr> <b>
                      <font color="#6699FF" style="text-shadow: black 1px 1px 1px;">
                        <i class="glyphicon glyphicon-refresh text-info fa-spin"></i>&nbsp;链接红了可在本站重新生成！</font>
                      <br>
                    </b>
                    <b>
                      <font color="#00CD00" style="text-shadow: black 1px 1px 1px;">
                        <i class="glyphicon glyphicon-ok-circle text-success"></i>&nbsp;VIP:可修改跳转链接</font>
                      <br>
                    </b>
                    <b>
                      <font color="#FF6347" style="text-shadow: black 1px 1px 1px;">
                        <i class="fa fa-diamond"></i>&nbsp;黑名单：<font color="#FF6347">充分理由即可解除黑名单！</font>
                      </font>
                    </b>
                  </li>
                </ul>
              </div>
              <center>
                <span class="text-muted">
                  <div class="btn-group btn-group-justified">
                    <a href="<?php echo $conf['group_link'] ?>" target="_blank" data-toggle="modal" class="btn btn-effect-ripple btn-default"><i class="fa fa-qq"></i> <span style="font-weight:bold">点我立即进入交流群</span></a> </div>
                </span>
              </center>
            </div>
          </div>
        </div>
      </div>
      <!--站长-->


      <!--底部排版-->
      <div class="panel panel-primary">
        <img src="https://ae01.alicdn.com/kf/H892e5f3d90064e95b39b9c41c27b4fa8C.gif" height="2px" width="100%">
        <div style="float:left; margin:0px 10px;">
          <img class="qqlogo" src="<?php echo $qqimg ?>" width="70px" height="70px" alt="联系客服" title="联系客服"></a>
        </div>
        <div style="line-height:24px;">
          <span class="bityears">
            <font color="teal">本站域名:<a href="http://<?php echo $conf['domain'] ?>">
                <font color="teal"><?php echo $conf['domain'] ?></font>
              </a></font>
          </span>
          </font>
        </div>
        <font color="teal">
          <div style="line-height:24px;" class="bityears"><a href="http://wpa.qq.com/msgrd?v=3&uin=<?php echo $conf['kf_qq'] ?>&site=qq&menu=yes">
              <font color="teal">全网最强防洪系统  永久更新防洪接口</font>
            </a> </div>
          <div style="line-height:24px;" class="bityears"><a href="http://wpa.qq.com/msgrd?v=3&uin=<?php echo $conf['kf_qq'] ?>&site=qq&menu=yes">
              <font color="teal">提供一流防洪服务  打造完美防洪体系</font>
            </a>
          </div>
          <div style="line-height:24px;">免责声明:<font color="teal">防护短网址由用户生成，所跳转的内容和本站无关，如若违法，本站概不承担任何法律责任，我们将对您的生成的IP和内容进行抓查！如有发现违法网站我们将进行拉黑处理并直接提交给有关部门！本站严禁生成黄、赌、毒、钓鱼、诈骗等违法站！</font>
        </font>
      </div>
      <img src="https://ae01.alicdn.com/kf/H892e5f3d90064e95b39b9c41c27b4fa8C.gif" height="2px" width="100%">
    </div>
    <!--底部排版-->


    <!--底部导航-->
    <a>
      <div class="block panel-body text-center" style="box-shadow:0px 5px 10px 0 rgba(0, 0, 0, 0.25);">
        <a href="javascript:void(0);" onclick="AddFavorite('<?php echo $conf['web_name'] ?>',location.href)" class="bityears">
          <?php echo $conf['index_bottom'] ?>
      </div>
    </a>
  </div>
  </div>
  <!--底部导航-->


  </div>
  </div>

  <br>

  <!-- 收藏代码开始-->
  <script>
    function AddFavorite(title, url) {
      try {
        window.external.addFavorite(url, title);
      } catch (e) {
        try {
          window.sidebar.addPanel(title, url, "");
        } catch (e) {
          alert("手机用户：点击底部 “≡” 添加书签/收藏网址!\n\n电脑用户：请您按 Ctrl+D 手动收藏本网址! ");
        }
      }
    }
  </script>
  <!-- 收藏代码结束-->


  <script src="//lib.baomitu.com/jquery/1.12.4/jquery.min.js"></script>
  <script src="//lib.baomitu.com/jquery.lazyload/1.9.1/jquery.lazyload.min.js"></script>
  <script src="//lib.baomitu.com/twitter-bootstrap/3.3.7/js/bootstrap.min.js"></script>
  <script src="//lib.baomitu.com/jquery-cookie/1.4.1/jquery.cookie.min.js"></script>
  <script src="//lib.baomitu.com/layer/2.3/layer.js"></script>
  <script src="./static/dsw/js/app.js"></script>
  <script>
    function sub() {
      url = $(':input[name=\'longurl\']').val();
      if (!url || url == '') {
        layer.alert('网址不能为空');
        return false;
      }
      var stype = $("select[id='dwz-type']").val();
      var pattern = $("select[id='dwz-pattern']").val();
      url = url.replace(/\+/g, "%2B");
      url = url.replace(/\&/g, "%26");
      var load = layer.load();
      $.ajax({
        type: "post",
        url: "ajax.php?act=creat1",
        dataType: "json",
        data: 'url=' + url + '&type=' + stype + '&pattern=' + pattern,
        async: true,
        success: function(a) {
          var strJson = JSON.stringify(a)
          var data = $.parseJSON(strJson);
          layer.close(load);
          if (data.code == 0) {
            $('#urlts').html('生成成功');
            $('#dwzdate').html('短链地址：' + data.dwz + '<br>数据统计：<a href="' + data.data + '" target="_blank">点击查看</a><br /><br /><img class="qrimg" width="200px" src="includes/libs/qrcode.php?size=300&text=' + data.dwz + '" />');
            var $modal = $('#your-modal');
            $modal.modal();
          } else {
            layer.alert(data.msg);
          }
        },
        error: function() {
          layer.alert('出问题咯，请联系站长！');
        }
      })
    }

    function hy() {
      url = $(':input[name=\'dwzurl\']').val();
      if (!url || url == '') {
        layer.alert('网址不能为空');
        return false;
      }
      url = url.replace(/\+/g, "%2B");
      url = url.replace(/\&/g, "%26");
      var load = layer.load();
      $.ajax({
        type: "post",
        url: "ajax.php?act=creat2",
        dataType: "json",
        data: 'url=' + url,
        async: true,
        success: function(a) {
          var strJson = JSON.stringify(a)
          var data = $.parseJSON(strJson);
          layer.close(load);
          if (data.code == 0) {
            $('#urlts').html('还原成功');
            $('#dwzdate').html('还原网址：' + data.url + '<br />' +
              '真实网址：' + data.tzurl + '<br />' +
              '<br /><img class="qrimg" width="200px" src="includes/libs/qrcode.php?size=300&text=' + data.tzurl + '" />');
            var $modal = $('#your-modal');
            $modal.modal();
          } else {
            layer.alert(data.msg);
          }
        },
        error: function() {
          layer.alert('出问题咯，请联系站长！');
        }
      })
    }
  </script>
</body>

</html>