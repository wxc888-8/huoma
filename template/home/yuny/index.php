<?php
if (!defined('IN_CRONLITE')) exit();
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width,initial-scale=1,user-scalable=0,viewport-fit=cover">
		 <title><?php echo $conf['web_name'] . '-' . $conf['title']; ?></title>
    <meta name="keywords" content="<?php echo $conf['keywords']; ?>">
    <meta name="description" content="<?php echo $conf['description']; ?>">
    <link rel="shortcut icon" href="./static/picture/favicon.ico">
		<link rel="stylesheet" href="../static/yuny/css/uikit.min.css">
		<link rel="stylesheet" href="../static/yuny/css/css-6.css">
	<style>
    body {
      background:   background: #00537e; background: -moz-linear-gradient(to bottom,  #00537e 0%,#3aa17e 100%); background: -webkit-linear-gradient(to bottom,  #00537e 0%,#3aa17e 100%); background: linear-gradient(to bottom,  #00537e 0%,#3aa17e 100%);
    
        background-repeat: no-repeat; /* 背景图片不重复 */
        background-size: cover; /* 背景图片覆盖整个屏幕 */
        background-position: center center; /* 背景图片居中 */
        background-attachment: fixed; /* 固定背景图片 */
    }
</style>
 <script src="https://lib.baomitu.com/jquery/2.2.4/jquery.min.js"></script>
    <script>
        $(document).ready(function() {
            $("#search").css("height", ($(window).height()) + "px");
            $("#search").css("margin-top", "-65px");
            $('#search-kw').click(function() {
                $("#dwz-type").removeClass("hide");
                $("#dwz-pattern").removeClass("hide");
            });
            $('#search-btn').click(function() {
                $("#dwzdate").hide();
                $('#search-btn').text('生成中...');
                var stype = $("select[id='dwz-type']").val();
                var pattern = $("select[id='dwz-pattern']").val();
                var url = $("input[id='search-kw']").val();
                url = url.replace(/\+/g, "%2B");
                url = url.replace(/\&/g, "%26");
                $.ajax({
                    type: "post",
                    url: "ajax.php?act=creat1",
                    dataType: "json",
                    data: 'url=' + url + '&type=' + stype + '&pattern=' + pattern,
                    success: function(obj) {
                        $('#search-btn').text('生成一下');
                        if (obj.code == 0) {
                            $("#dwzdate").html(
                                '短链地址：' + obj.dwz + '<br />' +
                                '数据统计：<a href="' + obj.data + '" target="_blank">点击查看</a><br />' +
                                '跳转网址：' + atob(obj.url) + '<br />' +
                                '<br /><img class="qrimg" width="200px" src="includes/libs/qrcode.php?size=300&text=' + obj.dwz + '" />'
                            );
                            $("#dwzdate").slideDown();
                        } else if (obj.code == -1) {
                            $("#dwzdate").html(
                                obj.msg
                            );
                            $("#dwzdate").slideDown();
                        } else {
                            alert(obj.msg);
                        }
                    },
                    error: function(a) {
                        $('#search-btn').text('生成失败');
                    }
                });
            });
            $("#search").css("background", "#000000 url('<?php echo $background_image; ?>') no-repeat right center");
            $("#search").css("background-size", "100% 100%");
        });
    </script>
	</head>
	<body >
		<div>
			<div class="uk-section-primary tm-section-textureuk-card" id="diy-bg">
				<div uk-sticky="media: 960" class="uk-navbar-container tm-navbar-container uk-navbar-transparent uk-sticky uk-sticky-fixed">
					<div class="uk-container uk-container-expand uk-animation-slide-left">
						<nav class="uk-navbar">
							<div class="uk-navbar-left">
								<a href="" class="uk-navbar-item uk-logo uk-active" style="color: #FFF;">
									<canvas width="28" height="34" uk-svg="" src="/assets/images/logo.svg" class="uk-margin-small-right" hidden="true"></canvas>
									<?php echo  $conf['web_name']; ?>
								</a>
							</div>
							<div class="uk-navbar-right">
								<ul class="uk-navbar-nav uk-visible@m">
								<!--api为弹窗无需改动-->
	<li class=""><a href="#modal-api" uk-toggle="">API接口</a></li>
	<li><a href="<?php echo $conf['group_link'] ?>" target="_blank">加入交流群</a></li>
	<li class=""><a href="http://wpa.qq.com/msgrd?v=3&uin=<?php echo $conf['kf_qq'];?>" target="_blank">购买源码</a></li>
	<li class=""><a href="#modal-api1" uk-toggle="">付费价格</a></li>
								</ul>
								<div class="uk-navbar-item uk-visible@m">
								    <a href="http://wpa.qq.com/msgrd?v=3&uin=<?php echo $conf['kf_qq'];?>" class="uk-button uk-button-default tm-button-default uk-icon" uk-icon="icon: settings" style="color: #FFF;">购买无广告付费版</a>
								</div>
								<a uk-navbar-toggle-icon="" href="#offcanvas" uk-toggle="" class="uk-navbar-toggle uk-hidden@m uk-icon uk-navbar-toggle-icon">
									<svg width="20" height="20" viewbox="0 0 20 20" xmlns="http://www.w3.org/2000/svg" data-svg="navbar-toggle-icon"><rect y="9" width="20" height="2"></rect><rect y="3" width="20" height="2"></rect><rect y="15" width="20" height="2"></rect></svg>
								</a>
							</div>
						</nav>
					</div>
				</div>
				<div uk-height-viewport="offset-top: true; offset-bottom: true" class="uk-section uk-section-small uk-flex uk-flex-middle uk-text-center uk-animation-slide-left">
					<div class="uk-width-1-1" uk-height-viewport="expand: true">
						<div class="uk-container">
							<p style="padding-bottom: 30px;">
								<!--<img src="../static/yuny/picture/logo.png" width="280" height="100">-->
								 <img src="static/picture/logo.png" alt="&#39;zhidu logo">
							</p>
							<form>
							<h2><?php echo  $conf['web_name']; ?>，永久免费使用！</h2>
								<div class="uk-margin" uk-margin="">
									<div class="uk-inline uk-width-1-3@s">
										<a class="uk-form-icon" href="#" uk-icon="icon: link"></a>
										<input class="uk-input" type="search" id="search-kw" name="longurl" placeholder="https://www.baidu.com/" autocomplete="off">
									</div>
								<div uk-form-custom="target: > * > span:first-child">
										<select class="form-control" id="dwz-type" name="dwz-type" default="986">
                                      <?php
                                      echo dwzList();
                                           ?>
										</select>
										<button class="uk-button uk-button-default" type="button" tabindex="-1">
											<span></span>
											<span uk-icon="icon: chevron-down"></span>
										</button>
									</div>
								<div uk-form-custom="target: > * > span:first-child">
										<select class="form-control" id="dwz-pattern" name="dwz-pattern" default="0">
                                              <option value="0">  <?php
                                                echo pattern_list();
                                                ?></option>
										</select>
										<button class="uk-button uk-button-default" type="button" tabindex="-1">
											<span></span>
											<span uk-icon="icon: chevron-down"></span>
										</button>
									</div>
								</div>
							</form>
						<div uk-grid="" class="uk-child-width-auto uk-grid-medium uk-flex-inline uk-flex-center uk-grid">
								<div class="uk-first-column" style="padding-top: 15px;">
									<button class="uk-button uk-button-default" type="submit" id="search-btn" name="dwz-btn">立刻生成</button>
								</div>
							</div>
							<div class="center-block" id="dwzdate" style="padding: 15px; border: 1px solid transparent;margin-bottom: 20px;margin-top: 20px;background: rgba(132, 131, 137, 0.67); color: #FFF; font-size:16px;text-align:center;display:none;"></div>
							</div>
							<br><br><b><font style="color:#e92b00">公告</font></b>：	域名被腾讯软件屏蔽了，导致无法打开，影响业务推广，防红帮你解决在QQ、TIM/微信上被拦截打开问题。
							<!--自定义html广告-->
							
								<div class="uk-align-center uk-visible@l" style="width: 70%;">
									<hr class="uk-divider-icon">
									<!--公告开始-->
	<ul class="uk-subnav uk-subnav-pill" uk-switcher="animation: uk-animation-slide-left-medium, uk-animation-slide-right-medium">
	    <li><a href="#">站点公告</a></li>
	    <li><a href="#">广告投放</a></li>
	    <li><a href="#">专业版介绍</a></li>
	</ul>
	<ul class="uk-switcher uk-margin">
	    <li class="uk-text-bold" style="color:#FFF">免责声明：短网址由用户生成，所跳转的内容与本站无关。<br>本站严禁<strong>钓鱼、诈骗</strong>等一切违法犯罪网站使用，如有发现立刻拉黑封停</li>
	    <li class="uk-text-bold" style="color:#FFF">客服QQ：<?php echo $conf['kf_qq']; ?> 联系请直奔主题说明来意 <a class="uk-button uk-button-default uk-button-small" href="https://api.btstu.cn/qqtalk/api.php?qq=<?php echo $conf['kf_qq']; ?>" target="_blank">联系客服</a></li>
	    <li class="uk-text-bold" style="color:#FFF">专业版请登入用户中心购买 可以自定义切换跳转模版 支持直链跳转 无限制访问生成等
	    <a class="uk-button uk-button-default uk-button-small" href="/user/reg.php" target="_blank">用户登陆注册</a></li>
	</ul>
	<!--广告开始-->
	<!--<a href="https://blog.302.com" target="_blank"><img src="http://img.2daigua.com/ds.gif" style="width: 100%;height: 70px;"></a>
	<a href="https://blog.302.com" target="_blank"><img src="http://img.2daigua.com/ds.gif" style="width: 100%;height: 70px;padding-top: 3px;"></a>-->
								</div>
							<div class="uk-align-center uk-hidden@l" style="width: 100%;">
									<hr class="uk-divider-icon">
									<!--公告开始-->
	<ul class="uk-subnav uk-subnav-pill" uk-switcher="animation: uk-animation-slide-left-medium, uk-animation-slide-right-medium">
	    <li><a href="#">站点公告</a></li>
	    <li><a href="#">广告投放</a></li>
	    <li><a href="#">专业版介绍</a></li>
	</ul>
	<ul class="uk-switcher uk-margin">
	    <li class="uk-text-bold" style="color:#FFF">免责声明：短网址由用户生成，所跳转的内容与本站无关。<br>本站严禁<strong>钓鱼、诈骗</strong>等一切违法犯罪网站使用，如有发现立刻拉黑封停</li>
	    <li class="uk-text-bold" style="color:#FFF">客服QQ：787447 联系请直奔主题说明来意 <a class="uk-button uk-button-default uk-button-small" href="http://wpa.qq.com/msgrd?v=3&uin=787447&site=qq&menu=yes" target="_blank">联系客服</a></li>
	    <li class="uk-text-bold" style="color:#FFF">专业版请登入用户中心购买 可以自定义切换跳转模版 支持直链跳转 无限制访问生成等</li>
	</ul>


								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="uk-section-small">
					<div class="uk-container uk-container-expand uk-text-center uk-position-relative">
						
						<ul uk-margin="" class="uk-subnav tm-subnav uk-flex-inline uk-flex-center uk-margin-remove-bottom">
							<li class="uk-first-column">
								<span>接口域名 <span id="apinum">获取中...</span>个</span>
							</li>
							<li>
								<a href="#">
									<span uk-icon="star" class="uk-margin-small-right uk-icon">
										<svg width="20" height="20" viewbox="0 0 20 20" xmlns="http://www.w3.org/2000/svg" data-svg="star">
											<polygon fill="none" stroke="#000" stroke-width="1.01" points="10 2 12.63 7.27 18.5 8.12 14.25 12.22 15.25 18 10 15.27 4.75 18 5.75 12.22 1.5 8.12 7.37 7.27"></polygon>
										</svg>
									</span>
									<span uikit-stargazers="">生成网址</span> <span id="count5">获取中...个
								</a>
							</li>
							<li>
								<a href="#" class="uk-text-lowercase">
									<span uk-icon="twitter" class="uk-margin-small-right uk-icon">
										<svg width="20" height="20" viewbox="0 0 20 20" xmlns="http://www.w3.org/2000/svg" data-svg="twitter">
											<path d="M19,4.74 C18.339,5.029 17.626,5.229 16.881,5.32 C17.644,4.86 18.227,4.139 18.503,3.28 C17.79,3.7 17.001,4.009 16.159,4.17 C15.485,3.45 14.526,3 13.464,3 C11.423,3 9.771,4.66 9.771,6.7 C9.771,6.99 9.804,7.269 9.868,7.539 C6.795,7.38 4.076,5.919 2.254,3.679 C1.936,4.219 1.754,4.86 1.754,5.539 C1.754,6.82 2.405,7.95 3.397,8.61 C2.79,8.589 2.22,8.429 1.723,8.149 L1.723,8.189 C1.723,9.978 2.997,11.478 4.686,11.82 C4.376,11.899 4.049,11.939 3.713,11.939 C3.475,11.939 3.245,11.919 3.018,11.88 C3.49,13.349 4.852,14.419 6.469,14.449 C5.205,15.429 3.612,16.019 1.882,16.019 C1.583,16.019 1.29,16.009 1,15.969 C2.635,17.019 4.576,17.629 6.662,17.629 C13.454,17.629 17.17,12 17.17,7.129 C17.17,6.969 17.166,6.809 17.157,6.649 C17.879,6.129 18.504,5.478 19,4.74"></path>
										</svg>
									</span>
								
								</a>
							</li>
						
						</ul>
					</br>
					<p></p>
					 <center>
					    <strong style="color: #FFF; font-weight: bold;">友情链接：</strong>
					   <a style="color: #FFF;" href="#" target="_blank">友情链接1</a>｜
                       <a style="color: #FFF;" href="#" target="_blank">友情链接2</a>｜
  
      </center>
					<p></p>
					 <p><a href="http://beian.miit.gov.cn" target="_blank" style="text-decoration:none;"><?php echo $conf['icp'] ?></a></p>
        © 2019-2023  <a href="/" target="_blank"><?php echo $conf['web_name']; ?></a>Design .
       
					</div>
				</div>
			</div>
			<div id="offcanvas" uk-offcanvas="mode: push; overlay: true" class="uk-offcanvas" style="">
				<div class="uk-offcanvas-bar">
					<div class="uk-panel">
						<ul class="uk-nav uk-nav-default tm-nav">
							<li class="uk-nav-header">功能菜单</li>
							<!--api为弹窗无需改动-->
	<li class=""><a href="#modal-api" uk-toggle="">API接口</a></li>
	<li><a href="<?php echo $conf['group_link'] ?>" target="_blank">加入交流群</a></li>
	<li class=""><a href="http://wpa.qq.com/msgrd?v=3&uin=<?php echo $conf['kf_qq'];?>" target="_blank">购买源码</a></li>
		<li class=""><a href="#modal-api1" uk-toggle="">付费价格</a></li>
							<div class="uk-navbar-item">
							    <a href="http://dh.yyfh.com/" class="uk-button uk-button-default tm-button-default uk-icon" uk-icon="icon: settings" style="color: #FFF;">生成专业版</a>
							</div>
						</ul>
					</div>
				</div>
			</div>
		</div>
		<div id="modal-api" bg-close="false" uk-modal="">
		    <div class="uk-modal-dialog uk-modal-body">
		        <h2 class="uk-modal-title">API接口文档</h2>
		        <li class="uk-text-bold" style="color:#FFF"> 
		        			<a class="uk-button uk-button-default uk-button-small" href="/user" target="_blank">用户登陆</a>&nbsp;&nbsp;
	                        <a class="uk-button uk-button-default uk-button-small" href="/user/reg.php" target="_blank">用户注册</a></li>
		        <ul uk-accordion="" id="isapi">
		        	<li class="uk-open">
		        		<a class="uk-accordion-title" href="#">彩虹代刷接口</a>
		        		<div class="uk-accordion-content">
		        			<p>直接在代刷后台-其他组件-防红接口设置-接口地址，填写接口API即可使用！</p>
		        			<hr>
		        			<p>接口API：<code>请登录获取</code></p>
		        		<!--	<p>如需纯文本格式请带上<code>format=txt</code></p>
		        			<p>如需二维码格式请带上<code>format=qrcode</code></p>
		        			<p>例：https://www.yyfh.com/dwz.php?format=txt&longurl=</p>-->
		        		</div>
		        	</li>
		        	<li>
		        		<a class="uk-accordion-title" href="#">API错误代码</a>
		        		<div class="uk-accordion-content">
							10001：URL不能为空<br>
							10002：URL地址错误<br>
							10003：当前IP已被拉黑<br>
							10004：当前IP生成频率太高，已禁止生成<br>
							10005：当前域名已被拉黑<br>
							10006：未知错误，联系管理员<br>
							10007：防洪接口异常，联系管理员
		        		</div>
		        	</li>
		        	<li>
		        		<a class="uk-accordion-title" href="#">提示黑名单</a>
		        		<div class="uk-accordion-content">
		        			1.域名被拉黑，可能您的域名有违规情况，出现<strong>博彩、钓鱼、黄、毒、反动</strong>等因素！<br>2.IP被拉黑，系统检测到您
							<strong>频繁生成、判断非人工访问、自动屏蔽</strong>，请联系官方群管理！
		        		</div>
		        	</li>	
		        </ul>
		        <p class="uk-text-right">
		            <button class="uk-button uk-button-primary uk-modal-close" type="button">知道了</button>
		        </p>
		    </div>
		</div>
		
				<div id="modal-api1" bg-close="false" uk-modal="">
		    <div class="uk-modal-dialog uk-modal-body">
		        <h2 class="uk-modal-title">VIP付费防红价格</h2>
		        <ul uk-accordion="" id="isapi">
		        	<li class="uk-open">
		        		<a class="uk-accordion-title" href="#">跳转VIP版</a>
		        		<div class="uk-accordion-content">

		        			<p>		        		单域名授权
