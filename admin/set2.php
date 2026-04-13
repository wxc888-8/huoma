<?php
include('../includes/common.php');
if ($islogin != 1) {
    exit('<script language=\'javascript\'>window.location.href=\'./login.php\';</script>');
}
$match = rand(1, 50);
$mod = isset($_GET['mod']) ? $_GET['mod'] : null;
?>
<!DOCTYPE html>
<html lang="zh">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" />
    <title>网站设置</title>
    <link rel="icon" href="favicon.ico" type="image/ico">
    <link href="../static/admin/css/bootstrap.min.css" rel="stylesheet">
    <link href="../static/admin/css/materialdesignicons.min.css" rel="stylesheet">
    <link href="../static/admin/js/jquery-confirm/jquery-confirm.min.css" rel="stylesheet">
    <link href="../static/admin/css/animate.min.css" rel="stylesheet">
    <link href="../static/admin/css/style.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #2ECC71;
            --primary-light: #7DCEA0;
            --primary-dark: #27AE60;
            --primary-transparent: rgba(46, 204, 113, 0.1);
            --danger-color: #E74C3C;
            --warning-color: #F39C12;
            --info-color: #3498DB;
            --success-color: #2ECC71;
            --light-color: #ECF0F1;
            --dark-color: #2C3E50;
            --bg-color: #f5f5f5;
            --text-color: #34495E;
            --border-color: #E5E7E9;
            --card-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            --hover-shadow: 0 8px 16px rgba(0, 0, 0, 0.12);
            --transition-normal: all 0.3s ease;
        }

        body {
            background-color: var(--bg-color);
            color: var(--text-color);
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            line-height: 1.6;
        }

        .container-fluid {
            padding: 25px;
        }

        /* 卡片样式 */
        .card {
            background: white;
            border-radius: 10px;
            box-shadow: var(--card-shadow);
            margin-bottom: 25px;
            border: none;
            overflow: hidden;
            transition: var(--transition-normal);
        }

        .card:hover {
            box-shadow: var(--hover-shadow);
        }

        .card-header {
            padding: 1.5rem 1.75rem;
            background-color: white;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .card-title {
            margin-bottom: 0;
            color: var(--dark-color);
            font-weight: 600;
            font-size: 1.25rem;
        }

        .card-body {
            padding: 1.75rem;
        }

        /* 按钮样式 */
        .btn {
            padding: 0.625rem 1.25rem;
            border-radius: 6px;
            font-weight: 500;
            transition: var(--transition-normal);
            border: none;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
        }

        .btn:active {
            transform: translateY(0);
        }

        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .btn-primary:hover, .btn-primary:focus, .btn-primary:active {
            background-color: var(--primary-dark) !important;
            border-color: var(--primary-dark) !important;
        }

        .btn-warning {
            background-color: var(--warning-color);
            border-color: var(--warning-color);
        }

        .btn-warning:hover {
            background-color: #E67E22;
            border-color: #E67E22;
        }

        .btn-danger {
            background-color: var(--danger-color);
            border-color: var(--danger-color);
        }

        .btn-danger:hover {
            background-color: #C0392B;
            border-color: #C0392B;
        }

        .btn-block {
            margin-bottom: 12px;
            padding: 0.75rem 1.25rem;
            text-align: left;
            position: relative;
        }

        .btn-block:after {
            content: "\f142";
            font-family: "Material Design Icons";
            position: absolute;
            right: 20px;
            opacity: 0.7;
        }

        .btn-block:hover:after {
            opacity: 1;
            right: 15px;
        }

        /* 表单样式 */
        .form-control, .form-control-file {
            border-radius: 6px;
            border: 1px solid var(--border-color);
            padding: 0.625rem 0.875rem;
            transition: var(--transition-normal);
            background-color: #FFF;
            color: var(--text-color);
        }

        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(46, 204, 113, 0.25);
        }

        .form-group {
            margin-bottom: 1.75rem;
        }

        label {
            font-weight: 500;
            margin-bottom: 0.625rem;
            color: var(--dark-color);
            display: block;
        }

        /* 图片样式 */
        img.img-responsive {
            max-width: 100%;
            height: auto;
            border-radius: 8px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
            transition: var(--transition-normal);
        }

        img.img-responsive:hover {
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
        }

        /* 列表样式 */
        .list-group-item {
            border: 1px solid rgba(0, 0, 0, 0.05);
            margin-bottom: 0.75rem;
            border-radius: 6px;
            padding: 1rem 1.25rem;
            background-color: #f8f9fa;
            font-family: SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
            word-break: break-all;
            transition: var(--transition-normal);
            font-size: 0.9rem;
        }

        .list-group-item:hover {
            background-color: #FFF;
            border-color: var(--primary-light);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            transform: translateY(-2px);
        }

        /* 提示框样式 */
        .alert {
            border-radius: 8px;
            border: none;
            padding: 1.25rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
        }

        .alert-info {
            background-color: #E1F0FA;
            color: #2573a7;
            border-left: 4px solid var(--info-color);
        }

        .alert-warning {
            background-color: #FFF3CD;
            color: #856404;
            border-left: 4px solid var(--warning-color);
        }

        /* 页脚样式 */
        .panel-footer {
            padding: 1rem 1.25rem;
            background-color: #f8f9fa;
            border-radius: 8px;
            margin-top: 1.5rem;
            font-style: italic;
            color: #666;
            border-left: 4px solid var(--primary-color);
        }

        /* 分隔线 */
        hr {
            margin: 2rem 0;
            border-color: var(--border-color);
        }

        /* 标题样式 */
        h4, h5 {
            color: var(--dark-color);
            font-weight: 600;
            margin-top: 1.5rem;
            margin-bottom: 1rem;
        }

        /* 链接样式 */
        .block-options {
            float: right;
        }

        .block-options a {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 500;
            transition: var(--transition-normal);
            padding: 0.5rem 0.75rem;
            border-radius: 4px;
        }

        .block-options a:hover {
            color: var(--primary-dark);
            background-color: var(--primary-transparent);
        }

        /* 表单行样式 */
        .form-row {
            margin-bottom: 1rem;
        }

        .form-row .col-auto {
            margin-bottom: 0.5rem;
        }

        /* 其他元素优化 */
        .well {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 1.25rem;
            margin-top: 1.5rem;
            border: 1px solid rgba(0, 0, 0, 0.05);
            box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.05);
        }

        /* 图标优化 */
        .mdi {
            margin-right: 0.5rem;
            font-size: 1.1rem;
            vertical-align: middle;
            color: var(--primary-color);
        }
    </style>
