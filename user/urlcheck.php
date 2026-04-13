<?php
include('../includes/common.php');
if ($islogin2 == 1) {
} else exit("<script language='javascript'>window.location.href='./login.php';</script>");
?>

<?php
$title = '网址监控';
include('head.php');
?>
<!-- 添加缺失的Bootstrap Table和niceScroll库 -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-table@1.18.3/dist/bootstrap-table.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap-table@1.18.3/dist/bootstrap-table.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap-table@1.18.3/dist/locale/bootstrap-table-zh-CN.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/jquery.nicescroll@3.7.6/jquery.nicescroll.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
<!-- 添加剪贴板和Bootstrap Tooltip插件 -->
<script src="https://cdn.jsdelivr.net/npm/clipboard@2.0.8/dist/clipboard.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
<!-- 添加layer弹窗组件 -->
<script src="https://cdn.jsdelivr.net/npm/layui-layer@3.5.1/dist/layer.min.js"></script>
<!-- 修复Bootstrap Table图标 -->
<style>
.bootstrap-table .fixed-table-toolbar .bs-bars,
.bootstrap-table .fixed-table-toolbar .search,
.bootstrap-table .fixed-table-toolbar .columns {
    position: relative;
    margin-top: 10px;
    margin-bottom: 10px;
}

.bootstrap-table .fixed-table-toolbar .columns .btn-group>.btn-group {
    display: inline-block;
    margin-left: -1px !important;
}

.bootstrap-table .fixed-table-toolbar .columns button.btn-default,
.bootstrap-table .fixed-table-toolbar .columns button.btn-default.dropdown-toggle {
    padding: 0.375rem 0.75rem;
    font-size: 1rem;
    line-height: 1.5;
}

/* 修复图标显示 */
.bootstrap-table .fixed-table-toolbar .columns div.dropdown-menu {
    max-height: 300px;
    overflow: auto;
    z-index: 1000;
}

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

.btn-group .btn-secondary {
    color: #333;
    background-color: #fff;
    border-color: #ccc;
}

.btn-group .btn-secondary:hover {
    color: #333;
    background-color: #e6e6e6;
    border-color: #adadad;
}

/* 操作按钮新样式 */
.action-buttons {
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
    justify-content: flex-start;
}

.action-btn {
    border: none;
    background: transparent;
    color: #666;
    width: 34px;
    height: 34px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s;
    font-size: 15px;
    cursor: pointer;
    position: relative;
    overflow: hidden;
}

.action-btn::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: currentColor;
    opacity: 0.12;
    border-radius: 8px;
    transition: opacity 0.2s;
}

.action-btn:hover::before {
    opacity: 0.18;
}

.action-btn:active::before {
    opacity: 0.25;
}

.action-btn i {
    position: relative;
    z-index: 1;
}

.action-btn-edit {
    color: #07C160;
}

.action-btn-delete {
    color: #dc3545;
}

.action-btn-tooltip {
    position: absolute;
    top: -30px;
    left: 50%;
    transform: translateX(-50%) translateY(10px);
    background: rgba(0,0,0,0.75);
    color: #fff;
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 12px;
    opacity: 0;
    pointer-events: none;
    transition: all 0.2s;
    white-space: nowrap;
    z-index: 10;
}

.action-btn:hover .action-btn-tooltip {
    opacity: 1;
    transform: translateX(-50%) translateY(0);
}

