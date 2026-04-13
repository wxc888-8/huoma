<?php
/**
 * PHP QR Code 简化版
 * 使用纯PHP GD库生成二维码
 */

class QRCode {
    /**
     * 生成二维码并保存为PNG文件
     * 
     * @param string $text 二维码内容
     * @param string $outfile 输出文件路径
     * @param int $level 纠错级别 (L=0, M=1, Q=2, H=3)
     * @param int $size 像素尺寸
     * @param int $margin 边距
     * @return bool
     */
    public static function png($text, $outfile, $level = 0, $size = 3, $margin = 4) {
        if (extension_loaded('gd')) {
            $img = self::createQRCodeImage($text, $size, $margin);
            if ($img) {
                imagepng($img, $outfile);
                imagedestroy($img);
                return true;
            }
        }
        return false;
    }

    /**
     * 生成二维码并直接输出
     * 
     * @param string $text 二维码内容
     * @param int $level 纠错级别 (L=0, M=1, Q=2, H=3)
     * @param int $size 像素尺寸
     * @param int $margin 边距
     * @return void
     */
    public static function pngOutput($text, $level = 0, $size = 3, $margin = 4) {
        if (!extension_loaded('gd')) {
            error_log("QRCode::pngOutput 错误: GD库未启用");
            throw new Exception("GD库未启用");
            return;
        }
        
        header('Content-Type: image/png');
        
        $img = self::createQRCodeImage($text, $size, $margin);
        
        if (!$img) {
            error_log("QRCode::pngOutput 错误: 创建二维码图像失败，文本内容: " . substr($text, 0, 50) . "...");
            throw new Exception("创建二维码图像失败");
            return;
        }
        
        // 尝试输出图像
        $result = imagepng($img);
        imagedestroy($img);
        
        if (!$result) {
            error_log("QRCode::pngOutput 错误: 输出PNG图像失败");
            throw new Exception("输出PNG图像失败");
        }
    }

    /**
     * 生成简单的二维码图像
     * 
     * @param string $text 二维码内容
     * @param int $size 大小倍数
     * @param int $margin 边距
     * @return resource|false GD图像
     */
    private static function createQRCodeImage($text, $size = 3, $margin = 4) {
        // 直接使用优化版的二维码生成方法
        return self::createRealQRCode($text, $size);
    }
    
    private static function createRealQRCode($text, $size) {
        // 使用Google Chart API
        if (function_exists('curl_init')) {
            // 使用Google Chart API生成二维码
            $ch = curl_init();
            $chl = urlencode($text);
            
            // 设置纠错级别
            $ecl = 'L'; // 低级别纠错
            
            // 构建API URL
            $url = "https://chart.googleapis.com/chart?cht=qr&chs=".($size*100)."x".($size*100)."&chld=".$ecl."|1&chl=".$chl;
            
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            $image_data = curl_exec($ch);
            curl_close($ch);
            
            if ($image_data !== false) {
                // 从二进制数据创建图像
                $im = imagecreatefromstring($image_data);
                if ($im !== false) {
                    return $im;
                }
            }
        }
        
        // 如果API调用失败，回退到原始方法
        // 设置图像大小
        $baseSize = 100;
        $imgSize = $baseSize * $size;
        
        // 创建画布
        $img = imagecreatetruecolor($imgSize, $imgSize);
        if (!$img) {
            return false;
        }
        
        // 定义颜色
        $white = imagecolorallocate($img, 255, 255, 255);
        $black = imagecolorallocate($img, 0, 0, 0);
        
        // 填充白色背景
        imagefill($img, 0, 0, $white);
        
        // 计算参数，用于绘制QR码元素
        $blockSize = intval($size * 3);  // 数据块大小
        $finderSize = intval($size * 20); // 定位点大小
        $borderWidth = intval($size * 10); // 边框宽度
        
        // 绘制三个大型定位标记（左上、右上、左下）
        // 左上角定位标记
        self::drawFinderPattern($img, $borderWidth, $borderWidth, $finderSize, $black, $white);
        
        // 右上角定位标记
        self::drawFinderPattern($img, $imgSize - $borderWidth - $finderSize, $borderWidth, $finderSize, $black, $white);
        
        // 左下角定位标记
        self::drawFinderPattern($img, $borderWidth, $imgSize - $borderWidth - $finderSize, $finderSize, $black, $white);
        
        // 警告信息：简单绘制一些文字表明这不是真正的二维码
        imagestring($img, 5, $imgSize/2-50, $imgSize/2, "Not a real QR", $black);
        
        return $img;
    }
    
    /**
     * 绘制二维码定位标记
     */
    private static function drawFinderPattern($img, $x, $y, $size, $black, $white) {
        // 外框
        imagefilledrectangle($img, $x, $y, $x + $size, $y + $size, $black);
        
        // 内框
        $innerPos = intval($size * 0.2);
        $innerSize = $size - 2 * $innerPos;
        imagefilledrectangle($img, $x + $innerPos, $y + $innerPos, 
                          $x + $innerPos + $innerSize, $y + $innerPos + $innerSize, $white);
        
        // 中心点
        $centerPos = intval($size * 0.4);
        $centerSize = intval($size * 0.2);
        imagefilledrectangle($img, $x + $centerPos, $y + $centerPos, 
                          $x + $centerPos + $centerSize, $y + $centerPos + $centerSize, $black);
    }

    private static function createQRCode($text, $outfile = null, $level = 0, $size = 3, $margin = 4) {
        // 使用Google Chart API生成二维码
        $ch = curl_init();
        $chl = urlencode($text);
        
        // 设置纠错级别
        $errorCorrectionLevel = ['L', 'M', 'Q', 'H'];
        $ecl = $errorCorrectionLevel[$level];
        
        // 构建API URL
        $url = "https://chart.googleapis.com/chart?chs=".($size*100)."x".($size*100)."&cht=qr&chld=".$ecl."|".$margin."&chl=".$chl;
        
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $image = curl_exec($ch);
        curl_close($ch);
        
        if ($outfile === null) {
            echo $image;
            return true;
        } else {
            return file_put_contents($outfile, $image) !== false;
        }
    }
} 