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
    <title>用户管理</title>
    <link rel="icon" href="favicon.ico" type="image/ico">
    <link href="../static/admin/css/bootstrap.min.css" rel="stylesheet">
    <link href="../static/admin/css/materialdesignicons.min.css" rel="stylesheet">
    <link href="../static/admin/js/bootstrap-table/bootstrap-table.min.css" rel="stylesheet">
    <link href="../static/admin/js/jquery-confirm/jquery-confirm.min.css" rel="stylesheet">
    <link href="../static/admin/js/bootstrap-datetimepicker/bootstrap-datetimepicker.min.css" rel="stylesheet">
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
    </style>
</head>

<body>
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">

                <div class="modal fade" id="add" tabindex="-1" role="dialog" aria-labelledby="addUrlChangeLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h6 class="modal-title" id="addChangeTitle">新增用户</h6>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <form name="add" action="ajax.php?act=addUser">
                                    <div class="form-group">
                                        <label for="add-user" class="control-label">用户名：</label>
                                        <input type="text" class="form-control" id="add-user" name="user" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="add-pwd" class="control-label">密码：</label>
                                        <input type="text" class="form-control" id="add-pwd" name="pwd" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="add-vip" class="control-label">vip时间：</label>
                                        <input type="text" class="form-control" id="add-vip" name="vip" placeholder="请选择具体时间" value="<?php echo $date ?>" data-provide="datetimepicker" data-side-by-side="true" data-format="YYYY-MM-DD HH:mm:ss" required />
                                    </div>
                                    <div class="form-group">
                                        <label for="add-qq" class="control-label">QQ：</label>
                                        <input type="text" class="form-control" id="add-qq" name="qq" required>
                                    </div>
                                </form>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">关闭</button>
                                <button type="submit" class="btn btn-primary ajax-post refresh" target-form="add">添加</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal fade" id="edit" tabindex="-1" role="dialog" aria-labelledby="editChangeLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h6 class="modal-title" id="editChangeTitle">修改用户信息</h6>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <form name="edit" action="ajax.php?act=editUser">
                                    <div class="form-group" style="display: none">
                                        <label for="edit-uid" class="control-label">UID：</label>
                                        <input type="text" class="form-control" name="uid" id="edit-uid" readonly>
                                    </div>
                                    <div class="form-group">
                                        <label for="edit-user" class="control-label">用户名：</label>
                                        <input type="text" class="form-control" name="user" id="edit-user" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="edit-pwd" class="control-label">密码：</label>
                                        <input type="text" class="form-control" name="pwd" id="edit-pwd" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="edit-vip" class="control-label">vip时间：</label>
                                        <input type="text" class="form-control" name="vip" id="edit-vip" placeholder="请选择具体时间" data-provide="datetimepicker" data-side-by-side="true" data-format="YYYY-MM-DD HH:mm:ss" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="edit-qq" class="control-label">QQ：</label>
                                        <input type="text" class="form-control" name="qq" id="edit-qq" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="edit-points" class="control-label">积分：</label>
                                        <input type="text" class="form-control" name="points" id="edit-points" readonly>
                                        <small class="text-muted">如需修改积分，请使用表格中的积分调整功能</small>
                                    </div>
                                </form>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">关闭</button>
                                <button type="submit" class="btn btn-primary ajax-post refresh" target-form="edit">修改</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <div id="toolbar" class="toolbar-btn-action">
                            <button id="btn_add" type="button" class="btn btn-primary" data-toggle="modal" data-target="#add">
                                <span class="mdi mdi-plus" aria-hidden="true"></span>新增
                            </button>
                            <button id="btn_edit" type="button" class="btn btn-success" onclick="setActiveAll(1)">
                                <span class="mdi mdi-check" aria-hidden="true"></span>解冻
                            </button>
                            <button id="btn_edit" type="button" class="btn btn-warning" onclick="setActiveAll(0)">
                                <span class="mdi mdi-block-helper" aria-hidden="true"></span>冻结
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
    <script src="https://lib.baomitu.com/clipboard.js/2.0.6/clipboard.min.js"></script>
    <script src="https://lib.baomitu.com/layer/3.1.1/layer.js"></script>
    <script type="text/javascript" src="../static/admin/js/main.min.js"></script>
    <script type="text/javascript">
        function initTable() {
            $('#listTable').bootstrapTable('destroy');
            $('#listTable').bootstrapTable({
                classes: 'table table-bordered table-hover',
                url: 'ajax.php?act=ulist',
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
                        title: 'ID',
                        sortable: true
                    }, {
                        field: 'user',
                        title: '用户名'
                    },
                    {
                        field: 'qq',
                        title: 'QQ',
                        formatter: function(value, row, index) {
                            var value = '<a href="https://api.btstu.cn/qqtalk/api.php?qq=' + row['qq'] + '" target="_blank" title="交流" class="table-link">' + row['qq'] + '</a>';
                            return value;
                        }
                    },
                    {
                        field: 'vip',
                        title: '会员',
                        formatter: function(value, row, index) {
                            var time = new Date().Format("yyyy-MM-dd hh:mm:ss");
                            var svip = row['vip'] > time ? 'color:var(--primary-color)' : '';
                            var value = '<a style="' + svip + '" href="javascript:renewVip(' + row['id'] + ')" title="点击续期" class="table-link">' + row['vip'] + '</a>';
                            return value;
                        },
                        sortable: true
                    },
                    {
                        field: 'points',
                        title: '积分',
                        formatter: function(value, row, index) {
                            var value = '<a href="javascript:showPoints(' + row['id'] + ')" title="调整积分" class="table-link">' + row['points'] + '</a>';
                            return value;
                        },
                        sortable: true
                    },
                    {
                        field: 'state',
                        title: '状态',
                        formatter: function(value, row, index) {
                            var value = "";
                            if (row.state == '0') {
                                value = '<span class="badge badge-danger" onclick="setactive(' + row['id'] + ',' + row['state'] + ')">冻结</span>';
                            } else if (row.state == '1') {
                                value = '<span class="badge badge-success" onclick="setactive(' + row['id'] + ',' + row['state'] + ')">正常</span>';
                            } else {
                                value = row.pType;
                            }
                            return value;
                        },
                        sortable: true
                    },
                    {
                        field: 'token',
                        title: '对接token',
                        visible: false
                    },
                    {
                        field: 'addtime',
                        title: '注册时间',
                        sortable: true,
                        visible: false
                    },
                    {
                        field: 'lasttime',
                        title: '最后登录',
                        sortable: true,
                        visible: false
                    },
                    {
                        field: 'addip',
                        title: '注册ip',
                        visible: false
                    },
                    {
                        field: 'lastip',
                        title: '最后登录ip',
                        visible: false
                    },
                    {
                        field: 'operate',
                        title: '操作',
                        formatter: function(value, row, index) {
                            var value = "";
                            value = '<div class="btn-group"><button type="button" class="btn btn-xs btn-default" title="编辑" data-toggle="modal" data-target="#edit" data-uid="' + row['id'] + '"><i class="mdi mdi-pencil"></i></button>';
                            value += '<button type="button" onclick="del(' + row['id'] + ')" class="btn btn-xs btn-default" title="删除"><i class="mdi mdi-window-close"></i></button></div>';
                            return value;
                        }
                    }
                ],
                onLoadSuccess: function(data) {
                    $("[data-toggle='tooltip']").tooltip();
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
                url: 'ajax.php?act=setUserState&id=' + id + '&state=' + state,
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
                                url: "ajax.php?act=setUserStateAll",
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
                                url: "ajax.php?act=delUserAll",
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

        function renewVip(uid) {
            layer.prompt({
                title: '需要延时多少个月',
                value: '1',
                formType: 0
            }, function(text, index) {
                var loader = $('body').lyearloading({
                    opacity: 0.2,
                    spinnerSize: 'lg'
                });
                $.ajax({
                    type: 'POST',
                    url: 'ajax.php?act=renewVip',
                    data: {
                        uid: uid,
                        month: text
                    },
                    dataType: 'json',
                    success: function(data) {
                        loader.destroy();
                        if (data.code == 0) {
                            layer.close(index);
                            showNotify(data.msg, 'success');
                            setTimeout(function() {
                                return $("#listTable").bootstrapTable('refresh');
                            }, 1000);
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
            });
        }

        function showCreate(uid) {
            layer.prompt({
                title: '请输入需要调整的次数，支持减号"-"',
                value: '100',
                formType: 0
            }, function(text, index) {
                var loader = $('body').lyearloading({
                    opacity: 0.2,
                    spinnerSize: 'lg'
                });
                $.ajax({
                    type: 'POST',
                    url: 'ajax.php?act=createRecharge',
                    data: {
                        uid: uid,
                        num: text
                    },
                    dataType: 'json',
                    success: function(data) {
                        loader.destroy();
                        if (data.code == 0) {
                            layer.close(index);
                            showNotify(data.msg, 'success');
                            setTimeout(function() {
                                return $("#listTable").bootstrapTable('refresh');
                            }, 1000);
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
            });
        }

        function showCheck(uid) {
            layer.prompt({
                title: '请输入需要调整的次数，支持减号"-"',
                value: '100',
                formType: 0
            }, function(text, index) {
                var loader = $('body').lyearloading({
                    opacity: 0.2,
                    spinnerSize: 'lg'
                });
                $.ajax({
                    type: 'POST',
                    url: 'ajax.php?act=checkRecharge',
                    data: {
                        uid: uid,
                        num: text
                    },
                    dataType: 'json',
                    success: function(data) {
                        loader.destroy();
                        if (data.code == 0) {
                            layer.close(index);
                            showNotify(data.msg, 'success');
                            setTimeout(function() {
                                return $("#listTable").bootstrapTable('refresh');
                            }, 1000);
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
            });
        }

        function showPoints(uid) {
            layer.prompt({
                title: '请输入需要调整的积分，支持减号"-"',
                value: '100',
                formType: 0
            }, function(text, index) {
                var loader = $('body').lyearloading({
                    opacity: 0.2,
                    spinnerSize: 'lg'
                });
                $.ajax({
                    type: 'POST',
                    url: 'ajax.php?act=pointsRecharge',
                    data: {
                        uid: uid,
                        num: text
                    },
                    dataType: 'json',
                    success: function(data) {
                        loader.destroy();
                        if (data.code == 0) {
                            layer.close(index);
                            showNotify(data.msg, 'success');
                            setTimeout(function() {
                                return $("#listTable").bootstrapTable('refresh');
                            }, 1000);
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
            });
        }

        $('#edit').on('show.bs.modal', function(event) {
            var loader = $('body').lyearloading({
                opacity: 0.2,
                spinnerSize: 'lg'
            });
            var button = $(event.relatedTarget)
            var uid = button.data('uid')
            var modal = $(this)
            $.ajax({
                type: 'GET',
                url: 'ajax.php?act=getUserInfo&uid=' + uid,
                dataType: 'json',
                success: function(data) {
                    loader.destroy();
                    if (data.code == 0) {
                        modal.find('#edit-uid').val(uid)
                        modal.find('#edit-user').val(data.user)
                        modal.find('#edit-pwd').val(data.pwd)
                        modal.find('#edit-vip').val(data.vip)
                        modal.find('#edit-qq').val(data.qq)
                        modal.find('#edit-points').val(data.points)
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
        })

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
                                url: "ajax.php?act=delUser",
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

        Date.prototype.Format = function(fmt) {
            var o = {
                "M+": this.getMonth() + 1,
                "d+": this.getDate(),
                "h+": this.getHours(),
                "m+": this.getMinutes(),
                "s+": this.getSeconds(),
                "q+": Math.floor((this.getMonth() + 3) / 3),
                "S": this.getMilliseconds()
            };
            if (/(y+)/.test(fmt))
                fmt = fmt.replace(RegExp.$1, (this.getFullYear() + "").substr(4 - RegExp.$1.length));
            for (var k in o)
                if (new RegExp("(" + k + ")").test(fmt)) fmt = fmt.replace(RegExp.$1, (RegExp.$1.length == 1) ? (o[k]) : (("00" + o[k]).substr(("" + o[k]).length)));
            return fmt;
        }

        $(function() {
            initTable();
            if ($(".search-input").val() != '') {
                $(".search-input").bind("click", initTable);
            }
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