<?php
//$verifycode = 1; //验证码开关
if (!function_exists("imagecreate") || !file_exists('code.php')) $verifycode = 0;
include("../includes/common.php");

// 初始化登录状态变量
$islogin = isset($_COOKIE["admin_token"]) ? 1 : 0;

if (isset($_POST['user']) && isset($_POST['pass'])) {
  $user = daddslashes($_POST['user']);
  $pass = daddslashes($_POST['pass']);
  $code = daddslashes($_POST['code']);
  $rememberme = daddslashes($_POST['rememberme']);
  if ($user === $conf['admin_user'] && $pass === $conf['admin_pwd']) {
    unset($_SESSION['vc_code']);
    $session = md5($user . $pass . $password_hash);
    $token = authcode("{$user}\t{$session}", 'ENCODE', SYS_KEY);
    if ($rememberme) {
      setcookie("admin_token", $token, time() + 604800);
    } else {
      setcookie("admin_token", $token, time() + 259200);
    }
    saveSetting('adminlogin', $date);
    log_result('后台登录', 'ip:' . $clientip, '登录成功');
    @header('Content-Type: text/html; charset=UTF-8');
    exit("<script language='javascript'>alert('登陆管理中心成功！');window.location.href='./';</script>");
  } else {
    unset($_SESSION['vc_code']);
    @header('Content-Type: text/html; charset=UTF-8');
    exit("<script language='javascript'>alert('用户名或密码不正确！');history.go(-1);</script>");
  }
} elseif (isset($_GET['logout'])) {
  setcookie("admin_token");
  @header('Content-Type: text/html; charset=UTF-8');
  exit("<script language='javascript'>alert('您已成功注销本次登陆！');window.location.href='./login.php';</script>");
} elseif ($islogin == 1) {
  @header('Content-Type: text/html; charset=UTF-8');
  exit("<script language='javascript'>alert('您已登陆！');window.location.href='./';</script>");
}
?>

