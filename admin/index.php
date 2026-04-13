<?php
include('../includes/common.php');

// 使用cookie token验证管理员登录状态
$islogin = isset($_COOKIE["admin_token"]) ? 1 : 0;
if ($islogin == 1) {
  $token = $_COOKIE["admin_token"];
  $token_array = explode("\t", authcode($token, 'DECODE', SYS_KEY));
  if ($token_array[0] !== $conf['admin_user']) {
    $islogin = 0;
  }
}

if ($islogin != 1) {
  exit('<script language=\'javascript\'>window.location.href=\'./login.php\';</script>');
}
?>
<!DOCTYPE html>
<html lang="zh">

<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
  <title>短网址后台管理中心</title>
  <link rel="shortcut icon" type="image/x-icon" href="favicon.ico">
  <meta name="apple-mobile-web-app-capable" content="yes">
  <meta name="apple-touch-fullscreen" content="yes">
  <meta name="apple-mobile-web-app-status-bar-style" content="default">
  <link rel="stylesheet" type="text/css" href="../static/admin/css/materialdesignicons.min.css">
  <style>
    :root {
      --primary-color: #2ECC71;
      --primary-dark: #27AE60;
      --bg-color: #f5f5f5;
      --text-color: #333;
      --card-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }

    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
    }

    body {
      background-color: var(--bg-color);
      color: var(--text-color);
    }

    .header {
      background: white;
      padding: 1rem 2rem;
      box-shadow: var(--card-shadow);
      display: flex;
      justify-content: space-between;
      align-items: center;
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      z-index: 1000;
    }

    .logo {
      color: var(--primary-color);
      font-size: 1.5rem;
      font-weight: bold;
    }

    .user-info {
      display: flex;
      align-items: center;
      gap: 1rem;
    }

    .main-container {
      display: flex;
      min-height: 100vh;
      padding-top: 64px;
    }

    .sidebar {
      width: 240px;
      background: white;
      padding: 1rem;
      box-shadow: var(--card-shadow);
      position: fixed;
      left: 0;
      top: 64px;
      bottom: 0;
      overflow-y: auto;
    }

    .sidebar-section {
      margin-bottom: 2rem;
    }

    .sidebar-title {
      font-size: 0.9rem;
      color: #666;
      margin-bottom: 0.5rem;
      padding: 0 0.5rem;
    }

    .sidebar-item {
      display: flex;
      align-items: center;
      gap: 0.5rem;
      padding: 0.75rem;
      margin-bottom: 0.25rem;
      border-radius: 4px;
      cursor: pointer;
      transition: all 0.3s;
      color: var(--text-color);
      text-decoration: none;
    }

    .sidebar-item:hover {
      background-color: var(--bg-color);
      color: var(--primary-color);
    }

    .sidebar-item.active {
      background-color: var(--primary-color);
      color: white;
    }

    .content {
      flex: 1;
      margin-left: 240px;
      padding: 2rem;
    }

    .nav-subnav {
      padding-left: 1.5rem;
    }

    .nav-subnav .sidebar-item {
      font-size: 0.9rem;
    }

    .dropdown-menu {
      background: white;
      border-radius: 4px;
      box-shadow: var(--card-shadow);
      padding: 0.5rem 0;
      position: absolute;
      right: 0;
      top: 100%;
      min-width: 180px;
      z-index: 1000;
      display: none;
    }

    .dropdown-item {
      padding: 0.5rem 1rem;
      color: var(--text-color);
      text-decoration: none;
      display: flex;
      align-items: center;
      gap: 0.5rem;
      transition: all 0.2s;
    }

    .dropdown-item:hover {
      background-color: var(--bg-color);
      color: var(--primary-color);
    }

    .dropdown-divider {
      height: 1px;
      background-color: #eee;
      margin: 0.5rem 0;
    }

    .img-avatar {
      width: 32px;
      height: 32px;
      border-radius: 50%;
    }

    /* 用户下拉菜单样式 */
    .user-dropdown {
      position: relative;
      display: flex;
      align-items: center;
      gap: 0.5rem;
      cursor: pointer;
      padding: 0.5rem;
      border-radius: 4px;
      transition: all 0.2s;
    }

    .user-dropdown:hover {
      background-color: var(--bg-color);
    }

    .user-dropdown:hover .dropdown-menu {
      display: block;
      animation: fadeIn 0.2s ease-out;
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(-10px); }
      to { opacity: 1; transform: translateY(0); }
    }

    #iframe-content {
      background: white;
      border-radius: 8px;
      box-shadow: var(--card-shadow);
      min-height: calc(100vh - 128px);
    }

    /* 选项卡样式 */
    .tabs-container {
      background: white;
      border-radius: 8px;
      box-shadow: var(--card-shadow);
      margin-bottom: 1rem;
    }

    .tabs-header {
      display: flex;
      background: #f8f9fa;
      border-bottom: 1px solid #eee;
      border-radius: 8px 8px 0 0;
      overflow-x: auto;
      white-space: nowrap;
    }

    .tab-item {
      padding: 0.75rem 1.5rem;
      cursor: pointer;
      border-right: 1px solid #eee;
      display: flex;
      align-items: center;
      gap: 0.5rem;
      color: var(--text-color);
      transition: all 0.3s;
    }

    .tab-item:hover {
      background: #f0f0f0;
    }

    .tab-item.active {
      background: white;
      color: var(--primary-color);
      border-bottom: 2px solid var(--primary-color);
    }

    .tab-item .close {
      margin-left: 0.5rem;
      opacity: 0.5;
      transition: opacity 0.3s;
    }

    .tab-item .close:hover {
      opacity: 1;
    }

    .tab-content {
      padding: 1rem;
    }

    .tab-pane {
      display: none;
    }

    .tab-pane.active {
      display: block;
    }
  </style>
