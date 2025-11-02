<?php defined('BLUDIT') or die('Bludit CMS.'); ?>

<link rel="stylesheet" href="<?php echo DOMAIN_CORE_CSS ?>authorization-settings.css">

<div class="container-xl">
	<div class="page-header d-print-none">
		<div class="row align-items-center">
			<div class="col">
				<h2 class="page-title"><?php echo $pageL->get('title') ?></h2>
				<div class="text-secondary mt-1"><?php echo $pageL->get('subtitle') ?></div>
			</div>
		</div>
	</div>
	
	<div class="page-body">
		<div class="authorization-settings">
			
			<?php if (!empty($message)): ?>
			<div class="alert alert-<?php echo $messageType ?> alert-dismissible" role="alert">
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
			
			<form method="POST" action="" autocomplete="off">
				
				<!-- Server IP Card -->
				<div class="card mb-3">
					<div class="card-header">
						<h3 class="card-title">
							<i class="bi bi-server me-2"></i>
							<?php echo $pageL->get('card_title_server') ?>
						</h3>
					</div>
					<div class="card-body">
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
					</div>
				</div>
				
				<!-- Credentials Card -->
				<div class="card mb-3">
					<div class="card-header">
						<h3 class="card-title">
							<i class="bi bi-person-lock me-2"></i>
							<?php echo $pageL->get('card_title_credentials') ?>
						</h3>
					</div>
					<div class="card-body">
						<small class="text-secondary d-block mb-3"><?php echo $pageL->get('hint_credentials') ?></small>
						
						<div class="mb-3">
							<label class="form-label"><?php echo $pageL->get('label_username') ?></label>
							<input type="text" class="form-control" name="username" placeholder="<?php echo $pageL->get('placeholder_username') ?>" required autocomplete="off">
						</div>
						
						<div class="mb-3">
							<label class="form-label"><?php echo $pageL->get('label_password') ?></label>
							<div class="input-group input-group-flat">
								<input type="password" class="form-control" name="password" id="password-input" placeholder="<?php echo $pageL->get('placeholder_password') ?>" required autocomplete="new-password">
								<span class="input-group-text">
									<a href="#" class="link-secondary" id="toggle-password" data-bs-toggle="tooltip" title="显示密码">
										<i class="bi bi-eye"></i>
									</a>
								</span>
							</div>
						</div>
					</div>
				</div>
				
				<!-- License Code Card -->
				<div class="card mb-3">
					<div class="card-header">
						<h3 class="card-title">
							<i class="bi bi-key me-2"></i>
							<?php echo $pageL->get('card_title_license') ?>
						</h3>
					</div>
					<div class="card-body">
						<div class="mb-3">
							<label class="form-label"><?php echo $pageL->get('label_license_code') ?></label>
							<input type="text" class="form-control" name="license_code" placeholder="<?php echo $pageL->get('placeholder_license') ?>" required autocomplete="off">
							<small class="form-hint"><?php echo $pageL->get('hint_license') ?></small>
						</div>
					</div>
				</div>
				
				<!-- Submit Button -->
				<div class="card">
					<div class="card-body">
						<div class="form-footer">
							<button type="submit" class="btn btn-primary w-100">
								<i class="bi bi-shield-check me-2"></i>
								<?php echo $pageL->get('btn_submit') ?>
							</button>
						</div>
					</div>
				</div>
				
			</form>
			
		</div>
	</div>
</div>

<script src="<?php echo DOMAIN_CORE_JS ?>authorization-settings.js"></script>
