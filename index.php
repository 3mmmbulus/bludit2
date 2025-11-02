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

// 4) 如果站点目录不存在，使用默认站点模板
if ($siteContentPath === null) {
	// 使用默认站点模板
	$defaultSitePath = $sitesRoot . 'default' . DS . 'maigewan' . DS;
	
	if (is_dir($defaultSitePath) && is_readable($defaultSitePath)) {
		// 使用默认站点目录
		$siteContentPath = $defaultSitePath;
		// 标记为默认站点（可选，供后续逻辑判断）
		define('SITE_IS_DEFAULT', true);
	} else {
		// 默认站点模板也不存在，显示错误
		http_response_code(503);
		header('Content-Type: text/html; charset=UTF-8');
		echo '<!DOCTYPE html><html><head><meta charset="UTF-8"><title>System Error</title>';
		echo '<style>body{font-family:sans-serif;text-align:center;padding:50px;}h1{color:#d63939;}p{color:#999;}</style></head><body>';
		echo '<h1>System Configuration Error</h1>';
		echo '<p>Default site template not found. Please contact system administrator.</p>';
		echo '<p style="font-size:0.9em;margin-top:30px;">Path: <code>' . htmlspecialchars($defaultSitePath, ENT_QUOTES, 'UTF-8') . '</code></p>';
		echo '</body></html>';
		exit;
	}
}

// 5) 动态定义 PATH_CONTENT 为解析后的站点目录
define('PATH_CONTENT', $siteContentPath);

// 6) 如果是默认站点，直接显示维护页面（不执行后续的 init.php 和站点逻辑）
if (defined('SITE_IS_DEFAULT')) {
	// 加载默认站点的维护页面
	$maintenancePage = PATH_CONTENT . 'index.php';
	if (file_exists($maintenancePage)) {
		include($maintenancePage);
		exit;
	} else {
		// 如果维护页面不存在，显示简单错误
		http_response_code(503);
		echo '<!DOCTYPE html><html><head><meta charset="UTF-8"><title>Site Not Available</title></head><body>';
		echo '<h1>Site Not Available</h1><p>This site has not been created yet.</p>';
		echo '</body></html>';
		exit;
	}
}

// 检查站点是否已安装（站点目录下必须有 databases/site.php）
if (!file_exists(PATH_CONTENT . 'databases' . DS . 'site.php')) {
	$base = dirname($_SERVER['SCRIPT_NAME']);
	$base = rtrim($base, '/');
	$base = rtrim($base, '\\'); // Workaround for Windows Servers
	header('Location:'.$base.'/install.php');
	exit('<a href="./install.php">Install Bludit first.</a>');
}

// Init
require(PATH_BOOT.'init.php');

// Admin area
if ($url->whereAmI()==='admin') {
	require(PATH_BOOT.'admin.php');
}
// Site
else {
	require(PATH_BOOT.'site.php');
}
