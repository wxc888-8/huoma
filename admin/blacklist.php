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
    <title>名单管理</title>
    <link rel="icon" href="favicon.ico" type="image/ico">
    <link href="../static/admin/css/bootstrap.min.css" rel="stylesheet">
    <link href="../static/admin/css/materialdesignicons.min.css" rel="stylesheet">
    <link href="../static/admin/js/bootstrap-table/bootstrap-table.min.css" rel="stylesheet">
    <link href="../static/admin/js/jquery-confirm/jquery-confirm.min.css" rel="stylesheet">
    <link href="../static/admin/css/style.min.css" rel="stylesheet">
</head>

<body>
    <div class="container-fluid p-t-15">
        <div class="row">
            <div class="col-lg-12">

                <div class="modal fade" id="add" tabindex="-1" role="dialog" aria-labelledby="addUrlChangeLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h6 class="modal-title" id="addChangeTitle">新增名单</h6>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <form name="add" action="ajax.php?act=addBlack">
                                    <div class="form-group">
                                        <label for="add-content" class="control-label">内容：</label>
                                        <input type="text" class="form-control" name="content" id="add-content" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="add-type" class="control-label">类型：</label>
                                        <select class="form-control" name="type" id="add-type">
                                            <option value="0">黑名单IP</option>
                                            <option value="1">黑名单链接</option>
                                            <option value="2">黑名单域名</option>
                                        </select>
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
                                <form name="edit" action="ajax.php?act=editBlack">
                                    <div class="form-group" style="display: none">
                                        <label for="edit-uid" class="control-label">ID：</label>
                                        <input type="text" class="form-control" name="id" id="edit-id" readonly>
                                    </div>
                                    <div class="form-group">
                                        <label for="edit-content" class="control-label">内容：</label>
                                        <input type="text" class="form-control" name="content" id="edit-content" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="edit-type" class="control-label">类型：</label>
                                        <select class="form-control" name="type" id="edit-type">
                                            <option value="0">黑名单IP</option>
                                            <option value="1">黑名单链接</option>
                                            <option value="2">黑名单域名</option>
                                        </select>
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
                            <button id="btn_add" type="button" class="btn btn-primary m-r-5" data-toggle="modal" data-target="#add">
                                <span class="mdi mdi-plus" aria-hidden="true"></span>新增
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
    <script src="https://lib.baomitu.com/layer/3.1.1/layer.js"></script>
    <script type="text/javascript" src="../static/admin/js/main.min.js"></script>
    <script type="text/javascript">
        function initTable() {
            $('#listTable').bootstrapTable('destroy');
            $('#listTable').bootstrapTable({
                classes: 'table table-bordered table-hover table-striped',
                url: 'ajax.php?act=blacklist',
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
                        field: 'content',
                        title: '内容'
                    }, {
                        field: 'type',
                        title: '类型',
                        formatter: function(value, row, index) {
                            switch (row['type']) {
                                case '0':
                                    type = '黑名单IP';
                                    break;
                                case '1':
                                    type = '黑名单链接';
                                    break;
                                case '2':
                                    type = '黑名单域名';
                                    break;
                                default:
                                    type = '未知';
                            }
                            return type;
                        },
                        sortable: true
                    },
                    {
                        field: 'addtime',
                        title: '添加时间',
                        sortable: true
                    },
                    {
                        field: 'operate',
                        title: '操作',
                        formatter: function(value, row, index) {
                            var value = "";
                            value = '<div class="btn-group"><button type="button" class="btn btn-xs btn-default" title="编辑" data-toggle="modal" data-target="#edit" data-id="' + row['id'] + '"><i class="mdi mdi-pencil"></i></button>';
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
                                url: "ajax.php?act=delBlackAll",
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
                url: 'ajax.php?act=getBlackInfo&id=' + id,
                dataType: 'json',
                success: function(data) {
                    loader.destroy();
                    if (data.code == 0) {
                        modal.find('#edit-id').val(id)
                        modal.find('#edit-content').val(data.content)
                        modal.find('#edit-type').val(data.type)
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
                                url: "ajax.php?act=delBlack",
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