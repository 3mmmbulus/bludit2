<aside class="navbar navbar-vertical navbar-expand-lg" data-bs-theme="dark">
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
		
		<!-- Mobile User Menu -->
		<div class="navbar-nav flex-row d-lg-none">
			<div class="nav-item dropdown">
				<a href="#" class="nav-link d-flex lh-1 p-0 px-2" data-bs-toggle="dropdown" aria-label="Open user menu">
					<span class="avatar avatar-sm" style="background-image: url(<?php echo HTML_PATH_CORE_IMG ?>favicon.png)"></span>
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
		
		<div class="collapse navbar-collapse" id="sidebar-menu">
			<!-- BEGIN NAVBAR MENU -->
			<ul class="navbar-nav pt-lg-3">
				<!-- Dashboard / Home -->
				<li class="nav-item">
					<a class="nav-link" href="<?php echo HTML_PATH_ADMIN_ROOT.'dashboard' ?>">
						<span class="nav-link-icon d-md-none d-lg-inline-block">
							<i class="bi bi-house"></i>
						</span>
						<span class="nav-link-title"><?php $L->p('Dashboard') ?></span>
					</a>
				</li>
				
				<!-- Website -->
				<li class="nav-item">
					<a class="nav-link" target="_blank" href="<?php echo HTML_PATH_ROOT ?>">
						<span class="nav-link-icon d-md-none d-lg-inline-block">
							<i class="bi bi-globe"></i>
						</span>
						<span class="nav-link-title"><?php $L->p('Website') ?></span>
					</a>
				</li>
				
				<!-- New content -->
				<li class="nav-item">
					<a class="nav-link" href="<?php echo HTML_PATH_ADMIN_ROOT.'new-content' ?>">
						<span class="nav-link-icon d-md-none d-lg-inline-block">
							<i class="bi bi-plus-circle"></i>
						</span>
						<span class="nav-link-title"><?php $L->p('New content') ?></span>
					</a>
				</li>
				
				<?php if (!checkRole(array('admin'),false)): ?>
				<!-- Content (Non-admin users) -->
				<li class="nav-item">
					<a class="nav-link" href="<?php echo HTML_PATH_ADMIN_ROOT.'content' ?>">
						<span class="nav-link-icon d-md-none d-lg-inline-block">
							<i class="bi bi-file-earmark-text"></i>
						</span>
						<span class="nav-link-title"><?php $L->p('Content') ?></span>
					</a>
				</li>
				
				<!-- Profile (Non-admin users) -->
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
				
				<!-- Content Management Dropdown -->
				<li class="nav-item dropdown">
					<a class="nav-link dropdown-toggle" href="#navbar-content" data-bs-toggle="dropdown" data-bs-auto-close="false" role="button" aria-expanded="false">
						<span class="nav-link-icon d-md-none d-lg-inline-block">
							<i class="bi bi-folder"></i>
						</span>
						<span class="nav-link-title"><?php $L->p('Content') ?></span>
					</a>
					<div class="dropdown-menu">
						<div class="dropdown-menu-columns">
							<div class="dropdown-menu-column">
								<a class="dropdown-item" href="<?php echo HTML_PATH_ADMIN_ROOT.'content' ?>">
									<i class="bi bi-file-earmark-text me-2"></i><?php $L->p('All content') ?>
								</a>
								<a class="dropdown-item" href="<?php echo HTML_PATH_ADMIN_ROOT.'categories' ?>">
									<i class="bi bi-bookmark me-2"></i><?php $L->p('Categories') ?>
								</a>
								<a class="dropdown-item" href="<?php echo HTML_PATH_ADMIN_ROOT.'new-content' ?>">
									<i class="bi bi-plus-circle me-2"></i><?php $L->p('New content') ?>
								</a>
							</div>
						</div>
					</div>
				</li>
				
				<!-- Users Management -->
				<li class="nav-item">
					<a class="nav-link" href="<?php echo HTML_PATH_ADMIN_ROOT.'users' ?>">
						<span class="nav-link-icon d-md-none d-lg-inline-block">
							<i class="bi bi-people"></i>
						</span>
						<span class="nav-link-title"><?php $L->p('Users') ?></span>
					</a>
				</li>
				
				<!-- Settings & Extensions Dropdown -->
				<li class="nav-item dropdown">
					<a class="nav-link dropdown-toggle" href="#navbar-settings" data-bs-toggle="dropdown" data-bs-auto-close="false" role="button" aria-expanded="false">
						<span class="nav-link-icon d-md-none d-lg-inline-block">
							<i class="bi bi-gear"></i>
						</span>
						<span class="nav-link-title"><?php $L->p('Settings') ?></span>
					</a>
					<div class="dropdown-menu">
						<div class="dropdown-menu-columns">
							<div class="dropdown-menu-column">
								<a class="dropdown-item" href="<?php echo HTML_PATH_ADMIN_ROOT.'settings' ?>">
									<i class="bi bi-sliders me-2"></i><?php $L->p('General') ?>
								</a>
								<a class="dropdown-item" href="<?php echo HTML_PATH_ADMIN_ROOT.'plugins' ?>">
									<i class="bi bi-puzzle me-2"></i><?php $L->p('Plugins') ?>
								</a>
								<a class="dropdown-item" href="<?php echo HTML_PATH_ADMIN_ROOT.'themes' ?>">
									<i class="bi bi-palette me-2"></i><?php $L->p('Themes') ?>
								</a>
							</div>
						</div>
					</div>
				</li>
				
				<!-- About & Help Dropdown -->
				<li class="nav-item dropdown">
					<a class="nav-link dropdown-toggle" href="#navbar-help" data-bs-toggle="dropdown" data-bs-auto-close="false" role="button" aria-expanded="false">
						<span class="nav-link-icon d-md-none d-lg-inline-block">
							<i class="bi bi-question-circle"></i>
						</span>
						<span class="nav-link-title"><?php $L->p('Help') ?></span>
					</a>
					<div class="dropdown-menu">
						<a class="dropdown-item" href="<?php echo HTML_PATH_ADMIN_ROOT.'about' ?>">
							<i class="bi bi-info-circle me-2"></i><?php $L->p('About') ?>
						</a>
						<a class="dropdown-item" href="https://docs.bludit.com" target="_blank" rel="noopener">
							<i class="bi bi-book me-2"></i><?php $L->p('Documentation') ?>
						</a>
						<div class="dropdown-divider"></div>
						<a class="dropdown-item" href="https://github.com/bludit/bludit" target="_blank" rel="noopener">
							<i class="bi bi-github me-2"></i>Source code
						</a>
					</div>
				</li>
				
				<?php endif; ?>
				
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
			
			<!-- Desktop User Menu at Bottom -->
			<div class="navbar-nav d-none d-lg-flex mt-auto">
				<div class="nav-item dropdown">
					<a href="#" class="nav-link d-flex lh-1 p-0 px-2" data-bs-toggle="dropdown" aria-label="Open user menu">
						<span class="avatar avatar-sm" style="background-image: url(<?php echo HTML_PATH_CORE_IMG ?>favicon.png)"></span>
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
	</div>
</aside>
