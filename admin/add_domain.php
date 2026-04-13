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
    <title>批量添加域名</title>
    <link rel="icon" href="favicon.ico" type="image/ico">
    <link href="../static/admin/css/bootstrap.min.css" rel="stylesheet">
    <link href="../static/admin/css/materialdesignicons.min.css" rel="stylesheet">
    <link href="../static/admin/js/bootstrap-table/bootstrap-table.min.css" rel="stylesheet">
    <link href="../static/admin/js/jquery-confirm/jquery-confirm.min.css" rel="stylesheet">
    <link href="../static/admin/css/style.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/ant-design-icons/dist/anticons.min.css" rel="stylesheet">
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

        .card-header {
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid rgba(0,0,0,0.05);
            background-color: white;
            border-top-left-radius: 8px;
            border-top-right-radius: 8px;
        }

        .card-header.bg-primary {
            background-color: var(--primary-color) !important;
            color: white;
        }

        .card-body {
            padding: 1.5rem;
        }

        .form-control {
            border-radius: 4px;
            border: 1px solid #ddd;
            padding: 0.5rem 0.75rem;
            box-shadow: none;
            transition: all 0.3s;
        }

        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(46, 204, 113, 0.25);
        }

        textarea.form-control {
            resize: vertical;
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

        .nav-tabs {
            border-bottom: 1px solid rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }

        .nav-tabs .nav-link {
            border: none;
            border-bottom: 2px solid transparent;
            color: var(--text-color);
            padding: 0.75rem 1rem;
            transition: all 0.3s;
        }

        .nav-tabs .nav-link:hover {
            border-color: transparent;
            color: var(--primary-color);
        }

        .nav-tabs .nav-link.active {
            color: var(--primary-color);
            border-bottom: 2px solid var(--primary-color);
            background-color: transparent;
        }

        .tab-content {
            padding: 1rem 0;
        }

        .alert {
            border-radius: 4px;
            border: none;
        }

        .alert-info {
            background-color: #e6f7ff;
            color: #1890ff;
        }

        .table {
            width: 100%;
            margin-bottom: 1rem;
            border-radius: 4px;
            overflow: hidden;
        }

        .table th {
            background-color: #fafafa;
            font-weight: 500;
            border-top: none;
            border-bottom: 1px solid #eee;
        }

        .table td {
            border-top: none;
            border-bottom: 1px solid #eee;
            vertical-align: middle;
        }

        .table-bordered {
            border: 1px solid #eee;
        }

        .table-bordered th,
        .table-bordered td {
            border: 1px solid #eee;
        }

        .badge {
            padding: 0.4rem 0.6rem;
            border-radius: 4px;
            font-size: 85%;
        }

        .badge-success {
            background-color: var(--primary-color);
        }

        .badge-danger {
            background-color: var(--danger-color);
        }

        .text-white {
            font-weight: 500;
        }
    </style>
</head>

<body>
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header bg-primary">
                        <h4 class="text-white"><i class="anticon anticon-plus-circle"></i> 批量添加域名</h4>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info" role="alert">
                            <i class="anticon anticon-info-circle"></i> 在此页面可以批量添加入口域名和落地域名
                        </div>
                        
                        <ul class="nav nav-tabs">
                            <li class="nav-item">
                                <a class="nav-link active" data-toggle="tab" href="#entryDomain">
                                    <i class="anticon anticon-import"></i> 入口域名
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-toggle="tab" href="#landingDomain">
                                    <i class="anticon anticon-export"></i> 落地域名
                                </a>
                            </li>
                        </ul>
                        
                        <div class="tab-content">
                            <!-- 入口域名 -->
                            <div class="tab-pane fade show active" id="entryDomain">
                                <form name="addEntryDomain" action="ajax.php?act=addEntryDomain" class="mt-3">
                                    <div class="form-group">
                                        <label for="entry-domains" class="control-label">域名列表：</label>
                                        <textarea class="form-control" name="domains" id="entry-domains" rows="5" placeholder="每行输入一个域名，如example.com"></textarea>
                                        <small class="form-text text-muted">每行输入一个域名，不需要添加http://或https://前缀</small>
                                    </div>
                                    <div class="form-group">
                                        <label for="entry-is_paid" class="control-label">付费状态：</label>
                                        <select class="form-control" name="is_paid" id="entry-is_paid">
                                            <option value="0">积分域名</option>
                                            <option value="1">付费域名</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="entry-price" class="control-label">价格：</label>
                                        <input type="number" class="form-control" name="price" id="entry-price" value="0.00" step="0.01">
                                        <small class="form-text text-muted">如果是付费域名，请设置价格</small>
                                    </div>
                                    <div class="form-group">
                                        <label for="entry-remark" class="control-label">备注：</label>
                                        <textarea class="form-control" name="remark" id="entry-remark" rows="3"></textarea>
                                    </div>
                                    <div class="form-group">
                                        <button type="button" class="btn btn-primary ajax-post-batch" data-type="entry" target-form="addEntryDomain">
                                            <i class="anticon anticon-upload"></i> 添加入口域名
                                        </button>
                                    </div>
                                </form>
                            </div>
                            
                            <!-- 落地域名 -->
                            <div class="tab-pane fade" id="landingDomain">
                                <form name="addLandingDomain" action="ajax.php?act=addLandingDomain" class="mt-3">
                                    <div class="form-group">
                                        <label for="landing-domains" class="control-label">域名列表：</label>
                                        <textarea class="form-control" name="domains" id="landing-domains" rows="5" placeholder="每行输入一个域名，如example.com"></textarea>
                                        <small class="form-text text-muted">每行输入一个域名，不需要添加http://或https://前缀</small>
                                    </div>
                                    <div class="form-group">
                                        <label for="landing-is_paid" class="control-label">付费状态：</label>
                                        <select class="form-control" name="is_paid" id="landing-is_paid">
                                            <option value="0">积分域名</option>
                                            <option value="1">付费域名</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="landing-price" class="control-label">价格：</label>
                                        <input type="number" class="form-control" name="price" id="landing-price" value="0.00" step="0.01">
                                        <small class="form-text text-muted">如果是付费域名，请设置价格</small>
                                    </div>
                                    <div class="form-group">
                                        <label for="landing-remark" class="control-label">备注：</label>
                                        <textarea class="form-control" name="remark" id="landing-remark" rows="3"></textarea>
                                    </div>
                                    <div class="form-group">
                                        <button type="button" class="btn btn-primary ajax-post-batch" data-type="landing" target-form="addLandingDomain">
                                            <i class="anticon anticon-upload"></i> 添加落地域名
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- 导入结果卡片 -->
                <div class="card mt-3" id="resultCard" style="display:none;">
                    <div class="card-header bg-primary">
                        <h4 class="text-white"><i class="anticon anticon-file-done"></i> 导入结果</h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>域名</th>
                                        <th>状态</th>
                                        <th>备注</th>
                                    </tr>
                                </thead>
                                <tbody id="resultBody">
                                </tbody>
                            </table>
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
    <script type="text/javascript" src="../static/admin/js/main.min.js"></script>
    <script type="text/javascript">
        $(function() {
            // 批量添加域名
            $('.ajax-post-batch').click(function() {
                var $btn = $(this);
                var type = $btn.data('type');
                var targetForm = $btn.attr('target-form');
                var $form = $('form[name="' + targetForm + '"]');
                
                // 获取表单数据
                var domainsText = $form.find('textarea[name="domains"]').val();
                if (!domainsText) {
                    showNotify('请输入要添加的域名', 'danger');
                    return;
                }
                
                // 分割域名
                var domains = domainsText.split('\n');
                var is_paid = $form.find('select[name="is_paid"]').val();
                var price = $form.find('input[name="price"]').val();
                var remark = $form.find('textarea[name="remark"]').val();
                
                // 确认操作
                $.confirm({
                    title: '确认添加',
                    content: '确定要添加这些域名吗？共 ' + domains.length + ' 个域名',
                    type: 'blue',
                    buttons: {
                        confirm: {
                            text: '确认',
                            btnClass: 'btn-primary',
                            action: function() {
                                processDomains(domains, type, is_paid, price, remark);
                            }
                        },
                        cancel: {
                            text: '取消'
                        }
                    }
                });
            });
            
            // 处理域名添加
            function processDomains(domains, type, is_paid, price, remark) {
                var successCount = 0;
                var failedCount = 0;
                var results = [];
                var loader = $('body').lyearloading({
                    opacity: 0.2,
                    spinnerSize: 'lg'
                });
                
                // 清空结果表格
                $('#resultBody').empty();
                
                // 按顺序处理每个域名
                processNext(0);
                
                function processNext(index) {
                    if (index >= domains.length) {
                        // 全部处理完毕，显示结果
                        loader.destroy();
                        $('#resultCard').show();
                        showNotify('处理完成，成功: ' + successCount + '，失败: ' + failedCount, 'success');
                        return;
                    }
                    
                    var domain = domains[index].trim();
                    if (!domain) {
                        // 跳过空行
                        processNext(index + 1);
                        return;
                    }
                    
                    // AJAX添加域名
                    var act = type === 'entry' ? 'addEntryDomain' : 'addLandingDomain';
                    $.ajax({
                        url: 'ajax.php?act=' + act,
                        type: 'POST',
                        dataType: 'json',
                        data: {
                            domain: domain,
                            is_paid: is_paid,
                            price: price,
                            remark: remark
                        },
                        success: function(res) {
                            var status, message;
                            if (res.code === 0) {
                                status = '<span class="badge badge-success">成功</span>';
                                message = '添加成功';
                                successCount++;
                            } else {
                                status = '<span class="badge badge-danger">失败</span>';
                                message = res.msg || '添加失败';
                                failedCount++;
                            }
                            
                            // 添加到结果表格
                            $('#resultBody').append(
                                '<tr>' +
                                '<td>' + domain + '</td>' +
                                '<td>' + status + '</td>' +
                                '<td>' + message + '</td>' +
                                '</tr>'
                            );
                            
                            // 处理下一个域名
                            processNext(index + 1);
                        },
                        error: function() {
                            // 添加到结果表格
                            $('#resultBody').append(
                                '<tr>' +
                                '<td>' + domain + '</td>' +
                                '<td><span class="badge badge-danger">失败</span></td>' +
                                '<td>服务器错误</td>' +
                                '</tr>'
                            );
                            failedCount++;
                            
                            // 处理下一个域名
                            processNext(index + 1);
                        }
                    });
                }
            }
        });
        
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
    </script>
</body>

</html> 