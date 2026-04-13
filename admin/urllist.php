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
    <title>网址列表</title>
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
                                <h6 class="modal-title" id="addChangeTitle">新增网址</h6>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <form name="add" action="ajax.php?act=addUrl">
                                    <div class="form-group" id="add11">
                                        <label for="add-id" class="control-label">自定义后缀：</label>
                                        <input type="text" class="form-control" name="id" id="add-id">
                                    </div>
                                    <div class="form-group" id="add1">
                                        <label for="add-uid" class="control-label">UID：</label>
                                        <input type="text" class="form-control" name="uid" id="add-uid" value="<?php echo $conf['uid'] ?>" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="add-url" class="control-label">跳转地址：</label>
                                        <input type="text" class="form-control" name="url" id="add-url" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="add-type" class="control-label">短链类型：</label>
                                        <select class="form-control" name="type" id="add-type">
                                            <?php
                                            echo dwzList();
                                            ?>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="add-pattern" class="control-label">跳转类型：</label>
                                        <select class="form-control" name="pattern" id="add-pattern" onchange="tz_pattern()">
                                            <?php
                                            echo pattern_list();
                                            ?>
                                        </select>
                                    </div>
                                    <div class="form-group" id="add10">
                                        <label for="add-jumpmb" class="control-label">跳转模板：</label>
                                        <select class="form-control" name="jumpmb" id="add-jumpmb">
                                            <?php
                                            $mblist = Template::getList2();
                                            foreach ($mblist as $row) {
                                                if ($row == $conf['tz_template']) {
                                                    $selected = 'selected';
                                                } else {
                                                    $selected = '';
                                                }
                                                echo '<option value="' . $row . '" ' . $selected . '>' . $row . '</option>';
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <div class="form-group" id="add4">
                                        <label for="add-title" class="control-label">直链标题：</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" name="title" id="add-title">
                                            <div class="input-group-btn">
                                                <button type="button" class="btn btn-default" aria-label="Help" data-toggle="tooltip" data-placement="top" title="不填写则自动获取网站标题"><span class="mdi mdi-alert-circle-outline"></span></button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group" id="add2">
                                        <label for="add-remarks" class="control-label">网址备注：</label>
                                        <input type="text" class="form-control" name="remarks" id="add-remarks">
                                    </div>
                                <!--    <div class="form-group" id="add3">
                                        <label for="add-pwd" class="control-label">访问密码：</label>
                                        <input type="text" class="form-control" name="pwd" id="add-pwd">
                                    </div>  -->
                                    <div class="form-group" id="add8">
                                        <label for="add-visit" class="control-label">限制访问次数：</label>
                                        <input type="number" class="form-control" name="visit" id="add-visit">
                                    </div>
                                    <div class="form-group" id="add9">
                                        <label for="add-visiturl" class="control-label">达到次数访问网址：</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" name="visiturl" id="add-visiturl">
                                            <div class="input-group-btn">
                                                <button type="button" class="btn btn-default" aria-label="Help" data-toggle="tooltip" data-placement="top" title="留空的话在访问次数用完后直接停止访问"><span class="mdi mdi-alert-circle-outline"></span></button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group" id="add5">
                                        <label for="add-qqjump" class="control-label">QQ跳转：</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" name="qqjump" id="add-qqjump">
                                            <div class="input-group-btn">
                                                <button type="button" class="btn btn-default" aria-label="Help" data-toggle="tooltip" data-placement="top" title="QQ无需特别跳转请留空"><span class="mdi mdi-alert-circle-outline"></span></button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group" id="add6">
                                        <label for="add-wxjump" class="control-label">微信跳转：</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" name="wxjump" id="add-wxjump">
                                            <div class="input-group-btn">
                                                <button type="button" class="btn btn-default" aria-label="Help" data-toggle="tooltip" data-placement="top" title="微信无需特别跳转请留空"><span class="mdi mdi-alert-circle-outline"></span></button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group" id="add7">
                                        <label for="add-alijump" class="control-label">支付宝跳转：</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" name="alijump" id="add-alijump">
                                            <div class="input-group-btn">
                                                <button type="button" class="btn btn-default" aria-label="Help" data-toggle="tooltip" data-placement="top" title="支付宝无需特别跳转请留空"><span class="mdi mdi-alert-circle-outline"></span></button>
                                            </div>
                                        </div>
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
                                <h6 class="modal-title" id="editChangeTitle">修改网址信息</h6>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <form name="edit" action="ajax.php?act=editUrl">
                                    <div class="form-group" style="display: none">
                                        <label for="edit-id" class="control-label">ID：</label>
                                        <input type="text" class="form-control" name="id" id="edit-id" readonly>
                                    </div>
                                    <div class="form-group">
                                        <label for="edit-dwz" class="control-label">短链接：</label>
                                        <input type="text" class="form-control" name="dwz" id="edit-dwz" readonly>
                                    </div>
                                    <div class="form-group" id="edit1">
                                        <label for="edit-url" class="control-label">跳转地址：</label>
                                        <input type="text" class="form-control" name="url" id="edit-url" required>
                                    </div>
                                    <div class="form-group" id="edit2">
                                        <label for="edit-pattern" class="control-label">跳转类型：</label>
                                        <select class="form-control" name="pattern" name="pattern" id="edit-pattern" onchange="edit_pattern()">
                                            <?php
                                            $patternList = '';
                                            $patternList .= $conf['jump1'] == 1 ? '<option value="1">普通跳转</option>' : '';
                                            $patternList .= $conf['jump2'] == 1 ? '<option value="2">防红跳转</option>' : '';
                                            $patternList .= $conf['jump3'] == 1 ? '<option value="3">直链防红</option>' : '';
                                            $patternList .= $conf['jump4'] == 1 ? '<option value="4" style="display:none">直接跳转</option>' : '';
                                            echo $patternList;
                                            ?>
                                        </select>
                                    </div>
                                    <div class="form-group" id="edit3">
                                        <label for="edit-jumpmb" class="control-label">跳转模板：</label>
                                        <select class="form-control" name="jumpmb" id="edit-jumpmb">
                                            <?php
                                            $mblist = Template::getList2();
                                            foreach ($mblist as $row) {
                                                echo '<option value="' . $row . '">' . $row . '</option>';
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <div class="form-group" id="edit4">
                                        <label for="edit-title" class="control-label">直链标题：</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" name="title" id="edit-title">
                                            <div class="input-group-btn">
                                                <button type="button" class="btn btn-default" aria-label="Help" data-toggle="tooltip" data-placement="top" title="不填写则自动获取网站标题"><span class="mdi mdi-alert-circle-outline"></span></button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group" id="edit5">
                                        <label for="edit-remarks" class="control-label">网址备注：</label>
                                        <input type="text" class="form-control" name="remarks" id="edit-remarks">
                                    </div>
                                    <div class="form-group" id="edit6">
                                        <label for="edit-visit" class="control-label">限制访问次数：</label>
                                        <input type="number" class="form-control" name="visit" id="edit-visit">
                                    </div>
                                    <div class="form-group" id="edit7">
                                        <label for="edit-visiturl" class="control-label">达到次数访问网址：</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" name="visiturl" id="edit-visiturl">
                                            <div class="input-group-btn">
                                                <button type="button" class="btn btn-default" aria-label="Help" data-toggle="tooltip" data-placement="top" title="留空的话在访问次数用完后直接停止访问"><span class="mdi mdi-alert-circle-outline"></span></button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group" id="edit8">
                                        <label for="edit-pwd" class="control-label">访问密码：</label>
                                        <input type="text" class="form-control" name="pwd" id="edit-pwd">
                                    </div>
                                    <div class="form-group" id="edit9">
                                        <label for="edit-qqjump" class="control-label">QQ跳转：</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" name="qqjump" id="edit-qqjump">
                                            <div class="input-group-btn">
                                                <button type="button" class="btn btn-default" aria-label="Help" data-toggle="tooltip" data-placement="top" title="QQ无需特别跳转请留空"><span class="mdi mdi-alert-circle-outline"></span></button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group" id="edit10">
                                        <label for="edit-wxjump" class="control-label">微信跳转：</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" name="wxjump" id="edit-wxjump">
                                            <div class="input-group-btn">
                                                <button type="button" class="btn btn-default" aria-label="Help" data-toggle="tooltip" data-placement="top" title="微信无需特别跳转请留空"><span class="mdi mdi-alert-circle-outline"></span></button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group" id="edit11">
                                        <label for="edit-alijump" class="control-label">支付宝跳转：</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" name="alijump" id="edit-alijump">
                                            <div class="input-group-btn">
                                                <button type="button" class="btn btn-default" aria-label="Help" data-toggle="tooltip" data-placement="top" title="支付宝无需特别跳转请留空"><span class="mdi mdi-alert-circle-outline"></span></button>
                                            </div>
                                        </div>
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
                                <span class="mdi mdi-check" aria-hidden="true"></span>解封
                            </button>
                            <button id="btn_edit" type="button" class="btn btn-warning" onclick="setActiveAll(0)">
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
                url: 'ajax.php?act=urllist',
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
                            var value = '<a href="javascript:void(0)" title="点击复制" onclick="msg()" class="clipboard table-link" data-clipboard-text="' + row['dwz'] + '">' + row['dwz'] + ' </a>';
                            return value;
                        }
                    },
                    {
                        field: 'url',
                        title: '跳转网址',
                        formatter: function(value, row, index) {
                            var value = '<a href="javascript:void(0)" title="点击复制" onclick="msg()" class="clipboard table-link" data-clipboard-text="' + row['url'] + '">' + row['url'] + ' </a>';
                            return value;
                        },
                        visible: false
                    },
                    {
                        field: 'qqjump',
                        title: 'QQ跳转',
                        formatter: function(value, row, index) {
                            var value = '<a href="javascript:void(0)" title="点击复制" onclick="msg()" class="clipboard table-link" data-clipboard-text="' + row['qqjump'] + '">' + row['qqjump'] + ' </a>';
                            return value;
                        },
                        visible: false
                    },
                    {
                        field: 'wxjump',
                        title: '微信跳转',
                        formatter: function(value, row, index) {
                            var value = '<a href="javascript:void(0)" title="点击复制" onclick="msg()" class="clipboard table-link" data-clipboard-text="' + row['wxjump'] + '">' + row['wxjump'] + ' </a>';
                            return value;
                        },
                        visible: false
                    },
                    {
                        field: 'alijump',
                        title: '支付跳转',
                        formatter: function(value, row, index) {
                            var value = '<a href="javascript:void(0)" title="点击复制" onclick="msg()" class="clipboard table-link" data-clipboard-text="' + row['alijump'] + '">' + row['alijump'] + ' </a>';
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
                        field: 'addtime',
                        title: '添加时间',
                        sortable: true
                    },
                    {
                        field: 'lasttime',
                        title: '最后访问',
                        sortable: true,
                        visible: false
                    },
                    {
                        field: 'operate',
                        title: '操作',
                        formatter: function(value, row, index) {
                            var value = "";
                            value = '<div class="btn-group"><button type="button" onclick="ewm(&quot;' + row['dwz'] + '&quot;)" class="btn btn-xs btn-default" title="二维码"><i class="mdi mdi-qrcode"></i></button>';
                            value += '<button type="button" class="btn btn-xs btn-default" title="编辑" data-toggle="modal" data-target="#edit" data-id="' + row['id'] + '"><i class="mdi mdi-pencil"></i></button>';
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
                                url: "ajax.php?act=delUrlAll",
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

        $('#edit').on('show.bs.modal', function(event) {
            var loader = $('body').lyearloading({
                opacity: 0.2,
                spinnerSize: 'lg'
            });
            var button = $(event.relatedTarget)
            var id = button.data('id')
            var modal = $(this)
            $.ajax({
                type: 'GET',
                url: 'ajax.php?act=getUrlInfo&id=' + id,
                dataType: 'json',
                success: function(data) {
                    loader.destroy();
                    if (data.code == 0) {
                        modal.find('#edit-id').val(id)
                        modal.find('#edit-dwz').val(data.dwz)
                        modal.find('#edit-url').val(data.url)
                        modal.find('#edit-pattern').val(data.pattern)
                        modal.find('#edit-title').val(data.title)
                        modal.find('#edit-remarks').val(data.remarks)
                        modal.find('#edit-visit').val(data.visit)
                        modal.find('#edit-visiturl').val(data.visiturl)
                        modal.find('#edit-pwd').val(data.pwd)
                        modal.find('#edit-qqjump').val(data.qqjump)
                        modal.find('#edit-wxjump').val(data.wxjump)
                        modal.find('#edit-alijump').val(data.alijump)
                        modal.find('#edit-jumpmb').val(data.jumpmb)
                        switch (data.pattern) {
                            case '1':
                                $('#edit1').css("display", "block");
                                $('#edit2').css("display", "block");
                                $('#edit3').css("display", "none");
                                $('#edit4').css("display", "none");
                                $('#edit5').css("display", "block");
                                $('#edit6').css("display", "block");
                                $('#edit7').css("display", "block");
                                $('#edit8').css("display", "block");
                                $('#edit9').css("display", "block");
                                $('#edit10').css("display", "block");
                                $('#edit11').css("display", "block");
                                break;
                            case '2':
                                $('#edit1').css("display", "block");
                                $('#edit2').css("display", "block");
                                $('#edit3').css("display", "block");
                                $('#edit4').css("display", "none");
                                $('#edit5').css("display", "block");
                                $('#edit6').css("display", "block");
                                $('#edit7').css("display", "block");
                                $('#edit8').css("display", "block");
                                $('#edit9').css("display", "none");
                                $('#edit10').css("display", "none");
                                $('#edit11').css("display", "none");
                                break;
                            case '3':
                                $('#edit1').css("display", "block");
                                $('#edit2').css("display", "block");
                                $('#edit3').css("display", "none");
                                $('#edit4').css("display", "block");
                                $('#edit5').css("display", "block");
                                $('#edit6').css("display", "block");
                                $('#edit7').css("display", "block");
                                $('#edit8').css("display", "block");
                                $('#edit9').css("display", "none");
                                $('#edit10').css("display", "none");
                                $('#edit11').css("display", "none");
                                break;
                            case '4':
                                $('#edit1').css("display", "none");
                                $('#edit2').css("display", "none");
                                $('#edit3').css("display", "none");
                                $('#edit4').css("display", "none");
                                $('#edit5').css("display", "block");
                                $('#edit6').css("display", "none");
                                $('#edit7').css("display", "none");
                                $('#edit8').css("display", "none");
                                $('#edit9').css("display", "none");
                                $('#edit10').css("display", "none");
                                $('#edit11').css("display", "none");
                                break;
                        }
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
                        layer.open({
                            type: 1,
                            skin: 'layui-layer-molv',
                            anim: 2,
                            shadeClose: true,
                            title: '详细信息',
                            content: '<div style="padding:15px"><p>网址ID：' + data.id + '</p><p>UID：' + data.uid + '</p><p>默认跳转：' + data.url + '</p><p>跳转类型：' + pattern + '</p><p>网址备注：' + data.remarks + '</p><p>限制次数：' + data.visit + '</p><p>限制跳转：' + data.visiturl + '</p><p>短链地址：' + data.dwz + '</p><p>跳转模板：' + data.jumpmb + '</p><p>直链标题：' + data.title + '</p><p>QQ跳转：' + data.qqjump + '</p><p>微信跳转：' + data.wxjump + '</p><p>支付宝跳转：' + data.alijump + '</p><p>访问次数：' + data.view + '</p><p>访问密码：' + data.pwd + '</p><p>网址状态：' + state + '</p><p>网址开关：' + u_state + '</p><p>生成IP：' + data.ip + '</p><p>添加时间：' + data.addtime + '</p></div>'
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
                                url: "ajax.php?act=delUrl",
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

        function ewm(url) {
            layer.open({
                type: 1,
                skin: 'layui-layer-lan',
                anim: 2,
                shadeClose: true,
                title: '二维码',
                content: '<div style="padding:20px;text-align:center;"><img width="200px" src="../includes/libs/qrcode.php?size=300&text=' + url + '" /></div>'
            });
        }

        function tz_pattern() {
            var a = $('#add-pattern').val();
            switch (a) {
                case '1':
                    $('#add1').css("display", "block");
                    $('#add2').css("display", "block");
                    $('#add3').css("display", "block");
                    $('#add4').css("display", "none");
                    $('#add5').css("display", "block");
                    $('#add6').css("display", "block");
                    $('#add7').css("display", "block");
                    $('#add8').css("display", "block");
                    $('#add9').css("display", "block");
                    $('#add10').css("display", "none");
                    $('#add11').css("display", "block");
                    break;
                case '2':
                    $('#add1').css("display", "block");
                    $('#add2').css("display", "block");
                    $('#add3').css("display", "block");
                    $('#add4').css("display", "none");
                    $('#add5').css("display", "none");
                    $('#add6').css("display", "none");
                    $('#add7').css("display", "none");
                    $('#add8').css("display", "block");
                    $('#add9').css("display", "block");
                    $('#add10').css("display", "block");
                    $('#add11').css("display", "block");
                    break;
                case '3':
                    $('#add1').css("display", "block");
                    $('#add2').css("display", "block");
                    $('#add3').css("display", "block");
                    $('#add4').css("display", "block");
                    $('#add5').css("display", "none");
                    $('#add6').css("display", "none");
                    $('#add7').css("display", "none");
                    $('#add8').css("display", "block");
                    $('#add9').css("display", "block");
                    $('#add10').css("display", "none");
                    $('#add11').css("display", "block");
                    break;
                case '4':
                    $('#add1').css("display", "none");
                    $('#add2').css("display", "block");
                    $('#add3').css("display", "none");
                    $('#add4').css("display", "none");
                    $('#add5').css("display", "none");
                    $('#add6').css("display", "none");
                    $('#add7').css("display", "none");
                    $('#add8').css("display", "none");
                    $('#add9').css("display", "none");
                    $('#add10').css("display", "none");
                    $('#add11').css("display", "none");
                    break;
            }
        }

        function edit_pattern() {
            var a = $('#edit-pattern').val();
            switch (a) {
                case '1':
                    $('#edit1').css("display", "block");
                    $('#edit2').css("display", "block");
                    $('#edit3').css("display", "none");
                    $('#edit4').css("display", "none");
                    $('#edit5').css("display", "block");
                    $('#edit6').css("display", "block");
                    $('#edit7').css("display", "block");
                    $('#edit8').css("display", "block");
                    $('#edit9').css("display", "block");
                    $('#edit10').css("display", "block");
                    $('#edit11').css("display", "block");
                    break;
                case '2':
                    $('#edit1').css("display", "block");
                    $('#edit2').css("display", "block");
                    $('#edit3').css("display", "block");
                    $('#edit4').css("display", "none");
                    $('#edit5').css("display", "block");
                    $('#edit6').css("display", "block");
                    $('#edit7').css("display", "block");
                    $('#edit8').css("display", "block");
                    $('#edit9').css("display", "none");
                    $('#edit10').css("display", "none");
                    $('#edit11').css("display", "none");
                    break;
                case '3':
                    $('#edit1').css("display", "block");
                    $('#edit2').css("display", "block");
                    $('#edit3').css("display", "none");
                    $('#edit4').css("display", "block");
                    $('#edit5').css("display", "block");
                    $('#edit6').css("display", "block");
                    $('#edit7').css("display", "block");
                    $('#edit8').css("display", "block");
                    $('#edit9').css("display", "none");
                    $('#edit10').css("display", "none");
                    $('#edit11').css("display", "none");
                    break;
                case '4':
                    $('#edit1').css("display", "none");
                    $('#edit2').css("display", "none");
                    $('#edit3').css("display", "none");
                    $('#edit4').css("display", "none");
                    $('#edit5').css("display", "block");
                    $('#edit6').css("display", "none");
                    $('#edit7').css("display", "none");
                    $('#edit8').css("display", "none");
                    $('#edit9').css("display", "none");
                    $('#edit10').css("display", "none");
                    $('#edit11').css("display", "none");
                    break;
            }
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

            var a = $('#add-pattern').val();
            switch (a) {
                case '1':
                    $('#add4').css("display", "none");
                    $('#add10').css("display", "none");
                    break;
                case '2':
                    $('#add4').css("display", "none");
                    $('#add5').css("display", "none");
                    $('#add6').css("display", "none");
                    $('#add7').css("display", "none");
                    break;
                case '3':
                    $('#add5').css("display", "none");
                    $('#add6').css("display", "none");
                    $('#add7').css("display", "none");
                    $('#add10').css("display", "none");
                    break;
                case '4':
                    $('#add1').css("display", "none");
                    $('#add2').css("display", "none");
                    $('#add3').css("display", "none");
                    $('#add4').css("display", "none");
                    $('#add5').css("display", "none");
                    $('#add6').css("display", "none");
                    $('#add7').css("display", "none");
                    $('#add8').css("display", "none");
                    $('#add9').css("display", "none");
                    $('#add10').css("display", "none");
                    break;
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