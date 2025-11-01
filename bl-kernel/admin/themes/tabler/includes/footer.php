<?php defined('BLUDIT') or die('Bludit CMS.'); ?>

<!-- Footer -->
<footer class="footer footer-transparent d-print-none">
	<div class="container-xl">
		<div class="row text-center align-items-center flex-row-reverse">
			<!-- Copyright Info -->
			<div class="col-lg-auto ms-lg-auto">
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
			<!-- Powered By -->
			<div class="col-12 col-lg-auto mt-3 mt-lg-0">
				<ul class="list-inline list-inline-dots mb-0">
					<li class="list-inline-item">
						Powered by <a href="https://bludit.com" target="_blank" rel="noopener noreferrer" class="link-secondary">Bludit</a>
					</li>
					<li class="list-inline-item">
						<a href="https://tabler.io" target="_blank" rel="noopener noreferrer" class="link-secondary">Tabler</a>
					</li>
				</ul>
			</div>
		</div>
	</div>
</footer>
