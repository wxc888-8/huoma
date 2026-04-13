<?php
include('../includes/common.php');
if ($islogin2 == 1) {
} else exit("<script language='javascript'>window.location.href='./login.php';</script>");
?>

<?php
$title = '常见问题';
include('head.php');
?>
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/jquery.nicescroll@3.7.6/jquery.nicescroll.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // 使事件监听器变为被动式的，提高滚动性能
    jQuery.event.special.touchstart = {
        setup: function(_, ns, handle) {
            this.addEventListener("touchstart", handle, { passive: true });
        }
    };
    jQuery.event.special.touchmove = {
        setup: function(_, ns, handle) {
            this.addEventListener("touchmove", handle, { passive: true });
        }
    };
    jQuery.event.special.wheel = {
        setup: function(_, ns, handle) {
            this.addEventListener("wheel", handle, { passive: true });
        }
    };
    jQuery.event.special.mousewheel = {
        setup: function(_, ns, handle) {
            this.addEventListener("mousewheel", handle, { passive: true });
        }
    };
</script>
<style>
    :root {
        --primary: #28a745;
        --primary-light: #5cb85c;
        --primary-dark: #218838;
        --secondary: #6c757d;
        --light: #f8f9fa;
        --dark: #343a40;
        --card-shadow: 0 5px 20px rgba(0,0,0,0.08);
        --border-radius: 12px;
    }
    
    body {
        background-color: #f9f9f9;
        color: #333;
        font-family: 'PingFang SC', 'Microsoft YaHei', sans-serif;
    }
    
    .faq-card {
        background: white;
        border-radius: var(--border-radius);
        box-shadow: var(--card-shadow);
        overflow: hidden;
        border: none;
        margin-bottom: 30px;
    }
    
    .faq-header {
        background: linear-gradient(135deg, var(--primary), var(--primary-dark));
        color: white;
        padding: 20px 25px;
        font-weight: 600;
        font-size: 18px;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .faq-header i {
        font-size: 22px;
    }
    
    .faq-body {
        padding: 25px;
    }
    
    .question-panel {
        background-color: white;
        border-radius: 10px;
        margin-bottom: 15px;
        box-shadow: 0 3px 10px rgba(0,0,0,0.04);
        transition: all 0.3s ease;
        overflow: hidden;
        border: 1px solid rgba(0,0,0,0.05);
    }
    
    .question-panel:hover {
        box-shadow: 0 5px 15px rgba(0,0,0,0.08);
        transform: translateY(-2px);
    }
    
    .question-heading {
        padding: 0;
        background: white;
        border: none;
        position: relative;
    }
    
    .question-heading h4 {
        margin: 0;
    }
    
    .question-heading h4 a {
        display: flex;
        align-items: center;
        padding: 16px 20px;
        color: var(--dark);
        text-decoration: none;
        font-weight: 500;
        justify-content: space-between;
        transition: all 0.3s ease;
    }
    
    .question-heading h4 a:hover {
        color: var(--primary);
    }
    
    .question-heading h4 a:after {
        content: "\F282";
        font-family: "bootstrap-icons";
        font-size: 20px;
        color: var(--primary);
        opacity: 0.7;
        transition: all 0.3s ease;
    }
    
    .question-heading h4 a.collapsed:after {
        content: "\F286";
        opacity: 0.4;
        transform: rotate(0deg);
    }
    
    .panel-collapse {
        border-top: 1px solid rgba(0,0,0,0.05);
    }
    
    .panel-body {
        padding: 20px;
        color: var(--secondary);
        line-height: 1.6;
        font-size: 15px;
    }
    
    /* 内容关键词突出显示 */
    .panel-body b, .panel-body strong {
        color: var(--primary);
        font-weight: 600;
    }
    
    /* 添加动画 */
    .panel-collapse.in, .panel-collapse.collapsing {
        animation: fadeIn 0.5s ease;
    }
    
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    /* 角标样式 */
    .question-number {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 26px;
        height: 26px;
        border-radius: 50%;
        background-color: rgba(40, 167, 69, 0.1);
        color: var(--primary);
        font-weight: 600;
        font-size: 14px;
        margin-right: 12px;
        flex-shrink: 0;
    }
    
    /* 页面标题 */
    .page-title {
        margin-bottom: 30px;
        padding-bottom: 15px;
        border-bottom: 1px solid rgba(0,0,0,0.05);
    }
    
    .page-title h2 {
        margin: 0;
        font-weight: 700;
        color: var(--dark);
        font-size: 24px;
        display: flex;
        align-items: center;
        gap: 12px;
    }
    
    .page-title h2 i {
        color: var(--primary);
        font-size: 26px;
    }
    
    /* 响应式调整 */
    @media (max-width: 768px) {
        .faq-header {
            padding: 16px 20px;
            font-size: 16px;
        }
        
        .faq-body {
            padding: 20px 15px;
        }
        
        .question-heading h4 a {
            padding: 14px 16px;
            font-size: 15px;
        }
        
        .panel-body {
            padding: 15px;
            font-size: 14px;
        }
    }
</style>

<section id="main-content">
    <div class="wrapper">
        <div class="row">
            <div class="col-sm-12">
                <div class="page-title">
                    <h2><i class="bi bi-question-circle-fill"></i> 常见问题解答</h2>
                </div>
                
                <div class="faq-card">
                    <div class="faq-header">
                        <i class="bi bi-info-circle"></i> 帮助中心
                    </div>
                    <div class="faq-body">
                        <div id="accordion">
                            <div class="question-panel">
                                <div class="question-heading">
                                    <h4>
                                        <a data-toggle="collapse" data-parent="#accordion" href="#collapseOne">
                                            <span><span class="question-number">1</span> 怎么生成短网址？</span>
                                        </a>
                                    </h4>
                                </div>
                                <div id="collapseOne" class="panel-collapse in" style="height: auto;">
                                    <div class="panel-body">
                                        在<strong>用户首页</strong>可以批量生成短网址，如果网址需要具备更多功能，请在<strong>网址列表</strong>里面添加。
                                    </div>
                                </div>
                            </div>
                            
                            <div class="question-panel">
                                <div class="question-heading">
                                    <h4>
                                        <a data-toggle="collapse" data-parent="#accordion" href="#collapseTwo" class="collapsed">
                                            <span><span class="question-number">2</span> 为什么不能修改网址信息？</span>
                                        </a>
                                    </h4>
                                </div>
                                <div id="collapseTwo" class="panel-collapse collapse" style="height: 0px;">
                                    <div class="panel-body">
                                        修改网址是<strong>会员</strong>才有的功能，您需要购买会员才可以修改网址信息。
                                    </div>
                                </div>
                            </div>
                            
                            <div class="question-panel">
                                <div class="question-heading">
                                    <h4>
                                        <a data-toggle="collapse" data-parent="#accordion" href="#collapseThree" class="collapsed">
                                            <span><span class="question-number">3</span> 为什么有些短网址无法生成？</span>
                                        </a>
                                    </h4>
                                </div>
                                <div id="collapseThree" class="panel-collapse collapse" style="height: 0px;">
                                    <div class="panel-body">
                                        有些短网址是<strong>第三方接口</strong>，有可能限制了生成，还有部分短网址是<strong>收费</strong>的，需要购买短网址点数才能生成。
                                    </div>
                                </div>
                            </div>
                            
                            <div class="question-panel">
                                <div class="question-heading">
                                    <h4>
                                        <a data-toggle="collapse" data-parent="#accordion" href="#collapseFourth" class="collapsed">
                                            <span><span class="question-number">4</span> 普通跳转，防红跳转，直链跳转，直接跳转有什么区别？</span>
                                        </a>
                                    </h4>
                                </div>
                                <div id="collapseFourth" class="panel-collapse collapse" style="height: 0px;">
                                    <div class="panel-body">
                                        <p><strong>普通跳转</strong>：经过本站的域名进行跳转，不具备防红功能，可以修改网址信息</p>
                                        <p><strong>防红跳转</strong>：用户在QQ或者微信打开你生成的网址，会提示用户用浏览器打开</p>
                                        <p><strong>直链跳转</strong>：用户可以在QQ微信直接打开被腾讯拦截了的网址</p>
                                        <p><strong>直接跳转</strong>：直接通过缩短的链接跳转，不经过本网站，所以不能修改网址信息和查看统计信息</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="question-panel">
                                <div class="question-heading">
                                    <h4>
                                        <a data-toggle="collapse" data-parent="#accordion" href="#collapseFive" class="collapsed">
                                            <span><span class="question-number">5</span> 为什么网址被封禁了？</span>
                                        </a>
                                    </h4>
                                </div>
                                <div id="collapseFive" class="panel-collapse collapse" style="height: 0px;">
                                    <div class="panel-body">
                                        本站禁止生成<strong>违法链接</strong>，发现了后会对违法链接进行封禁处理，严重者还会受到<strong>封号惩罚</strong>。
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="text-center" style="margin-top: 20px;">
                    <p class="text-muted">如果您有其他问题，请随时 <a href="contact.php" style="color: var(--primary); text-decoration: none;">联系我们</a></p>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
    $(document).ready(function(){
        // 增强折叠效果动画
        $('#accordion').on('show.bs.collapse', function (e) {
            $(e.target).prev('.question-heading').find('a').removeClass('collapsed');
        });
        
        $('#accordion').on('hide.bs.collapse', function (e) {
            $(e.target).prev('.question-heading').find('a').addClass('collapsed');
        });
        
        // 添加鼠标悬停效果
        $('.question-panel').hover(
            function() {
                $(this).find('a').css('color', 'var(--primary)');
            },
            function() {
                if (!$(this).find('.panel-collapse').hasClass('in')) {
                    $(this).find('a').css('color', 'var(--dark)');
                }
            }
        );
    });
</script>

<script src="../static/user/js/common-scripts.js"></script>