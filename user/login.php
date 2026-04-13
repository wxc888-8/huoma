<?php
include('../includes/common.php');
$my = (isset($_GET['my']) ? $_GET['my'] : NULL);

// 初始化用户登录状态
$islogin2 = isset($_COOKIE["user_token"]) ? 1 : 0;

if ($my == 'login') {
  if (isset($_POST['user']) && isset($_POST['pwd'])) {
    $user = daddslashes(strip_tags($_POST['user']));
    $pwd = strip_tags($_POST['pwd']);
    $res = $DB->get_row("select * from dwz_user where user='$user' limit 1");
    if ($res) {
      if (!verify_password_value($pwd, $res['pwd'])) {
        @header('Content-Type: text/html; charset=UTF-8');
        exit(json_encode(array('code'=>-1,'msg'=>'用户名或密码不正确！')));
      }
      $res['pwd'] = maybe_upgrade_password($res['id'], $pwd, $res['pwd']);
      if ($res['state'] == 0) {
        @header('Content-Type: text/html; charset=UTF-8');
        exit(json_encode(array('code'=>-1,'msg'=>'当前账号已被封禁！')));
      }
      $id = $res['id'];
      $clientip = real_ip();
      $DB->query("update dwz_user set lasttime='$date',lastip='$clientip' where id='$id'");
      $session = md5($res['user'] . $res['pwd'] . $password_hash);
      $token = authcode("{$id}\t{$session}", 'ENCODE', SYS_KEY);
      setcookie("user_token", $token, time() + 604800, '/');
      log_result('用户登录', 'id:' . $id . ',ip:' . $clientip, '登录成功');
      @header('Content-Type: text/html; charset=UTF-8');
      exit(json_encode(array('code'=>1,'msg'=>'登录成功！')));
    } else {
      @header('Content-Type: text/html; charset=UTF-8');
      exit(json_encode(array('code'=>-1,'msg'=>'用户名或密码不正确！')));
    }
  }
} elseif ($my == 'logout') {
  setcookie("user_token", "", time() - 604800, '/');
  @header('Content-Type: text/html; charset=UTF-8');
  
  // 检查是否要留在当前页面
  if(isset($_GET['stay']) && $_GET['stay'] == 1) {
    exit('logout_success');
  } else {
    exit("<script language='javascript'>window.location.href='./login.php';</script>");
  }
}
if ($islogin2 == 1) {
  @header('Content-Type: text/html; charset=UTF-8');
  exit("<script language='javascript'>window.location.href='./';</script>");
}
?>

<!DOCTYPE html>
<html lang="zh-CN">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
    <title><?php echo $conf['web_name']?> - 用户登录</title>
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
        
        .login-container {
            background-color: #fff;
            border-radius: 12px;
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            width: 100%;
            max-width: 420px;
            position: relative;
        }
        
        .login-header {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: white;
            padding: 30px 20px;
            text-align: center;
            position: relative;
        }
        
        .login-header h2 {
            margin: 0;
            font-size: 24px;
            font-weight: 600;
        }
        
        .login-header p {
            margin: 5px 0 0;
            font-size: 14px;
            opacity: 0.9;
        }
        
        .login-logo {
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
        
        .login-logo i {
            font-size: 35px;
            color: var(--primary);
        }
        
        .login-body {
            padding: 35px 30px;
        }
        
        .login-title {
            text-align: center;
            margin-bottom: 10px;
            font-size: 20px;
            font-weight: 600;
            color: var(--dark);
        }
        
        .login-subtitle {
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
        
        .btn-login {
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
        
        .btn-login:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(40, 167, 69, 0.3);
        }
        
        .login-footer {
            padding: 15px 20px;
            text-align: center;
            border-top: 1px solid #f1f1f1;
            background-color: var(--light);
            display: flex;
            justify-content: space-around;
        }
        
        .login-footer a {
            color: var(--secondary);
            font-size: 13px;
            text-decoration: none;
            display: flex;
            align-items: center;
            transition: color 0.2s;
        }
        
        .login-footer a i {
            margin-right: 5px;
            font-size: 16px;
        }
        
        .login-footer a:hover {
            color: var(--primary);
        }
        
        .register-link {
            text-align: center;
            margin-top: 20px;
            font-size: 14px;
        }
        
        .register-link a {
            color: var(--primary);
            text-decoration: none;
            font-weight: 500;
            transition: all 0.2s;
        }
        
        .register-link a:hover {
            text-decoration: underline;
        }
        
        /* 移动端优化 */
        @media (max-width: 576px) {
            .login-container {
                border-radius: 10px;
            }
            
            .login-header {
                padding: 25px 15px;
            }
            
            .login-logo {
                width: 60px;
                height: 60px;
            }
            
            .login-body {
                padding: 25px 20px;
            }
            
            .login-title {
                font-size: 18px;
            }
            
            .form-control {
                height: 46px;
            }
            
            .login-footer {
                flex-direction: column;
                gap: 10px;
            }
            
            .login-footer a {
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
    <div class="login-container">
        <div class="login-header">
            <h2><?php echo $conf['web_name']?></h2>
            <p>专业短链接生成平台·智能化管理系统</p>
            <div class="login-logo">
                <i class="bi bi-link-45deg"></i>
            </div>
            <div class="wave"></div>
        </div>
        <div class="login-body">
            <div class="login-title">用户登录</div>
            <div class="login-subtitle">欢迎回来，请输入您的账号信息</div>
            <form id="loginForm" method="post">
                <div class="form-group">
                    <i class="bi bi-person form-icon"></i>
                    <input type="text" class="form-control" id="user" name="user" placeholder="用户名" required>
                </div>
                <div class="form-group">
                    <i class="bi bi-lock form-icon"></i>
                    <input type="password" class="form-control" id="pwd" name="pwd" placeholder="密码" required>
                </div>
                <button type="button" class="btn btn-login btn-block w-100" id="loginBtn">
                    <i class="bi bi-box-arrow-in-right"></i> 安全登录
                </button>
                <div class="register-link">
                    没有账号？<a href="reg.php">立即注册</a>
                </div>
            </form>
        </div>
        <div class="login-footer">
            <a href="../"><i class="bi bi-house"></i> 返回首页</a>
            <a href="#"><i class="bi bi-question-circle"></i> 帮助中心</a>
            <a href="#"><i class="bi bi-shield-check"></i> 安全说明</a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
    <script>
        $(document).ready(function() {
            // 登录按钮点击事件
            $("#loginBtn").click(function() {
                var user = $("#user").val();
                var pwd = $("#pwd").val();
                
                if(user == '') {
                    Swal.fire({
                        icon: 'warning',
                        title: '提示',
                        text: '请输入用户名',
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
                
                // 修改按钮状态为加载中
                var $btn = $(this);
                var originalText = $btn.html();
                $btn.html('<div class="spinner"></div> 登录中...');
                $btn.prop('disabled', true);
                
                $.ajax({
                    type: "POST",
                    url: "./login.php?my=login",
                    data: {user:user, pwd:pwd},
                    dataType: "json",
                    success: function(data) {
                        if(data.code == 1) {
                            Swal.fire({
                                icon: 'success',
                                title: '登录成功',
                                text: data.msg,
                                confirmButtonColor: '#28a745',
                                timer: 1500,
                                timerProgressBar: true,
                                showConfirmButton: false
                            }).then(() => {
                                window.location.href = './index.php';
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: '登录失败',
                                text: data.msg,
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
            
            // 回车键提交表单
            $(document).keypress(function(e) {
                if (e.which == 13) {
                    $("#loginBtn").click();
                }
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
</html>