</head>

<body>
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <?php
                    if ($mod == 'setLogo') {
                    ?>
                        <div class="card-header">
                            <div class="card-title">更改首页logo</div>
                            <div class="block-options pull-right"><a href="set2.php?mod=setBg">更改背景图</a></div>
                        </div>
                        <div class="card-body">
                            <form action="#" method="post" role="form" enctype="multipart/form-data">
                                <div class="form-group">
                                    <input type="file" name="file-logo" id="file-logo" class="form-control-file">
                                </div>
                                <div class="form-group">
                                    <button type="submit" id="submit-logo" class="btn btn-primary btn-block">提交</button>
                                </div>
                            </form>
                            <div class="form-group">
                                <p>现在的logo：</p>
                                <img src="../static/picture/logo.png?<?php echo $match ?>" class="img-responsive" alt="当前Logo">
                            </div>
                        </div>
                    <?php
                    } elseif ($mod == 'setBg') {
                    ?>
                        <div class="card-header">
                            <div class="card-title">更改首页背景图</div>
                            <div class="block-options pull-right"><a href="set2.php?mod=setLogo">更改LOGO</a></div>
                        </div>
                        <div class="card-body">
                            <form action="set-style.php" method="post" role="form" enctype="multipart/form-data">
                                <div class="form-group">
                                    <input type="file" name="file-bg" id="file-bg" class="form-control-file">
                                </div>
                                <div class="form-group">
                                    <button type="submit" id="submit-bg" class="btn btn-primary btn-block">提交</button>
                                </div>
                            </form>
                            <div class="form-group">
                                <p>现在的背景图：</p>
                                <img src="../static/picture/bg.png?<?php echo $match ?>" class="img-responsive" alt="当前背景">
                            </div>
                        </div>
                    <?php
                    } elseif ($mod == 'cron') {
                    ?>
                        <div class="card-header">
                            <div class="card-title">计划任务配置</div>
                        </div>
                        <div class="card-body">
                            <form name="cron" action="ajax.php?act=setCron" class="edit-form">
                                <div class="form-group">
                                    <label for="cronkey">监控秘钥</label>
                                    <input class="form-control" type="text" id="cronkey" name="cronkey" value="<?php echo $conf['cronkey'] ?>" placeholder="请输入监控秘钥" required>
                                </div>
                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary m-r-5 ajax-post" target-form="cron">修 改</button>
                                </div>
                            </form>
                        </div>
                </div>
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">计划任务列表</div>
                    </div>
                    <div class="card-body">
                        <p>请按自己的需要监控以下网址。只能在一个地方监控，千万不要多节点监控或在多处监控，否则会导致数据错乱！</p>
                        
                        <h5 class="mt-4 mb-2">每日数据库维护（每天0点后执行）：</h5>
                        <div class="list-group-item"><?php echo 'http://' . $conf['domain'] . '/cron.php?do=daily&key=' . $conf['cronkey'] ?></div>
                        
                        <h5 class="mt-4 mb-2">域名拦截检测，域名少的建议不要监控，被QQ微信拦截的网址会自动停用，需要在设置（10到60分钟一次）：</h5>
                        <div class="list-group-item"><?php echo 'http://' . $conf['domain'] . '/cron.php?do=checkdomain&key=' . $conf['cronkey'] ?></div>
                        
                        <h5 class="mt-4 mb-2">会员不足三天到期提醒（1小时一次）：</h5>
                        <div class="list-group-item"><?php echo 'http://' . $conf['domain'] . '/cron.php?do=vipcheck&key=' . $conf['cronkey'] ?></div>
                        
                        <h5 class="mt-4 mb-2">IP记录清理,这个很重要，一定要监控（每天0点后执行一次）：</h5>
                        <div class="list-group-item"><?php echo 'http://' . $conf['domain'] . '/cron.php?do=cleanData&key=' . $conf['cronkey'] ?></div>
                        
                        <h5 class="mt-4 mb-2">清理七天前访客记录（每天0点后执行一次）：</h5>
                        <div class="list-group-item"><?php echo 'http://' . $conf['domain'] . '/cron.php?do=cleanVisitors&key=' . $conf['cronkey'] ?></div>
                        
                        <h5 class="mt-4 mb-2">清理游客生成超过七天的链接（不想清理就不要监控这一条，每天0点后执行一次）：</h5>
                        <div class="list-group-item"><?php echo 'http://' . $conf['domain'] . '/cron.php?do=cleanUrl&key=' . $conf['cronkey'] ?></div>
                        
                        <h5 class="mt-4 mb-2">网址拦截监控（四条监控执行时间分别为（五分钟，十分钟，一个小时，一天）：</h5>
                        <div class="list-group-item"><?php echo 'http://' . $conf['domain'] . '/cron.php?do=urlcheck1&key=' . $conf['cronkey'] ?></div>
                        <div class="list-group-item"><?php echo 'http://' . $conf['domain'] . '/cron.php?do=urlcheck2&key=' . $conf['cronkey'] ?></div>
                        <div class="list-group-item"><?php echo 'http://' . $conf['domain'] . '/cron.php?do=urlcheck3&key=' . $conf['cronkey'] ?></div>
                        <div class="list-group-item"><?php echo 'http://' . $conf['domain'] . '/cron.php?do=urlcheck4&key=' . $conf['cronkey'] ?></div>
                    </div>
                <?php
                    } elseif ($mod == 'clean') {
                ?>
                    <div class="card-header">
                        <div class="card-title">系统数据清理</div>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info">
                            <span class="mdi mdi-information-outline"></span>
                            定期清理数据有助于提升网站访问速度，系统自动清理不会影响VIP用户数据
                        </div>
                        
                        <div class="clean-section">
                            <h5 class="clean-section-title"><span class="mdi mdi-clock-outline"></span> 7天前数据清理</h5>
                            <div class="clean-grid">
                                <a href="ajax.php?act=cleanCache" class="clean-btn ajax-get confirm no-refresh">
                                    <span class="mdi mdi-cached"></span>
                                    <span class="clean-btn-text">清理设置缓存</span>
                                </a>
                                <a href="ajax.php?act=cleanvisitors" class="clean-btn ajax-get confirm no-refresh">
                                    <span class="mdi mdi-account-remove"></span>
                                    <span class="clean-btn-text">删除访客记录</span>
                                </a>
                                <a href="ajax.php?act=cleanurl" class="clean-btn ajax-get confirm no-refresh">
                                    <span class="mdi mdi-link-off"></span>
                                    <span class="clean-btn-text">删除无访问链接</span>
                                </a>
                                <a href="ajax.php?act=cleanuser1" class="clean-btn ajax-get confirm no-refresh">
                                    <span class="mdi mdi-account-off"></span>
                                    <span class="clean-btn-text">删除从未登录用户</span>
                                </a>
                                <a href="ajax.php?act=cleanmodify" class="clean-btn ajax-get confirm no-refresh">
                                    <span class="mdi mdi-history"></span>
                                    <span class="clean-btn-text">删除修改记录</span>
                                </a>
                                <a href="ajax.php?act=cleanurl5" class="clean-btn ajax-get confirm no-refresh">
                                    <span class="mdi mdi-account-question"></span>
                                    <span class="clean-btn-text">删除游客链接</span>
                                </a>
                                <a href="ajax.php?act=cleanurl3" class="clean-btn ajax-get confirm no-refresh">
                                    <span class="mdi mdi-delete-empty"></span>
                                    <span class="clean-btn-text">清理回收站网址</span>
                                </a>
                                <a href="ajax.php?act=cleancheck" class="clean-btn ajax-get confirm no-refresh">
                                    <span class="mdi mdi-eye-off"></span>
                                    <span class="clean-btn-text">清理未执行监控</span>
                                </a>
                            </div>
                        </div>
                        
                        <div class="clean-section">
                            <h5 class="clean-section-title"><span class="mdi mdi-calendar"></span> 30天前数据清理</h5>
                            <div class="clean-grid">
                                <a href="ajax.php?act=cleanuser2" class="clean-btn ajax-get confirm no-refresh">
                                    <span class="mdi mdi-account-clock"></span>
                                    <span class="clean-btn-text">删除未登录用户</span>
                                </a>
                                <a href="ajax.php?act=cleanpoints" class="clean-btn ajax-get confirm no-refresh">
                                    <span class="mdi mdi-currency-usd-off"></span>
                                    <span class="clean-btn-text">删除收支明细</span>
                                </a>
                            </div>
                        </div>
                        
                        <div class="clean-section">
                            <h5 class="clean-section-title"><span class="mdi mdi-tune"></span> 自定义清理</h5>
                            
                            <div class="custom-clean-form">
                                <form name="cleanurl2" action="ajax.php?act=cleanurl2" class="edit-form">
                                    <div class="form-row">
                                        <div class="form-group col-md-12">
                                            <label><span class="mdi mdi-link"></span> 跳转链接清理</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">清理</span>
                                                </div>
                                                <input type="number" class="form-control" name="days" placeholder="天数" required />
                                                <div class="input-group-middle">
                                                    <span class="input-group-text">天前，访问次数小于等于</span>
                                                </div>
                                                <input type="number" class="form-control" name="view" placeholder="访问次数" required />
                                                <div class="input-group-append">
                                                    <button type="submit" class="btn btn-primary ajax-post confirm" target-form="cleanurl2">立即清理</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                                
                                <form name="cleanurl4" action="ajax.php?act=cleanurl4" class="edit-form">
                                    <div class="form-row">
                                        <div class="form-group col-md-12">
                                            <label><span class="mdi mdi-link-variant-off"></span> 未访问网址清理</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">清理</span>
                                                </div>
                                                <input type="number" class="form-control" name="days" placeholder="天数" required />
                                                <div class="input-group-middle">
                                                    <span class="input-group-text">天前未访问的网址</span>
                                                </div>
                                                <div class="input-group-append">
                                                    <button type="submit" class="btn btn-primary ajax-post confirm" target-form="cleanurl4">立即清理</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                                
                                <form name="cleanuser3" action="ajax.php?act=cleanuser3" class="edit-form">
                                    <div class="form-row">
                                        <div class="form-group col-md-12">
                                            <label><span class="mdi mdi-account-multiple-remove"></span> 网站用户清理</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">清理超过</span>
                                                </div>
                                                <input type="number" class="form-control" name="days" placeholder="天数" required />
                                                <div class="input-group-middle">
                                                    <span class="input-group-text">天未登录的用户</span>
                                                </div>
                                                <div class="input-group-append">
                                                    <button type="submit" class="btn btn-primary ajax-post confirm" target-form="cleanuser3">立即清理</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                        
                        <style>
                            .clean-section {
                                margin-bottom: 30px;
                                background: #fff;
                                border-radius: 8px;
                                padding: 20px;
                                box-shadow: 0 2px 8px rgba(0,0,0,0.05);
                            }
                            
                            .clean-section-title {
                                margin-bottom: 20px;
                                color: var(--dark-color);
                                font-weight: 600;
                                border-bottom: 1px solid var(--border-color);
                                padding-bottom: 10px;
                            }
                            
                            .clean-section-title .mdi {
                                color: var(--primary-color);
                                margin-right: 8px;
                            }
                            
                            .clean-grid {
                                display: grid;
                                grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
                                gap: 15px;
                            }
                            
                            .clean-btn {
                                display: flex;
                                flex-direction: column;
                                align-items: center;
                                justify-content: center;
                                background: #f8f9fa;
                                border-radius: 8px;
                                padding: 18px 15px;
                                text-align: center;
                                color: var(--text-color);
                                transition: all 0.3s;
                                text-decoration: none;
                                min-height: 100px;
                                border: 1px solid var(--border-color);
                            }
                            
                            .clean-btn:hover {
                                background: var(--primary-transparent);
                                transform: translateY(-3px);
                                box-shadow: 0 5px 15px rgba(0,0,0,0.05);
                                color: var(--dark-color);
                                border-color: var(--primary-color);
                            }
                            
                            .clean-btn .mdi {
                                font-size: 24px;
                                margin-bottom: 10px;
                                color: var(--primary-color);
                            }
                            
                            .clean-btn-text {
                                font-weight: 500;
                            }
                            
                            .custom-clean-form {
                                background: #f8f9fa;
                                border-radius: 8px;
                                padding: 20px;
                            }
                            
                            .custom-clean-form form {
                                margin-bottom: 20px;
                            }
                            
                            .custom-clean-form form:last-child {
                                margin-bottom: 0;
                            }
                            
                            .input-group {
                                align-items: stretch;
                            }
                            
                            .input-group-prepend,
                            .input-group-append,
                            .input-group-middle {
                                display: flex;
                                align-items: center;
                            }
                            
                            .input-group-middle .input-group-text {
                                border-radius: 0;
                                height: 100%;
                                display: flex;
                                align-items: center;
                                white-space: nowrap;
                            }
                            
                            .input-group .form-control:not(:first-child):not(:last-child) {
                                border-radius: 0;
                            }
                            
                            .input-group .form-control {
                                height: 38px;
                                padding: 0.375rem 0.75rem;
                            }
                            
                            .input-group .btn {
                                height: 38px;
                                display: flex;
                                align-items: center;
                                justify-content: center;
                                padding: 0.375rem 0.75rem;
                            }
                            
                            .input-group-text {
                                background-color: #e9ecef;
                                border: 1px solid #ced4da;
                                color: #495057;
                                height: 38px;
                                padding: 0.375rem 0.75rem;
                                display: flex;
                                align-items: center;
                            }
                        </style>
                    </div>
                <?php
                    } elseif ($mod == 'update') {
                        function zipExtract($src, $dest)
                        {
                            $zip = new ZipArchive();
                            if ($zip->open($src) === true) {
                                $zip->extractTo($dest);
                                $zip->close();
                                return true;
                            }
                            return false;
                        }
                        function deldir($dir)
                        {
                            if (!is_dir($dir)) return false;
                            $dh = opendir($dir);
                            while ($file = readdir($dh)) {
                                if ($file != "." && $file != "..") {
                                    $fullpath = $dir . "/" . $file;
                                    if (!is_dir($fullpath)) {
                                        unlink($fullpath);
                                    } else {
                                        deldir($fullpath);
                                    }
                                }
                            }
                            closedir($dh);
                            if (rmdir($dir)) {
                                return true;
                            } else {
                                return false;
                            }
                        }

                        $scriptpath = str_replace('\\', '/', $_SERVER['SCRIPT_NAME']);
                        $scriptpath = substr($scriptpath, 0, strrpos($scriptpath, '/'));
                        $admin_path = substr($scriptpath, strrpos($scriptpath, '/') + 1);
                ?>
                    <div class="card-header">
                        <div class="card-title">检测版本更新</div>
                    </div>
                    <div class="card-body">
                         <div class="alert alert-info">如需更新请手动下载更新包并覆盖到程序根目录</div>
                    
                        <?php
                          
                        $act = isset($_GET['act']) ? $_GET['act'] : null;
                        switch ($act) {
                            default:
                                $res = update_version();
                                if (!$res['msg']) $res['msg'] = '啊哦，更新服务器开小差了，请刷新此页面。';
                                echo '<div class="alert alert-info">' . $res['msg'] . '</div>';
                                echo '<hr/>';

                                if ($res['code'] == 1) {
                                    if (!class_exists('ZipArchive') || defined("SAE_ACCESSKEY") || defined("BAE_ENV_APPID")) {
                                        echo '您的空间不支持自动更新，请手动下载更新包并覆盖到程序根目录！<br/>
更新包下载：<a href="' . $res['file'] . '" class="btn btn-primary mt-2">点击下载</a>';
                                    } else {
                                        echo '<a href="set2.php?act=do&mod=update" class="btn btn-primary btn-block">立即更新到最新版本</a>';
                                    }

                                    echo '<div class="well mt-3">' . $res['uplog'] . '</div>';
                                }
                                break;
                            case 'do':
                                $res = update_version();
                                $RemoteFile = $res['file'];
                                $ZipFile = "Archive.zip";
                                copy($RemoteFile, $ZipFile) or die("无法下载更新包文件！" . '<a href="set2.php?mod=update">返回上级</a>');
                                if (zipExtract($ZipFile, ROOT)) {
                                    if ($admin_path != 'admin') {
                                        deldir(ROOT . $admin_path);
                                        rename(ROOT . 'admin', ROOT . $admin_path);
                                    }
                                    if (function_exists("opcache_reset")) @opcache_reset();
                                    if (!empty($res['sql'])) {
                                        $sql = $res['sql'];
                                        $t = 0;
                                        $e = 0;
                                        $error = '';
                                        for ($i = 0; $i < count($sql); $i++) {
                                            if (trim($sql[$i]) == '') continue;
                                            if ($DB->query($sql[$i])) {
                                                ++$t;
                                            } else {
                                                ++$e;
                                                $error .= $DB->error() . '<br/>';
                                            }
                                        }
                                        $addstr = '<br/>数据库更新成功。SQL成功' . $t . '句/失败' . $e . '句';
                                    }
                                    echo '<div class="alert alert-success">程序更新成功！' . $addstr . '</div>';
                                    echo '<a href="./" class="btn btn-primary">返回首页</a>';
                                    unlink($ZipFile);
                                } else {
                                    echo '<div class="alert alert-danger">无法解压文件！</div>';
                                    echo '<a href="set2.php?mod=update" class="btn btn-primary">返回上级</a>';
                                    if (file_exists($ZipFile))
                                        unlink($ZipFile);
                                }
                                break;
                        }
                        ?>
                    </div>
                <?php
                    }
                ?>
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
    <script src="https://lib.baomitu.com/layer/3.1.1/layer.js"></script>
    <script type="text/javascript" src="../static/admin/js/main.min.js"></script>
    <script>
        $(function() {
            $('#submit-bg').click(function() {
                var fd = new FormData();
                fd.append("file", $('#file-bg')[0].files[0]);
                $.ajax({
                    type: "post",
                    url: "ajax.php?act=setBg",
                    dataType: "json",
                    processData: false,
                    contentType: false,
                    data: fd,
                    success: function(data) {
                        if (data.code == 0) {
                            layer.msg('修改成功');
                            setTimeout(function() {
                                window.location.reload();
                            }, 1 * 1000);
                        } else {
                            layer.msg(data.msg);
                        }
                    },
                    error: function(a) {
                        layer.msg('修改失败');
                    }
                });
                return false;
            });
            $('#submit-logo').click(function() {
                var fd = new FormData();
                fd.append("file", $('#file-logo')[0].files[0]);
                $.ajax({
                    type: "post",
                    url: "ajax.php?act=setLogo",
                    dataType: "json",
                    processData: false,
                    contentType: false,
                    data: fd,
                    success: function(data) {
                        if (data.code == 0) {
                            layer.msg('修改成功');
                            setTimeout(function() {
                                window.location.reload();
                            }, 1 * 1000);
                        } else {
                            layer.msg(data.msg);
                        }
                    },
                    error: function(a) {
                        layer.msg('修改失败');
                    }
                });
                return false;
            });


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

            jQuery(document).delegate('.ajax-get', 'click', function() {
                var self = $(this),
                    tips = self.data('tips'),
                    ajax_url = self.attr("href") || self.data("url");

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

                                    ajaxGetFun(self, ajax_url, loader);
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
                    var loader = $('body').lyearloading({
                        opacity: 0.2,
                        spinnerSize: 'lg'
                    });
                    self.attr('autocomplete', 'off').prop('disabled', true);

                    ajaxGetFun(self, ajax_url, loader);
                }
                return false;
            });


            function ajaxPostFun(selfObj, ajax_url, form_data, loader) {
                jQuery.post(ajax_url, form_data).done(function(res) {
                    loader.destroy();
                    var msg = res.msg;
                    if (res.code == 0) {
                        if (res.url && !selfObj.hasClass('no-refresh')) {
                            msg += '页面即将自动跳转';
                        }
                        showNotify(msg, 'success');
                        setTimeout(function() {
                            selfObj.attr("autocomplete", "on").prop("disabled", false);
                            return selfObj.hasClass("no-refresh") ? false : (res.url ? location.href = res.url : window.location.reload());
                        }, 1500);
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

            function ajaxGetFun(selfObj, ajax_url, loader) {
                jQuery.get(ajax_url).done(function(res) {
                    loader.destroy();
                    var msg = res.msg;
                    if (res.code == 0) {
                        if (res.url && !selfObj.hasClass('no-refresh')) {
                            msg += '页面即将自动跳转';
                        }
                        showNotify(msg, 'success');
                        setTimeout(function() {
                            selfObj.attr("autocomplete", "on").prop("disabled", false);
                            return selfObj.hasClass("no-refresh") ? false : (res.url ? location.href = res.url : location.reload());
                        }, 1500);
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
        });
    </script>
</body>

</html>