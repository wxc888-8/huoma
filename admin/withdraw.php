<?php
include("../includes/common.php");
$title = '提现审核';
if($islogin==1){}else exit("<script language='javascript'>window.location.href='./login.php';</script>");

// 处理搜索条件
$search_user = isset($_GET['user']) ? trim($_GET['user']) : null;
$search_status = isset($_GET['status']) ? intval($_GET['status']) : -1;
$search_time = isset($_GET['time']) ? trim($_GET['time']) : null;

$where = "1=1";
if($search_user) {
    $where .= " AND b.user LIKE '%".addslashes($search_user)."%'";
}
if($search_status >= 0) {
    $where .= " AND a.status='$search_status'";
}
if($search_time) {
    $times = explode(' - ', $search_time);
    if(count($times) == 2) {
        $start_time = $times[0];
        $end_time = $times[1];
        $where .= " AND a.create_time BETWEEN '$start_time' AND '$end_time'";
    }
}

// 移除旧的POST处理代码,现在通过AJAX处理

// 查询提现记录
$numrows=$DB->count("SELECT count(*) from dwz_withdraw");
$pagesize=30;
$pages=ceil($numrows/$pagesize);
$page=isset($_GET['page'])?intval($_GET['page']):1;
$offset=$pagesize*($page-1);

$rs=$DB->query("SELECT a.*,b.user FROM dwz_withdraw a LEFT JOIN dwz_user b ON a.uid=b.id WHERE {$where} ORDER BY a.create_time DESC LIMIT $offset,$pagesize");
?>

<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    <link href="../static/admin/css/style.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f7fa;
        }
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }
        .card:hover {
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
        }
        .card-header {
            background-color: #fff;
            border-bottom: none;
            padding: 20px;
        }
        .card-title {
            font-size: 24px;
            font-weight: 600;
            color: #333;
        }
        .btn-group .btn {
            border-radius: 5px;
            margin-right: 10px;
            transition: all 0.2s ease;
        }
        .btn-group .btn:hover {
            transform: translateY(-2px);
        }
        .form-floating .form-control,
        .form-floating .form-select {
            border-radius: 5px;
        }
        .form-floating label {
            color: #777;
        }
        .table {
            background-color: #fff;
            border-radius: 10px;
            overflow: hidden;
        }
        .table thead th {
            background-color: #f8f9fa;
            border-bottom: 2px solid #e9ecef;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 14px;
            letter-spacing: 0.5px;
        }
        .table tbody tr {
            transition: all 0.2s ease;
        }
        .table tbody tr:hover {
            background-color: rgba(46, 204, 113, 0.05);
        }
        .badge {
            padding: 8px 12px;
            font-size: 12px;
            font-weight: 500;
            border-radius: 5px;
        }
        .pagination .page-link {
            border-radius: 5px;
            margin-right: 5px;
            color: #6c757d;
            background-color: #f8f9fa;
            border: none;
            font-weight: 500;
            transition: all 0.2s ease;
        }
        .pagination .page-item.active .page-link {
            background-color: #2ECC71;
            color: #fff;
        }
        .pagination .page-link:hover {
            background-color: #e9ecef;
            color: #2ECC71;
            transform: translateY(-2px);
        }
        .loading-spinner {
            display: inline-block;
            width: 1.5rem;
            height: 1.5rem;
            border: 3px solid rgba(255,255,255,0.3);
            border-radius: 50%;
            border-top-color: #fff;
            animation: spin 1s ease-in-out infinite;
        }
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        .tooltip {
            font-size: 0.85rem;
        }
        .tooltip .tooltip-inner {
            padding: 0.5rem 1rem;
            box-shadow: 0 3px 15px rgba(0,0,0,.1);
        }
    </style>