<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <title>智能防红系统 - 管理登录</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.2.1/css/all.min.css">
    <style>
        :root {
            --primary-color: #4e73df;
            --secondary-color: #6c757d;
            --success-color: #3ac47d;
            --gradient-start: #5e7ce0;
            --gradient-mid: #4a6fd7;
            --gradient-end: #3661cf;
        }
        
        body {
            font-family: "Microsoft YaHei", "Segoe UI", "Helvetica Neue", Arial, sans-serif;
            height: 100vh;
            margin: 0;
            padding: 0;
            overflow-x: hidden;
            background-color: #f0f2f5;
            background-image: 
                url("data:image/svg+xml,%3Csvg width='100' height='100' viewBox='0 0 100 100' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M11 18c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm48 25c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm-43-7c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm63 31c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM34 90c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm56-76c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM12 86c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm28-65c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm23-11c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-6 60c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm29 22c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zM32 63c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm57-13c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-9-21c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM60 91c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM35 41c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM12 60c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2z' fill='%234e73df' fill-opacity='0.05' fill-rule='evenodd'/%3E%3C/svg%3E"),
                linear-gradient(135deg, rgba(240, 242, 245, 0.8) 0%, rgba(240, 242, 245, 0.9) 100%);
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .login-container {
            width: 100%;
            max-width: 420px;
            margin: 0 auto;
            background-color: #fff;
            border-radius: 16px;
            box-shadow: 0 10px 25px rgba(50, 50, 93, 0.1), 0 5px 15px rgba(0, 0, 0, 0.05);
            overflow: hidden;
            position: relative;
            transform: translateZ(0);
        }
        
        .login-header {
            background: linear-gradient(135deg, var(--gradient-start), var(--gradient-mid), var(--gradient-end));
            padding: 30px 20px;
            text-align: center;
            color: white;
            position: relative;
            overflow: hidden;
        }
        
        .login-header::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-image: url("data:image/svg+xml,%3Csvg width='100' height='100' viewBox='0 0 100 100' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M11 18c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm48 25c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm-43-7c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm63 31c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM34 90c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm56-76c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM12 86c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm28-65c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm23-11c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-6 60c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm29 22c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zM32 63c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm57-13c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-9-21c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM60 91c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM35 41c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM12 60c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2z' fill='%23ffffff' fill-opacity='0.1' fill-rule='evenodd'/%3E%3C/svg%3E");
            z-index: 1;
        }
        
        .login-header-title {
            font-size: 22px;
            font-weight: 600;
            margin-bottom: 5px;
            position: relative;
            z-index: 2;
        }
        
        .login-header-subtitle {
            font-size: 14px;
            opacity: 0.9;
            position: relative;
            z-index: 2;
        }
        
        .avatar-container {
            width: 100px;
            height: 100px;
            margin: 20px auto;
            position: relative;
            z-index: 2;
        }
        
        .avatar-bg {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            background-color: rgba(255, 255, 255, 0.95);
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            position: relative;
            overflow: hidden;
        }
        
        .avatar-bg::after {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.2), transparent);
            z-index: 1;
        }
        
        .cute-avatar {
            width: 80%;
            height: 80%;
            position: relative;
            z-index: 2;
        }
        
        .login-body {
            padding: 30px;
            background: linear-gradient(to bottom, #ffffff, #f8f9fa);
        }
        
        .login-title {
            font-size: 18px;
            font-weight: 600;
            color: #333;
            margin-bottom: 20px;
            text-align: center;
        }
        
        .login-subtitle {
            font-size: 14px;
            color: #6c757d;
            margin-bottom: 25px;
            text-align: center;
        }
        
        .form-group {
            margin-bottom: 20px;
            position: relative;
        }
        
        .form-control {
            height: 48px;
            border-radius: 10px;
            border: 1px solid #e0e0e0;
            padding: 10px 15px 10px 45px;
            font-size: 15px;
            transition: all 0.3s;
            background-color: #f8f9fa;
        }
        
        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.15);
            background-color: #fff;
        }
        
        .input-icon {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #adb5bd;
            font-size: 16px;
        }
        
        .btn-login {
            height: 48px;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 500;
            background: linear-gradient(to right, var(--gradient-start), var(--gradient-end));
            border: none;
            color: white;
            box-shadow: 0 4px 10px rgba(78, 115, 223, 0.25);
            transition: all 0.3s;
            width: 100%;
            margin-top: 10px;
        }
        
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(78, 115, 223, 0.3);
        }
        
        .btn-login:active {
            transform: translateY(0);
        }
        
        .btn-login i {
            margin-right: 8px;
        }
        
        .footer-links {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
            font-size: 13px;
        }
        
        .footer-link {
            color: #6c757d;
            text-decoration: none;
            display: flex;
            align-items: center;
        }
        
        .footer-link i {
            font-size: 12px;
            margin-right: 5px;
        }
        
        .footer-link:hover {
            color: var(--primary-color);
        }
        
        /* 响应式调整 */
        @media (max-width: 480px) {
            .login-container {
                max-width: 90%;
                margin: 0 15px;
            }
            
            .login-header {
                padding: 25px 15px;
            }
            
            .login-body {
                padding: 25px 20px;
            }
            
            .avatar-container {
                width: 90px;
                height: 90px;
                margin: 15px auto;
            }
            
            .form-control {
                height: 45px;
                font-size: 14px;
            }
            
            .btn-login {
                height: 45px;
                font-size: 15px;
            }
        }
        
        /* 动画效果 - 轻量级实现，避免卡顿 */
        @keyframes gentle-float {
            0% { transform: translateY(0); }
            50% { transform: translateY(-5px); }
            100% { transform: translateY(0); }
        }
        
        .avatar-bg {
            animation: gentle-float 3s infinite ease-in-out;
            will-change: transform;
        }
        
        /* 浮动气泡装饰 */
        .bubble {
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.15);
            z-index: 0;
        }
        
        .bubble-1 {
            width: 15px;
            height: 15px;
            top: 20%;
            left: 10%;
            animation: gentle-float 4s infinite ease-in-out;
        }
        
        .bubble-2 {
            width: 10px;
            height: 10px;
            top: 30%;
            right: 15%;
            animation: gentle-float 3.5s infinite ease-in-out 0.5s;
        }
        
        .bubble-3 {
            width: 12px;
            height: 12px;
            bottom: 20%;
            right: 25%;
            animation: gentle-float 5s infinite ease-in-out 1s;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <div class="login-header-title">智能防红系统</div>
            <div class="login-header-subtitle">企业级安全防护 · 智能化管理平台</div>
            
            <!-- 装饰气泡 -->
            <div class="bubble bubble-1"></div>
            <div class="bubble bubble-2"></div>
            <div class="bubble bubble-3"></div>
            
            <div class="avatar-container">
                <div class="avatar-bg">
                    <svg class="cute-avatar" viewBox="0 0 128 128" xmlns="http://www.w3.org/2000/svg">
                        <!-- 可爱头像 - 熊猫风格 -->
                        <circle cx="64" cy="64" r="60" fill="#ffffff"/>
                        <circle cx="64" cy="64" r="56" fill="#f0f0f0"/>
                        <!-- 耳朵 -->
                        <circle cx="32" cy="32" r="16" fill="#333333"/>
                        <circle cx="96" cy="32" r="16" fill="#333333"/>
                        <!-- 眼睛 -->
                        <circle cx="48" cy="62" r="10" fill="#333333"/>
                        <circle cx="80" cy="62" r="10" fill="#333333"/>
                        <circle cx="50" cy="58" r="3" fill="#ffffff"/>
                        <circle cx="82" cy="58" r="3" fill="#ffffff"/>
                        <!-- 鼻子和嘴 -->
                        <ellipse cx="64" cy="80" rx="8" ry="6" fill="#333333"/>
                        <path d="M50 90 Q64 100 78 90" stroke="#333333" stroke-width="2" fill="none"/>
                    </svg>
                </div>
            </div>
        </div>
        
        <div class="login-body">
            <div class="login-title">管理员登录</div>
            <div class="login-subtitle">欢迎回来，请输入您的账号信息</div>
            
            <form action="#!" method="post" class="login-form">
                <div class="form-group">
                    <i class="fas fa-user input-icon"></i>
                    <input type="text" class="form-control" name="user" placeholder="管理账号" required>
                </div>
                
                <div class="form-group">
                    <i class="fas fa-lock input-icon"></i>
                    <input type="password" class="form-control" name="pass" placeholder="管理密码" required>
                </div>
                
                <button type="submit" class="btn btn-login">
                    <i class="fas fa-sign-in-alt"></i> 安全登录
                </button>
                
                <div class="footer-links">
                    <a href="#" class="footer-link">
                        <i class="fas fa-question-circle"></i> 帮助中心
                    </a>
                    <a href="#" class="footer-link">
                        <i class="fas fa-shield-alt"></i> 安全登录
                    </a>
                    <a href="#" class="footer-link">
                        <i class="fas fa-cog"></i> 高级管理
                    </a>
                </div>
            </form>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>