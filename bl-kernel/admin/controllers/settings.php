<?php defined('BLUDIT') or die('Bludit CMS.');

// ============================================================================
// Check role
// ============================================================================

checkRole(array('admin'));

// ============================================================================
// Functions
// ============================================================================

// ============================================================================
// Main after POST
// ============================================================================

// ============================================================================
// POST Method
// ============================================================================

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	// CSRF Token 验证
	if (!isset($_POST['tokenCSRF']) || !$security->validateTokenCSRF($_POST['tokenCSRF'])) {
		Alert::set($L->g('Invalid security token'), ALERT_STATUS_FAIL);
		Redirect::page('settings');
	}
	
	// ============================================================================
	// 语言设置单一真源处理（LANG_SINGLE_SOURCE）
	// ============================================================================
	
	// SystemIntegrity 授权检查（必须）
	SystemIntegrity::isAuthorized();
	
	// 1. 处理语言设置（保存到 users.php 顶层，不写入 site.php）
	if (isset($_POST['language'])) {
		$newLang = Sanitize::html($_POST['language']);
		
		// 验证语言文件是否存在
		if (file_exists(PATH_LANGUAGES . $newLang . '.json')) {
			$usersFile = PATH_AUTHZ . 'users.php';
			
			if (file_exists($usersFile) && is_readable($usersFile)) {
				// 读取现有 users.php
				$usersContent = file_get_contents($usersFile);
				$usersContent = str_replace("<?php defined('BLUDIT') or die('Bludit CMS.'); ?>", '', $usersContent);
				$usersData = json_decode(trim($usersContent), true);
				
				if (is_array($usersData)) {
					// 更新顶层 language 键
					$usersData['language'] = $newLang;
					
					// 写入 users.php
					$content = "<?php defined('BLUDIT') or die('Bludit CMS.'); ?>\n";
					$content .= json_encode($usersData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
					file_put_contents($usersFile, $content, LOCK_EX);
					
					// 立刻生效（重新加载语言）
					global $language, $L;
					$language = new Language($newLang);
					$L = $language;
					
					// 记录日志
					Log::set(__METHOD__ . LOG_SEP . 'Language changed to: ' . $newLang);
				}
			}
			
			// ★ 迁移清理：删除所有站点 site.php 中的 language/locale/timezone 键
			$siteDirs = glob(PATH_ROOT . 'sites/*/maigewan/databases/site.php');
			if (is_array($siteDirs)) {
				foreach ($siteDirs as $siteFile) {
					if (file_exists($siteFile) && is_readable($siteFile)) {
						$siteContent = file_get_contents($siteFile);
						$siteContent = str_replace("<?php defined('BLUDIT') or die('Bludit CMS.'); ?>", '', $siteContent);
						$siteData = json_decode(trim($siteContent), true);
						
						if (is_array($siteData)) {
							// 删除语言相关键
							$modified = false;
							if (isset($siteData['language'])) {
								unset($siteData['language']);
								$modified = true;
							}
							if (isset($siteData['locale'])) {
								unset($siteData['locale']);
								$modified = true;
							}
							if (isset($siteData['timezone'])) {
								unset($siteData['timezone']);
								$modified = true;
							}
							
							// 只有修改了才写入
							if ($modified) {
								$siteNewContent = "<?php defined('BLUDIT') or die('Bludit CMS.'); ?>\n";
								$siteNewContent .= json_encode($siteData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
								file_put_contents($siteFile, $siteNewContent, LOCK_EX);
							}
						}
					}
				}
			}
		}
		
		// 从 $_POST 中移除 language，避免被 editSettings 处理
		unset($_POST['language']);
	}
	
	// 2. 处理其他设置（调用原有函数，但不包含 language）
	editSettings($_POST);
	Redirect::page('settings');
}

// ============================================================================
// Main after POST
// ============================================================================

// Title of the page
$layout['title'] .= ' - '.$L->g('Advanced Settings');