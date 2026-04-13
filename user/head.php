<!DOCTYPE html>
<html lang="zh-CN">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        :root {
            --primary: #28a745;
            --primary-light: #5cb85c;
            --primary-dark: #218838;
            --sidebar-width: 240px;
            --topbar-height: 60px;
            --border-color: rgba(0, 0, 0, 0.05);
            --text-primary: #333;
            --text-secondary: #666;
            --card-shadow: 0 2px 12px rgba(0, 0, 0, 0.05);
            --light-bg: #F7F7F7;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'PingFang SC', 'Microsoft YaHei', sans-serif;
            background-color: var(--light-bg);
            color: var(--text-primary);
            height: 100vh;
            overflow: hidden;
        }

        /* 整体布局 */
        .app-container {
            display: flex;
            height: 100vh;
            position: relative;
        }

        /* 侧边栏样式 */
        .sidebar {
            width: var(--sidebar-width);
            background-color: white;
            box-shadow: var(--card-shadow);
            z-index: 100;
            display: flex;
            flex-direction: column;
            height: 100%;
            border-right: 1px solid var(--border-color);
        }

        .sidebar-header {
            padding: 0 20px;
            height: var(--topbar-height);
            display: flex;
            align-items: center;
            border-bottom: 1px solid var(--border-color);
        }

        .sidebar-brand {
            font-weight: 600;
            color: var(--primary);
            font-size: 18px;
            text-decoration: none;
            display: flex;
            align-items: center;
        }

        .sidebar-brand i {
            margin-right: 8px;
            font-size: 20px;
        }

        .sidebar-menu {
            flex: 1;
            overflow-y: auto;
            padding: 16px 0;
        }

        .menu-group {
            padding: 8px 20px;
            color: var(--text-secondary);
            font-size: 12px;
            text-transform: uppercase;
            font-weight: 500;
            letter-spacing: 0.5px;
            margin-top: 8px;
        }

        .menu-item {
            display: flex;
            align-items: center;
            padding: 12px 20px;
            color: var(--text-primary);
            text-decoration: none;
            transition: all 0.2s;
            border-left: 3px solid transparent;
            font-size: 14px;
        }

        .menu-item:hover,
        .menu-item.active {
            background-color: rgba(40, 167, 69, 0.05);
            color: var(--primary);
            border-left-color: var(--primary);
        }

        .menu-item i {
            margin-right: 10px;
            font-size: 16px;
            width: 20px;
            text-align: center;
        }

        /* 主内容区 */
        .main-content {
            flex: 1;
            display: flex;
            flex-direction: column;
            height: 100vh;
            overflow: hidden;
        }

        /* 顶部导航栏 */
        .topbar {
            height: var(--topbar-height);
            background-color: white;
            padding: 0 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1px solid var(--border-color);
            box-shadow: var(--card-shadow);
            z-index: 90;
        }

        .topbar-left {
            display: flex;
            align-items: center;
        }

        .sidebar-toggle {
            border: none;
            background: none;
            color: var(--text-primary);
            font-size: 20px;
            margin-right: 16px;
            cursor: pointer;
            display: none;
        }

        .page-title {
            font-size: 18px;
            font-weight: 600;
            color: var(--text-primary);
        }

        /* 用户菜单 */
        .user-menu {
            display: flex;
            align-items: center;
        }

        .user-avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background-color: var(--primary);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 500;
            margin-right: 10px;
            overflow: hidden;
        }

        .user-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .user-info {
            display: flex;
            flex-direction: column;
        }

        .user-name {
            font-size: 14px;
            font-weight: 500;
        }

        .user-role {
            font-size: 12px;
            color: var(--text-secondary);
        }

        .dropdown-toggle {
            display: flex;
            align-items: center;
            text-decoration: none;
            color: var(--text-primary);
            cursor: pointer;
        }

        .dropdown-toggle::after {
            display: none;
        }

        .dropdown-menu {
            border: none;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            padding: 8px 0;
        }

        .dropdown-item {
            padding: 8px 16px;
            font-size: 14px;
            display: flex;
            align-items: center;
        }

        .dropdown-item i {
            margin-right: 8px;
            width: 20px;
            text-align: center;
        }

        /* 内容区 */
        .content-area {
            flex: 1;
            overflow: auto;
            padding: 20px;
        }

        /* 折叠子菜单 */
        .sub-menu {
            overflow: hidden;
            max-height: 0;
            transition: max-height 0.3s ease;
        }

        .sub-menu.show {
            max-height: 500px;
        }

        .sub-menu .menu-item {
            padding-left: 50px;
        }

        .menu-parent {
            cursor: pointer;
            display: flex;
            justify-content: space-between;
        }

        .menu-parent .caret {
            transition: transform 0.3s;
        }

        .menu-parent.active .caret {
            transform: rotate(90deg);
        }

        /* 响应式设计 */
        @media (max-width: 992px) {
            .sidebar {
                position: fixed;
                left: -100%;
                top: 0;
                bottom: 0;
                transition: all 0.3s;
            }

            .sidebar.show {
                left: 0;
            }

            .sidebar-toggle {
                display: block;
            }
        }

        /* 小屏幕优化 */
        @media (max-width: 576px) {
            .user-info {
                display: none;
            }
        }
    </style>
</head>
<?php
if ($userrow['state'] == 0) {
    sysmsg('你的账号已被封禁！', true);
    exit;
}
?>

