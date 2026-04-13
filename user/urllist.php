<?php
include('../includes/common.php');
$title = '网址列表';
include('head.php');
?>
<!-- 添加缺失的Bootstrap Table和niceScroll库 -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-table@1.18.3/dist/bootstrap-table.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap-table@1.18.3/dist/bootstrap-table.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap-table@1.18.3/dist/locale/bootstrap-table-zh-CN.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/jquery.nicescroll@3.7.6/jquery.nicescroll.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
<!-- 添加剪贴板和Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/clipboard@2.0.8/dist/clipboard.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
<!-- 添加layer弹窗组件 -->
<script src="https://cdn.jsdelivr.net/npm/layui-layer@3.5.1/dist/layer.min.js"></script>
<!-- 修复Bootstrap Table图标 -->
<style>
    :root {
        --primary: #2ECC71;
        --primary-light: #5cb85c;
        --primary-dark: #27AE60;
        --secondary: #6c757d;
        --light: #f8f9fa;
        --dark: #343a40;
        --danger: #E74C3C;
        --warning: #F39C12;
        --success: #2ECC71;
        --bg-color: #f5f5f5;
        --card-shadow: 0 5px 20px rgba(0,0,0,0.05);
    }

    body {
        background-color: var(--bg-color);
        color: var(--dark);
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
    }

    .card {
        border-radius: 12px;
        box-shadow: var(--card-shadow);
        border: none;
        margin-bottom: 30px;
        background: white;
    }

    .card-body {
        padding: 25px;
    }

    /* 表格样式 */
    .table {
        color: #333;
        border-radius: 8px;
        overflow: hidden;
    }

    .bootstrap-table .table thead th {
        background-color: var(--light);
        color: #495057;
        font-weight: 600;
        border-bottom: 2px solid #dee2e6;
    }

    .bootstrap-table .table tbody tr:hover {
        background-color: rgba(46, 204, 113, 0.05);
    }

    /* 按钮样式 */
    .btn {
        border-radius: 6px;
        font-weight: 500;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 5px;
        transition: all 0.3s;
    }

    .btn i {
        font-size: 1.1em;
    }

    .btn-primary {
        background-color: var(--primary);
        border-color: var(--primary);
    }

    .btn-primary:hover, .btn-primary:focus {
        background-color: var(--primary-dark);
        border-color: var(--primary-dark);
        box-shadow: 0 5px 15px rgba(46, 204, 113, 0.3);
        transform: translateY(-2px);
    }

    .btn-warning {
        background-color: var(--warning);
        border-color: var(--warning);
        color: #212529;
    }

    .btn-warning:hover, .btn-warning:focus {
        background-color: #e0a800;
        border-color: #e0a800;
        box-shadow: 0 5px 15px rgba(255, 193, 7, 0.3);
        transform: translateY(-2px);
    }

    .btn-danger {
        background-color: var(--danger);
        border-color: var(--danger);
    }

    .btn-danger:hover, .btn-danger:focus {
        background-color: #c82333;
        border-color: #c82333;
        box-shadow: 0 5px 15px rgba(220, 53, 69, 0.3);
        transform: translateY(-2px);
    }

    .btn-success {
        background-color: var(--success);
        border-color: var(--success);
    }

    .btn-success:hover, .btn-success:focus {
        background-color: var(--primary-dark);
        border-color: var(--primary-dark);
        box-shadow: 0 5px 15px rgba(46, 204, 113, 0.3);
        transform: translateY(-2px);
    }

    .btn-default {
        color: #495057;
        border-color: #ced4da;
        background-color: #fff;
    }

    .btn-default:hover, .btn-default:focus {
        background-color: #f8f9fa;
        border-color: #adb5bd;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        transform: translateY(-2px);
    }

    .btn-xs {
        padding: 0.15rem 0.4rem;
        font-size: 0.75rem;
        line-height: 1.5;
    }

    /* 模态框样式 - 增强版 */
    .modal-content {
        border-radius: 12px;
        border: none;
        box-shadow: 0 15px 40px rgba(0,0,0,0.1);
        overflow: hidden;
        animation: modalFadeIn 0.3s ease-out;
    }

    @keyframes modalFadeIn {
        from {
            opacity: 0;
            transform: translateY(-20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .modal-backdrop.show {
        opacity: 0.7;
    }

    .modal-header {
        border-bottom: 1px solid rgba(0,0,0,0.05);
        background-color: var(--light);
        padding: 18px 24px;
        display: flex;
        align-items: center;
    }

    .modal-header .close {
        padding: 0;
        margin: 0;
        width: 30px;
        height: 30px;
        border-radius: 50%;
        background-color: rgba(0,0,0,0.05);
        opacity: 0.7;
        transition: all 0.2s;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.4rem;
        font-weight: normal;
        outline: none;
    }

    .modal-header .close:hover {
        background-color: rgba(0,0,0,0.1);
        opacity: 1;
        transform: rotate(90deg);
    }

    .modal-title {
        font-weight: 600;
        font-size: 20px;
        color: var(--dark);
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .modal-title i {
        color: var(--primary);
    }

    .modal-body {
        padding: 24px;
    }

    .modal-body form {
        margin-bottom: 0;
    }

    .modal-footer {
        border-top: 1px solid rgba(0,0,0,0.05);
        padding: 15px 24px;
        justify-content: space-between;
    }

    .modal-dialog {
        margin-top: 50px;
        max-width: 550px;
    }

    /* 表单控件增强 */
    .form-group {
        margin-bottom: 20px;
        position: relative;
    }

    .form-group label {
        display: block;
        margin-bottom: 8px;
        font-weight: 500;
        color: #495057;
        font-size: 14px;
    }

    .form-control {
        border-radius: 8px;
        border: 1px solid #ddd;
        padding: 12px 15px;
        transition: all 0.3s;
        font-size: 15px;
        background-color: #fff;
        height: auto;
        box-shadow: 0 1px 3px rgba(0,0,0,0.02);
    }

    .form-control:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(46, 204, 113, 0.2);
    }

    select.form-control {
        appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='%23495057' class='bi bi-chevron-down' viewBox='0 0 16 16'%3E%3Cpath fill-rule='evenodd' d='M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 15px center;
        background-size: 14px;
        padding-right: 40px;
    }

    .help-block {
        color: var(--secondary);
        font-size: 13px;
        margin-top: 6px;
        font-style: italic;
    }

    /* 特定模态框样式 */
    #addUrl .modal-content, 
    #editUrl .modal-content {
        max-height: 80vh;
    }

    #addUrl .modal-body,
    #editUrl .modal-body {
        overflow-y: auto;
        max-height: calc(80vh - 120px);
        scrollbar-width: thin;
        scrollbar-color: rgba(0,0,0,0.2) transparent;
    }

    #addUrl .modal-body::-webkit-scrollbar,
    #editUrl .modal-body::-webkit-scrollbar {
        width: 6px;
    }

    #addUrl .modal-body::-webkit-scrollbar-track,
    #editUrl .modal-body::-webkit-scrollbar-track {
        background: transparent;
    }

    #addUrl .modal-body::-webkit-scrollbar-thumb,
    #editUrl .modal-body::-webkit-scrollbar-thumb {
        background-color: rgba(0,0,0,0.2);
        border-radius: 3px;
    }

    #up_url .modal-dialog {
        max-width: 450px;
    }

    /* 批量操作下拉菜单 */
    .dropdown-menu {
        border-radius: 8px;
        border: none;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        padding: 8px 0;
    }

    .dropdown-item {
        padding: 8px 15px;
        display: flex;
        align-items: center;
        gap: 8px;
        transition: all 0.2s;
    }

    .dropdown-item i {
        font-size: 1em;
    }

    .dropdown-item:hover, .dropdown-item:focus {
        background-color: rgba(46, 204, 113, 0.1);
        color: var(--primary);
    }

    .dropdown-item.active, .dropdown-item:active {
        background-color: var(--primary);
        color: #fff;
    }

    /* 标题和导航 */
    .section-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 20px;
        padding-bottom: 15px;
        border-bottom: 1px solid rgba(0,0,0,0.05);
    }

    .section-title {
        font-size: 24px;
        font-weight: 700;
        color: var(--dark);
        margin: 0;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .section-actions {
        display: flex;
        gap: 10px;
    }

    /* 帮助文本 */
    .help-block {
        color: var(--secondary);
        font-size: 13px;
        margin-top: 6px;
        font-style: italic;
    }

    /* 标签样式 */
    .badge {
        padding: 0.4rem 0.6rem;
        border-radius: 50px;
        font-weight: 500;
        font-size: 0.75rem;
    }

    .badge-success {
        background-color: var(--success);
        color: white;
    }

    .badge-danger {
        background-color: var(--danger);
        color: white;
    }

    .badge-primary {
        background-color: var(--primary);
        color: white;
    }

    .badge-warning {
        background-color: var(--warning);
        color: #212529;
    }

    .badge-info {
        background-color: #17a2b8;
        color: white;
    }

    .badge-secondary {
        background-color: var(--secondary);
        color: white;
    }

    /* 表格工具栏 */
    #toolbar {
        margin-bottom: 1rem;
    }

    /* 自定义滚动条 */
    .nicescroll-rails {
        z-index: 9999 !important;
    }

    /* 搜索框 */
    .fixed-table-toolbar .search .search-input {
        border-radius: 8px;
        border: 1px solid #ddd;
        padding: 10px 15px;
        transition: all 0.3s;
    }

    .fixed-table-toolbar .search .search-input:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(46, 204, 113, 0.2);
    }

    /* 表格分页 */
    .fixed-table-pagination .pagination-detail, 
    .fixed-table-pagination .pagination {
        margin-top: 1rem;
    }

    .pagination .page-item.active .page-link {
        background-color: var(--primary);
        border-color: var(--primary);
    }

    .pagination .page-link {
        color: var(--primary);
    }

    .pagination .page-link:hover {
        color: var(--primary-dark);
    }

    /* 适配移动设备 */
    @media (max-width: 768px) {
        .card-body {
            padding: 15px;
        }
        
        .section-header {
            flex-direction: column;
            align-items: flex-start;
        }
        
        .section-actions {
            margin-top: 10px;
            flex-wrap: wrap;
        }
        
        .section-actions .btn {
            margin-bottom: 5px;
        }
    }

    /* URL展示样式 */
    .url-item {
        display: inline-flex;
        align-items: center;
        max-width: 100%;
        background-color: #f8f9fa;
        border-radius: 8px;
        padding: 6px 10px;
        transition: all 0.2s ease;
        border: 1px solid #eaeaea;
        text-decoration: none;
        color: #333;
        overflow: hidden;
    }
    
    .url-item:hover {
        background-color: #e9ecef;
        box-shadow: 0 2px 5px rgba(0,0,0,0.08);
        transform: translateY(-1px);
        color: var(--primary);
        border-color: var(--primary-light);
    }
    
    .url-item i {
        margin-right: 8px;
        font-size: 14px;
        color: var(--primary);
    }
    
    .url-text {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 100%;
    }
    
    .url-short {
        font-weight: 500;
    }
    
    .long-url-container {
        max-width: 200px;
        position: relative;
    }
    
    .long-url-container .url-item {
        width: 100%;
    }
    
    .copy-tooltip {
        position: absolute;
        bottom: -25px;
        left: 50%;
        transform: translateX(-50%);
        background: rgba(0,0,0,0.7);
        color: white;
        padding: 3px 8px;
        border-radius: 4px;
        font-size: 12px;
        opacity: 0;
        transition: opacity 0.3s, transform 0.3s;
        pointer-events: none;
        white-space: nowrap;
    }
    
    .url-item:active + .copy-tooltip {
        opacity: 1;
    }

    .bootstrap-table .fixed-table-toolbar .bs-bars,
    .bootstrap-table .fixed-table-toolbar .search,
    .bootstrap-table .fixed-table-toolbar .columns {
        position: relative;
        margin-top: 10px;
        margin-bottom: 10px;
    }

    .bootstrap-table .fixed-table-toolbar .columns .btn-group>.btn-group {
        display: inline-block;
        margin-left: -1px !important;
    }

    .bootstrap-table .fixed-table-toolbar .columns button.btn-default,
    .bootstrap-table .fixed-table-toolbar .columns button.btn-default.dropdown-toggle {
        padding: 0.375rem 0.75rem;
        font-size: 1rem;
        line-height: 1.5;
    }

    /* 修复图标显示 */
    .bootstrap-table .fixed-table-toolbar .columns div.dropdown-menu {
        max-height: 300px;
        overflow: auto;
        z-index: 1000;
    }

    .bootstrap-table .icon-refresh::before {
        content: "\F133"!important;
        font-family: 'bootstrap-icons'!important;
    }

    .bootstrap-table .icon-toggle-off::before {
        content: "\F292"!important;
        font-family: 'bootstrap-icons'!important;
    }

    .bootstrap-table .icon-toggle-on::before {
        content: "\F293"!important;
        font-family: 'bootstrap-icons'!important;
    }

    .bootstrap-table .icon-list::before {
        content: "\F649"!important;
        font-family: 'bootstrap-icons'!important;
    }

    .bootstrap-table .icon-search::before {
        content: "\F52A"!important;
        font-family: 'bootstrap-icons'!important;
    }

    .btn-group .btn-secondary {
        color: #333;
        background-color: #fff;
        border-color: #ccc;
    }

    .btn-group .btn-secondary:hover {
        color: #333;
        background-color: #e6e6e6;
        border-color: #adadad;
    }

    /* 卡片视图样式 - 全新精致设计 */
    .bootstrap-table .card-view {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
        gap: 24px;
        padding: 20px;
        background-color: #f9fafc;
    }
    
    .bootstrap-table .card-view .card {
        position: relative;
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.04), 0 6px 10px rgba(0, 0, 0, 0.02);
        overflow: hidden;
        transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
        border: none;
        display: flex;
        flex-direction: column;
    }
    
    .bootstrap-table .card-view .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.06), 0 8px 15px rgba(0, 0, 0, 0.03);
    }
    
    /* 卡片顶部状态指示器 */
    .bootstrap-table .card-view .card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 5px;
        background: linear-gradient(135deg, #2ECC71, #1abc9c);
        opacity: 0.9;
    }
    
    /* 卡片内容容器 */
    .bootstrap-table .card-view .card-content {
        padding: 24px;
    }
    
    /* ID字段的特殊样式 */
    .bootstrap-table .card-view .card-body.id-field {
        padding: 0;
        border: none;
        margin: 0;
    }
    
    .bootstrap-table .card-view .card-body.id-field .title {
        display: none;
    }
    
    .bootstrap-table .card-view .card-body.id-field .value {
        position: absolute;
        top: 14px;
        right: 14px;
        background-color: rgba(0, 0, 0, 0.05);
        color: rgba(0, 0, 0, 0.5);
        font-size: 12px;
        padding: 4px 10px;
        border-radius: 20px;
        font-weight: 500;
        backdrop-filter: blur(5px);
        -webkit-backdrop-filter: blur(5px);
    }
    
    /* 短网址字段样式 */
    .bootstrap-table .card-view .card-body:nth-child(2) {
        order: -1;
        padding: 0 0 16px 0;
        margin-bottom: 16px;
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
    }
    
    .bootstrap-table .card-view .card-body:nth-child(2) .title {
        margin-bottom: 8px;
        color: rgba(0, 0, 0, 0.4);
        font-size: 10px;
        text-transform: uppercase;
        letter-spacing: 1px;
        font-weight: 600;
    }
    
    .bootstrap-table .card-view .card-body:nth-child(2) .value {
        font-size: 16px;
        font-weight: 600;
        letter-spacing: -0.2px;
    }
    
    /* 卡片主体内容 */
    .bootstrap-table .card-view .card-body {
        padding: 8px 0;
        display: flex;
        flex-direction: row;
        align-items: center;
        border-bottom: none;
        margin-bottom: 4px;
    }
    
    .bootstrap-table .card-view .card-body:last-child {
        border-top: 1px solid rgba(0, 0, 0, 0.05);
        margin-top: auto;
        padding-top: 16px;
        margin-bottom: 0;
    }
    
    /* 字段标题和内容样式 */
    .bootstrap-table .card-view .title {
        font-size: 11px;
        color: rgba(0, 0, 0, 0.5);
        margin-bottom: 0;
        font-weight: 500;
        width: 80px;
        flex-shrink: 0;
    }
    
    .bootstrap-table .card-view .value {
        color: rgba(0, 0, 0, 0.8);
        font-size: 13px;
        word-break: break-word;
        flex-grow: 1;
    }
    
    /* 卡片内的URL样式 */
    .bootstrap-table .card-view .value .url-item,
    .bootstrap-table .card-view .value .card-url-item {
        position: relative;
        display: inline-flex !important;
        align-items: center;
        padding: 10px 14px;
        background-color: #f3f9f6;
        border-radius: 10px;
        border: 1px solid rgba(46, 204, 113, 0.15);
        color: #1a7e45;
        text-decoration: none;
        font-size: 13px;
        width: 100%;
        margin: 3px 0;
        transition: all 0.3s;
    }
    
    .bootstrap-table .card-view .value .url-item:hover,
    .bootstrap-table .card-view .value .card-url-item:hover {
        background-color: #e8f6ef;
        border-color: rgba(46, 204, 113, 0.3);
        color: #16a085;
        transform: translateY(-2px);
        box-shadow: 0 3px 10px rgba(46, 204, 113, 0.1);
    }
    
    .bootstrap-table .card-view .value .url-item i,
    .bootstrap-table .card-view .value .card-url-item i {
        margin-right: 10px;
        color: #2ECC71;
        font-size: 16px;
    }
    
    /* 类型和状态标签 */
    .bootstrap-table .card-view .value .badge,
    .bootstrap-table .card-view .value .card-badge {
        padding: 5px 12px;
        border-radius: 8px;
        font-weight: 500;
        font-size: 12px;
        display: inline-flex;
        align-items: center;
        margin: 3px 0;
        letter-spacing: 0.3px;
    }
    
    .bootstrap-table .card-view .value .badge i,
    .bootstrap-table .card-view .value .card-badge i {
        margin-right: 5px;
    }
    
    .bootstrap-table .card-view .value .badge-primary {
        background-color: rgba(26, 188, 156, 0.15);
        color: #16a085;
    }
    
    .bootstrap-table .card-view .value .badge-success {
        background-color: rgba(46, 204, 113, 0.15);
        color: #27ae60;
    }
    
    .bootstrap-table .card-view .value .badge-info {
        background-color: rgba(52, 152, 219, 0.15);
        color: #2980b9;
    }
    
    .bootstrap-table .card-view .value .badge-warning {
        background-color: rgba(241, 196, 15, 0.15);
        color: #f39c12;
    }
    
    .bootstrap-table .card-view .value .badge-danger {
        background-color: rgba(231, 76, 60, 0.15);
        color: #c0392b;
    }
    
    /* 备注和日期字段 */
    .bootstrap-table .card-view .remarks-field {
        margin-top: 8px;
        padding: 12px;
        background-color: #f9f9fb;
        border-radius: 10px;
        margin-bottom: 16px !important;
    }
    
    .bootstrap-table .card-view .remarks-field .title {
        color: rgba(0, 0, 0, 0.4);
        font-size: 10px;
        width: auto;
        margin-bottom: 4px;
        display: block;
    }
    
    .bootstrap-table .card-view .remarks-field .value {
        color: rgba(0, 0, 0, 0.6);
        font-size: 12px;
        font-style: italic;
        line-height: 1.5;
    }
    
    .bootstrap-table .card-view .date-field .value {
        color: rgba(0, 0, 0, 0.5);
        font-size: 12px;
        display: flex;
        align-items: center;
    }
    
    .bootstrap-table .card-view .date-field .value i {
        margin-right: 5px;
        opacity: 0.7;
    }
    
    /* 访问计数字段 */
    .bootstrap-table .card-view .visits-field {
        padding: 0;
    }
    
    .bootstrap-table .card-view .visits-field .value {
        display: inline-flex;
        align-items: center;
        background-color: rgba(46, 204, 113, 0.1);
        color: #27ae60;
        padding: 4px 10px;
        border-radius: 20px;
        font-weight: 500;
        font-size: 12px;
    }
    
    .bootstrap-table .card-view .visits-field .value i {
        color: #2ECC71;
        margin-right: 5px;
    }
    
    /* 操作按钮区域 */
    .bootstrap-table .card-view .actions-field {
        border-top: 1px solid rgba(0, 0, 0, 0.05);
        margin-top: auto;
        padding-top: 16px;
        padding-bottom: 0;
    }
    
    .bootstrap-table .card-view .actions-field .title {
        display: none;
    }
    
    .bootstrap-table .card-view .value .btn-group,
    .bootstrap-table .card-view .value .card-buttons {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(40px, 1fr));
        gap: 8px;
        width: 100%;
    }
    
    .bootstrap-table .card-view .value .btn-group .btn,
    .bootstrap-table .card-view .value .card-buttons .btn {
        padding: 8px;
        border-radius: 10px;
        font-size: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s;
        border: none;
        background-color: #f8f9fa;
        color: #495057;
    }
    
    .bootstrap-table .card-view .value .btn-group .btn:hover,
    .bootstrap-table .card-view .value .card-buttons .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
    }
    
    .bootstrap-table .card-view .value .btn-group .btn i,
    .bootstrap-table .card-view .value .card-buttons .btn i {
        font-size: 14px;
    }
    
    /* 特定按钮样式 */
    .bootstrap-table .card-view .value .btn-danger {
        background-color: rgba(231, 76, 60, 0.1);
        color: #e74c3c;
    }
    
    .bootstrap-table .card-view .value .btn-danger:hover {
        background-color: #e74c3c;
        color: white;
        box-shadow: 0 4px 10px rgba(231, 76, 60, 0.25);
    }
    
    /* 复制提示样式 */
    .bootstrap-table .card-view .copy-tooltip {
        position: absolute;
        bottom: -25px;
        left: 50%;
        transform: translateX(-50%);
        background: rgba(0, 0, 0, 0.75);
        color: white;
        padding: 5px 10px;
        border-radius: 20px;
        font-size: 11px;
        opacity: 0;
        transition: all 0.3s;
        pointer-events: none;
        white-space: nowrap;
        backdrop-filter: blur(5px);
        -webkit-backdrop-filter: blur(5px);
        box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
    }
    
    .bootstrap-table .card-view .url-item:hover .copy-tooltip,
    .bootstrap-table .card-view .card-url-item:hover .copy-tooltip {
        opacity: 1;
        transform: translate(-50%, -5px);
    }
    
    /* 让URL溢出时显示省略号 */
    .bootstrap-table .card-view .url-text {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 100%;
    }
    
    /* 卡片加载动画 */
    @keyframes cardAppear {
        0% {
            opacity: 0;
            transform: translateY(30px) scale(0.95);
        }
        60% {
            transform: translateY(-5px) scale(1.02);
        }
        100% {
            opacity: 1;
            transform: translateY(0) scale(1);
        }
    }
    
    .bootstrap-table .card-view .card {
        opacity: 0;
        animation: cardAppear 0.6s cubic-bezier(0.2, 0.8, 0.2, 1) forwards;
    }
    
    /* 添加延迟加载动画 */
    .bootstrap-table .card-view .card:nth-child(1) { animation-delay: 0.05s; }
    .bootstrap-table .card-view .card:nth-child(2) { animation-delay: 0.1s; }
    .bootstrap-table .card-view .card:nth-child(3) { animation-delay: 0.15s; }
    .bootstrap-table .card-view .card:nth-child(4) { animation-delay: 0.2s; }
    .bootstrap-table .card-view .card:nth-child(5) { animation-delay: 0.25s; }
    .bootstrap-table .card-view .card:nth-child(6) { animation-delay: 0.3s; }
    .bootstrap-table .card-view .card:nth-child(7) { animation-delay: 0.35s; }
    .bootstrap-table .card-view .card:nth-child(8) { animation-delay: 0.4s; }
    
    /* 响应式调整 */
    @media (max-width: 576px) {
        .bootstrap-table .card-view {
            grid-template-columns: 1fr;
            padding: 15px;
            gap: 15px;
        }
        
        .bootstrap-table .card-view .card-content {
            padding: 18px;
        }
    }
    
    @media (min-width: 1400px) {
        .bootstrap-table .card-view {
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
        }
    }

    /* 操作按钮新样式 */
    .action-buttons {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
        justify-content: flex-start;
    }

    .action-btn {
        border: none;
        background: transparent;
        color: #666;
        width: 34px;
        height: 34px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s;
        font-size: 15px;
        cursor: pointer;
        position: relative;
        overflow: hidden;
    }

    .action-btn::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: currentColor;
        opacity: 0.12;
        border-radius: 8px;
        transition: opacity 0.2s;
    }

    .action-btn:hover::before {
        opacity: 0.18;
    }

    .action-btn:active::before {
        opacity: 0.25;
    }

    .action-btn i {
        position: relative;
        z-index: 1;
    }

    .action-btn-qrcode {
        color: #17a2b8;
    }

    .action-btn-edit {
        color: #07C160;
    }

    .action-btn-data {
        color: #6610f2;
    }

    .action-btn-info {
        color: #fd7e14;
    }

    .action-btn-delete {
        color: #dc3545;
    }

    .action-btn-tooltip {
        position: absolute;
        top: -30px;
        left: 50%;
        transform: translateX(-50%) translateY(10px);
        background: rgba(0,0,0,0.75);
        color: #fff;
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 12px;
        opacity: 0;
        pointer-events: none;
        transition: all 0.2s;
        white-space: nowrap;
        z-index: 10;
    }

    .action-btn:hover .action-btn-tooltip {
        opacity: 1;
        transform: translateX(-50%) translateY(0);
    }

    .bootstrap-table .card-view .action-buttons {
        justify-content: space-between;
        margin-top: 8px;
    }

    .bootstrap-table .card-view .action-btn {
        width: 40px;
        height: 40px;
        font-size: 16px;
    }

    @media (max-width: 576px) {
        .action-buttons {
            justify-content: space-between;
        }
        
        .action-btn {
            width: 38px;
            height: 38px;
        }
    }
