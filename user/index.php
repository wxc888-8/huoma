<?php
include('../includes/common.php');

// 使用cookie token验证用户登录状态
$islogin2 = isset($_COOKIE["user_token"]) ? 1 : 0;
if ($islogin2 == 1) {
  $token = $_COOKIE["user_token"];
  $token_array = explode("\t", authcode($token, 'DECODE', SYS_KEY));
  if (count($token_array) < 2) {
    $islogin2 = 0;
  } else {
    $user_id = intval($token_array[0]);
    $userrs = $DB->query("select * from dwz_user where id='$user_id' limit 1");
    if ($userrow = $DB->fetch($userrs)) {
      $session = md5($userrow['user'] . $userrow['pwd'] . $password_hash);
      if ($session != $token_array[1] || $userrow['state'] != 1) {
        $islogin2 = 0;
      }
    } else {
      $islogin2 = 0;
    }
  }
}

if ($islogin2 != 1) {
  exit("<script language='javascript'>window.location.href='./login.php';</script>");
}
?>

<?php
$title = '用户首页';
include('head.php');
?>
<!-- 内容部分开始 -->
<div class="dashboard">
    <!-- 通知栏 -->
    <div class="alert-container mb-4">
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <div class="d-flex align-items-center">
                <i class="bi bi-megaphone me-2"></i>
                <span id="gg">获取中...</span>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    </div>

    <!-- 核心数据统计 -->
    <div class="row stats-cards">
        <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
            <div class="stat-card">
                <div class="stat-card-icon bg-primary">
                    <i class="bi bi-link-45deg"></i>
                </div>
                <div class="stat-card-info">
                    <div class="stat-card-value" id="count1">获取中..</div>
                    <div class="stat-card-title">总链接数</div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
            <div class="stat-card">
                <div class="stat-card-icon bg-success">
                    <i class="bi bi-coin"></i>
                </div>
                <div class="stat-card-info">
                    <div class="stat-card-value"><?php echo $userrow['points']; ?></div>
                    <div class="stat-card-title">我的积分</div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
            <div class="stat-card">
                <div class="stat-card-icon bg-warning">
                    <i class="bi bi-eye"></i>
                </div>
                <div class="stat-card-info">
                    <div class="stat-card-value" id="count2">获取中..</div>
                    <div class="stat-card-title">总访问数</div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
            <div class="stat-card">
                <div class="stat-card-icon bg-info">
                    <i class="bi bi-graph-up"></i>
                </div>
                <div class="stat-card-info">
                    <div class="stat-card-value" id="count4">获取中..</div>
                    <div class="stat-card-title">昨日访问</div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- 功能选项卡 -->
    <div class="content-card mb-4">
        <div class="content-card-header">
            <ul class="nav nav-tabs card-header-tabs" id="dashboardTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="shortener-tab" data-bs-toggle="tab" data-bs-target="#shortener" type="button" role="tab" aria-selected="true">
                        <i class="bi bi-link"></i> 缩短链接
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="invite-tab" data-bs-toggle="tab" data-bs-target="#invite" type="button" role="tab" aria-selected="false">
                        <i class="bi bi-person-plus"></i> 邀请奖励
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="quick-tools-tab" data-bs-toggle="tab" data-bs-target="#quick-tools" type="button" role="tab" aria-selected="false">
                        <i class="bi bi-tools"></i> 快捷工具
                    </button>
                </li>
            </ul>
        </div>
        <div class="content-card-body">
            <div class="tab-content" id="dashboardTabsContent">
                <!-- 缩短链接选项卡 -->
                <div class="tab-pane fade show active" id="shortener" role="tabpanel" aria-labelledby="shortener-tab">
                    <div class="mb-4">
                        <label class="form-label">
                            <i class="bi bi-globe"></i> 输入长网址
                        </label>
                        <textarea id="urls" class="form-control" placeholder="每行一个长网址，可以批量缩短，一次最多50个网址" rows="6"></textarea>
                        <div class="form-text">每行一个，最多支持50个网址同时处理</div>
                    </div>
                    
                    <div class="row mb-4">
                        <div class="col-md-6 mb-3 mb-md-0">
                            <label class="form-label">
                                <i class="bi bi-gear"></i> 选择接口
                            </label>
                            <select class="form-select" id="dwz-type">
                                <?php echo dwzList(); ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">
                                <i class="bi bi-shuffle"></i> 选择模式
                            </label>
                            <select class="form-select" id="pattern">
                                <?php echo pattern_list(); ?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="text-center">
                        <div class="btn-group">
                            <button type="button" id="start" class="btn btn-primary px-4 py-2">
                                <i class="bi bi-lightning-charge"></i> 立即生成
                            </button>
                            <button type="button" id="reset" class="btn btn-outline-secondary px-4 py-2">
                                <i class="bi bi-arrow-repeat"></i> 重置内容
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- 邀请奖励选项卡 -->
                <div class="tab-pane fade" id="invite" role="tabpanel" aria-labelledby="invite-tab">
                    <div class="row">
                        <div class="col-lg-6 mb-4 mb-lg-0">
                            <div class="invite-code-container">
                                <div class="invite-code-box">
                                    <div class="invite-code-label">我的专属邀请码</div>
                                    <div class="invite-code" id="myInviteCode">获取中...</div>
                                    <button type="button" class="btn btn-sm btn-outline-primary copy-btn" onclick="copyInviteCode()">
                                        <i class="bi bi-clipboard"></i> 复制
                                    </button>
                                </div>
                                <div class="invite-link-box mt-3">
                                    <div class="invite-code-label">邀请链接</div>
                                    <div class="invite-link-input">
                                        <input type="text" class="form-control" id="inviteLink" readonly>
                                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="copyInviteLink()">
                                            <i class="bi bi-clipboard"></i> 复制
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="invite-rules">
                                <h6><i class="bi bi-trophy"></i> 邀请奖励</h6>
                                <div class="invite-rule-item">
                                    <div class="invite-rule-icon">
                                        <i class="bi bi-cash-coin"></i>
                                    </div>
                                    <div class="invite-rule-content">
                                        <div class="invite-rule-title">充值返现规则</div>
                                        <div class="invite-rule-desc">当您邀请的好友进行充值时，您将获得好友充值金额 <span class="text-primary" id="consume-percent">获取中...</span> 的积分返现</div>
                                    </div>
                                </div>
                                <div class="invite-rule-item">
                                    <div class="invite-rule-icon">
                                        <i class="bi bi-graph-up"></i>
                                    </div>
                                    <div class="invite-rule-content">
                                        <div class="invite-rule-title">邀请越多，奖励越多</div>
                                        <div class="invite-rule-desc">邀请的好友越多，获得的返现积分就越多，积分可直接用于站内消费</div>
                                    </div>
                                </div>
                                <div class="invite-rule-item">
                                    <div class="invite-rule-icon">
                                        <i class="bi bi-arrow-repeat"></i>
                                    </div>
                                    <div class="invite-rule-content">
                                        <div class="invite-rule-title">长期有效</div>
                                        <div class="invite-rule-desc">您邀请的好友每次充值，您都能获得对应比例的积分返现，长期有效</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="invite-records mt-4">
                        <h6><i class="bi bi-clock-history"></i> 最近邀请记录</h6>
                        <div class="table-responsive">
                            <table class="table table-hover" id="inviteRecordsTable">
                                <thead>
                                    <tr>
                                        <th>用户名</th>
                                        <th>注册时间</th>
                                        <th>获得奖励</th>
                                        <th>状态</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr id="inviteRecordsLoading">
                                        <td colspan="4" class="text-center">
                                            <div class="spinner-border spinner-border-sm text-primary me-2" role="status"></div>
                                            正在加载邀请记录...
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                
                <!-- 快捷工具选项卡 -->
                <div class="tab-pane fade" id="quick-tools" role="tabpanel" aria-labelledby="quick-tools-tab">
                    <div class="row">
                        <div class="col-md-6 col-lg-4 mb-4">
                            <div class="quick-tool-card">
                                <div class="quick-tool-icon">
                                    <i class="bi bi-cash-coin"></i>
                                </div>
                                <div class="quick-tool-content">
                                    <h5>积分充值</h5>
                                    <p>购买积分以使用更多高级功能</p>
                                    <a href="pay.php" class="btn btn-sm btn-primary">立即充值</a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-4 mb-4">
                            <div class="quick-tool-card">
                                <div class="quick-tool-icon">
                                    <i class="bi bi-qr-code"></i>
                                </div>
                                <div class="quick-tool-content">
                                    <h5>活码管理</h5>
                                    <p>创建和管理您的活码</p>
                                    <a href="qr-center.php" class="btn btn-sm btn-primary">立即管理</a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-4 mb-4">
                            <div class="quick-tool-card">
                                <div class="quick-tool-icon">
                                    <i class="bi bi-shield-check"></i>
                                </div>
                                <div class="quick-tool-content">
                                    <h5>域名检测</h5>
                                    <p>检测域名在各平台的安全状态</p>
                                    <a href="domain-check.php" class="btn btn-sm btn-primary">立即检测</a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-4 mb-4">
                            <div class="quick-tool-card">
                                <div class="quick-tool-icon">
                                    <i class="bi bi-shop"></i>
                                </div>
                                <div class="quick-tool-content">
                                    <h5>域名商店</h5>
                                    <p>购买高质量的安全域名</p>
                                    <a href="domain-store.php" class="btn btn-sm btn-primary">前往商店</a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-4 mb-4">
                            <div class="quick-tool-card">
                                <div class="quick-tool-icon">
                                    <i class="bi bi-graph-up"></i>
                                </div>
                                <div class="quick-tool-content">
                                    <h5>数据统计</h5>
                                    <p>查看您的链接访问数据</p>
                                    <a href="urllist.php" class="btn btn-sm btn-primary">查看数据</a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-4 mb-4">
                            <div class="quick-tool-card">
                                <div class="quick-tool-icon">
                                    <i class="bi bi-person-gear"></i>
                                </div>
                                <div class="quick-tool-content">
                                    <h5>账户设置</h5>
                                    <p>修改个人信息和安全设置</p>
                                    <a href="uset.php?mod=user" class="btn btn-sm btn-primary">修改设置</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- 内容部分结束 -->

