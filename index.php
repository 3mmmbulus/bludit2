<?php

/*
 * Bludit
 * https://www.bludit.com
 * Author Diego Najar
 * Bludit is opensource software licensed under the MIT license.
*/

// Load time init
$loadTime = microtime(true);

// Security constant
define('BLUDIT', true);

// Directory separator
define('DS', DIRECTORY_SEPARATOR);

// PHP paths for init
define('PATH_ROOT', __DIR__.DS);
define('PATH_BOOT', PATH_ROOT.'bl-kernel'.DS.'boot'.DS);

// === 多站点引导：域名解析 → 站点目录 ===
// 1) 获取当前访问域名（规范化为小写）
$currentHost = strtolower($_SERVER['HTTP_HOST'] ?? 'localhost');
// 去除端口号（如果有）
if (strpos($currentHost, ':') !== false) {
	$currentHost = substr($currentHost, 0, strpos($currentHost, ':'));
}

// 2) 多站点内容根目录
$sitesRoot = PATH_ROOT . 'sites' . DS;

// 3) 解析逻辑：完整匹配 → WWW 回退 → 不存在
$siteContentPath = null;

// 完整匹配
$fullMatchPath = $sitesRoot . $currentHost . DS . 'maigewan' . DS;
if (is_dir($fullMatchPath) && is_readable($fullMatchPath)) {
	$siteContentPath = $fullMatchPath;
} else {
	// WWW 回退：如果域名以 www. 开头，则尝试去掉 www.
	if (strpos($currentHost, 'www.') === 0) {
		$apexDomain = substr($currentHost, 4);
		$apexPath = $sitesRoot . $apexDomain . DS . 'maigewan' . DS;
		if (is_dir($apexPath) && is_readable($apexPath)) {
			$siteContentPath = $apexPath;
		}
	}
}

// 4) 如果站点目录不存在，尝试使用默认站点模板（如果存在）
if ($siteContentPath === null) {
	$defaultSitePath = $sitesRoot . 'default' . DS . 'maigewan' . DS;
	if (is_dir($defaultSitePath) && is_readable($defaultSitePath)) {
		$siteContentPath = $defaultSitePath;
		define('SITE_IS_DEFAULT', true);
	} else {
		// 默认站点也不存在，使用 bl-content 作为后备（让系统走正常安装流程）
		$siteContentPath = PATH_ROOT . 'bl-content' . DS;
	}
}

// 5) 动态定义 PATH_CONTENT 为解析后的站点目录
define('PATH_CONTENT', $siteContentPath);

// 6) 如果是默认站点，检查是否访问后台，非后台才显示维护页面
if (defined('SITE_IS_DEFAULT')) {
	// 检查是否访问 /admin 或 /admin/ 路径（白名单）
	$requestPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
	
	// 判断是否为后台路径：精确匹配 /admin 或以 /admin/ 开头
	$isAdminPath = ($requestPath === '/admin' || $requestPath === '/admin/' || strpos($requestPath, '/admin/') === 0);
	
	// 如果不是访问后台，显示维护页面
	if (!$isAdminPath) {
		$maintenancePage = PATH_CONTENT . 'index.php';
		if (file_exists($maintenancePage)) {
			include($maintenancePage);
			exit;
		} else {
			http_response_code(503);
			echo '<!DOCTYPE html><html><head><meta charset="UTF-8"><title>Site Not Available</title></head><body>';
			echo '<h1>Site Not Available</h1><p>This site has not been created yet.</p>';
			echo '</body></html>';
			exit;
		}
	}
	// 如果是访问后台，继续执行，不显示维护页面
}

// Init
require(PATH_BOOT.'init.php');

// Admin area
if ($url->whereAmI()==='admin') {
	require(PATH_BOOT.'admin.php');
}
// Site
else {
	// 如果是默认站点或站点未安装，不加载前台，显示维护信息
	if (defined('SITE_IS_DEFAULT') || !file_exists(PATH_CONTENT . 'databases' . DS . 'site.php')) {
		http_response_code(503);
		echo '<!DOCTYPE html><html><head><meta charset="UTF-8"><title>Site Not Available</title></head><body>';
		echo '<style>body{font-family:sans-serif;text-align:center;padding:50px;}h1{color:#333;}</style>';
		echo '<h1>Site Not Available</h1>';
		echo '<p>This site has not been created yet. Please contact the administrator.</p>';
		echo '</body></html>';
		exit;
	}
	require(PATH_BOOT.'site.php');
}
