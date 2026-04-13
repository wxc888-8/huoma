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
    <title>修改密码</title>
    <link rel="icon" href="favicon.ico" type="image/ico">
    <link href="../static/admin/css/materialdesignicons.min.css" rel="stylesheet">
    <style>
        :root {
            --wechat-green: #07C160;
            --wechat-dark: #1A1A1A;
            --light-bg: #F7F7F7;
            --border-color: rgba(0, 0, 0, 0.05);
            --text-primary: #333;
            --text-secondary: #666;
            --card-shadow: 0 2px 12px rgba(0, 0, 0, 0.05);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
        }

        body {
            background-color: var(--light-bg);
            color: var(--text-primary);
            font-size: 14px;
            line-height: 1.5;
        }

        .container-fluid {
            padding: 20px;
            max-width: 1000px;
            margin: 0 auto;
        }

        .card {
            background: white;
            border-radius: 8px;
            box-shadow: var(--card-shadow);
            overflow: hidden;
            margin-bottom: 20px;
        }

        .card-header {
            padding: 15px 20px;
            background-color: white;
            border-bottom: 1px solid var(--border-color);
        }

        .card-header h5 {
            margin: 0;
            font-size: 16px;
            font-weight: 500;
            color: var(--text-primary);
            display: flex;
            align-items: center;
        }

        .card-header h5 i {
            margin-right: 8px;
            color: var(--wechat-green);
        }

        .card-body {
            padding: 20px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: var(--text-primary);
        }

        .form-control {
            width: 100%;
            padding: 10px 15px;
            border: 1px solid #e1e1e1;
            border-radius: 6px;
            font-size: 14px;
            transition: all 0.3s;
        }

        .form-control:focus {
            border-color: var(--wechat-green);
            box-shadow: 0 0 0 2px rgba(7, 193, 96, 0.2);
            outline: none;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 10px 20px;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s;
            border: none;
        }

        .btn-primary {
            background-color: var(--wechat-green);
            color: white;
        }

        .btn-primary:hover {
            background-color: #06b054;
            box-shadow: 0 4px 10px rgba(7, 193, 96, 0.2);
            transform: translateY(-1px);
        }

        .btn i {
            margin-right: 5px;
        }

        /* 通知样式 */
        .notify {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 12px 20px;
            background: white;
            border-left: 4px solid var(--wechat-green);
            border-radius: 4px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            z-index: 1000;
            display: flex;
            align-items: center;
            animation: slideIn 0.3s ease, fadeOut 0.5s ease 2.5s forwards;
        }

        .notify.success {
            border-left-color: var(--wechat-green);
        }

        .notify.danger {
            border-left-color: #dc3545;
        }

        .notify i {
            margin-right: 10px;
            font-size: 20px;
        }

        .notify.success i {
            color: var(--wechat-green);
        }

        .notify.danger i {
            color: #dc3545;
        }

        @keyframes slideIn {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        @keyframes fadeOut {
            from {
                opacity: 1;
            }
            to {
                opacity: 0;
                visibility: hidden;
            }
        }

        /* 加载动画 */
        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(255, 255, 255, 0.7);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
        }

        .spinner {
            width: 40px;
            height: 40px;
            border: 3px solid rgba(7, 193, 96, 0.3);
            border-radius: 50%;
            border-top-color: var(--wechat-green);
            animation: spin 1s ease-in-out infinite;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }
    </style>
</head>

<body>
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h5><i class="mdi mdi-lock-reset"></i> 修改管理员密码</h5>
                    </div>
                    <div class="card-body">
                        <form name="editAdmin" action="ajax.php?act=editAdmin" class="site-form">
                            <div class="form-group">
                                <label for="admin_user">用户名</label>
                                <input type="text" class="form-control" name="admin_user" id="admin_user" placeholder="请输入用户名" value="<?php echo $conf['admin_user'] ?>">
                            </div>
                            <div class="form-group">
                                <label for="pwd_old">旧密码</label>
                                <input type="password" class="form-control" name="pwd_old" id="pwd_old" placeholder="输入原登录密码">
                            </div>
                            <div class="form-group">
                                <label for="pwd_new">新密码</label>
                                <input type="password" class="form-control" name="pwd_new" id="pwd_new" placeholder="输入新的密码">
                            </div>
                            <div class="form-group">
                                <label for="pwd_new2">确认新密码</label>
                                <input type="password" class="form-control" name="pwd_new2" id="pwd_new2" placeholder="请再次输入密码">
                            </div>
                            <button type="submit" class="btn btn-primary ajax-post" target-form="editAdmin">
                                <i class="mdi mdi-check"></i> 确认修改
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script type="text/javascript" src="../static/admin/js/jquery.min.js"></script>
    <script>
        $(function() {
            // 表单提交处理
            $('.ajax-post').click(function(e) {
                e.preventDefault();
                
                var self = $(this);
                var target_form = self.attr('target-form');
                var form = $('form[name="' + target_form + '"]');
                var form_data = form.serialize();
                var ajax_url = self.attr("href") || self.data("url") || form.attr("action");
                
                // 表单验证
                var admin_user = $('#admin_user').val();
                var pwd_old = $('#pwd_old').val();
                var pwd_new = $('#pwd_new').val();
                var pwd_new2 = $('#pwd_new2').val();
                
                if (!admin_user) {
                    showNotify('请输入用户名', 'danger');
                    return false;
                }
                
                if (!pwd_old) {
                    showNotify('请输入原密码', 'danger');
                    return false;
                }
                
                if (!pwd_new) {
                    showNotify('请输入新密码', 'danger');
                    return false;
                }
                
                if (pwd_new !== pwd_new2) {
                    showNotify('两次输入的密码不一致', 'danger');
                    return false;
                }
                
                // 显示加载动画
                var loader = showLoading();
                
                // 禁用提交按钮
                self.prop('disabled', true);
                
                // 发送AJAX请求
                $.post(ajax_url, form_data)
                    .done(function(res) {
                        // 隐藏加载动画
                        hideLoading(loader);
                        
                        var msg = res.msg;
                        if (res.code == 0) {
                            if (res.url) {
                                msg += '，页面即将自动跳转';
                            }
                            showNotify(msg, 'success');
                            setTimeout(function() {
                                self.prop('disabled', false);
                                return res.url ? window.parent.location.href = res.url : window.parent.location.reload();
                            }, 1500);
                        } else {
                            showNotify(msg, 'danger');
                            self.prop('disabled', false);
                        }
                    })
                    .fail(function() {
                        hideLoading(loader);
                        showNotify('服务器发生错误，请稍后再试', 'danger');
                        self.prop('disabled', false);
                    });
            });
            
            // 显示通知
            function showNotify(message, type) {
                type = type || 'success';
                var icon = type === 'success' ? 'mdi-check-circle' : 'mdi-alert-circle';
                
                var notify = $('<div class="notify ' + type + '"><i class="mdi ' + icon + '"></i>' + message + '</div>');
                $('body').append(notify);
                
                setTimeout(function() {
                    notify.remove();
                }, 3000);
            }
            
            // 显示加载动画
            function showLoading() {
                var loader = $('<div class="loading-overlay"><div class="spinner"></div></div>');
                $('body').append(loader);
                return loader;
            }
            
            // 隐藏加载动画
            function hideLoading(loader) {
                if (loader) {
                    loader.remove();
                }
            }
            
            // 输入框获得焦点时添加高亮效果
            $('.form-control').focus(function() {
                $(this).addClass('focused');
            }).blur(function() {
                $(this).removeClass('focused');
            });
        });
    </script>
</body>

</html>