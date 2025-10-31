<header class="navbar navbar-expand-md navbar-light d-lg-none d-print-none" style="z-index: 1030;">
	<div class="container-xl">
		<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#sidebar-menu" aria-controls="sidebar-menu" aria-expanded="false" aria-label="Toggle navigation">
			<span class="navbar-toggler-icon"></span>
		</button>
		<h1 class="navbar-brand navbar-brand-autodark d-none-navbar-horizontal pe-0 pe-md-3">
			<a href="<?php echo HTML_PATH_ADMIN_ROOT ?>">
				<img src="<?php echo HTML_PATH_CORE_IMG ?>logo.svg" width="110" height="32" alt="Bludit" class="navbar-brand-image">
			</a>
		</h1>
		<div class="navbar-nav flex-row order-md-last">
			<div class="nav-item dropdown">
				<a href="#" class="nav-link d-flex lh-1 text-reset p-0" data-bs-toggle="dropdown" aria-label="Open user menu">
					<span class="avatar avatar-sm" style="background-image: url(<?php echo HTML_PATH_CORE_IMG ?>favicon.png)"></span>
					<div class="d-none d-xl-block ps-2">
						<div><?php echo $login->username() ?></div>
						<div class="mt-1 small text-muted"><?php echo $login->role() ?></div>
					</div>
				</a>
				<div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
					<a href="<?php echo HTML_PATH_ADMIN_ROOT.'edit-user/'.$login->username() ?>" class="dropdown-item"><?php $L->p('Profile') ?></a>
					<div class="dropdown-divider"></div>
					<a href="<?php echo HTML_PATH_ADMIN_ROOT.'logout' ?>" class="dropdown-item"><?php $L->p('Logout') ?></a>
				</div>
			</div>
		</div>
	</div>
</header>