<style>
    .dashboard {
        padding: 10px 5px;
    }
    
    /* 提示框样式 */
    .alert-container {
        margin-bottom: 20px;
    }
    
    .alert {
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        border: none;
    }
    
    /* 统计卡片样式 */
    .stats-cards {
        margin-bottom: 20px;
    }
    
    .stat-card {
        display: flex;
        background-color: white;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 2px 15px rgba(0, 0, 0, 0.08);
        padding: 20px;
        height: 100%;
        transition: transform 0.3s, box-shadow 0.3s;
    }
    
    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.12);
    }
    
    .stat-card-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 56px;
        height: 56px;
        border-radius: 12px;
        margin-right: 16px;
        flex-shrink: 0;
    }
    
    .stat-card-icon i {
        font-size: 26px;
        color: white;
    }
    
    .bg-primary {
        background-color: var(--primary);
    }
    
    .bg-warning {
        background-color: #ffc107;
    }
    
    .bg-danger {
        background-color: #dc3545;
    }
    
    .bg-info {
        background-color: #0dcaf0;
    }
    
    .stat-card-info {
        display: flex;
        flex-direction: column;
        justify-content: center;
    }
    
    .stat-card-value {
        font-size: 24px;
        font-weight: 700;
        color: var(--text-primary);
        line-height: 1.2;
    }
    
    .stat-card-title {
        font-size: 14px;
        color: var(--text-secondary);
        margin-top: 4px;
    }
    
    /* 内容卡片样式 */
    .content-card {
        background-color: white;
        border-radius: 10px;
        box-shadow: 0 2px 15px rgba(0, 0, 0, 0.08);
        margin-bottom: 20px;
        overflow: hidden;
    }
    
    .content-card-header {
        padding: 15px 20px;
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        background-color: rgba(0, 0, 0, 0.02);
    }
    
    .content-card-title {
        margin: 0;
        font-size: 16px;
        font-weight: 600;
        color: var(--text-primary);
        display: flex;
        align-items: center;
    }
    
    .content-card-title i {
        margin-right: 8px;
        color: var(--primary);
    }
    
    .content-card-body {
        padding: 20px;
    }
    
    /* 表单元素样式 */
    .form-control, .form-select {
        border-color: rgba(0, 0, 0, 0.1);
        border-radius: 8px;
        padding: 12px 15px;
    }
    
    .form-control:focus, .form-select:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 0.25rem rgba(40, 167, 69, 0.25);
    }
    
    textarea.form-control {
        min-height: 120px;
    }
    
    .form-label {
        font-weight: 500;
        color: var(--text-primary);
        margin-bottom: 8px;
    }
    
    .form-text {
        color: var(--text-secondary);
        font-size: 13px;
        margin-top: 5px;
    }
    
    /* 按钮样式 */
    .btn {
        font-weight: 500;
        border-radius: 8px;
        padding: 8px 16px;
        transition: all 0.3s;
    }
    
    .btn-primary {
        background-color: var(--primary);
        border-color: var(--primary);
    }
    
    .btn-primary:hover, .btn-primary:focus {
        background-color: var(--primary-dark);
        border-color: var(--primary-dark);
        box-shadow: 0 5px 15px rgba(40, 167, 69, 0.3);
    }
    
    /* 响应式调整 */
    @media (max-width: 576px) {
        .dashboard {
            padding: 5px;
        }
        
        .stat-card {
            padding: 15px;
        }
        
        .stat-card-icon {
            width: 48px;
            height: 48px;
        }
        
        .stat-card-value {
            font-size: 20px;
        }
        
        .content-card-body {
            padding: 15px;
        }
    }
    
    /* 用户积分卡片样式 */
    .points-card {
        position: relative;
        padding-right: 120px;
    }
    
    .points-action {
        position: absolute;
        right: 20px;
        top: 50%;
        transform: translateY(-50%);
    }
    
    .btn-outline-success {
        color: var(--primary);
        border-color: var(--primary);
        transition: all 0.3s;
    }
    
    .btn-outline-success:hover {
        background-color: var(--primary);
        color: white;
    }
    
    .points-usage-card {
        padding: 20px;
    }
    
    .points-usage-info h5 {
        font-size: 16px;
        font-weight: 600;
        color: var(--dark);
        margin-bottom: 12px;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    
    .points-usage-info h5 i {
        color: var(--primary);
    }
    
    .points-usage-info ul {
        padding-left: 15px;
        margin-bottom: 0;
    }
    
    .points-usage-info li {
        margin-bottom: 6px;
        color: #666;
        font-size: 14px;
    }
    
    .bg-success {
        background-color: var(--primary);
    }
    
    @media (max-width: 992px) {
        .points-card {
            padding-right: 20px;
        }
        
        .points-action {
            position: static;
            margin-top: 15px;
            transform: none;
            text-align: right;
        }
    }
    
    /* 邀请码功能样式 */
    .invite-code-container {
        padding: 15px;
        background-color: #f8f9fa;
        border-radius: 8px;
        height: 100%;
    }
    
    .invite-code-box {
        position: relative;
        margin-bottom: 15px;
    }
    
    .invite-code-label {
        font-size: 14px;
        color: var(--text-secondary);
        margin-bottom: 8px;
    }
    
    .invite-code {
        font-size: 24px;
        font-weight: 700;
        color: var(--primary);
        letter-spacing: 2px;
        background-color: white;
        padding: 10px 15px;
        border-radius: 8px;
        border: 1px dashed var(--primary);
        margin-bottom: 10px;
        display: inline-block;
    }
    
    .copy-btn {
        position: absolute;
        right: 10px;
        top: 42px;
    }
    
    .invite-link-input {
        position: relative;
    }
    
    .invite-link-input .form-control {
        padding-right: 80px;
        background-color: white;
    }
    
    .invite-link-input .btn {
        position: absolute;
        right: 5px;
        top: 5px;
    }
    
    .invite-rules {
        height: 100%;
        padding: 15px;
        background-color: #f8f9fa;
        border-radius: 8px;
    }
    
    .invite-rules h6 {
        font-size: 16px;
        font-weight: 600;
        margin-bottom: 15px;
        color: var(--text-primary);
        display: flex;
        align-items: center;
        gap: 8px;
    }
    
    .invite-rules h6 i {
        color: var(--primary);
    }
    
    .invite-rule-item {
        display: flex;
        align-items: flex-start;
        margin-bottom: 15px;
        padding-bottom: 15px;
        border-bottom: 1px solid rgba(0,0,0,0.05);
    }
    
    .invite-rule-item:last-child {
        margin-bottom: 0;
        padding-bottom: 0;
        border-bottom: none;
    }
    
    .invite-rule-icon {
        width: 36px;
        height: 36px;
        background-color: rgba(40, 167, 69, 0.1);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 12px;
        flex-shrink: 0;
    }
    
    .invite-rule-icon i {
        font-size: 18px;
        color: var(--primary);
    }
    
    .invite-rule-title {
        font-weight: 600;
        font-size: 15px;
        margin-bottom: 5px;
        color: var(--text-primary);
    }
    
    .invite-rule-desc {
        font-size: 13px;
        color: var(--text-secondary);
        line-height: 1.5;
    }
    
    .invite-records h6 {
        font-size: 16px;
        font-weight: 600;
        margin-bottom: 15px;
        color: var(--text-primary);
        display: flex;
        align-items: center;
        gap: 8px;
    }
    
    .invite-records h6 i {
        color: var(--primary);
    }
    
    .table {
        font-size: 14px;
    }
    
    .table th {
        background-color: #f8f9fa;
        font-weight: 600;
    }
    
    /* 复制成功动画 */
    @keyframes copy-success {
        0% { background-color: rgba(40, 167, 69, 0.2); }
        100% { background-color: transparent; }
    }
    
    .copy-success {
        animation: copy-success 1.5s ease;
    }
    
    /* 选项卡样式 */
    .nav-tabs {
        border-bottom: none;
    }
    
    .nav-tabs .nav-link {
        border: none;
        padding: 12px 18px;
        margin-right: 2px;
        border-radius: 0;
        color: var(--text-secondary);
        font-weight: 500;
        transition: all 0.2s ease;
    }
    
    .nav-tabs .nav-link:hover {
        color: var(--primary);
        background-color: rgba(40, 167, 69, 0.05);
    }
    
    .nav-tabs .nav-link.active {
        color: var(--primary);
        border-bottom: 2px solid var(--primary);
        background-color: transparent;
    }
    
    .nav-tabs .nav-link i {
        margin-right: 6px;
    }
    
    .tab-content {
        padding-top: 20px;
    }
    
    /* 快捷工具卡片样式 */
    .quick-tool-card {
        background-color: white;
        border-radius: 10px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        padding: 20px;
        height: 100%;
        display: flex;
        flex-direction: column;
        transition: all 0.3s;
        border: 1px solid rgba(0,0,0,0.03);
    }
    
    .quick-tool-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.12);
        border-color: var(--primary);
    }
    
    .quick-tool-icon {
        width: 50px;
        height: 50px;
        background-color: rgba(40, 167, 69, 0.1);
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 15px;
    }
    
    .quick-tool-icon i {
        font-size: 24px;
        color: var(--primary);
    }
    
    .quick-tool-content h5 {
        font-size: 16px;
        font-weight: 600;
        margin-bottom: 8px;
        color: var(--text-primary);
    }
    
    .quick-tool-content p {
        font-size: 14px;
        color: var(--text-secondary);
        margin-bottom: 15px;
        flex: 1;
    }
    
    .quick-tool-content .btn {
        align-self: flex-start;
        padding: 6px 12px;
        font-size: 13px;
    }
