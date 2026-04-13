<?php
include('../includes/common.php');
if ($islogin2 == 1) {
} else exit("<script language='javascript'>window.location.href='./login.php';</script>");

// 设置页面标识符，确保导航菜单正确高亮
$_GET['mod'] = 'qr-center';
// 设置子页面标识，用于活码列表高亮
$_GET['subpage'] = 'qr-list';
?>

<?php
$title = '活码列表';
include('head.php');
?>

<!-- 添加Bootstrap Table和相关库 -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-table@1.18.3/dist/bootstrap-table.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap-table@1.18.3/dist/bootstrap-table.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap-table@1.18.3/dist/locale/bootstrap-table-zh-CN.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/jquery.nicescroll@3.7.6/jquery.nicescroll.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
<!-- 引入Layer.js - 确保在使用前加载 -->
<script src="https://cdn.jsdelivr.net/npm/layer@3.5.1/dist/layer.min.js"></script>
<!-- 添加剪贴板JS -->
<script src="https://cdn.jsdelivr.net/npm/clipboard@2.0.8/dist/clipboard.min.js"></script>

<!-- 引入SweetAlert2库 -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.1.4/dist/sweetalert2.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.1.4/dist/sweetalert2.all.min.js"></script>

<!-- 自定义样式 -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">

<section id="main-content">
    <section class="wrapper">
        <!-- 弹窗部分 - 分享二维码 -->
        <div class="modal fade" id="shareQRModal" tabindex="-1" role="dialog" aria-labelledby="shareQRModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="shareQRModalLabel"><i class="bi bi-share"></i> 分享二维码</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="关闭"></button>
                    </div>
                    <div class="modal-body" id="shareQRModalContent">
                        <div class="text-center p-3">
                            <div class="spinner-border text-primary" role="status">
                                <span class="sr-only">加载中...</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- 弹窗部分 - 查看统计 -->
        <div class="modal fade" id="statsModal" tabindex="-1" role="dialog" aria-labelledby="statsModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="statsModalLabel"><i class="bi bi-bar-chart-fill"></i> 活码统计</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="关闭"></button>
                    </div>
                    <div class="modal-body" id="statsModalContent">
                        <div class="text-center p-3">
                            <div class="spinner-border text-primary" role="status">
                                <span class="sr-only">加载中...</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <section class="panel">
                    <div class="card">
                        <div class="card-body">
                            <div class="section-header">
                                <h2 class="section-title"><i class="bi bi-qr-code"></i> 活码列表</h2>
                            </div>
                            
                            <!-- 工具栏 -->
                            <div id="toolbar" class="toolbar-container">
                                <div class="action-buttons">
                                    <a href="qr-edit.php" class="action-btn btn-create">
                                        <i class="bi bi-plus-lg"></i> 
                                        <span>创建新活码</span>
                                    </a>
                                    <button type="button" class="action-btn btn-delete" onclick="batchDelete()">
                                        <i class="bi bi-trash"></i> 
                                        <span>批量删除</span>
                                    </button>
                                    
                                    <div class="filter-toggle">
                                        <button type="button" class="action-btn btn-filter" id="toggleFilter">
                                            <i class="bi bi-funnel"></i>
                                            <span>筛选</span>
                                            <i class="bi bi-chevron-down toggle-icon"></i>
                                        </button>
                                    </div>
                                </div>
                                
                                <!-- 筛选区域 -->
                                <div class="filter-panel" id="filterPanel">
                                    <form class="filter-form" id="filterForm">
                                        <div class="filter-group">
                                            <label>微信状态</label>
                                            <select class="filter-select" name="wechat_status">
                                                <option value="">全部状态</option>
                                                <option value="1">正常</option>
                                                <option value="0">已屏蔽</option>
                                            </select>
                                        </div>
                                        <div class="filter-group">
                                            <label>创建时间</label>
                                            <select class="filter-select" name="time">
                                                <option value="">全部时间</option>
                                                <option value="today">今天</option>
                                                <option value="yesterday">昨天</option>
                                                <option value="week">本周</option>
                                                <option value="month">本月</option>
                                            </select>
                                        </div>
                                        <div class="filter-actions">
                                            <button type="button" class="filter-btn btn-apply" onclick="filterList()">
                                                <i class="bi bi-check2"></i> 应用筛选
                                            </button>
                                            <button type="button" class="filter-btn btn-reset" onclick="resetFilter()">
                                                <i class="bi bi-arrow-repeat"></i> 重置
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            
                            <!-- 表格区域 -->
                            <table id="qrListTable"></table>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </section>
</section>

