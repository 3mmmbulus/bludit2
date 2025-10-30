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

// 4) 如果站点目录不存在，输出提示并退出
if ($siteContentPath === null) {
	// 加载基础语言文件以显示友好提示
	$langCode = 'en'; // 默认英语
	if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
		$acceptLang = strtolower($_SERVER['HTTP_ACCEPT_LANGUAGE']);
		if (strpos($acceptLang, 'zh') !== false) {
			$langCode = 'zh_CN';
		}
	}
	
	$langFile = PATH_ROOT . 'bl-languages' . DS . 'pages' . DS . 'site-bootstrap' . DS . $langCode . '.json';
	$messages = ['site_missing_title' => 'Website Not Created', 'site_missing_hint' => 'Please create this website in admin panel.'];
	
	if (file_exists($langFile)) {
		$langData = @json_decode(file_get_contents($langFile), true);
		if (is_array($langData)) {
			$messages = array_merge($messages, $langData);
		}
	}
	
	http_response_code(404);
	header('Content-Type: text/html; charset=UTF-8');
	echo '<!DOCTYPE html><html><head><meta charset="UTF-8"><title>' . htmlspecialchars($messages['site_missing_title'], ENT_QUOTES, 'UTF-8') . '</title>';
	echo '<style>body{font-family:sans-serif;text-align:center;padding:50px;}h1{color:#666;}p{color:#999;}</style></head><body>';
	echo '<h1>' . htmlspecialchars($messages['site_missing_title'], ENT_QUOTES, 'UTF-8') . '</h1>';
	echo '<p>' . htmlspecialchars($messages['site_missing_hint'], ENT_QUOTES, 'UTF-8') . '</p>';
	echo '<p style="font-size:0.9em;margin-top:30px;">Domain: <strong>' . htmlspecialchars($currentHost, ENT_QUOTES, 'UTF-8') . '</strong></p>';
	echo '</body></html>';
	exit;
}

// 5) 动态定义 PATH_CONTENT 为解析后的站点目录
define('PATH_CONTENT', $siteContentPath);

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
