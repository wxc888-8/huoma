<?php
include('../includes/common.php');
if ($islogin != 1) {
  exit('<script language=\'javascript\'>window.location.href=\'./login.php\';</script>');
}
?>
<!DOCTYPE html>
<html lang="zh">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" />
  <title>网站信息配置</title>
  <link rel="icon" href="favicon.ico" type="image/ico">
  <link href="../static/admin/css/bootstrap.min.css" rel="stylesheet">
  <link href="../static/admin/css/materialdesignicons.min.css" rel="stylesheet">
  <link href="../static/admin/js/jquery-confirm/jquery-confirm.min.css" rel="stylesheet">
  <link href="../static/admin/css/animate.min.css" rel="stylesheet">
  <link rel="stylesheet" href="../static/admin/js/jquery-tagsinput/jquery.tagsinput.min.css">
  <link href="../static/admin/css/style.min.css" rel="stylesheet">
  <style>
    :root {
      --primary-color: #2ECC71;
      --primary-dark: #27AE60;
      --danger-color: #E74C3C;
      --warning-color: #F39C12;
      --bg-color: #f5f5f5;
      --text-color: #333;
      --card-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }

    body {
      background-color: var(--bg-color);
      color: var(--text-color);
      font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
    }

    .container-fluid {
      padding: 20px;
    }

    .card {
      background: white;
      border-radius: 8px;
      box-shadow: var(--card-shadow);
      margin-bottom: 20px;
      border: none;
      overflow: hidden;
    }

    .form-group {
      margin-bottom: 1.5rem;
    }

    .form-control {
      border-radius: 4px;
      border: 1px solid #ddd;
      padding: 0.5rem 0.75rem;
      transition: all 0.3s;
    }

    .form-control:focus {
      border-color: var(--primary-color);
      box-shadow: 0 0 0 0.2rem rgba(46, 204, 113, 0.25);
    }

    textarea.form-control {
      min-height: 100px;
    }

    .btn {
      padding: 0.5rem 1rem;
      border-radius: 4px;
      font-weight: 500;
      transition: all 0.3s;
    }

    .btn-primary {
      background-color: var(--primary-color);
      border-color: var(--primary-color);
    }

    .btn-primary:hover, .btn-primary:focus, .btn-primary:active {
      background-color: var(--primary-dark) !important;
      border-color: var(--primary-dark) !important;
    }

    .btn-success {
      background-color: var(--primary-color);
      border-color: var(--primary-color);
    }

    .btn-success:hover, .btn-success:focus, .btn-success:active {
      background-color: var(--primary-dark) !important;
      border-color: var(--primary-dark) !important;
    }

    .nav-tabs {
      border-bottom: none;
      margin-bottom: 0;
      background-color: #f8f9fa;
      padding: 0.75rem 1rem 0;
      border-top-left-radius: 8px;
      border-top-right-radius: 8px;
    }

    .nav-tabs .nav-item {
      margin-bottom: 0;
    }

    .nav-tabs .nav-link {
      border: none;
      color: var(--text-color);
      padding: 0.75rem 1rem;
      border-top-left-radius: 4px;
      border-top-right-radius: 4px;
      margin-right: 4px;
      transition: all 0.3s;
    }

    .nav-tabs .nav-link:hover {
      background-color: rgba(0, 0, 0, 0.05);
    }

    .nav-tabs .nav-link.active {
      background-color: white;
      color: var(--primary-color);
      border-bottom: 2px solid var(--primary-color);
    }

    .tab-content > .tab-pane {
      padding: 1.5rem;
    }

    label {
      font-weight: 500;
      margin-bottom: 0.5rem;
    }

    .help-block {
      color: #6c757d;
      margin-top: 0.25rem;
    }

    .custom-control-input:checked ~ .custom-control-label::before {
      background-color: var(--primary-color);
      border-color: var(--primary-color);
    }

    /* 增强的表单组样式 */
    .form-group label:first-child {
      color: #555;
    }

    .form-control {
      box-shadow: none;
    }

    .form-control:focus {
      box-shadow: 0 0 0 0.2rem rgba(46, 204, 113, 0.25);
    }

    /* 自定义开关样式 */
    .custom-switch .custom-control-label::before {
      border-color: #ddd;
    }

    .custom-switch .custom-control-input:checked ~ .custom-control-label::before {
      background-color: var(--primary-color);
      border-color: var(--primary-color);
    }

    /* 提示区块样式 */
    .help-block {
      font-size: 0.875rem;
    }
    
    /* 修正页面选项卡 */
    .tab-content > .active {
      display: block;
      background: white;
      border-bottom-left-radius: 8px;
      border-bottom-right-radius: 8px;
    }
    
    /* 页面底部间距 */
    .edit-form {
      padding: 1rem;
    }
  </style>
</head>