<style>
    :root {
        --primary: #2ECC71;
        --primary-light: #5cb85c;
        --primary-dark: #27AE60;
        --secondary: #6c757d;
        --light: #f8f9fa;
        --dark: #343a40;
        --danger: #E74C3C;
        --warning: #F39C12;
        --success: #2ECC71;
        --bg-color: #f5f5f5;
        --card-shadow: 0 5px 20px rgba(0,0,0,0.05);
    }

    body {
        background-color: var(--bg-color);
        color: var(--dark);
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
    }

    .card {
        border-radius: 12px;
        box-shadow: var(--card-shadow);
        border: none;
        margin-bottom: 30px;
        background: white;
    }

    .card-body {
        padding: 25px;
    }

    /* 表格样式 */
    .table {
        color: #333;
        border-radius: 8px;
        overflow: hidden;
    }

    .bootstrap-table .table thead th {
        background-color: var(--light);
        color: #495057;
        font-weight: 600;
        border-bottom: 2px solid #dee2e6;
    }

    .bootstrap-table .table tbody tr:hover {
        background-color: rgba(46, 204, 113, 0.05);
    }

    /* 按钮样式 */
    .btn {
        border-radius: 6px;
        font-weight: 500;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 5px;
        transition: all 0.3s;
    }

    .btn i {
        font-size: 1.1em;
    }

    .btn-primary {
        background-color: var(--primary);
        border-color: var(--primary);
    }

    .btn-primary:hover, .btn-primary:focus {
        background-color: var(--primary-dark);
        border-color: var(--primary-dark);
        box-shadow: 0 5px 15px rgba(46, 204, 113, 0.3);
        transform: translateY(-2px);
    }

    .btn-warning {
        background-color: var(--warning);
        border-color: var(--warning);
        color: #212529;
    }

    .btn-warning:hover, .btn-warning:focus {
        background-color: #e0a800;
        border-color: #e0a800;
        box-shadow: 0 5px 15px rgba(255, 193, 7, 0.3);
        transform: translateY(-2px);
    }

    .btn-danger {
        background-color: var(--danger);
        border-color: var(--danger);
    }

    .btn-danger:hover, .btn-danger:focus {
        background-color: #c82333;
        border-color: #c82333;
        box-shadow: 0 5px 15px rgba(220, 53, 69, 0.3);
        transform: translateY(-2px);
    }

    .btn-success {
        background-color: var(--success);
        border-color: var(--success);
    }

    .btn-success:hover, .btn-success:focus {
        background-color: var(--primary-dark);
        border-color: var(--primary-dark);
        box-shadow: 0 5px 15px rgba(46, 204, 113, 0.3);
        transform: translateY(-2px);
    }

    .btn-default {
        color: #495057;
        border-color: #ced4da;
        background-color: #fff;
    }

    .btn-default:hover, .btn-default:focus {
        background-color: #f8f9fa;
        border-color: #adb5bd;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        transform: translateY(-2px);
    }

    .btn-xs {
        padding: 0.15rem 0.4rem;
        font-size: 0.75rem;
        line-height: 1.5;
    }

    /* 模态框样式 */
    .modal-content {
        border-radius: 12px;
        border: none;
        box-shadow: 0 15px 40px rgba(0,0,0,0.1);
    }

    .modal-header {
        border-bottom: 1px solid rgba(0,0,0,0.05);
        background-color: var(--light);
        border-radius: 12px 12px 0 0;
        padding: 15px 20px;
    }

    .modal-title {
        font-weight: 600;
        color: var(--dark);
    }

    .modal-body {
        padding: 20px;
    }

    .modal-footer {
        border-top: 1px solid rgba(0,0,0,0.05);
        padding: 15px 20px;
    }

    /* 标题和导航 */
    .section-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 20px;
        padding-bottom: 15px;
        border-bottom: 1px solid rgba(0,0,0,0.05);
    }

    .section-title {
        font-size: 24px;
        font-weight: 700;
        color: var(--dark);
        margin: 0;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .section-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-bottom: 15px;
    }

    /* 标签样式 */
    .badge {
        padding: 0.4rem 0.6rem;
        border-radius: 50px;
        font-weight: 500;
        font-size: 0.75rem;
    }

    .badge-success {
        background-color: var(--success);
        color: white;
    }

    .badge-danger {
        background-color: var(--danger);
        color: white;
    }

    .badge-primary {
        background-color: var(--primary);
        color: white;
    }

    .badge-warning {
        background-color: var(--warning);
        color: #212529;
    }

    /* 表格工具栏 */
    #toolbar {
        margin-bottom: 1rem;
    }

    /* 搜索框 */
    .fixed-table-toolbar .search .search-input {
        border-radius: 6px;
        border: 1px solid #ddd;
        padding: 8px 12px;
        transition: all 0.3s;
    }

    .fixed-table-toolbar .search .search-input:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(46, 204, 113, 0.2);
    }

    /* 表格分页 */
    .fixed-table-pagination .pagination-detail, 
    .fixed-table-pagination .pagination {
        margin-top: 1rem;
    }

    .pagination .page-item.active .page-link {
        background-color: var(--primary);
        border-color: var(--primary);
    }

    .pagination .page-link {
        color: var(--primary);
    }

    .pagination .page-link:hover {
        color: var(--primary-dark);
    }

    /* QR样式 */
    .qr-image {
        cursor: pointer;
        transition: all 0.3s;
        border-radius: 8px;
    }
    
    .qr-image:hover {
        transform: scale(1.05);
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }

    /* Bootstrap table图标修复 */
    .bootstrap-table .icon-refresh::before {
        content: "\F133"!important;
        font-family: 'bootstrap-icons'!important;
    }

    .bootstrap-table .icon-toggle-off::before {
        content: "\F292"!important;
        font-family: 'bootstrap-icons'!important;
    }

    .bootstrap-table .icon-toggle-on::before {
        content: "\F293"!important;
        font-family: 'bootstrap-icons'!important;
    }

    .bootstrap-table .icon-list::before {
        content: "\F649"!important;
        font-family: 'bootstrap-icons'!important;
    }

    .bootstrap-table .icon-search::before {
        content: "\F52A"!important;
        font-family: 'bootstrap-icons'!important;
    }

    /* 分享容器 */
    .share-qr-container {
        padding: 20px;
    }
    
    .qr-image-wrapper {
        text-align: center;
        margin-bottom: 15px;
    }
    
    .qr-large-image {
        max-width: 200px;
        max-height: 200px;
        display: inline-block;
        border: 1px solid #ddd;
        padding: 5px;
        border-radius: 4px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }

    /* 统计数据样式 */
    .stat-container {
        padding: 15px;
    }
    
    .mini-stat {
        background: #fff;
        padding: 20px;
        border-radius: 12px;
        margin-bottom: 20px;
        border: 1px solid #ddd;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }
    
    .mini-stat-icon {
        width: 60px;
        height: 60px;
        display: inline-block;
        line-height: 60px;
        text-align: center;
        font-size: 30px;
        border-radius: 100%;
        float: left;
        margin-right: 10px;
        color: #fff;
    }
    
    .mini-stat-info {
        font-size: 14px;
        padding-top: 2px;
        color: #777;
    }
    
    .mini-stat-info span {
        display: block;
        font-size: 24px;
        font-weight: 600;
        color: #333;
        margin-bottom: 5px;
    }
    
    .bg-info {
        background-color: #5bc0de;
    }
    
    .bg-success {
        background-color: #5cb85c;
    }
    
    .bg-warning {
        background-color: #f0ad4e;
    }
    
    .bg-danger {
        background-color: #d9534f;
    }

    /* 卡片视图样式 */
    .bootstrap-table .card-view {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
        gap: 24px;
        padding: 20px;
        background-color: #f9fafc;
    }
    
    .bootstrap-table .card-view .card {
        position: relative;
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.04), 0 6px 10px rgba(0, 0, 0, 0.02);
        overflow: hidden;
        transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
        border: none;
        display: flex;
        flex-direction: column;
    }
    
    .bootstrap-table .card-view .card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 5px;
        background: linear-gradient(135deg, #2ECC71, #1abc9c);
        opacity: 0.9;
    }
    
    .bootstrap-table .card-view .card-content {
        padding: 24px;
    }
    
    .bootstrap-table .card-view .card-body {
        padding: 8px 0;
        display: flex;
        flex-direction: row;
        align-items: center;
        border-bottom: none;
        margin-bottom: 4px;
    }
    
    .bootstrap-table .card-view .title {
        font-size: 11px;
        color: rgba(0, 0, 0, 0.5);
        margin-bottom: 0;
        font-weight: 500;
        width: 80px;
        flex-shrink: 0;
    }
    
    .bootstrap-table .card-view .value {
        color: rgba(0, 0, 0, 0.8);
        font-size: 13px;
        word-break: break-word;
        flex-grow: 1;
    }
    
    /* 卡片加载动画 */
    @keyframes cardAppear {
        0% {
            opacity: 0;
            transform: translateY(30px) scale(0.95);
        }
        60% {
            transform: translateY(-5px) scale(1.02);
        }
        100% {
            opacity: 1;
            transform: translateY(0) scale(1);
        }
    }
    
    .bootstrap-table .card-view .card {
        opacity: 0;
        animation: cardAppear 0.6s cubic-bezier(0.2, 0.8, 0.2, 1) forwards;
    }
    
    /* 添加延迟加载动画 */
    .bootstrap-table .card-view .card:nth-child(1) { animation-delay: 0.05s; }
    .bootstrap-table .card-view .card:nth-child(2) { animation-delay: 0.1s; }
    .bootstrap-table .card-view .card:nth-child(3) { animation-delay: 0.15s; }
    .bootstrap-table .card-view .card:nth-child(4) { animation-delay: 0.2s; }
    .bootstrap-table .card-view .card:nth-child(5) { animation-delay: 0.25s; }
    
    /* 响应式调整 */
    @media (max-width: 768px) {
        .card-body {
            padding: 15px;
        }
        
        .section-header {
            flex-direction: column;
            align-items: flex-start;
        }
        
        .section-actions {
            margin-top: 10px;
            flex-wrap: wrap;
        }
        
        .bootstrap-table .card-view {
            grid-template-columns: 1fr;
            padding: 15px;
            gap: 15px;
        }
    }

    /* 筛选器样式 */
    .filter-container {
        background-color: rgba(255, 255, 255, 0.7);
        border-radius: 10px;
        padding: 15px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        margin-bottom: 20px;
    }
    
    .filter-container .form-inline {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }
    
    .filter-container label {
        font-weight: 500;
        color: #555;
    }
    
    .filter-container .form-control {
        border-radius: 6px;
    }
    
    @media (max-width: 768px) {
        .filter-container .form-inline {
            flex-direction: column;
        }
        
        .filter-container .form-group {
            margin-bottom: 10px;
            width: 100%;
        }
    }
    
    .mt-3 {
        margin-top: 15px;
    }
    
    .mb-3 {
        margin-bottom: 15px;
    }
    
    .mr-2 {
        margin-right: 8px;
    }
    
    .mr-3 {
        margin-right: 15px;
    }
    
    .ml-2 {
        margin-left: 8px;
    }
    
    /* 新的工具栏和按钮样式 */
    .toolbar-container {
        margin-bottom: 25px;
    }
    
    .action-buttons {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        margin-bottom: 15px;
    }
    
    .action-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 10px 18px;
        border-radius: 12px;
        font-weight: 600;
        font-size: 14px;
        color: #fff;
        border: none;
        cursor: pointer;
        transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.04), 0 1px 3px rgba(0, 0, 0, 0.08);
    }
    
    .action-btn:hover, .action-btn:focus {
        transform: translateY(-2px);
        box-shadow: 0 7px 14px rgba(50, 50, 93, 0.1), 0 3px 6px rgba(0, 0, 0, 0.08);
        color: #fff;
        text-decoration: none;
    }
    
    .action-btn:active {
        transform: translateY(-1px);
    }
    
    .action-btn i {
        font-size: 18px;
    }
    
    .btn-create {
        background: linear-gradient(135deg, #2ECC71, #27AE60);
    }
    
    .btn-delete {
        background: linear-gradient(135deg, #E74C3C, #C0392B);
    }
    
    .btn-filter {
        background: linear-gradient(135deg, #3498DB, #2980B9);
    }
    
    .toggle-icon {
        transition: transform 0.3s ease;
        font-size: 14px;
        margin-left: 5px;
    }
    
    .toggle-icon.rotate {
        transform: rotate(180deg);
    }
    
    /* 筛选面板样式 */
    .filter-panel {
        background: white;
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 20px;
        box-shadow: 0 7px 14px rgba(50, 50, 93, 0.1), 0 3px 6px rgba(0, 0, 0, 0.08);
        display: none;
    }
    
    .filter-form {
        display: flex;
        flex-wrap: wrap;
        gap: 20px;
        align-items: flex-end;
    }
    
    .filter-group {
        flex: 1;
        min-width: 200px;
    }
    
    .filter-group label {
        display: block;
        margin-bottom: 8px;
        font-weight: 600;
        color: #555;
        font-size: 13px;
    }
    
    .filter-select {
        width: 100%;
        padding: 10px 15px;
        border-radius: 8px;
        border: 1px solid #ddd;
        background-color: #f9f9f9;
        transition: all 0.3s;
        font-size: 14px;
    }
    
    .filter-select:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(46, 204, 113, 0.2);
        outline: none;
    }
    
    .filter-actions {
        display: flex;
        gap: 10px;
    }
    
    .filter-btn {
        padding: 10px 15px;
        border-radius: 8px;
        font-weight: 500;
        font-size: 13px;
        border: none;
        cursor: pointer;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }
    
    .btn-apply {
        background-color: var(--primary);
        color: white;
    }
    
    .btn-apply:hover {
        background-color: var(--primary-dark);
        box-shadow: 0 4px 8px rgba(46, 204, 113, 0.2);
    }
    
    .btn-reset {
        background-color: #f1f1f1;
        color: #555;
    }
    
    .btn-reset:hover {
        background-color: #e5e5e5;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.05);
    }
    
    @media (max-width: 768px) {
        .filter-form {
            flex-direction: column;
        }
        
        .filter-group, .filter-actions {
            width: 100%;
        }
        
        .action-btn {
            padding: 8px 15px;
            font-size: 13px;
        }
        
        .action-btn i {
            font-size: 16px;
        }
    }
</style>

<script>
// 页面加载完成后初始化所有功能
document.addEventListener('DOMContentLoaded', function() {
    // 初始化表格
    initTable();
    
    // 初始化筛选面板
    $("#filterPanel").hide();
    
    // 切换筛选面板显示/隐藏
    $("#toggleFilter").click(function() {
        $("#filterPanel").slideToggle(300);
        $(".toggle-icon").toggleClass("rotate");
    });
    
    $("#selectAll").click(function() {
        $("input[name='ids[]']").prop("checked", this.checked);
    });
    
    // 强制所有滚动事件为被动模式
    makeAllScrollEventsPassive();
    
    // 使用被动模式监听视图切换事件
    $(document).on('toggle.bs.table', '#qrListTable', {passive: true}, function(e, cardView) {
        if (cardView) {
            setTimeout(function() {
                enhanceCardView();
            }, 300);
        }
    });
    
    // 使用被动模式监听页码切换事件
    $(document).on('page-change.bs.table', '#qrListTable', {passive: true}, function() {
        setTimeout(function() {
            if ($('.bootstrap-table .card-view').length > 0) {
                enhanceCardView();
            }
        }, 300);
    });
    
    // 使用被动模式监听搜索事件
    $(document).on('search.bs.table', '#qrListTable', {passive: true}, function() {
        setTimeout(function() {
            if ($('.bootstrap-table .card-view').length > 0) {
                enhanceCardView();
            }
        }, 300);
    });
    
    // 使用事件委托绑定微信状态检测点击事件，避免使用行内onclick
    $(document).on('click', '.wechat-status-badge', {passive: true}, function() {
        var id = $(this).data('id');
        var entryDomain = $(this).data('entry-domain');
        var landingDomain = $(this).data('landing-domain');
        checkDomains(id, entryDomain, landingDomain);
    });
    
    // 使用事件委托绑定下拉菜单点击
    $(document).on('click', '.dropdown-toggle', {passive: true}, function() {
        var dropdown = new bootstrap.Dropdown(this);
        dropdown.toggle();
    });
    
    // 重新初始化所有弹出提示和工具提示
    setTimeout(function() {
        if (typeof bootstrap !== 'undefined') {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.forEach(function(tooltipTriggerEl) {
                new bootstrap.Tooltip(tooltipTriggerEl);
            });
            
            var dropdownElementList = [].slice.call(document.querySelectorAll('.dropdown-toggle'));
            dropdownElementList.forEach(function(dropdownToggleEl) {
                new bootstrap.Dropdown(dropdownToggleEl);
            });
        }
    }, 1000);
});

// 强制所有滚动事件为被动模式以解决浏览器警告
function makeAllScrollEventsPassive() {
    try {
        // 保存原始addEventListener方法
        const originalAddEventListener = EventTarget.prototype.addEventListener;
        
        // 重写addEventListener方法
        EventTarget.prototype.addEventListener = function(type, listener, options) {
            // 滚动相关事件列表
            const scrollEvents = ['scroll', 'wheel', 'mousewheel', 'touchstart', 'touchmove', 'touchend', 'touchcancel'];
            
            // 检查是否为滚动相关事件
            if (scrollEvents.indexOf(type) !== -1) {
                let newOptions = options;
                
                // 如果options是对象，添加passive: true
                if (typeof options === 'object') {
                    newOptions = Object.assign({}, options, { passive: true });
                } 
                // 如果options是布尔值或未定义，创建新对象
                else {
                    newOptions = { 
                        passive: true,
                        capture: options === true
                    };
                }
                
                // 使用新的options调用原始方法
                return originalAddEventListener.call(this, type, listener, newOptions);
            }
            
            // 其他事件类型正常处理
            return originalAddEventListener.call(this, type, listener, options);
        };
        
        // 强制jQuery的scroll事件也使用passive
        if (typeof $ !== 'undefined' && typeof $.event !== 'undefined' && $.event.special && $.event.special.scroll) {
            $.event.special.scroll = {
                setup: function() {
                    if (this.addEventListener) {
                        this.addEventListener('scroll', $.event.special.scroll.handler, { passive: true });
                    } else {
                        this.onscroll = $.event.special.scroll.handler;
                    }
                },
                teardown: function() {
                    if (this.removeEventListener) {
                        this.removeEventListener('scroll', $.event.special.scroll.handler, { passive: true });
                    } else {
                        this.onscroll = null;
                    }
                },
                handler: function(event) {
                    var orgEvent = event || window.event;
                    event = $.event.fix(orgEvent);
                    event.type = "scroll";
                    return $.event.dispatch.call(this, event);
                }
            };
        }
        
        // 尝试覆盖Bootstrap的scroll事件监听器
        if (typeof bootstrap !== 'undefined') {
            const origAddEventListener = Element.prototype.addEventListener;
            Element.prototype.addEventListener = function(type, listener, options) {
                if (type === 'scroll') {
                    return origAddEventListener.call(this, type, listener, { passive: true });
                }
                return origAddEventListener.call(this, type, listener, options);
            };
        }
        
        console.log('所有滚动事件已设置为被动模式');
    } catch (e) {
        console.error('设置被动滚动事件失败:', e);
    }
}

// 初始化表格
function initTable() {
    $('#qrListTable').bootstrapTable('destroy');
    $('#qrListTable').bootstrapTable({
        classes: 'table table-hover',
        url: 'ajax.php?act=qrcode_list',
        method: 'get',
        dataType: 'json',
        uniqueId: 'id',
        selectItemName: 'ids[]',
        idField: 'id',
        toolbar: '#toolbar',
        showColumns: false,
        showRefresh: true,
        showToggle: true,
        icons: {
            refresh: 'bi bi-arrow-clockwise',
            toggleOff: 'bi bi-layout-text-window-reverse',
            toggleOn: 'bi bi-grid-fill',
            columns: 'bi bi-list-ul'
        },
        pagination: true,
        sortOrder: "desc",
        queryParams: function(params) {
            var temp = {
                limit: params.limit,
                offset: params.offset,
                page: (params.offset / params.limit) + 1,
                sort: params.sort,
                sortOrder: params.order,
                kw: $(".search-input").val(),
                wechat_status: $("select[name='wechat_status']").val(),
                time: $("select[name='time']").val()
            };
            return temp;
        },
        sidePagination: "server",
        pageNumber: 1,
        pageSize: 10,
        pageList: [10, 25, 50, 100],
        search: true,
        columns: [{
                field: 'ids',
                checkbox: true
            }, {
                field: 'id',
                title: 'ID',
                sortable: true
            },
            {
                field: 'qr_url',
                title: '二维码',
                formatter: function(value, row, index) {
                    return '<img src="' + (row.qr_url || 'qrcode/' + row.id + '.png') + '" width="50" height="50" class="img-thumbnail qr-image" onclick="shareQR(' + row.id + ')" style="cursor:pointer" title="点击查看大图">';
                }
            },
            {
                field: 'name',
                title: '活码名称',
                formatter: function(value, row, index) {
                    return value || '未命名活码';
                },
                sortable: true
            },
            {
                field: 'views',
                title: '访问量',
                formatter: function(value, row, index) {
                    return '<span><i class="bi bi-eye"></i> ' + (value || '0') + '</span>';
                },
                sortable: true
            },
            {
                field: 'ip_count',
                title: '访问IP数',
                formatter: function(value, row, index) {
                    return '<span><i class="bi bi-people"></i> ' + (value || '0') + '</span>';
                },
                sortable: true
            },
            {
                field: 'entry_domain',
                title: '入口域名',
                formatter: function(value, row, index) {
                    return value || '-';
                }
            },
            {
                field: 'landing_domain',
                title: '落地页域名',
                formatter: function(value, row, index) {
                    return value || '-';
                }
            },
            {
                field: 'wechat_status',
                title: '微信状态',
                formatter: function(value, row, index) {
                    var statusClass = row.wechat_status == 1 ? 'badge-success' : 'badge-danger';
                    var statusText = row.wechat_status == 1 ? '正常' : '已屏蔽';
                    var statusIcon = row.wechat_status == 1 ? 'bi-check-circle' : 'bi-x-circle';
                    
                    // 使用data属性存储id和domain，避免使用onclick直接调用函数
                    return '<span class="badge ' + statusClass + ' cursor-pointer wechat-status-badge" data-id="' + row.id + '" data-entry-domain="' + row.entry_domain + '" data-landing-domain="' + row.landing_domain + '" style="cursor: pointer;" title="点击检测域名"><i class="bi ' + statusIcon + '"></i> ' + statusText + '</span>';
                },
                sortable: true
            },
            {
                field: 'addtime',
                title: '创建时间',
                formatter: function(value, row, index) {
                    return '<span><i class="bi bi-calendar-plus"></i> ' + value + '</span>';
                },
                sortable: true
            },
            {
                field: 'updatetime',
                title: '更新时间',
                formatter: function(value, row, index) {
                    return '<span><i class="bi bi-calendar-check"></i> ' + value + '</span>';
                },
                sortable: true
            },
            {
                field: 'edit',
                title: '编辑',
                formatter: function(value, row, index) {
                    return '<a href="javascript:void(0);" class="btn btn-xs btn-warning" onclick="editQR(' + row.id + ')">' +
                           '<i class="bi bi-pencil"></i> 编辑</a>';
                }
            },
            {
                field: 'operate',
                title: '更多操作',
                formatter: function(value, row, index) {
                    return                 '<div class="btn-group">' +
                           '<button type="button" class="btn btn-xs btn-primary dropdown-toggle" data-bs-toggle="dropdown">' +
                           '更多 <span class="caret"></span>' +
                           '</button>' +
                           '<ul class="dropdown-menu dropdown-menu-end">' +
                           '<li><a href="javascript:void(0);" onclick="shareQR(' + row.id + ')" class="dropdown-item"><i class="bi bi-share"></i> 分享二维码</a></li>' +
                           '<li><a href="javascript:void(0);" onclick="viewStats(' + row.id + ')" class="dropdown-item"><i class="bi bi-bar-chart"></i> 查看统计</a></li>' +
                           '<li><a href="javascript:void(0);" onclick="deleteQR(' + row.id + ')" class="dropdown-item"><i class="bi bi-trash"></i> 删除</a></li>' +
                           '</ul>' +
                           '</div>';
                }
            }
        ],
                    onLoadSuccess: function(data) {
                // 初始化Bootstrap 5 tooltips
                if (typeof bootstrap !== 'undefined') {
                    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
                    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                        return new bootstrap.Tooltip(tooltipTriggerEl);
                    });
                    
                    // 初始化下拉菜单
                    var dropdownElementList = [].slice.call(document.querySelectorAll('.dropdown-toggle'));
                    dropdownElementList.map(function (dropdownToggleEl) {
                        return new bootstrap.Dropdown(dropdownToggleEl);
                    });
                } else if (typeof $.fn.tooltip === 'function') {
                    $("[data-toggle='tooltip']").tooltip();
                }
                // 初始化复制功能
                if(typeof ClipboardJS !== 'undefined') {
                    new ClipboardJS('.clipboard');
                }
                // 美化卡片视图
                setTimeout(function() {
                    enhanceCardView();
                }, 100);
            },
        onToggle: function (cardView) {
            if (cardView) {
                setTimeout(function() {
                    enhanceCardView();
                }, 100);
            }
        }
    });
}

