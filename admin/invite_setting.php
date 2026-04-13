<?php
include("../includes/common.php");
$title = '邀请设置';
if ($islogin == 1) {
} else exit("<script language='javascript'>window.location.href='./login.php';</script>");

// 确保所有设置项都有默认值
if(!isset($conf['invite_system_enable'])) $conf['invite_system_enable'] = 0;
if(!isset($conf['invite_reg_reward'])) $conf['invite_reg_reward'] = 50;
if(!isset($conf['invite_reg_reward_new'])) $conf['invite_reg_reward_new'] = 20;
if(!isset($conf['invite_pay_percent'])) $conf['invite_pay_percent'] = 10;
if(!isset($conf['invite_consume_percent'])) $conf['invite_consume_percent'] = 5;
if(!isset($conf['invite_limit'])) $conf['invite_limit'] = 0;
if(!isset($conf['invite_daily_limit'])) $conf['invite_daily_limit'] = 0;
if(!isset($conf['invite_same_ip_limit'])) $conf['invite_same_ip_limit'] = 2;
if(!isset($conf['invite_valid_days'])) $conf['invite_valid_days'] = 0;

// 处理表单提交
if(isset($_POST['submit'])) {
  $invite_consume_percent = isset($_POST['invite_consume_percent']) ? floatval($_POST['invite_consume_percent']) : 5;
  
  // 保存设置
  saveSetting('invite_consume_percent', $invite_consume_percent);
  
  // 清除缓存
  $CACHE->clear();
  
  $msg = '保存成功！';
}

// 获取当前设置
$invite_consume_percent = isset($conf['invite_consume_percent']) ? floatval($conf['invite_consume_percent']) : 5;
?>
<!DOCTYPE html>
<html lang="zh">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
  <title><?php echo $title ?></title>
  <link rel="stylesheet" type="text/css" href="../static/admin/css/materialdesignicons.min.css">
  <link rel="stylesheet" type="text/css" href="../static/admin/css/bootstrap.min.css">
  <link rel="stylesheet" type="text/css" href="../static/admin/css/bootstrap-datepicker.min.css">
  <link rel="stylesheet" type="text/css" href="../static/admin/css/sweetalert2.min.css">
  <script type="text/javascript" src="../static/admin/js/jquery.min.js"></script>
  <script type="text/javascript" src="../static/admin/js/bootstrap.min.js"></script>
  <script type="text/javascript" src="../static/admin/js/bootstrap-datepicker.min.js"></script>
  <script type="text/javascript" src="../static/admin/js/bootstrap-datepicker.zh-CN.min.js"></script>
  <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- 页面样式 -->
