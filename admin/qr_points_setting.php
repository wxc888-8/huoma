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
  <title>活码积分设置 - 后台管理中心</title>
  <link rel="icon" href="../static/admin/favicon.ico" type="image/ico">
  <meta name="keywords" content="活码,积分设置,积分消费">
  <meta name="description" content="后台管理系统活码积分设置页面">
  <meta name="author" content="yuhai">
  <link href="../static/admin/css/bootstrap.min.css" rel="stylesheet">
  <link href="../static/admin/css/materialdesignicons.min.css" rel="stylesheet">
  <link href="../static/admin/css/style.min.css" rel="stylesheet">
</head>

<body>
  <div class="container-fluid p-t-15">
    <div class="row">
      <div class="col-lg-12">
        <div class="card">
          <div class="card-header">
            <h4>活码积分设置</h4>
          </div>
          <div class="card-body">
            <form id="pointsForm" method="post" class="row">
              <div class="form-group col-md-12">
                <label for="qr_use_points">使用活码扣除积分</label>
                <input type="number" class="form-control" id="qr_use_points" name="qr_use_points" value="<?php echo $conf['qr_use_points']?$conf['qr_use_points']:0; ?>" placeholder="每次使用活码扣除的积分数量">
                <small class="form-text text-muted">设置为0则不扣除积分</small>
              </div>
              
              <div class="form-group col-md-12">
                <div class="alert alert-info">
                  <p><i class="mdi mdi-information-outline"></i> 积分域名说明：</p>
                  <ul>
                    <li>积分域名用于普通用户使用活码，需要消耗积分</li>
                    <li>当用户在积分域名下使用活码时，系统将根据设置扣除相应积分</li>
                    <li>付费域名不消耗积分，可用于VIP用户或高级服务</li>
                  </ul>
                </div>
              </div>
              
              <div class="form-group col-md-12">
                <button type="button" id="saveBtn" class="btn btn-primary">保存设置</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script type="text/javascript" src="../static/admin/js/jquery.min.js"></script>
  <script type="text/javascript" src="../static/admin/js/bootstrap.min.js"></script>
  <script type="text/javascript" src="../static/admin/js/perfect-scrollbar.min.js"></script>
  <script type="text/javascript" src="../static/admin/js/main.min.js"></script>
  <script type="text/javascript" src="../static/layer/layer.js"></script>
  <script type="text/javascript">
    $(document).ready(function() {
      $("#saveBtn").click(function() {
        var qr_use_points = $("#qr_use_points").val();
        
        if(qr_use_points < 0) {
          layer.msg('积分设置不能为负数', {icon: 2});
          return;
        }
        
        var index = layer.load(2, {shade: [0.1, '#fff']});
        $.ajax({
          type: 'POST',
          url: 'ajax.php?act=saveQRPointsSettings',
          data: {
            qr_use_points: qr_use_points
          },
          dataType: 'json',
          success: function(data) {
            layer.close(index);
            if(data.code == 0) {
              layer.msg(data.msg, {icon: 1});
            } else {
              layer.alert(data.msg, {icon: 2});
            }
          },
          error: function(data) {
            layer.close(index);
            layer.msg('服务器错误', {icon: 2});
          }
        });
      });
    });
  </script>
</body>

</html> 