// 增强卡片视图的显示效果
function enhanceCardView() {
    // 首先确保我们处于卡片视图模式
    if ($('.bootstrap-table .card-view').length === 0) {
        return;
    }
    
    // 检查是否已经应用了增强
    if ($('.card-view').hasClass('enhanced')) {
        return;
    }
    
    // 标记已增强
    $('.card-view').addClass('enhanced');
    
    // 包装卡片内容
    $('.bootstrap-table .card-view .card').each(function() {
        if (!$(this).find('.card-content').length) {
            $(this).wrapInner('<div class="card-content"></div>');
        }
    });
    
    // 给卡片视图中的元素添加更好的样式
    $('.bootstrap-table .card-view .card-body').each(function() {
        // 如果已处理过这个元素，则跳过
        if ($(this).hasClass('processed')) {
            return;
        }
        
        // 标记为已处理
        $(this).addClass('processed');
        
        // 获取当前字段的标题文本
        var titleText = $(this).find('.title').text().trim();
        
        // 根据标题添加特殊类
        if (titleText === 'ID') {
            $(this).addClass('id-field');
        } else if (titleText === '微信状态') {
            $(this).addClass('state-field');
        } else if (titleText === '活码名称') {
            $(this).addClass('name-field');
        } else if (titleText === '创建时间' || titleText === '更新时间') {
            $(this).addClass('date-field');
        } else if (titleText === '访问量' || titleText === '访问IP数') {
            $(this).addClass('visits-field');
        } else if (titleText === '更多操作') {
            $(this).addClass('actions-field');
        }
    });
}