<style>
  :root {
    --primary-color: #10B981;
    --primary-hover: #059669;
    --primary-light: #D1FAE5;
    --primary-ultra-light: #ECFDF5;
    --primary-dark: #047857;
    --secondary-color: #0EA5E9;
    --secondary-light: #E0F2FE;
    --accent-color: #6EE7B7;
    --success-color: #059669;
    --warning-color: #F59E0B;
    --danger-color: #EF4444;
    --neutral-50: #FAFAFA;
    --neutral-100: #F5F5F5;
    --neutral-200: #E5E5E5;
    --neutral-300: #D4D4D4;
    --neutral-400: #A3A3A3;
    --neutral-500: #737373;
    --neutral-600: #525252;
    --neutral-700: #404040;
    --neutral-800: #262626;
    --neutral-900: #171717;
    --radius-sm: 0.25rem;
    --radius-md: 0.5rem;
    --radius-lg: 0.75rem;
    --radius-xl: 1rem;
    --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
    --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    --shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    --shadow-inner: inset 0 2px 4px 0 rgba(0, 0, 0, 0.06);
    --shadow-focus: 0 0 0 3px rgba(16, 185, 129, 0.2);
  }
  
  body {
    background-color: var(--neutral-100);
    color: var(--neutral-800);
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
    line-height: 1.6;
    font-size: 0.95rem;
  }
  
  /* 卡片样式 */
  .section-card {
    margin-bottom: 1.75rem;
    border-radius: var(--radius-lg);
    background-color: #fff;
    border: none;
    box-shadow: var(--shadow-md);
    transition: all 0.25s ease;
    overflow: hidden;
    position: relative;
    border: 1px solid rgba(0, 0, 0, 0.05);
  }
  
  .section-card:hover {
    box-shadow: var(--shadow-lg);
    transform: translateY(-2px);
  }
  
  .section-card .card-header {
    background: var(--primary-color);
    color: white;
    border-bottom: none;
    padding: 1.25rem 1.5rem;
    position: relative;
    overflow: hidden;
  }
  
  .section-card .card-header::before {
    content: '';
    position: absolute;
    top: 0;
    right: 0;
    bottom: 0;
    left: 0;
    background: url("data:image/svg+xml,%3Csvg width='100' height='100' viewBox='0 0 100 100' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M11 18c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm48 25c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm-43-7c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm63 31c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM34 90c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm56-76c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM12 86c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm28-65c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm23-11c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-6 60c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm29 22c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zM32 63c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm57-13c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-9-21c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM60 91c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM35 41c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM12 60c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2z' fill='%23ffffff' fill-opacity='0.1' fill-rule='evenodd'/%3E%3C/svg%3E") center center;
    opacity: 0.5;
    z-index: 0;
  }
  
  .section-card .card-header::after {
    content: '';
    position: absolute;
    top: -60%;
    right: -30%;
    width: 120px;
    height: 120px;
    border-radius: 50%;
    background: radial-gradient(rgba(255, 255, 255, 0.2), rgba(255, 255, 255, 0));
    z-index: 0;
  }
  
  .section-card .card-title {
    margin: 0;
    font-size: 1.35rem;
    font-weight: 600;
    display: flex;
    align-items: center;
    position: relative;
    z-index: 1;
    letter-spacing: -0.02em;
  }
  
  .section-card .card-title i {
    margin-right: 0.75rem;
    font-size: 1.5rem;
    background: rgba(255, 255, 255, 0.2);
    width: 38px;
    height: 38px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
  }
  
  .section-card .card-body {
    padding: 1.75rem;
  }
  
  /* 设置区块 */
  .setting-section {
    background-color: var(--neutral-50);
    border-radius: var(--radius-lg);
    padding: 1.5rem;
    margin-bottom: 1.75rem;
    border-left: 3px solid var(--primary-color);
    position: relative;
    transition: all 0.2s ease;
    box-shadow: var(--shadow-sm);
  }
  
  .setting-section:hover {
    box-shadow: var(--shadow-md);
  }
  
  .setting-section:last-child {
    margin-bottom: 1rem;
  }
  
  .setting-section-title {
    font-size: 1.1rem;
    font-weight: 600;
    margin-bottom: 1.5rem;
    color: var(--neutral-800);
    display: flex;
    align-items: center;
    position: relative;
  }
  
  .setting-section-title i {
    margin-right: 0.75rem;
    color: var(--primary-color);
    font-size: 1.25rem;
    opacity: 0.85;
  }
  
  .setting-section-title::after {
    content: '';
    position: absolute;
    bottom: -0.75rem;
    left: 0;
    width: 60px;
    height: 2px;
    background: linear-gradient(to right, var(--primary-color), transparent);
  }
  
  /* 表单元素 */
  .form-label {
    font-weight: 500;
    color: var(--neutral-700);
    margin-bottom: 0.5rem;
    font-size: 0.95rem;
  }
  
  .form-text {
    font-size: 0.8rem;
    color: var(--neutral-500);
    margin-top: 0.5rem;
    line-height: 1.4;
  }
  
  .form-control, .form-select {
    border-radius: var(--radius-md);
    border-color: var(--neutral-200);
    padding: 0.6rem 0.85rem;
    font-size: 0.95rem;
    transition: all 0.2s ease;
    color: var(--neutral-800);
    background-color: #fff;
  }
  
  .form-control:hover, .form-select:hover {
    border-color: var(--neutral-400);
  }
  
  .form-control:focus, .form-select:focus {
    box-shadow: var(--shadow-focus);
    border-color: var(--primary-color);
  }
  
  .input-group-text {
    background-color: var(--neutral-50);
    border-color: var(--neutral-200);
    color: var(--neutral-600);
    font-size: 0.95rem;
  }
  
  /* 切换开关 */
  .form-check-input {
    background-color: var(--neutral-200);
    border-color: var(--neutral-300);
    cursor: pointer;
  }
  
  .form-check-input:checked {
    background-color: var(--primary-color);
    border-color: var(--primary-color);
  }
  
  .form-switch .form-check-input {
    width: 2.75em;
    height: 1.4em;
    border-radius: 2em;
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='-4 -4 8 8'%3e%3ccircle r='3' fill='%23fff'/%3e%3c/svg%3e");
    background-position: left center;
    transition: background-position 0.25s ease-in-out;
  }
  
  .form-switch .form-check-input:focus {
    box-shadow: var(--shadow-focus);
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='-4 -4 8 8'%3e%3ccircle r='3' fill='%23fff'/%3e%3c/svg%3e");
  }
  
  .form-switch .form-check-label {
    font-weight: 500;
    font-size: 1rem;
    padding-top: 0.1rem;
  }
  
  /* 按钮 */
  .btn {
    border-radius: var(--radius-md);
    padding: 0.6rem 1.25rem;
    font-weight: 500;
    transition: all 0.25s ease;
    font-size: 0.95rem;
    position: relative;
    overflow: hidden;
  }
  
  .btn::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: linear-gradient(rgba(255, 255, 255, 0), rgba(255, 255, 255, 0.2));
    transform: translateY(100%);
    transition: transform 0.25s ease-out;
  }
  
  .btn:hover::before {
    transform: translateY(0);
  }
  
  .btn-primary {
    background: var(--primary-color);
    border: none;
    color: white;
    box-shadow: 0 2px 5px rgba(16, 185, 129, 0.25);
  }
  
  .btn-primary:hover {
    background: var(--primary-hover);
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(16, 185, 129, 0.35);
  }
  
  .btn-primary:active {
    transform: translateY(0);
    box-shadow: 0 1px 3px rgba(16, 185, 129, 0.2);
  }
  
  .btn-outline-success {
    border: 1px solid var(--success-color);
    color: var(--success-color);
    background: transparent;
  }
  
  .btn-outline-success:hover {
    background-color: var(--success-color);
    color: white;
    box-shadow: 0 2px 5px rgba(5, 150, 105, 0.2);
  }
  
  .btn i {
    font-size: 1rem;
  }
  
  .btn-action {
    min-width: 130px;
  }
  
  /* 警告框 */
  .alert-info {
    background-color: rgba(16, 185, 129, 0.08);
    border-left: 4px solid var(--primary-color);
    border-top: none;
    border-right: none;
    border-bottom: none;
    color: var(--primary-dark);
    border-radius: var(--radius-md);
    padding: 1.25rem;
  }
  
  .alert-info i {
    color: var(--primary-color);
    font-size: 1.25rem;
  }
  
  /* 表格样式 */
  .table {
    --bs-table-striped-bg: var(--neutral-50);
    border-radius: var(--radius-md);
    overflow: hidden;
  }
  
  .table th {
    background-color: var(--neutral-100);
    color: var(--neutral-700);
    font-weight: 600;
    padding: 1rem;
    border-bottom-width: 1px;
    border-color: var(--neutral-200);
    font-size: 0.9rem;
    text-transform: uppercase;
    letter-spacing: 0.05em;
  }
  
  .table thead tr {
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
  }
  
  .table td {
    padding: 1rem;
    vertical-align: middle;
    border-color: var(--neutral-100);
    color: var(--neutral-700);
  }
  
  .table tr:hover {
    background-color: rgba(16, 185, 129, 0.03);
  }
  
  /* 徽章 */
  .badge {
    padding: 0.45em 0.75em;
    font-weight: 500;
    border-radius: 99px;
    font-size: 0.75rem;
    letter-spacing: 0.01em;
  }
  
  .bg-success {
    background-color: var(--success-color) !important;
  }
  
  .bg-warning {
    background-color: var(--warning-color) !important;
  }
  
  .bg-secondary {
    background-color: var(--neutral-400) !important;
  }
  
  .bg-primary {
    background-color: var(--primary-color) !important;
  }
  
  /* 分页 */
  .pagination {
    margin-top: 1rem;
  }
  
  .pagination .page-link {
    color: var(--primary-color);
    border-radius: 50%;
    margin: 0 0.15rem;
    width: 36px;
    height: 36px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-color: var(--neutral-200);
    font-size: 0.9rem;
  }
  
  .pagination .page-item.active .page-link {
    background-color: var(--primary-color);
    border-color: var(--primary-color);
    box-shadow: 0 2px 5px rgba(16, 185, 129, 0.3);
    z-index: 1;
  }
  
  .pagination .page-link:hover {
    background-color: var(--primary-ultra-light);
    border-color: var(--primary-light);
    z-index: 1;
  }
  
  .pagination .page-link:focus {
    box-shadow: var(--shadow-focus);
  }
  
  /* 列表组 */
  .list-group-item {
    border-color: var(--neutral-200);
    padding: 1rem 1.25rem;
    transition: all 0.15s ease;
  }
  
  .list-group-item:hover {
    background-color: var(--primary-ultra-light);
  }
  
  /* 统计卡片 */
  .stat-card {
    display: flex;
    align-items: center;
    margin-bottom: 1.5rem;
    position: relative;
    padding: 0.25rem 0.25rem 0.25rem 0;
    transition: all 0.2s ease;
    border-radius: var(--radius-md);
  }
  
  .stat-card:last-child {
    margin-bottom: 0;
  }
  
  .stat-card:hover {
    transform: translateY(-2px);
  }
  
  .stat-icon {
    width: 56px;
    height: 56px;
    border-radius: var(--radius-md);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.75rem;
    margin-right: 1rem;
    color: white;
    position: relative;
    overflow: hidden;
    box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
  }
  
  .stat-icon::before {
    content: '';
    position: absolute;
    top: -10px;
    right: -10px;
    width: 50px;
    height: 50px;
    background: radial-gradient(rgba(255, 255, 255, 0.25), rgba(255, 255, 255, 0));
    border-radius: 50%;
  }
  
  .stat-icon i {
    position: relative;
    z-index: 1;
  }
  
  .stat-info {
    flex: 1;
  }
  
  .stat-title {
    color: var(--neutral-600);
    font-size: 0.875rem;
    margin-bottom: 0.35rem;
    letter-spacing: 0.01em;
  }
  
  .stat-value {
    font-size: 1.65rem;
    font-weight: 700;
    color: var(--neutral-800);
    margin: 0;
    line-height: 1.2;
    letter-spacing: -0.02em;
  }
  
  /* 动画效果 */
  .animate-fade-in {
    animation: fadeIn 0.5s ease-out forwards;
    opacity: 0;
  }
  
  @keyframes fadeIn {
    from { opacity: 0; transform: translateY(15px); }
    to { opacity: 1; transform: translateY(0); }
  }
  
  .animate-scale-in {
    animation: scaleIn 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards;
    transform: scale(0.8);
    opacity: 0;
  }
  
  @keyframes scaleIn {
    from { transform: scale(0.8); opacity: 0; }
    to { transform: scale(1); opacity: 1; }
  }
  
  .animate-slide-up {
    animation: slideUp 0.5s ease-out;
  }
  
  @keyframes slideUp {
    from { transform: translateY(10px); opacity: 0; }
    to { transform: translateY(0); opacity: 1; }
  }
  
  .stagger-item:nth-child(1) { animation-delay: 0.1s; }
  .stagger-item:nth-child(2) { animation-delay: 0.2s; }
  .stagger-item:nth-child(3) { animation-delay: 0.3s; }
  .stagger-item:nth-child(4) { animation-delay: 0.4s; }
  .stagger-item:nth-child(5) { animation-delay: 0.5s; }
  
  /* 加载状态 */
  .content-loader {
    position: relative;
    background-color: var(--neutral-100);
    border-radius: var(--radius-md);
    height: 10px;
    overflow: hidden;
  }
  
  .content-loader::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 30%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.6), transparent);
    animation: loading 1.5s infinite;
  }
  
  @keyframes loading {
    0% { left: -30%; }
    100% { left: 100%; }
  }
  
  /* 浮动标签 */
  .floating-label {
    position: absolute;
    top: -10px;
    left: 12px;
    background-color: white;
    padding: 0 8px;
    font-size: 0.8rem;
    color: var(--primary-color);
    border-radius: 4px;
  }
  
  /* 工具提示 */
  .tooltip-icon {
    width: 16px;
    height: 16px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    background-color: var(--neutral-200);
    color: var(--neutral-600);
    font-size: 0.7rem;
    margin-left: 5px;
    cursor: help;
    transition: all 0.2s;
  }
  
  .tooltip-icon:hover {
    background-color: var(--primary-color);
    color: white;
  }
  
  /* 响应式调整 */
  @media (max-width: 768px) {
    .section-card {
      border-radius: var(--radius-md);
    }
    
    .section-card .card-body {
      padding: 1.25rem;
    }
    
    .setting-section {
      padding: 1.25rem;
    }
    
    .stat-value {
      font-size: 1.5rem;
    }
  }
  
  /* 页面滚动条 */
  ::-webkit-scrollbar {
    width: 8px;
    height: 8px;
  }
  
  ::-webkit-scrollbar-track {
    background: var(--neutral-100);
  }
  
  ::-webkit-scrollbar-thumb {
    background: var(--neutral-300);
    border-radius: 10px;
  }
  
  ::-webkit-scrollbar-thumb:hover {
    background: var(--neutral-400);
  }
  
  /* 开关控件样式 */
  .custom-switch {
    position: relative;
    display: inline-block;
    width: 60px;
    height: 30px;
    margin-bottom: 0;
  }

  .custom-switch input { 
    opacity: 0;
    width: 0;
    height: 0;
  }

  .custom-switch .slider {
    position: absolute;
    cursor: pointer;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: var(--neutral-300);
    transition: .4s;
    border-radius: 30px;
  }

  .custom-switch .slider:before {
    position: absolute;
    content: "";
    height: 22px;
    width: 22px;
    left: 4px;
    bottom: 4px;
    background-color: white;
    transition: .4s;
    border-radius: 50%;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  }

  .custom-switch input:checked + .slider {
    background-color: var(--primary-color);
  }

  .custom-switch input:focus + .slider {
    box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.2);
  }

  .custom-switch input:checked + .slider:before {
    transform: translateX(30px);
  }

  /* 开关滑块动画效果 */
  .custom-switch .slider:after {
    position: absolute;
    content: "\2715";
    left: 8px;
    top: 5px;
    color: rgba(0, 0, 0, 0.2);
    font-size: 12px;
    font-weight: bold;
  }

  .custom-switch input:checked + .slider:after {
    content: "\2713";
    left: auto;
    right: 8px;
    color: rgba(255, 255, 255, 0.8);
  }

  /* 鼠标悬停效果 */
  .custom-switch:hover .slider {
    background-color: var(--neutral-400);
  }

  .custom-switch:hover input:checked + .slider {
    background-color: var(--primary-hover);
  }

  .custom-switch .slider:active:before {
    width: 28px;
  }

  .switch-label {
    font-weight: 500;
    margin-left: 15px;
    font-size: 1rem;
    cursor: pointer;
    color: var(--neutral-700);
  }

  .switch-wrapper {
    display: flex;
    align-items: center;
    margin-bottom: 0.75rem;
  }

  .switch-desc {
    margin-top: 0.5rem;
    color: var(--neutral-500);
    font-size: 0.85rem;
    padding-left: 75px;
  }
