<!doctype html>
<html lang="zh">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<meta name="robots" content="noindex,nofollow">
	
	<title><?php echo isset($layout['title']) ? $layout['title'] : '系统初始化' ?> - Bludit</title>
	
	<!-- Favicon -->
	<link rel="icon" type="image/png" href="/bl-kernel/img/favicon.png">
	
	<!-- CSS -->
	<link rel="stylesheet" href="/bl-kernel/admin/themes/tabler/css/tabler.min.css">
	<link rel="stylesheet" href="/bl-kernel/css/system-init.css">
	
	<!-- Tabler Theme Script (must be loaded before body) -->
	<script src="/bl-kernel/admin/themes/tabler/js/tabler-theme.min.js"></script>
</head>
<body>
	
	<!-- jQuery must load before view to support Bootstrap::formClose() -->
	<script src="/bl-kernel/js/jquery.min.js"></script>
	
	<!-- Top Bar with Language and Theme Buttons -->
	<div class="position-fixed top-0 end-0 p-3" style="z-index: 1050;">
		<div class="d-flex gap-2">
			<!-- Language Dropdown -->
			<div class="dropdown">
				<button class="btn btn-ghost-secondary btn-icon" type="button" data-bs-toggle="dropdown" aria-expanded="false" title="Language">
					<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon">
						<path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
						<path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0"></path>
						<path d="M3.6 9h16.8"></path>
						<path d="M3.6 15h16.8"></path>
						<path d="M11.5 3a17 17 0 0 0 0 18"></path>
						<path d="M12.5 3a17 17 0 0 1 0 18"></path>
					</svg>
				</button>
				<ul class="dropdown-menu dropdown-menu-end">
					<li><a class="dropdown-item" href="?language=zh_CN">简体中文</a></li>
					<li><a class="dropdown-item" href="?language=en">English</a></li>
				</ul>
			</div>
			
			<!-- Theme Button -->
			<button class="btn btn-ghost-secondary btn-icon" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasTheme" aria-controls="offcanvasTheme" title="Theme Settings">
				<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon">
					<path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
					<path d="M10.325 4.317c.426 -1.756 2.924 -1.756 3.35 0a1.724 1.724 0 0 0 2.573 1.066c1.543 -.94 3.31 .826 2.37 2.37a1.724 1.724 0 0 0 1.065 2.572c1.756 .426 1.756 2.924 0 3.35a1.724 1.724 0 0 0 -1.066 2.573c.94 1.543 -.826 3.31 -2.37 2.37a1.724 1.724 0 0 0 -2.572 1.065c-.426 1.756 -2.924 1.756 -3.35 0a1.724 1.724 0 0 0 -2.573 -1.066c-1.543 .94 -3.31 -.826 -2.37 -2.37a1.724 1.724 0 0 0 -1.065 -2.572c-1.756 -.426 -1.756 -2.924 0 -3.35a1.724 1.724 0 0 0 1.066 -2.573c-.94 -1.543 .826 -3.31 2.37 -2.37c1 .608 2.296 .07 2.572 -1.065z"></path>
					<path d="M9 12a3 3 0 1 0 6 0a3 3 0 0 0 -6 0"></path>
				</svg>
			</button>
		</div>
	</div>

	<div class="page page-center">
		<div class="container container-tight py-4">
			
			<!-- Brand Logo -->
			<div class="text-center mb-4">
				<a href="/" class="navbar-brand navbar-brand-autodark">
					<img src="/bl-kernel/img/logo.svg" height="36" alt="Bludit" class="navbar-brand-image">
				</a>
			</div>

			<!-- Main Card -->
			<div class="card card-md">
				<div class="card-body">
					<?php
						if (Sanitize::pathFile(PATH_ADMIN_VIEWS, $layout['view'].'.php')) {
							include(PATH_ADMIN_VIEWS.$layout['view'].'.php');
						}
					?>
				</div>
			</div>

			<!-- Security Tips Card -->
			<div class="card mt-3">
				<div class="card-body">
					<h3 class="card-title"><?php echo isset($pageL) ? $pageL->get('security_tips') : '安全提示' ?></h3>
					<ul class="mb-0 small text-muted">
						<li class="mb-1"><?php echo isset($pageL) ? $pageL->get('tip_username') : '请使用字母、数字组合的用户名' ?></li>
						<li class="mb-1"><?php echo isset($pageL) ? $pageL->get('tip_password') : '密码至少8位，建议包含大小写字母、数字和特殊字符' ?></li>
						<li><?php echo isset($pageL) ? $pageL->get('tip_save_credentials') : '请妥善保管初始管理员账号信息' ?></li>
					</ul>
				</div>
			</div>

			<!-- Footer -->
			<?php include(PATH_ADMIN_THEMES . $site->adminTheme() . DS . 'includes' . DS . 'footer.php'); ?>
		</div>
	</div>

	<!-- Theme Settings Offcanvas -->
	<div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasTheme" aria-labelledby="offcanvasThemeLabel">
		<div class="offcanvas-header">
			<h3 class="offcanvas-title" id="offcanvasThemeLabel"><?php echo isset($pageL) ? $pageL->get('theme_settings') : '主题设置' ?></h3>
			<button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
		</div>
		<div class="offcanvas-body">
			
			<!-- Theme Mode -->
			<div class="mb-4">
				<label class="form-label"><?php echo isset($pageL) ? $pageL->get('theme_mode') : '主题模式' ?></label>
				<div class="btn-group w-100" role="group" id="jsThemeModeGroup">
					<input type="radio" class="btn-check" name="theme-mode" id="theme-light" value="light" autocomplete="off">
					<label class="btn" for="theme-light">
						<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-sun me-1" viewBox="0 0 16 16">
							<path d="M8 11a3 3 0 1 1 0-6 3 3 0 0 1 0 6zm0 1a4 4 0 1 0 0-8 4 4 0 0 0 0 8zM8 0a.5.5 0 0 1 .5.5v2a.5.5 0 0 1-1 0v-2A.5.5 0 0 1 8 0zm0 13a.5.5 0 0 1 .5.5v2a.5.5 0 0 1-1 0v-2A.5.5 0 0 1 8 13zm8-5a.5.5 0 0 1-.5.5h-2a.5.5 0 0 1 0-1h2a.5.5 0 0 1 .5.5zM3 8a.5.5 0 0 1-.5.5h-2a.5.5 0 0 1 0-1h2A.5.5 0 0 1 3 8zm10.657-5.657a.5.5 0 0 1 0 .707l-1.414 1.415a.5.5 0 1 1-.707-.708l1.414-1.414a.5.5 0 0 1 .707 0zm-9.193 9.193a.5.5 0 0 1 0 .707L3.05 13.657a.5.5 0 0 1-.707-.707l1.414-1.414a.5.5 0 0 1 .707 0zm9.193 2.121a.5.5 0 0 1-.707 0l-1.414-1.414a.5.5 0 0 1 .707-.707l1.414 1.414a.5.5 0 0 1 0 .707zM4.464 4.465a.5.5 0 0 1-.707 0L2.343 3.05a.5.5 0 1 1 .707-.707l1.414 1.414a.5.5 0 0 1 0 .708z"/>
						</svg>
						<?php echo isset($pageL) ? $pageL->get('theme_light') : '亮色' ?>
					</label>
					
					<input type="radio" class="btn-check" name="theme-mode" id="theme-dark" value="dark" autocomplete="off">
					<label class="btn" for="theme-dark">
						<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-moon me-1" viewBox="0 0 16 16">
							<path d="M6 .278a.768.768 0 0 1 .08.858 7.208 7.208 0 0 0-.878 3.46c0 4.021 3.278 7.277 7.318 7.277.527 0 1.04-.055 1.533-.16a.787.787 0 0 1 .81.316.733.733 0 0 1-.031.893A8.349 8.349 0 0 1 8.344 16C3.734 16 0 12.286 0 7.71 0 4.266 2.114 1.312 5.124.06A.752.752 0 0 1 6 .278zM4.858 1.311A7.269 7.269 0 0 0 1.025 7.71c0 4.02 3.279 7.276 7.319 7.276a7.316 7.316 0 0 0 5.205-2.162c-.337.042-.68.063-1.029.063-4.61 0-8.343-3.714-8.343-8.29 0-1.167.242-2.278.681-3.286z"/>
						</svg>
						<?php echo isset($pageL) ? $pageL->get('theme_dark') : '暗色' ?>
					</label>
					
					<input type="radio" class="btn-check" name="theme-mode" id="theme-auto" value="auto" autocomplete="off" checked>
					<label class="btn" for="theme-auto">
						<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-circle-half me-1" viewBox="0 0 16 16">
							<path d="M8 15A7 7 0 1 0 8 1v14zm0 1A8 8 0 1 1 8 0a8 8 0 0 1 0 16z"/>
						</svg>
						<?php echo isset($pageL) ? $pageL->get('theme_auto') : '自动' ?>
					</label>
				</div>
			</div>
			
			<!-- Theme Color -->
			<div class="mb-4">
				<label class="form-label"><?php echo isset($pageL) ? $pageL->get('theme_color') : '主题颜色' ?></label>
				<div class="row g-2">
					<?php
					$colors = array(
						'blue' => '#206bc4',
						'azure' => '#4299e1',
						'indigo' => '#667eea',
						'purple' => '#a855f7',
						'pink' => '#ec4899',
						'red' => '#ef4444',
						'orange' => '#f59e0b',
						'yellow' => '#eab308',
						'lime' => '#84cc16',
						'green' => '#22c55e',
						'teal' => '#14b8a6',
						'cyan' => '#06b6d4'
					);
					
					foreach ($colors as $colorName => $colorValue) {
						$checked = ($colorName === 'blue') ? 'checked' : '';
						echo '<div class="col-4">';
						echo '<label class="form-colorinput">';
						echo '<input name="theme-color" type="radio" value="' . $colorName . '" class="form-colorinput-input" ' . $checked . '>';
						echo '<span class="form-colorinput-color" style="background-color: ' . $colorValue . '"></span>';
						echo '</label>';
						echo '</div>';
					}
					?>
				</div>
			</div>
			
			<div class="alert alert-info mb-0">
				<svg xmlns="http://www.w3.org/2000/svg" class="icon alert-icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
					<path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
					<circle cx="12" cy="12" r="9"></circle>
					<line x1="12" y1="8" x2="12.01" y2="8"></line>
					<polyline points="11 12 12 12 12 16 13 16"></polyline>
				</svg>
				<div>主题设置将保存在浏览器本地存储中</div>
			</div>
		</div>
	</div>
	
	<!-- JavaScript -->
	<script src="/bl-kernel/admin/themes/tabler/js/tabler.min.js"></script>
	<script src="/bl-kernel/js/system-init.js"></script>
</body>
</html>
