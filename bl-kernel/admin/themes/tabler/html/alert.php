<?php
	// Check if the alert is enabled from the controller
	if( Alert::defined() )
	{
?>
<div class="alert-bludit alert alert-<?php echo Alert::status() ?> alert-dismissible fade show" role="alert">
	<div class="d-flex">
		<div>
			<?php if (Alert::status() === 'success'): ?>
				<i class="bi bi-check-circle-fill me-2"></i>
			<?php elseif (Alert::status() === 'danger'): ?>
				<i class="bi bi-exclamation-triangle-fill me-2"></i>
			<?php elseif (Alert::status() === 'warning'): ?>
				<i class="bi bi-exclamation-circle-fill me-2"></i>
			<?php else: ?>
				<i class="bi bi-info-circle-fill me-2"></i>
			<?php endif; ?>
		</div>
		<div>
			<?php echo Alert::get() ?>
		</div>
	</div>
	<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>

<script>
$(document).ready(function() {
	setTimeout(function() {
		$(".alert-bludit").fadeOut(500, function() {
			$(this).remove();
		});
	}, 5000);
});
</script>

<?php
	}
?>
