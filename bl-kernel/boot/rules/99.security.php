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
	// ⚠️ 特殊处理：system-init 页面不需要 CSRF token 验证
	// 因为这是首次访问，用户还未登录，没有 session 来生成 token
	global $layout;
	
	if (isset($layout['slug']) && $layout['slug'] === 'system-init') {
		// 跳过 CSRF 验证
		unset($_POST['tokenCSRF']);
	} else {
		// 正常的 CSRF 验证
		$token = isset($_POST['tokenCSRF']) ? Sanitize::html($_POST['tokenCSRF']) : false;
		if (!$security->validateTokenCSRF($token)) {
			Log::set(__FILE__.LOG_SEP.'Error occurred when trying to validate the tokenCSRF.', ALERT_STATUS_FAIL);
			Log::set(__FILE__.LOG_SEP.'Token via POST ['.$token.']', ALERT_STATUS_FAIL);

			Session::destroy();
			Redirect::page('login');
		} else {
			unset( $_POST['tokenCSRF'] );
		}
	}
}

// ============================================================================
// Main after POST
// ============================================================================
