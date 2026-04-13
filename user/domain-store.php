<?php
include('../includes/common.php');
if ($islogin2 == 1) {
} else exit("<script language='javascript'>window.location.href='./login.php';</script>");

// 设置页面标识符
$_GET['mod'] = 'qr-center';
$_GET['subpage'] = 'domain-store';

$title = '域名商店';
include('head.php');
?>

<!-- 添加Layer.js库 -->
<script src="https://lib.baomitu.com/layer/3.1.1/layer.js"></script>

<!-- 添加自定义CSS样式 -->
<style>
:root {
    --wechat-green: #07C160;
    --light-bg: #F7F7F7;
    --border-color: rgba(0, 0, 0, 0.05);
    --text-primary: #333;
    --text-secondary: #666;
    --card-shadow: 0 2px 12px rgba(0, 0, 0, 0.05);
    --hover-bg: rgba(0, 0, 0, 0.03);
    --transition-time: 0.3s;
}

body {
    background-color: var(--light-bg);
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

.card {
    border: none;
    border-radius: 12px;
    box-shadow: var(--card-shadow);
    margin-bottom: 25px;
    transition: box-shadow var(--transition-time);
}

.card:hover {
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
}

.card-header {
    background-color: white;
    border-bottom: 1px solid var(--border-color);
    padding: 20px 25px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    border-top-left-radius: 12px;
    border-top-right-radius: 12px;
}

.card-header h5 {
    margin: 0;
    font-weight: 600;
    color: var(--text-primary);
    font-size: 1.2rem;
}

.nav-tabs {
    border-bottom: 1px solid var(--border-color);
    display: flex;
    justify-content: center;
}

.nav-tabs .nav-link {
    border: none;
    color: var(--text-secondary);
    padding: 15px 25px;
    border-radius: 0;
    margin-right: 10px;
    font-weight: 500;
    transition: all var(--transition-time);
}

.nav-tabs .nav-link:hover {
    color: var(--wechat-green);
    border-bottom: 2px solid rgba(7, 193, 96, 0.3);
    background-color: var(--hover-bg);
}

.nav-tabs .nav-link.active {
    color: var(--wechat-green);
    border-bottom: 2px solid var(--wechat-green);
    background-color: transparent;
}

.tab-content {
    padding: 25px 0;
}

.table {
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
}

.table th {
    background-color: #f8f9fa;
    font-weight: 600;
    color: var(--text-secondary);
    border-top: none;
    padding: 15px;
}

.table td {
    padding: 15px;
    vertical-align: middle;
}

.table tbody tr:hover {
    background-color: var(--hover-bg);
}

.badge {
    padding: 6px 12px;
    border-radius: 20px;
    font-weight: 500;
    font-size: 0.9rem;
}

.badge-success {
    background-color: rgba(7, 193, 96, 0.1);
    color: var(--wechat-green);
}

.badge-danger {
    background-color: rgba(220, 53, 69, 0.1);
    color: #dc3545;
}

.badge-primary {
    background-color: rgba(13, 110, 253, 0.1);
    color: #0d6efd;
}

.badge-info {
    background-color: rgba(13, 202, 240, 0.1);
    color: #0dcaf0;
}

.badge-warning {
    background-color: rgba(255, 193, 7, 0.1);
    color: #ffc107;
}

.btn-primary {
    background-color: var(--wechat-green);
    border-color: var(--wechat-green);
    border-radius: 6px;
    padding: 8px 16px;
    transition: all var(--transition-time);
}

.btn-primary:hover,
.btn-primary:focus {
    background-color: #06a452;
    border-color: #06a452;
    transform: scale(1.05);
}

.btn-outline-primary {
    color: var(--wechat-green);
    border-color: var(--wechat-green);
    border-radius: 6px;
    padding: 8px 16px;
    transition: all var(--transition-time);
}

.btn-outline-primary:hover {
    background-color: var(--wechat-green);
    border-color: var(--wechat-green);
    transform: scale(1.05);
}

.loading-spinner {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 30px;
}

.loading-spinner .spinner-border {
    color: var(--wechat-green);
    margin-bottom: 10px;
}

/* 模态框样式 */
.modal-content {
    border: none;
    border-radius: 16px;
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
}

.modal-header {
    border-bottom: 1px solid var(--border-color);
    padding: 20px 25px;
    border-top-left-radius: 16px;
    border-top-right-radius: 16px;
}

.modal-footer {
    border-top: 1px solid var(--border-color);
    padding: 20px 25px;
    border-bottom-left-radius: 16px;
    border-bottom-right-radius: 16px;
}

.alert-info {
    background-color: rgba(13, 202, 240, 0.1);
    border: 1px solid rgba(13, 202, 240, 0.2);
    color: #0dcaf0;
    border-radius: 12px;
    padding: 20px;
}

.alert-warning {
    background-color: rgba(255, 193, 7, 0.1);
    border: 1px solid rgba(255, 193, 7, 0.2);
    color: #664d03;
    border-radius: 12px;
    padding: 20px;
}

.form-label {
    font-weight: 500;
    color: var(--text-secondary);
}
</style>

<div class="container-fluid py-4">
    <!-- 页面标题 -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5><i class="bi bi-cart"></i> 域名商店</h5>
                    <a href="qr-center.php" class="btn btn-sm btn-outline-primary"><i class="bi bi-arrow-left"></i> 返回活码中心</a>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <div class="d-flex align-items-center mb-2">
                            <i class="bi bi-info-circle me-2"></i>
                            <strong>域名商店说明</strong>
                        </div>
                        <p>在这里您可以购买高质量的入口域名和落地域名，它们具有更高的稳定性和安全性，购买后将绑定到您的账户，仅供您使用。</p>
                        <div class="d-flex align-items-center mt-2">
                            <span class="me-2">当前账户积分：</span>
                            <span class="badge bg-primary"><?php echo $userrow['points']; ?></span>
                            <span>点</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 域名类型标签页 -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <ul class="nav nav-tabs" id="domainTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="entry-tab" data-bs-toggle="tab" data-bs-target="#entry-domains" type="button" role="tab" aria-selected="true">
                                <i class="bi bi-box-arrow-in-right"></i> 入口域名
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="landing-tab" data-bs-toggle="tab" data-bs-target="#landing-domains" type="button" role="tab" aria-selected="false">
                                <i class="bi bi-box-arrow-in-down-right"></i> 落地域名
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="my-tab" data-bs-toggle="tab" data-bs-target="#my-domains" type="button" role="tab" aria-selected="false">
                                <i class="bi bi-person"></i> 我的域名
                            </button>
                        </li>
                    </ul>
                    <div class="tab-content" id="domainTabsContent">
                        <!-- 入口域名 -->
                        <div class="tab-pane fade show active" id="entry-domains" role="tabpanel" aria-labelledby="entry-tab">
                            <div id="entryDomainLoading" class="loading-spinner">
                                <div class="spinner-border" role="status"></div>
                                <p class="mt-2">正在加载入口域名...</p>
                            </div>
                            <div class="table-responsive" id="entryDomainTableContainer" style="display:none;">
                                <table class="table table-hover" id="entryDomainTable">
                                    <thead>
                                        <tr>
                                            <th><i class="bi bi-link"></i> 域名</th>
                                            <th class="text-center"><i class="bi bi-shield"></i> QQ安全</th>
                                            <th class="text-center"><i class="bi bi-wechat"></i> 微信安全</th>
                                            <th class="text-center"><i class="bi bi-currency-yen"></i> 价格(积分)</th>
                                            <th class="text-center"><i class="bi bi-calendar3"></i> 添加时间</th>
                                            <th class="text-center"><i class="bi bi-gear"></i> 操作</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- 数据将通过AJAX加载 -->
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- 落地域名 -->
                        <div class="tab-pane fade" id="landing-domains" role="tabpanel" aria-labelledby="landing-tab">
                            <div id="landingDomainLoading" class="loading-spinner">
                                <div class="spinner-border" role="status"></div>
                                <p class="mt-2">正在加载落地域名...</p>
                            </div>
                            <div class="table-responsive" id="landingDomainTableContainer" style="display:none;">
                                <table class="table table-hover" id="landingDomainTable">
                                    <thead>
                                        <tr>
                                            <th><i class="bi bi-link"></i> 域名</th>
                                            <th class="text-center"><i class="bi bi-shield"></i> QQ安全</th>
                                            <th class="text-center"><i class="bi bi-wechat"></i> 微信安全</th>
                                            <th class="text-center"><i class="bi bi-currency-yen"></i> 价格(积分)</th>
                                            <th class="text-center"><i class="bi bi-calendar3"></i> 添加时间</th>
                                            <th class="text-center"><i class="bi bi-gear"></i> 操作</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- 数据将通过AJAX加载 -->
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- 我的域名 -->
                        <div class="tab-pane fade" id="my-domains" role="tabpanel" aria-labelledby="my-tab">
                            <div id="myDomainLoading" class="loading-spinner">
                                <div class="spinner-border" role="status"></div>
                                <p class="mt-2">正在加载我的域名...</p>
                            </div>
                            <div class="table-responsive" id="myDomainTableContainer" style="display:none;">
                                <table class="table table-hover" id="myDomainTable">
                                    <thead>
                                        <tr>
                                            <th><i class="bi bi-link"></i> 域名</th>
                                            <th class="text-center"><i class="bi bi-tag"></i> 类型</th>
                                            <th class="text-center"><i class="bi bi-shield"></i> QQ安全</th>
                                            <th class="text-center"><i class="bi bi-wechat"></i> 微信安全</th>
                                            <th class="text-center"><i class="bi bi-calendar3"></i> 购买时间</th>
                                            <th class="text-center"><i class="bi bi-gear"></i> 操作</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- 数据将通过AJAX加载 -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- 购买域名模态框 -->
<div class="modal fade" id="buyDomainModal" tabindex="-1" aria-labelledby="buyDomainModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="buyDomainModalLabel"><i class="bi bi-cart"></i> 购买域名</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="buyDomainForm">
                    <input type="hidden" id="domain_id" name="domain_id">
                    <input type="hidden" id="domain_type" name="domain_type">

                    <div class="mb-3 row">
                        <label class="col-sm-3 col-form-label">域名:</label>
                        <div class="col-sm-9">
                            <p class="form-control-plaintext fw-bold" id="domain_name"></p>
                        </div>
                    </div>

                    <div class="mb-3 row">
                        <label class="col-sm-3 col-form-label">类型:</label>
                        <div class="col-sm-9">
                            <p class="form-control-plaintext" id="domain_type_text"></p>
                        </div>
                    </div>

                    <div class="mb-3 row">
                        <label class="col-sm-3 col-form-label">价格:</label>
                        <div class="col-sm-9">
                            <p class="form-control-plaintext">
                                <span id="domain_price" class="fw-bold text-primary"></span> 积分
                            </p>
                        </div>
                    </div>

                    <div class="mb-3 row">
                        <label class="col-sm-3 col-form-label">当前积分:</label>
                        <div class="col-sm-9">
                            <p class="form-control-plaintext">
                                <span class="badge bg-info"><?php echo $userrow['points']; ?></span>
                            </p>
                        </div>
                    </div>

                    <div class="alert alert-warning">
                        <div class="d-flex align-items-center mb-2">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            <strong>购买提示</strong>
                        </div>
                        <p>购买后将从您的账户扣除相应积分，该域名将绑定到您的账户。</p>
                        <p class="mb-0">确认购买吗？</p>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i class="bi bi-x"></i> 取消</button>
                <button type="button" class="btn btn-primary" id="confirmBuyBtn"><i class="bi bi-check"></i> 确认购买</button>
            </div>
        </div>
    </div>
</div>

<!-- 确认释放域名模态框 -->
<div class="modal fade" id="releaseDomainModal" tabindex="-1" aria-labelledby="releaseDomainModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="releaseDomainModalLabel"><i class="bi bi-exclamation-triangle"></i> 释放确认</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="release_domain_id">
                <input type="hidden" id="release_domain_type">

                <p>确定要释放此域名吗？释放后将不再绑定到您的账户。</p>
                <div class="alert alert-danger">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                        <strong>注意：如果该域名正在被活码使用，请先修改相关活码！</strong>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i class="bi bi-x"></i> 取消</button>
                <button type="button" class="btn btn-danger" id="confirmReleaseBtn"><i class="bi bi-trash"></i> 确定释放</button>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // 加载入口域名列表
    loadEntryDomains();

    // 标签页切换事件
    $('#domainTabs button').on('shown.bs.tab', function (e) {
        var target = $(e.target).attr("data-bs-target");
        if (target == "#entry-domains") {
            loadEntryDomains();
        } else if (target == "#landing-domains") {
            loadLandingDomains();
        } else if (target == "#my-domains") {
            loadMyDomains();
        }
    });

    // 绑定购买按钮点击事件
    $("#confirmBuyBtn").click(function() {
        var domain_id = $("#domain_id").val();
        var domain_type = $("#domain_type").val();

        var modal = bootstrap.Modal.getInstance(document.getElementById('buyDomainModal'));
        modal.hide();

        var loadIndex = layer.load(2, {shade: [0.3, '#fff']});
        $.ajax({
            type: "POST",
            url: "ajax.php?act=buy_domain",
            data: {
                domain_id: domain_id,
                domain_type: domain_type
            },
            dataType: "json",
            success: function(data) {
                layer.close(loadIndex);
                if (data.code == 0) {
                    layer.msg(data.msg, {icon: 1});
                    // 重新加载数据
                    if (domain_type == "entry") {
                        loadEntryDomains();
                    } else {
                        loadLandingDomains();
                    }
                    loadMyDomains();

                    // 刷新页面以更新积分余额显示
                    setTimeout(function() {
                        location.reload();
                    }, 1500);
                } else {
                    layer.msg(data.msg, {icon: 2});
                }
            },
            error: function() {
                layer.close(loadIndex);
                layer.msg("服务器错误，请稍后再试", {icon: 2});
            }
        });
    });

    // 绑定释放按钮点击事件
    $("#confirmReleaseBtn").click(function() {
        var domain_id = $("#release_domain_id").val();
        var domain_type = $("#release_domain_type").val();

        var modal = bootstrap.Modal.getInstance(document.getElementById('releaseDomainModal'));
        modal.hide();

        var loadIndex = layer.load(2, {shade: [0.3, '#fff']});
        $.ajax({
            type: "POST",
            url: "ajax.php?act=release_domain",
            data: {
                domain_id: domain_id,
                domain_type: domain_type
            },
            dataType: "json",
            success: function(data) {
                layer.close(loadIndex);
                if (data.code == 0) {
                    layer.msg(data.msg, {icon: 1});
                    loadMyDomains();

                    // 根据释放的域名类型重载对应列表
                    if (domain_type == "entry") {
                        loadEntryDomains();
                    } else {
                        loadLandingDomains();
                    }
                } else {
                    layer.msg(data.msg, {icon: 2});
                }
            },
            error: function() {
                layer.close(loadIndex);
                layer.msg("服务器错误，请稍后再试", {icon: 2});
            }
        });
    });
});