@media (max-width: 576px) {
    .action-buttons {
        justify-content: space-between;
    }
    
    .action-btn {
        width: 38px;
        height: 38px;
    }
}
</style>
<!-- 解决滚动事件被动监听器警告 -->
<script>
// 添加被动事件监听器，提高页面响应性能
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
        --danger: #dc3545;
        --warning: #ffc107;
        --success: #28a745;
    }

    body {
        background-color: #fefefe;
    }

    .card {
        border-radius: 12px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.05);
        border: none;
        margin-bottom: 30px;
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
        background-color: #f8f9fa;
        color: #495057;
        font-weight: 600;
        border-bottom: 2px solid #dee2e6;
    }

    .bootstrap-table .table tbody tr:hover {
        background-color: rgba(40, 167, 69, 0.05);
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
        box-shadow: 0 5px 15px rgba(40, 167, 69, 0.3);
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
        box-shadow: 0 5px 15px rgba(40, 167, 69, 0.3);
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

    /* 模态框样式 - 增强版 */
    .modal-content {
        border-radius: 12px;
        border: none;
        box-shadow: 0 15px 40px rgba(0,0,0,0.1);
        overflow: hidden;
        animation: modalFadeIn 0.3s ease-out;
    }

    @keyframes modalFadeIn {
        from {
            opacity: 0;
            transform: translateY(-20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .modal-backdrop.show {
        opacity: 0.7;
    }

    .modal-header {
        border-bottom: 1px solid rgba(0,0,0,0.05);
        background-color: var(--light);
        padding: 18px 24px;
        display: flex;
        align-items: center;
    }

    .modal-header .close {
        padding: 0;
        margin: 0;
        width: 30px;
        height: 30px;
        border-radius: 50%;
        background-color: rgba(0,0,0,0.05);
        opacity: 0.7;
        transition: all 0.2s;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.4rem;
        font-weight: normal;
        outline: none;
    }

    .modal-header .close:hover {
        background-color: rgba(0,0,0,0.1);
        opacity: 1;
        transform: rotate(90deg);
    }

    .modal-title {
        font-weight: 600;
        font-size: 20px;
        color: var(--dark);
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .modal-title i {
        color: var(--primary);
    }

    .modal-body {
        padding: 24px;
    }

    .modal-body form {
        margin-bottom: 0;
    }

    .modal-footer {
        border-top: 1px solid rgba(0,0,0,0.05);
        padding: 15px 24px;
        justify-content: space-between;
    }

    .modal-dialog {
        margin-top: 50px;
        max-width: 550px;
    }

    /* 表单控件增强 */
    .form-group {
        margin-bottom: 20px;
        position: relative;
    }

    .form-group label {
        display: block;
        margin-bottom: 8px;
        font-weight: 500;
        color: #495057;
        font-size: 14px;
    }

    .form-control {
        border-radius: 8px;
        border: 1px solid #ddd;
        padding: 12px 15px;
        transition: all 0.3s;
        font-size: 15px;
        background-color: #fff;
        height: auto;
        box-shadow: 0 1px 3px rgba(0,0,0,0.02);
    }

    .form-control:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(40, 167, 69, 0.2);
    }

    select.form-control {
        appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='%23495057' class='bi bi-chevron-down' viewBox='0 0 16 16'%3E%3Cpath fill-rule='evenodd' d='M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 15px center;
        background-size: 14px;
        padding-right: 40px;
    }

    .help-block {
        color: var(--secondary);
        font-size: 13px;
        margin-top: 6px;
        font-style: italic;
    }

    /* 下拉菜单 */
    .dropdown-menu {
        border-radius: 8px;
        border: none;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        padding: 8px 0;
    }

    .dropdown-item {
        padding: 8px 15px;
        display: flex;
        align-items: center;
        gap: 8px;
        transition: all 0.2s;
    }

    .dropdown-item i {
        font-size: 1em;
    }

    .dropdown-item:hover, .dropdown-item:focus {
        background-color: rgba(40, 167, 69, 0.1);
        color: var(--primary);
    }

    .dropdown-item.active, .dropdown-item:active {
        background-color: var(--primary);
        color: #fff;
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
        gap: 10px;
    }

    /* 帮助文本 */
    .help-block {
        color: var(--secondary);
        font-size: 0.85rem;
        margin-top: 5px;
    }

    /* 标签样式 */
    .badge {
        padding: 5px 10px;
        border-radius: 50px;
        font-weight: 500;
        font-size: 0.75rem;
    }

    .badge-success {
        background-color: rgba(40, 167, 69, 0.1);
        color: var(--success);
    }

    .badge-danger {
        background-color: rgba(220, 53, 69, 0.1);
        color: var(--danger);
    }
</style>
<section id="main-content">
    <section class="wrapper">
        <div class="row">
            <div aria-hidden="true" aria-labelledby="addUrlLabel" role="dialog" tabindex="-1" id="addUrl" class="modal fade">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title"><i class="bi bi-plus-circle"></i> 添加监控</h4>
                            <button aria-hidden="true" data-dismiss="modal" class="close" type="button">&times;</button>
                        </div>
                        <div class="modal-body">
                            <form>
                                <div class="form-group">
                                    <label>监控网址:</label>
                                    <input type="text" name="url" value="" id="add-url" class="form-control" placeholder="请输入需要监控的网址" required />
                                </div>
                                <div class="form-group">
                                    <label>监控类型:</label>
                                    <select class="form-control" name="type" id="add-type">
                                        <option value="0">微信</option>
                                        <option value="1">QQ</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>监控频率:</label>
                                    <select class="form-control" name="pl" id="add-pl">
                                        <option value="0">每5分钟</option>
                                        <option value="1">每10分钟</option>
                                        <option value="2">每1小时</option>
                                        <option value="3">每天</option>
                                    </select>
                                </div>
                                <button type="button" onclick="insertUrl()" class="btn btn-primary btn-block"><i class="bi bi-check-lg"></i> 确认添加</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div aria-hidden="true" aria-labelledby="editUrlLabel" role="dialog" tabindex="-1" id="editUrl" class="modal fade">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title"><i class="bi bi-pencil"></i> 修改监控</h4>
                            <button aria-hidden="true" data-dismiss="modal" class="close" type="button">&times;</button>
                        </div>
                        <div class="modal-body">
                            <form>
                                <div class="form-group" style="display: none">
                                    <label>ID：</label>
                                    <input type="text" class="form-control" id="edit-id" readonly>
                                </div>
                                <div class="form-group">
                                    <label>监控网址:</label>
                                    <input type="text" name="url" value="" id="edit-url" class="form-control" placeholder="请输入需要监控的网址" required />
                                </div>
                                <div class="form-group">
                                    <label>监控类型:</label>
                                    <select class="form-control" name="type" id="edit-type">
                                        <option value="0">微信</option>
                                        <option value="1">QQ</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>监控频率:</label>
                                    <select class="form-control" name="pl" id="edit-pl">
                                        <option value="0">每5分钟</option>
                                        <option value="1">每10分钟</option>
                                        <option value="2">每1小时</option>
                                        <option value="3">每天</option>
                                    </select>
                                </div>
                                <button type="button" onclick="updateUrl()" class="btn btn-primary btn-block"><i class="bi bi-check-lg"></i> 确认修改</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-12">
                <section class="panel">
                    <div class="card">
                        <div class="card-body">
                            <div class="section-header">
                                <h2 class="section-title"><i class="bi bi-shield-check"></i> 网址监控</h2>
                            </div>
                            <div id="toolbar" class="section-actions">
                                <a href="#addUrl" data-toggle="modal" class="btn btn-primary"><i class="bi bi-plus-lg"></i> 添加网址</a>&nbsp;
                                <div class="btn-group">
                                    <a class="btn btn-warning" href="#" data-toggle="dropdown">
                                        <i class="bi bi-gear"></i> 批量操作
                                        <i class="bi bi-chevron-down"></i>
                                    </a>
                                    <ul class="dropdown-menu">
                                        <li><a href="#" onclick="datadel()" class="dropdown-item"><i class="bi bi-trash"></i> 一键删除</a></li>
                                        <li><a href="#" onclick="setactiveAll(1)" class="dropdown-item"><i class="bi bi-toggle-on"></i> 一键开启</a></li>
                                        <li><a href="#" onclick="setactiveAll(0)" class="dropdown-item"><i class="bi bi-toggle-off"></i> 一键关闭</a></li>
                                    </ul>
                                </div>
                            </div>
                            <table id="listTable"></table>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </section>
</section>
<script src="../static/user/js/common-scripts.js"></script>
<script>
    // 确保layer可用的辅助函数
    function showMsg(text, callback) {
        if (typeof layer !== 'undefined') {
            layer.msg(text, callback);
        } else {
            alert(text);
            if (callback && typeof callback === 'function') {
                callback();
            }
        }
    }
    
    function showAlert(text) {
        if (typeof layer !== 'undefined') {
            layer.alert(text);
        } else {
            alert(text);
        }
    }
    
    function showConfirm(text, confirmCallback, cancelCallback) {
        if (typeof layer !== 'undefined') {
            layer.confirm(text, {
                btn: ['确定', '取消']
            }, confirmCallback, cancelCallback);
        } else {
            if (confirm(text)) {
                if (confirmCallback && typeof confirmCallback === 'function') {
                    confirmCallback();
                }
            } else {
                if (cancelCallback && typeof cancelCallback === 'function') {
                    cancelCallback();
                }
            }
        }
    }

    function initTable() {
        $('#listTable').bootstrapTable('destroy');
        $('#listTable').bootstrapTable({
            classes: 'table table-hover',
            url: 'ajax.php?act=urlcheck',
            method: 'get',
            dataType: 'jsonp',
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
                    kw: $(".search-input").val()
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
                },
                {
                    field: 'url',
                    title: '监控网址',
                    cellStyle: function(value, row, index) {
                        return {
                            css: {
                                "white-space": "nowrap",
                                "text-overflow": "ellipsis",
                                "overflow": "hidden",
                                "max-width": "150px"
                            }
                        }
                    },
                    formatter: function(value, row, index) {
                        var span = document.createElement("span");
                        span.setAttribute("title", value);
                        span.innerHTML = '<a onclick="msg()" class="clipboard" data-clipboard-text="' + row['url'] + '"><i class="bi bi-link-45deg"></i> ' + value + ' </a>';
                        return span.outerHTML;
                    }
                },
                {
                    field: 'type',
                    title: '类型',
                    formatter: function(value, row, index) {
                        let icon = row['type'] == 0 ? 'bi-chat-dots' : 'bi-chat';
                        let type = row['type'] == 0 ? '微信' : 'QQ';
                        return '<span><i class="bi ' + icon + '"></i> ' + type + '</span>';
                    },
                    sortable: true
                },
                {
                    field: 'status',
                    title: '状态',
                    formatter: function(value, row, index) {
                        var value = "";
                        if (row.status == '0') {
                            value = '<span class="badge badge-danger"><i class="bi bi-shield-x"></i> 拦截</span>';
                        } else {
                            value = '<span class="badge badge-success"><i class="bi bi-shield-check"></i> 正常</span>';
                        }
                        return value;
                    },
                    sortable: true
                },
                {
                    field: 'pl',
                    title: '频率',
                    formatter: function(value, row, index) {
                        let icon = 'bi-clock';
                        switch (row['pl']) {
                            case '0':
                                pl = '每5分钟';
                                break;
                            case '1':
                                pl = '每10分钟';
                                break;
                            case '2':
                                pl = '每1个小时';
                                break;
                            case '3':
                                pl = '每天';
                                break;
                            default:
                                pl = '未知';
                        }
                        return '<span><i class="bi ' + icon + '"></i> ' + pl + '</span>';
                    },
                    sortable: true
                },
                {
                    field: 'num',
                    title: '监控次数',
                    formatter: function(value, row, index) {
                        return '<span><i class="bi bi-eye"></i> ' + value + '</span>';
                    },
                    sortable: true
                },
                {
                    field: 'switch',
                    title: '开关',
                    formatter: function(value, row, index) {
                        var value = "";
                        if (row.switch == '0') {
                            value = '<span class="badge badge-danger" onclick="setActive(\'' + row['id'] + '\',' + row['switch'] + ')" style="cursor:pointer;"><i class="bi bi-x-circle"></i> 已关闭</span>';
                        } else {
                            value = '<span class="badge badge-success" onclick="setActive(\'' + row['id'] + '\',' + row['switch'] + ')" style="cursor:pointer;"><i class="bi bi-check-circle"></i> 已开启</span>';
                        }
                        return value;
                    },
                    sortable: true
                },
                {
                    field: 'lasttime',
                    title: '最后执行时间',
                    formatter: function(value, row, index) {
                        return '<span><i class="bi bi-calendar-check"></i> ' + value + '</span>';
                    },
                    sortable: true
                },
                {
                    field: 'addtime',
                    title: '添加时间',
                    formatter: function(value, row, index) {
                        return '<span><i class="bi bi-calendar-plus"></i> ' + value + '</span>';
                    },
                    sortable: true
                },
                {
                    field: 'operate',
                    title: '操作',
                    formatter: function(value, row, index) {
                        return `<div class="action-buttons">
                            <button type="button" class="action-btn action-btn-edit" data-toggle="modal" data-target="#editUrl" data-id="${row.id}">
                                <i class="bi bi-pencil"></i>
                                <span class="action-btn-tooltip">编辑</span>
                            </button>
                            <button type="button" onclick="delUrl('${row.id}')" class="action-btn action-btn-delete">
                                <i class="bi bi-trash"></i>
                                <span class="action-btn-tooltip">删除</span>
                            </button>
                        </div>`;
                    }
                }
            ],
            onLoadSuccess: function(data) {
                // 确保表格加载完成后重新应用tooltip
                setTimeout(function() {
                    $('[data-toggle="tooltip"]').tooltip();
                }, 200);
            }
        });
    }

    $(document).ready(function() {
        // 初始化Bootstrap tooltip
        $('[data-toggle="tooltip"]').tooltip();
        
        // 使用延迟加载确保所有库都已加载
        setTimeout(function() {
            initTable();
        }, 100);
        
        if ($(".search-input").val() != '') {
            $(".search-input").bind("click", initTable);
        }

        // 正确初始化剪贴板
        var clipboard = new ClipboardJS('.clipboard');
        clipboard.on('success', function(e) {
            showMsg('复制成功');
            e.clearSelection();
        });
        clipboard.on('error', function(e) {
            showMsg('复制失败，请手动复制');
        });

        $('#editUrl').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget)
            var id = button.data('id')
            var modal = $(this)
            $.ajax({
                type: 'GET',
                url: 'ajax.php?act=getCheckInfo&id=' + id,
                dataType: 'json',
                success: function(data) {
                    if (data.code == 0) {
                        modal.find('#edit-id').val(id)
                        modal.find('#edit-url').val(data.url)
                        modal.find('#edit-type').val(data.type)
                        modal.find('#edit-pl').val(data.pl)
                    } else {
                        showAlert(data.msg);
                    }
                },
                error: function(data) {
                    showMsg('服务器错误');
                    return false;
                }
            });
        })
    })

    function msg() {
        // 复制成功提示已移动到剪贴板初始化中处理
    }

    function insertUrl() {
        url = $('#add-url').val();
        type = $('#add-type').val();
        pl = $('#add-pl').val();
        if (url == '' || type == '' || pl == '') {
            showAlert('输入内容不完整');
            return false;
        }
        $.ajax({
            url: "ajax.php?act=addCheck",
            type: "POST",
            data: {
                "url": url,
                "type": type,
                "pl": pl
            },
            dataType: "json",
            success: function(data) {
                if (data.code == 0) {
                    showConfirm(data.msg, function() {
                        window.location.reload();
                    }, function() {
                        $("#listTable").bootstrapTable('refresh');
                    });
                } else {
                    showAlert(data.msg);
                }
            },
            error: function(data) {
                showMsg('服务器错误');
                return false;
            }
        });
    }

    function updateUrl() {
        id = $('#edit-id').val();
        url = $('#edit-url').val();
        type = $('#edit-type').val();
        pl = $('#edit-pl').val();
        if (url == '') {
            showAlert('监控网址不能为空');
            return false;
        }
        $.ajax({
            url: "ajax.php?act=upCheck",
            type: "POST",
            data: {
                "id": id,
                "url": url,
                "type": type,
                "pl": pl
            },
            dataType: "json",
            success: function(data) {
                if (data.code == 0) {
                    showMsg('修改成功');
                    setTimeout(function() {
                        window.location.reload();
                    }, 1 * 1000);
                } else {
                    showAlert(data.msg);
                }
            },
            error: function(data) {
                showMsg('服务器错误');
                return false;
            }
        });
    }

    function delUrl(id) {
        showConfirm('您确定要删除这个监控吗？', function() {
            $.ajax({
                url: "ajax.php?act=delCheck",
                type: "POST",
                data: {
                    "id": id
                },
                dataType: "json",
                success: function(result) {
                    if (result.code == 0) {
                        showMsg('删除成功');
                        $("#listTable").bootstrapTable('refresh');
                    } else {
                        showMsg(result.msg);
                    }
                },
                error: function(data) {
                    showMsg('服务器错误');
                    return false;
                }
            });
        });
    }

    function datadel() {
        showConfirm('您确定要删除这些监控网址吗？', function() {
            var cks = document.getElementsByName("ids[]");
            var str = "";
            for (var i = 0; i < cks.length; i++) {
                if (cks[i].checked) {
                    str += cks[i].value + "&";
                }
            }
            str = str.substring(0, str.length - 1);
            $.ajax({
                url: "ajax.php?act=delSelect2",
                type: "POST",
                data: {
                    "str": str
                },
                dataType: "json",
                success: function(result) {
                    if (result.code == 0) {
                        showMsg('删除成功');
                        $("#listTable").bootstrapTable('refresh');
                    } else {
                        showMsg(result.msg);
                    }
                },
                error: function(data) {
                    showMsg('服务器错误');
                    return false;
                }
            });
        });
    }

    function setActive(id, state) {
        state == 1 ? state = 0 : state = 1;
        $.ajax({
            type: 'GET',
            url: 'ajax.php?act=setCheckState&id=' + id + '&state=' + state,
            dataType: 'json',
            success: function(data) {
                if (data.code == 0) {
                    showMsg('修改成功');
                    $("#listTable").bootstrapTable('refresh');
                } else {
                    showMsg(data.msg);
                }
            },
            error: function(data) {
                showMsg('服务器错误');
                return false;
            }
        });
    }

    function setactiveAll(state) {
        title = state == 1 ? '您确定要开启监控这些网址吗？' : '您确定要关闭监控这些网址吗？';
        showConfirm(title, function() {
            var cks = document.getElementsByName("ids[]");
            var str = "";
            for (var i = 0; i < cks.length; i++) {
                if (cks[i].checked) {
                    str += cks[i].value + "&";
                }
            }
            str = str.substring(0, str.length - 1);
            $.ajax({
                url: "ajax.php?act=setCheckStateAll",
                type: "POST",
                data: {
                    "state": state,
                    "str": str
                },
                dataType: "json",
                success: function(data) {
                    if (data.code == 0) {
                        showMsg('修改成功');
                        $("#listTable").bootstrapTable('refresh');
                    }
                },
                error: function(data) {
                    showMsg('服务器错误');
                    return false;
                }
            });
        });
    }
</script>