// 筛选列表
function filterList() {
    $('#qrListTable').bootstrapTable('refresh');
}

// 重置筛选
function resetFilter() {
    $("#filterForm")[0].reset();
    $('#qrListTable').bootstrapTable('refresh');
}

// 编辑活码
function editQR(id) {
    window.location.href = 'qr-edit.php?id=' + id;
}

// 分享二维码
function shareQR(id) {
    var shareQRModal = new bootstrap.Modal(document.getElementById('shareQRModal'));
    shareQRModal.show();
    $('#shareQRModalContent').html('<div class="text-center p-3"><div class="spinner-border text-primary" role="status"><span class="sr-only">加载中...</span></div></div>');
    
    $.ajax({
        type: 'GET',
        url: 'ajax.php?act=get_qr_info',
        data: {id: id},
        dataType: 'json',
        success: function(res) {
            if (res.code == 0) {
                var qrName = res.data.name || '未命名活码';
                var entryDomain = res.data.entry_domain || '';
                var qrCode = res.data.code || '';
                
                // 生成分享URL
                var qrUrl = '';
                if (qrCode && qrCode.trim() !== '') {
                    qrUrl = (entryDomain.indexOf('http') === 0 ? entryDomain : 'http://' + entryDomain) + '/h.' + qrCode;
                } else {
                    qrUrl = (entryDomain.indexOf('http') === 0 ? entryDomain : 'http://' + entryDomain) + '/qr/' + id;
                }
                
                var qrImageSrc = 'qrcode/' + id + '.png';
                if (res.data.qr_url) {
                    qrImageSrc = res.data.qr_url;
                }
                
                // 构建分享内容HTML
                var shareHtml = '<div class="share-qr-container">' +
                                '<div class="qr-image-wrapper">' +
                                    '<img src="' + qrImageSrc + '" class="img-responsive qr-large-image" id="shareQrImage">' +
                                '</div>' +
                                '<div class="qr-info">' +
                                    '<div class="form-group">' +
                                        '<label>访问链接:</label>' +
                                        '<div class="input-group">' +
                                            '<input type="text" class="form-control" id="qrShareUrl" value="' + qrUrl + '" readonly>' +
                                            '<span class="input-group-btn">' +
                                                '<button class="btn btn-primary" id="copyUrlBtn" data-clipboard-target="#qrShareUrl">' +
                                                    '<i class="bi bi-clipboard"></i> 复制' +
                                                '</button>' +
                                            '</span>' +
                                        '</div>' +
                                    '</div>' +
                                    
                                    '<!-- 短网址生成功能 -->' +
                                    '<div class="short-url-generator mt-3">' +
                                        '<div class="card">' +
                                            '<div class="card-header bg-light">' +
                                                '<i class="bi bi-link-45deg"></i> 生成短网址' +
                                            '</div>' +
                                            '<div class="card-body">' +
                                                '<div class="row mb-2">' +
                                                    '<div class="col-md-6">' +
                                                        '<label>选择接口:</label>' +
                                                        '<select class="form-select form-select-sm" id="dwz-type-' + id + '">' +
                                                            '<?php echo dwzList(); ?>' +
                                                        '</select>' +
                                                    '</div>' +
                                                    '<div class="col-md-6">' +
                                                        '<label>选择模式:</label>' +
                                                        '<select class="form-select form-select-sm" id="pattern-' + id + '">' +
                                                            '<?php echo pattern_list(); ?>' +
                                                        '</select>' +
                                                    '</div>' +
                                                '</div>' +
                                                '<div class="text-center mt-2">' +
                                                    '<button type="button" id="generateShortUrl-' + id + '" class="btn btn-success btn-sm" onclick="generateShortUrl(' + id + ', \'' + qrUrl + '\')">' +
                                                        '<i class="bi bi-lightning-charge"></i> 生成短网址' +
                                                    '</button>' +
                                                '</div>' +
                                                '<div id="shortUrlResult-' + id + '" class="mt-2" style="display:none;">' +
                                                    '<div class="alert alert-success">' +
                                                        '<div class="input-group input-group-sm">' +
                                                            '<input type="text" class="form-control" id="shortUrl-' + id + '" readonly>' +
                                                            '<button class="btn btn-outline-secondary copy-short-url-btn" type="button" data-clipboard-target="#shortUrl-' + id + '">' +
                                                                '<i class="bi bi-clipboard"></i>' +
                                                            '</button>' +
                                                        '</div>' +
                                                    '</div>' +
                                                '</div>' +
                                            '</div>' +
                                        '</div>' +
                                    '</div>' +
                                    
                                    '<div class="qr-extra-links mt-3">' +
                                        '<button class="btn btn-info btn-block" onclick="downloadQR(\'#shareQrImage\', \'活码_' + id + '\')"><i class="bi bi-download"></i> 下载二维码</button>' +
                                    '</div>' +
                                '</div>' +
                            '</div>';
                
                $('#shareQRModalContent').html(shareHtml);
                $('#shareQRModal .modal-title').html('<i class="bi bi-share"></i> 分享活码 - ' + qrName);
                
                // 初始化所有剪贴板功能
                if (typeof ClipboardJS !== 'undefined') {
                    // 原始链接复制按钮
                    var urlClipboard = new ClipboardJS('#copyUrlBtn');
                    urlClipboard.on('success', function(e) {
                        if (typeof layer !== 'undefined') {
                            layer.msg('链接已复制到剪贴板', {icon: 1});
                        } else if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                toast: true,
                                position: 'top-end',
                                icon: 'success',
                                title: '链接已复制到剪贴板',
                                showConfirmButton: false,
                                timer: 1500
                            });
                        } else {
                            alert('链接已复制到剪贴板');
                        }
                    });
                    
                    urlClipboard.on('error', function(e) {
                        if (typeof layer !== 'undefined') {
                            layer.msg('复制失败，请手动复制', {icon: 2});
                        } else if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                toast: true,
                                position: 'top-end',
                                icon: 'error',
                                title: '复制失败，请手动复制',
                                showConfirmButton: false,
                                timer: 1500
                            });
                        } else {
                            alert('复制失败，请手动复制');
                        }
                    });
                    
                    // 短网址复制按钮 - 预先绑定，虽然还没有生成短网址
                    var shortUrlClipboard = new ClipboardJS('.copy-short-url-btn');
                    shortUrlClipboard.on('success', function(e) {
                        if (typeof layer !== 'undefined') {
                            layer.msg('短网址已复制到剪贴板', {icon: 1});
                        } else if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                toast: true,
                                position: 'top-end',
                                icon: 'success',
                                title: '短网址已复制到剪贴板',
                                showConfirmButton: false,
                                timer: 1500
                            });
                        } else {
                            alert('短网址已复制到剪贴板');
                        }
                    });
                    
                    shortUrlClipboard.on('error', function(e) {
                        if (typeof layer !== 'undefined') {
                            layer.msg('复制失败，请手动复制', {icon: 2});
                        } else if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                toast: true,
                                position: 'top-end',
                                icon: 'error',
                                title: '复制失败，请手动复制',
                                showConfirmButton: false,
                                timer: 1500
                            });
                        } else {
                            alert('复制失败，请手动复制');
                        }
                    });
                } else {
                    console.warn('ClipboardJS未加载，将使用备用复制方法');
                }
            } else {
                $('#shareQRModalContent').html('<div class="alert alert-danger">获取活码信息失败：' + res.msg + '</div>');
            }
        },
        error: function() {
            $('#shareQRModalContent').html('<div class="alert alert-danger">服务器错误，请稍后重试</div>');
        }
    });
}

// 下载二维码图片
function downloadQR(imgSelector, filename) {
    var imgSrc = $(imgSelector).attr('src');
    if (!imgSrc) return;
    
    // 创建Canvas并绘制图像
    var canvas = document.createElement('canvas');
    var ctx = canvas.getContext('2d');
    var img = new Image();
    
    img.onload = function() {
        // 设置Canvas尺寸与图片一致
        canvas.width = img.width;
        canvas.height = img.height;
        
        // 绘制图像
        ctx.drawImage(img, 0, 0);
        
        // 将Canvas转换为DataURL并下载
        var dataURL = canvas.toDataURL('image/png');
        var a = document.createElement('a');
        a.href = dataURL;
        a.download = filename + '.png';
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
    };
    
    // 加载图片
    img.src = imgSrc;
    img.crossOrigin = 'Anonymous'; // 处理跨域问题
}

