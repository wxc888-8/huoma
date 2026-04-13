<?php
include('../includes/common.php');
if ($conf['is_reg'] == 0) {
  exit("<script language='javascript'>alert('注册通道已关闭'); setTimeout(function(){history.go(-1)},1500);</script>");
}
$my = (isset($_GET['my']) ? $_GET['my'] : NULL);
if ($my == 'reg') {
  $user = daddslashes(strip_tags($_POST['user']));
  $pwd = daddslashes(strip_tags($_POST['pwd']));
  $pwds = daddslashes(strip_tags($_POST['pwds']));
  $qq = daddslashes(strip_tags($_POST['qq']));
  $invite_code = isset($_POST['invite_code']) ? daddslashes(strip_tags($_POST['invite_code'])) : '';
  
  if ($user == '' || $pwd == '' || $pwds == '' || $qq == '') {
    exit("<script language='javascript'>alert('填写的信息不完整！'); setTimeout(function(){history.go(-1)},1500);</script>");
  }
  if ($pwd != $pwds) {
    exit("<script language='javascript'>alert('两次输入的密码不一致！'); setTimeout(function(){history.go(-1)},1500);</script>");
  }
  if (!preg_match('/[a-zA-Z0-9_]{5,10}$/', $user)) {
    exit("<script language='javascript'>alert('账号格式有误'); setTimeout(function(){history.go(-1)},1500);</script>");
  }
  if (!preg_match('/\w{5,12}$/', $pwd)) {
    exit("<script language='javascript'>alert('密码格式有误'); setTimeout(function(){history.go(-1)},1500);</script>");
  }
  if (!preg_match('/^[0-9]{5,11}+$/', $qq)) {
    exit("<script language='javascript'>alert('QQ格式不正确！'); setTimeout(function(){history.go(-1)},1500);</script>");
  }
  if ($DB->count("select count(*) from dwz_user where user='$user'") > 0) {
    exit("<script language='javascript'>alert('该用户名已存在！'); setTimeout(function(){history.go(-1)},1500);</script>");
  }
  if ($DB->count("select count(*) from dwz_user where qq='$qq'") > 0) {
    exit("<script language='javascript'>alert('该QQ号已经注册过了！'); setTimeout(function(){history.go(-1)},1500);</script>");
  }
  
  // 验证邀请码
  $inviter_id = 0;
  if (!empty($invite_code)) {
    $invite_row = $DB->get_row("SELECT * FROM dwz_invite_code WHERE code='$invite_code' AND state=1 LIMIT 1");
    if (!$invite_row) {
      exit("<script language='javascript'>alert('邀请码不存在或已失效！'); setTimeout(function(){history.go(-1)},1500);</script>");
    }
    $inviter_id = $invite_row['uid'];
    
    // 更新邀请码使用次数
    $DB->query("UPDATE dwz_invite_code SET use_num=use_num+1 WHERE code='$invite_code'");
  }
  
  // 获取QQ昵称和头像，添加错误处理
  $qq_info = qq_img($qq);
  $name = isset($qq_info['name']) && !empty($qq_info['name']) ? $qq_info['name'] : '用户'.substr($qq, -4);
  $img = isset($qq_info['img']) && !empty($qq_info['img']) ? $qq_info['img'] : 'https://q.qlogo.cn/headimg_dl?dst_uin='.$qq.'&spec=100';
  
  $day = $conf['default_vip'];
  $vip = date('Y-m-d H:i:s', strtotime("$date + $day day"));
  $mail = $qq . '@qq.com';
  $clientip = real_ip();
  if ($id = $DB->insert("insert into dwz_user(user,pwd,addtime,vip,qq,mail,addip,name,img) values('$user','$pwd','$date','$vip','$qq','$mail','$clientip','$name','$img')")) {
    $clientip = real_ip();
    $DB->query("update dwz_user set lasttime='$date',lastip='$clientip' where id='$id'");
    
    // 如果有邀请码，保存邀请关系
    if (!empty($invite_code) && $inviter_id > 0) {
      // 保存邀请记录
      $DB->query("INSERT INTO dwz_invite_record(invite_uid, invited_uid, code, reward_points, create_time, status) 
                 VALUES('$inviter_id', '$id', '$invite_code', 0, '$date', 1)");
      
      // 给邀请人增加积分奖励
      $DB->query("UPDATE dwz_user SET points=points+0 WHERE id='$inviter_id'");
      
      // 记录积分变动
      $DB->query("INSERT INTO dwz_points_log(uid, points, type, description, addtime) 
                 VALUES('$inviter_id', 0, 'invite_reward', '邀请用户 $user 注册奖励', '$date')");
    }
    
    // 为新注册用户生成邀请码
    $new_invite_code = strtoupper(substr(md5($id.$user.time().rand(1000,9999)), 0, 8));
    $DB->query("INSERT INTO dwz_invite_code(uid, code, create_time, state, use_num) VALUES('$id', '$new_invite_code', '$date', 1, 0)");
    
    $session = md5($user . $pwd . $password_hash);
    $token = authcode("{$id}\t{$session}", 'ENCODE', SYS_KEY);
    setcookie("user_token", $token, time() + 604800, '/');
    log_result('用户登录', 'id:' . $id . ',ip:' . $clientip, '登录成功');
    exit("<script language='javascript'>alert('注册成功，即将自动跳转登录！'); setTimeout(function(){window.location.href='./'},1500);</script>");
  } else {
    // 添加调试代码，输出具体的错误信息
    $db_error = $DB->error();
    $sql_query = "insert into dwz_user(user,pwd,addtime,vip,qq,mail,addip,name,img) values('$user','$pwd','$date','$vip','$qq','$mail','$clientip','$name','$img')";
    
    // 记录错误到日志文件
    error_log("注册失败 - SQL: $sql_query - 错误: $db_error", 0);
    
    // 在页面上显示错误信息（仅用于调试，生产环境建议移除）
    exit("<script language='javascript'>alert('注册失败！错误详情: " . addslashes($db_error) . "'); setTimeout(function(){history.go(-1)},1500);</script>");
  }
}

// 获取传入的邀请码
$default_invite_code = isset($_GET['invite']) ? daddslashes($_GET['invite']) : '';
?>

<!DOCTYPE html>
<html lang="zh-CN">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
    <title><?php echo $conf['web_name']?> - 用户注册</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        :root {
            --primary: #28a745;
            --primary-light: #5cb85c;
            --primary-dark: #218838;
            --secondary: #6c757d;
            --light: #f8f9fa;
            --dark: #343a40;
        }
        
        body {
            font-family: 'PingFang SC', 'Microsoft YaHei', sans-serif;
            background-color: #f5f7fa;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            padding: 20px;
            color: #333;
            line-height: 1.6;
        }
        
        .register-container {
            background-color: #fff;
            border-radius: 12px;
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            width: 100%;
            max-width: 420px;
            position: relative;
        }
        
        .register-header {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: white;
            padding: 30px 20px;
            text-align: center;
            position: relative;
        }
        
        .register-header h2 {
            margin: 0;
            font-size: 24px;
            font-weight: 600;
        }
        
        .register-header p {
            margin: 5px 0 0;
            font-size: 14px;
            opacity: 0.9;
        }
        
        .register-logo {
            width: 70px;
            height: 70px;
            background-color: white;
            border-radius: 50%;
            margin: 10px auto;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        
        .register-logo i {
            font-size: 35px;
            color: var(--primary);
        }
        
        .register-body {
            padding: 35px 30px;
        }
        
        .register-title {
            text-align: center;
            margin-bottom: 10px;
            font-size: 20px;
            font-weight: 600;
            color: var(--dark);
        }
        
        .register-subtitle {
            text-align: center;
            margin-bottom: 25px;
            font-size: 14px;
            color: var(--secondary);
        }
        
        .form-group {
            margin-bottom: 20px;
            position: relative;
        }
        
        .form-control {
            height: 50px;
            border-radius: 8px;
            border: 1px solid #e1e5eb;
            padding-left: 45px;
            font-size: 14px;
            transition: all 0.3s;
        }
        
        .form-control:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
        }
        
        .form-icon {
            position: absolute;
            left: 15px;
            top: 17px;
            color: #b1b5c9;
        }
        
        .btn-register {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            border: none;
            border-radius: 8px;
            color: white;
            font-weight: 600;
            height: 50px;
            font-size: 16px;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        
        .btn-register:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(40, 167, 69, 0.3);
        }
        
        .register-footer {
            padding: 15px 20px;
            text-align: center;
            border-top: 1px solid #f1f1f1;
            background-color: var(--light);
            display: flex;
            justify-content: space-around;
        }
        
        .register-footer a {
            color: var(--secondary);
            font-size: 13px;
            text-decoration: none;
            display: flex;
            align-items: center;
            transition: color 0.2s;
        }
        
        .register-footer a i {
            margin-right: 5px;
            font-size: 16px;
        }
        
        .register-footer a:hover {
            color: var(--primary);
        }
        
        .login-link {
            text-align: center;
            margin-top: 20px;
            font-size: 14px;
        }
        
        .login-link a {
            color: var(--primary);
            text-decoration: none;
            font-weight: 500;
            transition: all 0.2s;
        }
        
        .login-link a:hover {
            text-decoration: underline;
        }
        
        .form-tips {
            font-size: 12px;
            color: var(--secondary);
            margin-top: 5px;
            padding-left: 5px;
        }
        
        /* 移动端优化 */
        @media (max-width: 576px) {
            .register-container {
                border-radius: 10px;
            }
            
            .register-header {
                padding: 25px 15px;
            }
            
            .register-logo {
                width: 60px;
                height: 60px;
            }
            
            .register-body {
                padding: 25px 20px;
            }
            
            .register-title {
                font-size: 18px;
            }
            
            .form-control {
                height: 46px;
            }
            
            .register-footer {
                flex-direction: column;
                gap: 10px;
            }
            
            .register-footer a {
                justify-content: center;
            }
        }
        
        /* 加载动画 */
        .spinner {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid rgba(255,255,255,.3);
            border-radius: 50%;
            border-top-color: #fff;
            animation: spin 1s ease-in-out infinite;
        }
        
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        
        /* 波浪装饰 */
        .wave {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 15px;
            background: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 1440 320'%3E%3Cpath fill='%23ffffff' fill-opacity='1' d='M0,192L48,176C96,160,192,128,288,133.3C384,139,480,181,576,186.7C672,192,768,160,864,154.7C960,149,1056,171,1152,170.7C1248,171,1344,149,1392,138.7L1440,128L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z'%3E%3C/path%3E%3C/svg%3E") repeat-x;
            background-size: 1440px 100%;
        }
    </style>
</head>

<body>
    <div class="register-container">
        <div class="register-header">
            <h2><?php echo $conf['web_name']?></h2>
            <p>专业短链接生成平台·智能化管理系统</p>
            <div class="register-logo">
                <i class="bi bi-person-plus"></i>
            </div>
            <div class="wave"></div>
        </div>
        <div class="register-body">
            <div class="register-title">用户注册</div>
            <div class="register-subtitle">欢迎加入，请填写以下信息完成注册</div>
            <form id="registerForm" method="post" action="./reg.php?my=reg">
                <div class="form-group">
                    <i class="bi bi-person form-icon"></i>
                    <input type="text" class="form-control" id="user" name="user" placeholder="请输入用户名" required>
                    <div class="form-tips">用户名长度5-10位，只能包含字母、数字和下划线</div>
                </div>
                <div class="form-group">
                    <i class="bi bi-lock form-icon"></i>
                    <input type="password" class="form-control" id="pwd" name="pwd" placeholder="请输入密码" required>
                    <div class="form-tips">密码长度5-12位，可包含字母、数字和下划线</div>
                </div>
                <div class="form-group">
                    <i class="bi bi-shield-lock form-icon"></i>
                    <input type="password" class="form-control" id="pwds" name="pwds" placeholder="请再次输入密码" required>
                </div>
                <div class="form-group">
                    <i class="bi bi-chat-fill form-icon"></i>
                    <input type="text" class="form-control" id="qq" name="qq" placeholder="请输入QQ号" required>
                    <div class="form-tips">QQ号长度5-11位，用于获取头像和昵称</div>
                </div>
                <div class="form-group">
                    <i class="bi bi-gift form-icon"></i>
                    <input type="text" class="form-control" id="invite_code" name="invite_code" placeholder="请输入邀请码（选填）" value="<?php echo $default_invite_code; ?>">
                    <div class="form-tips">如果您是通过邀请链接访问，已自动填入邀请码</div>
                </div>
                <button type="submit" class="btn btn-register btn-block w-100" id="submitBtn">
                    <i class="bi bi-check2-circle"></i> 立即注册
                </button>
                <div class="login-link">
                    已有账号？<a href="login.php">立即登录</a>
                </div>
            </form>
        </div>
        <div class="register-footer">
            <a href="../"><i class="bi bi-house"></i> 返回首页</a>
            <a href="#"><i class="bi bi-question-circle"></i> 帮助中心</a>
            <a href="#"><i class="bi bi-file-earmark-text"></i> 用户协议</a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
    <script>
        $(document).ready(function() {
            // 表单验证
            $("#registerForm").submit(function(e) {
                e.preventDefault(); // 阻止默认提交
                
                var user = $("#user").val();
                var pwd = $("#pwd").val();
                var pwds = $("#pwds").val();
                var qq = $("#qq").val();
                var invite_code = $("#invite_code").val();
                
                if(user == '') {
                    Swal.fire({
                        icon: 'warning',
                        title: '提示',
                        text: '请输入用户名',
                        confirmButtonColor: '#28a745'
                    });
                    return false;
                }
                if(!user.match(/[a-zA-Z0-9_]{5,10}$/)) {
                    Swal.fire({
                        icon: 'warning',
                        title: '提示',
                        text: '用户名格式有误，长度5-10位，只能包含字母、数字和下划线',
                        confirmButtonColor: '#28a745'
                    });
                    return false;
                }
                if(pwd == '') {
                    Swal.fire({
                        icon: 'warning',
                        title: '提示',
                        text: '请输入密码',
                        confirmButtonColor: '#28a745'
                    });
                    return false;
                }
                if(!pwd.match(/\w{5,12}$/)) {
                    Swal.fire({
                        icon: 'warning',
                        title: '提示',
                        text: '密码格式有误，长度5-12位，可包含字母、数字和下划线',
                        confirmButtonColor: '#28a745'
                    });
                    return false;
                }
                if(pwds == '') {
                    Swal.fire({
                        icon: 'warning',
                        title: '提示',
                        text: '请再次输入密码',
                        confirmButtonColor: '#28a745'
                    });
                    return false;
                }
                if(pwd != pwds) {
                    Swal.fire({
                        icon: 'warning',
                        title: '提示',
                        text: '两次输入的密码不一致',
                        confirmButtonColor: '#28a745'
                    });
                    return false;
                }
                if(qq == '') {
                    Swal.fire({
                        icon: 'warning',
                        title: '提示',
                        text: '请输入QQ号',
                        confirmButtonColor: '#28a745'
                    });
                    return false;
                }
                if(!qq.match(/^[0-9]{5,11}$/)) {
                    Swal.fire({
                        icon: 'warning',
                        title: '提示',
                        text: 'QQ格式不正确，长度5-11位',
                        confirmButtonColor: '#28a745'
                    });
                    return false;
                }
                
                // 修改按钮状态为加载中
                var $btn = $("#submitBtn");
                var originalText = $btn.html();
                $btn.html('<div class="spinner"></div> 注册中...');
                $btn.prop('disabled', true);
                
                // 提交表单
                $.ajax({
                    type: "POST",
                    url: "./reg.php?my=reg",
                    data: {user:user, pwd:pwd, pwds:pwds, qq:qq, invite_code:invite_code},
                    dataType: "html",
                    success: function(data) {
                        if(data.indexOf('注册成功') > -1) {
                            Swal.fire({
                                icon: 'success',
                                title: '注册成功',
                                text: '注册成功，即将自动跳转登录！',
                                confirmButtonColor: '#28a745',
                                timer: 1500,
                                timerProgressBar: true,
                                showConfirmButton: false
                            }).then(() => {
                                window.location.href = './index.php';
                            });
                        } else {
                            // 错误信息提取
                            let errorMsg = '注册失败，请稍后再试';
                            const matches = data.match(/alert\('(.+?)'\)/);
                            if (matches && matches.length > 1) {
                                errorMsg = matches[1];
                            }
                            
                            Swal.fire({
                                icon: 'error',
                                title: '注册失败',
                                text: errorMsg,
                                confirmButtonColor: '#28a745'
                            });
                            
                            // 恢复按钮状态
                            $btn.html(originalText);
                            $btn.prop('disabled', false);
                        }
                    },
                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            title: '网络错误',
                            text: '服务器连接失败，请稍后再试',
                            confirmButtonColor: '#28a745'
                        });
                        // 恢复按钮状态
                        $btn.html(originalText);
                        $btn.prop('disabled', false);
                    }
                });
            });
            
            // 输入框获得焦点效果
            $('.form-control').focus(function() {
                $(this).prev('.form-icon').css('color', '#28a745');
            }).blur(function() {
                $(this).prev('.form-icon').css('color', '#b1b5c9');
            });
        });
    </script>
</body>

</html>