</style>

<section id="main-content">
    <section class="wrapper">
        <div class="modal fade" align="left" id="up_url" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="myModalLabel"><i class="bi bi-pencil-square"></i> 一键修改</h4>
                        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">关闭</span></button>
                    </div>
                    <div class="modal-body">
                        <input type="text" class="form-control" name="ckurl" placeholder="请输入修改网址"><br />
                        <button type="button" class="btn btn-primary btn-block" id="update_submit"><?php echo $vip == 1 ? '<i class="bi bi-check2-all"></i> 一键修改' : '<i class="bi bi-x-circle"></i> 您不是会员' ?></button>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal"><i class="bi bi-x"></i> 关闭</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div aria-hidden="true" aria-labelledby="addUrlLabel" role="dialog" tabindex="-1" id="addUrl" class="modal fade">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title"><i class="bi bi-plus-circle"></i> 添加网址</h4>
                            <button aria-hidden="true" data-dismiss="modal" class="close" type="button">&times;</button>
                        </div>
                        <div class="modal-body">
                            <form>
                                <div class="form-group" id="add10">
                                    <label>自定义后缀:</label>
                                    <input type="text" name="id" value="" id="add-id" class="form-control" />
                                    <p class="help-block">随机后缀请留空，部分短网址不支持</p>
                                </div>
                                <div class="form-group">
                                    <label>默认跳转:</label>
                                    <input type="text" name="url" value="" id="add-url" class="form-control" placeholder="请输入需要跳转的网址" required />
                                </div>
                                <div class="form-group">
                                    <label>短链类型:</label>
                                    <select class="form-control" id="add-type" name="type">
                                        <?php
                                        echo dwzList();
                                        ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>跳转类型:</label>
                                    <select class="form-control" name="pattern" id="add-pattern" onchange="tz_pattern()">
                                        <?php
                                        echo pattern_list();
                                        ?>
                                    </select>
                                </div>
                                <div class="form-group" id="add9">
                                    <label>跳转模板:</label>
                                    <select class="form-control" id="add-jumpmb" name="jumpmb">
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
                                <div class="form-group" id="add1">
                                    <label>直链标题:</label>
                                    <input type="text" name="title" value="" id="add-title" class="form-control" />
                                    <p class="help-block">不填则自动获取</p>
                                </div>
                                <div class="form-group" id="add2">
                                    <label>备注:</label>
                                    <input type="text" name="remarks" value="" id="add-remarks" class="form-control" />
                                </div>
                             <!--   <div class="form-group" id="add3">
                                    <label>访问密码:</label>
                                    <input type="text" name="pwd" value="" id="add-pwd" class="form-control" />
                                    <p class="help-block">不需要密码访问请留空</p>
                                </div> -->
                                <div class="form-group" id="add4">
                                    <label>限制访问次数:</label>
                                    <input type="number" name="visit" value="" id="add-visit" class="form-control" />
                                </div>
                                <div class="form-group" id="add5">
                                    <label>达到次数访问网址:</label>
                                    <input type="text" name="visiturl" value="" id="add-visiturl" class="form-control" />
                                    <p class="help-block">留空的话达到访问次数直接停止该网址访问</p>
                                </div>
                                <div class="form-group" id="add6">
                                    <label>QQ跳转:</label>
                                    <input type="text" name="qqjump" value="" id="add-qqjump" class="form-control" />
                                    <p class="help-block">QQ无需特别跳转请留空</p>
                                </div>
                                <div class="form-group" id="add7">
                                    <label>微信跳转:</label>
                                    <input type="text" name="wxjump" value="" id="add-wxjump" class="form-control" />
                                    <p class="help-block">微信无需特别跳转请留空</p>
                                </div>
                                <div class="form-group" id="add8">
                                    <label>支付宝跳转:</label>
                                    <input type="text" name="alijump" value="" id="add-alijump" class="form-control" />
                                    <p class="help-block">支付宝无需特别跳转请留空</p>
                                </div>
                                <button type="button" onclick="insertUrl()" class="btn btn-primary btn-block"><i class="bi bi-check-lg"></i> 确认添加</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div aria-hidden="true" aria-labelledby="editUrlLabel" role="dialog" tabindex="-1" id="editUrl" class="modal fade">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title"><i class="bi bi-pencil"></i> 修改网址</h4>
                            <button aria-hidden="true" data-dismiss="modal" class="close" type="button">&times;</button>
                        </div>
                        <div class="modal-body">
                            <form>
                                <div class="form-group" style="display: none">
                                    <label>ID：</label>
                                    <input type="text" class="form-control" id="edit-id" readonly>
                                </div>
                                <div class="form-group">
                                    <label>短链接：</label>
                                    <input type="text" class="form-control" id="edit-dwz" readonly>
                                </div>
                                <div class="form-group" id="edit1">
                                    <label>默认跳转:</label>
                                    <input type="text" name="url" value="" id="edit-url" class="form-control" placeholder="请输入需要跳转的网址" required />
                                </div>
                                <div class="form-group" id="edit2">
                                    <label>跳转类型:</label>
                                    <select class="form-control" name="pattern" id="edit-pattern" onchange="edit_pattern()">
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
                                    <label>跳转模板:</label>
                                    <select class="form-control" id="edit-jumpmb" name="jumpmb">
                                        <?php
                                        $mblist = Template::getList2();
                                        foreach ($mblist as $row) {
                                            echo '<option value="' . $row . '">' . $row . '</option>';
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="form-group" id="edit4">
                                    <label>直链标题:</label>
                                    <input type="text" name="title" value="" id="edit-title" class="form-control" />
                                    <p class="help-block">不填则自动获取</p>
                                </div>
                                <div class="form-group" id="edit5">
                                    <label>备注:</label>
                                    <input type="text" name="remarks" value="" id="edit-remarks" class="form-control" />
                                </div>
                           <!--     <div class="form-group" id="edit6">
                                    <label>访问密码:</label>
                                    <input type="text" name="pwd" value="" id="edit-pwd" class="form-control" />
                                    <p class="help-block">不需要密码访问请留空</p>
                                </div>-->
                                <div class="form-group" id="edit7">
                                    <label>限制访问次数:</label>
                                    <input type="number" name="visit" value="" id="edit-visit" class="form-control" />
                                </div>
                                <div class="form-group" id="edit8">
                                    <label>达到次数访问网址:</label>
                                    <input type="text" name="visiturl" value="" id="edit-visiturl" class="form-control" />
                                    <p class="help-block">留空的话达到访问次数直接停止该网址访问</p>
                                </div>
                                <div class="form-group" id="edit9">
                                    <label>QQ跳转:</label>
                                    <input type="text" name="qqjump" value="" id="edit-qqjump" class="form-control" />
                                    <p class="help-block">QQ无需特别跳转请留空</p>
                                </div>
                                <div class="form-group" id="edit10">
                                    <label>微信跳转:</label>
                                    <input type="text" name="wxjump" value="" id="edit-wxjump" class="form-control" />
                                    <p class="help-block">微信无需特别跳转请留空</p>
                                </div>
                                <div class="form-group" id="edit11">
                                    <label>支付宝跳转:</label>
                                    <input type="text" name="alijump" value="" id="edit-alijump" class="form-control" />
                                    <p class="help-block">支付宝无需特别跳转请留空</p>
                                </div>
                                <button type="button" onclick="updateUrl()" class="btn btn-primary btn-block"><i class="bi bi-check-lg"></i> 确认修改</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-12">
                <section class="panel">
                    <div class="card">
                        <div class="card-body">
                            <div class="section-header">
                                <h2 class="section-title"><i class="bi bi-link-45deg"></i> 网址列表</h2>
                            </div>
                            <div id="toolbar" class="section-actions">
                                <a href="#addUrl" data-toggle="modal" class="btn btn-primary"><i class="bi bi-plus-lg"></i> 添加网址</a>
                                <div class="btn-group">
                                    <a class="btn btn-warning" href="#" data-toggle="dropdown">
                                        <i class="bi bi-gear"></i> 批量操作
                                        <i class="bi bi-chevron-down"></i>
                                    </a>
                                    <ul class="dropdown-menu">
                                        <li><a href="#" onclick="datadel()" class="dropdown-item"><i class="bi bi-trash"></i> 一键删除</a></li>
                                        <li><a href="#" data-toggle="modal" data-target="#up_url" id="up_url" class="dropdown-item"><i class="bi bi-pencil"></i> 一键修改</a></li>
                                        <li><a href="#" onclick="setactiveAll(1)" class="dropdown-item"><i class="bi bi-toggle-on"></i> 一键开启</a></li>
                                        <li><a href="#" onclick="setactiveAll(0)" class="dropdown-item"><i class="bi bi-toggle-off"></i> 一键关闭</a></li>
                                    </ul>
                                </div>
                            </div>
                            <table id="listTable"></table>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </section>
</section>
<script src="../static/user/js/common-scripts.js"></script>
<script>
    // 确保layer可用的辅助函数
    function showMsg(text, callback) {
        if (typeof layer !== 'undefined') {
            layer.msg(text, callback);
        } else {
            alert(text);
            if (callback && typeof callback === 'function') {
                callback();
            }
        }
    }
    
    function showAlert(text) {
        if (typeof layer !== 'undefined') {
            layer.alert(text);
        } else {
            alert(text);
        }
    }
    
    function showConfirm(text, confirmCallback, cancelCallback) {
        if (typeof layer !== 'undefined') {
            layer.confirm(text, {
                btn: ['确定', '取消']
            }, confirmCallback, cancelCallback);
        } else {
            if (confirm(text)) {
                if (confirmCallback && typeof confirmCallback === 'function') {
                    confirmCallback();
                }
            } else {
                if (cancelCallback && typeof cancelCallback === 'function') {
                    cancelCallback();
                }
            }
        }
    }

    function initTable() {
        $('#listTable').bootstrapTable('destroy');
        $('#listTable').bootstrapTable({
            classes: 'table table-hover',
            url: 'ajax.php?act=urllist',
            method: 'get',
            dataType: 'jsonp',
            uniqueId: 'id',
            selectItemName: 'ids[]',
            idField: 'id',
            toolbar: '#toolbar',
            showColumns: false,
            showRefresh: true,
            showToggle: true,
            icons: {
                refresh: 'bi bi-arrow-clockwise',
                toggleOff: 'bi bi-layout-text-window-reverse',
                toggleOn: 'bi bi-grid-fill',
                columns: 'bi bi-list-ul'
            },
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
                },
                {
                    field: 'dwz',
                    title: '短网址',
                    formatter: function(value, row, index) {
                        return '<div class="url-container">' + 
                               '<a href="javascript:void(0)" class="url-item url-short clipboard" data-clipboard-text="' + row['dwz'] + '">' + 
                               '<i class="bi bi-link-45deg"></i>' + 
                               '<span class="url-text">' + row['dwz'] + '</span>' + 
                               '</a>' +
                               '<div class="copy-tooltip">已复制!</div>' +
                               '</div>';
                    }
                },
                {
                    field: 'url',
                    title: '跳转网址',
                    formatter: function(value, row, index) {
                        // 截断显示过长的URL
                        var displayUrl = value;
                        if (value.length > 30) {
                            displayUrl = value.substring(0, 27) + '...';
                        }
                        
                        return '<div class="long-url-container">' + 
                               '<a href="javascript:void(0)" title="' + value + '" class="url-item clipboard" data-clipboard-text="' + row['url'] + '">' + 
                               '<i class="bi bi-box-arrow-up-right"></i>' + 
                               '<span class="url-text">' + displayUrl + '</span>' + 
                               '</a>' +
                               '<div class="copy-tooltip">已复制!</div>' +
                               '</div>';
                    }
                },
                {
                    field: 'pattern',
                    title: '类型',
                    formatter: function(value, row, index) {
                        let patternClass = '';
                        let pattern = '';
                        switch (row['pattern']) {
                            case '1':
                                pattern = '普通';
                                patternClass = 'badge-primary';
                                break;
                            case '2':
                                pattern = '防红';
                                patternClass = 'badge-success';
                                break;
                            case '3':
                                pattern = '直链';
                                patternClass = 'badge-info';
                                break;
                            case '4':
                                pattern = '直跳';
                                patternClass = 'badge-warning';
                                break;
                            default:
                                pattern = '未知';
                                patternClass = 'badge-secondary';
                        }
                        return '<span class="badge ' + patternClass + '">' + pattern + '</span>';
                    },
                    sortable: true
                },
                {
                    field: 'view',
                    title: '访问',
                    formatter: function(value, row, index) {
                        return '<span><i class="bi bi-eye"></i> ' + value + '</span>';
                    },
                    sortable: true
                },
                {
                    field: 'remarks',
                    title: '备注',
                    cellStyle: function(value, row, index) {
                        return {
                            css: {
                                "white-space": "nowrap",
                                "text-overflow": "ellipsis",
                                "overflow": "hidden",
                                "max-width": "100px"
                            }
                        }
                    },
                    formatter: function(value, row, index) {
                        var span = document.createElement("span");
                        span.setAttribute("title", value);
                        span.innerHTML = value ? '<i class="bi bi-card-text"></i> ' + value : '<i class="bi bi-card-text"></i> 无备注';
                        return span.outerHTML;
                    },
                    sortable: true
                },
                {
                    field: 'u_state',
                    title: '状态',
                    formatter: function(value, row, index) {
                        var value = "";
                        if (row.u_state == '0') {
                            value = '<span class="badge badge-danger" onclick="setActive(\'' + row['id'] + '\',' + row['u_state'] + ')" style="cursor:pointer;"><i class="bi bi-x-circle"></i> 已关闭</span>';
                        } else {
                            value = '<span class="badge badge-success" onclick="setActive(\'' + row['id'] + '\',' + row['u_state'] + ')" style="cursor:pointer;"><i class="bi bi-check-circle"></i> 已开启</span>';
                        }
                        return value;
                    },
                    sortable: true
                },
                {
                    field: 'addtime',
                    title: '添加时间',
                    formatter: function(value, row, index) {
                        return '<span><i class="bi bi-calendar3"></i> ' + value + '</span>';
                    },
                    sortable: true
                },
                {
                    field: 'operate',
                    title: '操作',
                    formatter: function(value, row, index) {
                        return `<div class="action-buttons">
                            <button type="button" onclick="ewm('${row.dwz}')" class="action-btn action-btn-qrcode" title="二维码">
                                <i class="bi bi-qr-code"></i>
                                <span class="action-btn-tooltip">二维码</span>
                            </button>
                            <button type="button" class="action-btn action-btn-edit" data-toggle="modal" data-target="#editUrl" data-id="${row.id}">
                                <i class="bi bi-pencil"></i>
                                <span class="action-btn-tooltip">编辑</span>
                            </button>
                            <a href="../data.php?id=${row.id}" target="_blank" class="action-btn action-btn-data">
                                <i class="bi bi-graph-up"></i>
                                <span class="action-btn-tooltip">访问数据</span>
                            </a>
                            <button type="button" onclick="show('${row.id}')" class="action-btn action-btn-info">
                                <i class="bi bi-info-circle"></i>
                                <span class="action-btn-tooltip">详细信息</span>
                            </button>
                            <button type="button" onclick="delUrl('${row.id}')" class="action-btn action-btn-delete">
                                <i class="bi bi-trash"></i>
                                <span class="action-btn-tooltip">删除</span>
                            </button>
                        </div>`;
                    }
                }
            ],
            onLoadSuccess: function(data) {
                $("[data-toggle='tooltip']").tooltip();
                // 初始化复制功能
                if(typeof ClipboardJS !== 'undefined') {
                    new ClipboardJS('.clipboard');
                }
                // 美化卡片视图
                setTimeout(function() {
                    enhanceCardView();
                    console.log('enhanceCardView called by onLoadSuccess');
                }, 100);
            },
            onToggle: function (cardView) {
                console.log('Toggle to ' + (cardView ? 'card view' : 'table view'));
                if (cardView) {
                    setTimeout(function() {
                        enhanceCardView();
                        console.log('enhanceCardView called by onToggle');
                    }, 100);
                }
            }
        });
    }

    // 增强卡片视图的显示效果 - 优化版本
    function enhanceCardView() {
        console.log('enhanceCardView function executing');
        
        // 首先确保我们处于卡片视图模式
        const $cardView = $('.bootstrap-table .card-view');
        if ($cardView.length === 0) {
            console.log('Not in card view mode, exiting');
            return;
        }
        
        // 检查是否已经应用了增强
        if ($cardView.hasClass('enhanced')) {
            console.log('Card view already enhanced, skipping');
            return;
        }
        
        // 标记已增强
        $cardView.addClass('enhanced');
        
        // 包装卡片内容 - 使用更高效的方法
        $('.bootstrap-table .card-view .card').each(function() {
            const $card = $(this);
            if (!$card.find('.card-content').length) {
                // 减少DOM操作，一次性创建并附加内容
                const $content = $('<div class="card-content"></div>');
                $content.append($card.children().detach());
                $card.append($content);
            }
        });
        
        // 给卡片视图中的元素添加更好的样式 - 合并查询和处理
        $('.bootstrap-table .card-view .card-body:not(.processed)').each(function() {
            const $body = $(this);
            // 标记为已处理
            $body.addClass('processed');
            
            // 获取当前字段的标题文本
            const $title = $body.find('.title');
            const titleText = $title.text().trim();
            
            // 根据标题添加特殊类和处理
            switch(titleText) {
                case 'ID':
                    $body.addClass('id-field');
                    break;
                case '状态':
                    $body.addClass('state-field');
                    break;
                case '备注':
                    $body.addClass('remarks-field');
                    break;
                case '添加时间':
                    $body.addClass('date-field');
                    // 一次性操作DOM
                    $body.find('.value').html('<i class="bi bi-calendar-plus"></i> ' + 
                                             $body.find('.value').text().trim());
                    break;
                case '最后执行时间':
                    $body.addClass('date-field');
                    $body.find('.value').html('<i class="bi bi-clock-history"></i> ' + 
                                             $body.find('.value').text().trim());
                    break;
                case '访问':
                    $body.addClass('visits-field');
                    $body.find('.value').html('<i class="bi bi-eye"></i> ' + 
                                             $body.find('.value').text().trim());
                    break;
                case '类型':
                    $body.addClass('type-field');
                    // 为类型字段添加图标 - 减少查询次数
                    const $badge = $body.find('.badge:not(.enhanced)');
                    if ($badge.length) {
                        const badgeText = $badge.text().trim();
                        let iconClass = '';
                        let badgeClass = '';
                        
                        if (badgeText.includes('普通')) {
                            iconClass = 'bi-box-arrow-up-right';
                            badgeClass = 'badge-primary';
                        } else if (badgeText.includes('防红')) {
                            iconClass = 'bi-shield-check';
                            badgeClass = 'badge-success';
                        } else if (badgeText.includes('直链')) {
                            iconClass = 'bi-link-45deg';
                            badgeClass = 'badge-info';
                        } else if (badgeText.includes('直跳')) {
                            iconClass = 'bi-lightning';
                            badgeClass = 'badge-warning';
                        }
                        
                        // 一次性修改DOM
                        $badge.addClass('enhanced ' + badgeClass)
                              .html('<i class="bi ' + iconClass + '"></i> ' + badgeText);
                    }
                    break;
                case '操作':
                    $body.addClass('actions-field');
                    break;
            }
        });
        
        // 处理链接元素 - 减少重复处理
        $('.bootstrap-table .card-view .url-item:not(.processed)').each(function() {
            const $item = $(this);
            // 添加多个类一次性完成
            $item.addClass('processed card-url-item');
            
            // 添加复制提示 - 只有在需要时才创建元素
            if (!$item.find('.copy-tooltip').length) {
                // 一次性创建并附加元素
                $item.append('<div class="copy-tooltip">点击复制</div>');
                
                // 使用one而不是on，确保事件只绑定一次
                $item.one('click', function() {
                    const $tooltip = $(this).find('.copy-tooltip');
                    $tooltip.text('已复制!');
                    setTimeout(function() {
                        $tooltip.text('点击复制');
                    }, 1500);
                });
            }
        });
        
        // 批量添加类 - 更高效的选择器
        $('.bootstrap-table .card-view .badge:not(.card-badge)').addClass('card-badge');
        $('.bootstrap-table .card-view .btn-group:not(.card-buttons)').addClass('card-buttons');
        
        console.log('enhanceCardView function completed');
    }

    // 直接注入卡片样式，确保样式立即生效
    function injectCardStyles() {
        var styleId = 'card-view-direct-styles';
        
        if (document.getElementById(styleId)) {
            return; // 样式已存在，不重复添加
        }
        
        var style = document.createElement('style');
        style.id = styleId;
        style.innerHTML = `
        .bootstrap-table .card-view {
            display: grid !important;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)) !important;
            gap: 24px !important;
            padding: 20px !important;
            background-color: #f9fafc !important;
        }
        
        .bootstrap-table .card-view .card {
            position: relative !important;
            background: #fff !important;
            border-radius: 16px !important;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.04), 0 6px 10px rgba(0, 0, 0, 0.02) !important;
            overflow: hidden !important;
            transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1) !important;
            border: none !important;
            display: flex !important;
            flex-direction: column !important;
        }
        
        .bootstrap-table .card-view .card::before {
            content: '' !important;
            position: absolute !important;
            top: 0 !important;
            left: 0 !important;
            right: 0 !important;
            height: 5px !important;
            background: linear-gradient(135deg, #2ECC71, #1abc9c) !important;
            opacity: 0.9 !important;
        }
        
        .bootstrap-table .card-view .card-content {
            padding: 24px !important;
        }
        
        .bootstrap-table .card-view .card-body {
            padding: 8px 0 !important;
            display: flex !important;
            flex-direction: row !important;
            align-items: center !important;
            border-bottom: none !important;
            margin-bottom: 4px !important;
        }
        
        .bootstrap-table .card-view .title {
            font-size: 11px !important;
            color: rgba(0, 0, 0, 0.5) !important;
            margin-bottom: 0 !important;
            font-weight: 500 !important;
            width: 80px !important;
            flex-shrink: 0 !important;
        }
        
        .bootstrap-table .card-view .value {
            color: rgba(0, 0, 0, 0.8) !important;
            font-size: 13px !important;
            word-break: break-word !important;
            flex-grow: 1 !important;
        }
        
        .bootstrap-table .card-view .card-body.id-field .value {
            position: absolute !important;
            top: 14px !important;
            right: 14px !important;
            background-color: rgba(0, 0, 0, 0.05) !important;
            color: rgba(0, 0, 0, 0.5) !important;
            font-size: 12px !important;
            padding: 4px 10px !important;
            border-radius: 20px !important;
            font-weight: 500 !important;
        }
        
        .bootstrap-table .card-view .card-body.id-field .title {
            display: none !important;
        }
        
        .bootstrap-table .card-view .remarks-field {
            margin-top: 8px !important;
            padding: 12px !important;
            background-color: #f9f9fb !important;
            border-radius: 10px !important;
            margin-bottom: 16px !important;
        }
        
        .bootstrap-table .card-view .card-badge {
            padding: 5px 12px !important;
            border-radius: 8px !important;
            font-weight: 500 !important;
            font-size: 12px !important;
            display: inline-flex !important;
            align-items: center !important;
            margin: 3px 0 !important;
        }
        
        .bootstrap-table .card-view .badge-primary {
            background-color: rgba(26, 188, 156, 0.15) !important;
            color: #16a085 !important;
        }
        
        .bootstrap-table .card-view .badge-success {
            background-color: rgba(46, 204, 113, 0.15) !important;
            color: #27ae60 !important;
        }
        
        .bootstrap-table .card-view .badge-info {
            background-color: rgba(52, 152, 219, 0.15) !important;
            color: #2980b9 !important;
        }
        
        .bootstrap-table .card-view .badge-warning {
            background-color: rgba(241, 196, 15, 0.15) !important;
            color: #f39c12 !important;
        }
        
        .bootstrap-table .card-view .badge-danger {
            background-color: rgba(231, 76, 60, 0.15) !important;
            color: #c0392b !important;
        }
        
        @media (max-width: 576px) {
            .bootstrap-table .card-view {
                grid-template-columns: 1fr !important;
                padding: 15px !important;
                gap: 15px !important;
            }
        }
        
        @media (min-width: 768px) and (max-width: 991px) {
            .bootstrap-table .card-view {
                grid-template-columns: repeat(2, 1fr) !important;
            }
        }
        
        @media (min-width: 992px) and (max-width: 1399px) {
            .bootstrap-table .card-view {
                grid-template-columns: repeat(3, 1fr) !important;
            }
        }
        
        @media (min-width: 1400px) {
            .bootstrap-table .card-view {
                grid-template-columns: repeat(4, 1fr) !important;
            }
        }
        `;
        
        document.head.appendChild(style);
        console.log('Injected direct card styles');
    }

    function tz_pattern() {
        var a = $('#add-pattern').val();
        switch (a) {
            case '1':
                $('#add1').css("display", "none");
                $('#add2').css("display", "block");
                $('#add3').css("display", "block");
                $('#add4').css("display", "block");
                $('#add5').css("display", "block");
                $('#add6').css("display", "block");
                $('#add7').css("display", "block");
                $('#add8').css("display", "block");
                $('#add9').css("display", "none");
                $('#add10').css("display", "block");
                break;
            case '2':
                $('#add1').css("display", "none");
                $('#add2').css("display", "block");
                $('#add3').css("display", "block");
                $('#add4').css("display", "block");
                $('#add5').css("display", "block");
                $('#add6').css("display", "none");
                $('#add7').css("display", "none");
                $('#add8').css("display", "none");
                $('#add9').css("display", "block");
                $('#add10').css("display", "block");
                break;
            case '3':
                $('#add1').css("display", "block");
                $('#add2').css("display", "block");
                $('#add3').css("display", "block");
                $('#add4').css("display", "block");
                $('#add5').css("display", "block");
                $('#add6').css("display", "none");
                $('#add7').css("display", "none");
                $('#add8').css("display", "none");
                $('#add9').css("display", "none");
                $('#add10').css("display", "block");
                break;
            case '4':
                $('#add1').css("display", "none");
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
    }

    function insertUrl() {
        id = $('#add-id').val();
        url = $('#add-url').val();
        type = $('#add-type').val();
        pattern = $('#add-pattern').val();
        title = $('#add-title').val();
        remarks = $('#add-remarks').val();
        visit = $('#add-visit').val();
        visiturl = $('#add-visiturl').val();
        pwd = $('#add-pwd').val();
        qqjump = $('#add-qqjump').val();
        wxjump = $('#add-wxjump').val();
        alijump = $('#add-alijump').val();
        jumpmb = $('#add-jumpmb').val();
        if (url == '' || type == '' || pattern == '') {
            showAlert('输入内容不完整');
            return false;
        }
        $.ajax({
            url: "ajax.php?act=addUrl",
            type: "POST",
            data: {
                "id": id,
                "url": url,
                "type": type,
                "pattern": pattern,
                "title": title,
                "remarks": remarks,
                "visit": visit,
                "visiturl": visiturl,
                "pwd": pwd,
                "qqjump": qqjump,
                "wxjump": wxjump,
                "alijump": alijump,
                "jumpmb": jumpmb
            },
            dataType: "json",
            success: function(data) {
                if (data.code == 0) {
                    if(typeof layer !== 'undefined') {
                        layer.confirm(data.msg, {
                            btn: ['确定', '继续生成']
                        }, function() {
                            location.reload();
                        });
                    } else {
                        if (confirm(data.msg + "\n点击确定刷新页面，点击取消继续生成")) {
                            location.reload();
                        }
                    }
                } else {
                    showAlert(data.msg);
                }
            },
            error: function(data) {
                showMsg('服务器错误');
                return false;
            }
        });
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

    function updateUrl() {
        id = $('#edit-id').val();
        url = $('#edit-url').val();
        pattern = $('#edit-pattern').val();
        title = $('#edit-title').val();
        remarks = $('#edit-remarks').val();
        visit = $('#edit-visit').val();
        visiturl = $('#edit-visiturl').val();
        pwd = $('#edit-pwd').val();
        qqjump = $('#edit-qqjump').val();
        wxjump = $('#edit-wxjump').val();
        alijump = $('#edit-alijump').val();
        jumpmb = $('#edit-jumpmb').val();
        if (url == '' || pattern == '') {
            showAlert('信息填写不完整');
            return false;
        }
        $.ajax({
            url: "ajax.php?act=upUrl",
            type: "POST",
            data: {
                "id": id,
                "url": url,
                "pattern": pattern,
                "title": title,
                "remarks": remarks,
                "visit": visit,
                "visiturl": visiturl,
                "pwd": pwd,
                "qqjump": qqjump,
                "wxjump": wxjump,
                "alijump": alijump,
                "jumpmb": jumpmb
            },
            dataType: "json",
            success: function(result) {
                if (result.code == 0) {
                    showMsg('修改成功');
                    $('#editUrl').modal('hide');
                    $("#listTable").bootstrapTable('refresh');
                } else if (result.code == 2) {
                    // 非会员提示
                    if (typeof layer !== 'undefined') {
                        layer.open({
                            type: 1,
                            title: '会员功能提示',
                            skin: 'layui-layer-rim',
                            area: ['420px', 'auto'],
                            content: '<div class="text-center" style="padding: 20px;">' +
                                     '<i class="fa fa-exclamation-circle text-warning" style="font-size: 60px;"></i>' +
                                     '<p style="margin-top: 20px; font-size: 16px;">' + result.msg + '</p>' +
                                     '<p style="margin-top: 15px;">备注和密码已成功更新，其他字段未修改</p>' +
                                     '<div class="text-center" style="margin-top: 20px;">' +
                                     '<a href="recharge.php" class="btn btn-success">立即升级会员</a>' +
                                     '<button class="btn btn-default ml-2" onclick="layer.closeAll()">关闭</button>' +
                                     '</div>' +
                                     '</div>'
                        });
                    } else {
                        alert(result.msg + '\n备注和密码已成功更新，其他字段未修改');
                    }
                    $('#editUrl').modal('hide');
                    $("#listTable").bootstrapTable('refresh');
                } else {
                    showMsg(result.msg);
                }
            },
            error: function(data) {
                showMsg('服务器错误');
                return false;
            }
        });
    }

    function delUrl(id) {
        showConfirm('您确定要删除这个网址吗？', function() {
            $.ajax({
                url: "ajax.php?act=delUrl",
                type: "POST",
                data: {
                    "id": id
                },
                dataType: "json",
                success: function(result) {
                    if (result.code == 0) {
                        showMsg('删除成功');
                        $("#listTable").bootstrapTable('refresh');
                    } else {
                        showMsg(result.msg);
                    }
                },
                error: function(data) {
                    showMsg('服务器错误');
                    return false;
                }
            });
        });
    }

    function datadel() {
        showConfirm('您确定要删除这些网址吗？', function() {
            var cks = document.getElementsByName("ids[]");
            var str = "";
            for (var i = 0; i < cks.length; i++) {
                if (cks[i].checked) {
                    str += cks[i].value + "&";
                }
            }
            str = str.substring(0, str.length - 1);
            $.ajax({
                url: "ajax.php?act=delSelect",
                type: "POST",
                data: {
                    "str": str
                },
                dataType: "json",
                success: function(result) {
                    if (result.code == 0) {
                        showMsg('删除成功');
                        $("#listTable").bootstrapTable('refresh');
                    } else {
                        showMsg(result.msg);
                    }
                },
                error: function(data) {
                    showMsg('服务器错误');
                    return false;
                }
            });
        });
    }


    function ewm(url) {
        if(typeof layer !== 'undefined') {
            layer.open({
                type: 1,
                skin: 'layui-layer-lan',
                anim: 2,
                shadeClose: true,
                title: '二维码',
                content: '<div style="padding:20px;text-align:center;"><img width="200px" src="../includes/libs/qrcode.php?size=300&text=' + url + '" /></div>'
            });
        } else {
            // 创建一个简单的模态框来显示二维码
            var qrModal = document.createElement('div');
            qrModal.style.position = 'fixed';
            qrModal.style.top = '50%';
            qrModal.style.left = '50%';
            qrModal.style.transform = 'translate(-50%, -50%)';
            qrModal.style.backgroundColor = 'white';
            qrModal.style.padding = '20px';
            qrModal.style.border = '1px solid #ddd';
            qrModal.style.zIndex = '9999';
            qrModal.style.boxShadow = '0 0 10px rgba(0,0,0,0.2)';
            qrModal.innerHTML = '<div style="text-align:right;margin-bottom:10px;"><button id="closeQrModal" style="border:none;background:none;font-size:20px;cursor:pointer;">&times;</button></div><div style="text-align:center;"><img width="200px" src="../includes/libs/qrcode.php?size=300&text=' + url + '" /></div>';
            document.body.appendChild(qrModal);
            
            document.getElementById('closeQrModal').onclick = function() {
                document.body.removeChild(qrModal);
            };
        }
    }

    function show(id) {
        $.ajax({
            type: 'GET',
            url: 'ajax.php?act=getUrlInfo&id=' + id,
            dataType: 'json',
            success: function(data) {
                if (data.code == 0) {
                    state = data.code == 1 ? '正常' : '封禁';
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
                    }
                    if(typeof layer !== 'undefined') {
                        layer.open({
                            type: 1,
                            skin: 'layui-layer-molv',
                            anim: 2,
                            shadeClose: true,
                            title: '详细信息',
                            content: '<div style="padding:15px"><p>网址ID：' + data.id + '</p><p>默认跳转：' + data.url + '</p><p>跳转类型：' + pattern + '</p><p>网址备注：' + data.remarks + '</p><p>限制次数：' + data.visit + '</p><p>限制跳转：' + data.visiturl + '</p><p>短链地址：' + data.dwz + '</p><p>跳转模板：' + data.jumpmb + '</p><p>直链标题：' + data.title + '</p><p>QQ跳转：' + data.qqjump + '</p><p>微信跳转：' + data.wxjump + '</p><p>支付宝跳转：' + data.alijump + '</p><p>访问次数：' + data.view + '</p><p>访问密码：' + data.pwd + '</p><p>添加时间：' + data.addtime + '</p></div>'
                        });
                    } else {
                        // 简单提示
                        alert('网址详情：\n网址ID：' + data.id + '\n默认跳转：' + data.url + '\n跳转类型：' + pattern + '\n短链地址：' + data.dwz);
                    }
                } else {
                    showAlert(data.msg);
                }
            },
            error: function(data) {
                showMsg('服务器错误');
                return false;
            }
        });
    }

    function setActive(id, state) {
        state == 1 ? state = 0 : state = 1;
        $.ajax({
            type: 'GET',
            url: 'ajax.php?act=setUrlState&id=' + id + '&state=' + state,
            dataType: 'json',
            success: function(data) {
                if (data.code == 0) {
                    showMsg('修改成功');
                    $("#listTable").bootstrapTable('refresh');
                } else {
                    showMsg(data.msg);
                }
            },
            error: function(data) {
                showMsg('服务器错误');
                return false;
            }
        });
    }

    function setactiveAll(state) {
        title = state == 1 ? '您确定要开启这些网址吗？' : '您确定要关闭这些网址吗？';
        showConfirm(title, function() {
            var cks = document.getElementsByName("ids[]");
            var str = "";
            for (var i = 0; i < cks.length; i++) {
                if (cks[i].checked) {
                    str += cks[i].value + "&";
                }
            }
            str = str.substring(0, str.length - 1);
            $.ajax({
                url: "ajax.php?act=setUrlStateAll",
                type: "POST",
                data: {
                    "state": state,
                    "str": str
                },
                dataType: "json",
                success: function(data) {
                    if (data.code == 0) {
                        showMsg('修改成功');
                        $("#listTable").bootstrapTable('refresh');
                    }
                },
                error: function(data) {
                    showMsg('服务器错误');
                    return false;
                }
            });
        });
    }
    
    // 文档就绪后初始化
    $(document).ready(function() {
        // 先添加关键样式
        injectCardStyles();
        
        initTable();
        if ($(".search-input").val() != '') {
            $(".search-input").bind("click", initTable);
        }
        
        // 初始化剪贴板
        if(typeof ClipboardJS !== 'undefined') {
            var clipboard = new ClipboardJS('.clipboard');
            clipboard.on('success', function(e) {
                msg();
                e.clearSelection();
            });
        }

        var a = $('#add-pattern').val();
        switch (a) {
            case '1':
                $('#add1').css("display", "none");
                $('#add9').css("display", "none");
                break;
            case '2':
                $('#add1').css("display", "none");
                $('#add6').css("display", "none");
                $('#add7').css("display", "none");
                $('#add8').css("display", "none");
                break;
            case '3':
                $('#add6').css("display", "none");
                $('#add7').css("display", "none");
                $('#add8').css("display", "none");
                $('#add9').css("display", "none");
                break;
            case '4':
                $('#add1').css("display", "none");
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

        // 添加passive选项到事件监听器
        // 使用jQuery实现被动事件监听
        const eventOptions = {passive: true};
        
        // 优化视图切换事件监听 - 使用代理并添加passive选项
        $(document).on('click', '.bootstrap-table .toggle-button', function() {
            console.log('Toggle button clicked');
            setTimeout(function() {
                enhanceCardView();
                injectCardStyles(); // 再次注入样式
                console.log('enhanceCardView called from toggle button click');
            }, 300);
        });
        
        // 使用被动事件监听器模式
        $(document).on('toggle.bs.table', '#listTable', {passive: true}, function(e, cardView) {
            console.log('toggle.bs.table event fired, cardView:', cardView);
            if (cardView) {
                setTimeout(function() {
                    enhanceCardView();
                    injectCardStyles(); // 再次注入样式
                    console.log('enhanceCardView called from toggle.bs.table event');
                }, 300);
            }
        });
        
        // 优化页码切换事件
        $(document).on('page-change.bs.table', '#listTable', {passive: true}, function() {
            console.log('Page changed');
            setTimeout(function() {
                if ($('.bootstrap-table .card-view').length > 0) {
                    enhanceCardView();
                    injectCardStyles(); // 再次注入样式
                    console.log('enhanceCardView called from page change');
                }
            }, 300);
        });
        
        // 优化搜索事件
        $(document).on('search.bs.table', '#listTable', {passive: true}, function() {
            console.log('Search performed');
            setTimeout(function() {
                if ($('.bootstrap-table .card-view').length > 0) {
                    enhanceCardView();
                    injectCardStyles(); // 再次注入样式
                    console.log('enhanceCardView called after search');
                }
            }, 300);
        });

        // 优化首次加载调用，延迟更长以等待DOM完成渲染
        setTimeout(function() {
            if ($('.bootstrap-table .card-view').length > 0) {
                enhanceCardView();
                injectCardStyles(); // 再次注入样式
                console.log('enhanceCardView called on initial page load');
            }
        }, 1000);
        
        // 优化模态框事件，使用被动监听
        $('#editUrl').on('show.bs.modal', {passive: true}, function(event) {
            var button = $(event.relatedTarget)
            var id = button.data('id')
            var modal = $(this)
            $.ajax({
                type: 'GET',
                url: 'ajax.php?act=getUrlInfo&id=' + id,
                dataType: 'json',
                success: function(data) {
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
                        layer.alert(data.msg);
                    }
                },
                error: function(data) {
                    layer.msg('服务器错误');
                    return false;
                }
            });
        })
    })

    // 为剪贴板功能添加msg函数
    function msg() {
        if (typeof layer !== 'undefined') {
            layer.msg('复制成功', {icon: 1, time: 1000});
        }
    }
</script>
</body>
</html>