</style>
</head>
<body>

<div class="container-fluid p-3">
<!-- 主要内容区 -->
<div class="section-card animate-fade-in">
  <div class="card-header">
    <h4 class="card-title"><i class="mdi mdi-cash-register"></i> 返现设置</h4>
  </div>
  
  <div class="card-body">
    <div class="alert alert-info">
      <i class="mdi mdi-information-variant me-2"></i> 设置用户购买商品时的返现比例，提高用户消费积极性。
    </div>
    
    <form id="inviteSettingForm" method="post">
      <!-- 返现设置 -->
      <div class="setting-section stagger-item">
        <div class="setting-section-title">
          <i class="mdi mdi-cash-multiple"></i> 购买返现设置
        </div>
        
        <div class="row g-3">
          <div class="col-md-6 mb-3">
            <label class="form-label">消费返现比例 <span class="tooltip-icon" title="被邀请用户消费时，邀请人可获得的返现比例（积分形式）">?</span></label>
            <div class="input-group">
              <input type="number" class="form-control" name="invite_consume_percent" value="<?php echo $invite_consume_percent; ?>" min="0" max="100" step="0.1">
              <span class="input-group-text">%</span>
            </div>
            <div class="form-text mt-1">邀请人从被邀请用户消费中获得的返现比例</div>
        </div>
        </div>
      </div>
      
      <!-- 保存按钮 -->
      <div class="text-end mt-4">
        <button type="button" class="btn btn-primary btn-action" id="saveSettings">
          <i class="mdi mdi-content-save me-1"></i> 保存设置
        </button>
      </div>
    </form>
  </div>
