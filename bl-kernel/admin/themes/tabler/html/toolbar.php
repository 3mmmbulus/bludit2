<!-- Top Toolbar Navigation Bar (Desktop & Mobile) -->
<div class="navbar-toolbar d-print-none" id="navbar-toolbar">
	<header class="navbar navbar-expand-md">
		<div class="container-xl">
			<!-- Mobile Toggler -->
			<button class="navbar-toggler d-lg-none" type="button" data-bs-toggle="collapse" data-bs-target="#sidebar-menu" aria-controls="sidebar-menu" aria-expanded="false" aria-label="<?php $L->p('Toggle navigation') ?>">
				<span class="navbar-toggler-icon"></span>
			</button>
			
			<!-- Right Side Tools -->
			<div class="navbar-nav flex-row order-md-last">
				<!-- Theme Toggle -->
				<div class="d-none d-md-flex">
					<div class="nav-item">
						<a href="#" class="nav-link px-0 hide-theme-dark" data-theme-toggle="dark" title="<?php $L->p('Enable dark mode') ?>" data-bs-toggle="tooltip" data-bs-placement="bottom">
							<!-- Moon Icon -->
							<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon">
								<path d="M12 3c.132 0 .263 0 .393 0a7.5 7.5 0 0 0 7.92 12.446a9 9 0 1 1 -8.313 -12.454z"></path>
							</svg>
						</a>
						<a href="#" class="nav-link px-0 hide-theme-light" data-theme-toggle="light" title="<?php $L->p('Enable light mode') ?>" data-bs-toggle="tooltip" data-bs-placement="bottom">
							<!-- Sun Icon -->
							<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon">
								<path d="M12 12m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0"></path>
								<path d="M3 12h1m8 -9v1m8 8h1m-9 8v1m-6.4 -15.4l.7 .7m12.1 -.7l-.7 .7m0 11.4l.7 .7m-12.1 -.7l-.7 .7"></path>
							</svg>
						</a>
					</div>
				</div>
				
				<!-- Language Switcher -->
				<div class="nav-item dropdown d-none d-md-flex">
					<a href="#" class="nav-link px-0" data-bs-toggle="dropdown" 
					   title="<?php $L->p('Select language') ?>" 
					   aria-label="<?php $L->p('Select language') ?>"
					   data-bs-auto-close="true">
						<!-- Language Icon -->
						<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon">
							<path d="M4 5h7"></path>
							<path d="M9 3v2c0 4.418 -2.239 8 -5 8"></path>
							<path d="M5 9c0 2.144 2.952 3.908 6.7 4"></path>
							<path d="M12 20l4 -9l4 9"></path>
							<path d="M19.1 18h-6.2"></path>
						</svg>
					</a>
					<div class="dropdown-menu dropdown-menu-end">
						<?php
						// Get all available languages with error handling
						$languages = array('en', 'zh_CN'); // Default
						$currentLang = 'en';
						
						try {
							if (isset($site) && is_object($site)) {
								if (method_exists($site, 'getField')) {
									$siteLangs = $site->getField('languages');
									if (!empty($siteLangs) && is_array($siteLangs)) {
										$languages = $siteLangs;
									}
								}
								if (method_exists($site, 'currentLanguage')) {
									$currentLang = $site->currentLanguage();
								}
							}
							
							// Try Language class
							if (class_exists('Language') && isset($L)) {
								$currentLang = $L->currentLanguage();
							}
						} catch (Exception $e) {
							// Use defaults
						}
						
						// Language display names
						$langNames = array(
							'en' => 'English',
							'zh_CN' => '简体中文',
							'zh_TW' => '繁體中文',
							'es' => 'Español',
							'fr' => 'Français',
							'de' => 'Deutsch',
							'ja' => '日本語',
							'ko' => '한국어',
							'ru' => 'Русский',
							'pt' => 'Português',
							'it' => 'Italiano',
							'nl' => 'Nederlands',
							'pl' => 'Polski'
						);
						
						foreach ($languages as $langCode) {
							$langName = isset($langNames[$langCode]) ? $langNames[$langCode] : $langCode;
							$isActive = ($langCode === $currentLang);
							$activeClass = $isActive ? ' active' : '';
							
							echo '<a class="dropdown-item'.$activeClass.'" href="'.DOMAIN_ADMIN.'?language='.$langCode.'">';
							if ($isActive) {
								echo '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-inline me-2"><path d="M5 12l5 5l10 -10"></path></svg>';
							} else {
								echo '<span style="width:16px;display:inline-block;" class="me-2"></span>';
							}
							echo $langName;
							echo '</a>';
						}
						?>
					</div>
				</div>
				
				<!-- User Avatar Dropdown -->
				<div class="nav-item dropdown">
					<a href="#" class="nav-link d-flex lh-1 p-0 px-2" data-bs-toggle="dropdown" aria-label="<?php $L->p('Open user menu') ?>">
						<?php 
						// Auto-detect logo
						$logoPath = HTML_PATH_CORE_IMG . 'favicon.png';
						if (file_exists(PATH_UPLOADS . 'logo.svg')) {
							$logoPath = DOMAIN_UPLOADS . 'logo.svg';
						} elseif (file_exists(PATH_UPLOADS . 'logo.png')) {
							$logoPath = DOMAIN_UPLOADS . 'logo.png';
						} elseif (file_exists(PATH_CORE_IMG . 'logo.svg')) {
							$logoPath = HTML_PATH_CORE_IMG . 'logo.svg';
						}
						?>
						<span class="avatar avatar-sm" style="background-image: url(<?php echo $logoPath ?>)"></span>
						<div class="d-none d-xl-block ps-2">
							<div><?php echo $login->username() ?></div>
							<div class="mt-1 small text-secondary"><?php echo ucfirst($login->role()) ?></div>
						</div>
					</a>
					<div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
						<a href="<?php echo HTML_PATH_ADMIN_ROOT.'edit-user/'.$login->username() ?>" class="dropdown-item">
							<i class="bi bi-person me-2"></i><?php $L->p('Profile') ?>
						</a>
						<div class="dropdown-divider"></div>
						<a href="<?php echo HTML_PATH_ADMIN_ROOT.'logout' ?>" class="dropdown-item">
							<i class="bi bi-box-arrow-right me-2"></i><?php $L->p('Logout') ?>
						</a>
					</div>
				</div>
			</div>
		</div>
	</header>
</div>
