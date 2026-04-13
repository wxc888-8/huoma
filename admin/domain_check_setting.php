<?php
include('../includes/common.php');
if ($islogin != 1) {
  exit('<script language=\'javascript\'>window.location.href=\'./login.php\';</script>');
}

// 处理表单提交
if(isset($_POST['submit'])) {
  $check_api_key = isset($_POST['check_api_key']) ? trim($_POST['check_api_key']) : '';
  $check_points = isset($_POST['check_points']) ? intval($_POST['check_points']) : 1;
  $check_mode = isset($_POST['check_mode']) ? intval($_POST['check_mode']) : 0;
  
  // 保存设置
  saveSetting('check_api_key', $check_api_key);
  saveSetting('check_points', $check_points);
  saveSetting('check_mode', $check_mode);
  
  // 清除缓存
  $CACHE->clear();
  
  $msg = '保存成功！';
}
?>

<!DOCTYPE html>
<html lang="zh">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" />
  <title>域名检测设置 - 管理中心</title>
  <link rel="icon" href="favicon.ico" type="image/ico">
  <link href="../static/admin/css/bootstrap.min.css" rel="stylesheet">
  <link href="../static/admin/css/materialdesignicons.min.css" rel="stylesheet">
  <link href="../static/admin/js/bootstrap-table/bootstrap-table.min.css" rel="stylesheet">
  <link href="../static/admin/js/jquery-confirm/jquery-confirm.min.css" rel="stylesheet">
  <link href="../static/admin/css/style.min.css" rel="stylesheet">
  <style>
    .card-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
    }
    .promo-banner {
      background: linear-gradient(45deg, #2ECC71, #27AE60);
      color: white;
      border-radius: 8px;
      padding: 20px;
      margin-bottom: 20px;
      box-shadow: 0 4px 10px rgba(0,0,0,0.1);
      position: relative;
      overflow: hidden;
    }
    .promo-banner::before {
      content: '';
      position: absolute;
      top: -20px;
      right: -20px;
      width: 100px;
      height: 100px;
      background: rgba(255,255,255,0.1);
      border-radius: 50%;
    }
    .promo-banner h4 {
      margin-top: 0;
      font-weight: 600;
    }
    .promo-banner p {
      margin-bottom: 15px;
      opacity: 0.9;
    }
    .promo-banner .btn {
      background: white;
      color: #27AE60;
      border: none;
      font-weight: 600;
      transition: all 0.3s;
    }
    .promo-banner .btn:hover {
      transform: translateY(-2px);
      box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }
    .form-help {
      color: #6c757d;
      font-size: 0.875rem;
      margin-top: 0.25rem;
    }
  </style>
</head>

<body>
  <div class="container-fluid p-t-15">
    <div class="row">
      <div class="col-lg-12">
        <div class="card">
          <div class="card-header">
            <h4>域名检测设置</h4>
            <div class="toolbar">
              <div class="toolbar-action">
                <a class="btn btn-primary" href="javascript:history.back(-1)"><i class="mdi mdi-arrow-left"></i> 返回</a>
              </div>
            </div>
          </div>
          <div class="card-body">
            
            <?php if(isset($msg)){?>
            <div class="alert alert-success alert-dismissible" role="alert">
              <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
              <?php echo $msg?>
            </div>
            <?php }?>
            
            <!-- 推广横幅 -->
            <div class="promo-banner">
              <h4><i class="mdi mdi-information-outline"></i> 没有检测API秘钥？</h4>
              <p>请到太阳云检测平台注册获取API秘钥，实现高效准确的域名安全检测服务。</p>
              <a href="https://weixin.cadf.top" target="_blank" class="btn btn-rounded">立即获取秘钥</a>
            </div>
            
            <form action="" method="post" class="row">
              <div class="form-group col-md-12">
                <label for="check_api_key">检测API秘钥</label>
                <input type="text" class="form-control" id="check_api_key" name="check_api_key" value="<?php echo $conf['check_api_key']?>" placeholder="请输入检测API秘钥">
                <small class="form-help">请输入从太阳云检测平台获取的API秘钥，用于域名检测功能</small>
              </div>
              
              <div class="form-group col-md-6">
                <label for="check_points">每次检测消耗积分</label>
                <input type="number" class="form-control" id="check_points" name="check_points" value="<?php echo $conf['check_points'] ? $conf['check_points'] : 1?>" min="1" placeholder="请输入每次检测消耗的积分数">
                <small class="form-help">设置用户每检测一个域名需要消耗的积分数量</small>
              </div>
              
              <div class="form-group col-md-6">
                <label for="check_mode">检测模式</label>
                <select class="form-control" id="check_mode" name="check_mode">
                  <option value="0" <?php echo $conf['check_mode']==0?'selected':''?>>会员模式 (仅会员可使用检测功能)</option>
                  <option value="1" <?php echo $conf['check_mode']==1?'selected':''?>>积分模式 (消耗积分进行检测)</option>
                </select>
                <small class="form-help">选择域名检测功能的使用模式</small>
              </div>
              
              <div class="form-group col-md-12">
                <button type="submit" name="submit" class="btn btn-primary">保存设置</button>
              </div>
            </form>
            
          </div>
        </div>
      </div>
    </div>
  </div>

  <script type="text/javascript" src="../static/admin/js/jquery.min.js"></script>
  <script type="text/javascript" src="../static/admin/js/popper.min.js"></script>
  <script type="text/javascript" src="../static/admin/js/bootstrap.min.js"></script>
  <script type="text/javascript" src="../static/admin/js/main.min.js"></script>
</body>
</html> 