#!/bin/bash
# 活码系统 - 一键部署脚本 (适用于宝塔面板)
# 确保在网站根目录执行，例如: cd /www/wwwroot/www.qlhuoma.com && bash deploy.sh

echo "========================================="
echo "开始部署新版前后端分离活码系统..."
echo "========================================="

# 1. 检查当前目录
SITE_DIR=$(pwd)
if [ ! -d "$SITE_DIR/frontend/dist" ]; then
    echo "[错误] 找不到 frontend/dist 目录！"
    echo "请确保已经执行过 pnpm run build，或者您所在的目录不正确。"
    exit 1
fi

# 2. 部署前端单页应用到 /app 目录
echo "[1/3] 正在部署前端文件到 /app 目录..."
mkdir -p "$SITE_DIR/app"
# 清空旧的app目录内容
rm -rf "$SITE_DIR/app/*"
# 复制新的编译产物
cp -r "$SITE_DIR/frontend/dist/"* "$SITE_DIR/app/"

if [ -f "$SITE_DIR/app/index.html" ]; then
    echo "✅ 前端部署成功！文件已复制到 $SITE_DIR/app/"
else
    echo "❌ 前端部署失败！请检查文件权限。"
    exit 1
fi

# 3. 设置权限 (宝塔默认网站用户为 www)
echo "[2/3] 正在设置目录权限为 www:www ..."
if id "www" &>/dev/null; then
    chown -R www:www "$SITE_DIR/app"
    chown -R www:www "$SITE_DIR/api"
    chmod -R 755 "$SITE_DIR/app"
    chmod -R 755 "$SITE_DIR/api"
    echo "✅ 权限设置成功！"
else
    echo "⚠️ 未检测到 www 用户，跳过权限设置(如果您不在宝塔环境，请忽略)。"
fi

# 4. 提醒配置 Nginx
echo "[3/3] Nginx 伪静态配置提醒"
echo "========================================="
echo "代码已就位！最后一步，请前往【宝塔面板】 -> 【网站】 -> 【设置】 -> 【伪静态】"
echo "将以下规则追加到现有的伪静态规则末尾并保存："
echo ""
echo "location ^~ /app/ {"
echo "  try_files \$uri \$uri/ /app/index.html;"
echo "}"
echo ""
echo "location ^~ /api/v1/ {"
echo "  rewrite ^/api/v1/(.*)$ /api/v1/index.php?r=\$1 last;"
echo "}"
echo "========================================="
echo "🎉 部署完成！"
echo "用户端访问: https://您的域名/app/login"
echo "管理员访问: https://您的域名/app/admin/login"