// 加载入口域名列表
function loadEntryDomains() {
    $("#entryDomainLoading").show();
    $("#entryDomainTableContainer").hide();

    $.ajax({
        type: "GET",
        url: "ajax.php?act=get_store_domains",
        data: {
            type: "entry"
        },
        dataType: "json",
        success: function(data) {
            $("#entryDomainLoading").hide();
            $("#entryDomainTableContainer").show();

            var html = "";
            if (data.code == 0) {
                $.each(data.domains, function(i, item) {
                    var qqSafe = item.qqsafe == 1 ? 
                        '<span class="badge bg-success"><i class="bi bi-check-lg"></i> 安全</span>' : 
                        '<span class="badge bg-danger"><i class="bi bi-x-lg"></i> 拦截</span>';
                    var wxSafe = item.wxsafe == 1 ? 
                        '<span class="badge bg-success"><i class="bi bi-check-lg"></i> 安全</span>' : 
                        '<span class="badge bg-danger"><i class="bi bi-x-lg"></i> 拦截</span>';
                    var buyBtn = '<button class="btn btn-sm btn-primary" onclick="showBuyModal(' + item.id + ', \'' + item.domain + '\', \'entry\', ' + item.price + ')"><i class="bi bi-cart"></i> 购买</button>';

                    html += '<tr>' +
                           '<td><strong>' + item.domain + '</strong>' + (item.remark ? '<br><small class="text-muted">' + item.remark + '</small>' : '') + '</td>' +
                           '<td class="text-center">' + qqSafe + '</td>' +
                           '<td class="text-center">' + wxSafe + '</td>' +
                           '<td class="text-center"><span class="badge bg-primary">' + item.price + '</span></td>' +
                           '<td class="text-center">' + item.addtime + '</td>' +
                           '<td class="text-center">' + buyBtn + '</td>' +
                           '</tr>';
                });
            }
            if (html == "") {
                html = '<tr><td colspan="6" class="text-center">暂无可购买的入口域名</td></tr>';
            }
            $("#entryDomainTable tbody").html(html);
        },
        error: function() {
            $("#entryDomainLoading").hide();
            $("#entryDomainTableContainer").show();
            $("#entryDomainTable tbody").html('<tr><td colspan="6" class="text-center text-danger">加载入口域名失败，请刷新页面重试</td></tr>');
            layer.msg("加载入口域名失败，请稍后再试", {icon: 2});
        }
    });
}

