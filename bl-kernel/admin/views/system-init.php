<?php defined('BLUDIT') or die('Bludit CMS.'); ?>

<!-- Title -->
<h2 class="card-title text-center mb-4"><?php echo $pageL->get('title') ?></h2>

<!-- Subtitle -->
<?php if ($pageL->get('subtitle')): ?>
<p class="text-center text-muted mb-4"><?php echo $pageL->get('subtitle') ?></p>
<?php endif ?>

<!-- Alert Messages -->
<?php echo Alert::get() ?>

<!-- Form -->
<?php echo Bootstrap::formOpen(array('id' => 'jsformInit', 'autocomplete' => 'off')) ?>

	<!-- Username Input -->
	<div class="mb-3">
		<label class="form-label"><?php echo $pageL->get('username') ?></label>
		<input 
			type="text" 
			name="username" 
			class="form-control" 
			placeholder="<?php echo $pageL->get('username_placeholder') ?>"
			value="<?php echo isset($_POST['username']) ? Sanitize::html($_POST['username']) : '' ?>"
			autocomplete="off"
			autofocus
			required
		/>
	</div>
	
	<!-- Password Input -->
	<div class="mb-3">
		<label class="form-label"><?php echo $pageL->get('password') ?></label>
		<div class="input-group input-group-flat">
			<input 
				type="password" 
				name="password" 
				id="jspassword"
				class="form-control" 
				placeholder="<?php echo $pageL->get('password_placeholder') ?>"
				autocomplete="new-password"
				required
			/>
			<span class="input-group-text">
				<a href="#" class="link-secondary" title="<?php echo $pageL->get('show_password') ?>" id="jstogglePassword" data-bs-toggle="tooltip">
					<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon">
						<path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" />
						<path d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6" />
					</svg>
				</a>
			</span>
		</div>
	</div>
	
	<!-- Confirm Password Input -->
	<div class="mb-3">
		<label class="form-label"><?php echo $pageL->get('confirm_password') ?></label>
		<div class="input-group input-group-flat">
			<input 
				type="password" 
				name="confirm_password" 
				id="jsconfirmPassword"
				class="form-control" 
				placeholder="<?php echo $pageL->get('confirm_password_placeholder') ?>"
				autocomplete="new-password"
				required
			/>
			<span class="input-group-text">
				<a href="#" class="link-secondary" title="<?php echo $pageL->get('show_password') ?>" id="jstoggleConfirmPassword" data-bs-toggle="tooltip">
					<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon">
						<path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" />
						<path d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6" />
					</svg>
				</a>
			</span>
		</div>
	</div>
	
	<!-- Submit Button -->
	<div class="form-footer">
		<button type="submit" class="btn btn-primary w-100" id="jsbtnSubmit">
			<?php echo $pageL->get('btn_initialize') ?>
		</button>
	</div>

<?php echo Bootstrap::formClose() ?>

<!-- JavaScript i18n -->
<script>
	// Page translations for JavaScript
	var pageL_show_password = '<?php echo $pageL->get('show_password') ?>';
	var pageL_hide_password = '<?php echo $pageL->get('hide_password') ?>';
	var pageL_username_required = '<?php echo $pageL->get('username_required') ?>';
	var pageL_username_invalid = '<?php echo $pageL->get('username_invalid') ?>';
	var pageL_username_length = '<?php echo $pageL->get('username_length') ?>';
	var pageL_password_required = '<?php echo $pageL->get('password_required') ?>';
	var pageL_password_too_short = '<?php echo $pageL->get('password_too_short') ?>';
	var pageL_password_weak = '<?php echo $pageL->get('password_weak') ?>';
	var pageL_password_mismatch = '<?php echo $pageL->get('password_mismatch') ?>';
</script>