</style>

<script>
    $(document).ready(function() {
        // 加载统计数据和公告
        $.ajax({
            type: "GET",
            url: "ajax.php?act=getcount",
            dataType: 'json',
            async: true,
            success: function(data) {
                if (data.gg3 != '') {
                    Swal.fire({
                        title: '公告',
                        html: data.gg3,
                        icon: 'info',
                        confirmButtonText: '我知道了',
                        confirmButtonColor: '#28a745'
                    });
                }
                $('#gg').html('公告：' + data.gg1);
                $('#count1').html(data.count1);
                $('#count2').html(data.count2);
                $('#count4').html(data.count4);
                
                // 如果后端返回了积分数据，更新积分显示
                if(data.points !== undefined) {
                    $('.points-card .stat-card-value').html(data.points);
                }
            },
            error: function() {
                $('#gg').html('公告加载失败，请刷新页面重试');
                Swal.fire({
                    title: '加载失败',
                    text: '公告内容加载失败，请刷新页面重试',
                    icon: 'error',
                    confirmButtonText: '确定',
                    confirmButtonColor: '#28a745'
                });
            }
        });
        
        // 加载邀请码和邀请记录
        loadInviteInfo();
        
        // 生成短网址
        $('#start').click(function() {
            var $btn = $(this);
            var btnHtml = $btn.html();
            
            $btn.html('<i class="bi bi-arrow-repeat spin"></i> 生成中...');
            $btn.addClass("btn-warning").removeClass("btn-primary");
            $btn.prop('disabled', true);
            
            var type = $("#dwz-type").val();
            var pattern = $("#pattern").val();
            var url = $('#urls').val();
            
            if (url.trim() == '') {
                Swal.fire({
                    title: '提示',
                    text: '请输入至少一个网址',
                    icon: 'warning',
                    confirmButtonText: '确定',
                    confirmButtonColor: '#28a745'
                });
                $btn.html(btnHtml);
                $btn.addClass("btn-primary").removeClass("btn-warning");
                $btn.prop('disabled', false);
                return;
            }
            
            url = url.replace(/\+/g, "%2B");
            var urls = url.split(/[(\r\n)\r\n]+/);
            
            if (urls.length > 50) {
                Swal.fire({
                    title: '提示',
                    text: '一次最多处理50个网址',
                    icon: 'warning',
                    confirmButtonText: '确定',
                    confirmButtonColor: '#28a745'
                });
                $btn.html(btnHtml);
                $btn.addClass("btn-primary").removeClass("btn-warning");
                $btn.prop('disabled', false);
                return;
            }
            
            $.ajax({
                type: "post",
                url: "ajax.php?act=creatUrls",
                dataType: "json",
                data: {
                    "urls": urls,
                    "type": type,
                    "pattern": pattern
                },
                success: function(obj) {
                    $btn.html(btnHtml);
                    $btn.addClass("btn-primary").removeClass("btn-warning");
                    $btn.prop('disabled', false);
                    
                    if (obj.code == 0) {
                        // 成功处理
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'success',
                            title: '处理完成',
                            showConfirmButton: false,
                            timer: 3000
                        });
                        
                        var str = '';
                        for (let i in obj.data) {
                            str += obj.data[i] + '\n';
                        }
                        $('#urls').val(str);
                        
                        // 全选文本方便复制
                        $('#urls').focus();
                        $('#urls').select();
                    } else {
                        Swal.fire({
                            title: '错误',
                            text: obj.msg,
                            icon: 'error',
                            confirmButtonText: '确定',
                            confirmButtonColor: '#28a745'
                        });
                    }
                },
                error: function() {
                    $btn.html(btnHtml);
                    $btn.addClass("btn-primary").removeClass("btn-warning");
                    $btn.prop('disabled', false);
                    Swal.fire({
                        title: '错误',
                        text: '生成失败，请稍后重试',
                        icon: 'error',
                        confirmButtonText: '确定',
                        confirmButtonColor: '#28a745'
                    });
                }
            });
        });
        
        // 重置按钮
        $('#reset').click(function() {
            $('#urls').val('');
            $('#urls').focus();
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: '已重置',
                showConfirmButton: false,
                timer: 1500
            });
        });
        
        // 初始聚焦文本区域
        $('#urls').focus();
    });
    
    // 添加旋转动画样式
    (function() {
        // 创建一个新的style元素
        const styleEl = document.createElement('style');
        // 添加CSS规则
        styleEl.textContent = `
            @keyframes spin {
                to { transform: rotate(360deg); }
            }
            .spin {
                animation: spin 0.8s linear infinite;
                display: inline-block;
            }
        `;
        // 添加到文档头部
        document.head.appendChild(styleEl);
    })();
