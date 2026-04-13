#!/bin/bash
# 活码系统 - 一键部署脚本 (适用于宝塔面板)
# 此版本会自动清理旧版遗留的 PHP 文件，保持系统纯净！
# 确保在网站根目录执行，例如: cd /www/wwwroot/www.qlhuoma.com && bash deploy.sh

echo "========================================="
echo "开始部署新版前后端分离活码系统，并清理旧版文件..."
echo "========================================="

SITE_DIR=$(pwd)

# 1. 检查前端编译文件
if [ ! -d "$SITE_DIR/frontend/dist" ]; then
    echo "[错误] 找不到 frontend/dist 目录！"
    echo "请确保已经执行过 pnpm run build，或者您所在的目录不正确。"
    exit 1
fi

# 2. 删除旧版冗余文件 (这会让系统变干净，不再需要旧版的混编代码)
echo "[1/4] 正在清理旧版 PHP 文件与目录..."
rm -rf "$SITE_DIR/user"
rm -rf "$SITE_DIR/admin"
rm -rf "$SITE_DIR/template/default"
rm -rf "$SITE_DIR/template/index.php"
# (注：保留 template/page 因为它是用于底层防封跳转的模板)
# (注：保留 includes/ 和 qr.php 等核心逻辑引擎文件)
echo "✅ 旧版文件清理完成！"

# 3. 部署前端单页应用到 /app 目录
echo "[2/4] 正在部署前端文件到 /app 目录..."
mkdir -p "$SITE_DIR/app"
rm -rf "$SITE_DIR/app/*"
cp -r "$SITE_DIR/frontend/dist/"* "$SITE_DIR/app/"

if [ -f "$SITE_DIR/app/index.html" ]; then
    echo "✅ 前端部署成功！文件已复制到 $SITE_DIR/app/"
else
    echo "❌ 前端部署失败！请检查文件权限。"
    exit 1
fi

# 4. 设置权限 (宝塔默认网站用户为 www)
echo "[3/4] 正在设置目录权限为 www:www ..."
if id "www" &>/dev/null; then
    chown -R www:www "$SITE_DIR"
    find "$SITE_DIR" -type d -exec chmod 755 {} \;
    find "$SITE_DIR" -type f -exec chmod 644 {} \;
    echo "✅ 权限设置成功！"
else
    echo "⚠️ 未检测到 www 用户，跳过权限设置。"
fi

# 5. 提醒配置 Nginx
echo "[4/4] Nginx 伪静态配置提醒"
echo "========================================="
echo "系统更新完成！旧版已删除，现在只用新版！"
echo "最后一步，请前往【宝塔面板】 -> 【网站】 -> 【设置】 -> 【伪静态】"
echo "将以下规则追加到现有的伪静态规则末尾并保存："
echo ""
echo "location ^~ /app/ {"
echo "  try_files \$uri \$uri/ /app/index.html;"
echo "}"
echo ""
echo "location ^~ /api/v1/ {"
echo "  rewrite ^/api/v1/(.*)$ /api/v1/index.php?r=\$1 last;"
echo "}"
echo ""
echo "location = / {"
echo "  rewrite ^/$ /app/login redirect;"
echo "}"
echo "========================================="
echo "🎉 部署完成！"
echo "由于删除了旧版入口，现在访问您的域名首页会自动跳到新版登录页"
echo "新版用户端访问: https://您的域名/app/login"
echo "新版管理员访问: https://您的域名/app/admin/login"
