<?php
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
  <meta name="description" content="<?php echo $conf['description']; ?>">
  <meta name="keywords" content="<?php echo $conf['keywords']; ?>">
  <link rel="shortcut icon" href="./static/picture/favicon.ico">
  <link rel="stylesheet" href="https://lib.baomitu.com/amazeui/2.7.2/css/amazeui.min.css">
  <link rel="stylesheet" href="./static/jiuyun/assets/simple/css/plugins.css">
  <link rel="stylesheet" href="./static/jiuyun/assets/simple/css/main.css">
  <link rel="stylesheet" href="./static/jiuyun/assets/simple/css/oneui.css">
  <!--[if lt IE 9]>
    <script src="https://lib.baomitu.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://lib.baomitu.com/respond.js/1.4.2/respond.min.js"></script>
  <![endif]-->
  <style>
    .shuaibi-tip {
      background: #fafafa repeating-linear-gradient(-45deg, #fff, #fff 1.125rem, transparent 1.125rem, transparent 2.25rem);
      box-shadow: 0 2px 5px rgba(0, 0, 0, 0.15);
      margin: 20px 0px;
      padding: 15px;
      border-radius: 5px;
      font-size: 14px;
      color: #555555;
    }
  </style>
</head>

<body>
  <img style="background: linear-gradient(to right,#49BDAD,#f2b9ca);color:#000;" class="full-bg full-bg-bottom" ondragstart="return false;" oncontextmenu="return false;">
  <br>
  <div class="col-xs-12 col-sm-10 col-md-8 col-lg-5 center-block" style="float: none;">
    <div class="widget">
      <div class="widget-content themed-background-flat text-center" style="background-image:url(img/head.png);background-size: 100% 100%;">
        <a href="javascript:void(0)">
          <img src="<?php echo $qqimg ?>" alt="Avatar" width="80" style="height: auto filter: alpha(Opacity=80);-moz-opacity: 0.80;opacity: 0.80;" class="img-circle img-thumbnail img-thumbnail-avatar-1x animated zoomInDown">
        </a>
      </div>
      <h2 class="text-center"> <a href="javascript:void(alert('<?php echo $conf['web_name']; ?>，建议收藏到浏览器书签哦！'));"><b><?php echo $conf['web_name']; ?></b></a></h2>
      <div class="widget-content text-center">
        <div class="text-center text-muted">
          <div class="btn-group btn-group-justified">
            <div class="btn-group">
              <a href="https://api.btstu.cn/qqtalk/api.php?qq=<?php echo $conf['kf_qq'] ?>">
                <font color="#ff0000"><i class="fa fa-bolt"></i> 联系客服</font>
              </a></div>
            <div class="btn-group">
              <a href="./user">
                <font color="#ff0000"><i class="fa fa-bolt"></i> 用户中心</font>
              </a>
            </div>
          </div>
        </div>
      </div>

    </div>
    <div class="block full2">
      <div class="tab-content">
        <div class="tab-pane active" id="shop">
          <div class="shuaibi-tip animated tada text-center"><i class="fa fa-heart text-danger"></i> <b><?php echo $conf['gg2'] ?></b></div>
          <div class="form-group">
            <div class="input-group">
              <div class="input-group-addon">短链类型</div>
              <select name="dwz-type" id="dwz-type" class="form-control">
                <?php
                echo dwzList();
                ?>
              </select>
            </div>
          </div>
          <div class="form-group">
            <div class="input-group">
              <div class="input-group-addon">跳转类型</div>
              <select name="dwz-pattern" id="dwz-pattern" class="form-control">
                <?php
                echo pattern_list();
                ?>
              </select>
            </div>
          </div>
          <div class="form-group">
            <div class="input-group">
              <div class="input-group-addon">缩短网址</div>
              <input type="url" name="url" id="url" value="" class="form-control" placeholder="请输入正确网址" required="">
            </div>
          </div>
          <button type="submit" class="am-btn am-btn-success am-btn-sm am-btn-block am-round" id="start">一键生成</button>
          <br>
          <div id="result2" class="form-group" style="display:none;">
            <tbody id="list">
            </tbody>
            </table>
          </div>
          <div class="am-modal-bd">
            <p id="dwz"></p>
            <p id="data"></p>
            <p id="tzurl"></p>
            <p id="qrcode"></p>
          </div>
        </div>
      </div>
    </div>
    <div class="panel panel-default text-center">
      <div class="panel-body">
        <span style="font-weight:bold"><?php echo $conf['web_name']; ?> <i class="fa fa-heart text-danger"></i> -版权所有 | 本站域名: </span>
        <span><a href="http://<?php echo $conf['domain']; ?>"><span style="font-weight:bold"><?php echo $conf['domain']; ?></a></span><br>
        <span><a href="http://beian.miit.gov.cn" target="_blank" style="text-decoration:none;"><?php echo $conf['icp'] ?></a></span>
      </div>
    </div>
  </div>

  <script src="https://lib.baomitu.com/jquery/2.1.4/jquery.min.js"></script>
  <script src="https://lib.baomitu.com/amazeui/2.3.0/js/amazeui.min.js"></script>
  <script>
    $(document).ready(function() {
      $('#start').click(function() {
        $('#start').text('正在生成中，请耐心等待...');
        $("#start").addClass("am-btn-warning").removeClass("am-btn-success");
        var stype = $("select[id='dwz-type']").val();
        var pattern = $("select[id='dwz-pattern']").val();
        var url = $("input[id='url']").val();
        url = url.replace(/\+/g, "%2B");
        url = url.replace(/\&/g, "%26");
        $.ajax({
          type: "post",
          url: "ajax.php?act=creat1",
          dataType: "json",
          data: 'url=' + url + '&type=' + stype + '&pattern=' + pattern,
          async: true,
          success: function(obj) {
            $('#start').text('一键生成');
            $("#start").removeClass("am-btn-warning").addClass("am-btn-success");
            if (obj.code == 0) {
              var strJson = JSON.stringify(obj)
              var data = $.parseJSON(strJson);
              $('#dwz').html('短链地址：' + data.dwz);
              $('#data').html('数据统计：<a href="' + data.data + '" target="_blank">点击查看</a>');
              $('#tzurl').html('跳转链接：' + atob(data.url));
              $('#qrcode').html('<img class="qrimg" width="200px" src="includes/libs/qrcode.php?size=300&text=' + data.dwz + '" />');
            } else {
              alert(obj.msg);
            }
          },
          error: function(obj) {
            $('#start').text('生成失败');
            $("#start").removeClass("am-btn-warning").addClass("am-btn-danger");
          }

        });
      });
    });
  </script>
</body>

</html>