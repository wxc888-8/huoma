<?php
include('../includes/common.php');
if ($islogin2 == 1) {
} else exit("<script language='javascript'>window.location.href='./login.php';</script>");

// 设置页面标识符
$_GET['mod'] = 'qr-center';
$_GET['subpage'] = 'qr-edit';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$title = $id ? '编辑活码' : '创建新活码';
include('head.php');

// 如果是编辑模式，尝试获取活码数据
$qr_data = array();
if ($id) {
    try {
        // 直接从数据库查询所有数据
        $qr_info = $DB->get_row("SELECT * FROM dwz_qrcode WHERE id='{$id}' AND uid='{$uid}'");
        if ($qr_info) {
            $qr_data = $qr_info;
            
            // 获取活码对应的链接数据
            $qr_items = array();
            $rs = $DB->query("SELECT * FROM dwz_qrcode_data WHERE qid='{$id}' ORDER BY sort ASC");
            while($row = $DB->fetch($rs)) {
                $qr_items[] = $row;
            }
            $qr_data['items'] = $qr_items;
        }
    } catch (Exception $e) {
        // 记录错误但继续加载页面
        error_log("获取活码数据出错: " . $e->getMessage());
    }
}
?>

<section class="content-area py-4">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-lg-10 col-md-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="bi bi-<?php echo $id ? 'pencil-square' : 'plus-circle'; ?> me-2" style="color: var(--wechat-green);"></i><?php echo $title; ?></h5>
                    </div>
                    <div class="card-body">
                        <form id="qrForm" method="post">
                            <input type="hidden" name="id" value="<?php echo $id; ?>">
                            
                            <!-- 基本信息 -->
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h6 class="mb-0 d-flex align-items-center">
                                        <i class="bi bi-info-circle me-2" style="color: var(--wechat-green);"></i>基本信息
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="row mb-4">
                                        <label class="col-lg-2 col-md-3 col-form-label">活码名称</label>
                                        <div class="col-lg-8 col-md-9">
                                            <input type="text" class="form-control" name="name" placeholder="请输入活码名称" required>
                                        </div>
                                    </div>
    
                                    <div class="row mb-4">
                                        <label class="col-lg-2 col-md-3 col-form-label">入口域名</label>
                                        <div class="col-lg-8 col-md-9">
                                            <select class="form-select" name="entry_domain" id="entry_domain" required>
                                                <option value="">请选择入口域名</option>
                                            </select>
                                        </div>
                                    </div>
    
                                    <div class="row mb-4">
                                        <label class="col-lg-2 col-md-3 col-form-label">落地页域名</label>
                                        <div class="col-lg-8 col-md-9">
                                            <select class="form-select" name="landing_domain" id="landing_domain" required>
                                                <option value="">请选择落地页域名</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- 跳转设置 -->
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h6 class="mb-0 d-flex align-items-center">
                                        <i class="bi bi-arrow-repeat me-2" style="color: var(--wechat-green);"></i>跳转设置
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="row mb-4">
                                        <label class="col-lg-2 col-md-3 col-form-label">自动跳转时间</label>
                                        <div class="col-lg-8 col-md-9">
                                            <div class="input-group">
                                                <input type="number" class="form-control" name="jump_time" min="0" max="60" value="0" placeholder="自动跳转等待时间">
                                                <span class="input-group-text">秒</span>
                                            </div>
                                            <div class="form-text">设置为0表示不自动跳转</div>
                                        </div>
                                    </div>

                                    <div class="row mb-4">
                                        <label class="col-lg-2 col-md-3 col-form-label">安全认证提示</label>
                                        <div class="col-lg-8 col-md-9">
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="show_safe" id="show_safe_1" value="1" checked>
                                                <label class="form-check-label" for="show_safe_1">显示</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="show_safe" id="show_safe_0" value="0">
                                                <label class="form-check-label" for="show_safe_0">隐藏</label>
                                            </div>
                                            <div class="form-text">是否在跳转页面显示安全认证提示</div>
                                        </div>
                                    </div>

                                    <div class="row mb-4">
                                        <label class="col-lg-2 col-md-3 col-form-label">阈值类型</label>
                                        <div class="col-lg-8 col-md-9">
                                            <select class="form-select" name="threshold_type">
                                                <option value="1">顺序切换</option>
                                                <option value="2">随机切换</option>
                                                <option value="3">轮询切换</option>
                                            </select>
                                            <div class="form-text">内码切换的方式</div>
                                        </div>
                                    </div>

                                    <div class="row mb-4">
                                        <label class="col-lg-2 col-md-3 col-form-label">循环模式</label>
                                        <div class="col-lg-8 col-md-9">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="infinite_loop" id="infinite_loop" value="1">
                                                <label class="form-check-label" for="infinite_loop">
                                                    开启无限循环模式
                                                </label>
                                                <div class="form-text">启用后，阈值用完后会重新从第一个开始</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- 内码跳转列表 -->
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h6 class="mb-0 d-flex align-items-center">
                                        <i class="bi bi-list-ul me-2" style="color: var(--wechat-green);"></i>内码跳转网址列表
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div id="url_list" class="mb-4">
                                        <!-- URL项将通过JS动态添加 -->
                                    </div>
                                    <div class="text-center">
                                        <button type="button" class="btn btn-success" onclick="addUrlItem()">
                                            <i class="bi bi-plus-lg me-1"></i>添加跳转网址
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- 其他设置 -->
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h6 class="mb-0 d-flex align-items-center">
                                        <i class="bi bi-gear me-2" style="color: var(--wechat-green);"></i>其他设置
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="row mb-4">
                                        <label class="col-lg-2 col-md-3 col-form-label">备注说明</label>
                                        <div class="col-lg-8 col-md-9">
                                            <textarea class="form-control" name="remarks" rows="3" placeholder="请输入备注说明（可选）"></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- 提交按钮 -->
                            <div class="row">
                                <div class="col-lg-8 col-md-9 offset-lg-2 offset-md-3">
                                    <button type="submit" class="btn btn-primary" id="submitBtn">
                                        <i class="bi bi-save me-1"></i>保存
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary ms-2" onclick="history.back()">
                                        <i class="bi bi-arrow-left me-1"></i>返回
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
:root {
    --wechat-green: #07C160;
    --wechat-dark: #1A1A1A;
    --light-bg: #F7F7F7;
    --border-color: rgba(0, 0, 0, 0.05);
    --text-primary: #333;
    --text-secondary: #666;
    --card-shadow: 0 2px 12px rgba(0, 0, 0, 0.05);
    --sidebar-width: 240px;
    --topbar-height: 60px;
}