// 加载落地域名列表
function loadLandingDomains() {
    $("#landingDomainLoading").show();
    $("#landingDomainTableContainer").hide();

    $.ajax({
        type: "GET",
        url: "ajax.php?act=get_store_domains",
        data: {
            type: "landing"
        },
        dataType: "json",
        success: function(data) {
            $("#landingDomainLoading").hide();
            $("#landingDomainTableContainer").show();

            var html = "";
            if (data.code == 0) {
                $.each(data.domains, function(i, item) {
                    var qqSafe = item.qqsafe == 1 ? 
                        '<span class="badge bg-success"><i class="bi bi-check-lg"></i> 安全</span>' : 
                        '<span class="badge bg-danger"><i class="bi bi-x-lg"></i> 拦截</span>';
                    var wxSafe = item.wxsafe == 1 ? 
                        '<span class="badge bg-success"><i class="bi bi-check-lg"></i> 安全</span>' : 
                        '<span class="badge bg-danger"><i class="bi bi-x-lg"></i> 拦截</span>';
                    var buyBtn = '<button class="btn btn-sm btn-primary" onclick="showBuyModal(' + item.id + ', \'' + item.domain + '\', \'landing\', ' + item.price + ')"><i class="bi bi-cart"></i> 购买</button>';

                    html += '<tr>' +
                           '<td><strong>' + item.domain + '</strong>' + (item.remark ? '<br><small class="text-muted">' + item.remark + '</small>' : '') + '</td>' +
                           '<td class="text-center">' + qqSafe + '</td>' +
                           '<td class="text-center">' + wxSafe + '</td>' +
                           '<td class="text-center"><span class="badge bg-primary">' + item.price + '</span></td>' +
                           '<td class="text-center">' + item.addtime + '</td>' +
                           '<td class="text-center">' + buyBtn + '</td>' +
                           '</tr>';
                });
            }
            if (html == "") {
                html = '<tr><td colspan="6" class="text-center">暂无可购买的落地域名</td></tr>';
            }
            $("#landingDomainTable tbody").html(html);
        },
        error: function() {
            $("#landingDomainLoading").hide();
            $("#landingDomainTableContainer").show();
            $("#landingDomainTable tbody").html('<tr><td colspan="6" class="text-center text-danger">加载落地域名失败，请刷新页面重试</td></tr>');
            layer.msg("加载落地域名失败，请稍后再试", {icon: 2});
        }
    });
}