<body>
    <div class="app-container">
        <!-- 侧边栏 -->
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <a href="./" class="sidebar-brand">
                    <i class="bi bi-link-45deg"></i><?php echo $conf['web_name']; ?>
                </a>
            </div>
            
            <div class="sidebar-menu">
                <div class="menu-group">主要功能</div>
                <a href="./" class="menu-item <?php echo checkIfActive('index,'); ?>">
                    <i class="bi bi-house"></i>用户首页
                </a>
                <div class="menu-item menu-parent <?php echo checkIfActive('qr-center') ? 'active' : ''; ?>" data-bs-toggle="collapse" data-bs-target="#qrSubmenu">
                    <div><i class="bi bi-qr-code"></i>活码管理</div>
                    <i class="bi bi-chevron-right caret"></i>
                </div>
                <div class="sub-menu <?php echo checkIfActive('qr-center') ? 'show' : ''; ?>" id="qrSubmenu">
                    <a href="./qr-center.php" class="menu-item <?php echo basename($_SERVER['SCRIPT_NAME']) == 'qr-center.php' ? 'active' : ''; ?>">
                        <i class="bi bi-grid"></i>活码中心
                    </a>
                    <a href="./qr-list.php" class="menu-item <?php echo basename($_SERVER['SCRIPT_NAME']) == 'qr-list.php' ? 'active' : ''; ?>">
                        <i class="bi bi-list-ul"></i>活码列表
                    </a>
                    <a href="./domain-store.php" class="menu-item <?php echo basename($_SERVER['SCRIPT_NAME']) == 'domain-store.php' ? 'active' : ''; ?>">
                        <i class="bi bi-shop"></i>域名商店
                    </a>
                </div>
                <a href="./pay.php" class="menu-item <?php echo checkIfActive('pay'); ?>">
                    <i class="bi bi-credit-card"></i>充值中心
                </a>
                <a href="./urllist.php" class="menu-item <?php echo checkIfActive('urllist'); ?>">
                    <i class="bi bi-link"></i>网址列表
                </a>
                <a href="./urlcheck.php" class="menu-item <?php echo checkIfActive('urlcheck'); ?>">
                    <i class="bi bi-eye"></i>网址监控
                </a>
                <a href="./domain-check.php" class="menu-item <?php echo checkIfActive('domain-check'); ?>">
                    <i class="bi bi-globe"></i>域名检测
                </a>
                <a href="./invite.php" class="menu-item <?php echo checkIfActive('invite'); ?>">
                    <i class="bi bi-people"></i>邀请管理
                </a>
                <a href="./problem.php" class="menu-item <?php echo checkIfActive('problem'); ?>">
                    <i class="bi bi-question-circle"></i>常见问题
                </a>
                
                <div class="menu-group">系统管理</div>
                <div class="menu-item menu-parent <?php echo checkIfActive('uset') ? 'active' : ''; ?>" data-bs-toggle="collapse" data-bs-target="#settingsSubmenu">
                    <div><i class="bi bi-gear"></i>系统设置</div>
                    <i class="bi bi-chevron-right caret"></i>
                </div>
                <div class="sub-menu <?php echo checkIfActive('uset') ? 'show' : ''; ?>" id="settingsSubmenu">
                    <a href="./uset.php?mod=user" class="menu-item <?php echo checkIfActive('user'); ?>">
                        <i class="bi bi-person"></i>资料修改
                    </a>
                    <a href="./uset.php?mod=token" class="menu-item <?php echo checkIfActive('token'); ?>">
                        <i class="bi bi-key"></i>对接设置
                    </a>
                </div>
                
                
                
                <div class="menu-group">其他</div>
                <a href="./login.php?my=logout" class="menu-item">
                    <i class="bi bi-box-arrow-right"></i>退出登录
                </a>
            </div>
        </aside>

        <!-- 主内容区 -->
        <div class="main-content">
            <!-- 顶部导航栏 -->
            <header class="topbar">
                <div class="topbar-left">
                    <button class="sidebar-toggle" id="sidebarToggle">
                        <i class="bi bi-list"></i>
                    </button>
                    <div class="page-title">
                        <?php echo $title; ?>
                    </div>
                </div>
                <div class="user-menu">
                    <div class="dropdown">
                        <a href="#" class="dropdown-toggle" id="userDropdown" data-bs-toggle="dropdown">
                            <div class="user-avatar">
                                <?php if(!empty($userrow['img'])): ?>
                                <img alt="" src="<?php echo $userrow['img'] ?>">
                                <?php else: ?>
                                <?php echo substr($userrow['name'], 0, 1); ?>
                                <?php endif; ?>
                            </div>
                            <div class="user-info">
                                <span class="user-name"><?php echo $userrow['name'] ?></span>
                                <span class="user-role">用户</span>
                            </div>
                            <i class="bi bi-chevron-down ms-1"></i>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="./uset.php?mod=user"><i class="bi bi-person"></i>个人资料</a></li>
                            <li><a class="dropdown-item" href="./urllist.php"><i class="bi bi-link"></i>网址管理</a></li>
                            <li><a class="dropdown-item" href="./invite.php"><i class="bi bi-people"></i>邀请管理</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="./login.php?my=logout"><i class="bi bi-box-arrow-right"></i>退出登录</a></li>
                        </ul>
                    </div>
                </div>
            </header>

            <!-- 内容区域 -->
            <div class="content-area">
                <!-- 此处将包含各个页面的具体内容 -->
            
</body>

<script>
    // 侧边栏切换
    document.getElementById('sidebarToggle').addEventListener('click', function() {
        document.getElementById('sidebar').classList.toggle('show');
    });
    
    // 菜单折叠控制
    document.querySelectorAll('.menu-parent').forEach(item => {
        item.addEventListener('click', function() {
            this.classList.toggle('active');
        });
    });
    
    // 响应式布局处理
    window.addEventListener('resize', function() {
        if (window.innerWidth >= 992) {
            document.getElementById('sidebar').classList.remove('show');
        }
    });
</script>

</html>