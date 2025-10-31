<aside class="navbar navbar-vertical navbar-expand-lg navbar-dark">
	<div class="container-fluid">
		<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#sidebar-menu" aria-controls="sidebar-menu" aria-expanded="false" aria-label="Toggle navigation">
			<span class="navbar-toggler-icon"></span>
		</button>
		
		<h1 class="navbar-brand navbar-brand-autodark">
			<a href="<?php echo HTML_PATH_ADMIN_ROOT.'dashboard' ?>">
				<img src="<?php echo HTML_PATH_CORE_IMG ?>logo.svg" width="110" height="32" alt="Bludit" class="navbar-brand-image">
				<span class="ms-2"><?php echo (defined('BLUDIT_PRO'))?'BLUDIT PRO':'BLUDIT' ?></span>
			</a>
		</h1>
		
		<div class="navbar-nav flex-row d-lg-none">
			<div class="nav-item dropdown">
				<a href="#" class="nav-link d-flex lh-1 text-reset p-0" data-bs-toggle="dropdown" aria-label="Open user menu">
					<span class="avatar avatar-sm" style="background-image: url(<?php echo HTML_PATH_CORE_IMG ?>favicon.png)"></span>
				</a>
				<div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
					<a href="<?php echo HTML_PATH_ADMIN_ROOT.'logout' ?>" class="dropdown-item"><?php $L->p('Logout') ?></a>
				</div>
			</div>
		</div>
		
		<div class="collapse navbar-collapse" id="sidebar-menu">
			<ul class="navbar-nav pt-lg-3">
				<!-- Dashboard -->
				<li class="nav-item">
					<a class="nav-link" href="<?php echo HTML_PATH_ADMIN_ROOT.'dashboard' ?>">
						<span class="nav-link-icon d-md-none d-lg-inline-block">
							<i class="bi bi-speedometer2"></i>
						</span>
						<span class="nav-link-title"><?php $L->p('Dashboard') ?></span>
					</a>
				</li>
				
				<!-- Website -->
				<li class="nav-item">
					<a class="nav-link" target="_blank" href="<?php echo HTML_PATH_ROOT ?>">
						<span class="nav-link-icon d-md-none d-lg-inline-block">
							<i class="bi bi-house-door"></i>
						</span>
						<span class="nav-link-title"><?php $L->p('Website') ?></span>
					</a>
				</li>
				
				<!-- New content -->
				<li class="nav-item">
					<a class="nav-link" href="<?php echo HTML_PATH_ADMIN_ROOT.'new-content' ?>">
						<span class="nav-link-icon d-md-none d-lg-inline-block">
							<i class="bi bi-plus-circle-fill text-primary"></i>
						</span>
						<span class="nav-link-title"><?php $L->p('New content') ?></span>
					</a>
				</li>
				
				<?php if (!checkRole(array('admin'),false)): ?>
				<!-- Content -->
				<li class="nav-item">
					<a class="nav-link" href="<?php echo HTML_PATH_ADMIN_ROOT.'content' ?>">
						<span class="nav-link-icon d-md-none d-lg-inline-block">
							<i class="bi bi-archive"></i>
						</span>
						<span class="nav-link-title"><?php $L->p('Content') ?></span>
					</a>
				</li>
				
				<!-- Profile -->
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
				
				<!-- Manage Section -->
				<li class="nav-item dropdown">
					<a class="nav-link dropdown-toggle" href="#navbar-manage" data-bs-toggle="dropdown" data-bs-auto-close="false" role="button" aria-expanded="false">
						<span class="nav-link-icon d-md-none d-lg-inline-block">
							<i class="bi bi-folder"></i>
						</span>
						<span class="nav-link-title"><?php $L->p('Manage') ?></span>
					</a>
					<div class="dropdown-menu">
						<div class="dropdown-menu-columns">
							<div class="dropdown-menu-column">
								<a class="dropdown-item" href="<?php echo HTML_PATH_ADMIN_ROOT.'content' ?>">
									<i class="bi bi-folder me-2"></i><?php $L->p('Content') ?>
								</a>
								<a class="dropdown-item" href="<?php echo HTML_PATH_ADMIN_ROOT.'categories' ?>">
									<i class="bi bi-bookmark me-2"></i><?php $L->p('Categories') ?>
								</a>
								<a class="dropdown-item" href="<?php echo HTML_PATH_ADMIN_ROOT.'users' ?>">
									<i class="bi bi-people me-2"></i><?php $L->p('Users') ?>
								</a>
							</div>
						</div>
					</div>
				</li>
				
				<!-- Settings Section -->
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
									<i class="bi bi-gear me-2"></i><?php $L->p('General') ?>
								</a>
								<a class="dropdown-item" href="<?php echo HTML_PATH_ADMIN_ROOT.'plugins' ?>">
									<i class="bi bi-puzzle me-2"></i><?php $L->p('Plugins') ?>
								</a>
								<a class="dropdown-item" href="<?php echo HTML_PATH_ADMIN_ROOT.'themes' ?>">
									<i class="bi bi-palette me-2"></i><?php $L->p('Themes') ?>
								</a>
								<a class="dropdown-item" href="<?php echo HTML_PATH_ADMIN_ROOT.'about' ?>">
									<i class="bi bi-info-circle me-2"></i><?php $L->p('About') ?>
								</a>
							</div>
						</div>
					</div>
				</li>
				
				<?php endif; ?>
				
				<?php if (checkRole(array('admin', 'editor'),false)): ?>
					<?php
						if (!empty($plugins['adminSidebar'])) {
							echo '<li class="nav-item"><hr class="navbar-divider"></li>';
							foreach ($plugins['adminSidebar'] as $pluginSidebar) {
								echo '<li class="nav-item">';
								echo $pluginSidebar->adminSidebar();
								echo '</li>';
							}
						}
					?>
				<?php endif; ?>
				
				<!-- Logout -->
				<li class="nav-item mt-auto">
					<a class="nav-link" href="<?php echo HTML_PATH_ADMIN_ROOT.'logout' ?>">
						<span class="nav-link-icon d-md-none d-lg-inline-block">
							<i class="bi bi-box-arrow-right"></i>
						</span>
						<span class="nav-link-title"><?php $L->p('Logout') ?></span>
					</a>
				</li>
			</ul>
		</div>
	</div>
</aside>
