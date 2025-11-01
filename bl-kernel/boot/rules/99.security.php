<?php defined('BLUDIT') or die('Bludit CMS.');

// ============================================================================
// Variables
// ============================================================================

// ============================================================================
// Functions
// ============================================================================

// ============================================================================
// Main before POST
// ============================================================================

// ============================================================================
// POST Method
// ============================================================================

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	// 🔍 调试：记录 POST 进入 security.php 时的状态
	$debugLog = PATH_ROOT . 'system-init-debug.log';
	$debugMsg = date('Y-m-d H:i:s') . " - 99.security.php POST check\n";
	$debugMsg .= "POST count BEFORE check: " . count($_POST) . "\n";
	$debugMsg .= "POST keys BEFORE: " . implode(', ', array_keys($_POST)) . "\n";
	
	// ⚠️ 特殊处理：system-init 页面不需要 CSRF token 验证
	// 因为这是首次访问，用户还未登录，没有 session 来生成 token
	global $layout;
	
	$debugMsg .= "Slug: " . (isset($layout['slug']) ? $layout['slug'] : 'NOT SET') . "\n";
	$debugMsg .= "tokenCSRF in POST: " . (isset($_POST['tokenCSRF']) ? 'YES' : 'NO') . "\n";
	file_put_contents($debugLog, $debugMsg, FILE_APPEND);
	
	if (isset($layout['slug']) && $layout['slug'] === 'system-init') {
		// 跳过 CSRF 验证
		file_put_contents($debugLog, "SKIP: CSRF check bypassed for system-init\n", FILE_APPEND);
		unset($_POST['tokenCSRF']);
	} else {
		// 正常的 CSRF 验证
		$token = isset($_POST['tokenCSRF']) ? Sanitize::html($_POST['tokenCSRF']) : false;
		if (!$security->validateTokenCSRF($token)) {
			file_put_contents($debugLog, "FAIL: CSRF validation failed\n", FILE_APPEND);
			Log::set(__FILE__.LOG_SEP.'Error occurred when trying to validate the tokenCSRF.', ALERT_STATUS_FAIL);
			Log::set(__FILE__.LOG_SEP.'Token via POST ['.$token.']', ALERT_STATUS_FAIL);

			Session::destroy();
			Redirect::page('login');
		} else {
			file_put_contents($debugLog, "PASS: CSRF validation passed\n", FILE_APPEND);
			unset( $_POST['tokenCSRF'] );
		}
	}
}

// ============================================================================
// Main after POST
// ============================================================================
