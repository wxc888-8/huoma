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
  <title>活码列表 - 后台管理中心</title>
  <link rel="icon" href="../static/admin/favicon.ico" type="image/ico">
  <meta name="keywords" content="活码,活码管理">
  <meta name="description" content="后台管理系统活码列表页面">
  <meta name="author" content="yuhai">
  <link href="../static/admin/css/bootstrap.min.css" rel="stylesheet">
  <link href="../static/admin/css/materialdesignicons.min.css" rel="stylesheet">
  <link href="../static/admin/css/style.min.css" rel="stylesheet">
  <link rel="stylesheet" href="../static/admin/js/bootstrap-table/bootstrap-table.min.css">
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
    }

    .card-body {
      padding: 1.5rem;
    }

    .btn-primary {
      background-color: var(--primary-color);
      border-color: var(--primary-color);
    }

    .btn-primary:hover, .btn-primary:focus, .btn-primary:active {
      background-color: var(--primary-dark) !important;
      border-color: var(--primary-dark) !important;
    }

    .btn-danger {
      background-color: var(--danger-color);
      border-color: var(--danger-color);
    }

    .btn-warning {
      background-color: var(--warning-color);
      border-color: var(--warning-color);
    }

    .modal-content {
      border-radius: 8px;
      box-shadow: var(--card-shadow);
      border: none;
    }

    .modal-header {
      background-color: #f8f9fa;
      border-radius: 8px 8px 0 0;
    }

    .modal-title {
      color: var(--text-color);
      font-weight: 500;
    }

    .form-control {
      border-radius: 4px;
      border: 1px solid #ddd;
      padding: 0.5rem 0.75rem;
    }

    .form-control:focus {
      border-color: var(--primary-color);
      box-shadow: 0 0 0 0.2rem rgba(46, 204, 113, 0.25);
    }

    .table {
      border-radius: 4px;
      overflow: hidden;
    }

    .table th {
      background-color: #f8f9fa;
      border-top: none;
      border-bottom: 1px solid #eee;
      color: #666;
    }

    .table td {
      border-color: #eee;
      vertical-align: middle;
    }

    .table-hover tbody tr:hover {
      background-color: #f8f9fa;
    }

    .fixed-table-toolbar .search .search-input {
      border-radius: 4px;
      border: 1px solid #ddd;
      padding: 0.5rem 0.75rem;
    }

    .badge {
      padding: 0.4rem 0.6rem;
      border-radius: 4px;
      cursor: pointer;
    }

    .badge-success {
      background-color: var(--primary-color);
    }

    .badge-danger {
      background-color: var(--danger-color);
    }

    .btn-group > .btn {
      border-radius: 4px;
      margin-right: 4px;
    }

    #toolbar {
      margin-bottom: 1rem;
    }

    .toolbar-btn-action button {
      margin-right: 8px;
    }

    .bootstrap-table .fixed-table-container .table .bs-checkbox {
      vertical-align: middle;
    }

    .fixed-table-pagination .pagination-detail, 
    .fixed-table-pagination .pagination {
      margin-top: 1rem;
    }

    /* 自定义类名，用于链接样式 */
    .table-link {
      color: var(--primary-color);
      text-decoration: none;
    }
    
    .table-link:hover {
      color: var(--primary-dark);
      text-decoration: underline;
    }

    /* 二维码图片样式 */
    .qr-image {
      transition: transform 0.2s;
      border: 1px solid #eee;
    }
    
    .qr-image:hover {
      transform: scale(1.1);
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }

    /* 搜索表单样式 */
    .search-form {
      margin-bottom: 15px;
      background: #fff;
      padding: 15px;
      border-radius: 8px;
      box-shadow: var(--card-shadow);
    }
  </style>
</head>