// 加载我的域名列表
function loadMyDomains() {
    $("#myDomainLoading").show();
    $("#myDomainTableContainer").hide();

    $.ajax({
        type: "GET",
        url: "ajax.php?act=get_my_domains",
        dataType: "json",
        success: function(data) {
            $("#myDomainLoading").hide();
            $("#myDomainTableContainer").show();

            var html = "";
            if (data.code == 0) {
                $.each(data.domains, function(i, item) {
                    var typeText = item.type == "entry" ? 
                        '<span class="badge bg-primary"><i class="bi bi-box-arrow-in-right"></i> 入口域名</span>' : 
                        '<span class="badge bg-info"><i class="bi bi-box-arrow-in-down-right"></i> 落地域名</span>';
                    var qqSafe = item.qqsafe == 1 ? 
                        '<span class="badge bg-success"><i class="bi bi-check-lg"></i> 安全</span>' : 
                        '<span class="badge bg-danger"><i class="bi bi-x-lg"></i> 拦截</span>';
                    var wxSafe = item.wxsafe == 1 ? 
                        '<span class="badge bg-success"><i class="bi bi-check-lg"></i> 安全</span>' : 
                        '<span class="badge bg-danger"><i class="bi bi-x-lg"></i> 拦截</span>';

                    html += '<tr>' +
                           '<td><strong>' + item.domain + '</strong></td>' +
                           '<td class="text-center">' + typeText + '</td>' +
                           '<td class="text-center">' + qqSafe + '</td>' +
                           '<td class="text-center">' + wxSafe + '</td>' +
                           '<td class="text-center">' + item.addtime + '</td>' +
                           '<td class="text-center"><button class="btn btn-sm btn-danger" onclick="showReleaseModal(' + item.id + ', \'' + item.type + '\')"><i class="bi bi-link-break"></i> 释放</button></td>' +
                           '</tr>';
                });
            }
            if (html == "") {
                html = '<tr><td colspan="6" class="text-center">您还没有购买任何域名</td></tr>';
            }
            $("#myDomainTable tbody").html(html);
        },
        error: function() {
            $("#myDomainLoading").hide();
            $("#myDomainTableContainer").show();
            $("#myDomainTable tbody").html('<tr><td colspan="6" class="text-center text-danger">加载我的域名失败，请刷新页面重试</td></tr>');
            layer.msg("加载我的域名失败，请稍后再试", {icon: 2});
        }
    });
}

// 显示购买域名模态框
function showBuyModal(id, domain, type, price) {
    $("#domain_id").val(id);
    $("#domain_name").text(domain);
    $("#domain_type").val(type);
    $("#domain_type_text").text(type == "entry" ? "入口域名" : "落地域名");
    $("#domain_price").text(price);

    var buyDomainModal = new bootstrap.Modal(document.getElementById('buyDomainModal'));
    buyDomainModal.show();
}

// 显示释放域名模态框
function showReleaseModal(id, type) {
    $("#release_domain_id").val(id);
    $("#release_domain_type").val(type);

    var releaseDomainModal = new bootstrap.Modal(document.getElementById('releaseDomainModal'));
    releaseDomainModal.show();
}
</script>

            </div> <!-- 结束 content-area -->
        </div> <!-- 结束 main-content -->
    </div> <!-- 结束 app-container -->
</body>
</html>