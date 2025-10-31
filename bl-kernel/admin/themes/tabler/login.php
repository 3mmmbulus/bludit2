<!doctype html>
<html lang="<?php echo Theme::lang() ?>">
<head>
	<meta charset="<?php echo CHARSET ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover"/>
	<meta http-equiv="X-UA-Compatible" content="ie=edge"/>
	<meta name="robots" content="noindex,nofollow">
	<title><?php echo $site->title() ?> - Login</title>

	<!-- Favicon -->
	<link rel="shortcut icon" type="image/x-icon" href="<?php echo HTML_PATH_CORE_IMG.'favicon.png?version='.BLUDIT_VERSION ?>">

	<!-- CSS files -->
	<?php
		echo Theme::cssTabler();
		echo Theme::css(array('bludit-tabler.css'), DOMAIN_ADMIN_THEME_CSS);
	?>

	<!-- Javascript -->
	<?php
		echo Theme::jquery();
		echo Theme::jsTabler();
	?>

	<!-- Plugins -->
	<?php Theme::plugins('loginHead') ?>
</head>
<body class="d-flex flex-column">
	
	<!-- Global theme script -->
	<script src="<?php echo DOMAIN_ADMIN_THEME ?>js/tabler-theme.min.js?version=<?php echo BLUDIT_VERSION ?>"></script>

	<!-- Plugins -->
	<?php Theme::plugins('loginBodyBegin') ?>

	<div class="page page-center">
		<div class="container container-tight py-4">
			
			<!-- Brand Logo -->
			<div class="text-center mb-4">
				<a href="<?php echo DOMAIN ?>" aria-label="<?php echo $site->title() ?>" class="navbar-brand navbar-brand-autodark">
					<?php 
					$logoPath = HTML_PATH_CORE_IMG . 'logo.svg';
					if (file_exists(PATH_UPLOADS . 'logo.svg')) {
						$logoPath = DOMAIN_UPLOADS . 'logo.svg';
					} elseif (file_exists(PATH_UPLOADS . 'logo.png')) {
						$logoPath = DOMAIN_UPLOADS . 'logo.png';
					}
					?>
					<img src="<?php echo $logoPath ?>" height="36" alt="<?php echo $site->title() ?>" class="navbar-brand-image">
				</a>
			</div>

			<!-- Login Card -->
			<div class="card card-md">
				<div class="card-body">
					<!-- Alert -->
					<?php include('html/alert.php'); ?>
					
					<?php
						if (Sanitize::pathFile(PATH_ADMIN_VIEWS, $layout['view'].'.php')) {
							include(PATH_ADMIN_VIEWS.$layout['view'].'.php');
						}
					?>
				</div>
			</div>

			<!-- Footer -->
			<div class="text-center text-secondary mt-3">
				<?php echo $L->g('Powered by Bludit') ?> <?php echo (defined('BLUDIT_PRO')) ? 'PRO' : '' ?> v<?php echo BLUDIT_VERSION ?>
			</div>
		</div>
	</div>

	<!-- Plugins -->
	<?php Theme::plugins('loginBodyEnd') ?>

</body>
</html>