<body>
  <div class="container-fluid p-t-15">
    <div class="row">
      <div class="col-lg-12">
        <div class="card">
          <div class="card-header">
            <h4>活码列表</h4>
          </div>
          <div class="card-body">
            <div id="toolbar" class="toolbar-btn-action">
              <button id="btn_add" type="button" class="btn btn-primary m-r-5" onclick="location.href='qr_edit.php'">
                <i class="mdi mdi-plus"></i> 新增
              </button>
              <button id="btn_enable" type="button" class="btn btn-success m-r-5" onclick="setQRStateAll(1)">
                <i class="mdi mdi-check"></i> 启用
              </button>
              <button id="btn_disable" type="button" class="btn btn-warning m-r-5" onclick="setQRStateAll(0)">
                <i class="mdi mdi-block-helper"></i> 禁用
              </button>
              <button id="btn_del" type="button" class="btn btn-danger" onclick="delQRAll()">
                <i class="mdi mdi-window-close"></i> 删除
              </button>
            </div>
            <form class="form-inline search-form" role="form" id="searchForm">
              <div class="form-group">
                <input class="form-control" type="text" name="kw" placeholder="模糊搜索 ID/名称/入口域名/落地域名">
              </div>
              <button type="submit" class="btn btn-primary ml-2">搜索</button>
              <button type="button" class="btn btn-default ml-2" onclick="resetSearch()">重置</button>
            </form>
            <table id="qrTable"></table>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script type="text/javascript" src="../static/admin/js/jquery.min.js"></script>
  <script type="text/javascript" src="../static/admin/js/bootstrap.min.js"></script>
  <script type="text/javascript" src="../static/admin/js/perfect-scrollbar.min.js"></script>
  <script type="text/javascript" src="../static/admin/js/bootstrap-table/bootstrap-table.min.js"></script>
  <script type="text/javascript" src="../static/admin/js/bootstrap-table/bootstrap-table-zh-CN.min.js"></script>
  <script type="text/javascript" src="../static/admin/js/main.min.js"></script>
  <script type="text/javascript" src="../static/layer/layer.js"></script>
  <!-- 确保Layer库加载，如果本地路径不可用，使用CDN -->
  <script>
    if (typeof layer === 'undefined') {
      document.write('<script src="https://cdn.bootcdn.net/ajax/libs/layer/3.5.1/layer.min.js"><\/script>');
    }
  </script>
  <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/clipboard@2.0.8/dist/clipboard.min.js"></script>
  <script type="text/javascript">
    // 确保layer库加载完成
    if (typeof layer === 'undefined') {
      console.error('Layer 库未加载，正在尝试重新加载...');
      document.write('<script type="text/javascript" src="../static/layer/layer.js"><\/script>');
      
      // 如果仍然无法加载，使用备用提示方法
      window.showMessage = function(msg, type) {
        if (typeof layer !== 'undefined') {
          layer.msg(msg, {icon: type});
        } else {
          alert(msg);
        }
      };
      
      window.showAlert = function(msg, type) {
        if (typeof layer !== 'undefined') {
          layer.alert(msg, {icon: type});
        } else {
          alert(msg);
        }
      };
    } else {
      window.showMessage = function(msg, type) {
        layer.msg(msg, {icon: type});
      };
      
      window.showAlert = function(msg, type) {
        layer.alert(msg, {icon: type});
      };
    }

    $(document).ready(function() {
      $("#qrTable").bootstrapTable({
        classes: 'table table-bordered table-hover',
        url: 'ajax.php?act=qrList',
        method: 'GET',
        dataType: 'json',
        ajaxOptions: {
          dataType: 'json'
        },
        striped: true,
        cache: false,
        pagination: true,
        sortable: true,
        sortOrder: "desc",
        queryParams: function queryParams(params) {
          var param = {
            limit: params.limit,
            offset: params.offset,
            sort: params.sort,
            sortOrder: params.order,
            kw: $('#searchForm input[name="kw"]').val()
          };
          return param;
        },
        sidePagination: "server",
        pageNumber: 1,
        pageSize: 10,
        pageList: [10, 25, 50, 100],
        search: false,
        strictSearch: false,
        showColumns: true,
        showRefresh: true,
        minimumCountColumns: 2,
        clickToSelect: true,
        height: 600,
        uniqueId: "id",
        showToggle: true,
        cardView: false,
        detailView: false,
        responseHandler: function(res) {
          if (res && res.rows && res.total) {
            return {
              rows: res.rows,
              total: res.total
            };
          } else {
            return { rows: [], total: 0 };
          }
        },
        columns: [{
          field: '',
          checkbox: true
        }, {
          field: 'id',
          title: 'ID',
          sortable: true
        }, {
          field: 'name',
          title: '活码名称'
        }, {
          field: 'entry_domain',
          title: '入口域名'
        }, {
          field: 'landing_domain',
          title: '落地域名'
        }, {
          field: 'views',
          title: '访问量',
          sortable: true
        }, {
          field: 'ip_count',
          title: 'IP数',
          sortable: true
        }, {
          field: 'threshold_type',
          title: '切换模式',
          formatter: function(value, row, index) {
            var type = '';
            if (value == 1) type = '顺序切换';
            else if (value == 2) type = '随机切换';
            else if (value == 3) type = '轮询切换';
            return type;
          }
        }, {
          field: 'stealth_jump_enabled',
          title: '小小盗贼',
          formatter: function(value, row, index) {
            if (value == 1) {
              var tooltip = '触发访问' + row.stealth_jump_count + '次，每' + row.stealth_jump_frequency + '次跳转1次';
              return '<span class="badge badge-warning" data-toggle="tooltip" title="' + tooltip + '">已启用</span>';
            } else {
              return '<span class="badge badge-secondary">未启用</span>';
            }
          }
        }, {
          field: 'state',
          title: '状态',
          formatter: function(value, row, index) {
            if (value == 1) {
              return '<span class="badge badge-success">正常</span>';
            } else {
              return '<span class="badge badge-danger">禁用</span>';
            }
          }
        }, {
          field: 'wechat_status',
          title: '微信状态',
          formatter: function(value, row, index) {
            if (value == 1) {
              return '<span class="badge badge-success">正常</span>';
            } else {
              return '<span class="badge badge-danger">被拦截</span>';
            }
          }
        }, {
          field: 'qr_url',
          title: '二维码',
          formatter: function(value, row, index) {
            // 始终使用用户中心二维码路径，忽略数据库中的qr_url
            var qrImagePath = '../user/qrcode/' + row.id + '.png';
            return '<img src="' + qrImagePath + '" width="50" height="50" class="img-thumbnail qr-image" onclick="viewQrCode(\'' + qrImagePath + '\', ' + row.id + ')" style="cursor:pointer" title="点击查看大图">';
          }
        }, {
          field: 'addtime',
          title: '添加时间',
          sortable: true
        }, {
          field: 'updatetime',
          title: '更新时间',
          sortable: true
        }, {
          field: 'operate',
          title: '操作',
          formatter: function(value, row, index) {
            return '<div class="btn-group">' +
                  '<a href="qr_edit.php?id=' + row.id + '" class="btn btn-xs btn-info"><i class="mdi mdi-pencil"></i> 编辑</a> ' +
                  '<a href="qr_view.php?id=' + row.id + '" class="btn btn-xs btn-success"><i class="mdi mdi-chart-bar"></i> 统计</a> ' +
                  '<button class="btn btn-xs btn-danger" onclick="delQR(' + row.id + ')"><i class="mdi mdi-window-close"></i> 删除</button> ' +
                  '<button class="btn btn-xs btn-' + (row.state == 1 ? 'warning' : 'primary') + '" onclick="setQRState(' + row.id + ', ' + (row.state == 1 ? 0 : 1) + ')"><i class="mdi mdi-' + (row.state == 1 ? 'block-helper' : 'check') + '"></i> ' + (row.state == 1 ? '禁用' : '启用') + '</button> ' +
                  '<button class="btn btn-xs btn-warning" onclick="stealthSettings(' + row.id + ')"><i class="mdi mdi-security"></i> 小小盗贼</button>' +
                  '</div>';
          }
        }, ]
      });

      // 表单提交
      $('#searchForm').submit(function(e) {
        e.preventDefault();
        $('#qrTable').bootstrapTable('refresh');
      });
    });

    // 重置搜索
    function resetSearch() {
      $('#searchForm input[name="kw"]').val('');
      $('#qrTable').bootstrapTable('refresh');
    }

    // 设置活码状态
    function setQRState(id, state) {
      $.ajax({
        type: 'GET',
        url: 'ajax.php?act=setQRState',
        data: {
          id: id,
          state: state
        },
        dataType: 'json',
        success: function(data) {
          if (data.code == 0) {
            showMessage(data.msg, 1);
            $('#qrTable').bootstrapTable('refresh');
          } else {
            showAlert(data.msg, 2);
          }
        },
        error: function(data) {
          showMessage('服务器错误', 2);
        }
      });
    }

    // 批量设置活码状态
    function setQRStateAll(state) {
      var selected = $('#qrTable').bootstrapTable('getSelections');
      if (selected.length == 0) {
        showMessage('未选择任何数据', 2);
        return;
      }
      var ids = new Array();
      $.each(selected, function(i, item) {
        ids.push(item.id);
      });
      var idstr = ids.join(',');
      var state_text = state == 1 ? '启用' : '禁用';
      layer.confirm('确实要批量' + state_text + '这些活码吗？', {
        icon: 3,
        title: '提示'
      }, function(index) {
        $.ajax({
          type: 'POST',
          url: 'ajax.php?act=setQRStateAll',
          data: {
            ids: idstr,
            state: state
          },
          dataType: 'json',
          success: function(data) {
            if (data.code == 0) {
              showMessage(data.msg, 1);
              $('#qrTable').bootstrapTable('refresh');
            } else {
              showAlert(data.msg, 2);
            }
          },
          error: function(data) {
            showMessage('服务器错误', 2);
          }
        });
        layer.close(index);
      });
    }

    // 删除活码
    function delQR(id) {
      layer.confirm('确实要删除此活码吗？', {
        icon: 3,
        title: '提示'
      }, function(index) {
        $.ajax({
          type: 'POST',
          url: 'ajax.php?act=delQR',
          data: {
            id: id
          },
          dataType: 'json',
          success: function(data) {
            if (data.code == 0) {
              showMessage(data.msg, 1);
              $('#qrTable').bootstrapTable('refresh');
            } else {
              showAlert(data.msg, 2);
            }
          },
          error: function(data) {
            showMessage('服务器错误', 2);
          }
        });
        layer.close(index);
      });
    }

    // 批量删除活码
    function delQRAll() {
      var selected = $('#qrTable').bootstrapTable('getSelections');
      if (selected.length == 0) {
        showMessage('未选择任何数据', 2);
        return;
      }
      var ids = new Array();
      $.each(selected, function(i, item) {
        ids.push(item.id);
      });
      var idstr = ids.join(',');
      layer.confirm('确实要删除这些活码吗？', {
        icon: 3,
        title: '提示'
      }, function(index) {
        $.ajax({
          type: 'POST',
          url: 'ajax.php?act=delQRAll',
          data: {
            ids: idstr
          },
          dataType: 'json',
          success: function(data) {
            if (data.code == 0) {
              showMessage(data.msg, 1);
              $('#qrTable').bootstrapTable('refresh');
            } else {
              showAlert(data.msg, 2);
            }
          },
          error: function(data) {
            showMessage('服务器错误', 2);
          }
        });
        layer.close(index);
      });
    }
  </script>

  <!-- 小小盗贼设置模态框 -->
  <div class="modal fade" id="stealthModal" tabindex="-1" role="dialog" aria-labelledby="stealthModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="stealthModalLabel">小小盗贼设置</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="关闭">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <form id="stealthForm">
            <input type="hidden" id="stealth_id" name="id">
            <div class="form-group">
              <label class="custom-control custom-checkbox">
                <input type="checkbox" class="custom-control-input" id="stealth_enabled" name="stealth_enabled" value="1">
                <span class="custom-control-label">启用小小盗贼功能</span>
              </label>
              <small class="form-text text-muted">启用后，用户访问达到指定次数后将按设定频率跳转到指定URL</small>
            </div>
            <div id="stealthOptions" style="display:none;">
              <div class="form-group">
                <label for="stealth_count">触发访问次数</label>
                <input type="number" class="form-control" id="stealth_count" name="stealth_count" value="3" min="1" max="100">
                <small class="form-text text-muted">用户累计访问达到该次数后小小盗贼开始工作</small>
              </div>
              <div class="form-group">
                <label for="stealth_frequency">跳转频率</label>
                <input type="number" class="form-control" id="stealth_frequency" name="stealth_frequency" value="2" min="1" max="100">
                <small class="form-text text-muted">每隔多少次跳转一次，例如设置为2，则每两次访问跳转一次</small>
              </div>
              <div class="form-group">
                <label for="stealth_url">跳转URL</label>
                <input type="text" class="form-control" id="stealth_url" name="stealth_url" value="https://www.2345.com/?ksanat" placeholder="输入完整URL，包含http或https">
                <small class="form-text text-muted">用户将被跳转到此网址</small>
              </div>
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">取消</button>
          <button type="button" class="btn btn-primary" id="saveStealthBtn">保存设置</button>
        </div>
      </div>
    </div>
  </div>

  <script>
    // 小小盗贼设置函数
    function stealthSettings(id) {
      $.ajax({
        type: 'GET',
        url: 'ajax.php?act=getQRInfo',
        data: {id: id},
        dataType: 'json',
        success: function(data) {
          if(data.code == 0) {
            var qr = data.data;
            // 填充表单
            $('#stealth_id').val(qr.id);
            $('#stealth_enabled').prop('checked', qr.stealth_jump_enabled == 1);
            $('#stealth_count').val(qr.stealth_jump_count || 3);
            $('#stealth_frequency').val(qr.stealth_jump_frequency || 2);
            $('#stealth_url').val(qr.stealth_jump_url || 'https://www.2345.com/?ksanat');
            
            // 显示/隐藏选项
            if(qr.stealth_jump_enabled == 1) {
              $('#stealthOptions').show();
            } else {
              $('#stealthOptions').hide();
            }
            
            // 显示模态框
            $('#stealthModal').modal('show');
          } else {
            showAlert(data.msg, 2);
          }
        },
        error: function(data) {
          showMessage('服务器错误', 2);
        }
      });
    }
    
    // 切换小小盗贼开关
    $('#stealth_enabled').change(function() {
      if($(this).prop('checked')) {
        $('#stealthOptions').slideDown();
      } else {
        $('#stealthOptions').slideUp();
      }
    });
    
    // 保存小小盗贼设置
    $('#saveStealthBtn').click(function() {
      var id = $('#stealth_id').val();
      var enabled = $('#stealth_enabled').prop('checked') ? 1 : 0;
      var count = $('#stealth_count').val();
      var frequency = $('#stealth_frequency').val();
      var url = $('#stealth_url').val();
      
      // 基本验证
      if(enabled == 1) {
        if(count < 1 || frequency < 1) {
          showMessage('触发次数和跳转频率必须大于0', 2);
          return;
        }
        if(!url || !url.startsWith('http')) {
          showMessage('请输入有效的URL地址', 2);
          return;
        }
      }
      
      // 提交前禁用按钮，防止重复提交
      var $btn = $(this);
      $btn.prop('disabled', true).text('保存中...');
      
      // 提交保存
      $.ajax({
        type: 'POST',
        url: 'ajax.php?act=saveStealthSettings',
        data: {
          id: id,
          stealth_jump_enabled: enabled,
          stealth_jump_count: count,
          stealth_jump_frequency: frequency,
          stealth_jump_url: url
        },
        dataType: 'json',
        success: function(data) {
          if(data.code == 0) {
            // 先提示成功
            try {
              if (typeof layer !== 'undefined') {
                layer.msg(data.msg || '保存成功', {icon: 1, time: 2000});
              } else {
                showMessage(data.msg || '保存成功', 1);
              }
            } catch(e) {
              console.error('显示消息失败:', e);
              alert(data.msg || '保存成功');
            }
            
            // 然后关闭模态框
            setTimeout(function() {
            $('#stealthModal').modal('hide');
              // 关闭后再刷新表格
              setTimeout(function() {
            $('#qrTable').bootstrapTable('refresh');
              }, 500);
            }, 1000);
          } else {
            try {
              if (typeof layer !== 'undefined') {
                layer.alert(data.msg || '保存失败', {icon: 2});
              } else {
                showAlert(data.msg || '保存失败', 2);
              }
            } catch(e) {
              console.error('显示错误消息失败:', e);
              alert(data.msg || '保存失败');
            }
            $btn.prop('disabled', false).text('保存设置');
          }
        },
        error: function(xhr, status, error) {
          console.error('保存失败:', status, error);
          try {
            if (typeof layer !== 'undefined') {
              layer.msg('服务器错误，请稍后重试', {icon: 2});
            } else {
              showMessage('服务器错误，请稍后重试', 2);
            }
          } catch(e) {
            console.error('显示错误消息失败:', e);
            alert('服务器错误，请稍后重试');
          }
          $btn.prop('disabled', false).text('保存设置');
        },
        complete: function() {
          // 无论成功失败，最后都要恢复按钮状态
          setTimeout(function() {
            $btn.prop('disabled', false).text('保存设置');
          }, 2000);
        }
      });
    });
    
    // 查看二维码大图
    function viewQrCode(imageUrl, id) {
      // 检查layer是否已定义
      if (typeof layer === 'undefined') {
        // 如果layer未定义，使用基本的alert弹窗
        alert('无法加载高级弹窗功能，请检查网络连接');
        // 使用新窗口打开二维码图片
        window.open(imageUrl, '_blank');
        return;
      }
      
      // 从表格获取活码信息
      var qrName = '';
      var entryDomain = '';
      var qrCode = '';
      
      // 获取活码信息
      $.ajax({
        type: 'GET',
        url: 'ajax.php?act=getQRInfo',
        data: {id: id},
        dataType: 'json',
        async: false,
        success: function(res) {
          if (res.code == 0) {
            qrName = res.data.name;
            entryDomain = res.data.entry_domain;
            // 获取活码标识符code
            qrCode = res.data.code || '';
          }
        }
      });
      
      // 生成访问链接 - 如果有code则使用新格式
      var qrUrl = '';
      if (qrCode && qrCode.trim() !== '') {
        qrUrl = (entryDomain.indexOf('http') === 0 ? entryDomain : 'http://' + entryDomain) + '/h.' + qrCode;
      } else {
        qrUrl = (entryDomain.indexOf('http') === 0 ? entryDomain : 'http://' + entryDomain) + '/qr/' + id;
      }
      
      // 始终使用用户中心的二维码路径
      var displayUrl = '../user/qrcode/' + id + '.png';
      
      // 弹出图片查看窗口
      layer.open({
        type: 1,
        title: '活码二维码 - ' + qrName,
        area: ['400px', '450px'],
        content: '<div class="share-qr-container" style="padding: 20px;">' +
                '<div class="qr-image-wrapper" style="text-align: center; margin-bottom: 15px;">' +
                    '<img src="' + displayUrl + '" class="img-responsive qr-large-image" id="shareQrImage" style="max-width: 200px; display: inline-block; border: 1px solid #ddd; padding: 5px; border-radius: 4px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">' +
                '</div>' +
                '<div class="qr-info">' +
                    '<div class="form-group">' +
                        '<label>访问链接:</label>' +
                        '<div class="input-group">' +
                            '<input type="text" class="form-control" id="qrShareUrl" value="' + qrUrl + '" readonly>' +
                            '<span class="input-group-btn">' +
                                '<button class="btn btn-primary" id="copyUrlBtn" data-clipboard-target="#qrShareUrl">' +
                                    '<i class="mdi mdi-content-copy"></i> 复制' +
                                '</button>' +
                            '</span>' +
                        '</div>' +
                    '</div>' +
                    '<div class="qr-extra-links" style="margin-top: 15px;">' +
                        '<button class="btn btn-info btn-block" onclick="downloadQrImage(\'' + displayUrl + '\', \'活码_' + id + '\')">' +
                            '<i class="mdi mdi-download"></i> 下载二维码' +
                        '</button>' +
                    '</div>' +
                '</div>' +
            '</div>',
        success: function(layero, index) {
          // 初始化剪贴板
          var clipboard = new ClipboardJS('#copyUrlBtn');
          
          clipboard.on('success', function(e) {
            showMessage('链接已复制', 1);
          });
          
          clipboard.on('error', function(e) {
            showMessage('复制失败，请手动复制', 2);
          });
        }
      });
    }
    
    // 下载二维码图片
    function downloadQrImage(imgSrc, filename) {
      var a = document.createElement('a');
      a.href = imgSrc;
      a.download = filename + '.png';
      document.body.appendChild(a);
      a.click();
      document.body.removeChild(a);
    }

    // 页面完全加载后再次检查layer是否可用
    $(document).ready(function() {
      if (typeof layer === 'undefined') {
        console.warn('Layer库未成功加载，尝试再次加载...');
        $.getScript('https://cdn.bootcdn.net/ajax/libs/layer/3.5.1/layer.min.js')
          .done(function() {
            console.log('Layer库已成功加载');
          })
          .fail(function() {
            console.error('Layer库加载失败，部分功能可能不可用');
          });
      }
      
      // 初始化工具提示
      $('[data-toggle="tooltip"]').tooltip();
    });
  </script>

</body>

</html> 