<?php
include('../includes/common.php');
if ($islogin != 1) {
  exit('<script language=\'javascript\'>window.location.href=\'./login.php\';</script>');
}

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$title = $id ? '编辑活码' : '添加活码';

// 获取活码数据（如果是编辑模式）
$qr_data = [];
if ($id) {
    $qr_info = $DB->get_row("SELECT * FROM dwz_qrcode WHERE id='$id' LIMIT 1");
    if ($qr_info) {
        $qr_data = $qr_info;
        
        // 获取活码对应的链接数据
        $qr_items = [];
        $rs = $DB->query("SELECT * FROM dwz_qrcode_data WHERE qid='$id' ORDER BY sort ASC");
        while($row = $DB->fetch($rs)) {
            $qr_items[] = $row;
        }
        $qr_data['items'] = $qr_items;
    } else {
        exit("<script language='javascript'>alert('活码不存在');window.location.href='qr_list.php';</script>");
    }
}
?>
<!DOCTYPE html>
<html lang="zh">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" />
  <title><?php echo $title; ?> - 后台管理中心</title>
  <link rel="icon" href="../static/admin/favicon.ico" type="image/ico">
  <meta name="keywords" content="活码,活码管理">
  <meta name="description" content="后台管理系统活码编辑页面">
  <meta name="author" content="yuhai">
  <link href="../static/admin/css/bootstrap.min.css" rel="stylesheet">
  <link href="../static/admin/css/materialdesignicons.min.css" rel="stylesheet">
  <link href="../static/admin/css/style.min.css" rel="stylesheet">
  <style>
    .card-body {
      padding: 20px;
    }
    .url-item {
      margin-bottom: 20px;
      border: 1px solid #eee;
      border-radius: 6px;
      background-color: #fff;
      box-shadow: 0 1px 2px rgba(0,0,0,0.05);
      position: relative;
      transition: all 0.3s ease;
    }
    .url-item:hover {
      box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }
    .url-item-header {
      padding: 8px 12px;
      background-color: #f8f8f8;
      border-bottom: 1px solid #eee;
      border-radius: 6px 6px 0 0;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }
    .url-item-content {
      padding: 15px;
    }
    .url-index {
      display: inline-block;
      width: 24px;
      height: 24px;
      line-height: 24px;
      text-align: center;
      background: #33cabb;
      color: white;
      border-radius: 50%;
      font-weight: bold;
    }
    .form-help-text {
      color: #737373;
      font-size: 12px;
      margin-top: 5px;
    }
    /* 图片上传样式 */
    .image-upload-container {
      display: flex;
      align-items: flex-start;
    }
    .image-upload-wrapper {
      position: relative;
      width: 120px;
      height: 120px;
      border: 2px dashed #ddd;
      border-radius: 6px;
      background-color: #f9f9f9;
      cursor: pointer;
      overflow: hidden;
      transition: all 0.2s ease;
    }
    .image-upload-wrapper:hover {
      border-color: #33cabb;
      background-color: #f5f5f5;
    }
    .image-upload-placeholder {
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      color: #777;
    }
    .image-upload-placeholder i {
      font-size: 32px;
      margin-bottom: 5px;
    }
    .image-upload-input {
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      opacity: 0;
      cursor: pointer;
      z-index: 2;
    }
    .image-preview {
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      object-fit: cover;
      z-index: 1;
    }
    .image-clear-btn {
      margin-left: 10px;
    }
  </style>
</head>

