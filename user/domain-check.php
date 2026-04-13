<?php
include("../includes/common.php");
$title = '域名检测 - 用户中心';
include './head.php';

// 检查是否已登录
if (!$islogin2) {
    exit("<script language='javascript'>window.location.href='./login.php';</script>");
}
?>

<!-- 引入SweetAlert2库 -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.1.4/dist/sweetalert2.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.1.4/dist/sweetalert2.all.min.js"></script>

<!-- 自定义样式 -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">

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
    
    body {
        background-color: var(--light-bg);
        color: var(--text-primary);
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
    }
    
    /* 卡片样式 */
    .card {
        border-radius: 12px;
        border: none;
        box-shadow: var(--card-shadow);
        margin-bottom: 24px;
        background-color: #fff;
        transition: all 0.2s ease;
        overflow: hidden;
    }
    
    .card:hover {
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
        transform: translateY(-2px);
    }
    
    .card-header {
        padding: 16px 20px;
        border-bottom: 1px solid var(--border-color);
        background-color: #fff;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    
    .card-header h5 {
        margin: 0;
        font-weight: 600;
        color: var(--text-primary);
        font-size: 16px;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .card-header h5 i {
        color: var(--wechat-green);
    }
    
    .card-body {
        padding: 20px;
    }
    
    /* 统计卡片样式 */
    .stat-card {
        border-radius: 12px;
        padding: 0;
        overflow: hidden;
        box-shadow: var(--card-shadow);
        margin-bottom: 20px;
        border: none;
        transition: all 0.3s ease;
        position: relative;
    }
    
    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
    }
    
    .stat-header {
        padding: 20px;
        color: #fff;
        position: relative;
        overflow: hidden;
    }
    
    .stat-icon {
        position: absolute;
        right: -15px;
        top: -15px;
        opacity: 0.2;
        font-size: 80px;
    }
    
    .stat-number {
        font-size: 30px;
        font-weight: 700;
        margin-bottom: 8px;
        position: relative;
        z-index: 1;
    }
    
    .stat-label {
        font-size: 14px;
        opacity: 0.9;
        position: relative;
        z-index: 1;
        font-weight: 500;
    }
    
    .stat-footer {
        padding: 12px 20px;
        background-color: rgba(255, 255, 255, 0.1);
        font-size: 12px;
        color: rgba(255, 255, 255, 0.9);
        backdrop-filter: blur(5px);
        -webkit-backdrop-filter: blur(5px);
    }
    
    /* 按钮样式 */
    .btn {
        border-radius: 8px;
        padding: 10px 20px;
        font-weight: 500;
        transition: all 0.3s;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }
    
    .btn-green {
        background-color: var(--wechat-green);
        border-color: var(--wechat-green);
        color: white;
    }
    
    .btn-green:hover, .btn-green:focus {
        background-color: #06a050;
        border-color: #06a050;
        color: white;
        box-shadow: 0 5px 15px rgba(7, 193, 96, 0.3);
        transform: translateY(-2px);
    }
    
    .btn-secondary {
        background-color: #f1f1f1;
        border-color: #f1f1f1;
        color: var(--text-primary);
    }
    
    .btn-secondary:hover, .btn-secondary:focus {
        background-color: #e5e5e5;
        border-color: #e5e5e5;
        color: var(--text-primary);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        transform: translateY(-2px);
    }
    
    /* 表单控件样式 */
    .form-control {
        border-radius: 8px;
        border: 1px solid var(--border-color);
        padding: 12px 15px;
        transition: all 0.3s;
        font-size: 14px;
    }
    
    .form-control:focus {
        border-color: var(--wechat-green);
        box-shadow: 0 0 0 3px rgba(7, 193, 96, 0.2);
    }
    
    textarea.form-control {
        min-height: 120px;
        resize: vertical;
    }
    
    .form-label {
        font-weight: 500;
        color: var(--text-primary);
        margin-bottom: 8px;
        font-size: 14px;
    }
    
    .form-text {
        color: var(--text-secondary);
        font-size: 12px;
        margin-top: 5px;
    }
    
    /* 单选框和复选框样式 */
    .form-check {
        margin-bottom: 10px;
    }
    
    .form-check-input:checked {
        background-color: var(--wechat-green);
        border-color: var(--wechat-green);
    }
    
    .form-select {
        border-radius: 8px;
        border: 1px solid var(--border-color);
        padding: 12px 15px;
        padding-right: 30px;
        transition: all 0.3s;
        font-size: 14px;
        height: auto;
    }
    
    .form-select:focus {
        border-color: var(--wechat-green);
        box-shadow: 0 0 0 3px rgba(7, 193, 96, 0.2);
    }
    
    /* 页面标题 */
    .page-header {
        margin-bottom: 24px;
        padding-bottom: 16px;
        border-bottom: 1px solid var(--border-color);
    }
    
    .page-title {
        font-size: 24px;
        font-weight: 700;
        color: var(--text-primary);
        margin: 0;
        display: flex;
        align-items: center;
        gap: 12px;
    }
    
    .page-title i {
        color: var(--wechat-green);
    }
    
    .page-subtitle {
        color: var(--text-secondary);
        font-size: 14px;
        margin-top: 5px;
        font-weight: normal;
    }
    
    /* 徽章样式 */
    .badge {
        padding: 6px 12px;
        font-weight: 500;
        font-size: 12px;
        border-radius: 20px;
    }
    
    .badge-green {
        background-color: rgba(7, 193, 96, 0.1);
        color: var(--wechat-green);
    }
    
    .badge-blue {
        background-color: rgba(52, 152, 219, 0.1);
        color: #3498DB;
    }
    
    /* 帮助文本和提示 */
    .help-text {
        color: var(--text-secondary);
        font-size: 13px;
        margin-top: 6px;
    }
    
    .helper-text {
        display: flex;
        align-items: center;
        gap: 5px;
        color: var(--text-secondary);
        font-size: 12px;
        margin-top: 8px;
    }
    
    .helper-text i {
        color: var(--wechat-green);
    }
    
    /* 信息框样式 */
    .info-box {
        border-radius: 12px;
        padding: 20px;
        background-color: rgba(52, 152, 219, 0.05);
        border: 1px solid rgba(52, 152, 219, 0.1);
        margin-bottom: 20px;
    }
    
    .info-box h5 {
        color: #3498DB;
        font-size: 16px;
        font-weight: 600;
        margin-top: 0;
        margin-bottom: 15px;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    
    .info-box p {
        color: var(--text-secondary);
        font-size: 14px;
        margin-bottom: 10px;
        line-height: 1.6;
    }
    
    .info-box p:last-child {
        margin-bottom: 0;
    }
    
    /* 响应式调整 */
    @media (max-width: 768px) {
        .stat-card {
            margin-bottom: 15px;
        }
        
        .page-title {
            font-size: 20px;
        }
        
        .card-body {
            padding: 15px;
        }
        
        .btn {
            padding: 8px 16px;
        }
    }
</style>

<section id="main-content">
    <section class="wrapper">
        <!-- 页面标题 -->
        <div class="row">
            <div class="col-lg-12">
                <div class="page-header">
                    <h1 class="page-title">
                        <i class="bi bi-globe"></i> 域名检测中心
                        <small class="page-subtitle">Domain Check Center</small>
                    </h1>
                </div>
            </div>
        </div>
        
        <!-- 统计卡片 -->
        <div class="row">
            <div class="col-lg-3 col-md-6">
                <div class="stat-card">
                    <div class="stat-header" style="background: linear-gradient(135deg, #07C160, #06a050);">
                        <div class="stat-icon">
                            <i class="bi bi-check-circle"></i>
                        </div>
                        <div class="stat-number">0</div>
                        <div class="stat-label">已检测域名</div>
                    </div>
                    <div class="stat-footer">
                        <i class="bi bi-clock"></i> 最近更新：今天
                    </div>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6">
                <div class="stat-card">
                    <div class="stat-header" style="background: linear-gradient(135deg, #3498DB, #2980B9);">
                        <div class="stat-icon">
                            <i class="bi bi-link-45deg"></i>
                        </div>
                        <div class="stat-number">0</div>
                        <div class="stat-label">正常域名</div>
                    </div>
                    <div class="stat-footer">
                        <i class="bi bi-shield-check"></i> 安全状态：良好
                    </div>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6">
                <div class="stat-card">
                    <div class="stat-header" style="background: linear-gradient(135deg, #F39C12, #E67E22);">
                        <div class="stat-icon">
                            <i class="bi bi-exclamation-triangle"></i>
                        </div>
                        <div class="stat-number">0</div>
                        <div class="stat-label">异常域名</div>
                    </div>
                    <div class="stat-footer">
                        <i class="bi bi-exclamation"></i> 需要关注
                    </div>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6">
                <div class="stat-card">
                    <div class="stat-header" style="background: linear-gradient(135deg, #E74C3C, #C0392B);">
                        <div class="stat-icon">
                            <i class="bi bi-slash-circle"></i>
                        </div>
                        <div class="stat-number">0</div>
                        <div class="stat-label">封禁域名</div>
                    </div>
                    <div class="stat-footer">
                        <i class="bi bi-exclamation-circle"></i> 高风险
                    </div>
                </div>
            </div>
        </div>
        
        <!-- 检测工具面板 -->
        <div class="card">
            <div class="card-header">
                <h5><i class="bi bi-search"></i> 域名批量检测</h5>
                <span class="badge badge-green">批量检测工具</span>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-8">
                        <div class="mb-3">
                            <label for="domain-textarea" class="form-label">输入待检测域名</label>
                            <textarea id="domain-textarea" class="form-control" rows="6" placeholder="请输入要检测的域名，每行一个，例如: example.com"></textarea>
                            <div class="helper-text">
                                <i class="bi bi-info-circle"></i> 已输入 <span id="domain-count" class="badge badge-green">0</span> 个域名，最多可检测 100 个
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">检测设置</label>
                            <div class="card">
                                <div class="card-body" style="background-color: #f9fafb;">
                                    <div class="mt-4">
                                        <button type="button" class="btn btn-green w-100 mb-2">
                                            <i class="bi bi-play-fill"></i> 开始检测
                                        </button>
                                        <button type="button" class="btn btn-secondary w-100">
                                            <i class="bi bi-trash"></i> 清空域名
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- 进度信息面板 -->
        <div class="card">
            <div class="card-header">
                <h5><i class="bi bi-info-circle"></i> 检测提示</h5>
                <span class="badge badge-blue">帮助信息</span>
            </div>
            <div class="card-body">
                <div class="info-box">
                    <h5><i class="bi bi-lightbulb"></i> 使用说明</h5>
                    <p>1. 在左侧文本框中输入需要检测的域名，每行一个</p>
                    <p>2. 选择需要的检测类型和检测频率</p>
                    <p>3. 点击"开始检测"按钮开始批量检测</p>
                    <p>4. 检测结果将在检测完成后显示</p>
                    <p>5. 检测过程中请勿关闭页面，否则检测将中断</p>
                    <div class="text-end">
                        <a href="#" class="btn btn-sm" style="background-color: #3498DB; color: white;">了解更多</a>
                    </div>
                </div>
            </div>
        </div>
    </section>
</section>

<!-- JavaScript 代码 -->
<script>
    $(document).ready(function() {
        // 域名计数功能
        $('#domain-textarea').on('input', function() {
            var lines = $(this).val().split('\n').filter(function(line) {
                return line.trim() !== '';
            });
            $('#domain-count').text(lines.length);
            
            // 超过100个域名时提示
            if (lines.length > 100) {
                $(this).next('.helper-text').html('<i class="bi bi-exclamation-circle" style="color: #E74C3C;"></i> 最多只能检测 <span style="color: #E74C3C;">100</span> 个域名');
            } else {
                $(this).next('.helper-text').html('<i class="bi bi-info-circle"></i> 已输入 <span id="domain-count" class="badge badge-green">' + lines.length + '</span> 个域名，最多可检测 100 个');
            }
        });
        
        // 清空按钮功能
        $('.btn-secondary').click(function() {
            $('#domain-textarea').val('');
            $('#domain-count').text('0');
            $('#domain-textarea').next('.helper-text').html('<i class="bi bi-info-circle"></i> 已输入 <span id="domain-count" class="badge badge-green">0</span> 个域名，最多可检测 100 个');
        });
        
        // 添加动画效果
        $('.stat-card').css('opacity', '0');
        setTimeout(function() {
            $('.stat-card').each(function(index) {
                var card = $(this);
                setTimeout(function() {
                    card.css({
                        'transition': 'all 0.5s ease',
                        'opacity': '1',
                        'transform': 'translateY(0)'
                    });
                }, index * 100);
            });
        }, 300);
        
        // 域名检测按钮点击事件
        $('.btn-green').click(function() {
            // 获取域名列表
            var domains = $('#domain-textarea').val();
            
            // 检查是否有输入域名
            if (!domains.trim()) {
                Swal.fire({
                    icon: 'error',
                    title: '错误',
                    text: '请输入至少一个域名',
                });
                return;
            }
            
            // 显示正在检测对话框
            Swal.fire({
                title: '检测中...',
                html: '正在检测域名，请稍候',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            // 发送检测请求
            $.ajax({
                url: 'ajax.php?act=check_domain',
                type: 'POST',
                data: {
                    domains: domains
                },
                dataType: 'json',
                success: function(response) {
                    // 关闭加载对话框
                    Swal.close();
                    
                    if (response.code === 0) {
                        // 检测成功，显示结果
                        showCheckResults(response);
                        
                        // 更新统计数据
                        updateStats(response);
                    } else {
                        // 显示错误信息
                        Swal.fire({
                            icon: 'error',
                            title: '检测失败',
                            text: response.msg
                        });
                    }
                },
                error: function() {
                    // 关闭加载对话框
                    Swal.close();
                    
                    // 显示错误信息
                    Swal.fire({
                        icon: 'error',
                        title: '请求失败',
                        text: '服务器连接失败，请稍后再试'
                    });
                }
            });
        });
        
        // 显示检测结果
        function showCheckResults(response) {
            // 创建结果表格
            var resultsHtml = `
                <div class="card mt-4">
                    <div class="card-header">
                        <h5><i class="bi bi-list-check"></i> 检测结果</h5>
                        <span class="badge badge-green">完成时间: ${getCurrentTime()}</span>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info">
                            <strong><i class="bi bi-info-circle"></i> 检测完成!</strong> 共检测 ${response.total} 个域名，成功: ${response.success_count}，失败: ${response.failed_count}
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>域名</th>
                                        <th>状态</th>
                                        <th>状态码</th>
                                        <th>检测结果</th>
                                    </tr>
                                </thead>
                                <tbody>`;
            
            // 添加每行结果
            response.results.forEach(function(result) {
                var statusClass = '';
                var statusIcon = '';
                var statusText = '';
                
                // 检查是API返回成功还是请求失败
                if (result.status === 'success') {
                    statusText = '检测成功';
                    
                    // 将原始响应中的status状态码映射到HTTP状态码
                    if (result.raw_response && result.raw_response.status) {
                        var apiStatus = parseInt(result.raw_response.status);
                        // status=4表示访问受限(403)
                        if (apiStatus === 4) {
                            result.status_code = 403;
                        }
                    }
                    
                    // 根据状态码设置不同的样式和图标
                    switch(Number(result.status_code)) {
                        case 200:
                            statusClass = 'text-success';
                            statusIcon = '<i class="bi bi-check-circle"></i>';
                            break;
                        case 401:
                        case 402:
                            statusClass = 'text-warning';
                            statusIcon = '<i class="bi bi-exclamation-triangle"></i>';
                            break;
                        case 403:
                            statusClass = 'text-success';
                            statusIcon = '<i class="bi bi-check-circle"></i>';
                        case 404:
                            statusClass = 'text-danger';
                            statusIcon = '<i class="bi bi-x-circle"></i>';
                            break;
                        default:
                            statusClass = 'text-info';
                            statusIcon = '<i class="bi bi-info-circle"></i>';
                    }
                } else {
                    statusText = '检测失败';
                    statusClass = 'text-danger';
                    statusIcon = '<i class="bi bi-exclamation-circle"></i>';
                }
                
                resultsHtml += `
                    <tr>
                        <td><strong>${result.domain}</strong></td>
                        <td class="${statusClass}">${statusIcon} ${statusText}</td>
                        <td>${result.status_code || '-'}</td>
                        <td>${result.message}</td>
                    </tr>`;
                
                // 添加调试信息到控制台，方便开发者查看实际返回数据
                console.log("域名:", result.domain);
                console.log("状态码:", result.status_code);
                console.log("原始响应:", result.raw_response);
            });
            
            resultsHtml += `
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>`;
            
            // 将结果添加到页面
            if ($('#results-container').length) {
                $('#results-container').html(resultsHtml);
            } else {
                $('<div id="results-container"></div>').insertAfter('.card:last').html(resultsHtml);
            }
            
            // 滚动到结果区域
            $('html, body').animate({
                scrollTop: $('#results-container').offset().top - 100
            }, 500);
        }
        
        // 更新统计卡片
        function updateStats(response) {
            // 计算各种状态的域名数量
            var total = response.total;
            var normal = 0;
            var warning = 0;
            var blocked = 0;
            
            response.results.forEach(function(result) {
                if (result.status === 'success') {
                    switch(result.status_code) {
                        case 200:
                            normal++;
                            break;
                        case 401:
                        case 402:
                            warning++;
                            break;
                        case 403:
                        case 404:
                            blocked++;
                            break;
                        // 其他状态码可以根据需要分类
                    }
                }
            });
            
            // 更新统计卡片上的数字
            $('.stat-card:eq(0) .stat-number').text(total);
            $('.stat-card:eq(1) .stat-number').text(normal);
            $('.stat-card:eq(2) .stat-number').text(warning);
            $('.stat-card:eq(3) .stat-number').text(blocked);
            
            // 更新最后检测时间
            $('.stat-card .stat-footer').html('<i class="bi bi-clock"></i> 最近更新：' + getCurrentTime());
        }
        
        // 获取当前时间
        function getCurrentTime() {
            var now = new Date();
            var hours = now.getHours().toString().padStart(2, '0');
            var minutes = now.getMinutes().toString().padStart(2, '0');
            var seconds = now.getSeconds().toString().padStart(2, '0');
            return hours + ':' + minutes + ':' + seconds;
        }
    });
</script>