</div>

<script>
  $(document).ready(function(){
    // 保存设置
    $("#saveSettings").click(function(){
      var $btn = $(this);
      var btnHtml = $btn.html();
      $btn.html('<i class="mdi mdi-loading mdi-spin me-1"></i> 保存中...');
      $btn.prop('disabled', true);
      
      var data = $("#inviteSettingForm").serialize();
      $.ajax({
        type: 'POST',
        url: 'ajax.php?act=saveInviteSettings',
        data: data,
        dataType: 'json',
        success: function(res){
          if(res.code == 0){
            Swal.fire({
              icon: 'success',
              title: '保存成功',
              text: res.msg,
              confirmButtonText: '确定',
              confirmButtonColor: '#10B981'
            });
          } else {
            Swal.fire({
              icon: 'error',
              title: '保存失败',
              text: res.msg,
              confirmButtonText: '确定',
              confirmButtonColor: '#10B981'
            });
          }
          $btn.html(btnHtml);
          $btn.prop('disabled', false);
        },
        error: function(){
          Swal.fire({
            icon: 'error',
            title: '网络错误',
            text: '服务器错误，请稍后再试！',
            confirmButtonText: '确定',
            confirmButtonColor: '#10B981'
          });
          $btn.html(btnHtml);
          $btn.prop('disabled', false);
        }
      });
    });
    
    // 提示工具初始化
    $(document).on('mouseover', '.tooltip-icon', function(){
      var title = $(this).attr('title');
      $(this).tooltip({
        title: title,
        placement: 'top',
        trigger: 'hover',
        container: 'body'
            });
      $(this).tooltip('show');
    });
    
    // 表单元素交互效果
    $('.form-control, .form-select').on('focus', function(){
      $(this).closest('.input-group').css('box-shadow', '0 0 0 3px rgba(16, 185, 129, 0.1)');
    }).on('blur', function(){
      $(this).closest('.input-group').css('box-shadow', 'none');
    });
    
    // 添加动画效果
    $('.stagger-item').each(function(i){
      $(this).css('animation-delay', (i * 0.1) + 's');
    });
  });
</script>
</body>
</html>