body {
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
    background-color: var(--light-bg);
    color: var(--text-primary);
}

.content-area {
    background-color: var(--light-bg);
    min-height: calc(100vh - var(--topbar-height));
    padding: 20px 0;
}

.card {
    border-radius: 8px;
    overflow: hidden;
    transition: all 0.3s ease;
    border: none;
    box-shadow: var(--card-shadow);
    margin-bottom: 20px;
}

.card-header {
    font-weight: 500;
    background-color: white;
    border-bottom: 1px solid var(--border-color);
    padding: 15px 20px;
}

.card-body {
    padding: 20px;
}

.form-control,
.form-select {
    border-radius: 6px;
    border: 1px solid var(--border-color);
    padding: 10px 12px;
    font-size: 14px;
    transition: all 0.2s ease;
}

.form-control:focus,
.form-select:focus {
    border-color: var(--wechat-green);
    box-shadow: 0 0 0 0.2rem rgba(7, 193, 96, 0.1);
}

.form-label, 
.col-form-label {
    font-weight: 500;
    font-size: 14px;
    color: var(--text-primary);
}

.btn {
    border-radius: 6px;
    font-weight: 500;
    padding: 8px 16px;
    transition: all 0.2s ease;
}

.btn-primary {
    background-color: var(--wechat-green);
    border-color: var(--wechat-green);
}

.btn-primary:hover,
.btn-primary:focus {
    background-color: #06b054;
    border-color: #06b054;
}

.btn-outline-secondary {
    color: var(--text-secondary);
    border-color: #ced4da;
}

.btn-outline-secondary:hover {
    background-color: #f8f9fa;
    color: var(--text-primary);
}

.btn-danger {
    background-color: #dc3545;
    border-color: #dc3545;
}

.btn-danger:hover {
    background-color: #c82333;
    border-color: #bd2130;
}

.btn-success {
    background-color: var(--wechat-green);
    border-color: var(--wechat-green);
}

.btn-success:hover {
    background-color: #06b054;
    border-color: #06b054;
}

/* 旋转动画 */
@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

.spin {
    animation: spin 1s linear infinite;
    display: inline-block;
}

/* 自定义表单样式 */
.form-text {
    font-size: 0.875rem;
    color: var(--text-secondary);
    margin-top: 5px;
}

.form-check-input:checked {
    background-color: var(--wechat-green);
    border-color: var(--wechat-green);
}

/* URL项样式 */
.url-item {
    background-color: white;
    border-radius: 8px;
    margin-bottom: 1.5rem;
    border: 1px solid var(--border-color);
    box-shadow: var(--card-shadow);
    transition: all 0.3s ease;
}

