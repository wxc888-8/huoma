<?php

/**
 * 找回密码
 **/
$is_defend = true;
include("../includes/common.php");
if (isset($_GET['act']) && $_GET['act'] == 'qrlogin') {
    if (isset($_SESSION['findpwd_qq']) && $qq = $_SESSION['findpwd_qq']) {
        $rs = $DB->query("select * from dwz_user where qq='$qq' limit 1");
        $res = $DB->fetch($rs);
        unset($_SESSION['findpwd_qq']);
        if ($res['user']) {
            $session = md5($res['user'] . $res['pwd'] . $password_hash);
            $token = authcode("{$res['id']}\t{$session}", 'ENCODE', SYS_KEY);
            setcookie("user_token", $token, time() + 604800, '/');
            $DB->query("update dwz_user set lasttime='$date' where id='{$res["id"]}'");
            log_result('找回密码', 'id:' . $res['id'] . ',ip:' . $clientip, '登录成功');
            exit('{"code":1,"msg":"登录成功，请在用户资料设置里重置密码","url":"./"}');
        } else {
            @header('Content-Type: application/json; charset=UTF-8');
            exit('{"code":-1,"msg":"当前QQ不存在，请注册！","url":"./reg.php"}');
        }
    } else {
        @header('Content-Type: application/json; charset=UTF-8');
        exit('{"code":-2,"msg":"验证失败，请重新扫码"}');
    }
} elseif (isset($_GET['act']) && $_GET['act'] == 'qrcode') {
    $image = trim($_POST['image']);
    $result = qrcodelogin($image);
    exit(json_encode($result));
} elseif ($islogin2 == 1) {
    @header('Content-Type: text/html; charset=UTF-8');
    exit("<script language='javascript'>alert('您已登陆！');window.location.href='./';</script>");
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>找回密码 | <?php echo $conf['web_name'] ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
    <link rel="stylesheet" href="../static/user/css/bootstrap.min.css" type="text/css" />
    <link rel="stylesheet" href="https://cdn.staticfile.org/font-awesome/4.7.0/css/font-awesome.css">
    <link rel="stylesheet" href="../static/user/css/style.css" type="text/css" />
    <script src="https://cdn.staticfile.org/jquery/2.1.1/jquery.min.js"></script>
    <script src="https://cdn.staticfile.org/twitter-bootstrap/3.3.7/js/bootstrap.min.js"></script>
</head>

<body class="login-body">
    <div class="container">
        <form class="form-signin">
            <h2 class="form-signin-heading">找回密码</h2>
            <div class="login-wrap" style="text-align: center;">
                <div class="list-group-item list-group-item-info" style="font-weight: bold;" id="login">
                    <span id="loginmsg">请使用QQ手机版扫描二维码</span><span id="loginload" style="padding-left: 10px;color: #790909;">.</span>
                </div>
                <div id="qrimg">
                </div>
                <div class="list-group-item" id="mobile" style="display:none;"><button type="button" id="mlogin" onclick="mloginurl()" class="btn btn-warning btn-block">跳转QQ快捷登录</button><br /><button type="button" onclick="loadScript()" class="btn btn-success btn-block">我已完成登录</button></div>
                <label class="checkbox">
                    <a href="./login.php" class="btn btn-lg btn-login btn-block" type="submit">返回登录</a>
                </label>
            </div>

        </form>
    </div>
    <script src="../static/user/js/qrlogin.js"></script>
</body>

</html>