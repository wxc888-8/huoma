<?php

include('../includes/common.php');
if ($islogin != 1) {
    exit('<script language=\'javascript\'>window.location.href=\'./login.php\';</script>');
}

?>
<!DOCTYPE html>
<html lang="zh">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" />
    <title>网址回收站</title>
    <link rel="icon" href="favicon.ico" type="image/ico">
    <link href="../static/admin/css/bootstrap.min.css" rel="stylesheet">
    <link href="../static/admin/css/materialdesignicons.min.css" rel="stylesheet">
    <link href="../static/admin/js/bootstrap-table/bootstrap-table.min.css" rel="stylesheet">
    <link href="../static/admin/js/jquery-confirm/jquery-confirm.min.css" rel="stylesheet">
    <link href="../static/admin/css/style.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #2ECC71;
            --primary-dark: #27AE60;
            --danger-color: #E74C3C;
            --warning-color: #F39C12;
            --bg-color: #f5f5f5;
            --text-color: #333;
            --card-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        body {
            background-color: var(--bg-color);
            color: var(--text-color);
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
        }

        .container-fluid {
            padding: 20px;
        }

        .card {
            background: white;
            border-radius: 8px;
            box-shadow: var(--card-shadow);
            margin-bottom: 20px;
            border: none;
        }

        .card-body {
            padding: 1.5rem;
        }

        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .btn-primary:hover, .btn-primary:focus, .btn-primary:active {
            background-color: var(--primary-dark) !important;
            border-color: var(--primary-dark) !important;
        }

        .btn-danger {
            background-color: var(--danger-color);
            border-color: var(--danger-color);
        }

        .btn-warning {
            background-color: var(--warning-color);
            border-color: var(--warning-color);
        }

        .modal-content {
            border-radius: 8px;
            box-shadow: var(--card-shadow);
            border: none;
        }

        .modal-header {
            background-color: #f8f9fa;
            border-radius: 8px 8px 0 0;
        }

        .modal-title {
            color: var(--text-color);
            font-weight: 500;
        }

        .form-control {
            border-radius: 4px;
            border: 1px solid #ddd;
            padding: 0.5rem 0.75rem;
        }

        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(46, 204, 113, 0.25);
        }

        .table {
            border-radius: 4px;
            overflow: hidden;
        }

        .table th {
            background-color: #f8f9fa;
            border-top: none;
            border-bottom: 1px solid #eee;
            color: #666;
        }

        .table td {
            border-color: #eee;
            vertical-align: middle;
        }

        .table-hover tbody tr:hover {
            background-color: #f8f9fa;
        }

        .fixed-table-toolbar .search .search-input {
            border-radius: 4px;
            border: 1px solid #ddd;
            padding: 0.5rem 0.75rem;
        }

        .badge {
            padding: 0.4rem 0.6rem;
            border-radius: 4px;
            cursor: pointer;
        }

        .badge-success {
            background-color: var(--primary-color);
        }

        .badge-danger {
            background-color: var(--danger-color);
        }

        .btn-group > .btn {
            border-radius: 4px;
            margin-right: 4px;
        }

        #toolbar {
            margin-bottom: 1rem;
        }

        .toolbar-btn-action button {
            margin-right: 8px;
        }

        .bootstrap-table .fixed-table-container .table .bs-checkbox {
            vertical-align: middle;
        }

        .fixed-table-pagination .pagination-detail, 
        .fixed-table-pagination .pagination {
            margin-top: 1rem;
        }

        /* 自定义类名，用于链接样式 */
        .table-link {
            color: var(--primary-color);
            text-decoration: none;
        }
        
        .table-link:hover {
            color: var(--primary-dark);
            text-decoration: underline;
        }
        
        /* 添加层样式 */
        .layui-layer-molv .layui-layer-title {
            background-color: var(--primary-color) !important;
            color: white;
            border: none;
        }
        
        .layui-layer-molv .layui-layer-content {
            background-color: white;
            color: var(--text-color);
        }
    </style>
