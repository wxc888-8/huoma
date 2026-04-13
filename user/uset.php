<?php
include("../includes/common.php");
$mod = isset($_GET['mod']) ? $_GET['mod'] : null;

// 设置页面标题
if($mod == 'user'){
    $title = '用户资料设置';
} elseif($mod == 'token'){
    $title = 'API接口设置';
} else {
    $title = '用户设置';
}

include 'head.php';
?>
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/jquery.nicescroll@3.7.6/jquery.nicescroll.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/layer@3.5.1/dist/layer.min.js"></script>
<script>
    // 设置事件监听器为被动模式，提高滚动性能
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
    jQuery.event.special.scroll = {
        setup: function(_, ns, handle) {
            this.addEventListener("scroll", handle, { passive: true });
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
    
    .page-title {
        margin-bottom: 25px;
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
    
    .card {
        background: white;
        border-radius: var(--border-radius);
        box-shadow: var(--card-shadow);
        overflow: hidden;
        border: none;
        margin-bottom: 30px;
        transition: all 0.3s ease;
    }
    
    .card:hover {
        box-shadow: 0 8px 25px rgba(0,0,0,0.1);
        transform: translateY(-2px);
    }
    
    .card-header {
        background: linear-gradient(135deg, var(--primary), var(--primary-dark));
        color: white;
        padding: 18px 25px;
        font-weight: 600;
        font-size: 18px;
        border: none;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .card-header i {
        font-size: 20px;
    }
    
    .card-body {
        padding: 25px;
    }
    
    .form-group {
        margin-bottom: 25px;
    }
    
    .form-control {
        border-radius: 8px;
        padding: 12px 15px;
        border: 1px solid #e1e1e1;
        height: 48px;
        font-size: 15px;
        box-shadow: none !important;
        transition: all 0.3s ease;
        background-color: #fff;
        color: #333;
    }
    
    .form-control:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(40, 167, 69, 0.15) !important;
    }
    
    .form-control::placeholder {
        color: #aaa;
    }
    
    .control-label {
        font-weight: 600;
        color: #444;
        margin-bottom: 8px;
    }
    
    .btn {
        padding: 10px 24px;
        font-weight: 600;
        border-radius: 8px;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }
    
    .btn-success {
        background-color: var(--primary);
        border-color: var(--primary);
    }
    
    .btn-success:hover {
        background-color: var(--primary-dark);
        border-color: var(--primary-dark);
        box-shadow: 0 5px 15px rgba(40, 167, 69, 0.3);
        transform: translateY(-2px);
    }
    
    .btn-danger {
        background-color: #dc3545;
        border-color: #dc3545;
    }
    
    .btn-danger:hover {
        background-color: #c82333;
        border-color: #bd2130;
        box-shadow: 0 5px 15px rgba(220, 53, 69, 0.3);
        transform: translateY(-2px);
    }
    
    .table {
        background: white;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        margin-top: 15px;
    }
    
    .table thead {
        background-color: rgba(40, 167, 69, 0.08);
    }
    
    .table thead td {
        border-top: none;
        color: #444;
        font-weight: 600;
        padding: 12px 15px;
    }
    
    .table tbody td {
        padding: 12px 15px;
        vertical-align: middle;
        border-top: 1px solid rgba(0,0,0,0.03);
    }
    
    .table-striped tbody tr:nth-of-type(odd) {
        background-color: rgba(0,0,0,0.01);
    }
    
    .api-section {
        margin-top: 40px;
    }
    
    .api-section h3 {
        font-size: 20px;
        font-weight: 600;
        color: #333;
        margin-bottom: 15px;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .api-section h3 i {
        color: var(--primary);
    }
    
    .api-section p {
        margin-bottom: 10px;
        color: #555;
    }
    
    .api-url {
        background-color: #f5f5f5;
        padding: 12px 15px;
        border-radius: 6px;
        font-family: monospace;
        font-size: 14px;
        color: #333;
        margin-bottom: 15px;
        word-break: break-all;
        border: 1px solid #e0e0e0;
    }
    
    .form-section {
        margin-bottom: 40px;
    }
    
    .token-display {
        position: relative;
    }
    
    .token-input {
        padding-right: 50px;
        font-family: monospace;
        letter-spacing: 1px;
    }
    
    .token-copy {
        position: absolute;
        right: 10px;
        top: 50%;
        transform: translateY(-50%);
        background: none;
        border: none;
        color: var(--primary);
        cursor: pointer;
        font-size: 18px;
        padding: 5px;
        opacity: 0.7;
        transition: all 0.2s ease;
    }
    
    .token-copy:hover {
        opacity: 1;
    }
    
    /* 响应式调整 */
    @media (max-width: 768px) {
        .card-header {
            padding: 15px 20px;
            font-size: 16px;
        }
        
        .card-body {
            padding: 20px 15px;
        }
        
        .control-label {
            text-align: left !important;
        }
    }
    
    /* 修复输入框组中图标和输入框高度不一致的问题 */
    
    /* 输入框组样式优化 */
    .input-group {
        position: relative;
        display: flex;
        flex-wrap: nowrap;
        align-items: stretch;
        width: 100%;
    }
    
    .input-group-prepend {
        display: flex;
        margin-right: -1px;
    }
    
    .input-group-text {
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 12px 15px;
        height: 100%;
        background-color: rgba(40, 167, 69, 0.05);
        border: 1px solid #e1e1e1;
        border-radius: 8px 0 0 8px;
        color: var(--primary);
        font-size: 16px;
        line-height: 1;
        border-right: none;
        transition: all 0.3s ease;
    }
    
    .input-group .form-control {
        position: relative;
        flex: 1 1 auto;
        width: 1%;
        min-width: 0;
        border-left: none;
        border-top-left-radius: 0;
        border-bottom-left-radius: 0;
    }
    
    .input-group.focused .input-group-text {
        border-color: var(--primary);
        background-color: rgba(40, 167, 69, 0.1);
    }
    
    /* 确保图标和文本垂直居中 */
    .input-group-text i {
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
    }
    
    /* 确保所有输入元素高度一致 */
    .form-control, 
    .input-group-text, 
    select.form-control {
        height: 48px;
        line-height: 24px;
    }
    
    /* 对于文本区域保持自动高度 */
    textarea.form-control {
        height: auto;
    }
    
    /* 优化选择框样式 */
    .form-control, select.form-control {
        border-radius: 8px;
        padding: 12px 15px;
        border: 1px solid #e1e1e1;
        height: 48px;
        font-size: 15px;
        box-shadow: none !important;
        transition: all 0.3s ease;
        background-color: #fff;
        color: #333;
    }
    
    /* 确保选择框和输入框样式一致 */
    select.form-control {
        padding-right: 30px;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='%23333' viewBox='0 0 16 16'%3E%3Cpath d='M7.247 11.14 2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 10px center;
        background-size: 12px;
        -webkit-appearance: none;
        -moz-appearance: none;
        appearance: none;
    }
    
    /* 特殊照顾一下输入框组内的select元素 */
    .input-group select.form-control {
        border-top-left-radius: 0;
        border-bottom-left-radius: 0;
    }
</style>

<section id="main-content">
    <section class="wrapper">
        <?php
        if ($mod == 'user') {
        ?>
            <div class="row">
                <div class="col-lg-12">
                    <div class="page-title">
                        <h2><i class="bi bi-person-gear"></i> 用户资料设置</h2>
                    </div>
                    
                    <div class="card">
                        <div class="card-header">
                            <i class="bi bi-pencil-square"></i> 个人信息
                        </div>
                        <div class="card-body">
                            <form class="form-horizontal">
                                <div class="form-section">
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label control-label">ＱＱ</label>
                                        <div class="col-sm-9">
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="bi bi-chat-dots"></i></span>
                                                </div>
                                                <input type="text" name="qq" id="qq" value="<?php echo $userrow['qq']; ?>" class="form-control" placeholder="您的QQ号码" />
                                            </div>
                                            <small class="form-text text-muted">用于联系与找回密码</small>
                                        </div>
                                    </div>
                                    
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label control-label">昵称</label>
                                        <div class="col-sm-9">
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="bi bi-person"></i></span>
                                                </div>
                                                <input type="text" name="name" id="name" value="<?php echo $userrow['name']; ?>" class="form-control" placeholder="您的昵称" />
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label control-label">收件邮箱</label>
                                        <div class="col-sm-9">
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                                                </div>
                                                <input type="text" name="mail" id="mail" value="<?php echo $userrow['mail']; ?>" class="form-control" placeholder="您的电子邮箱" />
                                            </div>
                                            <small class="form-text text-muted">用于联系与找回密码</small>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="form-section">
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label control-label">默认跳转类型</label>
                                        <div class="col-sm-9">
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="bi bi-box-arrow-up-right"></i></span>
                                                </div>
                                                <select name="pattern" id="pattern" class="form-control">
                                                    <?php
                                                    echo pattern_list();
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label control-label">默认短链</label>
                                        <div class="col-sm-9">
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="bi bi-link-45deg"></i></span>
                                                </div>
                                                <select name="dwz_type" id="dwz_type" class="form-control">
                                                    <?php
                                                    echo dwzList();
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="form-section">
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label control-label">重置密码</label>
                                        <div class="col-sm-9">
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="bi bi-lock"></i></span>
                                                </div>
                                                <input type="password" name="pwd" id="pwd" value="" class="form-control" placeholder="不修改请留空" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="form-group row">
                                    <div class="col-sm-9 offset-sm-3">
                                        <button class="btn btn-success" type="button" onclick="updateUser()">
                                            <i class="bi bi-check-lg"></i> 保存设置
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        <?php
        } elseif ($mod == 'token') {
        ?>
            <div class="row">
                <div class="col-lg-12">
                    <div class="page-title">
                        <h2><i class="bi bi-key"></i> API设置</h2>
                    </div>
                    
                    <div class="card">
                        <div class="card-header">
                            <i class="bi bi-shield-lock"></i> 用户Token管理
                        </div>
                        <?php
                        if ($userrow['token'] == '') {
                            $token = md5($userrow['user'] . date('Ymd') . time() . rand(11111, 99999));
                            $DB->query("update dwz_user set token = '$token' where id = '$id'");
                        } else {
                            $token = $userrow['token'];
                        }
                        if ($conf['is_https'] == 1) {
                            $http = 'https';
                        } else {
                            $http = 'http';
                        }
                        $type = $userrow['dwz_type'] == '' ? $conf['dwz_type'] : $userrow['dwz_type'];
                        ?>
                        <div class="card-body">
                            <form class="form-horizontal" role="form">
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label control-label">您的API Token:</label>
                                    <div class="col-sm-9">
                                        <div class="token-display">
                                            <input type="text" name="token" id="token" value="<?php echo $token; ?>" class="form-control token-input" readonly />
                                            <button type="button" class="token-copy" onclick="copyToken()"><i class="bi bi-clipboard"></i></button>
                                        </div>
                                        <small class="form-text text-muted">用于API接口的身份验证，请妥善保管</small>
                                    </div>
                                </div>
                                
                                <div class="form-group row">
                                    <div class="col-sm-9 offset-sm-3">
                                        <button class="btn btn-danger" type="button" onclick="setToken()">
                                            <i class="bi bi-arrow-clockwise"></i> 重置Token
                                        </button>
                                        <small class="form-text text-muted mt-2">重置后旧Token将立即失效，请谨慎操作</small>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    
                    <div class="card">
                        <div class="card-header">
                            <i class="bi bi-file-earmark-code"></i> API接口文档
                        </div>
                        <div class="card-body">
                            <div class="api-section">
                                <h3><i class="bi bi-link-45deg"></i> 生成短网址</h3>
                                <p>请求地址:</p>
                                <div class="api-url"><?php echo $http . '://' . $conf['domain'] . '/api/url.php' ?></div>
                                <p>请求方式: GET/POST</p>
                                <p>请求示例:</p>
                                <div class="api-url"><?php echo $http . '://' . $conf['domain'] . '/api/url.php?type=' . $type . '&pattern=1&token=' . $token . '&url=https://www.baidu.com' ?></div>
                                
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <td>参数</td>
                                            <td>是否必须</td>
                                            <td>说明</td>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>url</td>
                                            <td>是</td>
                                            <td>需要缩短的长网址</td>
                                        </tr>
                                        <tr>
                                            <td>type</td>
                                            <td>否</td>
                                            <td>短网址类型，默认<?php echo $type ?>，suiji则随机短网址类型</td>
                                        </tr>
                                        <tr>
                                            <td>pattern</td>
                                            <td>否</td>
                                            <td>短网址模式（普通：1，防红：2，直链：3, 缩短：4）</td>
                                        </tr>
                                        <tr>
                                            <td>token</td>
                                            <td>是</td>
                                            <td>用户token</td>
                                        </tr>
                                        <tr>
                                            <td>title</td>
                                            <td>否</td>
                                            <td>直链标题</td>
                                        </tr>
                                        <tr>
                                            <td>bz</td>
                                            <td>否</td>
                                            <td>网址备注</td>
                                        </tr>
                                        <tr>
                                            <td>pwd</td>
                                            <td>否</td>
                                            <td>访问密码</td>
                                        </tr>
                                        <tr>
                                            <td>visit</td>
                                            <td>否</td>
                                            <td>限制访问次数</td>
                                        </tr>
                                        <tr>
                                            <td>visiturl</td>
                                            <td>否</td>
                                            <td>超过访问次数后访问网址</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <div class="api-section">
                                <h3><i class="bi bi-calculator"></i> 查询剩余短网址点数</h3>
                                <p>请求地址:</p>
                                <div class="api-url"><?php echo $http . '://' . $conf['domain'] . '/api/quota.php' ?></div>
                                <p>请求方式: GET/POST</p>
                                <p>请求示例:</p>
                                <div class="api-url"><?php echo $http . '://' . $conf['domain'] . '/api/quota.php?token=' . $token ?></div>
                                
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <td>参数</td>
                                            <td>是否必须</td>
                                            <td>说明</td>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>token</td>
                                            <td>是</td>
                                            <td>用户token</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php
        }
        ?>
    </section>
</section>

<!-- 自定义确认弹窗 -->
<div class="custom-modal-overlay" id="confirmTokenModal">
    <div class="custom-modal">
        <div class="custom-modal-header">
            <h3 class="custom-modal-title"><i class="bi bi-shield-exclamation"></i> 操作确认</h3>
        </div>
        <div class="custom-modal-body">
            <div class="custom-modal-icon">
                <i class="bi bi-key"></i>
            </div>
            <p class="custom-modal-message">您确定要重置Token吗？<br>重置后之前的Token将立即失效。</p>
        </div>
        <div class="custom-modal-footer">
            <button class="custom-modal-btn btn-cancel" id="cancelTokenReset">
                <i class="bi bi-x"></i> 取消
            </button>
            <button class="custom-modal-btn btn-confirm" id="confirmTokenReset">
                <i class="bi bi-check2"></i> 确认重置
            </button>
        </div>
    </div>
</div>

<!-- 自定义成功提示弹窗 -->
<div class="custom-modal-overlay" id="successModal">
    <div class="custom-modal custom-modal-sm">
        <div class="custom-modal-body">
            <div class="custom-modal-icon success-icon">
                <i class="bi bi-check2-circle"></i>
            </div>
            <p class="custom-modal-message" id="successMessage">操作成功</p>
        </div>
        <div class="custom-modal-footer">
            <button class="custom-modal-btn btn-confirm" id="successModalClose">
                <i class="bi bi-check"></i> 确定
            </button>
        </div>
    </div>
</div>

<!-- 自定义错误提示弹窗 -->
<div class="custom-modal-overlay" id="errorModal">
    <div class="custom-modal custom-modal-sm">
        <div class="custom-modal-body">
            <div class="custom-modal-icon error-icon">
                <i class="bi bi-exclamation-circle"></i>
            </div>
            <p class="custom-modal-message" id="errorMessage">操作失败</p>
        </div>
        <div class="custom-modal-footer">
            <button class="custom-modal-btn btn-confirm" id="errorModalClose">
                <i class="bi bi-check"></i> 确定
            </button>
        </div>
    </div>
</div>

<!-- 自定义警告/提示弹窗 -->
<div class="custom-modal-overlay" id="alertModal">
    <div class="custom-modal">
        <div class="custom-modal-header">
            <h3 class="custom-modal-title"><i class="bi bi-info-circle"></i> 提示信息</h3>
        </div>
        <div class="custom-modal-body">
            <div class="custom-modal-icon alert-icon">
                <i class="bi bi-bell"></i>
            </div>
            <p class="custom-modal-message" id="alertMessage"></p>
        </div>
        <div class="custom-modal-footer">
            <button class="custom-modal-btn btn-confirm" id="alertModalClose">
                <i class="bi bi-check"></i> 我知道了
            </button>
        </div>
    </div>
</div>

<script src="../static/user/js/common-scripts.js"></script>
<script>
    // 确保页面加载完成后执行
    $(document).ready(function() {
        // 表单输入框聚焦效果
        $(".form-control").focus(function() {
            $(this).closest('.input-group').addClass('focused');
        }).blur(function() {
            $(this).closest('.input-group').removeClass('focused');
        });
        
        // 为卡片添加进入动画
        $(".card").each(function(index) {
            var card = $(this);
            setTimeout(function() {
                card.css({
                    'opacity': '1',
                    'transform': 'translateY(0)'
                });
            }, index * 150);
        });
        
        // 确保卡片初始状态
        $(".card").css({
            'opacity': '0',
            'transform': 'translateY(20px)',
            'transition': 'all 0.5s ease'
        });
        
        // 设置自定义弹窗事件
        $("#confirmTokenReset").click(function() {
            // 关闭弹窗
            $("#confirmTokenModal").removeClass("active");
            
            // 执行重置操作
            resetTokenAction();
        });
        
        $("#cancelTokenReset").click(function() {
            // 关闭弹窗
            $("#confirmTokenModal").removeClass("active");
        });
        
        // 成功弹窗关闭按钮
        $("#successModalClose").click(function() {
            $("#successModal").removeClass("active");
        });
        
        // 错误弹窗关闭按钮
        $("#errorModalClose").click(function() {
            $("#errorModal").removeClass("active");
        });
        
        // 警告弹窗关闭按钮
        $("#alertModalClose").click(function() {
            $("#alertModal").removeClass("active");
        });
        
        // 点击遮罩层关闭弹窗
        $(".custom-modal-overlay").click(function(e) {
            if (e.target === this) {
                $(this).removeClass("active");
            }
        });
        
        // 确保输入框组中的元素在页面加载后正确对齐
        function adjustInputGroups() {
            $('.input-group').each(function() {
                var maxHeight = 0;
                // 获取组内所有元素中的最大高度
                $(this).find('.form-control, .input-group-text').each(function() {
                    var elemHeight = $(this).outerHeight();
                    if (elemHeight > maxHeight) {
                        maxHeight = elemHeight;
                    }
                });
                
                // 应用最大高度到所有元素
                if (maxHeight > 0) {
                    $(this).find('.input-group-text').css('height', maxHeight + 'px');
                    $(this).find('.form-control:not(textarea)').css('height', maxHeight + 'px');
                }
            });
        }
        
        // 页面加载后调整
        adjustInputGroups();
        
        // 窗口大小改变时重新调整
        $(window).resize(function() {
            adjustInputGroups();
        });
    });
    
    // 显示成功提示弹窗
    function showSuccessModal(message, callback) {
        $("#successMessage").text(message || "操作成功");
        $("#successModal").addClass("active");
        
        // 如果提供了回调函数，设置关闭按钮的点击事件
        if (callback && typeof callback === 'function') {
            $("#successModalClose").off('click').on('click', function() {
                $("#successModal").removeClass("active");
                callback();
            });
        }
        
        // 自动关闭
        setTimeout(function() {
            if($("#successModal").hasClass("active")) {
                $("#successModal").removeClass("active");
                if (callback && typeof callback === 'function') {
                    callback();
                }
            }
        }, 2000);
    }
    
    // 显示错误提示弹窗
    function showErrorModal(message) {
        $("#errorMessage").text(message || "操作失败");
        $("#errorModal").addClass("active");
    }
    
    // 显示警告/提示弹窗
    function showAlertModal(message) {
        $("#alertMessage").html(message || "");
        $("#alertModal").addClass("active");
    }
    
    // 复制Token函数
    function copyToken() {
        var tokenInput = document.getElementById("token");
        tokenInput.select();
        document.execCommand("copy");
        
        // 显示复制成功提示
        var copyBtn = document.querySelector(".token-copy");
        var originalIcon = copyBtn.innerHTML;
        copyBtn.innerHTML = '<i class="bi bi-check2"></i>';
        
        setTimeout(function() {
            copyBtn.innerHTML = originalIcon;
        }, 2000);
        
        // 使用自定义成功弹窗
        showSuccessModal("Token已复制到剪贴板");
    }
    
    function updateUser() {
        qq = $('#qq').val();
        name = $('#name').val();
        pattern = $('#pattern').val();
        dwz_type = $('#dwz_type').val();
        mail = $('#mail').val();
        pwd = $('#pwd').val();
        
        // 显示加载状态
        var btn = $(".btn-success");
        var originalText = btn.html();
        btn.html('<i class="bi bi-hourglass-split"></i> 保存中...').attr('disabled', true);
        
        $.ajax({
            url: "ajax.php?act=upUser",
            type: "POST",
            data: {
                "qq": qq,
                "name": name,
                "pattern": pattern,
                "dwz_type": dwz_type,
                "mail": mail,
                "pwd": pwd,
            },
            dataType: "json",
            success: function(data) {
                // 恢复按钮状态
                btn.html(originalText).attr('disabled', false);
                
                if (data.code == 0) {
                    // 使用自定义成功弹窗并设置回调函数刷新页面
                    showSuccessModal("修改成功", function() {
                        window.location.reload();
                    });
                } else {
                    // 使用自定义警告弹窗
                    showAlertModal(data.msg);
                }
            },
            error: function(data) {
                // 恢复按钮状态
                btn.html(originalText).attr('disabled', false);
                // 使用自定义错误弹窗
                showErrorModal("服务器错误");
                return false;
            }
        });
    }

    // Token重置确认操作
    function setToken() {
        // 显示自定义弹窗
        $("#confirmTokenModal").addClass("active");
    }
    
    // 实际执行Token重置的函数
    function resetTokenAction() {
        // 显示加载状态
        var btn = $(".btn-danger");
        var originalText = btn.html();
        btn.html('<i class="bi bi-hourglass-split"></i> 处理中...').attr('disabled', true);
        
        $.ajax({
            url: "ajax.php?act=setToken",
            type: "POST",
            dataType: "json",
            success: function(data) {
                // 恢复按钮状态
                btn.html(originalText).attr('disabled', false);
                
                if (data.code == 0) {
                    // 使用自定义成功弹窗
                    showSuccessModal("重置成功");
                    $("#token").val(data.token);
                    
                    // 添加高亮效果
                    $("#token").addClass('highlight-animation');
                    setTimeout(function() {
                        $("#token").removeClass('highlight-animation');
                    }, 2000);
                } else {
                    // 使用自定义警告弹窗
                    showAlertModal(data.msg);
                }
            },
            error: function(data) {
                // 恢复按钮状态
                btn.html(originalText).attr('disabled', false);
                // 使用自定义错误弹窗
                showErrorModal("服务器错误");
                return false;
            }
        });
    }
</script>
<style>
    /* 输入框聚焦效果 */
    .input-group.focused {
        box-shadow: 0 0 0 3px rgba(40, 167, 69, 0.15);
        border-radius: 8px;
    }
    
    /* Token高亮动画 */
    @keyframes highlight {
        0% { background-color: transparent; }
        50% { background-color: rgba(40, 167, 69, 0.15); }
        100% { background-color: transparent; }
    }
    
    .highlight-animation {
        animation: highlight 2s ease;
    }
    
    /* 自定义弹窗样式 */
    .custom-modal-overlay {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: rgba(0, 0, 0, 0.5);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 9999;
        opacity: 0;
        visibility: hidden;
        transition: all 0.3s ease;
        backdrop-filter: blur(3px);
    }
    
    .custom-modal-overlay.active {
        opacity: 1;
        visibility: visible;
    }
    
    .custom-modal {
        background-color: white;
        border-radius: 12px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
        width: 90%;
        max-width: 420px;
        padding: 0;
        transform: translateY(30px) scale(0.95);
        transition: all 0.4s cubic-bezier(0.19, 1, 0.22, 1);
        overflow: hidden;
        opacity: 0;
    }
    
    .custom-modal-sm {
        max-width: 360px;
    }
    
    .custom-modal-overlay.active .custom-modal {
        transform: translateY(0) scale(1);
        opacity: 1;
    }
    
    .custom-modal-header {
        background: linear-gradient(135deg, var(--primary), var(--primary-dark));
        color: white;
        padding: 20px 25px;
        position: relative;
    }
    
    .custom-modal-title {
        margin: 0;
        font-size: 18px;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .custom-modal-title i {
        font-size: 22px;
    }
    
    .custom-modal-body {
        padding: 25px;
        color: #555;
        font-size: 15px;
        line-height: 1.6;
    }
    
    .custom-modal-icon {
        width: 65px;
        height: 65px;
        background-color: rgba(40, 167, 69, 0.1);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 20px;
    }
    
    .custom-modal-icon i {
        font-size: 30px;
        color: var(--primary);
    }
    
    /* 成功图标 */
    .success-icon {
        background-color: rgba(40, 167, 69, 0.1);
    }
    
    .success-icon i {
        color: var(--primary);
    }
    
    /* 错误图标 */
    .error-icon {
        background-color: rgba(220, 53, 69, 0.1);
    }
    
    .error-icon i {
        color: #dc3545;
    }
    
    /* 警告图标 */
    .alert-icon {
        background-color: rgba(255, 193, 7, 0.1);
    }
    
    .alert-icon i {
        color: #ffc107;
    }
    
    .custom-modal-message {
        text-align: center;
        margin: 0;
    }
    
    .custom-modal-footer {
        padding: 0 25px 25px;
        display: flex;
        justify-content: center;
        gap: 15px;
    }
    
    .custom-modal-btn {
        padding: 12px 24px;
        border-radius: 8px;
        font-weight: 600;
        font-size: 14px;
        cursor: pointer;
        transition: all 0.3s ease;
        border: none;
        outline: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        flex: 1;
    }
    
    .btn-confirm {
        background-color: var(--primary);
        color: white;
    }
    
    .btn-confirm:hover {
        background-color: var(--primary-dark);
        box-shadow: 0 5px 15px rgba(40, 167, 69, 0.3);
        transform: translateY(-2px);
    }
    
    .btn-cancel {
        background-color: #f1f2f3;
        color: #555;
    }
    
    .btn-cancel:hover {
        background-color: #e5e6e7;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        transform: translateY(-2px);
    }
    
    @keyframes pulse {
        0% { transform: scale(1); }
        50% { transform: scale(1.05); }
        100% { transform: scale(1); }
    }
    
    .custom-modal-icon {
        animation: pulse 2s infinite;
    }
    
    /* 弹窗淡入淡出效果 */
    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }
    
    @keyframes fadeOut {
        from { opacity: 1; }
        to { opacity: 0; }
    }
    
    .fadeIn {
        animation: fadeIn 0.3s forwards;
    }
    
    .fadeOut {
        animation: fadeOut 0.3s forwards;
    }
</style>