// 查看统计
function viewStats(id) {
    var statsModal = new bootstrap.Modal(document.getElementById('statsModal'));
    statsModal.show();
    $('#statsModalContent').html('<div class="text-center p-3"><div class="spinner-border text-primary" role="status"><span class="sr-only">加载中...</span></div></div>');
    
    $.ajax({
        type: 'GET',
        url: 'ajax.php?act=get_qr_info',
        data: {id: id},
        dataType: 'json',
        success: function(res) {
            if (res.code == 0) {
                var qrName = res.data.name || '未命名活码';
                $('#statsModal .modal-title').html('<i class="bi bi-bar-chart-fill"></i> 活码统计 - ' + qrName);
                
                // 构建统计过滤器
                var today = new Date().toISOString().split('T')[0];
                var weekAgo = new Date(Date.now() - 7 * 24 * 60 * 60 * 1000).toISOString().split('T')[0];
                
                var statsHtml = '<div class="stat-container">' +
                                '<div class="row">' +
                                    '<div class="col-md-12">' +
                                        '<div class="panel panel-default">' +
                                            '<div class="panel-body" style="padding: 10px;">' +
                                                '<form class="form-inline" id="statFilterForm">' +
                                                    '<input type="hidden" name="id" value="' + id + '">' +
                                                    '<div class="form-group mr-3">' +
                                                        '<label>统计类型：</label>' +
                                                        '<select class="form-control input-sm" name="type" id="statType">' +
                                                            '<option value="day">按天统计</option>' +
                                                            '<option value="month">按月统计</option>' +
                                                            '<option value="year">按年统计</option>' +
                                                        '</select>' +
                                                    '</div>' +
                                                    '<div class="form-group mr-3">' +
                                                        '<label>时间范围：</label>' +
                                                        '<div class="input-daterange input-group">' +
                                                            '<input type="date" class="form-control input-sm" name="start_date" id="popStartDate" value="' + weekAgo + '">' +
                                                            '<span class="input-group-addon">至</span>' +
                                                            '<input type="date" class="form-control input-sm" name="end_date" id="popEndDate" value="' + today + '">' +
                                                        '</div>' +
                                                    '</div>' +
                                                    '<button type="button" class="btn btn-primary btn-sm" onclick="loadStats(' + id + ')">' +
                                                        '<i class="bi bi-search"></i> 查询' +
                                                    '</button>' +
                                                '</form>' +
                                            '</div>' +
                                        '</div>' +
                                    '</div>' +
                                '</div>' +
                                
                                '<!-- 数据概览 -->' +
                                '<div class="row">' +
                                    '<div class="col-md-3">' +
                                        '<div class="mini-stat clearfix">' +
                                            '<span class="mini-stat-icon bg-info"><i class="bi bi-eye"></i></span>' +
                                            '<div class="mini-stat-info">' +
                                                '<span id="popTotalViews">' + (res.data.views || '0') + '</span>' +
                                                '总访问量' +
                                            '</div>' +
                                        '</div>' +
                                    '</div>' +
                                    '<div class="col-md-3">' +
                                        '<div class="mini-stat clearfix">' +
                                            '<span class="mini-stat-icon bg-success"><i class="bi bi-people"></i></span>' +
                                            '<div class="mini-stat-info">' +
                                                '<span id="popTotalIp">' + (res.data.ip_count || '0') + '</span>' +
                                                '访问IP数' +
                                            '</div>' +
                                        '</div>' +
                                    '</div>' +
                                    '<div class="col-md-3">' +
                                        '<div class="mini-stat clearfix">' +
                                            '<span class="mini-stat-icon bg-warning"><i class="bi bi-clock"></i></span>' +
                                            '<div class="mini-stat-info">' +
                                                '<span id="popTodayViews">-</span>' +
                                                '今日访问量' +
                                            '</div>' +
                                        '</div>' +
                                    '</div>' +
                                    '<div class="col-md-3">' +
                                        '<div class="mini-stat clearfix">' +
                                            '<span class="mini-stat-icon bg-danger"><i class="bi bi-calendar"></i></span>' +
                                            '<div class="mini-stat-info">' +
                                                '<span id="popYesterdayViews">-</span>' +
                                                '昨日访问量' +
                                            '</div>' +
                                        '</div>' +
                                    '</div>' +
                                '</div>' +
                                
                                '<!-- 统计图表 -->' +
                                '<div class="row mt-3">' +
                                    '<div class="col-md-12">' +
                                        '<div class="panel panel-default">' +
                                            '<div class="panel-heading">' +
                                                '<h3 class="panel-title">访问趋势</h3>' +
                                            '</div>' +
                                            '<div class="panel-body">' +
                                                '<div id="popVisitChart" style="height: 350px;"></div>' +
                                            '</div>' +
                                        '</div>' +
                                    '</div>' +
                                '</div>' +
                                
                                '<!-- 最近访问记录 -->' +
                                '<div class="row mt-3">' +
                                    '<div class="col-md-12">' +
                                        '<div class="panel panel-default">' +
                                            '<div class="panel-heading">' +
                                                '<h3 class="panel-title">最近访问记录</h3>' +
                                            '</div>' +
                                            '<div class="panel-body">' +
                                                '<div class="table-responsive">' +
                                                    '<table class="table table-striped table-bordered table-hover">' +
                                                        '<thead>' +
                                                            '<tr>' +
                                                                '<th width="60px">序号</th>' +
                                                                '<th>访问IP</th>' +
                                                                '<th>所在地区</th>' +
                                                                '<th>设备类型</th>' +
                                                                '<th>浏览器</th>' +
                                                                '<th>来源网址</th>' +
                                                                '<th width="160px">访问时间</th>' +
                                                            '</tr>' +
                                                        '</thead>' +
                                                        '<tbody id="popVisitLogBody">' +
                                                            '<tr>' +
                                                                '<td colspan="7" class="text-center">加载中...</td>' +
                                                            '</tr>' +
                                                        '</tbody>' +
                                                    '</table>' +
                                                '</div>' +
                                            '</div>' +
                                        '</div>' +
                                    '</div>' +
                                '</div>' +
                            '</div>';
                
                $('#statsModalContent').html(statsHtml);
                
                // 初始化图表
                loadStats(id);
                
                // 加载访问记录
                loadVisitLogs(id);
            } else {
                $('#statsModalContent').html('<div class="alert alert-danger">获取活码信息失败：' + res.msg + '</div>');
            }
        },
        error: function() {
            $('#statsModalContent').html('<div class="alert alert-danger">服务器错误，请稍后重试</div>');
        }
    });
}

// 加载统计数据
function loadStats(id) {
    var type = $('#statType').val();
    var startDate = $('#popStartDate').val();
    var endDate = $('#popEndDate').val();
    
    $.ajax({
        type: 'GET',
        url: 'ajax.php?act=get_qr_stats',
        data: {
            id: id,
            type: type,
            start_date: startDate,
            end_date: endDate
        },
        dataType: 'json',
        success: function(res) {
            if (res.code == 0) {
                updateCharts(res.data);
                updateTodayStats(id);
            } else {
                layer.msg(res.msg || '加载统计数据失败', {icon: 2});
            }
        },
        error: function() {
            layer.msg('服务器错误', {icon: 2});
        }
    });
}

// 更新图表数据
function updateCharts(data) {
    var stats = data.stats || [];
    
    // 访问趋势图表数据
    var dates = [];
    var viewsData = [];
    var ipData = [];
    
    // 处理数据
    for (var i = 0; i < stats.length; i++) {
        dates.push(stats[i].date);
        viewsData.push(stats[i].views);
        ipData.push(stats[i].unique_ips);
    }
    
    // 初始化图表
    if (typeof echarts !== 'undefined') {
        var popVisitChart = echarts.init(document.getElementById('popVisitChart'));
        
        popVisitChart.setOption({
            title: {
                text: '访问量统计',
                left: 'center'
            },
            tooltip: {
                trigger: 'axis'
            },
            legend: {
                data: ['访问量', '独立IP'],
                top: 'bottom'
            },
            grid: {
                left: '3%',
                right: '4%',
                bottom: '10%',
                top: '10%',
                containLabel: true
            },
            xAxis: {
                type: 'category',
                boundaryGap: false,
                data: dates
            },
            yAxis: {
                type: 'value'
            },
            series: [
                {
                    name: '访问量',
                    type: 'line',
                    data: viewsData,
                    smooth: true,
                    lineStyle: {
                        width: 3,
                        color: '#5bc0de'
                    },
                    areaStyle: {
                        color: {
                            type: 'linear',
                            x: 0, y: 0, x2: 0, y2: 1,
                            colorStops: [
                                { offset: 0, color: 'rgba(91,192,222,0.5)' },
                                { offset: 1, color: 'rgba(91,192,222,0.1)' }
                            ]
                        }
                    }
                },
                {
                    name: '独立IP',
                    type: 'line',
                    data: ipData,
                    smooth: true,
                    lineStyle: {
                        width: 3,
                        color: '#5cb85c'
                    },
                    areaStyle: {
                        color: {
                            type: 'linear',
                            x: 0, y: 0, x2: 0, y2: 1,
                            colorStops: [
                                { offset: 0, color: 'rgba(92,184,92,0.5)' },
                                { offset: 1, color: 'rgba(92,184,92,0.1)' }
                            ]
                        }
                    }
                }
            ]
        });
        
        // 窗口大小变化时重绘图表
        $(window).on('resize', function() {
            if (popVisitChart) popVisitChart.resize();
        });
    }
}

// 获取今日和昨日统计
function updateTodayStats(id) {
    var today = new Date().toISOString().split('T')[0];
    var yesterday = new Date(Date.now() - 24 * 60 * 60 * 1000).toISOString().split('T')[0];
    
    $.ajax({
        type: 'GET',
        url: 'ajax.php?act=get_qr_stats',
        data: {
            id: id,
            type: 'day',
            start_date: yesterday,
            end_date: today
        },
        dataType: 'json',
        success: function(res) {
            if (res.code == 0) {
                var stats = res.data.stats || [];
                var todayViews = 0;
                var yesterdayViews = 0;
                
                // 查找今日和昨日数据
                for (var i = 0; i < stats.length; i++) {
                    if (stats[i].date === today) {
                        todayViews = stats[i].views;
                    } else if (stats[i].date === yesterday) {
                        yesterdayViews = stats[i].views;
                    }
                }
                
                // 更新显示
                $('#popTodayViews').text(todayViews);
                $('#popYesterdayViews').text(yesterdayViews);
            }
        }
    });
}