</head>

<body>
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <div id="toolbar" class="toolbar-btn-action">
                            <button id="btn_add" type="button" class="btn btn-primary m-r-5" onclick="reducUrlAll()">
                                <span class="mdi mdi-plus" aria-hidden="true"></span>恢复
                            </button>
                            <button id="btn_edit" type="button" class="btn btn-success m-r-5" onclick="setActiveAll(1)">
                                <span class="mdi mdi-check" aria-hidden="true"></span>解封
                            </button>
                            <button id="btn_edit" type="button" class="btn btn-warning m-r-5" onclick="setActiveAll(0)">
                                <span class="mdi mdi-block-helper" aria-hidden="true"></span>封禁
                            </button>
                            <button id="btn_delete" type="button" class="btn btn-danger" onclick="delAll()">
                                <span class="mdi mdi-window-close" aria-hidden="true"></span>删除
                            </button>
                        </div>
                        <table id="listTable"></table>
                    </div>
                </div>
            </div>

        </div>

    </div>

    <script type="text/javascript" src="../static/admin/js/jquery.min.js"></script>
    <script type="text/javascript" src="../static/admin/js/popper.min.js"></script>
    <script type="text/javascript" src="../static/admin/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="../static/admin/js/lyear-loading.js"></script>
    <script type="text/javascript" src="../static/admin/js/bootstrap-notify.min.js"></script>
    <script type="text/javascript" src="../static/admin/js/jquery-confirm/jquery-confirm.min.js"></script>
    <script type="text/javascript" src="../static/admin/js/bootstrap-table/bootstrap-table.min.js"></script>
    <script type="text/javascript" src="../static/admin/js/bootstrap-table/locale/bootstrap-table-zh-CN.min.js"></script>
    <script type="text/javascript" src="../static/admin/js/moment.js/moment.min.js"></script>
    <script type="text/javascript" src="../static/admin/js/bootstrap-datetimepicker/bootstrap-datetimepicker.min.js"></script>
    <script type="text/javascript" src="../static/admin/js/moment.js/locale/zh-cn.min.js"></script>
    <script src="https://lib.baomitu.com/layer/3.1.1/layer.js"></script>
    <script src="https://lib.baomitu.com/clipboard.js/2.0.6/clipboard.min.js"></script>
    <script type="text/javascript" src="../static/admin/js/main.min.js"></script>
    <script type="text/javascript">
        function initTable() {
            $('#listTable').bootstrapTable('destroy');
            $('#listTable').bootstrapTable({
                classes: 'table table-bordered table-hover',
                url: 'ajax.php?act=urldel',
                method: 'get',
                dataType: 'jsonp',
                uniqueId: 'id',
                selectItemName: 'ids[]',
                idField: 'id',
                toolbar: '#toolbar',
                showColumns: true,
                showRefresh: true,
                showToggle: true,
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
                    }, {
                        field: 'id',
                        title: 'ID'
                    }, {
                        field: 'uid',
                        title: 'UID',
                        sortable: true
                    },
                    {
                        field: 'dwz',
                        title: '短网址',
                        formatter: function(value, row, index) {
                            var value = '<a href="javascript:void(0)" title="点击复制" onclick="msg()" class="clipboard" data-clipboard-text="' + row['dwz'] + '">' + row['dwz'] + ' </a>';
                            return value;
                        }
                    },
                    {
                        field: 'url',
                        title: '跳转网址',
                        formatter: function(value, row, index) {
                            var value = '<a href="javascript:void(0)" title="点击复制" onclick="msg()" class="clipboard" data-clipboard-text="' + row['url'] + '">' + row['url'] + ' </a>';
                            return value;
                        },
                        visible: false
                    },
                    {
                        field: 'qqjump',
                        title: 'QQ跳转',
                        formatter: function(value, row, index) {
                            var value = '<a href="javascript:void(0)" title="点击复制" onclick="msg()" class="clipboard" data-clipboard-text="' + row['qqjump'] + '">' + row['qqjump'] + ' </a>';
                            return value;
                        },
                        visible: false
                    },
                    {
                        field: 'wxjump',
                        title: '微信跳转',
                        formatter: function(value, row, index) {
                            var value = '<a href="javascript:void(0)" title="点击复制" onclick="msg()" class="clipboard" data-clipboard-text="' + row['wxjump'] + '">' + row['wxjump'] + ' </a>';
                            return value;
                        },
                        visible: false
                    },
                    {
                        field: 'alijump',
                        title: '支付跳转',
                        formatter: function(value, row, index) {
                            var value = '<a href="javascript:void(0)" title="点击复制" onclick="msg()" class="clipboard" data-clipboard-text="' + row['alijump'] + '">' + row['alijump'] + ' </a>';
                            return value;
                        },
                        visible: false
                    },
                    {
                        field: 'pattern',
                        title: '类型',
                        formatter: function(value, row, index) {
                            switch (row['pattern']) {
                                case '1':
                                    pattern = '普通';
                                    break;
                                case '2':
                                    pattern = '防红';
                                    break;
                                case '3':
                                    pattern = '直链';
                                    break;
                                case '4':
                                    pattern = '直跳';
                                    break;
                                default:
                                    pattern = '未知';
                            }
                            return pattern;
                        },
                        sortable: true
                    },
                    {
                        field: 'view',
                        title: '访问',
                        sortable: true
                    },
                    {
                        field: 'remarks',
                        title: '备注',
                        visible: false
                    },
                    {
                        field: 'ip',
                        title: '生成IP',
                        visible: false
                    },
                    {
                        field: 'state',
                        title: '状态',
                        formatter: function(value, row, index) {
                            var value = "";
                            if (row.state == '0') {
                                value = '<span class="badge badge-danger" onclick="setactive(\'' + row['id'] + '\',' + row['state'] + ')">封禁</span>';
                            } else if (row.state == '1') {
                                value = '<span class="badge badge-success" onclick="setactive(\'' + row['id'] + '\',' + row['state'] + ')">正常</span>';
                            } else {
                                value = row.pType;
                            }
                            return value;
                        },
                        sortable: true
                    },
                    {
                        field: 'lasttime',
                        title: '最后访问',
                        sortable: true,
                        visible: false
                    },
                    {
                        field: 'deltime',
                        title: '删除时间',
                        sortable: true
                    },
                    {
                        field: 'operate',
                        title: '操作',
                        formatter: function(value, row, index) {
                            var value = "";
                            value = '<div class="btn-group"><button type="button" onclick="reducUrl(&quot;' + row['id'] + '&quot;)" class="btn btn-xs btn-default" title="恢复"><i class="mdi mdi-keyboard-backspace"></i></button>';
                            value += '<button type="button" onclick="show(&quot;' + row['id'] + '&quot;)" class="btn btn-xs btn-default" title="详细信息"><i class="mdi mdi-information-outline"></i></button>';
                            value += '<button type="button" onclick="del(&quot;' + row['id'] + '&quot;)" class="btn btn-xs btn-default" title="删除"><i class="mdi mdi-window-close"></i></button></div>';
                            return value;
                        }
                    }
                ],
                onLoadSuccess: function(data) {
                    $("[data-toggle='tooltip']").tooltip();
                }
            });
        }

        function reducUrlAll() {
            var selRows = $("#listTable").bootstrapTable("getSelections");
            if (selRows.length == 0) {
                alert("请至少选择一条数据");
                return;
            }
            $.confirm({
                title: '',
                content: '您确定要恢复这些网址吗？',
                type: 'orange',
                typeAnimated: true,
                buttons: {
                    confirm: {
                        text: '确认',
                        btnClass: 'btn-blue',
                        action: function() {
                            var loader = $('body').lyearloading({
                                opacity: 0.2,
                                spinnerSize: 'lg'
                            });
                            var postData = "";
                            $.each(selRows, function(i) {
                                postData += this.id;
                                if (i < selRows.length - 1) {
                                    postData += "&";
                                }
                            });
                            $.ajax({
                                url: "ajax.php?act=reducUrlAll",
                                type: "POST",
                                data: {
                                    "str": postData
                                },
                                dataType: "json",
                                success: function(data) {
                                    loader.destroy();
                                    if (data.code == 0) {
                                        showNotify(data.msg, 'success');
                                        setTimeout(function() {
                                            return $("#listTable").bootstrapTable('refresh');
                                        }, 1000);
                                    } else {
                                        showNotify(data.msg, 'danger');
                                    }
                                },
                                error: function(data) {
                                    loader.destroy();
                                    showNotify('服务器发生错误，请稍后再试', 'danger');
                                    return false;
                                }
                            });
                        }
                    },
                    cancel: {
                        text: '取消',
                        action: function() {}
                    }
                }
            });
        }

        function setactive(id, state) {
            var loader = $('body').lyearloading({
                opacity: 0.2,
                spinnerSize: 'lg'
            });
            state == 1 ? state = 0 : state = 1;
            $.ajax({
                type: 'GET',
                url: 'ajax.php?act=setUrlState&id=' + id + '&state=' + state,
                dataType: 'json',
                success: function(data) {
                    loader.destroy();
                    if (data.code == 0) {
                        showNotify(data.msg, 'success');
                        setTimeout(function() {
                            return $("#listTable").bootstrapTable('refresh');
                        }, 1000);
                    } else {
                        showNotify(data.msg, 'danger');
                    }
                },
                error: function(data) {
                    loader.destroy();
                    showNotify('服务器发生错误，请稍后再试', 'danger');
                    return false;
                }
            });
        }

        function setActiveAll(state) {
            var selRows = $("#listTable").bootstrapTable("getSelections");
            if (selRows.length == 0) {
                alert("请至少选择一条数据");
                return;
            }
            $.confirm({
                title: '',
                content: '确认要执行该操作吗？',
                type: 'orange',
                typeAnimated: true,
                buttons: {
                    confirm: {
                        text: '确认',
                        btnClass: 'btn-blue',
                        action: function() {
                            var loader = $('body').lyearloading({
                                opacity: 0.2,
                                spinnerSize: 'lg'
                            });
                            var postData = "";
                            $.each(selRows, function(i) {
                                postData += this.id;
                                if (i < selRows.length - 1) {
                                    postData += "&";
                                }
                            });
                            $.ajax({
                                url: "ajax.php?act=setUrlStateAll",
                                type: "POST",
                                data: {
                                    "state": state,
                                    "str": postData
                                },
                                dataType: "json",
                                success: function(data) {
                                    loader.destroy();
                                    if (data.code == 0) {
                                        showNotify(data.msg, 'success');
                                        setTimeout(function() {
                                            return $("#listTable").bootstrapTable('refresh');
                                        }, 1000);
                                    } else {
                                        showNotify(data.msg, 'danger');
                                    }
                                },
                                error: function(data) {
                                    loader.destroy();
                                    showNotify('服务器发生错误，请稍后再试', 'danger');
                                    return false;
                                }
                            });
                        }
                    },
                    cancel: {
                        text: '取消',
                        action: function() {}
                    }
                }
            });
        }

        function delAll() {
            var selRows = $("#listTable").bootstrapTable("getSelections");
            if (selRows.length == 0) {
                alert("请至少选择一条数据");
                return;
            }
            $.confirm({
                title: '',
                content: '确认要执行该操作吗？',
                type: 'red',
                typeAnimated: true,
                buttons: {
                    confirm: {
                        text: '确认',
                        btnClass: 'btn-blue',
                        action: function() {
                            var loader = $('body').lyearloading({
                                opacity: 0.2,
                                spinnerSize: 'lg'
                            });
                            var postData = "";
                            $.each(selRows, function(i) {
                                postData += this.id;
                                if (i < selRows.length - 1) {
                                    postData += "&";
                                }
                            });
                            $.ajax({
                                url: "ajax.php?act=delUrlAll2",
                                type: "POST",
                                data: {
                                    "str": postData
                                },
                                dataType: "json",
                                success: function(data) {
                                    loader.destroy();
                                    if (data.code == 0) {
                                        showNotify(data.msg, 'success');
                                        setTimeout(function() {
                                            return $("#listTable").bootstrapTable('refresh');
                                        }, 1000);
                                    } else {
                                        showNotify(data.msg, 'danger');
                                    }
                                },
                                error: function(data) {
                                    loader.destroy();
                                    showNotify('服务器发生错误，请稍后再试', 'danger');
                                    return false;
                                }
                            });
                        }
                    },
                    cancel: {
                        text: '取消',
                        action: function() {}
                    }
                }
            });
        }

        function reducUrl(id) {
            $.confirm({
                title: '',
                content: '确定要还原这个网址吗？',
                type: 'orange',
                typeAnimated: true,
                buttons: {
                    confirm: {
                        text: '确认',
                        btnClass: 'btn-blue',
                        action: function() {
                            var loader = $('body').lyearloading({
                                opacity: 0.2,
                                spinnerSize: 'lg'
                            });
                            $.ajax({
                                url: "ajax.php?act=reducUrl",
                                type: "POST",
                                data: {
                                    "id": id
                                },
                                dataType: "json",
                                success: function(data) {
                                    loader.destroy();
                                    if (data.code == 0) {
                                        showNotify(data.msg, 'success');
                                        setTimeout(function() {
                                            return $("#listTable").bootstrapTable('refresh');
                                        }, 1000);
                                    } else {
                                        showNotify(data.msg, 'danger');
                                    }
                                },
                                error: function(data) {
                                    loader.destroy();
                                    showNotify('服务器发生错误，请稍后再试', 'danger');
                                    return false;
                                }
                            });
                        }
                    },
                    cancel: {
                        text: '取消',
                        action: function() {}
                    }
                }
            });
        }

        function show(id) {
            var loader = $('body').lyearloading({
                opacity: 0.2,
                spinnerSize: 'lg'
            });
            $.ajax({
                type: 'GET',
                url: 'ajax.php?act=getUrlInfo&id=' + id,
                dataType: 'json',
                success: function(data) {
                    loader.destroy();
                    if (data.code == 0) {
                        state = data.state == 1 ? '正常' : '封禁';
                        u_state = data.u_state == 1 ? '开启' : '关闭';
                        switch (data.pattern) {
                            case '1':
                                pattern = '普通';
                                break;
                            case '2':
                                pattern = '防红';
                                break;
                            case '3':
                                pattern = '直链';
                                break;
                            case '4':
                                pattern = '直跳';
                                break;
                            default:
                                pattern = '未知';
                                break;
                        }
                        switch (data.jumpmb) {
                            case 'jump1':
                                jumpmb = '模板1';
                                break;
                            case 'jump2':
                                jumpmb = '模板2';
                                break;
                            case 'jump3':
                                jumpmb = '模板3';
                                break;
                            case 'jump4':
                                jumpmb = '模板4';
                                break;
                        }
                        layer.open({
                            type: 1,
                            skin: 'layui-layer-molv',
                            anim: 2,
                            shadeClose: true,
                            title: '详细信息',
                            content: '<div style="padding:15px"><p>网址ID：' + data.id + '</p><p>UID：' + data.uid + '</p><p>默认跳转：' + data.url + '</p><p>跳转类型：' + pattern + '</p><p>网址备注：' + data.remarks + '</p><p>限制次数：' + data.visit + '</p><p>限制跳转：' + data.visiturl + '</p><p>短链地址：' + data.dwz + '</p><p>跳转模板：' + jumpmb + '</p><p>直链标题：' + data.title + '</p><p>QQ跳转：' + data.qqjump + '</p><p>微信跳转：' + data.wxjump + '</p><p>支付宝跳转：' + data.alijump + '</p><p>访问次数：' + data.view + '</p><p>访问密码：' + data.pwd + '</p><p>网址状态：' + state + '</p><p>网址开关：' + u_state + '</p><p>生成IP：' + data.ip + '</p><p>添加时间：' + data.addtime + '</p></div>'
                        });
                    } else {
                        showNotify(data.msg, 'warning');
                    }
                },
                error: function(data) {
                    loader.destroy();
                    showNotify('服务器错误', 'danger');
                    return false;
                }
            });
        }

        function del(id) {
            $.confirm({
                title: '',
                content: '确认要执行该操作吗？',
                type: 'red',
                typeAnimated: true,
                buttons: {
                    confirm: {
                        text: '确认',
                        btnClass: 'btn-blue',
                        action: function() {
                            var loader = $('body').lyearloading({
                                opacity: 0.2,
                                spinnerSize: 'lg'
                            });
                            $.ajax({
                                url: "ajax.php?act=delUrl2",
                                type: "POST",
                                data: {
                                    "id": id
                                },
                                dataType: "json",
                                success: function(data) {
                                    loader.destroy();
                                    if (data.code == 0) {
                                        showNotify(data.msg, 'success');
                                        setTimeout(function() {
                                            return $("#listTable").bootstrapTable('refresh');
                                        }, 1000);
                                    } else {
                                        showNotify(data.msg, 'danger');
                                    }
                                },
                                error: function(data) {
                                    loader.destroy();
                                    showNotify('服务器发生错误，请稍后再试', 'danger');
                                    return false;
                                }
                            });

                        }
                    },
                    cancel: {
                        text: '取消',
                        action: function() {}
                    }
                }
            });
        }

        function msg() {
            showNotify('复制成功', 'success');
        }

        $(function() {
            initTable();
            if ($(".search-input").val() != '') {
                $(".search-input").bind("click", initTable);
            }
            new ClipboardJS('.clipboard');

            jQuery(document).delegate('.ajax-post', 'click', function() {
                var self = jQuery(this),
                    tips = self.data('tips'),
                    ajax_url = self.attr("href") || self.data("url");
                var target_form = self.attr('target-form');
                var text = self.data('tips');
                var form = jQuery('form[name="' + target_form + '"]');

                if (form.length == 0) {
                    form = jQuery('.' + target_form);
                }

                var form_data = form.serialize();
                if ('submit' == self.attr('type') || ajax_url) {
                    if (void 0 == form.get(0)) return false;

                    if ('FORM' == form.get(0).nodeName) {
                        ajax_url = ajax_url || form.get(0).action;

                        if (self.hasClass('confirm')) {
                            $.confirm({
                                title: '',
                                content: tips || '确认要执行该操作吗？',
                                type: 'orange',
                                typeAnimated: true,
                                buttons: {
                                    confirm: {
                                        text: '确认',
                                        btnClass: 'btn-blue',
                                        action: function() {
                                            var loader = $('body').lyearloading({
                                                opacity: 0.2,
                                                spinnerSize: 'lg'
                                            });
                                            self.attr('autocomplete', 'off').prop('disabled', true);
                                            ajaxPostFun(self, ajax_url, form_data, loader);
                                        }
                                    },
                                    cancel: {
                                        text: '取消',
                                        action: function() {}
                                    }
                                }
                            });
                            return false;
                        } else {
                            self.attr("autocomplete", "off").prop("disabled", true);
                        }
                    } else if ('INPUT' == form.get(0).nodeName || 'SELECT' == form.get(0).nodeName || 'TEXTAREA' == form.get(0).nodeName) {
                        if (form.get(0).type == 'checkbox' && form_data == '') {
                            showNotify('请选择您要操作的数据', 'danger');
                            return false;
                        }

                        if (self.hasClass('confirm')) {
                            $.confirm({
                                title: '',
                                content: tips || '确认要执行该操作吗？',
                                type: 'orange',
                                typeAnimated: true,
                                buttons: {
                                    confirm: {
                                        text: '确认',
                                        btnClass: 'btn-blue',
                                        action: function() {
                                            var loader = $('body').lyearloading({
                                                opacity: 0.2,
                                                spinnerSize: 'lg'
                                            });
                                            self.attr('autocomplete', 'off').prop('disabled', true);

                                            ajaxPostFun(self, ajax_url, form_data, loader);
                                        }
                                    },
                                    cancel: {
                                        text: '取消',
                                        action: function() {}
                                    }
                                }
                            });
                            return false;
                        } else {
                            self.attr("autocomplete", "off").prop("disabled", true);
                        }
                    } else {
                        if (self.hasClass('confirm')) {
                            $.confirm({
                                title: '',
                                content: tips || '确认要执行该操作吗？',
                                type: 'orange',
                                typeAnimated: true,
                                buttons: {
                                    confirm: {
                                        text: '确认',
                                        btnClass: 'btn-blue',
                                        action: function() {
                                            var loader = $('body').lyearloading({
                                                opacity: 0.2,
                                                spinnerSize: 'lg'
                                            });
                                            self.attr('autocomplete', 'off').prop('disabled', true);

                                            ajaxPostFun(self, ajax_url, form_data, loader);
                                        }
                                    },
                                    cancel: {
                                        text: '取消',
                                        action: function() {}
                                    }
                                }
                            });
                            return false;
                        } else {
                            form_data = form.find("input,select,textarea").serialize();
                            self.attr("autocomplete", "off").prop("disabled", true);
                        }
                    }

                    var loader = $('body').lyearloading({
                        opacity: 0.2,
                        spinnerSize: 'lg'
                    });
                    ajaxPostFun(self, ajax_url, form_data, loader);

                    return false;
                }
            });


            function ajaxPostFun(selfObj, ajax_url, form_data, loader) {
                jQuery.post(ajax_url, form_data).done(function(res) {
                    loader.destroy();
                    var msg = res.msg;
                    if (res.code == 0) {
                        showNotify(msg, 'success');
                        setTimeout(function() {
                            selfObj.attr("autocomplete", "on").prop("disabled", false);
                            return selfObj.hasClass("refresh") ? location.reload() : $("#listTable").bootstrapTable('refresh');
                        }, 1000);
                    } else {
                        showNotify(msg, 'danger');
                        selfObj.attr("autocomplete", "on").prop("disabled", false);
                    }
                }).fail(function() {
                    loader.destroy();
                    showNotify('服务器发生错误，请稍后再试', 'danger');
                    selfObj.attr("autocomplete", "on").prop("disabled", false);
                });
            }
        });

        function showNotify($msg, $type, $delay, $icon, $from, $align) {
            $type = $type || 'info';
            $delay = $delay || 3000;
            $from = $from || 'top';
            $align = $align || 'right';
            $enter = $type == 'danger' ? 'animated shake' : 'animated fadeInUp';

            jQuery.notify({
                icon: $icon,
                message: $msg
            }, {
                element: 'body',
                type: $type,
                allow_dismiss: true,
                newest_on_top: true,
                showProgressbar: false,
                placement: {
                    from: $from,
                    align: $align
                },
                offset: 20,
                spacing: 10,
                z_index: 10800,
                delay: $delay,
                animate: {
                    enter: $enter,
                    exit: 'animated fadeOutDown'
                }
            });
        }
    </script>
</body>

</html>