15元7天<br>
25元30天<br>
75元90天<br>
150元180天<br></p>
		        			<hr>
		        			<p>泛域名授权<br>

199元7天<br>
666元30天<br>
2288元90天<br>
4299元180天<br></p>
		        			
		        		</div>
		        	</li>
		        	<li>
		        		<a class="uk-accordion-title" href="#">直连强开VIP版</a>
		        		<div class="uk-accordion-content">
						<p>		单域名授权<br>

166元7天<br>
500元30天<br>
1425元90天<br>
2700元180天<br></p>
	<hr>
	
	
		<p>泛域名授权<br>


666元7天<br>
2000元30天<br>
5699元90天<br>
10800元180天<br></p>
		        		</div>
		        	</li>
		        
		        </ul>
		        <p class="uk-text-right">
		            <button class="uk-button uk-button-primary uk-modal-close" type="button">知道了</button>
		        </p>
		    </div>
		</div>
		
		
		
		
		<div id="modal-url" bg-close="false" uk-modal="">
		    <div class="uk-modal-dialog uk-modal-body">
		        <span class="uk-label uk-label-success">成功生成防红链接请直接复制</span>
		        <div class="uk-margin uk-text-center" uk-margin="">
		        	<div class="uk-inline uk-width-1-1@s">
		        		<a class="uk-form-icon" href="#" uk-icon="icon: link"></a>
		        		<input style="padding-right: 80px!important;" class="uk-input" id="isurl" name="v-url" disabled="">
						<button style="width: 80px!important;border:none;cursor: pointer;" class="uk-form-icon uk-form-icon-flip" id="copyrul">复制链接</button>
		        	</div>
					<p style="padding-top:15px;margin-bottom:5px">二维码(长按保存)</p>
					<p id="qrcode" style="padding:0 0 8px 0;"></p>
				</div>
				<p class="uk-text-center">
				    <button class="uk-button uk-modal-close" type="button">关闭</button>
				</p>
		    </div>
		</div>
	</body>
	<script src="../static/yuny/js/uikit.min.js"></script>
   <script src="../static/yuny/js/uikit-icons.min.js"></script>
	<div>
</div>

</html>
 