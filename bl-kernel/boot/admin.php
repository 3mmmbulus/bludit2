<?php defined('BLUDIT') or die('Bludit CMS.');

// Start the session
// If the session is not possible to start the admin area is not available
Session::start($site->urlPath(), $site->isHTTPS());
if (Session::started()===false) {
	exit('Bludit CMS. Session initialization failed.');
}

$login = new Login();

$layout = array(
	'controller'=>null,
	'view'=>null,
	'template'=>'index.php',
	'slug'=>null,
	'plugin'=>false,
	'parameters'=>null,
	'title'=>'Bludit'
);

// Get the Controller
$explodeSlug = $url->explodeSlug();
$layout['controller'] = $layout['view'] = $layout['slug'] = empty($explodeSlug[0])?'dashboard':$explodeSlug[0];
unset($explodeSlug[0]);

// Get the Plugins
include(PATH_RULES.'60.plugins.php');
// Check if the user want to access to an admin controller or view from a plugin
if ($layout['controller'] === 'plugin' && !empty($explodeSlug)) {
	// Lowercase plugins class name to search by case-insensitive
	$pluginsLowerCases = array_change_key_case($pluginsInstalled);
	$pluginName = Text::lowercase(array_shift($explodeSlug));
	if (isset($pluginsLowerCases[$pluginName])) {
		$layout['plugin'] = $pluginsLowerCases[$pluginName];
	}
}

// Get the URL parameters
$layout['parameters'] = implode('/', $explodeSlug);

// --- AJAX ---
if ($layout['slug']==='ajax') {
	if ($login->isLogged()) {
		// Rules: Security check CSRF
		include(PATH_RULES.'99.security.php');

		// Load the ajax file
		if (Sanitize::pathFile(PATH_AJAX, $layout['parameters'].'.php')) {
			include(PATH_AJAX.$layout['parameters'].'.php');
		}
	}
	header('HTTP/1.1 401 User not logged.');
	exit(0);
}
// --- ADMIN AREA ---
else
{
	// Boot rules
	include(PATH_RULES.'69.pages.php');
	include(PATH_RULES.'99.header.php');
	include(PATH_RULES.'99.paginator.php');
	include(PATH_RULES.'99.themes.php');
	include(PATH_RULES.'98.page-languages.php');
	include(PATH_RULES.'99.security.php');

	// ========== 系统初始化检测（懒加载+缓存） ==========
	// 检查系统是否已初始化，使用静态缓存，每个进程只检测一次
	// 性能开销：首次 ~0.05ms，后续 0ms
	
	$isInitialized = SystemIntegrity::isSystemInitialized();
	
	if (!$isInitialized && $layout['slug'] !== 'system-init') {
		// 系统未初始化，重定向到初始化页面
		Redirect::page('system-init');
		exit;
	}

	// Page not found.
	// User not logged.
	// Slug is login.
	// ⚠️ 特殊处理：system-init 页面允许在未登录状态下访问，不触发登录逻辑
	if (($url->notFound() || !$login->isLogged() || ($url->slug()==='login')) && $layout['slug'] !== 'system-init') {
		// If user is not logged and trying to access /admin/ (dashboard), redirect to login
		if (!$login->isLogged() && ($layout['slug'] === 'dashboard' || $url->slug() === '')) {
			Redirect::page('login');
		}
		
		$layout['controller']	= 'login';
		$layout['view']			= 'login';
		$layout['template']		= 'login.php';

		// Generate the tokenCSRF for the user not logged, when the user log-in the token will be change.
		$security->generateTokenCSRF();
	}

	// Define variables
	$ADMIN_CONTROLLER 	= $layout['controller'];
	$ADMIN_VIEW 		= $layout['view'];

	// Load plugins before the admin area will be load.
	Theme::plugins('beforeAdminLoad');

	// Load init.php if the theme has one.
	if (Sanitize::pathFile(PATH_ADMIN_THEMES, $site->adminTheme().DS.'init.php')) {
		include(PATH_ADMIN_THEMES.$site->adminTheme().DS.'init.php');
	}

	// Load controller.
	if (Sanitize::pathFile(PATH_ADMIN_CONTROLLERS, $layout['controller'].'.php')) {
		include(PATH_ADMIN_CONTROLLERS.$layout['controller'].'.php');
	} elseif ($layout['plugin'] && method_exists($layout['plugin'], 'adminController')) {
		$layout['plugin']->adminController();
	}

	// Load view and theme.
	if (Sanitize::pathFile(PATH_ADMIN_THEMES, $site->adminTheme().DS.$layout['template'])) {
		include(PATH_ADMIN_THEMES.$site->adminTheme().DS.$layout['template']);
	}

	// Load plugins after the admin area is loaded.
	Theme::plugins('afterAdminLoad');
}
