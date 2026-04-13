<?php
$install = true;
require_once('../includes/common.php');
@header('Content-Type: text/html; charset=UTF-8');
if ($conf['version'] < 4000) {
    exit('网站程序版本太旧，不支持直接升级');
} elseif ($conf['version'] < 4001) {
    $sqls = file_get_contents('update1.sql');
    $version = 4001;
    unlink(ROOT . 'includes/ajax.func.php');
    unlink(ROOT . 'includes/cache.class.php');
    unlink(ROOT . 'includes/template.class.php');
    unlink(ROOT . 'includes/txprotect.php');
} elseif ($conf['version'] < 4002) {
    $sqls = file_get_contents('update2.sql');
    $version = 4002;
} elseif ($conf['version'] < 4003) {
    $sqls = file_get_contents('update3.sql');
    $version = 4003;
} elseif ($conf['version'] < 4004) {
    $sqls = file_get_contents('update4.sql');
    $version = 4004;
} elseif ($conf['version'] < 4005) {
    $sqls = file_get_contents('update5.sql');
    $version = 4005;
} elseif ($conf['version'] < 4006) {
    $sqls = file_get_contents('update6.sql');
    $version = 4006;
} elseif ($conf['version'] < 4007) {
    $sqls = file_get_contents('update7.sql');
    $version = 4007;
} elseif ($conf['version'] < 4008) {
    $sqls = file_get_contents('update8.sql');
    $version = 4008;
    unlink(ROOT . 'admin/blacklist-table.php');
    unlink(ROOT . 'admin/data-clean.php');
    unlink(ROOT . 'admin/dlist-table.php');
    unlink(ROOT . 'admin/set-cron.php');
    unlink(ROOT . 'admin/set-domain.php');
    unlink(ROOT . 'admin/set-email.php');
    unlink(ROOT . 'admin/set-jump.php');
    unlink(ROOT . 'admin/set-pay.php');
    unlink(ROOT . 'admin/set-safe.php');
    unlink(ROOT . 'admin/set-site.php');
    unlink(ROOT . 'admin/set-style.php');
    unlink(ROOT . 'admin/set-vip.php');
    unlink(ROOT . 'admin/ulist-table.php');
    unlink(ROOT . 'admin/url-modify-table.php');
    unlink(ROOT . 'admin/urlcheck-table.php');
    unlink(ROOT . 'admin/urllist-del-table.php');
    unlink(ROOT . 'admin/urllist-table.php');
    unlink(ROOT . 'user/urllist-table.php');
    unlink(ROOT . 'user/urlcheck-table.php');
} elseif ($conf['version'] < 4009) {
    $sqls = file_get_contents('update9.sql');
    $version = 4009;
    $res = $DB->get_row("SELECT * FROM information_schema.columns WHERE table_name='dwz_visitors' and column_name = 'a'");
    if (!$res) {
        $DB->query("ALTER TABLE `dwz_visitors` ADD COLUMN `adddate` date DEFAULT NULL");
    }
    unlink(ROOT . 'template/jump/jump1');
    unlink(ROOT . 'template/jump/jump2');
    unlink(ROOT . 'template/jump/jump3');
    unlink(ROOT . 'template/jump/jump4');
} else {
    exit('你的网站已经升级到最新版本了');
}
$explode = explode(';', $sqls);
$num = count($explode);
foreach ($explode as $sql) {
    if ($sql = trim($sql)) {
        $DB->query($sql);
    }
}
saveSetting('version', $version);
$CACHE->clear();
exit("<script language='javascript'>alert('网站数据库升级完成！');window.location.href='../';</script>");
