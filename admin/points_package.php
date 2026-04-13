<?php
include('../includes/common.php');
$title = '积分充值设置';
if ($islogin != 1) {
  exit("<script language='javascript'>window.location.href='./login.php';</script>");
}
?>

<!-- 添加必要的CSS库 -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
  :root {
    --wechat-green: #07C160;
    --wechat-dark: #1A1A1A;
    --light-bg: #F7F7F7;
    --sidebar-width: 240px;
    --topbar-height: 60px;
    --border-color: rgba(0, 0, 0, 0.05);
    --text-primary: #333;
    --text-secondary: #666;
    --card-shadow: 0 2px 12px rgba(0, 0, 0, 0.05);
  }

  .content-card {
    background-color: white;
    border-radius: 10px;
    box-shadow: var(--card-shadow);
    margin-bottom: 20px;
    overflow: hidden;
  }

  .content-card-header {
    padding: 15px 20px;
    border-bottom: 1px solid var(--border-color);
    background-color: rgba(0, 0, 0, 0.02);
    display: flex;
    justify-content: space-between;
    align-items: center;
  }

  .content-card-title {
    margin: 0;
    font-size: 16px;
    font-weight: 600;
    color: var(--text-primary);
    display: flex;
    align-items: center;
  }

  .content-card-title i {
    margin-right: 8px;
    color: var(--wechat-green);
  }

  .content-card-body {
    padding: 20px;
  }

  .btn-success {
    background-color: var(--wechat-green);
    border-color: var(--wechat-green);
  }

  .btn-success:hover, .btn-success:focus {
    background-color: #06AA53;
    border-color: #06AA53;
  }

  .btn-primary {
    background-color: #3498db;
    border-color: #3498db;
  }

  .btn-primary:hover, .btn-primary:focus {
    background-color: #2980b9;
    border-color: #2980b9;
  }

  .form-control:focus, .form-select:focus {
    border-color: var(--wechat-green);
    box-shadow: 0 0 0 0.25rem rgba(7, 193, 96, 0.25);
  }

  .table {
    margin-bottom: 0;
  }

  .badge-success {
    background-color: var(--wechat-green);
    color: white;
  }

  .badge-danger {
    background-color: #e74c3c;
    color: white;
  }

  .badge {
    padding: 5px 8px;
    border-radius: 4px;
    font-weight: 500;
  }

  .modal-header {
    background-color: #f8f9fa;
    border-bottom: 1px solid var(--border-color);
  }

  .modal-footer {
    background-color: #f8f9fa;
    border-top: 1px solid var(--border-color);
  }

  .btn-sm {
    padding: 0.25rem 0.5rem;
    font-size: 0.875rem;
    border-radius: 0.2rem;
  }

  .action-buttons .btn {
    margin-right: 5px;
  }

  .loading-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(255, 255, 255, 0.8);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 9999;
  }

  .spinner-border {
    width: 3rem;
    height: 3rem;
  }
</style>

<div class="container-fluid py-3">
  <div class="content-card">
    <div class="content-card-header">
      <h4 class="content-card-title"><i class="bi bi-list-ul"></i> 积分充值套餐管理</h4>
      <button class="btn btn-success btn-sm" onclick="addPackage()"><i class="bi bi-plus-lg me-1"></i>添加套餐</button>
    </div>
    <div class="content-card-body p-0">
      <div class="table-responsive">
        <table class="table table-striped table-hover">
          <thead class="table-light">
            <tr>
              <th>ID</th>
              <th>套餐名称</th>
              <th>积分数量</th>
              <th>价格(元)</th>
              <th>状态</th>
              <th>排序</th>
              <th>创建时间</th>
              <th>操作</th>
            </tr>
          </thead>
          <tbody id="package-list">
            <!-- 数据将通过AJAX加载 -->
            <tr>
              <td colspan="8" class="text-center">
                <div class="spinner-border text-success" role="status">
                  <span class="visually-hidden">加载中...</span>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<!-- 添加/编辑套餐模态框 -->
<div class="modal fade" id="packageModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalTitle">添加套餐</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="package-form">
          <input type="hidden" id="package-id" value="0">
          <div class="mb-3">
            <label class="form-label">套餐名称</label>
            <input type="text" class="form-control" id="package-name" required>
          </div>
          <div class="mb-3">
            <label class="form-label">积分数量</label>
            <input type="number" class="form-control" id="package-points" min="1" required>
          </div>
          <div class="mb-3">
            <label class="form-label">价格(元)</label>
            <input type="number" class="form-control" id="package-price" min="0.01" step="0.01" required>
          </div>
          <div class="mb-3">
            <label class="form-label">排序</label>
            <input type="number" class="form-control" id="package-sort" value="0">
            <div class="form-text text-muted">数字越小越靠前</div>
          </div>
          <div class="mb-3">
            <label class="form-label">状态</label>
            <select class="form-select" id="package-status">
              <option value="1">启用</option>
              <option value="0">禁用</option>
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label">备注说明</label>
            <textarea class="form-control" id="package-remarks" rows="2"></textarea>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">取消</button>
        <button type="button" class="btn btn-success" id="save-package"><i class="bi bi-check-lg me-1"></i>保存</button>
      </div>
    </div>
  </div>
</div>

<!-- 加载遮罩 -->
<div class="loading-overlay" id="loading-overlay" style="display: none;">
  <div class="spinner-border text-success" role="status">
    <span class="visually-hidden">处理中...</span>
  </div>
</div>