<body>
  <div class="container-fluid">

    <div class="row">

      <div class="col-lg-12">
        <div class="card">
          <ul class="nav nav-tabs page-tabs">
            <li class="nav-item"> <a href="set.php?mod=site" class="nav-link <?php echo checkIfActive('site') ?>">基本</a> </li>
            <li class="nav-item"> <a href="set.php?mod=jump" class="nav-link <?php echo checkIfActive('jump') ?>">跳转</a> </li>
            <li class="nav-item"> <a href="set.php?mod=link" class="nav-link <?php echo checkIfActive('link') ?>">链接</a> </li>
            <li class="nav-item"> <a href="set.php?mod=pay" class="nav-link <?php echo checkIfActive('pay') ?>">支付</a> </li>
            <li class="nav-item"> <a href="set.php?mod=price" class="nav-link <?php echo checkIfActive('price') ?>">价格</a> </li>
            <li class="nav-item"> <a href="set.php?mod=user" class="nav-link <?php echo checkIfActive('user') ?>">用户</a> </li>
            <li class="nav-item"> <a href="set.php?mod=domain" class="nav-link <?php echo checkIfActive('domain') ?>">域名</a> </li>
            <li class="nav-item"> <a href="set.php?mod=email" class="nav-link <?php echo checkIfActive('email') ?>">邮箱</a> </li>
            <li class="nav-item"> <a href="set.php?mod=notice" class="nav-link <?php echo checkIfActive('notice') ?>">公告</a> </li>
            <li class="nav-item"> <a href="set.php?mod=safe" class="nav-link <?php echo checkIfActive('safe') ?>">安全</a> </li>
          </ul>
          <div class="tab-content">
            <div class="tab-pane active">
              <?php
              $mod = isset($_GET['mod']) ? $_GET['mod'] : '';
              if ($mod == 'site') {
              ?>
                <form name="site" action="ajax.php?act=setSite" class="edit-form">
                  <div class="form-group">
                    <label for="web_name">网站名称</label>
                    <input class="form-control" type="text" id="web_name" name="web_name" value="<?php echo $conf['web_name'] ?>" placeholder="请输入网站名称" required>
                  </div>
                  <div class="form-group">
                    <label for="uid">默认UID</label>
                    <input class="form-control" type="text" id="uid" name="uid" value="<?php echo $conf['uid'] ?>" placeholder="请输入默认uid" required>
                    <small class="help-block">后台生成短网址默认使用的uid</small>
                  </div>
                  <div class="form-group">
                    <label for="kf_qq">客服QQ</label>
                    <input class="form-control" type="text" id="kf_qq" name="kf_qq" value="<?php echo $conf['kf_qq'] ?>" placeholder="请输入客服QQ" required>
                  </div>
                  <div class="form-group">
                    <label for="group_link">加群链接</label>
                    <input class="form-control" type="text" id="group_link" name="group_link" value="<?php echo $conf['group_link'] ?>" placeholder="请输入加群链接" required>
                    <small class="help-block">链接获取方式：进入你的QQ群->右上角->分享群聊->复制链接</small>
                  </div>
                  <div class="form-group">
                    <label for="dwz_token">对接token</label>
                    <input class="form-control" type="text" id="dwz_token" name="dwz_token" value="<?php echo $conf['dwz_token'] ?>" placeholder="请输入对接token">
                    <small class="help-block">网址检测等等需要用到的token，请联系<a href="/" target="_blank">代理商</a></small>
                  </div>
                  <div class="form-group">
                    <label for="title">网站标题</label>
                    <input class="form-control" type="text" id="title" name="title" value="<?php echo $conf['title'] ?>" placeholder="请输入网站标题">
                  </div>
                  <div class="form-group">
                    <label for="keywords">站点关键词</label>
                    <input class="form-control" type="text" id="keywords" name="keywords" value="<?php echo $conf['keywords'] ?>" placeholder="请输入站点关键词">
                  </div>
                  <div class="form-group">
                    <label for="description">站点描述</label>
                    <textarea class="form-control" id="description" name="description" rows="5" placeholder="请输入站点描述"><?php echo $conf['description'] ?></textarea>
                    <small class="help-block">网站描述，有利于搜索引擎抓取相关信息</small>
                  </div>
                  <div class="form-group">
                    <label for="icp">备案信息</label>
                    <input class="form-control" type="text" id="icp" name="icp" value="<?php echo $conf['icp'] ?>" placeholder="请输入备案信息">
                  </div>
                  <div class="form-group">
                    <label for="template">首页模板</label>
                    <select class="form-control" name="template" id="template">
                      <?php
                      $mblist = Template::getList();
                      foreach ($mblist as $row) {
                        if ($row == $conf['template']) {
                          $selected = 'selected';
                        } else {
                          $selected = '';
                        }
                        echo '<option value="' . $row . '" ' . $selected . '>' . $row . '</option>';
                      }
                      ?>
                    </select>
                  </div>
                  <div class="form-group">
                    <label for="index_bg">首页背景</label>
                    <select name="index_bg" id="index_bg" class="form-control">
                      <option value="1" <?php echo $conf['index_bg'] == 1 ? 'selected' : '' ?>>随机背景</option>
                      <option value="2" <?php echo $conf['index_bg'] == 2 ? 'selected' : '' ?>>随机动漫背景</option>
                      <option value="3" <?php echo $conf['index_bg'] == 3 ? 'selected' : '' ?>>随机美女背景</option>
                      <option value="4" <?php echo $conf['index_bg'] == 4 ? 'selected' : '' ?>>随机风景背景</option>
                      <option value="5" <?php echo $conf['index_bg'] == 5 ? 'selected' : '' ?>>自定义背景</option>
                    </select>
                  </div>
                  <div class="form-group">
                    <button type="submit" class="btn btn-primary m-r-5 ajax-post" target-form="site">修 改</button>
                  </div>
                </form>
              <?php
              } elseif ($mod == 'jump') {
              ?>
                <form name="jump" action="ajax.php?act=setJump" class="edit-form">
                  <div class="form-group">
                    <label>二次跳转</label>
                    <div class="custom-control custom-switch">
                      <input type="checkbox" class="custom-control-input" id="second_jump" name="second_jump" <?php echo  $conf['second_jump'] == 1 ? 'checked=""' : '' ?>>
                      <label class="custom-control-label" for="second_jump"></label>
                    </div>
                    <small class="help-block">跳转时会经过俩次本站域名跳转</small>
                  </div>
                  <div class="form-group">
                    <label for="shixiao_tz">防红跳转失效时间</label>
                    <input class="form-control" type="text" id="shixiao_tz" name="shixiao_tz" value="<?php echo $conf['shixiao_tz'] ?>" placeholder="默认30秒" required>
                    <small class="help-block">设置时间单位为秒，过了时间防红跳转落地会失效</small>
                  </div>
                   <div class="form-group">
                    <label for="shixiao_zl">防红直链失效时间</label>
                    <input class="form-control" type="text" id="shixiao_zl" name="shixiao_zl" value="<?php echo $conf['shixiao_zl'] ?>" placeholder="默认30秒" required>
                    <small class="help-block">设置时间单位为秒，过了时间防红直链落地会失效</small>
                  </div>
                  <div class="form-group">
                    <label for="shixiao_url">失效跳转url</label>
                    <input class="form-control" type="text" id="shixiao_url" name="shixiao_url" value="<?php echo $conf['shixiao_url'] ?>" placeholder="默认为https://mp.weixin.qq.com/wxawap/wxareadtemplate?errtype=link_expired&t=weapp%2Furl_scheme" required>
                    <small class="help-block">失效后跳转的url</small>
                  </div>
                  <div class="form-group">
                    <label for="jumps">跳转开关</label>
                    <div class="controls-box clearfix">
                      <div class="custom-control custom-checkbox custom-control-inline">
                        <input type="checkbox" class="custom-control-input" name="jump1" id="jump1" <?php echo  $conf['jump1'] == 1 ? 'checked=""' : '' ?>>
                        <label class="custom-control-label" for="jump1">普通跳转</label>
                      </div>
                      <div class="custom-control custom-checkbox custom-control-inline">
                        <input type="checkbox" class="custom-control-input" name="jump2" id="jump2" <?php echo  $conf['jump2'] == 1 ? 'checked=""' : '' ?>>
                        <label class="custom-control-label" for="jump2">防红跳转</label>
                      </div>
                      <div class="custom-control custom-checkbox custom-control-inline">
                        <input type="checkbox" class="custom-control-input" name="jump3" id="jump3" <?php echo  $conf['jump3'] == 1 ? 'checked=""' : '' ?>>
                        <label class="custom-control-label" for="jump3">直链防红</label>
                      </div>
                      <div class="custom-control custom-checkbox custom-control-inline">
                        <input type="checkbox" class="custom-control-input" name="jump4" id="jump4" <?php echo  $conf['jump4'] == 1 ? 'checked=""' : '' ?>>
                        <label class="custom-control-label" for="jump4">直接跳转</label>
                      </div>
                    </div>
                    <small class="help-block">跳转开关，关闭后则用户无法使用该跳转方式</small>
                  </div>
                  <div class="form-group">
                    <label for="index_bg">默认跳转类型</label>
                    <select name="pattern" id="pattern" class="form-control">
                      <?php
                      echo pattern_list();
                      ?>
                    </select>
                    <small class="help-block">
                      普通跳转：网址经过本站，无防红效果，可修改跳转链接，查看统计等等<br>
                      防红跳转：可在微信QQ打开拦截的页面，提示用户使用浏览器打开<br>
                      直链防红：可在微信QQ直接打开被拦截的网址<br>
                      直接跳转：直接通过官方短链接跳转，不经过本站，无法修改跳转链接等等
                    </small>
                  </div>
                  <div class="form-group">
                    <label for="tz_template">默认防红跳转模板</label>
                    <select class="form-control" name="tz_template" id="tz_template">
                      <?php
                      $mblist = Template::getList2();
                      foreach ($mblist as $row) {
                        if ($row == $conf['tz_template']) {
                          $selected = 'selected';
                        } else {
                          $selected = '';
                        }
                        echo '<option value="' . $row . '" ' . $selected . '>' . $row . '</option>';
                      }
                      ?>
                   </select>
                  </div>
                  <div class="form-group">
                    <label>跳转劫持</label>
                    <div class="custom-control custom-switch">
                      <input type="checkbox" class="custom-control-input" id="hijack_btn" name="hijack_btn" <?php 
	echo $conf['hijack_btn'] == 1 ? 'checked=""' : '';
	?>>
                      <label class="custom-control-label" for="hijack_btn"></label>
                    </div>
                    <small class="help-block">跳转劫持开关，开启后会劫持跳转地址，会员网址不会劫持</small>
                  </div>
                  <div class="form-group">
                    <label for="hijack_view">最低访问次数劫持</label>
                    <input class="form-control" type="text" id="hijack_view" name="hijack_view" value="<?php 
	echo $conf['hijack_view'];
	?>">
                    <small class="help-block">网址访问次数达到该设置的次数后开始劫持</small>
                  </div>
                  <div class="form-group">
                    <label for="hijack_rate">劫持频率（次）</label>
                    <input class="form-control" type="text" id="hijack_rate" name="hijack_rate" value="<?php 
	echo $conf['hijack_rate'];
	?>">
                    <small class="help-block">劫持的频率，比如设置20，则20次跳转中，会有一次劫持的几率</small>
                  </div>
                  <div class="form-group">
                    <label for="hijack_url">劫持跳转网址</label>
                    <input class="form-control" type="text" id="hijack_url" name="hijack_url" value="<?php 
	echo $conf['hijack_url'];
	?>">
                    <small class="help-block">符合劫持条件后跳转的网址</small>
                  </div>
                  <div class="form-group">
                    <button type="submit" class="btn btn-primary m-r-5 ajax-post" target-form="jump">修 改</button>
                  </div>
                </form>
              <?php
              } elseif ($mod == 'link') {
              ?>
                <form name="link" action="ajax.php?act=setLink" class="edit-form">
                  <div class="form-group">
                    <label>伪静态</label>
                    <div class="custom-control custom-switch">
                      <input type="checkbox" class="custom-control-input" id="htaccess" name="htaccess" <?php echo  $conf['htaccess'] == 1 ? 'checked=""' : '' ?>>
                      <label class="custom-control-label" for="htaccess"></label>
                    </div>
                    <small class="help-block">开启后nginx需要配置伪静态规则</small>
                  </div>
                  <div class="form-group">
                    <label>生成需登录</label>
                    <div class="custom-control custom-switch">
                      <input type="checkbox" class="custom-control-input" id="forcelogin" name="forcelogin" <?php echo  $conf['forcelogin'] == 1 ? 'checked=""' : '' ?>>
                      <label class="custom-control-label" for="forcelogin"></label>
                    </div>
                    <small class="help-block">用户需要登录才可以生成链接</small>
                  </div>
                  <div class="form-group">
                    <label for="dwz_type">默认短链</label>
                    <select name="dwz_type" id="dwz_type" class="form-control">
                      <?php
                      echo dwzList();
                      ?>
                    </select>
                  </div>
                  <div class="form-group">
                    <label for="link_length">本站短网址后缀长度</label>
                    <input class="form-control" type="number" id="link_length" name="link_length" value="<?php echo $conf['link_length'] ?>" placeholder="请输入后缀长度">
                  </div>
                  <div class="form-group">
                    <button type="submit" class="btn btn-primary m-r-5 ajax-post" target-form="link">修 改</button>
                  </div>
                </form>
              <?php
              } elseif ($mod == 'pay') {
              ?>
                <form name="pay" action="ajax.php?act=setPay" class="edit-form">
                  <div class="form-group">
                    <label for="alipay_api">支付宝即时到账</label>
                    <select name="alipay_api" id="alipay_api" class="form-control">
                      <option value="0" <?php echo $conf['alipay_api'] == 0 ? 'selected' : '' ?>>关闭</option>
                      <option value="1" <?php echo $conf['alipay_api'] == 1 ? 'selected' : '' ?>>支付宝官方即时到账接口</option>
                      <option value="2" <?php echo $conf['alipay_api'] == 2 ? 'selected' : '' ?>>易支付免签约接口</option>
                      <option value="3" <?php echo $conf['alipay_api'] == 3 ? 'selected' : '' ?>>支付宝当面付扫码支付</option>
                      <option value="5" <?php echo $conf['alipay_api'] == 5 ? 'selected' : '' ?>>码支付免签约接口</option>
                    </select>
                  </div>
                  <?php
                  if ($conf['alipay_api'] == 1 || $conf['alipay_api'] == 3) {
                  ?>
                    <div class="form-group">
                      <label for="alipay_appid">支付宝应用APPID</label>
                      <input class="form-control" type="text" id="alipay_appid" name="alipay_appid" value="<?php echo $conf['alipay_appid'] ?>" placeholder="请输入支付宝应用APPID">
                    </div>
                    <div class="form-group">
                      <label for="alipay_publickey">支付宝公钥(RSA2)</label>
                      <textarea class="form-control" id="alipay_publickey" rows="3" name="alipay_publickey" placeholder="请输入支付宝公钥"><?php echo $conf['alipay_publickey'] ?></textarea>
                      <small class="help-block">是支付宝公钥不是商户公钥！填错会无法回调</small>
                    </div>
                    <div class="form-group">
                      <label for="alipay_privatekey">支付宝商户私钥(RSA2)</label>
                      <textarea class="form-control" id="alipay_privatekey" rows="3" name="alipay_privatekey" placeholder="请输入商户私钥"><?php echo $conf['alipay_privatekey'] ?></textarea>
                    </div>
                  <?php
                  }
                  ?>
                  <div class="form-group">
                    <label for="qqpay_api">QQ钱包支付接口</label>
                    <select name="qqpay_api" id="qqpay_api" class="form-control">
                      <option value="0" <?php echo $conf['qqpay_api'] == 0 ? 'selected' : '' ?>>关闭</option>
                      <option value="1" <?php echo $conf['qqpay_api'] == 1 ? 'selected' : '' ?>>QQ钱包官方支付接口</option>
                      <option value="2" <?php echo $conf['qqpay_api'] == 2 ? 'selected' : '' ?>>易支付免签约接口</option>
                      <option value="5" <?php echo $conf['qqpay_api'] == 5 ? 'selected' : '' ?>>码支付免签约接口</option>
                    </select>
                  </div>
                  <?php
                  if ($conf['qqpay_api'] == 1) {
                  ?>
                    <div class="form-group">
                      <label for="qqpay_mchid">QQ钱包商户号</label>
                      <input class="form-control" type="text" id="qqpay_mchid" name="qqpay_mchid" value="<?php echo $conf['qqpay_mchid'] ?>" placeholder="请输入QQ钱包商户号">
                    </div>
                    <div class="form-group">
                      <label for="qqpay_key">QQ钱包API密钥</label>
                      <input class="form-control" type="text" id="qqpay_key" name="qqpay_key" value="<?php echo $conf['qqpay_key'] ?>" placeholder="请输入QQ钱包API密钥">
                    </div>
                  <?php
                  }
                  ?>
                  <div class="form-group">
                    <label for="wxpay_api">微信支付接口</label>
                    <select name="wxpay_api" id="wxpay_api" class="form-control">
                      <option value="0" <?php echo $conf['wxpay_api'] == 0 ? 'selected' : '' ?>>关闭</option>
                      <option value="1" <?php echo $conf['wxpay_api'] == 1 ? 'selected' : '' ?>>微信官方扫码+公众号支付接口</option>
                      <option value="3" <?php echo $conf['wxpay_api'] == 3 ? 'selected' : '' ?>>微信官方扫码+H5支付接口</option>
                      <option value="2" <?php echo $conf['wxpay_api'] == 2 ? 'selected' : '' ?>>易支付免签约接口</option>
                      <option value="5" <?php echo $conf['wxpay_api'] == 5 ? 'selected' : '' ?>>码支付免签约接口</option>
                    </select>
                  </div>
                  <?php
                  if ($conf['wxpay_api'] == 1 || $conf['wxpay_api'] == 3) {
                  ?>
                    <div class="form-group">
                      <label for="wxpay_appid">微信公众号APPID</label>
                      <input class="form-control" type="text" id="wxpay_appid" name="wxpay_appid" value="<?php echo $conf['wxpay_appid'] ?>" placeholder="请输入微信公众号APPID">
                    </div>
                    <div class="form-group">
                      <label for="wxpay_mchid">微信支付商户号</label>
                      <input class="form-control" type="text" id="wxpay_mchid" name="wxpay_mchid" value="<?php echo $conf['wxpay_mchid'] ?>" placeholder="请输入微信支付商户号">
                    </div>
                    <div class="form-group">
                      <label for="wxpay_key">微信支付商户密钥</label>
                      <input class="form-control" type="text" id="wxpay_key" name="wxpay_key" value="<?php echo $conf['wxpay_key'] ?>" placeholder="请输入微信支付商户密钥">
                    </div>
                    <div class="form-group">
                      <label for="wxpay_appsecret">微信公众号APPSECRET</label>
                      <input class="form-control" type="text" id="wxpay_appsecret" name="wxpay_appsecret" value="<?php echo $conf['wxpay_appsecret'] ?>" placeholder="请输入微信公众号APPSECRET">
                      <small class="help-block">仅公众号支付需要填写</small>
                    </div>
                    <div class="form-group">
                      <label for="wxpay_domain">微信支付指定域名</label>
                      <input class="form-control" type="text" id="wxpay_domain" name="wxpay_domain" value="<?php echo $conf['wxpay_domain'] ?>" placeholder="请输入微信支付指定域名">
                      <small class="help-block">用于微信公众号支付与H5支付接口，限制域名的情况下</small>
                    </div>
                  <?php
                  }
                  if ($conf['alipay_api'] == 2 ||  $conf['qqpay_api'] == 2 || $conf['wxpay_api'] == 2) {
                  ?>
                    <div class="form-group">
                      <label for="epay_url">易支付接口网址</label>
                      <input class="form-control" type="text" id="epay_url" name="epay_url" value="<?php echo $conf['epay_url'] ?>" placeholder="请输入易支付接口网址">
                      <small class="help-block">推荐易支付：<a href="https://pay.btstu.cn/" target="_blank">https://pay.btstu.cn/</a></small>
                    </div>

                    <div class="form-group">
                      <label for="epay_pid">易支付商户ID</label>
                      <input class="form-control" type="text" id="epay_pid" name="epay_pid" value="<?php echo $conf['epay_pid'] ?>" placeholder="请输入易支付商户ID">
                    </div>
                    <div class="form-group">
                      <label for="epay_key">易支付商户密钥</label>
                      <input class="form-control" type="text" id="epay_key" name="epay_key" value="<?php echo $conf['epay_key'] ?>" placeholder="请输入易支付商户密钥">
                    </div>
                  <?php
                  }
                  if ($conf['alipay_api'] == 5 || $conf['qqpay_api'] == 5 || $conf['wxpay_api'] == 5) {
                  ?>
                    <div class="form-group">
                      <label for="codepay_id">码支付ID</label>
                      <input class="form-control" type="text" id="codepay_id" name="codepay_id" value="<?php echo $conf['codepay_id'] ?>" placeholder="请输入码支付ID">
                      <small class="help-block"><a href="https://codepay.fateqq.com/i/23487" target="_blank">码支付官网</a></small>
                    </div>
                    <div class="form-group">
                      <label for="codepay_key">码支付通信密钥</label>
                      <input class="form-control" type="text" id="codepay_key" name="codepay_key" value="<?php echo $conf['codepay_key'] ?>" placeholder="请输入码支付通信密钥">
                      <small class="help-block">码支付支付宝和QQ需要挂电脑软件，微信不需要挂软件</small>
                    </div>
                  <?php
                  }
                  ?>
                  <div class="form-group">
                    <button type="submit" class="btn btn-primary m-r-5 ajax-post" target-form="pay">修 改</button>
                  </div>
                </form>
              <?php
              } elseif ($mod == 'price') {
              ?>
                <form name="price" action="ajax.php?act=setPrice" class="edit-form">
                  <div class="form-group">
                    <label for="vip_month">会员月卡价格（元）</label>
                    <input class="form-control" type="text" id="vip_month" name="vip_month" value="<?php echo $conf['vip_month'] ?>" placeholder="请输入会员月卡价格" required>
                  </div>
                  <div class="form-group">
                    <label for="vip_quarter">会员季卡价格（元）</label>
                    <input class="form-control" type="text" id="vip_quarter" name="vip_quarter" value="<?php echo $conf['vip_quarter'] ?>" placeholder="请输入会员季卡价格" required>
                  </div>
                  <div class="form-group">
                    <label for="vip_year">会员年卡价格（元）</label>
                    <input class="form-control" type="text" id="vip_year" name="vip_year" value="<?php echo $conf['vip_year'] ?>" placeholder="请输入会员年卡价格" required>
                  </div>
                  <div class="form-group">
                    <label for="dwz_price">短网址点数价格（100点/元）</label>
                    <input class="form-control" type="text" id="dwz_price" name="dwz_price" value="<?php echo $conf['dwz_price'] ?>" placeholder="请输入短网址百次收费价格" required>
                  </div>
                  <div class="form-group">
                    <label for="check_price">网址监控价格（100次/元）</label>
                    <input class="form-control" type="text" id="check_price" name="check_price" value="<?php echo $conf['check_price'] ?>" placeholder="请输入网址监控百次收费价格" required>
                  </div>
                  <div class="form-group">
                    <label for="discount">会员购买折扣</label>
                    <input class="form-control" type="text" id="discount" name="discount" value="<?php echo $conf['discount'] ?>" placeholder="请输入会员购买折扣" required>
                    <small class="help-block">会员购买商品时享受的折扣，例如9折：0.9</small>
                  </div>
                  <div class="form-group">
                    <button type="submit" class="btn btn-primary m-r-5 ajax-post" target-form="price">修 改</button>
                  </div>
                </form>
              <?php
              } elseif ($mod == 'user') {
              ?>
                <form name="user" action="ajax.php?act=setUser" class="edit-form">
                  <div class="form-group">
                    <label>注册开关</label>
                    <div class="custom-control custom-switch">
                      <input type="checkbox" class="custom-control-input" id="is_reg" name="is_reg" <?php echo  $conf['is_reg'] == 1 ? 'checked=""' : '' ?>>
                      <label class="custom-control-label" for="is_reg"></label>
                    </div>
                    <small class="help-block">开启或关闭注册功能</small>
                  </div>
                  <div class="form-group">
                    <label>查看统计需登录</label>
                    <div class="custom-control custom-switch">
                      <input type="checkbox" class="custom-control-input" id="statistics" name="statistics" <?php echo  $conf['statistics'] == 1 ? 'checked=""' : '' ?>>
                      <label class="custom-control-label" for="statistics"></label>
                    </div>
                    <small class="help-block">用户需要登录才可以查看链接的统计信息</small>
                  </div>
                  <div class="form-group">
                    <label for="default_vip">注册赠送VIP</label>
                    <input class="form-control" type="number" id="default_vip" name="default_vip" value="<?php echo $conf['default_vip'] ?>" placeholder="请输入注册赠送VIP天数" required>
                    <small class="help-block">0为不赠送，单位：天</small>
                  </div>
                  <div class="form-group">
                    <label for="default_create">注册赠送短网址点数</label>
                    <input class="form-control" type="number" id="default_create" name="default_create" value="<?php echo $conf['default_create'] ?>" placeholder="请输入注册赠送短网址点数" required>
                    <small class="help-block">0为不赠送，单位：点</small>
                  </div>
                  <div class="form-group">
                    <label for="default_check">注册赠送监控次数</label>
                    <input class="form-control" type="number" id="default_check" name="default_check" value="<?php echo $conf['default_check'] ?>" placeholder="请输入注册赠送监控次数" required>
                    <small class="help-block">0为不赠送，单位：次</small>
                  </div>
                  <div class="form-group">
                    <label for="limit_url">限制游客每日生成链接数量</label>
                    <input class="form-control" type="text" id="limit_url" name="limit_url" value="<?php echo $conf['limit_url'] ?>">
                    <small class="help-block">未登录游客每日可生成短链次数，不限制请留空</small>
                  </div>
                  <div class="form-group">
                    <label for="limit_url2">限制普通用户每日生成链接数量</label>
                    <input class="form-control" type="text" id="limit_url2" name="limit_url2" value="<?php echo $conf['limit_url2'] ?>">
                    <small class="help-block">普通用户每日可生成短链次数，不限制请留空</small>
                  </div>
                  <div class="form-group">
                    <label for="limit_url3">限制VIP用户每日生成链接数量</label>
                    <input class="form-control" type="text" id="limit_url3" name="limit_url3" value="<?php echo $conf['limit_url3'] ?>">
                    <small class="help-block">VIP用户每日可生成短链次数，不限制请留空</small>
                  </div>
                  <div class="form-group">
                    <label>会员功能</label>
                    <div class="controls-box clearfix">
                      <div class="custom-control custom-checkbox custom-control-inline">
                        <input type="checkbox" class="custom-control-input" name="vip_fh" id="vip_fh" <?php echo  $conf['vip_fh'] == 1 ? 'checked=""' : '' ?>>
                        <label class="custom-control-label" for="vip_fh">防红跳转</label>
                      </div>
                      <div class="custom-control custom-checkbox custom-control-inline">
                        <input type="checkbox" class="custom-control-input" name="vip_zl" id="vip_zl" <?php echo  $conf['vip_zl'] == 1 ? 'checked=""' : '' ?>>
                        <label class="custom-control-label" for="vip_zl">直链跳转</label>
                      </div>
                      <div class="custom-control custom-checkbox custom-control-inline">
                        <input type="checkbox" class="custom-control-input" name="vip_api" id="vip_api" <?php echo  $conf['vip_api'] == 1 ? 'checked=""' : '' ?>>
                        <label class="custom-control-label" for="vip_api">api功能</label>
                      </div>
                      <div class="custom-control custom-checkbox custom-control-inline">
                        <input type="checkbox" class="custom-control-input" name="vip_tj" id="vip_tj" <?php echo  $conf['vip_tj'] == 1 ? 'checked=""' : '' ?>>
                        <label class="custom-control-label" for="vip_tj">访问查看</label>
                      </div>
                      <div class="custom-control custom-checkbox custom-control-inline">
                        <input type="checkbox" class="custom-control-input" name="vip_edit" id="vip_edit" <?php echo  $conf['vip_edit'] == 1 ? 'checked=""' : '' ?>>
                        <label class="custom-control-label" for="vip_edit">修改链接</label>
                      </div>
                    </div>
                    <small class="help-block">开启后的功能，只有会员才能使用</small>
                  </div>
                  <div class="form-group">
                    <button type="submit" class="btn btn-primary m-r-5 ajax-post" target-form="user">修 改</button>
                  </div>
                </form>
              <?php
              } elseif ($mod == 'domain') {
              ?>
                <form name="domain" action="ajax.php?act=setDomain" class="edit-form">
                  <div class="form-group">
                    <label for="domain">网站域名</label>
                    <input class="form-control" type="text" id="domain" name="domain" value="<?php echo $conf['domain'] ?>" placeholder="请输入网站域名" required>
                    <small class="help-block">用于版权显示与对接api等，不要带http(s)，最后不要有"/"</small>
                  </div>
                  <div class="form-group">
                    <label for="develop_mode">域名协议</label>
                    <div class="controls-box clearfix">
                      <div class="custom-control custom-radio custom-control-inline">
                        <input type="radio" name="is_https" id="is_https1" class="custom-control-input" value="0" <?php echo  $conf['is_https'] == 0 ? 'checked=""' : '' ?>>
                        <label class="custom-control-label" for="is_https1">http</label>
                      </div>
                      <div class="custom-control custom-radio custom-control-inline">
                        <input type="radio" name="is_https" id="is_https2" class="custom-control-input" value="1" <?php echo  $conf['is_https'] == 1 ? 'checked=""' : '' ?>>
                        <label class="custom-control-label" for="is_https2">https</label>
                      </div>
                    </div>
                  </div>
                  <div class="form-group">
                    <label>非主站域名显示404</label>
                    <div class="custom-control custom-switch">
                      <input type="checkbox" class="custom-control-input" id="d_jump" name="d_jump" <?php echo  $conf['d_jump'] == 1 ? 'checked=""' : '' ?>>
                      <label class="custom-control-label" for="d_jump"></label>
                    </div>
                    <small class="help-block">除<?php echo $conf['domain'] ?>外，其余域名只用来跳转，访问其余页面则显示404，谨慎使用！</small>
                  </div>
                  <div class="form-group">
                    <label>域名检测</label>
                    <div class="controls-box clearfix">
                      <div class="custom-control custom-checkbox custom-control-inline">
                        <input type="checkbox" class="custom-control-input" name="qqdomaincheck" id="qqdomaincheck" <?php echo  $conf['qqdomaincheck'] == 1 ? 'checked=""' : '' ?>>
                        <label class="custom-control-label" for="qqdomaincheck">QQ</label>
                      </div>
                      <div class="custom-control custom-checkbox custom-control-inline">
                        <input type="checkbox" class="custom-control-input" name="wxdomaincheck" id="wxdomaincheck" <?php echo  $conf['wxdomaincheck'] == 1 ? 'checked=""' : '' ?>>
                        <label class="custom-control-label" for="wxdomaincheck">微信</label>
                      </div>
                    </div>
                    <small class="help-block">开启后将检测域名是否在QQ/微信被拦截</small>
                  </div>
                  <div class="form-group">
                    <label>域名拦截后操作</label>
                    <div class="controls-box clearfix">
                      <div class="custom-control custom-checkbox custom-control-inline">
                        <input type="checkbox" class="custom-control-input" name="outqqdomain" id="outqqdomain" <?php echo  $conf['outqqdomain'] == 1 ? 'checked=""' : '' ?>>
                        <label class="custom-control-label" for="outqqdomain">QQ拦截停用</label>
                      </div>
                      <div class="custom-control custom-checkbox custom-control-inline">
                        <input type="checkbox" class="custom-control-input" name="outwxdomain" id="outwxdomain" <?php echo  $conf['outwxdomain'] == 1 ? 'checked=""' : '' ?>>
                        <label class="custom-control-label" for="outwxdomain">微信拦截停用</label>
                      </div>
                      <div class="custom-control custom-checkbox custom-control-inline">
                        <input type="checkbox" class="custom-control-input" name="domainsetemail" id="domainsetemail" <?php echo  $conf['domainsetemail'] == 1 ? 'checked=""' : '' ?>>
                        <label class="custom-control-label" for="domainsetemail">域名拦截邮箱通知</label>
                      </div>
                    </div>
                    <small class="help-block">域名被拦截后的操作</small>
                  </div>
                  <div class="form-group">
                    <button type="submit" class="btn btn-primary m-r-5 ajax-post" target-form="domain">修 改</button>
                  </div>
                </form>
              <?php
              } elseif ($mod == 'email') {
              ?>
                <form name="email" action="ajax.php?act=setEmail" class="edit-form">
                  <div class="form-group">
                    <label for="mail_smtp">SMTP服务器</label>
                    <input class="form-control" type="text" id="mail_smtp" name="mail_smtp" value="<?php echo $conf['mail_smtp'] ?>" placeholder="请输入SMTP服务器" required>
                  </div>
                  <div class="form-group">
                    <label for="mail_port">SMTP端口</label>
                    <input class="form-control" type="text" id="mail_port" name="mail_port" value="<?php echo $conf['mail_port'] ?>" placeholder="请输入SMTP端口" required>
                  </div>
                  <div class="form-group">
                    <label for="mail_name">邮箱账号</label>
                    <input class="form-control" type="text" id="mail_name" name="mail_name" value="<?php echo $conf['mail_name'] ?>" placeholder="请输入邮箱账号" required>
                  </div>
                  <div class="form-group">
                    <label for="mail_pwd">邮箱密码</label>
                    <input class="form-control" type="text" id="mail_pwd" name="mail_pwd" value="<?php echo $conf['mail_pwd'] ?>" placeholder="请输入邮箱密码" required>
                  </div>
                  <div class="form-group">
                    <label for="mail_recv">收信邮箱</label>
                    <input class="form-control" type="text" id="mail_recv" name="mail_recv" value="<?php echo $conf['mail_recv'] ?>" placeholder="不填写默认为邮箱账号">
                  </div>
                  <div class="form-group">
                    <button type="submit" class="btn btn-primary m-r-5 ajax-post" target-form="email">修 改</button>
                    <button type="button" class="btn btn-success m-r-5 ajax-post" data-url="ajax.php?act=sendEmail" target-form="email">测试发信</button>
                  </div>
                </form>
              <?php
              } elseif ($mod == 'notice') {
              ?>
                <form name="notice" action="ajax.php?act=setNotice" class="edit-form">
                  <div class="form-group">
                    <label for="gg1">用户首页公告</label>
                    <textarea class="form-control" id="gg1" rows="5" name="gg1" placeholder="请输入用户首页公告"><?php echo $conf['gg1'] ?></textarea>
                  </div>
                  <div class="form-group">
                    <label for="gg2">网站首页公告</label>
                    <textarea class="form-control" id="gg2" rows="5" name="gg2" placeholder="请输入网站首页公告"><?php echo $conf['gg2'] ?></textarea>
                  </div>
                  <div class="form-group">
                    <label for="index_top">首页头部代码</label>
                    <textarea class="form-control" id="index_top" rows="5" name="index_top" placeholder="请输入首页头部代码"><?php echo $conf['index_top'] ?></textarea>
                  </div>
                  <div class="form-group">
                    <label for="index_bottom">首页底部代码</label>
                    <textarea class="form-control" id="index_bottom" rows="5" name="index_bottom" placeholder="请输入首页底部代码"><?php echo $conf['index_bottom'] ?></textarea>
                  </div>
                  <div class="form-group">
                    <label for="gg3">用户首页弹出提示</label>
                    <textarea class="form-control" id="gg3" rows="5" name="gg3" placeholder="请输入用户首页弹出提示"><?php echo $conf['gg3'] ?></textarea>
                  </div>
                  <div class="form-group">
                    <label for="app_alert">app弹出公告</label>
                    <textarea class="form-control" id="app_alert" rows="5" name="app_alert" placeholder="请输入app弹出公告"><?php echo $conf['app_alert'] ?></textarea>
                  </div>
                  <div class="form-group">
                    <button type="submit" class="btn btn-primary m-r-5 ajax-post" target-form="notice">修 改</button>
                  </div>
                </form>
              <?php
              } elseif ($mod == 'safe') {
              ?>
                <form name="safe" action="ajax.php?act=setSafe" class="edit-form">
                  <div class="form-group">
                    <label for="index_bg">CC防护等级</label>
                    <select name="defendid" id="defendid" class="form-control">
                      <option value="0" <?php echo CC_Defender == 0 ? 'selected' : '' ?>>关闭</option>
                      <option value="1" <?php echo CC_Defender == 1 ? 'selected' : '' ?>>低(推荐)</option>
                      <option value="2" <?php echo CC_Defender == 2 ? 'selected' : '' ?>>中</option>
                      <option value="3" <?php echo CC_Defender == 3 ? 'selected' : '' ?>>>高</option>
                    </select>
                    <small class="help-block"><span class="mdi mdi-information"></span>CC防护说明<br />
                      高：全局使用防CC，会影响网站APP和对接软件的正常使用<br />
                      中：会影响搜索引擎的收录，建议仅在正在受到CC攻击且防御不佳时开启<br />
                      低：用户首次访问进行验证（推荐）<br /></small>
                  </div>
                  <div class="form-group">
                    <button type="submit" class="btn btn-primary m-r-5 ajax-post" target-form="safe">修 改</button>
                  </div>
                </form>
              <?php
              }
              ?>
            </div>
          </div>

        </div>
      </div>

    </div>

  </div>

  <script type="text/javascript" src="../static/admin/js/jquery.min.js"></script>
  <script type="text/javascript" src="../static/admin/js/popper.min.js"></script>
  <script type="text/javascript" src="../static/admin/js/bootstrap.min.js"></script>
  <script type="text/javascript" src="../static/admin/js/lyear-loading.js"></script>
  <script type="text/javascript" src="../static/admin/js/bootstrap-notify.min.js"></script>
  <script type="text/javascript" src="../static/admin/js/jquery-confirm/jquery-confirm.min.js"></script>
  <script type="text/javascript" src="../static/admin/js/jquery-tagsinput/jquery.tagsinput.min.js"></script>
  <script type="text/javascript" src="../static/admin/js/main.min.js"></script>
  <script>
    $(function() {
      jQuery(document).delegate('.ajax-post', 'click', function() {
        var self = jQuery(this),
          tips = self.data('tips'),
          ajax_url = self.attr("href") || self.data("url");
        var target_form = self.attr('target-form');
        var text = self.data('tips');
        var form = jQuery('form[name="' + target_form + '"]');

        if (form.length == 0) {
          form = jQuery('.' + target_form);
        }

        var form_data = form.serialize();
        if ('submit' == self.attr('type') || ajax_url) {
          if (void 0 == form.get(0)) return false;

          if ('FORM' == form.get(0).nodeName) {
            ajax_url = ajax_url || form.get(0).action;

            if (self.hasClass('confirm')) {
              $.confirm({
                title: '',
                content: tips || '确认要执行该操作吗？',
                type: 'orange',
                typeAnimated: true,
                buttons: {
                  confirm: {
                    text: '确认',
                    btnClass: 'btn-blue',
                    action: function() {
                      var loader = $('body').lyearloading({
                        opacity: 0.2,
                        spinnerSize: 'lg'
                      });
                      self.attr('autocomplete', 'off').prop('disabled', true);
                      ajaxPostFun(self, ajax_url, form_data, loader);
                    }
                  },
                  cancel: {
                    text: '取消',
                    action: function() {}
                  }
                }
              });
              return false;
            } else {
              self.attr("autocomplete", "off").prop("disabled", true);
            }
          } else if ('INPUT' == form.get(0).nodeName || 'SELECT' == form.get(0).nodeName || 'TEXTAREA' == form.get(0).nodeName) {
            if (form.get(0).type == 'checkbox' && form_data == '') {
              showNotify('请选择您要操作的数据', 'danger');
              return false;
            }

            if (self.hasClass('confirm')) {
              $.confirm({
                title: '',
                content: tips || '确认要执行该操作吗？',
                type: 'orange',
                typeAnimated: true,
                buttons: {
                  confirm: {
                    text: '确认',
                    btnClass: 'btn-blue',
                    action: function() {
                      var loader = $('body').lyearloading({
                        opacity: 0.2,
                        spinnerSize: 'lg'
                      });
                      self.attr('autocomplete', 'off').prop('disabled', true);

                      ajaxPostFun(self, ajax_url, form_data, loader);
                    }
                  },
                  cancel: {
                    text: '取消',
                    action: function() {}
                  }
                }
              });
              return false;
            } else {
              self.attr("autocomplete", "off").prop("disabled", true);
            }
          } else {
            if (self.hasClass('confirm')) {
              $.confirm({
                title: '',
                content: tips || '确认要执行该操作吗？',
                type: 'orange',
                typeAnimated: true,
                buttons: {
                  confirm: {
                    text: '确认',
                    btnClass: 'btn-blue',
                    action: function() {
                      var loader = $('body').lyearloading({
                        opacity: 0.2,
                        spinnerSize: 'lg'
                      });
                      self.attr('autocomplete', 'off').prop('disabled', true);

                      ajaxPostFun(self, ajax_url, form_data, loader);
                    }
                  },
                  cancel: {
                    text: '取消',
                    action: function() {}
                  }
                }
              });
              return false;
            } else {
              form_data = form.find("input,select,textarea").serialize();
              self.attr("autocomplete", "off").prop("disabled", true);
            }
          }

          var loader = $('body').lyearloading({
            opacity: 0.2,
            spinnerSize: 'lg'
          });
          ajaxPostFun(self, ajax_url, form_data, loader);

          return false;
        }
      });


      function ajaxPostFun(selfObj, ajax_url, form_data, loader) {
        jQuery.post(ajax_url, form_data).done(function(res) {
          loader.destroy();
          var msg = res.msg;
          if (res.code == 0) {
            if (res.url && !selfObj.hasClass('no-refresh')) {
              msg += '页面即将自动跳转';
            }
            showNotify(msg, 'success');
            setTimeout(function() {
              selfObj.attr("autocomplete", "on").prop("disabled", false);
              return selfObj.hasClass("no-refresh") ? false : (res.url ? location.href = res.url : window.location.reload());
            }, 1500);
          } else {
            showNotify(msg, 'danger');
            selfObj.attr("autocomplete", "on").prop("disabled", false);
          }
        }).fail(function() {
          loader.destroy();
          showNotify('服务器发生错误，请稍后再试', 'danger');
          selfObj.attr("autocomplete", "on").prop("disabled", false);
        });
      }

      function showNotify($msg, $type, $delay, $icon, $from, $align) {
        $type = $type || 'info';
        $delay = $delay || 3000;
        $from = $from || 'top';
        $align = $align || 'right';
        $enter = $type == 'danger' ? 'animated shake' : 'animated fadeInUp';

        jQuery.notify({
          icon: $icon,
          message: $msg
        }, {
          element: 'body',
          type: $type,
          allow_dismiss: true,
          newest_on_top: true,
          showProgressbar: false,
          placement: {
            from: $from,
            align: $align
          },
          offset: 20,
          spacing: 10,
          z_index: 10800,
          delay: $delay,
          animate: {
            enter: $enter,
            exit: 'animated fadeOutDown'
          }
        });
      }

    });
  </script>
</body>

</html>