.url-item:hover {
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.url-item-header {
    padding: 12px 16px;
    background-color: rgba(0,0,0,0.02);
    border-bottom: 1px solid var(--border-color);
    border-radius: 8px 8px 0 0;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.url-index {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 32px;
    height: 32px;
    background-color: var(--wechat-green);
    color: white;
    border-radius: 50%;
    font-weight: 500;
    font-size: 14px;
    box-shadow: 0 2px 5px rgba(7, 193, 96, 0.2);
}

.url-item-content {
    padding: 20px;
}

/* 图片上传样式 */
.image-upload-container {
    display: flex;
    align-items: flex-start;
}

.image-upload-wrapper {
    position: relative;
    width: 120px;
    height: 120px;
    border: 2px dashed #dee2e6;
    border-radius: 8px;
    background-color: #f8f9fa;
    cursor: pointer;
    overflow: hidden;
    transition: all 0.2s ease;
}

.image-upload-wrapper:hover {
    border-color: var(--wechat-green);
    background-color: rgba(7, 193, 96, 0.1);
}

.image-upload-placeholder {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    color: var(--text-secondary);
}

.image-upload-placeholder i {
    font-size: 32px;
    margin-bottom: 8px;
    color: var(--wechat-green);
}

.image-upload-input {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    opacity: 0;
    cursor: pointer;
    z-index: 2;
}

.image-preview {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    object-fit: contain;
    z-index: 1;
}

.image-clear-btn {
    margin-left: 10px;
}

/* 新增 - 图片上传包装器尺寸 */
.image-upload-wrapper {
    width: 200px;  /* 增加尺寸 */
    height: 200px;
}

@media (max-width: 768px) {
    .col-form-label {
        text-align: left;
        margin-bottom: 5px;
    }
    
    .card-body {
        padding: 15px;
    }
    
    .url-item-content {
        padding: 15px;
    }
}
</style>

<script>
<?php if($id && !empty($qr_data)): ?>
// 编辑模式：为JavaScript设置qr_data变量
var qr_data = <?php echo json_encode($qr_data); ?>;
<?php endif; ?>

// 载入入口域名和落地域名数据
function loadDomains() {
    // 入口域名
    $.ajax({
        url: 'ajax.php?act=get_domains&type=entry',
        type: 'GET',
        dataType: 'json',
        success: function(res) {
            if (res.code === 0) {
                var $select = $('#entry_domain');
                
                // 先添加积分域名
                if (res.data.free.length > 0) {
                    var $optgroup = $('<optgroup label="积分域名"></optgroup>');
                    $.each(res.data.free, function(i, item) {
                        $optgroup.append($('<option></option>').val(item.domain).text(item.domain));
                    });
                    $select.append($optgroup);
                }
                
                // 再添加付费域名
                if (res.data.paid.length > 0) {
                    var $optgroup = $('<optgroup label="付费域名"></optgroup>');
                    $.each(res.data.paid, function(i, item) {
                        $optgroup.append($('<option></option>').val(item.domain).text(item.domain));
                    });
                    $select.append($optgroup);
                }
                
                // 如果是编辑模式，设置选中的值
                <?php if($id && !empty($qr_data)): ?>
                if (qr_data.entry_domain) {
                    // 改进域名匹配逻辑
                    // 提取原始入口域名的主域名（去除子域名前缀和协议）
                    var originalDomain = qr_data.entry_domain;
                    var mainDomain = extractMainDomain(originalDomain);
                    
                    console.log("原始入口域名:", originalDomain);
                    console.log("提取的入口主域名:", mainDomain);
                    
                    // 添加一个特殊选项来保留原始域名
                    $select.prepend($('<option></option>')
                        .val(mainDomain)
                        .text("[保留原域名] " + originalDomain)
                        .prop('selected', true)
                        .attr('data-original', originalDomain));
                    
                    // 标记当前使用的是原始域名
                    $select.attr('data-using-original', 'true');
                    $select.attr('data-original-domain', originalDomain);
                    
                    function extractMainDomain(domain) {
                        // 移除协议前缀
                        domain = domain.replace(/^https?:\/\//, '');
                        
                        // 分割域名部分
                        var parts = domain.split('.');
                        
                        // 如果有二级域名前缀，返回主域名
                        if (parts.length > 2) {
                            return parts.slice(parts.length - 2).join('.');
                        }
                        
                        return domain;
                    }
                }
                <?php endif; ?>
            } else {
                layer.msg('加载入口域名失败：' + res.msg, {icon: 2});
            }
        },
        error: function() {
            layer.msg('网络错误，请刷新页面重试', {icon: 2});
        }
    });
    
    // 落地域名
    $.ajax({
        url: 'ajax.php?act=get_domains&type=landing',
        type: 'GET',
        dataType: 'json',
        success: function(res) {
            if (res.code === 0) {
                var $select = $('#landing_domain');
                
                // 先添加积分域名
                if (res.data.free.length > 0) {
                    var $optgroup = $('<optgroup label="积分域名"></optgroup>');
                    $.each(res.data.free, function(i, item) {
                        $optgroup.append($('<option></option>').val(item.domain).text(item.domain));
                    });
                    $select.append($optgroup);
                }
                
                // 再添加付费域名
                if (res.data.paid.length > 0) {
                    var $optgroup = $('<optgroup label="付费域名"></optgroup>');
                    $.each(res.data.paid, function(i, item) {
                        $optgroup.append($('<option></option>').val(item.domain).text(item.domain));
                    });
                    $select.append($optgroup);
                }
                
                // 如果是编辑模式，设置选中的值
                <?php if($id && !empty($qr_data)): ?>
                if (qr_data.landing_domain) {
                    // 改进域名匹配逻辑
                    // 提取原始落地域名的主域名（去除子域名前缀和协议）
                    var originalDomain = qr_data.landing_domain;
                    var mainDomain = extractMainDomain(originalDomain);
                    
                    console.log("原始落地域名:", originalDomain);
                    console.log("提取的落地主域名:", mainDomain);
                    
                    // 添加一个特殊选项来保留原始域名
                    $select.prepend($('<option></option>')
                        .val(mainDomain)
                        .text("[保留原域名] " + originalDomain)
                        .prop('selected', true)
                        .attr('data-original', originalDomain));
                    
                    // 标记当前使用的是原始域名
                    $select.attr('data-using-original', 'true');
                    $select.attr('data-original-domain', originalDomain);
                    
                    function extractMainDomain(domain) {
                        // 移除协议前缀
                        domain = domain.replace(/^https?:\/\//, '');
                        
                        // 分割域名部分
                        var parts = domain.split('.');
                        
                        // 如果有二级域名前缀，返回主域名
                        if (parts.length > 2) {
                            return parts.slice(parts.length - 2).join('.');
                        }
                        
                        return domain;
                    }
                }
                <?php endif; ?>
            } else {
                layer.msg('加载落地域名失败：' + res.msg, {icon: 2});
            }
        },
        error: function() {
            layer.msg('网络错误，请刷新页面重试', {icon: 2});
        }
    });
}

// 生成随机字符串，用于二级域名
function generateRandomSubdomain(minLength, maxLength) {
    const chars = 'abcdefghijklmnopqrstuvwxyz0123456789';
    const length = Math.floor(Math.random() * (maxLength - minLength + 1)) + minLength;
    let result = '';
    
    for (let i = 0; i < length; i++) {
        result += chars.charAt(Math.floor(Math.random() * chars.length));
    }
    
    return result;
}

// 生成随机8位标识码，包含数字、字母和特殊符号
function generateRandomQrCode(type) {
    // 生成8位随机字符串部分
    let string_part = '';
    // 修改字符集，使其包含更多特殊符号，参考格式G.U$YC3U
    let string_chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789.$!@#$%^&*()-_=+';
    
    // 可以根据type参数来控制字符串部分的字符集
    if (type === 'alpha') {
        string_chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
    } else if (type === 'alphanumeric') {
        string_chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
    }
    
    // 生成8位随机字符串
    for (let i = 0; i < 8; i++) {
        string_part += string_chars.charAt(Math.floor(Math.random() * string_chars.length));
    }
    
    // 生成8位随机数字部分
    let number_part = '';
    for (let i = 0; i < 8; i++) {
        number_part += Math.floor(Math.random() * 10);
    }
    
    // 组合为最终格式：随机字符串/随机数字
    return string_part + '/' + number_part;
}

// 页面加载完成后初始化表单数据
$(document).ready(function() {
    // 加载域名数据
    loadDomains();
    
    // 添加域名选择框的事件监听器
    $('#entry_domain, #landing_domain').on('change', function() {
        var $select = $(this);
        var selectedValue = $select.val();
        var isEntryDomain = $select.attr('id') === 'entry_domain';
        
        // 如果当前正在使用原始域名且选择了新的值
        if ($select.attr('data-using-original') === 'true' && selectedValue) {
            // 获取原始域名
            var originalDomain = $select.attr('data-original-domain');
            
            // 显示确认对话框
            var message = '您确定要将' + (isEntryDomain ? '入口域名' : '落地域名') + 
                        '从"' + originalDomain + '"更改为"' + selectedValue + '"吗？\n\n' + 
                        '这可能会导致现有的二维码失效。';
            
            if (!confirm(message)) {
                // 用户取消，恢复选择原始域名的选项
                $select.find('option[data-original="' + originalDomain + '"]').prop('selected', true);
                return false;
            }
            
            // 用户确认更改，移除标记
            $select.removeAttr('data-using-original');
        }
    });
    
    <?php if($id && !empty($qr_data)): ?>
    // 编辑模式：加载现有数据
    // 已在页面开始处定义了全局qr_data变量，此处不需要重复定义
    
    // 基本信息填充
    $('input[name="name"]').val(qr_data.name);
    
    // 跳转设置
    $('input[name="jump_time"]').val(parseInt(qr_data.jump_time));
    $('input[name="show_safe"][value="' + parseInt(qr_data.show_safe) + '"]').prop('checked', true);
    $('select[name="threshold_type"]').val(parseInt(qr_data.threshold_type));
    $('input[name="infinite_loop"]').prop('checked', parseInt(qr_data.infinite_loop) ? true : false);
    
    // 备注
    $('textarea[name="remarks"]').val(qr_data.remarks);
    
    // 清空默认URL项
    $('#url_list').empty();
    
    <?php if(!empty($qr_data['items'])): ?>
    // 添加URL项
    <?php foreach($qr_data['items'] as $index => $item): ?>
    addUrlItem(
        <?php echo $index + 1; ?>, 
        '<?php echo addslashes($item['jump_url']); ?>', 
        <?php echo !empty($item['image_url']) ? '"'.addslashes($item['image_url']).'"' : 'null'; ?>,
        <?php echo intval($item['threshold']); ?>
    );
    <?php endforeach; ?>
    <?php else: ?>
    // 如果没有URL项，添加一个默认的
    addUrlItem();
    <?php endif; ?>
    
    <?php else: ?>
    // 新建模式：使用默认值
    <?php endif; ?>
});

// 添加跳转URL项
function addUrlItem(index, jump_url, image_url, threshold) {
    // 如果没有指定索引，则使用当前项数量
    if (index === undefined) index = $('.url-item').length + 1;
    
    // 如果没有提供值，使用默认值
    jump_url = jump_url || '';
    image_url = image_url || '';
    threshold = threshold || 100;
    
    // 改进图片URL存在的判断逻辑
    var hasImage = image_url && image_url.trim() !== '';
    
    var html = '<div class="url-item">' +
               '<div class="url-item-header">' +
               '<span class="url-index">' + index + '</span>' +
               '<button type="button" class="btn btn-danger btn-sm remove-url" onclick="removeUrlItem(this)">' +
               '<i class="bi bi-trash"></i>' +
               '</button>' +
               '</div>' +
               '<div class="url-item-content">' +
               '<div class="row mb-4">' +
               '<div class="col-md-7">' +
               '<div class="mb-3">' +
               '<label class="form-label"><i class="bi bi-link me-1" style="color: var(--wechat-green);"></i>跳转地址</label>' +
               '<input type="url" class="form-control" name="jump_url[]" value="' + jump_url + '" placeholder="请输入跳转URL地址" required>' +
               '</div>' +
               '</div>' +
               '<div class="col-md-3">' +
               '<div class="mb-3">' +
               '<label class="form-label"><i class="bi bi-speedometer2 me-1" style="color: var(--wechat-green);"></i>阈值</label>' +
               '<input type="number" class="form-control" name="threshold[]" min="1" value="' + threshold + '" placeholder="阈值">' +
               '</div>' +
               '</div>' +
               '</div>' +
               '<div class="row">' +
               '<div class="col-md-7">' +
               '<div class="mb-3">' +
               '<label class="form-label"><i class="bi bi-image me-1" style="color: var(--wechat-green);"></i>展示图片</label>' +
               '<div class="image-upload-container">' +
               '<div class="image-upload-wrapper">' +
               '<div class="image-upload-placeholder"' + (hasImage ? ' style="display:none;"' : '') + '>' +
               '<i class="bi bi-image"></i>' +
               '<span>点击上传图片</span>' +
               '</div>' +
               '<img class="image-preview" src="' + image_url + '"' + (hasImage ? '' : ' style="display:none;"') + '>' +
               '<input type="file" class="image-upload-input" name="image_file[]" accept="image/*" onchange="previewImage(this)">' +
               '<input type="hidden" name="image_url[]" value="' + (image_url || '') + '">' +
               '</div>' +
               '<button type="button" class="btn btn-sm btn-danger image-clear-btn" onclick="clearImage(this)"' + (hasImage ? '' : ' style="display:none;"') + '>' +
               '<i class="bi bi-x"></i>' +
               '</button>' +
               '</div>' +
               '</div>' +
               '</div>' +
               '</div>' +
               '</div>';
    $('#url_list').append(html);
    
    // 滚动到新添加的元素
    if (!image_url && !jump_url) { // 只在添加新空白项时滚动
    $('html, body').animate({
        scrollTop: $('#url_list .url-item:last-child').offset().top - 100
    }, 500);
    }
}

// 移除跳转URL项
function removeUrlItem(btn) {
    // 确保至少保留一个URL项
    if ($('.url-item').length > 1) {
        $(btn).closest('.url-item').remove();
        
        // 重新编号
        $('.url-item').each(function(index) {
            $(this).find('.url-index').text(index + 1);
        });
    } else {
        layer.msg('至少需要保留一个跳转地址', {icon: 2});
    }
}

// 预览图片
function previewImage(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        var container = $(input).closest('.image-upload-container');
        var preview = container.find('.image-preview');
        var placeholder = container.find('.image-upload-placeholder');
        var clearBtn = container.find('.image-clear-btn');
        var hiddenInput = $(input).next('input[type="hidden"]');
        
        // 找到当前URL项的跳转地址输入框
        var urlItem = $(input).closest('.url-item');
        var jumpUrlInput = urlItem.find('input[name="jump_url[]"]');
        
        reader.onload = function(e) {
            // 执行图片压缩处理，避免base64数据过大
            compressImage(e.target.result, function(compressedImageData) {
                // 设置隐藏字段的值为压缩后的base64图片数据（用于表单提交）
                hiddenInput.val(compressedImageData);
                
                // 显示预览图，隐藏占位符
                preview.attr('src', compressedImageData);
                preview.show();
                placeholder.hide();
                
                // 显示清除按钮
                clearBtn.show();
                
                // 尝试识别二维码
                try {
                    var img = new Image();
                    img.onload = function() {
                        // 创建多个尺寸的canvas尝试解码
                        var tryDecode = function(width, height) {
                            // 创建Canvas元素
                            var canvas = document.createElement('canvas');
                            var context = canvas.getContext('2d');
                            canvas.width = width || img.width;
                            canvas.height = height || img.height;
                            
                            // 在Canvas上绘制图片
                            context.drawImage(img, 0, 0, canvas.width, canvas.height);
                            
                            // 获取图像数据
                            var imageData = context.getImageData(0, 0, canvas.width, canvas.height);
                            
                            // 使用jsQR库解析二维码
                            return jsQR(imageData.data, imageData.width, imageData.height, {
                                inversionAttempts: "dontInvert",
                            });
                        };
                        
                        // 尝试多种尺寸和设置来提高识别率
                        var code = tryDecode(); // 原始尺寸
                        
                        if (!code) {
                            code = tryDecode(img.width * 1.5, img.height * 1.5); // 放大
                        }
                        
                        if (!code) {
                            code = tryDecode(800, 800); // 固定尺寸
                        }
                        
                        if (!code) {
                            // 最后尝试不同参数
                            var canvas = document.createElement('canvas');
                            var context = canvas.getContext('2d');
                            canvas.width = img.width;
                            canvas.height = img.height;
                            context.drawImage(img, 0, 0, img.width, img.height);
                            var imageData = context.getImageData(0, 0, canvas.width, canvas.height);
                            
                            code = jsQR(imageData.data, imageData.width, imageData.height, {
                                inversionAttempts: "attemptBoth",
                            });
                        }
                        
                        // 如果识别到二维码数据
                        if (code && code.data) {
                            console.log("识别到二维码:", code.data);
                            
                            // 检查是否是有效URL
                            if (isValidURL(code.data)) {
                                // 设置跳转地址
                                jumpUrlInput.val(code.data);
                                
                                // 显示成功提示 - 使用弹出气泡而不是全局layer
                                var successTip = $('<div class="alert alert-success" style="margin-top:10px;padding:5px 10px;font-size:12px;">' +
                                                   '<i class="bi bi-check-circle"></i> 已自动识别二维码并填充URL</div>');
                                
                                // 先移除可能存在的其他提示
                                urlItem.find('.alert').remove();
                                
                                // 添加到跳转地址输入框后面
                                jumpUrlInput.after(successTip);
                                
                                // 3秒后自动淡出
                                setTimeout(function() {
                                    successTip.fadeOut(function() {
                                        $(this).remove();
                                    });
                                }, 3000);
                            } else {
                                // 提示识别到非URL内容
                                var warningTip = $('<div class="alert alert-warning" style="margin-top:10px;padding:5px 10px;font-size:12px;">' +
                                                  '<i class="bi bi-exclamation-circle"></i> 识别到内容不是有效URL</div>');
                                
                                // 先移除可能存在的其他提示
                                urlItem.find('.alert').remove();
                                
                                // 添加到跳转地址输入框后面
                                jumpUrlInput.after(warningTip);
                                
                                // 3秒后自动淡出
                                setTimeout(function() {
                                    warningTip.fadeOut(function() {
                                        $(this).remove();
                                    });
                                }, 3000);
                            }
                        } else {
                            // 未识别到二维码
                            console.log("未能识别二维码");
                            
                            // 可选：显示未识别提示
                            var errorTip = $('<div class="alert alert-danger" style="margin-top:10px;padding:5px 10px;font-size:12px;">' +
                                            '<i class="bi bi-exclamation-triangle"></i> 未能识别图片中的二维码</div>');
                            
                            // 先移除可能存在的其他提示
                            urlItem.find('.alert').remove();
                            
                            // 添加到跳转地址输入框后面
                            jumpUrlInput.after(errorTip);
                            
                            // 3秒后自动淡出
                            setTimeout(function() {
                                errorTip.fadeOut(function() {
                                    $(this).remove();
                                });
                            }, 3000);
                        }
                    };
                    
                    img.src = compressedImageData;
                } catch (error) {
                    console.error("二维码解析错误:", error);
                }
            });
        };
        
        reader.readAsDataURL(input.files[0]);
    }
}

// 压缩图片函数 - 更强的压缩率和更小的尺寸
function compressImage(base64, callback, maxWidth = 400, maxHeight = 400, quality = 0.6) {
    var img = new Image();
    img.onload = function() {
        var width = img.width;
        var height = img.height;
        
        // 计算缩放比例
        var scale = 1;
        if (width > maxWidth || height > maxHeight) {
            scale = Math.min(maxWidth / width, maxHeight / height);
        }
        
        // 创建Canvas
        var canvas = document.createElement('canvas');
        canvas.width = width * scale;
        canvas.height = height * scale;
        
        // 绘制压缩后的图片
        var ctx = canvas.getContext('2d');
        ctx.drawImage(img, 0, 0, canvas.width, canvas.height);
        
        // 转换为base64，使用降低的质量
        var compressedBase64 = canvas.toDataURL('image/jpeg', quality);
        
        // 如果压缩后仍然过大，进一步降低质量和尺寸
        if (compressedBase64.length > 200000) { // 如果大于约200KB
            compressImage(compressedBase64, callback, maxWidth * 0.8, maxHeight * 0.8, quality * 0.8);
        } else {
            callback(compressedBase64);
        }
    };
    img.src = base64;
}

// 判断字符串是否是有效URL
function isValidURL(str) {
    var pattern = new RegExp('^(https?:\\/\\/)?'+ // 协议
        '((([a-z\\d]([a-z\\d-]*[a-z\\d])*)\\.)+[a-z]{2,}|'+ // 域名
        '((\\d{1,3}\\.){3}\\d{1,3}))'+ // 或IP地址
        '(\\:\\d+)?(\\/[-a-z\\d%_.~+]*)*'+ // 端口和路径
        '(\\?[;&a-z\\d%_.~+=-]*)?'+ // 查询字符串
        '(\\#[-a-z\\d_]*)?$','i'); // 片段标识符
    return !!pattern.test(str);
}

// 清除图片
function clearImage(btn) {
    var container = $(btn).closest('.image-upload-container');
    var wrapper = container.find('.image-upload-wrapper');
    var input = wrapper.find('input[type="file"]');
    var hiddenInput = wrapper.find('input[type="hidden"]');
    var preview = wrapper.find('.image-preview');
    var placeholder = wrapper.find('.image-upload-placeholder');
    
    // 清除文件输入
    input.val('');
    
    // 清除隐藏值
    hiddenInput.val('');
    
    // 隐藏预览图，显示占位符
    preview.attr('src', '').hide();
    placeholder.show();
    
    // 隐藏清除按钮
    $(btn).hide();
}

// 表单提交
$(document).ready(function() {
    $('#qrForm').submit(function(e) {
        e.preventDefault();
        
        // 表单验证
        if (!validateForm()) {
            return false;
        }
        
        // 禁用提交按钮，防止重复提交
        $('#submitBtn').prop('disabled', true).html('<i class="bi bi-arrow-clockwise spin"></i> 提交中...');
        
        // 使用异步处理确保所有图片压缩完成后再提交
        var formData = new FormData(this);
        var imagesToProcess = $('input[name="image_url[]"]').filter(function() {
            return $(this).val() && $(this).val().startsWith('data:image');
        }).length;
        
        if (imagesToProcess === 0) {
            // 没有图片需要处理，直接提交
            submitFormData(formData);
            return;
        }
        
        // 遍历处理所有图片
        $('input[name="image_url[]"]').each(function(index) {
            var $input = $(this);
            var imageData = $input.val();
            
            if (imageData && imageData.startsWith('data:image')) {
                // 强制对所有图片进行压缩
                compressImage(imageData, function(compressed) {
                    console.log('图片 #' + index + ' 压缩前: ' + Math.round(imageData.length/1024) + 'KB, 压缩后: ' + Math.round(compressed.length/1024) + 'KB');
                    
                    // 更新FormData中的图片数据
                    formData.set('image_url[' + index + ']', compressed);
                    
                    // 更新隐藏字段的值（这很重要，确保DOM中也更新了）
                    $input.val(compressed);
                    
                    // 减少待处理图片计数
                    imagesToProcess--;
                    
                    // 所有图片处理完成后提交表单
                    if (imagesToProcess === 0) {
                        submitFormData(formData);
                    }
                }, 400, 400, 0.5); // 使用更激进的压缩参数
            }
        });
    });
    
    // 单独的函数处理表单数据提交
    function submitFormData(formData) {
        // 添加随机二级域名前缀到入口域名和落地域名
        var entryDomain = $('select[name="entry_domain"]').val();
        var landingDomain = $('select[name="landing_domain"]').val();
        
        // 生成随机标识码（如果是新建模式）
        <?php if(!$id): ?>
        // 对于新建活码，生成随机标识码
        var qrCodeType = 'mixed'; // 可以设置为 'mixed' 或 'number'
        var randomQrCode = generateRandomQrCode(qrCodeType);
        formData.append('qr_code', randomQrCode);
        
        // 新建模式：生成两个不同的随机二级域名前缀
        var entrySubdomain = generateRandomSubdomain(5, 8);
        var landingSubdomain = generateRandomSubdomain(5, 8);
        
        // 记录原始域名值
        var originalEntryDomain = entryDomain;
        var originalLandingDomain = landingDomain;
        
        // 确保域名前添加http://前缀
        var fullEntryDomain = 'http://' + entrySubdomain + '.' + entryDomain;
        var fullLandingDomain = 'http://' + landingSubdomain + '.' + landingDomain;
        
        // 将二级域名添加到域名中，替换FormData中的值
        formData.set('entry_domain', entrySubdomain + '.' + entryDomain);
        formData.set('landing_domain', landingSubdomain + '.' + landingDomain);
        
        // 添加原始域名值到FormData，以供服务器端参考
        formData.append('original_entry_domain', originalEntryDomain);
        formData.append('original_landing_domain', originalLandingDomain);
        
        // 添加完整URL前缀，用于二维码生成
        formData.append('full_entry_domain', fullEntryDomain);
        formData.append('full_landing_domain', fullLandingDomain);
        
        console.log("添加随机二级域名: 入口域名=" + fullEntryDomain + 
                    ", 落地域名=" + fullLandingDomain);
        <?php endif; ?>
        
        // 在编辑模式下，检查是否保留原始域名
        <?php if($id && !empty($qr_data)): ?>
        // 编辑模式：改进域名处理逻辑，确保保留原始域名
        var originalEntryDomain = '<?php echo addslashes($qr_data["entry_domain"]); ?>';
        var originalLandingDomain = '<?php echo addslashes($qr_data["landing_domain"]); ?>';
        
        console.log("原始入口域名:", originalEntryDomain);
        console.log("原始落地域名:", originalLandingDomain);
        console.log("选择的入口域名:", entryDomain);
        console.log("选择的落地域名:", landingDomain);
        
        // 添加原始域名值到FormData，以供服务器端参考
        formData.append('original_entry_domain', originalEntryDomain);
        formData.append('original_landing_domain', originalLandingDomain);
        
        // 检查选择的域名是否与原始域名的主域名部分相同
        // 提取原始域名的主域名部分（去除二级域名前缀）
        function extractMainDomain(domain) {
            // 检查是否有http://或https://前缀，如果有则去除
            domain = domain.replace(/^https?:\/\//, '');
            
            // 分割域名部分
            var parts = domain.split('.');
            
            // 如果域名部分大于2，说明有二级域名前缀
            if (parts.length > 2) {
                // 返回最后两个部分（主域名）
                return parts.slice(parts.length - 2).join('.');
            }
            
            return domain;
        }
        
        var originalEntryMainDomain = extractMainDomain(originalEntryDomain);
        var originalLandingMainDomain = extractMainDomain(originalLandingDomain);
        
        console.log("原始入口主域名:", originalEntryMainDomain);
        console.log("原始落地主域名:", originalLandingMainDomain);
        
        // 如果选择的域名与原始主域名相同或为空，标记保留原始域名
        var keepOriginalEntry = entryDomain === originalEntryMainDomain || entryDomain === originalEntryDomain || entryDomain === '';
        var keepOriginalLanding = landingDomain === originalLandingMainDomain || landingDomain === originalLandingDomain || landingDomain === '';
        
        formData.append('keep_original_entry', keepOriginalEntry ? '1' : '0');
        formData.append('keep_original_landing', keepOriginalLanding ? '1' : '0');
        
        console.log("保留原始入口域名:", keepOriginalEntry);
        console.log("保留原始落地域名:", keepOriginalLanding);
        
        // 如果保留原始域名，使用原始域名值
        if (keepOriginalEntry) {
            formData.set('entry_domain', originalEntryDomain);
        }
        
        if (keepOriginalLanding) {
            formData.set('landing_domain', originalLandingDomain);
        }
        <?php endif; ?>
        
        // 添加随机参数避免缓存
        var randomParam = new Date().getTime();
        
        // AJAX提交表单
        $.ajax({
            url: 'ajax.php?act=save_qr&noCache=' + randomParam,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(res) {
                if (res.code == 0) {
                    alert('保存成功');
                    setTimeout(function() {
                        location.href = 'qr-list.php';
                    }, 1000);
                } else {
                    alert('保存失败：' + res.msg);
                    $('#submitBtn').prop('disabled', false).html('<i class="bi bi-save me-1"></i>保存');
                }
            },
            error: function(xhr, status, error) {
                console.error("AJAX错误:", status, error);
                console.log("响应文本:", xhr.responseText);
                alert('网络错误，请重试');
                $('#submitBtn').prop('disabled', false).html('<i class="bi bi-save me-1"></i>保存');
            }
        });
    }
});

// 表单验证
function validateForm() {
    // 验证活码名称
    if (!$('input[name="name"]').val()) {
        layer.msg('请输入活码名称', {icon: 2});
        return false;
    }
    
    // 验证入口域名
    if (!$('select[name="entry_domain"]').val()) {
        layer.msg('请选择入口域名', {icon: 2});
        return false;
    }
    
    // 验证落地页域名
    if (!$('select[name="landing_domain"]').val()) {
        layer.msg('请选择落地页域名', {icon: 2});
        return false;
    }
    
    // 验证跳转地址
    var hasEmptyUrl = false;
    $('input[name="jump_url[]"]').each(function() {
        if (!$(this).val()) {
            hasEmptyUrl = true;
            return false;
        }
    });
    
    if (hasEmptyUrl) {
        layer.msg('请填写所有跳转地址', {icon: 2});
        return false;
    }
    
    return true;
}
</script>

<!-- 添加jsQR库 -->
<script src="https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.min.js"></script>

<script src="../static/user/js/common-scripts.js"></script>

<!-- 添加Bootstrap 5和必要的脚本 -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
<!-- 确保jQuery已正确加载 -->
<script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
<!-- 添加layer.js引用 - 放在jQuery之后 -->
<script src="https://cdn.jsdelivr.net/npm/layer-src@3.5.1/src/layer.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.min.js"></script>
<script src="../static/user/js/common-scripts.js"></script>
</body>
</html> 