</script>

<script>
    // 加载邀请码和邀请记录信息
    function loadInviteInfo() {
        // 加载邀请码
        $.ajax({
            type: "GET",
            url: "ajax.php?act=getInviteCode",
            dataType: 'json',
            success: function(data) {
                if (data.code == 0) {
                    var inviteCode = data.inviteCode || '未生成';
                    $('#myInviteCode').html(inviteCode);
                    
                    // 设置邀请链接
                    var baseUrl = window.location.protocol + "//" + window.location.host;
                    var inviteLink = baseUrl + "/user/reg.php?invite=" + inviteCode;
                    $('#inviteLink').val(inviteLink);
                } else {
                    $('#myInviteCode').html('获取失败');
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'error',
                        title: '邀请码获取失败',
                        text: data.msg,
                        showConfirmButton: false,
                        timer: 3000
                    });
                }
            },
            error: function() {
                $('#myInviteCode').html('获取失败');
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'error',
                    title: '邀请码获取失败',
                    showConfirmButton: false,
                    timer: 3000
                });
            }
        });
        
        // 加载邀请记录
        $.ajax({
            type: "GET",
            url: "ajax.php?act=getInviteRecords",
            dataType: 'json',
            success: function(data) {
                $('#inviteRecordsLoading').hide();
                
                if (data.code == 0) {
                    if (data.records && data.records.length > 0) {
                        var html = '';
                        $.each(data.records, function(i, item) {
                            var statusText = item.status == 1 ? 
                                '<span class="badge bg-success">有效</span>' : 
                                '<span class="badge bg-secondary">无效</span>';
                            
                            html += '<tr>' +
                                   '<td>' + (item.username || '***') + '</td>' +
                                   '<td>' + item.create_time + '</td>' +
                                   '<td><span class="text-primary">+' + item.reward_points + '</span></td>' +
                                   '<td>' + statusText + '</td>' +
                                   '</tr>';
                        });
                        $('#inviteRecordsTable tbody').append(html);
                    } else {
                        $('#inviteRecordsTable tbody').append('<tr><td colspan="4" class="text-center">暂无邀请记录</td></tr>');
                    }
                } else {
                    $('#inviteRecordsTable tbody').append('<tr><td colspan="4" class="text-center">获取邀请记录失败</td></tr>');
                }
            },
            error: function() {
                $('#inviteRecordsLoading').hide();
                $('#inviteRecordsTable tbody').append('<tr><td colspan="4" class="text-center">获取邀请记录失败</td></tr>');
            }
        });
        
        // 加载充值返现比例
        $.ajax({
            type: "GET",
            url: "ajax.php?act=getInviteConsumePercent",
            dataType: 'json',
            success: function(data) {
                if (data.code == 0) {
                    $('#consume-percent').html(data.percent + '%');
                } else {
                    $('#consume-percent').html('5%');
                }
            },
            error: function() {
                $('#consume-percent').html('5%');
            }
        });
    }
    
    // 复制邀请码
    function copyInviteCode() {
        var inviteCode = document.getElementById("myInviteCode").innerText;
        copyToClipboard(inviteCode);
        
        // 添加复制成功的动画效果
        $("#myInviteCode").addClass("copy-success");
        setTimeout(function() {
            $("#myInviteCode").removeClass("copy-success");
        }, 1500);
        
        // 显示成功提示
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'success',
            title: '邀请码已复制到剪贴板',
            showConfirmButton: false,
            timer: 2000
        });
    }
    
    // 复制邀请链接
    function copyInviteLink() {
        var inviteLink = document.getElementById("inviteLink").value;
        copyToClipboard(inviteLink);
        
        // 显示成功提示
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'success',
            title: '邀请链接已复制到剪贴板',
            showConfirmButton: false,
            timer: 2000
        });
    }
    
    // 通用复制到剪贴板函数
    function copyToClipboard(text) {
        // 创建临时输入框
        var tempInput = document.createElement("input");
        tempInput.value = text;
        document.body.appendChild(tempInput);
        
        // 选择并复制
        tempInput.select();
        document.execCommand("copy");
        
        // 删除临时输入框
        document.body.removeChild(tempInput);
    }
</script>