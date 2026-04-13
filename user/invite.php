<?php
include('../includes/common.php');
if ($islogin2 == 1) {
} else exit("<script language='javascript'>window.location.href='./login.php';</script>");

// 设置页面标识符
$_GET['mod'] = 'invite';

$title = '邀请管理';
include('head.php');

// 检查邀请系统相关的表是否存在
$tables_needed = ['dwz_invite_code', 'dwz_invite_record', 'dwz_consume_reward'];
$tables_missing = [];

foreach ($tables_needed as $table) {
    $query = "SHOW TABLES LIKE '{$table}'";
    $result = $DB->query($query);
    
    if ($result === false) {
        // 查询本身失败
        echo "<div class='alert alert-danger'>检查表 {$table} 时发生错误: " . $DB->error() . "</div>";
        exit;
    }
    
    if (mysqli_num_rows($result) == 0) {
        $tables_missing[] = $table;
    }
}

// 如果有表缺失，显示错误信息并尝试创建表
if (!empty($tables_missing)) {
    echo "<div class='alert alert-warning'>邀请系统需要的数据表缺失: " . implode(', ', $tables_missing) . "。系统将尝试创建这些表。</div>";
    
    // 创建缺失的表
    if (in_array('dwz_invite_code', $tables_missing)) {
        $sql = "CREATE TABLE IF NOT EXISTS `dwz_invite_code` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `uid` int(11) NOT NULL COMMENT '用户ID',
          `code` varchar(20) NOT NULL COMMENT '邀请码',
          `create_time` datetime DEFAULT NULL COMMENT '创建时间',
          `state` tinyint(1) DEFAULT '1' COMMENT '状态：1启用，0禁用',
          `use_num` int(11) DEFAULT '0' COMMENT '使用次数',
          PRIMARY KEY (`id`),
          UNIQUE KEY `code` (`code`),
          KEY `uid` (`uid`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='邀请码表';";
        
        if (!$DB->query($sql)) {
            echo "<div class='alert alert-danger'>创建表 dwz_invite_code 失败: " . $DB->error() . "</div>";
        }
    }
    
    if (in_array('dwz_invite_record', $tables_missing)) {
        $sql = "CREATE TABLE IF NOT EXISTS `dwz_invite_record` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `invite_uid` int(11) NOT NULL COMMENT '邀请人ID',
          `invited_uid` int(11) NOT NULL COMMENT '被邀请人ID',
          `code` varchar(20) NOT NULL COMMENT '使用的邀请码',
          `reward_points` int(11) DEFAULT '0' COMMENT '奖励积分',
          `create_time` datetime DEFAULT NULL COMMENT '邀请时间',
          `status` tinyint(1) DEFAULT '1' COMMENT '状态：1正常，0已取消',
          `consume_amount` decimal(10,2) DEFAULT '0.00' COMMENT '消费金额',
          `consume_reward` int(11) DEFAULT '0' COMMENT '消费返现奖励积分',
          `consume_time` datetime DEFAULT NULL COMMENT '消费时间',
          PRIMARY KEY (`id`),
          KEY `invite_uid` (`invite_uid`),
          KEY `invited_uid` (`invited_uid`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='邀请记录表';";
        
        if (!$DB->query($sql)) {
            echo "<div class='alert alert-danger'>创建表 dwz_invite_record 失败: " . $DB->error() . "</div>";
        }
    }
    
    if (in_array('dwz_consume_reward', $tables_missing)) {
        $sql = "CREATE TABLE IF NOT EXISTS `dwz_consume_reward` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `invite_uid` int(11) NOT NULL COMMENT '邀请人用户ID',
          `invited_uid` int(11) NOT NULL COMMENT '被邀请人用户ID',
          `order_no` varchar(64) DEFAULT NULL COMMENT '订单号',
          `consume_amount` decimal(10,2) NOT NULL COMMENT '消费金额',
          `reward_percent` decimal(5,2) NOT NULL COMMENT '返现比例',
          `reward_points` int(11) NOT NULL COMMENT '返现积分',
          `create_time` datetime DEFAULT NULL COMMENT '创建时间',
          `status` tinyint(1) DEFAULT '1' COMMENT '状态：1正常，0已取消',
          `remark` varchar(255) DEFAULT NULL COMMENT '备注',
          PRIMARY KEY (`id`),
          KEY `invite_uid` (`invite_uid`),
          KEY `invited_uid` (`invited_uid`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='消费返现记录表';";
        
        if (!$DB->query($sql)) {
            echo "<div class='alert alert-danger'>创建表 dwz_consume_reward 失败: " . $DB->error() . "</div>";
        }
    }
    
    // 添加邀请系统配置
    $check_config = $DB->get_row("SELECT * FROM dwz_config WHERE k='invite_consume_percent' LIMIT 1");
    if (!$check_config) {
        $DB->query("INSERT INTO dwz_config (k, v) VALUES ('invite_consume_percent', '5')");
    }
    
    // 刷新页面
    echo "<script>setTimeout(function(){location.reload();}, 3000);</script>";
    echo "<div class='alert alert-info'>表创建完成，页面将在3秒后刷新...</div>";
    exit;
}

// 安全获取统计数据
try {
    // 获取邀请相关统计数据
    $invite_count_query = $DB->query("SELECT COUNT(*) AS count FROM dwz_invite_record WHERE invite_uid='$uid'");
    if ($invite_count_query === false) {
        $invite_count = 0;
        error_log("邀请统计查询失败: " . $DB->error());
    } else {
        $row = $DB->fetch($invite_count_query);
        $invite_count = $row ? intval($row['count']) : 0;
    }
    
    $reward_total_query = $DB->query("SELECT SUM(reward_points) AS total FROM dwz_consume_reward WHERE invite_uid='$uid'");
    if ($reward_total_query === false) {
        $reward_total = 0;
        error_log("奖励统计查询失败: " . $DB->error());
    } else {
        $row = $DB->fetch($reward_total_query);
        $reward_total = $row && $row['total'] ? floatval($row['total']) : 0;
    }
} catch (Exception $e) {
    error_log("邀请页面统计查询异常: " . $e->getMessage());
    $invite_count = 0;
    $reward_total = 0;
}

// 获取邀请配置参数
$consume_percent = isset($conf['invite_consume_percent']) ? floatval($conf['invite_consume_percent']) : 5;

// 获取用户邀请码
$invite_code_row = $DB->get_row("SELECT * FROM dwz_invite_code WHERE uid='$uid' AND state=1 ORDER BY id DESC LIMIT 1");
if (!$invite_code_row) {
    // 检查是否有任何状态的邀请码存在
    $any_code_row = $DB->get_row("SELECT * FROM dwz_invite_code WHERE uid='$uid' ORDER BY id DESC LIMIT 1");
    
    if ($any_code_row) {
        // 用户已有邀请码，但状态不是启用状态
        $invite_code = $any_code_row['code'];
        // 如果邀请码状态为禁用，可以选择性地启用它
        if ($any_code_row['state'] == 0) {
            $DB->query("UPDATE dwz_invite_code SET state=1 WHERE id='{$any_code_row['id']}'");
        }
    } else {
        // 如果确实没有邀请码，则生成一个
        $new_code = strtoupper(substr(md5($uid.$userrow['user'].time().rand(1000,9999)), 0, 8));
        $result = $DB->query("INSERT INTO dwz_invite_code(uid, code, create_time, state, use_num) VALUES('$uid', '$new_code', '$date', 1, 0)");
        if ($result === false) {
            error_log("创建邀请码失败: " . $DB->error());
            // 再次检查是否有邀请码，以防在此过程中其他进程创建了邀请码
            $final_check = $DB->get_row("SELECT * FROM dwz_invite_code WHERE uid='$uid' ORDER BY id DESC LIMIT 1");
            $invite_code = $final_check ? $final_check['code'] : '生成失败';
        } else {
            $invite_code = $new_code;
        }
    }
} else {
    $invite_code = $invite_code_row['code'];
}

// 生成邀请链接
$site_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https://" : "http://").$_SERVER['HTTP_HOST'];
$invite_url = $site_url.'/user/reg.php?invite='.$invite_code;

// 获取最近邀请的用户
$recent_invites = array();
$rs = $DB->query("SELECT r.*, u.user, u.name, u.img, u.addtime AS reg_time 
                 FROM dwz_invite_record r 
                 LEFT JOIN dwz_user u ON r.invited_uid = u.id 
                 WHERE r.invite_uid='$uid' 
                 ORDER BY r.create_time DESC 
                 LIMIT 10");

if ($rs !== false) {
    while ($row = $DB->fetch($rs)) {
        $recent_invites[] = $row;
    }
} else {
    error_log("获取邀请记录失败: " . $DB->error());
}

// 获取最近返现记录
$recent_rewards = array();
$rs = $DB->query("SELECT * FROM dwz_consume_reward 
                 WHERE invite_uid='$uid' 
                 ORDER BY create_time DESC 
                 LIMIT 10");

if ($rs !== false) {
    while ($row = $DB->fetch($rs)) {
        $recent_rewards[] = $row;
    }
} else {
    error_log("获取返现记录失败: " . $DB->error());
}
?>

<style>
body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background-color: #f4f7f9;
    color: #333;
}

.invite-section {
    background-color: white;
    border-radius: 15px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    padding: 30px;
    margin-bottom: 30px;
}

.invite-card {
    background: linear-gradient(135deg, #2ecc71 0%, #27ae60 100%);
    color: white;
    border-radius: 15px;
    padding: 40px;
    position: relative;
    overflow: hidden;
    text-align: center;
}

.invite-card::before {
    content: "";
    position: absolute;
    top: -50px;
    right: -50px;
    width: 250px;
    height: 250px;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 50%;
}

.invite-card::after {
    content: "";
    position: absolute;
    bottom: -80px;
    left: -80px;
    width: 350px;
    height: 350px;
    background: rgba(255, 255, 255, 0.05);
    border-radius: 50%;
}

.invite-code {
    font-size: 36px;
    font-weight: 700;
    letter-spacing: 3px;
    margin: 20px 0;
    position: relative;
    z-index: 1;
}

.invite-url {
    position: relative;
    z-index: 1;
    background: rgba(255, 255, 255, 0.15);
    border-radius: 10px;
    padding: 15px;
    font-size: 16px;
    word-break: break-all;
    margin-bottom: 20px;
}

.copy-btn {
    background: rgba(255, 255, 255, 0.25);
    border: none;
    color: white;
    border-radius: 8px;
    padding: 12px 24px;
    cursor: pointer;
    transition: all 0.3s;
    position: relative;
    z-index: 1;
    margin: 0 5px;
}

.copy-btn:hover {
    background: rgba(255, 255, 255, 0.35);
    transform: translateY(-2px);
}

.stat-card {
    background-color: white;
    border-radius: 15px;
    padding: 30px;
    text-align: center;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    transition: all 0.3s;
    height: 100%;
}

.stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.12);
}

.stat-icon {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 60px;
    height: 60px;
    background-color: rgba(46, 204, 113, 0.1);
    color: #2ecc71;
    border-radius: 15px;
    margin-bottom: 20px;
    font-size: 30px;
}

.stat-value {
    font-size: 30px;
    font-weight: 700;
    margin-bottom: 10px;
    color: #333;
}

.stat-label {
    font-size: 16px;
    color: #666;
}

.table td, .table th {
    vertical-align: middle;
    padding: 15px;
}

.avatar-sm {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    object-fit: cover;
}

.steps {
    display: flex;
    justify-content: space-between;
    margin: 40px 0;
    position: relative;
}

.steps::before {
    content: "";
    position: absolute;
    top: 30px;
    left: 0;
    right: 0;
    height: 2px;
    background-color: #eee;
    z-index: 0;
}

.step {
    position: relative;
    z-index: 1;
    text-align: center;
    width: 30%;
}

.step-icon {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 60px;
    height: 60px;
    background-color: white;
    border: 3px solid #2ecc71;
    border-radius: 50%;
    margin-bottom: 15px;
    font-size: 30px;
    color: #2ecc71;
}

.step-text {
    font-size: 16px;
    color: #333;
    font-weight: 500;
}

.tab-content {
    padding-top: 30px;
}

@media (max-width: 768px) {
    .invite-code {
        font-size: 28px;
    }
    
    .steps {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .steps::before {
        display: none;
    }
    
    .step {
        display: flex;
        align-items: center;
        width: 100%;
        margin-bottom: 25px;
    }
    
    .step-icon {
        margin-bottom: 0;
        margin-right: 20px;
    }
}
</style>

<div class="container-fluid">
    <!-- 邀请卡片 -->
    <div class="invite-section invite-card mb-4">
        <h3>我的邀请码</h3>
        <div class="invite-code"><?php echo $invite_code; ?></div>
        <p class="mb-2">邀请链接：</p>
        <div class="invite-url"><?php echo $invite_url; ?></div>
        <div class="d-flex flex-wrap gap-2 justify-content-center">
            <button type="button" class="copy-btn" id="copyCode">
                <i class="bi bi-clipboard me-1"></i>复制邀请码
            </button>
            <button type="button" class="copy-btn" id="copyLink">
                <i class="bi bi-link-45deg me-1"></i>复制邀请链接
            </button>
            <button type="button" class="copy-btn" id="shareBtn">
                <i class="bi bi-share me-1"></i>分享
            </button>
        </div>
    </div>
    
    <!-- 统计卡片 -->
    <div class="row mb-4">
        <div class="col-md-4 mb-3">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="bi bi-people"></i>
                </div>
                <div class="stat-value"><?php echo $invite_count; ?></div>
                <div class="stat-label">已邀请用户数</div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="bi bi-currency-yen"></i>
                </div>
                <div class="stat-value"><?php echo number_format($reward_total, 2); ?></div>
                <div class="stat-label">累计奖励积分</div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="bi bi-percent"></i>
                </div>
                <div class="stat-value"><?php echo $consume_percent; ?>%</div>
                <div class="stat-label">返现比例</div>
            </div>
        </div>
    </div>
    
    <!-- 如何获得奖励 -->
    <div class="invite-section mb-4">
        <h4 class="mb-3">如何获得奖励</h4>
        <div class="steps">
            <div class="step">
                <div class="step-icon">
                    <i class="bi bi-1-circle"></i>
                </div>
                <div class="step-text">分享邀请码给好友</div>
            </div>
            <div class="step">
                <div class="step-icon">
                    <i class="bi bi-2-circle"></i>
                </div>
                <div class="step-text">好友注册成为用户</div>
            </div>
            <div class="step">
                <div class="step-icon">
                    <i class="bi bi-3-circle"></i>
                </div>
                <div class="step-text">好友消费，您获得<?php echo $consume_percent; ?>%积分返现</div>
            </div>
        </div>
        <div class="alert alert-success">
            <i class="bi bi-info-circle-fill me-2"></i>
            当您邀请的好友进行充值消费时，您将获得其消费金额的<?php echo $consume_percent; ?>%作为积分奖励！
        </div>
    </div>
    
    <!-- 邀请记录和奖励记录标签页 -->
    <div class="invite-section">
        <ul class="nav nav-tabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="invites-tab" data-bs-toggle="tab" data-bs-target="#invites" type="button" role="tab" aria-controls="invites" aria-selected="true">
                    <i class="bi bi-people me-1"></i>邀请记录
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="rewards-tab" data-bs-toggle="tab" data-bs-target="#rewards" type="button" role="tab" aria-controls="rewards" aria-selected="false">
                    <i class="bi bi-cash-coin me-1"></i>返现记录
                </button>
            </li>
        </ul>
        <div class="tab-content">
            <!-- 邀请记录 -->
            <div class="tab-pane fade show active" id="invites" role="tabpanel" aria-labelledby="invites-tab">
                <?php if (count($recent_invites) > 0): ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>用户</th>
                                <th>注册时间</th>
                                <th>邀请时间</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recent_invites as $invite): ?>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <?php if (!empty($invite['img'])): ?>
                                        <img src="<?php echo $invite['img']; ?>" alt="" class="avatar-sm me-2">
                                        <?php else: ?>
                                        <div class="avatar-sm me-2 bg-primary d-flex align-items-center justify-content-center text-white rounded-circle">
                                            <?php echo mb_substr($invite['name'] ?? $invite['user'] ?? '?', 0, 1, 'UTF-8'); ?>
                                        </div>
                                        <?php endif; ?>
                                        <div>
                                            <div class="fw-medium"><?php echo $invite['name'] ?? $invite['user']; ?></div>
                                            <small class="text-muted"><?php echo $invite['user']; ?></small>
                                        </div>
                                    </div>
                                </td>
                                <td><?php echo $invite['reg_time']; ?></td>
                                <td><?php echo $invite['create_time']; ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <div class="text-center py-5">
                    <i class="bi bi-people fs-1 text-muted"></i>
                    <p class="mt-3 text-muted">您还没有邀请任何用户</p>
                    <button type="button" class="btn btn-success" id="shareNowBtn">
                        <i class="bi bi-share me-1"></i>立即邀请
                    </button>
                </div>
                <?php endif; ?>
            </div>
            
            <!-- 返现记录 -->
            <div class="tab-pane fade" id="rewards" role="tabpanel" aria-labelledby="rewards-tab">
                <?php if (count($recent_rewards) > 0): ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>被邀请用户</th>
                                <th>消费金额</th>
                                <th>积分奖励</th>
                                <th>时间</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recent_rewards as $reward): ?>
                            <?php
                                $invited_user = $DB->get_row("SELECT user, name, img FROM dwz_user WHERE id='{$reward['invited_uid']}' LIMIT 1");
                            ?>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <?php if (!empty($invited_user['img'])): ?>
                                        <img src="<?php echo $invited_user['img']; ?>" alt="" class="avatar-sm me-2">
                                        <?php else: ?>
                                        <div class="avatar-sm me-2 bg-primary d-flex align-items-center justify-content-center text-white rounded-circle">
                                            <?php echo mb_substr($invited_user['name'] ?? $invited_user['user'] ?? '?', 0, 1, 'UTF-8'); ?>
                                        </div>
                                        <?php endif; ?>
                                        <div>
                                            <div class="fw-medium"><?php echo $invited_user['name'] ?? $invited_user['user'] ?? '未知用户'; ?></div>
                                            <small class="text-muted"><?php echo $invited_user['user'] ?? ''; ?></small>
                                        </div>
                                    </div>
                                </td>
                                <td><?php echo number_format($reward['consume_amount'], 2); ?></td>
                                <td class="text-success"><?php echo number_format($reward['reward_points'], 2); ?></td>
                                <td><?php echo $reward['create_time']; ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <div class="text-center py-5">
                    <i class="bi bi-cash-coin fs-1 text-muted"></i>
                    <p class="mt-3 text-muted">暂无返现记录</p>
                    <p class="text-muted small">当您邀请的好友进行充值消费时，您将获得奖励</p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // 复制邀请码
    document.getElementById('copyCode').addEventListener('click', function() {
        copyToClipboard('<?php echo $invite_code; ?>');
        Swal.fire({
            icon: 'success',
            title: '复制成功',
            text: '邀请码已复制到剪贴板',
            showConfirmButton: false,
            timer: 1500
        });
    });
    
    // 复制邀请链接
    document.getElementById('copyLink').addEventListener('click', function() {
        copyToClipboard('<?php echo $invite_url; ?>');
        Swal.fire({
            icon: 'success',
            title: '复制成功',
            text: '邀请链接已复制到剪贴板',
            showConfirmButton: false,
            timer: 1500
        });
    });
    
    // 分享按钮
    document.getElementById('shareBtn').addEventListener('click', function() {
        shareInvite();
    });
    
    // 立即邀请按钮
    if (document.getElementById('shareNowBtn')) {
        document.getElementById('shareNowBtn').addEventListener('click', function() {
            shareInvite();
        });
    }
    
    // 复制到剪贴板函数
    function copyToClipboard(text) {
        const input = document.createElement('textarea');
        input.value = text;
        document.body.appendChild(input);
        input.select();
        document.execCommand('copy');
        document.body.removeChild(input);
    }
    
    // 分享邀请函数
    function shareInvite() {
        if (navigator.share) {
            navigator.share({
                title: '邀请您加入<?php echo $conf['web_name']; ?>',
                text: '您的好友邀请您加入<?php echo $conf['web_name']; ?>，注册即可享受专业的短链接服务。',
                url: '<?php echo $invite_url; ?>'
            }).catch(console.error);
        } else {
            Swal.fire({
                title: '分享邀请',
                html: `
                <div class="mt-3">
                    <p>邀请码: <strong><?php echo $invite_code; ?></strong></p>
                    <p>邀请链接:</p>
                    <div class="form-control mb-3" style="word-break: break-all;"><?php echo $invite_url; ?></div>
                    <p>将以上信息分享给好友，邀请他们注册使用。</p>
                </div>
                `,
                confirmButtonText: '复制邀请链接',
                showCancelButton: true,
                cancelButtonText: '关闭'
            }).then((result) => {
                if (result.isConfirmed) {
                    copyToClipboard('<?php echo $invite_url; ?>');
                    Swal.fire({
                        icon: 'success',
                        title: '复制成功',
                        text: '邀请链接已复制到剪贴板',
                        showConfirmButton: false,
                        timer: 1500
                    });
                }
            });
        }
    }
});
</script>