// 加载访问日志
function loadVisitLogs(id) {
    $.ajax({
        type: 'GET',
        url: 'ajax.php?act=get_qr_visit_logs',
        data: {
            id: id,
            limit: 20
        },
        dataType: 'json',
        success: function(res) {
            if (res.code == 0) {
                var logs = res.data || [];
                var html = '';
                
                if (logs.length > 0) {
                    for (var i = 0; i < logs.length; i++) {
                        var log = logs[i];
                        html += '<tr>';
                        html += '<td>' + (i + 1) + '</td>';
                        html += '<td>' + (log.ip || '-') + '</td>';
                        html += '<td>' + (log.address || '-') + '</td>';
                        html += '<td>' + (log.device || '-') + '</td>';
                        html += '<td>' + (log.browser || '-') + '</td>';
                        html += '<td>' + (log.referer || '-') + '</td>';
                        html += '<td>' + log.addtime + '</td>';
                        html += '</tr>';
                    }
                } else {
                    html = '<tr><td colspan="7" class="text-center">暂无访问记录</td></tr>';
                }
                
                $('#popVisitLogBody').html(html);
            } else {
                $('#popVisitLogBody').html('<tr><td colspan="7" class="text-center">加载失败</td></tr>');
            }
        },
        error: function() {
            $('#popVisitLogBody').html('<tr><td colspan="7" class="text-center">服务器错误</td></tr>');
        }
    });
}

// 删除活码
function deleteQR(id) {
    // 检查layer是否可用
    if (typeof layer === 'undefined') {
        console.error('Layer库未加载，尝试使用SweetAlert2');
        
        // 检查SweetAlert2是否可用
        if (typeof Swal !== 'undefined') {
            // 使用SweetAlert2
            Swal.fire({
                title: '确认删除',
                text: '确定要删除该活码吗？删除后将无法恢复！',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#E74C3C',
                cancelButtonColor: '#3085d6',
                confirmButtonText: '确定删除',
                cancelButtonText: '取消'
            }).then((result) => {
                if (result.isConfirmed) {
                    performDelete(id);
                }
            });
        } else {
            // 使用原生confirm
            if (confirm('确定要删除该活码吗？删除后将无法恢复！')) {
                performDelete(id);
            }
        }
        return;
    }
    
    layer.confirm('确定要删除该活码吗？删除后将无法恢复！', {
        btn: ['确定','取消'],
        title: '确认删除'
    }, function(){
        performDelete(id);
    });
    
    // 执行实际删除操作
    function performDelete(id) {
        $.ajax({
            type: 'POST',
            url: 'ajax.php?act=delete_qr',
            data: {id: id},
            dataType: 'json',
            success: function(res) {
                if (res.code == 0) {
                    // 删除成功
                    if (typeof layer !== 'undefined') {
                        layer.msg('删除成功', {icon: 1});
                    } else if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            title: '删除成功',
                            icon: 'success',
                            timer: 1500,
                            showConfirmButton: false
                        });
                    } else {
                        alert('删除成功');
                    }
                    $('#qrListTable').bootstrapTable('refresh'); // 刷新表格
                } else {
                    // 删除失败
                    if (typeof layer !== 'undefined') {
                        layer.msg(res.msg, {icon: 2});
                    } else if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            title: '删除失败',
                            text: res.msg,
                            icon: 'error'
                        });
                    } else {
                        alert('删除失败：' + res.msg);
                    }
                }
            },
            error: function() {
                // 网络错误
                if (typeof layer !== 'undefined') {
                    layer.msg('操作失败', {icon: 2});
                } else if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        title: '操作失败',
                        text: '网络连接异常',
                        icon: 'error'
                    });
                } else {
                    alert('操作失败，网络连接异常');
                }
            }
        });
    }
}

// 批量删除
function batchDelete() {
    // 获取所有选中行的数据
    var selections = $('#qrListTable').bootstrapTable('getSelections');
    
    // 检查是否有选中的行
    if (!selections || selections.length === 0) {
        // 没有选中任何项
        if (typeof layer !== 'undefined') {
            layer.msg('请至少选择一项进行删除', {icon: 2});
        } else if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: '提示',
                text: '请至少选择一项进行删除',
                icon: 'warning'
            });
        } else {
            alert('请至少选择一项进行删除');
        }
        return;
    }
    
    // 提取所有选中行的ID
    var ids = [];
    for (var i = 0; i < selections.length; i++) {
        ids.push(selections[i].id);
    }
    
    console.log("已选中的ID: ", ids); // 调试信息
    
    // 检查layer是否可用
    if (typeof layer === 'undefined') {
        console.error('Layer库未加载，尝试使用SweetAlert2');
        
        // 检查SweetAlert2是否可用
        if (typeof Swal !== 'undefined') {
            // 使用SweetAlert2
            Swal.fire({
                title: '批量删除',
                text: '确定要删除选中的 ' + ids.length + ' 个活码吗？',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#E74C3C',
                cancelButtonColor: '#3085d6',
                confirmButtonText: '确定删除',
                cancelButtonText: '取消'
            }).then((result) => {
                if (result.isConfirmed) {
                    performBatchDelete(ids);
                }
            });
        } else {
            // 使用原生confirm
            if (confirm('确定要删除选中的 ' + ids.length + ' 个活码吗？')) {
                performBatchDelete(ids);
            }
        }
        return;
    }
    
    layer.confirm('确定要删除选中的 ' + ids.length + ' 个活码吗？', {
        btn: ['确定','取消'],
        title: '批量删除'
    }, function(){
        performBatchDelete(ids);
    });
    
    // 执行实际批量删除操作
    function performBatchDelete(ids) {
        // 调试日志
        console.log("执行批量删除，IDs:", ids);
        console.log("发送的数据:", {ids: ids.join(',')});
        
        if (ids.length === 0) {
            console.error("没有选中任何ID，不执行删除操作");
            return;
        }
        
        // 准备表单数据
        var formData = new FormData();
        formData.append('ids', ids.join(','));
        
        // 创建XHR对象监控完整请求过程
        var xhr = new XMLHttpRequest();
        xhr.open('POST', 'ajax.php?act=batch_delete_qr', true);
        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4) {
                console.log("XHR响应状态:", xhr.status);
                console.log("XHR响应头:", xhr.getAllResponseHeaders());
                console.log("XHR原始响应:", xhr.responseText);
                
                if (xhr.status === 200) {
                    try {
                        var res = JSON.parse(xhr.responseText);
                        console.log("批量删除解析后响应:", res);
                        
                        if (res.code == 0) {
                            // 删除成功
                            if (typeof layer !== 'undefined') {
                                layer.msg('批量删除成功', {icon: 1});
                            } else if (typeof Swal !== 'undefined') {
                                Swal.fire({
                                    title: '批量删除成功',
                                    icon: 'success',
                                    timer: 1500,
                                    showConfirmButton: false
                                });
                            } else {
                                alert('批量删除成功');
                            }
                            $('#qrListTable').bootstrapTable('refresh'); // 刷新表格
                        } else {
                            // 删除失败
                            if (typeof layer !== 'undefined') {
                                layer.msg(res.msg || '删除失败', {icon: 2});
                            } else if (typeof Swal !== 'undefined') {
                                Swal.fire({
                                    title: '批量删除失败',
                                    text: res.msg || '删除失败',
                                    icon: 'error'
                                });
                            } else {
                                alert('批量删除失败：' + (res.msg || '未知错误'));
                            }
                        }
                    } catch (e) {
                        console.error("解析响应JSON失败:", e);
                        
                        if (typeof layer !== 'undefined') {
                            layer.msg('响应解析失败', {icon: 2});
                        } else if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                title: '操作失败',
                                text: '响应解析失败',
                                icon: 'error'
                            });
                        } else {
                            alert('操作失败，响应解析失败');
                        }
                    }
                } else {
                    console.error("HTTP错误:", xhr.status);
                    
                    if (typeof layer !== 'undefined') {
                        layer.msg('网络错误: ' + xhr.status, {icon: 2});
                    } else if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            title: '操作失败',
                            text: '网络错误: ' + xhr.status,
                            icon: 'error'
                        });
                    } else {
                        alert('操作失败，网络错误: ' + xhr.status);
                    }
                }
            }
        };
        xhr.send(formData);
        
        // 保留原有的jQuery AJAX请求作为备份
        /*
        $.ajax({
            type: 'POST',
            url: 'ajax.php?act=batch_delete_qr',
            data: {ids: ids.join(',')},
            dataType: 'json',
            success: function(res) {
                console.log("批量删除响应:", res); // 调试信息
                
                if (res.code == 0) {
                    // 删除成功
                    if (typeof layer !== 'undefined') {
                        layer.msg('批量删除成功', {icon: 1});
                    } else if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            title: '批量删除成功',
                            icon: 'success',
                            timer: 1500,
                            showConfirmButton: false
                        });
                    } else {
                        alert('批量删除成功');
                    }
                    $('#qrListTable').bootstrapTable('refresh'); // 刷新表格
                } else {
                    // 删除失败
                    if (typeof layer !== 'undefined') {
                        layer.msg(res.msg, {icon: 2});
                    } else if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            title: '批量删除失败',
                            text: res.msg,
                            icon: 'error'
                        });
                    } else {
                        alert('批量删除失败：' + res.msg);
                    }
                }
            },
            error: function(xhr, status, error) {
                // 网络错误
                console.error("AJAX错误:", status, error); // 调试信息
                console.log("响应文本:", xhr.responseText); // 调试信息
                
                if (typeof layer !== 'undefined') {
                    layer.msg('操作失败', {icon: 2});
                } else if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        title: '操作失败',
                        text: '网络连接异常',
                        icon: 'error'
                    });
                } else {
                    alert('操作失败，网络连接异常');
                }
            }
        });
        */
    }
}