</head>
<body>
    <div class="container-fluid p-4">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header border-0">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                <div class="card-title h5 mb-0">
                                    <i class="fas fa-wallet text-primary me-2"></i>提现审核
                                </div>
                            </div>
                            <div class="btn-group" role="group">
                                <button type="button" class="btn btn-outline-primary position-relative">
                                    <i class="fas fa-clock me-1"></i>
                                    待审核
                                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-primary">
                                        <?php echo $DB->count("SELECT COUNT(*) FROM dwz_withdraw WHERE status=0"); ?>
                                    </span>
                                </button>
                                <button type="button" class="btn btn-outline-success position-relative">
                                    <i class="fas fa-check-circle me-1"></i>
                                    已通过
                                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-success">
                                        <?php echo $DB->count("SELECT COUNT(*) FROM dwz_withdraw WHERE status=1"); ?>
                                    </span>
                                </button>
                                <button type="button" class="btn btn-outline-danger position-relative">
                                    <i class="fas fa-times-circle me-1"></i>
                                    已拒绝
                                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                        <?php echo $DB->count("SELECT COUNT(*) FROM dwz_withdraw WHERE status=2"); ?>
                                    </span>
                                </button>
                            </div>
                        </div>
                    </div>
                    <!-- 搜索表单 -->
                    <form method="GET" class="card-body border-bottom bg-light rounded-3 mb-3">
                        <div class="row g-3 align-items-center">
                            <div class="col-md-3">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="search_user" name="user" value="<?php echo htmlspecialchars($search_user); ?>" placeholder="搜索用户名">
                                    <label for="search_user"><i class="fas fa-user text-muted me-1"></i>用户名</label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-floating">
                                    <select class="form-select" id="search_status" name="status">
                                        <option value="-1" <?php echo $search_status==-1?'selected':''; ?>>全部状态</option>
                                        <option value="0" <?php echo $search_status==0?'selected':''; ?>>待审核</option>
                                        <option value="1" <?php echo $search_status==1?'selected':''; ?>>已通过</option>
                                        <option value="2" <?php echo $search_status==2?'selected':''; ?>>已拒绝</option>
                                    </select>
                                    <label for="search_status"><i class="fas fa-filter text-muted me-1"></i>状态</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="daterange" name="time" value="<?php echo htmlspecialchars($search_time); ?>" placeholder="选择时间范围">
                                    <label for="daterange"><i class="fas fa-calendar text-muted me-1"></i>申请时间</label>
                                </div>
                            </div>
                            <div class="col-md-2 d-grid">
                                <button type="submit" class="btn btn-primary btn-lg h-100">
                                    <i class="fas fa-search me-2"></i>搜索
                                </button>
                            </div>
                        </div>
                    </form>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover table-striped align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th class="text-center" style="width: 5%">ID</th>
                                        <th style="width: 12%">用户信息</th>
                                        <th class="text-end" style="width: 10%">提现金额</th>
                                        <th style="width: 15%">支付宝账号</th>
                                        <th style="width: 10%">支付宝姓名</th>
                                        <th style="width: 12%">申请时间</th>
                                        <th class="text-center" style="width: 8%">状态</th>
                                        <th style="width: 12%">处理时间</th>
                                        <th>备注</th>
                                        <th class="text-center" style="width: 10%">操作</th>
                                    </tr>
                                </thead>
                                <tbody class="align-middle">
                                <?php
                                while($res = $DB->fetch($rs)){
                                    $status_class = '';
                                    $status_text = '';
                                    switch($res['status']) {
                                        case 0:
                                            $status_class = 'bg-warning';
                                            $status_text = '待审核';
                                            break;
                                        case 1:
                                            $status_class = 'bg-success';
                                            $status_text = '已通过';
                                            break;
                                        case 2:
                                            $status_class = 'bg-danger';
                                            $status_text = '已拒绝';
                                            break;
                                    }
                                ?>
                                    <tr>
                                        <td class="text-center fw-bold"><?php echo $res['id']; ?></td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="flex-shrink-0">
                                                    <img src="../assets/img/avatar.jpg" class="rounded-circle" width="32" height="32">
                                                </div>
                                                <div class="flex-grow-1 ms-2">
                                                    <div class="fw-semibold"><?php echo htmlspecialchars($res['user']); ?></div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-end">
                                            <span class="fw-bold">¥<?php echo number_format($res['amount'], 2); ?></span>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <i class="fab fa-alipay text-primary me-2"></i>
                                                <?php echo htmlspecialchars($res['alipay_account']); ?>
                                            </div>
                                        </td>
                                        <td><?php echo htmlspecialchars($res['alipay_name']); ?></td>
                                        <td>
                                            <div class="d-flex align-items-center text-muted">
                                                <i class="far fa-clock me-1"></i>
                                                <small><?php echo $res['create_time']; ?></small>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge rounded-pill <?php echo $status_class; ?> px-3 py-2">
                                                <?php echo $status_text; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <small class="text-muted">
                                                <?php echo $res['process_time'] ? '<i class="far fa-calendar-check me-1"></i>'.$res['process_time'] : '-'; ?>
                                            </small>
                                        </td>
                                        <td>
                                            <?php if($res['remark']): ?>
                                                <span class="text-muted" data-bs-toggle="tooltip" title="<?php echo htmlspecialchars($res['remark']); ?>">
                                                    <i class="far fa-comment-dots me-1"></i>
                                                    <?php echo mb_strlen($res['remark']) > 20 ? mb_substr($res['remark'], 0, 20).'...' : $res['remark']; ?>
                                                </span>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center">
                                            <?php if($res['status'] == 0): ?>
                                            <div class="btn-group">
                                                <button class="btn btn-sm btn-outline-success" onclick="approve(<?php echo $res['id']; ?>)">
                                                    <i class="fas fa-check me-1"></i>通过
                                                </button>
                                                <button class="btn btn-sm btn-outline-danger" onclick="reject(<?php echo $res['id']; ?>)">
                                                    <i class="fas fa-times me-1"></i>拒绝
                                                </button>
                                            </div>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php } ?>
                                </tbody>
                            </table>
                        </div>
                        <!-- 分页 -->
                        <div class="d-flex justify-content-between align-items-center border-top pt-3 mt-4">
                            <div class="text-muted d-flex align-items-center">
                                <i class="fas fa-list-ul me-2"></i>
                                共 <span class="fw-bold mx-1"><?php echo $numrows; ?></span> 条记录
                                <span class="mx-2">|</span>
                                每页显示 <span class="fw-bold mx-1"><?php echo $pagesize; ?></span> 条
                            </div>
                            <?php if($pages > 1): ?>
                            <nav aria-label="提现记录分页" class="pagination-container">
                                <ul class="pagination pagination-separated mb-0">
                                    <?php
                                    // 计算要显示的页码范围
                                    $start = max(1, min($page - 2, $pages - 4));
                                    $end = min($pages, max($page + 2, 5));

                                    // 首页和上一页
                                    if ($page > 1) {
                                        echo '<li class="page-item">
                                                <a class="page-link rounded-start" href="?page=1" title="首页">
                                                    <i class="fas fa-angle-double-left"></i>
                                                </a>
                                              </li>';
                                        echo '<li class="page-item">
                                                <a class="page-link" href="?page='.($page-1).'" title="上一页">
                                                    <i class="fas fa-angle-left"></i>
                                                </a>
                                              </li>';
                                    }

                                    // 显示页码
                                    if($start > 1) {
                                        echo '<li class="page-item disabled">
                                                <span class="page-link">...</span>
                                              </li>';
                                    }

                                    for ($i = $start; $i <= $end; $i++) {
                                        if($i == $page) {
                                            echo '<li class="page-item active">
                                                    <span class="page-link">'.$i.'</span>
                                                  </li>';
                                        } else {
                                            echo '<li class="page-item">
                                                    <a class="page-link" href="?page='.$i.'">'.$i.'</a>
                                                  </li>';
                                        }
                                    }

                                    if($end < $pages) {
                                        echo '<li class="page-item disabled">
                                                <span class="page-link">...</span>
                                              </li>';
                                    }

                                    // 下一页和末页
                                    if ($page < $pages) {
                                        echo '<li class="page-item">
                                                <a class="page-link" href="?page='.($page+1).'" title="下一页">
                                                    <i class="fas fa-angle-right"></i>
                                                </a>
                                              </li>';
                                        echo '<li class="page-item">
                                                <a class="page-link rounded-end" href="?page='.$pages.'" title="末页">
                                                    <i class="fas fa-angle-double-right"></i>
                                                </a>
                                              </li>';
                                    }
                                    ?>
                                </ul>
                            </nav>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 页面底部JavaScript -->
    <script type="text/javascript" src="../static/admin/js/jquery.min.js"></script>
    <script type="text/javascript" src="../static/admin/js/popper.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script type="text/javascript" src="../static/admin/js/bootstrap-notify.min.js"></script>
    <script type="text/javascript" src="../static/admin/js/lyear-loading.js"></script>
    <script type="text/javascript" src="../static/admin/js/jquery-confirm/jquery-confirm.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

    <script>
        // 美化确认框和提示样式
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer)
                toast.addEventListener('mouseleave', Swal.resumeTimer)
            }
        });

        // 表单验证增强
        function validateForm(formData) {
            let errors = [];

            // 验证金额格式
            if(formData.amount && !/^\d+(\.\d{1,2})?$/.test(formData.amount)) {
                errors.push('金额格式不正确');
            }

            // 验证支付宝账号
            if(formData.alipay_account && !formData.alipay_account.trim()) {
                errors.push('支付宝账号不能为空');
            }

            return errors;
        }

        // 重新初始化所有tooltip
        function initTooltips() {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            });
        }

        // 刷新表格数据
        function refreshTable() {
            return new Promise((resolve) => {
                setTimeout(() => {
                    location.reload();
                    resolve();
                }, 1000);
            });
        }

        // 增强处理结果显示
        async function showEnhancedResult(result) {
            const icon = result.code === 0 ? 'success' : 'error';
            const title = result.code === 0 ? '操作成功' : '操作失败';

            await Swal.fire({
                icon: icon,
                title: title,
                text: result.msg,
                showConfirmButton: false,
                timer: 1500,
                customClass: {
                    popup: 'sweet-alert-custom'
                }
            });

            if(result.code === 0) {
                await refreshTable();
            }
        }

        function handleRequest(url, data) {
            return new Promise((resolve, reject) => {
                $.ajax({
                    url: url,
                    type: 'POST',
                    data: data,
                    dataType: 'json',
                    success: resolve,
                    error: reject
                });
            });
        }

        async function approve(id) {
            try {
                const { value: formValues } = await Swal.fire({
                    title: '确认通过提现申请',
                    html: `
                        <div class="text-start p-3">
                            <div class="alert alert-info bg-light border-0 mb-4">
                                <div class="d-flex mb-2">
                                    <i class="fas fa-info-circle text-info mt-1 me-2"></i>
                                    <strong>请确认已完成以下操作：</strong>
                                </div>
                                <div class="ps-4">
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="fas fa-check-circle text-success me-2"></i>
                                        <span>已完成支付宝转账操作</span>
                                    </div>
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="fas fa-check-circle text-success me-2"></i>
                                        <span>已核实转账金额无误</span>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-check-circle text-success me-2"></i>
                                        <span>已保存转账记录凭证</span>
                                    </div>
                                </div>
                            </div>
                            <div class="form-floating">
                                <input type="text" class="form-control" id="remark" placeholder="请输入备注信息">
                                <label for="remark">备注信息（选填）</label>
                            </div>
                        </div>
                    `,
                    showCancelButton: true,
                    confirmButtonText: '确认通过',
                    cancelButtonText: '取消',
                    confirmButtonColor: '#2ECC71',
                    cancelButtonColor: '#6c757d',
                    focusConfirm: false,
                    preConfirm: () => {
                        return {
                            remark: document.getElementById('remark').value
                        }
                    }
                });

                if (formValues) {
                    Swal.fire({
                        title: '处理中...',
                        html: `
                            <div class="text-center">
                                <div class="loading-spinner mb-3"></div>
                                <div class="text-muted">正在提交审核结果</div>
                            </div>
                        `,
                        showConfirmButton: false,
                        allowOutsideClick: false,
                        allowEscapeKey: false
                    });
                    const response = await handleRequest('ajax.php?act=withdraw_verify', {
                        id: id,
                        status: 1,
                        remark: formValues.remark
                    });

                    if(response.code === 1) {
                        Toast.fire({
                            icon: 'success',
                            title: '提现申请已通过'
                        });
                        setTimeout(() => location.reload(), 1500);
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: '操作失败',
                            text: response.msg,
                            confirmButtonColor: '#2ECC71'
                        });
                    }
                }
            } catch(error) {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: '系统错误',
                    text: '请稍后再试',
                    confirmButtonColor: '#2ECC71'
                });
            }
        }

        async function reject(id) {
            try {
                const { value: formValues } = await Swal.fire({
                    title: '确认拒绝提现申请',
                    html: `
                        <div class="text-start p-3">
                            <div class="alert alert-warning bg-light border-0 mb-4">
                                <div class="d-flex">
                                    <i class="fas fa-exclamation-triangle text-warning mt-1 me-2"></i>
                                    <div>
                                        <strong>注意事项：</strong>
                                        <div class="text-muted mt-1">拒绝后将自动退还用户积分，请确认操作</div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-floating">
                                <textarea class="form-control" id="remark" style="height: 100px" placeholder="请输入拒绝原因"></textarea>
                                <label for="remark">拒绝原因（必填）</label>
                            </div>
                        </div>
                    `,
                    showCancelButton: true,
                    confirmButtonText: '确认拒绝',
                    cancelButtonText: '取消',
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d',
                    focusConfirm: false,
                    preConfirm: () => {
                        const remark = document.getElementById('remark').value;
                        if (!remark.trim()) {
                            Swal.showValidationMessage('请输入拒绝原因')
                        }
                        return { remark }
                    }
                });

                if (formValues) {
                    Swal.fire({
                        title: '处理中...',
                        html: `
                            <div class="text-center">
                                <div class="loading-spinner mb-3"></div>
                                <div class="text-muted">正在提交审核结果</div>
                            </div>
                        `,
                        showConfirmButton: false,
                        allowOutsideClick: false,
                        allowEscapeKey: false
                    });
                    const response = await handleRequest('ajax.php?act=withdraw_verify', {
                        id: id,
                        status: 2,
                        remark: formValues.remark
                    });

                    if(response.code === -1) {
                        Toast.fire({
                            icon: 'success',
                            title: '提现申请已拒绝'
                        });
                        setTimeout(() => location.reload(), 1500);
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: '操作失败',
                            text: response.msg,
                            confirmButtonColor: '#dc3545'
                        });
                    }
                }
            } catch(error) {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: '系统错误',
                    text: '请稍后再试',
                    confirmButtonColor: '#dc3545'
                });
            }
        }

        // 页面加载完成后初始化
        $(document).ready(function() {
            // 初始化提示
            initTooltips();

            // 初始化日期选择器
            $('#daterange').daterangepicker({
                autoUpdateInput: false,
                locale: {
                    format: 'YYYY-MM-DD',
                    applyLabel: '确定',
                    cancelLabel: '取消',
                    fromLabel: '从',
                    toLabel: '至',
                    customRangeLabel: '自定义',
                    weekLabel: 'W',
                    daysOfWeek: ['日', '一', '二', '三', '四', '五', '六'],
                    monthNames: ['一月', '二月', '三月', '四月', '五月', '六月', '七月', '八月', '九月', '十月', '十一月', '十二月']
                },
                ranges: {
                   '今天': [moment(), moment()],
                   '昨天': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                   '近7天': [moment().subtract(6, 'days'), moment()],
                   '近30天': [moment().subtract(29, 'days'), moment()],
                   '本月': [moment().startOf('month'), moment().endOf('month')],
                   '上月': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                }
            });

            $('#daterange').on('apply.daterangepicker', function(ev, picker) {
                $(this).val(picker.startDate.format('YYYY-MM-DD') + ' - ' + picker.endDate.format('YYYY-MM-DD'));
            });

            $('#daterange').on('cancel.daterangepicker', function(ev, picker) {
                $(this).val('');
            });

            // 添加表单搜索动画
            $('.btn-primary[type="submit"]').on('click', function() {
                $(this).addClass('disabled').html('<span class="spinner-border spinner-border-sm me-2"></span>搜索中...');
                setTimeout(() => {
                    $(this).removeClass('disabled').html('<i class="fas fa-search me-2"></i>搜索');
                }, 500);
            });
        });
    </script>
</body>
</html>