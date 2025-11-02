<?php defined('BLUDIT') or die('Bludit CMS.'); ?>

<link rel="stylesheet" href="<?php echo DOMAIN_CORE_CSS ?>authorization-settings.css">

<div class="page page-center">
	<div class="container container-tight py-4">
		
		<?php if (!empty($message)): ?>
		<div class="alert alert-<?php echo $messageType ?> alert-dismissible mb-3" role="alert">
			<div class="d-flex">
				<div>
					<?php if ($messageType === 'success'): ?>
					<i class="bi bi-check-circle me-2"></i>
					<?php else: ?>
					<i class="bi bi-exclamation-triangle me-2"></i>
					<?php endif; ?>
				</div>
				<div>
					<?php echo $message ?>
				</div>
			</div>
			<a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
		</div>
		<?php endif; ?>
		
		<?php if ($licenseExists && $licenseData): ?>
		<div class="alert alert-success mb-3" role="alert">
			<div class="d-flex">
				<div>
					<i class="bi bi-shield-check me-2"></i>
				</div>
				<div>
					<h4 class="alert-title">授权已激活</h4>
					<div class="text-secondary">
						<strong>用户名:</strong> <?php echo htmlspecialchars($licenseData['username'] ?? '') ?><br>
						<strong>授权时间:</strong> <?php echo htmlspecialchars($licenseData['authorized_at'] ?? '') ?><br>
						<strong>到期时间:</strong> <?php echo htmlspecialchars($licenseData['expires_at'] ?? '') ?><br>
						<strong>状态:</strong> <span class="badge bg-success"><?php echo htmlspecialchars($licenseData['status'] ?? '') ?></span>
					</div>
				</div>
			</div>
		</div>
		<?php endif; ?>
		
		<form class="card card-md" method="POST" action="" autocomplete="off" novalidate>
			<div class="card-body">
				<h2 class="card-title text-center mb-4"><?php echo $pageL->get('title') ?></h2>
				
				<!-- Server IP -->
				<div class="mb-3">
					<label class="form-label"><?php echo $pageL->get('label_server_ip') ?></label>
					<div class="input-group">
						<input type="text" class="form-control" id="server-ip" value="<?php echo htmlspecialchars($serverIP) ?>" readonly>
						<button class="btn btn-icon" type="button" id="refresh-ip" title="<?php echo $pageL->get('btn_refresh_ip') ?>">
							<i class="bi bi-arrow-clockwise"></i>
						</button>
					</div>
					<small class="form-hint"><?php echo $pageL->get('hint_server_ip') ?></small>
				</div>
				
				<!-- Username -->
				<div class="mb-3">
					<label class="form-label"><?php echo $pageL->get('label_username') ?></label>
					<input type="text" class="form-control" name="username" placeholder="<?php echo $pageL->get('placeholder_username') ?>" required autocomplete="off">
				</div>
				
				<!-- License Code -->
				<div class="mb-3">
					<label class="form-label"><?php echo $pageL->get('label_license_code') ?></label>
					<input type="text" class="form-control" name="license_code" placeholder="<?php echo $pageL->get('placeholder_license') ?>" required autocomplete="off">
					<small class="form-hint"><?php echo $pageL->get('hint_license') ?></small>
				</div>
				
				<!-- Terms Agreement -->
				<div class="mb-3">
					<label class="form-check">
						<input type="checkbox" class="form-check-input" id="terms-agree" required>
						<span class="form-check-label"><?php echo $pageL->get('label_terms') ?></span>
					</label>
				</div>
				
				<!-- Submit Button -->
				<div class="form-footer">
					<button type="submit" class="btn btn-primary w-100">
						<i class="bi bi-shield-check me-2"></i>
						<?php echo $pageL->get('btn_submit') ?>
					</button>
				</div>
			</div>
		</form>
		
	</div>
</div>

<script src="<?php echo DOMAIN_CORE_JS ?>authorization-settings.js"></script>