// 域名检测功能 - 同时检测入口域名和落地域名
function checkDomains(id, entryDomain, landingDomain) {
    // 检查layer是否可用，如果不可用则使用SweetAlert2或alert作为后备方案
    if (typeof layer === 'undefined') {
        console.error('Layer库未加载，尝试使用SweetAlert2');
        
        // 检查SweetAlert2是否可用
        if (typeof Swal !== 'undefined') {
            // 使用SweetAlert2
            checkDomainsWithSwal(id, entryDomain, landingDomain);
        } else {
            // 使用原生alert
            alert('系统提示：layer组件未加载，无法显示美化对话框。');
            checkDomainsWithAlert(id, entryDomain, landingDomain);
        }
        return;
    }
    
    // 检查是否有可检测的域名
    if ((!entryDomain || entryDomain.trim() === '' || entryDomain === '-') && 
        (!landingDomain || landingDomain.trim() === '' || landingDomain === '-')) {
        layer.msg('该活码未设置域名，无法检测', {icon: 2});
        return;
    }
    
    // 处理域名格式
    var domains = [];
    if (entryDomain && entryDomain.trim() !== '' && entryDomain !== '-') {
        domains.push({
            type: '入口域名',
            domain: entryDomain.replace(/^https?:\/\//, '').split('/')[0]
        });
    }
    
    if (landingDomain && landingDomain.trim() !== '' && landingDomain !== '-') {
        domains.push({
            type: '落地域名',
            domain: landingDomain.replace(/^https?:\/\//, '').split('/')[0]
        });
    }
    
    // 构建确认信息
    var confirmMsg = '确定要检测以下域名吗？<br>';
    domains.forEach(function(item) {
        confirmMsg += '<br><b>' + item.type + ':</b> ' + item.domain;
    });
    
    // 确认是否检测
    layer.confirm(confirmMsg, {
        btn: ['确定','取消'],
        title: '域名检测',
        icon: 3
    }, function(){
        // 显示加载中
        var index = layer.load(1, {
            shade: [0.5,'#fff']
        });
        
        // 提取纯域名列表，用于API请求
        var domainList = domains.map(function(item) {
            return item.domain;
        }).join('\n');
        
        // 发送AJAX请求
        $.ajax({
            url: 'ajax.php?act=check_domain',
            type: 'POST',
            data: {
                domains: domainList
            },
            dataType: 'json',
            success: function(res) {
                layer.close(index);
                
                if (res.code === 0) {
                    // 检测成功，分析结果并显示
                    showDomainsCheckResult(domains, res);
                } else {
                    // 检测失败
                    layer.msg('检测失败：' + res.msg, {icon: 2});
                }
            },
            error: function() {
                layer.close(index);
                layer.msg('网络错误，请稍后再试', {icon: 2});
            }
        });
    });
}

// SweetAlert2版本的域名检测功能（多域名）
function checkDomainsWithSwal(id, entryDomain, landingDomain) {
    // 检查是否有可检测的域名
    if ((!entryDomain || entryDomain.trim() === '' || entryDomain === '-') && 
        (!landingDomain || landingDomain.trim() === '' || landingDomain === '-')) {
        Swal.fire({
            title: '提示',
            text: '该活码未设置域名，无法检测',
            icon: 'error'
        });
        return;
    }
    
    // 处理域名格式
    var domains = [];
    if (entryDomain && entryDomain.trim() !== '' && entryDomain !== '-') {
        domains.push({
            type: '入口域名',
            domain: entryDomain.replace(/^https?:\/\//, '').split('/')[0]
        });
    }
    
    if (landingDomain && landingDomain.trim() !== '' && landingDomain !== '-') {
        domains.push({
            type: '落地域名',
            domain: landingDomain.replace(/^https?:\/\//, '').split('/')[0]
        });
    }
    
    // 构建确认信息
    var confirmMsg = '确定要检测以下域名吗？<br>';
    domains.forEach(function(item) {
        confirmMsg += '<br><b>' + item.type + ':</b> ' + item.domain;
    });
    
    // 确认是否检测
    Swal.fire({
        title: '域名检测',
        html: confirmMsg,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: '确定',
        cancelButtonText: '取消'
    }).then((result) => {
        if (result.isConfirmed) {
            // 显示加载中
            Swal.fire({
                title: '检测中',
                text: '正在检测域名，请稍候...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            // 提取纯域名列表，用于API请求
            var domainList = domains.map(function(item) {
                return item.domain;
            }).join('\n');
            
            // 发送AJAX请求
            $.ajax({
                url: 'ajax.php?act=check_domain',
                type: 'POST',
                data: {
                    domains: domainList
                },
                dataType: 'json',
                success: function(res) {
                    if (res.code === 0) {
                        // 检测成功，分析结果并显示
                        showDomainsCheckResultSwal(domains, res);
                    } else {
                        // 检测失败
                        Swal.fire({
                            title: '检测失败',
                            text: res.msg,
                            icon: 'error'
                        });
                    }
                },
                error: function() {
                    Swal.fire({
                        title: '网络错误',
                        text: '请稍后再试',
                        icon: 'error'
                    });
                }
            });
        }
    });
}

// 使用SweetAlert2显示多域名检测结果
function showDomainsCheckResultSwal(domains, res) {
    if (!res.results || res.results.length === 0) {
        Swal.fire({
            title: '未找到结果',
            text: '未找到域名检测结果',
            icon: 'error'
        });
        return;
    }
    
    // 构建结果表格
    var tableHtml = '<table class="table table-bordered table-striped">' +
                    '<thead><tr>' +
                    '<th>域名类型</th>' +
                    '<th>域名</th>' +
                    '<th>状态</th>' +
                    '<th>详情</th>' +
                    '</tr></thead><tbody>';
                    
    // 处理每个域名的结果
    domains.forEach(function(domainObj) {
        var domainResult = null;
        
        // 查找对应的检测结果
        for (var i = 0; i < res.results.length; i++) {
            if (res.results[i].domain === domainObj.domain) {
                domainResult = res.results[i];
                break;
            }
        }
        
        if (!domainResult) {
            // 没有找到结果
            tableHtml += '<tr>' +
                        '<td>' + domainObj.type + '</td>' +
                        '<td>' + domainObj.domain + '</td>' +
                        '<td><span class="text-danger">未检测</span></td>' +
                        '<td>未找到该域名的检测结果</td>' +
                        '</tr>';
        } else {
            // 处理状态
            var statusText = '';
            var statusClass = '';
            
            if (domainResult.status === 'success') {
                // 将API状态码映射到HTTP状态码
                if (domainResult.raw_response && domainResult.raw_response.status) {
                    var apiStatus = parseInt(domainResult.raw_response.status);
                    if (apiStatus === 4) {
                        domainResult.status_code = 403;
                    }
                }
                
                // 根据状态码设置样式和描述
                switch(Number(domainResult.status_code)) {
                    case 200:
                        statusText = '正常访问';
                        statusClass = 'text-success';
                        break;
                    case 401:
                    case 402:
                        statusText = '异常访问';
                        statusClass = 'text-warning';
                        break;
                    case 403:
                        statusText = '访问受限';
                        statusClass = 'text-success';
                        break;
                    case 404:
                        statusText = '不存在';
                        statusClass = 'text-danger';
                        break;
                    default:
                        statusText = '未知状态';
                        statusClass = 'text-info';
                }
            } else {
                statusText = '检测失败';
                statusClass = 'text-danger';
            }
            
            tableHtml += '<tr>' +
                        '<td>' + domainObj.type + '</td>' +
                        '<td>' + domainObj.domain + '</td>' +
                        '<td><span class="' + statusClass + '">' + statusText + '</span></td>' +
                        '<td>' + (domainResult.message || '') + '</td>' +
                        '</tr>';
        }
    });
    
    tableHtml += '</tbody></table>';
    
    // 添加注释和检测时间
    tableHtml += '<div class="text-muted small mt-2">' +
                '<p>检测时间: ' + getCurrentTime() + '</p>' +
                '<p>注：域名检测结果仅供参考，建议定期检测确保正常访问</p>' +
                '</div>';
    
    // 显示结果
    Swal.fire({
        title: '域名检测结果',
        icon: 'info',
        html: tableHtml,
        width: '700px',
        confirmButtonText: '确定'
    });
}

// 显示多域名检测结果
function showDomainsCheckResult(domains, res) {
    if (!res.results || res.results.length === 0) {
        layer.msg('未找到域名检测结果', {icon: 2});
        return;
    }
    
    // 构建结果表格
    var tableHtml = '<table class="table table-bordered table-striped">' +
                    '<thead><tr>' +
                    '<th>域名类型</th>' +
                    '<th>域名</th>' +
                    '<th>状态</th>' +
                    '<th>详情</th>' +
                    '</tr></thead><tbody>';
                    
    // 处理每个域名的结果
    domains.forEach(function(domainObj) {
        var domainResult = null;
        
        // 查找对应的检测结果
        for (var i = 0; i < res.results.length; i++) {
            if (res.results[i].domain === domainObj.domain) {
                domainResult = res.results[i];
                break;
            }
        }
        
        if (!domainResult) {
            // 没有找到结果
            tableHtml += '<tr>' +
                        '<td>' + domainObj.type + '</td>' +
                        '<td>' + domainObj.domain + '</td>' +
                        '<td><span class="text-danger">未检测</span></td>' +
                        '<td>未找到该域名的检测结果</td>' +
                        '</tr>';
        } else {
            // 处理状态
            var statusText = '';
            var statusClass = '';
            
            if (domainResult.status === 'success') {
                // 将API状态码映射到HTTP状态码
                if (domainResult.raw_response && domainResult.raw_response.status) {
                    var apiStatus = parseInt(domainResult.raw_response.status);
                    if (apiStatus === 4) {
                        domainResult.status_code = 403;
                    }
                }
                
                // 根据状态码设置样式和描述
                switch(Number(domainResult.status_code)) {
                    case 200:
                        statusText = '正常访问';
                        statusClass = 'text-success';
                        break;
                    case 401:
                    case 402:
                        statusText = '异常访问';
                        statusClass = 'text-warning';
                        break;
                    case 403:
                        statusText = '访问受限';
                        statusClass = 'text-success';
                        break;
                    case 404:
                        statusText = '不存在';
                        statusClass = 'text-danger';
                        break;
                    default:
                        statusText = '未知状态';
                        statusClass = 'text-info';
                }
            } else {
                statusText = '检测失败';
                statusClass = 'text-danger';
            }
            
            tableHtml += '<tr>' +
                        '<td>' + domainObj.type + '</td>' +
                        '<td>' + domainObj.domain + '</td>' +
                        '<td><span class="' + statusClass + '">' + statusText + '</span></td>' +
                        '<td>' + (domainResult.message || '') + '</td>' +
                        '</tr>';
        }
    });
    
    tableHtml += '</tbody></table>';
    
    // 添加注释和检测时间
    tableHtml += '<div class="text-muted small mt-2">' +
                '<p>检测时间: ' + getCurrentTime() + '</p>' +
                '<p>注：域名检测结果仅供参考，建议定期检测确保正常访问</p>' +
                '</div>';
    
    // 显示结果
    layer.open({
        type: 1,
        title: '域名检测结果',
        area: ['700px', 'auto'],
        content: tableHtml,
        btn: ['确定']
    });
}

// 使用原生alert的域名检测功能
function checkDomainsWithAlert(id, entryDomain, landingDomain) {
    // 检查是否有可检测的域名
    if ((!entryDomain || entryDomain.trim() === '' || entryDomain === '-') && 
        (!landingDomain || landingDomain.trim() === '' || landingDomain === '-')) {
        alert('该活码未设置域名，无法检测');
        return;
    }
    
    // 处理域名格式
    var domains = [];
    var confirmMsg = "确定要检测以下域名吗？\n";
    
    if (entryDomain && entryDomain.trim() !== '' && entryDomain !== '-') {
        var cleanEntryDomain = entryDomain.replace(/^https?:\/\//, '').split('/')[0];
        domains.push({
            type: '入口域名',
            domain: cleanEntryDomain
        });
        confirmMsg += "\n入口域名: " + cleanEntryDomain;
    }
    
    if (landingDomain && landingDomain.trim() !== '' && landingDomain !== '-') {
        var cleanLandingDomain = landingDomain.replace(/^https?:\/\//, '').split('/')[0];
        domains.push({
            type: '落地域名',
            domain: cleanLandingDomain
        });
        confirmMsg += "\n落地域名: " + cleanLandingDomain;
    }
    
    // 确认是否检测
    if (confirm(confirmMsg)) {
        alert('正在检测域名，请稍候...');
        
        // 提取纯域名列表，用于API请求
        var domainList = domains.map(function(item) {
            return item.domain;
        }).join('\n');
        
        // 发送AJAX请求
        $.ajax({
            url: 'ajax.php?act=check_domain',
            type: 'POST',
            data: {
                domains: domainList
            },
            dataType: 'json',
            success: function(res) {
                if (res.code === 0) {
                    // 简单显示结果
                    var resultMsg = "域名检测结果:\n";
                    
                    domains.forEach(function(domainObj) {
                        var result = null;
                        for (var i = 0; i < res.results.length; i++) {
                            if (res.results[i].domain === domainObj.domain) {
                                result = res.results[i];
                                break;
                            }
                        }
                        
                        if (result) {
                            resultMsg += "\n" + domainObj.type + ": " + domainObj.domain;
                            resultMsg += "\n状态码: " + (result.status_code || '-');
                            resultMsg += "\n检测结果: " + result.message;
                            resultMsg += "\n";
                        }
                    });
                    
                    alert(resultMsg);
                } else {
                    // 检测失败
                    alert('检测失败：' + res.msg);
                }
            },
            error: function() {
                alert('网络错误，请稍后再试');
            }
        });
    }
}

// 获取当前时间格式化字符串
function getCurrentTime() {
    var now = new Date();
    var year = now.getFullYear();
    var month = (now.getMonth() + 1).toString().padStart(2, '0');
    var day = now.getDate().toString().padStart(2, '0');
    var hours = now.getHours().toString().padStart(2, '0');
    var minutes = now.getMinutes().toString().padStart(2, '0');
    var seconds = now.getSeconds().toString().padStart(2, '0');
    return year + '-' + month + '-' + day + ' ' + hours + ':' + minutes + ':' + seconds;
}

// 生成短网址函数
function generateShortUrl(id, url) {
    var $btn = $('#generateShortUrl-' + id);
    var btnHtml = $btn.html();
    
    // 保存按钮状态并禁用
    $btn.html('<i class="bi bi-arrow-repeat spin"></i> 生成中...');
    $btn.addClass("btn-warning").removeClass("btn-success");
    $btn.prop('disabled', true);
    
    var type = $("#dwz-type-" + id).val();
    var pattern = $("#pattern-" + id).val();
    
    // 将URL存入数组，模拟批量处理的单个URL
    var urls = [url];
    
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
            // 恢复按钮状态
            $btn.html(btnHtml);
            $btn.addClass("btn-success").removeClass("btn-warning");
            $btn.prop('disabled', false);
            
            if (obj.code == 0) {
                // 成功处理
                if (typeof layer !== 'undefined') {
                    layer.msg('短网址生成成功', {icon: 1});
                } else if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: '短网址生成成功',
                        showConfirmButton: false,
                        timer: 3000
                    });
                } else {
                    alert('短网址生成成功');
                }
                
                // 显示结果
                $('#shortUrl-' + id).val(obj.data[0]);
                $('#shortUrlResult-' + id).show();
                
                // 重新初始化复制功能
                if (typeof ClipboardJS !== 'undefined') {
                    // 为了安全起见，先销毁可能存在的剪贴板实例
                    $('.copy-short-url-btn').each(function() {
                        if ($(this).data('clipboard')) {
                            $(this).data('clipboard').destroy();
                        }
                    });
                    
                    // 重新创建复制按钮的剪贴板实例
                    var shortUrlClipboard = new ClipboardJS('.copy-short-url-btn');
                    shortUrlClipboard.on('success', function(e) {
                        if (typeof layer !== 'undefined') {
                            layer.msg('短网址已复制到剪贴板', {icon: 1});
                        } else if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                toast: true,
                                position: 'top-end',
                                icon: 'success',
                                title: '短网址已复制到剪贴板',
                                showConfirmButton: false,
                                timer: 1500
                            });
                        } else {
                            alert('短网址已复制到剪贴板');
                        }
                    });
                    
                    shortUrlClipboard.on('error', function(e) {
                        console.error('复制短网址失败:', e);
                        if (typeof layer !== 'undefined') {
                            layer.msg('复制失败，请手动复制', {icon: 2});
                        } else if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                toast: true,
                                position: 'top-end',
                                icon: 'error',
                                title: '复制失败，请手动复制',
                                showConfirmButton: false,
                                timer: 1500
                            });
                        } else {
                            alert('复制失败，请手动复制');
                        }
                    });
                    
                    // 存储剪贴板实例到按钮数据中
                    $('.copy-short-url-btn').data('clipboard', shortUrlClipboard);
                }
            } else {
                if (typeof layer !== 'undefined') {
                    layer.msg(obj.msg, {icon: 2});
                } else if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        title: '错误',
                        text: obj.msg,
                        icon: 'error',
                        confirmButtonText: '确定',
                        confirmButtonColor: '#28a745'
                    });
                } else {
                    alert('错误: ' + obj.msg);
                }
            }
        },
        error: function(jqXHR, textStatus, errorThrown) {
            console.error('短网址生成请求失败:', textStatus, errorThrown);
            
            // 恢复按钮状态
            $btn.html(btnHtml);
            $btn.addClass("btn-success").removeClass("btn-warning");
            $btn.prop('disabled', false);
            
            if (typeof layer !== 'undefined') {
                layer.msg('生成失败，请稍后重试', {icon: 2});
            } else if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: '错误',
                    text: '生成失败，请稍后重试',
                    icon: 'error',
                    confirmButtonText: '确定',
                    confirmButtonColor: '#28a745'
                });
            } else {
                alert('生成失败，请稍后重试');
            }
        }
    });
}

