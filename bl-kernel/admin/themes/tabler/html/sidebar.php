<?php
/**
 * Navigation State Helpers
 * 检查当前路由并添加对应的 active/show 类
 */
function isCurrentRoute($routes) {
	global $url;
	$currentSlug = $url->slug();
	if (!is_array($routes)) {
		$routes = [$routes];
	}
	foreach ($routes as $route) {
		if (strpos($currentSlug, $route) === 0) {
			return true;
		}
	}
	return false;
}

function getActiveClass($routes) {
	return isCurrentRoute($routes) ? 'active' : '';
}

function getShowClass($routes) {
	return isCurrentRoute($routes) ? 'show' : '';
}
?>
<aside class="navbar navbar-vertical navbar-expand-lg navbar-light navbar-transparent">
	<div class="container-fluid">
		<!-- BEGIN NAVBAR TOGGLER -->
		<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#sidebar-menu" aria-controls="sidebar-menu" aria-expanded="false" aria-label="Toggle navigation">
			<span class="navbar-toggler-icon"></span>
		</button>
		<!-- END NAVBAR TOGGLER -->
		
		<!-- BEGIN NAVBAR LOGO -->
		<div class="navbar-brand navbar-brand-autodark">
			<a href="<?php echo HTML_PATH_ADMIN_ROOT.'dashboard' ?>" aria-label="<?php echo (defined('BLUDIT_PRO'))?'BLUDIT PRO':'BLUDIT' ?>">
				<img src="<?php echo HTML_PATH_CORE_IMG ?>logo.svg" width="110" height="32" alt="Bludit" class="navbar-brand-image">
			</a>
		</div>
		<!-- END NAVBAR LOGO -->
		
		<div class="collapse navbar-collapse" id="sidebar-menu">
			<!-- BEGIN NAVBAR MENU -->
			<ul class="navbar-nav pt-lg-3">
			<!-- 1. 首页 / Dashboard -->
			<li class="nav-item">
				<a class="nav-link <?php echo getActiveClass('dashboard') ?>" href="<?php echo HTML_PATH_ADMIN_ROOT.'dashboard' ?>">
					<span class="nav-link-icon d-md-none d-lg-inline-block">
						<i class="bi bi-house-door"></i>
					</span>
					<span class="nav-link-title"><?php $L->p('Dashboard') ?></span>
				</a>
			</li>				<?php if (!checkRole(array('admin'),false)): ?>
				<!-- Non-admin: Simple Navigation -->
				<li class="nav-item">
					<a class="nav-link" href="<?php echo HTML_PATH_ADMIN_ROOT.'new-content' ?>">
						<span class="nav-link-icon d-md-none d-lg-inline-block">
							<i class="bi bi-plus-circle"></i>
						</span>
						<span class="nav-link-title"><?php $L->p('New content') ?></span>
					</a>
				</li>
				<li class="nav-item">
					<a class="nav-link" href="<?php echo HTML_PATH_ADMIN_ROOT.'content' ?>">
						<span class="nav-link-icon d-md-none d-lg-inline-block">
							<i class="bi bi-file-earmark-text"></i>
						</span>
						<span class="nav-link-title"><?php $L->p('Content') ?></span>
					</a>
				</li>
				<li class="nav-item">
					<a class="nav-link" href="<?php echo HTML_PATH_ADMIN_ROOT.'edit-user/'.$login->username() ?>">
						<span class="nav-link-icon d-md-none d-lg-inline-block">
							<i class="bi bi-person"></i>
						</span>
						<span class="nav-link-title"><?php $L->p('Profile') ?></span>
					</a>
				</li>
				<?php endif; ?>
				
				<?php if (checkRole(array('admin'),false)): ?>
				
			<!-- 2. 站点管理 dropdown -->
			<li class="nav-item dropdown <?php echo getActiveClass(['website', 'site-new']) ?>" data-nav-id="navbar-site">
				<a class="nav-link dropdown-toggle" href="#navbar-site" data-bs-toggle="dropdown" data-bs-auto-close="false" role="button" aria-expanded="<?php echo isCurrentRoute(['website', 'site-new']) ? 'true' : 'false' ?>">
					<span class="nav-link-icon d-md-none d-lg-inline-block">
						<i class="bi bi-globe"></i>
					</span>
					<span class="nav-link-title"><?php $L->p('Site Management') ?></span>
				</a>
				<div class="dropdown-menu <?php echo getShowClass(['website', 'site-new']) ?>">
					<div class="dropdown-menu-columns">
						<div class="dropdown-menu-column">
							<a class="dropdown-item <?php echo getActiveClass('website') ?>" href="<?php echo HTML_PATH_ADMIN_ROOT.'website' ?>">
								<i class="bi bi-bar-chart me-2"></i><?php $L->p('Site Overview') ?>
							</a>
							<a class="dropdown-item <?php echo getActiveClass('site-new') ?>" href="<?php echo HTML_PATH_ADMIN_ROOT.'site-new' ?>">
								<i class="bi bi-plus-circle me-2"></i><?php $L->p('Create New Site') ?>
							</a>
						</div>
					</div>
				</div>
			</li>				<!-- 3. SEO dropdown -->
			<li class="nav-item dropdown <?php echo getActiveClass(['seo-settings', 'content', 'media-images', 'brand-logo']) ?>" data-nav-id="navbar-seo">
				<a class="nav-link dropdown-toggle" href="#navbar-seo" data-bs-toggle="dropdown" data-bs-auto-close="false" role="button" aria-expanded="<?php echo isCurrentRoute(['seo-settings', 'content', 'media-images', 'brand-logo']) ? 'true' : 'false' ?>">
					<span class="nav-link-icon d-md-none d-lg-inline-block">
						<i class="bi bi-search"></i>
					</span>
					<span class="nav-link-title">SEO</span>
				</a>
				<div class="dropdown-menu <?php echo getShowClass(['seo-settings', 'content', 'media-images', 'brand-logo']) ?>">
					<div class="dropdown-menu-columns">
						<div class="dropdown-menu-column">
							<a class="dropdown-item <?php echo getActiveClass('seo-settings') ?>" href="<?php echo HTML_PATH_ADMIN_ROOT.'seo-settings' ?>">
								<i class="bi bi-sliders me-2"></i><?php $L->p('SEO Settings') ?>
							</a>
							<a class="dropdown-item <?php echo getActiveClass('content') ?>" href="<?php echo HTML_PATH_ADMIN_ROOT.'content' ?>">
								<i class="bi bi-files me-2"></i><?php $L->p('Content Management') ?>
							</a>
							<a class="dropdown-item <?php echo getActiveClass('media-images') ?>" href="<?php echo HTML_PATH_ADMIN_ROOT.'media-images' ?>">
								<i class="bi bi-images me-2"></i><?php $L->p('Media Images') ?>
							</a>
							<a class="dropdown-item <?php echo getActiveClass('brand-logo') ?>" href="<?php echo HTML_PATH_ADMIN_ROOT.'brand-logo' ?>">
								<i class="bi bi-badge-4k me-2"></i><?php $L->p('Brand Logo') ?>
							</a>
						</div>
					</div>
				</div>
			</li>				<!-- 4. 广告 -->
			<li class="nav-item">
				<a class="nav-link <?php echo getActiveClass('ads-settings') ?>" href="<?php echo HTML_PATH_ADMIN_ROOT.'ads-settings' ?>">
					<span class="nav-link-icon d-md-none d-lg-inline-block">
						<i class="bi bi-megaphone"></i>
					</span>
					<span class="nav-link-title"><?php $L->p('Advertising') ?></span>
				</a>
			</li>
			
			<!-- 5. 蜘蛛管理 dropdown -->
			<li class="nav-item dropdown <?php echo getActiveClass(['spider-logs', 'spider-settings']) ?>" data-nav-id="navbar-spider">
				<a class="nav-link dropdown-toggle" href="#navbar-spider" data-bs-toggle="dropdown" data-bs-auto-close="false" role="button" aria-expanded="<?php echo isCurrentRoute(['spider-logs', 'spider-settings']) ? 'true' : 'false' ?>">
					<span class="nav-link-icon d-md-none d-lg-inline-block">
						<i class="bi bi-bug"></i>
					</span>
					<span class="nav-link-title"><?php $L->p('Spider Management') ?></span>
				</a>
				<div class="dropdown-menu <?php echo getShowClass(['spider-logs', 'spider-settings']) ?>">
					<div class="dropdown-menu-columns">
						<div class="dropdown-menu-column">
							<a class="dropdown-item <?php echo getActiveClass('spider-logs') ?>" href="<?php echo HTML_PATH_ADMIN_ROOT.'spider-logs' ?>">
								<i class="bi bi-list-ul me-2"></i><?php $L->p('Spider Logs') ?>
							</a>
							<a class="dropdown-item <?php echo getActiveClass('spider-settings') ?>" href="<?php echo HTML_PATH_ADMIN_ROOT.'spider-settings' ?>">
								<i class="bi bi-gear me-2"></i><?php $L->p('Spider Settings') ?>
							</a>
						</div>
					</div>
				</div>
			</li>
			
			<!-- 6. 插件 -->
			<li class="nav-item">
				<a class="nav-link <?php echo getActiveClass('plugins') ?>" href="<?php echo HTML_PATH_ADMIN_ROOT.'plugins' ?>">
					<span class="nav-link-icon d-md-none d-lg-inline-block">
						<i class="bi bi-puzzle"></i>
					</span>
					<span class="nav-link-title"><?php $L->p('Plugins') ?></span>
				</a>
			</li>
			
			<!-- 7. 主题 -->
			<li class="nav-item">
				<a class="nav-link <?php echo getActiveClass('themes') ?>" href="<?php echo HTML_PATH_ADMIN_ROOT.'themes' ?>">
						<span class="nav-link-icon d-md-none d-lg-inline-block">
							<i class="bi bi-palette"></i>
						</span>
						<span class="nav-link-title"><?php $L->p('Themes') ?></span>
					</a>
				</li>
				
			</li>
			
			<!-- 8. 缓存 dropdown -->
			<li class="nav-item dropdown <?php echo getActiveClass(['cache-settings', 'cache-list']) ?>" data-nav-id="navbar-cache">
				<a class="nav-link dropdown-toggle" href="#navbar-cache" data-bs-toggle="dropdown" data-bs-auto-close="false" role="button" aria-expanded="<?php echo isCurrentRoute(['cache-settings', 'cache-list']) ? 'true' : 'false' ?>">
					<span class="nav-link-icon d-md-none d-lg-inline-block">
						<i class="bi bi-lightning"></i>
					</span>
					<span class="nav-link-title"><?php $L->p('Cache') ?></span>
				</a>
				<div class="dropdown-menu <?php echo getShowClass(['cache-settings', 'cache-list']) ?>">
					<div class="dropdown-menu-columns">
						<div class="dropdown-menu-column">
							<a class="dropdown-item <?php echo getActiveClass('cache-settings') ?>" href="<?php echo HTML_PATH_ADMIN_ROOT.'cache-settings' ?>">
								<i class="bi bi-gear me-2"></i><?php $L->p('Cache Settings') ?>
							</a>
							<a class="dropdown-item <?php echo getActiveClass('cache-list') ?>" href="<?php echo HTML_PATH_ADMIN_ROOT.'cache-list' ?>">
								<i class="bi bi-list-check me-2"></i><?php $L->p('Cache Overview') ?>
							</a>
						</div>
					</div>
				</div>
			</li>
			
			<!-- 9. 安全管理 dropdown -->
			<li class="nav-item dropdown <?php echo getActiveClass(['security-system', 'security-general', 'audit-logs', 'system-repair-upgrade']) ?>" data-nav-id="navbar-security">
				<a class="nav-link dropdown-toggle" href="#navbar-security" data-bs-toggle="dropdown" data-bs-auto-close="false" role="button" aria-expanded="<?php echo isCurrentRoute(['security-system', 'security-general', 'audit-logs', 'system-repair-upgrade']) ? 'true' : 'false' ?>">
					<span class="nav-link-icon d-md-none d-lg-inline-block">
						<i class="bi bi-shield-check"></i>
					</span>
					<span class="nav-link-title"><?php $L->p('Security Management') ?></span>
				</a>
				<div class="dropdown-menu <?php echo getShowClass(['security-system', 'security-general', 'audit-logs', 'system-repair-upgrade']) ?>">
					<div class="dropdown-menu-columns">
						<div class="dropdown-menu-column">
							<a class="dropdown-item <?php echo getActiveClass('security-system') ?>" href="<?php echo HTML_PATH_ADMIN_ROOT.'security-system' ?>">
								<i class="bi bi-cpu me-2"></i><?php $L->p('System Settings') ?>
							</a>
						<a class="dropdown-item <?php echo getActiveClass('security-general') ?>" href="<?php echo HTML_PATH_ADMIN_ROOT.'security-general' ?>">
							<i class="bi bi-sliders me-2"></i><?php $L->p('Security General Settings') ?>
						</a>
						<a class="dropdown-item <?php echo getActiveClass('audit-logs') ?>" href="<?php echo HTML_PATH_ADMIN_ROOT.'audit-logs' ?>">
							<i class="bi bi-journal-text me-2"></i><?php $L->p('Audit Logs') ?>
						</a>
						<a class="dropdown-item <?php echo getActiveClass('system-repair-upgrade') ?>" href="<?php echo HTML_PATH_ADMIN_ROOT.'system-repair-upgrade' ?>">
							<i class="bi bi-tools me-2"></i><?php $L->p('System Repair & Upgrade') ?>
						</a>
					</div>
				</div>
			</div>
		</li>
		
		<!-- 10. 授权管理 -->
		<li class="nav-item">
			<a class="nav-link <?php echo getActiveClass('authorization-settings') ?>" href="<?php echo HTML_PATH_ADMIN_ROOT.'authorization-settings' ?>">
				<span class="nav-link-icon d-md-none d-lg-inline-block">
					<i class="bi bi-key"></i>
				</span>
				<span class="nav-link-title"><?php $L->p('Authorization') ?></span>
			</a>
		</li>
		
		<!-- 11. 其他 dropdown -->
		<li class="nav-item dropdown <?php echo getActiveClass(['new-content', 'categories', 'users', 'edit-user', 'settings']) ?>" data-nav-id="navbar-others">
			<a class="nav-link dropdown-toggle" href="#navbar-others" data-bs-toggle="dropdown" data-bs-auto-close="false" role="button" aria-expanded="<?php echo isCurrentRoute(['new-content', 'categories', 'users', 'edit-user', 'settings']) ? 'true' : 'false' ?>">
			<span class="nav-link-icon d-md-none d-lg-inline-block">
				<i class="bi bi-three-dots"></i>
			</span>
			<span class="nav-link-title"><?php $L->p('Others') ?></span>
		</a>
		<div class="dropdown-menu <?php echo getShowClass(['new-content', 'categories', 'users', 'edit-user', 'settings']) ?>">
			<div class="dropdown-menu-columns">
				<div class="dropdown-menu-column">
					<a class="dropdown-item" target="_blank" href="<?php echo HTML_PATH_ROOT ?>">
						<i class="bi bi-globe me-2"></i><?php $L->p('Website') ?>
					</a>
					<a class="dropdown-item <?php echo getActiveClass('new-content') ?>" href="<?php echo HTML_PATH_ADMIN_ROOT.'new-content' ?>">
						<i class="bi bi-file-earmark-plus me-2"></i><?php $L->p('New content') ?>
					</a>
					<a class="dropdown-item <?php echo getActiveClass('categories') ?>" href="<?php echo HTML_PATH_ADMIN_ROOT.'categories' ?>">
						<i class="bi bi-folder me-2"></i><?php $L->p('Categories') ?>
					</a>
					<a class="dropdown-item <?php echo getActiveClass('users') ?>" href="<?php echo HTML_PATH_ADMIN_ROOT.'users' ?>">
						<i class="bi bi-people me-2"></i><?php $L->p('Users') ?>
					</a>
					<a class="dropdown-item <?php echo getActiveClass('edit-user') ?>" href="<?php echo HTML_PATH_ADMIN_ROOT.'edit-user/'.$login->username() ?>">
						<i class="bi bi-person me-2"></i><?php $L->p('Profile') ?>
					</a>
					<a class="dropdown-item <?php echo getActiveClass('settings') ?>" href="<?php echo HTML_PATH_ADMIN_ROOT.'settings' ?>">
						<i class="bi bi-sliders me-2"></i><?php $L->p('General') ?>
					</a>
				</div>
			</div>
		</div>
	</li>
	
	<!-- 12. 关于 MAIGEWAN -->
	<li class="nav-item">
		<a class="nav-link <?php echo getActiveClass('about-maigewan') ?>" href="<?php echo HTML_PATH_ADMIN_ROOT.'about-maigewan' ?>">
			<span class="nav-link-icon d-md-none d-lg-inline-block">
				<i class="bi bi-info-circle"></i>
			</span>
			<span class="nav-link-title"><?php $L->p('About MAIGEWAN') ?></span>
		</a>
	</li>				<?php endif; ?>
				
				<?php if (checkRole(array('admin', 'editor'),false)): ?>
					<?php
						if (!empty($plugins['adminSidebar'])) {
							echo '<li class="nav-item"><hr class="navbar-divider my-3"></li>';
							foreach ($plugins['adminSidebar'] as $pluginSidebar) {
								echo '<li class="nav-item">';
								echo $pluginSidebar->adminSidebar();
								echo '</li>';
							}
						}
					?>
				<?php endif; ?>
			</ul>
			<!-- END NAVBAR MENU -->
			
			<!-- Bottom Navigation (Theme Settings & Logout) -->
			<div class="navbar-nav d-none d-lg-flex mt-auto">
				<!-- Theme Settings Button -->
				<div class="nav-item">
					<a class="nav-link" href="#" data-bs-toggle="offcanvas" data-bs-target="#offcanvasTheme" aria-controls="offcanvasTheme">
						<span class="nav-link-icon d-md-none d-lg-inline-block">
							<i class="bi bi-brush"></i>
						</span>
						<span class="nav-link-title"><?php $L->p('Theme Settings') ?></span>
					</a>
				</div>
				
				<!-- Logout Link -->
				<div class="nav-item">
					<a class="nav-link" href="<?php echo HTML_PATH_ADMIN_ROOT.'logout' ?>">
						<span class="nav-link-icon d-md-none d-lg-inline-block">
							<i class="bi bi-box-arrow-right"></i>
						</span>
						<span class="nav-link-title"><?php $L->p('Logout') ?></span>
					</a>
				</div>
			</div>
		</div>
	</div>
</aside>
