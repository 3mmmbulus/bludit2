<?php defined('BLUDIT') or die('Bludit CMS.');

echo '<h2 class="h2 text-center mb-4">' . $L->g('Login to your account') . '</h2>';

echo Bootstrap::formOpen(array());

echo Bootstrap::formInputHidden(array(
	'name' => 'tokenCSRF',
	'value' => $security->getTokenCSRF()
));

echo '
	<div class="mb-3">
		<label class="form-label">' . $L->g('Username') . '</label>
		<input type="text" dir="auto" value="' . (isset($_POST['username']) ? Sanitize::html($_POST['username']) : '') . '" class="form-control" id="jsusername" name="username" placeholder="' . $L->g('Enter your username') . '" autocomplete="username" autofocus>
	</div>
	';

echo '
	<div class="mb-2">
		<label class="form-label">' . $L->g('Password') . '</label>
		<div class="input-group input-group-flat">
			<input type="password" class="form-control" id="jspassword" name="password" placeholder="' . $L->g('Your password') . '" autocomplete="current-password">
			<span class="input-group-text">
				<a href="#" class="link-secondary" title="' . $L->g('Show password') . '" data-bs-toggle="tooltip" onclick="togglePassword(); return false;">
					<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon">
						<path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" />
						<path d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6" />
					</svg>
				</a>
			</span>
		</div>
	</div>
	';

echo '
	<div class="mb-2">
		<label class="form-check">
			<input class="form-check-input" type="checkbox" value="true" id="jsremember" name="remember">
			<span class="form-check-label">' . $L->g('Remember me on this device') . '</span>
		</label>
	</div>
	';

echo '
	<div class="form-footer">
		<button type="submit" class="btn btn-primary w-100" name="save">' . $L->g('Sign in') . '</button>
	</div>
	';

echo '</form>';

?>

<script>
function togglePassword() {
	var passwordField = document.getElementById('jspassword');
	if (passwordField.type === 'password') {
		passwordField.type = 'text';
	} else {
		passwordField.type = 'password';
	}
}
</script>
