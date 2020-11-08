<script>
	$(document).ready(function() {
		new PNotify({
			title: '<?= __('Warning'); ?>',
			addclass: 'bg-warning',
			text: "<?= h($message); ?>",
			timeout: 6000
		});
	});
</script>