<body>
  <div class="container-fluid p-t-15">
    <div class="row">
      <div class="col-lg-12">
        <div class="card">
          <div class="card-header">
            <h4><?php echo $title; ?></h4>
          </div>
          <div class="card-body">
            <form id="qrForm" method="post" class="row">
              <input type="hidden" name="id" value="<?php echo $id; ?>">
              
              <!-- 左侧面板 -->
              <div class="col-md-6">
                <div class="card">
                  <div class="card-header">
                    <h4>基本信息</h4>
                  </div>
                  <div class="card-body">
                    <div class="form-group">
                      <label for="name">活码名称</label>
                      <input type="text" class="form-control" id="name" name="name" placeholder="请输入活码名称" required>
                    </div>
                    
                    <div class="form-group">
                      <label for="entry_domain">入口域名</label>
                      <select class="form-control" id="entry_domain" name="entry_domain" required>
                        <option value="">请选择入口域名</option>
                      </select>
                    </div>
                    
                    <div class="form-group">
                      <label for="landing_domain">落地域名</label>
                      <select class="form-control" id="landing_domain" name="landing_domain" required>
                        <option value="">请选择落地域名</option>
                      </select>
                    </div>
                    
                    <div class="form-group">
                      <label for="uid">绑定用户ID</label>
                      <input type="number" class="form-control" id="uid" name="uid" placeholder="绑定到指定用户(可选)" value="0">
                      <div class="form-help-text">如不绑定用户请留空或填0</div>
                    </div>
                  </div>
                </div>
              </div>
              
              <!-- 右侧面板 -->
              <div class="col-md-6">
                <div class="card">
                  <div class="card-header">
                    <h4>切换设置</h4>
                  </div>
                  <div class="card-body">
                    <div class="form-group">
                      <label for="jump_time">自动跳转时间</label>
                      <div class="input-group">
                        <input type="number" class="form-control" id="jump_time" name="jump_time" min="0" max="60" value="0" placeholder="自动跳转等待时间">
                        <div class="input-group-append">
                          <span class="input-group-text">秒</span>
                        </div>
                      </div>
                      <div class="form-help-text">设置为0表示不自动跳转</div>
                    </div>
                    
                    <div class="form-group">
                      <label>安全认证提示</label>
                      <div>
                        <label class="lyear-radio radio-inline radio-primary">
                          <input type="radio" name="show_safe" value="1" checked><span>显示</span>
                        </label>
                        <label class="lyear-radio radio-inline radio-primary">
                          <input type="radio" name="show_safe" value="0"><span>隐藏</span>
                        </label>
                      </div>
                      <div class="form-help-text">是否在跳转页面显示安全认证提示</div>
                    </div>
                    
                    <div class="form-group">
                      <label for="threshold_type">切换模式</label>
                      <select class="form-control" id="threshold_type" name="threshold_type">
                        <option value="1">顺序切换</option>
                        <option value="2">随机切换</option>
                        <option value="3">轮询切换</option>
                      </select>
                    </div>
                    
                    <div class="form-group">
                      <label>循环模式</label>
                      <div>
                        <label class="lyear-checkbox checkbox-inline checkbox-primary">
                          <input type="checkbox" name="infinite_loop" value="1"><span>开启无限循环</span>
                        </label>
                      </div>
                      <div class="form-help-text">启用后，阈值用完后会重新从第一个开始</div>
                    </div>
                  </div>
                </div>
              </div>
              
              <!-- 内码跳转列表 -->
              <div class="col-md-12 mt-3">
                <div class="card">
                  <div class="card-header">
                    <h4>内码跳转网址列表</h4>
                  </div>
                  <div class="card-body">
                    <div id="url_list">
                      <!-- 这里会通过JS动态添加URL项 -->
                    </div>
                    <div class="text-center mt-3">
                      <button type="button" class="btn btn-primary" onclick="addUrlItem()">
                        <i class="mdi mdi-plus"></i> 添加跳转网址
                      </button>
                    </div>
                  </div>
                </div>
              </div>
              
              <!-- 其他设置 -->
              <div class="col-md-12 mt-3">
                <div class="card">
                  <div class="card-header">
                    <h4>其他设置</h4>
                  </div>
                  <div class="card-body">
                    <div class="form-group">
                      <label for="remarks">备注说明</label>
                      <textarea class="form-control" id="remarks" name="remarks" rows="3" placeholder="请输入备注说明（可选）"></textarea>
                    </div>
                  </div>
                </div>
              </div>
              
              <!-- 提交按钮 -->
              <div class="col-md-12 mt-3">
                <button type="submit" class="btn btn-primary">
                  <i class="mdi mdi-check"></i> 保存
                </button>
                <a href="qr_list.php" class="btn btn-default">
                  <i class="mdi mdi-arrow-left"></i> 返回
                </a>
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
    // 载入入口域名和落地域名数据
    function loadDomains() {
      // 入口域名
      $.ajax({
        url: 'ajax.php?act=getDomainList&type=entry',
        type: 'GET',
        dataType: 'json',
        success: function(res) {
          if (res && res.rows && res.rows.length) {
            var $select = $('#entry_domain');
            $.each(res.rows, function(i, item) {
              $select.append($('<option></option>').val(item.domain).text(item.domain + (item.is_paid == 1 ? ' [付费域名]' : ' [积分域名]')));
            });
            
            <?php if(isset($qr_data['entry_domain'])): ?>
            $select.val('<?php echo $qr_data['entry_domain']; ?>');
            <?php endif; ?>
          }
        }
      });
      
      // 落地域名
      $.ajax({
        url: 'ajax.php?act=getDomainList&type=landing',
        type: 'GET',
        dataType: 'json',
        success: function(res) {
          if (res && res.rows && res.rows.length) {
            var $select = $('#landing_domain');
            $.each(res.rows, function(i, item) {
              $select.append($('<option></option>').val(item.domain).text(item.domain + (item.is_paid == 1 ? ' [付费域名]' : ' [积分域名]')));
            });
            
            <?php if(isset($qr_data['landing_domain'])): ?>
            $select.val('<?php echo $qr_data['landing_domain']; ?>');
            <?php endif; ?>
          }
        }
      });
    }

    // 添加跳转URL项
    function addUrlItem(index, jump_url, image_url, threshold) {
      // 如果没有指定索引，则使用当前项数量
      if (index === undefined) index = $('.url-item').length + 1;
      
      // 如果没有提供值，使用默认值
      jump_url = jump_url || '';
      image_url = image_url || '';
      threshold = threshold || 100;
      
      // 检查图片URL是否为Base64格式或常规URL
      var hasImage = image_url && (image_url.indexOf('data:image') === 0 || image_url.indexOf('http') === 0);
      
      var html = '<div class="url-item">' +
                 '<div class="url-item-header">' +
                 '<span class="url-index">' + index + '</span>' +
                 '<button type="button" class="btn btn-danger btn-sm" onclick="removeUrlItem(this)">' +
                 '<i class="mdi mdi-delete"></i>' +
                 '</button>' +
                 '</div>' +
                 '<div class="url-item-content">' +
                 '<div class="row">' +
                 '<div class="col-md-7">' +
                 '<div class="form-group">' +
                 '<label><i class="mdi mdi-link"></i> 跳转地址</label>' +
                 '<input type="url" class="form-control" name="jump_url[]" value="' + jump_url + '" placeholder="请输入跳转URL地址" required>' +
                 '</div>' +
                 '</div>' +
                 '<div class="col-md-3">' +
                 '<div class="form-group">' +
                 '<label><i class="mdi mdi-speedometer"></i> 阈值</label>' +
                 '<input type="number" class="form-control" name="threshold[]" min="1" value="' + threshold + '" placeholder="阈值">' +
                 '</div>' +
                 '</div>' +
                 '</div>' +
                 '<div class="row">' +
                 '<div class="col-md-7">' +
                 '<div class="form-group">' +
                 '<label><i class="mdi mdi-image"></i> 展示图片</label>' +
                 '<div class="image-upload-container">' +
                 '<div class="image-upload-wrapper">' +
                 '<div class="image-upload-placeholder"' + (hasImage ? ' style="display:none;"' : '') + '>' +
                 '<i class="mdi mdi-image-outline"></i>' +
                 '<span>点击上传图片</span>' +
                 '</div>' +
                 '<img class="image-preview" src="' + image_url + '"' + (hasImage ? '' : ' style="display:none;"') + '>' +
                 '<input type="file" class="image-upload-input" name="image_file[]" accept="image/*" onchange="previewImage(this)">' +
                 '<input type="hidden" name="image_url[]" value="' + (image_url || '') + '">' +
                 '</div>' +
                 '<button type="button" class="btn btn-xs btn-danger image-clear-btn" onclick="clearImage(this)"' + (hasImage ? '' : ' style="display:none;"') + '>' +
                 '<i class="mdi mdi-close"></i>' +
                 '</button>' +
                 '</div>' +
                 '</div>' +
                 '</div>' +
                 '</div>' +
                 '</div>' +
                 '</div>';
      $('#url_list').append(html);
    }

    // 移除跳转URL项
    function removeUrlItem(btn) {
      // 确保至少保留一个URL项
      if ($('.url-item').length > 1) {
        $(btn).closest('.url-item').remove();
        
        // 重新编号
        $('.url-item').each(function(index) {
          $(this).find('.url-index').text(index + 1);
        });
      } else {
        layer.msg('至少需要保留一个跳转地址', {icon: 2});
      }
    }

    // 预览图片
    function previewImage(input) {
      if (input.files && input.files[0]) {
        var reader = new FileReader();
        var container = $(input).closest('.image-upload-container');
        var preview = container.find('.image-preview');
        var placeholder = container.find('.image-upload-placeholder');
        var clearBtn = container.find('.image-clear-btn');
        var hiddenInput = $(input).next('input[type="hidden"]');
        
        reader.onload = function(e) {
          // 设置隐藏字段的值为base64图片数据（用于表单提交）
          hiddenInput.val(e.target.result);
          
          // 显示预览图，隐藏占位符
          preview.attr('src', e.target.result);
          preview.show();
          placeholder.hide();
          
          // 显示清除按钮
          clearBtn.show();
        };
        
        reader.readAsDataURL(input.files[0]);
      }
    }

    // 清除图片
    function clearImage(btn) {
      var container = $(btn).closest('.image-upload-container');
      var wrapper = container.find('.image-upload-wrapper');
      var input = wrapper.find('input[type="file"]');
      var hiddenInput = wrapper.find('input[type="hidden"]');
      var preview = wrapper.find('.image-preview');
      var placeholder = wrapper.find('.image-upload-placeholder');
      
      // 清除文件输入
      input.val('');
      
      // 清除隐藏值
      hiddenInput.val('');
      
      // 隐藏预览图，显示占位符
      preview.attr('src', '').hide();
      placeholder.show();
      
      // 隐藏清除按钮
      $(btn).hide();
    }

    // 页面加载完成后初始化表单数据
    $(document).ready(function() {
      // 加载域名数据
      loadDomains();
      
      <?php if($id && !empty($qr_data)): ?>
      // 编辑模式：加载现有数据
      $('#name').val('<?php echo addslashes($qr_data['name']); ?>');
      $('#uid').val('<?php echo intval($qr_data['uid']); ?>');
      $('#jump_time').val(<?php echo intval($qr_data['jump_time']); ?>);
      $('input[name="show_safe"][value="<?php echo intval($qr_data['show_safe']); ?>"]').prop('checked', true);
      $('#threshold_type').val(<?php echo intval($qr_data['threshold_type']); ?>);
      $('input[name="infinite_loop"]').prop('checked', <?php echo intval($qr_data['infinite_loop']) ? 'true' : 'false'; ?>);
      $('#remarks').val('<?php echo addslashes($qr_data['remarks']); ?>');
      
      <?php if(!empty($qr_data['items'])): ?>
      // 添加URL项
      <?php foreach($qr_data['items'] as $index => $item): ?>
      addUrlItem(
          <?php echo $index + 1; ?>,
          '<?php echo addslashes($item['jump_url']); ?>',
          <?php echo !empty($item['image_url']) ? "'".addslashes($item['image_url'])."'" : 'null'; ?>,
          <?php echo intval($item['threshold']); ?>
      );
      <?php endforeach; ?>
      <?php else: ?>
      // 如果没有URL项，添加一个默认的
      addUrlItem();
      <?php endif; ?>
      
      <?php else: ?>
      // 新建模式：使用默认值
      addUrlItem(); // 添加一个默认的URL项
      <?php endif; ?>
      
      // 表单提交
      $('#qrForm').submit(function(e) {
        e.preventDefault();
        
        // 表单验证
        if (!validateForm()) {
          return false;
        }
        
        // 使用FormData支持文件上传
        var formData = new FormData(this);
        
        // 显示加载提示
        var loadingIndex = layer.msg('正在保存，请稍候...', {icon: 16, time: 0, shade: 0.3});
        
        $.ajax({
          type: 'POST',
          url: 'ajax.php?act=saveQR',
          data: formData,
          contentType: false,
          processData: false,
          dataType: 'json',
          success: function(res) {
            layer.close(loadingIndex);
            if(res.code == 0) {
              layer.msg('保存成功', {icon: 1});
              setTimeout(function() {
                window.location.href = 'qr_list.php';
              }, 1000);
            } else {
              layer.alert(res.msg, {icon: 2});
            }
          },
          error: function(xhr) {
            layer.close(loadingIndex);
            layer.alert('服务器错误，请稍后再试', {icon: 2});
          }
        });
      });
    });

    // 表单验证
    function validateForm() {
      // 验证活码名称
      if (!$('#name').val()) {
        layer.msg('请输入活码名称', {icon: 2});
        return false;
      }
      
      // 验证入口域名
      if (!$('#entry_domain').val()) {
        layer.msg('请选择入口域名', {icon: 2});
        return false;
      }
      
      // 验证落地页域名
      if (!$('#landing_domain').val()) {
        layer.msg('请选择落地页域名', {icon: 2});
        return false;
      }
      
      // 验证跳转地址
      var hasEmptyUrl = false;
      $('input[name="jump_url[]"]').each(function() {
        if (!$(this).val()) {
          hasEmptyUrl = true;
          return false;
        }
      });
      
      if (hasEmptyUrl) {
        layer.msg('请填写所有跳转地址', {icon: 2});
        return false;
      }
      
      return true;
    }
  </script>
</body>

</html> 