</head>

<body>
  <header class="header">
    <div class="logo">短网址后台管理中心</div>
    <div class="user-info">
      <div class="user-dropdown">
        <img class="img-avatar" src="https://q.qlogo.cn/headimg_dl?dst_uin=<?php echo $conf['kf_qq'] ?>&spec=100" alt="<?php echo $conf['admin_user'] ?>" />
        <span><?php echo $conf['admin_user'] ?></span>
        <i class="mdi mdi-chevron-down"></i>
        
        <div class="dropdown-menu">
          <a class="dropdown-item" href="pwd.php" onclick="tabsManager.addTab('pwd.php', '修改密码'); return false;">
            <i class="mdi mdi-lock-reset"></i>
            <span>修改密码</span>
          </a>
          <div class="dropdown-divider"></div>
          <a class="dropdown-item" href="login.php?logout=1">
            <i class="mdi mdi-logout-variant"></i>
            <span>退出登录</span>
          </a>
        </div>
      </div>
    </div>
  </header>

  <div class="main-container">
    <aside class="sidebar">
      <div class="sidebar-section">
        <div class="sidebar-title">系统管理</div>
        <a class="sidebar-item active" href="home.php">
          <i class="mdi mdi-home"></i>
          <span>后台首页</span>
        </a>
        <a class="sidebar-item" href="ulist.php">
          <i class="mdi mdi-account-multiple"></i>
          <span>用户管理</span>
        </a>
        <a class="sidebar-item" href="dlist.php">
          <i class="mdi mdi-web"></i>
          <span>域名管理</span>
        </a>
        <a class="sidebar-item" href="qr_points_setting.php">
          <i class="mdi mdi-star"></i>
          <span>积分设置</span>
        </a>
        <a class="sidebar-item" href="points_package.php">
          <i class="mdi mdi-currency-cny"></i>
          <span>积分充值设置</span>
        </a>
        <a class="sidebar-item" href="withdraw.php">
          <i class="mdi mdi-home"></i>
          <span>提现审核</span>
        </a>
        <a class="sidebar-item" href="invite_setting.php">
          <i class="mdi mdi-account-multiple-plus"></i>
          <span>邀请设置</span>
        </a>
        <a class="sidebar-item" href="domain_check_setting.php">
          <i class="mdi mdi-shield-check"></i>
          <span>域名检测设置</span>
        </a>
      </div>
      
      

      <div class="sidebar-section">
        <div class="sidebar-title">活码管理</div>
        <a class="sidebar-item" href="qr_list.php">
          <i class="mdi mdi-qrcode"></i>
          <span>活码列表</span>
        </a>
        <a class="sidebar-item" href="entry_domain.php">
          <i class="mdi mdi-domain"></i>
          <span>入口域名管理</span>
        </a>
        <a class="sidebar-item" href="landing_domain.php">
          <i class="mdi mdi-domain"></i>
          <span>落地域名管理</span>
        </a>
        <a class="sidebar-item" href="add_domain.php">
          <i class="mdi mdi-plus-circle"></i>
          <span>批量添加域名</span>
        </a>


      <div class="sidebar-section">
        <div class="sidebar-title">网址管理</div>
        <a class="sidebar-item" href="urllist.php">
          <i class="mdi mdi-link-variant"></i>
          <span>网址列表</span>
        </a>
        <a class="sidebar-item" href="urldel.php">
          <i class="mdi mdi-delete"></i>
          <span>网址回收站</span>
        </a>
        <a class="sidebar-item" href="urlmodify.php">
          <i class="mdi mdi-pencil"></i>
          <span>修改记录</span>
        </a>
      </div>

      <div class="sidebar-section">
        <div class="sidebar-title">系统设置</div>
        <a class="sidebar-item" href="set.php?mod=site">
          <i class="mdi mdi-settings"></i>
          <span>网站信息设置</span>
        </a>
        <a class="sidebar-item" href="apilist.php">
          <i class="mdi mdi-api"></i>
          <span>短网址接口设置</span>
        </a>
        <a class="sidebar-item" href="set2.php?mod=setLogo">
          <i class="mdi mdi-image"></i>
          <span>网站logo设置</span>
        </a>
        <a class="sidebar-item" href="set2.php?mod=cron">
          <i class="mdi mdi-clock"></i>
          <span>计划任务设置</span>
        </a>
        <a class="sidebar-item" href="set2.php?mod=clean">
          <i class="mdi mdi-broom"></i>
          <span>系统数据清理</span>
        </a>
        <a class="sidebar-item" href="set2.php?mod=update">
          <i class="mdi mdi-update"></i>
          <span>检测版本更新</span>
        </a>
      </div>
    </aside>

    <main class="content">
      <div class="tabs-container">
        <div class="tabs-header" id="tabsHeader">
          <!-- 选项卡标题将通过 JavaScript 动态添加 -->
        </div>
        <div class="tab-content" id="tabsContent">
          <!-- 选项卡内容将通过 JavaScript 动态添加 -->
        </div>
      </div>
      <div id="iframe-content"></div>
    </main>
  </div>

  <script type="text/javascript" src="../static/admin/js/jquery.min.js"></script>
  <script type="text/javascript" src="../static/admin/js/popper.min.js"></script>
  <script type="text/javascript" src="../static/admin/js/bootstrap.min.js"></script>
  <script type="text/javascript" src="../static/admin/js/perfect-scrollbar.min.js"></script>
  <script type="text/javascript" src="../static/admin/js/bootstrap-multitabs/multitabs.min.js"></script>
  <script type="text/javascript" src="../static/admin/js/jquery.cookie.min.js"></script>
  <script>
    // 选项卡管理
    const tabsManager = {
      tabs: [],
      activeTab: null,

      init() {
        this.bindEvents();
        // 删除从存储加载标签的调用
        // 默认打开首页标签
        this.addTab('home.php', '后台首页');
      },

      bindEvents() {
        // 监听链接点击
        document.querySelectorAll('.sidebar-item').forEach(item => {
          item.addEventListener('click', (e) => {
            e.preventDefault();
            const url = item.getAttribute('href');
            const title = item.querySelector('span').textContent;
            this.addTab(url, title);
          });
        });
      },

      addTab(url, title) {
        const tabId = `tab_${Date.now()}`;
        const tab = {
          id: tabId,
          url: url,
          title: title
        };

        // 检查是否已存在相同URL的选项卡
        const existingTab = this.tabs.find(t => t.url === url);
        if (existingTab) {
          this.activateTab(existingTab.id);
          return;
        }

        // 对于修改密码页面，添加特殊处理
        if (url === 'set_pwd.php') {
          // 如果是修改密码页面，先关闭现有的修改密码标签
          const pwdTab = this.tabs.find(t => t.url === 'set_pwd.php');
          if (pwdTab) {
            this.removeTab(pwdTab.id);
          }
        }

        this.tabs.push(tab);
        this.renderTabs();
        this.activateTab(tabId);
        // 删除保存标签到存储的调用
      },

      removeTab(tabId) {
        const index = this.tabs.findIndex(tab => tab.id === tabId);
        if (index === -1) return;

        this.tabs.splice(index, 1);
        this.renderTabs();

        // 如果删除的是当前激活的选项卡，则激活最后一个选项卡
        if (this.activeTab === tabId && this.tabs.length > 0) {
          this.activateTab(this.tabs[this.tabs.length - 1].id);
        }
        // 删除保存标签到存储的调用
      },

      activateTab(tabId) {
        const tab = this.tabs.find(t => t.id === tabId);
        if (!tab) return;

        this.activeTab = tabId;
        this.renderTabs();
        this.loadTabContent(tab);
      },

      renderTabs() {
        const header = document.getElementById('tabsHeader');
        header.innerHTML = this.tabs.map(tab => `
          <div class="tab-item ${tab.id === this.activeTab ? 'active' : ''}" data-tab-id="${tab.id}">
            <span>${tab.title}</span>
            <span class="close" onclick="tabsManager.removeTab('${tab.id}')">&times;</span>
          </div>
        `).join('');

        // 绑定选项卡点击事件
        header.querySelectorAll('.tab-item').forEach(item => {
          item.addEventListener('click', (e) => {
            if (!e.target.classList.contains('close')) {
              this.activateTab(item.dataset.tabId);
            }
          });
        });
      },

      loadTabContent(tab) {
        const iframe = document.createElement('iframe');
        iframe.src = tab.url;
        iframe.style.width = '100%';
        iframe.style.height = 'calc(100vh - 200px)';
        iframe.style.border = 'none';

        const content = document.getElementById('iframe-content');
        content.innerHTML = '';
        content.appendChild(iframe);
      },

      // 删除saveTabsToStorage方法和loadTabsFromStorage方法
    };

    // 初始化选项卡管理器
    document.addEventListener('DOMContentLoaded', () => {
      tabsManager.init();
      
      // 处理用户下拉菜单点击事件
      const userDropdown = document.querySelector('.user-dropdown');
      userDropdown.addEventListener('click', function(e) {
        // 如果点击的是下拉菜单项，阻止事件冒泡
        if (e.target.closest('.dropdown-item')) {
          e.stopPropagation();
        }
      });
      
      // 点击页面其他区域关闭下拉菜单
      document.addEventListener('click', function(e) {
        if (!e.target.closest('.user-dropdown')) {
          document.querySelectorAll('.dropdown-menu').forEach(menu => {
            menu.style.display = 'none';
          });
        }
      });
    });

    function cleanCache() {
      $.ajax({
        url: "ajax.php?act=cleanCache",
        type: "GET",
        dataType: "json",
        success: function(data) {
          if (data.code == 0) {
            alert('清理成功');
          }
        },
        error: function(data) {
          alert('服务器错误');
          return false;
        }
      });
    }
  </script>
</body>

</html>
