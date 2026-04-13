<?php
include('../includes/common.php');
if ($islogin2 == 1) {
} else exit("<script language='javascript'>window.location.href='./login.php';</script>");
?>

<?php
$title = '充值中心';
include('head.php');
?>

<!-- 内容部分开始 -->
<div class="dashboard">
    <div class="row">
        <!-- 左侧面板 -->
        <div class="col-lg-6 mb-4">
            <!-- 购买商品卡片 -->
            <div class="content-card mb-4">
                <div class="content-card-header">
                    <h5 class="content-card-title">
                        <i class="bi bi-cart3"></i> 购买商品
                    </h5>
                </div>
                <div class="content-card-body">
                    <div class="mb-3">
                        <label class="form-label">选择商品</label>
                        <select id="number" class="form-select">
                            <option value="1">会员月卡(<?php echo $conf['vip_month'] ?>元)</option>
                            <option value="2">会员季卡(<?php echo $conf['vip_quarter'] ?>元)</option>
                            <option value="3">会员年卡(<?php echo $conf['vip_year'] ?>元)</option>
                            <option value="6">积分充值(<?php echo $conf['points_price'] ?>元/<?php echo $conf['points_num'] ?>积分)</option>
                        </select>
                        
                        <!-- 显示选中套餐 - 现代化设计 -->
                        <div id="selectedPackageContainer" class="mt-3" style="display:none;">
                            <div class="package-card">
                                <div class="package-info">
                                    <div class="package-icon">
                                        <i class="bi bi-gift"></i>
                                    </div>
                                    <div class="package-details">
                                        <div class="package-title">已选套餐</div>
                                        <div id="selectedPackageInfo" class="package-name"></div>
                                    </div>
                                </div>
                                <button type="button" id="reselectPackage" class="package-action-btn">
                                    <span class="btn-text">更换套餐</span>
                                    <span class="btn-icon"><i class="bi bi-shuffle"></i></span>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="mb-4">
                        <label class="form-label">购买数量</label>
                        <input type="number" class="form-control" value="1" id="num" min="1">
                        <!-- 隐藏字段存储套餐ID -->
                        <input type="hidden" id="packageId" value="0">
                    </div>
                    <div class="d-flex justify-content-center gap-2">
                        <?php
                        if ($conf['alipay_api']) echo '<button type="button" class="btn btn-primary" id="buy_alipay"><i class="bi bi-credit-card me-1"></i>支付宝支付</button>';
                        if ($conf['qqpay_api']) echo '<button type="button" class="btn btn-warning" id="buy_usdt"><i class="bi bi-credit-card me-1"></i>USDT支付</button>';
                        if ($conf['wxpay_api']) echo '<button type="button" class="btn btn-success" id="buy_wxpay"><i class="bi bi-credit-card me-1"></i>微信支付</button>';
                        ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- 右侧面板 -->
        <div class="col-lg-6">
            <!-- 充值记录卡片 -->
            <div class="content-card mb-4">
                <div class="content-card-header">
                    <h5 class="content-card-title">
                        <i class="bi bi-clock-history"></i> 充值记录
                    </h5>
                </div>
                <div class="content-card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>类型</th>
                                    <th>名称</th>
                                    <th>数量</th>
                                    <th>金额</th>
                                    <th>时间</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $rs = $DB->query("select * from dwz_points where uid='$uid' order by id desc limit 10");
                                if ($rs->num_rows == 0) {
                                    echo '<tr><td colspan="5" class="text-center">暂无充值记录</td></tr>';
                                } else {
                                    while ($res = $DB->fetch($rs)) {
                                        echo '<tr>
                                            <td>' . $res['action'] . '</td>
                                            <td>' . $res['bz'] . '</td>
                                            <td>' . $res['number'] . '</td>
                                            <td>' . $res['point'] . '元</td>
                                            <td>' . $res['addtime'] . '</td>
                                        </tr>';
                                    }
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- 短网址接口卡片 -->
            <div class="content-card">
                <div class="content-card-header">
                    <h5 class="content-card-title">
                        <i class="bi bi-link-45deg"></i> 短网址接口列表
                    </h5>
                </div>
                <div class="content-card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>名称</th>
                                    <th>接口类型</th>
                                    <th>价格</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $rs = $DB->query("select * from dwz_api where status=1 order by id desc");
                                if ($rs->num_rows == 0) {
                                    echo '<tr><td colspan="3" class="text-center">暂无接口，请联系管理员</td></tr>';
                                } else {
                                    while ($res = $DB->fetch($rs)) {
                                        echo '<tr>
                                            <td>' . $res['name'] . '</td>
                                            <td>' . $res['keyname'] . '</td>
                                            <td>' . $res['num'] . '点</td>
                                        </tr>';
                                    }
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- 内容部分结束 -->

<style>
    /* 与index.php保持相同的样式 */
    .dashboard {
        padding: 10px 5px;
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
    
    .form-label {
        font-weight: 500;
        color: var(--text-primary);
        margin-bottom: 8px;
    }
    
    /* 按钮样式 */
    .btn {
        font-weight: 500;
        border-radius: 8px;
        padding: 8px 16px;
        transition: all 0.3s;
    }
    
    .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    }
    
    .btn-primary {
        background-color: var(--primary);
        border-color: var(--primary);
    }
    
    .btn-primary:hover, .btn-primary:focus {
        background-color: var(--primary-dark);
        border-color: var(--primary-dark);
    }
    
    /* 表格样式 */
    .table {
        margin-bottom: 0;
    }
    
    .table th {
        font-weight: 600;
        white-space: nowrap;
    }
    
    .table-hover tbody tr:hover {
        background-color: rgba(40, 167, 69, 0.05);
    }
    
    .badge {
        font-weight: 500;
        padding: 5px 8px;
        border-radius: 4px;
    }
    
    /* 响应式调整 */
    @media (max-width: 576px) {
        .dashboard {
            padding: 5px;
        }
        
        .content-card-body {
            padding: 15px;
        }
        
        .table {
            white-space: nowrap;
        }
    }

/* 添加下面的CSS代码用于新的套餐选择界面 */

    /* 套餐卡片样式 */
    .package-card {
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: linear-gradient(145deg, #ffffff, #f0f0f0);
        border-radius: 16px;
        padding: 16px 20px;
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.05);
        transition: all 0.3s ease;
        border: 1px solid rgba(46, 204, 113, 0.1);
        overflow: hidden;
        position: relative;
    }
    
    .package-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 15px 30px rgba(46, 204, 113, 0.1);
    }
    
    .package-card::before {
        content: "";
        position: absolute;
        top: 0;
        left: 0;
        width: 6px;
        height: 100%;
        background: linear-gradient(to bottom, #2ecc71, #27ae60);
        border-radius: 3px;
    }
    
    .package-info {
        display: flex;
        align-items: center;
        gap: 15px;
    }
    
    .package-icon {
        width: 50px;
        height: 50px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(145deg, #2ecc71, #27ae60);
        border-radius: 12px;
        color: white;
        font-size: 24px;
        box-shadow: 0 4px 10px rgba(39, 174, 96, 0.3);
    }
    
    .package-details {
        display: flex;
        flex-direction: column;
    }
    
    .package-title {
        font-size: 13px;
        color: #7f8c8d;
        margin-bottom: 4px;
    }
    
    .package-name {
        font-size: 16px;
        font-weight: 600;
        color: #2c3e50;
    }
    
    .package-action-btn {
        display: flex;
        align-items: center;
        gap: 8px;
        background: transparent;
        border: 1px solid #2ecc71;
        color: #2ecc71;
        padding: 8px 16px;
        border-radius: 12px;
        font-weight: 500;
        font-size: 14px;
        cursor: pointer;
        transition: all 0.3s ease;
        outline: none;
    }
    
    .package-action-btn:hover {
        background: rgba(46, 204, 113, 0.1);
        transform: translateY(-2px);
    }
    
    .package-action-btn:active {
        transform: translateY(0);
    }
    
    .package-action-btn .btn-icon {
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .package-action-btn .btn-icon i {
        font-size: 18px;
    }
    
    /* 套餐选择动画 */
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    #selectedPackageContainer {
        animation: fadeInUp 0.5s ease forwards;
    }
    
    /* 响应式调整 */
    @media (max-width: 576px) {
        .package-card {
            flex-direction: column;
            gap: 15px;
            align-items: flex-start;
        }
        
        .package-action-btn {
            align-self: center;
            width: 100%;
            justify-content: center;
        }
    }
</style>

<script>
    function dopay(type) {
        var value = $("#number").val();
        var num = $("#num").val();
        
        // 检查是否为积分充值且已选择套餐
        if (value == '6') {
            var packageId = $("#packageId").val();
            if (packageId == 0) {
                Swal.fire({
                    icon: 'warning',
                    title: '提示',
                    text: '请先选择积分充值套餐',
                    confirmButtonText: '确定',
                    confirmButtonColor: '#28a745'
                });
                return;
            }
            // 使用packageId作为数量参数
            num = packageId;
        }
        
        console.log("支付参数:", {
            type: type, 
            value: value, 
            num: num, 
            isPackage: (value == '6')
        });
        
        // 显示加载中
        Swal.fire({
            title: '处理中',
            text: '正在创建支付订单...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
        
        $.ajax({
            url: "ajax.php?act=recharge&type=" + type + "&value=" + value + "&num=" + num,
            type: "GET",
            dataType: "json",
            success: function(data) {
                if (data.code == 0) {
                    window.location.href = '../other/submit.php?type=alipay&orderid=' + data.trade_no;
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: '创建订单失败',
                        text: data.msg,
                        confirmButtonText: '确定',
                        confirmButtonColor: '#28a745'
                    });
                }
            },
            error: function(xhr, status, error) {
                console.error("请求错误:", error);
                Swal.fire({
                    icon: 'error',
                    title: '创建订单失败',
                    text: '请求发生错误，请稍后再试',
                    confirmButtonText: '确定',
                    confirmButtonColor: '#28a745'
                });
            }
        });
    }
    
    $(document).ready(function() {
        // 支付按钮点击事件
        $("#buy_alipay").click(function() {
            dopay('alipay');
        });
        
        $("#buy_usdt").click(function() {
            dopay('usdt');
        });
        
        $("#buy_wxpay").click(function() {
            dopay('wxpay');
        });
        
        // 商品选择变化事件
        $("#number").change(function() {
            var value = $(this).val();
            
            // 如果不是积分充值，平滑隐藏套餐卡片
            if (value != '6') {
                if ($("#selectedPackageContainer").is(":visible")) {
                    $("#selectedPackageContainer").css({
                        "animation": "none",
                        "opacity": "1"
                    }).animate({
                        opacity: 0,
                        marginTop: "-10px"
                    }, 300, function() {
                        $(this).hide();
                        // 重置套餐ID
                        $("#packageId").val(0);
                    });
                }
            } else {
                // 如果选择积分充值，加载积分套餐
                showPointsPackages();
            }
        });
        
        // 重选套餐按钮点击事件
        $("#reselectPackage").click(function() {
            showPointsPackages();
        });
        
        // 显示积分套餐选择
        function showPointsPackages() {
            // 显示加载中
            Swal.fire({
                title: '加载中',
                text: '正在获取积分套餐列表...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            $.ajax({
                url: "ajax.php?act=get_points_packages",
                type: "GET",
                dataType: "json",
                success: function(data) {
                    console.log("获取积分套餐数据:", data);
                    
                    if (data.code == 0 && data.packages && data.packages.length > 0) {
                        // 保存套餐数据到全局变量便于后续使用
                        window.pointsPackages = data.packages;
                        
                        var options = '';
                        $.each(data.packages, function(i, item) {
                            options += '<option value="' + item.id + '">' + item.name + ' (' + item.points_num + '积分 / ' + item.price + '元)</option>';
                        });
                        
                        Swal.fire({
                            title: '选择积分套餐',
                            html: '<select id="points_package" class="form-select mb-3">' + options + '</select>',
                            showCancelButton: true,
                            confirmButtonText: '确定',
                            cancelButtonText: '取消',
                            confirmButtonColor: '#28a745'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                var packageId = $("#points_package").val();
                                console.log("选择的套餐ID:", packageId);
                                
                                // 找到选择的套餐信息
                                var selectedPackage = null;
                                for (var i = 0; i < window.pointsPackages.length; i++) {
                                    if (window.pointsPackages[i].id == packageId) {
                                        selectedPackage = window.pointsPackages[i];
                                        break;
                                    }
                                }
                                
                                if (selectedPackage) {
                                    // 保持商品类型为积分充值
                                    $("#number").val(6);
                                    // 存储套餐ID到隐藏字段
                                    $("#packageId").val(packageId);
                                    // 显示套餐信息
                                    $("#selectedPackageInfo").html(selectedPackage.name + 
                                                               ' <strong>' + selectedPackage.points_num + '积分</strong> / ' + 
                                                               '<span class="text-success">' + selectedPackage.price + '元</span>');
                                    // 显示套餐卡片容器
                                    $("#selectedPackageContainer").show();
                                }
                            } else {
                                // 用户取消，恢复默认商品
                                $("#number").val('1');
                            }
                        });
                    } else {
                        // 没有积分套餐或加载失败
                        Swal.fire({
                            icon: 'warning',
                            title: '提示',
                            text: '暂无可用的积分充值套餐',
                            confirmButtonText: '确定',
                            confirmButtonColor: '#28a745'
                        });
                        $("#number").val('1');
                    }
                },
                error: function(xhr, status, error) {
                    console.error("加载积分套餐失败:", error);
                    Swal.fire({
                        icon: 'error',
                        title: '错误',
                        text: '加载积分套餐失败，请稍后再试',
                        confirmButtonText: '确定',
                        confirmButtonColor: '#28a745'
                    });
                    $("#number").val('1');
                }
            });
        }
        
        // 数字输入框限制
        $("#num").on("input", function() {
            var value = $(this).val();
            if (value < 1) {
                $(this).val(1);
            }
        });
    });
</script>

<script>
window.onerror = function(message, source, lineno, colno, error) {
    console.log('捕获到错误：', {
        message: message,
        source: source,
        lineno: lineno,
        colno: colno,
        error: error
    });
    return false;
};
</script>