<!-- 添加必要的JavaScript库 -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
  // 显示加载中
  function showLoading() {
    $('#loading-overlay').show();
  }
  
  // 隐藏加载中
  function hideLoading() {
    $('#loading-overlay').hide();
  }
  
  // 加载套餐列表
  function loadPackages() {
    showLoading();
    $.ajax({
      url: 'ajax.php?act=get_points_package_list',
      type: 'GET',
      dataType: 'json',
      success: function(res) {
        hideLoading();
        if (res.code === 0) {
          renderPackageList(res.data);
        } else {
          showAlert('error', res.msg || '加载失败');
        }
      },
      error: function() {
        hideLoading();
        showAlert('error', '网络错误，请重试');
      }
    });
  }
  
  // 渲染套餐列表
  function renderPackageList(data) {
    let html = '';
    if (data && data.length > 0) {
      data.forEach(function(item) {
        html += `<tr>
          <td>${item.id}</td>
          <td>${item.name}</td>
          <td>${item.points_num}</td>
          <td>${parseFloat(item.price).toFixed(2)}</td>
          <td>${item.status == 1 
            ? '<span class="badge bg-success">启用</span>' 
            : '<span class="badge bg-danger">禁用</span>'}</td>
          <td>${item.sort}</td>
          <td>${item.addtime}</td>
          <td class="action-buttons">
            <button class="btn btn-primary btn-sm" onclick="editPackage(${item.id})">
              <i class="bi bi-pencil"></i> 编辑
            </button>
            <button class="btn btn-danger btn-sm" onclick="deletePackage(${item.id})">
              <i class="bi bi-trash"></i> 删除
            </button>
          </td>
        </tr>`;
      });
    } else {
      html = '<tr><td colspan="8" class="text-center">暂无数据</td></tr>';
    }
    $('#package-list').html(html);
  }

  // 添加套餐
  function addPackage() {
    $('#modalTitle').text('添加套餐');
    $('#package-id').val(0);
    $('#package-name').val('');
    $('#package-points').val('');
    $('#package-price').val('');
    $('#package-sort').val(0);
    $('#package-status').val(1);
    $('#package-remarks').val('');
    var packageModal = new bootstrap.Modal(document.getElementById('packageModal'));
    packageModal.show();
  }

  // 编辑套餐
  function editPackage(id) {
    showLoading();
    $.ajax({
      url: 'ajax.php?act=get_points_package_info&id=' + id,
      type: 'GET',
      dataType: 'json',
      success: function(res) {
        hideLoading();
        if (res.code === 0) {
          const data = res.data;
          $('#modalTitle').text('编辑套餐');
          $('#package-id').val(data.id);
          $('#package-name').val(data.name);
          $('#package-points').val(data.points_num);
          $('#package-price').val(data.price);
          $('#package-sort').val(data.sort);
          $('#package-status').val(data.status);
          $('#package-remarks').val(data.remarks);
          var packageModal = new bootstrap.Modal(document.getElementById('packageModal'));
          packageModal.show();
        } else {
          showAlert('error', res.msg || '获取套餐信息失败');
        }
      },
      error: function() {
        hideLoading();
        showAlert('error', '网络错误，请重试');
      }
    });
  }

  // 删除套餐
  function deletePackage(id) {
    Swal.fire({
      title: '确认删除',
      text: '确定要删除这个套餐吗？',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#07C160',
      cancelButtonColor: '#d33',
      confirmButtonText: '确定删除',
      cancelButtonText: '取消'
    }).then((result) => {
      if (result.isConfirmed) {
        showLoading();
        $.ajax({
          url: 'ajax.php?act=delete_points_package&id=' + id,
          type: 'GET',
          dataType: 'json',
          success: function(res) {
            hideLoading();
            if (res.code === 0) {
              showAlert('success', res.msg || '删除成功');
              loadPackages(); // 重新加载列表
            } else {
              showAlert('error', res.msg || '删除失败');
            }
          },
          error: function() {
            hideLoading();
            showAlert('error', '网络错误，请重试');
          }
        });
      }
    });
  }

  // 保存套餐
  $('#save-package').click(function() {
    const id = $('#package-id').val();
    const name = $('#package-name').val();
    const points_num = $('#package-points').val();
    const price = $('#package-price').val();
    const sort = $('#package-sort').val();
    const status = $('#package-status').val();
    const remarks = $('#package-remarks').val();

    if (!name || !points_num || !price) {
      showAlert('error', '请填写完整信息');
      return;
    }
    
    const formData = {
      id: id,
      name: name,
      points_num: points_num,
      price: price,
      sort: sort,
      status: status,
      remarks: remarks
    };
    
    showLoading();
    $.ajax({
      url: 'ajax.php?act=save_points_package',
      type: 'POST',
      data: formData,
      dataType: 'json',
      success: function(res) {
        hideLoading();
        if (res.code === 0) {
          var packageModal = bootstrap.Modal.getInstance(document.getElementById('packageModal'));
          packageModal.hide();
          showAlert('success', res.msg || '保存成功');
          loadPackages(); // 重新加载列表
        } else {
          showAlert('error', res.msg || '保存失败');
        }
      },
      error: function() {
        hideLoading();
        showAlert('error', '网络错误，请重试');
      }
    });
  });

  // 显示提示信息
  function showAlert(type, message) {
    const Toast = Swal.mixin({
      toast: true,
      position: 'top-end',
      showConfirmButton: false,
      timer: 3000,
      timerProgressBar: true
    });

    Toast.fire({
      icon: type === 'error' ? 'error' : 'success',
      title: message
    });
  }

  $(document).ready(function() {
    loadPackages();
  });
</script> 