// 复制短网址函数
function copyShortUrl(id) {
    try {
        // 尝试使用ClipboardJS复制
        if (typeof ClipboardJS !== 'undefined') {
            // 创建一个临时的剪贴板实例
            var tempButton = document.createElement('button');
            tempButton.setAttribute('data-clipboard-text', $('#shortUrl-' + id).val());
            document.body.appendChild(tempButton);
            
            var clipboard = new ClipboardJS(tempButton);
            
            clipboard.on('success', function(e) {
                // 显示复制成功提示
                if (typeof layer !== 'undefined') {
                    layer.msg('短网址已复制到剪贴板', {icon: 1});
                } else if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: '短网址已复制到剪贴板',
                        showConfirmButton: false,
                        timer: 1500
                    });
                } else {
                    alert('短网址已复制到剪贴板');
                }
                
                // 清理
                clipboard.destroy();
                document.body.removeChild(tempButton);
            });
            
            clipboard.on('error', function(e) {
                fallbackCopy();
                // 清理
                clipboard.destroy();
                document.body.removeChild(tempButton);
            });
            
            // 触发点击事件
            tempButton.click();
        } else {
            fallbackCopy();
        }
    } catch (err) {
        console.error('复制失败:', err);
        fallbackCopy();
    }
    
    // 回退到传统的复制方法
    function fallbackCopy() {
        var shortUrl = $('#shortUrl-' + id).val();
        
        // 创建临时元素
        var tempInput = document.createElement('input');
        tempInput.style.position = 'fixed';
        tempInput.style.opacity = '0';
        tempInput.value = shortUrl;
        document.body.appendChild(tempInput);
        
        // 选择并复制
        tempInput.focus();
        tempInput.select();
        
        var successful = false;
        try {
            successful = document.execCommand('copy');
        } catch (err) {
            console.error('复制失败:', err);
        }
        
        // 删除临时元素
        document.body.removeChild(tempInput);
        
        // 显示复制结果提示
        if (successful) {
            if (typeof layer !== 'undefined') {
                layer.msg('短网址已复制到剪贴板', {icon: 1});
            } else if (typeof Swal !== 'undefined') {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: '短网址已复制到剪贴板',
                    showConfirmButton: false,
                    timer: 1500
                });
            } else {
                alert('短网址已复制到剪贴板');
            }
        } else {
            if (typeof layer !== 'undefined') {
                layer.msg('复制失败，请手动复制', {icon: 2});
            } else if (typeof Swal !== 'undefined') {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'error',
                    title: '复制失败，请手动复制',
                    showConfirmButton: false,
                    timer: 1500
                });
            } else {
                alert('复制失败，请手动复制');
            }
        }
    }
}
</script>

<!-- jQuery UI 1.11.4 for nicescroll -->
<script src="https://cdn.jsdelivr.net/npm/jquery-ui@1.12.1/jquery-ui.min.js"></script>
<!-- 引入ECharts图表库 -->
<script src="https://cdn.jsdelivr.net/npm/echarts@5.4.0/dist/echarts.min.js"></script>
<!-- 初始化页面脚本 -->
<script src="../static/user/js/common-scripts.js"></script>