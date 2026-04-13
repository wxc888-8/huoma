<?php
include('../includes/common.php');
if ($islogin != 1) {
    exit('<script language=\'javascript\'>window.location.href=\'./login.php\';</script>');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.bootcss.com/twitter-bootstrap/3.0.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="../static/user/css/bootstrap-reset.css" rel="stylesheet">
    <script src="https://cdn.bootcss.com/jquery/1.10.2/jquery.min.js"></script>
    <script src="https://cdn.bootcss.com/twitter-bootstrap/3.0.0/js/bootstrap.min.js"></script>
</head>
<section id="main-content">
    <div class="wrapper">
        <div class="col-sm-12">
            <div class="panel panel-default">
              <!--  <div class="panel-heading font-bold" style="background-color: #FF6C60;color: white;">使用帮助</div>  -->
                <div class="panel-body">
                    <div id="accordion">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h4 class="panel-title">
                                    <a data-toggle="collapse" data-parent="#accordion" href="#collapseOne" style='color:#551A8B' >程序安装</a>
                                </h4>
                            </div>
                            <div id="collapseOne" class="panel-collapse in" style="height: auto;">
                                <div class="panel-body">
 
<h4>第一次安装请下载完整包，后续更新请下载更新包或者直接在站点后台更新</h4>
<h4 style="color:blue">环境要求</h4>
php版本：php56-php74（建议使用php7.0及以上版本）
<br>
<h4 style="color:blue">程序安装</h4>
把下载好的安装包放入站点的根目录解压，访问站点的域名开始安装程序
<br>
填好数据库配置，安装完成
<br>
后台目录：域名/admin
<br>
后台账号：admin
<br>
后台密码：123456
                              </div>  
                            </div>
                        </div>
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h4 class="panel-title">
                                    <a data-toggle="collapse" data-parent="#accordion" href="#collapseTwo" class="collapsed" style='color:#551A8B'>温馨提示</a>
                                </h4>
                            </div>
                            <div id="collapseTwo" class="panel-collapse collapse" style="height: 0px;">
                                <div class="panel-body">
 <h5 style="color:blue">进入后台后，请先修改默认的后台账号密码</h5>
 <h4 style="color:blue">站点域名</h4>
设置好站点的域名，用户对接api等等需要使用到<br>
<img alt="" src="../static/admin/images/jiaocheng.png" width="1000px" height="800px">
             </div>
                            </div>
                        </div>
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h4 class="panel-title">
                                    <a data-toggle="collapse" data-parent="#accordion" href="#collapseThree" class="collapsed" style='color:#551A8B'>伪静态</a>
                                </h4>
                            </div>
                            <div id="collapseThree" class="panel-collapse collapse" style="height: 0px;">
                                <div class="panel-body">
<h4 style="color:blue">Apache伪静态规则</h4>
<?php echo base64_decode('PA==');?>
    IfModule mod_rewrite.c
<?php echo base64_decode('Pg==');?> <br>   
    RewriteEngine On<br> 
    RewriteCond %{REQUEST_FILENAME} !-f<br> 
    RewriteCond %{REQUEST_FILENAME} !-d<br> 
<br> 
    #RewriteRule . tz.php<br> 
    RewriteRule (.*)$ tz.php?id=$1 [L]<br> 
<?php echo base64_decode('PA==');?>
    IfModule
<?php echo base64_decode('Pg==');?> <br> 
<h4 style="color:blue">nginx伪静态规则</h4>
location / {<br>
	if (!-f $request_filename){<br>
		set $rule_0 1$rule_0;<br>
	}<br>
	if (!-d $request_filename){<br>
		set $rule_0 2$rule_0;<br>
	}<br>
	if ($rule_0 = "21"){<br>
		rewrite /mm(.*)$ /xend.php?id=$1 last;<br>
	}<br>
	if ($rule_0 = "21"){<br>
		rewrite /jump1(.*)$ /template/jump/jump1/$1 last;<br>
	}<br>
		if ($rule_0 = "21"){<br>
		rewrite /jump2(.*)$ /template/jump/jump2/$1 last;<br>
	}        <br>
	if ($rule_0 = "21"){<br>
		rewrite /jump3(.*)$ /template/jump/jump3/$1 last;<br>
	}<br>
		if ($rule_0 = "21"){<br>
		rewrite /jump4(.*)$ /template/jump/jump4/$1 last;<br>
	}<br>
			if ($rule_0 = "21"){<br>
		rewrite /jump5(.*)$ /template/jump/jump5/$1 last;<br>
	}<br>
		if ($rule_0 = "21"){<br>
		rewrite /zl(.*)$ /zhilian.php$1 last;<br>
	}<br>
	if ($rule_0 = "21"){<br>
		rewrite /(.*)$ /tz.php?id=$1 last;<br>
	}<br>
}

                                </div>
                            </div>
                        </div>
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h4 class="panel-title">
                                    <a data-toggle="collapse" data-parent="#accordion" href="#collapseFourth" class="collapsed" style='color:#551A8B'>后台配置</a>
                                </h4>
                            </div>
                            <div id="collapseFourth" class="panel-collapse collapse" style="height: 0px;">
                                <div class="panel-body">
<h4 style="color:blue">域名管理</h4>                                    
在使用第三方短网址时，需要用到自己域名当中转，所以需要在域名管理添加一个通用入口域名来保障短网址的正常生成<br><br>
<img alt="" src="../static/admin/images/jiaocheng1.png" width="800px" height="600px">
<br><br><h4 style="color:blue">设置监控</h4>     
按提示设置监控<br>
<img alt="" src="../static/admin/images/jiaocheng2.png" width="800px" height="600px">

                                </div>
                            </div>
                        </div>
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h4 class="panel-title">
                                    <a data-toggle="collapse" data-parent="#accordion" href="#collapseFive" class="collapsed" style='color:#551A8B'>短网址设置</a>
                                </h4>
                            </div>
                            <div id="collapseFive" class="panel-collapse collapse" style="height: 0px;">
                                <div class="panel-body">
<h4 style="color:blue">自己域名作为短网址</h4>
<img alt="" src="../static/admin/images/jiaocheng3.png" width="800px" height="600px"><br>
<h4 style="color:blue">同系统短网址对接</h4>
<img alt="" src="../static/admin/images/jiaocheng4.png" width="800px" height="600px">
<h4 style="color:blue">第三方短网址对接</h4>
这个第三方只能对接授权商短网址提供的第三方短网址，具体对接方式联系授权商<br>
<h4 style="color:blue">自定义对接</h4>
首先需要去网站根目录的includes/libs/dwz.class.php里面按示例的格式对接好接口，然后在后台设置好对接信息即可
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
</section>

