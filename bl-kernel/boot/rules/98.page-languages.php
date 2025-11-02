<?php defined('BLUDIT') or die('Bludit CMS.');

// ============================================================================
// Load page-specific language files
// 加载页面特定的语言文件
// ============================================================================

$currentLang = $language->currentLanguage();
$pagesDir = PATH_LANGUAGES . 'pages/';

// Load all page language files to make navigation translations available
// 加载所有页面语言文件，使导航翻译可用
if (is_dir($pagesDir)) {
	$pageDirs = scandir($pagesDir);
	foreach ($pageDirs as $pageDir) {
		if ($pageDir === '.' || $pageDir === '..') {
			continue;
		}
		
		$pageLanguageFile = $pagesDir . $pageDir . '/' . $currentLang . '.json';
		if (file_exists($pageLanguageFile)) {
			$content = file_get_contents($pageLanguageFile);
			$pageTranslations = json_decode($content, true);
			
			if (is_array($pageTranslations)) {
				// Add page translations to global language object
				// 将页面翻译添加到全局语言对象
				$L->add($pageTranslations);
			}
		}
	}
}
