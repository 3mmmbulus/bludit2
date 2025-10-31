<!doctype html>
<html lang="<?php echo Theme::lang() ?>">
<head>
	<meta charset="<?php echo CHARSET ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover"/>
	<meta http-equiv="X-UA-Compatible" content="ie=edge"/>
	<meta name="robots" content="noindex,nofollow">
	<title>Bludit - Login</title>

	<!-- Favicon -->
	<link rel="shortcut icon" type="image/x-icon" href="<?php echo HTML_PATH_CORE_IMG.'favicon.png?version='.BLUDIT_VERSION ?>">

	<!-- CSS files -->
	<?php
		echo Theme::cssTabler();
		echo Theme::cssBootstrapIcons();
		echo Theme::css(array('bludit-tabler.css'), DOMAIN_ADMIN_THEME_CSS);
	?>

	<!-- Javascript -->
	<?php
		echo Theme::jquery();
		echo Theme::jsTabler();
	?>

	<!-- Plugins -->
	<?php Theme::plugins('loginHead') ?>

	<style>
		.login-page {
			display: flex;
			flex-direction: column;
			min-height: 100vh;
			justify-content: center;
			align-items: center;
			background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
		}
		
		.login-card {
			max-width: 400px;
			width: 100%;
		}
		
		.login-brand {
			margin-bottom: 2rem;
			text-align: center;
		}
		
		.login-brand img {
			height: 40px;
			margin-bottom: 1rem;
		}
		
		.login-brand h1 {
			color: #fff;
			font-weight: 600;
			margin: 0;
		}
	</style>
</head>
<body class="d-flex flex-column">

<!-- Plugins -->
<?php Theme::plugins('loginBodyBegin') ?>

<!-- Alert -->
<?php include('html/alert.php'); ?>

<div class="login-page">
	<div class="login-brand">
		<img src="<?php echo HTML_PATH_CORE_IMG ?>logo.svg" alt="Bludit">
		<h1><?php echo (defined('BLUDIT_PRO'))?'BLUDIT PRO':'BLUDIT' ?></h1>
	</div>
	
	<div class="login-card">
		<div class="card card-md">
			<div class="card-body">
				<?php
					if (Sanitize::pathFile(PATH_ADMIN_VIEWS, $layout['view'].'.php')) {
						include(PATH_ADMIN_VIEWS.$layout['view'].'.php');
					}
				?>
			</div>
		</div>
	</div>
	
	<div class="text-center text-white mt-3" style="opacity: 0.8;">
		<small>Version <?php echo BLUDIT_VERSION ?></small>
	</div>
</div>

<!-- Plugins -->
<?php Theme::plugins('loginBodyEnd') ?>

</body>
</html>
