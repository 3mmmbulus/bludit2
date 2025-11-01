<?php defined('BLUDIT') or die('Bludit CMS.'); ?>

<!-- Footer -->
<div class="text-center text-secondary mt-3">
	<ul class="list-inline list-inline-dots mb-0">
		<li class="list-inline-item">
			<a href="https://maigewan.com" target="_blank" rel="noopener noreferrer" class="link-secondary">
				© <?php echo date('Y') ?> Maigewan
			</a>
		</li>
		<li class="list-inline-item">
			<a href="https://maigewan.com/about" target="_blank" rel="noopener noreferrer" class="link-secondary">
				<?php echo isset($L) ? $L->get('About') : 'About' ?>
			</a>
		</li>
		<li class="list-inline-item">
			<a href="https://maigewan.com/docs" target="_blank" rel="noopener noreferrer" class="link-secondary">
				<?php echo isset($L) ? $L->get('Documentation') : 'Documentation' ?>
			</a>
		</li>
	</ul>
</div>
